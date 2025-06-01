<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;

class AttendanceTimestampDisplayTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_display_current_datetime_when_access_attendance_form_expect_form_shows_formatted_datetime()
    {
        $fixedNow = Carbon::create(2025, 5, 31, 9, 30);
        Carbon::setTestNow($fixedNow);

        $user = User::where('email', 'test@user.com')->first();
        $this->actingAs($user);

        $response = $this->get(route('attendances.create'));
        $response->assertStatus(200);

        $wday = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $wday[$fixedNow->dayOfWeek];

        $response->assertSee($fixedNow->format("Y年n月j日({$weekday})"));
        $response->assertSee('09:30');
    }
}