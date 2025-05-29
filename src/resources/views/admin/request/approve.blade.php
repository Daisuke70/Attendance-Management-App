@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/request/approve.css') }}">
@endsection

@section('content')
<div class="correction-detail__container">
    <h2>勤怠詳細</h2>

    <form action="{{ route('admin.correction_requests.storeApproval', $correctionRequest->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <table class="correction-detail__table">
            <tr class="correction-detail__tr">
                <th class="correction-detail__th">名前</th>
                <td class="correction-detail__name">{{ $correctionRequest->user->name }}</td>
            </tr>
            <tr class="correction-detail__tr">
                <th class="correction-detail__th">日付</th>
                <td class="correction-detail__year">{{ \Carbon\Carbon::parse($correctionRequest->attendance->date)->format('Y年') }}</td>
                <td class="correction-detail__date">{{ \Carbon\Carbon::parse($correctionRequest->attendance->date)->format('n月j日') }}</td>
            </tr>
            <tr class="correction-detail__tr">
                <th class="correction-detail__th--start-end">出勤・退勤</th>
                <td class="correction-detail__start-end">
                    <div class="start-end__group">
                        <input type="time" name="new_clock_in"
                            value="{{ old('new_clock_in', $correctionRequest->new_clock_in ? \Carbon\Carbon::parse($correctionRequest->new_clock_in)->format('H:i') : '') }}"
                            class="correction-detail__input"
                            readonly />
                        〜
                        <input type="time" name="new_clock_out"
                            value="{{ old('new_clock_out', $correctionRequest->new_clock_out ? \Carbon\Carbon::parse($correctionRequest->new_clock_out)->format('H:i') : '') }}"
                            class="correction-detail__input"
                            readonly />
                    </div>
                </td>
            </tr>
            <tr class="correction-detail__tr">
                <th class="correction-detail__th">休憩</th>
                <td class="correction-detail__break">
                    @foreach($correctionRequest->correctionBreakTimes as $index => $break)
                        <div class="break-time__group">
                            <input type="time" name="breaks[{{ $index }}][new_start_time]"
                                value="{{ old("breaks.$index.new_start_time", \Carbon\Carbon::parse($break->new_start_time)->format('H:i')) }}"
                                class="correction-detail__input"
                                readonly />
                            〜
                            <input type="time" name="breaks[{{ $index }}][new_end_time]"
                                value="{{ old("breaks.$index.new_end_time", \Carbon\Carbon::parse($break->new_end_time)->format('H:i')) }}"
                                class="correction-detail__input"
                                readonly />
                        </div>
                    @endforeach
                </td>
            </tr>
            <tr class="correction-detail__tr">
                <th class="correction-detail__th">備考</th>
                <td class="correction-detail__note">
                    <textarea name="new_note" rows="3" cols="40"  class="correction-detail__textarea">{{ old('new_note', $correctionRequest->new_note) }}</textarea>
                </td>
            </tr>
        </table>
        
        @if ($isApproved)
            <p class="approve-message">承認済み</p>
        @else
            <div class="button-content">
                <button type="submit" class="approve-button">承認</button>
            </div>
        @endif
    </form>
</div>
@endsection