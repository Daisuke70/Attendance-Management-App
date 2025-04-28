<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/user/common.css')}}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">

        <div class="header-content">
            <div class="header-logo">
                <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title" class="logo-img">
            </div>
            <div class="search-form">
                <form class="search-form" action="/" method="get">
                    @csrf
                    <input type="text" name="keyword" class="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                    <input type="hidden" name="tab" value="{{ request('tab', 'recommended') }}">
                </form>
            </div>
            <div class="logout-login">
                @if (Auth::check())
                    <form class="logout-form" action="/logout" method="post">
                        @csrf
                        <button type="submit" class="logout-button">
                            <span class="logout">ログアウト</span>
                        </button>
                    </form>
                @else
                    <div class="login">
                        <a href="/login" class="login-link">ログイン</a>
                    </div>
                @endif
            </div>
            <div class="link-content">
                <a href="/mypage" class="mypage-link">マイページ</a>
                <a href="/sell" class="sell-link">出品</a>
            </div>
        </div>
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>

</html>