<?php
/**
 * Created by PhpStorm.
 * User: 尚逸文
 * Date: 2019/3/24
 * Time: 16:54
 */
require 'header.php';
require 'conn.php';

$id = $_POST['user_id'];
$login = $_POST['user_login'];
$password = $_POST['user_password'];
$nickname = $_POST['user_nickname'];
$id_card_id = $_POST['user_id_card'];
$student_id = $_POST['user_student_id'];
$phone = $_POST['user_phone'];
$email = $_POST['user_email'];
$credit = 0;
$register = $_POST['user_register'];
$status = 0;

$result = array();
if (isset($id) && isset($login) && isset($password) && isset($nickname) && isset($id_card_id) && isset($student_id) && isset($phone) && isset($email) && isset($register)) {
    $sql = "INSERT INTO account (user_id, user_login, lost_password, user_salt, user_nickname, user_id_card, user_student_id, user_phone, user_email, user_credit, user_register, user_register_ip, user_last_ip, user_status )
VALUES ($id,$login, $password, $salt, $nickname, $id_card_id, $student_id, $phone, $email, $credit, $register,$register_ip, $last_ip, $status)";
    $conn->query($sql);

    $result["msg"] = "Success";
    $result["code"] = 200;
    $result["extra"] = "";
} else {
    $result["msg"] = "Failure";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);

$conn->close();
?>