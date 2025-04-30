<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/user/auth/register.css')}}">
    <title>会員登録画面</title>
</head>
<body>
    <header class="register-form__header">
        <img src="{{ asset('/images/logo.svg') }}" alt="ロゴの画像" id="title">
    </header>
    <div class="register-form">
        <div class="register-form__title">
            <h2>会員登録</h2>
        </div>

        <div class="register-form__inner">
            <form class="register-form__form" action="/register" method="post">
                @csrf
                <div class="register-form__group">
                    <label class="register-form__label" for="name">名前</label>
                    <input class="register-form__input" type="text" name="name" id="name" value="{{ old('name') }}" />
                    <p class="register-form__error-message">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="register-form__group">
                    <label class="register-form__label" for="email">メールアドレス</label>
                    <input class="register-form__input" type="email" name="email" id="email" value="{{ old('email') }}" />
                    <p class="register-form__error-message">
                        @error('email')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="register-form__group">
                    <label class="register-form__label" for="password">パスワード</label>
                    <input class="register-form__input" type="password" name="password" id="password"/>
                    <p class="register-form__error-message">
                        @error('password')
                        {{ $message }}
                        @enderror
                    </p>
                </div>
                <div class="register-form__group">
                    <label class="register-form__label" for="password_confirmation">パスワード確認</label>
                    <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation" />
                </div>
                <div class="register-form__button">
                    <input class="register-form__submit" type="submit" value="登録する">
                </div>
            </form>
            <a href="/login" class="login">ログインはこちら</a>
        </div>
    </div>
</body>
</html>