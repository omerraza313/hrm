<?php

namespace App\Enums;

enum PermissionEnum: string
{
        // Department Enums
    case AddDepartment = 'add_department';
    case EditDepartment = 'edit_department';
    case ViewDepartment = 'view_department';
    case DeleteDepartment = 'delete_department';

        // Designation Enums
    case AddDesignation = 'add_designation';
    case EditDesignation = 'edit_designation';
    case ViewDesignation = 'view_designation';
    case DeleteDesignation = 'delete_designation';

        // Employee Enums
    case AddEmployee = 'add_employee';
    case EditEmployee = 'edit_employee';
    case ViewEmployee = 'view_employee';
    case DeleteEmployee = 'delete_employee';
}
