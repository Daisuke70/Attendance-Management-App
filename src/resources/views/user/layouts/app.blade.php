<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理アプリ')</title>
    <link rel="stylesheet" href="{{ asset('css/user/common.css')}}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">

        <div class="header-content">
            <div class="header-content__logo">
                <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title" class="logo-img">
            </div>x
            <div class="header-content__link">
                <a href="{{ route('attendances.create')}}" class="header-link">勤怠</a>
                <a href="{{ route('attendances.index')}}" class="header-link">勤怠一覧</a>
                <a href="{{ route('correction-requests.index')}}" class="header-link">申請</a>
                <form action="{{ route('logout') }}" class="header-content__logout-form" method="post">
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