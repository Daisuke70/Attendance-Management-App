@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css')}}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css')}}">
@endsection

@section('content')
<div class="admin-attendance-table">
    <h2> {{ $date->format('Y年n月j日') }}の勤怠 </h2>

    <div class="admin-attendance-table__nav">
        <a href="{{ route('admin.attendances.index', ['date' => $prevDate]) }}"
            class="admin-attendance-table__nav--prev">
            <span class="calender-label__back">
                <img src="{{ asset('/images/back-arrow.png') }}" alt="矢印の画像" class="arrow-img">
            </span>
            前日
        </a>
        <img src="{{ asset('/images/calender.png') }}" alt="カレンダーの画像" id="title" class="calender-img">
        <span class="admin-attendance-table__nav--current">
            {{ $date->format('Y/m/d') }}
        </span>
        <a href="{{ route('admin.attendances.index', ['date' => $nextDate]) }}"
            class="admin-attendance-table__nav--next">
            翌日
            <span class="calender-label__next">
                <img src="{{ asset('/images/next-arrow.png') }}" alt="矢印の画像" class="arrow-img">
            </span>
        </a>
    </div>

    <table class="admin-attendance-table__table">
        <thead class="admin-attendance-table__head">
            <tr class="admin-attendance-table__head-row">
                <th class="admin-attendance-table__head--name">名前</th>
                <th class="admin-attendance-table__head--in">出勤</th>
                <th class="admin-attendance-table__head--out">退勤</th>
                <th class="admin-attendance-table__head--break">休憩</th>
                <th class="admin-attendance-table__head--total">合計</th>
                <th class="admin-attendance-table__head--detail">詳細</th>
            </tr>
        </thead>

        <tbody class="admin-attendance-table__body">
            @foreach ($attendances as $attendance)
                @php
                    $breakTime = $attendance && $attendance->total_break_minutes !== null
                        ? floor($attendance->total_break_minutes / 60) . ':' . str_pad($attendance->total_break_minutes % 60, 2, '0', STR_PAD_LEFT)
                        : '';
                    $workTime = $attendance && $attendance->work_minutes !== null
                        ? floor($attendance->work_minutes / 60) . ':' . str_pad($attendance->work_minutes % 60, 2, '0', STR_PAD_LEFT)
                        : '';
                @endphp
                <tr class="admin-attendance-table__body-row">
                    <td class="admin-attendance-table__body--name">{{ $attendance->user->name }}</td>
                    <td class="admin-attendance-table__body--in">
                        {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}
                    </td>
                    <td class="admin-attendance-table__body--out">
                        {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}
                    </td>
                    <td class="admin-attendance-table__body--break">{{ $breakTime }}</td>
                    <td class="admin-attendance-table__body--total">{{ $workTime }}</td>
                    <td class="admin-attendance-table__body--detail">
                        <a href="{{ route('admin.attendances.detail', $attendance->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection