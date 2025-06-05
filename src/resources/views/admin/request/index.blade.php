@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/request/index.css') }}">
@endsection

@section('content')
<div class="admin-request-table">
    <h2>申請一覧</h2>
    <div class="admin-request-table__tabs">
        <a href="{{ route('admin.correction_requests.index', ['status' => 'pending']) }}"
            class="admin-request-table__tabs-pending {{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.correction_requests.index', ['status' => 'approved']) }}"
            class="admin-request-table__tabs-approved {{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="admin-request-table__tabs-border"></div>

    <table class="admin-request-table__table">
        <thead class="admin-request-table__head">
            <tr class="admin-request-table__head-row">
                <th class="admin-request-table__head--condition">状態</th>
                <th class="admin-request-table__head-th">名前</th>
                <th class="admin-request-table__head--date">対象日時</th>
                <th class="admin-request-table__head-th">申請理由</th>
                <th class="admin-request-table__head-th">申請日時</th>
                <th class="admin-request-table__head-th">詳細</th>
            </tr>
        </thead>
        <tbody class="admin-request-table__body">
            @foreach ($requests as $request)
                <tr class="admin-request-table__body-row">
                    <td class="admin-request-table__condition">{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td class="admin-request-table__user-name">{{ $request->user->name }}</td>
                    <td class="admin-request-table__attendance-date">
                        {{ optional($request->attendance)->date ? \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') : '-' }}
                    </td>
                    <td class="admin-request-table__note">{{ $request->new_note }}</td>
                    <td class="admin-request-table__request-date">{{ $request->created_at->format('Y/m/d') }}</td>
                    <td class="admin-request-table__detail">
                        <a href="{{ route('admin.correction_requests.showApproval', ['attendance_correct_request' => $request->id]) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection