<?php

namespace App\Services;

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
use App\Enums\ApprovedStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\Employee\ApplyLeaveEvent;
use App\Events\Admin\DeleteEmployeeEvent;
use App\Events\Admin\ApplyLeaveAdminEvent;

class LeaveApplicationService {
    public function get_apply_leaves($data = [])
    {
        if (!isset($data['name'])) {
            $data['name'] = null;
        }
        if (!isset($data['types'])) {
            $data['types'] = null;
        }
        if (!isset($data['status'])) {
            $data['status'] = null;
        }
        if (!isset($data['from_date'])) {
            $data['from_date'] = null;
        }
        if (!isset($data['to_date'])) {
            $data['to_date'] = null;
        }
        $apply_leaves = Applyleaves::
            when($data['name'], function ($query, $name) {
                $query->whereHas('employee', function ($query) use ($name) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%');
                });
            })
            ->when($data['types'], function ($query, $types) {
                $query->whereHas('adjust_leaves', function ($query) use ($types) {
                    $query->where('leave_plan_id', $types);
                });
            })
            ->when($data['status'] != null, function ($query) use ($data) {
                $query->where('status', $data['status']);
            })
            ->when($data['from_date'], function ($query, $from_date) {
                $query->whereHas('adjust_leaves', function ($query) use ($from_date) {
                    $query->where('leave_date', '>=', $from_date);
                });
            })
            ->when($data['to_date'], function ($query, $to_date) {
                $query->whereHas('adjust_leaves', function ($query) use ($to_date) {
                    $query->where('leave_date', '<=', $to_date);
                });
            })
            ->with([
                'employee' => function ($query) {
                    $query->withTrashed();
                },
                'employee.employee_details.designation',
                'approved_by',
                'adjust_leaves',
                'adjust_leaves.leave_plan' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->get();


        return $apply_leaves;
    }

    public function get_leave_types()
    {
        $leave_types = LeavePlan::all();
        return $leave_types;
    }

    public function get_employees()
    {
        $employees = User::role(RolesEnum::Employee->value)->get();
        // dd($employees);

        return $employees;
    }

    public function get_pending_leaves()
    {
        $pending = Applyleaves::where('status', ApprovedStatusEnum::Pending->value)->get();
        return $pending;
    }

    public function get_total_leaves()
    {
        $total_leaves = Applyleaves::get();
        return $total_leaves;
    }
    public function get_approved_leaves()
    {
        $approved_leaves = Applyleaves::where('status', ApprovedStatusEnum::Approved->value)->get();
        return $approved_leaves;
    }
    public function get_declined_leaves()
    {
        $approved_leaves = Applyleaves::where('status', ApprovedStatusEnum::Declined->value)->get();
        return $approved_leaves;
    }
    public function change_leave_status(mixed $data): bool
    {
        if ($data['status'] == ApprovedStatusEnum::Approved->value) {
            $this->approved_status($data);
        } else {
            $this->declined_status($data);
        }
        return true;
    }

    private function approved_status(mixed $data)
    {
        $apply_leave = Applyleaves::where('id', $data['apply_leave_id'])->with('adjust_leaves', 'employee', 'adjust_leaves.leave_plan')->first();
        if ($apply_leave->status == ApprovedStatusEnum::Declined->value) {
            foreach ($apply_leave->adjust_leaves as $adjust_leave) {
                $assignLeaves = AssignLeave::where('user_id', $apply_leave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
                $assignLeaves->remaining_leave = $assignLeaves->remaining_leave - $adjust_leave->quantity;
                $assignLeaves->save();
            }
        }
        $apply_leave->note = null;
        $apply_leave->status = ApprovedStatusEnum::Approved->value;
        $apply_leave->approved_by = Auth::user()->id;
        $apply_leave->save();
        event(new ApplyLeaveEvent($apply_leave->employee, $apply_leave, Auth::user()));
    }

    private function declined_status(mixed $data)
    {
        $apply_leave = Applyleaves::where('id', $data['apply_leave_id'])->with('adjust_leaves', 'employee', 'adjust_leaves.leave_plan')->first();
        foreach ($apply_leave->adjust_leaves as $adjust_leave) {
            $assignLeaves = AssignLeave::where('user_id', $apply_leave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
            $assignLeaves->remaining_leave = $assignLeaves->remaining_leave + $adjust_leave->quantity;
            $assignLeaves->save();
        }
        $apply_leave->note = $data['status_note'];
        $apply_leave->status = ApprovedStatusEnum::Declined->value;
        $apply_leave->approved_by = Auth::user()->id;
        $apply_leave->save();
        event(new ApplyLeaveEvent($apply_leave->employee, $apply_leave, Auth::user()));
    }

    public function get_leave_plans($employee_id)
    {
        $employee = User::where('id', $employee_id)->with('employee_details')->first();
        $leaves = LeavePlan::whereIn('leave_gender_type', [$employee->employee_details->gender, 'both'])
//            ->where('c_from_date', '<=', Carbon::now()->format('n'))
//            ->where('c_to_date', '>=', Carbon::now()->format('n'))
            ->with('unit')
            ->get();
        return $leaves;
    }

    public function apply_employee_leave(array|object $data): bool
    {
        $employee = User::find($data['add_leave_employee']);
        $remain_status = $this->remaning_and_limit_query($data);
        if (!$remain_status) {
            return false;
        }

        $image = "";
        if (isset($data['add_leave_document']) && $data['add_leave_document']) {
            $image = time() . '.' . $data['add_leave_document']->extension();
            $data['add_leave_document']->move('images/leaves/', $image);
        }

        $apply_leaves = Applyleaves::create([
            'subject' => $data['add_leave_subject'],
            'body' => $data['add_leave_body'],
            'leave_from' => $data['add_leave_from'],
            'leave_upto' => $data['add_leave_to'],
            'status' => 0,
            'document' => $image,
            'user_id' => $employee->id
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
            event(new ApplyLeaveAdminEvent($employee, $apply_leaves->id));
        } catch (\Throwable $th) {
            //throw $th;
        }
        $admins = User::role([RolesEnum::Admin->value, RolesEnum::SuperAdmin->value])->get();
        foreach ($admins as $admin) {
            // Mail::to($admin->email)->send(new ApplyLeaveMail($employee));
        }
        // dispatch( new ApplyLeaveAdminEvent($employee, $apply_leaves->id) );
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
                $assign_leaves = AssignLeave::where('user_id', $data['add_leave_employee'])
                    ->where('leave_plan_id', $leave_plan)
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

            return false;
        }

        return true;
    }

    public function delete_leave($id)
    {
        $applyLeave = Applyleaves::where('id', $id)->with('adjust_leaves')->first();
        if ($applyLeave) {
            foreach ($applyLeave->adjust_leaves as $adjust_leave) {
                $assignLeave = AssignLeave::where('user_id', $applyLeave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
                $assignLeave->remaining_leave = $assignLeave->remaining_leave + $adjust_leave->quantity;
                $assignLeave->save();
            }
            $applyLeave->delete();
            try {
                event(new DeleteEmployeeEvent($applyLeave->user_id, $applyLeave->id));
            } catch (\Throwable $th) {
                //throw $th;
            }
            return true;
        } else {
            return false;
        }
    }
}
