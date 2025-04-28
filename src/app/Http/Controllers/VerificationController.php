<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function showVerificationNotice()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        return view('user.auth.email');
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals(sha1($user->email), $hash)) {
            return redirect('/');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);

        return redirect('/attendance');
    }

    public function resendVerificationEmail()
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect('/attendance')->with('success', 'すでにメール認証が完了しています。');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', '認証メールを再送しました！');
    }
}
