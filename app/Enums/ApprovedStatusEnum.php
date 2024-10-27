<?php

namespace App\Enums;

enum ApprovedStatusEnum: string
{
    case Pending = '0';
    case Declined = '1';
    case Approved = '2';
}
