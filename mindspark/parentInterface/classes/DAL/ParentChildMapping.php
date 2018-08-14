<?php
namespace DAL;
use DAL;
require_once 'BaseClass.php';
//require_once(dirname(dirname(__FILE__)) . '/class.db.php');
//require_once(dirname(dirname(__FILE__)) . '/slaveConnection.php');

class ParentChildMapping extends BaseClass {

    public function getUsers($parentUserID) {
//        $sql = "SELECT pc.*,(IF(ISNULL(f.userID),0,1)) AS freeTrial FROM parentChildMapping pc left join freeTrialDetail f on f.userID=pc.childUserID WHERE parentUserID=$parentUserID and IFNULL(f.status,'Active')='Active'";
        $sql = "SELECT pc.*,(IF(ISNULL(f.userID),0,(CASE status WHEN 'Active' THEN 1 ELSE 0 END))) AS freeTrial FROM parentChildMapping pc left join freeTrialDetail f on f.userID=pc.childUserID WHERE parentUserID=$parentUserID ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function getParents($userID) {
        $sql = "SELECT * FROM parentChildMapping WHERE userID=$userID";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    
    public function addParentUserMapping($userID,$parentUserID) {
        $sql = "INSERT INTO parentChildMapping(parentUserID,childUserID,dateMapped) VALUES($parentUserID,$userID,now())";
        $result = $this->db->run($sql);
        return $result;
    }
    
    public function getUsersMappedToEmail($emailID) {
        $sql = "SELECT userID FROM adepts_userDetails WHERE (parentEmail=':emailID' OR FIND_IN_SET(':emailID',secondaryParentEmail))";    
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emailID', $emailID);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    
    
    public function find($userID) {
        
    }

}
