<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\StoreEducationRequest;
use App\Http\Requests\Experience\StoreExperienceRequest;
use App\Http\Requests\Profile\StoreUpdateProfileRequest;
use App\Models\User;

class ProfileController extends Controller {
    public function __construct(protected ProfileService $profileService)
    {
    }
    public function index($id)
    {
        $employee = $this->profileService->get_employee($id);
        $departments = $this->profileService->get_department_list();
        $department_id = $employee->employee_details->department_id ?? 0;
        $designations = $this->profileService->get_designation($department_id);
        $employee_roles = User::withTrashed()->findOrFail($id)->getRoleNames();
        return view('admin.profile.view', ['employee' => $employee, 'departments' => $departments,  'designations' => $designations, 'employee_roles' => $employee_roles]);
    }
    public function store_update_education(StoreEducationRequest $request)
    {
        $data = $request->validated();
        $this->profileService->store_update_education($data);

        return redirect()->back()->with('success', 'Education saved successfully');
    }
    public function store_update_experience(StoreExperienceRequest $request)
    {
        $data = $request->validated();
        $this->profileService->store_update_experience($data);

        return redirect()->back()->with('success', 'Experience saved successfully');
    }

    public function store_update_profile_info(StoreUpdateProfileRequest $request)
    {
        $data = $request->validated();
        //dd($data);
        $this->profileService->store_update_profile_info($data);

        return redirect()->back()->with('success', 'Profile Information saved successfully');
    }
}
