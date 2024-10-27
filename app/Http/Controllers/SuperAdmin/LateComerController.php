<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Services\LateComerService;
use App\Http\Controllers\Controller;

class LateComerController extends Controller {
    public function __construct(protected LateComerService $lateComerService)
    {
    }

    public function index(Request $request)
    {
        $data = $this->lateComerService->getAttendenceData($request->all());
        return view('admin.attendence.late.view', $data);
    }
}