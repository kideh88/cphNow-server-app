<?php
if(array_key_exists('strUsername', $arrParameters) && array_key_exists('strAppToken', $arrParameters)) {

    require_once($strProjectPath . '/classes/Authentication.class.php');
    $strUsername = $arrParameters['strUsername'];
    $strAppToken = $arrParameters['strAppToken'];

    $objAuthClass = new Authentication($strProjectPath);
    $blnUserValid = $objAuthClass->authenticateAppUser($strUsername, $strAppToken);

    if($blnUserValid) {
        $arrResponse['status'] = true;

//        $arrActiveEvents =

        $arrResponse['result'] = array(
            array(
                "strEventName" => "Party 1"
                , "strEventDescription" => "Party at dronning louises bro"
            )
            , array(
                "strEventName" => "Party 2"
                , "strEventDescription" => "Party at fælledparken"
            )
            , array(
                "strEventName" => "Party 3"
                , "strEventDescription" => "Party at nørrebro"
            )
            , array(
                "strEventName" => "Party 4"
                , "strEventDescription" => "Party at frederiksberg"
            )
            , array(
                "strEventName" => "Party 5"
                , "strEventDescription" => "Party at Kgs. Have"
            )
        );

    }
    else {
        $arrResponse['status'] = false;
    }

}
else {
    $arrResponse['error'] = 31;
}