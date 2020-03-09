<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/23
 * Time: 14:03
 */

class LostFoundModel extends Model
{
    protected $_column = [
        'lost_id' => ['int(11)'],
        'lost_type' => ['tinyint(1)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'lost_title' => ['varchar(255）', 'NOT NULL'],
        'lost_detail' => ['text'],
        'lost_status' => ['tinyint(1)', 'NOT NULL', '0'],
        'lost_com_type' => ['tinyint(1)', 'NOT NULL', '0'],
        'lost_com_detail' => ['text'],
        'lost_time' => ['timestamp', 'NOT NULL', 'CURRENT_TIMESTAMP'],
        'lost_update_time' => ['timestamp']
    ];

    protected $_pk = ['lost_id']; // Primary Key

    protected $_ai = 'lost_id';   // Auto Increment

    /**
     * @param $page
     * @param $limit
     * @param $lost_type
     * @param $lost_status
     * @return array
     */
    public function getLostAndFound($page, $limit, $lost_type, $lost_status)
    {

        $begin = ($page - 1) * $limit;
        $lostFound = lostFoundModel::name();

        if ($lost_status == 3) {
            if ($lost_type == 2) {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("lost_status = 0 OR lost_status = 1")
                    ->order('lost_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_id", "lost_title", "lost_detail", "lost_status", "lost_com_type", "lost_com_detail", "lost_time", "lost_update_time"]);
            } else {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostFound}.lost_type = :type AND (lost_status = 0 OR lost_status = 1)", ["type" => $lost_type])
                    ->order('lost_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_id", "lost_title", "lost_detail", "lost_status", "lost_com_type", "lost_com_detail", "lost_time", "lost_update_time"]);
            }
        } else {
            if ($lost_type == 2) {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostFound}.lost_status = :status", ["status" => $lost_status])
                    ->order('lost_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_id", "lost_title", "lost_detail", "lost_status", "lost_com_type", "lost_com_detail", "lost_time", "lost_update_time"]);
            } else {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostFound}.lost_type = :type AND {$lostFound}.lost_status = :status", ["type" => $lost_type, "status" => $lost_status])
                    ->order('lost_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_id", "lost_title", "lost_detail", "lost_status", "lost_com_type", "lost_com_detail", "lost_time", "lost_update_time"]);
            }
        }
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }

    /**
     * @param $user_id
     * @param $lost_title
     * @return bool
     */
    public function checkLostAndFound($user_id, $lost_title)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("user_id = :uid AND lost_title = :lt AND lost_status = 0 AND TIMESTAMPDIFF(SECOND, lost_time, :ct) < 300", ["uid" => $user_id, "lt" => $lost_title, "ct" => $current_timestamp])->fetch(["1"]) > 0)
            return true;
        return false;
    }

    /**
     * @param $lost_type
     * @param $user_id
     * @param $lost_title
     * @param $lost_detail
     * @param $lost_com_type
     * @param $lost_com_detail
     * @return bool
     */
    public function addLostAndFound($lost_type, $user_id, $lost_title, $lost_detail, $lost_com_type, $lost_com_detail)
    {
        if ($this->add(["lost_type" => $lost_type, "user_id" => $user_id, "lost_title" => $lost_title, "lost_detail" => $lost_detail, "lost_com_type" => $lost_com_type, "lost_com_detail" => $lost_com_detail]) > 0)
            return true;
        return false;
    }

    /**
     * @param $lost_id
     * @param $user_id
     * @param $lost_status
     * @return bool
     */
    public function updateLostAndFound($lost_id, $user_id, $lost_status)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());

        if ($this->where("lost_id = :lid AND user_id = :uid AND lost_status = 0", ["lid" => $lost_id, "uid" => $user_id])->update(["lost_status" => $lost_status, "lost_update_time" => $current_timestamp]) > 0)
            return true;
        return false;
    }

    /**
     * @param $page
     * @param $limit
     * @param $keyword
     * @return array
     */
    public function searchLostAndFound($page, $limit, $keyword)
    {
        $like = "%" . $keyword . "%";
        $order = "CASE WHEN lost_title LIKE '%" . $keyword . "%' THEN 1 WHEN lost_detail LIKE '%" . $keyword . "%' THEN 2 END";
        $begin = $limit * ($page - 1);
        $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
            ->where("(lost_title LIKE :l OR lost_detail LIKE :ll) AND lost_status = 0", ['l' => $like, 'll' => $like])
            ->order($order)
            ->limit($limit, $begin)
            ->fetchAll(["lost_id", "lost_title", "lost_detail", "lost_com_type", "lost_com_detail", "lost_time"]);
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }

}