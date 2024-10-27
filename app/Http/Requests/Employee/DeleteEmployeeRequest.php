<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class DeleteEmployeeRequest extends FormRequest {
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
            'route_name' => 'required',
            'delete_note' => 'required',
            'delete_clearance' => 'required|in:yes,no',
            'delete_notice_period_served' => 'required',
            'delete_notice_period_date' => 'required',
            'delete_notice_period_duration' => 'required',
            'delete_exit_date' => 'required',
            'delete_reason' => 'required|in:1,2,3,4,5,6',
        ];
    }


    public function messages()
    {
        return [
            'delete_note.required' => 'The note is required.',

            'delete_clearance.required' => 'The clearance is required.',
            'delete_clearance.in' => 'Please select the valid option.',


            'delete_notice_period_served.required' => 'The notice period served is required.',

            'delete_notice_period_date.required' => 'The notice period date is required.',

            'delete_notice_period_duration.required' => 'The notice period duration is required.',

            'delete_exit_date.required' => 'The exit date is required.',

            'delete_reason.required' => 'The reason is required.',
            'delete_reason.in' => 'Please select the valid option.',
        ];
    }

}