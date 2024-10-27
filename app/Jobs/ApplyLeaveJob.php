<?php

namespace App\Jobs;

use App\Models\User;
use App\Enums\RolesEnum;
use App\Mail\ApplyLeaveMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ApplyLeaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected User $employee)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $admins = User::role([RolesEnum::Admin->value, RolesEnum::SuperAdmin->value])->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new ApplyLeaveMail($this->employee));
        }
    }
}
