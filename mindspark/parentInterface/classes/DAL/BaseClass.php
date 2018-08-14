<?php

//require_once(dirname(__FILE__) .'/db_credentials.php');

namespace DAL;
if (!class_exists('db')) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/techmCodeCommit/mindsparkProduct/mindspark/parentInterface/classes/class.db.php');
}

class BaseClass {

    var $db;

    public function __construct($db) {
        $this->db=$db;
//        if ($data == 'MASTER') {
//            $db = new \db('MASTER', 'educatio_adepts');
//            $db->setErrorCallbackFunction('dbErrorReport');
////        $db->errorCallbackFunction=dbErrorReport;
//            $this->db = $db;
//        }
//        if ($data == 'slave') {
//            require_once(dirname(__FILE__) . '/slaveConnection.php');
//            $this->db = $sdb;
//        }
    }

    public function dbErrorReport($msg) {
        echo 'test' . $msg;
        exit;
    }

}
