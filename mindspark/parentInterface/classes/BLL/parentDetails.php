<?php

namespace BLL;

use DAL;

//use DAL\ParentDetail\getParentDetails;
require_once 'BaseClass.php';
//require_once('../constants.php');
//require_once(DIR_BASE .'db_credentials.php');
require_once DIR_DAL . 'parentDetails.php';
require_once DIR_DAL . 'UserDetail.php';
require_once DIR_DAL . 'ParentSession.php';
require_once DIR_DAL . 'ParentChildMapping.php';

class ParentDetails extends BaseClass {

    public $db;
    public $parentUserID;
    public $username;
    public $firstName;
    public $lastName;
    public $password;
    public $loginType;
    public $phoneNumber;
    public $city;
    public $state;
    public $country;
    public $address;
    public $registrationDate;
    public $enabled;
    public $verified;
    public $modifiedBy;
    public $lastModified;

    public function __construct($db, $parentUserID = null) {
        $this->db = $db;
        if ($parentUserID != NULL) {
            $parentDetail = new DAL\ParentDetail($this->db);
            $data = $parentDetail->getParentDetails($parentUserID);
            $details = $data[0];            
            $this->parentUserID = $details['parentUserID'];
            $this->username = $details['username'];
            $this->firstName = $details['firstName'];
            $this->lastName = $details['lastName'];
            $this->password = $details['password'];
            $this->loginType = $details['loginType'];
            $this->phoneNumber = $details['phoneNumber'];
            $this->city = $details['city'];
            $this->state = $details['state'];
            $this->country = $details['country'];
            $this->address = $details['address'];
            $this->registrationDate = $details['registrationDate'];
            $this->enabled = $details['enabled'];
            $this->verified = $details['verified'];
            $this->modifiedBy = $details['modifiedBy'];
            $this->lastModified = $details['lastModified'];
        } else {
            $this->parentUserID = NULL;
            $this->username = NULL;
            $this->firstName = NULL;
            $this->lastName = NULL;
            $this->password = NULL;
            $this->loginType = NULL;
            $this->phoneNumber = NULL;
            $this->city = NULL;
            $this->state = NULL;
            $this->country = NULL;
            $this->address = NULL;
            $this->registrationDate = NULL;
            $this->enabled = NULL;
            $this->verified = NULL;
            $this->modifiedBy = NULL;
            $this->lastModified = NULL;
        }
    }

    public function addParentDetails() {
//        $parentSession = new DAL\ParentSession();
        $parentDetail = new DAL\ParentDetail($this->db);
        $parentUserID = $parentDetail->addParentDetails($this->username, $this->firstName, $this->lastName, $this->password, $this->loginType, $this->phoneNumber, $this->city, $this->state, $this->country, $this->address, $this->registrationDate, $this->enabled, $this->verified, $this->modifiedBy);
//        echo $parentUserID;
        if ($parentUserID > 0 && $this->verified) {
            $this->parentUserID = $parentUserID;
            $parentSession = new DAL\ParentSession($this->db);
            $parentSession->updateParentUserID($this->username, $this->parentUserID);
            $userDetails = new DAL\UserDetail($this->db);
            $userIDs = $userDetails->getUsersMappedToEmail($this->username);
            $userIDs = array_slice($userIDs, 0, 3);

//            var_dump($userIDs);
            foreach ($userIDs as $userID) {
                $tmpUserID = $userID['userID'];
                $parentChildMapping = new DAL\ParentChildMapping($this->db);
                $parentChildMapping->addParentUserMapping($tmpUserID, $this->parentUserID);
                $userDetail = new DAL\userDetail($this->db);
                $userDetail->removeParentEmail($tmpUserID, $this->username);
            }
        }
        return $parentUserID;
    }

    public function verifyParent() {
            $parentDetail = new DAL\ParentDetail($this->db);
            $parentDetail->verifyParentUserName($this->username);
            $parentSession = new DAL\ParentSession($this->db);
            $parentSession->updateParentUserID($this->username, $this->parentUserID);
            $userDetails = new DAL\UserDetail($this->db);
            $userIDs = $userDetails->getUsersMappedToEmail($this->username);
            $userIDs = array_slice($userIDs, 0, 3);
            foreach ($userIDs as $userID) {
                $tmpUserID = $userID['userID'];
                $parentChildMapping = new DAL\ParentChildMapping($this->db);
                $parentChildMapping->addParentUserMapping($tmpUserID, $this->parentUserID);
                $userDetail = new DAL\userDetail($this->db);
                $userDetail->removeParentEmail($tmpUserID, $this->username);
            }
    }
    
    public function mapChild($userID, $removeParentEmail = true, $relation = '') {
        $parentChildMapping = new DAL\ParentChildMapping($this->db);
        $parentChildMapping->addParentUserMapping($userID, $this->parentUserID, $relation);
        if ($removeParentEmail) {
            $userDetail = new DAL\userDetail($this->db);
            $userDetail->removeParentEmail($userID, $this->username);
        }
    }

    public function getParentDetailsByEmail($emailID) {
        if ($emailID != '') {
            $parentChildMapping = new DAL\ParentDetail($this->db);
            return $parentChildMapping->getParentDetailsByEmail($emailID);
        } else
            return array();
    }

    public function verifyParentUsername($username) {
        if ($emailID != '') {
            $parentDetail = new DAL\ParentDetail($this->db);
            $success = $parentDetail->verifyParentUserName($emailID);
            $detail = $this->getParentDetailsByEmail($emailID);
            $parentUserID = $detail[0]['parentUserID'];
            $username = $emailID;
            if ($success == true) {
                $parentSession = new DAL\ParentSession($this->db);
                $parentSession->updateParentUserID($username, $parentUserID);
                $userDetails = new DAL\UserDetail($this->db);
                $userIDs = $userDetails->getUsersMappedToEmail($username);
//            var_dump($userIDs);
                foreach ($userIDs as $userID) {
                    $tmpUserID = $userID['userID'];
                    $parentChildMapping = new DAL\ParentChildMapping($this->db);
                    $parentChildMapping->addParentUserMapping($tmpUserID, $parentUserID);
                    $userDetail = new DAL\userDetail($this->db);
                    $userDetail->removeParentEmail($tmpUserID, $username);
                }
            } else
                return false;
        } else
            return false;
    }

    public function getChildrenMapped() {
        if ($this->parentUserID > 0) {
            $parentChildMapping = new DAL\ParentChildMapping($this->db);
            return $parentChildMapping->getUsers($this->parentUserID);
        } else
            return array();
    }

    public function updatePassword() {
        if ($this->parentUserID > 0) {
            $parentDetail = new DAL\ParentDetail($this->db);
            return $parentDetail->updatePassword($this->parentUserID, $this->password);            
        } else
            return false;
    }
}
