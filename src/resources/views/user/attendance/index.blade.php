@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/attendance/index.css') }}">
@endsection

@section('content')
<div class="attendance-table">
    <h2>勤怠一覧</h2>

    <div class="attendance-table__month-nav">
        <a href="{{ route('attendances.index', ['date' => $targetDate->copy()->subMonth()->format('Y-m')]) }}"
            class="attendance-table__month-nav--prev">
            <span class="calender-label__back">
                <img src="{{ asset('/images/back-arrow.png') }}" alt="矢印の画像" class="arrow-img">
            </span>
            前月
        </a>
        <img src="{{ asset('/images/calender.png') }}" alt="カレンダーの画像" id="title" class="calender-img">
        <span class="attendance-table__month-nav--current">
            {{ $targetDate->format('Y/m') }}
        </span>
        <a href="{{ route('attendances.index', ['date' => $targetDate->copy()->addMonth()->format('Y-m')]) }}"
            class="attendance-table__month-nav--next">
            翌月
            <span class="calender-label__next">
                <img src="{{ asset('/images/next-arrow.png') }}" alt="矢印の画像" class="arrow-img">
            </span>
        </a>
    </div>

    <table class="attendance-table__table">
        <thead class="attendance-table__head">
            <tr class="attendance-table__head-row">
                <th class="attendance-table__head--date">日付</th>
                <th class="attendance-table__head--in">出勤</th>
                <th class="attendance-table__head--out">退勤</th>
                <th class="attendance-table__head--break">休憩</th>
                <th class="attendance-table__head--total">合計</th>
                <th class="attendance-table__head--detail">詳細</th>
            </tr>
        </thead>
        <tbody class="attendance-table__body">
            @php
                $weekDays = ['Sun' => '日', 'Mon' => '月', 'Tue' => '火', 'Wed' => '水', 'Thu' => '木', 'Fri' => '金', 'Sat' => '土'];
            @endphp
            @foreach($datesInMonth as $date)
                @php
                    $attendance = $attendances->get($date->format('Y-m-d'));
                    $weekday = $weekDays[$date->format('D')] ?? '';
                    $clockIn = $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '';
                    $clockOut = $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '';
                    $breakTime = $attendance && $attendance->total_break_minutes !== null
                        ? floor($attendance->total_break_minutes / 60) . ':' . str_pad($attendance->total_break_minutes % 60, 2, '0', STR_PAD_LEFT)
                        : '';
                    $workTime = $attendance && $attendance->work_minutes !== null
                        ? floor($attendance->work_minutes / 60) . ':' . str_pad($attendance->work_minutes % 60, 2, '0', STR_PAD_LEFT)
                        : '';
                @endphp
                <tr class="attendance-table__body-row">
                    <td class="attendance-table__body--date">{{ $date->format('m/d') }}({{ $weekday }})</td>
                    <td class="attendance-table__body--in">{{ $clockIn }}</td>
                    <td class="attendance-table__body--out">{{ $clockOut }}</td>
                    <td class="attendance-table__body--break">{{ $breakTime }}</td>
                    <td class="attendance-table__body--total">{{ $workTime }}</td>
                    <td class="attendance-table__body--detail">
                        @if($attendance)
                            <a href="{{ route('attendances.detail', $attendance->id) }}" class="detail-link">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection