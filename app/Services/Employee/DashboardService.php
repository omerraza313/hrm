<?php

namespace App\Services\Employee;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendence;
use App\Models\Department;
use App\Helpers\DateHelper;
use Illuminate\Support\Facades\Auth;

class DashboardService {
    private function get_employee(): object|array
    {
        $employee = User::where('id', Auth::user()->id)->with([
            'emergency_contacts',
            'family_contacts',
            'address',
            'employee_details',
            'employee_details.designation' => function ($query){
                $query->withTrashed();
            },
            'employee_details.department'  => function ($query){
                $query->withTrashed();
            },
            'bank',
            'educations',
            'experiences',
            'policy' => function ($query) {
                $query->where('status', 1);
            },
            'policy.working_settings',
            'policy.working_day' => function ($query) {
                $query->where('active', 1);
            },
            'policy.pay_roll_settings'
        ])->first();

        return $employee;
    }
    public function get_department_list(): object|array
    {
        return Department::all();
    }

    public function get_dashboard_data()
    {
        $employee = $this->get_employee();
//        dd($employee);

        $lastAttendence = Attendence::where('user_id', Auth::user()->id)->whereDate('arrival_time', '=', Carbon::now()->format('Y-m-d'))->orderBy('id', 'asc')->first();
        $policyDays = [];
        $totalWorkingDays = 0;
        $totalWorkingHours = 0;
        $workingHours = 0;
        $newAttendences = [];
        $absentLog = [];
        if (!empty($employee->policy->toArray())) {
            foreach ($employee->policy[0]->working_day as $workingDay) {
                $policyDays[] = $workingDay->day;
            }


            $startTime = $employee->policy[0]->working_settings->shift_start;
            $closeTime = $employee->policy[0]->working_settings->shift_close;

            $month = Carbon::now()->format('m'); // December
            $days = $policyDays; // Monday to Friday

            $dob_before = $employee->employee_details->dob;
            $doj_before = $employee->employee_details->join_date;
            $totalWorkingHours = $this->calculateWorkingHoursForCurrentDay($startTime, $closeTime, $days, Carbon::now());

            $newAttendences = $this->get_all_attendence_log($month);
            $currentAttendance = $this->get_current_attendence_log($month);
            $absentLog = $this->get_total_attedence_log($month);

            foreach ($currentAttendance as $att) {
                $workingHours += $att['effective_hrs_in_hours'];
            }
            // dd($totalWorkingHours);
        }
        $dob = Carbon::createFromFormat('d/m/Y', $employee->employee_details->dob)->format('m/d/Y'); // Parse the date using Carbon
        $doj = Carbon::createFromFormat('d/m/Y', $employee->employee_details->join_date)->format('m/d/Y'); // Parse the date using Carbon

        //dd($employee);

        return compact('employee', 'totalWorkingHours', 'workingHours', 'lastAttendence', 'newAttendences', 'absentLog', 'dob', 'doj');
    }

    private function get_current_attendence_log($month)
    {
        $currentYear = Carbon::now()->year;
        $allAtendence = Attendence::where('user_id', Auth::user()->id)->whereDate('arrival_time', Carbon::now()->format('Y-m-d'))->where('leave_time', '!=', null)->with([
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

    private function calculateWorkingHoursForCurrentDay($startTime, $closeTime, $days, $currentDate)
    {
        // Check if the current day is one of the specified days
        if (in_array($currentDate->dayOfWeek, $days)) {
            // Calculate working hours for the current day
            $startDateTime = $currentDate->copy()->setTimeFromTimeString($startTime);
            $closeDateTime = $currentDate->copy()->setTimeFromTimeString($closeTime);

            $workingHours = $closeDateTime->diffInHours($startDateTime);

            return $workingHours;
        }

        return 0; // Return 0 if the current day is not a working day
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

    private function get_all_attendence_log($month)
    {
        $currentYear = Carbon::now()->year;
        $allAtendence = Attendence::where('user_id', Auth::user()->id)->where('leave_time', '!=', null)->with([
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

    private function get_total_attedence_log($month)
    {
        $currentYear = Carbon::now()->year;
        $allAtendence = Attendence::where('user_id', Auth::user()->id)->where('leave_time', '!=', null)->where('status', 3)->with([
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
}
