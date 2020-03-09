<?php
/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/12
 * Time: 14:03
 */

class UsersModel extends Model
{
    protected $_column = [
        'user_id' => ['int(11)'],
        'user_username' => ['varchar(255)', 'NOT NULL'],
        'user_password' => ['varchar(255)', 'NOT NULL'],
        'user_salt' => ['varchar(255)', 'NOT NULL'],
        'user_nickname' => ['varchar(255)', 'NOT NULL'],
        'user_id_card' => ['char(18)'],
        'user_student_id' => ['varchar(10)', 'NOT NULL'],
        'user_phone' => ['char(11)', 'NOT NULL'],
        'user_email' => ['varchar(255)'],
        'user_credit' => ['int(11)', 'NOT NULL', '0'],
        'user_register' => ['timestamp', 'NOT NULL', 'CURRENT_TIMESTAMP'],
        'user_register_ip' => ['varchar(39)', 'NOT NULL'],
        'user_last_ip' => ['varchar(39)', 'NOT NULL'],
        'user_status' => ['int(11)', 'NOT NULL']
    ];

    protected $_pk = ['user_id']; // Primary Key

    protected $_ai = 'user_id';   // Auto Increment

    protected $_uk = [['user_email'], ['user_id_card'], ['user_login'], ['user_nickname'], ['user_phone_number'], ['user_student_number']];

    /**
     * @param $user_id
     * @return array|null
     */
    public function getUserProfile($user_id)
    {
        if ($row = $this->where('user_id = ?', [$user_id])->fetch(['user_username', 'user_nickname', 'user_credit', 'user_status'])) {
            return [
                'username' => $row['user_username'],
                'nickname' => $row['user_nickname'],
                'credit' => $row['user_credit'],
                'status' => $row['user_status'],
                'avatar_url' => $this->getAvatarUrl($user_id)
            ];
        } else {
            return null;
        }
    }

