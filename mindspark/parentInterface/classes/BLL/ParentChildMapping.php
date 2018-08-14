<?php

namespace BLL;
use DAL;
//use DAL\ParentDetail\getParentDetails;
require_once 'BaseClass.php';
//require_once('../constants.php');
//require_once(DIR_BASE .'db_credentials.php');
require_once DIR_DAL . 'ParentChildMapping.php';

class ParentChildMapping extends BaseClass {

    public $parentUserID;
    public $childUserID;

    public function __construct($db) {
        $this->db = $db;
        $this->parentUserID = null;
        $this->childUserID = null;
    }

    public function addUserMapping() {
        $parentChildMapping = new DAL\ParentChildMapping($this->db);
        return $parentChildMapping->addParentUserMapping($this->childUserID, $this->parentUserID);
    }

}
