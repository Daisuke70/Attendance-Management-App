@extends('user.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/user/request/index.css') }}">
@endsection

@section('content')
    <div class="tabs">
        <a href="{{ route('correction-requests.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('correction-requests.index', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>承認状態</th>
                <th>ユーザー名</th>
                <th>対象日</th>
                <th>申請理由</th>
                <th>申請日時</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->attendance->date ?? '-' }}</td>
                    <td>{{ $request->new_note }}</td>
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
@endsection