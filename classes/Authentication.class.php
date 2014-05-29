<?php

class Authentication {

    private $_pdo;
    private $_strTablePrefix;

    public function __construct($strProjectPath) {
        require_once($strProjectPath . '/classes/Data.class.php');
        $objDataClass = new Data($strProjectPath);
        $this->_pdo = $objDataClass->pdo();
        $this->_strTablePrefix = $objDataClass->getTablePrefix();
    }


    public function getUserIdFromName($strUsername) {
        $strCheckExistingStatement = "SELECT id FROM " . $this->_strTablePrefix . "users WHERE username LIKE :uname";
        $objExistUserPDO = $this->_pdo->prepare($strCheckExistingStatement);
        $objExistUserPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        if($objExistUserPDO->execute()) {
            $intUserId = (int)$objExistUserPDO->fetchColumn();
            return $intUserId;
        }
        else {
            return 0;
        }
    }

    public function createNewUser($strUsername, $strAppToken) {
        $strNewUserStatement = "INSERT INTO " . $this->_strTablePrefix . "users ( `username`, `app_token`, "
            . "`time_registered` ) VALUES ( :uname, :token, :time )";
        $objNewUserPDO = $this->_pdo->prepare($strNewUserStatement);

        $intRegisterTime = time();
        $objNewUserPDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':token', $strAppToken, PDO::PARAM_STR);
        $objNewUserPDO->bindValue(':time', $intRegisterTime, PDO::PARAM_INT);
        if($objNewUserPDO->execute()) {
            $intUserId = (int)$this->_pdo->lastInsertId();
            return (0 < $intUserId);
        }
        else {
            return false;
        }
    }

    public function getAppToken() {
        $strAppToken = uniqid('', true);
        if($this->_checkExistingAppToken($strAppToken)) {
            return $this->getAppToken();
        }
        else {
            return $strAppToken;
        }
    }

    private function _checkExistingAppToken($strAppToken) {
        $strCheckExistingStatement = "SELECT id FROM " . $this->_strTablePrefix . "users WHERE app_token LIKE :token";
        $objExistTokenPDO = $this->_pdo->prepare($strCheckExistingStatement);
        $objExistTokenPDO->bindValue(':token', $strAppToken, PDO::PARAM_STR);
        if($objExistTokenPDO->execute()) {
            $intTokenUserId = (int)$objExistTokenPDO->fetchColumn();
            if(0 < $intTokenUserId) {
                return true;
            }
        }
        return false;
    }

    public function authenticateAppUser($strUsername, $strAppToken) {
        $strAuthenticationStatement = "SELECT id FROM " . $this->_strTablePrefix . "users WHERE username LIKE :uname "
                . "AND app_token LIKE :token";
        $objAuthenticatePDO = $this->_pdo->prepare($strAuthenticationStatement);
        $objAuthenticatePDO->bindValue(':uname', $strUsername, PDO::PARAM_STR);
        $objAuthenticatePDO->bindValue(':token', $strAppToken, PDO::PARAM_STR);
        if($objAuthenticatePDO->execute()) {
            $intTokenUserId = (int)$objAuthenticatePDO->fetchColumn();
            if(0 < $intTokenUserId) {
                return true;
            }
        }
        return false;

    }

}