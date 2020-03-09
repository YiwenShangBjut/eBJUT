<?php

/**
 * Created by PhpStorm.
 * User: 方心韵
 * Date: 2019/4/13
 * Time: 16:51
 */
require 'header.php';
require 'conn.php';
$result = array();

if(isset($_POST["lost_card_id"]))
{
    $id = $_POST['lost_card_id'];
    if ($stmt = $conn->prepare("UPDATE lost_student_card SET lost_card_status = 1 WHERE lost_card_id = ?"))
    {

        $stmt->bind_param("i", $id);
        $stmt->execute();
        if($stmt->affected_rows > 0)
        {
            $result["msg"] = "CREATED";
            $result["code"] = 201;
            $result["extra"] = "";
        }else{
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

