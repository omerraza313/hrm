<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Attendence;
use App\Helpers\DateHelper;
use App\Enums\AttendenceEnum;
use App\Helpers\AttendanceLogging;
use App\Helpers\AttendenceHelper;
use App\Helpers\LoggingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;

class AttendenceService {
     /*public function getAttendenceData($data): array
     {
         // dd($data);
         $monthAttendence = Carbon::now()->format('m');
         // dd($monthAttendence);
         $employee_id = null;
         $to_date = null;
         $from_date = null;

         if (isset ($data['employee_id'])) {
             $employee_id = $data['employee_id'];
         }
         if (isset ($data['to_date'])) {
             $to_date = $data['to_date'];
         }
         if (isset ($data['from_date'])) {
             $from_date = $data['from_date'];
         }

         $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];


         $newAttendence = $this->get_all_attendence_log($monthAttendence, $args);


         $employees = User::Role(RolesEnum::Employee->value)->get();
         // dd($newAttendence);
         $data = compact('employees', 'newAttendence');
         return $data;
     }*/
     /*private function get_all_attendence_log($month, $args)
     {
         $currentYear = Carbon::now()->year;
         // $allAtendence = Attendence::where('leave_time', '!=', null)->when($args['emp_id'], function ($query, $emp_id) {
         //     return $query->where('user_id', $emp_id);
         // })
         //     ->when($args['to_date'], function ($query, $to_date) {
         //         $carbonDate = Carbon::createFromFormat('d/m/Y', $to_date);
         //         $formattedDate = $carbonDate->format('Y-m-d');
         //         // return $query->whereDate('arrival_time', '<=', $formattedDate);
         //         return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '<=', $formattedDate);
         //     })
         //     ->when($args['from_date'], function ($query, $from_date) {
         //         $carbonDate = Carbon::createFromFormat('d/m/Y', $from_date);
         //         $formattedDate = $carbonDate->format('Y-m-d');
         //         // return $query->whereDate('arrival_time', '>=', $formattedDate);
         //         return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '>=', $formattedDate);
         //     })
         //     ->with([
         //         'policy' => function ($query) {
         //             $query->withTrashed();
         //         },
         //         'policy.working_settings',
         //         'user',
         //         'user.employee_details',
         //         'user.employee_details.designation' => function ($query) {
         //             $query->withTrashed();
         //         },
         //     ])->get();

         // $allAtendence = Attendence::when($args['emp_id'], function ($query, $emp_id) {
         //     return $query->where('user_id', $emp_id);
         // })
         //     ->when($args['to_date'], function ($query, $to_date) {
         //         $carbonDate = Carbon::createFromFormat('d/m/Y', $to_date);
         //         $formattedDate = $carbonDate->format('Y-m-d');
         //         // return $query->whereDate('arrival_time', '<=', $formattedDate);
         //         return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '<=', $formattedDate);
         //     })
         //     ->when($args['from_date'], function ($query, $from_date) {
         //         $carbonDate = Carbon::createFromFormat('d/m/Y', $from_date);
         //         $formattedDate = $carbonDate->format('Y-m-d');
         //         // return $query->whereDate('arrival_time', '>=', $formattedDate);
         //         return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '>=', $formattedDate);
         //     })
         //     ->with([
         //         'policy' => function ($query) {
         //             $query->withTrashed();
         //         },
         //         'policy.working_settings',
         //         'user',
         //         'user.employee_details',
         //         'user.employee_details.designation' => function ($query) {
         //             $query->withTrashed();
         //         },
         //     ])->get();


         $allAtendence = Attendence::when($args['emp_id'], function ($query, $emp_id) {
             return $query->where('user_id', $emp_id);
         })
             ->when($args['to_date'], function ($query, $to_date) {
                 $carbonDate = Carbon::createFromFormat('d/m/Y', $to_date);
                 $formattedDate = $carbonDate->format('Y-m-d');
                 // return $query->whereDate('arrival_time', '<=', $formattedDate);
                 // return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '<=', $formattedDate);
                 return $query->where('arrival_date', '<=', $formattedDate);
             })
             ->when($args['from_date'], function ($query, $from_date) {
                 $carbonDate = Carbon::createFromFormat('d/m/Y', $from_date);
                 $formattedDate = $carbonDate->format('Y-m-d');
                 // return $query->whereDate('arrival_time', '>=', $formattedDate);
                 // return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '>=', $formattedDate);
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
             ])->orderBy('id', 'asc')->get();


         $newAttendence = [];
         // dd($allAtendence->toArray());
         if ($args['from_date']) {
             $carbonFromDate = Carbon::createFromFormat('d/m/Y', $args['from_date']);
             $newMonth = $carbonFromDate->format('m');
         } else {
             $newMonth = $month;
         }


         // foreach ($allAtendence as $attendence) {
         //     $newDate = (string) Carbon::parse($attendence->arrival_time)->format('Y-m-d');
         //     $today = Carbon::create($currentYear, $newMonth, 1, 0, 0, 0)->format('Y-m');
         //     $cmDate = Carbon::parse($attendence->arrival_time)->format('Y-m');
         //     if ($cmDate == $today) {
         //         if (array_key_exists($attendence->user_id, $newAttendence)) {
         //             if (array_key_exists($newDate, $newAttendence[$attendence->user_id])) {
         //                 $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')) + $newAttendence[$attendence->user_id][$newDate]['attendence_visual'];
         //                 $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s'));
         //                 $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s'));
         //                 $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();

         //             } else {
         //                 $newAttendence[$attendence->user_id][$newDate] = [
         //                     'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
         //                     'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                     'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                     'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                     'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
         //                     'status' => $attendence->status,
         //                     'user' => $attendence->user,
         //                     'id' => $attendence->id,
         //                     'data' => $attendence,
         //                     'logs' => [$attendence->toArray()]
         //                 ];
         //             }
         //         } else {
         //             $newAttendence[$attendence->user_id][$newDate] = [
         //                 'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
         //                 'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                 'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                 'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? Carbon::now()->format('Y-m-d H:i:s')),
         //                 'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
         //                 'status' => $attendence->status,
         //                 'user' => $attendence->user,
         //                 'id' => $attendence->id,
         //                 'data' => $attendence,
         //                 'logs' => [$attendence->toArray()]
         //             ];
         //         }
         //     }
         // }



         foreach ($allAtendence as $attendence) {
             // $newDate = (string) Carbon::parse($attendence->arrival_date)->format('Y-m-d');
             $newDate = $attendence->arrival_date;
             $today = Carbon::create($currentYear, $newMonth, 1, 0, 0, 0)->format('Y-m');
             $cmDate = Carbon::parse($attendence->arrival_date)->format('Y-m');
             if ($cmDate == $today) {
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
         // dd($newAttendence);
         return $newAttendence;
     }*/


