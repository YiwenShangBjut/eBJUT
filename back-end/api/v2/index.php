<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2017/10/5
 * Time: 15:31
 */

header('X-Powered-By:BunnyPHP');
define('APP_PATH', __DIR__ . '/');
define('APP_DEBUG', true);
define("IN_TWIMI_PHP", "True", TRUE);
date_default_timezone_set('PRC');
require(APP_PATH . 'BunnyPHP/BunnyPHP.php');
(new BunnyPHP())->run();