<?php

namespace App\Http\Requests\Education;

use App\Helpers\ProfileHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
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
        $sub = "";
        foreach (ProfileHelper::get_education_subject() as $key => $subject) {
            $sub .= $key . ',';
        }
        return [
            'edu_id.*' => 'nullable',
            'edu_name.*' => 'required',
            'edu_subject.*' => 'required|in:'.$sub,
            'edu_start_date.*' => 'required',
            'edu_complete_date.*' => 'required|date_format:m/d/Y|after_or_equal:edu_start_date.*',
            'edu_degree.*' => 'required',
            'edu_grade.*' => 'required',
            'employee_id' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'edu_name.*.required' => 'The education name field is required.',
            'edu_subject.*.required' => 'The education subject field is required.',
            'edu_start_date.*.required' => 'The education start date field is required.',
            'edu_complete_date.*.required' => 'The education completion date field is required.',
            'edu_degree.*.required' => 'The education degree field is required.',
            'edu_grade.*.required' => 'The education grade field is required.',
            'edu_complete_date.*.after_or_equal' => 'The Complete Date must be equal to or later than the Starting Date',
        ];
    }
}
