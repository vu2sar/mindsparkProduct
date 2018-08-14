<?php
	include("dbconf.php");
	$sep = "/";

	//ini_set('session.gc_maxlifetime',86400);
	$sessdir = ini_get('session.save_path').$sep."mindspar_sessions";
	session_cache_limiter('private');
	session_cache_limiter('must-revalidate');
	//session_cache_expire(30);
	session_start();
	$timeout = 25; // Set timeout minutes
	$logout_redirect_url = "/mindspark/userInterface/error.php"; // Set logout URL
	if(isset($_SESSION['sessionID']))
		checkForSessionTime($_SESSION['sessionID'],$logout_redirect_url);
		
	$timeout = $timeout * 60; // Converts minutes to seconds
	if (isset($_SESSION['__StartTime'])) {
		$elapsed_time = time() - $_SESSION['__StartTime'];
		if ($elapsed_time >= $timeout) { 
			$query  = "UPDATE adepts_sessionStatus 
					   SET endType=(case when ISNULL(endType) then '4(server)' else concat_ws(',',endType,'4(server)') end), logout_flag=1 
					   WHERE sessionID=".$_SESSION['sessionID'];
			$result = mysql_query($query) or die(mysql_error().$query);
			
			$sqError = "INSERT INTO adepts_errorLogs SET bugType='AutoLogout',bugText='".time()." - ".$_SESSION['__StartTime']."',qcode='',userID=".$_SESSION['userID'].",sessionID=".$_SESSION['sessionID'].",schoolCode='".$_SESSION['schoolCode']."'";
			mysql_query($sqError) or die(mysql_error().$sqError);
			
			session_destroy();
			header("Location: $logout_redirect_url");
		}
	}
	
	$_SESSION['__StartTime'] = time();

	$keys=array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key}=$_REQUEST[$key];
	}
	
	$language = isset($_REQUEST["language"])?$_REQUEST["language"]:"en";
	
	if(isset($_SESSION['theme']) && ($_SESSION['theme']==0 || $_SESSION['theme']==4) && $_SESSION['admin'] != "TEACHER" && $_SESSION['admin'] != "School Admin")
	{
		if($_SESSION["childClass"]<=3)
			$theme	=	1;
		else if($_SESSION["childClass"]<=7 && $_SESSION["childClass"]>=4)
			$theme	=	2;
		else if($_SESSION["childClass"]>=8)
			$theme	=	3;
		$sq	=	"UPDATE adepts_userDetails SET theme=$theme WHERE userID=".$_SESSION["userID"];
		$rs	=	mysql_query($sq);
	}
	else
	{
		$theme	=	$_SESSION['theme'];
	}
	
	function checkForSessionTime($sessionID,$logout_redirect_url)
	{
		$sq = "SELECT TIME_TO_SEC(TIMEDIFF('".date("Y-m-d H:i:s")."', startTime)) FROM adepts_sessionStatus WHERE sessionID=$sessionID";
		$rs = mysql_query($sq);
		$rw = mysql_fetch_array($rs);
		if($rw[0]>5400) //session timeout after 2400 seconds, so putting a check for 2500 seconds
		{
			$query  = "UPDATE adepts_sessionStatus 
					   SET endType=(case when ISNULL(endType) then '4(server)' else concat_ws(',',endType,'4(server)') end), logout_flag=1 
					   WHERE sessionID=$sessionID";
			$result = mysql_query($query) or die(mysql_error().$query);
			session_destroy();
			header("location:$logout_redirect_url");
			exit();
		}
	}
?>