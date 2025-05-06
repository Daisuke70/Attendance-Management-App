<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreCorrectionRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionBreakTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceCorrectionController extends Controller
{
    public function submitCorrectionRequest(StoreCorrectionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $correction = AttendanceCorrectionRequest::create([
                'user_id' => Auth::id(),
                'attendance_id' => $id,
                'new_clock_in' => $request->input('clock_in'),
                'new_clock_out' => $request->input('clock_out'),
                'new_note' => $request->input('note'),
                'status' => 'pending',
            ]);
    
            foreach ($request->input('breaks', []) as $break) {
                if (!empty($break['start']) || !empty($break['end'])) {
                    AttendanceCorrectionBreakTime::create([
                        'attendance_correction_request_id' => $correction->id,
                        'new_start_time' => $break['start'],
                        'new_end_time' => $break['end'],
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('attendance.detail', ['id' => $id])
                ->with('message', '修正申請を送信しました');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '修正申請の送信に失敗しました。']);
        }
    }
}
