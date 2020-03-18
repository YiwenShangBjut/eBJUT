<?php
/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/3/21
 * Time: 19:44
 */

require 'header.php';
require 'conn.php';

$result = array();
if (isset($_POST['lost_type']) && isset($_POST['user_id']) && isset($_POST['lost_item']) && isset($_POST['lost_com_type'])) {
    $type = $_POST['lost_type'];
    $id = $_POST['user_id'];
    $item = $_POST['lost_item'];
    $com_type = $_POST['lost_com_type'];

    if (isset($_POST["lost_content"]))
        $cont = $_POST['lost_content'];
    else
        $cont = null;
    if (isset($_POST['lost_com_detail']))
        $com_detail = $_POST['lost_com_detail'];
    else
        $com_detail = null;

    $flag = 0;

    if ($stmt = $conn->prepare("SELECT COUNT(lost_id) AS c FROM lost_found WHERE user_id = ? AND lost_item = ? AND lost_status = 0")) {
        $stmt->bind_param("is", $id, $item);
        $stmt->execute();
        $stmt->bind_result($c);
        $stmt->fetch();
        if ($c > 0)
            $flag = 1;
    } else {
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }
    $stmt->free_result();
    $stmt->close();

    if ($flag == 0) {
        if ($stmt = $conn->prepare("INSERT INTO lost_found (lost_type, user_id, lost_item, lost_content, lost_com_type, lost_com_detail)VALUES (?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param("iissis", $type, $id, $item, $cont, $com_type, $com_detail);
            $stmt->execute();
            if($stmt->affected_rows > 0)
            {
                $result["msg"] = "CREATED";
                $result["code"] = 201;
                $result["extra"] = "";
            }else{
                $result["msg"] = "INVALID_ARGUMENTS";
                $result["code"] = 400;
                $result["extra"] = "";
            }
            $stmt->free_result();
            $stmt->close();
        } else {
            $result["msg"] = "BAD_GATEWAY";
            $result["code"] = 502;
            $result["extra"] = "";
        }
    } else {
        $result["msg"] = "NOT_ACCEPTABLE";
        $result["code"] = 406;
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