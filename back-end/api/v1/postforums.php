<?php
/**
 * Created by PhpStorm.
 * User: 王星人
 * Date: 2019/4/13
 * Time: 19:56
 */
require 'header.php';
require 'conn.php';

$result = array();
if (isset($_POST['categories_id']) && isset($_POST['user_id']) && isset($_POST['forums_title']) && isset($_POST['forums_content']) && isset($_POST['forums_status'])) {
    $id = $_POST['user_id'];
    $categories = $_POST['categories_id'];
    $title = $_POST['forums_title'];
    $content = $_POST['forums_content'];
    $states = $_POST['forums_status'];
    #$sql = "INSERT INTO moments (user_id,moments_content,moments_status) VALUES (?,?,?)";
    if ($stmt = $conn->prepare("INSERT INTO forums (categories_id,user_id,forums_title,forums_content,forums_status) VALUES (?,?,?,?,?)")) {
        $stmt->bind_param("iissi", $categories,$id, $title,$content, $states);
        $result["msg"] = "CREATED";
        $result["code"] = 201;
        $result["extra"] = "";
    } else {
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }

} else {
    $result["msg"] = "MISSING_MESSAGE";
    $result["code"] = 400;
    $result["extra"] = "";
}
echo json_encode($result);

$conn->close();
?>


