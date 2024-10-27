<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Services\ManagerService;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreManagerRequest;
use App\Http\Requests\Manager\UpdateManagerRequest;
use App\Models\User;

class ManagerController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected ManagerService $managerService)
    {
    }
    public function index(Request $request)
    {
        $data = $this->managerService->get_managers_data($request->all());
        return view('admin.manager.view', $data);
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
    public function store(StoreManagerRequest $request)
    {
        $data = $request->validated();
        $this->managerService->create($data);

        return redirect()->route('admin.manager.all')->with('success', 'Manager created successfully');
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
    public function update(UpdateManagerRequest $request, User $manager)
    {
        $data = $request->validated();
        $updateEmployee = $this->managerService->update_employee($data, $manager);
        if ($updateEmployee) {
            return redirect()->route('admin.manager.all')->with('success', 'Manager updated successfully');
        }
        return redirect()->route('admin.manager.all')->with('error', 'Manager not found ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $data = $request->validate([
            'route_name' => 'required',
            'delete_note' => 'required',
            'delete_clearance' => 'required|in:yes,no',
        ]);

        $isDelete = $this->managerService->destroy($data, $id);
        if ($isDelete) {
            return redirect()->route('admin.manager.all')->with('success', 'Manager deleted successfully');
        }

        return redirect()->route('admin.manager.all')->with('error', 'Manager not found ');
    }
}