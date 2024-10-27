<?php

namespace App\Http\Requests\Designation;

use Illuminate\Foundation\Http\FormRequest;

class CreateDesignationRequest extends FormRequest
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
        return [
            'add_name' => 'required|string',
            'add_department' => 'required|exists:departments,id'
        ];
    }

    public function messages(): array {
        return [
            'add_name.required' => "Please enter a name of designation",
            'add_name.string' => "Please enter a correct string",
            'add_department.required' => "Please select the department",
            'add_department.exists' => "Department not exist"
        ];
    }
}
