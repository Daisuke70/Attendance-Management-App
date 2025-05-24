<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;

class AdminAttendanceCorrectionController extends Controller
{
    public function listAllCorrectionRequests(Request $request)
    {
        $status = $request->query('status');
    
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
}
