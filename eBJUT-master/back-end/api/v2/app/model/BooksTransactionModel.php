<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/25
 * Time: 14:54
 */

class BooksTransactionModel extends Model
{

    protected $_column = [
        'book_transaction_id' => ['int(11)'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'book_category' => ['int(11)', 'NOT NULL'],
        'book_transaction_title' => ['varchar(255)', 'NOT NULL'],
        'book_transaction_detail' => ['text'],
        'book_transaction_price' => ['decimal[5,2]', 'NOT NULL'],
        'book_transaction_status' => ['tinyint(1)', 'NOT NULL', '0'],
        'book_transaction_com_type' => ['tinyint(1)', 'NOT NULL', '0'],
        'book_transaction_com_detail' => ['text'],
        'book_transaction_time' => ['timestamp', 'NOT NULL', 'CURRENT_TIMESTAMP'],
        'book_transaction_update_time' => ['timestamp',]
    ];

    protected $_pk = ['book_transaction_id']; // Primary Key
    protected $_ai = 'book_transaction_id';   // Auto Increment

    /**
     * @param $page
     * @param $limit
     * @param $book_category
     * @param $book_transaction_status
     * @return array
     */
    public function getBookTransaction($page, $limit, $book_category, $book_transaction_status)
    {
        $begin = ($page - 1) * $limit;

        $condition = '(TRUE';
        $param = [];

        $book_category = urldecode($book_category);
        if (!empty($book_category)) {
            $book_category = explode(',', $book_category);
            $c = 0;
            $flag = false;
            foreach ($book_category as $bc) {
                $param['bc' . $c] = trim($bc);
                $condition .= ($flag ? ' OR' : ' AND') . ' book_category = :bc' . $c++;
                $flag = true;
            }
        }

        if ($book_transaction_status == 3) {
            $condition .= ') AND (books_transaction.book_transaction_status = 0 OR books_transaction.book_transaction_status = 1)';
        } else {
            $param['sta'] = $book_transaction_status;
            $condition .= ') AND books_transaction.book_transaction_status = :sta';
        }

        $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
            ->join(BooksCategoriesModel::class, [["book_category_id", "book_category"]], ["book_category_id", "book_category_name"])
            ->where($condition, $param)
            ->order('book_transaction_id DESC')
            ->limit($limit, $begin)
            ->fetchAll(["book_transaction_id", "book_transaction_title", "book_transaction_detail", "book_transaction_price", "book_transaction_com_type", "book_transaction_com_detail", "book_transaction_status", "book_transaction_time", "book_transaction_update_time"]);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }

    /**
     * @param $user_id
     * @param $book_transaction_title
     * @return bool
     */
    public function checkBookTransaction($user_id, $book_transaction_title)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("user_id = :uid AND book_transaction_title = :btt AND book_transaction_status = 0 AND  TIMESTAMPDIFF(SECOND, book_transaction_time, :ct) < 300", ["uid" => $user_id, "btt" => $book_transaction_title, "ct" => $current_timestamp])->fetch(["1"]) > 0)
            return true;
        return false;
    }

    /**
     * @param $book_category
     * @param $user_id
     * @param $book_transaction_title
     * @param $book_transaction_detail
     * @param $book_transaction_price
     * @param $book_transaction_com_type
     * @param $book_transaction_com_detail
     * @return bool
     */
    public function addBookTransaction($book_category, $user_id, $book_transaction_title, $book_transaction_detail, $book_transaction_price, $book_transaction_com_type, $book_transaction_com_detail)
    {
        if ($this->add(["book_category" => $book_category, "user_id" => $user_id, "book_transaction_title" => $book_transaction_title, "book_transaction_detail" => $book_transaction_detail, "book_transaction_price" => $book_transaction_price, "book_transaction_com_type" => $book_transaction_com_type, "book_transaction_com_detail" => $book_transaction_com_detail]) > 0)
            return true;
        return false;
    }

    /**
     * @param $book_transaction_id
     * @param $user_id
     * @param $book_transaction_status
     * @return bool
     */
    public function updateBookTransaction($book_transaction_id, $user_id, $book_transaction_status)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("book_transaction_id = :lid AND user_id = :uid AND book_transaction_status = 0", ["lid" => $book_transaction_id, "uid" => $user_id])->update(["book_transaction_status" => $book_transaction_status, "book_transaction_update_time" => $current_timestamp]) > 0)
            return true;
        return false;
    }

    /**
     * @param $page
     * @param $limit
     * @param $keyword
     * @return array
     */
    public function searchBookTransaction($page, $limit, $keyword)
    {
        $like = "%" . $keyword . "%";
        $order = "CASE WHEN book_transaction_title LIKE '%" . $keyword . "%' THEN 1 WHEN book_transaction_detail LIKE '%" . $keyword . "%' THEN 2 END";
        $begin = $limit * ($page - 1);
        $data =  $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
            ->join("books_categories", [["book_category_id", "book_category"]], ["book_category_id", "book_category_name"])
            ->where("(book_transaction_title LIKE :l OR book_transaction_detail LIKE :ll) AND book_transaction_status = 0", ['l' => $like, 'll' => $like])
            ->order($order)
            ->limit($limit, $begin)
            ->fetchAll(["book_transaction_id", "book_transaction_title", "book_transaction_detail", "book_transaction_price", "book_transaction_com_type", "book_transaction_com_detail", "book_transaction_time"]);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }
}