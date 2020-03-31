<?php
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'setting.php';

function send_repass_complete_mail($mail_address, $user, $password_hide){
// PHPMailerのインスタンス生成
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
    $mail->SMTPAuth = true;
    $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
    $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
    $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
    $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
    $mail->Port = SMTP_PORT; // 接続するTCPポート

    // メール内容設定
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";
    $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
    $mail->addAddress($mail_address, $user); //受信者（送信先）を追加する
//    $mail->addReplyTo('xxxxxxxxxx@xxxxxxxxxx','返信先');
//    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
//    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
    $subject = '【Web掲示板】パスワード再設定完了のお知らせ';
    $mail->Subject = $subject; // メールタイトル
    $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
    $body = 'パスワードの再設定が完了しました。'.'<br>';
    $body = $body.'このメールに身に覚えがない方は不正アクセスをされた可能性があります。'.'<br>';
    $body = $body.'至急このサイトの管理者にお問い合わせください。';
    
    $mail->Body  = $body; // メール本文
    // メール送信の実行
    if(!$mail->send()) {
    	return 'メッセージは送られませんでした！'.'<br>'.'Mailer Error: ' . $mail->ErrorInfo;
    } else {
    	return '送信完了！';
    }
}
