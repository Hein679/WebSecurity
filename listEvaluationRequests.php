<?php
session_start();
require "functions.php";

is_logged_in();

if ($_SESSION['role'] !== 1) {
    header('Location: home.php');
    exit();
}

if (!empty($_POST['CSRFToken'])) {
    if (hash_equals($_SESSION['token'], $_POST['CSRFToken'])) {
        // echo "May proceed";
    } else {
        exit("Invalid CSRF Token");
    }
} else {
    exit("Invalid CSRF Token");
}

echo "<title>Evaluation Requests</title>";
echo "<h1>Evaluation Requests</h1>";
echo '<a href="home.php">Home</a><br>';

