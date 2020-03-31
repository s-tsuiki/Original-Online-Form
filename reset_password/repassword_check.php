<?php
require '../tools/database_connect/database_connect.php';

	session_start();
 
	header("Content-type: text/html; charset=utf-8");
 
	//cookieがオフの場合
	if(!isset($_SESSION['token'])){
		echo "cookieを有効にしてください。";
		exit();
	}

	if(empty($_POST)) {
		header("Location: reset_top.php");
		exit();
	}

	//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
	if ($_POST['token'] != $_SESSION['token']){
		//echo "不正アクセスの可能性あり";
		exit();
	}
 
	//クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');
 
	//前後にある半角全角スペースを削除する関数
	function spaceTrim ($str) {
		// 行頭
		$str = preg_replace('/^[ 　]+/u', '', $str);
		// 末尾
		$str = preg_replace('/[ 　]+$/u', '', $str);
		return $str;
	}
 
	//エラーメッセージの初期化
	$errors = array();
 
	if(empty($_POST)) {
		header("Location: reset_top.php");
		exit();
	}else{
		//POSTされたデータを各変数に入れる
		$user = isset($_POST['user']) ? $_POST['user'] : NULL;
		$password = isset($_POST['password']) ? $_POST['password'] : NULL;
		$password2 = isset($_POST['password2']) ? $_POST['password2'] : NULL;
	
		//前後にある半角全角スペースを削除
		$user = spaceTrim($user);
		$password = spaceTrim($password);
		$password2 = spaceTrim($password2);
	
		//パスワード入力判定
		if ($password == '' || $password2 == ''):
			$errors['password'] = "パスワードが入力されていません。";
		elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password"]) || !preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password2"])):
			$errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
		elseif($password !== $password2):
			$errors['password_match'] = "パスワードが一致しません。もう一度やり直してください。";
		else:
			//パスワードを伏せる
			$password_hide = str_repeat('*', strlen($password));
		endif;
	
	}
 
	//エラーが無ければセッションに登録
	if(count($errors) === 0){
		$_SESSION['user'] = $user;
		$_SESSION['password'] = $password;
	}
 
?>
 
<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
  <link rel="stylesheet" type="text/css" href="../layout/repassword_check.css">
  <title>パスワード確認画面</title>
</head>
<body>
<div class="confirm_area">

<h1>Web掲示板</h1>
 
<?php if (count($errors) === 0): ?>
 
<h2>パスワードの再設定の確認</h2>

<?=htmlspecialchars($user, ENT_QUOTES)?>さんのパスワードを再設定しますか？

<form action="password_reregistration_complete.php" method="post" class="form">
 
<input type="button" value="戻る" onClick="history.back()" class ="back">
<input type="hidden" name="token" value="<?=$_POST['token']?>">
<input type="submit" value="はい" class = "registrate">
 
</form>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
	foreach($errors as $value){
		echo "<p><strong>".$value."</strong></p>";
	}
?>
 
<input type="button" value="戻る" onClick="history.back()" class = "back">
 
<?php endif; ?>

</div>
 
</body>
</html>