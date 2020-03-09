<?php
/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/12
 * Time: 14:29
 */

class UserController extends Controller
{
    /**
     * @param $user_id integer path(0)
     *
     * @api {get} /user/profile/ Get user profile
     * @apiName GetUserProfile
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/profile/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} user_id User id.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {String} data.username Username.
     * @apiSuccess (200) {String} data.nickname Nickname.
     * @apiSuccess (200) {Integer} data.credit User credit.
     * @apiSuccess (200) {Integer} data.status User grade.
     * @apiSuccess (200) {String} data.avatar_url User avatar URL.
     *
     * @apiError (401) (String) INVALID_USER_ID Invalid user id.
     */
    public function ac_profile($user_id)
    {
        if ($user_id > 0) {
            $data = (new UsersModel())->getUserProfile($user_id);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_USER_ID', 'code' => 401, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canGetEmail
     * @param $user_id integer path(0)
     *
     * @api {post} /user/email/ Get user email
     * @apiName GetUserEmail
     * @apiGroup User
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/user/email/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     * @apiParam {Integer} user_id User id.
     *
     * @apiSuccess (200) {String} data OK
     */
    public function ac_email_post($user_id)
    {
        $data = (new UsersModel())->getEmailByUserId($user_id);
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
    }

    /**
     * @param $username string path(0)
     *
     * @api {get} /user/checkUsername/ Check username
     * @apiName CheckUsername
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/checkUsername/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} username Username.
     *
     * @apiSuccess (200) {String} OK Valid username.
     *
     * @apiError (400) {String} INVALID_USERNAME Invalid username.
     */
    public function ac_checkUsername($username)
    {
        if ((new UsersModel())->checkUsername($username))
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
        else
            $this->assignAll(['msg' => 'INVALID_USERNAME', 'code' => 400, 'extra' => ''])->render();
    }

    /**
     * @param $nickname string path(0)
     *
     * @api {post} /user/checkNickname Check nickname
     * @apiGroup User
     * @apuPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/checkNickname/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} nickname Nickname
     *
     * @apiSuccess (200) {String} OK Valid nickname.
     *
     * @apiError (400) {String} INVALID_NICKNAME Invalid nickname.
     */
    public function ac_checkNickname_post($nickname)
    {
        if ((new UsersModel())->checkNickname($nickname))
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
        else
            $this->assignAll(['msg' => 'INVALID_NICKNAME', 'code' => 400, 'extra' => ''])->render();
    }

    /**
     * @param $student_id string path(0)
     *
     * @api {post} /user/checkStudentId Check student id
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/checkStudentId/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} student_id Student id
     *
     * @apiSuccess (200) {String} OK Valid student id.
     *
     * @apiError (400) {String} INVALID_STUDENT_ID Invalid student id.
     */
    public function ac_checkStudentId_post($student_id)
    {
        if ((new UsersModel())->checkStudentId($student_id)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_STUDENT_ID', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $email string path(0)
     *
     * @api {post} /user/checkEmail Check email
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/checkEmail/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} email Email address.
     *
     * @apiSuccess (200) {String} OK Valid email address.
     *
     * @apiError (400) {String} INVALID_EMAIL Invalid email address.
     */
    public function ac_checkEmail_post($email)
    {
        if ((new UsersModel())->checkEmail($email)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_EMAIL', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $username string
     * @param $password string
     *
     * @api {post} /user/login/ Login
     * @apiName Login
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/login/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} username Username or phone number.
     * @apiParam {String} password Password.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {String} data.username Username.
     * @apiSuccess (200) {String} data.nickname Nickname.
     * @apiSuccess (200) {String} data.student_id Student ID.
     * @apiSuccess (200) {String} data.email User email.
     * @apiSuccess (200) {Integer} data.credit User credit
     * @apiSuccess (200) {Integer} data.status User level.
     * @apiSuccess (200) {String} data.token User access token.
     * @apiSuccess (200) {timestamp} data.expired_time The time that after which the token will be expired。
     * @apiSuccess (200) {String} data.avatar_url Avatar URL.
     *
     * @apiSuccess (400) {String} MISSING_ARGUMENTS Missing arguments.
     *
     * @apiSuccess (401) {String} UNAUTHORIZED Incorrect username or password.
     */
    public function ac_login_post($username, $password)
    {
        if (!empty($username) && !empty($password)) {
            if (null === $data = (new UsersModel())->login($username, $password, $_SERVER['REMOTE_ADDR'])) {
                $this->assignAll(['msg' => 'UNAUTHORIZED', 'code' => 401, 'extra' => ''])->render();
            } else {
                $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canLogout
     *
     * @api {post} /user/logout Logout
     * @apiName Logout
     * @apiGroup User
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/user/logout/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     *
     * @apiSuccess (200) {String} OK Logout successful.
     */
    public function ac_logout_post()
    {
        (new TokensModel())->revokeTokenByToken((new TokensModel())->getToken());
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
    }

    /**
     * @param $username
     * @param $password
     * @param $nickname
     * @param $id_card
     * @param $student_id
     * @param $phone
     * @param $email
     *
     * @api {post} /user/register/ Register
     * @apiName Register
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/register/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} username Username.
     * @apiParam {String} password Password.
     * @apiParam {String} nickname Nickname.
     * @apiParam {String} id_card PRC id card.
     * @apiParam {String} student_id BJUT student id.
     * @apiParam {String} phone PRC phone number.
     * @apiParam {String} email Email address.
     *
     * @apiSuccess (200) {String} OK Registration successful.
     *
     * @apiError (400) {String} INVALID_USERNAME Username invalid.
     * @apiError (400) {String} INVALID_NICKNAME Nickname invalid.
     * @apiError (400) {String} INVALID_EMAIL Email address invalid.
     * @apiError (400) {String} INVALID_PHONE Phone number invalid.
     * @apiError (400) {String} INVALID_STUDENT_ID Student id invalid.
     * @apiError (400) {String} MISSING_ARGUMENTS Missing arguments.
     */
    public function ac_register_post($username, $password, $nickname, $id_card, $student_id, $phone, $email)
    {
        if (!empty($username) && !empty($password) && !empty($student_id) && !empty($phone)) {
            if (!(new UsersModel())->checkUsername($username)) {
                $this->assignAll(['msg' => 'INVALID_USERNAME', 'code' => 400, 'extra' => ''])->render();
            } elseif (!(new UsersModel())->checkNickname($nickname)) {
                $this->assignAll(['msg' => 'INVALID_NICKNAME', 'code' => 400, 'extra' => ''])->render();
            } elseif (!empty($email) && !((new UsersModel())->checkEmail($email))) {
                $this->assignAll(['msg' => 'INVALID_EMAIL', 'code' => 400, 'extra' => ''])->render();
            } elseif (!(new UsersModel())->checkPhone($phone)) {
                $this->assignAll(['msg' => 'INVALID_PHONE', 'code' => 400, 'extra' => ''])->render();
            } elseif (!(new UsersModel())->checkStudentId($student_id)) {
                $this->assignAll(['msg' => 'INVALID_STUDENT_ID', 'code' => 400, 'extra' => ''])->render();
            } elseif ($data = (new UsersModel())->register($username, $password, $nickname, $id_card, $student_id, $phone, $email, $_SERVER['REMOTE_ADDR'])) {
                $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
            } else {
                $this->assignAll(['msg' => 'BAD_GATEWAY', 'code' => 502, 'extra' => $data])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $id_card string path(0)
     *
     * @api {post} /article/checkIdCard Check ID card
     * @apiName CheckIdCard
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/checkIdCard/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} id_card PRC id card.
     *
     * @apiSuccess (200) {String} OK Id card is valid.
     *
     * @apiError (400) {String} INVALID_ID_CARD Id card is invalid.
     */
    public function ac_checkIdCard_post($id_card)
    {
        if ((new UsersModel())->checkIdCard($id_card)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_ID_CARD', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canGetUserId
     *
     * @api {post} /user/id/ Get user id by token
     * @apiName GetUserIdByToken
     * @apiGroup User
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/user/id/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token.
     *
     * @apiSuccess (200) {Integer} data User id which correspond with the token.
     */
    public function ac_id_post()
    {
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => (new TokensModel())->getUserIdByToken()])->render();
    }

    /**
     * @param $user_id string path(0)
     * @param $size int
     *
     * @api {get} /user/avatar Get user avatar url
     * @apiName GetUserAvatarUrl
     * @apiGroup User
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/user/avatar/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_id User id
     * @apiParam {Integer} size Image size, for example 64 represents 64 x 64 px.
     *
     * @apiSuccess (200) {String} data URL of avatar.
     *
     * @apiError (400) {String} INVALID_USER_ID User id invalid.
     */
    public function ac_avatar_get($user_id, $size)
    {
        if (!empty($user_id) && $data = (new UsersModel())->getAvatarUrl($user_id, $size)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'INVALID_USER_ID', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter auth canChangePassword
     * @param $old_password string
     * @param $new_password string
     *
     * @api {put} /user/changePassword Change password
     * @apiName ChangePassword
     * @apiGroup User
     * @apiPermission user
     * @apiSampleRequest https://dev.iecho.cc/api/user/changePassword/
     * @apiVersion 0.1.0
     * @apiDescription User's token will be revoked automatically after changed his password so that it should show login screen after this process finished.
     *
     * @apiParam {String} old_password Old password.
     * @apiParam {String} new_password New password.
     * @apiParam {String} user_token User access token.
     *
     * @apiSuccess (200) {String} data OK Password changed successfully.
     *
     * @apiError (400) {String} MISSING_ARGUMENTS Missing arguments.
     *
     * @apiError (400) {String} INVALID_PASSWORD Old password invalid.
     */
    public function ac_changePassword_put($old_password, $new_password)
    {
        if (!empty($old_password) && !empty($new_password)) {
            if ((new UsersModel())->changePassword($old_password, $new_password)) {
                $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
            } else {
                $this->assignAll(['msg' => 'INVALID_PASSWORD', 'code' => 400, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @filter admin isAdmin
     *
     * @api {post} /user/isAdmin Is admin
     * @apiName IsAdmin
     * @apiGroup User
     * @apiPermission admin
     * @apiSampleRequest https://dev.iecho.cc/api/user/isAdmin
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User access token
     *
     * @apiSuccess (200) {String} data OK Current user is admin.
     */
    public function ac_isAdmin_post()
    {
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
    }
}

?>