<?php

session_start();
//error_reporting(E_ALL);
//error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);

//use BLL;

if (!class_exists('db')) {
    require_once('../parentInterface/classes/class.db.php');
}

//include("../userInterface/dbconf.php");
include("../userInterface/constants.php");
//include("../userInterface/classes/clsUser.php");
include('constants.php');
require_once DIR_BLL . 'parentDetails.php';
require_once DIR_BLL . 'ParentSession.php';
//require_once DIR_BLL . 'userDetails.php';
include("../parentInterface/common.php");
//include("../parentInterface/saveParentStatus.php");
//require_once('../parentInterface/classes/masterConnection.php');
//$db = new db('MASTER','educatio_adepts');

$code = $_REQUEST["code"];
session_start();
if ($_SESSION['state'] == null || ($_SESSION['state'] != $_REQUEST['state']) || empty($code)) {
    $errorMsg = 'Error in login process.Please try logging in again.';
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this
    exit(0);
}
$openIDOrigin = $_SESSION['openIDOrigin'];
session_unset();
session_destroy();
session_start();
$_SESSION['openIDOrigin'] = $openIDOrigin;

$access_token_details = getAccessTokenDetails(GOOGLECLIENTID, GOOGLE_CLIENT_SECRET, GOOGLEREDIRECTURL, $code);

if ($access_token_details == null) {
    $errorMsg = 'Error in login process.Please try logging in again.';
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this
    exit(0);
}

$_SESSION['access_token'] = $access_token_details->access_token; //save token is session 
$user = getUserDetails($access_token_details->access_token);
if ($user) {
    $_SESSION['isOpenID'] = true;
//    $_SESSION['OAuthID'] = $user->id;
    $_SESSION['name'] = $user->name;
    $_SESSION['firstName'] = $user->given_name;
    $_SESSION['lastName'] = $user->family_name;
    $_SESSION['openIDEmail'] = $user->email;
    $_SESSION['openIDMethod'] = 'google';
    if ($user->picture) {
        $_SESSION['picture'] = $user->picture;
    }
    $_SESSION['openIDProvider'] = 'Google';
    require_once DIR_CLASSES . 'slaveConnection.php';   
    $parentDetails = new BLL\ParentDetails($sdb);
    $checkDuplicateParent = $parentDetails->getParentDetailsByEmail($_SESSION['openIDEmail']);
    if (count($checkDuplicateParent) > 0) {
        $parentDetails->parentUserID = $checkDuplicateParent[0]['parentUserID'];
        $_SESSION['parentUserID'] = $parentDetails->parentUserID;
        $parentUserID = $_SESSION['parentUserID'];
        include ("../userInterface/dbconf.php");
        $query = "SELECT * FROM parentUserDetails WHERE parentUserID=$parentUserID ";
        $result = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($result) > 0) {
            $line = mysql_fetch_array($result);
            set_parent_json_array($line);
        }
        $childrenMapped = $parentDetails->getChildrenMapped();
        require_once DIR_CLASSES . 'masterConnection.php';
        $parentSession = new BLL\ParentSession($db);
        $sessionID = '';
        $ids = array();
       $nextPage = PARENT_CONNECT_PATH;
        if (count($childrenMapped) > 0) {
            setSesssionValues($childrenMapped);
            $ids = array_map(function($item) {
                return $item['childUserID'];
            }, $childrenMapped);
        $nextPage = PARENT_CONNECT_PATH;
        }
        $students = implode(',', $ids);
        $startTime = date("Y-m-d H:i:s");
        $parentSession->parentEmail = $_SESSION['openIDEmail'];
        $parentSession->provider = "Google";
        $parentSession->students = $students;
        $parentSession->startTime = $startTime;
        $parentSession->parentUserID = $_SESSION['parentUserID'];
        $_SESSION['sessionID'] = $parentSession->saveParentStatus();
        header("Location:$nextPage");
        exit;
    } else {
        /*if ($_SESSION['openIDOrigin'] == 'Free Trial') {*/
            include ("../userInterface/dbconf.php");
             $_SESSION['parentUserID'] = register_new_parent($user->email,$user->given_name,$user->family_name);
             $parentUserID = $_SESSION['parentUserID'];

             $query = "SELECT * FROM parentUserDetails WHERE parentUserID=$parentUserID ";
            $result = mysql_query($query) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
                $line = mysql_fetch_array($result);
                set_parent_json_array($line);
            }
            require_once DIR_CLASSES . 'masterConnection.php';
            $parentSession = new BLL\ParentSession($db);
            $sessionID = '';
             $ids = array();
            $students = implode(',', $ids);
            $startTime = date("Y-m-d H:i:s");
            $parentSession->parentEmail = $_SESSION['openIDEmail'];
            $parentSession->provider = "Google";
            $parentSession->students = $students;
            $parentSession->startTime = $startTime;
            $parentSession->parentUserID = $_SESSION['parentUserID'];
            $_SESSION['sessionID'] = $parentSession->saveParentStatus();
            header("Location:../app/parent/ui/redirect.php");
            exit;
        
    }
    $email = urlencode($user->email);
    header("Location:../parentInterface/parentEmailRegistration.php?email=$email");
    exit;
} else {
    $errorMsg = urlencode('Error while getting account information');
    //Might need to put the error page in userInterface once we let students use openID
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Google&error=$errorMsg"); //change this
    exit(0);
}

function getAccessTokenDetails($client_id, $client_secret, $redirect_url, $code) {

    $url = 'https://accounts.google.com/o/oauth2/token';
    $params = array(
        "code" => $code,
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "redirect_uri" => $redirect_url,
        "grant_type" => "authorization_code"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $json_response = curl_exec($curl);
    curl_close($curl);
    $authObj = json_decode($json_response);
    return $authObj;
}

function getUserDetails($access_token) {
    $graph_url = "https://www.googleapis.com/plus/v1/people/me/openIdConnect?access_token=" . urlencode($access_token);
    $easeCurl = curl_init($graph_url);
    curl_setopt($easeCurl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($easeCurl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($easeCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($easeCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    //curl_setopt($easeCurl,CURLOPT_HTTPHEADER,array("Authorization: $access_token"));
    $result = curl_exec($easeCurl);
    $err = curl_errno($easeCurl);
    $errmsg = curl_error($easeCurl);
    $header = curl_getinfo($easeCurl);
    $httpCode = curl_getinfo($easeCurl, CURLINFO_HTTP_CODE);
    $user = json_decode($result);
    if ($user != null && isset($user->email))
        return $user;
    return null;
}
function setParentSesssionValues($childrenMapped) {
    $childID = array();
    $i = 0;
    foreach ($childrenMapped as $child1) {
        $childID[$i] = $child1['childUserID'];
        $child[$i] = new User( $childID[$i]);
        $childName[$i] = $child[$i]->childName;
        $childFreeTrial[$i] = $child1['freeTrial'];
        if ($i == 0) {
            $childSelected = $child[$i];
            $_SESSION['childID'] = $childSelected->userID;
            $_SESSION['childIDUsed'] = $childSelected->userID;
            $_SESSION['childNameUsed'] = $childSelected->childName;
            $_SESSION['childClassUsed'] = $childSelected->childClass;
            $_SESSION['childSubcategory'] = $childSelected->subcategory;
            $_SESSION['packageExpiryDate'] = $childSelected->endDate;
        }
        $i++;
    }
    $_SESSION['arrChildID'] = $childID;
    $_SESSION['arrChildName'] = $childName;
    $_SESSION['arrChildFreeTrial'] = $childFreeTrial;
}

?>