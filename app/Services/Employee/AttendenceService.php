<?php

namespace App\Services\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Models\Attendence;
use App\Helpers\DateHelper;
use App\Enums\AttendenceEnum;
use App\Enums\RolesEnum;
use App\Helpers\AttendanceLogging;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendenceService {
    public function getAttendenceData($data): array
    {
        // dd($data);
        $monthAttendence = Carbon::now()->format('m');
        if (isset($data['filterMonth'])) {
            $monthAttendence = $data['filterMonth'];
        }
        $todayAttendence = Attendence::where('user_id', Auth::user()->id)->whereDate('arrival_time', Carbon::today())->where('leave_time', null)->orderBy('id', 'desc')->first();
        $lastAttendence = Attendence::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        $policy = $this->getPolicy();

        $newAttendence = $this->get_all_attendence_log($monthAttendence);
        $presentAttendence = $this->get_present_attendence_log($monthAttendence);

        // dd($newAttendence);
        $month = $monthAttendence; // December
        $daysCount = $this->countDaysInMonth($month);


        $policyDays = [];
        $totalWorkingDays = 0;
        $totalWorkingHours = 0;
        $workingHours = 0;
        if (!empty($policy)) {
            foreach ($policy->working_day as $workingDay) {
                $totalWorkingDays += $daysCount[$workingDay->day];
                $policyDays[] = $workingDay->day;
            }


            $startTime = $policy->working_settings->shift_start;
            $closeTime = $policy->working_settings->shift_close;

            $days = $policyDays; // Monday to Friday

            $totalWorkingHours = $this->calculateWorkingHours($startTime, $closeTime, $month, $days);

            $workingHours = 0;

            foreach ($newAttendence as $att) {
                $workingHours += $att['effective_hrs_in_hours'];
            }
        }

        // $month = 12; // December
        $data = compact('todayAttendence', 'lastAttendence', 'policy', 'newAttendence', 'totalWorkingDays', 'presentAttendence', 'totalWorkingHours', 'workingHours');
        return $data;
    }

    public function getAttendenceDataManager($data): array
    {
        $employee_id = $data['employee_id'] ?? null;
        $to_date = $data['to_date'] ?? null;
        $from_date = $data['from_date'] ?? null;

        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];

        $newAttendence = $this->get_all_attendence_log_admin($args);

        $manager_id = Auth::user()->id;
        $employees = User::with('employee_details') // Eager load details if needed
        ->whereHas('roles', function ($query) {
            $query->where('name', 'employee'); // Assuming roles table with 'name' column
        })
            ->whereHas('employee_details', function ($query) use ($manager_id) {
                $query->where('manager_id', $manager_id);
            })
            ->get();

        $data = compact('employees', 'newAttendence');
        return $data;
    }
    private function calculateWorkingHours($startTime, $closeTime, $month, $days)
    {
        $totalWorkingHours = 0;

        $currentDate = Carbon::create(null, $month, 1, 0, 0, 0);
        $endDate = $currentDate->copy()->endOfMonth();

        // Loop through each day between the start and end dates
        while ($currentDate->lte($endDate)) {
            // Check if the current day is one of the specified days
            if (in_array($currentDate->dayOfWeek, $days)) {
                // Calculate working hours for the current day
                $startDateTime = $currentDate->copy()->setTimeFromTimeString($startTime);
                $closeDateTime = $currentDate->copy()->setTimeFromTimeString($closeTime);

                $workingHours = $closeDateTime->diffInHours($startDateTime);

                // Add working hours to the total
                $totalWorkingHours += $workingHours;
            }

            // Move to the next day
            $currentDate->addDay();
        }

        return $totalWorkingHours;
    }

    public function countDaysInMonth($month)
    {
        $startDate = Carbon::create(null, $month, 1, 0, 0, 0);
        $endDate = $startDate->copy()->endOfMonth();

        $dayCounts = [];

        for ($day = 1; $day <= 7; $day++) {
            $dayCounts[$day] = 0;
        }

        while ($startDate->lte($endDate)) {
            $dayOfWeek = $startDate->dayOfWeek + 1; // Carbon uses 0-based index for day of week

            // Increment the count for the day of the week
            $dayCounts[$dayOfWeek]++;

            // Move to the next day
            $startDate->addDay();
        }

        return $dayCounts;
    }
    private function get_all_attendence_log_admin($args)
    {
        $currentYear = Carbon::now()->year;
        $args['manager_id'] = Auth::user()->id;
        $manager_id = Auth::user()->id;


        $currentYear = Carbon::now()->year;

        $allAtendence = Attendence::when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('arrival_date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('arrival_date', '>=', $formattedDate);
            })
            ->with([
                'policy' => function ($query) {
                    $query->withTrashed();
                },
                'policy.working_settings',
                'user' => function ($query) {
                    $query->withTrashed();
                },
                'user.employee_details',
                'user.employee_details.designation' => function ($query) {
                    $query->withTrashed();
                },
            ])
            ->orderBy('id', 'asc')
            ->get();

        $newAttendence = [];
        $manager_id = Auth::user()->id;

        $policies_working_settings = \App\Helpers\PolicyHelper::get_timeout_policy();

        foreach ($allAtendence as $attendence) {
            $newDate = $attendence->arrival_date;
            $original_attendence = $attendence;
            $newDate = $attendence->arrival_date;
            $employee_manager_id = $attendence->user->employee_details->manager_id;
            $employee_id = $attendence->user->id;
            if( $manager_id != $employee_manager_id && $employee_id != $manager_id){
                continue;
            }
            $alternate_leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time);
            if( is_null($attendence->leave_date) ){
                $empty_date = true;
                $attendence->leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time );
            }

            if (!isset($newAttendence[$attendence->user_id][$newDate])) {
                if($attendence->arrival_time > $attendence->leave_time && !is_null($attendence->leave_date)){
                    $swaper = $attendence->arrival_time ;
                    $attendence->arrival_time = $attendence->leave_time;
                    $attendence->leave_time = $swaper;
                }
                $newAttendence[$attendence->user_id][$newDate] = [
                    'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_date),
                    'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                    'status' => $attendence->status,
                    'user' => $attendence->user,
                    'id' => $attendence->id,
                    'data' => $attendence,

                    'arrival_date' =>   $attendence->arrival_date,
                    'leave_date' =>   $attendence->leave_date,
                    'arrival_time' =>   $attendence->arrival_time,
                    'leave_time' =>     $attendence->leave_time,
                    'shift_start' =>    $attendence->policy->working_settings->shift_start,
                    'shift_close' =>    $attendence->policy->working_settings->shift_close,
                    'timeout_policy' =>    $policies_working_settings[$attendence->policy->working_settings->timeout_policy],
                    'timeout_policy_id' =>    $attendence->policy->working_settings->timeout_policy,

                    'logs' => [$attendence->toArray()]
                ];
//                dd($newAttendence[$attendence->user_id][$newDate], $original_attendence);

            } else {

                $old_row = $newAttendence[$attendence->user_id][$newDate];

                if($attendence->arrival_time > $attendence->leave_time){
                    $swaper = $attendence->arrival_time ;
                    $attendence->arrival_time = $attendence->leave_time;
                    $attendence->leave_time = $swaper;
                }

                $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();
                if($old_row['effective_hrs_in_minus']>=6 && empty($attendence->leave_date)){
                    // un handled case
                }else{
                    $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] += DateHelper::progressBarWithTime($old_row['shift_start'], $old_row['shift_close'], $attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] += DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] += DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                }

                if($newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus']>=60){
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] -= 60;
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] +=1;
                }

                $new_row = $newAttendence[$attendence->user_id][$newDate];
