<?php
// Set content type to image
header('Content-Type: image/png');

// Get parameters
$width = isset($_GET['w']) ? (int)$_GET['w'] : 400;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 400;
$text = isset($_GET['text']) ? $_GET['text'] : 'Product Image';

// Create image
$image = imagecreatetruecolor($width, $height);

// Colors
$bg = imagecolorallocate($image, 245, 245, 245);
$textColor = imagecolorallocate($image, 150, 150, 150);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Add text
$fontSize = 5;
$textBox = imagettfbbox($fontSize, 0, 'arial', $text);
$textWidth = abs($textBox[4] - $textBox[0]);
$textHeight = abs($textBox[5] - $textBox[1]);
$x = ($width - $textWidth) / 2;
$y = ($height - $textHeight) / 2;

// Draw text
imagestring($image, $fontSize, $x, $y, $text, $textColor);

// Output image
imagepng($image);
imagedestroy($image);
?> 