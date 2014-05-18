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

    public function createNewEvent() {

    }





}