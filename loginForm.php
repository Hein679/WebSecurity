<?php
session_start();
require('ban.php');

if (check_if_banned() == true) {
    exit("Try again later.");
}

if (!isset($_SESSION['loginAttempts'])) {
    $_SESSION['loginAttempts'] = 0;
}

if ($_SESSION['loginAttempts'] >= 10) {
    $serverName = "localhost";
    $rootUser = " ";
    $rootPassowrd = " ";
    $db = " ";
    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());
    session_destroy();

    $stmt = $con->prepare('UPDATE banned_ips SET is_banned = ?, last_banned = ? WHERE ip_address = ?');
    $ip_address = get_ip();
    if ($ip_address == "") {
        exit("IP address is empty.");
    }
    $one = 1;
    $currentTime = time();
    $stmt->bind_param('iis', $one, $currentTime, $ip_address);
    $stmt->execute();

    exit("Too many login attempts, you have been locked. Try again later.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serverName = "localhost";
    $rootUser = " ";
    $rootPassowrd = " ";
    $db = " ";
    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());
    $username = htmlspecialchars(strtolower($_POST['username']));
    if (strlen($_POST["username"]) === 0 or strlen($_POST["password"]) === 0) {
        exit('Please enter username and password.');
    }

    if ($stmt = $con->prepare('SELECT id, password FROM customer_details WHERE username = ?')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $stmt->bind_param('s', $username);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();
    }

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['id'] = $id;
            unset($_SESSION['loginAttempts']);

            // Generate CSRF Token
            if (empty($_SESSION['token'])) {
                $_SESSION['token'] = bin2hex(random_bytes(32));
            }
            header('Location: home.php');
            exit();
        } else {
            // Incorrect password
            echo 'Incorrect username and/or password!';
            $_SESSION['loginAttempts'] += 1;
        }
    } else {
        // Incorrect username
        echo 'Incorrect username and/or password!';
        $_SESSION['loginAttempts'] += 1;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>

<body>
    <h1>Login Form</h1>
    <br />
    <form method="post" action="./loginForm.php">
        Username:
        <input type="text" name="username" /><br /><br />
        Password:
        <input type="password" name="password" /><br /><br />
        <input type="submit" />
    </form>
    <br />
    <br />
    <div>
        <a href="registerForm.php">Register</a><br>
        <a href="forgotPassword.php">Forgot Password</a>
    </div>
</body>

</html>