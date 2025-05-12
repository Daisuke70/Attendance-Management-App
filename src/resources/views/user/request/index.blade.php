@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/request/index.css') }}">
@endsection

@section('content')
<div class="request-table">
    <h2>申請一覧</h2>
    <div class="request-table__tabs">
        <a href="{{ route('correction-requests.index', ['status' => 'pending']) }}"
            class="request-table__tabs-pending {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('correction-requests.index', ['status' => 'approved']) }}"
            class="request-table__tabs-approved {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <table class="request-table__table">
        <thead class="request-table__head">
            <tr class="request-table__head-row">
                <th class="request-table__head--condition">状態</th>
                <th class="request-table__head-th">名前</th>
                <th class="request-table__head-th">対象日時</th>
                <th class="request-table__head-th">申請理由</th>
                <th class="request-table__head-th">申請日時</th>
                <th class="request-table__head-th">詳細</th>
            </tr>
        </thead>
        <tbody class="request-table__body">
            @foreach ($requests as $request)
                <tr class="request-table__body-row">
                    <td class="request-table__body--condition">{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td class="request-table__body-td">{{ $request->user->name }}</td>
                    <td class="request-table__body-td">{{ $request->attendance->date ?? '-' }}</td>
                    <td class="request-table__body-td">{{ $request->new_note }}</td>
                    <td class="request-table__body-td">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                    <td class="request-table__body--detail">
                        <a href="{{ route('attendances.detail', $request->attendance->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection