<?php
session_start();
require "mail.php";
require "functions.php";

is_logged_in();

$serverName = "localhost";
$rootUser = " ";
$rootPassowrd = " ";
$db = " ";
$con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());


if ($_SERVER['REQUEST_METHOD'] == "GET" && !check_verified()) {

    // Send mail
    $code =  rand(10000, 99999);

    $stmt = $con->prepare('UPDATE customer_details SET verify_code = ? WHERE username = ?');
    $stmt->bind_param('is', $code, $_SESSION['name']);
    $stmt->execute();

    $message = "your code is " . $code;
    $subject = "Email verification";
    $recipient = get_email();
    send_mail($recipient, $subject, $message);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (check_verified() == false) {
        $stmt = $con->prepare('SELECT verify_code FROM customer_details WHERE username = ?');
        $stmt->bind_param('s', $_SESSION['name']);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($verify_code);
        $stmt->fetch();

        $entered_code = $_POST['code'];

        if ($entered_code == $verify_code) {
            $stmt = $con->prepare('UPDATE customer_details SET is_verified = 1, verify_code = NULL WHERE username = ?');
            $stmt->bind_param('s', $_SESSION['name']);
            $stmt->execute();
            echo "<script type='text/javascript'>alert('Successfully Verified');location='home.php';</script>";
            exit();
        }
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify</title>
</head>

<body>
    <h1>Verify Email</h1>
    <br>
    <br>
    <div>
        <form method="post" action="verify.php">
            <input type="text" name="code" placeholder="Enter your Code"><br>
            <br>
            <input type="submit">
        </form>
    </div>

</body>

</html>