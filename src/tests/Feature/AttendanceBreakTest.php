<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceBreakTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 5, 31, 10, 00));
        $this->seed();
    }

    public function test_display_break_in_button_when_user_status_is_working_expect_button_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
            'date' => Carbon::now()->toDateString(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertSee('休憩入');
    }

    public function test_change_status_to_on_break_when_user_clicks_break_in_expect_status_is_休憩中()
    {
        $user = User::where('email', 'test@user.com')->first();
    
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
            'clock_in' => '09:00:00',
            'clock_out' => null,
            'date' => Carbon::today()->toDateString(),
        ]);
    
        $this->actingAs($user);
    
        $response = $this->post('/attendance/break-start');
        $response->assertRedirect(route('attendances.create'));
    
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::today()->toDateString())
            ->latest()
            ->first();
    
        $this->assertEquals('休憩中', $attendance->status);
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'start_time' => '10:00:00',
        ]);
    }

    public function test_display_break_in_button_after_return_from_break_expect_button_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
            'date' => Carbon::now()->toDateString(),
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('attendances.create'));
        $response->assertSee('休憩入');
    }

    public function test_change_status_to_working_when_user_clicks_break_out_expect_status_is_出勤中()
    {
        $user = User::where('email', 'test@user.com')->first();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '休憩中',
            'date' => Carbon::now()->toDateString(),
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => '09:00:00',
            'end_time' => null,
        ]);

        $this->actingAs($user);
        $response = $this->post('/attendance/break-end');
        $response->assertRedirect(route('attendances.create'));

        $this->assertEquals('出勤中', $attendance->fresh()->status);
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'end_time' => '10:00:00',
        ]);
    }

    public function test_display_break_out_button_when_user_status_is_on_break_expect_button_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '休憩中',
            'date' => Carbon::now()->toDateString(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertSee('休憩戻');
    }

    public function test_display_break_time_in_attendance_index_view_when_user_takes_break_expect_time_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '出勤中',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('attendances.index', [
            'date' => Carbon::now()->format('Y-m')
        ]));

        $response->assertStatus(200);
        $response->assertSee('1:00');
    }
}
