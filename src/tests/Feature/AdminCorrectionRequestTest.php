<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AdminCorrectionRequestTest extends TestCase
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

    public function test_display_pending_corrections_when_accessing_pending_tab_expect_all_pending_requests_visible()
    {
        $admin = $this->getSeededAdmin();
    
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
    
        AttendanceCorrectionRequest::factory()->count(3)->create([
            'status' => 'pending',
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
        ]);
    
        $response = $this->actingAs($admin)->get(route('admin.correction_requests.index', ['status' => 'pending']));
    
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
    }

    public function test_display_approved_corrections_when_accessing_approved_tab_expect_all_approved_requests_visible()
    {
        $admin = $this->getSeededAdmin();

        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
    
        AttendanceCorrectionRequest::factory()->count(3)->create([
            'status' => 'approved',
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.correction_requests.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }

    public function test_display_correction_detail_when_accessing_detail_page_expect_correct_data_shown()
    {
        $admin = $this->getSeededAdmin();

        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $request = AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_note' => '勤務時間に誤りがあるため修正',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.correction_requests.showApproval', ['attendance_correct_request' => $request->id])
        );

        $response->assertStatus(200);
        $response->assertSee('勤務時間に誤りがあるため修正');
    }

    public function test_approve_correction_when_clicking_approve_button_expect_request_status_and_attendance_updated()
    {
        $admin = $this->getSeededAdmin();
        $user = User::factory()->create(['role' => 'user']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
        ]);

        $correction = AttendanceCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_clock_in' => '09:00',
            'new_clock_out' => '18:00',
            'new_note' => '修正承認テスト',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.correction_requests.storeApproval', $correction->id));

        $response->assertRedirect(route('admin.correction_requests.index'));

        $this->assertDatabaseHas('attendance_correction_requests', [
            'id' => $correction->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'note' => '修正承認テスト',
        ]);
    }
}