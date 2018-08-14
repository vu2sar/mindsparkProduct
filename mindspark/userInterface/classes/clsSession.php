<?php
class session
{		
	var $userID;
	var $sessionID;
	
	function session($userID, $sessionID="")
	{
		$this->userID = $userID;		
		if($sessionID!="")
			$this->populateSessionDetails($sessionID);
	}
	function populateSessionDetails($sessionID)
	{
		$this->sessionID = $sessionID;
	}
	
	function createSession($table, $startTime)
	{
		$_SESSION["windowName"]	=	date("Ymd").$this->userID.rand(5, 15);	
		$_SESSION['sessionReportFlag']=1;	
		$startTime_int = date("Ymd");
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];   //Needed since apps behind a load balancer
	    if(strpos($ip,',') !== false) {
	        $ip = substr($ip,0,strpos($ip,','));
	    }
		$query = "INSERT INTO $table SET userID=$this->userID,startTime='$startTime', startTime_int=$startTime_int, noOfJumps=0, ipaddress='$ip'";
		mysql_query($query) or die(mysql_error());
		$sessionID = mysql_insert_id();
		$this->sessionID = $sessionID;
		return $sessionID;
	}
	
	function checkDuplicateLogin($timeAllowedPerDay, $browser="")
	{
		$duplicateSessionID = -1;
		$check_already_login_query 	=	"SELECT startTime,endTime,sessionID,'2' as subjectno,ipaddress,browser FROM adepts_sessionStatus
										 WHERE userID=$this->userID AND startTime_int = ".date("Ymd")." AND logout_flag=0 AND sessionID<>$this->sessionID 
										 ORDER BY startTime DESC LIMIT 1";
                    /*$check_already_login_query .= " UNION ";
                    $check_already_login_query .= "SELECT startTime,endTime,sessionID,'3' as subjectno FROM adepts_sessionStatus_sc WHERE userID=$userID and logout_flag=0 and isnull(endTime)";*/
		$check_already_login_result	=	mysql_query($check_already_login_query) or die(mysql_error());
		if($check_already_login = mysql_fetch_array($check_already_login_result))
		{
			$lastStartTime = $check_already_login['startTime'];
			$lastEndTime   = $check_already_login['endTime'];
			$lastSessionID = $check_already_login['sessionID'];
			$ipaddress	=	$check_already_login['ipaddress'];
			$prevBrowser	=	$check_already_login['browser'];
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];   //Needed since apps behind a load balancer
			if(strpos($ip,',') !== false) {
				$ip = substr($ip,0,strpos($ip,','));
			}
			if($lastEndTime=='')
			{                
				if($timeAllowedPerDay=="")
					$timeAllowedPerDay = 30;
				$startTime = strtotime(date("Y-m-d H:i:s"));                
				$startTime = $startTime - ($timeAllowedPerDay * 60);
				$startTime = date("Y-m-d H:i:s",$startTime);
				if($lastStartTime >= $startTime)
				{
					if($ipaddress==$ip && $browser==$prevBrowser)
					{
						mysql_query("UPDATE ".TBL_SESSION_STATUS." SET logout_flag=1 WHERE sessionID=".mysql_escape_string($lastSessionID)) or die(mysql_error());
					}
					else
					{
						$duplicateSessionID = $lastSessionID;
					}
				}
			}
		}		
		return $duplicateSessionID;
	}
	
	function updateBrowserDetails($browser)
	{
		$update_browser = 'UPDATE '.TBL_SESSION_STATUS.' SET browser="'.mysql_real_escape_string($browser).'" WHERE sessionID='.$this->sessionID;
		mysql_query($update_browser);
	}
	
	function setEndTime($from)
	{
		switch ($from)
		{
	
			case "1" :
				$endtype = "2"; //user has pressed end session button (Clean Exit)
				break;
	
			//Weekly limit no longer exits
			/*case "-5" :
				$endtype = "3";//quota of day is over (Daily Quota Over)
				break;*/
	
			case "-7" :
				$endtype = "1";//session time limit of 30 minutes is over (Session Time Over)
				break;
			case "-6" :
				$endtype = "3";//quota of day is over (Daily Quota Over)
				break;
	
			case "6" :
				$endtype = "4";//session time out due to inactivity (Session inactivity)
				break;
	
			default :
				$endtype = "5";//default case (Unknown reason)
		}
		$query = "UPDATE ".TBL_SESSION_STATUS." SET endTime='".date("Y-m-d H:i:s")."', endType=concat_ws(',',endType,'".$endtype."') WHERE sessionID=".$this->sessionID;
		mysql_query($query);
	
		if(SUBJECTNO==2)
		{
			$query = 'SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct d.gameID) totgms,  count(distinct e.srno) totmonrev, count(distinct f.srno) tottoprev
			FROM (((((adepts_sessionStatus z LEFT JOIN '.TBL_QUES_ATTEMPT_CLASS.' a on z.sessionID=a.sessionID)
			LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
			LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
			LEFT JOIN adepts_userGameDetails d on z.sessionID=d.sessionID)
			LEFT JOIN adepts_revisionSessionDetails e on z.sessionID=e.sessionID)
			LEFT JOIN adepts_topicRevisionDetails f on z.sessionID=f.sessionID
			WHERE z.sessionID='.$this->sessionID.' GROUP BY z.sessionID';
	
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
			$query = 'SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct f.srno) tottoprev
			FROM (adepts_sessionStatus_sc z LEFT JOIN adepts_teacherTopicQuesAttempt_sc a on z.sessionID=a.sessionID)
			  	LEFT JOIN adepts_topicRevisionDetails_sc f on z.sessionID=f.sessionID
			WHERE z.sessionID='.$this->sessionID.' GROUP BY z.sessionID';
	
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
}

?>