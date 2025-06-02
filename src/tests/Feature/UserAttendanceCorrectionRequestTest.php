<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getSeededUser(): User
    {
        return User::where('email', 'test@user.com')->firstOrFail();
    }

    public function test_validation_error_when_clock_in_is_after_clock_out_expect_error_message()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();
    
        $response = $this->actingAs($user)->post(
            route('attendance.corrections.submit', $attendance->id),
            [
                'start_time' => '19:00',
                'end_time' => '18:00',
                'note' => '出勤と退勤が逆',
            ]
        );
    
        $response->assertSessionHasErrors([
            'start_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_validation_error_when_break_start_is_after_clock_out_expect_error_message()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_out' => '18:00:00',
        ]);

        $response = $this->followingRedirects()->actingAs($user)->post(
            route('attendance.corrections.submit', $attendance->id),
            [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_times' => [
                    ['start_time' => '19:00', 'end_time' => '19:30'],
                ],
                'note' => '休憩開始が退勤後',
            ]
        );

        $response->assertSee('休憩時間が勤務時間外です');
    }

    public function test_validation_error_when_break_end_is_after_clock_out_expect_error_message()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_out' => '18:00:00',
        ]);

        $response = $this->followingRedirects()->actingAs($user)->post(
            route('attendance.corrections.submit', $attendance->id),
            [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_times' => [
                    ['start_time' => '17:00', 'end_time' => '19:00'],
                ],
                'note' => '休憩終了が退勤後',
            ]
        );

        $response->assertSee('休憩時間が勤務時間外です');
    }

    public function test_validation_error_when_note_is_empty_expect_error_message()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        $response = $this->followingRedirects()->actingAs($user)->post(
            route('attendance.corrections.submit', $attendance->id),
            [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'note' => '',
            ]
        );

        $response->assertSee('備考を記入してください');
    }

    public function test_submit_correction_request_when_valid_data_expect_request_saved()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        $response = $this->actingAs($user)->post(
            route('attendance.corrections.submit', $attendance->id),
            [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'note' => '時間修正のため申請',
            ]
        );

        $response->assertStatus(302);
        $this->assertDatabaseHas('attendance_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'new_note' => '時間修正のため申請',
            'status' => 'pending',
        ]);
    }

    public function test_display_pending_requests_when_user_opens_pending_tab_expect_own_requests_shown()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'new_note' => '申請テスト',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('correction-requests.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('申請テスト');
    }

    public function test_display_approved_requests_when_admin_approves_expect_approved_list_shown()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'new_note' => '承認済み申請',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get(route('correction-requests.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }

    public function test_navigate_to_request_detail_when_user_clicks_detail_expect_redirect_to_detail_page()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        $request = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'new_note' => '詳細確認用',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.detail', $request->id));

        $response->assertStatus(200);
        $response->assertSee('詳細確認用');
        $response->assertSee('勤怠詳細');
    }
}