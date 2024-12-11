<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceLogController extends Controller
{
    public function edit($id)
    {
        $deviceLog = DeviceLog::find($id);

        if ($deviceLog) {
            return response()->json(['success' => true, 'device_log' => $deviceLog]);
        }

        return response()->json(['error' => 'Device log not found!'], 404);
    }

    public function update(Request $request, $id)
    {
        Log::info($request->all());

        $deviceLog = DeviceLog::find($id);
        if (!$deviceLog) {
            return response()->json(['error' => 'Device log not found!'], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|string',
            'time' => 'required|date',
            'type' => 'required|string|in:CheckIn,CheckOut',
            'imported' => 'required|integer|in:0,1',
            'date' => 'required|date',
        ]);

        $deviceLog->update($validated);

        return response()->json(['success' => 'Device log updated successfully!']);
    }

    public function destroy(DeviceLog $log) {
        $log->delete();
        return response()->json(['success' => 'User deleted successfully!']);
    }
}
