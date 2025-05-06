<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionBreakTime extends Model
{
    protected $fillable = [
        'attendance_correction_request_id',
        'new_start_time',
        'new_end_time',
    ];

    public function correctionRequest()
    {
        return $this->belongsTo(AttendanceCorrectionRequest::class);
    }
}
