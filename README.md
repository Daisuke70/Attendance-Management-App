# 模擬案件　勤怠管理アプリ  
  
## 環境構築
Dockerビルド  
1.git clone git@github.com:Daisuke70/Attendance-Management-App.git  
2.docker-compose up -d --build  

Laravel環境構築  
1.docker-compose exec php bash  
2.composer install  
3. .envファイルを.env.exampleからコピー  
4..envに以下の環境変数を追加  
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  
  
MAIL_MAILER=smtp  
MAIL_HOST=mailhog  
MAIL_PORT=1025  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
  
5.アプリケーションキーの作成  
php artisan key:generate  
6.マイグレーションの実行  
php artisan migrate  
7.シーディングの実行  
php artisan db:seed  
8.テストユーザーのメールアドレス、パスワード  
1．一般ユーザー  
・ユーザー1  
名前：山田太郎  
メールアドレス：test@user.com  
パスワード：11111111  
・ユーザー2  
名前：西怜奈  
メールアドレス：test@user2.com  
パスワード：22222222  
・ユーザー3  
名前：増田一世  
メールアドレス：test@user3.com  
パスワード：33333333  
・ユーザー4  
名前：山本敬吉  
メールアドレス：test@user4.com  
パスワード：44444444  
・ユーザー5  
名前：山本敬吉  
メールアドレス：test@user5.com  
パスワード：55555555  
2．管理者ユーザー  
名前：管理太郎  
メールアドレス：test@admin.com  
パスワード：12345678  

## 使用技術(実行環境)  
・PHP 7.4.9  
・Laravel 8.83.8  
・MySQL 15.1  
・Docker / Docker Compose  
・MailHog（開発用メール確認ツール）  

## ER図  
<img width="577" alt="勤怠管理アプリ　ER図" src="https://github.com/user-attachments/assets/c70e1a40-3f9a-45f2-af8b-8eeecb5a2f87" />

## URL
・開発環境：http://localhost/  
・phpMyAdmin：http://localhost:8080/  
・Mailhog：http://localhost:8025/  

## メール送信テスト（Mailhog）  
MailHog は、アプリから送信されるメールを実際に外部に送信することなく、ブラウザ上で確認できるツールです。  
アクセス：http://localhost:8025/  
  
利用手順：  
1.MailHogを起動  
2.アプリ内で会員登録を実行  
3.上記URLにアクセスし、送信されたメールを確認  
4.メール内の「Verify Email Address」をクリックすることで、メール認証が実行され、勤怠登録画面へ遷移される  
  
## テスト（PHPUnit）  
本アプリケーションでは、PHPUnit によるテストを導入しています。  
  
テストの事前準備  
.env.testing ファイルを作成し、以下を設定（デフォルトはSQLiteインメモリDBを使用）  
DB_CONNECTION=sqlite  
DB_DATABASE=:memory:  
  
・CSRFトークン検証を無効化しています（テスト中のリクエスト簡略化のため）  
  
テストの実施方法：  
1.コンテナに入る  
docker-compose exec php bash  
2.テスト実行  
php artisan test

