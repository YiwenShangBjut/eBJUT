<?php
/**
 * Created by PhpStorm.
 * User: LeeXi
 * Date: 3/21/2019
 * Time: 10:19
 */

$hostname = "localhost";
$username = "root";
$password = "Uc67t6iU6j9R0PvV";
$database = "ebjut";

$conn = new mysqli($hostname, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$conn->close();

?>