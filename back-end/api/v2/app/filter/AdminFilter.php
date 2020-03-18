<?php

class AdminFilter extends Filter
{
    public function doFilter($param = [])
    {
        $request = BunnyPHP::getRequest();
        $request->process();
        if (isset($request['user_token']) && (new TokensModel())->verify($request['user_token'])) {
            $user_id = (new TokensModel())->getUserIdByToken();
            if ((new UsersModel())->where('user_id = ? AND user_status = -1', [$user_id])->fetch()) {
                return self::NEXT;
            }
        }
        $this->error(['msg' => 'UNAUTHORIZED', 'code' => 401, 'extra' => '']);
        return self::STOP;
    }
}