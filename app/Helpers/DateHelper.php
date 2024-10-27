<?php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;

class DateHelper {
    public static function getMonths(): array
    {
        return [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        ];
    }

    public static function calculateNumberOfDays($fromDate, $toDate)
    {
        $fromDate = Carbon::createFromFormat('d/m/Y', $fromDate);
        $toDate = Carbon::createFromFormat('d/m/Y', $toDate);

        // Calculate the difference in days (including the first date)
        $numberOfDays = $toDate->diffInDays($fromDate) + 1;

        return $numberOfDays;
    }

    public static function dateFormat($format = 'M j Y', $date)
    {
        $date = Carbon::createFromFormat('m/d/Y', $date);
        return $date->format($format);
    }

    public static function globaldateFormat($format = 'j M Y', $date)
    {
        $date = Carbon::parse($date);
        return $date->format($format);
    }

    public static function differenceHours($start, $close)
    {

        $carbonTime1 = Carbon::createFromFormat('h:i A', $start);
        $carbonTime2 = Carbon::createFromFormat('h:i A', $close);

        $hoursDifference = $carbonTime1->diffInHours($carbonTime2);
        $minutesDifference = $carbonTime1->diffInMinutes($carbonTime2) % 60;

        return "$hoursDifference Hrs $minutesDifference mins";
    }
    public static function calculateLateTime($arrival_time, $shift_start)
    {
        // Parse times using Carbon
        $time1 = Carbon::parse($shift_start)->format('H:i:s');
        $time2 = Carbon::parse($arrival_time)->format('H:i:s');

        $time1 = Carbon::createFromFormat('H:i:s', $time1);
        $time2 = Carbon::createFromFormat('H:i:s', $time2);

        // Check if arrival is after shift start
        if ($time2->greaterThan($time1) ) {
            // Calculate late time and return formatted string
            $diffInSeconds = $time2->diffInSeconds($time1);
            $hours = floor($diffInSeconds / 3600);
            $minutes = floor(($diffInSeconds % 3600) / 60);
            $seconds = $diffInSeconds % 60;

            $late_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            return $late_time > '00:15:00' ? $late_time : '';
        }

        // Return null if not late
        return '';
    }
    public static function convert_time_to_seconds($time_string) {
        // Split the time string into hours, minutes, and seconds
        list($hours, $minutes, $seconds) = explode(':', $time_string);

        // Convert each component to seconds and add them together
        $total_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        return $total_seconds;
    }
    public static function calculateTimePercentage($earned_time, $gross_hours) {
        // Convert to seconds
        $earnedSeconds = Carbon::createFromFormat('H:i:s', $earned_time)->diffInSeconds(Carbon::now());
        $grossSeconds = Carbon::createFromFormat('H:i:s', $gross_hours)->diffInSeconds(Carbon::now());

        // Calculate percentage, ensuring it doesn't exceed 100%
        $percentage = min(($earnedSeconds / $grossSeconds) * 100, 100);

        // Format the percentage
        return number_format($percentage, 0, '.', '');
    }
    public static function differenceHoursMinutes2($start, $close)
    {
        $carbonTime1 = Carbon::createFromFormat('H:i A', $start);
        $carbonTime2 = Carbon::createFromFormat('H:i A', $close);

        // Handle cases where end time is before start time (e.g., overnight shifts)
        if ($carbonTime2->lessThan($carbonTime1)) {
            $carbonTime2->addDay();
        }

        // Calculate the total difference in minutes
        $minutesDifference = $carbonTime1->diffInMinutes($carbonTime2);

        // Subtract 60 minutes from the total difference
        $minutesDifference = max(0, $minutesDifference - 60); // Ensure it does not go below zero

        // Calculate hours and remaining minutes
        $hours = floor($minutesDifference / 60);
        $minutes = $minutesDifference % 60;

        // Format the result as hh:ii
        return sprintf('%02d:%02d:00', $hours, $minutes);
    }
    public static function differenceHoursMinutes($start, $close)
    {
        $carbonTime1 = Carbon::createFromFormat('H:i A', $start);
        $carbonTime2 = Carbon::createFromFormat('H:i A', $close);

        // Calculate the total difference in minutes
        $minutesDifference = $carbonTime1->diffInMinutes($carbonTime2);

        // Subtract 60 minutes from the total difference
        $minutesDifference = max(0, $minutesDifference - 60); // Ensure it does not go below zero

        // Calculate hours and remaining minutes
        $hours = floor($minutesDifference / 60);
        $minutes = $minutesDifference % 60;

        // Format the result as hh:ii
        return sprintf('%02d:%02d', $hours, $minutes).":00";
    }
    public static function globaldifferenceHours($start, $close)
    {
        $carbonTime1 = Carbon::parse($start);
        $carbonTime2 = Carbon::parse($close);

        $hoursDifference = $carbonTime1->diffInHours($carbonTime2);
//        dd([$start,$close, $hoursDifference]);
        return $hoursDifference;
    }

