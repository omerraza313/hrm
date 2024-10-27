<?php

namespace App\Schedule;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ApiLog;
use App\Models\Salary;
use App\Enums\RolesEnum;
use App\Models\Attendence;
use App\Models\AbsentQueue;
use App\Enums\AttendenceEnum;
use App\Helpers\LoggingHelper;
use App\Enums\ApprovedStatusEnum;
use App\Helpers\AttendanceLogging;

class AttendanceSchedule {
    public static function markAttendance()
    {
        try {
            $roles = [RolesEnum::Employee, RolesEnum::Manager];
            $users = User::Role($roles)->whereHas('policy')->with([
                'employee_details',
                'policy' => function ($query) {
                    $query->where('status', 1);
                },
                'policy.working_settings',
                'applyLeaves' => function ($query) {
                    $currentDate = Carbon::now()->format('d/m/Y');
                    $query->where('leave_from', '<=', $currentDate)
                        ->where('leave_upto', '>=', $currentDate);
                },
                'policy.working_day' => function ($query) {
                    $currentDate = Carbon::now()->format('N');
                    $query->where('active', '0')->where('day', $currentDate);
                },
                'employee_details'
            ])->get();

            $now_time = Carbon::now()->timezone('America/New_York');
            // dd($now_time);
            foreach ($users as $user) {
                // if ($user->id == 3) {
                $newNowDate = Carbon::now()->format('Y-m-d');
                $attendences = Attendence::where('user_id', $user->id)->whereDate('arrival_date', $newNowDate)->get();
                // dd($now_time);
                // dd($attendences->toArray());
                ApiLog::create([
                    'method' => "Attendance 1",
                    'path' => "Attendance 1",
                    'ip' => "Attendance 1",
                    'data' => json_encode($attendences->toArray()),
                ]);
                if (true) {
                    $attendance_collection = collect($attendences->toArray());
                    // dd($attendance_collection);
                    ApiLog::create([
                        'method' => "Attendance 2",
                        'path' => "Attendance 2",
                        'ip' => "Attendance 2",
                        'data' => json_encode($attendance_collection->toArray()),
                    ]);
                    if (
                        $attendance_collection->contains(function ($item) use ($now_time) {
                            return $item['arrival_date'] === $now_time->format('Y-m-d');
                        })
                    ) {
                        // The date exists in at least one arrival_time field
                        // dd("Date exists in at least one arrival_time field");
                        // echo "Date exists in at least one arrival_time field.\n";

                        ApiLog::create([
                            'method' => "Date exists in at least one arrival_time field",
                            'path' => "Date exists in at least one arrival_time field",
                            'ip' => "Date exists in at least one arrival_time field",
                            'data' => "Date exists in at least one arrival_time field",
                        ]);
                    } else {
                        // The date does not exist in any arrival_time field
                        // dd("Date does not exist in any arrival_time field");
                        // echo "Date does not exist in any arrival_time field.\n";
                        // dd($user->policy[0]->working_settings->shift_close);

                        ApiLog::create([
                            'method' => "Date does not exist in any arrival_time field.",
                            'path' => "Date does not exist in any arrival_time field.",
                            'ip' => "Date does not exist in any arrival_time field.",
                            'data' => "Date does not exist in any arrival_time field.",
                        ]);
                        $policy_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_close, 'America/New_York');
                        $policy_start_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_start, 'America/New_York');

                        // dd($policy_time, $now_time);
                        Salary::create([
                            'date' => $now_time->utc()->format('Y-m-d'),
                            'salary' => $user->employee_details->salary,
                            'user_id' => $user->id,
                        ]);


                        if (count($user->toArray()['policy'][0]['working_day']) > 0) {
                            $holidayAttend = Attendence::create([
                                'user_id' => $user->id,
                                'arrival_time' => $now_time->utc(),
                                'leave_time' => $now_time->utc(),
                                'policy_id' => $user->policy[0]->id,
                                'status' => AttendenceEnum::Holiday,
                                'arrival_date' => $now_time->utc()->format('Y-m-d'),
                                'leave_date' => $now_time->utc()->format('Y-m-d'),
                            ]);

                            AttendanceLogging::log('Job Holiday Attendance', $holidayAttend->toArray());
//                            AttendanceLogging::log('Job Holiday Employee', $user->toArray());

                            // dd($policy_time, $now_time);

                            ApiLog::create([
                                'method' => "Holiday",
                                'path' => "Holiday",
                                'ip' => "Holiday",
                                'data' => json_encode($user),
                            ]);
                        } else {

                            // For Leave
                            if (count($user->toArray()['apply_leaves']) > 0) {
                                $leaveAttend = Attendence::create([
                                    'user_id' => $user->id,
                                    'arrival_time' => $now_time->utc(),
                                    'leave_time' => $now_time->utc(),
                                    'policy_id' => $user->policy[0]->id,
                                    'status' => AttendenceEnum::Leave,
                                    'arrival_date' => $now_time->utc()->format('Y-m-d'),
                                    'leave_date' => $now_time->utc()->format('Y-m-d'),
                                ]);

                                AttendanceLogging::log('Job Leave Attendance', $leaveAttend->toArray());
                                AttendanceLogging::log('Job Leave Employee', $user->toArray());

                                // dd($policy_time, $now_time);
                                ApiLog::create([
                                    'method' => "Leave",
                                    'path' => "Leave",
                                    'ip' => "Leave",
                                    'data' => json_encode($user),
                                ]);
                            } else {

                                // For Absent mark
                                if ($now_time->timezone('America/New_York')->greaterThan($policy_time)) {
                                    $absendAttend = Attendence::create([
                                        'user_id' => $user->id,
                                        'arrival_time' => $now_time->utc(),
                                        'leave_time' => $now_time->utc(),
                                        'policy_id' => $user->policy[0]->id,
                                        'status' => AttendenceEnum::Absent,
                                        'arrival_date' => $now_time->utc()->format('Y-m-d'),
                                        'leave_date' => $now_time->utc()->format('Y-m-d'),
                                    ]);

                                    AttendanceLogging::log('Job Absent Attendance', $absendAttend->toArray());
                                    AttendanceLogging::log('Job Absent Employee', $user->toArray());
                                    // dd($policy_time, $now_time);

                                    ApiLog::create([
                                        'method' => "Absent",
                                        'path' => "Absent",
                                        'ip' => "Absent",
                                        'data' => json_encode($user),
                                    ]);
                                }
                                // dd("sec", $policy_time, $now_time, $now_time->timezone('America/New_York')->greaterThan($policy_time));
                            }
                        }
                    }
                    // dd($user, $attendences);
                    ApiLog::create([
                        'method' => "Nothing",
                        'path' => "Nothing",
                        'ip' => "Nothing",
                        'data' => "Nothing",
                    ]);
                }
                // }

            }

