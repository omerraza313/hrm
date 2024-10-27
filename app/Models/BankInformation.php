<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{
    use HasFactory;

    protected $table = "bank_informations";

    protected $fillable = [
        'name',
        'account_no',
        'branch_code',
        'iban_number',
        'user_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id', 'employee_id');
    }
}
