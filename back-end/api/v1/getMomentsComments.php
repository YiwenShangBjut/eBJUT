<?php
/**
 * Created by PhpStorm.
 * User: 王星人
 * Date: 2019/4/10
 * Time: 19:07
 */
require 'header.php';
require 'conn.php';
$result = array();
$result["data"] = array();
$upper_limit = 19;
if ((isset($_GET["limit"]))) {
    if (isset($_GET["page"])) {
        if ($_GET["limit"] > 0 && $_GET["limit"] <= $upper_limit && $_GET["page"] > 0) {
            $limit = $_GET['limit'];
            $page = $_GET["page"];
#$sql = "SELECT * FROM moments";

            if ($stmt = $conn->prepare("SELECT user_id, moments_comment,moments_publish_time,moments_comment_status FROM moments_comments WHERE moments_comment_status = 0 ORDER BY moments_publish_time DESC LIMIT ?,?")) {
                #out put the result
                $left = ($page - 1) * $limit;
                $right = $page * $limit;
                $stmt->bind_param("ii", $left, $right);
                $stmt->execute();
                $stmt->bind_result($user_id, $moments_comment, $moments_publish_time, $moments_comment_states, $moments_publish_time, $moments_comment_states);
                while ($row = $stmt->fetch() && $moments_states = 0) {
                    $result["data"][] = array("user_id" => $user_id, "moments_comment" => $moments_comment, "moments_publish_time" => $moments_publish_time, "moments_comment_status" => $moments_comment_states);
                }
                $stmt->free_result();
                $stmt->close();
                $result["msg"] = "OK";
                $result["code"] = 200;
                $result["extra"] = "";
            } else {
                $result["msg"] = "BAD_GATEWAY";
                $result["code"] = 502;
                $result["extra"] = "";
            }
        } else {
            $result["msg"] = "OUT_OF_RANGE";
            $result["code"] = 400;
            $result["extra"] = "";
        }
    } else {
        $result["msg"] = "MISSING_PAGE";
        $result["code"] = 400;
        $result["extra"] = "";
    }
} else {
    $result["msg"] = "MISSING_LIMIT";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);
$conn->close();
?>