    public static function newglobaldifferenceHours($start, $close, $policy_start, $policy_end)
    {
        $start_time = Carbon::parse($start, 'America/New_York');
        $close_time = Carbon::parse($close, 'America/New_York');
        $policy_start_time = Carbon::createFromFormat('h:i A', $policy_start, 'America/New_York')->setDate($start_time->year, $start_time->month, $start_time->day);
        $policy_end_time = Carbon::createFromFormat('h:i A', $policy_end, 'America/New_York')->setDate($start_time->year, $start_time->month, $start_time->day);

        //dd($start_time, $close_time, $policy_start_time, $policy_end_time);

        // Check if close time is on the next day compared to start time
        if ($close_time->lt($start_time)) {
            $close_time->addDay(); // Adjust close time to the next day
        }

        // Calculate working hours within policy time range
        $workingHours = $start_time->diffInHours($close_time, false);

        // Adjust working hours based on policy start and end time
        if ($start_time->lt($policy_start_time)) {
            $workingHours -= $start_time->diffInHours($policy_start_time, false);
        }

        if ($close_time->gt($policy_end_time)) {
            $workingHours -= $close_time->diffInHours($policy_end_time, false);
        }

        // dd($workingHours);
        return $workingHours;
    }


    public static function convertSecondsToTimeFormat($totalSeconds) {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
    public static function add_time($time1) {
        $times = explode(':', $time1);
//        dd($times);
        return (int) (isset($times[0]) ? (int) $times[0] *3600 : 0 ) + (isset($times[1]) ? (int) $times[1] *60 : 0) +  (isset($times[2]) ? (int) $times[2]: 0) ;
    }

    public static function get_HIA_to_HIS($time1) {
        $carbonTime1 = Carbon::createFromFormat('H:i A', $time1);
        return $carbonTime1->format('H:i:s');
    }
    public static function getLateTime($time1, $time2) {
        if(empty($time2) || empty($time1)){return null;}
            $carbonTime1 = Carbon::createFromFormat('h:i A', $time1);
            $time1f = $carbonTime1->format('H:i:s');

            // Convert the second time from 'H:i:s' format
            $datetime1 = Carbon::createFromFormat('H:i:s', $time1f);
            $datetime2 = Carbon::createFromFormat('H:i:s', $time2);

            // Calculate the difference in seconds
            $secondsDifference = $datetime1->diffInSeconds($datetime2);
        // Check if the difference is more than 15 minutes (900 seconds) and $time2 is after $time1
        if ($datetime2->greaterThan($datetime1) && $secondsDifference > 900) {
            $hours = intdiv($secondsDifference, 3600);
            $minutes = intdiv($secondsDifference % 3600, 60);
            $seconds = $secondsDifference % 60;

            // Format the time difference as "hh:mm:ss"
            $timeDifference = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

            return $timeDifference;
        }

        return null;
    }
    public static function globaldifferenceMinus($start, $close)
    {
        $carbonTime1 = Carbon::parse($start);
        $carbonTime2 = Carbon::parse($close);

        $minutesDifference = $carbonTime1->diffInMinutes($carbonTime2) % 60;

        return $minutesDifference;
    }

    public static function progressBarWith9H($earned_time, $hours = 9){
        $earned_times = explode(':', $earned_time);
        $shift_time = $hours * 60 ;
        $total_minutes = ( ( $earned_times[0] ?? 0) * 60 ) + ($earned_times[1] ?? 0);
        $attendence_visual = (int) ( ( $total_minutes*100 ) / $shift_time );
        $attendence_visual = $attendence_visual > 100 ? 100 : $attendence_visual;
        $attendence_visual = $attendence_visual < 0 ? 0 : $attendence_visual;

        return $attendence_visual;
    }
    public static function calculateTimeDifference($start_time, $end_time, $new_date) {
        $carbonTime1 = Carbon::createFromFormat('Y-m-d H:i A', $new_date . ' ' . $start_time);
        $carbonTime2 = Carbon::createFromFormat('Y-m-d H:i A', $new_date . ' ' . '08:01 AM');

        // Handle cases where end time is before start time (e.g., overnight shifts)
        if ($carbonTime2->lessThan($carbonTime1)) {
            $carbonTime2->addDay();
        }

        $duration = $carbonTime2->diff($carbonTime1)->format('%H:%M:%S');

        // Subtract one hour from the duration
        $durationCarbon = Carbon::createFromFormat('H:i:s', $duration);
        $decreasedDuration = $durationCarbon->subHour()->format('H:I:S');

        return $decreasedDuration;
    }

    public static function progressBarWithTime($shiftStart, $shiftEnd, $attendanceStart, $attendanceEnd)
    {
        // Calculate differences
        $shiftDifference = static::differenceHours($shiftStart, $shiftEnd);
        $attendanceDifference = static::globaldifferenceHours($attendanceStart, $attendanceEnd);

        // Extract hours and minutes for progress bar
        list($shiftHours, $shiftMinutes) = sscanf($shiftDifference, "%d Hrs %d mins");
        list($attendanceHours, $attendanceMinutes) = sscanf($attendanceDifference, "%d Hrs %d mins");

        // Calculate progress bar width as a percentage based on the shift time
        $maxShiftDifference = ($shiftHours * 60 + $shiftMinutes);
        $attendanceCompletion = min(($attendanceHours * 60 + $attendanceMinutes) / $maxShiftDifference * 100, 100);

        return $attendanceCompletion;
    }

    public static function datedashformat($value)
    {
        // dd($value);
        if (!strpos($value, "-")) {
            $utcTime = Carbon::createFromFormat('d/m/Y', $value) // Set the time to midnight
                ->setTimezone('UTC');
            $formattedUtcTime = $utcTime->format('Y-m-d');
        } else {
            $formattedUtcTime = $value;
        }
        // dd($formattedUtcTime);
        return $formattedUtcTime;
    }


    public static function isWeekend($date)
    {
        //dd($date);
        // Create a Carbon instance from the provided date
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);

        // Check if the date is a Saturday or Sunday
        if ($carbonDate->isSaturday() || $carbonDate->isSunday()) {
            return true;
        }

        return false;
    }

