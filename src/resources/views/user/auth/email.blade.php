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

            @if (session('success'))
                <div class="email-verify__alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="email-verify__alert-error" id="error-message">
                    <span id="error-message-text">
                    <span id="countdown"></span>
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('email_for_verification') }}">
                <button type="submit" id="resend-button-1" class="email-verify__button">認証はこちらから</button>
            </form>
            <br>
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('email_for_verification') }}">
                <button type="submit" id="resend-button-2" class="email-verify__resend-button">認証メールを再送する</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.textContent = '送信中...';
                    }
                });
            });

            const retryAfter = Number({{ session('retry_after', 0) }});
            const countdownElement = document.getElementById('countdown');

            if (countdownElement && retryAfter > 0) {
                let secondsLeft = retryAfter;

                const timer = setInterval(function () {
                    if (secondsLeft <= 0) {
                        clearInterval(timer);
                        countdownElement.textContent = '再送が可能になりました。';

                        document.querySelectorAll('button[type="submit"]').forEach(btn => {
                            btn.disabled = false;
                            btn.textContent = '再送する';
                        });
                    } else {
                        countdownElement.textContent = `認証メールの再送回数が上限に達しました。${secondsLeft}秒後にもう一度お試しください。`;
                        secondsLeft--;
                    }
                }, 1000);
            }

            const alertSuccess = document.querySelector('.email-verify__alert-success');
            if (alertSuccess && retryAfter === 0) {
                setTimeout(() => {
                    alertSuccess.style.transition = "opacity 0.5s ease";
                    alertSuccess.style.opacity = "0";
                    setTimeout(() => alertSuccess.remove(), 500);
                }, 4000);
            }
        });
    </script>
</body>
</html>