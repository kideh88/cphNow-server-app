<?php
if(array_key_exists('strUsername', $arrParameters) && array_key_exists('strAppToken', $arrParameters)) {

    require_once($strProjectPath . '/classes/Authentication.class.php');
    $strUsername = $arrParameters['strUsername'];
    $strAppToken = $arrParameters['strAppToken'];

    $objAuthClass = new Authentication($strProjectPath);
    $blnUserValid = $objAuthClass->authenticateAppUser($strUsername, $strAppToken);

    if($blnUserValid) {
        $arrResponse['status'] = true;
//        $arrResponse['result'];

        $strEventName = $arrParameters['strEventName'];
        $intUserId = (int)$objAuthClass->getUserIdFromName($strUsername);
        $fltLatitude = $arrParameters['dblLatitude'];
        $fltLongitude = $arrParameters['dblLongitude'];
        $intEventType = $arrParameters['intEventType'];
        $intEventTime = $arrParameters['intEventTime'];
        $intEventDuration = $arrParameters['intEventDuration'];
        $intEventFee = $arrParameters['intEventFee'];
        $blnMusic = $arrParameters['blnMusic'];
        $blnDrinks = $arrParameters['blnDrinks'];
        $blnFood = $arrParameters['blnFood'];
        $intPeople = $arrParameters['intPeople'];

        require_once($strProjectPath . '/classes/Event.class.php');
        $objEventClass = new Event($strProjectPath);
        $blnCreated = $objEventClass->createNewEvent($strEventName, $intUserId, $fltLatitude, $fltLongitude,
            $intEventType, $intEventTime, $intEventDuration, $intEventFee, $blnMusic, $blnDrinks, $blnFood, $intPeople);

        if($blnCreated) {
            $arrResponse['result'] = array("blnEventCreated" => true);
        }
        else {
            $arrResponse['result'] = array("blnEventCreated" => false);
            $arrResponse['error'] = 43;
        }
    }
    else {
        $arrResponse['error'] = 42;
    }

}
else {
    $arrResponse['error'] = 41;
}