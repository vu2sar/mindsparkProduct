<?php

//use BLL;

//error_reporting(E_ALL);
if (!class_exists('db')) {
    require_once('../parentInterface/classes/class.db.php');
}
//@include("../userInterface/dbconf.php");
include("../userInterface/constants.php");
//include("classes/clsUser.php");
include('constants.php');
require_once DIR_BLL . 'parentDetails.php';
require_once DIR_BLL . 'ParentSession.php';
include("../parentInterface/common.php");
//include("../parentInterface/saveParentStatus.php");

//$app_id = "548639571895188"; //change this
//$app_secret = "f368bdf26364ce866b3bf163e505d9b6"; //change this
//$domain = $_SERVER['HTTP_HOST'];
//$redirect_url = "http://$domain/mindspark/userInterface/callbackFB.php"; //change this
if (isset($_REQUEST['error_code'])) {
    $errorMsg = urlencode($_REQUEST['error_message']);
    //Might need to put the error page in userInterface once we let students use openID
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Facebook&error=$errorMsg"); //change this
    exit(0);
}
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

$access_token_details = getAccessTokenDetails(FBAPPID, FBAPPSECRET, FBREDIRECTURL, $code);
if ($access_token_details == null) {
    $errorMsg = 'Error in login process.Please try logging in again.';
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this
    exit(0);
}

$_SESSION['access_token'] = $access_token_details['access_token']; //save token is session 

$user = getUserDetails($access_token_details['access_token']);

if ($user) {
    if (!isset($user->email)) {
        $errorMsg = urlencode('No email ID found for this account. Please assign email ID to your account for access.');
        //Might need to put the error page in userInterface once we let students use openID
        header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this
        exit(0);
    }
    /* echo "Facebook OAuth is OK<br>";
      echo "<h3>User Details</h3><br>";
      echo "<b>ID: </b>".$user->id."<br>";
      echo "<b>Name: </b>".$user->name."<br>";
      echo "<b>First Name: </b>".$user->first_name."<br>";
      echo "<b>Last Name: </b>".$user->last_name."<br>";
      echo "<b>Username: </b>".$user->username."<br>";
      echo "<b>Profile Link: </b>".$user->link."<br>";
      echo "<b>email: </b>".$user->email."<br>"; */
    include ("../userInterface/dbconf.php");
    $_SESSION['isOpenID'] = true;
    $_SESSION['OAuthID'] = $user->id;
    $_SESSION['firstName'] = $user->first_name;
    $_SESSION['lastName'] = $user->last_name;
    $_SESSION['openIDEmail'] = $user->email;
    if ($user->picture->data->url) {
        $_SESSION['picture'] = $user->picture->data->url;
    }
    $_SESSION['openIDProvider'] = 'Facebook';
    require_once('../parentInterface/classes/slaveConnection.php');
    $parentDetails = new BLL\ParentDetails($sdb);
    $checkDuplicateParent = $parentDetails->getParentDetailsByEmail($_SESSION['openIDEmail']);
    if (count($checkDuplicateParent) > 0) {
		include ("../userInterface/dbconf.php");
        $parentDetails->parentUserID = $checkDuplicateParent[0]['parentUserID'];
        $_SESSION['parentUserID'] = $parentDetails->parentUserID;
        $parentUserID = $_SESSION['parentUserID'];
        $query = "SELECT * FROM parentUserDetails WHERE parentUserID=$parentUserID ";
        $result = mysql_query($query) or die(mysql_error());
        if (mysql_num_rows($result) > 0) {
            $line = mysql_fetch_array($result);
            set_parent_json_array($line);
        }
        $childrenMapped = $parentDetails->getChildrenMapped();
        require_once('../parentInterface/classes/masterConnection.php');
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
        $parentSession->provider = "Facebook";
        $parentSession->students = $students;
        $parentSession->startTime = $startTime;
        $parentSession->parentUserID = $_SESSION['parentUserID'];
        $_SESSION['sessionID'] = $parentSession->saveParentStatus();
        header("Location:$nextPage");
        exit;
    } else {
        /*if ($_SESSION['openIDOrigin'] == 'Free Trial') {*/
            include ("../userInterface/dbconf.php");
             $_SESSION['parentUserID'] = register_new_parent($user->email,$user->first_name,$user->last_name);
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
            $parentSession->provider = "Facebook";
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
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Facebook&error=$errorMsg"); //change this
    exit(0);
}

function getAccessTokenDetails($app_id, $app_secret, $redirect_url, $code) {
//    $token_url =  FBTOKENURL;
    $params = array(
        "code" => $code,
        "client_id" => $app_id,
        "client_secret" => $app_secret,
        "redirect_uri" => $redirect_url,
        "grant_tyhpe" => 'authorization_code',
        "response_type" => 'code'
    );
    $curl = curl_init(FBTOKENURL);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    echo '1.'.var_dump(urlencode($redirect_url)).'<br/>';
    $response = curl_exec($curl);
//    echo '2.'.var_dump($response).'<br/>';
    curl_close($curl);
    $params = null;
    parse_str($response, $params);
    //$access_token = $params['access_token'];
    //$authObj = json_decode($json_response);
    return $params;
}

function getUserDetails($access_token) {
    $graph_url = FBGRAPHURL . $access_token;
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
//    echo $result;
    $user = json_decode($result);
    if ($user != null)
        return $user;
    return null;
}

?>