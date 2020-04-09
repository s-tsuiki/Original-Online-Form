# Original-Online-Form
Line風のシンプルなweb掲示板。  
基本的にはフレームワークやライブラリを使わずPHPで直に書いてますが、メール送信部分はPHPMailerというライブラリを用いています。  
PC/スマホに対応してます。
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.gif" alt="基本的な操作の動画（PC）">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.gif" alt="基本的な操作の動画（スマホ）">
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
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.jpeg" alt="トップページの画像（PC）">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.jpeg" alt="トップページの画像（スマホ）">
</div>

トップページはこのようになってます。  
ログインボタン(ログインページに移動する)と、アカウント作成のリンク(アカウント作成ページに移動する)があります。  
最新のコメントが読めるように、一番下に移動する↓マークのボタンもあります。  
ログインせずに、このページを閲覧することができ、コメントの投稿・削除・編集の場合はログインします。  
コメント一覧はLine風にしてみました。
### 会員登録
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.gif" alt="会員登録の動画">
</div>


### ログイン
<div align="center">
<img src="https://raw.github.com/wiki/s-tsuiki/Original-Online-Form/images/Untitled.gif" alt="ログインの動画">
</div>
