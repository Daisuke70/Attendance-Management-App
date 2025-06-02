<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAttendanceDetailTest extends TestCase
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

    public function test_name_displayed_when_user_views_own_attendance_detail()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('attendances.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_date_displayed_when_user_views_own_attendance_detail()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create([
            'date' => '2025-06-01',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('2025年');
        $response->assertSee('6月1日');
    }

    public function test_clock_in_out_displayed_when_user_views_own_attendance_detail()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_break_time_displayed_when_user_views_own_attendance_detail()
    {
        $user = $this->getSeededUser();
        $attendance = Attendance::factory()->for($user)->create();

        BreakTime::factory()->for($attendance)->create([
            'start_time' => '12:00:00',
            'end_time' => '12:30:00',
        ]);
        BreakTime::factory()->for($attendance)->create([
            'start_time' => '15:00:00',
            'end_time' => '15:15:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendances.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('12:30');
        $response->assertSee('15:00');
        $response->assertSee('15:15');
    }
}
