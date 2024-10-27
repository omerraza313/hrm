<?php

namespace App\Enums;

enum RolesEnum: string {
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Employee = 'employee';
    case Manager = 'manager';
}