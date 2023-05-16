<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Environment variables
    $serverName = "localhost";
    $rootUser = " ";
    $rootPassowrd = " ";
    $db = " ";
    $table = "customer_details";

    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or die("Error connecting to server.");

    $username = htmlspecialchars(strtolower($_POST["username"]));
    $enter_email = strtolower($_POST["enter-email"]);
    $confirm_email = strtolower($_POST["confirm-email"]);
    $enter_password = $_POST["enter-password"];
    $confirm_password = $_POST["confirm-password"];
    $phone_number = htmlspecialchars($_POST["phone_number"]);

    $errorOccurred = 0;

    if (strlen($username) === 0) {
        // Setting the errorOccured counter not actually necessary as the script exits. However for I will still include it.
        $errorOccurred = 1;
        exit("Empty Username !<br/>");
    }

    if (!(filter_var($enter_email, FILTER_VALIDATE_EMAIL) and filter_var($confirm_email, FILTER_VALIDATE_EMAIL))) {
        $errorOccurred = 1;
        exit("Invalid Email <br/>");
    }

    if ($enter_email != $confirm_email) {
        $errorOccurred = 1;
        exit("Emails do not match <br/>");
    }

    if (!preg_match('/^[0-9]{11,15}+$/', $phone_number)) {
        $errorOccurred = 1;
        exit("Invalid Phone number <br/>");
    }

    $pattern = '/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$/';
    if (!(preg_match($pattern, $enter_password) and preg_match($pattern, $confirm_password))) {
        $errorOccurred = 1;
        exit("Password much be at least 8 characters long with at least one number, one lowercase & uppercase letter and one special character.");
    }

    if ($enter_password != $confirm_password) {
        $errorOccurred = 1;
        exit("Passwords do not match <br/>");
    } else {
        $password_Hash = password_hash($enter_password, PASSWORD_DEFAULT);
    }

    $userResult = $con->query("SELECT * FROM $table");

    while ($userRow = mysqli_fetch_array($userResult)) {
        // Check to see if the curren user' username matchs the one from the user
        if ($userRow['username'] == $username) {
            $errorOccurred = 1;
            exit("Username taken! <br/>");
        }
    }

    while ($userRow = mysqli_fetch_array($userResult)) {
        // CHeck to see if the Email entered matches with any value in the database. 
        if ($userRow['email'] == $enter_email) {
            $errorOccurred = 1;
            exit("Email in-use! <br/>");
        }
    }

    if ($errorOccurred == 0) // No error
    {

        $stmt = $con->prepare('INSERT INTO customer_details (username, password, email, phone_number) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $password_Hash, $enter_email, $phone_number);
        $stmt->execute();
        echo "<script type='text/javascript'>alert('Successfully registered');location='loginForm.php';</script>";
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Form</title>
</head>

<body>
    <h1>Register Form</h1>
    <br />
    <form method="post" action="./registerForm.php">
        Username:
        <input type="text" name="username" /><br /><br />
        Enter Password:
        <input type="password" name="enter-password" /><br /><br />
        Confirm Password:
        <input type="password" name="confirm-password" /><br /><br />
        Enter Email:
        <input type="text" name="enter-email" /><br /><br />
        Confirm Email:
        <input type="text" name="confirm-email" /><br /><br />
        Phone Number:
        <input type="text" name="phone_number" /><br /><br />
        <input type="submit" />
    </form>
    <br>
    <div>
        <a href="loginForm.php">Login</a>
    </div>
</body>

</html>