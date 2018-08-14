<?php
include("check1.php");
include("constants.php");
function setEndTime($sessionID, $endtype)
{	
	$query = "UPDATE ".TBL_SESSION_STATUS." SET endTime='".date("Y-m-d H:i:s")."', endType=concat_ws(',',endType,'".$endtype."'), logout_flag=1 WHERE sessionID=".$sessionID;
	mysql_query($query);

	if(SUBJECTNO==2)
	{
		$query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct d.gameID) totgms,  count(distinct e.srno) totmonrev, count(distinct f.srno) tottoprev
		FROM (((((adepts_sessionStatus z LEFT JOIN ".TBL_QUES_ATTEMPT_CLASS." a on z.sessionID=a.sessionID)
		LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
		LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
		LEFT JOIN adepts_userGameDetails d on z.sessionID=d.sessionID)
		LEFT JOIN adepts_revisionSessionDetails e on z.sessionID=e.sessionID)
		LEFT JOIN (SELECT srno, sessionID FROM adepts_topicRevisionDetails WHERE sessionID=".$sessionID." UNION SELECT id as srno, sessionID FROM practiseModulesQuestionAttemptDetails  WHERE sessionID=".$sessionID.") f on z.sessionID=f.sessionID
		WHERE z.sessionID=".$sessionID." GROUP BY z.sessionID";

		$result = mysql_query($query) or die(mysql_error());

		$row = mysql_num_rows($result);

		if($row > 0)
		{
			$line = mysql_fetch_array($result);

			$update_query = "UPDATE adepts_sessionStatus SET ";
			$update_query .= "totalQ=".$line['totalq'].",";
			$update_query .= "totalCQ=".$line['totalcq'].",";
			$update_query .= "totalTmTst=".$line['totTmTst'].",";
			$update_query .= "totalGms=".$line['totgms'].",";
			$update_query .= "totalMonRevQ=".$line['totmonrev'].",";
			$update_query .= "totalTopRevQ=".$line['tottoprev'].",";
			$update_query .= "lastModified='".$line['lastMod']."' WHERE sessionID=".$line['sessID'];

			mysql_query($update_query) or die($update_query."<br>".mysql_error());
		}
	}
	elseif (SUBJECTNO==3)
	{
		$query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct f.srno) tottoprev
		FROM (adepts_sessionStatus_sc z LEFT JOIN adepts_teacherTopicQuesAttempt_sc a on z.sessionID=a.sessionID)
		  	LEFT JOIN adepts_topicRevisionDetails_sc f on z.sessionID=f.sessionID
		WHERE z.sessionID=".$sessionID." GROUP BY z.sessionID";

		$result = mysql_query($query) or die(mysql_error());

		$row = mysql_num_rows($result);

		if($row > 0)
		{
			$line = mysql_fetch_array($result);

			$update_query = "UPDATE adepts_sessionStatus_sc SET ";
			$update_query .= "totalQ=".$line['totalq'].",";
			$update_query .= "totalTopRevQ=".$line['tottoprev'].",";
			$update_query .= "lastModified='".$line['lastMod']."' WHERE sessionID=".$line['sessID'];
			mysql_query($update_query) or die($update_query."<br>".mysql_error());
		}
	}
}
if(isset($_GET["mode"]) && $_GET["mode"]==13)
{
	if(isset($_SESSION['sessionID']))
	{
		$sq = "UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'13'),endTime=NOW(),logout_flag=1 WHERE sessionID=".$_SESSION['sessionID'];
		mysql_query($sq);
	}
}

if($_SERVER['HTTP_REFERER'] != '' || $_SERVER["REQUEST_URI"]!= '')
{	
		$pageurl = $_SERVER["REQUEST_URI"];	
		$pageurl = explode ('/',$pageurl);
		//print_r($pageurl);
		$interfaceName=$pageurl[2];
		$pageurl = $pageurl[3];
		
		if (strpos($pageurl,'?') !== false) {
			$pageurl = substr($pageurl, 0, strpos($pageurl, '?'));
		}

		$type="";
		if($interfaceName=='userInterface')
			$type='userInterface';
		
		$query = "SELECT * FROM trackingPageDetails WHERE pageName LIKE '%".$pageurl."%' and pageType='".$type."'";
	   // echo $query;
		$result = mysql_query($query);

		$l = mysql_fetch_array($result);
		//print_r($l);
		
	    $pageid = $l[0];
		if($pageid && isset($_SESSION['sessionID']))
		{ 
			$query = "insert into trackingUserInterface (userID, sessionID, pageID, lastmodified) values (".$_SESSION['userID'].",".$_SESSION['sessionID'].",$pageid,now())";	
			mysql_query($query) or die(mysql_error());
		}
}

// Unset all of the session variables.
// mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endTime=if(isnull(endTime), now(), endTime), endType=concat_ws(',',endType,'7'), logout_flag=1 WHERE sessionID=".$_SESSION['sessionID']);                   
if(isset($_SESSION['sessionID']) && $_SESSION['sessionID']!="")
	setEndTime($_SESSION['sessionID'], 7);

session_unset();
// Finally, destroy the session.
session_destroy();
if(isset($_SERVER['HTTP_COOKIE']))
{
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach ($cookies as $cookie)
	{
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time() - 1000);
		setcookie($name, '', time() - 1000, '/');
	}
}
//print_r( $_SESSION);
//Created a hidden form/html to prevent the users accessing the cached page by pressing back button after logout.
//echo "<html><body><form id='frmHidForm' action=\"../index.php\">";
echo "<html><body><form id='frmHidForm' action=\"../login/index.php\">";
echo "<script>try {
	sessionStorage.removeItem('sessionID');
}
catch(err) {
	
}
document.getElementById('frmHidForm').submit();</script>";
echo "</form></body></html>";
exit();

?>