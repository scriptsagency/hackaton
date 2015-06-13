<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$page = '';
if(isset($_GET['req']))
    $page = $_GET['req'];

class API
{
    private $debug = true;

    /** @var db settings */
    protected $db;

    protected $dbHost;
    protected $dbUser;
    protected $dbPass;
    protected $dbName;

    function __construct($host = "localhost", $dbUser = "developer", $dbPass = "kAR3fCe4", $dbName = "hackathon")
    {
        $this->dbHost = $host;
        $this->dbUser = $dbUser;
        $this->dbPass = $dbPass;
        $this->dbName = $dbName;

        $this->db = new mysqli($host, $dbUser, $dbPass, $dbName);
        if($this->db->connect_error)
        {
            die("Db connection failed, please contact admin");
        }
    }

    function getImgText($filePath)
	{
		$resultFile = 'tmp/'.md5($filePath);
		
		//actiune teserract
		exec('tesseract ./uploads/'.$filePath.' '.$resultFile);
		//$chars = str_replace(array(" ", "\n", "\r"), "", file_get_contents($resultFileFull));

		return file_get_contents($resultFile);
	}
    /**
     * Returns file path as string
    */
    function upload(){
        $target_path = "uploads/";

        if(!isset($_FILES['image']['name']))
            return false;

        $target_path = $target_path.basename($_FILES['image']['name']);

        try {
            //throw exception if can't move the file
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                return false;
				//throw new Exception('Could not move file');
			}
            return $target_path;

			//echo "The file " . basename($_FILES['image']['name']) ." has been uploaded";
			//echo json_encode($_POST);
		} catch (Exception $e) {
			return false;
			//die('File did not upload: ' . $e->getMessage());
		}
	}

    public function getReviews($pid)
    {
        $sql = $this->db->query("SELECT r.`text`, r.nota, r.titlu FROM reviews r WHERE r.prod_id='".$pid."' LIMIT 5");
        $tmp = array();

        while( $r = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
            $r['text'] = utf8_encode($r['text']);
            $tmp[] = $r;
        }

        return $tmp;
    }

    /**
     *
    */
    public function getProductData($pid)
    {
        $q = $this->db->query("select * from products p where p.id=".$pid." limit 1");
        $r = mysqli_fetch_array($q);

        return $r;
    }

    /**
     * Find most common product
    */
    public function findAccurateIdProduct($arrResult)
    {
        if(count($arrResult) == 1)
            return $arrResult[0];

        $max = 0;
        $pid = 0;
        foreach($arrResult as $id => $count) {
            if($max < $count) {
                $max = $count;
                $pid = $id;
            }
        }

        return $id;
    }

    /**
     * Prepare search, split by each keyword
    */
    private function prepareSearch($str)
    {
        $arrKw = explode(" ",$str);

        return $arrKw;
    }

    /**
     * Find product by str
    */
    public function findProduct($str)
    {
        $totalResults = array();

        $arrSearch = $this->prepareSearch($str);

        foreach($arrSearch as $kw) {
            $sql = $this->db->query("select p.id from products p where p.name like '%".$kw."%' limit 10");

            if(!$sql)
                continue;

            while($r = mysqli_fetch_array($sql, MYSQLI_ASSOC)) {
                if(!isset( $totalResults[$r['id']]) )
                    $totalResults[$r['id']] = 1;
                else
                    $totalResults[$r['id']]++;
            }

        }

        // identify most common product
        $productId = $this->findAccurateIdProduct($totalResults);

        return $productId;
    }

    /**
     * Find product rating
     * @return int | boolean
    */
    public function findProductRating($pid)
    {
        $q = $this->db->query("SELECT p.nota FROM products_rating p WHERE p.prod_id='".$pid."' LIMIT 1");
//        // no rating found
//        if(!$q || mysqli_num_rows($q) < 1)
//            return false;

        $r = mysqli_fetch_array($q,MYSQLI_ASSOC);

        return $r['nota'];
    }

    public function renderStars($pid)
    {
        $rating = $this->findProductRating($pid);
        $str = "<table><tr>";
        for($i=0;$i<$rating;$i++)
        {
            $str .= "<td><img src='images/star.png'</td>";
        }
        $str .="</tr></table>";
        return $str;
    }
}


$api = new API;

if($page == "image_upload") {
    // get file path
    $filePath = $api->upload();

    // get image text from ocr
    $strText = $api->getImgText($filePath);

    // recognize product id
    $productId = $api->findProduct($search);
    if (!$productId)
        $productId = "104179";

    header("LOCATION: apiRegongition.php?req=json&productId=" . $productId);

} else if($page == 'find_product') {
    $search = $_POST['search'];

    $productId = $api->findProduct($search);
    if (!$productId)
        $productId = "104179";

    header("LOCATION: apiRegongition.php?req=json&productId=" . $productId);

}else if($page == "test") {
    phpinfo();

}else if( $page == "json") {
    $productId = $_GET['productId'];

//    $productRating = $api->findProductRating($productId);
//    $productData = $api->getProductData($productId);
//    $productReview = $api->getReviews($productId);
//
//    $arrJson['rating']          = $productRating;
//    $arrJson['name']            = $productData['name'];
//    $arrJson['short_desc']      = $productData['short_desc'];
//    $arrJson['image1']          = $productData['image1'];
//    $arrJson['image2']          = $productData['image2'];
//    $arrJson['part_number']     = $productData['part_number'];
//    $arrJson['part_number_key'] = $productData['part_number_key'];
//    $arrJson['review']          = $productReview;

    echo 'http://hackathon.fup.ro/apiRegongition.php?req=html_result&productId='.$productId;

//    echo json_encode($arrJson);

}else if( $page == "html_result"){
    $productId = $_GET['productId'];

    $productRating = $api->findProductRating($productId);
    $productData = $api->getProductData($productId);

    $renderRatingScore = $api->renderStars($productId);
    $htmlPage = '<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ShopAdvisor</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        </head>
        <body>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-9 col-md-9 col-lg-9">
                        <p>Recomandat</p>
                        <h3>'.$productData['name'].'</h3>
                        <p>'.$productData['short_desc'].'</p>
                        '.$renderRatingScore.'
                        <hr/>
                    </div>
                </div>
                <div class="row">
					<div class="col-sm-9 col-md-9 col-lg-9">
						<h3>Product video reviews</h3>
						<ul>
							<li>Image 1 - yt img preview</li>
							<li>Image 2 - yt img preview 2</li>
						</ul>
					</div>
                </div>
            </div>
        </body>
        </html>';
    echo $htmlPage;
}