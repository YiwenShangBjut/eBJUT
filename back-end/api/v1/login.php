<?PHP

require 'header.php';
require 'conn.php';

$result = array();
$result['data'] = array();

if (isset($_POST['user_login']) && isset($_POST['user_password'])) {
    $user_login = $_POST['user_login'];
    $user_password_raw = $_POST['user_password'];

    // fetch user_salt by user_login
    if ($stmt = $conn->prepare("SELECT user_salt FROM users WHERE ? in (user_login, user_phone)")) {
        $stmt->bind_param("s", $user_login);
        $stmt->execute();
        $stmt->bind_result($user_salt);

        if ($stmt->fetch()) {
            // user exist
            $stmt->close();

            if ($stmt = $conn->prepare("SELECT * FROM users WHERE ? in (user_login, user_phone) AND user_password = ?")) {

                $stmt->bind_param("ss", $user_login, md5(md5($user_password_raw . $user_salt) . $user_salt));
                $stmt->execute();

                if ($stmt->fetch()) {
                    $result["msg"] = "OK";
                    $result["code"] = 200;
                    $result["extra"] = "";
                } else {
                    $result["msg"] = "UNAUTHENTICATED";
                    $result["code"] = 401;
                    $result["extra"] = "";
                }
            }
        }
        else {
            // user not exist
            $stmt->close();

            $result["msg"] = "USER_NOT_EXISTS";
            $result["code"] = 401;
            $result["extra"] = $user_salt;
        }



    } else {
        $result["msg"] = "BAD_GATEWAY";
        $result["code"] = 502;
        $result["extra"] = "";
    }
} else {
    $result["msg"] = "MISSING_ARGUMENTS";
    $result["code"] = 400;
    $result["extra"] = "";
}

echo json_encode($result);

$stmt->close();
$conn->close();

?>