//                dd($newAttendence[$attendence->user_id][$newDate], $original_attendence);

            }

        }

        return $newAttendence;
    }

    private function get_timeout( $policy_id, $start_time) {
        $policies_working_settings = \App\Helpers\PolicyHelper::get_timeout_policy();

        $start_time1 = $start_time;
        if($policy_id==3){
            $multiplier = 4.5;
        }elseif ($policy_id==2){
            $multiplier = 0;
        }elseif ($policy_id==1){
            $multiplier = 9;
        }else{
            $multiplier = 1;
        }

        $utcTimestamp = Carbon::parse($start_time)->addHours($multiplier);

        return $utcTimestamp;
    }
    private function get_timeout1($policy_rules, $policy_id, $start_time, $dateString){

        $startTime = strtotime($start_time);
        $duration = 4.5 * 3600;
        $endTime = $startTime + $duration;
        $timeString = date("H:i:00", $endTime);

        $dateTime = new DateTime("$dateString $timeString");
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $utcTimestamp = $dateTime->getTimestamp();

        return $utcTimestamp;
    }

    private function get_all_attendence_log($month)
    {
        $currentYear = Carbon::now()->year;
        // $allAtendence = Attendence::where('user_id', Auth::user()->id)->where('leave_time', '!=', null)->where('status', '!=', 2)->with([
        //     'policy' => function ($query) {
        //         $query->withTrashed();
        //     },
        //     'policy.working_settings'
        // ])->get();

        $allAtendence = Attendence::where('user_id', Auth::user()->id)->where('status', '!=', 2)->with([
            'policy' => function ($query) {
                $query->withTrashed();
            },
            'policy.working_settings'
        ])->get();


        // dd($allAtendence->toArray());
        $newAttendence = [];
        foreach ($allAtendence as $attendence) {
//             dd($attendence);
            $newDate = (string) Carbon::parse($attendence->arrival_time)->format('Y-m-d');
            $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
            $cmDate = Carbon::parse($attendence->arrival_time)->format('Y-m');

            $alternate_leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time);
            if( is_null($attendence->leave_date) ){
                $empty_date = true;
                $attendence->leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time );
            }

            if($attendence->arrival_time > $attendence->leave_time && !is_null($attendence->leave_date)){
                $swaper = $attendence->arrival_time ;
                $attendence->arrival_time = $attendence->leave_time;
                $attendence->leave_time = $swaper;
            }

            if ($cmDate == $today) {
                if (array_key_exists($newDate, $newAttendence)) {
                    $newAttendence[$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')) + $newAttendence[$newDate]['attendence_visual'];
                    $newAttendence[$newDate]['effective_hrs_in_hours'] = $newAttendence[$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s'));
                    $newAttendence[$newDate]['effective_hrs_in_minus'] = $newAttendence[$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s') ?? Carbon::now()->format('Y-m-d H:i:s'));
                    $newAttendence[$newDate]['logs'][] = $attendence->toArray();

                } else {
                    $newAttendence[$newDate] = [
                        'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
                        'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                        'status' => $attendence->status,
                        'logs' => [$attendence->toArray()]
                    ];
                }
            }
        }

//         dd($newAttendence);
        return $newAttendence;
    }
    private function get_present_attendence_log($month)
    {
        $currentYear = Carbon::now()->year;
        $allAtendence = Attendence::where('user_id', Auth::user()->id)->whereIn('status', [0, 1])->where('leave_time', '!=', null)->with([
            'policy' => function ($query) {
                $query->withTrashed();
            },
            'policy.working_settings'
        ])->get();
        $newAttendence = [];
        foreach ($allAtendence as $attendence) {
            $newDate = (string) Carbon::parse($attendence->arrival_time)->format('Y-m-d');
            $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
            $cmDate = Carbon::parse($attendence->arrival_time)->format('Y-m');
            if ($cmDate == $today) {
                if (array_key_exists($newDate, $newAttendence)) {
                    $newAttendence[$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time) + $newAttendence[$newDate]['attendence_visual'];
                    $newAttendence[$newDate]['effective_hrs_in_hours'] = $newAttendence[$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time);
                    $newAttendence[$newDate]['effective_hrs_in_minus'] = $newAttendence[$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time);

                } else {
                    $newAttendence[$newDate] = [
                        'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
                        'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time),
                        'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time),
                        'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time),
                        'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                        'status' => $attendence->status
                    ];
                }
            }
        }

        return $newAttendence;
    }
    private function getPolicy(): mixed
    {
        $user = User::withTrashed()->whereId(Auth::user()->id)->with([
            'policy' => function ($query) {
                $query->where('status', 1);
            },
            'policy.working_day' => function ($query) {
                $query->where('active', '!=', '0');
            },
            'policy.working_settings',
            'policy.pay_roll_settings'
        ])->first();
        return $user->policy[0] ?? [];
    }

    /* public function markAttendence()
     {
         $markdate = Carbon::now()->timezone('America/New_York');
         $user = User::whereId(Auth::user()->id)->with(
             [
                 'policy' => function ($query) {
                     $query->where('status', 1)->latest();
                 },
                 'policy.pay_roll_settings',
                 'policy.working_settings',
                 'policy.working_day',
                 'employee_details'
             ]
         )->first();

         Salary::create([
             'date' => $markdate->format('Y-m-d'),
             'salary' => $user->employee_details->salary,
             'user_id' => $user->id,
         ]);
         // dd($user->toArray());
         try {
             $user->policy[0]->pay_roll_settings->generation_type;
         } catch (\Throwable $th) {
             return ['status' => false, 'message' => 'You do not have a policy assigned.'];
         }
         // Attendence Base
         if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
             $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start)->format('h:i A');

             dd($markdate->format('h:i A'), $shift_start);
             if ($markdate->format('h:i A') < $shift_start) {
                 // dd($shift_start);
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }

             // dd($shift_start, "Second");
         } else {
             $attendanceStartTime = $markdate;
             $policyStartTime = Carbon::parse($user->policy[0]->working_settings->shift_start);

             // Calculate the duration in minutes
             $durationInMinutes = $attendanceStartTime->diffInMinutes($policyStartTime);

             // Convert the duration to hours
             $durationInHours = $durationInMinutes / 60;

             if ($durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }
         }
         if ($user->policy) {
             $mainDate = $markdate;
             $attendence = Attendence::where('user_id', $user->id)
                 ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
                 ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
                 ->first();
             // $attendence = Attendence::where('user_id', $user->id)
             //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
             //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
             //     ->first();
             // dd($attendence);
             if ($attendence) {
                 $attendence->status = $status;
                 $attendence->save();
             }
             Attendence::create([
                 'user_id' => $user->id,
                 'arrival_time' => $markdate,
                 'arrival_date' => $markdate->format('Y-m-d'),
                 'policy_id' => $user->policy[0]->id,
                 'status' => $status,
             ]);
         } else {
             return ['status' => false, 'message' => 'Some Error Occurred.'];
         }
         return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];
     }

     public function markAttendence()
     {
         $markdate = Carbon::now()->timezone('America/New_York');
         $user = User::whereId(Auth::user()->id)->with(
             [
                 'policy' => function ($query) {
                     $query->where('status', 1)->latest();
                 },
                 'policy.pay_roll_settings',
                 'policy.working_settings',
                 'policy.working_day',
                 'employee_details'
             ]
         )->first();

         Salary::create([
             'date' => $markdate->format('Y-m-d'),
             'salary' => $user->employee_details->salary,
             'user_id' => $user->id,
         ]);
         // dd($user->toArray());
         try {
             $user->policy[0]->pay_roll_settings->generation_type;
         } catch (\Throwable $th) {
             return ['status' => false, 'message' => 'You do not have a policy assigned.'];
         }
         // Attendence Base
         if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
             $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start, 'America/New_York');

             // dd($shift_start, $markdate);
             // dd($markdate->format('h:i A'), $shift_start);
             $min_diff = $markdate->diffInMinutes($shift_start, false);
             // dd($min_diff);


             // Calculate the time difference in seconds
             // $timeDifferenceInSeconds = $markdate->diffInSeconds($shift_start);

             // // Convert seconds to minutes
             // $timeDifferenceInMinutes = $timeDifferenceInSeconds / 60;

             // dd($timeDifferenceInMinutes);
             $neg_status = false;
             if ($min_diff < 0) {
                 $neg_status = true;
             }
             $postive_time_diff = abs($min_diff);
             // dd($postive_time_diff);


             if ($markdate->format('h:i A') < $shift_start->format('h:i A') || ($neg_status && $postive_time_diff <= $user->policy[0]->working_settings->late_c_l_t)) {
                 // dd($shift_start);
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }

             // dd($shift_start, "Second");
         } else {
             $attendanceStartTime = $markdate;
             $policyStartTime = Carbon::parse($user->policy[0]->working_settings->shift_start);

             // Calculate the duration in minutes
             $durationInMinutes = $attendanceStartTime->diffInMinutes($policyStartTime);

             // Convert the duration to hours
             $durationInHours = $durationInMinutes / 60;

             if ($durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }
         }
         if ($user->policy) {
             $mainDate = $markdate;
             $attendence = Attendence::where('user_id', $user->id)
                 // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
                 ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
                 ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
                 ->first();
             // $attendence = Attendence::where('user_id', $user->id)
             //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
             //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
             //     ->first();
             // dd($attendence);
             if ($attendence) {
                 $attendence->status = $status;
                 $attendence->save();
             }
             $check_attendance = Attendence::whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
                 ->whereNull('leave_time')->first();
             if (!$check_attendance) {
                 $save_time = $markdate;
                 if (($markdate->format('h:i A') < $shift_start->format('h:i A')) && ($user->policy[0]->working_settings->early_arrival_policy == 2)) {
                     $save_time = $shift_start;
                 }
                 // dd($save_time);
                 Attendence::create([
                     'user_id' => $user->id,
                     'arrival_time' => $save_time,
                     'arrival_date' => $save_time->format('Y-m-d'),
                     'policy_id' => $user->policy[0]->id,
                     'status' => $status,
                 ]);
             }

         } else {
             return ['status' => false, 'message' => 'Some Error Occurred.'];
         }
         return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];
     }*/

    public function updateAttendence($data): bool
    {
        // dd($data);
        $loop_length = count($data['id']);
        for ($index = 0; $index < $loop_length; $index++) {
            $attendence = Attendence::find($data['id'][$index]);
            // dd($attendence->toArray());
            $arrival_time = $this->combineDateAndTime($attendence->arrival_date, $data['arrival_time'][$index]);
            $leave_time = $this->combineDateAndTime($attendence->arrival_date, $data['leave_time'][$index]);

            // dd($attendence->toArray());

            /**
             *
             * Update Arrival Time
             *
             */
            $attendence->leave_time = $leave_time;
            $attendence->leave_date = $attendence->arrival_date;
            $attendence->save();
            $this->update_arrival_time($attendence, $arrival_time);

        }
        return true;
    }

    private function update_arrival_time($attendence, $arrival_time)
    {
        $user = User::whereId($attendence->user_id)->with(
            [
                'policy' => function ($query) {
                    $query->where('status', 1)->latest();
                },
                'policy.pay_roll_settings',
                'policy.working_settings',
                'policy.working_day',
                'employee_details'
            ]
        )->first();

        try {
            $user->policy[0]->pay_roll_settings->generation_type;
        } catch (\Throwable $th) {
            return false;
        }


        // HRMS policy start and end times in AM/PM format
        $policyStartTime = $user->policy[0]->working_settings->shift_start;
        $policyEndTime = $user->policy[0]->working_settings->shift_close;


        // Marked attendance date and time
        // $markedDateTime = '2024-02-06 00:59:00'; // Example date and time
        $markedDateTime = Carbon::create($arrival_time, 'America/New_York'); // Example date and time
        // dd($markedDateTime);

        // Convert HRMS policy times to Carbon objects
        $policyStart = Carbon::createFromFormat('h:iA', $policyStartTime, 'America/New_York');
        $policyEnd = Carbon::createFromFormat('h:iA', $policyEndTime, 'America/New_York');

        $markedDateTime = Carbon::parse($markedDateTime);
        if ($policyStart->format('a') == 'pm' && $policyEnd->format('a') == 'am') {
            dd("Two Day");
            AttendanceLogging::log('Attendance Marked Portal', 'Two Day Mark');
            AttendanceLogging::log('User Details Portal', $user->toArray());
            $this->twoDayMark($markedDateTime, $user, $attendence);

        } else {
            // dd("One Day");
            AttendanceLogging::log('Attendance Marked Portal', 'One Day Mark');
            AttendanceLogging::log('User Details Portal', $user->toArray());
            $this->oneDaymark($markedDateTime, $user, $attendence);
        }
    }

    private function combineDateAndTime($date, $time)
    {
        // Parse the existing date
        $carbonDate = Carbon::parse($date);

        // Parse the user-provided time
        $carbonTime = Carbon::parse($time);

        // Set the time portion of the existing date to the user-provided time
        $carbonDate->setTime($carbonTime->hour, $carbonTime->minute, $carbonTime->second);

        // Format the resulting datetime
        $combinedDatetime = $carbonDate->format('Y-m-d H:i:s');

        return $combinedDatetime;
    }
    public function markAttendence()
    {
        $markdate = Carbon::now()->setTimezone('America/New_York');
        $user = User::whereId(Auth::user()->id)->with(
            [
                'policy' => function ($query) {
                    $query->where('status', 1)->latest();
                },
                'policy.pay_roll_settings',
                'policy.working_settings',
                'policy.working_day',
                'employee_details'
            ]
        )->first();

        Salary::create([
            'date' => $markdate->format('Y-m-d'),
            'salary' => $user->employee_details->salary,
            'user_id' => $user->id,
        ]);

        try {
            $user->policy[0]->pay_roll_settings->generation_type;
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => 'You do not have a policy assigned.'];
        }


        // HRMS policy start and end times in AM/PM format
        $policyStartTime = $user->policy[0]->working_settings->shift_start;
        $policyEndTime = $user->policy[0]->working_settings->shift_close;


        // Marked attendance date and time
        // $markedDateTime = '2024-02-06 00:59:00'; // Example date and time
        $markedDateTime = $markdate; // Example date and time

        // Convert HRMS policy times to Carbon objects
        $policyStart = Carbon::createFromFormat('h:iA', $policyStartTime, 'America/New_York');
        $policyEnd = Carbon::createFromFormat('h:iA', $policyEndTime, 'America/New_York');

        $markedDateTime = Carbon::parse($markedDateTime);
        if ($policyStart->format('a') == 'pm' && $policyEnd->format('a') == 'am') {
            // dd("Two Day");
            AttendanceLogging::log('Attendance Marked Portal', 'Two Day Mark');
            AttendanceLogging::log('User Details Portal', $user->toArray());
            $this->twoDayMark($markedDateTime, $user);
        } else {
            // dd("One Day");
            AttendanceLogging::log('Attendance Marked Portal', 'One Day Mark');
            AttendanceLogging::log('User Details Portal', $user->toArray());
            $this->oneDaymark();
        }

        return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];

    }

    private function oneDaymark()
    {
        $markdate = Carbon::now()->timezone('America/New_York');
        $user = User::whereId(Auth::user()->id)->with(
            [
                'policy' => function ($query) {
                    $query->where('status', 1)->latest();
                },
                'policy.pay_roll_settings',
                'policy.working_settings',
                'policy.working_day',
                'employee_details'
            ]
        )->first();
        // Attendence Base
        if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
            $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start, 'America/New_York');

            // dd($shift_start, $markdate);
            // dd($markdate->format('h:i A'), $shift_start);
            $min_diff = $markdate->diffInMinutes($shift_start, false);
            // dd($min_diff);


            // Calculate the time difference in seconds
            // $timeDifferenceInSeconds = $markdate->diffInSeconds($shift_start);

            // // Convert seconds to minutes
            // $timeDifferenceInMinutes = $timeDifferenceInSeconds / 60;

            // dd($timeDifferenceInMinutes);
            $neg_status = false;
            if ($min_diff < 0) {
                $neg_status = true;
            }
            $postive_time_diff = abs($min_diff);
            // dd($postive_time_diff);


            if ($markdate->format('h:i A') < $shift_start->format('h:i A') || ($neg_status && $postive_time_diff <= $user->policy[0]->working_settings->late_c_l_t)) {
                // dd($shift_start);
                $status = AttendenceEnum::OnTime;
            } else {
                $status = AttendenceEnum::Late;
            }

            // dd($shift_start, "Second");
        } else {
            $attendanceStartTime = $markdate;
            $policyStartTime = Carbon::parse($user->policy[0]->working_settings->shift_start);

            // Calculate the duration in minutes
            $durationInMinutes = $attendanceStartTime->diffInMinutes($policyStartTime);

            // Convert the duration to hours
            $durationInHours = $durationInMinutes / 60;

            if ($durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                $status = AttendenceEnum::OnTime;
            } else {
                $status = AttendenceEnum::Late;
            }
        }
        if ($user->policy) {
            $mainDate = $markdate;
            $attendence = Attendence::where('user_id', $user->id)
                // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
                ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
                ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
                ->first();
            // $attendence = Attendence::where('user_id', $user->id)
            //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
            //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
            //     ->first();
            // dd($attendence);
            if ($attendence) {

                AttendanceLogging::log('Delete Attendance Portal', $attendence->toArray());
                AttendanceLogging::log('User Details Portal', $user->toArray());

                // $attendence->status = $status;
                $attendence->delete();
            }
            $check_attendance = Attendence::where('user_id', $user->id)->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
                ->whereNull('leave_time')->first();
            // dd($check_attendance);
            if (!$check_attendance) {
                $save_time = $markdate;
                if (($markdate->format('h:i A') < $shift_start->format('h:i A')) && ($user->policy[0]->working_settings->early_arrival_policy == 2)) {
                    $save_time = $shift_start;
                }
                // dd($save_time);
                $newAttend = Attendence::create([
                    'user_id' => $user->id,
                    'arrival_time' => $save_time,
                    'arrival_date' => $save_time->format('Y-m-d'),
                    'policy_id' => $user->policy[0]->id,
                    'status' => $status,
                ]);

                AttendanceLogging::log('New Attendance Portal', $newAttend->toArray());
                // AttendanceLogging::log('User Marked Portal', $user->toArray());
            }

        } else {
            // return ['status' => false, 'message' => 'Some Error Occurred.'];
        }
        // return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];
    }

    private function twoDayMark($markedDateTime, $user)
    {
        // $policyStartTime = '11:40AM';
        // $policyEndTime = '10:30PM';

        // Latency time in minutes
        $latencyMinutes = $user->policy[0]->working_settings->late_c_l_t;

        // $markedDateTime = '2024-02-06 23:49:00'; // Example date and time

        $markedDateTime = Carbon::parse($markedDateTime);

        // Convert HRMS policy times to Carbon objects
        // $policyStart = Carbon::createFromFormat('h:iA', $policyStartTime);
        // $policyEnd = Carbon::createFromFormat('h:iA', $policyEndTime);
        $policyStart = Carbon::createFromFormat('h:iA', $user->policy[0]->working_settings->shift_start, 'America/New_York');
        $policyEnd = Carbon::createFromFormat('h:iA', $user->policy[0]->working_settings->shift_close, 'America/New_York');

        // dd($markedDateTime, $policyStart, $policyEnd);

        // Adjust policy start time by adding latency time
        $policyStartWithLatency = $policyStart->copy()->addMinutes($latencyMinutes);

        // Check if policy end time is before policy start time (spans two days)
        if ($policyEnd->lessThan($policyStart)) {
            // If so, adjust marked time if it's after policy start time but before midnight
            if ($markedDateTime->greaterThanOrEqualTo($policyStart) && $markedDateTime->lte(Carbon::parse('23:59'))) {
                $markedDateTime->subDay();
            }
        }

        // dd($markedDateTime->greaterThan($policyStart), $markedDateTime->lte($policyStartWithLatency), $markedDateTime, $policyStartWithLatency, $policyStart, $policyEnd, $markedDateTime->gt($policyEnd));
        // Check if marked attendance time is before policy start time
        // dd($markedDateTime, $policyStartWithLatency, $policyEnd, $markedDateTime->format('H:i:s') > $policyEnd->format('H:i:s'));
        // dd($markedDateTime->lte($policyStartWithLatency), $markedDateTime->gt($policyEnd), $markedDateTime->gt($policyStartWithLatency));
        $status = '';
        if ($markedDateTime->format('H:i:s') <= $policyStartWithLatency->format('H:i:s') && $markedDateTime->format('H:i:s') > $policyEnd->format('H:i:s')) {
            echo "Attendance marked on time.";
            $status = AttendenceEnum::OnTime->value;
        } elseif ($markedDateTime->format('H:i:s') > $policyStartWithLatency->format('H:i:s') || $markedDateTime->format('H:i:s') <= $policyEnd->format('H:i:s')) {
            echo "Attendance marked late.";
            $status = AttendenceEnum::Late->value;
        } else {
            echo "Attendance marked late or outside of policy hours.";
            $status = AttendenceEnum::Late->value;
            // $status = AttendenceEnum::Holiday->value;
        }

        $mainDate = $markedDateTime;
        $attendence = Attendence::where('user_id', $user->id)
            // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
            ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
            ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
            ->first();
        // $attendence = Attendence::where('user_id', $user->id)
        //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
        //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
        //     ->first();
        // dd($attendence);
        if ($attendence) {
            // $attendence->status = $status;
            // $attendence->save();

            AttendanceLogging::log('Delete Attendance Portal', $attendence->toArray());
            AttendanceLogging::log('User Details Portal', $user->toArray());

            $attendence->delete();
        }

        // dd($status);
        $newAttend = Attendence::create([
            'user_id' => $user->id,
            'arrival_time' => $markedDateTime,
            'arrival_date' => $markedDateTime->format('Y-m-d'),
            'policy_id' => $user->policy[0]->id,
            'status' => $status,
        ]);

        AttendanceLogging::log('New Attendance Portal', $newAttend->toArray());


    }
    public function leaveAttendence($id): bool
    {
        if ($id == null) {
            return false;
        }
        $markdate = Carbon::now()->timezone('America/New_York');
        // dd($markdate);
        $user = User::whereId(Auth::user()->id)->with(
            [
                'policy' => function ($query) {
                    $query->where('status', 1)->latest();
                }
            ]
        )->first();

        if ($user->policy) {
            $markdate = $markdate->utc();
            $newAttend = Attendence::whereId($id)->update([
                'user_id' => $user->id,
                'leave_time' => $markdate,
                'leave_date' => $markdate->format('Y-m-d'),
                'policy_id' => $user->policy[0]->id,
            ]);
//            dd($newAttend);
//            AttendanceLogging::log('Leave Attendance Portal', $newAttend->toArray());
            AttendanceLogging::log('User Details Portal', $user->toArray());

        } else {
            return false;
        }
        return true;
    }

    // late
    public function getAttendenceDataLate($data): array
    {
        // dd($data);
        $monthAttendence = Carbon::now()->format('m');
        $employee_id = null;
        $to_date = null;
        $from_date = null;

        if (isset($data['employee_id'])) {
            $args['emp_id'] = $employee_id = $data['employee_id'];
        }
        if (isset($data['to_date'])) {
            $args['to_date'] = $to_date = $data['to_date'];
        }
        if (isset($data['from_date'])) {
            $args['from_date'] = $from_date = $data['from_date'];
        }

        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];


        $newAttendence = $this->get_all_attendence_log_late($monthAttendence, $args);

        $manager_id = Auth::user()->id;
        $employees = User::with('employee_details') // Eager load details if needed
        ->whereHas('roles', function ($query) {
            $query->where('name', 'employee'); // Assuming roles table with 'name' column
        })
            ->whereHas('employee_details', function ($query) use ($manager_id) {
                $query->where('manager_id', $manager_id);
            })
            ->get();

        $data = compact('employees', 'newAttendence');
        return $data;
    }
    private function get_all_attendence_log_late($month, $args)
    {
        $currentYear = Carbon::now()->year;
        $manager_id = Auth::user()->id;


        $allAtendence = Attendence::where('leave_time', '!=', null)->where('status', 0)->when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate('arrival_date', '<=', $formattedDate);
                // return $query->whereDate('arrival_time', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate('arrival_date', '>=', $formattedDate);
                // return $query->whereDate('arrival_time', '>=', $formattedDate);
            })
            ->with([
                'policy' => function ($query) {
                    $query->withTrashed();
                },
                'policy.working_settings',
                'user',
                'user.employee_details',
                'user.employee_details.designation' => function ($query) {
                    $query->withTrashed();
                }
            ])->get();
        $newAttendence = [];
