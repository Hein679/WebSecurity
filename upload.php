<?php
session_start();
require "functions.php";

is_logged_in();

if ($_POST) {
    if ((isset($_POST['captcha_challenge'])) && $_POST['captcha_challenge'] == $_SESSION['captcha_text']) {

        $write_file = fopen("listEvaluationRequests.php", "a") or exit("Something went wrong.");
        $file = $_FILES["fileToUpload"]["name"];

        if (htmlspecialchars($_POST['contact']) !== "Email" or htmlspecialchars($_POST['contact']) !== "Phone") {
            $contactMethod = "Email";
        } else {
            $contactMethod = htmlspecialchars($_POST['contact']);
        }

        // No photograph was uploaded. Just submit the method of contact and comment.
        if (!$file) {
            $txt = "\necho '<h2>Request from " . $_SESSION['name'] . "</h2>';\necho '<h4>Contact by " .
                $contactMethod
                . "</h4>';\necho '<p>" .  htmlspecialchars($_POST['comment']) .
                "</p>';";
            fwrite($write_file, $txt);
            fclose($write_file);
            echo "<script type='text/javascript'>alert('Upload Successful');location='home.php';</script>";
            exit;


            exit("File error");
        }



        if (!file_exists('uploads/' . $_SESSION['name'])) {
            mkdir('uploads/' . $_SESSION['name'] . '/', 0700, true);
        }

        $target_dir = "uploads/" . $_SESSION['name'] . '/';
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                exit("File is not an image.");
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            exit("File already exists.");
            $uploadOk = 0;
        }

        // Check file size (5Mb max)
        if ($_FILES["fileToUpload"]["size"] > 5242880) {
            exit("File is too large.");
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            exit("Only JPG, JPEG, PNG & GIF files are allowed.");
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            exit("Something went wrong. File is not uploaded.");
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $txt = "\necho '<h2>Request from " . $_SESSION['name'] . "</h2>';\necho '<h4>Contact by " .
                    $contactMethod
                    . "</h4>';\necho '<p>" .  htmlspecialchars($_POST['comment']) .
                    "</p>';\necho '<img src=\'" . $target_file . "\' alt=\'" . basename($_FILES["fileToUpload"]["name"]) . "\' width=300 height=300>';";
                fwrite($write_file, $txt);
                fclose($write_file);
                echo "<script type='text/javascript'>alert('Upload Successful');location='home.php';</script>";
                exit;
            } else {
                exit("Sorry, there was an error uploading your file.");
            }
        }
    } else {
        echo "<script type='text/javascript'>alert('Invalid Captcha');location='requestEvaluation.php';</script>";
    }
}
