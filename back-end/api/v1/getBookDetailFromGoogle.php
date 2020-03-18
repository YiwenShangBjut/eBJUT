<?php
/**
 * Created by PhpStorm.
 * User: LeeXi
 * Date: 3/28/2019
 * Time: 10:26
 */

require 'header.php';
require 'conn.php';

$result = array();
$result['data'] = array();

if (isset($_GET['isbn'])) {
    if (strlen($_GET['isbn']) == 13) {
        $google_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $_GET['isbn'] . "&country=CN";
        $google_content = file_get_contents($google_url);
        $google_object = json_decode($google_content, true);
        echo $google_object->access_token;
        $result['data'] = $google_object;
        $result["msg"] = "OK";
        $result["code"] = 200;
        $result["extra"] = "";

    } else {
        $result["msg"] = "INVALID_FORMAT";
        $result["code"] = 400;
        $result["extra"] = "";
    }
} else {
    $result["msg"] = "MISSING_ISBN";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);
$conn->close();

?>