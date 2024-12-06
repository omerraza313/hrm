<?php

namespace App\Enums;

enum PermissionEnum: string
{
        // Department Enums
    case AddDepartment = 'department.create';
    case EditDepartment = 'department.edit';
    case ViewDepartment = 'departments.index';
    case DeleteDepartment = 'department.delete';

        // Designation Enums
    case AddDesignation = 'designation.create';
    case EditDesignation = 'designation.edit';
    case ViewDesignation = 'designations.index';
    case DeleteDesignation = 'designation.delete';

        // Employee Enums
    case AddEmployee = 'employees.create';
    case EditEmployee = 'employees.edit';
    case ViewEmployee = 'employees.index';
    case DeleteEmployee = 'employees.delete';
}
