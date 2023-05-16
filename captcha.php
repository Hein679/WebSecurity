<?php
session_start();
$permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

function gen_captcha_text($input, $str_len = 5, $secure = true)
{
    $input_length = strlen($input);
    $rand_str = '';
    for ($i = 0; $i < $str_len; $i++) {
        if ($secure) {
            $rand_char = $input[random_int(0, $input_length - 1)];
        } else {
            $rand_char = $input[mt_rand(0, $input_length - 1)];
        }
        $rand_str .= $rand_char;
    }

    return $rand_str;
}

// Make Captcha background
$colors = [];
$red = rand(125, 175);
$green = rand(125, 175);
$blue = rand(125, 175);
$img = imagecreatetruecolor(200, 50);

imageantialias($img, true);

for ($i = 0; $i < 5; $i++) {
    $colors[] = imagecolorallocate($img, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
}

imagefill($img, 0, 0, $colors[0]);

for ($i = 0; $i < 10; $i++) {
    imagesetthickness($img, rand(2, 10));
    $rect_color = $colors[rand(1, 4)];
    imagerectangle($img, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
}

// Background img$img test
// header('Content-type: img$img/png');
// imagepng($img);
// imagedestroy($img);

// Uncomment extension=gd in php.ini
// Test extension for imagecreatetruecolor
// $testGD = get_extension_funcs("gd"); // Grab function list 
// if (!$testGD) {
//     echo "GD not even installed.";
//     exit;
// }
// echo "<pre>" . print_r($testGD, true) . "</pre>";

// phpinfo();

$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 255, 255, 255);
$text_colors = [$black, $white];

$fonts = [dirname(__FILE__) . '/fonts/Acme-Regular.ttf', dirname(__FILE__) . '/fonts/Merriweather-Light.ttf', dirname(__FILE__) . '/fonts/PlayfairDisplay-SemiBoldItalic.ttf', dirname(__FILE__) . '/fonts/Ubuntu-Medium.ttf'];

$string_length = 5;
$captcha_string = gen_captcha_text($permitted_chars, $string_length, true);
$_SESSION['captcha_text'] = $captcha_string;

for ($i = 0; $i < $string_length; $i++) {
    $letter_space = 170 / $string_length;
    $initial = 15;

    imagettftext($img, 30, rand(-15, 15), $initial + $i * $letter_space, rand(25, 50), $text_colors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
}

header('Content-type: img$img/png');
imagepng($img);
imagedestroy($img);
