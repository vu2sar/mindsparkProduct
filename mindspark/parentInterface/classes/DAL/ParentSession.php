<?php

namespace DAL;

use DAL;

require_once 'BaseClass.php';

//require_once(DIR_CLASSES + 'class.db.php');
//require_once(DIR_CLASSES + 'slaveConnection.php');

class ParentSession extends BaseClass {

    public function getParentSesssionDetails($sesionID) {
        $sql = "SELECT * FROM adepts_parentSessionStatus WHERE sessionID=$sessionID";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function updateParentUserID($openID, $parentUserID) {
        $sql = "UPDATE adepts_parentSessionStatus SET parentUserID=:parentUserID WHERE openID=:openID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parentUserID', $parentUserID);
        $stmt->bindParam(':openID', $openID);
        $result = $stmt->execute();
        return $result;
    }

    public function saveParentStatus($parentUserID, $parentEmail, $provider, $students, $startTime) {
        $IPaddress = get_ip_address();
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $sql = "INSERT into adepts_parentSessionStatus SET parentUserID=$parentUserID, openID=:parentEmail, provider='$provider', IPaddress=:IPAddress, userAgent=:userAgent,students='$students', startTime='$startTime'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parentEmail', $parentEmail);
        $stmt->bindParam(':IPAddress', $IPaddress);
        $stmt->bindParam(':userAgent', $userAgent);
        $result = $stmt->execute();
        if ($result == false)
            return 0;
        else
            return($this->db->lastInsertId());        
    }

    public static function get_ip_address() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

}
