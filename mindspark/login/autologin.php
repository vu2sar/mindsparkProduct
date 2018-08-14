<?php
error_reporting(0);
include ("../userInterface/dbconf.php");
include ("../userInterface/classes/clsUser.php");
//include("../userInterface/classes/clsSession.php");
session_start();
include("loginFunctions.php");
if(count($_POST) == 0)
{
	echo 'Not Authorized';
	exit();
}
$user_id = $_POST['userID'];
$post_key = $_POST['post_key'];
$user_id = base64_decode($user_id);
if(md5($_POST['userID']) == $post_key)
parentLogin($user_id,false);
else
echo 'Invaid request';

?>