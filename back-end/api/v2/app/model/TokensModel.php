<?php
/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/12
 * Time: 21:24
 */


class TokensModel extends Model
{
    protected $_column = [
        'token_id' => ['int(11)'],
        'token_user_id' => ['int(11)', 'NOT NULL'],
        'token_value' => ['char(128)', 'NOT NULL'],
        'token_expired_timestamp' => ['timestamp', 'NOT NULL'],
        'token_user_ip' => ['varchar(39)', 'NOT NULL']
    ];

    protected $_pk = ['token_id']; // Primary Key

    protected $_ai = 'token_id';   // Auto Increment

    /**
     * @param $user_token string User access token.
     * @return integer
     */
    public function getUserIdByToken($user_token = null)
    {
        if (empty($user_token)) {
            $user_token = $this->getToken();
        }
        return $this->where('token_value = :t', ['t' => $user_token])->fetch(['token_user_id'])['token_user_id'];
    }

    /**
     * @param $token string
     * @return bool
     */
    public function verify($token)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        return $this->where('token_value = :t AND token_expired_timestamp >= :ts', ['t' => $token, 'ts' => $current_timestamp])->fetch(['1']);
    }

    /**
     * @param $user_id integer
     * @param $user_ip string
     * @return mixed token & expired time
     */
    public function addToken($user_id, $user_ip)
    {
        $this->revokeTokenByUserId($user_id);

        $token = hash("sha512", md5(uniqid(md5(microtime(true)), true)));
        $expired_time = time() + 31 * 24 * 60 * 60;
        $this->add(['token_user_id' => $user_id, 'token_value' => $token, 'token_expired_timestamp' => date('Y-m-d H:i:s', $expired_time), 'token_user_ip' => $user_ip]);
        return [
            'token' => $token,
            'expired_time' => $expired_time
        ];
    }

    /**
     * @param $user_id integer
     * @return bool
     */
    public function revokeTokenByUserId($user_id)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        return $this->where('token_user_id = :uid AND token_expired_timestamp > :t', ['uid' => $user_id, 't' => $current_timestamp])->update(['token_expired_timestamp' => $current_timestamp]) > 0;
    }

    /**
     * @param $token string
     * @return bool
     */
    public function revokeTokenByToken($token)
    {
        $current_timestamp = date('Y-m-d H:i:s', time());
        return $this->where('token_value = :t', ['t' => $token])->update(['token_expired_timestamp' => $current_timestamp]) > 0;
    }

    /**
     * @return string User access token
     */
    public function getToken()
    {
        $request = BunnyPHP::getRequest();
        $request->process();
        return $request['user_token'];
    }
}

?>