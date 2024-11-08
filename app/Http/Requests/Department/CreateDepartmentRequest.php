<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
            'add_name' => 'required|string'
        ];
    }

    public function messages(): array {
        return [
            'add_name.required' => "Please enter a name of department",
            'add_name.string' => "Please enter a correct string"
        ];
    }
}
