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
                <p class="attendance-detail__year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</p>
                <p class="attendance-detail__date">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</p>
            </div>

            <div class="attendance-detail__group">
                <p class="attendance-detail__label">出勤・退勤</p>
                <div class="attendance-detail__start-end">
                    @php
                        $clockIn = old('start_time')
                            ?? optional($correctionRequest)->new_clock_in
                                ?? $attendance->clock_in;

                        $clockOut = old('end_time')
                            ?? optional($correctionRequest)->new_clock_out
                            ?? $attendance->clock_out;
                    @endphp

                    <input type="time" name="start_time"
                        value="{{ $clockIn ? \Carbon\Carbon::parse($clockIn)->format('H:i') : '' }}"
                        class="attendance-detail__input"
                        @if ($isPending) readonly @endif
                        onclick="this.showPicker && this.showPicker()">

                    <span class="tilde">〜</span>

                    <input type="time" name="end_time"
                        value="{{ $clockOut ? \Carbon\Carbon::parse($clockOut)->format('H:i') : '' }}"
                        class="attendance-detail__input"
                        @if ($isPending) readonly @endif
                        onclick="this.showPicker && this.showPicker()">
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
                    @php
                        $oldBreaksRaw = old('break_times', []);
                        $oldBreaks = collect($oldBreaksRaw)->filter(function ($break) {
                            return !empty($break['start_time']) || !empty($break['end_time']);
                        })->values();

                        $correctionBreaksCount = $correctionBreaks->count();
                        $attendanceBreaksCount = $attendance->breakTimes->count();
                        $maxExistingCount = max($oldBreaks->count(), $correctionBreaksCount, $attendanceBreaksCount);
                        $breakCount = $maxExistingCount + 1;
                    @endphp

                    @for ($i = 0; $i < $breakCount; $i++)
                        @php
                            $start = old("break_times.$i.start_time")
                                ?? optional($correctionBreaks[$i] ?? null)->new_start_time
                                ?? optional($attendance->breakTimes[$i] ?? null)->start_time;

                            $end = old("break_times.$i.end_time")
                                ?? optional($correctionBreaks[$i] ?? null)->new_end_time
                                ?? optional($attendance->breakTimes[$i] ?? null)->end_time;
                        @endphp

                        <div class="attendance-detail__break-time__input">
                            <label class="break-time__label">
                                <input type="time" name="break_times[{{ $i }}][start_time]"
                                    value="{{ $start ? \Carbon\Carbon::parse($start)->format('H:i') : '' }}"
                                    class="break-time__input"
                                    @if ($isPending) readonly @endif
                                    onclick="this.showPicker && this.showPicker()">
                            </label>
                            <span class="break-time__tilde">〜</span>
                            <label class="break-time__label">
                                <input type="time" name="break_times[{{ $i }}][end_time]"
                                    value="{{ $end ? \Carbon\Carbon::parse($end)->format('H:i') : '' }}"
                                    class="break-time__input"
                                    @if ($isPending) readonly @endif
                                    onclick="this.showPicker && this.showPicker()">
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
                    @endfor
                </div>
            </div>

            <div class="attendance-detail__group-remarks">
                <p class="attendance-detail__label">備考</p>
                <textarea name="note" class="attendance-detail__textarea @if($isPending) is-readonly @endif" rows="4"
                    @if ($isPending) readonly @endif>{{ old('note') ?? optional($correctionRequest)->new_note ?? $attendance->note }}</textarea>
            </div>
            <p class="attendance-detail__error-message__note">
                @error('note')
                {{ $message }}
                @enderror
            </p>
        </div>

        @if (!$isPending)
            <div class="attendance-detail__button">
                <button type="submit" class="attendance-detail__button-submit">修正</button>
            </div>
        @else
            <p class="attendance-detail__pending-message">*承認待ちのため修正はできません。</p>
        @endif
    </form>
</div>
@endsection