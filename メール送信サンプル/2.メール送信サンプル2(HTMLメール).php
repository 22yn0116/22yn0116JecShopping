<?php
// PHPでメールを送信するサンプルプログラムです。
// ★の部分を各自変更して下さい。
//
// PHPMailerというライブラリを使用します。
// 参考： https://qiita.com/e__ri/items/857b12e73080019e00b5

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// PHPMailerの読み込みパス
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/SMTP.php';

// 文字エンコードを指定
mb_language('uni');
mb_internal_encoding('UTF-8');

// インスタンスを生成（true指定で例外を有効化）
$mail = new PHPMailer(true);

// 文字エンコードを指定
$mail->CharSet = 'utf-8';

try {
    // SMTPサーバの設定
    $mail->isSMTP();                          // SMTPの使用宣言
    $mail->Host       = 'smtp.gmail.com';     // SMTPサーバーを指定
    $mail->SMTPAuth   = true;                 // SMTP authenticationを有効化
    $mail->Username   = '22yn0116@jec.ac.jp';   // ★自分の学校メールアドレス
    $mail->Password   = 'pd6Jhc37hgfF';             // ★Gmailパスワード
    $mail->SMTPSecure = 'ssl';                // 暗号化モード（tls or ssl）。無効の場合はfalse。
    $mail->Port       = 465;                  // TCPポートを指定（tlsの場合は465や587）

    // 送信元（第2引数は省略可）
    //$mail->setFrom('XXXXXX@jec.ac.jp', '差出人名');

    // 宛先（第2引数は省略可）
    $mail->addAddress('22yn0116@jec.ac.jp', '受信者名');      // ★宛先TO
    // $mail->addAddress('XXXXXX@example.com', '受信者名'); // 他にも宛先TOがあれば指定
    // $mail->addCC('XXXXXX@example.com', '受信者名');      // CC
    // $mail->addBCC('XXXXXX@example.com');                // BCC

    // 返信先
    //$mail->addReplyTo('XXXXXX@example.com', 'お問い合わせ');

    // 件名
    $mail->Subject = 'JecShopping購入完了';

    // ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓  HTMLメールサンプル ここから  ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

    // HTMLメールを有効に。
    $mail->isHTML(true);

    //本文。HTMLで記述。
    $mail->Body = 'HTML形式の本文<B>太字</b>';

    // 埋込画像の設定。
    // 第一引数： 画像ファイル
    // 第二引数： コンテンツID名。任意の名前を付ける。
    //           本文の画像指定で『cid:コンテンツID名』で利用できるようになる。
    $mail->AddEmbeddedImage('./images/goods/A0005.jpg', 'A0005.jpg');
    //AddEmbeddedImage()メソッドで『画像ファイルパス』と『コンテンツID』を指定する。コンテンツIDには任意の名前を付けることができる。
    //画像を表示したい位置に<img src='cid:コンテンツid名'>を指定する。埋め込み画像を表示する場合は『cid:』指定が必要

    //『cid:コンテンツID名』で画像を指定
    $mail->Body="<img src='cid:A0005.jpg'>";

    // 本文。HTMLタグが使える。
    $mail->Body = "
        <p>".$member->name."さん。</p>
        <p>購入ありがとうございました。</p>

        <table>
            <tr>
                <td><img src='cid:A0005.jpg'></td>
            </tr>
            <tr>
                <td>Tシャツ(紅白)</td>
            </tr>
            <tr>
                <td>1,500円</td>
            </tr>
        </table>
        <hr>
    ";

    // ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑  HTMLメールサンプル ここまで  ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

    // メール送信
    $mail->send();
    echo 'メールを送信しました。';
}
catch (Exception $e) {
    echo "メールを送信できませんでした。Mailer Error: {$mail->ErrorInfo}";
}