    public function getAttendenceData($data): array
    {
        //dd($data);
        $employee_id = $data['employee_id'] ?? null;
        $to_date = $data['to_date'] ?? null;
        $from_date = $data['from_date'] ?? null;

        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];

        //$newAttendence = $this->get_all_attendence_log($args);
        $newAttendence = AttendenceHelper::get_all_attendence_log($args);

        //dd($newAttendence);
        $employees = User::Role(RolesEnum::Employee->value)->get();
        $data = compact('employees', 'newAttendence', 'args');
        return $data;
    }


    private function get_all_attendence_log($args)
{
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

            }

        }

        $dates = array_keys($newAttendence[$attendence->user_id]);
        $end_date = $dates[sizeof($dates) - 1];
        $start_date = $dates[0];

        $date_range = DateHelper::getDatesRange($start_date, $end_date);
        foreach ($date_range as $k => $current_date){
//            if($current_date=='2024-08-16'){
//                dd($newAttendence[$attendence->user_id][$current_date]);
//            }
            if(!empty($newAttendence[$attendence->user_id]) && empty($newAttendence[$attendence->user_id][$current_date])){
                $new_attendance = $newAttendence[$attendence->user_id][$start_date];
                $new_attendance['arrival_time'] = Carbon::parse($current_date.' '.$new_attendance['shift_start'])->addHours(4);
                $new_attendance['leave_time'] = $new_attendance['arrival_time'];
                $new_attendance['a_date'] = DateHelper::globaldateFormat('j M Y', $current_date);
                $new_attendance['leave_date'] = $current_date;
                $new_attendance['arrival_date'] = $current_date;
                $new_attendance['logs'] = [];
                $new_attendance['effective_hrs_in_hours'] = 0;
                $new_attendance['effective_hrs_in_minus'] = 0;
                $new_attendance['attendence_visual'] = 0;
                if(Carbon::parse($current_date)->format('D') == 'Sat' || Carbon::parse($current_date)->format('D') == 'Sun'){
                    $new_attendance['status'] = 2;
                }else{
                    $new_attendance['status'] = 3;
                }

                $newAttendence[$attendence->user_id][$current_date] = $new_attendance;
            }

        }

        ksort($newAttendence[$attendence->user_id]);
        //dd([$start_date, $end_date, $dates, $date_range, $newAttendence[$attendence->user_id]]);
        return $newAttendence;
    }

    private function get_timeout( $policy_id, $start_time) {
        return $start_time;
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

    public function updateAttendence($data): bool
    {
        // dd($data);
        $loop_length = count($data['id']);
        for ($index = 0; $index < $loop_length; $index++) {
            $attendence = Attendence::find($data['id'][$index]);
            $arrival_time = $this->combineDateAndTime($attendence->arrival_date, $data['arrival_time'][$index]);
            $leave_time = $this->combineDateAndTime($attendence->arrival_date, $data['leave_time'][$index]);


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
            //dd("Two Day");
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


    private function oneDaymark($markdate, $user, $attendance_update)
    {
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

            // dd($markdate);
            $attendance_update->status = $status;
            $attendance_update->arrival_time = $markdate;
            $attendance_update->save();

            AttendanceLogging::log('Attendance Admin Update', $attendance_update->toArray());

        } else {
            // return ['status' => false, 'message' => 'Some Error Occurred.'];
        }
        // return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];
    }

    private function twoDayMark($markedDateTime, $user, $attendance_update)
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


        $attendance_update->status = $status;
        $attendance_update->arrival_time = $markedDateTime;
        $attendance_update->save();
        AttendanceLogging::log('Attendance Admin Update', $attendance_update->toArray());


    }



    /* public function updateAttendence($data): bool
     {
         // dd($data);
         $attendence = Attendence::find($data['id']);
         $arrival_time = $this->combineDateAndTime($attendence->arrival_time, $data['arrival_time']);
         $attendence->arrival_time = $arrival_time;
         $leave_time = $this->combineDateAndTime($attendence->leave_time, $data['leave_time']);
         $attendence->leave_time = $leave_time;

         $user = User::whereId($attendence->user_id)->with(
             [
                 'policy' => function ($query) {
                     $query->where('status', 1)->latest();
                 },
                 'policy.pay_roll_settings',
                 'policy.working_settings',
                 'policy.working_day'
             ]
         )->first();

         if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
             $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start)->format('h:i A');
             $start_time = Carbon::parse($data['arrival_time'])->format('h:i A');
             if ($start_time < $shift_start) {
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }

         } else {
             $attendanceStartTime = Carbon::parse($data['arrival_time']);
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

         $attendence->status = $status;
         $attendence->save();

         return true;
     }*/

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
}
