<?php

/**
 * Created by PhpStorm.
 * User: XingRen
 * Date: 2019/4/24
 * Time: 13:53
 */
class MomentsLikeModel extends Model
{
    // moment_like_status = 0 : the moment like can be found
    // moment_like_status = 1 : the moment like is deleted (is hidden)
    protected $_column = [
        'moment_id' => ['int(11)', 'NOT NULL'],
        'user_id' => ['int(11)', 'NOT NULL'],
        'moment_like_status' => ['tinyint(1)', 'DEFAULT 0']
    ];

    protected $_uk = [['moment_id', 'user_id']];//unique key

    /**
     * @param $moment_id
     * @return array
     */
    public function likeList($moment_id)
    {
        return $this->join(UsersModel::class, ['user_id'], ['user_id', 'user_nickname', 'user_username'])
            ->where('moment_id = ? AND moment_like_status = 0', [$moment_id])
            ->fetchAll(['moment_id', 'moment_like_status']);
    }

    /**
     * @param $user_id
     * @param $moment_id
     * @return mixed|null
     */
    public function like($user_id, $moment_id)
    {
        if (!($this->where('user_id = ? AND moment_id = ? AND (moment_like_status = 0 OR moment_like_status = 1)', [$user_id, $moment_id])->fetch(['1']) > 0)) {
            $this->add(['moment_id' => $moment_id, 'user_id' => $user_id]);
            $number = $this->where('moment_id = ? AND moment_like_status = 0', [$moment_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new MomentsModel())->updateMomentLikeNumber($moment_id, $number);
        } elseif ($this->where('user_id = ? AND moment_id = ? AND moment_like_status = 1', [$user_id, $moment_id])->fetch(['1']) > 0) {
            $this->where('user_id = :uid AND moment_id = :mid', ['uid' => $user_id, 'mid' => $moment_id])->update(['moment_like_status' => 0]);
            $number = $this->where('moment_id = ? AND moment_like_status = 0', [$moment_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new MomentsModel())->updateMomentLikeNumber($moment_id, $number);
        } else {
            return null;
        }
    }

    /**
     * @param $user_id
     * @param $moment_id
     * @return mixed|null
     */
    public function dislike($user_id, $moment_id)
    {
        if ($this->where('user_id = :uid AND moment_id = :mid AND moment_like_status = 0', ['uid' => $user_id, 'mid' => $moment_id])->update(['moment_like_status' => 1, 'moment_like_timestamp' => date("Y-m-d H:i:s", time())]) > 0) {
            $number = $this->where('moment_id = :mid AND moment_like_status = 0', ['mid' => $moment_id])->fetch('COUNT(*)')['COUNT(*)'];
            return (new MomentsModel())->updateMomentLikeNumber($moment_id, $number);
        } else {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function timeDiffer()
    {
        $user_id = (new TokensModel())->getUserIdByToken();
        if (ceil(time() - ceil($this->where('user_id = ? AND (moment_like_status = 0 OR moment_like_status = 1)', [$user_id])->fetch('MAX(moment_like_timestamp)')['MAX(moment_like_timestamp)'])) > 5) {
            return true;
        } else {
            return false;
        }
    }
}