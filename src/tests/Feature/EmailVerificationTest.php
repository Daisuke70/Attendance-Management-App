<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Carbon\Carbon;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_verification_email_when_user_registers_expect_verification_email_sent()
    {
        Notification::fake();
    
        $user = User::factory()->unverified()->create(['role' => 'user']);

        $user->sendEmailVerificationNotification();
    
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_redirect_to_verification_url_when_click_verification_link_expect_show_verification_page()
    {
        $user = User::factory()->unverified()->create(['role' => 'user']);

        $this->actingAs($user);

        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
    }

    public function test_complete_verification_when_clicking_verification_link_expect_redirect_to_attendance_page()
    {
        $user = User::factory()->unverified()->create(['role' => 'user']);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}