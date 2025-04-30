<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function showAttendanceForm()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => Attendance::STATUS_OFF_DUTY]
        );

        return view('user.attendance.create', compact('attendance'));
    }

    public function clockIn()
    {
        $attendance = $this->getTodayAttendance();

        if ($attendance->clock_in === null) {
            $attendance->clock_in = now();
            $attendance->status = Attendance::STATUS_WORKING;
            $attendance->save();
        }

        return redirect()->back();
    }

    public function startBreak()
    {
        $attendance = $this->getTodayAttendance();

        $attendance->status = Attendance::STATUS_ON_BREAK;
        $attendance->save();

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now()->format('H:i:s'),
            'end_time' => null,
        ]);
    
        return redirect()->back();
    }
    
    public function endBreak()
    {
        $attendance = $this->getTodayAttendance();

        $lastBreak = $attendance->breaks()->whereNull('end_time')->latest()->first();
        if ($lastBreak) {
            $lastBreak->end_time = now()->format('H:i:s');
            $lastBreak->save();
        }

        $attendance->status = Attendance::STATUS_WORKING;
        $attendance->save();
    
        return redirect()->back();
    }


    public function clockOut()
    {
        $attendance = $this->getTodayAttendance();

        if ($attendance->clock_out === null) {
            $attendance->clock_out = now();
            $attendance->status = Attendance::STATUS_FINISHED;
            $attendance->save();
        }

        return redirect()->back()->with('message', 'お疲れ様でした。');
    }

    private function getTodayAttendance()
    {
        return Attendance::firstOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()],
            ['status' => Attendance::STATUS_OFF_DUTY]
        );
    }
}
