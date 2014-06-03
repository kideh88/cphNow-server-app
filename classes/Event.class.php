<?php

class Event {

    private $_pdo;
    private $_strTablePrefix;

    private $_arrDurationConvert = array(1800, 3600, 7200, 18000, 36000, 86400);
    private $_arrRangeConvert = array(250, 500, 750, 1000, 5000);

    public function __construct($strProjectPath) {
        require_once($strProjectPath . '/classes/Data.class.php');
        $objDataClass = new Data($strProjectPath);
        $this->_pdo = $objDataClass->pdo();
        $this->_strTablePrefix = $objDataClass->getTablePrefix();
    }

    public function createNewEvent($strEventName, $strEventDescription, $intUserId, $fltLatitude, $fltLongitude,
            $intEventType, $intEventTime, $intEventDuration, $intEventFee, $blnMusic, $blnDrinks, $blnFood, $intPeople) {

        $intTimePosted = time();
        $strNewUserStatement = "INSERT INTO " . $this->_strTablePrefix . "events ( `name`, `description`, `cphnow_users_fk`, "
            . "`latitude`, `longitude`, `event_type`, `time_posted`, `time_start`, `time_duration`, `entrance_fee`, "
            . "`music`, `bar`, `food`, `estimated_people` ) VALUES ( :name, :desc, :userid, :lat, :long, :type, :posted, "
            . ":start, :duration, :fee, :music, :bar, :food, :people)";
        $objNewUserPDO = $this->_pdo->prepare($strNewUserStatement);

        $intEventDuration = $this->_arrDurationConvert[$intEventDuration];
        $objNewUserPDO->bindValue(':name', $strEventName, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':desc', $strEventDescription, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':userid', $intUserId, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':lat', strval($fltLatitude), PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':long', strval($fltLongitude), PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':type', $intEventType, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':posted', $intTimePosted, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':start', $intEventTime, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':duration', $intEventDuration, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':fee', $intEventFee, PDO::PARAM_INT);
        $objNewUserPDO->bindValue(':music', $blnMusic, PDO::PARAM_BOOL);
        $objNewUserPDO->bindValue(':bar', $blnDrinks, PDO::PARAM_BOOL);
        $objNewUserPDO->bindValue(':food', $blnFood, PDO::PARAM_BOOL);
        $objNewUserPDO->bindValue(':people', $intPeople, PDO::PARAM_INT);
        if($objNewUserPDO->execute()) {
            $intUserId = (int)$this->_pdo->lastInsertId();
            return (0 < $intUserId);
        }
        else {
            return false;
        }

    }
    public function getEventList($intTimeMax, $intRangeMax, $mixWhereType, $arrCurrentLocation) {
        $intTimestamp = time();
        $intTimeLimit = $this->_calculateTimestampLimit($intTimeMax);
        $strEventStatement = "SELECT `cne`.`id`, `cne`.`name`, `cne`.`description`, `cne`.`latitude`, "
            . "`cne`.`longitude`, `cne`.`event_type`, `cne`.`time_posted`, `cne`.`time_start`, "
            . "`cne`.`time_duration`, `cne`.`entrance_fee`, `cne`.`music`, `cne`.`bar`, `cne`.`food`, "
            . "`cne`.`estimated_people`, `cnu`.`username` FROM ". $this->_strTablePrefix . "events AS cne "
            . "LEFT JOIN ". $this->_strTablePrefix . "users AS `cnu` ON `cne`.`cphnow_users_fk` = `cnu`.`id` "
            . "WHERE (`cne`.`time_start` + `cne`.`time_duration`) > :time ";
        if($mixWhereType !== false) {
            $strEventStatement .= "AND `cne`.`event_type` IN " . $mixWhereType . " ";
        }
        if($intTimeLimit !== false) {
            $strEventStatement .= "AND `cne`.`time_start` < :timelimit ";
        }
        $strEventStatement .= "ORDER BY `cne`.`time_start` ASC";

        $objEventPDO = $this->_pdo->prepare($strEventStatement);
        $objEventPDO->bindValue(':time', $intTimestamp, PDO::PARAM_INT);
        if($intTimeLimit !== false) {
            $objEventPDO->bindValue(':timelimit', $intTimeLimit, PDO::PARAM_INT);
        }
        if($objEventPDO->execute()) {
            $arrResults = $objEventPDO->fetchAll(PDO::FETCH_ASSOC);
            if(0 < count($arrResults)) {
                if($intRangeMax < 5) {
                    $arrResults = $this->_prepareResponseArray($arrResults, $arrCurrentLocation, $this->_arrRangeConvert[$intRangeMax]);
                }
                else {
                    $arrResults = $this->_prepareResponseArray($arrResults, $arrCurrentLocation, false);
                }
                return $arrResults;
            }
        }
        return array();

    }



