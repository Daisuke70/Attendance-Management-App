<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;

class AttendancesSeeder extends Seeder
{
    public function run(): void
    {
        $emails = [
            'test@user.com',
            'test@user2.com',
            'test@user3.com',
            'test@user4.com',
            'test@user5.com',
        ];

        $users = User::whereIn('email', $emails)->get()->keyBy('email');

        foreach ($emails as $email) {
            $user = $users->get($email);

            if (!$user) {
                $this->command->warn("ユーザー {$email} が見つかりませんでした。");
                continue;
            }

            $startDate = Carbon::parse('2025-04-01');
            $endDate = Carbon::parse('2025-05-22');

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
}