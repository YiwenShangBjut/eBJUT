<?php

/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 2019/5/4
 * Time: 19:25
 */
class ForumReplyModel extends Model
{
    // moment_status = 0 : the forum comment can be found
    // moment_status = 1 : the forum comment is deleted (is hidden)
    protected $_column = [
        'forum_comments_id' => ['int(11)', 'NOT NULL'],
        'forum_id' => ['int(11)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'forum_comments' => ['text', 'NOT NULL'],
        'forum_comments_timestamp' => ['timestamp', 'NOT NULL'],
        'forum_comments_status' => ['tinyint(1)', 'DEFAULT 0']
    ];
    protected $_pk = ['forum_comments_id']; // Primary Key

    protected $_ai = 'forum_comments_id';   // Auto Increment

    /**
     * @param $forum_id
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getReply($forum_id, $page, $limit)
    {
        $row = $this->join(UsersModel::class, ['user_id'], ['user_id', 'user_username', 'user_nickname'])
            ->where('forum_id = ? AND forum_comments_status = 0', [$forum_id])
            ->order('forum_comments_timestamp DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['forum_comments_id', 'forum_comments', 'forum_comments_timestamp']);
        for ($i = 0; $i < count($row); $i++) {
            $row[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($row[$i]["user_id"]);
        }
        return $row;
    }

    /**
     * @param $forum_id
     * @param $user_id
     * @param $forum_comments
     * @param $forum_comments_status
     * @return array|null
     */
    public function addReply($forum_id, $user_id, $forum_comments, $forum_comments_status)
    {
        if (strlen($forum_comments) > 0 && $this->checkReplyStatus($forum_comments_status)) {
            $row = $this->add(['forum_id' => $forum_id, 'user_id' => $user_id, 'forum_comments' => $forum_comments, 'forum_comments_status' => $forum_comments_status]) > 0;
            $number = $this->addReplyNumber($forum_id);
            return [$row['forum_comments'], $number];
        } else {
            return null;
        }
    }

    /**
     * @param $forum_id
     * @return bool
     */
    public function addReplyNumber($forum_id)
    {
        if ($this->where('forum_id = ? AND forum_comments_status = 0', [$forum_id])->fetch(['1']) > 0) {
            $number = $this->where('forum_id = ? AND forum_comments_status = 0', [$forum_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new ForumModel())->updateReplyNumber($forum_id, $number);
        } else {
            return false;
        }
    }

    /**
     * @param $forum_id
     * @param $forum_comments_id
     * @return bool|null
     */
    public function deleteReply($forum_id, $forum_comments_id)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ($row = $this->where('user_id = :uid AND forum_id = :id AND forum_comments_id = :mcid AND forum_comments_status = 0', ['uid' => $user_id, 'id' => $forum_id, 'mcid' => $forum_comments_id])->update(['forum_comments_status' => 1]) > 0) {
            $number = $this->where('forum_id = ? AND forum_comments_status = 0', [$forum_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new ForumModel())->updateReplyNumber($forum_id, $number);
        } else
            return null;
    }

    /**
     * @param $forum_comments_status
     * @return bool
     */
    public function checkReplyStatus($forum_comments_status)
    {
        if ($forum_comments_status == 0 || $forum_comments_status == 1) {
            return true;
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
        if (ceil((time()) - ceil($this->where('user_id = ? AND (forum_comments_status = 0 OR forum_comments_status = 1)', [$user_id])->fetch('MAX(forum_comments_timestamp)')['MAX(forum_comments_timestamp)']) > 60)) {
            return true;
        } else {
            return false;
        }
    }

}