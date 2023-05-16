<?php
session_start();
require "functions.php";

is_logged_in();

if (!empty($_POST['CSRFToken'])) {
    if (hash_equals($_SESSION['token'], $_POST['CSRFToken'])) {
        // echo "May proceed";
    } else {
        exit("Invalid CSRF Token");
    }
} else {
    exit("Invalid CSRF Token");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Request Evaluation</title>
    <script>
        var loadPreviewImage = function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('previewImg');
                document.getElementById('previewImg').height = 300;
                document.getElementById('previewImg').width = 300;
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
</head>

<body>
    <h1>Request Evaluation</h1>

    <form method="post" action="upload.php" enctype="multipart/form-data">

        Preferred mode of contact:
        <select name="contact">
            <option value="email">Email</option>
            <option value="phone">Phone</option>
        </select>
        <br>
        <br>
        <textarea rows="4" cols="50" name="comment" placeholder="Describe yourself here..."></textarea>
        <br>
        <br>
        Select image to upload:
        <input type="file" name="fileToUpload" accept="image/gif, image/jpeg, image/png" onChange="loadPreviewImage(event)" />
        <br>
        <br>
        <img id="previewImg" />
        <br>
        <br>
        <div>
            <label for="captcha">Please Enter the Captcha Text</label><br>
            <img src="captcha.php" alt="CAPTCHA" class="captcha-image">
            <br>
            <input type="text" id="captcha" name="captcha_challenge" pattern="[a-zA-Z0-9]{5}">
        </div>
        <br>
        <input type="submit" value="submit" name="submit" />
    </form>

    <br />
    <br />
    <div>
        <a href="home.php">Home Page</a>
        <a href="logout.php">Log out</a>
    </div>
</body>


</html>