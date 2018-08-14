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

class UserDetail extends BaseClass {

    public $db;
    public $userID;
    public $username;
    public $password;
    public $secretQues;
    public $secretAns;
    public $buddyID;
    public $category;
    public $subcategory;
    public $childName;
    public $childClass;
    public $gender;
//    public $upgrade_month;
    public $classUpgradationDate;
    public $childSection;
    public $childDob;
    public $pan_number;
    public $schoolCode;
    public $childEmail;
    public $parentName;
    public $parentEmail;
    public $secondaryParentEmail;
    public $city;
    public $state;
    public $country;
    public $pincode;
    public $contactno_res;
    public $contactno_cel;
    public $startDate;
    public $endDate;
    public $timeAllowedPerDay;
    public $schoolName;
    public $address;
    public $orderID;
    public $amount;
    public $package;
    public $discount;
    public $type;
    public $paymentMode;
    public $bankName;
    public $chequeno;
    public $heardFrom;
    public $registrationDate;
    public $verified;
    public $referenceCode;
    public $comment;
    public $callDate;
    public $callTime;
    public $updated_by;
    public $classChangeHistory;
    public $enabled;
    public $deactivationHistory;
    public $subjects;
    public $theme;
    public $board;
    public $homeSchool;    
    public $profilePicture;    
    public $lastModified;

