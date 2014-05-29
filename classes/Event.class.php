<?php

class Event {

    private $_pdo;
    private $_strTablePrefix;

    public function __construct($strProjectPath) {
        require_once($strProjectPath . '/classes/Data.class.php');
        $objDataClass = new Data($strProjectPath);
        $this->_pdo = $objDataClass->pdo();
        $this->_strTablePrefix = $objDataClass->getTablePrefix();
    }

    public function createNewEvent($strEventName, $intUserId, $fltLatitude, $fltLongitude, $intEventType, $intEventTime,
                                   $intEventDuration, $intEventFee, $blnMusic, $blnDrinks, $blnFood, $intPeople) {

        $intTimePosted = time();
        $strNewUserStatement = "INSERT INTO " . $this->_strTablePrefix . "events ( `name`, `cphnow_users_fk`, "
            . "`latitude`, `longitude`, `event_type`, `time_posted`, `time_start`, `time_duration`, `entrance_fee`, "
            . "`music`, `bar`, `food`, `estimated_people` ) VALUES ( :name, :userid, :lat, :long, :type, :posted, "
            . ":start, :duration, :fee, :music, :bar, :food, :people)";
        $objNewUserPDO = $this->_pdo->prepare($strNewUserStatement);

        $intRegisterTime = time();
        $objNewUserPDO->bindValue(':name', $strEventName, PDO::PARAM_STR);
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


    public function getEventList() {

    }

    public function filterEventRange() {

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
            return ($miles * 1.609344 * 1000);
        }else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }




}