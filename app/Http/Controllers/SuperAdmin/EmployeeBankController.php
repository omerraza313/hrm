<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeBank\StoreEmployeeBankRequest;
use App\Services\EmployeeBankService;
use Illuminate\Http\Request;

class EmployeeBankController extends Controller
{
    public function __construct(protected EmployeeBankService $employeeBankService)
    {
    }
    public function store_bank(StoreEmployeeBankRequest $request)
    {
        $data = $request->validated();
        $employeeBank = $this->employeeBankService->store_update_bank($data);

        if ($employeeBank) {
            return redirect()->back()->with("success", "Bank account save successfully!");
        }

        return redirect()->back()->with("error", "Some error occured!");
    }
}
