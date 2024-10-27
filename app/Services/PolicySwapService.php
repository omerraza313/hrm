<?php

namespace App\Services;

use App\Models\PolicySwapJob;
use Carbon\Carbon;

class PolicySwapService {
    public function store_swap_job(array|object $data): bool
    {
        // dd($data);
        if ($data['swap_effect_date'] || $data['swap_effect_time']) {
            if (!$data['swap_effect_date']) {
                $data['swap_effect_date'] = Carbon::now()->endOfDay()->format('d/m/Y');
                // $data['swap_effect_date'] = Carbon::now()->timezone('America/New_York')->endOfDay()->format('d/m/Y');
            }

            if (!$data['swap_effect_time']) {
                $data['swap_effect_time'] = Carbon::now()->endOfDay()->format('h:i A');
                // $data['swap_effect_time'] = Carbon::now()->timezone('America/New_York')->endOfDay()->format('h:i A');
            }
        }


        if ($data['swap_rollback_date'] || $data['swap_rollback_time']) {
            if (!$data['swap_rollback_date']) {
                $data['swap_rollback_date'] = Carbon::now()->endOfDay()->format('d/m/Y');
                // $data['swap_rollback_date'] = Carbon::now()->timezone('America/New_York')->endOfDay()->format('d/m/Y');
            }

            if (!$data['swap_rollback_time']) {
                $data['swap_rollback_time'] = Carbon::now()->endOfDay()->format('h:i A');
                // $data['swap_rollback_time'] = Carbon::now()->timezone('America/New_York')->endOfDay()->format('h:i A');
            }
        }

        PolicySwapJob::create([
            'current_policy' => $data['swap_current_policy'],
            'swap_policy' => $data['swap_with_policy'],
            'effect_date' => $data['swap_effect_date'],
            'effect_time' => $data['swap_effect_time'],
            'rollback_date' => $data['swap_rollback_date'],
            'rollback_time' => $data['swap_rollback_time'],
            'status' => 1,
        ]);
        return true;
    }
}
