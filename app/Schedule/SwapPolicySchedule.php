<?php

namespace App\Schedule;

use Carbon\Carbon;
use App\Models\PolicySwapJob;

class SwapPolicySchedule {
    public static function swapPolicy()
    {
        $currentDateTime = Carbon::now();

        $swapJobs = PolicySwapJob::where('status', 1)->where(function ($query) use ($currentDateTime) {
            // Policies with the same date or in the past
            $query->where('effect_date', '<', $currentDateTime->format('d/m/Y'))
                ->orWhere(function ($query) use ($currentDateTime) {
                    // Policies with the same date and time in the past
                    $query->where('effect_date', '=', $currentDateTime->format('d/m/Y'))
                        ->where('effect_time', '<=', $currentDateTime->format('g:i A'));
                });
        })->get();

        // dd($swapJobs->toSql());
        foreach ($swapJobs as $job) {

            $mainJob = PolicySwapJob::find($job->id);
            $currentPolicy = $job->current_policy;
            $swapPolicy = $job->swap_policy;

            $currentPolicy = \App\Models\Policy::whereId($currentPolicy)->with(['departments' => function ($query) {
                $query->withTrashed();
            }, 'users'])->first();
            $swapPolicy = \App\Models\Policy::whereId($swapPolicy)->with(['departments' => function ($query) {
                $query->withTrashed();
            }, 'users'])->first();
            $swapPolicy2 = \App\Models\Policy::whereId($job->swap_policy)->with(['departments' => function ($query) {
                $query->withTrashed();
            }, 'users'])->first();



            // Assign Swap Policy to Current Policy
            $swapPolicy->departments()->attach($currentPolicy->departments, ['start_time' => now(), 'created_at' => now(),
                'updated_at' => now(), 'status' => '0']);

            $swapPolicy->users()->attach($currentPolicy->users, ['start_time' => now(), 'created_at' => now(),
                'updated_at' => now(), 'status' => '0']);

            $currentPolicy->departments()->attach($swapPolicy2->departments, ['start_time' => now(), 'created_at' => now(),
                'updated_at' => now(), 'status' => '0']);

            $currentPolicy->users()->attach($swapPolicy2->users, ['start_time' => now(), 'created_at' => now(),
                'updated_at' => now(), 'status' => '0']);
            // Assign Current Policy to Swap Policy

            $mainJob->status = 0;
            $mainJob->save();
        }


    }
}