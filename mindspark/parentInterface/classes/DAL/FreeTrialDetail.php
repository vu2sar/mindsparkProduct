<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

class FreeTrialDetail extends BaseClass {

    public function getFreeTrialDetail($userID) {
        $sql = "SELECT * FROM freeTrialDetail WHERE userID=$userID and status='Active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;  
    }
    
    public function deactivateFreeTrial($userID)
    {
        $sql = "UPDATE freeTrialDetail SET status='Inactive' WHERE userID=$userID and status='Active'";
        $result = $this->db->run($sql);
        return $result;
    }

    public function addFreeTrialDetail($userID) {
        $sql = "INSERT INTO freeTrialDetail(userID,startDate,endDate,extension,status) VALUES($userID,now(),date_add(now(), INTERVAL 7 DAY),0,'Active')";
        $result = $this->db->run($sql);
        return $result;
    }

    public function extendFreeTrial($userID) {
        $sql = "UPDATE freeTrialDetail SET extension=extension+1, endDate=(CASE WHEN endDate<curdate() THEN date_add(curdate(), INTERVAL 7 DAY) ELSE date_add(endDate, INTERVAL 7 DAY) END) WHERE userID=$userID";
        $result = $this->db->run($sql);
        return $result;
    }
}
