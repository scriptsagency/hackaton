<?php

//$test_img = './uploads/poza_prel.jpg';

$resultFile = "result";
$resultFileFull = "result.txt";

function getImgText($filePath){
	$resultFile = md5($filePath);
	
	//actiune teserract
	exec('tesseract '.$fileName.' '.$resultFile);
	//$chars = str_replace(array(" ", "\n", "\r"), "", file_get_contents($resultFileFull));
	return file_get_contents($resultFile);
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

?>
