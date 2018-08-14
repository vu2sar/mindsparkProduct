<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../../userInterface/check1.php");
include_once("../notifications.php");
error_reporting(E_ALL);
if(isset($_POST['username']))
{
	ajax_serve($_POST['username']);
}

?>