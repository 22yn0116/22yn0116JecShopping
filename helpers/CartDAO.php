<?php
require_once 'DAO.php';

Class Cart
{
    public int $memberid; //会員ID
    public String $goodscode; //商品コード
    public String $goodsname; //商品名
    public int $price; //価格
    public String $detail; //商品紹介
    public String $goodsimage; //商品画像
    public int $num; //数量
}

Class CartDAO
{
    //会員のカートデータを取得する
    public function get_cart_by_memberid(int $memberid){
        //DBに接続する
        $dbh = DAO::get_db_connect();

        $sql = "SELECT memberid, Cart.goodscode, goodsname, price, detail, goodsimage, num 
                FROM Cart INNER JOIN Goods ON Cart.goodscode = Goods.goodscode 
                WHERE memberid = :memberid";
        
        $stmt = $dbh->prepare($sql);

        //SQLに変数の値を当てはめる
        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
        $stmt->execute();

        //取得したデータをCartクラスに配列にする
        $data = [];
        while($row = $stmt->fetchObject('Cart')){
            $data[] = $row;
        }

        return $data;
    }

    //指定した商品がカートテーブルに存在するか確認する
    public function cart_exists(int $memberid, String $goodscode)
    {
        //DBに接続する
        $dbh = DAO::get_db_connect();

        $sql = "SELECT * FROM Cart WHERE memberid=:memberid AND goodscode=:goodscode ";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
        $stmt->bindValue('goodscode', $goodscode, PDO::PARAM_STR);

        $stmt->execute();

        if($stmt->fetch() !== false){
            return true; //カートに商品が存在する
        }
        else{
            return false; //カートに商品が存在しない
        }
    }

    //カートテーブルに商品を追加する
    public function insert(int $memberid, string $goodscode, int $num)
    {
        $dbh = DAO::get_db_connect();

        //カートテーブルに同じ商品がないとき
        if(!$this->cart_exists($memberid, $goodscode))
        {
            //カートテーブルに商品を登録する
            $sql = "INSERT INTO Cart(memberid, goodscode, num) VALUES(:memberid, :goodscode, :num)";
            
            $stmt = $dbh->prepare($sql);

            //SQLに変数の値を当てはめる
            $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
            $stmt->bindValue('goodscode', $goodscode, PDO::PARAM_STR);
            $stmt->bindValue('num', $num, PDO::PARAM_INT);
            $stmt->execute();
        }
        //カートテーブルに同じ商品があるとき
        else{
            //カートテーブルに商品個数を加算する
            $sql = "UPDATE Cart SET num += :num WHERE memberid = :memberid AND goodscode = :goodscode";
            
            $stmt = $dbh->prepare($sql);

            $stmt->bindValue('num', $num, PDO::PARAM_INT);
            $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
            $stmt->bindValue('goodscode', $goodscode, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    //カートテーブルの商品個数を変更する
    public function update(int $num, int $memberid, string $goodscode)
    {
        $dbh = DAO::get_db_connect();

        $sql = "UPDATE Cart SET num = :num WHERE memberid = :memberid AND goodscode = :goodscode";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue('num', $num, PDO::PARAM_INT);
        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
        $stmt->bindValue('goodscode', $goodscode, PDO::PARAM_STR);
        $stmt->execute();
    }

    //カートテーブルから商品を削除する
    public function delete(int $memberid, string $goodscode)
    {
        $dbh = DAO::get_db_connect();

        $sql = "DELETE FROM Cart WHERE memberid = :memberid AND goodscode = :goodscode";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
        $stmt->bindValue('goodscode', $goodscode, PDO::PARAM_STR);
        $stmt->execute();
    }

    //会員のカート情報を全て削除する
    public function delete_by_memberid(int $memberid){
        $dbh = DAO::get_db_connect();

        $sql = "DELETE FROM Cart WHERE memberid = :memberid";

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);

        $stmt->execute();
    }

    //カート商品の個数を表示
    public function get_number_of_goods(int $memberid){
        $dbh = DAO::get_db_connect();

        $sql = "SELECT SUM(num) AS num FROM Cart WHERE memberid = :memberid";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);
        $stmt->execute();

        //①オブジェクトnumとして取り出す(AS num必須)
        $data = $stmt->fetchObject();
        return $data->num;

        //②列名numとして取り出す(AS num必須)
        // $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $data['num'];

        //③配列番号、文字列でも取り出し可、配列番号ならAS num不要、②より処理速度が遅い
        // $data = $stmt->fetch();
        // return $data[0];
    }
}