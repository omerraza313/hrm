<?php

namespace App\Http\Requests\FamilyContact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyContactRequest extends FormRequest
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
            'family_edit_name' => 'required|string',
            'family_edit_relation' => ['required', 'in:Brother,Sister,Spouse,Mother,Father'],
            'family_edit_dob' => 'required',
            'family_edit_phone' => 'required',
            'family_edit_employee_id' => 'required|exists:users,id',
        ];
    }
}
