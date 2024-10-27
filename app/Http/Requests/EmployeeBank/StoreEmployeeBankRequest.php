<?php

namespace App\Http\Requests\EmployeeBank;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeBankRequest extends FormRequest {
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
            'bank_name' => 'required',
            'bank_account_no' => 'required',
            'bank_branch_code' => 'required',
            'bank_iban_number' => 'required',
            'bank_employee_id' => 'required|exists:users,id',
            'bank_id' => [
                    'nullable',
                    Rule::requiredIf($this->filled('bank_id'))
                ]
        ];
    }

    public function messages(): array
    {
        return [
            'bank_branch_code.required' => 'Bank Title is required',
        ];
    }
}