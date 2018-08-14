<?php

namespace BLL;
use DAL;
//use DAL\ParentDetail\getParentDetails;
require_once 'BaseClass.php';
//require_once('../constants.php');
//require_once(DIR_BASE .'db_credentials.php');
require_once DIR_DAL . 'ParentSession.php';

class ParentSession extends BaseClass {

    public $sessionID;
    public $parentUserID;
    public $parentEmail;
    public $provider;
    public $students;
    public $startTime;

    public function __construct($db, $sessionID = null) {
        $this->db = $db;
        if($sessionID!=NULL)
        {
            $parentSession = new DAL\ParentSession($this->db);
            $data = $parentSession->getParentSesssionDetails($sesionID);
            $detail = $data[0];
            $this->sessionID = $detail['sessionID'];
            $this->parentUserID = $detail['parentUserID'];
            $this->parentEmail = $detail['parentEmail'];
            $this->provider = $detail['provider'];
            $this->students = $detail['students'];
            $this->startTime = $detail['startTime'];
        }
        $this->sessionID = null;
        $this->parentUserID = null;
        $this->parentEmail = null;
        $this->provider = null;
        $this->students = null;
        $this->startTime = null;
    }

    public function saveParentStatus() {
        $parentSession = new DAL\ParentSession($this->db);
        return $parentSession->saveParentStatus($this->parentUserID, $this->parentEmail, $this->provider, $this->students, $this->startTime);        
    }

}
