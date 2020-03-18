<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/5/3
 * Time: 20:49
 */

class ItemsTransactionModel extends Model
{
    protected $_column = [
        'item_transaction_id' => ['int(11)'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'item_transaction_title' => ['varchar(255)', 'NOT NULL'],
        'item_transaction_detail' => ['text'],
        'item_transaction_price' => ['decimal[5,2]', 'NOT NULL'],
        'item_transaction_status' => ['tinyint(1)', 'NOT NULL', '0'],
        'item_transaction_com_type' => ['tinyint(1)', 'NOT NULL', '0'],
        'item_transaction_com_detail' => ['text'],
        'item_transaction_time' => ['timestamp', 'NOT NULL', 'CURRENT_TIMESTAMP'],
        'item_transaction_update_time' => ['timestamp']
    ];

    protected $_pk = ['item_transaction_id']; // Primary Key
    protected $_ai = 'item_transaction_id';   // Auto Increment

    /**
     * @param $page
     * @param $limit
     * @param $item_transaction_status
     * @return array
     */
    public function getItemTransaction($page, $limit, $item_transaction_status)
    {
        $begin = ($page - 1) * $limit;
        if ($item_transaction_status == 3) {
            $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                ->where("item_transaction_status = 0 OR item_transaction_status = 1")
                ->order('item_transaction_id DESC')
                ->limit($limit, $begin)
                ->fetchAll(["item_transaction_id", "item_transaction_title", "item_transaction_detail", "item_transaction_price", "item_transaction_com_type", "item_transaction_com_detail", "item_transaction_time", "item_transaction_update_time"]);
        } else {
            $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                ->where("item_transaction_status = ?", [$item_transaction_status])
                ->order('item_transaction_id DESC')
                ->limit($limit, $begin)
                ->fetchAll(["item_transaction_id", "item_transaction_title", "item_transaction_detail", "item_transaction_price", "item_transaction_com_type", "item_transaction_com_detail", "item_transaction_status", "item_transaction_time", "item_transaction_update_time"]);
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }

        return $data;
    }

    /**
     * @param $user_id
     * @param $item_transaction_title
     * @return bool
     */
    public function checkItemTransaction($user_id, $item_transaction_title)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("user_id = :uid AND item_transaction_title = :itt AND item_transaction_status = 0 AND  TIMESTAMPDIFF(SECOND, item_transaction_time, :ct) < 300", ["uid" => $user_id, "itt" => $item_transaction_title, "ct" => $current_timestamp])->fetch(["1"]) > 0)
            return true;
        return false;
    }

    /**
     * @param $user_id
     * @param $item_transaction_title
     * @param $item_transaction_detail
     * @param $item_transaction_price
     * @param $item_transaction_com_type
     * @param $item_transaction_com_detail
     * @return bool
     */
    public function addItemTransaction($user_id, $item_transaction_title, $item_transaction_detail, $item_transaction_price, $item_transaction_com_type, $item_transaction_com_detail)
    {
        if ($this->add(["user_id" => $user_id, "item_transaction_title" => $item_transaction_title, "item_transaction_detail" => $item_transaction_detail, "item_transaction_price" => $item_transaction_price, "item_transaction_com_type" => $item_transaction_com_type, "item_transaction_com_detail" => $item_transaction_com_detail]) > 0)
            return true;
        return false;
    }

    /**
     * @param $item_transaction_id
     * @param $user_id
     * @param $item_transaction_status
     * @return bool
     */
    public function updateItemTransaction($item_transaction_id, $user_id, $item_transaction_status)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("item_transaction_id = :iid AND user_id = :uid AND item_transaction_status = 0", ["iid" => $item_transaction_id, "uid" => $user_id])->update(["item_transaction_status" => $item_transaction_status, "item_transaction_update_time" => $current_timestamp]) > 0)
            return true;
        return false;
    }

    /**
     * @param $page
     * @param $limit
     * @param $keyword
     * @return array
     */
    public function searchItemTransaction($page, $limit, $keyword)
    {
        $like = "%" . $keyword . "%";
        $order = "CASE WHEN item_transaction_title LIKE '%" . $keyword . "%' THEN 1 WHEN item_transaction_detail LIKE '%" . $keyword . "%' THEN 2 END";
        $begin = $limit * ($page - 1);
        $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
            ->where("(item_transaction_title LIKE :l OR item_transaction_detail LIKE :ll) AND item_transaction_status = 0", ['l' => $like, 'll' => $like])
            ->order($order)
            ->limit($limit, $begin)
            ->fetchAll(["item_transaction_id", "item_transaction_title", "item_transaction_detail", "item_transaction_price", "item_transaction_com_type", "item_transaction_com_detail", "item_transaction_time"]);

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }
}