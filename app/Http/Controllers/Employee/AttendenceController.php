<?php

namespace App\Http\Controllers\Employee;

use App\Enums\AttendenceEnum;
use App\Enums\RolesEnum;
use App\Helpers\AttendanceLogging;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;

use App\Http\Requests\Attendence\AttendenceUpdateRequest;
use App\Http\Requests\EmployeeRequests\Attendence\AttendenceMarkRequest;
use App\Models\Attendence;
use App\Models\User;
use App\Services\Employee\AttendenceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Auth;
use App\Models\DeviceLog;

class AttendenceController extends Controller {
    public function __construct(protected AttendenceService $attendenceService)
    {
    }
    public function index(Request $request)
    {
        //dd($request);
        //return $request;
        // $filter_data = array(
        //     'employee_id' => Auth::id(),

        // );
        //$currentMonth = 10;
        // $newAttendence_data = Attendence::where('user_id', Auth::id())
        // ->whereMonth('arrival_date', $monthAttendence)
        // ->orderBy('id', 'desc')
        // ->get();

        $currentMonth = Carbon::now()->month;

        if ($request->has('filterMonth')) {
            $currentMonth = $request->input('filterMonth');
        }
        $currentYear = Carbon::now()->year;

        $currentMonth = Carbon::now()->month;

        if ($request->has('filterMonth')) {
            $currentMonth = $request->input('filterMonth');
        }
        $currentYear = Carbon::now()->year;

        // Step 1: Fetch unique dates from device_log for the specified user and month
        $uniqueDates = DeviceLog::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->selectRaw('date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
            
        // Step 2: Fetch all logs for each unique date
        $newAttendanceData = $uniqueDates->map(function ($logDate) {
            $logsForDate = DeviceLog::where('user_id', Auth::id())
                ->whereDate('date', $logDate->date)
                ->orderBy('time')
                ->get();

            if ($logsForDate->isNotEmpty()) {
                // Calculate earned time based on first and last log of the day
                $firstLogTime = Carbon::parse($logsForDate->first()->time);
                $lastLogTime = Carbon::parse($logsForDate->last()->time);

                $effectiveHours = $firstLogTime->diff($lastLogTime);
                $earnedHoursFormatted = sprintf('%02d:%02d:%02d', $effectiveHours->h, $effectiveHours->i, $effectiveHours->s);
                $checkinTime = null;
                $totalMinutes = null;

                // Find the first "CheckIn" entry for check-in time
                foreach ($logsForDate as $log) {
                    if ($log->type === 'CheckIn') {
                        $checkinTime = Carbon::parse($log->time)->format('h:i A'); // 12-hour format without seconds
                        break;
                    }
                }
                
                // Calculate effective time spent across all CheckIn and CheckOut pairs
                for ($i = 0; $i < $logsForDate->count(); $i++) {
                    $currentLog = $logsForDate[$i];

                    if ($currentLog->type === 'CheckIn') {
                        $checkInTime = Carbon::parse($currentLog->time);

                        // Look for the next CheckOut log after this CheckIn
                        for ($j = $i + 1; $j < $logsForDate->count(); $j++) {
                            if ($logsForDate[$j]->type === 'CheckOut') {
                                $checkOutTime = Carbon::parse($logsForDate[$j]->time);
                                $minutesSpent = $checkInTime->diffInMinutes($checkOutTime);

                                $totalMinutes += $minutesSpent;

                                // Move index to the position of this CheckOut to continue
                                $i = $j;
                                break;
                            }
                        }
                    }
                }

                $earnedHours = sprintf('%02d:%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60, 0);

                return [
                    'date' => $logDate->date,
                    'checkin_time' => $checkinTime,
                    'effective_time' => $earnedHoursFormatted,
                    'earned_time' => $earnedHours,
                    'device_logs' => $logsForDate,
                    'user_id' => Auth::id()
                ];
            } else {
                return [
                    'date' => $logDate->date,
                    'checkin_time' => null,
                    'earned_time' => '00:00:00',
                    'effective_time' => '00:00:00',
                    'device_logs' => [],
                    'user_id' => Auth::id()
                ];
            }
        });
        
        //return $newAttendanceData;
        return view('employee.attendence.view', compact('newAttendanceData'));
        $data = $this->attendenceService->getAttendenceData($request->all());
        //dd($data);
        return view('employee.attendence.view', $data);
    }
    public function fetch_device_log(Request $request)
    {
        $arrivalDate = $request->input('arrival_date');
        $userId = $request->input('user_id');

        // Fetch the device logs based on the arrival date and user ID
        $deviceLogs = DeviceLog::where('user_id', $userId)
            ->where('date', $arrivalDate)
            ->orderBy('time')
            ->get();

        $result = [];
        $count = $deviceLogs->count();

        for ($i = 0; $i < $count; $i++) {
            $currentLog = $deviceLogs[$i];

            // Check if the current log is a CheckIn
            if ($currentLog->type == 'CheckIn') {
                $checkinTime = date('g:i A', strtotime($currentLog->time));
                $nextCheckoutTime = null;
                
                // Look for the next Checkout log
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($deviceLogs[$j]->type == 'CheckOut') {
                        $nextCheckoutTime = $deviceLogs[$j]->time;
                        break;
                    }
                }
                
                // If we found a Checkout log, calculate time spent
                if ($nextCheckoutTime) {
                    $checkoutTime = date('g:i A', strtotime($nextCheckoutTime));
                    $timeSpent = (strtotime($nextCheckoutTime) - strtotime($currentLog->time)) / 60; // in minutes

                    $result[] = [
                        'device_id' => $currentLog->device_id,
                        'arrivalDate' => $arrivalDate,
                        'checkin' => $checkinTime,
                        'checkout' => $checkoutTime,
                        'time_spent' => "{$timeSpent} Min"
                    ];
                }
            }

            // Check if the current log is a CheckOut
            if ($currentLog->type == 'CheckOut') {
                // In case of consecutive CheckOuts, we just skip to the next iteration
                continue;
            }
        }

        return response()->json($result);
    }

    // late commers service

    public function attendence_view(Request $request){
        $data = $this->attendenceService->getAttendenceDataManager($request->all());
        return view('employee.manager.attendence.regular.view', $data);
    }

    public function late_commers(Request $request){
        $data = $this->attendenceService->getAttendenceDataLate($request->all());
        return view('employee.manager.attendence.late.view', $data);
    }
    public function mark_arrival_attendance()
    {
        AttendanceLogging::log('Portal Check In', 'Employee Checked In');
        $aStatus = $this->attendenceService->markAttendence();

        if (!$aStatus['status']) {
            return back()->with('error', $aStatus['message']);
        }

        return back()->with('success', $aStatus['message']);
    }
    public function mark_leave_attendance($id = null)
    {
        AttendanceLogging::log('Portal Check Out', 'Employee Checked Out');
        $aStatus = $this->attendenceService->leaveAttendence($id);

        if (!$aStatus) {
            return back()->with('error', 'Some Error Occurred');
        }

        return back()->with('success', 'Your Attendence Checkout Successfully');
    }

    public function export(Request $request)
    {
        $data = json_decode($request->data, true);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add Excel headers
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Check In Date');
        $sheet->setCellValue('C1', 'Check In Time');
        $sheet->setCellValue('D1', 'Check Out Date');
        $sheet->setCellValue('E1', 'Check Out Time');
        $sheet->setCellValue('F1', 'status');

        $row = 2;

        Attendence::when(isset($data['from_date']) && $data['from_date'], function ($query) use ($data) {
            $carbonDate = Carbon::createFromFormat('d/m/Y', $data['from_date']);
            $formattedDate = $carbonDate->format('Y-m-d');
            return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '>=', $formattedDate);
        })
            ->when(isset($data['to_date']) && $data['to_date'], function ($query) use ($data) {
                $carbonDate = Carbon::createFromFormat('d/m/Y', $data['to_date']);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '<=', $formattedDate);
            })
            ->when(isset($data['employee_id']) && $data['employee_id'], function ($query) use ($data) {
                return $query->where('user_id', $data['employee_id']);
            })
            ->with(['user'])->chunk(100, function ($attendances) use ($sheet, &$row) {
                foreach ($attendances as $attendance) {
                    // Convert times to New York timezone
                    $checkIn = Carbon::parse($attendance->arrival_time)->setTimezone('America/New_York');
                    $checkOut = Carbon::parse($attendance->leave_time)->setTimezone('America/New_York');

                    // Add a new row with data
//                    dd($attendance->user->first_name);
                    if(empty($attendance->user)){
                        $sheet->setCellValue('A' . $row, $attendance->user_id);
                        $row++;
                        continue;
                    }
                    $sheet->setCellValue('A' . $row, $attendance->user->first_name . ' ' . $attendance->user->last_name);
                    $sheet->setCellValue('B' . $row, $checkIn->format('d/m/Y'));
                    $sheet->setCellValue('C' . $row, $checkIn->format('g:i A'));
                    $sheet->setCellValue('D' . $row, $checkOut->format('d/m/Y'));
                    $sheet->setCellValue('E' . $row, $checkOut->format('g:i A'));
                    $sheet->setCellValue('F' . $row, $this->getAttendenceLabel($attendance->status));
                    $row++;
                }
            });
        $today = date('Y-m-d-H-i-s');
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename='NovitaMS-Attendence-export-".$today.".xlsx",
        ]);

        return $response;
    }

    public function update(AttendenceUpdateRequest $request)
    {
        $data = $request->validated();
        $updateStatus = $this->attendenceService->updateAttendence($data);
        if ($updateStatus) {
            return redirect()->back()->with('success', 'Attendence Updated Successfully');
        }
        return redirect()->back()->with('error', 'Attendence Not Updated');

    }

    private function getAttendenceLabel(string $value): string
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

}
