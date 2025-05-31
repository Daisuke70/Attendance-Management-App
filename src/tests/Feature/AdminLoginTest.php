<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_login_fails_when_email_is_missing_expect_validation_message()
    {
        $response = $this->post('/admin/login', [
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_admin_login_fails_when_password_is_missing_expect_validation_message()
    {
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_admin_login_fails_when_credentials_are_invalid_expect_validation_message()
    {
        $response = $this->post('/admin/login', [
            'email' => 'notfound@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}

