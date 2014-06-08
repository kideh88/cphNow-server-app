<?php
if(array_key_exists('strUsername', $arrParameters)) {
    require_once($strProjectPath . '/classes/Authentication.class.php');
    $strUsername = $arrParameters['strUsername'];
    $objAuthClass = new Authentication($strProjectPath);
    $strAppToken = $objAuthClass->getAppToken();
    $blnUserRegistered = $objAuthClass->createNewUser($strUsername, $strAppToken);

    $arrResponse['status'] = true;
    if($blnUserRegistered) {
        $arrResponse['result'] = array(
            "blnUserCreated" => true
            , "strAppToken" => $strAppToken
            , "strUsername" => $strUsername
        );
    }
    else {
        $arrResponse['result'] = array(
            "blnUserCreated" => false
            , "strAppToken" => ""
            , "strUsername" => ""
        );
    }
}
else {
    $arrResponse['error'] = 21;
}

