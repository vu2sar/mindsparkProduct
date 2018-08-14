<?php
session_start();
if (!isset($_SESSION['openIDEmail'])) {
    echo 'Error while loggin in';
    exit;
}
if (isset($_REQUEST['username']))
    $username = $_REQUEST['username'];
else {
    echo 'Error while loggin in';
    exit();
}
if (isset($_REQUEST['userID']))
    $userID = $_REQUEST['userID'];
else {
    echo 'Error while loggin in';
    exit();
}
use BLL;

require_once 'constants.php';
require_once 'common.php';
require_once DIR_BLL . 'ASSETOnline.php';
require_once DIR_BLL . 'UserDetail.php';
require_once DIR_CLASSES . 'masterConnection.php';

$asset = new BLL\ASSETOnline($db);
$found = $asset->getMapping($userID);
if($found>0)
{
    $asset = new BLL\ASSETOnline($db,$found);
    $username = $asset->username;
}
 else {
    $asset = new BLL\ASSETOnline($db);
    $msUser = new BLL\UserDetail($db, $userID);
    $asset->MSUserID = $msUser->userID;
    $asset->username = $msUser->username;
    $password = generatePassword(6);
    $asset->password = $password;
    $asset->fatherName = $msUser->parentName;
    $childName = explode(' ',$msUser->childName);
    $firstName=$childName[0];
    if(count($childName)>1)
        $lastName = $childName[1];
    $asset->childFirstName = $firstName;
    $asset->childLastName = $lastName;
    $asset->class = $msUser->childClass;
    $asset->gender = $msUser->gender;
    $asset->schoolName = $msUser->schoolName;
    $asset->cityName = $msUser->city;
//$asset->country='India';
    $asset->dob = $msUser->childDob;
    $asset->phoneMob = $msUser->contactno_cel;
    $asset->email = $msUser->parentEmail;
    $asset->enteredBy = $msUser->parentEmail;
    $username = $asset->insertUser();
}
$response = cURL("http://www.assetonline.in/asset_online/index.php?mode=login", 1, null, "username=$username&password=FrLtAGGHnlR29qm63f42LZ9yKXca3D75", true);

function cURL($url, $header, $cookie, $p, $isLogin = FALSE) {
//	if(!$header)
//		session_write_close();
    $ch = curl_init();
    $url = 'http://www.assetonline.in/asset_online/Asset_online_login.php?mode=login';
    curl_setopt($ch, CURLOPT_HEADER, $header);
    curl_setopt($ch, CURLOPT_NOBODY, $header);
    curl_setopt($ch, CURLOPT_URL, $url);
//	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//        var_dump($p);
    if ($p) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
    };
//        var_dump($ch);
    $result = curl_exec($ch);
    $response = $result;
//    print_r($result);
    preg_match('/^Set-Cookie: (.*?);/m', $response, $m);
    if (!isset($m[1]) || $m[1] == "")
        die("Could not initiate session.");
    $msSessionCookie = explode("=", $m[0]);
    $msSessionCookie = $msSessionCookie[1];
    $msSessionCookie = rtrim($msSessionCookie, ";");
    $_SESSION['msSessionCookie'] = $msSessionCookie;
    $strCookie = $_SESSION['msSessionCookie'];
    preg_match('/id="sessionID" value="(.*?)"/', $response, $m);
    $arrReturn = array();
    if (strpos($result, "<script>window.location='Asset_online_userhome.php'</script>") !== false) {
        $arrReturn['sessionCookie'] = $strCookie;
    } else {
        $arrReturn['sessionCookie'] = false;
    }
    echo json_encode($arrReturn);
//        echo "<script>window.location.href='http://www.assetonline.in/asset_online/Asset_online_userhome.php'</script>";
//        echo '<form id="frmHidForm" action="http://www.assetonline.in/asset_online/remoteLogin.php" method="post">';
//		echo '<input type="hidden" name="sessionCookie" id="sessionCookie" value="'.$strCookie.'">';
//	echo '</form>';
//	echo "<script>
//				  document.getElementById('frmHidForm').submit();
//			  </script>";
//	echo '</body>';
//	echo '</html>';
//	exit();
//        $error = curl_error($ch);
    curl_close($ch);

    if ($result) {
        return $result;
    } else {
        
    };
}

?>