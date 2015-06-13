<?php

if(isset($_POST) && isset($_POST['img_name'])){


$fileName = $POST['img_name'];
$filePath = "./uploads/".$fileNamae;
$resultFile = "./uploads/result.txt";


//actiune teserract
//$imgText = exec($filePath $resultFile test);
//$chars = str_replace(array(" ", "\n", "\r"), "", file_get_contents($resultFile));







}









public function ImageToBlackAndWhite($im) {

    for ($x = imagesx($im); $x--;) {
        for ($y = imagesy($im); $y--;) {
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8 ) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r + $g + $b) / 3;
            if ($gray < 0xFF) {

                imagesetpixel($im, $x, $y, 0xFFFFFF);
            }else
                imagesetpixel($im, $x, $y, 0x000000);
        }
    }

    imagefilter($im, IMG_FILTER_NEGATE);

}

//imagefilter($im, IMG_FILTER_GRAYSCALE);
//imagefilter($im, IMG_FILTER_CONTRAST, 1000);


?>
