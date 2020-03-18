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

if(isset($_POST["book_transaction_id"]))
{
    $id = $_POST['book_transaction_id'];
    if ($stmt = $conn->prepare("UPDATE books_transaction SET book_transaction_status = 1 WHERE book_transaction_id = ?"))
    {

        $stmt->bind_param("i", $id);
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
    }else{
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }
}else{
    $result["msg"] = "MISSING_MESSAGE";
    $result["code"] = 400;
    $result["extra"] = "";
}


echo json_encode($result);


$conn->close();
?>