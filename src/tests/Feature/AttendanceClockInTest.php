<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceClockInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 5, 31, 9, 30));
        $this->seed();
    }

    public function test_display_clock_in_button_when_user_status_is_off_duty_expect_button_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '勤務外',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertSee('出勤');
    }

    public function test_prevent_duplicate_clock_in_when_user_already_clocked_out_expect_button_not_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '退勤済',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    public function test_record_clock_in_time_when_user_clicks_clock_in_expect_time_saved_and_status_updated()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '勤務外',
            'clock_in' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/clock-in', [
            'date' => Carbon::now()->toDateString(),
        ]);

        $response->assertRedirect(route('attendances.create'));

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::now()->toDateString())
            ->first();

        $this->assertEquals('出勤中', $attendance->status);
        $this->assertEquals('09:30:00', $attendance->clock_in);
    }

    public function test_display_clock_in_time_in_attendance_index_view_when_user_clocked_in_expect_time_is_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'clock_in' => '09:30:00',
            'status' => '出勤中',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('attendances.index', [
            'date' => Carbon::now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('09:30');
    }
}
