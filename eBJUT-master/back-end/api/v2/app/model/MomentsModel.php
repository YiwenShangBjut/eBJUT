<?php

/**
 * Created by PhpStorm.
 * User: XingRen
 * Date: 2019/4/23
 * Time: 01:01
 */
class MomentsModel extends Model
{
    // moment_status = 0 : the moment can be found
    // moment_status = 1 : the moment is deleted (is hidden)
    protected $_column = [
        'moment_id' => ['int(11)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'moment_content' => ['text', 'NOT NULL'],
        'moment_like_number' => ['int(11)', 'NOT NULL', '0'],
        'moment_comment_number' => ['int(11)', 'NOT NULL'],
        'moment_timestamp' => ['timestamp', 'NOT NULL'],
        'moment_status' => ['tinyint(1)', 'DEFAULT 0']
    ];

    protected $_pk = ['moment_id']; // Primary Key

    protected $_ai = 'moment_id';   // Auto Increment

    /**
     * @param $page
     * @param $limit
     * @return array
     */
    public function getMoments($page, $limit)
    {
        $row = $this->join(UsersModel::class, ['user_id'], ['user_id', 'user_nickname', 'user_username'])
            ->where("moment_status = 0")
            ->order('moment_timestamp DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['moment_id', 'moment_content', 'moment_like_number', 'moment_comment_number', 'moment_timestamp']);
        for ($i = 0; $i < count($row); $i++) {
            $row[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($row[$i]["user_id"]);
        }
        return $row;
    }

    /**
     * @param $moment_content
     * @return bool
     */
    public function addMoment($moment_content)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if (!empty($moment_content)) {
            return $this->add(['user_id' => $user_id, 'moment_content' => $moment_content]) > 0;
        } else {
            return false;

        }
    }

    /**
     * @param $moment_id
     * @return bool
     */
    public function deleteMoment($moment_id)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        return ($this->where('user_id = :uid AND moment_id = :mid AND moment_status = 0', ['uid' => $user_id, 'mid' => $moment_id])->update(['moment_status' => 1, 'moment_timestamp' => date("Y-m-d H:i:s", time())]) > 0);
    }

    /**
     * @param $moment_status
     * @return bool
     */
    public function checkMomentStatus($moment_status)
    {
        if ($moment_status == 0 || $moment_status == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $moment_id
     * @param $number
     * @return bool
     */
    public function updateMomentLikeNumber($moment_id, $number)
    {
        if ($this->checkMomentId($moment_id)) {
            if ($this->where('moment_id = :id', ['id' => $moment_id])->update(['moment_like_number' => $number]) > 0) {
                return $this->where('moment_id = ?', [$moment_id])->fetch(['moment_like_number'])['moment_like_number'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $moment_id
     * @return mixed
     */
    public function checkMomentId($moment_id)
    {
        return $this->where('moment_id = ? AND moment_status = 0', [$moment_id])->fetch(['1']);
    }

    /**
     * @param $moment_id
     * @param $number
     * @return bool
     */
    public function updateMomentCommentNumber($moment_id, $number)
    {
        if ($this->checkMomentId($moment_id)) {
            if ($this->where('moment_id = :id', ['id' => $moment_id])->update(['moment_comment_number' => $number]) > 0) {
                return $this->where('moment_id = ?', [$moment_id])->fetch(['moment_comment_number'])['moment_comment_number'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function timeDiffer()
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if (ceil(time() - ceil($this->where('user_id = ? AND (moment_status = 0 OR moment_status = 1)', [$user_id])->fetch('MAX(moment_timestamp)')['MAX(moment_timestamp)']) > 60)) {
            return true;
        } else {
            return false;
        }
    }

}