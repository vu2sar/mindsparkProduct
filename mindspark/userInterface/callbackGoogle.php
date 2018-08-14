<?php
//require_once("dbconf.php");
//include("constants.php");
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/dbconf.php");
include("../userInterface/constants.php");
include("../parentInterface/saveParentStatus.php");
include("classes/clsUser.php");
$client_id = "561957817913-dk0oi64c86kaisk01l9d15ltedl86vge.apps.googleusercontent.com"; //live
//$client_id = "288984402212.apps.googleusercontent.com";
$client_secret = "netWKc01_JFD5HOIbfVPZkzZ"; //live
//$client_secret = "lqo-P0aHXeSLG3pKdT7dqGET";
$domain = 'www.mindspark.in';
//$domain = 'ec2-122-248-236-40.ap-southeast-1.compute.amazonaws.com';
//$domain = 'www.educationalinitiatives.com';
//$domain = 'localhost';
$domain = $_SERVER['HTTP_HOST'];
$redirect_url = "http://$domain/mindspark/userInterface/callbackGoogle.php"; //change this

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

//echo var_dump($_SESSION);
//if (empty($code)) {
//    $errorMsg = 'Error in login process.';
//     header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this    
//    exit(0);
//}

$access_token_details = getAccessTokenDetails($client_id, $client_secret, $redirect_url, $code);

if ($access_token_details == null) {
     $errorMsg = 'Error in login process.Please try logging in again.';
     header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Mindspark&error=$errorMsg"); //change this
    exit(0);
}

$_SESSION['access_token'] = $access_token_details->access_token; //save token is session 
$user = getUserDetails($access_token_details->access_token);

if ($user) {
//    echo "Google OAuth is OK<br>";
//    echo "<h3>User Details</h3><br>";
    $_SESSION['isOpenID'] = true;
//    echo "<b>ID: </b>" . $user->id . "<br>";
    $_SESSION['OAuthID'] = $user->id;
//    echo "<b>Name: </b>" . $user->name . "<br>";
    $_SESSION['name'] = $user->name;
//    echo "<b>First Name: </b>" . $user->given_name . "<br>";
    $_SESSION['firstName'] = $user->given_name;
//    echo "<b>Last Name: </b>" . $user->family_name . "<br>";
    $_SESSION['lastName'] = $user->family_name;
//    echo "<b>email: </b>" . $user->email . "<br>";
    $_SESSION['openIDEmail'] = $user->email;
    //$_SESSION['userID'] = 113619;
    if($user->picture)
    {
        $_SESSION['picture'] = $user->picture;
    }
    $query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";    
//    $query = "SELECT userID FROM adepts_userDetails WHERE parentEmail='" . $_SESSION['openIDEmail'] . "' ";    
    $i = 0;
    include("../slave_connectivity.php");
    $r = mysql_query($query) or die(mysql_error());
    @include("../userInterface/dbconf.php");
    if(mysql_num_rows($r)==0)
    {        
        $query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";    
        $r = mysql_query($query) or die(mysql_error());
    }
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
//    $i=0;
//	while($line = mysql_fetch_array($r)){
//		$students[$i] =$line[0]+",";
//		$i++;
//	}
	$students = implode(",",$students);
	$startTime = date("Y-m-d H:i:s");
    saveParentStatus($_SESSION['openIDEmail'],"Google",$students,$startTime);
    if($num_rows != null && $num_rows>0)
    {
        header("Location:../parentInterface/home.php");
        exit;
    }
    $email = urlencode($user->email);
    header("Location:../parentInterface/parentEmailRegistration.php?email=$email");
    exit;
}else {
    $errorMsg = urlencode('Error while getting account information');
    //Might need to put the error page in userInterface once we let students use openID
    header("Location: http://$domain/mindspark/parentInterface/openIDLoginError.php?openIDProvider=Google&error=$errorMsg"); //change this
    exit(0);
}

function recordAccessToken($user) {
    $query = "INSERT INTO adepts_OAuthToken(openid,code,access_token,id_token,expires_in) VALUES '".$user->id."','".$code."','$access_token','$id_token',$expires_in;";
    $result = mysql_query($query) or die(mysql_error());
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
//    echo '1.'.var_dump($redirect_url).'<br/>';
    $json_response = curl_exec($curl);
//    echo '2.'.var_dump($json_response).'<br/>';
    curl_close($curl);
    $authObj = json_decode($json_response);
    return $authObj;
}

function getUserDetails($access_token) {
//    echo '3'.$access_token.'<br/>';
    $graph_url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=" . urlencode($access_token);
    //$graph_url = "https://www.googleapis.com/oauth2/v1/userinfo";
    $easeCurl = curl_init($graph_url);
    curl_setopt($easeCurl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($easeCurl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($easeCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($easeCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    //curl_setopt($easeCurl,CURLOPT_HTTPHEADER,array("Authorization: $access_token"));
    $result = curl_exec($easeCurl);
    $err = curl_errno ( $easeCurl );
    $errmsg = curl_error ( $easeCurl );
    $header = curl_getinfo ( $easeCurl );
    $httpCode = curl_getinfo ( $easeCurl, CURLINFO_HTTP_CODE );
//    echo $result;
    $user = json_decode($result);
    if ($user != null && isset($user->email))
        return $user;
    return null;
}

?>