<?php

/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/4/13
 * Time: 11:12
 */

require 'header.php';
require 'conn.php';
$upper_limit = 50;
$result = array();
$result["data"] = array();


if (isset($_GET["limit"]) && isset($_GET["page"])) {
    $limit = $_GET["limit"];
    $page = $_GET["page"];
    if (($limit > 0 && $limit < $upper_limit) && $page > 0) {
        if ($stmt = $conn->prepare("SELECT item_tag_name, user_nickname, user_phone, item_transaction_title, item_transaction_detail, item_transaction_price, item_transaction_com_type, item_transaction_com_detail, item_transaction_time FROM items_transaction as tran, items_tags as tag, users WHERE tran.user_id = users.user_id AND tag.item_tag_id = tran.item_transaction_tag AND item_transaction_status = 0 ORDER BY item_transaction_id DESC LIMIT ?,?")) {
            $left = ($page - 1) * $limit;
            $right = $page * $limit;
            $stmt->bind_param("ii", $left, $right);
            $stmt->execute();
            $stmt->bind_result($item_tag_name, $user_nickname, $user_phone, $item_transaction_title, $item_transaction_detail, $item_transaction_price, $item_transaction_com_type, $item_transaction_com_detail, $item_transaction_time);
            while ($stmt->fetch())
                $result["data"][] = array("tag_name" => $item_tag_name, "user_nickname" => $user_nickname, "user_phone" => $user_phone, "item_transaction_title" => $item_transaction_title, "item_transaction_detail" => $item_transaction_detail, "item_transaction_price" => $item_transaction_price, "item_transaction_com_type" => $item_transaction_com_type, "item_transaction_com_detail" => $item_transaction_com_detail, "item_transaction_time"=> $item_transaction_time);

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
    $result["msg"] = "MISSING_LIMIT";
    $result["code"] = 400;
    $result["extra"] = [];
}


echo json_encode($result);

$conn->close();

?>