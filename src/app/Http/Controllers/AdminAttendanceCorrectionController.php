<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;
use App\Models\Attendance;

class AdminAttendanceCorrectionController extends Controller
{
    public function listAllCorrectionRequests(Request $request)
    {
        $status = $request->query('status','pending');
    
        $query = AttendanceCorrectionRequest::query()
            ->join('attendances', 'attendance_correction_requests.attendance_id', '=', 'attendances.id')
            ->with(['user', 'attendance'])
            ->select('attendance_correction_requests.*');
    
        if (in_array($status, ['pending', 'approved'])) {
            $query->where('attendance_correction_requests.status', $status);
        }
    
        $requests = $query->orderBy('attendances.date', 'asc')->get();
    
        return view('admin.request.index', compact('requests', 'status'));
    }

    public function showApprovalPage($id)
    {
        $correctionRequest = AttendanceCorrectionRequest::with([
            'user',
            'attendance',
            'correctionBreakTimes'
        ])->findOrFail($id);

        return view('admin.request.approve', compact('correctionRequest'));
    }

    public function approveCorrectionRequest(Request $request, $id)
    {
        $correction = AttendanceCorrectionRequest::with(['attendance', 'correctionBreakTimes'])->findOrFail($id);

        $attendance = $correction->attendance;
        $attendance->clock_in = $correction->new_clock_in;
        $attendance->clock_out = $correction->new_clock_out;
        $attendance->note = $correction->new_note;
        $attendance->save();

        $attendance->breakTimes()->delete();
    
        foreach ($correction->correctionBreakTimes as $break) {
            $attendance->breakTimes()->create([
                'start_time' => $break->new_start_time,
                'end_time' => $break->new_end_time,
            ]);
        }

        $correction->status = 'approved';
        $correction->save();
    
        return redirect()->route('admin.correction_requests.index');
    }
}
