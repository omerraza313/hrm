<?php

namespace App\Http\Requests\EmergencyContact;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyContactRequest extends FormRequest
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
            'eme_id' => 'required|exists:family_contacts,id',
            'eme_name' => 'required|string',
            'eme_relation' => ['required', 'in:Brother,Sister,Spouse,Mother,Father'],
            'eme_number' => 'required',
            'eme_employee_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'eme_id.required' => 'Emergency contact ID is required.',
            'eme_id.exists' => 'The selected emergency contact ID is invalid.',

            'eme_name.required' => 'Emergency contact name is required.',
            'eme_name.string' => 'Emergency contact name must be a string.',

            'eme_relation.required' => 'Emergency contact relation is required.',
            'eme_relation.in' => 'Invalid emergency contact relation. Allowed values are Brother, Sister, Spouse, Mother, Father.',

            'eme_number.required' => 'Emergency contact number is required.',

            'eme_employee_id.required' => 'Employee ID is required.',
            'eme_employee_id.exists' => 'The selected employee ID is invalid.',
        ];
    }
}
