<?php
 session_start();
 
 header("Content-type: text/html; charset=utf-8");
 
 //クロスサイトリクエストフォージェリ（CSRF）対策
 $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
 $token = $_SESSION['token'];
 
 //クリックジャッキング対策
 header('X-FRAME-OPTIONS: SAMEORIGIN');
 
?>
 
<!DOCTYPE html>
<html lang = "ja">
<head>
 <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
 <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
 <title>メール登録画面</title>
</head>
<body>
<h1>メール登録画面</h1>
 
アカウントの作成には、メールアドレスの登録が必要です。<br>
アカウント登録に用いるメールアドレスを入力してください。<br>
<br>
<form action="mail_check.php" method="post">
 
メールアドレス：<input type="email" name="mail" placeholder="welcome@example.com">
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="登録する">
 
</form>
 
</body>
</html>