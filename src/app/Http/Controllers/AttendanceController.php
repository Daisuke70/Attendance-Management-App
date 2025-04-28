<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function showAttendanceForm()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        return view('user.attendance.create');
    }
}
