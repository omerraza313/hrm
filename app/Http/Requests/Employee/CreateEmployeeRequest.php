<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest {
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
        // dd($this->input('add_role'));
        return [
            'add_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'add_first_name' => 'required|string',
            'add_last_name' => 'required|string',
            'add_pseudo_name' => 'required|string|unique:user_details,pseudo_name',
            'add_dob' => 'required',
            'add_email' => 'required|email|unique:users,email',
            'add_password' => [
                'required',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers()
            ],
            'add_confirm_password' => [
                'required',
                Password::min(8)->letters()->symbols()->mixedCase()->numbers(),
                'same:add_password'
            ],
            'add_join_date' => 'required',
            'add_phone' => 'required',
            'add_martial_status' => ['required', 'in:Single,Married'],
            'add_em_contact_name' => 'required',
            'add_em_contact_num' => 'required',
            'add_em_contact_relation' => ['required', 'in:Brother,Sister,Spouse,Mother,Father'],
            'add_cnic' => 'required',
            'add_designation' => 'required|exists:designations,id',
            'add_department' => 'required|exists:departments,id',
            'add_per_address' => 'required',
            'add_per_city' => 'required',
            'add_per_state' => 'required',
            'add_per_zip' => 'required',
            'add_per_country' => 'required',
            'add_curr_status' => ['required', 'in:true,false'],
            'add_curr_address' => 'required_unless:add_curr_status,true',
            'add_curr_city' => 'required_unless:add_curr_status,true',
            'add_curr_state' => 'required_unless:add_curr_status,true',
            'add_curr_zip' => 'required_unless:add_curr_status,true',
            'add_curr_country' => 'required_unless:add_curr_status,true',
            'add_gender' => ['required', 'in:Female,Male'],
            'add_salary' => 'required|numeric',
            'add_blood_group' => 'required',
            // 'add_report_manager' => 'required|exists:users,id',
            'add_report_manager' => [
                Rule::requiredIf(function () {
                    // return $this->input('add_role') == '1' ? true : false;

                    return $this->input('add_role') == '1';
                }),
            ],
            'add_role' => 'required|in:1,2'
        ];
    }

    public function messages()
    {
        return [
            'add_image.required' => 'The image is required.',
            'add_image.image' => 'The file must be an image.',
            'add_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'add_image.max' => 'The image may not be greater than 2048 kilobytes (2 MB).',


            'add_first_name.required' => 'The first name is required.',
            'add_first_name.string' => 'The first name must be a string.',

            'add_last_name.required' => 'The last name is required.',
            'add_last_name.string' => 'The last name must be a string.',

            'add_pseudo_name.required' => 'The pseudo name is required.',
            'add_pseudo_name.string' => 'The pseudo name must be a string.',
            'add_pseudo_name.unique' => 'The pseudo name is already in use.',

            'add_dob.required' => 'The Date of Birth is required.',
            'add_dob.date' => 'The Date of Birth must be a date.',

            'add_email.required' => 'The email address is required.',
            'add_email.email' => 'Please provide a valid email address.',
            'add_email.unique' => 'The email address is already in use.',

            'add_password.required' => 'The password is required.',
            'add_password.min' => 'The password must be at least :min characters long.',
            'add_password.letters' => 'The password must contain letters.',
            'add_password.symbols' => 'The password must contain symbols.',
            'add_password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'add_password.numbers' => 'The password must contain numbers.',

            'add_confirm_password.required' => 'The password confirmation is required.',
            'add_confirm_password.min' => 'The password confirmation must be at least :min characters long.',
            'add_confirm_password.letters' => 'The password confirmation must contain letters.',
            'add_confirm_password.symbols' => 'The password confirmation must contain symbols.',
            'add_confirm_password.mixed_case' => 'The password confirmation must contain both uppercase and lowercase letters.',
            'add_confirm_password.numbers' => 'The password confirmation must contain numbers.',
            'add_confirm_password.same' => 'The password confirmation must match the password.',

            'add_join_date.required' => 'The join date is required.',
            'add_join_date.date' => 'Please provide a valid date for the join date.',

            'add_phone.required' => 'The phone number is required.',

            'add_martial_status.required' => 'The marital status is required.',
            'add_martial_status.in' => 'Invalid marital status. Please select "Single" or "Married',

            'add_em_contact_name.required' => 'The emergency contact name is required.',

            'add_em_contact_num.required' => 'The emergency contact number is required.',

            'add_em_contact_relation.required' => 'The relationship with the emergency contact is required.',
            'add_em_contact_relation.in' => 'Invalid relationship with the emergency contact.',

            'add_cnic.required' => 'The CNIC is required.',

            'add_per_address.required' => 'The permanent address is required.',
            'add_per_city.required' => 'The city in the permanent address is required.',
            'add_per_state.required' => 'The state in the permanent address is required.',
            'add_per_zip.required' => 'The ZIP code in the permanent address is required.',
            'add_per_country.required' => 'The country in the permanent address is required.',

            'add_curr_status.required' => 'The current address status is required.',
            'add_curr_status.in' => 'Invalid current address status. Please select "True," or "False".',

            'add_curr_address.required_unless' => 'The current address is required when the address status is not "checked."',
            'add_curr_city.required_unless' => 'The city in the current address is required when the address status is not "checked."',
            'add_curr_state.required_unless' => 'The state in the current address is required when the address status is not "checked."',
            'add_curr_zip.required_unless' => 'The ZIP code in the current address is required when the address status is not "checked."',
            'add_curr_country.required_unless' => 'The country in the current address is required when the address status is not "checked."',

            'add_designation.required' => 'Please select the designation',
            'add_designation.exists' => 'Please select the valid designation',

            'add_department.required' => 'Please select the department',
            'add_department.exists' => 'Please select the valid department',

            'add_gender.required' => 'Please select the gender',
            'add_gender.in' => 'Invalid current address status. Please select "Male" or"Female".',

            'add_salary.required' => 'The salary is required.',
            'add_salary.numeric' => 'The salary must be a number.',

            'add_blood_group.required' => "The blood group is required",

            'add_report_manager.required' => 'The report manager is required',
            'add_report_manager.exists' => 'Please select the valid option',

            'add_role.required' => 'The role is required',
            'add_role.in' => 'Please select the valid option',
        ];
    }
}