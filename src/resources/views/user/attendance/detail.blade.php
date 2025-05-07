@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/attendance/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h2>勤怠詳細</h2>
    <div class="attendance-detail__table">
        <form action="{{ route('attendance.corrections.submit', ['id' => $attendance->id]) }}" method="POST">
            @csrf
            <div class="attendance-detail__group">
                <p class="attendance-detail__label">名前</p>
                <p class="attendance-detail__name">{{ $attendance->user->name }}</p>
            </div>
            <div class="attendance-detail__group">
                <p class="attendance-detail__label">日付</p>
                <p class="attendance-detail__year">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                </p>
                <p class="attendance-detail__date">
                    {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                </p>
            </div>
            <div class="attendance-detail__group">
                <p class="attendance-detail__label">出勤・退勤</p>
                <div class="attendance-detail__start-end">
                    <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}" class="attendance-detail__input">
                        〜
                    <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}" class="attendance-detail__input">
                </div>
            </div>
            <div class="attendance-detail__group">
                <p class="attendance-detail__label">休憩</p>
                <div class="attendance-detail__break-time">
                    @foreach ($attendance->breakTimes as $i => $break)
                        <input type="time" name="breaks[{{ $i }}][start]" value="{{ \Carbon\Carbon::parse($break->start_time)->format('H:i') }}" class="attendance-detail__input">
                            〜
                        <input type="time" name="breaks[{{ $i }}][end]" value="{{ \Carbon\Carbon::parse($break->end_time)->format('H:i') }}" class="attendance-detail__input">
                    @endforeach
                    <input type="time" name="breaks[{{ $attendance->breakTimes->count() }}][start]" class="attendance-detail__input">
                        ~
                    <input type="time" name="breaks[{{ $attendance->breakTimes->count() }}][end]" class="attendance-detail__input">
                </div>
            </div>
            <div class="attendance-detail__group-remarks">
                <p class="attendance-detail__label">備考</p>
                <textarea name="note" class="attendance-detail__textarea" rows="3"></textarea>
            </div>
            <div class="attendance-detail__button">
                <button type="submit" class="attendance-detail__button">修正</button>
            </div>
        </form>
    </div>
</div>
@endsection