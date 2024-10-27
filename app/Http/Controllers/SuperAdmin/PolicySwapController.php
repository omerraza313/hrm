<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PolicySwap\StorePolicySwapRequest;
use App\Services\PolicySwapService;
use Illuminate\Http\Request;

class PolicySwapController extends Controller
{
    public function __construct(protected PolicySwapService $policySwapService)
    {
    }
    public function store(StorePolicySwapRequest $request)
    {
        $data = $request->validated();
        $storeStatus = $this->policySwapService->store_swap_job($data);
        if ($storeStatus) {
            return redirect()->back()->with('success', 'Policy Swap Successfully');
        }

        return redirect()->back()->with('error', 'Some Error Occurred');
    }
}
