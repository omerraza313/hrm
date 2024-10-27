<?php

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class DateRange implements ValidationRule
{
    public $fromDate;
    public $toDate;
    public function __construct($fromDate, $toDate)
    {
        if ($fromDate && $toDate) {
            $this->fromDate = Carbon::createFromFormat('d/m/Y', $fromDate);
            $this->toDate = Carbon::createFromFormat('d/m/Y', $toDate);
        }
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // foreach ($value as $date) {
        $carbonDate = Carbon::createFromFormat('d/m/Y', $value);
        if (!$carbonDate->between($this->fromDate, $this->toDate)) {
            $fail($attribute . "must be within the specified date range.");
        }
        // }
    }
}
