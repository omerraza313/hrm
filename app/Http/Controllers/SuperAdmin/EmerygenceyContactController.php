<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Models\FamilyContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmergencyContact\StoreEmergencyContactRequest;

class EmerygenceyContactController extends Controller
{
    public function store_update(StoreEmergencyContactRequest $request)
    {
        $data = $request->validated();
        FamilyContact::updateOrCreate(
            ['id' => $data['eme_id']],
            [
                'name' => $data['eme_name'],
                'relation' => $data['eme_relation'],
                'number' => $data['eme_number'],
                'employee_id' => $data['eme_employee_id'],
                'ice_status' => 1,
            ]
        );

        return redirect()->back()->with('success', 'Emergency contact updated successfully!');
    }
}
