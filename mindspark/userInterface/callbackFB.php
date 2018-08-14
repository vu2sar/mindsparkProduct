<?php

@include("../userInterface/dbconf.php");
include("../userInterface/constants.php");
include("../parentInterface/saveParentStatus.php");
include("classes/clsUser.php");
$app_id = "548639571895188"; //change this
$app_secret = "f368bdf26364ce866b3bf163e505d9b6"; //change this
//$domain = 'www.educationalinitiatives.com';
//$domain = 'ec2-122-248-236-40.ap-southeast-1.compute.amazonaws.com';
//$domain = 'www.mindspark.in';
//$domain = 'localhost';
$domain = $_SERVER['HTTP_HOST'];
$redirect_url = "http://$domain/mindspark/userInterface/callbackFB.php"; //change this
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

session_unset();
session_destroy();
session_start();

//if (empty($code)) {
//    $errorMsg = 'Error in login process.';
//    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this    
//    exit(0);
//}

$access_token_details = getAccessTokenDetails($app_id, $app_secret, $redirect_url, $code);
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
    $_SESSION['isOpenID'] = true;
    $_SESSION['OAuthID'] = $user->id;
    $_SESSION['firstName'] = $user->first_name;
    $_SESSION['lastName'] = $user->last_name;
    $_SESSION['openIDEmail'] = $user->email;
    if ($user->picture->data->url) {
        $_SESSION['picture'] = $user->picture->data->url;
    }
$query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";    
//    $query = "SELECT userID FROM adepts_userDetails WHERE parentEmail='" . $_SESSION['openIDEmail'] . "'";  //change according to parent email    
include("../slave_connectivity.php");
    $r = mysql_query($query);
    @include("../userInterface/dbconf.php");
    if(mysql_num_rows($r)==0)
    {
        $query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";    
        $r = mysql_query($query);
    }
	$i=0;
	while ($l = mysql_fetch_array($r)) {
        $childID[$i] = $l[0];
        $childSelected = new User($childID[0]);
        $child[$i] = new User($childID[$i]);
        $childName[$i] = $child[$i]->childName;
        $students[$i] =$l[0]+",";
        $i++;
        $_SESSION['childID'] = $childID[0];
        $_SESSION['childIDUsed'] = $childSelected->userID;
        $_SESSION['childNameUsed'] = $childSelected->childName;
        $_SESSION['childClassUsed'] = $childSelected->childClass;
		$_SESSION['childSubcategory'] = $childSelected->subcategory;
        $_SESSION['packageExpiryDate'] = $childSelected->endDate;
    }
    $_SESSION['arrChildID'] = $childID;    
    $_SESSION['arrChildName'] = $childName;  
    $num_rows = mysql_num_rows($r);
	$students = implode(",",$students);
	 $startTime = date("Y-m-d H:i:s");
    saveParentStatus($_SESSION['openIDEmail'],"Facebook",$students,$startTime);
    if ($num_rows != null && $num_rows > 0) {
        header("Location:../parentInterface/home.php");
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
    $token_url = "https://graph.facebook.com/oauth/access_token";
    $params = array(
        "code" => $code,
        "client_id" => $app_id,
        "client_secret" => $app_secret,
        "redirect_uri" => $redirect_url,
        "grant_tyhpe" => 'authorization_code',
        "response_type" => 'code'
    );
    $curl = curl_init($token_url);
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
    $graph_url = "https://graph.facebook.com/me?fields=picture,email,first_name,last_name&access_token=" . $access_token;
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