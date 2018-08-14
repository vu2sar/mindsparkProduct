<?php

$domain = $_SERVER['HTTP_HOST'];
define("FBAPPID","548639571895188");
//define("FBAPPID","716202281742135");
define("FBAPPSECRET","f368bdf26364ce866b3bf163e505d9b6");
//define("FBAPPSECRET","c199e4b5027eb6b258a8b630ed8e1ee4");
$redirect_url = "http://$domain/mindspark/login/callbackFB.php";
define("FBREDIRECTURL",$redirect_url);
define("FBTOKENURL",'https://graph.facebook.com/oauth/access_token');
define("FBGRAPHURL",'https://graph.facebook.com/me?fields=picture,email,first_name,last_name&access_token=');
$fbURL="https://www.facebook.com/dialog/oauth?client_id="
        . FBAPPID . "&redirect_uri=" . urlencode(FBREDIRECTURL) . "&state=[state]&response_type=code&scope=email";
define("FBURL",$fbURL);


define("GOOGLECLIENTID","561957817913-dk0oi64c86kaisk01l9d15ltedl86vge.apps.googleusercontent.com");
//define("GOOGLECLIENTID","528432110375-k8hk81ds8jv9q5d8e9ag687e66m85fh3.apps.googleusercontent.com");
define("GOOGLE_CLIENT_SECRET","netWKc01_JFD5HOIbfVPZkzZ");
//define("GOOGLE_CLIENT_SECRET","5JCOIch8rZJVI9cHpfHPpdz5");
//$redirect_url = "http://$domain/oauth2callback";
$redirect_url = "http://$domain/mindspark/login/callbackGoogle.php";
define("GOOGLEREDIRECTURL",$redirect_url);
$google_url = "https://accounts.google.com/o/oauth2/auth?client_id="
        . GOOGLECLIENTID . "&redirect_uri=" . urlencode(GOOGLEREDIRECTURL) . "&state=[state]&scope=openid%20email+profile&response_type=code";
define("GOOGLEURL",$google_url);

define('DIR_BASE', dirname(dirname(__FILE__)) . '/');
define('DIR_CLASSES', DIR_BASE . 'parentInterface/classes/');
define('DIR_BLL', DIR_CLASSES . 'BLL/');
define('DIR_DAL', DIR_CLASSES . 'DAL/');

$statusArr = array('0300'=>'Success','0399'=>'Rejected','NA'=>'Rejected','0002'=>'Pending','0001'=>'Rejected');
?>
