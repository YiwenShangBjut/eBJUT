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
if (isset($_POST['user_id']) && isset($_POST['moments_content']) && isset($_POST['moments_status'])) {
    $id = $_POST['user_id'];
    $moments = $_POST['moments_content'];
    $states = $_POST['moments_status'];
    #$sql = "INSERT INTO moments (user_id,moments_content,moments_status) VALUES (?,?,?)";
    if ($stmt = $conn->prepare("INSERT INTO moments (user_id,moments_content,moments_status) VALUES (?,?,?)")) {
        $stmt->bind_param("isi", $id, $moments, $states);
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

