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
	if(empty($_POST['token'])){
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

	// ログイン状態チェック
	if (!isset($_SESSION['user']) || !isset($_SESSION['password'])) {
    		header("Location: top.php");
    		exit();
	}

	//ユーザー名を設定
	$user = $_SESSION['user'];

?>

<?php
	//フォーム処理部分

	//必要な値を設定
	$id = 1;
	$date_time = date("Y/m/d H:i:s");
	$name = $user;
	$password = $_SESSION['password'];
	//パスワードが正しいかの確認 0:初期状態 1:正しい 2:正しくない
	$is_correct = 0;
	//どの状況かを判断
	$case = 0;

	//データベースへの接続
	$pdo = db_connect();

	//データベースの作成
	$sql = "CREATE TABLE IF NOT EXISTS comment"
	." ("
	. "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	. "name VARCHAR(50) NOT NULL,"
	. "comment TEXT,"
	. "date_time DATETIME,"
	. "password VARCHAR(128) NOT NULL"
	.");";
	$stmt = $pdo->query($sql);

	
	//入力フォームの処理
	if(!empty($_POST['e_number'])){
		//編集モード
		$case = 1;

		if(isset($_POST['name']) || isset($_POST['comment'])){
			
			if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])){
				//POSTで各値を受け取る
				$id = $_POST['e_number'];
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$password = $_POST['password'];

				//パスワードのハッシュ化
				$password_hash =  password_hash($password, PASSWORD_DEFAULT);
				
				//入力したデータをupdateによって編集する
				$sql = 'update comment set name=:name,comment=:comment,date_time=:date_time,password=:password_hash where id=:id and name=:name';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt->bindParam(':date_time', $date_time, PDO::PARAM_STR);	//注意
				$stmt->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();

				//初期化
				//$name = "";
				$comment = "";
				$e_number = NULL;
			}
		}
	}
	elseif(isset($_POST['name']) || isset($_POST['comment'])){
		//入力モード
		$case = 2;
		if(!empty($_POST['name']) && !empty($_POST['comment'])&& !empty($_POST['password'])){
			//POSTで各値を受け取る
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$password = $_POST['password'];

			//パスワードのハッシュ化
			$password_hash =  password_hash($password, PASSWORD_DEFAULT);
			
			//insertを行ってデータを入力
			$sql = $pdo -> prepare("INSERT INTO comment (name, comment, date_time, password) VALUES (:name, :comment, :date_time, :password_hash)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':date_time', $date_time, PDO::PARAM_STR);	//注意
			$sql -> bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
			$sql -> execute();
			
			//初期化
			//$name = "";
			$comment = "";
			$e_number = NULL;
		}
	}

	//削除フォームの処理
	elseif(isset($_POST['delete_number'])){
		$case = 3;
		if(!empty($_POST['delete_number'])&& !empty($_POST['delete_password'])){
			//削除番号を格納
			$id = $_POST['delete_number'];
			$password = $_POST['delete_password'];
			
			//入力したデータをdeleteによって削除する
			if($id > 0){
				//削除番号と一致するパスワードを取得
				$sql = 'select password from comment where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();
				$registrated_password = $stmt->fetch();

				if(password_verify($password, $registrated_password['password'])){
					$sql = 'delete from comment where id=:id and name=:name';
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);
					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->execute();
					if($stmt->rowCount() == 1){
						//削除成功
						$is_correct = 1;
					}else{
						//ユーザー名が一致しないとき、削除しない
						$is_correct = 2;
					}
				}else{
					//パスワードが一致しないとき、削除しない
					$is_correct = 2;
				}
			}
		}
	}

	//編集フォームの処理
	elseif(isset($_POST['edit_number'])){
		$case = 4;
		if(!empty($_POST['edit_number']) && !empty($_POST['edit_password'])){
			//編集番号を格納
			$id = $_POST['edit_number'];
			$password = $_POST['edit_password'];
			
			if($id > 0){
				$sql = 'SELECT * FROM comment';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				//編集対象番号の名前とコメントを取得する
				foreach($results as $row){
					if($row['id'] === $id && password_verify($password, $row['password']) && $row['name'] === $name){
						//編集番号をセット
						$e_number = $row['id'];
						//名前とコメントを取得
						$name = $row['name'];
						$comment = $row['comment'];
						$is_correct = 1;
					}elseif($row['id'] === $id){
						$is_correct = 2;
					}
				}
			}
		}
	}

	//クロスサイトリクエストフォージェリ（CSRF）対策
	$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
	$token = $_SESSION['token'];
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
  <link rel="stylesheet" type="text/css" href="../layout/mypage.css">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet"><!--Font Awesome 5 Freeの使用-->
  <title><?=htmlspecialchars($user, ENT_QUOTES, 'UTF-8')?>さんのマイページ</title>
</head>
<body>

<div class = "user_bar">
  <h2>ようこそ、<strong><?=htmlspecialchars($user, ENT_QUOTES, 'UTF-8')?></strong>さん</h2>

  <!--CSRF対策-->
  <form method = "post" action = "logout.php">
   <input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
   <input type = "submit" value="ログアウト" class = "logout"><br>
  </form>

</div>

<div class = "start_bar">
  <div class = "title">
    <h1>Web掲示板</h1>
    <p>テーマ:雑談(何を話してもよいです！)</p>
  </div>
