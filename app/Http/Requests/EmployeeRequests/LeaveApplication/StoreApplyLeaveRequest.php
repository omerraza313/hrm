<?php

namespace App\Http\Requests\EmployeeRequests\LeaveApplication;

use App\Rules\DateRange;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplyLeaveRequest extends FormRequest {
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
            'add_leave_subject' => 'required',
            'add_leave_body' => 'required',
            'add_leave_from' => 'required|date_format:m/d/Y|',
            'add_leave_to' => 'required|date_format:m/d/Y|after_or_equal:add_leave_from',
            'add_leave_adjust_date.*' => ['required', 'date_format:m/d/Y', new DateRange($this->input('add_leave_from'), $this->input('add_leave_to'))],
            'add_leave_adjust_plan.*' => 'required|exists:leave_plans,id',
            'add_leave_adjust_hour.*' => 'nullable|numeric|between:0,24',
            'add_leave_half_day.*' => 'nullable',
            'add_leave_document' => 'nullable|file|mimes:png,jpg,jpeg,pdf,doc,docx'
        ];
    }

    public function messages(): array
    {
        return [
            'add_leave_subject.required' => 'Subject is required.',

            'add_leave_body.required' => 'Body is required',

            'add_leave_from.required' => 'From Date is required',
            'add_leave_from.date_format' => 'Please enter the valid date format',

            'add_leave_to.required' => 'To Date is required',
            'add_leave_to.date_format' => 'Please enter the valid date format',

            'add_leave_adjust_date.*.required' => 'Date is required',
            'add_leave_adjust_date.*.date_format' => 'Please enter the valid date format',
            'add_leave_adjust_date.*.date_range' => 'Please enter the valid date format',

            'add_leave_adjust_plan.*.required' => 'Leave Plan is required',
            'add_leave_adjust_plan.*.exists' => 'Please select a valid leave plan',

            'add_leave_document.required' => 'Document is required.',
            'add_leave_document.file' => 'Document must be file.',
            'add_leave_document.mimes' => 'Document must be "JPG", "PNG", "JPEG", "PDF", "DOC", "DOCX".',

            'add_leave_adjust_hour.*.numeric' => 'Hour must be numeric.',
            'add_leave_adjust_hour.*.between' => 'Hour must be between 0 to 24.',

            'add_leave_half_day.*.boolean' => 'Half Day must be boolean.'
        ];
    }
}
