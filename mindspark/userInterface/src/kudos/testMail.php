<?php
include_once("../../check1.php");
include('common_functions.php');

for($i=1;$i<100;$i++)
{
	sendMail($i);
}

?>