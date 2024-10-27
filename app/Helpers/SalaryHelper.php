<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\User;

class SalaryHelper {
    public static function calculate_salary($employee, $month = NULL, $year = NULL)
    {
        if (is_null($month)) {
            $month = Carbon::now()->format('m');
        }

        if (is_null($year)) {
            $year = Carbon::now()->format('Y');
        }
        /**
         *
         * First Get the Salary of the Employee
         *
         */
        $salary = $employee->employee_details->salary;


        /**
         *
         * Then Get the Attendence of the Employee
         *
         */

        $attendences = $employee->attendences;

        /**
         *
         *
         * Then Get the Total Working Days of the Month
         *
         */

        $total_working_days = self::get_total_working_days_of_month($employee, $month, $year);

        $salary_per_sec = self::earningsPerSecond($employee, $salary, $total_working_days);



        $attendences_log = $attendences->groupBy('arrival_date')->toArray();
        // dd($earn_per_sec, $salary, $attendences->groupBy('arrival_date')->toArray(), $total_working_days);

        /**
         *
         * Now we have to calculate the salary
         *
         */

        $total_earn_hours = 0;
        foreach ($attendences_log as $attendance_dates) {
            foreach ($attendance_dates as $att) {
                $hours = DateHelper::globaldifferenceHours($att['arrival_time'], $att['leave_time']);
                $total_earn_hours += $hours;
            }
        }



        // Calculate total earned time in seconds
        $totalEarnedTimeSeconds = $total_earn_hours * 3600; // Convert hours to seconds


        // Calculate total earned salary
        $totalEarnedSalary = $salary_per_sec * $totalEarnedTimeSeconds;

        // Add the total earned salary to the total salary
        $monthlySalary = $totalEarnedSalary;

        // dd($monthlySalary, $totalEarnedSalary, $totalEarnedTimeSeconds, $total_earn_hours, $attendences_log, $total_working_days, $salary_per_sec, $attendences, $salary, $employee, $month, $year);
        return $monthlySalary;
    }

    private static function get_total_working_days_of_month($employee, $month, $year)
    {
        /**
         *
         * Get the policy of the employee
         *
         */
        $user = User::whereId($employee->id)->with([
            'policy' => function ($query) {
                $query->where('status', 1)->latest();
            },
            'policy.working_day' => function ($query) {
                $query->where('active', 1);
            }
        ])->first();

        if (count($user->policy) > 0) {
            $policy = $user->policy[0];

            /**
             *
             * Get the total working days of the month and year
             *
             */
            $working_days_array = $policy->working_day->pluck('day')->toArray();

            // Create a Carbon instance for the specified month and year
            $currentMonth = Carbon::createFromFormat('Y-m-d', "$year-$month-01")->startOfMonth();

            // Initialize a counter for the total number of days
            $totalDaysInMonth = 0;

            // Loop through each day of the month
            for ($day = 1; $day <= $currentMonth->daysInMonth; $day++) {
                // Create a Carbon instance for the current day
                $currentDay = $currentMonth->copy()->day($day);

                // Check if the current day's integer representation is in your array
                if (in_array($currentDay->dayOfWeekIso, $working_days_array)) {
                    $totalDaysInMonth++;
                }
            }

            return $totalDaysInMonth;
        }


        return 0;
    }

    private static function earningsPerSecond($employee, $monthlySalary, $totalWorkingDays)
    {
        // Assuming hourlyWage is provided in currency per hour
        $hourlyWage = self::calculateHourlyWage($employee, $monthlySalary, $totalWorkingDays);

        if ($hourlyWage > 0) {

            // Number of seconds in an hour
            $secondsInAnHour = 3600; // 60 seconds * 60 minutes

            // Calculate earnings per second
            $earningsPerSecond = $hourlyWage / $secondsInAnHour;

            return $earningsPerSecond;
        }

        return 0;
    }


    private static function calculateHourlyWage($employee, $monthlySalary, $totalWorkingDays)
    {
        // Assuming 8 hours of work per day
        $hoursPerDay = self::calculate_working_hours_daily($employee);

        if ($hoursPerDay > 0) {
            // Calculate the total number of hours worked in the month
            $totalHoursWorked = $totalWorkingDays * $hoursPerDay;

            // Calculate the hourly wage
            $hourlyWage = $monthlySalary / $totalHoursWorked;

            return $hourlyWage;
        }

        return 0;
    }

    private static function calculate_working_hours_daily($employee)
    {
        /**
         *
         * Get the policy of the employee
         *
         */
        $user = User::whereId($employee->id)->with([
            'policy' => function ($query) {
                $query->where('status', 1)->latest();
            },
            'policy.working_day' => function ($query) {
                $query->where('active', 1);
            }
        ])->first();

        if (count($user->policy) > 0) {
            $policy = $user->policy[0];

            /**
             *
             *
             * Get the start and close shift
             *
             */

            $start_shift = $policy->working_settings->shift_start;
            $close_shift = $policy->working_settings->shift_close;

            /**
             *
             * Get the total working hours of the day
             *
             */

            $total_working_hours = DateHelper::globaldifferenceHours($start_shift, $close_shift);
            return $total_working_hours;
        }

        return 0;
    }
}