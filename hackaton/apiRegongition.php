<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$page = '';
if(isset($_GET['req']))
    $page = $_GET['req'];

class API
{
    private $apiKey = "mpQHBNe011mshFIHx5sQabAcDm2yp1cjTALjsn3eFdybOd5Frv";

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
		exec('tesseract ./uploads/'.$fileName.' '.$resultFile);
		//$chars = str_replace(array(" ", "\n", "\r"), "", file_get_contents($resultFileFull));

		return file_get_contents($resultFile);
	}

	function upload(){
		$target_path = "uploads/";

		$target_path = $target_path . basename($_FILES['image']['name']);

		try {
			//throw exception if can't move the file
			if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
				return false;
				//throw new Exception('Could not move file');
			}

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
//
//    /**
//     * Send image url request
//     * @return string|bool       request token - needed to request
//    */
//    public function sendImage($imgUrl)
//    {
//        // get image width + height
//        $size = $this->getImageSize($imgUrl);
//        if(!$size)
//            return false;
//
//        // These code snippets use an open-source library. http://unirest.io/php
//        $response = Unirest\Request::post("https://camfind.p.mashape.com/image_requests",
//            array(
//                "X-Mashape-Key" => $this->apiKey
//            ),
//            array(
//                "focus[x]" => $size['x'],
//                "focus[y]" => $size['y'],
//                "image_request[altitude]" => "27.912109375",
//                "image_request[language]" => "en",
//                "image_request[latitude]" => "35.8714220766008",
//                "image_request[locale]" => "en_US",
//                "image_request[longitude]" => "14.3583203002251",
//                "image_request[remote_image_url]" => $imgUrl
//            )
//        );
//
//        $jsonResponse = json_decode($response);
//
//        if(!$jsonResponse || !isset($jsonResponse['token']))
//            return false;
//
//        return $jsonResponse['token'];
//
//    }
//
//    /**
//     * Read image string from token
//     * @return string | boolean     Boolean when blurry or wasn't able to read image
//    */
//    public function readToken($token)
//    {
//        $response = Unirest\Request::get("https://camfind.p.mashape.com/image_responses/".$token,
//            array(
//                "X-Mashape-Key" => $this->apiKey,
//                "Accept" => "application/json"
//            )
//        );
//
//        $jsonRequest = json_decode($response);
//        if(!$jsonRequest || isset($jsonRequest['reason'])) {
//            if($this->debug)
//                var_dump($jsonRequest);
//
//            return false;
//        }
//
//        return $jsonRequest['name'];
//    }
//
//    /**
//     * Read text from the image
//    */
//    public function readImage($imgUrl)
//    {
//        $token = $this->sendImage($imgUrl);
//        // request failed ?
//        if(!$token) {
//            if(isset($this->debug))
//                echo "Token failed!".PHP_EOL;
//
//            return false;
//        }
//
//        // image result as string
//        $imgStrResult = $this->readToken($token);
//
//        return $imgStrResult;
//    }
//
//    /**
//     * Get image size
//     * @return array    array( x,y )
//    */
//    public function getImageSize($imgUrl)
//    {
//        $size = getimagesize($imgUrl);
//        if(!isset($size[1]))
//        {
//            if(isset($this->debug))
//                echo "Image size failed!".PHP_EOL;
//
//            return false;
//        }
//
//        // return sizes
//        return array(
//            'x'    =>  $size[0],
//            'y'    =>  $size[1]
//        );
//    }
}


$api = new API;

if($page == "image_upload") {
//    $start = microtime(true);
//
//    $imgUrl = "http://hackathon.fup.ro/uploads/20150613_131700.jpg";
//    $api = new API;
//
//    $strResult = $api->readImage($imgUrl);
//    echo $strResult;
//
//    $stop = microtime(true) - $start;
//
//    echo "took: $stop";
} else if($page == 'find_product') {
    $search = $_POST['search'];

    $productId = $api->findProduct($search);
    if (!$productId)
        $productId = "104179";

    header("LOCATION: apiRegongition.php?req=json&productId=" . $productId);

}else if($page == "test"){
    phpinfo();
    
}else if( $page == "json") {
    $productId = $_GET['productId'];

    echo $productId;

    $productRating = $api->findProductRating($productId);
    $productData = $api->getProductData($productId);
    $productReview = $api->getReviews($productId);

    $arrJson['rating']          = $productRating;
    $arrJson['name']            = $productData['name'];
    $arrJson['short_desc']      = $productData['short_desc'];
    $arrJson['image1']          = $productData['image1'];
    $arrJson['image2']          = $productData['image2'];
    $arrJson['part_number']     = $productData['part_number'];
    $arrJson['part_number_key'] = $productData['part_number_key'];
    $arrJson['review']          = $productReview;

    echo json_encode($arrJson);
}