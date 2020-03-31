<?php
require '../phpmailer/send_repass_mail.php';
require '../tools/database_connect/database_connect.php';
require '../tools/make_url/make_url.php';

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
 
	//エラーメッセージの初期化
	$errors = array();
 
	if(empty($_POST)) {
		header("Location: reset_top.php");
		exit();
	}else{
		//POSTされたデータを各変数に入れる
		$user = isset($_POST['user']) ? $_POST['user'] : NULL;
		$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
 
		//ユーザー名入力判定
		if ($user == ''){
			$errors['user'] = "ユーザー名が入力されていません。";
		}
		//メールアドレス入力判定
		if ($mail == ''){
			$errors['mail'] = "メールアドレスが入力されていません。";
		}
		
		//ユーザー名がすでに登録されているか確認
		if(count($errors) == 0){
			//データベースへの接続
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

			//本登録用のmemberテーブルにすでに登録されているuserかどうかをチェックする
			$stmt = $pdo->prepare("SELECT mail FROM member WHERE user=(:user) AND flag =1 AND mail=(:mail)");
			$stmt->bindValue(':user', $user, PDO::PARAM_STR);
			$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
			$stmt->execute();

			if( $stmt->rowCount() == 1){
				$urltoken = hash('sha256',uniqid(rand(),1));
				$url = make_url()."/reset_password/password_reregistration.php"."?urltoken=".$urltoken;

				$mail_array = $stmt->fetch();
				$mail = $mail_array['mail'];

				//データベースの作成
				$sql = "CREATE TABLE IF NOT EXISTS reset_pass_member"
				." ("
				."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
				."urltoken VARCHAR(128) NOT NULL,"
				."user VARCHAR(50) NOT NULL,"
				."date DATETIME NOT NULL,"
				."flag TINYINT(1) NOT NULL DEFAULT 0"
 				.")ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
	
				$stmt = $pdo->query($sql);
		
				//ここでデータベースに登録する
				try{
					$sql = $pdo->prepare("INSERT INTO reset_pass_member (urltoken, user, date) VALUES (:urltoken, :user, now() )");
		
					$sql->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
					$sql->bindValue(':user', $user, PDO::PARAM_STR);
					$sql->execute();
		
					//データベース接続切断
					$pdo = null;
		
				}catch (PDOException $e){
					echo 'Error:'.$e->getMessage();
					die();
				}

				//メール送信
				$message = send_repass_mail($mail, $url, $user);
		
				if ($message == '送信完了！') {
 	
 					$message = "<p>メールをお送りしました。</p><p><strong>30分以内</strong>にメールに記載されたURLからパスワードを再設定して下さい。</p>";
 	
	 			} else {
					$errors['mail_error'] = $message;
				}
			}
			else{
				//辞書型攻撃対策で、結果を表示しない
				//$errors['user_registration'] = "ユーザー名が登録されていません。";
			}
			
			//データベース接続切断
			$pdo = null;
		}
	
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
 
<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
  <link rel="stylesheet" type="text/css" href="../layout/reset_user_check.css">
  <title>ユーザー確認画面</title>
</head>
<body>
<div class="confirm_area">

<h1>Web掲示板</h1>
 
<?php if (count($errors) === 0): ?>
 
<h2>送信完了</h2>

<p>登録されているメールアドレスに再設定用のURLを送りました。</p>
<p>URLを開いてパスワードの再設定を<strong>30分以内</strong>に行ってください。</p>
 
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