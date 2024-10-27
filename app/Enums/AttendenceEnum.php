<?php

namespace App\Enums;

enum AttendenceEnum: string {
    case Late = '0';
    case OnTime = '1';
    case Holiday = '2';
    case Absent = '3';
    case Leave = '4';
}