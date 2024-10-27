<?php

namespace App\Helpers;

use App\Enums\ApprovedStatusEnum;
use App\Models\User;

class AatefsExcelHelper
{
    public static function status_html($status)
    {
        if ($status == ApprovedStatusEnum::Pending->value) {
            return '
            <div class="action-label">
                <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                    <i class="fa fa-dot-circle-o text-primary"></i> Pending
                </a>
            </div>
            ';
        } else if ($status == ApprovedStatusEnum::Declined->value) {
            return '
            <div class="action-label">
                <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                    <i class="fa fa-dot-circle-o text-danger"></i> Declined
                </a>
            </div>
            ';
        } else {
            return '
            <div class="action-label">
                <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                    <i class="fa fa-dot-circle-o text-success"></i> Approved
                </a>
            </div>
            ';
        }
    }

    public static function approved_html($name)
    {
        $img = asset('assets/img/profiles/avatar-09.jpg');
        if (!$name) {
            return '<h2 class="table-avatar">
                    <a href="#" class="avatar avatar-xs"><img
                            src="' . $img . '" alt=""></a>
                    <a href="#">Waiting</a>
                </h2>';
        }
        $admin = User::withTrashed()->find($name);


      $full_name = $admin->first_name . " " . $admin->last_name;
        return '<h2 class="table-avatar">
        <a href="#" class="avatar avatar-xs"><img
                src="' . $img . '" alt=""></a>
        <a href="#">' . $full_name . '</a>
    </h2>';
    }
}
