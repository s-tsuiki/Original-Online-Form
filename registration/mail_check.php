<?php
require '../phpmailer/send_test_mail.php';
require '../tools/database_connect/database_connect.php';

	session_start();
	
	header("Content-type: text/html; charset=utf-8");
 	
	//cookieがオフの場合
	if(!isset($_SESSION['token'])){
		echo "cookieを有効にしてください。";
		exit();
	}

	if(empty($_POST)) {
		header("Location: mail_registration.php");
		exit();
	}

 	//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
 	if ($_POST['token'] != $_SESSION['token']){
		echo "不正なリクエスト";
		exit();
 	}
 	
	//クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');
 	
	//データベースへの接続
	$pdo = db_connect();

	//データベースの作成
	$sql = "CREATE TABLE IF NOT EXISTS pre_member"
	." ("
	."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	."urltoken VARCHAR(128) NOT NULL,"
	."mail VARCHAR(50) NOT NULL,"
	."date DATETIME NOT NULL,"
	."flag TINYINT(1) NOT NULL DEFAULT 0"
 	.")ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
	
	$stmt = $pdo->query($sql);
 
	//エラーメッセージの初期化
	$errors = array();
 	
	if(empty($_POST)) {
		header("Location: mail_registration.php");
		exit();
	}else{
		//POST送信された場合

		//POSTされたデータを変数に入れる
		$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
		
		//メール入力判定
		if ($mail == ''){
			$errors['mail'] = "メールが入力されていません。";
		}else{
			if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
				$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
			}else{
			
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
				
				//本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする
				$stmt = $pdo->prepare("SELECT mail FROM member WHERE mail=(:mail) AND flag =1");
				$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
				$stmt->execute();

				if( $stmt->rowCount() == 1){
					$errors['member_check'] = "このメールアドレスはすでに利用されています。";
				}
			}
		}
	}
 
	if (count($errors) === 0){
	
		$urltoken = hash('sha256',uniqid(rand(),1));
		//*****.comの部分は、このページが置いてあるURLと同じ
		$url = "https://*****.com/registration/user_registration.php"."?urltoken=".$urltoken;
		
		//ここでデータベースに登録する
		try{
			$sql = $pdo->prepare("INSERT INTO pre_member (urltoken, mail, date) VALUES (:urltoken, :mail, now() )");
		
			$sql->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$sql->bindValue(':mail', $mail, PDO::PARAM_STR);
			$sql->execute();
		
			//データベース接続切断
			$pdo = null;
		
		}catch (PDOException $e){
			echo 'Error:'.$e->getMessage();
			die();
		}

		//メール送信
		$message = send_test_mail($mail, $url);
		
		if ($message == '送信完了！') {
	
	 		//セッション変数を全て解除
			$_SESSION = array();
	
			//クッキーの削除
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}
	
 			//セッションを破棄する
 			session_destroy();
 	
 			$message = "メールをお送りしました。10分以内にメールに記載されたURLからご登録下さい。";
 	
	 	} else {
			$errors['mail_error'] = $message;
		}	
	}
 
?>
 
<!DOCTYPE html>
<html lang = "ja">
<head>
 <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
 <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
 <title>メール確認画面</title>
</head>
<body>
<h1>メール確認画面</h1>
 
<?php if (count($errors) === 0): ?>
 
<p><?=$message?></p>
 
<?php elseif(count($errors) > 0): ?>
 
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>
 
<input type="button" value="戻る" onClick="history.back()">
 
<?php endif; ?>
 
</body>
</html>