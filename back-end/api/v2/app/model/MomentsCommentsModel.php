<?php

/**
 * Created by PhpStorm.
 * User: XingRen
 * Date: 2019/4/23
 * Time: 11:10
 */
class MomentsCommentsModel extends Model
{
    // moment_status = 0 : the moment comment can be found
    // moment_status = 1 : the moment comment is deleted (is hidden)
    protected $_column = [
        'moment_comment_id' => ['int(11)', 'NOT NULL'],
        'moment_id' => ['int(11)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'moment_comment' => ['text', 'NOT NULL'],
        'moment_publish_timestamp' => ['timestamp', 'NOT NULL'],
        'moment_comment_status' => ['tinyint(1)', 'DEFAULT 0']
    ];
    protected $_pk = ['moment_comment_id']; // Primary Key

    protected $_ai = 'moment_comment_id';   // Auto Increment

    /**
     * @param $moment_id
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getMomentComments($moment_id, $page = 1, $limit = 20)
    {
        $row = $this->join(UsersModel::class, ['user_id'], ['user_id', 'user_nickname', 'user_username'])
            ->where('moment_id = ? AND moment_comment_status = 0', [$moment_id])
            ->order('moment_publish_timestamp DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['moment_comment_id', 'moment_comment', 'moment_publish_timestamp']);
        for ($i = 0; $i < count($row); $i++) {
            $row[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($row[$i]["user_id"]);
        }
        return $row;
    }

    /**
     * @param $moment_id
     * @param $user_id
     * @param $moment_comment
     * @return array|null
     */
    public function addMomentComment($moment_id, $user_id, $moment_comment)
    {
        if (strlen($moment_comment) > 0) {
            $row = $this->add(['moment_id' => $moment_id, 'user_id' => $user_id, 'moment_comment' => $moment_comment]) > 0;
            $number = $this->addMomentCommentNumber($moment_id);
            return [$row['moment_comment'], $number];
        } else {
            return null;
        }
    }

    /**
     * @param $moment_id
     * @return bool
     */
    public function addMomentCommentNumber($moment_id)
    {
        if ($this->where('moment_id = ? AND moment_comment_status = 0', [$moment_id])->fetch(['1']) > 0) {
            $number = $this->where('moment_id = ? AND moment_comment_status = 0', [$moment_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new MomentsModel())->updateMomentCommentNumber($moment_id, $number);
        } else {
            return false;
        }
    }

    /**
     * @param $moment_id
     * @param $moment_comment_id
     * @return bool|null
     */
    public function deleteMomentComment($moment_id, $moment_comment_id)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ($row = $this->where('user_id = :uid AND moment_id = :id AND moment_comment_id = :mcid AND moment_comment_status = 0', ['uid' => $user_id, 'id' => $moment_id, 'mcid' => $moment_comment_id])->update(['moment_comment_status' => 1, 'moment_publish_timestamp' => date("Y-m-d H:i:s", time())]) > 0) {
            $number = $this->where('moment_id = ? AND moment_comment_status = 0', [$moment_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new MomentsModel())->updateMomentCommentNumber($moment_id, $number);
        } else
            return null;
    }

    /**
     * @param $moment_id
     * @return bool
     */
    public function checkCommentStatus($moment_id)
    {
        return $this->where('moment_id = ? AND moment_comment_status = 0', [$moment_id])->fetch(['1']);

    }

    /**
     * @return bool
     */
    public function timeDiffer()
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if (ceil(time() - ceil($this->where('user_id = ? AND (moment_comment_status = 0 OR moment_comment_status = 1)', [$user_id])->fetch('MAX(moment_publish_timestamp)')['MAX(moment_publish_timestamp)'])) > 60) {
            return true;
        } else {
            return false;
        }
    }
}