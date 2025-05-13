@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css')}}">
<link rel="stylesheet" href="{{ asset('css/user/attendance/create.css')}}">
@endsection

@section('content')
<div class="register-attendance">
    <p class="register-attendance__status">{{ $attendance->status }}</p>
    @php
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $now = $attendance->created_at ?? \Carbon\Carbon::now();
    @endphp

    <p class="register-attendance__date">{{ $now->format('Y年n月j日') }}({{ $weekdays[$now->dayOfWeek] }})</p>
    <p class="register-attendance__time" id="current-time">
        {{ $now->format('H:i') }}
    </p>

    @if ($attendance->status === '勤務外')
        <form action="{{ route('attendance.clockIn') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__clock-in">出勤</button>
        </form>
    @elseif ($attendance->status === '出勤中')
        <div class="register-attendance__working">
            <form action="{{ route('attendance.clockOut') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $now->toDateString() }}">
                <button type="submit" class="register-button__clock-out">退勤</button>
            </form>
            <form action="{{ route('attendance.startBreak') }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ $now->toDateString() }}">
                <button type="submit" class="register-button__start-break">休憩入</button>
            </form>
        </div>
    @elseif ($attendance->status === '休憩中')
        <form action="{{ route('attendance.endBreak') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__end-break">休憩戻</button>
        </form>
    @elseif ($attendance->status === '退勤済')
        <p class="register-attendance__message">お疲れ様でした。</p>
    @endif
</div>

<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('current-time').textContent = `${hours}:${minutes}`;
    }

    updateClock();

    setInterval(updateClock, 60000);
</script>
@endsection