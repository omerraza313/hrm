<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PolicyRequest\StorePolicyRequest;
use App\Models\Policy;
use App\Services\PolicyService;
use Illuminate\Http\Request;

class PolicyController extends Controller {
    public function __construct(protected PolicyService $policyService)
    {

    }

    public function index()
    {
        $department_list = $this->policyService->get_department_list();
        $employee_list = $this->policyService->get_enployee_list();
        $policy_list = Policy::with([
            'pay_roll_settings',
            'working_settings',
            'working_day' => function ($query) {
                $query->where('active', '1');
            },
            'overtime',
            'holiday_overtime',
            'departments' => function ($query) {
                // Add your condition on the pivot table (assuming 'start_time' is the column name)
                // $query->latest('start_time');
                $query->withTrashed()->where('status', 1);
            },
            'users' => function ($query) {
                // Add your condition on the pivot table (assuming 'start_time' is the column name)
                // $query->latest('start_time');
                // $query->orderBy('start_time', 'desc')->limit(1);
                $query->where('status', 1);
            }
        ])->get();

        // dd($policy_list);
        $data = compact('department_list', 'employee_list', 'policy_list');
        return view('admin.policy.view', $data);
    }

    public function store(StorePolicyRequest $request)
    {
        $data = $request->validated();

        $storeStatus = $this->policyService->store($data);
        if ($storeStatus) {
            return redirect()->back()->with('success', "Policy add successfully!");
        }
        return redirect()->back()->with('error', "Some Error Occured!");
    }

    public function delete($id)
    {
        $isDelete = $this->policyService->destroy($id);
        if ($isDelete) {
            return redirect()->back()->with('success', 'Policy deleted successfully');
        }

        return redirect()->route('admin.employee.all')->with('error', 'Policy Not Found ');
    }

    public function assign(Request $request)
    {
        $policy = $this->policyService->assign($request->all());
        if ($policy) {
            return redirect()->back()->with('success', 'Policy Assigned Successfully');
        }

        return redirect()->back()->with('error', 'Policy Not Found');
    }
}