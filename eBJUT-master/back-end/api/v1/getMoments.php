<?php
/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 3/21/2019
 * Time: 16:27
 */

header('Content-type:text/json');
require 'conn.php';
/**
 * $id = $_POST['user_id'];
 * $weibo_id = $_POST['moments_id'];
 * $cont = $_POST['moments_content'];
 * $like_num = $_POST['moments_like_number'];
 * $comm_num = $_POST['moments_comment_number'];
 */
$result = array();
$result["data"] = array();
$upper_limit = 19;
if ((isset($_GET["limit"]))) {
    if (isset($_GET["page"])) {
        if ($_GET["limit"] > 0 && $_GET["limit"] <= $upper_limit && $_GET["page"] > 0) {
            $limit = $_GET['limit'];
            $page = $_GET["page"];
#$sql = "SELECT * FROM moments";

            if ($stmt = $conn->prepare("SELECT user_id, moments_content, moments_like_number, moments_comment_number,moments_time,moments_status FROM moments WHERE moments_status = 0 ORDER BY moments_time DESC LIMIT ?,?")) {
                #out put the result
                $left = ($page - 1) * $limit;
                $right = $page * $limit;
                $stmt->bind_param("ii", $left, $right);
                $stmt->execute();
                $stmt->bind_result($user_id, $moments_comment, $moments_like_number, $moments_comment_number, $moments_time, $moments_states);
                while ($row = $stmt->fetch() && $moments_states = 0) {
                    $result["data"][] = array("user_id" => $user_id, "moments_comment" => $moments_comment, "moments_like_number" => $moments_like_number, "moments_comment_number" => $moments_comment_number, "moments_time" => $moments_time);
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