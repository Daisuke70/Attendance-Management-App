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
        $user = User::where('email', 'test@user.com')->first();
        if (!$user) {
            $this->command->warn('ユーザーが見つかりませんでした。');
            return;
        }

        $attendances = Attendance::where('user_id', $user->id)->inRandomOrder()->limit(10)->get();

        foreach ($attendances as $index => $attendance) {
            $status = $index < 7 ? 'pending' : 'approved';

            $correction = AttendanceCorrectionRequest::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'new_clock_in' => '09:00',
                'new_clock_out' => '17:30',
                'new_note' => $status === 'pending' ? '電車遅延のため（遅延証明書あり）' : '遅刻申請承認済み',
                'status' => $status,
            ]);

            AttendanceCorrectionBreakTime::create([
                'attendance_correction_request_id' => $correction->id,
                'new_start_time' => '12:00',
                'new_end_time' => '12:30',
            ]);

            AttendanceCorrectionBreakTime::create([
                'attendance_correction_request_id' => $correction->id,
                'new_start_time' => '12:30',
                'new_end_time' => '13:00',
            ]);
        }
    }
}
