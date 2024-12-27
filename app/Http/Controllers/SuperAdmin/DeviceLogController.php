<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Models\DeviceLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DeviceLogController extends Controller
{
    public function index() 
    {
        $employees = User::Role(RolesEnum::Employee->value)->get();
        return view('admin.device_logs.index', get_defined_vars());
    }

    public function datatable(Request $request)
    {
        $data = DeviceLog::has('user')->latest();

        return DataTables::of($data->with(['user']))
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="javascript:void(0)" class="editModal btn btn-primary btn-sm" onclick="editItem(' . $row->id . ')">Edit</a> ' .
                    '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="deleteItem(' . $row->id . ')">Delete</a>';
                return $btn;
            })            
            ->addColumn('user_name', function($row){
                return $row->user->full_name;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        Log::info($request->all());

        $validated = $request->validate([
            'user_id' => 'required',
            'device_id' => 'required|string',
            'time' => 'required|date',
            'type' => 'required|string|in:CheckIn,CheckOut',
            'remarks' =>  'nullable|string'
        ]);

        $input = $validated;
        $input['date'] = Carbon::parse($input['time'])->format('Y-m-d');
        $input['time'] = Carbon::parse($input['time']);
        DeviceLog::create($input);

        return response()->json(['success' => 'Device log updated successfully!']);
    }

    public function show($id) {

    }

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
        $deviceLog = DeviceLog::find($id);
        if (!$deviceLog) {
            return response()->json(['error' => 'Device log not found!'], 404);
        }

        $validated = $request->validate([
            'device_id' => 'required|string',
            'user_id' => 'required',
            'time' => 'required|date',
            'type' => 'required|string|in:CheckIn,CheckOut',
            'remarks' =>  'nullable|string'
        ]);

        $input = $validated;
        $input['date'] = Carbon::parse($input['time'])->format('Y-m-d');
        $input['time'] = Carbon::parse($input['time']);

        $deviceLog->update($input);

        return response()->json(['success' => 'Device log updated successfully!']);
    }

    public function destroy(DeviceLog $deviceLog) {
        $deviceLog->delete();
        return response()->json(['success' => 'Device Log deleted successfully!']);
    }
}
