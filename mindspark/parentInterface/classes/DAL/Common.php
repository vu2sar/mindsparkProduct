<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

//require_once(dirname(dirname(__FILE__)) . '/class.db.php');
//require_once(dirname(dirname(__FILE__)) . '/slaveConnection.php');

class Common extends BaseClass {

    public function getDetailsByEmail($emailID) {
        $sql = "SELECT * FROM adepts_registrationDetails WHERE childEmail=:emailID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        $stmt->execute();
        $result = $stmt->fetchAll();
//        var_dump($result);
        return $result;
    }
    
     public function addChildVerificationDetail($sessionID, $studentName, $userName, $schoolName, $childClass, $city, $dobFormatted) {
        $sql = "INSERT INTO adepts_childVerificationDetails (sessionID,studentName,username,schoolName,class,city,dob)
            VALUES ($sessionID,:studentName,:userName,:schoolName,:childClass,:city,:dobFormatted)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':studentName', $studentName);
        $stmt->bindParam(':userName', $userName);
        $stmt->bindParam(':schoolName', $schoolName);
        $stmt->bindParam(':childClass', $childClass);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':dobFormatted', $dobFormatted);
        $result = $stmt->execute();
        if ($result == false)
            return 0;
        else
            return($this->db->lastInsertId());
    }
    
    public function addChangeLog($tableChanged, $columnChanged, $identifier, $oldValue, $newValue, $changeComment, $modifiedBy) {
        $sql = "INSERT INTO adepts_changeLog(tableChanged,columnChanged,identifier,oldValue,newValue,changeComment,modifiedBy) 
                        VALUES('$tableChanged','$tableChanged','$identifier',:oldValue, :newValue, :changeComment, :modifiedBy)";
                            
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':oldValue', $oldValue);
        $stmt->bindParam(':newValue', $newValue);
        $stmt->bindParam(':changeComment', $changeComment);
        $stmt->bindParam(':modifiedBy', $modifiedBy);
        return $stmt->execute();        
    }

    public function addEmailUnsubscribe($emailID) {
        $sql = "INSERT INTO emailUnsubscribe(emailID, dateUnsubscribed) VALUES(:emailID,NOW());";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        return $stmt->execute();        
    }
    
    public function addMobileUsage($parentID, $sessionID) {
        $sql = "INSERT INTO parentPortalMobileUsage(parentUserID, sessionID) VALUES($parentID, $sessionID);";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();        
    }
    
    public function getClassMPIScore($schoolCode, $class, $date) {
        $sql = "SELECT * FROM dailyMPIScore WHERE schoolCode=$schoolCode AND class=$class AND date='$date'";
        $stmt = $this->db->prepare($sql);        
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}