    /**
     * @param $user_id
     * @param int $size
     * @return string
     */
    public function getAvatarUrl($user_id, $size = 64)
    {
        /**
         * Icon service powered by Gravatar
         * https://en.gravatar.com/
         */

        $user_email = $this->where('user_id = ?', [$user_id])->fetch(['user_email'])['user_email'];
        $default = "identicon";
        $grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($user_email))) . "?d=" . urlencode($default) . "&s=" . (empty($size) ? 64 : $size);

        return $grav_url;
    }

    /**
     * @param $student_id
     * @return bool
     */
    public function checkStudentId($student_id)
    {
        if (preg_match('/^[0-9]{8}$/', $student_id) || preg_match('/^S20[12]\d[\d]{5}$/', $student_id)) {
            if (!$this->where('user_student_id = ?', [$student_id])->fetch(['1'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $user_id
     * @return string|null
     */
    public function getEmailByUserId($user_id)
    {
        if (empty($user_id))
            $user_id = (new TokensModel())->getUserIdByToken();

        if ($row = $this->where('user_id = ?', [$user_id])->fetch(['user_email'])) {
            return $row['user_email'];
        } else {
            return null;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function checkEmail($email)
    {
        if (preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)) {
            if (!$this->where('user_email = ?', [$email])->fetch(['1'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $username
     * @param $password
     * @param $nickname
     * @param $id_card
     * @param $student_id
     * @param $phone
     * @param $email
     * @param $ip
     * @return bool
     */
    public function register($username, $password, $nickname, $id_card, $student_id, $phone, $email, $ip)
    {
        $nickname = empty($nickname) ? $username : $nickname;
        $id_card = empty($id_card) ? null : $id_card;
        $email = empty($email) ? null : $email;

        $salt = md5(uniqid(microtime(true), true));
        $hash = md5(md5($password . $salt) . $salt);

        if ($this->checkNickname($nickname)) {
            return $this->add(['user_username' => $username, 'user_password' => $hash, 'user_salt' => $salt,
                    'user_nickname' => $nickname, 'user_id_card' => $id_card, 'user_student_id' => $student_id,
                    'user_phone' => $phone, 'user_email' => $email, 'user_register_ip' => $ip,
                    'user_last_ip' => $ip]) > 0;
        } else {
            return false;
        }
    }

    /**
     * @param $nickname
     * @return bool
     */
    public function checkNickname($nickname)
    {
        /**
         * length:
         * charset:
         * format:
         */
        if (!$this->where('user_nickname = ?', [$nickname])->fetch(['1'])) {
            return true;
        }
        return false;
    }

    /**
     * @param $id_card
     * @return bool
     */
    public function checkIdCard($id_card)
    {
        /**
         * length: 18
         * age: 15~35
         */

        if (preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/', $id_card)) {
            if ((int)(substr($id_card, 6, 4)) > (int)(date("Y")) - 35 && (int)(substr($id_card, 6, 4)) < (int)(date("Y")) - 15) {
                if (!$this->where('user_id_card = ?', [$id_card])->fetch(['1'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $phone
     * @return bool
     */
    public function checkPhone($phone)
    {
        if (preg_match('/^1(3|4|5|7|8)\d{9}$/', $phone)) {
            if (!$this->where('user_phone = ?', [$phone])->fetch(['1'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $username
     * @return bool
     */
    public function checkUsername($username)
    {
        /**
         * length: 4-12
         * charset: a-zA-Z0-9_
         * format: not start from 's', not pure digits, not start or end with '_'
         */
        if (strlen($username) >= 4 && strlen($username) <= 12) {
            if (preg_match('/^[a-rt-zzA-RT-Z0-9][a-zA-Z0-9_]{2,9}[a-zA-Z0-9]$/', $username) && !preg_match('/^[0-9]*$/', $username)) {
                if (!$this->where('user_username = ?', [$username])->fetch(['1']))
                    return true;
            }
        }
        return false;
    }

    /**
     * @param $username
     * @param $password
     * @param $user_ip
     * @return array|null
     */
    public function login($username, $password, $user_ip)
    {
        if ($row = $this->where('? IN (user_username, user_phone)', [$username])->fetch(['user_salt'])) {
            $hash = md5(md5($password . $row['user_salt']) . $row['user_salt']);
            if ($row = $this->where('? IN (user_username, user_phone) AND user_password = ?', [$username, $hash])->fetch()) {
                $this->where('user_id = :uid', ['uid' => $row['user_id']])->update(['user_last_ip' => $user_ip]);
                $misc = (new TokensModel())->addToken($row['user_id'], $_SERVER['REMOTE_ADDR']);
                return [
                    'username' => $row['user_username'],
                    'nickname' => $row['user_nickname'],
                    'student_id' => $row['user_student_id'],
                    'phone' => str_split($row['user_phone'], 3)[0] . '*****' . str_split($row['user_phone'], 8)[1],
                    'email' => $row['user_email'],
                    'credit' => intval($row['user_credit']),
                    'status' => $row['user_status'],
                    'token' => $misc['token'],
                    'expired_time' => $misc['expired_time'],
                    'avatar_url' => $this->getAvatarUrl($row['user_id'], 64)
                ];
            }
        }
        return null;
    }

    /**
     * @param $old_password string
     * @param $new_password string
     * @return bool
     */
    public function changePassword($old_password, $new_password)
    {
        $user_id = (new TokensModel())->getUserIdByToken($_POST['user_token']);
        $old_salt = $this->where('user_id = ?', [$user_id])->fetch(['user_salt'])['user_salt'];
        $new_salt = md5(uniqid(microtime(true), true));
        $old_hash = md5(md5($old_password . $old_salt) . $old_salt);
        $new_hash = md5(md5($new_password . $new_salt) . $new_salt);

        if ($this->where('user_password = :p AND user_id = :uid', ['p' => $old_hash, 'uid' => $user_id])->update(['user_password' => $new_hash, 'user_salt' => $new_salt])) {
            (new TokensModel())->revokeTokenByUserId($user_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $user_id
     * @return string Student id
     */
    public function getStudentIdByUserId($user_id)
    {
        if (empty($user_id))
            $user_id = (new TokensModel())->getUserIdByToken();

        return $this->where('user_id = ?', [$user_id])->fetch(['user_student_id'])['user_student_id'];
    }

    /**
     * @param $user_id
     * @return string User phone
     */
    public function getUserPhoneByUserId($user_id)
    {
        if (empty($user_id))
            $user_id = (new TokensModel())->getUserIdByToken();

        return $this->where('user_id = ?', [$user_id])->fetch(['user_phone'])['user_phone'];
    }

    /**
     * @param $user_id
     * @return string User username.
     */
    public function getUsernameByUserId($user_id)
    {
        if (empty($user_id))
            $user_id = (new TokensModel())->getUserIdByToken();

        return $this->where('user_id = ?', [$user_id])->fetch(['user_username'])['user_username'];
    }

    /**
     * @param $user_id
     * @return string User nickname.
     */
    public function getNicknameByUserId($user_id)
    {
        if (empty($user_id))
            $user_id = (new TokensModel())->getUserIdByToken();

        return $this->where('user_id = ?', [$user_id])->fetch(['user_nickname'])['user_nickname'];
    }

    /**
     * @param $user_student_id
     * @return mixed
     */
    public function getUserIdByStudentCard($user_student_id)
    {
        return $this->where('user_student_id = ?', [$user_student_id])->fetch(['user_id'])['user_id'];
    }
}

?>