//        dd($allAtendence);
//        dd($args);
        $date_range = ( !empty($args['to_date']) || !empty($args['from_date']) ) ? true : false;
        foreach ($allAtendence as $attendence) {
            if(empty($attendence->user->employee_details)){
//                dd($attendence);
                continue;
            }
            $employee_manager_id = $attendence->user->employee_details->manager_id ;
            if( $manager_id != $employee_manager_id){
                continue;
            }

            $newDate = $attendence->arrival_date;
            $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
            $cmDate = Carbon::parse($attendence->arrival_date)->format('Y-m');
            if ($date_range || $cmDate == $today) {
                if (array_key_exists($attendence->user_id, $newAttendence)) {
                    if (array_key_exists($newDate, $newAttendence[$attendence->user_id])) {
                        $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')) + $newAttendence[$attendence->user_id][$newDate]['attendence_visual'];
                        $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s'));
                        $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s'));
                        $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();
                        // dd($newAttendence);

                    } else {
                        $newAttendence[$attendence->user_id][$newDate] = [
                            'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_date),
                            'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                            'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                            'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                            'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                            'status' => $attendence->status,
                            'user' => $attendence->user,
                            'id' => $attendence->id,
                            'data' => $attendence,
                            'logs' => [$attendence->toArray()]
                        ];
                        // dd($newAttendence);
                        // echo "<pre>";
                        // print_r($newAttendence);
                        // echo "</pre>";
                        // die();

                    }
                } else {
                    $newAttendence[$attendence->user_id][$newDate] = [
                        'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_date),
                        'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
                        'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                        'status' => $attendence->status,
                        'user' => $attendence->user,
                        'id' => $attendence->id,
                        'data' => $attendence,
                        'logs' => [$attendence->toArray()]
                    ];

                    // dd($newAttendence);
                }
            }
        }
