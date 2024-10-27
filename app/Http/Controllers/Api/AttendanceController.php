<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ApiLog;
use App\Models\ApiRaw;
use App\Models\DeviceLog;
use App\Models\Salary;
use App\Models\Attendence;
use Illuminate\Http\Request;
use App\Enums\AttendenceEnum;
use App\Helpers\AttendanceLogging;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpParser\Node\Expr\Cast\Object_;
use function Psy\debug;
use function Symfony\Component\VarDumper\Dumper\esc;

class AttendanceController extends Controller {
    public function doNotMarkAttendance(Request $request){
        exit('Oops : Invalid Access');
    }

    public function markAttendance(Request $request)
    {

        try {
            $data = $request->all();
            // avoid local host
            if($_SERVER['HTTP_HOST']=='localhost' && empty($_REQUEST['donotcheckhost']) ){
                exit('Wasting my time !');
            }


            $attendance_obj = $data['attendance'] ?? ($data['bulk_attendance'] ?? []);
            $import_mode = !empty($data['bulk_attendance']) ? "bulk" :'single';
            $attendance_recs_count = 0;
            if( !empty($data['attendance']) ){
                $attendance = json_decode($attendance_obj);
                $log_rec = [
                    'method' => $import_mode,
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                    'data' => json_encode($attendance),
                    'imported' => 0
                ];
                $attendance_recs_count = 1;
                ApiLog::create($log_rec);
            }else if( !empty($data['bulk_attendance']) ){
                $attendance_recs = json_decode($attendance_obj);
                $attendance_recs_count = count((array)$attendance_recs);

                // if attendance object is empty
                if( empty($attendance_obj) ){
                    return response()->json(['data' => $attendance_obj, 'status' => "Failed : attendance object not found.", 'code' => 412], 200);
                }

                // Split the records into chunks of 20
                $chunks = array_chunk((array) $attendance_recs, 20);

                foreach ($chunks as $chunk) {
                    $chunk_count = count($chunk); // Get the number of records in this chunk
                    ApiRaw::create([
                        'method' => $import_mode.' : '.$request->method()." ({$chunk_count}) dev",
                        'path' => $request->path().' Dev',
                        'ip' => $request->ip(),
                        'data' => json_encode($chunk),
                        'imported' => 0
                    ]);
                }
            }

            $this->processApiLogs();

        } catch (Exception $e) {
            return $e->getMessage();
        }

        return response()->json(['data' => "{$attendance_recs_count} raw records logged.", 'status' => "success", 'code' => 200], 200);
    }
    public function markSingleAttendance(Object $attendance_rec, $log_id){
        if(empty($attendance_rec)){
            return false;
        }

        $record = (array) $attendance_rec;

        if( !isset($attendance_rec->source) ){
            $rec_type = $this->processOneLog($record, $log_id);
        }
        $attendance_type =  !empty($attendance_rec->type) ? $attendance_rec->type : '';
        $attendance_date = $attendance_rec->time;
        $attendance_date_new = Carbon::createFromFormat('YmdHis', $attendance_date);
        $dateTime = $attendance_date_new->format('Y-m-d H:i:s');

        if(!$attendance_type){
            return false;
        }
        // attendance user
        $user = User::whereId($attendance_rec->bio_id)->with(
            [
                'policy' => function ($query) {
                    $query->where('status', 1)->latest();
                },
                'policy.pay_roll_settings',
                'policy.working_settings',
                'policy.working_day',
                'employee_details'
            ]
        )->first();

        if( !$user ){
            $request_method = ($_POST ? 'POST' : "GET");
            //AttendanceLogging::log("User not found. {$dateTime}", "User ID: {$attendance_rec->bio_id} ");
            return false;
        }

        // check policy
        try {
            $user->policy[0]->pay_roll_settings->generation_type;
        } catch (\Throwable $th) {
            AttendanceLogging::log("Policy Not Assigned From Machine : {$dateTime}", "User ID: ".$user->id.", ".$user->first_name.' '.$user->last_name);
        }

        // check if bio_id in attendance matches a user
        if ($user) {

            $attendanceStartTime = $attendance_date_new;
            if(!isset($user->policy[0])){
                //dd($user->id);
            }
            if (isset($user->policy[0]) && $user->policy[0]->pay_roll_settings->generation_type == 2) {
                $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start, 'America/New_York');

                $min_diff = $attendance_date_new->diffInMinutes($shift_start, false);
                $neg_status = false;
                if ($min_diff < 0) {
                    $neg_status = true;
                }
                $postive_time_diff = abs($min_diff);

                if ($attendanceStartTime->format('h:i A') < $shift_start->format('h:i A') || ($neg_status && $postive_time_diff <= $user->policy[0]->working_settings->late_c_l_t)) {
                    $status = AttendenceEnum::OnTime;
                } else {
                    $status = AttendenceEnum::Late;
                }

            } else {
                $policyStartTime = isset($user->policy[0]) ? Carbon::parse($user->policy[0]->working_settings->shift_start) : 0;

                // Calculate the duration in minutes
                $durationInMinutes = $attendanceStartTime->diffInMinutes($policyStartTime);

                // Convert the duration to hours
                $durationInHours = $durationInMinutes / 60;

                if(!isset($user->policy[0]) && isset($_GET['dbg']) && $_GET['dbg']>0){
                    dd($user);
                }
                if (isset($user->policy[0]->pay_roll_settings->working_hours) && $durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                    $status = AttendenceEnum::OnTime;
                } else {
                    $status = AttendenceEnum::Late;
                }
            }

            // attendence new workflow
            $attendance_today = Attendence::
            whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $attendance_date_new->format('Y-m-d'))
                ->where('user_id', $user->id)
                ->orderBy(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), 'desc')
                ->get();

            if( in_array($attendance_rec->type, ['CheckedOut', 'CheckOut']) ){
                $attendance_results[] = $this->checked_out($user, $attendance_rec, $attendance_today, $status, $log_id);

            }else if( in_array($attendance_rec->type, ['CheckedIn', 'CheckIn']) ){
                $attendance_results[] = $this->checked_in($user, $attendance_rec, $attendance_today, $status, $log_id);

            }

            return $attendance_results;
        }else{

            return false;

        }
    }
    private function organize_by_device_and_time($attendance_today, $attendance_rec, $mode = ''){
        $new_attendance = [];
        foreach ($attendance_today as $k => $attendance){
//            dd([$attendance_rec->device_id, $attendance->device_id]);
            if($attendance->device_id != $attendance_rec->device_id){
                continue;
            }
            $attendance_date_new = $this->timezone_fix($attendance_rec->time);; // Carbon::createFromFormat('YmdHis', $attendance_date);
            $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
            $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';
            $formated_date = $attendance_date_new->format('Y-m-d H:i:s');

            if(
                ($formated_date >= $arrival_time && !empty($leave_time) && $formated_date >= $leave_time)
                ||
                ($formated_date >= $arrival_time && empty($leave_time))
            ){
                $new_attendance[] = $attendance;
            }


            $row = [
                'arrival' => $arrival_time,
                'leave_time' => $leave_time,
                'formated_date' => $formated_date,
                'attendance_date_new' => $formated_date,
                'condition1' => ($formated_date >= $arrival_time && !empty($leave_time) && $formated_date >= $leave_time),
                'condition2' => ($formated_date >= $arrival_time && empty($leave_time)),
            ];
            $rows[] = $row;
        }

        return $new_attendance;
    }
    private function duplicate_leave_time($attendance_today, $attendance_rec){
        $new_attendance = [];
        foreach ($attendance_today as $k => $attendance){
            $attendance_date_new = $this->timezone_fix($attendance_rec->time);; // Carbon::createFromFormat('YmdHis', $attendance_date);
            $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
            $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';

            $formated_date = $attendance_date_new->format('Y-m-d H:i:s');

            $row = [
                'arrival' => $arrival_time,
                'leave_time' => $leave_time,
                'attendance_date_new' => $attendance_date_new->format('Y-m-d H:i:s'),
                'conclude' => ($formated_date == $arrival_time),
            ];
            $rows[] = $row;
            if( $formated_date == $leave_time ){
                $times[] = [$formated_date, $leave_time];
                $new_attendance[] = $attendance;
            }
        }
        //dd($rows);
        if( !empty($new_attendance) ){
            $new_attendance[0] = $new_attendance[ count($new_attendance)-1];
        }
        //dd([$new_attendance[ count($new_attendance)-1], $new_attendance, $formated_date, $attendance_rec, $attendance_today]);
        //dd([$new_attendance, $formated_date, $attendance_rec, $attendance_today]);
//        dd($new_attendance);
        return $new_attendance;
    }
    private function duplicate_arrivals($attendance_today, $attendance_rec){
        $new_attendance = [];
        foreach ($attendance_today as $k => $attendance){
            $attendance_date_new = $this->timezone_fix($attendance_rec->time);; // Carbon::createFromFormat('YmdHis', $attendance_date);
            $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
            $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';

            $formated_date = $attendance_date_new->format('Y-m-d H:i:s');

            $row = [
                'arrival' => $arrival_time,
                'leave_time' => $leave_time,
                'attendance_date_new' => $attendance_date_new->format('Y-m-d H:i:s'),
                'conclude' => ($formated_date == $arrival_time),
            ];
            $rows[] = $row;
            if( $formated_date == $arrival_time ){
                $new_attendance[] = $attendance;
            }
        }
//        dd($rows);
        if( !empty($new_attendance) ){
            $new_attendance[0] = $new_attendance[ count($new_attendance)-1];
        }
        return $new_attendance;
    }
    private function timezone_fix($this_date, $addHours = 4){
        return Carbon::createFromFormat('YmdHis', $this_date)->timezone('America/New_York')->addHours($addHours);
    }
    public function checked_in($user, $attendance_rec, $attendance_today, $status){
        $attendance_time = $this->timezone_fix($attendance_rec->time);//->timezone('America/New_York');
        $attendance_date_new = $this->timezone_fix($attendance_rec->time);//->timezone('America/New_York');
        $attendance_date_new->format('Y-m-d H:i:s');
        $dateTime = $attendance_date_new->format('Y-m-d');

        // organize by device_id and arrival_time
        $this_device_attendance = $this->organize_by_device_and_time($attendance_today, $attendance_rec);
        $duplicate_check_ins = $this->duplicate_arrivals($attendance_today, $attendance_rec);

        $attendance = [];
        if( !empty($this_device_attendance) ){
            $attendance = $this_device_attendance[0];
        }
        $full_name = ($user->first_name ?? '').' '.($user->last_name ?? '');
        // create salary if it is a new record
        if ( empty($attendance) ) {
            if(isset($user->employee_details->salary)){
                Salary::create([
                    'date' => $dateTime,
                    'salary' => $user->employee_details->salary,
                    'user_id' => $user->id,
                ]);
                //AttendanceLogging::log("Api CheckIn : created salary of : {$dateTime}", "Created Salary for '{$full_name}' : #{$attendance_rec->bio_id}");
            }
        }

        if(!isset($user->policy[0])){
            dd($user->id);
        }
        $attendance_record = [
            'user_id' => $user->id,
            'arrival_time' => $attendance_date_new->subHours(4),
//            'arrival_time' => $attendance_date_new,
            'arrival_date' => $attendance_date_new->format('Y-m-d'),
            'policy_id' => $user->policy[0]->id ?? 6,
            'status' => $status,
            'device_id' => $attendance_rec->device_id,
        ];

        $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
        $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';
        $newAttend = [];

        if ( count($duplicate_check_ins) > 0 ) {
            //AttendanceLogging::log("Api CheckIn Duplicate Time : {$dateTime}", "Skipped CheckIn for '{$full_name}' : #{$attendance_rec->bio_id}");
            $newAttend[] = "Api CheckIn Duplicate Time : {$dateTime} - Skipped CheckIn for User : '{$full_name}' : #{$attendance_rec->bio_id}";

        }else if( count($duplicate_check_ins) <= 0 && (empty($this_device_attendance) || !empty($leave_time)) ){
            // no duplicates and no prior attendance or last attendence was good
            $newAttend[] = Attendence::create($attendance_record);
        }else if ( !empty($arrival_time) && empty($leave_time) && count($duplicate_check_ins) <= 0 ) {
            $attendance_date_new2 = $attendance_date_new->subHours(4);

            $bad_attendance = $attendance_date_new2;
            $bad_attendance = $bad_attendance->addHours(4)->format('Y-m-d H:i:s');

            $attendance_record = [
                'user_id' => $user->id,
                'arrival_time' => $attendance_time->subHours(4),
                'arrival_date' => $attendance_time->format('Y-m-d'),
                'policy_id' => $user->policy[0]->id,
                'status' => $status,
                'device_id' => $attendance_rec->device_id,
            ];
            //AttendanceLogging::log("Updated Api CheckIn", "Bad Logout at: {$bad_attendance} for '{$full_name}' : #{$attendance_rec->bio_id}");
            $newAttend[] = Attendence::whereId($attendance->id)->update([
                'leave_time' => $attendance->arrival_time,
                'leave_date' => $attendance->arrival_time->format('Y-m-d'),
                'remarks' => "Policy-enforced Check-out with 0 time"
            ]);
            $policy_time = $attendance->arrival_time->format('H:i:s');

            $attendance_record['remarks'] = "Bad Check-In, did not check out for the last check-in at : {$policy_time}";
            //AttendanceLogging::log("Added Api CheckIn", "Added for '{$full_name}' : #{$attendance_rec->bio_id}");
            $newAttend[] = Attendence::create($attendance_record);
        }
        return $newAttend;
    }

    public function checked_out($user, $attendance_rec, $attendance_today, $status){
        $attendance_time = $this->timezone_fix($attendance_rec->time);//->timezone('America/New_York');
        $attendance_date_new = $this->timezone_fix($attendance_rec->time);//->timezone('America/New_York');
        $attendance_date_new->format('Y-m-d H:i:s');
        $dateTime = $attendance_date_new->format('Y-m-d');


        // organize by device_id and arrival_time
        $this_device_attendance = $this->organize_by_device_and_time($attendance_today, $attendance_rec, 'CheckOut');
        $duplicate_check_outs = $this->duplicate_leave_time($attendance_today, $attendance_rec);
        $duplicate_count = count($duplicate_check_outs);
        $full_name = ($user->first_name ?? '').' '.($user->last_name ?? '');

        $attendance = [];
        if( !empty($this_device_attendance) ){
            //$attendance = $this_device_attendance[ count($this_device_attendance)-1 ];
            $attendance = $this_device_attendance[0];
        }

        if(!empty($_POST['dbg']) && $_POST['dbg']==2) { dd(['CheckOut 2', $attendance]); }

        $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
        $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';

        $attendance_record = [
            'user_id' => $user->id,
            'arrival_time' => $attendance_date_new,
            'arrival_date' => $attendance_date_new->format('Y-m-d'),
            'policy_id' => $user->policy[0]->id,
            'status' => $status,
            'device_id' => $attendance_rec->device_id,
        ];

        //dd([$duplicate_count, $duplicate_check_outs, $this_device_attendance]);
        //dd(['faisal 2', $duplicate_count, count($duplicate_check_outs), $duplicate_check_outs]);
        $is_duplicate = $this->checkDuplicateAttendance($attendance_rec);
        //dd([$full_name, $is_duplicate, $duplicate_count]);
        if($duplicate_count == 0){
            //dd([$full_name,$duplicate_count, $attendance, $attendance_record, $attendance_rec, $attendance_today]);
            //dd([$user->id,$duplicate_count, $attendance_rec, $attendance_today]);
        }

        $leave_time = !empty($attendance) ? $attendance->getAttributes()['leave_time'] : '';
        $arrival_time = !empty($attendance) ? $attendance->getAttributes()['arrival_time'] : '';
        $newAttend = [];
        if ( count($duplicate_check_outs) > 0 ) {
            // skip if it is a duplicate checkout
            //AttendanceLogging::log("Api CheckOut Duplicate Time : {$dateTime}", "Skipped CheckOut for '{$full_name}' : #{$attendance_rec->bio_id}");

        }else if( empty($this_device_attendance) || !empty($leave_time) ){
            //dd([$user->id,$duplicate_count, $attendance_rec, $attendance_today]);
            // bad checkout : if we have no prior rec for the day
            $formated_time_only = $formated_time = $attendance_record['arrival_time'];
            $formated_time = $formated_time->format('Y-m-d H:i:s');

            $formated_time_only = $formated_time_only->timezone('America/New_York')->addHours(4)->format('H:i:s');

            $attendance_record['arrival_time'] = $formated_time;
            $attendance_record['leave_time'] = $attendance_record['arrival_time'];

            $attendance_record['arrival_date'] = $attendance_record['arrival_date'];
            $attendance_record['leave_date'] = $attendance_record['arrival_date'];

            $attendance_record['remarks'] = "Bad Check-Out, did not check in for the last check-out at :{$formated_time_only}";
            if(empty($this_device_attendance)){
                $attendance_record['remarks'] = "Bad Check-Out, Policy-enforced Check-out with 0 time.";
            }

            //AttendanceLogging::log("Api CheckOut : {$attendance_record['arrival_time']} by {$full_name}", $attendance_record['remarks']);
            $newAttend[] = Attendence::create($attendance_record);

        }else if ( empty($leave_time) && !empty($attendance) ) {
            $attendance_date_new2 = $attendance_date_new->subHours(4);

            $bad_attendance = $attendance_date_new2;
            $bad_attendance = $bad_attendance->format('Y-m-d H:i:s');

            $attendance_record = [
                'leave_time' => $attendance_time,
                'leave_date' => $attendance_time->format('Y-m-d'),
            ];
            //AttendanceLogging::log("Api Checked Out", "'{$full_name}' : #{$attendance_rec->bio_id} Logout at {$attendance_rec->device_id} : on {$bad_attendance}");
            $newAttend[] = Attendence::whereId($attendance->id)->update($attendance_record);
        }else if ( empty($arrival_time) && !empty($leave_time) ) {
            $attendance_date_new2 = $attendance_date_new->subHours(4);

            $bad_attendance = $attendance_date_new2;
            $bad_attendance = $bad_attendance->addHours(4)->format('Y-m-d H:i:s');

            $attendance_record = [
                'user_id' => $user->id,
                'arrival_time' => $attendance_time->subHours(4),
                'arrival_date' => $attendance_time->format('Y-m-d'),
                'policy_id' => $user->policy[0]->id,
                'status' => $status,
                'device_id' => $attendance_rec->device_id,
            ];
            //AttendanceLogging::log("Updated Api CheckIn", "Bad Logout at: {$bad_attendance} for User : {$attendance_rec->bio_id}");
            $newAttend[] = Attendence::whereId($attendance->id)->update([
                'leave_time' => $attendance->arrival_time,
                'leave_date' => $attendance->arrival_time->format('Y-m-d'),
                'remarks' => "Policy-enforced Check-out with 0 time"
            ]);

            $attendance_record['remarks'] = "Bad CheckIn, did not logout for last checkIn";
            //AttendanceLogging::log("Added Api CheckIn", "Added for '{$full_name}' : #{$attendance_rec->bio_id}");
            $newAttend[] = Attendence::create($attendance_record);
        }
        return $newAttend;
    }

    public function checkDuplicateAttendance($attendence_rec) {
        // Extract values from the incoming array
        $user_id = $attendence_rec->bio_id;  // 'bio_id' corresponds to 'user_id'
        $time = $attendence_rec->time;       // time in 'YYYYMMDDHHMMSS' format
        $type = $attendence_rec->type;       // type can be 'CheckIn' or 'CheckOut'

        // Format the time into the correct format (YYYY-MM-DD HH:MM:SS)
        $formattedTime = Carbon::createFromFormat('YmdHis', $time)->format('Y-m-d H:i:s');

        //dd([$formattedTime, $time]);
        // Define the time field to check based on type
        $timeField = $type === 'CheckIn' ? 'arrival_time' : 'leave_time';

        // Query to check for duplicates in the attendances table
        $duplicate = DB::table('attendences')
            ->where('user_id', $user_id)
            ->where($timeField, $formattedTime)
            ->count();

        // Return true if duplicate exists, false otherwise
        return $duplicate;
    }
    public function processOneLog( $record, $log_id ){
        $specifiedUserId = '{{user_id}}';
        $specifiedTimeSubstring = '{{time}}';
//        dd([$specifiedUserId, $specifiedTimeSubstring, $record]);

        if(empty($record['bio_id']) || empty($record['time']) || empty($record['device_id']) || empty($record['type']) ){
            return false;
        }else {
            $record['time'] = str_replace('{{date}}', '20240801', $record['time']);
            $record['bio_id'] = str_replace('{{user_id}}', '0', $record['bio_id']);
            //dd($record['time']);
            $deviceLogData = [
                'device_id' => $record['device_id'],
                'time' => Carbon::createFromFormat('YmdHis', $record['time'])->format('Y-m-d H:i:s'),
                'date' => Carbon::createFromFormat('YmdHis', $record['time'])->format('Y-m-d'),
                'log_id' => $log_id,
                'user_id' => $record['bio_id'], // Mapping bio_id to user_id
                'type' => $record['type'],
                'imported' => 0, // Marking as imported (optional if you want to keep track)
            ];
//            dd([$record, $deviceLogData]);
            $exists = DeviceLog::where('user_id', $deviceLogData['user_id'])
                ->where('time', $deviceLogData['time'])
                ->where('type', $deviceLogData['type'])
                ->where('device_id', $deviceLogData['device_id'])
                ->exists();

            if (!$exists) {
                DeviceLog::create($deviceLogData);
            }
        }
    }

    // new process algo
    public function processOneLogNew($record, $log_id) {
        // Ensure all necessary fields are set
        if (!isset($record['device_id'], $record['time'], $record['bio_id'], $record['type'])) {
            return false;
        }
//        $specifiedUserId = '{{user_id}}';
//        $specifiedTimeSubstring = '{{time}}';
//
//        // Skip records that match the specified conditions
//        if ((isset($record['time']) && strpos($record['time'], $specifiedTimeSubstring) !== false) ||
//            (isset($record['bio_id']) && $record['bio_id'] == $specifiedUserId)) {
//            return false;
//        }

        // Create a timestamp from the 'time' field
        $formattedTime = Carbon::createFromFormat('YmdHis', $record['time'])->format('Y-m-d H:i:s');
        $deviceLogData = [
            'device_id' => $record['device_id'],
            'time' => $formattedTime,
            'date' => Carbon::parse($formattedTime)->format('Y-m-d'),
            'log_id' => $log_id,
            'user_id' => $record['bio_id'], // Mapping bio_id to user_id
            'type' => $record['type'],
            'imported' => 0, // Marking as imported (optional if you want to keep track)
        ];

        // Check if a record with the same unique combination exists
        $exists = DeviceLog::where([
            ['user_id', '=', $deviceLogData['user_id']],
            ['device_id', '=', $deviceLogData['device_id']],
            ['time', '=', $deviceLogData['time']],
            ['type', '=', $deviceLogData['type']]
        ])->exists();

        // Only insert the record if it doesn't exist
        if (!$exists) {
            DeviceLog::create($deviceLogData);
        }
        return true;
    }
    public function calculateApiTimes(){
        //->orderBy('time', 'desc')->limit(100)
        //->where('user_id', 38)
        //->orderBy('time', 'desc')
        $apiLogs = DeviceLog::where('imported', 0)
            ->get();

        foreach ($apiLogs as $k => $device_log){
            $attendance = [
                'device_id' => $device_log->device_id ?? '',
                'time' => $device_log->time ? str_replace(' ', '', str_replace(':', '', str_replace('-', '', $device_log->time))) : '',
                'bio_id' => $device_log->user_id ?? '',
                'type' => $device_log->type ?? '',
                'source' => 'DeviceLogs'
            ];

            if( empty($attendance['device_id']) || empty($attendance['time']) || empty($attendance['bio_id']) || empty($attendance['type']) ){
                continue;
            }

            //$this->markSingleAttendance((object) $attendance);
            $device_log->imported = 1;
            $device_log->save();
        }
        $k = ($k ?? 0);
        $count = $k == 0 ? 0 : $k+1;
        return response()->json(['data' =>  "{$count} Completed", 'status' => "success", 'code' => 200], 200);

        //echo "Completed" ;
    }
    public function reCountAttendance(){
        $apiLogs = ApiLog::where('method', 'like', '%bulk%')
            ->whereIn('imported', [0, 1])
            ->get();

        //dd($apiLogs);
        foreach ($apiLogs as $attendance){
            $attendance_obj = $attendance->data;
            $attendance_recs = json_decode($attendance_obj);
            $attendance_recs_count = count( (array) $attendance_recs);


            // Split the records into chunks of 20
            // Only create chunks for data above 2024-08-25
            $cutout_date = $attendance['created_at']->format('Y-m-d');
            if($cutout_date > '2024-08-07'){

                if($attendance_recs_count > 20){
                    //dd($attendance['created_at']->format('Y-m-d'));
                    $chunks = array_chunk((array) $attendance_recs, 20);

                    foreach ($chunks as $chunk) {
                        $chunk_count = count($chunk);
                        $this_ApiLog = ApiRaw::create([
                            'method' => "bulk : POST  ({$chunk_count})",
                            'path' => $attendance->path,
                            'ip' => $attendance->ip,
                            'data' => json_encode($chunk),
                            'imported' => 0
                        ]);
                    }

                    $attendance->method = "bulk : POST ({$attendance_recs_count})";
                    $attendance->imported = 2;
                    $attendance->save() ;
                }else{
                    $this_ApiLog = ApiRaw::create([
                        'method' => "bulk : POST  ({$attendance_recs_count})",
                        'path' => $attendance->path,
                        'ip' => $attendance->ip,
                        'data' => json_encode($attendance_recs),
                        'imported' => 0
                    ]);
                    $attendance->method = "bulk : POST ({$attendance_recs_count})";
                    $attendance->imported = 2;
                    $attendance->save() ;
                }
            }
        }
        echo count($apiLogs).' Imported and ready to be processed';
    }
    public function processExcel($floor = 'floor2')
    {
        if( !in_array($floor, ['floor1', 'floor2']) ){
            exit('Invalid Floor reference.');
        }
        // Dynamically build the file path based on the floor parameter
        $filePath = storage_path('app/excel/' . $floor . '.xlsx');
        // Load the Excel file
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Process each row
        foreach ($rows as $key => $row) {
            // Skip header
            if ($key == 0) { continue; }

            $log_device = $row[1];
            $log_time = Carbon::createFromFormat('YmdHis', $row[2])->format('Y-m-d H:i:s');
            $log_date = Carbon::createFromFormat('YmdHis', $row[2])->format('Y-m-d');
            $log_user = $row[3];
            $log_type = $row[4];

            $new_log = [
                ['device_id', $log_device],
                ['time', $log_time],
                ['user_id', $log_user],
                ['type', $log_type],
            ];
            $new_log_row = [
                'device_id' => $log_device,
                'time'      => $log_time,
                'date'      => $log_date,
                'user_id'    => $log_user,
                'type'      => $log_type,
                'log_id'      => 0
            ];
            // Check if entry already exists
            $exists = DeviceLog::where($new_log)->exists();

//            dd([$new_log, $exists, $new_log_row]);
            if (!$exists) {
                // Insert if not exists
                DeviceLog::create($new_log_row);
                Log::channel('excel_import')->info("New log Added.  ( {$log_time} : {$log_type} for {$log_user} at {$log_device} )");
                continue;
            }else{
                Log::channel('excel_import')->info("Already Exists. ( {$log_time} : {$log_type} for {$log_user} at {$log_device} )");
            }
        }

        return response()->json(['message' => 'Excel file processed successfully'], 200);
    }

    public function processApiLogs()
    {
        // Fetch records from api_logs where method contains 'bulk : POST' and imported is 0
        $apiRawLogs = ApiRaw::where('method', 'like', '%bulk%')
            ->where('imported', 0)
            ->orderBy('id')
            ->get();
        if(isset($_GET['dbg']) && $_GET['dbg']==1){
            dd($apiRawLogs);
        }
        foreach ($apiRawLogs as $k => $apiLog) {
            $data = json_decode($apiLog->data, true);
            $data_count = count((array) $data);
            $apiLog_method = "bulk : POST ({$data_count})";
            $log_id = $apiLog->id;

            if (is_array($data)) {
                if (isset($data->type)) {
                    $attendance_rec = (object) $data;
                    // create device log
                    $this->processOneLog($data, $log_id);
                    // create attendence records
                    //$this->markSingleAttendance($attendance_rec, $log_id);
                }else{
                    foreach ($data as $record) {
                        $attendance_rec = (object) $record;
                        // create device log
                        $this->processOneLog($record, $log_id);
                        // create attendence records
                        //$this->markSingleAttendance($attendance_rec, $log_id);
                    }
                }
            }

            $apiLog->method = $apiLog_method;
            $apiLog->imported = 1;
            $apiLog->save();
        }

        $raw_logs_count = count($apiRawLogs);
        return "Processing Complete for {$raw_logs_count}!";
    }

    public function getapilogs(Request $request)
    {
        $perPage = 10; // Adjust per page count as needed
        $page = $request->query('page', 1); // Get page from query, default to 1

        $logs = ApiLog::orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        $response = [
            'total_records' => $logs->total(),
            'total_pages' => $logs->lastPage(),
            'page' => $page,
            'data' => $logs->items(),
        ];

        return response()->json($response);
    }
    public function markAttendanceOld(Request $request)
    {

        try {
            $data = $request->all();

            $attendance = json_decode($data['attendance']);

            ApiLog::create([
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'data' => json_encode(json_decode($data['attendance'], true)),
            ]);

            // $dateTime = Carbon::createFromFormat('YmdHis', $attendance->time, 'America/New_York');
            $dateTime = Carbon::now()->timezone('America/New_York');

            $user = User::whereId($attendance->bio_id)->with(
                [
                    'policy' => function ($query) {
                        $query->where('status', 1)->latest();
                    },
                    'policy.pay_roll_settings',
                    'policy.working_settings',
                    'policy.working_day',
                    'employee_details'
                ]
            )->first();

            try {
                $user->policy[0]->pay_roll_settings->generation_type;
            } catch (\Throwable $th) {
                AttendanceLogging::log("Policy Not Assigned From Machine : {$dateTime}", "User ID: ".$user->id.", ".$user->first_name.' '.$user->last_name);
            }
            if ($user) {
                $attendance = Attendence::
                whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'),
                    $dateTime->format('Y-m-d'))
                    ->where('user_id', $user->id)
                    ->last();

                if ($attendance) {
                    $attendance2 = Attendence::
                    whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'),
                        $dateTime->format('Y-m-d'))
                        ->whereNull('leave_time')
                        ->where('user_id', $user->id)
                        ->first();
                    if ($attendance2) {
                        AttendanceLogging::log('Api Check Out', 'Employee Checked Out');
                        $this->check_out($user, $dateTime, $attendance2->id);
                    } else {
                        AttendanceLogging::log('Api Check In', 'Employee Checked In');
                        $this->check_in($user, $dateTime);
                    }
                } else {
                    AttendanceLogging::log('Api Check In', 'Employee Checked In');
                    $this->check_in($user, $dateTime);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return response()->json(['data' => $request->all(), 'status' => "success", 'code' => 200], 200);
    }

    /* private function check_in($user, $dateTime)
     {
         Salary::create([
             'date' => $dateTime->format('Y-m-d'),
             'salary' => $user->employee_details->salary,
             'user_id' => $user->id,
         ]);

         if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
             $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start, 'America/New_York');

             // dd($shift_start, $markdate);
             // dd($markdate->format('h:i A'), $shift_start);
             $min_diff = $dateTime->diffInMinutes($shift_start, false);
             // dd($min_diff);

             // Calculate the time difference in seconds
             // $timeDifferenceInSeconds = $markdate->diffInSeconds($shift_start);

             // // Convert seconds to minutes
             // $timeDifferenceInMinutes = $timeDifferenceInSeconds / 60;

             // dd($timeDifferenceInMinutes);
             $neg_status = false;
             if ($min_diff < 0) {
                 $neg_status = true;
             }
             $postive_time_diff = abs($min_diff);
             // dd($postive_time_diff);


             if ($dateTime->format('h:i A') < $shift_start->format('h:i A') || ($neg_status && $postive_time_diff <= $user->policy[0]->working_settings->late_c_l_t)) {
                 // dd($shift_start);
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }

             // dd($shift_start, "Second");
         } else {
             $attendanceStartTime = $dateTime;
             $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start);

             // Calculate the duration in minutes
             $durationInMinutes = $attendanceStartTime->diffInMinutes($shift_start);

             // Convert the duration to hours
             $durationInHours = $durationInMinutes / 60;

             if ($durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                 $status = AttendenceEnum::OnTime;
             } else {
                 $status = AttendenceEnum::Late;
             }
         }

         if ($user->policy) {
             $mainDate = $dateTime;
             $attendence = Attendence::where('user_id', $user->id)
                 // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
                 ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
                 ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
                 ->first();
             // $attendence = Attendence::where('user_id', $user->id)
             //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
             //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
             //     ->first();
             // dd($attendence);
             if ($attendence) {
                 $attendence->status = $status;
                 $attendence->save();
             }
             $check_attendance = Attendence::whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
                 ->whereNull('leave_time')->first();
             if (!$check_attendance) {

                 $save_time = $dateTime;
                 if (($dateTime->format('h:i A') < $shift_start->format('h:i A')) && ($user->policy[0]->working_settings->early_arrival_policy == 2)) {
                     $save_time = $shift_start;
                 }
                 Attendence::create([
                     'user_id' => $user->id,
                     'arrival_time' => $save_time,
                     'arrival_date' => $save_time->format('Y-m-d'),
                     'policy_id' => $user->policy[0]->id,
                     'status' => $status,
                 ]);
             }
         }
         // dd($status, $dateTime, $shift_start);
     }*/

    private function check_out($user, $dateTime, $id)
    {
        if ($id == null) {
            return false;
        }
        $markdate = $dateTime;
        // dd($markdate);

        if ($user->policy) {
            $markdate = $markdate->utc();

            $newAttend = Attendence::whereId($id)->update([
                'user_id' => $user->id,
                'leave_time' => $markdate,
                'leave_date' => $markdate->format('Y-m-d'),
                'policy_id' => $user->policy[0]->id,
            ]);

            //AttendanceLogging::log('Api Check Out Attendance', $newAttend->toArray());
            //AttendanceLogging::log('Api Check Out Employee', $user->toArray());

        } else {
            return false;
        }
        return true;
    }

    public function check_in($user, $dateTime)
    {
        $markdate = Carbon::now()->setTimezone('America/New_York');

        Salary::create([
            'date' => $markdate->format('Y-m-d'),
            'salary' => $user->employee_details->salary,
            'user_id' => $user->id,
        ]);

        try {
            $user->policy[0]->pay_roll_settings->generation_type;
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => 'You do not have a policy assigned.'];
        }


        // HRMS policy start and end times in AM/PM format
        $policyStartTime = $user->policy[0]->working_settings->shift_start;
        $policyEndTime = $user->policy[0]->working_settings->shift_close;


        // Marked attendance date and time
        // $markedDateTime = '2024-02-06 00:59:00'; // Example date and time
        $markedDateTime = $markdate; // Example date and time

        // Convert HRMS policy times to Carbon objects
        $policyStart = Carbon::createFromFormat('h:iA', $policyStartTime, 'America/New_York');
        $policyEnd = Carbon::createFromFormat('h:iA', $policyEndTime, 'America/New_York');

        $markedDateTime = Carbon::parse($markedDateTime);
        if ($policyStart->format('a') == 'pm' && $policyEnd->format('a') == 'am') {
            // dd("Two Day");
            AttendanceLogging::log('Api Two Day Mark', 'Employee Checked In');
            AttendanceLogging::log('Api Two Day Mark User', $user->toArray());
            $this->twoDayMark($markedDateTime, $user);
        } else {
            // dd("One Day");
            AttendanceLogging::log('Api One Day Day Mark', 'Employee Checked In');
            AttendanceLogging::log('Api One Day Day Mark User', $user->toArray());
            $this->oneDaymark($user);
        }

        return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];

    }

    private function oneDaymark($user)
    {
        $markdate = Carbon::now()->timezone('America/New_York');
        // Attendence Base
        if ($user->policy[0]->pay_roll_settings->generation_type == 2) {
            $shift_start = Carbon::parse($user->policy[0]->working_settings->shift_start, 'America/New_York');

            // dd($shift_start, $markdate);
            // dd($markdate->format('h:i A'), $shift_start);
            $min_diff = $markdate->diffInMinutes($shift_start, false);
            // dd($min_diff);


            // Calculate the time difference in seconds
            // $timeDifferenceInSeconds = $markdate->diffInSeconds($shift_start);

            // // Convert seconds to minutes
            // $timeDifferenceInMinutes = $timeDifferenceInSeconds / 60;

            // dd($timeDifferenceInMinutes);
            $neg_status = false;
            if ($min_diff < 0) {
                $neg_status = true;
            }
            $postive_time_diff = abs($min_diff);
            // dd($postive_time_diff);


            if ($markdate->format('h:i A') < $shift_start->format('h:i A') || ($neg_status && $postive_time_diff <= $user->policy[0]->working_settings->late_c_l_t)) {
                // dd($shift_start);
                $status = AttendenceEnum::OnTime;
            } else {
                $status = AttendenceEnum::Late;
            }

            // dd($shift_start, "Second");
        } else {
            $attendanceStartTime = $markdate;
            $policyStartTime = Carbon::parse($user->policy[0]->working_settings->shift_start);

            // Calculate the duration in minutes
            $durationInMinutes = $attendanceStartTime->diffInMinutes($policyStartTime);

            // Convert the duration to hours
            $durationInHours = $durationInMinutes / 60;

            if ($durationInHours >= $user->policy[0]->pay_roll_settings->working_hours) {
                $status = AttendenceEnum::OnTime;
            } else {
                $status = AttendenceEnum::Late;
            }
        }
        if ($user->policy) {
            $mainDate = $markdate;
            $attendence = Attendence::where('user_id', $user->id)
                // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
                ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
                ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
                ->first();
            // $attendence = Attendence::where('user_id', $user->id)
            //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
            //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
            //     ->first();
            // dd($attendence);
            if ($attendence) {
                // $attendence->status = $status;
                // $attendence->save();
                AttendanceLogging::log('Api Delete Attendance', $attendence->toArray());
                AttendanceLogging::log('Api Delete Attendance User', $user->toArray());
                $attendence->delete();
            }
            $check_attendance = Attendence::where('user_id', $user->id)->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
                ->whereNull('leave_time')->first();
            if (!$check_attendance) {
                $save_time = $markdate;
                if (($markdate->format('h:i A') < $shift_start->format('h:i A')) && ($user->policy[0]->working_settings->early_arrival_policy == 2)) {
                    $save_time = $shift_start;
                }
                // dd($save_time);
                $newAttend = Attendence::create([
                    'user_id' => $user->id,
                    'arrival_time' => $save_time,
                    'arrival_date' => $save_time->format('Y-m-d'),
                    'policy_id' => $user->policy[0]->id,
                    'status' => $status,
                ]);

                // real time attendence marking
                AttendanceLogging::log('Api Mark Attendance Attendance', $newAttend->toArray());
                AttendanceLogging::log('Api Mark Attendance User', $user->toArray());
            }

        } else {
            // return ['status' => false, 'message' => 'Some Error Occurred.'];
        }
        // return ['status' => true, 'message' => 'Your Attendence Checkout Successfully.'];
    }

    private function twoDayMark($markedDateTime, $user)
    {
        // $policyStartTime = '11:40AM';
        // $policyEndTime = '10:30PM';

        // Latency time in minutes
        $latencyMinutes = $user->policy[0]->working_settings->late_c_l_t;

        // $markedDateTime = '2024-02-06 23:49:00'; // Example date and time

        $markedDateTime = Carbon::parse($markedDateTime);

        // Convert HRMS policy times to Carbon objects
        // $policyStart = Carbon::createFromFormat('h:iA', $policyStartTime);
        // $policyEnd = Carbon::createFromFormat('h:iA', $policyEndTime);
        $policyStart = Carbon::createFromFormat('h:iA', $user->policy[0]->working_settings->shift_start, 'America/New_York');
        $policyEnd = Carbon::createFromFormat('h:iA', $user->policy[0]->working_settings->shift_close, 'America/New_York');

        // dd($markedDateTime, $policyStart, $policyEnd);

        // Adjust policy start time by adding latency time
        $policyStartWithLatency = $policyStart->copy()->addMinutes($latencyMinutes);

        // Check if policy end time is before policy start time (spans two days)
        if ($policyEnd->lessThan($policyStart)) {
            // If so, adjust marked time if it's after policy start time but before midnight
            if ($markedDateTime->greaterThanOrEqualTo($policyStart) && $markedDateTime->lte(Carbon::parse('23:59'))) {
                $markedDateTime->subDay();
            }
        }

        // dd($markedDateTime->greaterThan($policyStart), $markedDateTime->lte($policyStartWithLatency), $markedDateTime, $policyStartWithLatency, $policyStart, $policyEnd, $markedDateTime->gt($policyEnd));
        // Check if marked attendance time is before policy start time
        // dd($markedDateTime, $policyStartWithLatency, $policyEnd, $markedDateTime->format('H:i:s') > $policyEnd->format('H:i:s'));
        // dd($markedDateTime->lte($policyStartWithLatency), $markedDateTime->gt($policyEnd), $markedDateTime->gt($policyStartWithLatency));
        $status = '';
        if ($markedDateTime->format('H:i:s') <= $policyStartWithLatency->format('H:i:s') && $markedDateTime->format('H:i:s') > $policyEnd->format('H:i:s')) {
            echo "Attendance marked on time.";
            $status = AttendenceEnum::OnTime->value;
        } elseif ($markedDateTime->format('H:i:s') > $policyStartWithLatency->format('H:i:s') || $markedDateTime->format('H:i:s') <= $policyEnd->format('H:i:s')) {
            echo "Attendance marked late.";
            $status = AttendenceEnum::Late->value;
        } else {
            echo "Attendance marked late or outside of policy hours.";
            $status = AttendenceEnum::Late->value;
            // $status = AttendenceEnum::Holiday->value;
        }

        $mainDate = $markedDateTime;
        $attendence = Attendence::where('user_id', $user->id)
            // ->whereDate('arrival_time', $mainDate->utc()->format('Y-m-d'))
            ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "-04:00")'), $mainDate->format('Y-m-d'))
            ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
            ->first();
        // $attendence = Attendence::where('user_id', $user->id)
        //     ->whereDate(DB::raw('CONVERT_TZ(arrival_time, "+00:00", "+05:00")'), $mainDate->format('Y-m-d'))
        //     ->whereIn('status', [AttendenceEnum::Leave->value, AttendenceEnum::Absent->value, AttendenceEnum::Holiday->value])
        //     ->first();
        // dd($attendence);
        if ($attendence) {
            // $attendence->status = $status;
            // $attendence->save();
            AttendanceLogging::log('Api Delete Attendance', $attendence->toArray());
            AttendanceLogging::log('Api Delete Attendance User', $user->toArray());
            $attendence->delete();
        }

        // dd($status);
        $newAttend = Attendence::create([
            'user_id' => $user->id,
            'arrival_time' => $markedDateTime,
            'arrival_date' => $markedDateTime->format('Y-m-d'),
            'policy_id' => $user->policy[0]->id,
            'status' => $status,
        ]);
        AttendanceLogging::log('Api Mark Attendance', $newAttend->toArray());
        AttendanceLogging::log('Api Mark Attendance User', $user->toArray());

    }
}
