<?php

namespace App\Http\Requests\Experience;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
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
            'exp_id.*' => 'nullable',
            'exp_company_name.*' => 'required',
            'exp_location.*' => 'required',
            'exp_period_from.*' => 'required',
            'exp_period_to.*' => 'required|date_format:m/d/Y|after_or_equal:exp_period_from.*',
            'exp_job_position.*' => 'required',
            'employee_id' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'exp_company_name.*.required' => 'Company name is required for all experiences.',
            'exp_location.*.required' => 'Location is required for all experiences.',
            'exp_period_from.*.required' => 'Period from date is required for all experiences.',
            'exp_period_to.*.required' => 'Period to date is required for all experiences.',
            'exp_job_position.*.required' => 'Job position is required for all experiences.',
            'exp_period_to.*.after_or_equal' => 'The Period To date must be equal to or later than the Period From date.',
            'employee_id.required' => 'Employee ID is required.',
            'exp_id.*.nullable' => 'The experience ID should be nullable.',

        ];
    }
}
