<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminStaffController extends Controller
{
    public function listAllStaff()
    {
        $users = User::where('role', 'user')->get();

        return view('admin.staff.index', compact('users'));
    }
}
