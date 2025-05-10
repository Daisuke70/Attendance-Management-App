<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

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

        $lastBreak = $attendance->breakTimes()->whereNull('end_time')->latest()->first();
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

    public function listUserAttendances(Request $request)
    {
        $translator = new Translator('ja');
        $translator->addLoader('array', new ArrayLoader());
        Carbon::setTranslator($translator);
        Carbon::setLocale('ja');

        $user = auth()->user();

        $targetDate = $request->input('date')
            ? Carbon::createFromFormat('Y-m', $request->input('date'))
            : Carbon::now();

        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();

        $datesInMonth = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $datesInMonth->push($date->copy());
        }

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('date');

        return view('user.attendance.index', compact('datesInMonth', 'attendances', 'targetDate'));
    }

    public function showAttendanceDetail($id)
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);
    
        $correctionRequest = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->first();
    
        $correctionBreaks = $correctionRequest 
            ? $correctionRequest->correctionBreakTimes->values()
            : collect();
    
        $isPending = !is_null($correctionRequest);
    
        return view('user.attendance.detail', compact('attendance', 'correctionRequest', 'correctionBreaks', 'isPending'));
    }
}
