<?php
/**
 * Created by PhpStorm.
 * User: 王星人
 * Date: 2019/4/9
 * Time: 12:26
 */
require 'header.php';
require 'conn.php';

$result = array();
if (isset($_POST['user_id']) && isset($_POST['moments_content']) && isset($_POST['moments_comment_status']) && isset($_POST['moments_comment_number']) && isset($_POST['moments_id'])) {
    $id = $_POST['user_id'];
    $moments = $_POST['moments_content'];
    $states = $_POST['moments_comment_status'];
    $number = $_POST['moments_comment_number'];
    $moments_id = $_POST['moments_id'];
    #$sql = "INSERT INTO moments_comments (user_id,moments_content,moments_comment_status) VALUES (?,?,?)";
    if ($stmt = $conn->prepare("INSERT INTO moments_comments (user_id,moments_comment,moments_comment_status,moments_id) VALUES (?,?,?,?)")) {
        $stmt->bind_param("isii", $id, $moments, $states,$moments_id);
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