<?php

/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/4/11
 * Time: 19:44
 */

require 'header.php';
require 'conn.php';
$result = array();

if (isset($_POST["user_id"]) && isset($_POST["item_transaction_tag"]) && isset($_POST["item_transaction_title"]) && isset($_POST["item_transaction_price"]) && isset($_POST["item_transaction_com_type"])) {

    $id = $_POST["user_id"];
    $tag = $_POST["item_transaction_tag"];
    $price = $_POST["item_transaction_price"];
    $title = $_POST["item_transaction_title"];
    $com_type = $_POST["item_transaction_com_type"];
    if (isset($_POST["item_transaction_details"]))
        $detail = $_POST["item_transaction_details"];
    else
        $detail = null;
    if (isset($_POST["item_transaction_com_details"]))
        $com_detail = $_POST["item_transaction_com_details"];
    else
        $com_detail = null;

    $flag = 0;

    if ($stmt = $conn->prepare("SELECT COUNT(item_transaction_id) AS c FROM items_transaction WHERE user_id = ? AND item_transaction_title= ? and item_transaction_status = 0")) {
        $stmt->bind_param("is", $id, $title);
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

        if ($stmt = $conn->prepare("INSERT INTO items_transaction (user_id, item_transaction_tag, item_transaction_price, item_transaction_title, item_transaction_detail, item_transaction_com_type, item_transaction_com_detail) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param("iisssis", $id, $tag, $price, $title, $detail, $com_type, $com_detail);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $result["msg"] = "CREATED";
                $result["code"] = 201;
                $result["extra"] = "";
            } else {
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
