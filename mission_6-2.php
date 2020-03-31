<?php
require 'tools/database_connect/database_connect.php';

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

	
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
  <meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
  <link rel="stylesheet" type="text/css" href="/layout/mission_6-2.css">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet"><!--Font Awesome 5 Freeの使用-->
  <title>Web掲示板</title>
</head>

<body>
<div class = "start_bar">
  <div class = "title">
    <h1>Web掲示板</h1>
    <p>テーマ:雑談(何を話してもよいです！)</p>
  </div>
  <input type="button" value="ログイン" onclick="location.href='/login/top.php'" class = "login">
</div>

<div class = "description">
  <p>コメントをするには、<strong>ログイン</strong>が必要です。</p>
  <p>右上のログインボタンからログインしてください。</p>
  <br>
  <p>アカウントがない場合は、<a href="/registration/mail_registration.php" target="_blank">アカウントを作成</a></p>
</div>

<br>

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
<br>

<input type="button" value="コメントを更新" onclick = "window.location.reload();location.href='#2'" class = "update" id = "2">

<input type="button" value="↓" onclick = "location.href='#2'" class = "jump_bottom">

</body>

</html>