<?php
ini_set("display_errors","On");
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$page = '';
if(isset($_GET['req']))
    $page = $_GET['req'];

/**
 *
*/
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
            $sql = $this->db->query("select * from products p where p.name like '%".$str."%' limit 10");
            $resultArray = $this->getResult($sql);

            $totalResults[] = $resultArray;
        }

        return $totalResults;
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
    var_dump($results);
}