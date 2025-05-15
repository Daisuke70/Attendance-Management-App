<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showAdminLoginForm()
    {
        return view('admin.auth.login');
    }

    public function adminLogin(LoginRequest $request)
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->role !== 'admin') {
            Auth::logout();
            return redirect()->back()
                ->withErrors(['role' => 'この画面では、一般ユーザーはログインできません'])
                ->withInput();
        }

        return redirect('/admin/attendance/list');
    }


}
