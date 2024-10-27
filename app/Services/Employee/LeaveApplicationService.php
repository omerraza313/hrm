<?php

namespace App\Services\Employee;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\LeavePlan;
use App\Jobs\ApplyLeaveJob;
use App\Models\Applyleaves;
use App\Models\AssignLeave;
use App\Models\LeaveAdjust;
use App\Mail\ApplyLeaveMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\Admin\ApplyLeaveEvent;
use App\Events\Employee\DeleteEmployeeEvent;

class LeaveApplicationService {
    public function get_leave_application_view_data(): array|object
    {
        $employee = User::where('id', Auth::user()->id)->with('employee_details')->first();
        $leaves = LeavePlan::whereIn('leave_gender_type', [$employee->employee_details->gender, 'both'])
            ->where('c_from_date', '<=', Carbon::now()->format('n'))
            ->where('c_to_date', '>=', Carbon::now()->format('n'))
            ->with('unit')
            ->get();

        $assignLeaves = AssignLeave::where('user_id', $employee->id)
            ->whereHas('leave_plan', function ($query) {
                $query->where('c_from_date', '<=', Carbon::now()->format('n'))->where('c_to_date', '>=', Carbon::now()->format('n'));
            })
            ->with(['leave_plan', 'leave_plan.unit'])->get();
        $applyLeaves = Applyleaves::where('user_id', $employee->id)->with([
            'adjust_leaves',
            'adjust_leaves.leave_plan' => function ($query) {
                $query->withTrashed();
            }
        ])->get();

//         dd($applyLeaves);

        return compact('leaves', 'applyLeaves', 'assignLeaves');
    }
    public function get_leave_plans(): array|object
    {
        $employee = User::where('id', Auth::user()->id)->with('employee_details')->first();
        $leaves = LeavePlan::whereIn('leave_gender_type', [$employee->employee_details->gender, 'both'])
            ->where('c_from_date', '<=', (int) Carbon::now()->format('n'))
            ->where('c_to_date', '>=', (int) Carbon::now()->format('n'))
            ->with('unit')
            ->get();
        return $leaves;
    }
    public function store_leave(array|object $data): bool
    {
        $employee = User::find(Auth::user()->id);
        $remain_status = $this->remaning_and_limit_query($data);
        if (!$remain_status) {
            return false;
        }

        $image = "";
        if (isset($data['add_leave_document']) && $data['add_leave_document']) {
            $image = time() . '.' . $data['add_leave_document']->extension();
            $data['add_leave_document']->move('images/leaves/', $image);
        }

        $add_leave_from = Carbon::createFromFormat('m/d/Y', $data['add_leave_from'])->format('d/m/Y'); // Parse the date using Carbon
        $add_leave_to = Carbon::createFromFormat('m/d/Y', $data['add_leave_to'])->format('d/m/Y'); // Parse the date using Carbon

        $apply_leaves = Applyleaves::create([
            'subject' => $data['add_leave_subject'],
            'body' => $data['add_leave_body'],
            'leave_from' => $add_leave_from,
            'leave_upto' => $add_leave_to,
            'document' => $image,
            'status' => 0,
            'user_id' => Auth::user()->id
        ]);

        for ($index = 0; $index < count($data['add_leave_adjust_date']); $index++) {
            $plan = LeavePlan::whereId($data['add_leave_adjust_plan'][$index])->first();
            if ($plan->unit_id == 2) {
                $qty = $data['add_leave_adjust_hour'][$index] ?? 0;
            } else {
                if ((isset($data['add_leave_half_day'][$index]) && $data['add_leave_half_day'][$index]) == "true") {
                    $qty = 0.5;
                } else {
                    $qty = 1;
                }
            }
            LeaveAdjust::create([
                'leave_date' => $data['add_leave_adjust_date'][$index],
                'leave_plan_id' => $data['add_leave_adjust_plan'][$index],
                'applyleave_id' => $apply_leaves->id,
                'quantity' => $qty
            ]);
        }
        try {
            event(new ApplyLeaveEvent($employee, $apply_leaves->id));
        } catch (Exception $e) {
            // return false;
        }
        // $admins = User::role([RolesEnum::Admin->value, RolesEnum::SuperAdmin->value])->get();
        // foreach ($admins as $admin) {
        //     Mail::to($admin->email)->send(new ApplyLeaveMail($employee));
        // }
        // dispatch( new ApplyLeaveJob($employee) );
        return true;
    }

