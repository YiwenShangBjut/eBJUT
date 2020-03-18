<?php
/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/3/21
 * Time: 21:46
 */

require 'header.php';
require 'conn.php';

$upper_limit = 50;
$result = array();
$result["data"] = array();


if (isset($_GET["lost_type"])) {
    $type = $_GET["lost_type"];
    if (isset($_GET["limit"])) {
        $limit = $_GET["limit"];
        if (isset($_GET["page"])) {
            $page = $_GET["page"];
            if (($type == 1 || $type == 0) && ($limit > 0 && $limit < $upper_limit) && $page > 0) {
                if ($stmt = $conn->prepare("SELECT lost_item, lost_content, lost_com_type, lost_com_detail, lost_time, user_nickname, user_phone FROM lost_found JOIN users USING(user_id) WHERE lost_type = ? and lost_status = 0 ORDER BY lost_id DESC LIMIT ?,?")) {
                    $left = ($page - 1) * $limit;
                    $right = $page * $limit;
                    $stmt->bind_param("iii", $type,$left, $right);
                    $stmt->execute();
                    $stmt->bind_result($lost_item, $lost_content, $lost_com_type, $lost_com_detail, $lost_time, $user_nickname, $user_phone);
                    while ($stmt->fetch())
                        $result["data"][] = array("lost_item" => $lost_item, "lost_content" => $lost_content, "lost_com_type" => $lost_com_type, "lost_com_detail" => $lost_com_detail, "lost_time" => $lost_time,"user_nickname" => $user_nickname, "user_phone" => $user_phone);

                    $result["msg"] = "OK";
                    $result["code"] = 200;
                    $result["extra"] = [];
                    $stmt->free_result();
                    $stmt->close();
                } else {
                    $result["msg"] = "BAD_GATEWAY";
                    $result["code"] = 502;
                    $result["extra"] = "";
                }
            } else {
                $result["msg"] = "OUT_OF_RANGE";
                $result["code"] = 400;
                $result["extra"] = [];
            }
        } else {
            $result["msg"] = "MISSING_PAGE";
            $result["code"] = 400;
            $result["extra"] = [];
        }
    } else {
        $result["msg"] = "MISSING_LIMIT";
        $result["code"] = 400;
        $result["extra"] = [];
    }
} else {
    $result["msg"] = "MISSING_LOST_TYPE";
    $result["code"] = 400;
    $result["extra"] = [];
}

echo json_encode($result);

$conn->close();

?>