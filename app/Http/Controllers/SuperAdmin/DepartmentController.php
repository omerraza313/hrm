<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends Controller {
    public function __construct(protected DepartmentService $departmentService)
    {
        // $this->authorizeResource(Department::class, 'department');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Department::class);
        $departments = $this->departmentService->get_department_list();
        return view('admin.department.view', ['departments' => $departments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request)
    {
        $this->authorize('create', Department::class);
        $data = $request->validated();
        $this->departmentService->create($data);

        return redirect()->route('admin.department.all')->with('success', 'Department created successfully');
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
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $this->authorize('update', $department);
        $data = $request->validated();
        $isUpdate = $this->departmentService->update($data, $department);
        if ($isUpdate) {
            return redirect()->route('admin.department.all')->with('success', 'Department updated successfully');
        }

        return redirect()->route('admin.department.all')->with('error', 'Department not found ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Department::findorFail($id);
        $this->authorize('delete', $department);
        $isDelete = $this->departmentService->destroy($id);
        if ($isDelete) {
            return redirect()->route('admin.department.all')->with('success', 'Department deleted successfully');
        }

        return redirect()->route('admin.department.all')->with('error', 'Department not found ');
    }

    public function get_designation($department_id)
    {
        $designations = $this->departmentService->get_designation($department_id);

        return response()->json(['success' => true, 'data' => $designations,], 200);
    }

    public function get_departments()
    {
        $departments = Department::get();
        return response()->json(['success' => true, 'data' => $departments,], 200);
    }
}