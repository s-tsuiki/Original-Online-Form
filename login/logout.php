<?php
	session_start();

	header("Content-type: text/html; charset=utf-8");

	//cookieがオフの場合
	if(!isset($_SESSION['token'])){
		echo "cookieを有効にしてください。";
		exit();
	}

	//POST送信されていなかった場合
	if(empty($_POST)){
		header("Location: top.php");
		exit();
	}

	//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
	if ($_POST['token'] != $_SESSION['token']){
		echo "不正なリクエスト";
		exit();
	}

	//クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');

	if (isset($_SESSION['user'])) {
    		$message = "ログアウトしました。";
	} else {
    		$message = "セッションがタイムアウトしました。";
	}
	// セッション変数を全て解除
	$_SESSION = array();

	//セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsesssionidを名前でやる
	if (isset($_COOKIE["PHPSESSID"])) {
    		setcookie("PHPSESSID", '', time() - 1800, '/');
	}

	// セッションクリア
	session_destroy();
?>

<!doctype html>
<html lang = "ja">
    <head>
	<meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  	<meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
	<link rel="stylesheet" type="text/css" href="../layout/logout.css">
        <title>ログアウト画面</title>
    </head>
    <body>
	<div class="logout_area">

	<h1>Web掲示板</h1>
        <h2>ログアウト</h2>
	<br>
        <p><?=$message?></p>
	<br>
        <p><a href="../mission_6-2.php">トップページに戻る</a></p>

	</div>
    </body>
</html>