<?php

namespace App\Http\Requests\PersonalInfo;

use App\Models\UserDetail;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class StorePersonalInfoRequest extends FormRequest
{
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
        $employeeId = Route::getCurrentRequest()->request->get('pinfo_employee_id');
        $emp_details = UserDetail::where('user_id', $employeeId)->first();
        return [
            'pinfo_employee_id' => 'required|exists:users,id',
            'pinfo_pseudo_name' => [
                'required',
                Rule::unique('user_details', 'pseudo_name')->ignore($emp_details->id)
            ],
            'pinfo_cnic' => 'required',
            'pinfo_phone' => 'required',
            'pinfo_marital_status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pinfo_employee_id.required' => 'Employee ID is required.',
            'pinfo_employee_id.exists' => 'The selected employee ID is invalid.',

            'pinfo_pseudo_name.required' => 'Pseudo name is required.',
            'pinfo_pseudo_name.unique' => 'The pseudo name is already taken. Please choose a different one.',

            'pinfo_cnic.required' => 'CNIC is required.',

            'pinfo_phone.required' => 'Phone number is required.',

            'pinfo_marital_status.required' => 'Marital status is required.',
        ];
    }
}
