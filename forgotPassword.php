<?php
session_start();
require "mail.php";
require 'functions.php';

$serverName = "localhost";
$rootUser = " ";
$rootPassowrd = " ";
$db = " ";
$con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());

const APP_URL = ' ';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email_b64 = base64_encode(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $rand_chars = getRandomString(20);


    $stmt = $con->prepare('UPDATE customer_details SET change_passwd_token = ? WHERE email = ?');
    $stmt->bind_param('ss', $rand_chars, $email);
    $stmt->execute();

    $change_passwd_link = APP_URL . "/changePassword.php?email=$email_b64&passwd_token=$rand_chars";

    $message = "Change password link " . $change_passwd_link;
    $subject = "Change Password";
    $recipient = $email;
    send_mail($recipient, $subject, $message);
    echo "<script type='text/javascript'>alert('Rest link sent. Check your email');location='loginForm.php';</script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password</title>
</head>

<body>

    <h1>Forgot Password</h1>

    <form method="post" action="./forgotPassword.php">
        Enter Email:
        <input type="text" name="email" /><br /><br />
        <input type="submit" />
    </form>


</body>

</html>