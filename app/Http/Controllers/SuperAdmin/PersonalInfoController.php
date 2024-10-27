<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalInfo\StorePersonalInfoRequest;
use App\Models\UserDetail;

class PersonalInfoController extends Controller
{
    public function store_update(StorePersonalInfoRequest $request)
    {
        $data = $request->validated();
        $eme_info = UserDetail::where('user_id', $data['pinfo_employee_id']);
        $eme_info->update(
            [
                'pseudo_name' => $data['pinfo_pseudo_name'],
                'cnic' => $data['pinfo_cnic'],
                'phone' => $data['pinfo_phone'],
                'martial_status' => $data['pinfo_marital_status'],
            ]
        );

        return redirect()->back()->with('success', 'Personal Information updated successfully!');
    }
}
