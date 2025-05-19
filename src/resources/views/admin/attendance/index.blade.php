@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css')}}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css')}}">
@endsection

@section('content')
<div class="attendance-page">
    <h2> {{ $date->format('Y年n月j日') }}の勤怠 </h2>

    <div class="date-nav">
        <a href="{{ route('admin.attendances.index', ['date' => $prevDate]) }}">← 前日</a>
        <span class="current-date">
            📅 {{ $date->format('Y/m/d') }}
        </span>
        <a href="{{ route('admin.attendances.index', ['date' => $nextDate]) }}">翌日 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in ?? '' }}</td>
                    <td>{{ $attendance->clock_out ?? '' }}</td>
                    <td>{{ $attendance->total_break ?? '' }}</td>
                    <td>{{ $attendance->work_time ?? '' }}</td>
                    <td><a href="{{ route('admin.attendances.detail', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection