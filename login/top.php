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
 <link rel="stylesheet" type="text/css" href="../layout/top.css">
 <title>ログイン画面</title>
</head>
<body>
<div class = "login_area">

<h1>Web掲示板</h1>
<h2>ログイン</h2>

<form action="check.php" method="post">
 
<p><lavel for="user">ユーザー名:</lavel></p>
<p><input type="text" name="user"></p>
<p><lavel for="password">パスワード:</lavel></p>
<p><input type="password" name="password"></p>
 
<input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
<input type="submit" value="ログイン" class = "login">
 
</form>

</div>
</body>
</html>