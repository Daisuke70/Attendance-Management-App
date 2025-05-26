@extends('admin.layouts.app')

@section('title', '勤怠修正申請詳細')

@section('content')
<div class="correction-detail-container">
    <h2>勤怠修正申請詳細</h2>

    <table class="correction-detail-table">
        <tr>
            <th>名前</th>
            <td>{{ $correctionRequest->user->name }}</td>
        </tr>
        <tr>
            <th>日付</th>
            <td>{{ \Carbon\Carbon::parse($correctionRequest->attendance->date)->format('Y年n月j日') }}</td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td>
                {{ $correctionRequest->new_clock_in ? \Carbon\Carbon::parse($correctionRequest->new_clock_in)->format('H:i') : '-' }}
                〜
                {{ $correctionRequest->new_clock_out ? \Carbon\Carbon::parse($correctionRequest->new_clock_out)->format('H:i') : '-' }}
            </td>
        </tr>
        <tr>
            <th>休憩</th>
            <td>
                @forelse($correctionRequest->correctionBreakTimes as $break)
                    <div>
                        {{ \Carbon\Carbon::parse($break->new_start_time)->format('H:i') }}
                        〜
                        {{ \Carbon\Carbon::parse($break->new_end_time)->format('H:i') }}
                    </div>
                @empty
                    なし
                @endforelse
            </td>
        </tr>
        <tr>
            <th>備考</th>
            <td>{{ $correctionRequest->new_note ?? 'なし' }}</td>
        </tr>
    </table>
</div>
@endsection