<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => '有効なメールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }

    public function authenticate()
    {
        $credentials = $this->only('email', 'password');
        $remember = $this->filled('remember');

        $email = $this->input('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        if (!Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'ログイン情報が登録されていません',
            ]);
        }

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => '認証に失敗しました。もう一度お試しください',
            ]);
        }
    }
}