//        dd($newAttendence);
        /* foreach ($allAtendence as $attendence) {
             $newDate = (string) Carbon::parse($attendence->arrival_time)->format('Y-m-d');
             $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
             $cmDate = Carbon::parse($attendence->arrival_time)->format('Y-m');
             if ($cmDate == $today) {
                 if (array_key_exists($attendence->user_id, $newAttendence)) {
                     if (array_key_exists($newDate, $newAttendence[$attendence->user_id])) {
                         $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time) + $newAttendence[$attendence->user_id][$newDate]['attendence_visual'];
                         $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time);
                         $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time);
                         $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();

                     } else {
                         $newAttendence[$attendence->user_id][$newDate] = [
                             'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
                             'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time),
                             'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time),
                             'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time),
                             'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                             'status' => $attendence->status,
                             'user' => $attendence->user,
                             'id' => $attendence->id,
                             'data' => $attendence,
                             'logs' => [$attendence->toArray()]
                         ];
                     }
                 } else {
                     $newAttendence[$attendence->user_id][$newDate] = [
                         'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
                         'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time),
                         'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time),
                         'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time),
                         'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                         'status' => $attendence->status,
                         'user' => $attendence->user,
                         'id' => $attendence->id,
                         'data' => $attendence,
                         'logs' => [$attendence->toArray()]
                     ];
                 }
             }
         }*/
        return $newAttendence;
    }
}