    public static function dateslashformat($value)
    {
        if (!strpos($value, "/")) {
            $utcTime = Carbon::createFromFormat('Y-m-d', $value) // Set the time to midnight
                ->setTimezone('UTC');
            $formattedUtcTime = $utcTime->format('d/m/Y');
        } else {
            $formattedUtcTime = $value;
        }
        // dd($formattedUtcTime);
        return $formattedUtcTime;
    }

    public static function convertGrossHrsToTime($grossHrs) {
        if ($grossHrs == '00:00:00') {
            return $grossHrs;
        }

        // If input is already in HH:MM:SS format, return it directly
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $grossHrs)) {
            return $grossHrs;
        }

        // Extract hours and minutes using regular expression
        if (preg_match('/(\d+)\s*Hrs\s*(\d+)\s*mins/', $grossHrs, $matches)) {
            // Extracted hours and minutes
            $hours = (int) $matches[1];
            $minutes = (int) $matches[2];

            // Convert to total seconds
            $totalSeconds = ($hours * 3600) + ($minutes * 60);

            // Format as HH:MM:SS
            $formattedTime = gmdate('H:i:s', $totalSeconds);

            return $formattedTime;
        }

        // If the pattern doesn't match, return a default or handle the error as needed
        return '00:00:00';
    }
    public static function convertGrossHrsToTime_old($grossHrs) {

        if($grossHrs == '00:00:00'){
            return $grossHrs;
        }
        // Extract hours and minutes using regular expression
        preg_match('/(\d+)\s*Hrs\s*(\d+)\s*mins/', $grossHrs, $matches);

        // Extracted hours and minutes
        $hours = (int) $matches[1];
        $minutes = (int) $matches[2];

        // Convert to total seconds
        $totalSeconds = ($hours * 3600) + ($minutes * 60);

        // Format as HH:MM:SS
        $formattedTime = gmdate('H:i:s', $totalSeconds);

        return $formattedTime;
    }
    public static function formatDuration($hours, $minutes) {
        // Calculate total seconds
        $totalSeconds = ($hours * 3600) + ($minutes * 60);

        // Format hours, minutes, and seconds into HH:MM:SS
        $formattedTime = gmdate('H:i:s', $totalSeconds);

        return $formattedTime;
    }
    public static function getDatesRange($start_date, $end_date) {
        $st_dates= [$start_date, $end_date];
        $startDate = Carbon::createFromFormat('m/d/Y', $start_date)->format('Y-m-d') ;
        $endDate = Carbon::create($end_date) ;
        //dd([$start_date, $end_date, $st_dates, $startDate, $endDate]);

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates_range[$date->format('Y-m-d')] = $date->format('Y-m-d') ;
        }

        return $dates_range;
    }
    public static function calculateEffectiveTime($logs, $times = false) {
        if (empty($logs)) {
            return '00:00:00'; // Return if there are no logs
        }

        $earliestArrival = new DateTime($logs[0]['arrival_time']);
        $latestLeave = new DateTime($logs[0]['leave_time']);

        foreach ($logs as $log) {
            $log['leave_time'] = is_null($log['leave_time']) ? $log['arrival_time'] : $log['leave_time'];
            $checkin = new DateTime($log['arrival_time']);
            $checkout = new DateTime($log['leave_time']);

            // Update the earliest arrival time
            if ($checkin < $earliestArrival) {
                $earliestArrival = $checkin;
            }

            // Update the latest leave time
            if ($checkout > $latestLeave) {
                $latestLeave = $checkout;
            }
        }

        if($times){
            return [
                'last_leave' => $earliestArrival,
                'first_arival' => $latestLeave,
                $logs
            ];
        }
        // Calculate the total difference in seconds
        $totalTimeDifference = $latestLeave->getTimestamp() - $earliestArrival->getTimestamp();

        return DateHelper::formatSeconds($totalTimeDifference);
    }

    public static function formatSeconds($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }



    public static function calculateTotalTimeDifference($logs) {
        $totalTimeDifference = 0;

        if(empty($logs)){
            return DateHelper::formatMilliseconds($totalTimeDifference);
        }
        foreach ($logs as $log) {
            $checkin = new DateTime($log['arrival_time']);
            $checkout = new DateTime($log['leave_time']);

            // Calculate the difference in milliseconds and add it to the total
            $interval = $checkout->diff($checkin);
            $totalSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
            $totalTimeDifference += $totalSeconds * 1000; // Convert to milliseconds
        }

        return DateHelper::formatMilliseconds($totalTimeDifference);
    }

    public static function formatMilliseconds($ms) {
        // Calculate total seconds, minutes, and hours
        $totalSeconds = floor($ms / 1000);
        $totalMinutes = floor($totalSeconds / 60);
        $totalHours = floor($totalMinutes / 60);

        $seconds = $totalSeconds % 60;
        $minutes = $totalMinutes % 60;
        $hours = $totalHours;

        // Ensure two digits for hours, minutes, and seconds
        $formattedHours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $formattedMinutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $formattedSeconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

        // Construct the formatted time string
        $formattedTime = "{$formattedHours}:{$formattedMinutes}:{$formattedSeconds}";

        return $formattedTime;
    }

}