    public function __construct($db, $userID = null) {
        $this->db = $db;
        if ($userID != null) {
            $userDetail = new DAL\UserDetail($this->db);
            $data = $userDetail->getUserDetails($userID);
            $details = $data[0];
            $this->userID = $details['userID'];
            $this->username = $details['username'];
            $this->password = $details['password'];
            $this->secretQues = $details['secretQues'];
            $this->secretAns = $details['secretAns'];
            $this->buddyID = $details['buddyID'];
            $this->category = $details['category'];
            $this->subcategory = $details['subcategory'];
            $this->childName = $details['childName'];
            $this->childClass = $details['childClass'];
            $this->gender = $details['gender'];
            $this->upgrade_month = $details['upgrade_month'];
            $this->classUpgradationDate = $details['classUpgradationDate'];
            $this->childSection = $details['childSection'];
            $this->childDob = $details['childDob'];
            $this->pan_number = $details['pan_number'];
            $this->schoolCode = $details['schoolCode'];
            $this->childEmail = $details['childEmail'];
            $this->parentName = $details['parentName'];
            $this->parentEmail = $details['parentEmail'];
            $this->secondaryParentEmail = $details['secondaryParentEmail'];
            $this->city = $details['city'];
            $this->state = $details['state'];
            $this->country = $details['country'];
            $this->pincode = $details['pincode'];
            $this->contactno_res = $details['contactno_res'];
            $this->contactno_cel = $details['contactno_cel'];
            $this->startDate = $details['startDate'];
            $this->endDate = $details['endDate'];
            $this->timeAllowedPerDay = $details['timeAllowedPerDay'];
            $this->schoolName = $details['schoolName'];
            $this->address = $details['address'];
            $this->orderID = $details['orderID'];
            $this->amount = $details['amount'];
            $this->package = $details['package'];
            $this->discount = $details['discount'];
            $this->type = $details['type'];
            $this->paymentMode = $details['paymentMode'];
            $this->bankName = $details['bankName'];
            $this->chequeno = $details['chequeno'];
            $this->heardFrom = $details['heardFrom'];
            $this->registrationDate = $details['registrationDate'];
            $this->verified = $details['verified'];
            $this->referenceCode = $details['referenceCode'];
            $this->comment = $details['comment'];
            $this->callDate = $details['callDate'];
            $this->callTime = $details['callTime'];
            $this->updated_by = $details['updated_by'];
            $this->classChangeHistory = $details['classChangeHistory'];
            $this->enabled = $details['enabled'];
            $this->deactivationHistory = $details['deactivationHistory'];
            $this->subjects = $details['subjects'];
            $this->theme = $details['theme'];
            $this->board = $details['board'];
            $this->homeSchool = $details['homeSchool'];
            $this->profilePicture = $details['profilePicture'];
            $this->lastModified = $details['lastModified'];
        } else {
            $this->userID = NULL;
            $this->username = NULL;
            $this->password = NULL;
            $this->secretQues = NULL;
            $this->secretAns = NULL;
            $this->buddyID = NULL;
            $this->category = NULL;
            $this->subcategory = NULL;
            $this->childName = NULL;
            $this->childClass = NULL;
            $this->gender = NULL;
            $this->upgrade_month = NULL;
            $this->classUpgradationDate = NULL;
            $this->childSection = NULL;
            $this->childDob = NULL;
            $this->pan_number = NULL;
            $this->schoolCode = NULL;
            $this->childEmail = NULL;
            $this->parentName = NULL;
            $this->parentEmail = NULL;
            $this->secondaryParentEmail = NULL;
            $this->city = NULL;
            $this->state = NULL;
            $this->country = NULL;
            $this->pincode = NULL;
            $this->contactno_res = NULL;
            $this->contactno_cel = NULL;
            $this->startDate = NULL;
            $this->endDate = NULL;
            $this->timeAllowedPerDay = NULL;
            $this->schoolName = NULL;
            $this->address = NULL;
            $this->orderID = NULL;
            $this->amount = NULL;
            $this->package = NULL;
            $this->discount = NULL;
            $this->type = NULL;
            $this->paymentMode = NULL;
            $this->bankName = NULL;
            $this->chequeno = NULL;
            $this->heardFrom = NULL;
            $this->registrationDate = NULL;
            $this->verified = NULL;
            $this->referenceCode = NULL;
            $this->comment = NULL;
            $this->callDate = NULL;
            $this->callTime = NULL;
            $this->updated_by = NULL;
            $this->classChangeHistory = NULL;
            $this->enabled = NULL;
            $this->deactivationHistory = NULL;
            $this->subjects = NULL;
            $this->theme = NULL;
            $this->board = NULL;
            $this->homeSchool = NULL;
            $this->profilePicture = NULL;
            $this->lastModified = NULL;
        }
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
                if (count($registrations) > 0)
                    $found = 2;
            }
        }
        return $found;
    }

    public function addUser() {
        if ($this->username != '') {
            $userDetails = new DAL\UserDetail($this->db);
            if(strtolower($this->category)=='student' && strtolower($this->subcategory)=='individual')
            {
                $this->schoolCode=3216130;
            }
            $userID = $userDetails->insertUser($this->firstName, $this->lastName,$this->password, $this->childClass, $this->schoolName, $this->city, $this->childDob, $this->childEmail, $this->classUpgradationDate, $this->username, $this->parentEmail, $this->parentName, $this->category, $this->subcategory, $this->updated_by, $this->endDate, $this->gender, $this->contactno_cel, $this->type, $this->schoolCode);
            $userDetails->updateMSID($userID, $userID);
            $userDetails->updateUserAdditionalDetails($userID, $this->board, $this->homeSchool);
            
            if ($this->package != NULL)
            {
//                $userDetails->updateUserDetailsTable($userID, $this->package, $this->amount, $this->board, $this->homeSchool);
            
            }
            else {
                $freeTrialDetail = new DAL\FreeTrialDetail($this->db);
                $freeTrialDetail->addFreeTrialDetail($userID);
            }
            return $userID;
        }
    }

    public function searchUsers($username, $class, $dob) {
        $userDetails = new DAL\UserDetail($this->db);
        return $userDetails->searchUsers($username, $class, $dob);
    }

    public function updateParentEmail($parentEmail, $userID) {
        $userDetails = new DAL\UserDetail($this->db);
        return $userDetails->updateParentEmail($parentEmail, $userID);
    }
    
    public function updateProfilePic() {
        $userDetails = new DAL\UserDetail($this->db);
        return $userDetails->updateProfilePic($this->profilePicture, $this->userID);
    }

}
