<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

//require_once(dirname(dirname(__FILE__)) . '/class.db.php');
//require_once(dirname(dirname(__FILE__)) . '/slaveConnection.php');

class UserDetail extends BaseClass {

    public function getUserDetails($userID) {
        $sql = "SELECT * FROM adepts_userDetails WHERE userID=$userID";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function getUsersMappedToEmail($emailID) {
        $sql = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='$emailID' OR FIND_IN_SET('$emailID',secondaryParentEmail)) order by userID desc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function removeParentEmail($userID, $emailID) {
        if ($emailID != '') {
            $sql = "UPDATE adepts_userDetails SET secondaryParentEmail=REPLACE(REPLACE(secondaryParentEmail ,'$emailID', ''),',,',',') WHERE userID=$userID;";
            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(':emailID', $emailID);
            $result = $stmt->execute();
//            $result = $stmt->fetchAll();
            return $result;
        }
    }

    public function getUserDetailsByEmail($emailID) {
        $sql = "SELECT * FROM educatio_educat.common_user_details WHERE username=:emailID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function insertUser($firstName, $lastName, $password, $childClass, $schoolName, $city, $dob, $childEmail, $classUpgradationDate, $username, $parentEmail, $parentName, $category, $subcategory, $updated_by, $endDate, $gender, $contactno_cel, $type, $schoolCode) {
        if ($username != '') {
            $sql = "INSERT INTO educatio_educat.common_user_details(Name,first_name,last_name,username,password,class,dob,schoolCode,schoolName,childEmail,additionalEmail,
                                parentName,category,subcategory,city,MS_enabled,MS_activationdate,startDate,endDate,registrationDate,classUpgradeDate,type,gender, contactno_cel)
                                VALUES(:Name,:first_name,:last_name,:username,password(:password),:class,:dob,:schoolCode,:schoolName,:childEmail,:additionalEmail,
                                :parentName,:category,:subcategory,:city,1,now(),now(),:endDate,now(),:classUpgradeDate,'$type',:gender, :contactno_cel)";
            $stmt = $this->db->prepare($sql);
            $name = $firstName . ' ' . $lastName;
            $stmt->bindParam(':Name', $name);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':class', $childClass);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':schoolName', $schoolName);
            $stmt->bindParam(':childEmail', $childEmail);
            $stmt->bindParam(':additionalEmail', $parentEmail);
            $stmt->bindParam(':parentName', $parentName);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':subcategory', $subcategory);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->bindParam(':classUpgradeDate', $classUpgradationDate);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':contactno_cel', $contactno_cel);
            $stmt->bindParam(':schoolCode', $schoolCode);
            $result = $stmt->execute();
            if ($result == false)
                return 0;
            else
                return($this->db->lastInsertId());
        }
    }

    public function updateMSID($MSuserID, $id) {
        if ($id != '') {
            $sql = "UPDATE educatio_educat.common_user_details SET MS_userID=$MSuserID WHERE id=$id;";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute();
            return $result;
        }
    }

    public function updateUserAdditionalDetails($userID, $board, $homeSchool) {
        if ($userID != '') {
            $sql = "UPDATE adepts_userDetails SET board='$board', homeSchool=$homeSchool WHERE userID=$userID;";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute();
            return $result;
        }
    }
    
    public function extendFreeTrial($userID) {
        if ($userID != '') {
            $sql = "UPDATE educatio_educat.common_user_details SET endDate=(CASE WHEN endDate<curdate() THEN date_add(curdate(), INTERVAL 7 DAY) ELSE date_add(endDate, INTERVAL 6 DAY) END) WHERE MS_userID=$userID;";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute();
            return $result;
        }
    }

    public function searchUsers($username, $class, $dob) {
        $sql = "select userID,username,parentEmail,secondaryParentEmail, childName from adepts_userDetails "
                . " where username=:username and childClass=:class and (childDob IS NULL OR childDob='0000-00-00' OR childDob=:dob);";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':dob', $dob);
        $r = $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function updateParentEmail($parentEmail, $userID) {
        $sql = "UPDATE educatio_educat.common_user_details set additionalEmail=:parentEmail where MS_userID=$userID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parentEmail', $parentEmail);
        $result = $stmt->execute();
        return $result;
    }
    
    public function updateProfilePic($profilePic, $userID) {
        $sql = "UPDATE educatio_educat.common_user_details set profilePicture=:profilePicture where MS_userID=$userID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':profilePicture', $profilePic);
        $result = $stmt->execute();
        return $result;
    }

}
