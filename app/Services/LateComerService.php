<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Attendence;
use App\Helpers\DateHelper;

class LateComerService {
    public function getAttendenceData($data): array
    {
        // dd($data);
        $monthAttendence = Carbon::now()->format('m');
        $employee_id = null;
        $to_date = null;
        $from_date = null;

        if (isset($data['employee_id'])) {
            $employee_id = $data['employee_id'];
        }
        if (isset($data['to_date'])) {
            $to_date = $data['to_date'];
        }
        if (isset($data['from_date'])) {
            $from_date = $data['from_date'];
        }

        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];


        $newAttendence = $this->get_all_attendence_log($monthAttendence, $args);

        $employees = User::Role(RolesEnum::Employee->value)->get();
        // dd($allAtendence->toArray(), $newAttendence);
        $data = compact('employees', 'newAttendence');
        return $data;
    }
    private function get_all_attendence_log($month, $args)
    {
        $currentYear = Carbon::now()->year;
        $allAtendence = Attendence::where('leave_time', '!=', null)->where('status', 0)->when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('d/m/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate('arrival_date', '<=', $formattedDate);
                // return $query->whereDate('arrival_time', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('d/m/Y', $from_date);
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
        // dd($allAtendence->toArray());
        foreach ($allAtendence as $attendence) {
            // $newDate = (string) Carbon::parse($attendence->arrival_date)->format('Y-m-d');
            $newDate = $attendence->arrival_date;
            $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
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
        // foreach ($allAtendence as $attendence) {
        //     $newDate = (string) Carbon::parse($attendence->arrival_time)->format('Y-m-d');
        //     $today = Carbon::create($currentYear, $month, 1, 0, 0, 0)->format('Y-m');
        //     $cmDate = Carbon::parse($attendence->arrival_time)->format('Y-m');
        //     if ($cmDate == $today) {
        //         if (array_key_exists($attendence->user_id, $newAttendence)) {
        //             if (array_key_exists($newDate, $newAttendence[$attendence->user_id])) {
        //                 $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] = DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time) + $newAttendence[$attendence->user_id][$newDate]['attendence_visual'];
        //                 $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] + DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time);
        //                 $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] = $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] + DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time);
        //                 $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();

        //             } else {
        //                 $newAttendence[$attendence->user_id][$newDate] = [
        //                     'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_time),
        //                     'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time),
        //                     'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time),
        //                     'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time),
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
        //                 'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time),
        //                 'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time),
        //                 'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time),
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
        return $newAttendence;
    }
}