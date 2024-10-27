<?php

namespace App\Http\Requests\PolicyRequest;

use App\Helpers\PolicyHelper;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StorePolicyRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $gen_type = '';
        foreach (PolicyHelper::pay_gen_type() as $key => $value) {
            $gen_type .= $key . ',';
        }


        // Early Arrival Policcy
        $early_arr_p = '';
        foreach (PolicyHelper::early_arrival_policy() as $key => $value) {
            $early_arr_p .= $key . ',';
        }

        // Timeout Option
        $timeout_array = '';
        foreach (PolicyHelper::get_force_timeout() as $key => $value) {
            $timeout_array .= $key . ',';
        }

        // Timeout pOLICY Option
        $timeout_policy_array = '';
        foreach (PolicyHelper::get_timeout_policy() as $key => $value) {
            $timeout_policy_array .= $key . ',';
        }


        // add_policy_overtime_status
        $overtime_status = "";
        foreach (PolicyHelper::get_over_time_status() as $key => $value) {
            $overtime_status .= $key . ',';
        }


        // add_policy_overtime_rate
        $overRate = "";
        foreach (PolicyHelper::get_over_time_rate() as $key => $value) {
            $overRate .= $key . ',';
        }

        // add_policy_holiday_overtime_rate
        $holidayoverRate = "";
        foreach (PolicyHelper::get_holiday_over_time_rate() as $key => $value) {
            $holidayoverRate .= $key . ',';
        }



        $rules = [
            'add_policy_name' => 'required',
            'add_policy_department' => [
                    'nullable',
                    Rule::requiredIf($this->isEmptyString('add_policy_employee'))
                ],
            'add_policy_employee' => [
                'nullable',
                Rule::requiredIf($this->isEmptyString('add_policy_department'))
            ],


            'add_policy_payslip_gen_type' => 'required|in:' . $gen_type,
            'add_policy_off_days' => 'required|numeric|min:0',
            'add_policy_working_hours' => [
                    Rule::requiredIf(function () {
                        return $this->input('add_policy_payslip_gen_type') == 3;
                    })
                ],
            // 'add_policy_minutes' => [
            //     Rule::requiredIf(function () {
            //         return $this->input('add_policy_payslip_gen_type') == 3;
            //     })
            // ],
            'add_policy_shift_hours' => [
                Rule::requiredIf(function () {
                    return $this->input('add_policy_payslip_gen_type') == 3;
                })
            ],
            'add_policy_shift_start' => 'required|date_format:h:i A',
            'add_policy_late_c_l_t' => 'required|numeric',
            'add_policy_e_a_p' => 'required|in:' . $early_arr_p,
            'add_policy_shift_close' => 'required|date_format:h:i A',
            'add_policy_force_timeout' => 'required|in:' . $timeout_array,
            'add_policy_timeout_policy' => 'required|in:' . $timeout_policy_array,
            'add_policy_monthly_late_minute' => 'required|numeric|min:0|max:999',
            'add_policy_late_comers_penalty' => 'required|numeric|min:0|max:4',
            'add_policy_working_day' => 'required|array',
            'add_policy_working_day.*' => 'required|string',
            'add_policy_working_array' => 'required',

            'add_policy_overtime_status' => 'required|in:' . $overtime_status,
            'add_policy_ot_atfer_closing_duty' => [
                    Rule::requiredIf(function () {
                        return $this->input('add_policy_overtime_status') == 2;
                    }),
                    'numeric'
                ],
            'add_policy_ot_min_minutes' => [
                Rule::requiredIf(function () {
                    return $this->input('add_policy_overtime_status') == 2;
                }),
                'numeric'
            ],
            'add_policy_ot_rate' => [
                Rule::requiredIf(function () {
                    return $this->input('add_policy_overtime_status') == 2;
                }),
                'in:' . $overRate
            ],
            'add_policy_ot_rate_value' => [
                Rule::requiredIf(function () {
                    return $this->input('add_policy_overtime_status') == 2 && ($this->input('add_policy_ot_rate') == 1 || $this->input('add_policy_ot_rate') == 3);
                }),
                'numeric'
            ],
            'add_policy_ot_amount' => [
                Rule::requiredIf(function () {
                    return $this->input('add_policy_overtime_status') == 2 && ($this->input('add_policy_ot_rate') == 1);
                }),
                'numeric'
            ],
            'add_policy_holiday_ot' => 'required|in:' . $holidayoverRate,
            'add_policy_holiday_ot_rate' => [
                Rule::requiredIf(function () {
                    return ($this->input('add_policy_holiday_ot') == 3 || $this->input('add_policy_holiday_ot') == 4 || $this->input('add_policy_holiday_ot') == 5);
                }),
                'numeric'
            ],
            'add_policy_holiday_ot_amount' => [
                Rule::requiredIf(function () {
                    return ($this->input('add_policy_holiday_ot') == 3 || $this->input('add_policy_holiday_ot') == 4);
                }),
                'numeric'
            ],

        ];
        return $rules;
    }

    public function messages(): array
    {
        return [
            'add_policy_name.required' => 'Policy name is required',
            'add_policy_department.required' => 'Department is required, when employee is empty',
            'add_policy_employee.required' => 'Employee is required, when department is empty',


            'add_policy_payslip_gen_type.required' => 'Payslip Generation Slip is required',
            'add_policy_payslip_gen_type.in' => 'Please select the correct option',
            'add_policy_off_days.required' => 'Off Days is required',
            'add_policy_off_days.numeric' => 'Please enter numeric value',
            'add_policy_off_days.min' => 'Value must be atleast 0',
            'add_policy_working_hours.required' => 'Working hours is requred',
            // 'add_policy_minutes.required' => 'Working minutes is requred',
            'add_policy_shift_hours.required' => 'Shift retaining hours is requred',

            'add_policy_shift_start.required' => 'Shift hours is required',
            'add_policy_shift_start.date_format' => 'Shift hours must be in hours',
            'add_policy_late_c_l_t.required' => 'Late comming eniency time is required',
            'add_policy_late_c_l_t.numeric' => 'Late comming leniency time must be numeric',
            'add_policy_e_a_p.required' => 'Early Arrival Policy is required',
            'add_policy_e_a_p.in' => 'Please select the correct early arrival policy',
            'add_policy_shift_close.required' => 'Shift hours is required',
            'add_policy_shift_close.date_format' => 'Shift hours must be in hours',
            'add_policy_force_timeout.required' => 'Force Timeout is required',
            'add_policy_force_timeout.in' => 'Please select the correct option',
            'add_policy_timeout_policy.required' => 'Timeout Policy is required',
            'add_policy_timeout_policy.in' => 'Please select the correct option',
            'add_policy_monthly_late_minute.required' => 'Late minute monthly bucket is required',
            'add_policy_monthly_late_minute.numeric' => 'Please insert the numeric value',
            'add_policy_monthly_late_minute.max' => 'Value should be between 0 to 999',
            'add_policy_monthly_late_minute.min' => 'Value should be between 0 to 999',
            'add_policy_late_comers_penalty.required' => 'Late comers penalty is required',
            'add_policy_late_comers_penalty.numeric' => 'Please insert the numeric value',
            'add_policy_late_comers_penalty.max' => 'Value should be between 0 to 4',
            'add_policy_late_comers_penalty.min' => 'Value should be between 0 to 4',
            'add_policy_working_day.required' => 'Working Day must be required',
            'add_policy_working_day.array' => 'Working days must be in array form',

            'add_policy_overtime_status.required' => 'Overtime Status is required',
            'add_policy_overtime_status.in' => 'Please select the correct overtime status',
            'add_policy_ot_atfer_closing_duty.required' => 'Closing Duty is required',
            'add_policy_ot_atfer_closing_duty.numeric' => 'Value must be a number',
            'add_policy_ot_min_minutes.required' => 'minutes is required',
            'add_policy_ot_min_minutes.numeric' => 'Value must be a number',
            'add_policy_ot_rate.required' => 'Overtime Rate is required',
            'add_policy_ot_rate.in' => 'Please select the correct Overtime Rate',
            'add_policy_ot_rate_value.required' => 'Rate is Required',
            'add_policy_ot_rate_value.numeric' => 'Rate mus be a number',
            'add_policy_ot_amount.required' => 'Amount is Required',
            'add_policy_ot_amount.numeric' => 'Amount mus be a number',

            'add_policy_holiday_ot.required' => 'Holiday Overtime Rate is required',
            'add_policy_holiday_ot.in' => 'Please select the correct Holiday Overtime Rate',
            'add_policy_holiday_ot_rate.required' => 'Rate is Required',
            'add_policy_holiday_ot_rate.numeric' => 'Rate mus be a number',
            'add_policy_holiday_ot_amount.required' => 'Amount is Required',
            'add_policy_holiday_ot_amount.numeric' => 'Amount mus be a number',


        ];
    }
}