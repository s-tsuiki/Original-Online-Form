<?php
 
function db_connect(){
	//データベースへの接続
	//$dsnの式の中にスペースを入れないこと！
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	return $pdo;
}
 
?>