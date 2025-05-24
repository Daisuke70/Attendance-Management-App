<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrectionRequest;

class AdminAttendanceCorrectionController extends Controller
{
    public function listAllCorrectionRequests(Request $request)
    {
        $status = $request->query('status', 'pending');

        $correctionRequests = AttendanceCorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status)
            ->whereHas('user', function ($query) {
                $query->where('role', 'user');
            })
            ->latest();

        return view('admin.correction_requests.index', [
            'correctionRequests' => $correctionRequests,
            'currentStatus' => $status,
        ]);
    }
}
