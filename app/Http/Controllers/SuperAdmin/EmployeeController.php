<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RolesEnum;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Services\EmployeeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\DeleteEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\DateLog;

class EmployeeController extends Controller {
    public function __construct(protected EmployeeService $employeeService)
    {
        // $this->authorizeResource(Employee::class, 'employee');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $departments = $this->employeeService->get_department_list();
        $designations = $this->employeeService->get_designation_list();
        $employees = $this->employeeService->get_employees($request->all());
        $managers = $this->employeeService->get_manager_list();
        $args = compact('departments', 'designations', 'employees', 'managers');
        return view('admin.employee.view', $args);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateEmployeeRequest $request)
    {
        $this->authorize('create', Employee::class);
        $data = $request->validated();
        $this->employeeService->create($data);

        return redirect()->route('admin.employee.all')->with('success', 'Employee created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $data = $request->validated();
        $updateEmployee = $this->employeeService->update_employee($data, $employee);
        if ($updateEmployee) {
            return redirect()->route('admin.employee.all')->with('success', 'Employee updated successfully');
        }
        return redirect()->route('admin.employee.all')->with('error', 'Employee not found ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteEmployeeRequest $request, string $id)
    {
        $data = $request->validated();

        $isDelete = $this->employeeService->destroy($data, $id);
        if ($isDelete) {
            return redirect()->route('admin.employee.all')->with('success', 'Employee deactive successfully');
        }

        return redirect()->route('admin.employee.all')->with('error', 'Employee not found ');
    }

    public function get_employee(Request $request)
    {
        $data = $request->validate([
            'dept_id' => 'required'
        ]);

        if ($data['dept_id'] == "all") {
            $employee_list = User::Role(RolesEnum::Employee)->with('employee_details')->get();
        } else {
            $employee_list = User::Role(RolesEnum::Employee)
                ->whereHas('employee_details', function ($query) use ($data) {
                    $query->where('department_id', $data['dept_id']);
                })
                ->with(['employee_details'])
                ->get();
        }
        return response()->json(['data' => $employee_list]);
    }

    public function restore_employee(Request $request, $id)
    {
        $data = $request->validate([
            'reactive_rejoining' => 'required',
            'reactive_probation_period' => 'required',
        ]);

        User::withTrashed()->findOrFail($id)->restore();

        $user = User::with('employee_details')->findOrFail($id);

        $user->employee_details->update([
            'join_date' => $data['reactive_rejoining'],
        ]);

        DateLog::create([
            'user_id' => $user->id,
            'date' => $data['reactive_rejoining'],
            'dateable_type' => User::class,
            'dateable_id' => $user->id,
            'type' => 'join'
        ]);

        $user->save();

        return redirect()->back()->with('success', 'Employee has been activated');
    }

    public function get_employee_department(Request $request)
    {
        if ($request->has('department_ids')) {
            $dpt_ids = $request->department_ids;
        } else {
            $dpt_ids = null;
        }



        if ($dpt_ids) {
            $dpt_obj = json_decode($dpt_ids);
            $dpt_collection = collect($dpt_obj);
            $dpt_new_ids = $dpt_collection->pluck('id');
        }
        $employee_list = User::Role(RolesEnum::Employee)
            ->whereHas('employee_details', function ($query) use ($dpt_new_ids) {
                $query->whereIn('department_id', $dpt_new_ids);
            })
            ->with(['employee_details'])
            ->get();
        return response()->json(['data' => $employee_list]);
    }
}