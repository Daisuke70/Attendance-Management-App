<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    const STATUS_OFF_DUTY = '勤務外';
    const STATUS_WORKING = '出勤中';
    const STATUS_ON_BREAK = '休憩中';
    const STATUS_FINISHED = '退勤済';

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->reduce(function ($carry, $break) {
            if ($break->end_time) {
                return $carry + Carbon::parse($break->end_time)->diffInMinutes(Carbon::parse($break->start_time));
            }
            return $carry;
        }, 0);
    }

    public function getWorkMinutesAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) return null;
        $total = Carbon::parse($this->clock_out)->diffInMinutes(Carbon::parse($this->clock_in));
        return $total - $this->total_break_minutes;
    }

    public function pendingCorrectionRequest()
    {
        return $this->correctionRequests()->where('status', 'pending')->latest()->first();
    }

    public function hasPendingCorrection(): bool
    {
        return $this->correctionRequests()->where('status', 'pending')->exists();
    }
}
