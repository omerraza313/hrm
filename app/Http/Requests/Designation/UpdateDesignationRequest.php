<?php

namespace App\Http\Requests\Designation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignationRequest extends FormRequest
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
            'edit_name' => 'required|string',
            'edit_department' => 'required|exists:departments,id'
        ];
    }

    public function messages(): array {
        return [
            'edit_name.required' => "Please enter a name of designation",
            'edit_name.string' => "Please enter a correct string",
            'edit_department.required' => "Please select the department",
            'edit_department.exists' => "Department not exist"
        ];
    }
}
