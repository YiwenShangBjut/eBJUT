<?php
/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/3/28
 * Time: 10:57
 */

require 'header.php';
require 'conn.php';

$upper_limit = 50;
$result = array();
$result["data"] = array();


if (isset($_GET["limit"]) && isset($_GET["page"]) && isset($_GET["book_transaction_tag"])) {
    $limit = $_GET["limit"];
    $page = $_GET["page"];
    $tag = $_GET["book_transaction_tag"];
    if (($limit > 0 && $limit < $upper_limit) && $page > 0) {
        if ($stmt = $conn->prepare("SELECT tag_name, user_nickname, user_phone, book_transaction_title, book_transaction_detail, book_transaction_price, book_transaction_com_type, book_transaction_com_detail, book_transaction_time FROM books_transaction as tran, books_tags as tag, users WHERE tran.user_id = users.user_id AND tag.tag_id = tran.book_transaction_tag AND book_transaction_tag = ? AND book_transaction_status = 0 ORDER BY book_transaction_id DESC LIMIT ?,?")) {
            $left = ($page - 1) * $limit;
            $right = $page * $limit;
            $stmt->bind_param("iii", $tag, $left, $right);
            $stmt->execute();
            $stmt->bind_result($tag_name, $user_nickname, $user_phone, $book_transaction_title, $book_transaction_detail, $book_transaction_price, $book_transaction_com_type, $book_transaction_com_detail, $book_transaction_time);
            while ($stmt->fetch())
                $result["data"][] = array("tag_name" => $tag_name, "user_nickname" => $user_nickname, "user_phone" => $user_phone, "book_transaction_title" => $book_transaction_title, "book_transaction_detail" => $book_transaction_detail, "book_transaction_price" => $book_transaction_price, "book_transaction_com_type" => $book_transaction_com_type, "book_transaction_com_detail" => $book_transaction_com_detail, "book_transaction_time"=>$book_transaction_time);

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