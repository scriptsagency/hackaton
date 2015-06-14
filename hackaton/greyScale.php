<?php
header('Content-Type: image/jpeg');


$imgPath = "uploads/20150613_131327.jpg";
$im = imagecreatefromjpeg($imgPath);

imagefilter($im, IMG_FILTER_GRAYSCALE);
imagefilter($im, IMG_FILTER_NEGATE);
imagefilter($im, IMG_FILTER_BRIGHTNESS, 50);
imagefilter($im, IMG_FILTER_CONTRAST, -70);
// Rotate


//imagefilter($im, IMG_FILTER_GRAYSCALE);
imagejpeg($im);
imagedestroy($im);