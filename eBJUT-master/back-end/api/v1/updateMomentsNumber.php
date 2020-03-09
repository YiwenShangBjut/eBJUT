<?php
/**
 * Created by PhpStorm.
 * User: 王星人
 * Date: 2019/4/14
 * Time: 13:52
 */
require 'header.php';
require 'conn.php';
if (isset($_POST['moments_comment_number']) && isset($_POST['moments_id'])) {
    $id = $_POST['moments_id'];
    $number = $_POST['moments_comment_number'];
    if ($stmt = $conn->prepare("UPDATE moments SET moments_comment_number = ? WHERE (SELECT moments_comments_status FROM moments_comments WHERE moments_id = ?) = 0 ")) {
        $number = $number + 1;
        $stmt->bind_param("ii", $number, $id);
        $stmt->execute();

        $result["msg"] = "CREATED";
        $result["code"] = 201;
        $result["extra"] = "";
    }elseif($stmt = $conn->prepare("UPDATE moments SET moments_comment_number = ? WHERE (SELECT moments_comments_status FROM moments_comments WHERE moments_id = ?) = 1 ")){
        $number = $number - 1;
        $stmt->bind_param("ii", $number, $id);
        $stmt->execute();

        $result["msg"] = "CREATED";
        $result["code"] = 201;
        $result["extra"] = "";
    }
    else{
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }
}else{
    $result["msg"] = "MISSING_MESSAGE";
    $result["code"] = 400;
    $result["extra"] = "";
}


echo json_encode($result);

$stmt->close();
$conn->close();
?>

