<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceClockOutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 5, 31, 9, 30));
        $this->seed();
    }

    public function test_display_clock_out_button_when_user_status_is_working_expect_button_is_visible()
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

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertSee('退勤');
    }

    public function test_record_clock_out_time_when_user_clicks_clock_out_expect_time_saved_and_status_updated()
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

        $response = $this->post('/attendance/clock-out', [
            'date' => Carbon::now()->toDateString(),
        ]);

        $response->assertRedirect(route('attendances.create'));

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::now()->toDateString())
            ->first();

        $this->assertEquals('退勤済', $attendance->status);
        $this->assertEquals('09:30:00', $attendance->clock_out);
    }

    public function test_display_clock_out_time_in_attendance_index_view_when_user_clocked_in_expect_time_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'date' => Carbon::today()->toDateString(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendances.index', [
            'date' => Carbon::now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('18:00');
    }
}


