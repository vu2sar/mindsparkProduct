<?php

namespace DAL;
use DAL;
require_once 'BaseClass.php';

class ParentFeedback extends BaseClass {

    public function getParentFeedback($parentUserID) {
        $sql = "SELECT * FROM parentFeedback WHERE parentID=$parentUserID ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function addParentFeedback($parentUserID, $studentID, $subject, $message) {
        $sql = "INSERT INTO parentFeedback(parentID,studentID,subject,message,dateSubmitted) VALUES($parentUserID, $studentID,:subject,:message,now())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        $result = $stmt->execute();
        return $result;
    }

}
