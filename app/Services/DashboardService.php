<?php

namespace App\Services;

use App\Enums\RolesEnum;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendence;
use App\Models\Department;
use App\Helpers\DateHelper;
use App\Models\Applyleaves;
use App\Models\Designation;
use Illuminate\Support\Facades\DB;

class DashboardService {
    public function get_dashboard_data()
    {
        // $department = Department::withTrashed()->get();
        $department = Department::get();

        $employee = User::role('employee')->get();

        $monthAttendence = Carbon::now()->format('m');
        $employee_id = null;
        $to_date = null;
        $from_date = null;
        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];
        $attendances = $this->get_all_attendence_log($monthAttendence, $args);

        $leaves = $this->get_all_leaves();

        $designations = $this->get_designations();
        $birthdays = $this->get_birthdays();

        $today = date('Y-m-d', strtotime('2024-07-01'));
        $attendence_counts = $this->get_attendence_counts($today);
        $present = $attendence_counts['On Time'] ?? 0;
        $absent = $attendence_counts['Absent'] ?? 0;
        $onleave = $attendence_counts['Leave'] ?? 0;

        return compact( 'department', 'designations', 'employee', 'present', 'absent', 'onleave', 'leaves', 'attendances', 'birthdays');
    }

    private function get_birthdays()
    {
        $upcomingBirthdays = User::Role(RolesEnum::Employee->value)
            ->whereHas('employee_details', function ($query) {
                $today = now();
                // dd($today->format('m'), $today->format('d'));zx
                $query->whereMonth('dob', '=', $today->format('m'))
                    ->whereDay('dob', '>=', $today->format('d'));
                // ->orWhereMonth('dob', '>', $today->format('m'));
            })
            ->with([
                'employee_details',
                'employee_details.department'  => function ($query) {
                $query->withTrashed();
            }
            ])
            ->get();

        return $upcomingBirthdays;

    }

    private function get_all_attendence_log($month, $args)
    {
        $currentYear = Carbon::now()->year;
        // $allAtendence = Attendence::where('leave_time', '!=', null)->with('policy.working_settings', 'user', 'user.employee_details', 'user.employee_details.designation')->get();
        $allAtendence = Attendence::select(DB::raw('DATE(arrival_time) as arrival_date'), DB::raw('COUNT(DISTINCT user_id) as user_count'))
            ->where('leave_time', '!=', null)
            ->whereYear('arrival_time', $currentYear)
            ->whereMonth('arrival_time', $month)
            ->groupBy(DB::raw('DATE(arrival_time)'))
            ->orderBy(DB::raw('DATE(arrival_time)'))
            ->get();

        return $allAtendence;
    }

    private function get_all_leaves()
    {
        $leaves = Applyleaves::orderBy('id', 'desc')->with([
            'employee' => function ($query) {
                $query->withTrashed();
            },
            'employee.employee_details',
            'employee.employee_details.designation' => function ($query) {
                $query->withTrashed();
            }
        ])->take(10)->get();

        return $leaves;
    }

    private function get_designations()
    {
        $designation = Designation::with([
            'department' => function ($query) {
                $query->withTrashed();
            },
            'users.user'
        ])->get();

        return $designation;
    }

    private function get_attendence_counts($today = '')
    {
        if(empty($today)){
            $today = Carbon::today();
        }

        $attendence_statuses = \App\Helpers\AttendenceHelper::attendenceStatus();
        $attendence_counts = Attendence::select('status', DB::raw('count(*) as total'))
            ->where('arrival_date', $today)
            ->groupBy('status')
            ->get();
        $attendence_status_counts = [];

        foreach ($attendence_counts as $k=>$attendence_status){
            $attendence_status_counts[$attendence_statuses[$attendence_status->status] ]= $attendence_status->total;
        }
//        dd($attendence_counts);
//        dd($attendence_status_counts);
        return $attendence_status_counts;
    }
}
