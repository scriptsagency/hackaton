<?php


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$test_img = './uploads/poza_prel.jpg';

$resultFile = "result";



$string = getImgText($test_img, $resultFile);



function getImgText($fileName, $resultFile){

	//actiune teserract
	exec('tesseract '.$fileName.' '.$resultFile);
	$chars = str_replace(array(" ", "\n", "\r"), "", file_get_contents($resultFile ));
	
	

print_r($chars);
die('xx');
	
	
	
	return $chars;

}



function ImageToBlackAndWhite($im) {

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
