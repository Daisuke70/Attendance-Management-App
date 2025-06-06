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
                'new_clock_in' => $request->input('start_time'),
                'new_clock_out' => $request->input('end_time'),
                'new_note' => $request->input('note'),
                'status' => 'pending',
            ]);
    
            foreach ($request->input('break_times', []) as $break) {
                if (!empty($break['start_time']) || !empty($break['end_time'])) {
                    AttendanceCorrectionBreakTime::create([
                        'attendance_correction_request_id' => $correction->id,
                        'new_start_time' => $break['start_time'],
                        'new_end_time' => $break['end_time'],
                    ]);
                }
            }
    
            DB::commit();
    
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back();
        }
    }

    public function listUserRequests(Request $request)
    {
        $status = $request->query('status', 'pending');
        $userId = Auth::id();
    
        $query = AttendanceCorrectionRequest::query()
            ->join('attendances', 'attendance_correction_requests.attendance_id', '=', 'attendances.id')
            ->with(['user', 'attendance'])
            ->select('attendance_correction_requests.*')
            ->where('attendance_correction_requests.user_id', $userId);
    
        if (in_array($status, ['pending', 'approved'])) {
            $query->where('attendance_correction_requests.status', $status);
        }
    
        $requests = $query->orderBy('attendances.date', 'asc')->get();
    
        return view('user.request.index', compact('requests', 'status'));
    }
}
