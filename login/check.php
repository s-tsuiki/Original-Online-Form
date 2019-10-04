<?php
require '../tools/database_connect/database_connect.php';

	session_start();
 
	header("Content-type: text/html; charset=utf-8");
 
	//cookieがオフの場合
	if(!isset($_SESSION['token'])){
		echo "cookieを有効にしてください。";
		exit();
	}

	//POST送信されていなかった場合
	if(empty($_POST)){
		header('Location: top.php');
		exit();
	}

	//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
	if ($_POST['token'] != $_SESSION['token']){
		echo "不正なリクエスト";
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

	//POSTされたデータを変数に入れる
	$user = isset($_POST['user']) ? $_POST['user'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;

	//前後にある半角全角スペースを削除
	$user = spaceTrim($user);
	$password = spaceTrim($password);

	if(empty($user)){
		$errors['user'] = "ユーザー名が入力されていません。";
	}
	elseif(empty($password)){
		$errors['password'] = "パスワードが入力されていません。";
	}
	//入力されているとき
	else{
		//データベースへの接続
		$pdo = db_connect();

		//ユーザー名とパスワードが一致したら、マイページに飛ぶ
		$stmt = $pdo->prepare("SELECT password FROM member WHERE user=(:user) AND flag =1");
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		$stmt->execute();
		$registrated_password = $stmt->fetch();

		if(password_verify($password, $registrated_password['password'])){
			echo "ログインできました。"."<br>";
			//CSRF対策
			session_regenerate_id(true);
		}else{
			$errors['member_check'] = "ユーザー名かパスワードが誤っています。";
		}

		//データベース接続切断
		$pdo = null;
	}

	//エラーが無ければセッションに登録し、マイページに飛ぶ
	if(count($errors) === 0){
		$_SESSION['user'] = $user;
		$_SESSION['password'] = $password;

		//POSTデータを保持しながら、マイページに飛ぶ
		header('Location: mypage.php', true, 307);
		exit();
	}
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
  <title>処理画面</title>
</head>
<body>
<?php if(count($errors) > 0):?>

<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<input type="button" value="戻る" onClick="history.back()">

<?php endif; ?>
</body>
</html>
