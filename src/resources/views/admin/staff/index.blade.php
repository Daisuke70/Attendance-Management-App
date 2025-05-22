@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/common.css')}}">
<link rel="stylesheet" href="{{ asset('css/admin/staff/index.css')}}">
@endsection

@section('content')
<div class="staff-table">
    <h2>スタッフ一覧</h2>

    <table class="staff-table__table">
        <thead class="staff-table__head">
            <tr class="staff-table__head-row">
                <th class="staff-table__head--name">名前</th>
                <th class="staff-table__head--email">メールアドレス</th>
                <th class="staff-table__head--attendance">月次勤怠</th>
            </tr>
        </thead>

        <tbody class="staff-table__body">
            @foreach ($users as $user)
                <tr class="staff-table__body-row">
                    <td class="staff-table__body--name">{{ $user->name }}</td>
                    <td class="staff-table__body--email">{{ $user->email }}</td>
                    <td class="staff-table__body--detail">
                        <a href="{{ route('admin.staff.attendances.index', $user->id) }}" class="staff-table__detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection