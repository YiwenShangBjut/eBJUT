<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/23
 * Time: 23:55
 */

class LostStudentCardModel extends Model
{
    protected $_column = [
        'lost_card_id' => ['int(11)'],
        'lost_card_type' => ['tinyint(1)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'lost_student_id' => ['varchar(10）', 'NOT NULL'],
        'lost_card_detail' => ['text'],
        'lost_card_status' => ['tinyint(1)', 'NOT NULL', '0'],
        'lost_card_com_type' => ['tinyint(1)', 'NOT NULL', '0'],
        'lost_card_com_detail' => ['text'],
        'lost_card_time' => ['timestamp', 'NOT NULL', 'CURRENT_TIMESTAMP'],
        'lost_card_update_time' => ['timestamp']
    ];

    protected $_pk = ['lost_card_id']; // Primary Key

    protected $_ai = 'lost_card_id';   // Auto Increment

    /**
     * @param $page
     * @param $limit
     * @param $lost_card_type
     * @param $lost_card_status
     * @return array
     */
    public function getLostCard($page, $limit, $lost_card_type, $lost_card_status)
    {
        $begin = ($page - 1) * $limit;
        $lostCard = LostStudentCardModel::name();
        if ($lost_card_status == 3) {
            if ($lost_card_type == 2) {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("lost_card_status = 0 OR lost_card_status = 1")
                    ->order('lost_card_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_card_id", "lost_student_id", "lost_card_detail", "lost_card_type", "lost_card_status", "lost_card_com_type", "lost_card_com_detail", "lost_card_time"]);
            } else {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostCard}.lost_card_type = :type AND (lost_card_status = 0 OR lost_card_status = 1)", ["type" => $lost_card_type])
                    ->order('lost_card_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_card_id", "lost_student_id", "lost_card_detail", "lost_card_type", "lost_card_status", "lost_card_com_type", "lost_card_com_detail", "lost_card_time"]);
            }
        } else {
            if ($lost_card_type == 2) {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostCard}.lost_card_status = :sta", ["sta" => $lost_card_status])
                    ->order('lost_card_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_card_id", "lost_student_id", "lost_card_detail", "lost_card_type", "lost_card_status", "lost_card_com_type", "lost_card_com_detail", "lost_card_time"]);
            } else {
                $data = $this->join(UsersModel::class, ["user_id"], ["user_id", "user_nickname"])
                    ->where("{$lostCard}.lost_card_type = :type AND {$lostCard}.lost_card_status = :sta", ["type" => $lost_card_type, "sta" => $lost_card_status])
                    ->order('lost_card_id DESC')
                    ->limit($limit, $begin)
                    ->fetchAll(["lost_card_id", "lost_student_id", "lost_card_detail", "lost_card_type", "lost_card_status", "lost_card_com_type", "lost_card_com_detail", "lost_card_time"]);
            }
        }
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($data[$i]["user_id"]);
        }
        return $data;
    }

    /**
     * @param $user_id
     * @param $lost_student_id
     * @return bool
     */
    public function checkLostCard($user_id, $lost_student_id)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("user_id = :uid AND lost_student_id = :lid AND lost_card_status = 0 AND TIMESTAMPDIFF(SECOND, lost_card_time, :ct) < 300", ["uid" => $user_id, "lid" => $lost_student_id, "ct" => $current_timestamp])->fetch(["1"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $lost_card_type
     * @param $user_id
     * @param $lost_student_id
     * @param $lost_card_detail
     * @param $lost_card_com_type
     * @param $lost_card_com_detail
     * @return bool
     */
    public function addLostCard($lost_card_type, $user_id, $lost_student_id, $lost_card_detail, $lost_card_com_type, $lost_card_com_detail)
    {
        if ($this->add(["lost_card_type" => $lost_card_type, "user_id" => $user_id, "lost_student_id" => $lost_student_id, "lost_card_detail" => $lost_card_detail, "lost_card_com_type" => $lost_card_com_type, "lost_card_com_detail" => $lost_card_com_detail]) > 0)
            return true;
        return false;
    }

    /**
     * @param $lost_card_id
     * @param $user_id
     * @param $lost_card_status
     * @param $student_id
     * @return bool
     */
    public function updateLostCard($lost_card_id, $user_id, $lost_card_status, $student_id)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        if ($this->where("lost_card_id = :lid AND (user_id = :uid OR lost_student_id = :sid) AND lost_card_status = 0", ["lid" => $lost_card_id, "uid" => $user_id, "sid" => $student_id])->update(["lost_card_status" => $lost_card_status, "lost_card_update_time" => $current_timestamp]) > 0)
            return true;
        return false;
    }
}