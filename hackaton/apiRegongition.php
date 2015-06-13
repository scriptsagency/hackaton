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

    function __construct($host = "188.166.4.252", $dbUser = "developer", $dbPass = "kAR3fCe4", $dbName = "hackathon")
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

    public function getResult($query)
    {
        $tmp = array();
        while($r = mysqli_fetch_array($query, MYSQLI_ASSOC) ) {
            $tmp[] = $r;
        }

        return $tmp;
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
        $q = $this->db->query("SELECT p.nota AS nota from products_rating p WHERE p.id ='".$pid."' LIMIT 1");
        // no rating found
        if(!$q || mysqli_num_rows($q) < 1)
            return false;

        $r = mysqli_fetch_array($q);
        return $r['nota'];
    }

    /**
     * Find product data
     * @param int $pid
    */
    public function findProductDetails($pid)
    {
        $nota = $this->findProductRating($pid);

    }

    public function renderStars($pid)
    {
        $rating = $this->findProductRating($pid);

        $str = "<table>";
        for($i=0;$i<$rating;$i++)
        {
            $str .= "<td><img src='images/star.png'</td>";
        }

        $str .="</table>";
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
} else if($page == 'phpinfo') {
    phpinfo();
} else if($page == 'result') {

    $search = "ll Fact
cross Pro M

Cadm aâumvnlu
Eclnpare Shlmano XT r 27 vheze
Flana ludraulica disc Shlmano

Furca Sunlom | Anvelo enda
Pie! Hems' -20Â°o

IâBlllâ 3.599,â

2.19999";

    $results = $api->findProduct($search);
    // find most popular one, or first product choice
    var_dump($results);
}else if( $page == "bootstrap"){
    $productId = $_GET['productId'];

    $productRating = $api->findProductRating($productId);

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
                        <h3>Nume produs</h3>
                        <p>Descriere short...</p>
                        '.$renderRatingScore.'
                        <hr/>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        echo $htmlPage;
}