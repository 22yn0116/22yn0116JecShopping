<?php
    require_once './helpers/MemberDAO.php';
    require_once './helpers/CartDAO.php';

    //セッションの開始
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    //ログイン中の時
    if(!empty($_SESSION['member'])){
        //セッション変数の会員情報を取得する
        $member = $_SESSION['member'];

        $cartDAO = new CartDAO();
        $num = $cartDAO->get_number_of_goods($member->memberid);
    }

    if(!empty($_GET['keyword'])){
        $keyword = $_GET['keyword'];
    }
    
?>
<header><!-- ログイン中ヘッダー -->
    <link href="css/HeaderStyle.css" rel="stylesheet"> <!--スタイルシートを読み込む-->

    <div id="logo">
        <a href="index.php">
            <img src="images/JecShoppingLogo.jpg" alt="JecShoppingロゴ">
        </a>
    </div>

    
    <div id="link"> <!-- Lesson4 -->

        <!-- 検索機能をつける　Lesson9 -->
        <form action="index.php" METHOD="GET">
            <?php if(isset($keyword)): ?>
                <input type="text" value="<?= $keyword; ?>" name="keyword">
            <?php else: ?>
                <input type="text" name="keyword">        
            <?php endif; ?>
            <input type="submit" value="検索">
        </form>

        <?php if(isset($member)): ?>
            <?= $member->membername ?>さん
            <!-- Lesson5 -->
            <a href="cart.php">カート<?= '('.$num.')' ; ?></a> <!-- Lesson05 P.80 -->
            <a href="logout.php">ログアウト</a>
        <?php else: ?>    
            <a href="login.php">ログイン</a>
        <?php endif; ?>
    </div>

    <div id="clear">
        <hr>
    </div>
    
</header>
