@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/attendance/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h2>勤怠詳細</h2>
    <form action="{{ route('attendance.corrections.submit', ['id' => $attendance->id]) }}" method="POST">
        @csrf
        <div class="attendance-detail__table">
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
                    <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}" class="attendance-detail__input" onclick="this.showPicker && this.showPicker()">
                        <span class="tilde">〜</span>
                    <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}" class="attendance-detail__input" onclick="this.showPicker && this.showPicker()">
                    <p class="attendance-detail__error-message">
                        @error('start_time')
                        {{ $message }}
                        @enderror
                    </p>
                    <p class="attendance-detail__error-message">
                        @error('end_time')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
            </div>
            <div class="attendance-detail__group">
                <p class="attendance-detail__label">休憩</p>
                <div class="attendance-detail__break-time">
                    @foreach ($attendance->breakTimes as $i => $break)
                        <div class="attendance-detail__break-time__input">
                            <label class="break-time__label">
                                <input type="time" name="break_times[{{ $i }}][start_time]" value="{{ \Carbon\Carbon::parse($break->start_time)->format('H:i') }}" class="break-time__input" onclick="this.showPicker && this.showPicker()">
                            </label>
                            <span class="break-time__tilde">〜</span>
                            <label class="break-time__label">
                                <input type="time" name="break_times[{{ $i }}][end_time]" value="{{ \Carbon\Carbon::parse($break->end_time)->format('H:i') }}" class="break-time__input" onclick="this.showPicker && this.showPicker()">
                            </label>
                        </div>
                        <p class="attendance-detail__break-time__error-message">
                            @error("break_times.$i.start_time")
                            {{ $message }}
                            @enderror
                        </p>
                        <p class="attendance-detail__break-time__error-message">
                            @error("break_times.$i.end_time")
                            {{ $message }}
                            @enderror
                        </p>
                    @endforeach

                    @php $index = $attendance->breakTimes->count(); @endphp
                    <div class="attendance-detail__break-time__input">
                        <label class="break-time__label">
                            <input type="time" name="break_times[{{ $index }}][start_time]" class="break-time__input" onclick="this.showPicker && this.showPicker()">
                        </label>
                        <span class="break-time__tilde">〜</span>
                        <label class="break-time__label">
                            <input type="time" name="break_times[{{ $index }}][end_time]" class="break-time__input" onclick="this.showPicker && this.showPicker()">
                        </label>
                    </div>
                    <p class="attendance-detail__break-time__error-message">
                        @error("break_times.$index.start_time")
                        {{ $message }}
                        @enderror
                    </p>
                    <p class="attendance-detail__break-time__error-message">
                        @error("break_times.$index.end_time")
                        {{ $message }}
                        @enderror
                    </p>
                </div>
            </div>
            <div class="attendance-detail__group-remarks">
                <p class="attendance-detail__label">備考</p>
                <textarea name="note" class="attendance-detail__textarea" rows="4"></textarea>
            </div>
            <p class="attendance-detail__error-message">
                @error('note')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="attendance-detail__button">
            <button type="submit" class="attendance-detail__button-submit">修正</button>
        </div>
    </form>
</div>
@endsection