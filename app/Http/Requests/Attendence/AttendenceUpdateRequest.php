<?php

namespace App\Http\Requests\Attendence;

use Illuminate\Foundation\Http\FormRequest;

class AttendenceUpdateRequest extends FormRequest {
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
        // dd(request()->all());
        return [
            'arrival_time.*' => 'required',
            'leave_time.*' => 'required',
            // 'status' => 'required|in:0,1,2,3,4',
            'id.*' => 'required',
            'status.*' => 'nullable'
        ];
    }
}