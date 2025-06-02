<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 5, 15));
        $this->seed();
    }

    public function test_display_all_attendances_when_user_has_attendance_records_expect_all_are_listed()
    {
        $user = User::where('email', 'test@user.com')->first();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
            'clock_in' => '09:00:00',
        ]);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-10',
            'clock_in' => '09:30:00',
        ]);

        $this->actingAs($user);
    
        $response = $this->get(route('attendances.index', ['date' => '2025-05']));

        $response->assertStatus(200);
        $response->assertSee('2025/05');
        $response->assertSee('05/01');
        $response->assertSee('05/10');
    }

    public function test_display_current_month_when_accessing_attendance_list_expect_current_month_visible()
    {
        $user = User::where('email', 'test@user.com')->first();
        $this->actingAs($user);

        $response = $this->get(route('attendances.index'));
        $response->assertStatus(200);
        $response->assertSee('2025/05');
    }

    public function test_display_previous_month_attendances_when_user_clicks_prev_expect_previous_month_data()
    {
        $user = User::where('email', 'test@user.com')->first();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-04-20',
            'clock_in' => '10:00:00',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('attendances.index', ['date' => '2025-04']));

        $response->assertStatus(200);
        $response->assertSee('2025/04');
        $response->assertSee('04/20');
    }

    public function test_display_next_month_attendances_when_user_clicks_next_expect_next_month_data()
    {
        $user = User::where('email', 'test@user.com')->first();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-05',
            'clock_in' => '08:45:00',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('attendances.index', ['date' => '2025-06']));

        $response->assertStatus(200);
        $response->assertSee('2025/06');
        $response->assertSee('06/05');
    }

    public function test_redirect_to_attendance_detail_when_user_clicks_detail_expect_navigated_to_detail_page()
    {
        $user = User::where('email', 'test@user.com')->first();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('attendances.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
        $response->assertSee('2025年');
        $response->assertSee('5月1日');
    }
}