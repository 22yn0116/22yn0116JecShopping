<?php
require_once 'DAO.php';
require_once 'CartDAO.php';
require_once 'SaleDetailDAO.php';

class SaleDAO
{
    //Saleテーブルから指針のSalenoを取得する
    private function get_saleno()
    {
        $dbh = DAO::get_db_connect();

        //Saleテーブルから、最新の販売番号を取得するSQL
        $sql = "SELECT IDENT_CURRENT('Sale') AS saleno";

        //SQLを実行する
        $stmt = $dbh->query($sql);

        $row = $stmt->fetchObject();
        return $row->saleno; //最新のsalenoを返す
    }

    //DBに購入データを追加する
    public function insert(int $memberid, array $cart_list)
    {
        //戻り値
        $ret = false;

        //DBに接続する
        $dbh = DAO::get_db_connect();

        try {
            //トランザクションを開始する
            $dbh->beginTransaction();

            //トランザクション終了までSale表を共有ロックする
            $sql = "SELECT * FROM Sale WITH(TABLOCK, HOLDLOCK)"; //TABLOCK：テーブルにロックをかける/HOLDLOCK：トランザクション終了までロック状態を維持する
            $dbh->query($sql);


            //Saleテーブルに購入情報を追加するSQL
            $sql = "INSERT INTO Sale(saledate, memberid) VALUES(:saledate, :memberid)";

            $stmt = $dbh->prepare($sql);

            //現在時刻を取得する
            $saledate = date('Y-m-d H:i:s');

            //SQLに変数の値を当てはめる
            $stmt->bindValue('saledate', $saledate, PDO::PARAM_STR);
            $stmt->bindValue('memberid', $memberid, PDO::PARAM_INT);

            $stmt->execute();

            //最新のsalenoの値を取得する
            $saleno = $this->get_saleno();

            $saleDetailDAO = new SaleDetailDAO();

            //カートの商品をSaleDetailテーブルに追加する
            foreach ($cart_list as $cart) {
                $saleDetail = new SaleDetail();

                $saleDetail->saleno = $saleno;
                $saleDetail->goodscode = $cart->goodscode;
                $saleDetail->num = $cart->num;

                $saleDetailDAO->insert($saleDetail, $dbh);
            }

            //コミットしてトランザクションを終了する
            $dbh->commit();
            $ret = true;
        }
        //DB更新中に例外が発生したとき
        catch (PDOException $e) {
            //ロールバックしてトランザクションを終了する
            $dbh->rollBack();
            $ret = false;
        }
        return $ret;
    }
}
