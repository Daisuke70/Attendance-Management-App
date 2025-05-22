<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理アプリ（管理者）')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/common.css')}}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">

        <div class="header-content">
            <div class="header-content__logo">
                <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title" class="logo-img">
            </div>
            <div class="header-content__link">
                <a href="{{ route('admin.attendances.index')}}" class="header-link">勤怠一覧</a>
                <a href="{{ route('admin.staff.index')}}" class="header-link">スタッフ一覧</a>
                <a href="{{ route('admin.correction_requests.index')}}" class="header-link">申請一覧</a>
                <form action="{{ route('admin.logout') }}" class="header-content__logout-form" method="post">
                    @csrf
                    <button type="submit" class="logout-button">
                        <span class="logout">ログアウト</span>
                    </button>
                </form>
            </div>
        </div>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>