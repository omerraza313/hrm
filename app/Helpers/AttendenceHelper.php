<?php

namespace App\Helpers;

use App\Helpers\DateTime;
use App\Enums\AttendenceEnum;
use App\Models\Attendence;
use App\Models\DeviceLog;
use App\Models\Designation;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function Psy\debug;

class AttendenceHelper {
    public static function get_capital_months(): array
    {
        return [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];
    }
    public static function exceeds24Hours($seconds) {
        // 1 day = 24 hours * 60 minutes * 60 seconds
        $secondsInADay = 24 * 60 * 60;

        if ($seconds > $secondsInADay) {
            return true; // Exceeds 24 hours
        } else {
            return false; // Does not exceed 24 hours
        }
    }
    public static function get_log_2ndOption($args)
    {
        $allAttendance = DeviceLog::when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '>=', $formattedDate);
            })
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($logsByUser, $userId) {
                $previousDayLastCheckIn = null; // Last CheckIn of previous day
                $previousDayCheckInDate = null; // Date of last CheckIn of previous day
                $attendanceEntries = [];

                return $logsByUser->groupBy('date')->map(function ($dailyLogs, $date) use ($userId, &$previousDayLastCheckIn, &$previousDayCheckInDate) {
                    $deviceState = [];
                    $deviceLogsForDay = [];
                    $totalEarnedTime = 0;

                    foreach ($dailyLogs as $log) {
                        $deviceId = $log->device_id;
                        $logType = $log->type;
                        $logTime = Carbon::parse($log->time);

                        // Collect raw logs for the day
                        $deviceLogsForDay[] = [
                            'device_id' => $deviceId,
                            'datetime' => $logTime->format('Y-m-d H:i:s'),
                            'date' => $logTime->format('Y-m-d'),
                            'time' => $logTime->format('H:i:s'),
                            'type' => $logType,
                            'user_id' => $userId,
                        ];

                        // Process CheckOuts
                        if ($logType === 'CheckOut' || $logType === 'CheckedOut') {
                            if (isset($deviceState[$deviceId]) && $deviceState[$deviceId]['status'] === 'checked_in') {
                                // There was a corresponding CheckIn
                                $checkInTime = Carbon::parse($deviceState[$deviceId]['time']);
                                $earnedTimeInSeconds = $checkInTime->diffInSeconds($logTime);

                                $attendanceEntries[] = [
                                    'user_id' => $userId,
                                    'device_id' => $deviceId,
                                    'arrival_time' => $checkInTime->format('Y-m-d H:i:s'),
                                    'leave_time' => $logTime->format('Y-m-d H:i:s'),
                                    'earned_time' => gmdate('H:i:s', $earnedTimeInSeconds),
                                    'date' => $date,
                                    'remarks' => ''
                                ];

                                $totalEarnedTime += $earnedTimeInSeconds;
                                // Clear the state for this device
                                unset($deviceState[$deviceId]);
                            }
                        }

                        // Process CheckIns
                        if ($logType === 'CheckIn' || $logType === 'CheckedIn') {
                            // Check for previous day's unpaired CheckIn
                            if ($previousDayLastCheckIn) {
                                // Match with first CheckOut of this day if it exists
                                if (!isset($deviceState[$deviceId]) || $deviceState[$deviceId]['status'] !== 'checked_in') {
                                    // Unmatched CheckIn from the previous day
                                    $attendanceEntries[] = [
                                        'user_id' => $userId,
                                        'device_id' => $deviceId,
                                        'arrival_time' => $previousDayLastCheckIn->format('Y-m-d H:i:s'),
                                        'leave_time' => $logTime->format('Y-m-d H:i:s'),
                                        'earned_time' => gmdate('H:i:s', $previousDayLastCheckIn->diffInSeconds($logTime)),
                                        'date' => $previousDayCheckInDate,
                                        'remarks' => 'Matched with next day CheckOut'
                                    ];

                                    $totalEarnedTime += $previousDayLastCheckIn->diffInSeconds($logTime);
                                    $previousDayLastCheckIn = null; // Reset
                                    $previousDayCheckInDate = null; // Reset
                                }
                            }

                            // Update state for current CheckIn
                            $deviceState[$deviceId] = ['status' => 'checked_in', 'time' => $logTime->format('H:i:s')];

                            // Capture the last CheckIn for the previous day
                            $previousDayLastCheckIn = $logTime;
                            $previousDayCheckInDate = $date;
                        }
                    }

                    // Carry over the last check-in of the day to the next day
                    foreach ($deviceState as $deviceId => $state) {
                        if ($state['status'] === 'checked_in') {
                            $previousDayLastCheckIn = Carbon::parse($state['time']);
                            $previousDayCheckInDate = $date;
                        }
                    }

                    return [
                        'attendance' => $attendanceEntries,
                        'device_logs' => $deviceLogsForDay,
                        'earned_time' => gmdate('H:i:s', $totalEarnedTime),
                        'date' => $date,
                    ];
                });
            });

        return $allAttendance;
    }

    public static function get_log($args){


        $allAttendance = DeviceLog::when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '>=', $formattedDate);
            })
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($logsByUser, $userId) {
                $previousDayLastCheckIn = null; // Last CheckIn of previous day
                $previousDayCheckInDate = null; // Date of last CheckIn of previous day

                return $logsByUser->groupBy('date')->map(function ($dailyLogs, $date) use ($userId, &$previousDayLastCheckIn, &$previousDayCheckInDate) {
                    $attendanceEntries = [];
                    $deviceState = []; // Holds the last state per device_id
                    $deviceLogsForDay = []; // Raw logs for the day

                    $totalEarnedTime = 0;
                    $totalEffectiveTime = 0;
                    $firstArrivalTime = null;
                    $lastLeaveTime = null;

                    $firstLogOfDay = true;

                    foreach ($dailyLogs as $log) {
                        $deviceId = $log->device_id;
                        $logType = $log->type;
                        $logTime = Carbon::parse($log->time);

                        // Collect raw logs for the day
                        $deviceLogsForDay[] = [
                            'device_id' => $deviceId,
                            'datetime' => $logTime->format('Y-m-d H:i:s'),
                            'date' => $logTime->format('Y-m-d'),
                            'time' => $logTime->format('H:i:s'),
                            'type' => $logType,
                            'user_id' => $userId,
                        ];

                        // Check for first log of the day
                        if ($firstLogOfDay) {
                            // Ensure the first log is a CheckOut
                            if ($logType === 'CheckOut' && !is_null($previousDayLastCheckIn)) {
                                $checkInTime = Carbon::parse($previousDayCheckInDate . ' ' . $previousDayLastCheckIn);
                                $earnedTimeInSeconds = $checkInTime->diffInSeconds($logTime);

                                $total_diff = Carbon::parse($previousDayCheckInDate . ' ' . $previousDayLastCheckIn);
                                $totalTimeInSeconds = $total_diff->diffInSeconds($logTime);

                                if(!AttendenceHelper::exceeds24Hours($totalTimeInSeconds)){
                                    $attendanceEntries[] = [
                                        'user_id' => $userId,
                                        'device_id' => $deviceId,
                                        'arrival_time' => $previousDayCheckInDate . ' ' . $previousDayLastCheckIn,
                                        'leave_time' => $logTime->format('Y-m-d H:i:s'),
                                        'arrival_date' => $previousDayCheckInDate,
                                        'leave_date' => $date,
                                        'earned_time' => gmdate('H:i:s', $earnedTimeInSeconds),
                                        'date' => $previousDayCheckInDate, // Log it under the previous day's attendance
                                        'remarks' => "Checkout from next day matched with previous days CheckIn {$totalTimeInSeconds}"
                                    ];
                                    $totalEarnedTime += $earnedTimeInSeconds;

                                    // Reset previous day check-in
                                    $previousDayLastCheckIn = null;
                                    $previousDayCheckInDate = null;
                                    $firstLogOfDay = false;
                                    continue; // Skip the current day CheckOut as it's moved to the previous day's attendance
                                }

                            }
                            $firstLogOfDay = false;
                        }

                        // Handle device state and attendance entries
                        if (!isset($deviceState[$deviceId])) {
                            $deviceState[$deviceId] = null;
                        }

                        if ($logType === 'CheckIn' || $logType === 'CheckedIn') {
                            if ($deviceState[$deviceId] === 'checked_in') {
                                // Previous check-in without a check-out
                                $attendanceEntries[] = [
                                    'user_id' => $userId,
                                    'device_id' => $deviceId,
                                    'arrival_time' => $date . ' ' . $deviceState[$deviceId.'_time'],
                                    'leave_time' => $date . ' ' . $deviceState[$deviceId.'_time'],
                                    'earned_time' => '00:00:00',
                                    'date' => $date,
                                    'remarks' => 'Unmatched CheckIn'
                                ];
                            }
                            $deviceState[$deviceId] = 'checked_in';
                            $deviceState[$deviceId.'_time'] = $logTime->format('H:i:s');
                        }
                        elseif ($logType === 'CheckOut' || $logType === 'CheckedOut') {
                            if ($deviceState[$deviceId] === 'checked_in') {
                                $checkInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $deviceState[$deviceId.'_time']);
                                $leaveDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $logTime->format('Y-m-d H:i:s'));
                                $earnedTimeInSeconds = $checkInDateTime->diffInSeconds($leaveDateTime);

                                $attendanceEntries[] = [
                                    'user_id' => $userId,
                                    'device_id' => $deviceId,
                                    'arrival_time' => $checkInDateTime->format('Y-m-d H:i:s'),
                                    'leave_time' => $leaveDateTime->format('Y-m-d H:i:s'),
                                    'earned_time' => gmdate('H:i:s', $earnedTimeInSeconds),
                                    'date' => $date,
                                    'arrival_date' => $date,
                                    'leave_date' => $date,
                                    'remarks' => ''
                                ];

                                $totalEarnedTime += $earnedTimeInSeconds;
                            }
                            else {
                                // Check-out without a preceding check-in
                                $attendanceEntries[] = [
                                    'user_id' => $userId,
                                    'device_id' => $deviceId,
                                    'arrival_time' => $logTime->format('Y-m-d H:i:s'),
                                    'leave_time' => $logTime->format('Y-m-d H:i:s'),
                                    'earned_time' => '00:00:00',
                                    'date' => $date,
                                    'remarks' => 'Unmatched CheckOut'
                                ];
                            }
                            $deviceState[$deviceId] = 'checked_out';
                        }

                        // Set first arrival_time and last leave_time for the day
                        if ($logType === 'CheckIn' || $logType === 'CheckedIn') {
                            if (is_null($firstArrivalTime) || $logTime->lessThan($firstArrivalTime)) {
                                $firstArrivalTime = $logTime->format('Y-m-d H:i:s');
                            }
                        } elseif ($logType === 'CheckOut' || $logType === 'CheckedOut') {
                            if (is_null($lastLeaveTime) || $logTime->greaterThan($lastLeaveTime)) {
                                $lastLeaveTime = $logTime->format('Y-m-d H:i:s');
                            }
                        }
                    }

                    // Carry over the last check-in of the day to the next day
                    foreach ($deviceState as $deviceId => $state) {
                        if ($state === 'checked_in') {
                            $previousDayLastCheckIn = $deviceState[$deviceId.'_time'];
                            $previousDayCheckInDate = $date;
                        }
                    }

                    return [
                        'attendance' => $attendanceEntries,
                        'device_logs' => $deviceLogsForDay,
                        'arrival_time' => $firstArrivalTime,
                        'leave_time' => $lastLeaveTime,
                        'earned_time' => gmdate('H:i:s', $totalEarnedTime),
                        'effective_time' => gmdate('H:i:s', $totalEffectiveTime),
                        'date' => $date,
                    ];
                });
            });


        return $allAttendance;
    }

    public static function get_log2($args) {
        /*$logs = DeviceLog::where('user_id', $args['emp_id'])
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '>=', $formattedDate);
            })
            ->get();
        dd($logs);*/

        $allAttendance = DeviceLog::when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('date', '>=', $formattedDate);
            })
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($logsByUser, $userId) {
                $previousDayLastCheckIn = null; // Last CheckIn of previous day
                $previousDayCheckInDate = null; // Date of last CheckIn of previous day

                return $logsByUser->groupBy('date')->map(function ($dailyLogs, $date) use ($userId, &$previousDayLastCheckIn, &$previousDayCheckInDate) {
                    $attendanceEntries = [];
                    $deviceState = []; // Holds the last state per device_id
                    $deviceLogsForDay = []; // Raw logs for the day

                    $totalEarnedTime = 0;
                    $firstArrivalTime = null;
                    $lastLeaveTime = null;

                    $firstLogOfDay = true;

                    foreach ($dailyLogs as $log) {
                        $deviceId = $log->device_id;
                        $logType = $log->type;
                        $logTime = Carbon::parse($log->time);

                        // Collect raw logs for the day
                        $deviceLogsForDay[] = [
                            'device_id' => $deviceId,
                            'datetime' => $logTime->format('Y-m-d H:i:s'),
                            'date' => $logTime->format('Y-m-d'),
                            'time' => $logTime->format('H:i:s'),
                            'type' => $logType,
                            'user_id' => $userId,
                        ];

                        // Check for first log of the day
                        if ($firstLogOfDay) {
                            // If first log is a CheckOut and there's a previous day CheckIn
                            if ($logType === 'CheckOut' && !is_null($previousDayLastCheckIn)) {
                                $checkInTime = Carbon::parse($previousDayCheckInDate . ' ' . $previousDayLastCheckIn);
                                $earnedTimeInSeconds = $checkInTime->diffInSeconds($logTime);

                                // Add the checkout to the previous day
                                $attendanceEntries[] = [
                                    'user_id' => $userId,
                                    'device_id' => $deviceId,
                                    'arrival_time' => $previousDayCheckInDate . ' ' . $previousDayLastCheckIn,
                                    'leave_time' => $logTime->format('Y-m-d H:i:s'),
                                    'arrival_date' => $previousDayCheckInDate,
                                    'leave_date' => $date,
                                    'earned_time' => gmdate('H:i:s', $earnedTimeInSeconds),
                                    'date' => $previousDayCheckInDate, // Log under previous day
                                    'remarks' => 'Checkout from next day matched with previous day\'s CheckIn'
                                ];

                                $totalEarnedTime += $earnedTimeInSeconds;

                                // Reset previous day check-in
                                $previousDayLastCheckIn = null;
                                $previousDayCheckInDate = null;
                                $firstLogOfDay = false;
                                continue; // Skip the current day CheckOut
                            }
                            $firstLogOfDay = false;
                        }

                        // Rest of the processing logic follows...
                        // Handle device state and attendance entries
                        if (!isset($deviceState[$deviceId])) {
                            $deviceState[$deviceId] = null;
                        }

                        // Handle CheckIn and CheckOut logic here
                        // ...
                    }

                    // Carry over the last check-in of the day to the next day
                    foreach ($deviceState as $deviceId => $state) {
                        if ($state === 'checked_in') {
                            $previousDayLastCheckIn = $deviceState[$deviceId . '_time'];
                            $previousDayCheckInDate = $date;
                        }
                    }

                    return [
                        'attendance' => $attendanceEntries,
                        'device_logs' => $deviceLogsForDay,
                        'arrival_time' => $firstArrivalTime,
                        'leave_time' => $lastLeaveTime,
                        'earned_time' => gmdate('H:i:s', $totalEarnedTime),
                        'date' => $date,
                    ];
                });
            });

        return $allAttendance;
    }

    public static function get_all_attendence_log($args)
    {
        $currentYear = Carbon::now()->year;

        $users = User::with([
            'policy' => function ($query) {
                $query->withTrashed(); // Include soft-deleted policies
            },
            'policy.working_settings', // Include related working settings of the policy
            'employee_details', // Include related employee details of the user
            'employee_details.designation' => function ($query) {
                $query->withTrashed(); // Include soft-deleted designations
            }
        ])
            ->orderBy('id', 'asc') // Optional: Order the results by ID
            ->get()
            ->keyBy('id'); // Index the collection by user_id
        $usersArray = $users->toArray();

        //dd($usersArray);
        $departments = Department::orderBy('id', 'asc')->pluck('name', 'id');
        $departmentsArray = $departments->toArray();

        // get arranged data
        $allAttendance = AttendenceHelper::get_log($args);

        $allAtendence = $allAttendance;
        if( !empty($_GET['dbg2']) && $_GET['dbg']==1 ) {            $emp_id = $args['emp_id'] ?? '';
            $attendanceObject = !empty($emp_id) ? $allAttendance[$emp_id] : $allAttendance;
            dd([$_GET['dbg'], $args, $attendanceObject??['no data for the user']]);
        }

        $manager_id = Auth::user()->id;
        //$policies_working_settings = \App\Helpers\PolicyHelper::get_timeout_policy();

        //reorganize attendance data
        $newAttendence = [];
        $skip_users = [4050, 3969, 3044, 515, 99, 1, 0, 1234, 1252, 2356, 2456, 2492, 2790, 3044, 3969, 4050, 9865];// Extra users
        foreach ($allAttendance as $this_user_id => $user_attendence) {
            //$new_keys[$this_user_id]= $this_user_id; continue;

            if(in_array($this_user_id, $skip_users) || empty($usersArray) || $this_user_id>99){
                continue;
            }

            $user_working_settings = $this_user['working_settings'] = $usersArray[$this_user_id]['policy'][0]['working_settings'] ?? [];
            $this_user['data'] = $usersArray[$this_user_id] ?? [];
            $this_user['designation'] = $this_user['data']['employee_details']['designation']['name'] ?? '';
            $department_id = $this_user['data']['employee_details']['designation']['department_id'] ?? 0;
            $this_user['department'] = $departments[$department_id] ?? '';
            $this_user['user_id'] = $this_user_id;
            $this_user['id'] = $this_user_id;

            if(empty($this_user['working_settings']['shift_start'])){
                dd(['empty policy', $this_user]);
            }
            foreach ($user_attendence as $new_date => $attendance){
                $newAttendence[$this_user_id][$new_date] = $attendance;
                $newAttendence[$this_user_id][$new_date]['user'] = $this_user;
                $newAttendence[$this_user_id][$new_date]['arrival_date'] = $new_date;
                $newAttendence[$this_user_id][$new_date]['leave_date'] = $new_date;
                $shift_start = $newAttendence[$this_user_id][$new_date]['shift_start'] = $this_user['working_settings']['shift_start'] ;
                $newAttendence[$this_user_id][$new_date]['shift_close'] = $this_user['working_settings']['shift_close'];

                $this_date_data = $newAttendence[$this_user_id][$new_date];
                $newAttendence[$this_user_id][$new_date]['logs'] = $attendance['attendance'];
                unset($newAttendence[$this_user_id][$new_date]['attendance']);
                $newAttendence[$this_user_id][$new_date]['device_logs'] = $attendance['device_logs'];

                if(!empty($newAttendence[$this_user_id][$new_date]['logs'])){
                    $this_date_data = [];
                    $newAttendence[$this_user_id][$new_date]['arrival_time'] = $newAttendence[$this_user_id][$new_date]['logs'][0]['arrival_time'];
                    $newAttendence[$this_user_id][$new_date]['leave_time'] = $newAttendence[$this_user_id][$new_date]['logs'][count($newAttendence[$this_user_id][$new_date]['logs'])-1]['leave_time'];
                    $newAttendence[$this_user_id][$new_date]['logs_count'] = count($newAttendence[$this_user_id][$new_date]['logs']);

                }

                // calculate gross time
                $start_time = $user_working_settings['shift_start'];
                $end_time = $user_working_settings['shift_close'];
                $gross_time = $newAttendence[$this_user_id][$new_date]['Gross_Hrs'] = DateHelper::differenceHoursMinutes2($start_time, $end_time);

                $late_time = $newAttendence[$this_user_id][$new_date]['late_time'] = DateHelper::calculateLateTime($newAttendence[$this_user_id][$new_date]['arrival_time'], $shift_start);
                $newAttendence[$this_user_id][$new_date]['weekday'] = $week_day = DateHelper::globaldateFormat('D', $new_date);
                $is_week_end = $newAttendence[$this_user_id][$new_date]['isWeekEnd'] = in_array($week_day, ['Sun', 'Sat']);

                $newAttendence[$this_user_id][$new_date]['status'] = $late_time > '00:15:00' ? 0 : (count($attendance['device_logs']) > 0 ? 1 : 3);
                if($is_week_end){
                    $newAttendence[$this_user_id][$new_date]['status'] = 2;
                }

                $earned_seconds = DateHelper::convert_time_to_seconds($attendance['earned_time']);
                $gross_seconds = DateHelper::convert_time_to_seconds($gross_time);
                $attendence_visual = (int)(($earned_seconds*100) / $gross_seconds );
                $newAttendence[$this_user_id][$new_date]['attendence_visual'] = $attendence_visual;

                // effective_time - new formula from logs
                $newAttendence[$this_user_id][$new_date]['effective_time'] = AttendenceHelper::getFormatedEffective_time($newAttendence[$this_user_id][$new_date]['logs']);

                // move two days entry to previous day
                if(!empty($previous_date) && !empty($current_date_logs['arrival_date'])){
                    $current_date_logs = $newAttendence[$this_user_id][$new_date]['logs'][0] ?? [];
                    if(!empty($current_date_logs) && $current_date_logs['arrival_date'] != $current_date_logs['leave_date']){
                        $newAttendence[$this_user_id][$current_date_logs['arrival_date']]['logs'][] = $current_date_logs;
                        unset($newAttendence[$this_user_id][$current_date_logs['leave_date']]['logs'][0]);
                        usort($newAttendence[$this_user_id][$current_date_logs['arrival_date']]['logs'], function($a, $b) { return strtotime($a['arrival_time']) - strtotime($b['arrival_time']); });
                        usort($newAttendence[$this_user_id][$current_date_logs['leave_date']]['logs'], function($a, $b) { return strtotime($a['arrival_time']) - strtotime($b['arrival_time']); });

                        //dd($newAttendence[$this_user_id][$current_date_logs['leave_date']]['logs']);
                        // recalculate times
                        $newAttendence[$this_user_id][$new_date]['effective_time'] = AttendenceHelper::getFormatedEffective_time($newAttendence[$this_user_id][$new_date]['logs']);
                        $newAttendence[$this_user_id][$previous_date]['effective_time'] = AttendenceHelper::getFormatedEffective_time($newAttendence[$this_user_id][$previous_date]['logs']);

                        $newAttendence[$this_user_id][$new_date]['earned_time2'] = AttendenceHelper::getEarnedTimeFromLogs($newAttendence[$this_user_id][$new_date]['logs']);
                        $newAttendence[$this_user_id][$previous_date]['earned_time2'] = AttendenceHelper::getEarnedTimeFromLogs($newAttendence[$this_user_id][$previous_date]['logs']);

                    }
                }
                $previous_date = $new_date;

            }// end of date loop

        }// end of user loop
        //ksort($new_keys);
        //dd($new_keys);

        ksort($newAttendence);
        if( !empty($_GET['dbg']) && $_GET['dbg']==2 ) {
            $emp_id = $args['emp_id'] ?? '';
            $attendanceObject = !empty($emp_id) ? $newAttendence[$emp_id] : $newAttendence;
            dd([$_GET['dbg'], $args, $attendanceObject??['no data for the user']]);
        }


        // add missing dates
        foreach ($newAttendence as $user_id => $attendance) {
            // get date range
            $today = Carbon::now()->format('Y-m-d');
            $dates = array_keys($attendance) ;
            $end_date = !empty($args['to_date']) ? $args['to_date'] : $today ; //$dates[sizeof($dates) - 1];
            $start_date = !empty($args['from_date']) ? $args['from_date'] : $dates[0];
            // $s_date_arr = explode('/',$start_date);
            // $start_date = $s_date_arr[2].'-'.$s_date_arr[0].'-'.$s_date_arr[1];
            // echo "end_date=".$end_date;
            // echo "<br>";
            // echo "start_date=".$start_date;
            // die();
            $first_date = $dates[0];
            $date_range = DateHelper::getDatesRange($start_date, $end_date);
            ksort($date_range);

            //dd([$start_date, $end_date, $date_range]);

            // date range for employee
            ksort($attendance);
            $users[$user_id] = $user_id;

            $previous_date = '';
            foreach ($date_range as $current_date) {
                if (!empty($attendance) && empty($attendance[$current_date])) {
                    $new_attendance = collect($attendance)->first();
                    $new_attendance['arrival_time'] = Carbon::createFromFormat('H:i A', $new_attendance['shift_start'])->addHours(4)->format('H:i:s');
                    $new_attendance['leave_time'] = $new_attendance['arrival_time'];
                    $new_attendance['a_date'] = DateHelper::globaldateFormat('j M Y', $current_date);
                    $new_attendance['leave_date'] = $current_date;
                    $new_attendance['arrival_date'] = $current_date;
                    $new_attendance['earned_time'] = '';
                    $new_attendance['effective_time'] = '';
                    $new_attendance['late_time'] = '';
                    $new_attendance['logs'] = [];
                    $new_attendance['logs_count'] = 0;
                    $new_attendance['device_logs'] = [];
                    $new_attendance['attendence_visual'] = 0;
                    $new_attendance['status'] = DateHelper::isWeekend($current_date) ? 2 : 3;
                    $week_day = $new_attendance['weekday'] =  DateHelper::globaldateFormat('D', $current_date);
                    $new_attendance['isWeekEnd'] = in_array($week_day, ['Sun', 'Sat']);

                    // load missing date attendance
                    $newAttendence[$user_id][$current_date] = $new_attendance;
                    //dd($new_attendance);
                }else{
                    // calculate gross time
                    $start_time = $user_working_settings['shift_start'];
                    $end_time = $user_working_settings['shift_close'];
                    $gross_time = $newAttendence[$user_id][$current_date]['Gross_Hrs'] = DateHelper::differenceHoursMinutes2($start_time, $end_time);
                }

            }// end of dates loop
            // sort by date in Y-m-d for a user_id
            ksort($newAttendence[$user_id]);
        } // end of users loop
        // sort by user_id
        ksort($newAttendence);

        if( !empty($_GET['dbg']) && $_GET['dbg']==3 ) {
            $emp_id = $args['emp_id'] ?? '';
            $attendanceObject = !empty($emp_id) ? $newAttendence[$emp_id] : $newAttendence;
            dd([$_GET['dbg'], $args, $attendanceObject??['no data for the user']]);
        }

        //dd($newAttendence);
        return $newAttendence;
    }
    public static function getEarnedTimeFromLogs($attendance_logs = []){
        $total_earned_time = 0;
        foreach ($attendance_logs as &$log) {
            $arrival_time = strtotime($log['arrival_time']);
            $leave_time = strtotime($log['leave_time']);
            $earned_seconds = $leave_time - $arrival_time;
            $log['earned_time'] = gmdate("H:i:s", $earned_seconds);
            $total_earned_time += $earned_seconds;
        }

        $total_earned_time_formatted = gmdate("H:i:s", $total_earned_time);

        return $total_earned_time_formatted ?? '00:00:00';
    }
    public static function getFormatedEffective_time($logs = [])
    {
        if (empty($logs)) {
            return '00:00:00';
        }

        $last_log = end($logs);
        $first_log = reset($logs);

        $arrival_date = $first_log['arrival_date'] ?? '';
        $leave_date = $last_log['leave_date'] ?? '';
        $arrival_time = $first_log['arrival_time'] ?? '';
        $leave_time = $last_log['leave_time'] ?? '';

        $datetime1 = Carbon::parse($arrival_time);
        $datetime2 = Carbon::parse($leave_time);

        // Calculate the total seconds between the two datetimes
        $totalSeconds = $datetime2->diffInSeconds($datetime1);

        // Convert seconds to hours, minutes, and seconds
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

//        if($leave_date == "2024-09-04" ) {
//            dd([$arrival_date, $arrival_time, $leave_time, $last_log, $first_log, $datetime1, $datetime2, $hours, $minutes, $seconds]);
//        }
        // Format as HH:MM:SS
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    public static function getFormatedEffective_timeold($logs = [])
    {
        if(empty($logs)){
            return '00:00:00';
        }
        $last_log = end($logs);
        $first_log = reset($logs);

        $arrival_date = $first_log['arrival_date'] ?? '';
        $leave_date = $last_log['leave_date'] ?? '';
        $arrival_time = $first_log['arrival_time'] ?? '';
        $leave_time = $last_log['leave_time'] ?? '';

        $datetime1 = Carbon::parse($arrival_time);
        $datetime2 = Carbon::parse($leave_time);

        //dd([$logs, $datetime1, $datetime2, $first_log, $last_log]);

        $interval = $datetime1->diff($datetime2);
        if($leave_date == "2024-09-04" ){
            dd([$arrival_date, $arrival_time, $leave_time, $last_log, $first_log, $datetime1, $datetime2,  $interval->format('%H:%I:%S')]);
            dd([$first_log, $last_log]);
        }

        // Format the interval as HH:MM:SS
        return $interval->format('%H:%I:%S');
    }
    public static function getAttendenceLabel(string $value): string
    {
        $enum = AttendenceEnum::from($value);

        // Using match expression (PHP 8+)
        return match ($enum) {
            AttendenceEnum::Late => 'Late',
            AttendenceEnum::OnTime => 'On Time',
            AttendenceEnum::Holiday => 'Holiday',
            AttendenceEnum::Absent => 'Absent',
            AttendenceEnum::Leave => 'Leave',
        };
    }
    public static function sortLogRows($logRows){
        $new_logs = [];
        foreach ($logRows as $k => $log_row){
            $current_date = $log_row['arrival_date'];
            $arrival_time = Carbon::parse($log_row['arrival_time'])->format('Y-m-d H:i:s');
            $new_logs[$current_date][$arrival_time] = $log_row;
            ksort($new_logs[$current_date]);
        }
        return array_values($new_logs[$current_date]);
        //$newAttendence[$attendence->user_id][$current_date]['logs'] = array_values($new_logs[$current_date]);
    }
    public static function getDesignations(){
        $designations = Designation::with([ 'department' => function ($query) { $query->withTrashed(); } ])->get();
        $final_list = [];
        foreach ($designations as $k => $designation){
            $final_list[$designation->id] = [ 'designation' => $designation->name, 'department' => $designation->department->name ];
        }
        return $final_list;
    }
    private static function get_timeout( $policy_id, $start_time) {
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

    public static function calculateAttendancePercentage($startShiftTime, $endShiftTime, $actualAttendanceHours)
    {
        // Parse time strings using Carbon
        $startTime = Carbon::createFromFormat('H:i:s', $startShiftTime);
        $endTime = Carbon::createFromFormat('H:i:s', $endShiftTime);

        // Calculate total working hours using Carbon methods
        $totalWorkingHours = $endTime->diffInHours($startTime);

        // Calculate attendance percentage
        $attendancePercentage = ($actualAttendanceHours / $totalWorkingHours) * 100;

        return number_format($attendancePercentage, 2); // Limiting the decimal places to two
    }

    public static function attendenceStatus()
    {
        return [
            '0' => 'Late',
            '1' => 'On Time',
            '2' => 'Holiday',
            '3' => 'Absent',
            '4' => 'Leave'
        ];
    }

    public static function getattendenceStatusName($index)
    {
        $ary = static::attendenceStatus();
        return $ary[$index];
    }
    public static function isItWeekEnd($day_of_week) {
        return in_array($day_of_week, AttendenceHelper::getWeekEndDays());
    }
    public static function getWeekEndDays() {
        return [
            'Sat',
            'Sun'
        ];
    }
    public static function getShortWeekDays() {
        return [
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
            'Sun',
        ];
    }
    public static function getWeekDays() {
        return [
            'Monday',
            'Tuesday',
            'Wednessday',
            'Thursday',
            'Friday',
            'Satday',
            'Sunday',
        ];
    }

    public static function getTimeOut( $policy_id, $start_time) {
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

        $utcTimestamp = Carbon::parse($start_time)->addMinutes($multiplier*60);

        return $utcTimestamp;
    }
}
