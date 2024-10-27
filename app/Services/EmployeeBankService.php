<?php

namespace App\Services;

use App\Models\BankInformation;

class EmployeeBankService
{
    public function store_update_bank($data): bool
    {
        $bankInfo = BankInformation::updateOrCreate(
            ['id' => $data['bank_id']],

            [
                'name' => $data['bank_name'],
                'account_no' => $data['bank_account_no'],
                'branch_code' => $data['bank_branch_code'],
                'iban_number' => $data['bank_iban_number'],
                'user_id' => $data['bank_employee_id'],
            ]
        );
        if ($bankInfo) {
            return true;
        }
        return false;
    }
}
