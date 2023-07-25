<?php
require_once './helpers/MemberDAO.php';
require_once './helpers/CartDAO.php';
require_once './helpers/SaleDAO.php';

//セッションを開始する
session_start();

//未ログインの場合
if(empty('member')){
    //ログインページにリダイレクトする
    header('Location:login.php');
    exit;
}

//「購入する」ボタンをクリックせずにこのページを表示した場合はCart.phpにリダイレクトする
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location:cart.php');
    exit;
}

//ログイン中の会員データを取得する
$member = $_SESSION['member'];

//会員のカートデータを取得
$cartDAO = new CartDAO();
$cart_list = $cartDAO->get_cart_by_memberid($member->memberid);

//カートの商品をSaleテーブルに登録する
$saleDAO = new SaleDAO();
$ret = $saleDAO->insert($member->memberid, $cart_list);

//購入処理が成功したとき
if($ret === true){
    //会員のカートデータを全て削除する
    $cartDAO->delete_by_memberid($member->memberid);
}



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>購入完了</title>
    </head>
    <body>
        <?php include "header2.php" ?>

        <?php if($ret === true): ?>
            <p>購入が完了しました。</p>
            <p><a href="index.php">トップページへ</a></p>
        <?php else: ?>
            <p>購入処理でエラーが発生しました。カートページへ戻りもう一度やり直してください。</p>
            <p><a href="cart.php">カートページへ</a></p>
        <?php endif; ?>
    </body>
</html>

