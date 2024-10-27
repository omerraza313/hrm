<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class RouteService
{
    public static function get_resource_names(string $name): array
    {
        return [
            'index'   => "$name.all",
            'create'  => "$name.create",
            'store'   => "$name.save",
            'show'    => "$name.view",
            'edit'    => "$name.modify",
            'update'  => "$name.update",
            'destroy' => "$name.delete",
        ];
    }

    public static function get_view_with_role(){
        if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin')) {

            return redirect()->route('admin.dashboard');
        }
        else{
            return redirect()->route('employee.dashboard');
        }
    }
}
