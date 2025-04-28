<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/user/auth/login.css')}}">
    <title>ログイン画面</title>
</head>
<body>
    <div class="login-form">
        <header class="login-form__header">
            <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title">
        </header>

        <div class="login-form__title">
            <h2>ログイン</h2>
        </div>

        <div class="login-form__inner">
            <form class="login-form__form" action="/login" method="post">
                @csrf
                <div class="login-form__group">
                    <label class="login-form__label" for="email">メールアドレス</label>
                    <input class="login-form__input" type="mail" name="email" id="email" placeholder="メールアドレスを入力" value="{{ old('email') }}" />
                    <p class="login-form__error-message">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="login-form__group">
                    <label class="login-form__label" for="password">パスワード</label>
                    <input class="login-form__input" type="password" name="password" id="password" placeholder="パスワードを入力" />
                    <p class="login-form__error-message">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="login-form__button">
                    <input class="login" type="submit" value="ログインする">
                </div>
            </form>
            <a href="/register" class="register">会員登録はこちら</a>
        </div>
    </div>
</body>
</html>