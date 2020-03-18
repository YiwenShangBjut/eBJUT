<?php


class AuthFilter extends Filter
{
    public function doFilter($param = [])
    {
        $request = BunnyPHP::getRequest();
        $request->process();
        if (isset($request['user_token']) && (new TokensModel())->verify($request['user_token'])) {
            return self::NEXT;
        }
        $this->error(['msg' => 'UNAUTHORIZED', 'code' => 401, 'extra' => '']);
        return self::STOP;
    }
}

?>