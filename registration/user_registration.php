<?php
require '../tools/database_connect/database_connect.php';
	session_start();
 
	header("Content-type: text/html; charset=utf-8");
 
	//クロスサイトリクエストフォージェリ（CSRF）対策
	$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
	$token = $_SESSION['token'];
 
	//クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');
 
	//データベース接続
	$pdo = db_connect();

	//データベースの作成
	$sql = "CREATE TABLE IF NOT EXISTS member"
	." ("
	."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	."user VARCHAR(50) NOT NULL,"
	."mail VARCHAR(50) NOT NULL,"
	."password VARCHAR(128) NOT NULL,"
	."flag TINYINT(1) NOT NULL DEFAULT 1"
 	.")ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
	
	$stmt = $pdo->query($sql);
 
	//エラーメッセージの初期化
	$errors = array();
 
	if(empty($_GET)) {
		header("Location: mail_registration.php");
		exit();
	}else{
		//GETデータを変数に入れる
		$urltoken = isset($_GET['urltoken']) ? $_GET['urltoken'] : NULL;
		//メール入力判定
		if ($urltoken == ''){
			$errors['urltoken'] = "もう一度登録をやりなおして下さい。";
		}else{
			
			//flagが0の未登録者・仮登録日から10分以内の場合
			$stmt = $pdo->prepare("SELECT mail FROM pre_member WHERE urltoken=(:urltoken) AND flag =0 AND date > now() - interval 10 minute");
			$stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$stmt->execute();
			
			//10分以内に仮登録され、本登録されていないトークンの場合
			if( $stmt->rowCount() == 1){
				$mail_array = $stmt->fetch();
				$mail = $mail_array['mail'];
				$_SESSION['mail'] = $mail;
			}else{
				$errors['urltoken_timeover1'] = "このURLはご利用できません。";
				$errors['urltoken_timeover2'] = "有効期限が過ぎた等の問題があります。";
				$errors['urltoken_timeover3'] = "もう一度登録をやりなおして下さい。";
			}
			
			//データベース接続切断
			$pdo = null;
		}
	}
 
?>
 
<!DOCTYPE html>
<html lang = "ja">
<head>
 <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
 <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
 <link rel="stylesheet" type="text/css" href="../layout/user_registration.css">
 <title>ユーザー登録画面</title>
</head>

<body>
<div class = "registration_area">

<h1>Web掲示板</h1>
 
<?php if (count($errors) === 0): ?>

<h2>ユーザー登録</h2>
 
<form action="user_check.php" method="post" class = "form">
 
<p>メールアドレス：</p>
<p class = "mail"><?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p><!-- text/plainをtext/htmlへ変換 -->
<p><lavel for="user">ユーザー名：</lavel></p>
<p><input type="text" name="user"></p>
<p><lavel for="password">パスワード：</lavel></p>
<p><input type="password" name="password"></p>
<p><lavel for="password2">パスワード(確認用)：</lavel></p>
<p><input type="password" name="password2"></p>
 
<input type="hidden" name="token" value="<?=$token?>">
<input type="submit" value="確認する" class = "confirm">
</form>

 
<h2>注意事項</h2>
<ul>
<li>ユーザー名は、<strong>10文字以内</strong>で入力して下さい。</li>
<li>パスワードは、<strong>半角英数字の5文字以上30文字以下</strong>で入力して下さい。</li>
</ul>

</form>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p><strong>".$value."</strong></p>";
}
?>
 
<?php endif; ?>

</div>
 
</body>
</html>