</div>

<div class = "form">
<h2>入力フォーム</h2>
<p>コメントを入力してください。</p>
<p><strong>誹謗中傷等がないよう、投稿内容には十分注意してください。</strong></p>
<form method = "post" action = "mypage.php">
 <input type = "hidden" name = "name" value = <?php if(!empty($name)){echo "'$name'";}?> >
 <div class = "item">
   <lavel for="comment">コメント:</lavel>
   <textarea name="comment" cols="40" rows="5"><?php if(!empty($comment)){echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');}?></textarea>
 </div>
 <input type = "hidden" name = "password" value = <?php if(!empty($password)){echo "'$password'";}?>>
 <input type = "submit" value = "送信" class = "submit">
 <input type = "hidden" name = "e_number" value = <?php if(!empty($e_number)){echo $e_number;}?> >
 <input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
</form>
</div>

<div class = "form">
<h2>削除フォーム</h2>
<p>削除番号を入力してください。</p>
<p>自分が投稿したコメントのみ削除できます。</p>
<form method = "post" action = "mypage.php">
 <div class = "item">
  <lavel for="delete_number">削除番号:</lavel>
  <input type = "number" name = "delete_number">
 </div>
 <input type = "hidden" name = "delete_password" value = <?php if(!empty($password)){echo "'$password'";}?>>
 <input type = "submit" value = "削除" class = "submit">
 <input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
</form>
</div>

<div class = "form">
<h2>編集フォーム</h2>
<p>編集番号を入力してください。</p>
<p>自分が投稿したコメントのみ編集できます。</p>
<form method = "post" action = "mypage.php">
 <div class = "item">
  <lavel for="edit_number">編集番号:</lavel>
  <input type = "number" name = "edit_number">
 </div>
 <input type = "hidden" name = "edit_password" value = <?php if(!empty($password)){echo "'$password'";}?>>
 <input type = "submit" value = "編集" class = "submit">
 <input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
</form>
</div>

<br>

<div class = "error_message">
<?php
	//エラーメッセージ
	if($case == 1 || $case == 2){
		if(empty($_POST['name'])){
			echo "<br>";
			echo "<strong>Error: 不正なリクエストです。</strong><br>";
			echo "<br>";
		}
		elseif(empty($_POST['comment'])){
			echo "<br>";
			echo "<strong>Error: コメントを入力してください。</strong><br>";
			echo "<br>";
		}
		elseif(empty($_POST['password'])){
			echo "<br>";
			echo "<strong>Error: 不正なリクエストです。</strong><br>";
			echo "<br>";
		}
		elseif($is_correct == 2){
			echo "<br>";
			echo "<strong>Error: 不正なリクエストです。</strong><br>";
			echo "<br>";
		}
	}
	elseif($case == 3){
		if(empty($_POST['delete_number'])){
			echo "<br>";
			echo "<strong>Error: 削除番号を入力してください。</strong><br>";
			echo "<br>";
		}
		elseif($_POST['delete_number'] <= 0){
			echo "<br>";
			echo "<strong>Error: 正しい削除番号を入力してください。</strong><br>";
			echo "<br>";
		}
		elseif(empty($_POST['delete_password'])){
			echo "<br>";
			echo "<strong>Error: 不正なリクエストです。</strong><br>";
			echo "<br>";
		}
		elseif($is_correct == 2){
			echo "<br>";
			echo "<strong>Error: 自分のコメントの削除番号を指定してください。</strong><br>";
			echo "<br>";
		}
	}
	elseif($case == 4){
		if(empty($_POST['edit_number'])){
			echo "<br>";
			echo "<strong>Error: 編集番号を入力してください。</strong><br>";
			echo "<br>";
		}
		elseif($_POST['edit_number'] <= 0){
			echo "<br>";
			echo "<strong>Error: 正しい編集番号を入力してください。</strong><br>";
			echo "<br>";
		}
		elseif(empty($_POST['edit_password'])){
			echo "<br>";
			echo "<strong>Error: 不正なリクエストです。</strong><br>";
			echo "<br>";
		}
		elseif($is_correct == 2){
			echo "<br>";
			echo "<strong>Error: 自分のコメントの編集番号を指定してください。</strong><br>";
			echo "<br>";
		}
	}
	
?>
</div>

<h2 class = "comment_title" id = "1">コメント一覧</h2>

<br>

<?php
	//現時点でのテーブル内容の表示
	$sql = 'SELECT * FROM comment';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo '<div class = "status">';
		echo $row['id'].'　';
		echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8').'　';
		echo date('Y/m/d H:i:s', strtotime($row['date_time']));
		echo '</div>';
		echo '<div class = "comment">';
		echo '<p>'.str_replace(["\r\n","\r","\n"], "</p><p>", htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8')).'</p>';
		echo '</div>';
	}

	//データベース接続切断
	$pdo = null;
?>

<!--CSRF対策-->
<form method = "post" action = "mypage.php#2"  id = "2">
 <input type = "hidden" name = "token" value = <?=htmlspecialchars($token, ENT_QUOTES, 'UTF-8')?> >
 <input type = "submit" value="コメントを更新" class = "update"><br> 
</form>

<input type="button" value="↓" onclick = "location.href='#2'" class = "jump_bottom">

</body>

</html>
