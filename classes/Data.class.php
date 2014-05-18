<?php

class Data {

    public $objPDO;
    private $_strTablePrefix;

    public function __construct($strProjectPath) {
        require($strProjectPath . '/config/config.inc.php');
        try {
            $objConnection = new PDO('mysql:host=' . $database_server . '; dbname=' . $database_name, $database_user, $database_password);
            $objConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $objConnection->exec("SET CHARACTER SET utf8"); // return all sql requests as UTF-8
            $this->objPDO = $objConnection;
            $this->_strTablePrefix = $strTablePrefix;
        }
        catch (PDOException $err) {
            return false;
        }
    }

    public function pdo() {
        return $this->objPDO;
    }

    public function getTablePrefix() {
        return $this->_strTablePrefix;
    }
}