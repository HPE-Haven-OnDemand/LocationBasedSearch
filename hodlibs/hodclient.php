<?php

/**
 * Created by PhpStorm.
 * User: vuv
 * Date: 10/5/2015
 * Time: 3:53 PM
 */
interface REQ_MODE
{
    const ASYNC = "async";
    const SYNC = "sync";
}

class HODClient
{
    private $apiKey = '';
    private $ver;
    private $hodAppBase = 'https://api.havenondemand.com/1/api/';
    private $hodJobResultBase = "https://api.havenondemand.com/1/job/result/";
    private $hodJobStatusBase = "https://api.havenondemand.com/1/job/status/";
    private $hodCombineAsync = "async/executecombination";
	private $hodCombineSync = "sync/executecombination";
    private $requestTimeout = 600;
    private $mime_boundary = "";
    function HODClient($apiKey, $version = "v1") {
        $this->apiKey = $apiKey;
        $this->ver = "/".$version;
    }
	public function setAPIKey($newkey) {
		$this->apiKey = $newkey;
	}
    public function GetJobResult($jobID, $callback) {
        $param = $this->hodJobResultBase;
        $param .= $jobID;
        $param .= "?apikey=" . $this->apiKey;
        try {
            $ch = curl_init($param);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);

            //execute post
            $strResponse = curl_exec($ch);
           //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }
    public function GetJobStatus($jobID, $callback) {
        $param = $this->hodJobStatusBase;
        $param .= $jobID;
        $param .= "?apikey=" . $this->apiKey;
        try {
            $ch = curl_init($param);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);

            //execute post
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }
    public function GetRequest($paramArr, $hodApp, $mode, $callback)
    {
        $app = "";
        if ($mode == "sync") {
            $app .= $this->hodAppBase . "sync/" . $hodApp . $this->ver;
        } else {
            $app .= $this->hodAppBase . "async/" . $hodApp . $this->ver;
        }
        $param = $app;
        $param .= "?apikey=" . $this->apiKey;
        //
        foreach($paramArr as $key => $value) {
            if ($key == "file") {
                error_log("HODClient Error: Invalid parameter\n");
                throw new Exception("Failed. File upload must be used with PostRequest method", UPLOAD_ERR_NO_FILE);
            }
            $type = gettype($value);
            if ($type == "array") {
                foreach($value as $kk => $vv) {
                    $param .= "&" . $key . "=" . rawurlencode($vv);
                }
            } else {
                $param .= "&". $key."=" . rawurlencode($value);
            }
        }
        try {
            $ch = curl_init($param);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);

            //execute post
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }
    public function GetCombinationRequest($paramArr, $hodApp, $mode, $callback)
    {
        $queryStr = $this->hodAppBase;
        if ($mode == "sync") {
            $queryStr .= $this->hodCombineSync . $this->ver;
        } else {
            $queryStr .= $this->hodCombineAsync . $this->ver;
        }
        $queryStr .= "?apikey=" . $this->apiKey;
        $queryStr .= "&combination=" . $hodApp;
        foreach($paramArr as $key => $value) {
            if ($key == "file") {
                error_log("HODClient Error: Invalid parameter\n");
                throw new Exception("Failed. File upload must be used with PostRequest method", UPLOAD_ERR_NO_FILE);
            }
            $type = gettype($value);
            if ($type == "array") {
                foreach($value as $kk => $vv) {
                    $queryStr .= '&parameters={"name":"'.$key.'","value":"'.rawurlencode($vv).'"}';
                }
            } else {
                $queryStr .= '&parameters={"name":"'.$key.'","value":"'.rawurlencode($value).'"}';
            }
        }
        error_log($queryStr);
        try {
            $ch = curl_init($queryStr);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);

            //execute post
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }
    public function PostRequest($paramArr, $hodApp, $mode, $callback)
    {
        $app = "";
        if ($mode == "sync") {
            $app .= $this->hodAppBase . "sync/" . $hodApp . $this->ver;
        } else {
            $app .= $this->hodAppBase . "async/" . $hodApp . $this->ver;
        }
        $this->mime_boundary = md5(time());
        $param = $this->packData($paramArr);
        $header = array('Content-Type: multipart/form-data; boundary=' . $this->mime_boundary);
        try {
            $ch = curl_init($app);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

            //execute post
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }

    private function packData($paramArr) {
        $eol = "\r\n";
        $boundary = '--' . $this->mime_boundary;
        /*
        $data = $boundary . $eol;
        $data .= 'Content-Disposition: form-data; name="apikey"' . $eol . $eol;
        $data .= $this->apiKey . $eol;
        */
        $data = $this->postDataField("apikey", $this->apiKey);
        foreach($paramArr as $key => $value) {
            $type = gettype($value);
            //error_log("Param type: " . $type);
            if ($type == "array") {
                foreach($value as $kk => $vv) {
                    if ($key == "file") {
                        $fileName = $vv;
                        //$fileSize = filesize($fileName);
                        ini_set('memory_limit', '-1');
                        if(!file_exists($fileName)) {
                            error_log("HODClient Error: " . $fileName . " does not exist.");
                            throw new Exception('File not found.', UPLOAD_ERR_NO_FILE);
                        }
                        $mime = mime_content_type($fileName);

                        $data .= $boundary . $eol;
                        $data .= 'Content-Disposition: form-data; name="'.$key.'"; filename="'.$value.'"' . $eol;
                        $data .= 'Content-Type: '. $mime . $eol . $eol;

                        //$handle = fopen($fileName, "rb");
                        //$contents = fread($handle, $fileSize);
                        $contents = file_get_contents($fileName);
                        $data .= $contents . $eol;
                        //fclose($handle);
                    } else {
                        /*
                        $data .= $boundary . $eol;
                        $data .= 'Content-Disposition: form-data; name="'.$key.'"' . $eol . $eol;
                        $data .= $vv . $eol;
                        */
                        $data .= $this->postDataField($key, $vv);
                    }
                }
            } else {
                if ($key == "file") {
                    $fileName = $value;
                    //$fileSize = filesize($fileName);
                    ini_set('memory_limit', '-1');
                    if(!file_exists($fileName)) {
                        error_log("HODClient Error: " . $fileName . " does not exist.");
                        throw new Exception('File not found.', UPLOAD_ERR_NO_FILE);
                    }
                    $mime = mime_content_type($fileName);

                    $data .= $boundary . $eol;
                    $data .= 'Content-Disposition: form-data; name="'.$key.'"; filename="'.$value.'"' . $eol;
                    $data .= 'Content-Type: '. $mime . $eol . $eol;

                    //$handle = fopen($fileName, "rb");
                    //$contents = fread($handle, $fileSize);
                    $contents = file_get_contents($fileName);
                    $data .= $contents . $eol;
                    //fclose($handle);
                } else {
                    /*
                    $data .= $boundary . $eol;
                    $data .= 'Content-Disposition: form-data; name="'.$key.'"' . $eol . $eol;
                    $data .= $value . $eol;
                    */
                    $data .= $this->postDataField($key, $value);
                }
            }
        }
        $data .= $boundary . $eol;
        return $data;
    }
    public function PostCombinationRequest($paramArr, $hodApp, $mode, $callback)
    {
        $endPoint = $this->hodAppBase;
        if ($mode == "sync") {
            $endPoint .= $this->hodCombineSync . $this->ver;
        } else {
            $endPoint .= $this->hodCombineAsync . $this->ver;
        }

        $this->mime_boundary = md5(time());
        $param = $this->packCombinationData($paramArr, $hodApp);
        error_log($endPoint);
        error_log($param);
        $header = array('Content-Type: multipart/form-data; boundary=' . $this->mime_boundary);
        try {
            $ch = curl_init($endPoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

            //execute post
            $strResponse = curl_exec($ch);
            //Get the Error Code returned by Curl.
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                $curlError = curl_error($ch);
                error_log("HODClient Error: " . $curlError);
                throw new Exception($curlError, $curlErrno);
            }
            curl_close($ch);
            $callback($strResponse);
        } catch (Exception $e) {
            error_log("HODClient Exception: " . $e->getMessage());
            throw new Exception($e);
        }
    }
    private function packCombinationData($paramArr, $hodApp) {
        $eol = "\r\n";
        $boundary = '--' . $this->mime_boundary;
        /*
        $data = $boundary . $eol;
        $data .= 'Content-Disposition: form-data; name="apikey"' . $eol . $eol;
        $data .= $this->apiKey . $eol;

        $data .= $boundary . $eol;
        $data .= 'Content-Disposition: form-data; name="combination"' . $eol . $eol;
        $data .= $hodApp . $eol;
        */
        $data = $this->postDataField("apikey", $this->apiKey);
        $data .= $this->postDataField("combination", $hodApp);

        foreach($paramArr as $key => $value) {
            $type = gettype($value);
            //error_log("Param type: " . $type);
            if ($type == "array") {
                foreach($value as $kk => $vv) {
                    if ($key == "file") {
                        $fileName = $vv;
                        //$fileSize = filesize($fileName);
                        ini_set('memory_limit', '-1');
                        if(!file_exists($fileName)) {
                            error_log("HODClient Error: " . $fileName . " does not exist.");
                            throw new Exception('File not found.', UPLOAD_ERR_NO_FILE);
                        }
                        $mime = mime_content_type($fileName);

                        $data .= $boundary . $eol;
                        $data .= 'Content-Disposition: form-data; name="'.$key.'"; filename="'.$value.'"' . $eol;
                        $data .= 'Content-Type: '. $mime . $eol . $eol;

                        //$handle = fopen($fileName, "rb");
                        //$contents = fread($handle, $fileSize);
                        $contents = file_get_contents($fileName);
                        $data .= $contents . $eol;
                        //fclose($handle);
                    } else {
                        /*
                        $data .= $boundary . $eol;
                        $data .= 'Content-Disposition: form-data; name="parameters"' . $eol . $eol;
                        $data .= '{"name":"'.$key.'","value":"'.$vv.'"}';
                        $data .= $eol;
                        */
                        $data .= $this->postDataField("parameters", '{"name":"'.$key.'","value":"'.$vv.'"}');
                    }
                }
            } else {
                if ($key == "file") {
                    $fileName = $value;
                    //$fileSize = filesize($fileName);
                    ini_set('memory_limit', '-1');
                    if(!file_exists($fileName)) {
                        error_log("HODClient Error: " . $fileName . " does not exist.");
                        throw new Exception('File not found.', UPLOAD_ERR_NO_FILE);
                    }
                    $mime = mime_content_type($fileName);

                    $data .= $boundary . $eol;
                    $data .= 'Content-Disposition: form-data; name="'.$key.'"; filename="'.$value.'"' . $eol;
                    $data .= 'Content-Type: '. $mime . $eol . $eol;

                    //$handle = fopen($fileName, "rb");
                    //$contents = fread($handle, $fileSize);
                    $contents = file_get_contents($fileName);
                    $data .= $contents . $eol;
                    //fclose($handle);
                } else {
                    /*
                    $data .= $boundary . $eol;
                    $data .= 'Content-Disposition: form-data; name="parameters"' . $eol . $eol;
                    $data .= '{"name":"'.$key.'","value":"'.$value.'"}';
                    $data .= $eol;
                    */
                    $data .= $this->postDataField("parameters", '{"name":"'.$key.'","value":"'.$value.'"}');
                }
            }
        }
        $data .= $boundary . $eol;
        return $data;
    }
    private function postDataField($key, $value)
    {
        $eol = "\r\n";
        $boundary = '--' . $this->mime_boundary;
        $data = $boundary . $eol;
        $data .= 'Content-Disposition: form-data; name="'.$key.'"' . $eol . $eol;
        $data .= $value . $eol;
        return $data;
    }
}
interface HODApps
{
    const RECOGNIZE_SPEECH = "recognizespeech";
    const DETECT_SCENE_CHANGES = "detectscenechanges";
    const LICENSE_PLATE_RECOGNITION = "licenseplaterecognition";

    const CANCEL_CONNECTOR_SCHEDULE = "cancelconnectorschedule";
    const CONNECTOR_HISTORY = "connectorhistory";
    const CONNECTOR_STATUS = "connectorstatus";
    const CREATE_CONNECTOR = "createconnector";
    const DELETE_CONNECTOR = "deleteconnector";
    const RETRIEVE_CONFIG = "retrieveconfig";
    const START_CONNECTOR = "startconnector";
    const STOP_CONNECTOR = "stopconnector";
    const UPDATE_CONNECTOR = "updateconnector";

    const EXPAND_CONTAINER = "expandcontainer";
    const STORE_OBJECT = "storeobject";
    const EXTRACT_TEXT = "extracttext";
    const VIEW_DOCUMENT = "viewdocument";

    const MAP_COORDINATES = "mapcoordinates";

    const OCR_DOCUMENT = "ocrdocument";
    const RECOGNIZE_BARCODES = "recognizebarcodes";
    const DETECT_FACES = "detectfaces";
    const RECOGNIZE_IMAGES = "recognizeimages";

    const GET_COMMON_NEIGHBORS = "getcommonneighbors";
    const GET_NEIGHBORS = "getneighbors";
    const GET_NODES = "getnodes";
    const GET_SHORTEST_PATH = "getshortestpath";
    const GET_SUB_GRAPH = "getsubgraph";
    const SUGGEST_LINKS = "suggestlinks";
    const SUMMARIZE_GRAPH = "summarizegraph";

    const CREATE_CLASSIFICATION_OBJECTS = "createclassificationobjects";
    const CREATE_POLICY_OBJECTS = "createpolicyobjects";
    const DELETE_CLASSIFICATION_OBJECTS = "deleteclassificationobjects";
    const DELETE_POLICY_OBJECTS = "deletepolicyobjects";
    const RETRIEVE_CLASSIFICATION_OBJECTS = "retrieveclassificationobjects";
    const RETRIEVE_POLICY_OBJECTS = "retrievepolicyobjects";
    const UPDATE_CLASSIFICATION_OBJECTS = "updateclassificationobjects";
    const UPDATE_POLICY_OBJECTS = "updatepolicyobjects";

    const PREDICT = "predict";
    const RECOMMEND = "recommend";
    const TRAIN_PREDICTOR = "trainpredictor";

    const CREATE_QUERY_PROFILE = "createqueryprofile";
    const DELETE_QUERY_PROFILE = "deletequeryprofile";
    const RETRIEVE_QUERY_PROFILE = "retrievequeryprofile";
    const UPDATE_QUERY_PROFILE = "updatequeryprofile";

    const FIND_RELATED_CONCEPTS = "findrelatedconcepts";
    const FIND_SIMILAR = "findsimilar";
    const GET_CONTENT = "getcontent";
    const GET_PARAMETRIC_VALUES = "getparametricvalues";
    const QUERY_TEXT_INDEX = "querytextindex";
    const RETRIEVE_INDEX_FIELDS = "retrieveindexfields";

    const AUTO_COMPLETE = "autocomplete";
    const CLASSIFY_DOCUMENT = "classifydocument";
    const EXTRACT_CONCEPTS = "extractconcepts";
    const CATEGORIZE_DOCUMENT = "categorizedocument";
    const ENTITY_EXTRACTION = "extractentities";
    const EXPAND_TERMS = "expandterms";
    const HIGHLIGHT_TEXT = "highlighttext";
    const IDENTIFY_LANGUAGE = "identifylanguage";
    const ANALYZE_SENTIMENT = "analyzesentiment";
    const GET_TEXT_STATISTICS = "gettextstatistics";
    const TOKENIZE_TEXT = "tokenizetext";

    const ADD_TO_TEXT_INDEX = "addtotextindex";
    const CREATE_TEXT_INDEX = "createtextindex";
    const DELETE_TEXT_INDEX = "deletetextindex";
    const DELETE_FROM_TEXT_INDEX = "deletefromtextindex";
    const INDEX_STATUS = "indexstatus";
    //const LIST_INDEXES = "listindexes"; REMOVED
    const LIST_RESOURCES = "listresources";
    const RESTORE_TEXT_INDEX = "restoretextindex";
}
?>