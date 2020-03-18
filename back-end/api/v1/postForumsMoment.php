<?php
/**
 * Created by PhpStorm.
 * User: 王星人
 * Date: 2019/4/14
 * Time: 14:26
 */
require 'header.php';
require 'conn.php';

$result = array();
if (isset($_POST['user_id']) && isset($_POST['forums_id']) && isset($_POST['forums_comments_status']) && isset($_POST['forums_comments'])) {
    $id = $_POST['user_id'];
    $forums_id = $_POST['forums_id'];
    $comments = $_POST['forums_comments'];
    $states = $_POST['forums_comments_status'];
    if ($stmt = $conn->prepare("INSERT INTO forums_comments (user_id,forums_id,forums_comments,forums_comments_status) VALUES (?,?,?,?)")) {
        $stmt->bind_param("iisi", $id, $forums_id,$comments, $states);
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