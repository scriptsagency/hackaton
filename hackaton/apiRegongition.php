<?php
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

    /**
     * Send image url request
     * @return string       request token - needed to request
    */
    public function sendImage($imgUrl)
    {
        // These code snippets use an open-source library. http://unirest.io/php
        $response = Unirest\Request::post("https://camfind.p.mashape.com/image_requests",
            array(
                "X-Mashape-Key" => $this->apiKey
            ),
            array(
                "focus[x]" => "480",
                "focus[y]" => "640",
                "image_request[altitude]" => "27.912109375",
                "image_request[language]" => "en",
                "image_request[latitude]" => "35.8714220766008",
                "image_request[locale]" => "en_US",
                "image_request[longitude]" => "14.3583203002251",
                "image_request[remote_image_url]" => $imgUrl
            )
        );

        $jsonResponse = json_decode($response);

        if(!$jsonResponse || !isset($jsonResponse['token']))
            return false;

        return $jsonResponse['token'];

    }

    /**
     * Read image string from token
     * @return string | boolean     Boolean when blurry or wasn't able to read image
    */
    public function readToken($token)
    {
        $response = Unirest\Request::get("https://camfind.p.mashape.com/image_responses/{$token}",
            array(
                "X-Mashape-Key" => $this->apiKey,
                "Accept" => "application/json"
            )
        );

        $jsonRequest = json_decode($response);
        if(!$jsonRequest || isset($jsonRequest['reason']))
            return false;

        return $jsonRequest['name'];
    }

    /**
     * Read text from the image
    */
    public function readImage($imgUrl)
    {
        $token = $this->sendImage($imgUrl);
        // request failed ?
        if(!$token)
            return false;

        // image result as string
        $imgStrResult = $this->readToken($token);

        return $imgStrResult;
    }
}

if($page == "image_upload") {
    $imgUrl = "http://hackathon.fup.ro/uploads/20150613_131700.jpg";
    $api = new API;

    $imageToken = $api->readImage($imgUrl);

}
else if($page == 'phpinfo') {
    phpinfo();
}