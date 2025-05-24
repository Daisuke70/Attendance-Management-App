@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance/detail.css') }}">
@endsection

@section('content')
<div class="admin-attendance-detail">
    <h2>勤怠詳細</h2>

    <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <table class="admin-attendance-detail__table">
            <tr class="admin-attendance-detail__row">
                <th class="admin-attendance-detail__th--name">名前</th>
                <td class="admin-attendance-detail__td--name">{{ $attendance->user->name }}</td>
            </tr>
            <tr class="admin-attendance-detail__row">
                <th class="admin-attendance-detail__th--date">日付</th>
                <td class="admin-attendance-detail__td--year">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</td>
                <td class="admin-attendance-detail__td--date">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</td>
            </tr>
            <tr class="admin-attendance-detail__row">
                <th class="admin-attendance-detail__th--start-end">出勤・退勤</th>
                <td class="admin-attendance-detail__td--start-end">
                    <div class="admin-attendance-detail__start-end__group">
                        <label class="start-end__label">
                            <input type="time" name="start_time"
                                class="admin-attendance-detail__input"
                                value="{{ old('start_time', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')) }}"
                                onclick="this.showPicker && this.showPicker()"
                            >
                        </label>
                        <span class="tilde">〜</span>
                        <label class="start-end__label">
                            <input type="time" name="end_time"
                                class="admin-attendance-detail__input"
                                value="{{ old('end_time', \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')) }}"
                                onclick="this.showPicker && this.showPicker()"
                            >
                        </label>
                    </div>

                    @if ($errors->has('start_time'))
                        <p class="form-error-message__start-time">{{ $errors->first('start_time') }}</p>
                    @endif
                    @if ($errors->has('end_time'))
                        <p class="form-error-message__end-time">{{ $errors->first('end_time') }}</p>
                    @endif
                </td>
            </tr>
            <tr class="admin-attendance-detail__row">
                <th class="admin-attendance-detail__th--break">休憩</th>
                <td class="admin-attendance-detail__td--break">
                    @php
                        $oldBreaks = old('break_times');
                        if ($oldBreaks !== null) {
                            $breaks = $oldBreaks;
                            $breakCount = count($breaks);
                        } else {
                            $breaks = $attendance->breakTimes;
                            $breakCount = (is_countable($breaks) ? count($breaks) : 0) + 1;
                        }
                    @endphp

                    @for ($i = 0; $i < $breakCount; $i++)
                        @php
                            $break = $breaks[$i] ?? null;

                            $startTime = old("break_times.$i.start_time") ??
                                        (is_array($break) ? ($break['start_time'] ?? null) : optional($break)->start_time);

                            $endTime = old("break_times.$i.end_time") ??
                                        (is_array($break) ? ($break['end_time'] ?? null) : optional($break)->end_time);

                            try {
                                $startTime = $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $startTime = '';
                            }

                            try {
                                $endTime = $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '';
                            } catch (\Exception $e) {
                                $endTime = '';
                            }
                        @endphp

                        <div class="admin-attendance-table__break-time__group">
                            <label class="break-label">
                                <input type="time" name="break_times[{{ $i }}][start_time]"
                                    class="admin-attendance-detail__input--break" value="{{ $startTime }}"
                                    onclick="this.showPicker && this.showPicker()"
                                >
                            </label>
                            <span class="tilde">〜</span>
                            <label class="break-label">
                                <input type="time" name="break_times[{{ $i }}][end_time]"
                                    class="admin-attendance-detail__input--break" value="{{ $endTime }}"
                                    onclick="this.showPicker && this.showPicker()"
                                >
                            </label>
                        </div>
                        @if ($errors->has("break_times.$i.start_time"))
                            <p class="form-error-message__break">{{ $errors->first("break_times.$i.start_time") }}</p>
                        @endif
                    @endfor
                </td>
            </tr>
            <tr class="admin-attendance-detail__row--note">
                <th class="admin-attendance-detail__th--note">備考</th>
                <td class="admin-attendance-detail__td--note">
                    <textarea name="note" rows="4" class="admin-attendance-detail__textarea">{{ old('note', $attendance->note) }}</textarea>
                    @if ($errors->has('note'))
                        <p class="form-error-message__note">{{ $errors->first('note') }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <div class="admin-attendance-detail__button">
            <button type="submit" class="admin-attendance-detail__button-submit">修正</button>
        </div>
    </form>
@endsection