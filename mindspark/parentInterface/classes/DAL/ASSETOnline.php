<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

//require_once(dirname(dirname(__FILE__)) . '/class.db.php');
//require_once(dirname(dirname(__FILE__)) . '/slaveConnection.php');

class ASSETOnline extends BaseClass {

    public function getUserMapping($userID) {
        $sql = "SELECT * FROM educatio_adepts.msASSETOnlineMapping WHERE mindsparkUserID=$userID";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    
    public function getASSETOnlineUser($srno) {
        $sql = "SELECT * FROM educatio_educat.registration_master WHERE srno=$srno";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    
    public function checkUsername($username) {
        $sql = "SELECT * FROM educatio_educat.registration_master WHERE username='$username'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function insertUserMapping($MSUserID, $ASSETOnlineID) {
            $sql = "INSERT INTO educatio_adepts.msASSETOnlineMapping(mindsparkUserID,assetOnlineUserID,dateMapped) VALUES($MSUserID, $ASSETOnlineID, NOW());";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute();
            if ($result == false)
                return 0;
            else
                return true;
        
    }

    public function addASSETOnlineAccount($srno, $username, $password, $fatherName, $childFirstName, $childLastName, $class, $sex, $schoolName, $cityName, $country, $dob, $phoneMob, $email, $enteredBy, $pan_number,$registrationDAte) {
        $freeExams = 0;
        $sql = "INSERT into educatio_educat.registration_master(srno,pan_number,username,password,childName,childLastName,registrationDate,class,dob,sex,fatherName,cityName,phoneMob,email,schoolName,freeExams,enteredBy,enteredOn,lastLogin) "
                . " VALUES($srno, $pan_number, :username, old_password(:password),:childName,:childLastName,curdate(),:class,:dob,:sex,:fatherName,:cityName,:phoneMob,:email,:schoolName,:freeExams,:enteredBy,NOW(),NOW());";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':childName', $childFirstName);
        $stmt->bindParam(':childLastName', $childLastName);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':sex', $sex);
        $stmt->bindParam(':fatherName', $fatherName);
        $stmt->bindParam(':cityName', $cityName);
        $stmt->bindParam(':phoneMob', $phoneMob);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':schoolName', $schoolName);
        $stmt->bindParam(':freeExams', $freeExams);
        $stmt->bindParam(':enteredBy', $enteredBy);

        $result = $stmt->execute();
        if ($result == false)
            return 0;
        else
        {
            $id = $this->getSerialNo();
            return($id);
        }
    }

    public function getSerialNo() {
        $query = "SELECT max(srno) FROM educatio_educat.registration_master";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $srno=1;
        if(count($result)>0)
            $srno=$result[0][0];
        return $srno;
    }
    
  public function generate_PAN_number() {
    $series = "1000000";
    $query = "SELECT max(substring(pan_number,2,7)) FROM educatio_educat.registration_master WHERE substring(pan_number,2,7) > $series";
    $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $pan_number = $series + 1;
        if(count($result)>0)
            $pan_number=$result[0][0];

    $pan_number = rand(1, 9) . $pan_number . rand(1, 9);
    return $pan_number;
}

}
