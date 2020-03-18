<?php
/**
 * Created by PhpStorm.
 * User: LeeXi
 * Date: 3/21/2019
 * Time: 16:27
 */

require 'header.php';
require 'conn.php';

$upper_limit = 50;

$result = array();
$result["data"] = array();

if ((isset($_GET["limit"]))) {
    if (isset($_GET["page"])) {
        if ($_GET["limit"] > 0 && $_GET["limit"] <= $upper_limit && $_GET["page"] > 0) {
            $limit = $_GET['limit'];
            $page = $_GET["page"];

            if ($stmt = $conn->prepare("SELECT * FROM news WHERE news_department LIKE ? AND news_category LIKE ? ORDER BY news_publish_date DESC LIMIT ?,?")) {
                $left = ($page - 1) * $limit;
                $right = $page * $limit;
                $department = isset($_GET['department']) ? $_GET['department'] : "%";
                $category = isset($_GET['category']) ? $_GET['category'] : "%";

                $stmt->bind_param("ssii", $department, $category, $left, $right);
                $stmt->execute();
                $stmt->bind_result($id, $title, $department, $publish_date, $is_external, $external_url, $category, $has_image, $has_attachment, $content);
                while ($stmt->fetch()) {
                    # Remove poster useless content
                    if (($category == "海报" || $category == "科技动态") && strlen($content) < 150) {
                        $content = null;
                    }
                    $result["data"][] = array("title" => $title, "date" => $publish_date, "department" => $department, "isExternal" => (bool)$is_external, "externalURL" => $external_url, "category" => $category, "hasImage" => (bool)$has_image, "hasAttachment" => (bool)$has_attachment, "content" => $content);

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