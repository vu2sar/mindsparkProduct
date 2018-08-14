<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

//require_once(dirname(dirname(__FILE__)) . '/class.db.php');
//require_once(dirname(dirname(__FILE__)) . '/slaveConnection.php');

class ParentDetail extends BaseClass {

    public function getParentDetails($parentUserID) {
        $sql = "SELECT * FROM parentUserDetails WHERE parentUserID=$parentUserID";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function getParentDetailsByEmail($emailID) {
        $sql = "SELECT * FROM parentUserDetails WHERE username=:emailID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        $stmt->execute();
        $result = $stmt->fetchAll();
//        var_dump($result);
        return $result;
    }
    
    public function verifyParentUserName($emailID) {
        $sql = "UPDATE parentUserDetails SET verified=true,enabled=1 WHERE username=:emailID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        $result=$stmt->execute();
//        $result = $stmt->fetchAll();
        return $result;
    }

    public function updatePassword($parentUserID, $newPassword) {
        $sql = "UPDATE parentUserDetails SET password=password(:password) WHERE parentUserID=:parentUserID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $newPassword);
        $stmt->bindParam(':parentUserID', $parentUserID);
        $result=$stmt->execute();
//        $result = $stmt->fetchAll();
        return $result;
    }
    
    public function addParentDetails($username, $firstName, $lastName, $password, $loginType, $phoneNumber, $city, $state, $country, $address, $registrationDate, $enabled, $verified, $modifiedBy) {
        $sql = "INSERT INTO parentUserDetails(username,firstName,lastName,password,loginType,phoneNumber,city,state,country,address,registrationDate,enabled,verified,modifiedBy) "
                . "VALUES(:username,:firstName,:lastName,password(:password),:loginType,:phoneNumber,:city,:state,:country,:address,:registrationDate,:enabled,:verified,:modifiedBy);";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':loginType', $loginType);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':registrationDate', $registrationDate);
        $stmt->bindParam(':enabled', $enabled);
        $stmt->bindParam(':verified', $verified);
        $stmt->bindParam(':modifiedBy', $modifiedBy);
        $result = $stmt->execute();
//        $result = $this->db->run($sql);
        if ($result == false)
            return 0;
        else
            return($this->db->lastInsertId());
    }

    public function find($userID) {
        
    }

}
