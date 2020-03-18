<?php


class CaptchaModel extends Model
{
    public function sendEmailCode()
    {
        return (new MailModel())->sendVerifyCode($this->generateCode(4), (new UsersModel())->getNicknameByUserId(null), (new UsersModel())->getEmailByUserId(null));
    }

    private function generateCode($length)
    {
        $charSet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $charSet[rand(0, strlen($charSet))];
        }
        return $str;
    }
}