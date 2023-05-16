<?php
session_start();

$serverName = "localhost";
$rootUser = " ";
$rootPassowrd = " ";
$db = " ";
$con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());

if ($_SERVER['REQUEST_METHOD'] == "GET" and isset($_GET['email']) and isset($_GET['passwd_token'])) {
    $email = filter_var(base64_decode($_GET['email']), FILTER_SANITIZE_EMAIL);
    $passwd_token = filter_var($_GET['passwd_token'], FILTER_SANITIZE_SPECIAL_CHARS);

    $stmt = $con->prepare('SELECT change_passwd_token FROM customer_details WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($passwd_db_passwd_token);
    $stmt->fetch();

    if ($passwd_token == $passwd_db_passwd_token) {
        $_SESSION['email'] = $email;
        $_SESSION['passwd_token'] = $passwd_token;
    } else {
        exit("Invalid details. Please try again.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (!isset($_POST['passwd_token'])) {
        exit("Invalid, Please try again.");
    }

    if ($_POST['passwd_token'] !== $_SESSION['passwd_token']) {
        exit("Invalid, Please try again.");
    }

    $enter_password = $_POST['enter_password'];
    $confirm_password = $_POST['confirm_password'];

    $pattern = '/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/';
    if (!(preg_match($pattern, $enter_password) and preg_match($pattern, $confirm_password))) {
        exit("Password much be at least 8 characters long with at least one number, one lowercase & uppercase letter and one special character.");
    }

    if ($enter_password != $confirm_password) {
        exit("Passwords do not match <br/>");
    } else {
        $password_Hash = password_hash($enter_password, PASSWORD_DEFAULT);
    }

    $stmt = $con->prepare('UPDATE customer_details SET password = ?, change_passwd_token = NULL WHERE email = ?');
    $stmt->bind_param('ss', $password_Hash, $_SESSION['email']);
    $stmt->execute();

    echo "<script type='text/javascript'>alert('Password Changed Successfully');location='loginForm.php';</script>";
    session_destroy();
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password</title>
</head>

<body>

    <h1>Change Password</h1>
    <p>Password Policy: Password must be at least 8 characters long with at least one number, one lowercase & uppercase letter and one special character.</p>

    <form method="post" action="./changePassword.php">
        Enter New Password:
        <input type="password" name="enter_password" /><br /><br />
        Confirm New Password:
        <input type="password" name="confirm_password" /><br /><br />
        <input type="hidden" name="passwd_token" value="<?= $_SESSION['passwd_token'] ?>">
        <input type="submit" />
    </form>


</body>

</html>