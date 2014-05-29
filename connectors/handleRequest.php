<?php

$strProjectPath = $_SERVER['DOCUMENT_ROOT'] . '/cphnow';
$arrResponse = array(
    "status" => false
    , "error" => 0
    , "result" => array()
);

// Request parameter check

if(!array_key_exists('request', $_REQUEST)) {
    $arrResponse['error'] = 2;
    return json_encode($arrResponse);
}
if(!array_key_exists('params', $_REQUEST) || null == json_decode($_REQUEST['params'], true)) {
    $arrResponse['error'] = 3;
    return json_encode($arrResponse);
}

// Variable definitions
$strRequest = $_REQUEST['request'];
$arrParameters = json_decode($_REQUEST['params'], true);
$strProcessorFile = $strProjectPath . '/processors/' . $strRequest . '.php';

if(file_exists($strProcessorFile)) {
    require($strProcessorFile);
}
else {
    $arrResponse['error'] = 4;
}

echo json_encode($arrResponse);