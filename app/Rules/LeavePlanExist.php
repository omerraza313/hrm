<?php

namespace App\Rules;

use App\Models\AssignLeave;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LeavePlanExist implements ValidationRule
{
    public $employee_id;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function __construct(mixed $employe_id){
        $this->employee_id = $employe_id;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $assignLeave = AssignLeave::where('user_id', $this->employee_id)->where('leave_plan_id', $value)->first();
        if(!$assignLeave){
            $fail("Please select a valid plan");
        }
    }
}
