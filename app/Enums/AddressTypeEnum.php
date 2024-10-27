<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    case permanent = '1';
    case current = '2';
    case both = '3';
}
