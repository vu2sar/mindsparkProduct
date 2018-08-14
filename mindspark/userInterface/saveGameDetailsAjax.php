<?php

include("check1.php");
include_once("constants.php");
include("functions/comprehensiveModuleFunction.php");
if($windowName!="" && $windowName != $_SESSION["windowName"])
{
	echo "error.php";
	exit();
}
if(isset($_SESSION['userID']))
{
    $userID         = $_SESSION['userID'];
    $sessionID      = $_POST['sessionID'];
	if($sessionID != $_SESSION["sessionID"])  //check for mantis ID - 9204
		exit();
	$mode			= $_POST['mode'];
    $srno			= $_POST['gameAttempt_srno'];
	$gameID			= $_POST['gameID'];
	$totalScore     = $_POST['totalScore'];
	$timeTaken		= $_POST['timeTaken'];
	
	$extraParams    = $_POST['extraParams'];
	$extraParams	= str_replace("√", "&#8730;", $extraParams);
	$ttAttemptID    = $_POST['ttAttemptID'];
	$completed     = $_POST['completed'];
	$gameCodeParam  = $_POST['gameCode'];
	$gameMode		= $_POST['gameMode'];
	$type			= $_POST['type'];
	$noOfJumps		= 0;
	$score			= 0;
//added for new format
	$levelsAttempted = $_POST['levelsAttempted'];
	$levelWiseStatus = $_POST['levelWiseStatus'];
	$levelWiseScore = $_POST['levelWiseScore'];
	$levelWiseTimeTaken = $_POST['levelWiseTimeTaken'];
	$currentLevel = $_POST['currentLevel'];
	$activityFormat = $_POST['activityFormat'];
//-----	
	
	if($completed==1) //student will get sparkie up to third attempt
	{
		if($activityFormat=="new")
		{
			$levelsAttemptedArr		=	explode("|",$levelsAttempted);
			$levelWiseStatusArr		=	explode("|",$levelWiseStatus);
			$levelWiseScoreArr		=	explode("|",$levelWiseScore);
			$levelWiseTimeTakenArr	=	explode("|",$levelWiseTimeTaken);
			$extraParamsArr			=	explode("|",$extraParams);
			$timeTaken	=	array_sum($levelWiseTimeTakenArr);
			for($n=0;$n<count($levelsAttemptedArr);$n++)
			{
				$level	=	str_replace("L","",$levelsAttemptedArr[$n]);
				$sq	=	"UPDATE adepts_activityLevelDetails
						 SET score='".$levelWiseScoreArr[$n]."', timeTaken='".$levelWiseTimeTakenArr[$n]."', status='".$levelWiseStatusArr[$n]."', 
						 extraParams='".$extraParamsArr[$n]."' WHERE srno=".$srno." AND level=".$level." AND type='$type'";
				$rs	=	mysql_query($sq);
			}
			$sqScore	=	"SELECT SUM(score),SUM(timeTaken) FROM adepts_activityLevelDetails WHERE srno=".$srno;
			$rsScore	=	mysql_query($sqScore);
			$rwScore	=	mysql_fetch_array($rsScore);
			$score	=	$rwScore[0];
			$timeTaken	=	$rwScore[1];
		}
		$noOfJumps=0;
		if($score>=100)
			$noOfJumps=5;
		else if($score>=75 && $score<=99)
			$noOfJumps=4;
		else if($score>=50 && $score<=74)
			$noOfJumps=3;
		else if($score>=25 && $score<=49)
			$noOfJumps=2;
		else if($score>=1 && $score<=24)
			$noOfJumps=1;
		else if($score!=-1)
			$noOfJumps=3;
		$attemptNo	=	activityAttemptNo($userID,$gameID);
		$noOfJumps	=	$noOfJumps - $attemptNo;
		
		if($noOfJumps < 0)
			$noOfJumps = 0;
		/*
		Commented code on 4th July 2014 (To check why large amount of sparkies are given to user)
		if($noOfJumps > 0){
			addSparkies($noOfJumps, $sessionID);
			$_SESSION['sparkie']['game']= $_SESSION['sparkie']['game'] + $noOfJumps;
		}
		else
			$noOfJumps=0;
		*/
		if($_SESSION["comprehensiveModule"]!="")
		{
			$_SESSION['game'] = false;			
			if($remedialItemAttemptID!="")
				$activityAttemptID	=	$remedialItemAttemptID;
			else
				$activityAttemptID	=	$remedialAttemptID;
			$sq	=	"UPDATE adepts_userComprehensiveFlow SET activityAttemptID=$srno ,timeTaken='$timeTaken', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"]." and moduleType='activity'";
			$rs	=	mysql_query($sq);
			getNextComprehensiveInflow();
		}
		if($_SESSION["subModule"]!="")
		{
			$query = "SELECT timeTaken FROM educatio_adepts.adepts_userGameDetails WHERE srno=$srno";
			$resGame=mysql_query($query);
			$gameLine=mysql_fetch_array($resGame,MYSQL_ASSOC);
			$timeTaken = $gameLine['timeTaken'];
			$_SESSION['game'] = false; 
			if($remedialItemAttemptID!="")
				$activityAttemptID	=	$remedialItemAttemptID;
			else
				$activityAttemptID	=	$remedialAttemptID;
			
			$sq	=	"UPDATE kst_userFlowDetails SET activityAttemptID=$srno ,timeTaken=$timeTaken, status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"]." ";
			//echo $sq;die;
			$rs	=	mysql_query($sq);
			getNextKstSubModuleInflow();
		} 
		$querygame="select  REPLACE(gameDesc,\"'\",\"`\"),thumbImg,type from adepts_gamesMaster where gameId=".$gameID;
		$resGame=mysql_query($querygame) or die(mysql_error().$querygame);
		$gameLine=mysql_fetch_array($resGame);
		$querysrno="select srno from adepts_userFeeds where actID=".$gameID." AND userid=".$userID;
		$ressrno=mysql_query($querysrno) or die(mysql_error().$querysrno);
		$flag=0;
		while($line1=mysql_fetch_array($ressrno)){
			if($line1[0]==$srno)
				$flag=1;
		}
		if($score>0)
			$totalScore=$score;
		if(!$flag && ($gameLine[2]=='enrichment' || $gameLine[2]=='game')){
			if($gameLine[2]!='enrichment')
				$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES ($userID, '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", '".$_SESSION['schoolCode']."', '".$gameID."', '".$gameLine[0]."', '".$gameLine[1]."','".$totalScore."', $timeTaken, $srno , 'game')";
			else
				$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES ($userID, '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", '".$_SESSION['schoolCode']."', '".$gameID."', '".$gameLine[0]."', '".$gameLine[1]."','".$totalScore."', $timeTaken, $srno , 'enrichment')";
			mysql_query($query2) or die(mysql_error().$query2);
		}
	}
	else if($activityFormat=="new")
	{
        if($levelWiseStatus!=0){
		    $sq	=	"UPDATE adepts_activityLevelDetails
				     SET score='".$levelWiseScore."',timeTaken='".$levelWiseTimeTaken."',status='".$levelWiseStatus."',extraParams='".$extraParams."' 
				     WHERE srno=".$srno." AND level=".$currentLevel." AND type='$type'";
		    $rs	=	mysql_query($sq);
        }
	}
	
	if($mode!="saveRmedialLevel")
	{
		if($score>0)
			$totalScore=$score;
		$query	=	"UPDATE adepts_userGameDetails SET  timeTaken=timeTaken+15, score='$totalScore', extraParams='$extraParams', sessionID=$sessionID,
					 completed=$completed, noOfJumps=$noOfJumps WHERE srno=$srno AND completed=0";
		$exec_query = mysql_query($query);
		if($exec_query) {
			if($noOfJumps > 0) {
				addSparkies($noOfJumps, $sessionID);
				$_SESSION['sparkie']['game']= $_SESSION['sparkie']['game'] + $noOfJumps;
			}
		} else {
			die(mysql_error().$query);
		}
		
		foreach($_SESSION['classSpecificClustersForTT'] as $key=>$val)
		{
			if($val[0]===$gameID)
			$arrPos = $key;
		}
		
		$_SESSION['classSpecificClustersForTT'][$arrPos][3] = "attempted";
	}

	$gameCode = "";
	if($gameID	== "22")
		$gameCode = "FPH";
	if($gameID	== "23")
		$gameCode = "HN";
	if($gameID	== "24")
		$gameCode = "NB";
	if($gameID	== "25")
		$gameCode = "DA";

	if($completed == "1" || $completed == "3")
	{
	    if($gameMode == "DCTstage")
	    {
    		$status = "";
    		if($gameID == "23")
    			$status = "$gameCode-$extraParams||";
    		else if($gameCodeParam == "")
    			$status = "$gameCode||";
    		else
    			$status = "$gameCode-$gameCodeParam||";

    		$sql = "UPDATE adepts_dctDetails SET status = CONCAT_WS('',status,'$status') WHERE ttAttemptID = '$ttAttemptID'";
    	    mysql_query($sql) or die(mysql_error());
    		$sql = "UPDATE adepts_dctDetails SET current = '' WHERE ttAttemptID = '$ttAttemptID'";
    	    mysql_query($sql) or die(mysql_error().$sql);
	    }
	    else if($gameMode=="afterCluster")
	    {
			$query = "UPDATE ".TBL_CURRENT_STATUS." SET gameID='' WHERE userID=$userID AND ttAttemptID=$ttAttemptID";
	        mysql_query($query);
	        $_SESSION['game'] = false;
	    }
	    else if ($gameMode=="researchModule")
	    {
	    	$rmCode = $_SESSION['rmCode'];
	    	$diagnosticCodeStr = "";
	    	if($gameID==49 || $gameID==77)
	    	{
	    		$tmpArray = explode("~",$extraParams);
	    		$query = "SELECT gameAttemptIDs FROM adepts_researchModuleAttemptDetails WHERE userID=$userID AND ttAttemptID = '$ttAttemptID' AND rmCode='$rmCode'";
	    		$result = mysql_query($query);
	    		$line = mysql_fetch_array($result);
	    		if($line[0]=="")
	    			$diagnosticCodeStr = ", preDiagnosticCode='".$tmpArray[1]."'";
	    		else
	    			$diagnosticCodeStr = ", postDiagnosticCode='".$tmpArray[1]."', status='Completed'";
	    	}
	    	$sql = "UPDATE adepts_researchModuleAttemptDetails SET gameAttemptIDs = if(gameAttemptIDs='',$srno,concat(gameAttemptIDs,',',$srno)), current='' $diagnosticCodeStr WHERE userID=$userID AND ttAttemptID = '$ttAttemptID' AND rmCode='$rmCode'";
    	    mysql_query($sql) or die(mysql_error().$sql);
	    }
	}
}

function addSparkies($noOfSparkies, $sessionID)
{
	$_SESSION["noOfJumps"] =    $_SESSION["noOfJumps"] + $noOfSparkies;
	
	$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps = noOfJumps + $noOfSparkies WHERE sessionID=".$sessionID;
	mysql_query($query);
}

function activityAttemptNo($userID,$gameID)
{
	$sq	=	"SELECT count(*) FROM adepts_userGameDetails WHERE userID=$userID AND gameID=$gameID AND completed=1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

mysql_close();
?>
