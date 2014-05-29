<?php
if(array_key_exists('strUsername', $arrParameters)) {

    require_once($strProjectPath . '/classes/Authentication.class.php');
    $strUsername = $arrParameters['strUsername'];
    $objAuthClass = new Authentication($strProjectPath);
    $intExistingId = $objAuthClass->getUserIdFromName($strUsername);

    $arrResponse['status'] = true;
    if(0 < $intExistingId) {
        $arrResponse['result'] = array('blnUsernameTaken' => true);
    }
    else {
        $arrResponse['result'] = array('blnUsernameTaken' => false);
    }

}
else {
    $arrResponse['error'] = 11;
}