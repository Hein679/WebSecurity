<?php

$serverName = "localhost";
$rootUser = " ";
$rootPassowrd = " ";
$db = " ";

function check_verified()
{

    global $serverName, $rootUser, $rootPassowrd, $db;
    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());
    $stmt = $con->prepare('SELECT is_verified FROM customer_details WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $_SESSION['name']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($is_verified);
    $stmt->fetch();

    if ($is_verified) {
        return true;
    }
    return false;
}

function get_email()
{
    global $serverName, $rootUser, $rootPassowrd, $db;
    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());
    $stmt = $con->prepare('SELECT email FROM customer_details WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $_SESSION['name']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($email);
    $stmt->fetch();
    return $email;
}

function is_logged_in()
{
    if (!isset($_SESSION['loggedin']) or !isset($_SESSION['token'])) {
        header('Location: loginForm.php');
        exit();
    }
}

function getRandomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}
