<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('user.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        session(['email_for_verification' => $user->email]);

        return redirect('/email/verify');
    }

    public function showLoginForm()
    {
        return view('user.auth.login');
    }

    public function login(LoginRequest $request)
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    
        $user = Auth::user();
    
        if (!$user->hasVerifiedEmail()) {
            $email = $request->input('email');
            Auth::logout();
            session(['email_for_verification' => $email]);
            return redirect('/email/verify');
        }
    
        return redirect('/attendance');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
