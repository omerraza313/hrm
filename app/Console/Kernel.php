<?php

namespace App\Console;

use App\Schedule\AttendanceSchedule;
use App\Schedule\AutoCheckoutAttendance;
use App\Schedule\SwapPolicySchedule;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            // SwapPolicySchedule::swapPolicy();    // Closed on 2024-03-05
            // AttendanceSchedule::markAttendance();   // Closed on 2024-03-05
            AttendanceSchedule::attendance_marking();
            AutoCheckoutAttendance::auto_checkout();
        })->everyFiveSeconds();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}