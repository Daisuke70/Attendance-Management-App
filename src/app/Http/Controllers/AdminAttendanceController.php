<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateAttendanceByAdminRequest;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminAttendanceController extends Controller
{
    public function listAllAttendances(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : now()->startOfDay();

        $attendances = Attendance::with(['user', 'breakTimes'])
            ->whereDate('date', $date)
            ->get();

        $attendances = $attendances->map(function ($attendance) {
            $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                if ($break->end_time) {
                    return \Carbon\Carbon::parse($break->start_time)->diffInMinutes($break->end_time);
                }
                return 0;
            });
    
            $attendance->total_break = gmdate("H:i", $totalBreakMinutes * 60);
    
            if ($attendance->clock_in && $attendance->clock_out) {
                $workMinutes = \Carbon\Carbon::parse($attendance->clock_in)->diffInMinutes($attendance->clock_out) - $totalBreakMinutes;
                $attendance->work_time = gmdate("H:i", max($workMinutes, 0) * 60);
            } else {
                $attendance->work_time = '';
            }
    
            return $attendance;
        });

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'date' => $date,
            'prevDate' => $date->copy()->subDay()->toDateString(),
            'nextDate' => $date->copy()->addDay()->toDateString(),
        ]);
    }

    public function showStaffAttendance($id)
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);
        return view('admin.attendance.detail', compact('attendance'));
    }

    public function updateStaffAttendance(UpdateAttendanceByAdminRequest $request, $id)
    {
        DB::beginTransaction();
    
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->clock_in = $request->input('start_time');
            $attendance->clock_out = $request->input('end_time');
            $attendance->note = $request->input('note');
            $attendance->save();
            $attendance->breakTimes()->delete();
    
            foreach ($request->input('break_times', []) as $break) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => $break['start_time'],
                    'end_time' => $break['end_time'],
                ]);
            }
    
            DB::commit();
            return redirect()->route('admin.attendances.index');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('更新処理に失敗しました。')->withInput();
        }
    }
}
