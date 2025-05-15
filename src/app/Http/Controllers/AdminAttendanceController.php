<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function listAllAttendances()
    {
        return view('admin.attendance.index');
    }
}
