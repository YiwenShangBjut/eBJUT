<?php
/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/3/28
 * Time: 10:57
 */

require 'header.php';
require 'conn.php';
$result = array();

if (isset($_POST["user_id"]) && isset($_POST["book_isbn"]) && isset($_POST["book_transaction_tag"]) && isset($_POST["book_transaction_price"]) && isset($_POST["book_transaction_title"]) && isset($_POST["book_transaction_com_type"])) {

    $id = $_POST["user_id"];
    $isbn = $_POST["book_isbn"];
    $tag = $_POST["book_transaction_tag"];
    $price = $_POST["book_transaction_price"];
    $title = $_POST["book_transaction_title"];
    $com_type = $_POST["book_transaction_com_type"];
    if (isset($_POST["book_transaction_details"]))
        $detail = $_POST["book_transaction_details"];
    else
        $detail = null;
    if (isset($_POST["book_transaction_com_details"]))
        $com_detail = $_POST["book_transaction_com_details"];
    else
        $com_detail = null;

    $flag = 0;

    if ($stmt = $conn->prepare("SELECT COUNT(book_transaction_id) AS c FROM books_transaction WHERE user_id = ? AND book_transaction_title= ? and book_transaction_status = 0")) {
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

    if ($flag == 0) {
        if ($stmt = $conn->prepare("INSERT INTO books_transaction (user_id, book_isbn, book_transaction_tag, book_transaction_price, book_transaction_title, book_transaction_detail, book_transaction_com_type, book_transaction_com_detail) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")) {
            $stmt->bind_param("isisssis", $id, $isbn, $tag, $price, $title, $detail, $com_type, $com_detail);
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
    $stmt->free_result();
    $stmt->close();
} else {
    $result["msg"] = "MISSING_ARGUMENTS";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);

$conn->close();
?>