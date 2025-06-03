<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function getSeededUser(): User
    {
        return User::where('email', 'test@admin.com')->firstOrFail();
    }

    public function test_display_all_attendance_data_when_admin_accesses_attendance_list_expect_all_data_visible()
    {
        $admin = $this->getSeededUser();
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        $today = Carbon::today()->toDateString();

        Attendance::factory()->create(['user_id' => $user1->id, 'date' => $today]);
        Attendance::factory()->create(['user_id' => $user2->id, 'date' => $today]);

        $response = $this->actingAs($admin)->get(route('admin.attendances.index', ['date' => $today]));

        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    public function test_display_current_date_when_admin_accesses_attendance_list_expect_today_visible()
    {
        $admin = $this->getSeededUser();
        $today = now()->format('Y/m/d');

        $response = $this->actingAs($admin)->get(route('admin.attendances.index'));

        $response->assertStatus(200);
        $response->assertSee($today);
    }

    public function test_display_previous_day_data_when_admin_clicks_prev_expect_previous_day_visible()
    {
        $admin = $this->getSeededUser();
        $user = User::factory()->create(['role' => 'user']);
        $yesterday = now()->format('Y/m/d');

        Attendance::factory()->create(['user_id' => $user->id, 'date' => $yesterday]);

        $response = $this->actingAs($admin)->get(route('admin.attendances.index', ['date' => $yesterday]));

        $response->assertStatus(200);
        $response->assertSee($yesterday);
        $response->assertSee($user->name);
    }

    public function test_display_next_day_data_when_admin_clicks_next_expect_next_day_visible()
    {
        $admin = $this->getSeededUser();
        $user = User::factory()->create(['role' => 'user']);
        $tomorrow = now()->format('Y/m/d');

        Attendance::factory()->create(['user_id' => $user->id, 'date' => $tomorrow]);

        $response = $this->actingAs($admin)->get(route('admin.attendances.index', ['date' => $tomorrow]));

        $response->assertStatus(200);
        $response->assertSee($tomorrow);
        $response->assertSee($user->name);
    }
}