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


class AttendenceController extends Controller {
    public function __construct(protected AttendenceService $attendenceService)
    {
    }
    public function index(Request $request)
    {
        //dd('Omer');
        $data = $this->attendenceService->getAttendenceData($request->all());
        return view('employee.attendence.view', $data);
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
