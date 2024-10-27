<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applyleaves extends Model
{
    use HasFactory;

    protected $table = 'applyleaves';

    protected $fillable = [
        'subject',
        'body',
        'leave_from',
        'leave_upto',
        'status',
        'approved_by',
        'user_id',
        'document',
        'status_note'
    ];

    public function employee(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approved_by(){
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function adjust_leaves(){
        return $this->hasMany(LeaveAdjust::class, 'applyleave_id', 'id');
    }
}
