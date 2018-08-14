<?php
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING); 	//sets what errors to report
	set_time_limit (0);   												//Setting it zero removes any time limits to report a fatal error. Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	
	include("../../check.php");											//checking authorization
	
	//$_SESSION['username'] = 'kalpesh.awathare';//dev
	if(!isset($_SESSION['username'])){
		echo "You must be logged in to create a passage. <a href='../../index.php'> LOGIN </a>";	
	}	
	else{
		include('src/structure.php');
	}
	
?>