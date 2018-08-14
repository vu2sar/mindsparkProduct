<?php

include('dbconf.php');
include('functions/functions.php');

$userName=$_POST['userName'];
//echo $userName;
$resetKudos=$_POST['resetKudos'];
	if ($resetKudos == "YES"){
	resetKudosCounter($userName); 
	}	              

?>