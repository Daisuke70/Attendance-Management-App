@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/request/approve.css') }}">
@endsection

@section('content')
<div class="correction-detail-container">
    <h2>勤怠詳細</h2>

    <form action="{{ route('admin.correction_requests.storeApproval', $correctionRequest->id) }}" method="POST">
        @csrf
        @method('PATCH')

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
                    <input type="time" name="new_clock_in"
                        value="{{ old('new_clock_in', $correctionRequest->new_clock_in ? \Carbon\Carbon::parse($correctionRequest->new_clock_in)->format('H:i') : '') }}">
                    〜
                    <input type="time" name="new_clock_out"
                        value="{{ old('new_clock_out', $correctionRequest->new_clock_out ? \Carbon\Carbon::parse($correctionRequest->new_clock_out)->format('H:i') : '') }}">
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    @forelse($correctionRequest->correctionBreakTimes as $index => $break)
                        <div class="break-time-group">
                            <input type="time" name="breaks[{{ $index }}][new_start_time]" value="{{ old("breaks.$index.new_start_time", \Carbon\Carbon::parse($break->new_start_time)->format('H:i')) }}">
                            〜
                            <input type="time" name="breaks[{{ $index }}][new_end_time]" value="{{ old("breaks.$index.new_end_time", \Carbon\Carbon::parse($break->new_end_time)->format('H:i')) }}">
                        </div>
                    @empty
                        なし
                    @endforelse
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="new_note" rows="3" cols="40">{{ old('new_note', $correctionRequest->new_note) }}</textarea>
                </td>
            </tr>
        </table>

        <div class="form-buttons">
            <button type="submit" class="btn btn-approve">承認</button>
        </div>
    </form>
</div>
@endsection