<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionBreakTime;
use Illuminate\Support\Carbon;

class AttendanceCorrectionRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emails = [
            'test@user.com',
            'test@user3.com',
            'test@user5.com',
        ];

        $fixedDate = Carbon::parse('2025-05-08 18:00:00');

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->command->warn("ユーザー {$email} が見つかりませんでした。");
                continue;
            }

            $attendances = Attendance::where('user_id', $user->id)
                ->inRandomOrder()
                ->limit(6)
                ->get()
                ->sortBy('id')
                ->values();

            foreach ($attendances as $index => $attendance) {
                $status = $index < 3 ? 'pending' : 'approved';

                $correction = AttendanceCorrectionRequest::create([
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'new_clock_in' => '09:00',
                    'new_clock_out' => '17:30',
                    'new_note' => '電車遅延のため',
                    'status' => $status,
                    'created_at' => $fixedDate,
                    'updated_at' => $fixedDate,
                ]);

                AttendanceCorrectionBreakTime::insert([
                    [
                        'attendance_correction_request_id' => $correction->id,
                        'new_start_time' => '12:30',
                        'new_end_time' => '13:00',
                        'created_at' => $fixedDate,
                        'updated_at' => $fixedDate,
                    ],
                    [
                        'attendance_correction_request_id' => $correction->id,
                        'new_start_time' => '13:00',
                        'new_end_time' => '13:30',
                        'created_at' => $fixedDate,
                        'updated_at' => $fixedDate,
                    ]
                ]);
            }
        }
    }
}
