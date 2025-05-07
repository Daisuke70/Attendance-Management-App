<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@user.com')->first();
        if (!$user) {
            $this->command->warn('ユーザーが見つかりませんでした。');
            return;
        }

        $startDate = Carbon::parse('2025-01-01');
        $endDate = Carbon::parse('2025-03-31');

        while ($startDate->lte($endDate)) {
            if ($startDate->isWeekday()) {
                $attendance = Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => $startDate->toDateString(),
                    'clock_in' => '08:30',
                    'clock_out' => '17:30',
                    'status' => '退勤済み',
                ]);

                BreakTime::factory()->create([
                    'attendance_id' => $attendance->id,
                    'start_time' => '12:00',
                    'end_time' => '12:30',
                ]);

                BreakTime::factory()->create([
                    'attendance_id' => $attendance->id,
                    'start_time' => '12:30',
                    'end_time' => '13:00',
                ]);
            }

            $startDate->addDay();
        }
    }
}