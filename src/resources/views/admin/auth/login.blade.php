<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin/auth/login.css')}}">
    <title>ログイン画面</title>
</head>
<body>
    <div class="admin-login-form">
        <header class="admin-login-form__header">
            <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title">
        </header>

        <div class="admin-login-form__title">
            <h2>管理者ログイン</h2>
        </div>

        <div class="admin-login__form--inner">
            <form action="{{ route('admin.login') }}" method="POST" class="admin-login-form__form">
                @csrf
                <div class="admin-login-form__group">
                    <label for="email" class="admin-login-form__label">メールアドレス</label>
                    <input type="email" name="email" id="email" class="admin-login-form__input" value="{{ old('email') }}">
                    <p class="admin-login-form__error-message">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="admin-login-form__group">
                    <label for="password" class="admin-login-form__label">パスワード</label>
                    <input type="password" name="password" id="password" class="admin-login-form__input">
                    <p class="admin-login-form__error-message">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="admin-login-form__button">
                    <input type="submit" class="admin-login-form__submit" value="管理者ログインする">
                </div>

                @if ($errors->has('role'))
                    <div class="admin-login-form__alert">
                        {{ $errors->first('role') }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>