    private function _prepareResponseArray($arrEvents,  $arrCurrentLocation, $mixRange) {
        $dblUserLat = $arrCurrentLocation['dblLatitude'];
        $dblUserLon = $arrCurrentLocation['dblLongitude'];
        $arrResponse = array();
        foreach($arrEvents as $intKey => $arrEventData) {
            $dblEventLat = doubleval($arrEventData['latitude']);
            $dblEventLon = doubleval($arrEventData['longitude']);
            $intDistance = $this->_calculatePointDistance($dblEventLat, $dblEventLon, $dblUserLat, $dblUserLon, "M");
            if($mixRange !== false && $intDistance > $mixRange) {
                continue;
            }
            $arrSortedData = array(
                "intEventId" => (int)$arrEventData['id']
                , "strEventName" => $arrEventData['name']
                , "strEventDescription" => $arrEventData['description']
                , "strUsername" => $arrEventData['username']
                , "dblLatitude" => $dblEventLat
                , "dblLongitude" => $dblEventLon
                , "intEventType" => (int)$arrEventData['event_type']
                , "intTimePosted" => (int)$arrEventData['time_posted']
                , "intEventTime" => (int)$arrEventData['time_start']
                , "intEventDuration" => array_search((int)$arrEventData['time_duration'], $this->_arrDurationConvert)
                , "intEventFee" => (int)$arrEventData['entrance_fee']
                , "blnMusic" => (bool)$arrEventData['music']
                , "blnDrinks" => (bool)$arrEventData['bar']
                , "blnFood" => (bool)$arrEventData['food']
                , "intPeople" => (int)$arrEventData['estimated_people']
                , "intEventDistance" => $intDistance
            );
            $arrResponse[$intKey] = $arrSortedData;
        }

        usort($arrResponse, function($a, $b) {
            return $a['intEventDistance'] - $b['intEventDistance'];
        });
        return $arrResponse;

    }

    private function _calculatePointDistance($lat1, $lon1, $lat2, $lon2, $unit) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        }
        else if ($unit == "M") {
            return $this->_ceiling(($miles * 1.609344 * 1000), 50);
        }else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }


    private function _calculateTimestampLimit($intMaxDays) {
        switch($intMaxDays) {
            case 0:
                // Case Today
                $intTimeMax = strtotime('+1 day midnight');
                break;

            case 1:
                // Case 2 Days
                $intTimeMax = strtotime('+2 day midnight');
                break;

            case 2:
                // Case 5 Days
                $intTimeMax = strtotime('+5 day midnight');
                break;

            case 3:
                // Case 1 Week
                $intTimeMax = strtotime('+1 week midnight');
                break;

            case 4:
                // Case 2 Weeks
                $intTimeMax = strtotime('+2 week midnight');
                break;
            case 5:
                // Case All
                $intTimeMax = false;
                break;
            default:
                $intTimeMax = false;
                break;
        }
        return $intTimeMax;

    }

    private function _ceiling($intNumber, $intFullRound = 1)
{
    return ( is_numeric($intNumber) && is_numeric($intFullRound) ) ? (ceil($intNumber/$intFullRound)*$intFullRound) : false;
}



}