<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/user/auth/email.css')}}">
    <title>メール認証誘導画面</title>
</head>
<body>
    <div class="email-verify">
        <header class="email-verify__header">
            <img src="{{ asset('/images/logo.svg') }}" class="email-verify__header-img" alt="ロゴの画像" id="title">
        </header>

        <div class="email-verify__content">
            <h2>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</h2>

            @if(Auth::check())
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="email-verify__button">認証はこちらから</button>
                </form>
            @else
                <p></p>
            @endif
            <br>
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="email-verify__resend-button">認証メールを再送する</button>
            </form>
        </div>
    </div>
</body>
</html>