
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
    <p class="register-attendance__time">{{ $now->format('H:i') }}</p>

    @if (session('message'))
        <p class="message">{{ session('message') }}</p>
    @endif

    @if ($attendance->status === '勤務外')
        <form action="{{ route('attendance.clockIn') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__attendance">出勤</button>
        </form>
    @elseif ($attendance->status === '出勤中')
        <form action="{{ route('attendance.startBreak') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__break">休憩</button>
        </form>
        <form action="{{ route('attendance.clockOut') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__leave">退勤</button>
        </form>
    @elseif ($attendance->status === '休憩中')
        <form action="{{ route('attendance.endBreak') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $now->toDateString() }}">
            <button type="submit" class="register-button__return-break">休憩戻</button>
        </form>
    @endif
</div>
@endsection