<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designation\CreateDesignationRequest;
use App\Http\Requests\Designation\UpdateDesignationRequest;
use App\Models\Designation;
use App\Services\DesignationService;
use Illuminate\Http\Request;

class DesignationController extends Controller {
    public function __construct(protected DesignationService $designationService)
    {
        // $this->authorizeResource(Designation::class, 'designation');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Designation::class);
        $departments = $this->designationService->get_department_list();
        $designations = $this->designationService->get_designation_list();
        $args = [
            'departments' => $departments,
            'designations' => $designations
        ];
        return view('admin.designation.view', $args);
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
    public function store(CreateDesignationRequest $request)
    {
        $this->authorize('create', Designation::class);
        $data = $request->validated();
        $this->designationService->create($data);

        return redirect()->route('admin.designation.all')->with('success', 'Desingation created successfully');
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
    public function update(UpdateDesignationRequest $request, Designation $designation)
    {
        $this->authorize('update', $designation);
        $data = $request->validated();
        $isUpdate = $this->designationService->update($data, $designation);
        if ($isUpdate) {
            return redirect()->route('admin.designation.all')->with('success', 'Designation updated successfully');
        }

        return redirect()->route('admin.designation.all')->with('error', 'Designation not found ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $designation = Designation::findorFail($id);
        $this->authorize('delete', $designation);
        $isDelete = $this->designationService->destroy($id);
        if ($isDelete) {
            return redirect()->route('admin.designation.all')->with('success', 'Designation deleted successfully');
        }

        return redirect()->route('admin.designation.all')->with('error', 'Designation not found ');
    }
}