<?php
/**
 * Created by PhpStorm.
 * User: vuv
 * Date: 10/14/2016
 * Time: 3:15 PM
 */
include "hodlibs/hodclient.php";

$client = new HODClient("YOUR-HOD-APIKEY");

class POI {
    public $reference = "";
    public $category = "";
    public $lat = 0;
    public $lon = 0;
}
$hodApp = HODApps::ADD_TO_TEXT_INDEX;
$newPOI = new POI();
$newPOI->reference = $_REQUEST['ref'];
$newPOI->lat = $_REQUEST['lat'];
$newPOI->lon = $_REQUEST['lon'];
$newPOI->category = $_REQUEST['cat'];
$json = json_encode($newPOI);

$paramArr = array(
    'index' => "havensearch",
    'url' => $_REQUEST['ref'],
    'additional_metadata' => $json
);

try {
    $client->PostRequest($paramArr, $hodApp, REQ_MODE::SYNC, 'requestCompleted');
} catch (Exception $ex) {
    error_log("Post Exception: " . $ex->getMessage() . ". Code: " . $ex->getCode());
    echo $ex->getMessage();
}

function requestCompleted($data) {
    echo $data;
}
