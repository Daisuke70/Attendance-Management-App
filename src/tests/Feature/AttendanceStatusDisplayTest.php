<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceStatusDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Carbon::setTestNow(Carbon::create(2025, 5, 31, 9, 30));
    }

    public function test_display_attendance_status_when_user_status_is_off_duty_expect_status_is_勤務外()
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
        $response->assertSee('勤務外');
    }

    public function test_display_attendance_status_when_user_status_is_working_expect_status_is_出勤中()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '出勤中',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function test_display_attendance_status_when_user_status_is_on_break_expect_status_is_休憩中()
    {
        $user = User::where('email', 'test@user.com')->first();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now()->toDateString(),
            'status' => '休憩中',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function test_display_attendance_status_when_user_status_is_clocked_out_expect_status_is_退勤済()
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
        $response->assertSee('退勤済');
    }
}
