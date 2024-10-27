<?php

namespace App\Http\Requests\Employee;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest {
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
        $employeeId = $this->route('employee');  // Assuming you have a route parameter for the employee ID.

        return [
            'edit_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'edit_first_name' => 'required|string',
            'edit_last_name' => 'required|string',
            'edit_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($employeeId),
            ],
            'edit_password' => [
                'nullable',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers(),
                Rule::requiredIf($this->filled('edit_password'))
            ],
            'edit_confirm_password' => [
                'nullable',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers(),
                Rule::requiredIf($this->filled('edit_password')),
                'same:edit_password'
            ],
            'edit_joining_date' => 'required',
            'edit_phone' => 'required',
            'edit_designation' => 'required|exists:designations,id',
            'edit_department' => 'required|exists:departments,id',
            'edit_salary' => 'required|numeric',
            'edit_blood_group' => 'required',
            'edit_report_manager' => [
                Rule::requiredIf(function () {
                    return $this->input('edit_role') == 1;
                })
            ],
            'edit_role' => 'required|in:1,2'
        ];
    }

    public function messages(): array
    {
        return [
            'edit_image.nullable' => 'The image field may be empty.',
            'edit_image.image' => 'The file must be an image.',
            'edit_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'edit_image.max' => 'The image may not be greater than 2048 kilobytes (2 MB).',


            'edit_first_name.required' => 'The first name field is required.',
            'edit_first_name.string' => 'The first name must be a string.',
            'edit_last_name.required' => 'The last name field is required.',
            'edit_last_name.string' => 'The last name must be a string.',
            'edit_email.required' => 'The email field is required.',
            'edit_email.email' => 'The email must be a valid email address.',
            'edit_email.unique' => 'The email has already been taken.',
            'edit_password.required' => 'The password field is required when provided.',
            'edit_password.min' => 'The password must be at least 8 characters long.',
            'edit_confirm_password.required' => 'The confirm password field is required when provided.',
            'edit_confirm_password.min' => 'The confirm password must be at least 8 characters long.',
            'edit_confirm_password.same' => 'The confirm password must match the password.',
            'edit_joining_date.required' => 'The joining date field is required.',
            'edit_joining_date.date' => 'The joining date must be a valid date.',
            'edit_phone.required' => 'The phone field is required.',
            'edit_designation.required' => 'The designation field is required.',
            'edit_designation.exists' => 'The selected designation is invalid.',
            'edit_department.required' => 'The department field is required.',
            'edit_department.exists' => 'The selected department is invalid.',
            'edit_salary.required' => 'The salary field is required.',
            'edit_salary.numeric' => 'The salary must be a number.',
            'edit_blood_group.required' => "The blood group is required",

            'edit_report_manager.required' => 'The report manager is required',
            'edit_report_manager.exists' => 'Please select the valid option',

            'edit_role.required' => 'The role is required',
            'edit_role.in' => 'Please select the valid option',
        ];
    }
}