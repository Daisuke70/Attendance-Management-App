@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance/detail.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>勤怠詳細</h2>

    <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="attendance-detail__table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="start_time" value="{{ old('start_time', $attendance->clock_in) }}">
                    〜
                    <input type="time" name="end_time" value="{{ old('end_time', $attendance->clock_out) }}">
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    @php
                        $oldBreaks = old('break_times', []);
                        $breaks = !empty($oldBreaks) ? $oldBreaks : $attendance->breakTimes;
                    @endphp

                    @foreach ($breaks as $i => $break)
                        <div class="break-time-row">
                            <input type="time" name="break_times[{{ $i }}][start_time]"
                                   value="{{ old("break_times.$i.start_time", $break['start_time'] ?? $break->start_time) }}">
                            〜
                            <input type="time" name="break_times[{{ $i }}][end_time]"
                                   value="{{ old("break_times.$i.end_time", $break['end_time'] ?? $break->end_time) }}">
                        </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" rows="3" style="width:100%">{{ old('note', $attendance->note) }}</textarea>
                </td>
            </tr>
        </table>

        <div class="attendance-detail__actions" style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">修正</button>
        </div>
    </f
@endsection