            // dd($users->toArray());
            ApiLog::create([
                'method' => "Nothing 2",
                'path' => "Nothing 2",
                'ip' => "Nothing 2",
                'data' => json_encode($users->toArray()),
            ]);
        } catch (Exception $e) {
            //throw $th;
            // dd($e->getMessage(), $e->getCode(), $e->getLine());
            ApiLog::create([
                'method' => "Error",
                'path' => $e->getMessage(),
                'ip' => $e->getCode(),
                'data' => $e->getLine(),
            ]);
        }
    }

    public static function attendance_marking()
    {
        /**
         *
         *
         * Get All the Users First
         *
         */

        $users = User::Role([RolesEnum::Employee, RolesEnum::Manager])->whereHas('policy', function ($query) {
            $query->where('status', 1);
        })->with([
                    'employee_details',
                    'attendences',
                    'applyLeaves',
                    'policy' => function ($query) {
                        $query->where('status', 1);
                    }
                ])->get();
        // dd($users->toArray());
        foreach ($users as $user) {
            /**
             *
             * This logic is for the leave checking only
             *
             */
            static::mark_leave($user);

            /**
             *
             * This logic is for the Holiday Checking Only
             *
             */
            static::mark_holiday($user);

            /**
             *
             * This logic is for the Absent Checking Only
             *
             */
            static::mark_absent($user);
        }
    }

    private static function mark_leave($user)
    {
        $leaves = $user->applyLeaves;
        foreach ($leaves as $leave) {

            /**
             *
             *  Check if the leave is approved or not
             *
             */
            // LoggingHelper::log('Leave Status', $leave->status);
            if ($leave->status == ApprovedStatusEnum::Approved->value) {
                /**
                 *
                 * Get the Dates of the leave that is applied
                 *
                 * Check if the current date is between the leave date
                 *
                 * This Variable is only in this section
                 *
                 */
                $from_date_only = Carbon::createFromFormat('d/m/Y', $leave->leave_from)->startOfDay();
                $to_date_only = Carbon::createFromFormat('d/m/Y', $leave->leave_upto)->startOfDay();
                $now_date_only = Carbon::now()->startOfDay();

                // LoggingHelper::log('from_date_only', $from_date_only);
                // LoggingHelper::log('to_date_only', $to_date_only);
                // LoggingHelper::log('now_date_only', $now_date_only);
                // LoggingHelper::log('condition', $now_date_only->between($from_date_only, $to_date_only, true));
                // dd($now_date_only->format('Y-m-d'), $from_date_only, $to_date_only);


                $now_date_only->between($from_date_only, $to_date_only, true) == true;
                if ($now_date_only->between($from_date_only, $to_date_only, true) == true) {

                    /**
                     *
                     * Now we have to check if the user is marked or not
                     *
                     * If the user is not marked then we have to mark the user as leave
                     *
                     */
                    $now_date = Carbon::now()->setTimezone('America/New_York');
                    // dd($now_date->format('Y-m-d'), $user);
                    $attendences_count = count($user->attendences->where('arrival_date', $now_date->format('Y-m-d')));
                    // LoggingHelper::log('User', $user);
                    // LoggingHelper::log('Attendence Count', $attendences_count);

                    // $attendences_count == 0
                    if ($attendences_count == 0) {
                        /**
                         *
                         * Now we have to mark the user as leave
                         *
                         * But with start time and end time of the policy
                         *
                         */

                        $policy_start_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_start, 'America/New_York');
                        $policy_end_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_close, 'America/New_York');
                        // $policy_start_time = $user->policy[0]->working_settings->shift_start;
                        // $policy_end_time = $user->policy[0]->working_settings->shift_close;

                        // dd($policy_start_time, $policy_end_time);
                        $attendence = Attendence::create([
                            'user_id' => $user->id,
                            'arrival_time' => $policy_start_time,
                            'leave_time' => $policy_start_time,
                            'policy_id' => $user->policy[0]->id,
                            'status' => AttendenceEnum::Leave,
                            'arrival_date' => $now_date->format('Y-m-d'),
                            'leave_date' => $now_date->format('Y-m-d'),
                        ]);

                        LoggingHelper::log('Job Leave Attendance', $attendence->toArray());
                    }
                }
            }
        }
    }

    private static function mark_holiday($user)
    {
        /**
         *
         * Get the holidays of the user
         *
         */

        $holidays = $user->policy[0]->working_day->where('active', 0);
        // dd($holidays->toArray());

        foreach ($holidays as $holiday) {

            /**
             *
             * Check if holdays are same or not
             *
             */
            $now_date_only = Carbon::now()->setTimezone('America/New_York');
            $now_day = $now_date_only->format('N');
            // dd($holiday->day, $now_day);
            if ($holiday->day == $now_day) {
                /**
                 *
                 * Now we have to check if the user is marked or not
                 *
                 * If the user is not marked then we have to mark the user as Holiday
                 *
                 */
                $now_date = Carbon::now()->setTimezone('America/New_York');
                // dd($now_date->format('Y-m-d'), $user);
                $attendences_count = count($user->attendences->where('arrival_date', $now_date->format('Y-m-d')));
                // LoggingHelper::log('User', $user);
                // LoggingHelper::log('Attendence Count', $attendences_count);
                if ($attendences_count == 0) {
                    /**
                     *
                     * Now we have to mark the user as Holiday
                     *
                     * But with start time and end time of the policy
                     *
                     */

                    $policy_start_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_start, 'America/New_York');
                    $policy_end_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_close, 'America/New_York');

                    $attendence = Attendence::create([
                        'user_id' => $user->id,
                        'arrival_time' => $policy_start_time,
                        'leave_time' => $policy_start_time,
                        'policy_id' => $user->policy[0]->id,
                        'status' => AttendenceEnum::Holiday->value,
                        'arrival_date' => $now_date->format('Y-m-d'),
                        'leave_date' => $now_date->format('Y-m-d'),
                    ]);

                    LoggingHelper::log('Job Holiday Attendance', $attendence->toArray());
                }
            }
        }
    }

    private static function mark_absent($user)
    {
        /**
         * Check Queue for Absent
         */

        $absent_queues = AbsentQueue::where('user_id', $user->id)->where('status', 'pending')->get();
        if ($absent_queues->count() > 0) {

            /**
             *
             * Logic for if the queue is pending
             */

            foreach ($absent_queues as $absent_queue) {
                /**
                 *
                 * Get the absent queue date only
                 *
                 */
                $absent_queue_date_only = Carbon::createFromFormat('Y-m-d', $absent_queue->date);
                $year_only = $absent_queue_date_only->format('Y');
                $month_only = $absent_queue_date_only->format('m');
                $day_only = $absent_queue_date_only->format('d');
                $now_date_only = Carbon::now()->setDate($year_only, $month_only, $day_only);
                $policy_end_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_close);
                // if ($user->id == 7) {
                // dd($policy_end_time, $now_date_only);
                // }

                if ($now_date_only->greaterThan($policy_end_time)) {
                    /**
                     *
                     * Now we have to check if the user is marked or not
                     *
                     * If the user is not marked then we have to mark the user as Absent
                     *
                     */
                    $now_date = Carbon::now()->setDate($year_only, $month_only, $day_only)->setTimezone('America/New_York');
                    // dd($now_date->format('Y-m-d'), $user);
                    $attendences_count = count($user->attendences->where('arrival_date', $now_date->format('Y-m-d')));
                    // dd($attendences_count, $user->toArray());
                    // LoggingHelper::log('User', $user);
                    // LoggingHelper::log('Attendence Count', $attendences_count);
                    if ($attendences_count == 0) {
                        /**
                         *
                         * Now we have to mark the user as Absent
                         *
                         * But with start time and end time of the policy
                         *
                         */

                        $policy_start_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_start, 'America/New_York');
                        // $policy_end_time = Carbon::createFromFormat('h:i A', $user->policy[0]->working_settings->shift_close);

                        $attendence = Attendence::create([
                            'user_id' => $user->id,
                            'arrival_time' => $policy_start_time,
                            'leave_time' => $policy_start_time,
                            'policy_id' => $user->policy[0]->id,
                            'status' => AttendenceEnum::Absent->value,
                            'arrival_date' => $now_date->format('Y-m-d'),
                            'leave_date' => $now_date->format('Y-m-d'),
                        ]);

                        LoggingHelper::log('Job Absent Attendance', $attendence->toArray());
                        $absent_queue->status = 'marked';
                        $absent_queue->save();
                    } else {
                        $absent_queue->status = 'exist';
                        $absent_queue->save();
                    }
                }
            }

        } else {

            /**
             *
             * Logic for if the queue is not pending
             */

            $queues = AbsentQueue::where('user_id', $user->id)->where('date', Carbon::now()->format('Y-m-d'))->get();
            if ($queues->count() == 0) {
                /**
                 *
                 * If the queue is not present then we have to create the queue
                 *
                 */
                AbsentQueue::create([
                    'user_id' => $user->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'status' => 'pending'
                ]);
            }
        }
    }
}