    private function remaning_and_limit_query(array|object $data): bool
    {
        DB::beginTransaction();

        $valueCounts = array_count_values($data['add_leave_adjust_plan']);
        // dd($valueCounts);
        foreach ($valueCounts as $key => $value) {
            $limit = LeavePlan::whereId($key)->where('consective_leaves', '>=', $value)->get()->toArray();
            if (empty($limit)) {
                DB::rollBack();
                return false;
            }
        }

        $hours_array = [];
        if (!empty($data['add_leave_adjust_plan'])) {
            for ($i = 0; $i < count($data['add_leave_adjust_plan']); $i++) {
                $mleave_plan = LeavePlan::whereId($data['add_leave_adjust_plan'][$i])->first();

                if ($mleave_plan->unit_id == 2) {
                    if (array_key_exists($data['add_leave_adjust_plan'][$i], $hours_array)) {
                        if (array_key_exists($data['add_leave_adjust_date'][$i], $hours_array[$data['add_leave_adjust_plan'][$i]])) {
                            $hours_array[$data['add_leave_adjust_plan'][$i]][$data['add_leave_adjust_date'][$i]] += $data['add_leave_adjust_hour'][$i] ?? 0;
                        } else {
                            $hours_array[$data['add_leave_adjust_plan'][$i]][$data['add_leave_adjust_date'][$i]] = $data['add_leave_adjust_hour'][$i] ?? 0;
                        }
                    } else {
                        $hours_array[$data['add_leave_adjust_plan'][$i]] = [
                            $data['add_leave_adjust_date'][$i] => $data['add_leave_adjust_hour'][$i] ?? 0
                        ];
                    }
                }
            }
        }
        foreach ($hours_array as $key => $value) {
            foreach ($value as $key2 => $value2) {
                $limit = LeavePlan::whereId($key)->where('consective_leaves', '>=', $value2)->get()->toArray();
                if (empty($limit)) {
                    DB::rollBack();
                    return false;
                }
            }
        }


        try {
            foreach ($data['add_leave_adjust_plan'] as $index => $leave_plan) {
                $assign_leaves = AssignLeave::where('user_id', Auth::user()->id)
                    ->where('leave_plan_id', $leave_plan)->with('leave_plan')
                    ->lockForUpdate() // Lock the row for update to prevent race conditions
                    ->first();

                if ($assign_leaves) {
                    if ($assign_leaves->leave_plan->unit_id == 1) {
                        $mainValue = 1;
                        if ((isset($data['add_leave_half_day'][$index]) && $data['add_leave_half_day'][$index]) == 'true') {
                            $mainValue = 0.5;
                        }
                        // dd($mainValue, $data['add_leave_half_day'][$index]);
                        $assign_leaves->remaining_leave = $assign_leaves->remaining_leave - $mainValue;

                        if ($assign_leaves->remaining_leave < 0) {
                            // Rollback the transaction if remaining leave becomes negative
                            DB::rollBack();

                            return false;
                        }
                    } else {
                        $values = $data['add_leave_adjust_hour'][$index] ?? 0;
                        $assign_leaves->remaining_leave = $assign_leaves->remaining_leave - $values;
                        if ($assign_leaves->remaining_leave < 0) {
                            // Rollback the transaction if remaining leave becomes negative
                            DB::rollBack();

                            return false;
                        }
                    }

                    $assign_leaves->save();
                }
            }

            // Commit the transaction if all updates are successful
            DB::commit();
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            DB::rollBack();
            // Log or handle the exception as needed
            // dd($e->getMessage(), $e->getLine(), $e->getFile(), $e->getTrace(), $e->getCode());

            return false;
        }

        return true;
    }

    public function delete_leave(mixed $id)
    {
        $applyLeave = Applyleaves::where('id', $id)->with('adjust_leaves')->first();
        if ($applyLeave->adjust_leaves) {
            foreach ($applyLeave->adjust_leaves as $adjust_leave) {
                $assignLeave = AssignLeave::where('user_id', $applyLeave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
                if($assignLeave){
                    $assignLeave->remaining_leave = $assignLeave->remaining_leave ?? 0;
                    $assignLeave->remaining_leave = ($assignLeave->remaining_leave ?? 0) + $adjust_leave->quantity;
                    $assignLeave->save();
                }
            }
            $applyLeave->delete();
            event(new DeleteEmployeeEvent($applyLeave->user_id, $applyLeave->id));
            return true;
        } else {
            return false;
        }
    }
}
