<?php

/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/28
 * Time: 17:39
 */

class InstallController extends Controller
{
    public function ac_index()
    {
        if (Config::checkLock('install')) {
            die("Execute after delete ./config/install.lock");
        } else {
            $db_info = Config::load('config');
            define('DB_TYPE', $db_info->get(['db', 'type'], 'mysql'));
            define('DB_HOST', $db_info->get(['db', 'host'], 'localhost'));
            define('DB_PORT', $db_info->get(['db', 'port'], '3306'));
            define('DB_NAME', $db_info->get(['db', 'database']));
            define('DB_USER', $db_info->get(['db', 'username']));
            define('DB_PASS', $db_info->get(['db', 'password']));
            define('DB_PREFIX', $db_info->get(['db', 'prefix']));

            $models = scandir(APP_PATH . "app/model");
            /**
             * @var $modelClass Model
             */
            foreach ($models as $model) {
                if (substr($model, -9) == "Model.php") {
                    $modelClass = substr($model, 0, -4);
                    $modelClass::create();
                }
            }
            $lock_file = fopen(APP_PATH . "config/install.lock", "w") or die("Unable to open file!");
            fclose($lock_file);
        }
    }
}