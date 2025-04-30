<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:3,1')->only('resendVerificationEmail');
    }

    public function showVerificationNotice()
    {
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

        session()->forget('email_for_verification');

        return redirect('/attendance');
    }

    public function resendVerificationEmail(Request $request)
    {
        $email = $request->input('email');

        if (!$email) {
            return redirect('/login');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', '認証メールを再送しました！ご確認ください。');
    }
}