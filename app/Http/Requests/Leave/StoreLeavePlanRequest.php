<?php

namespace App\Http\Requests\Leave;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeavePlanRequest extends FormRequest {
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
        return [
            'leave_plan_title' => 'required',
            'leave_plan_from' => 'required',
            'leave_plan_to' => 'required',
            'leave_plan_quantity' => 'required',
            'leave_plan_unit' => 'required|in:1,2',
            'leave_plan_carry_f' => 'required',
            'leave_plan_con_allow' => 'required',
            'leave_plan_apply_year' => 'required',
            'leave_plan_apply_month' => 'required',
            'leave_plan_type' => 'required|in:1,2',
            'leave_plan_gender_type' => 'required|in:Male,Female,both',
        ];
    }
    public function messages(): array
    {
        return [
            'leave_plan_title.required' => 'Leave title is required.',
            'leave_plan_from.required' => 'Calendar from is required.',
            'leave_plan_to.required' => 'Calendar upto is required.',
            'leave_plan_quantity.required' => 'Quantity is required.',
            'leave_plan_unit.required' => 'Unit is required.',
            'leave_plan_unit.in' => 'Unit should be days or hours.',
            'leave_plan_carry_f.required' => 'Carry from is required.',
            'leave_plan_con_allow.required' => 'Consective leaves is required.',
            'leave_plan_apply_year.required' => 'Apply year is required.',
            'leave_plan_apply_month.required' => 'Apply month title is required.',
            'leave_plan_type.required' => 'Plan type is required.',
            'leave_plan_type.in' => 'Plan type should be paid or unpaid.',
            'leave_plan_gender_type.required' => 'Gender leave type is required.',
            'leave_plan_gender_type.in' => 'Gender leave type should be male, female or both.',
        ];
    }
}