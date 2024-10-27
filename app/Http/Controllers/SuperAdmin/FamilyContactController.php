<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyContact\StoreFamilyContactRequest;
use App\Http\Requests\FamilyContact\UpdateFamilyContactRequest;
use App\Services\FamilyContactService;
use Illuminate\Http\Request;

class FamilyContactController extends Controller
{
    public function __construct(protected FamilyContactService $familyContactService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreFamilyContactRequest $request)
    {
        $data = $request->validated();
        $this->familyContactService->store($data);

        return redirect()->back()->with('success', 'Family member added successfully!');
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
    public function update(UpdateFamilyContactRequest $request, string $id)
    {
        $data = $request->validated();
        $isUpdate = $this->familyContactService->update($data, $id);

        if ($isUpdate) {
            return redirect()->back()->with('success', 'Family member updated successfully!');
        }
        return redirect()->back()->with('error', 'Some error occured!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id)
    {
        $delete_status = $this->familyContactService->destory($id);
        if ($delete_status) {
            return redirect()->back()->with('success', 'Family member deleted successfully!');
        }
        return redirect()->back()->with('error', 'Some error occured!');
    }
}
