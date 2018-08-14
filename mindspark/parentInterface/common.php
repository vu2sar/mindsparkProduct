<?php
//error_reporting(E_ALL);
//session_start();

//use BLL;

require_once '../parentInterface/constants.php';
require_once DIR_BLL . 'Common.php';
require_once DIR_BLL . 'UserDetail.php';
include("../userInterface/constants.php");
//require_once DIR_CLASSES . 'slaveConnection.php';
//require_once "common.php";

function setSesssionValues($childrenMapped) {
//    session_start();
    global $sdb;
//    require_once DIR_CLASSES . 'slaveConnection.php';
    $childID = array();
    $i = 0;
    foreach ($childrenMapped as $child1) {
        $childID[$i] = $child1['childUserID'];
        $child[$i] = new BLL\UserDetail($sdb, $childID[$i]);
        $childName[$i] = $child[$i]->childName;
        $childUserName[$i] = $child[$i]->username;
        $childFreeTrial[$i] = $child1['freeTrial'];
        if ($i == 0) {
            $childSelected = $child[$i];
            $_SESSION['childID'] = $childSelected->userID;
            $_SESSION['childIDUsed'] = $childSelected->userID;
            $_SESSION['childNameUsed'] = $childSelected->childName;
            $_SESSION['childClassUsed'] = $childSelected->childClass;
            $_SESSION['childSubcategory'] = $childSelected->subcategory;
            $_SESSION['packageExpiryDate'] = $childSelected->endDate;
			$_SESSION['packageExpiryDate'] = date("d-m-Y", strtotime($_SESSION['packageExpiryDate']) );
            $_SESSION['childFreeTrial'] = $childFreeTrial[$i];
        }
        $i++;
    }
    $_SESSION['arrChildID'] = $childID;
    $_SESSION['arrChildName'] = $childName;
    $_SESSION['arrUserName'] = $childUserName;
    $_SESSION['arrChildFreeTrial'] = $childFreeTrial;
}
function set_parent_json_array($data)
{
            $data_accesstoken = file_get_contents(BASE_URL."mindspark_product/parent/api/auth/create_access_token/".$data['parentUserID']);
            $data_accesstoken = json_decode($data_accesstoken);
            $json_array = array();
            $tmp = array();
            $tmp['parentUserID'] = $data['parentUserID'];
            $tmp['firstName'] = $data['firstName'];
            $tmp['lastName'] = $data['lastName'];
            $tmp['username'] = $data['username'];
            $tmp['access_token'] = $data_accesstoken->accesstoken;
            $json_array['data'] = $tmp;
            $str = json_encode($json_array);
            $_SESSION['data_set_parent'] = $str;
     
}

function dbErrorReport($msg) {
    echo 'test' . $msg;
    exit;
}

function generatePassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function register_new_parent($username,$firstName,$lastName)
{
    $sql_query = "INSERT INTO parentUserDetails(username,firstName,lastName,loginType,registrationDate,enabled,verified) VALUES('$username','$firstName','$lastName',2,NOW(),1,1)";
    $result = mysql_query($sql_query) or die('parent_register:'.mysql_error());
    return mysql_insert_id();
    
}


function get_ip_address() {
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

?>