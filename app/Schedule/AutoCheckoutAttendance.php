<?php

namespace App\Schedule;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\Attendence;
use App\Enums\AttendenceEnum;
use App\Helpers\DateHelper;
use App\Helpers\LoggingHelper;

class AutoCheckoutAttendance {
    public static function auto_checkout()
    {
        // LoggingHelper::log('auto_checkout', 'Auto checkout is running');
        /**
         *
         * Get All the Attendence where leave_time is null
         *
         * Then run the loop and check the current time with the policy time
         */
        $attendances = Attendence::whereNull('leave_time')->with(['policy'])->get();

        foreach ($attendances as $attendance) {
            /**
             *
             * get the policy end time from the working settings
             *
             * if the current time is greater than the policy end time then
             *
             *
             */
            $policy_start_time = Carbon::createFromFormat('h:i A', $attendance->policy->working_settings->shift_start, 'America/New_York');
            $policy_end_time = Carbon::createFromFormat('h:i A', $attendance->policy->working_settings->shift_close, 'America/New_York');

            // dd($policy_start_time, $policy_end_time);
            $now_time = Carbon::now('America/New_York');

            /**
             * first the check the esstimated leave date with the help of arrival date and policy time
             *
             * Right now this logic is only for the am to pm
             */

            // dd($policy_start_time->format('A'));
            if ($policy_start_time->format('A') == 'AM' && $policy_end_time->format('A') == 'PM') {
                $estimated_leave_date = self::getEstimatedLeaveDate($now_time, $policy_end_time, $attendance->arrival_date);

                /**
                 *
                 * In this just need to check the time is greater than the policy end time
                 */

                $currentDate = Carbon::now('America/New_York')->startOfDay();
                // dd($attendance->arrival_date);
                $attendanceDate = Carbon::createFromDate($attendance->arrival_date)->setTimezone('America/New_York')->startOfDay();

                // dd($currentDate, $attendanceDate);
                if ($currentDate->gte($attendanceDate)) {
                    // dd("s", $attendanceDate, $currentDate);


                    /**
                     *
                     * Now check the time of the day
                     *
                     * if the current time is greater than the policy end time then
                     */

                    $policy_end_time = $policy_end_time->setDate($currentDate->year, $currentDate->month, $currentDate->day);
                    // dd($now_time, $policy_end_time);
                    // echo "<pre>";
                    // print_r($attendance->toArray());
                    // echo "</pre>";
                    if ($now_time->gte($policy_end_time)) {
                        // dd($now_time, $policy_end_time);

                        /**
                         * Now update the leave time
                         *
                         * According to the policy settings
                         */

                        $force_timeout = $attendance->policy->working_settings->force_timeout;
                        // dd($force_timeout);

                        /**
                         *
                         * force timout that is mentioned in the policy hourly base
                         *
                         * Right now force timeout is 1 hours
                         *
                         * need to fix the dyanmically
                         */

                        // $policy_end_time_2 = Carbon::now('America/New_York')->subHours(3);
                        $difference = DateHelper::globaldifferenceHours($policy_end_time, $now_time);

                        // dd($difference, $policy_end_time, $now_time, $policy_end_time);
                        if ($difference >= 2) {
                            // $leave_time = $policy_end_time->addHours($force_timeout);

                            /**
                             *
                             * Check Timeout policy and then update the leave time
                             *
                             */

                            $timeout_policy = $attendance->policy->working_settings->timeout_policy;

                            if ($timeout_policy == '1') {
                                /**
                                 *
                                 * Mark it as present
                                 *
                                 */
                                echo "present <br />";
                            }

                            if ($timeout_policy == '2') {
                                /**
                                 *
                                 * Mark it as absent
                                 *
                                 */
                                echo "Absent <br />";
                            }

                            if ($timeout_policy == '3') {
                                /**
                                 *
                                 * Deduct half day from the attendance time
                                 *
                                 */
                                echo "Half Day <br />";
                                // echo "<pre>";
                                // print_r($attendance->toArray());
                                // print_r($attendance->user->toArray());
                                // echo "</pre>";

                                /**
                                 *
                                 * Get First mark attendance of the day
                                 */

                                // Right Just Checkout it regularly
                                // dd($policy_end_time);
                                $attendance->leave_time = $policy_end_time;
                                $attendance->leave_date = $policy_end_time->format('Y-m-d');
                                $attendance->save();
                                LoggingHelper::log('auto_checkout_half', $attendance->toArray());
                            }

                            if ($timeout_policy == '4') {
                                /**
                                 *
                                 * Deduct one hour from the attendance time
                                 *
                                 */
                                echo "One Hour <br />";
                            }

                        }
                    }

                    // dd("work");
                } else {
                    dd("s2", $attendanceDate, $currentDate);
                }
            }
        }

    }

    public static function getEstimatedLeaveDate($now_time, $policy_end_time, $arrival_date)
    {
        $estimated_leave_date = Carbon::createFromFormat('Y-m-d', $arrival_date, 'America/New_York')->setTime($policy_end_time->hour, $policy_end_time->minute, $policy_end_time->second);
        return $estimated_leave_date;
    }
}