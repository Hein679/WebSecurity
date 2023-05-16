<?php

function check_if_banned()
{
    $serverName = "localhost";
    $rootUser = " ";
    $rootPassowrd = " ";
    $db = " ";
    $con = mysqli_connect($serverName, $rootUser, $rootPassowrd, $db) or exit("Error connecting to server." . mysqli_connect_error());

    $stmt = $con->prepare('SELECT * FROM banned_ips WHERE ip_address = ? LIMIT 1');

    $ip_address = get_ip();
    if ($ip_address == "") {
        exit("IP address is empty.");
    }

    $stmt->bind_param('s', $ip_address);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $ip, $is_banned, $last_bannned);
        $stmt->fetch();

        // Ban 10 mins if fail login for 10 times.
        if (time() - $last_bannned > (60*10)) {
            $stmt = $con->prepare('UPDATE banned_ips SET is_banned = ?, last_banned = NULL WHERE id = ?');
            $zero = 0;
            $stmt->bind_param('ii', $zero, $id);
            $stmt->execute();
            return false;
        } else {
            return true;
        }
    } else {
        $stmt = $con->prepare('INSERT INTO banned_ips (ip_address, is_banned) VALUES (?, ?)');
        $zero = 0;
        $stmt->bind_param('si', $ip_address, $zero);
        $stmt->execute();
        return false;
    }
}

function get_ip()
{
    $ip = "";

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if (isset($_SERVER['REMOTE_ADDR'])) {

        return $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}
