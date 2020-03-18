<?php
/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 4/13/2019
 * Time: 23:27
 */

header('Content-type:text/json');
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

            if ($stmt = $conn->prepare("SELECT user_id, forums_title, forums_content, forums_timestamp,forums_status,forums_comments_number FROM forums WHERE forums_status = 0 ORDER BY forums_timestamp DESC LIMIT ?,?")) {
                #out put the result
                $left = ($page - 1) * $limit;
                $right = $page * $limit;
                $stmt->bind_param("ii", $left, $right);
                $stmt->execute();
                $stmt->bind_result($user_id, $forums_title, $forums_content, $forums_timestamp,$forums_status,$forums_comments_number);
                while ($row = $stmt->fetch() && $forums_status = 0) {
                    $result["data"][] = array("user_id" => $user_id, "forums_title" => $forums_title, "forums_content" => $forums_content, "forums_timestamp" => $forums_timestamp, "forums_status" => $forums_status,"forums_comments_number" => $forums_comments_number);
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