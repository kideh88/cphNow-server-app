<?php

class Data {

    public $objPDO;
    private $_strTablePrefix;

    // Contructor creates a PDO connection using config.inc.php's settings
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

    // Returns the connection
    public function pdo() {
        return $this->objPDO;
    }

    // Returns the database table prefix used for the project
    public function getTablePrefix() {
        return $this->_strTablePrefix;
    }
}

