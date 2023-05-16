<?php
session_start();
require "functions.php";

// If the user is not logged in redirect to the login page...
is_logged_in();

$serverName = "localhost";
$rootUser = " ";
$rootPassowrd = " ";
$db = " ";

$con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());
$stmt = $con->prepare('SELECT is_Admin FROM customer_details WHERE username = ?');
$username = $_SESSION['name'];
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($role);
$stmt->fetch(); // Without calling fetch, there is no results. TOok some time figuring this out.
$_SESSION['role'] = $role;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page</title>
</head>

<body>
    <h1>Home Page</h1>
    <h2>Welcome back, <?= $_SESSION['name'] ?></h2>
    <br />

    <?php if (check_verified()) : ?>

        <div>
            <form id="requestEvaluationForm" action="requestEvaluation.php" method="post">
                <!-- CSRF Token -->
                <input type="hidden" name="CSRFToken" value="<?= $_SESSION['token'] ?>">
                <a href="javascript:;" onclick="document.getElementById('requestEvaluationForm').submit();">Request Evaluation</a><br>
            </form>
            <?php
            if ($role === 1) {
            echo '<form id="listEvaluationRequests" action="listEvaluationRequests.php" method="post">';
            echo '<input type="hidden" name="CSRFToken" value="' . $_SESSION['token'] . '">'; 
            echo '<a href="javascript:;" onclick="document.getElementById(\'listEvaluationRequests\').submit();">List Evaluation Requests</a><br></form>';
            }
            ?>
            <a href="logout.php">Log out</a><br>
        </div>
        Currently Logged in as <?= $username ?>
        <br>
        <br>

    <?php else : ?>
        <a href="verify.php">
            <button>Verify Email</verify>
        </a>

    <?php endif; ?>

</body>
</html>
