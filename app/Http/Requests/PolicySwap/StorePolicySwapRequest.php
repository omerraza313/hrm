<?php

namespace App\Http\Requests\PolicySwap;

use Illuminate\Foundation\Http\FormRequest;

class StorePolicySwapRequest extends FormRequest
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
            'swap_current_policy' => 'required|exists:policies,id',
            'swap_with_policy' => 'required|exists:policies,id',
            'swap_effect_date' => 'nullable',
            'swap_effect_time' => 'nullable',
            'swap_rollback_date' => 'nullable',
            'swap_rollback_time' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'swap_current_policy.required' => 'Please select the current policy',
            'swap_current_policy.exists' => 'Please select the correct policy',

            'swap_with_policy.required' => 'Please select the swap policy',
            'swap_with_policy.exists' => 'Please select the correct policy',
        ];
    }
}
