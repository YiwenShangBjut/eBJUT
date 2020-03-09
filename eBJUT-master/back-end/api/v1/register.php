<?php

require 'header.php';
require 'conn.php';

$result = array();
//$result['data'] = array();

if (isset($_POST['user_login']) && isset($_POST['user_password']) && isset($_POST['user_student_id']) && isset($_POST['user_phone'])) {
    $user_login = $_POST['user_login'];
    $user_password_raw = $_POST['user_password'];
    $user_salt = md5(uniqid(microtime(true), true));
    $user_password = md5(md5($user_password_raw . $user_salt) . $user_salt);
    $user_nickname = isset($_POST["user_nickname"]) ? $_POST['user_nickname'] : $user_login;
    $user_student_id = $_POST['user_student_id'];
    $user_phone = $_POST['user_phone'];
    // user email is optional
    $user_email = isset($_POST["user_email"]) ? $_POST["user_email"] : null;

    //    register time generate by MySQL by NOW() as DEFAULT
    //    $user_register = date("Y-m-d h:i:sa", time());

    //    IP address of the last hop
    $user_register_ip = $_SERVER['REMOTE_ADDR'];
    $user_last_ip = $_SERVER['REMOTE_ADDR'];

    if ($stmt = $conn->prepare("INSERT IGNORE INTO users (user_login, user_password, user_salt, user_nickname, user_student_id, user_phone, user_email, user_register_ip, user_last_ip) VALUES (?,?,?,?,?,?,?,?,?)")) {
        $stmt->bind_param("sssssssss", $user_login, $user_password, $user_salt, $user_nickname, $user_student_id, $user_phone, $user_email, $user_register_ip, $user_last_ip);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $result["msg"] = "OK";
            $result["code"] = 200;
            $result["extra"] = "";
        } else {
            $result["msg"] = "ALREADY_EXISTS";
            $result["code"] = 409;
            $result["extra"] = "";
        }
        $stmt->close();
    } else {
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }
} else {
    $result["msg"] = "MISSING_ARGUMENTS";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);
$conn->close();

?>