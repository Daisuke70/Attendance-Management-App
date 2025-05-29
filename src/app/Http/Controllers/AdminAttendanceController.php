<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateAttendanceByAdminRequest;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                if (!empty($break['start_time']) && !empty($break['end_time'])) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $break['start_time'],
                        'end_time' => $break['end_time'],
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('admin.attendances.index');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('更新処理に失敗しました。')->withInput();
        }
    }

    public function listStaffAttendances(Request $request, $id)
    {
        $translator = new Translator('ja');
        $translator->addLoader('array', new ArrayLoader());
        Carbon::setTranslator($translator);
        Carbon::setLocale('ja');

        $user = User::findOrFail($id);

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

        return view('admin.staff.attendance', compact('datesInMonth', 'attendances', 'targetDate', 'user'));
    }

    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $targetDate = $request->input('date')
            ? Carbon::createFromFormat('Y-m', $request->input('date'))
            : Carbon::now();
    
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();
    
        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();
    
        $response = new StreamedResponse(function () use ($attendances, $user, $targetDate) {
            $handle = fopen('php://output', 'w');

            $title = "{$user->name}さんの" . $targetDate->format('Y年n月') . "の勤怠情報";
            fputcsv($handle, [$title]);

            fputcsv($handle, ['日付', '出勤時間', '退勤時間', '休憩時間', '勤務時間']);
        
            foreach ($attendances as $attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->clock_in) : null;
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;
        
                $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                    if ($break->start_time && $break->end_time) {
                        return Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time));
                    }
                    return 0;
                });
        
                $workMinutes = ($clockIn && $clockOut)
                    ? $clockIn->diffInMinutes($clockOut) - $totalBreakMinutes
                    : null;
            
                fputcsv($handle, [
                    Carbon::parse($attendance->date)->format('Y-m-d'),
                    $clockIn ? $clockIn->format('H:i') : '',
                    $clockOut ? $clockOut->format('H:i') : '',
                    gmdate('H:i', $totalBreakMinutes * 60),
                    $workMinutes !== null ? gmdate('H:i', max($workMinutes, 0) * 60) : '',
                ]);
            }
            
            fclose($handle);
        });
    
        $fileName = 'attendance_' . $user->id . '_' . $targetDate->format('Y_m') . '.csv';
    
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$fileName}");
    
        return $response;
    }
}
