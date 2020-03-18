<?php
/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 2019/5/3
 * Time: 19:16
 */

class ForumModel extends Model
{
    // forum_status = 0 : the forum can be found
    // forum_status = 1 : the forum is deleted (is hidden)
    protected $_column = [
        'category_id' => ['int(11)', 'NOT NULL'],
        'forum_id' => ['int(11)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'forum_title' => ['varchar(39)', 'NOT NULL'],
        'forum_content' => ['text', 'NOT NULL'],
        'forum_timestamp' => ['timestamp', 'NOT NULL'],
        'forum_status' => ['tinyint(1)', 'DEFAULT 0'],
        'forum_comment_number' => ['int(11)', 'NOT NULL', 'DEFAULT 0']
    ];

    protected $_pk = ['forum_id']; // Primary Key

    protected $_ai = 'forum_id';   // Auto Increment

    /**
     * @param $category_id
     * @param $page
     * @param $limit
     * @return array
     */
    public function getThreadTitle($category_id, $page, $limit)
    {

        $condition = '( TRUE';
        $param = [];
        if (!empty($category_id)) {
            $category = explode(',', $category_id);
            $c = 0;
            $flag = false;
            foreach ($category as $ca) {
                $param['ca' . $c] = trim($ca);
                $condition .= ($flag ? ' OR' : ' AND') . ' forums.category_id = :ca' . $c++;
                $flag = true;
            }

        }
        $condition .= ' )';
        $condition .= ' AND forum_status = 0';
        return $this->join(ForumCategoriesModel :: class, ['category_id'], ['category_id', 'category_name'])
            ->join(UsersModel::class, ['user_id'], ['user_id', 'user_username', 'user_nickname'])
            ->where($condition, $param)
            ->order('forum_timestamp DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['forum_id', 'forum_title', 'forum_comment_number', 'forum_timestamp']);
    }

    /**
     * @param $forum_id
     * @return array|bool
     */
    public function getThreadContent($forum_id)
    {
        $row = $this->join(UsersModel::class, ['user_id'], ['user_id', 'user_username', 'user_nickname'])
            ->join(ForumCategoriesModel :: class, ['category_id'], ['category_id', 'category_name'])
            ->where('forum_id = ? AND forum_status = 0', [$forum_id])
            ->fetchAll(['forum_title', 'forum_content', 'forum_timestamp']);
        for ($i = 0; $i < count($row); $i++) {
            $row[$i]["user_avatar_url"] = (new UsersModel())->getAvatarUrl($row[$i]["user_id"], 32);
        }
        return $row;

    }

    /**
     * @param $category_id
     * @param $forum_content
     * @param $forum_title
     * @return bool
     */
    public function addThread($category_id, $forum_content, $forum_title)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if (!empty($forum_title) && !empty($forum_content)) {
            return $this->add(['user_id' => $user_id, 'forum_title' => $forum_title, 'forum_content' => $forum_content, 'category_id' => $category_id]) > 0;
        } else {
            return false;
        }
    }

    /**
     * @param $forum_id
     * @return bool
     */
    public function deleteThread($forum_id)
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if ($this->where('user_id = :uid AND forum_id = :id AND forum_status = 0', ['uid' => $user_id, 'id' => $forum_id])->update(['forum_status' => 1]) > 0) {
            return true;
        } else
            return false;
    }

    /**
     * @param $forum_id
     * @param $number
     * @return bool
     */
    public function updateReplyNumber($forum_id, $number)
    {
        if (!empty($forum_id)) {
            if ($this->where('forum_id = :id', ['id' => $forum_id])->update(['forum_comment_number' => $number]) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param $forum_id
     * @return bool
     */
    public function checkThreadId($forum_id)
    {
        if (!empty($forum_id)) {
            $this->where('forum_id = ?', [$forum_id])->fetch(['1']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $forum_id
     * @return bool
     */
    public function checkThreadStatus($forum_id)
    {
        if ($this->where('forum_id = ? AND forum_status = 0', [$forum_id])->fetch(['1'])) {
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
        if (ceil((time()) - ceil($this->where('user_id = ? AND (forum_status = 0 OR forum_status = 1)', [$user_id])->fetch('MAX(forum_timestamp)')['MAX(forum_timestamp)']) > 60)) {
            return true;
        } else {
            return false;
        }
    }
}