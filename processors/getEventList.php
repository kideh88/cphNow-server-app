<?php
if(array_key_exists('strUsername', $arrParameters) && array_key_exists('strAppToken', $arrParameters)) {

    require_once($strProjectPath . '/classes/Authentication.class.php');
    $strUsername = $arrParameters['strUsername'];
    $strAppToken = $arrParameters['strAppToken'];

    $objAuthClass = new Authentication($strProjectPath);
    $blnUserValid = $objAuthClass->authenticateAppUser($strUsername, $strAppToken);

    if($blnUserValid) {
        $arrResponse['status'] = true;

        require_once($strProjectPath . '/classes/Event.class.php');
        $objEventClass = new Event($strProjectPath);

        $intTimeMax = (int)$arrParameters['intMaxDays'];
        $intDistanceMax = (int)$arrParameters['intMaxDistance'];

        $blnShowParty = (bool)$arrParameters['blnShowParty'];
        $blnShowMarket = (bool)$arrParameters['blnShowMarket'];
        $blnShowShow = (bool)$arrParameters['blnShowShow'];
        $blnShowAction = (bool)$arrParameters['blnShowAction'];

        $arrCurrentLocation = array(
            "dblLatitude" => doubleval($arrParameters['dblCurrentLatitude'])
            , "dblLongitude" => doubleval($arrParameters['dblCurrentLongitude'])
        );

        $arrTypeConditions = array();
        $strTypeWhereClause = "";
        if($blnShowParty) {
            $arrTypeConditions[] = 0;
        }
        if($blnShowMarket) {
            $arrTypeConditions[] = 1;
        }
        if($blnShowShow) {
            $arrTypeConditions[] = 2;
        }
        if($blnShowAction) {
            $arrTypeConditions[] = 3;
        }
        if(count($arrTypeConditions) > 0) {
            $strTypeWhereClause = "( ";
            foreach($arrTypeConditions as $intKey => $intEventType) {
                $strTypeWhereClause .= $intEventType;
                if(array_key_exists(($intKey+1), $arrTypeConditions)) {
                    $strTypeWhereClause .= ", ";
                }
            }
            $strTypeWhereClause .= " )";
            $arrActiveEvents = $objEventClass->getEventList($intTimeMax, $intDistanceMax,
                                                            $strTypeWhereClause, $arrCurrentLocation);
        }
        else {
            $arrActiveEvents = array("blnFilterError" => true);
        }

        $arrResponse['result'] = $arrActiveEvents;

    }
    else {
        $arrResponse['status'] = false;
    }

}
else {
    $arrResponse['error'] = 31;
}