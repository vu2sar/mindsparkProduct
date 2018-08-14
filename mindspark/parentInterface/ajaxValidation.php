<?php

//echo 'called';
//error_reporting(E_ALL);
use BLL;
require_once 'constants.php';
require_once DIR_CLASSES.'slaveConnection.php';
require_once DIR_BLL.'Common.php';
$emailID = $_REQUEST['email'];
$emailID = urldecode($emailID);
$common = new BLL\Common($sdb);
echo $common->checkIfEmailExist($emailID);
?>