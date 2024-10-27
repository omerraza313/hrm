<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\AttendenceHelper;
use App\Helpers\DateHelper;
use App\Models\Designation;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendence;
use Illuminate\Http\Request;
use App\Enums\AttendenceEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AttendenceService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\Attendence\AttendenceUpdateRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
class AttendenceController extends Controller {
    public function __construct(protected AttendenceService $attendenceService)
    {
    }

    public function index(Request $request)
    {
        $data = $this->attendenceService->getAttendenceData($request->all());
        return view('admin.attendence.regular.view', $data);
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

    public function export_old(Request $request)
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
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Earned Hours');

        $row = 2;

            Attendence::when(isset($data['from_date']) && $data['from_date'], function ($query) use ($data) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $data['from_date']);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '>=', $formattedDate);
            })
            ->when(isset($data['to_date']) && $data['to_date'], function ($query) use ($data) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $data['to_date']);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), '<=', $formattedDate);
            })
            ->when(isset($data['employee_id']) && $data['employee_id'], function ($query) use ($data) {
                return $query->where('user_id', $data['employee_id']);
            })
            ->with(['user'])->chunk(100, function ($attendances) use ($sheet, &$row) {
//                dd($attendances);
                foreach ($attendances as $attendance) {
                    // Convert times to New York timezone
                    if(is_null($attendance->leave_date)){
                        $attendance->leave_time = $this->get_timeout($attendance->policy->working_settings->timeout_policy, $attendance->arrival_time);
                    }
                    $checkIn = Carbon::parse($attendance->arrival_time)->setTimezone('America/New_York');
                    $checkOut = Carbon::parse($attendance->leave_time)->setTimezone('America/New_York');

                    // Add a new row with data
                    if(empty($attendance->user)){
                        continue;
                        /*$sheet->setCellValue('A' . $row, $attendance->user_id);
                        $row++;*/
                    }
                    $effective_hrs_in_hours = DateHelper::globaldifferenceHours($checkIn, $checkOut ?? $checkIn);
                    $effective_hrs_in_minus = DateHelper::globaldifferenceMinus($checkIn, $checkOut ?? $checkIn);
                    $time_string = ($effective_hrs_in_hours || $effective_hrs_in_minus) && $effective_hrs_in_hours<24 ? ($effective_hrs_in_hours<=9?"0":"").$effective_hrs_in_hours.":".($effective_hrs_in_minus<=9?"0":"").$effective_hrs_in_minus:'';
                    $sheet->setCellValue('A' . $row, $attendance->user->first_name . ' ' . $attendance->user->last_name);
                    $sheet->setCellValue('B' . $row, $checkIn->format('m/d/Y'));
                    $sheet->setCellValue('C' . $row, $checkIn->format('G:i A'));
                    $sheet->setCellValue('D' . $row, $checkOut->format('m/d/Y'));
                    $sheet->setCellValue('E' . $row, $checkOut->format('G:i A'));
                    $sheet->setCellValue('F' . $row, $this->getAttendenceLabel($attendance->status));
                    $sheet->setCellValue('G' . $row, $time_string);
                    $row++;
                }
            });

        $today = date('Y-m-d-H-i-s');
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=NovitaMS-HR-Attendence-export-".$today.".xlsx",
        ]);

        return $response;
    }

    public function export(Request $request)
    {
        $data = json_decode($request->data, true);

        $employee_id = $data['employee_id'] ?? null;
        $to_date = $data['to_date'] ?? null;
        $from_date = $data['from_date'] ?? null;
        $args = ['emp_id' => $employee_id, 'to_date' => $to_date, 'from_date' => $from_date];

        $report_period = $this->get_report_period($args);
        $report_name = "Attendance Sheet ({$report_period})";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $sheet = $this->get_excel_header($sheet, $row++, $report_name);
        //$sheet = $this->get_excel_headings($sheet, $row++);

        $attendances = AttendenceHelper::get_all_attendence_log($args);
        //$designations = AttendenceHelper::getDesignations();

//        dd($attendances);
        $user_headings = [];
        $time_totals = [];
        ksort($attendances);
        //dd($attendances);
        foreach ($attendances as $this_user => $user_attendances) {
            foreach ($user_attendances as $attendance) {
                $attendance = (object) $attendance;
                $user = $attendance->user;
                $userId = $user->id ?? $user['id'];

                $employee_name = ($user['data']['first_name'] ?? '') . ' ' . ($user['data']['last_name'] ?? '');
                //dd([$employee_details, $user, $designations]);
                if($userId==1){
                    $designation = ($userId==1 ? 'Super Admin' : ($attendence->user->employee_details->designation->name ?? $userId) );
                    $department = 'Novita MS';
                }else{
                    $designation = $user['designation'] ?? 'n/a';
                    $department = $user['department'] ?? 'n/a';
                    //dd([$department, $designation, $user]);
                }

                if(empty($user_headings[$userId])){
                    $user_headings[$userId] = $userId;
                    // empty row after employee
                    //dd("A{$row}:K{$row}");
                    $sheet = $this->excel_populate_row($sheet, $row++, ['', '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' ]);

                    $sheet = $this->add_single_column_row($sheet, $row++, 'Employee Name : '.$employee_name);
                    $sheet = $this->add_single_column_row($sheet, $row++, 'Employee ID : '.$userId);
                    $sheet = $this->add_single_column_row($sheet, $row++, 'Department : '.$department);
                    $sheet = $this->add_single_column_row($sheet, $row++, 'Designation: '.$designation);
                    //$sheet = $this->excel_populate_row($sheet, $row++, ['', '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' , '' ]);

                    $sheet = $this->get_excel_headings($sheet, $row++);
                }
                //dd([$user, $attendance, $userId]);

                if(empty($time_totals[$userId])){
                    $time_totals[$userId] = [ 'earned_times' => 0, 'effective_times' => 0,  'late_hours' => 0, 'gross_times' => 0 ];
                }
                $employee_details = $user['data']['employee_details'] ;
                if (is_null($attendance->leave_date) && !is_null($attendance->arrival_date)) {
                    $attendance->leave_time = $attendance->arrival_time;
                }

//                $day_of_week = Carbon::parse($attendance->arrival_date)->format('D');
//                $is_it_weekend = AttendenceHelper::isItWeekEnd($day_of_week);
//                if($is_it_weekend){ $sheet = $this->excel_format_row_bg($sheet, $row, 'D0D9E3FF'); }
                if (empty($attendance->user)) { continue; }

                $isWeekEnd = $attendance->isWeekEnd;
                $attendance_date = Carbon::parse($attendance->arrival_date);

                $effective_time = $attendance->effective_time;
                $gross_time = $attendance->Gross_Hrs ;//= DateHelper::convertGrossHrsToTime($attendance->Gross_Hrs);
                $earned_time = $attendance->earned_time;
                $late_hours = $attendance->late_time;
                $time_logs_count = $logs_count = count($attendance->logs) ; // count($attendance->logs);

                $attendance_status = $this->getAttendenceLabelExcel($attendance->status);
                if($attendance_status == 3 && ($earned_time > '00:00:00' || $logs_count > 0 )){
                    $attendance_status = $this->getAttendenceLabelExcel(1);
                }else if($isWeekEnd){
                    $attendance_status = $this->getAttendenceLabelExcel(2);
                    if($earned_time == '00:00:00' || $earned_time == 0){
                        $gross_time = $effective_time = $earned_time = $late_hours = '';
                    }
                }

                if($attendance->status == 3){
                    $sheet = $this->excel_format_row_bg($sheet, $row, 'FFFFFFC5');
                }
                if($isWeekEnd){
                    $statuses[$attendance->arrival_date] = $attendance_status;
                    $gross_time = '';
                    $sheet = $this->excel_format_row_bg($sheet, $row, 'FFE3E3E3');
                }
                $do_not_show_times = ($earned_time == '00:00:00' && $isWeekEnd);

                $time_totals[$userId]['earned_times'] += DateHelper::add_time($earned_time);
                $time_totals[$userId]['effective_times'] += DateHelper::add_time($effective_time);
                $time_totals[$userId]['late_hours'] += DateHelper::add_time($late_hours);
                $time_totals[$userId]['gross_times'] += DateHelper::add_time($gross_time);

                $time_totals[$userId]['earned_times1'][$attendance->arrival_date][] = DateHelper::add_time($earned_time);
                $time_totals[$userId]['effective_times1'][$attendance->arrival_date][] = DateHelper::add_time($effective_time);
                $time_totals[$userId]['gross_times1'][$attendance->arrival_date][] = DateHelper::add_time($gross_time);
                $time_totals[$userId]['late_hours1'][$attendance->arrival_date][] = DateHelper::add_time($late_hours);
                $time_totals[$userId]['late_hours2'][$attendance->arrival_date][] = [Carbon::parse($attendance->shift_start)->addHours(4)->format('h:i A'), Carbon::parse($attendance->arrival_time)->format('H:i:s')];

                if(empty($attendance->arrival_time) && empty($attendance->leave_time)){
                    $data = [
//                        $employee_name,
                        $attendance_date->format('m/d/Y'), $attendance_date->format('D'),
                        '', '', '', '', $attendance_status, '', '', '', ''
                    ];
                }else{
                    $data = [
//                        $employee_name,
                        $attendance_date->format('m/d/Y'),
                        $attendance_date->format('D'),
                        $do_not_show_times ? '': $attendance->shift_start,
                        $do_not_show_times ? '': Carbon::parse($attendance->arrival_time)->format('h:i A'),
                        $do_not_show_times ? '': Carbon::parse($attendance->leave_time)->format('h:i A'),
                        $do_not_show_times ? '': $late_hours,
                        $attendance_status ,//. " ({$time_logs_count})"
                        $earned_time,
                        $effective_time,
                        $gross_time,
                        '',
//                        $department, $designation
                    ];
                }

                $sheet = $this->excel_populate_row($sheet, $row, $data);
                $row++;
            }// end of users rows
            $data = [
                'Total Times',
                //'',
                '', '', '', '',
                DateHelper::convertSecondsToTimeFormat($time_totals[$this_user]['late_hours']),
                '',
                DateHelper::convertSecondsToTimeFormat($time_totals[$this_user]['earned_times']),
                DateHelper::convertSecondsToTimeFormat($time_totals[$this_user]['effective_times']),
                DateHelper::convertSecondsToTimeFormat($time_totals[$this_user]['gross_times']),
                '', '', ''
            ];
            $sheet = $this->excel_populate_row($sheet, $row, $data);
            $sheet = $this->excel_format_totals_row($sheet, $row++);



            //$sheet = $this->get_excel_headings($sheet, $row);

        }
        $today = date('Y-m-d-H-i-s');
        $response = new StreamedResponse(function () use ($spreadsheet) {
            ob_clean();
            flush();
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=Vibeh {$report_name} - Generated at " . $today . ".xlsx",
        ]);

        return $response;
    }
    private function get_all_attendence_log($args)
    {
        $currentYear = Carbon::now()->year;

        $allAtendence = Attendence::when($args['emp_id'], function ($query, $emp_id) {
            return $query->where('user_id', $emp_id);
        })
            ->when($args['to_date'], function ($query, $to_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $to_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('arrival_date', '<=', $formattedDate);
            })
            ->when($args['from_date'], function ($query, $from_date) {
                $carbonDate = Carbon::createFromFormat('m/d/Y', $from_date);
                $formattedDate = $carbonDate->format('Y-m-d');
                return $query->where('arrival_date', '>=', $formattedDate);
            })
            ->with([
                'policy' => function ($query) {
                    $query->withTrashed();
                },
                'policy.working_settings',
                'user' => function ($query) {
                    $query->withTrashed();
                },
                'user.employee_details',
                'user.employee_details.designation' => function ($query) {
                    $query->withTrashed();
                },
            ])
            ->orderBy('id', 'asc')
            ->get();

        $newAttendence = [];
        $manager_id = Auth::user()->id;

        $policies_working_settings = \App\Helpers\PolicyHelper::get_timeout_policy();

        foreach ($allAtendence as $attendence) {
            $newDate = $attendence->arrival_date;
            $original_attendence = $attendence;
            $alternate_leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time);
            if( is_null($attendence->leave_date) ){
                $empty_date = true;
                $attendence->leave_time = $this->get_timeout( $attendence->policy->working_settings->timeout_policy, $attendence->arrival_time );
            }

            if (!isset($newAttendence[$attendence->user_id][$newDate])) {
                $day_of_week = Carbon::parse($attendence->arrival_date)->format('D');
                $is_it_weekend = AttendenceHelper::isItWeekEnd($day_of_week);
                // weekend check
                $policy_start_today = Carbon::parse($attendence->arrival_date. ' '.$attendence->policy->working_settings->shift_start)->addHours(4)->format('Y-m-d H:i:s');
                $policy_end_today = Carbon::parse($attendence->arrival_date. ' '.$attendence->policy->working_settings->shift_close)->addHours(4)->format('Y-m-d H:i:s');
                if($is_it_weekend && $attendence->arrival_time->format('Y-m-d H:i:s') == $policy_start_today){
                        $arrival_time = '';
                        $leave_time = '';
                }else{
                    $arrival_time = $attendence->arrival_time;
                    $leave_time = $attendence->leave_time;
                }

                if($attendence->arrival_time > $attendence->leave_time && !is_null($attendence->leave_date)){
                    $swaper = $attendence->arrival_time ;
                    $attendence->arrival_time = $attendence->leave_time;
                    $attendence->leave_time = $swaper;
                }

                $newAttendence[$attendence->user_id][$newDate] = [
                    'a_date' => DateHelper::globaldateFormat('j M Y', $attendence->arrival_date),
                    'attendence_visual' => DateHelper::progressBarWithTime($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close, $attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'effective_hrs_in_hours' => DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'effective_hrs_in_minus' => DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time),
                    'Gross_Hrs' => DateHelper::differenceHours($attendence->policy->working_settings->shift_start, $attendence->policy->working_settings->shift_close),
                    'status' => $attendence->status,
                    'user' => $attendence->user,
                    'id' => $attendence->id,
                    'arrival_date' =>   $attendence->arrival_date,
                    'leave_date' =>   $attendence->leave_date,
                    'arrival_time' =>   $arrival_time,
                    'leave_time' =>     $leave_time,
                    'shift_start' =>    $attendence->policy->working_settings->shift_start,
                    'shift_close' =>    $attendence->policy->working_settings->shift_close,
                    'timeout_policy' =>    $policies_working_settings[$attendence->policy->working_settings->timeout_policy],
                    'timeout_policy_id' =>    $attendence->policy->working_settings->timeout_policy,
                    'logs' => [$attendence->toArray()]
                ];

            } else {

                $old_row = $newAttendence[$attendence->user_id][$newDate];

                if($attendence->arrival_time > $attendence->leave_time){
                    $swaper = $attendence->arrival_time ;
                    $attendence->arrival_time = $attendence->leave_time;
                    $attendence->leave_time = $swaper;
                }

                $newAttendence[$attendence->user_id][$newDate]['logs'][] = $attendence->toArray();
                if($old_row['effective_hrs_in_minus']>=6 && empty($attendence->leave_date)){
                    // un handled case
                }else{
                    $newAttendence[$attendence->user_id][$newDate]['attendence_visual'] += DateHelper::progressBarWithTime($old_row['shift_start'], $old_row['shift_close'], $attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] += DateHelper::globaldifferenceHours($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] += DateHelper::globaldifferenceMinus($attendence->arrival_time, $attendence->leave_time ?? $alternate_leave_time);
                }

                if($newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus']>=60){
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_minus'] -= 60;
                    $newAttendence[$attendence->user_id][$newDate]['effective_hrs_in_hours'] +=1;
                }

            }

        }



        return $newAttendence;
    }

    public function get_excel_headings($sheet, $row){
        $data = [
//            'Employee Name',
            'Date',
            'Day',
            'Shift Time',
            'In Time',
            'Out Time',
            'Late Hours',
            'Status',
            'Earned Hours',
            'Effective Hours',
            'Gross Hours',
            'Comments',
//            'Department',
//            'Designation'
        ];
        $sheet = $this->excel_populate_row($sheet, $row, $data);
        $sheet = $this->excel_format_headings($sheet, $row);
        return $sheet;
    }
    public function get_excel_header($sheet, $row, $report_name){
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('NovitaMS Logo');
        $imagePath = realpath( 'assets/img/novitams-logo.png');

        if (!file_exists($imagePath)) {
            throw new \Exception("File {$imagePath} not found!");
        }
        $drawing->setPath($imagePath)->setHeight(55)->setCoordinates('A' . $row)->setOffsetX(7)->setOffsetY(7);
        $drawing->setWorksheet($sheet);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $this->excel_apply_format($sheet, 1, "A", "C",  ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => Color::COLOR_DARKBLUE]]);
        $sheet->setCellValue('D' . $row, $report_name);
        return $sheet;
    }
    public function excel_apply_format($sheet, $row, $col1 = "A", $col2 = "K", $format){
        foreach (range($col1, $col2) as $column) {
            $cell = $column . $row;
            $sheet->getStyle($cell)->applyFromArray($format);
        }
        return $sheet;
    }

    public function add_single_column_row($sheet, $row, $data = ''){
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet = $this->excel_populate_row($sheet, $row, [$data]);
        $sheet = $this->excel_format_headings($sheet, $row);
        return $sheet;
    }
    public function excel_populate_row($sheet, $row, $data = []){
        if (is_array($data) && !empty($data)) {
            foreach ($data as $index => $value) {
                $column = chr(65 + $index); // chr(65) is 'A', chr(66) is 'B', etc.
                $sheet->setCellValue($column . $row, $value);
            }
        }
        $totals_cell_format['alignment'] = [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ];;
        $sheet->getStyle('G1')->applyFromArray($totals_cell_format);
        $sheet->getStyle('I1')->applyFromArray($totals_cell_format);
        $sheet->getStyle('J1')->applyFromArray($totals_cell_format);
        $sheet->getStyle('K1')->applyFromArray($totals_cell_format);

        return $sheet;
    }
    public function excel_set_column_widths($sheet, $row, $data){
        if (is_array($data) && !empty($data)) {
            foreach ($data as $index => $value) {
                $column = chr(65 + $index); // chr(65) is 'A', chr(66) is 'B', etc.
                $sheet->getColumnDimension($column)->setWidth($value);
            }
        }
        return $sheet;
    }
    public function excel_format_totals_row($sheet, $row, $bg_color = 'FF008AFC'){
        $totals_cell_format = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => $bg_color],
                'size' => 12,
                'name' => 'Arial',
            ]
        ];
        $sheet = $this->excel_apply_format($sheet, $row, "A", "N", $totals_cell_format);
        return $sheet;
    }
    public function excel_format_row_bg($sheet, $row, $bg_color = Color::COLOR_DARKBLUE){
        $row_bg_format['fill'] = [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => $bg_color],
        ];
        $sheet = $this->excel_apply_format($sheet, $row, "A", "K", $row_bg_format);
        return $sheet;
    }
    public function excel_format_headings($sheet, $row){
        //$data = [ 25 ,13 ,7 ,12 ,12 ,12 ,14 ,12 ,17 ,17 ,17 ,35 ,29 ,31 ];
        $data = [ 13 ,7 ,12 ,12 ,12 ,14 ,12 ,17 ,17 ,17 ,60 ];
        $sheet = $this->excel_set_column_widths($sheet, $row, $data);
        $header_cell_format = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 10,
                'name' => 'Arial',
            ]
        ];
        $alignment = [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
        ];
        $borders = [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                'color' => ['argb' => 'FF0000FF'],
            ],
        ];
        $fill = [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF008AFC'],
        ];

        $header_bg_format = $header_cell_format;
        $header_bg_format['fill'] = $fill;
        $header_bg_format['alignment'] = $alignment;

        $header_cell_format['alignment'] = $alignment;
        $header_cell_format['alignment']['vertical'] = Alignment::VERTICAL_CENTER;
        $header_cell_format['font']['color']['argb'] = 'FF008AFC';
        $header_cell_format['font']['size'] = 16;

        $sheet->getStyle('D1')->applyFromArray($header_cell_format);

        $sheet->getStyle('A1')->applyFromArray($header_bg_format);
        $sheet->getStyle('B1')->applyFromArray($header_bg_format);
        $sheet->getStyle('C1')->applyFromArray($header_bg_format);
        $sheet = $this->excel_apply_format($sheet, $row, "A", "K", $header_bg_format);
        return $sheet;
    }

    public function get_report_period($args){
        $report_period = '';
        if( !empty($args['from_date']) && empty($args['to_date']) ) {
            $report_period .= 'From '.$args['from_date'];
        }else if( empty($args['from_date']) && !empty($args['to_date']) ){
            $report_period .= 'To '.$args['to_date'];
        }else if( !empty($args['from_date']) && !empty($args['to_date']) ){
            $report_period .= $args['from_date'].' - '.$args['to_date'];
        }else if( $args['from_date'] == $args['to_date'] ){
            $report_period .= $args['from_date'];
        }
        return $report_period;
    }

    private function get_timeout( $policy_id, $start_time) {
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
    private function getAttendenceLabelExcel(string $value): string
    {
        $enum = AttendenceEnum::from($value);

        // Using match expression (PHP 8+)
        return match ($enum) {
            AttendenceEnum::Late => 'Late',
            AttendenceEnum::OnTime => 'Present',
            AttendenceEnum::Holiday => 'Holiday',
            AttendenceEnum::Absent => 'Absent',
            AttendenceEnum::Leave => 'Leave',
        };
    }
}
