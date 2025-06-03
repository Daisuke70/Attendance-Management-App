<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getSeededAdmin(): User
    {
        return User::where('email', 'test@admin.com')->firstOrFail();
    }

    public function test_display_attendance_detail_when_admin_accesses_detail_expect_correct_data_shown()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-03',
            'clock_in' => '08:30:00',
            'clock_out' => '17:30:00',
            'note' => '通常勤務',
            'status' => 'finished'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendances.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('08:30');
        $response->assertSee('17:30');
        $response->assertSee('通常勤務');
    }

    public function test_validation_error_when_clock_in_is_after_clock_out_expect_error_message()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->patch(route('admin.attendances.update', ['id' => $attendance->id]), [
            'start_time' => '18:00',
            'end_time' => '09:00',
            'note' => '時間エラー',
        ]);

        $response->assertSessionHasErrors(['end_time']);
        $response->assertRedirect();
    }

    public function test_validation_error_when_break_start_is_after_clock_out_expect_error_message()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->patch(route('admin.attendances.update', ['id' => $attendance->id]), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => '休憩時間エラー',
            'break_times' => [
                ['start_time' => '19:00', 'end_time' => '19:30']
            ]
        ]);

        $response->assertSessionHasErrors(['break_times.0.start_time']);
        $response->assertRedirect();
    }

    public function test_validation_error_when_break_end_is_after_clock_out_expect_error_message()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->patch(route('admin.attendances.update', ['id' => $attendance->id]), [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => '休憩終了時間エラー',
            'break_times' => [
                ['start_time' => '17:00', 'end_time' => '19:00']
            ]
        ]);

        $response->assertSessionHasErrors(['break_times.0.start_time']);
        $response->assertRedirect();
    }

    public function test_validation_error_when_note_is_empty_expect_error_message()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->patch(route('admin.attendances.update', ['id' => $attendance->id]), [
            'start_time' => '08:30',
            'end_time' => '17:30',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note']);
        $response->assertRedirect();
    }
}