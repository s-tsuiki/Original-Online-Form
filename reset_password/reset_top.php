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
 <link rel="stylesheet" type="text/css" href="../layout/reset_top.css">
 <title>ユーザー入力画面</title>
</head>
<body>
<div class = "registration_area">

<h1>Web掲示板</h1>
<h2>パスワードの再設定</h2>
 
<p>あなたのユーザー名とメールアドレスを記入してください。</p>
<p>登録してあるメールアドレスに再設定用のURLを送ります。</p>
<br>
<form action="reset_user_check.php" method="post" class = "form">
 
<p><label for="user">あなたのユーザー名：</lavel></p>
<p><input type="text" name="user"></p>
<p><label for="mail">あなたのメールアドレス：</lavel></p>
<p><input type="email" name="mail" placeholder="welcome@example.com"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="確認" class = "confirm">
 
</form>
 
</div>

</body>
</html>