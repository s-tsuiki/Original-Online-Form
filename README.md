# Original-Online-Form
Line風のシンプルなweb掲示板。  
基本的にはフレームワークやライブラリを使わずPHPで直に書いてますが、メール送信部分はPHPMailerを用いています。  
PC/スマホに対応してます。(レスポンシブデザイン)
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/logo.jpg" alt="ロゴ">
</div>

## 概要
メール認証機能付きのLine風のシンプルなWeb掲示板です。  
ユーザーはまず会員登録をします。その際に、メール認証を用います。  
会員登録完了後、ユーザーはログインをすることで、コメントの投稿、自分のコメントの削除・編集ができます。  
ログインをしなくてもコメントを閲覧することは可能です。  
XSS、CSRF対策など、基本的なセキュリティ対策は実装済み。

## 内容
### トップページ
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/toppage_PC.jpg" alt="トップページの画像（PC）" width="600">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/toppage_phone.jpg" alt="トップページの画像（スマホ）" width="200">
</div>

左がPCの画面で、右がスマホの画面です。  
ログインボタン(ログインページに移動する)と、アカウント作成のリンク(アカウント作成ページに移動する)があります。  
最新のコメントが読めるように、一番下に移動する↓マークのボタンもあります。  
ログインせずに、このページを閲覧することができ、コメントの投稿・削除・編集の場合はログインします。  
コメント一覧はLine風にしてみました。
### 会員登録
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/registration.gif" alt="会員登録の動画(PC)" width="600">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/registration_phone.gif" alt="会員登録の動画(スマホ)" width="200">
</div>

左がPCの画面で、右がスマホの画面です。  
会員登録はなりすましと不正なアカウントの大量作成を防ぐため、メール認証をつけています。  
会員登録の流れは、  
「トップページのアカウント作成を押す」→「会員登録に用いるメールアドレスを登録する」→「送信されたメールからURLを開く（期限付き）」→「ユーザー名とパスワードを設定」→「確認画面で登録するボタンを押す」→「登録完了」  です。

### ログイン
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/normal.gif" alt="ログインの動画(PC)" width="600">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/normal_phone.gif" alt="ログインの動画(スマホ)" width="200">
</div>

左がPCの画面で、右がスマホの画面です。  
ログインをする場合は、まずトップページからログインボタンを押し、ログイン画面に進みます。  
ここで、「ユーザー名」と「パスワード」を入力し、正しい場合、マイページに進みます。  
マイページでは、各ユーザーは新規コメント投稿と、自分のコメントの削除・編集できます。  
ログアウトボタンを押すと、ログアウトし、トップページのリンクが出ます。  

パスワードの再設定機能もついています。  
「パスワードを忘れた場合はこちら」を押し、ユーザー名とメールアドレスを入力することで、登録してあるメールアドレスに再設定用のURLを送ります。

## 構成
ウェブページの本体を各PHPファイル内のHTMLで記述し、デザインを「layout」フォルダ内のCSSで指定しています。  
サイトのサーバー内の動きを各PHPファイル内のphpの記述部分で指定しています。  
仮メンバーのリスト、メンバーのリスト、コメントなどはすべてサーバー内のデータベース上で管理しています。

## 開発言語
フロントエンド・・・HTML, CSS(, Javascript)  
バックエンド・・・PHP  
データベース・・・MySQL

## 開発環境
エディタ・・・TeraPad  
ブラウザ・・・Chrome  
サーバー環境・・・Linux, Apache, PHP
