<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminStaffAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2025, 5, 15));
        $this->seed();
    }

    private function getSeededAdmin(): User
    {
        return User::where('email', 'test@admin.com')->firstOrFail();
    }

    public function test_display_user_list_when_admin_accesses_staff_list_expect_all_users_visible()
    {
        $admin = $this->getSeededAdmin();

        $users = User::factory()->count(3)->create(['role' => 'user']);

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function test_display_attendances_when_admin_accesses_user_attendance_list_expect_data_visible()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);

        Attendance::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendances.index', ['id' => $user->id]));

        $response->assertStatus(200);

        $response->assertSee($user->name);
    }

    public function test_display_previous_month_when_admin_clicks_prev_expect_previous_data_visible()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);

        $lastMonth = Carbon::now()->subMonth();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-04-20',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendances.index', [
            'id' => $user->id,
            'date' => '2025-04'
        ]));

        $response->assertStatus(200);
        $response->assertSee('2025/04');
    }

    public function test_display_next_month_when_admin_clicks_next_expect_next_data_visible()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);

        $nextMonth = Carbon::now()->addMonth();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-20',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.staff.attendances.index', [
            'id' => $user->id,
            'date' => '2025-06'
        ]));

        $response->assertStatus(200);
        $response->assertSee('2025/06');
    }

    public function test_transition_to_detail_page_when_admin_clicks_detail_button_expect_redirect_to_detail()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.attendances.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
