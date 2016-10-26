<?php
/**
 * Created by PhpStorm.
 * User: vuv
 * Date: 6/18/2016
 * Time: 10:37 AM
 */
include "hodlibs/hodclient.php";

$client = new HODClient("YOUR-HOD-APIKEY");

$hodApp = HODApps::QUERY_TEXT_INDEX;
$fieldtext = "(DISTSPHERICAL{".$_REQUEST["lat"].",".$_REQUEST["lon"].",".$_REQUEST["rad"]." in KM}:lat:lon)";
if ($_REQUEST['cat'] != "all")
    $fieldtext .= " AND (MATCH{".$_REQUEST['cat']."}:category)";

$paramArr = array(
    'indexes' => "havensearch",
    'text' => "*",
    'field_text' => $fieldtext,
    'summary' => "quick",
    'print_fields' => "category,lat,lon",
    'absolute_max_results' => 40
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