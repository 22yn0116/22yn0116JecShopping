<?php
    require_once './helpers/MemberDAO.php';

    $email = '';
    $errs = [];

    //セッションの開始
    session_start();

    //ログイン済みの時
    if(!empty($_SESSION['member'])){
        //index.phpへリダイレクトする
        header('Location: index.php');
        exit;
    }

    //POSTメソッドでリクエストされたとき
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //入力されたメールアドレスとパスワードを受け取る
        $email = $_POST['email'];
        $password = $_POST['password'];

        //メールアドレスのバリデーションチェック
        if($email === ''){
            $errs[] = 'メールアドレスを入力してください。';
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //メールアドレスの形式チェック
            $errs[] = 'メールアドレスの形式に誤りがあります。';
        }

        //パスワードの未入力チェック
        if($password === ''){
            $errs[] = 'パスワードを入力してください。';    
        }

        //エラーがないとき
        if(empty($errs)){
            $memberDAO = new MemberDAO();
            $member = $memberDAO->get_member($email, $password);

            //会員データが取り出せたとき
            if($member !== false){
                //セッションIDを変更する
                session_regenerate_id(true);

                //セッション変数に会員データを保存する
                $_SESSION['member'] = $member;

                //index.phpに移動
                header('Location: index.php');
                exit;
            }
            //会員データが取り出せなかったとき
            else{
                $errs[] = 'メールアドレスまたはパスワードに誤りがあります。';

            }
        }
        
    }    

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ログイン</title>
    <link href="css/LoginStyle.css" rel="stylesheet">
</head>
<body>
    <?php include "header2.php"; ?>

    <form action=" " method="POST">
        <table id=LoginTable class="box">
            <tr>
                <th colspan="2">
                    ログイン
                </th>
            </tr>
            <tr>
                <td>
                    メールアドレス
                </td>
                <td>
                    <input type="email" required autofocus name="email">
                </td>
            </tr>
            <tr>
                <td>
                    パスワード
                </td>
                <td>
                    <input type="password" required name="password">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value="ログイン">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php foreach($errs as $e): ?>
                        <span style="color:red"><?= $e ?></span>
                        <br>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>
    </form>

    <table class="box">
        <tr>
            <th>初めてご利用の方</th>
        </tr>
        <tr>
            <td>
                ログインするには会員登録が必要です。
            </td>
        </tr>
        <tr>
            <td><a href="signup.php">新規会員登録はこちら</a></td>
        </tr>
    </table>
</body>
</html>
