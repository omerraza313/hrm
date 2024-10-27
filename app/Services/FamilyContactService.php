<?php

namespace App\Services;

use App\Models\FamilyContact;

class FamilyContactService
{
    public function store(array|object $data)
    {
        FamilyContact::create([
            'name' => $data['family_add_name'],
            'relation' => $data['family_add_relation'],
            'dob' => $data['family_add_dob'],
            'number' => $data['family_add_phone'],
            'user_id' => $data['family_add_employee_id'],
            'ice_status' => 0,
        ]);
    }

    public function destory(int|string $id): bool
    {
        $familyContact = FamilyContact::find($id);
        if ($familyContact) {
            $familyContact->delete();
            return true;
        }
        return false;
    }

    public function update(array|object $data, int|string $id): bool
    {
        $familyContact = FamilyContact::find($id);
        if ($familyContact) {
            $familyContact->update([
                'name' => $data['family_edit_name'],
                'relation' => $data['family_edit_relation'],
                'dob' => $data['family_edit_dob'],
                'number' => $data['family_edit_phone'],
                'user_id' => $data['family_edit_employee_id'],
                'ice_status' => 0,
            ]);
            return true;
        }
        return false;
    }
}
