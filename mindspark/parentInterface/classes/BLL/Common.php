<?php

namespace BLL;

use DAL;

//use DAL\ParentDetail\getParentDetails;
require_once 'BaseClass.php';
//require_once('../constants.php');
//require_once(DIR_BASE .'db_credentials.php');
require_once DIR_DAL . 'Common.php';
require_once DIR_DAL . 'UserDetail.php';
require_once DIR_DAL . 'FreeTrialDetail.php';

class Common extends BaseClass {
    public $db;
    public function __construct($db, $parentUserID = null) {
        $this->db=$db;
    }

    public function checkIfEmailExist($emailID) {
        $found = 0;
        if ($emailID != '') {
            $userDetails = new DAL\UserDetail($this->db);
            $users = $userDetails->getUserDetailsByEmail($emailID);
            if (count($users) > 0)
                $found = 1;
            else {
                $common = new DAL\Common($this->db);
                $registrations = $common->getDetailsByEmail($emailID);
                if(count($registrations)>0)
                    $found=2;
            }
        }
        return $found;
    }
    
    public function addChildVerificationDetail($sessionID, $studentName, $userName, $schoolName, $childClass, $city, $dobFormatted) {
        $common = new DAL\Common($this->db);
        return $common->addChildVerificationDetail($sessionID, $studentName, $userName, $schoolName, $childClass, $city, $dobFormatted);
    }

    public function addChangeLog($tableChanged, $columnChanged, $identifier, $oldValue, $newValue, $changeComment, $modifiedBy) {
        $common = new DAL\Common($this->db);
        return $common->addChangeLog($tableChanged, $columnChanged, $identifier, $oldValue, $newValue, $changeComment, $modifiedBy);        
    }
    
    public function addMobileUsage($parentID, $sessionID) {
        $common = new DAL\Common($this->db);
        return $common->addMobileUsage($parentID, $sessionID);
    }
    
    public function addEmailUnsubscribe($emailID) {
        $common = new DAL\Common($this->db);
        return $common->addEmailUnsubscribe($emailID);
    }
    
    public function extendFreeTrial($userID) {
        $userDetail = new DAL\UserDetail($this->db);
        $userDetail->extendFreeTrial($userID);
        $freeTrialDetail = new DAL\FreeTrialDetail($this->db);
        return $freeTrialDetail->extendFreeTrial($userID);
    }
    
    public function getFreeTrialDetail($userID) {
        $freeTrialDetail = new DAL\FreeTrialDetail($this->db);
        return $freeTrialDetail->getFreeTrialDetail($userID);
    }
    
    public function getClassMPIScore($schoolCode, $class, $date) {
        $common = new DAL\Common($this->db);
        return $common->getClassMPIScore($schoolCode, $class, $date);        
    }
}
