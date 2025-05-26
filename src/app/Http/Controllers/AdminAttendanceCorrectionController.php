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
}
