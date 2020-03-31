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
 <link rel="stylesheet" type="text/css" href="../layout/mail_registration.css">
 <title>メール登録画面</title>
</head>
<body>
<div class = "registration_area">

<h1>Web掲示板</h1>
<h2>メール登録</h2>
 
<p>アカウントの作成には、<strong>メールアドレスの登録が必要です</strong>。</p>
<p>アカウント登録に用いるメールアドレスを入力してください。</p>
<br>
<form action="mail_check.php" method="post" class = "form">
 
<p><label for="mail">メールアドレス：</lavel></p>
<p><input type="email" name="mail" placeholder="welcome@example.com"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="登録する" class = "registrate">
 
</form>
 
</div>

</body>
</html>