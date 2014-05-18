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

/* MOVE SHIT TO PROCESSORS
if(!array_key_exists('authUser', $_REQUEST) || !array_key_exists('authToken', $_REQUEST)) {
    $arrResponse['error'] = 1;
    return json_encode($arrResponse);
}
$strUsername = $_REQUEST['authUser'];
$strAppToken = $_REQUEST['authToken'];
require_once($strProjectPath . '/classes/Authentication.class.php');
// Class definitions
$objAuthClass = new Authentication($strProjectPath);
$objAuthClass->validateUser($strUsername, $strAppToken)
*/

// Variable definitions
$strRequest = $_REQUEST['request'];
$arrParameters = json_decode($_REQUEST['params'], true);
$strProcessorFile = $strProjectPath . '/processors/' . $strRequest . '.php';

if(file_exists($strProcessorFile)) {
    require($strProcessorFile);
}

echo json_encode($arrResponse);