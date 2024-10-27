<?php

namespace App\Http\Requests\Profile;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateProfileRequest extends FormRequest {
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
            'pro_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pro_user_id' => 'required',
            'pro_first_name' => 'required',
            'pro_last_name' => 'required',
            'pro_dob' => 'required',
            'pro_gender' => 'required',
            'pro_address_id' => 'required',
            'pro_address' => 'required',
            'pro_city' => 'required',
            'pro_state' => 'required',
            'pro_country' => 'required',
            'pro_zip' => 'required',
            'pro_number' => 'required',
            'pro_department' => 'required|exists:departments,id',
            'pro_designation' => [
                Rule::requiredIf(function () {
                    $user = User::findOrFail($this->input('pro_user_id'));
                    if ($user->hasRole(RolesEnum::Manager->value)) {
                        return false;
                    } else {
                        return $this->input('pro_designation');
                    }
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pro_image.nullable' => 'The image field may be empty.',
            'pro_image.image' => 'The file must be an image.',
            'pro_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'pro_image.max' => 'The image may not be greater than 2048 kilobytes (2 MB).',


            'pro_first_name.required' => 'The first name field is required.',
            'pro_last_name.required' => 'The last name field is required.',
            'pro_dob.required' => 'The date of birth field is required.',
            'pro_gender.required' => 'The gender field is required.',
            'pro_address_id.required' => 'The address ID field is required.',
            'pro_address.required' => 'The address field is required.',
            'pro_state.required' => 'The state field is required.',
            'pro_country.required' => 'The country field is required.',
            'pro_zip.required' => 'The ZIP code field is required.',
            'pro_number.required' => 'The number field is required.',
            'pro_department.required' => 'The department field is required.',
            'pro_department.exists' => 'The selected department is invalid.',
            'pro_designation.required' => 'The designation field is required.',
            'pro_designation.exists' => 'The selected designation is invalid.',
        ];
    }
}