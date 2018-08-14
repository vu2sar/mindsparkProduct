<?php

class Sparkies
{
	function Sparkies($userID="")
	{
		$this->arrayRewards	=	array();
		if($userID!="")
		{
			$sq	=	"SELECT startDate,sparkies,theme FROM adepts_rewardPoints WHERE userID=$userID";
			$rs	=	mysql_query($sq);
			$rw	=	mysql_fetch_assoc($rs);
			$this->userID     		= $userID;
			$this->sparkie  		= $rw['sparkies'];
			$this->rewardTheme 		= $rw['theme'];
			$this->startDate 		= $rw['startDate'];
		}
	}

	function getCurrentDaySparkies()
	{
		$sq	=	"SELECT SUM(noOfJumps) FROM adepts_sessionStatus WHERE userID=".$this->userID." AND startTime_int=".date("Ymd");
		//$sq	=	"SELECT SUM(noOfJumps) as sparkie FROM adepts_sessionStatus WHERE userID=".$this->userID." AND startTime_int=".date("Ymd");
		$result	=	mysql_query($sq);
		if($line = mysql_fetch_array($result))
		{
			$noOfSparkies = $line[0];
			$sq = "SELECT SUM(sparkieEarned),SUM(sparkieConsumed) FROM adepts_userBadges
					 WHERE userID=$userID AND batchType!='topicCompletion' AND DATE_FORMAT(lastModified,'%Y%m%d')>=$fromDateInt AND DATE_FORMAT(lastModified,'%Y%m%d')<=$tillDateInt";
			$rs = mysql_query($sq);
			$rw = mysql_fetch_array($rs);
			$noOfSparkies += $rw[0];
			$noOfSparkies -= $rw[1];

		}		
		//$rw	=	mysql_fetch_assoc($result);
		//return $rw["sparkie"];
		return $noOfSparkies;
	}
	
	function updateTheme($theme){
		$sqCheck = "SELECT userID FROM adepts_userBadges WHERE sparkieConsumed>0 AND userID=".$this->userID." and batchType='".$theme."'";
		$rsCheck = mysql_query($sqCheck);
		if(mysql_num_rows($rsCheck)>0 || $theme='default')
		{
			$sq	=	"UPDATE adepts_rewardPoints SET theme='".$theme."' WHERE userID=".$this->userID;
			$rs	=	mysql_query($sq);
			$_SESSION["rewardTheme"] = $theme;
		}
	}
	
	function tradeTheme($theme) { 
		if($theme=="dark" || $theme=="light")
			$sparkie = 30;
		else if($theme=="girl" || $theme=="boy")
			$sparkie = 50;
		$sq	=	"UPDATE adepts_userBadges SET sparkieConsumed=$sparkie WHERE userID=".$this->userID." and batchType='".$theme."'";
		if(mysql_query($sq))
		{
			$sq	=	"UPDATE adepts_rewardPoints SET sparkies=sparkies-$sparkie WHERE userID=".$this->userID;
			$rs	=	mysql_query($sq);
		}
	}
	
	function getTotalSparkies()
	{
		return $this->getCurrentDaySparkies() + $this->sparkie;
	}
	
	function getSparkieLogic($badge="")
	{
		$sq	=	"SELECT logicID, logicType, logicStartDate, sparkieNeeded, sparkieAwarded, logicDesc, extraInfo FROM adepts_sparkieLogic";
		if($badge=="badge")
		{
			$sq	.=	" where logicDesc='$badge'";
		}
		$sq	.=	" ORDER BY logicStartDate";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_assoc($rs))
		{
			$this->arrayRewards[$rw["logicType"]]["logicID"]			=	$rw["logicID"];
			$this->arrayRewards[$rw["logicType"]]["sparkieNeeded"]		=	$rw["sparkieNeeded"];
			$this->arrayRewards[$rw["logicType"]]["sparkieAwarded"]		=	$rw["sparkieAwarded"];
			$this->arrayRewards[$rw["logicType"]]["logicStartDate"]		=	$rw["logicStartDate"];
			$this->arrayRewards[$rw["logicType"]]["logicDesc"]			=	$rw["logicDesc"];
			$this->arrayRewards[$rw["logicType"]]["logicType"]			=	$rw["logicType"];
			$this->arrayRewards[$rw["logicType"]]["extraInfo"]			=	$rw["extraInfo"];
		}
		return $this->arrayRewards;
	}
	
	function checkForBadgesEarned($notNotified="")
	{
		$badgesEarnedArray	=	array();
		$i=0;
		$sq	=	"SELECT batchType, batchDate, sparkieEarned, sparkieConsumed, notification FROM adepts_userBadges WHERE userID=".$this->userID;
		if($notNotified!="")
			$sq	.=	" AND notification=0";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_assoc($rs))
		{
			$badgesEarnedArray[$i][$rw["batchType"]]["batchDate"]			=	$rw["batchDate"];
			$badgesEarnedArray[$i][$rw["batchType"]]["sparkieEarned"]		=	$rw["sparkieEarned"];
			$badgesEarnedArray[$i][$rw["batchType"]]["sparkieConsumed"]		=	$rw["sparkieConsumed"];
			$badgesEarnedArray[$i][$rw["batchType"]]["notification"]		=	$rw["notification"];
			$i++;
		}
		return $badgesEarnedArray;
	}
	
	function previousBadgesEarned()
	{
		$badgesEarnedArray	=	array();
		$i=0;
		$sq	=	"SELECT batchType, batchDate FROM adepts_userBadges_archive WHERE userID=".$this->userID;
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_assoc($rs))
		{
			$badgesEarnedArray[$i][$rw["batchType"]]["batchDate"]			=	$rw["batchDate"];
			$i++;
		}
		return $badgesEarnedArray;
	}
	
	function getPendingNotification()
	{
		$pendingNotification	=	$this->checkForBadgesEarned(1);
		return $pendingNotification;
	}
	
	function giveTopicReward($teacherTopiCode)
	{
		$arrayRewards	=	$this->getSparkieLogic();
		$this->insertUserBadge("topicCompletion",$arrayRewards["topicCompletion"]["sparkieAwarded"],0,0,$teacherTopiCode);
		$_SESSION['sparkie']['topicCompletion']	+=	$arrayRewards["topicCompletion"]["sparkieAwarded"];
	}
	
	function checkForSparkieAlertsAtStart()
	{
		$totalSparkie	=	$this->getTotalSparkies();
		$arrayPrompts	=	array();
		$arrayPrompts["minValue"]="";
		$arrayRewards	=	$this->getSparkieLogic();
		$userRewardsArr	=	$this->userAllRewards();
		foreach($arrayRewards as $rewardType=>$rewardDetails)
		{
			if(!in_array($rewardType,$userRewardsArr) && $totalSparkie < $rewardDetails["sparkieNeeded"] && ($rewardDetails["sparkieNeeded"]-$totalSparkie) < 300)
			{
				$arrayPrompts[$rewardType]["sparkieNeeded"]	=	$rewardDetails["sparkieNeeded"];
				if($arrayPrompts["minValue"]=="")
					$arrayPrompts["minValue"]	=	$rewardDetails["sparkieNeeded"]-$totalSparkie;
				else if($arrayPrompts["minValue"]>$rewardDetails["sparkieNeeded"]-$totalSparkie)
					$arrayPrompts["minValue"]	=	$rewardDetails["sparkieNeeded"]-$totalSparkie;
			}
		}
		return $arrayPrompts;
	}
	
	function userAllRewards()
	{
		$userRewardsArr	=	array();
		$sq	=	"SELECT DISTINCT batchType FROM adepts_userBadges WHERE userID=".$this->userID;
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$userRewardsArr[]	=	$rw[0];
		}
		return $userRewardsArr;
	}
	
	function checkForSparkieAlertsCurrent()
	{
		$dispBadgeType	=	"";
		$totalSparkie	=	$this->getTotalSparkies();
		$arrCurrentPrompt	=	$_SESSION["arrayPrompts"];
		foreach($arrCurrentPrompt as $badgeType=>$rewardDetails)
		{
			if($totalSparkie >= $rewardDetails["sparkieNeeded"] && $badgeType!="minValue")
			{
				unset($_SESSION["arrayPrompts"][$badgeType]);
				$this->insertUserBadge($badgeType,0,0,1);
				$dispBadgeType	=	$badgeType;
			}
		}
		unset($_SESSION["arrayPrompts"]["minValue"]);
		if($dispBadgeType=="mileStone1")
			$_SESSION['sparkieImage']='level2';
		else if($dispBadgeType=="mileStone2")
			$_SESSION['sparkieImage']='level3';
		return $dispBadgeType;
	}
	
	function insertUserBadge($badgeType,$sparkieEarned=0,$sparkieConsumed=0,$view=1,$badgeDescription="")
	{
		$sq	=	"INSERT INTO adepts_userBadges
		SET batchType='$badgeType' ,userID=".$this->userID.", sparkieEarned=$sparkieEarned, sparkieConsumed=$sparkieConsumed, badgeDescription='$badgeDescription',
		notification=$view, batchDate='".date('Y-m-d')."'";
		mysql_query($sq);
        //feeds triggr
		if($_SESSION['subcategory']=="School"&&$_SESSION['schoolCode']!=""){
			$arrayRewards=$this->getSparkieLogic();
			if($arrayRewards[$badgeType]['logicType']=='mileStone1'||$arrayRewards[$badgeType]['logicType']=='mileStone2'||$arrayRewards[$badgeType]['logicType']=='mileStone3')
				$batchtype='milestone';
			else if($arrayRewards[$badgeType]['logicDesc']=='badge')
				$batchtype='badge';
			if($batchtype=='badge'||$batchtype=='milestone'){
				$query2 = "INSERT INTO  adepts_userFeeds (userID, studentIcon, childName, childClass, schoolCode, actID, actDesc, actIcon, score, timeTaken, srno, ftype) VALUES(".$this->userID.", '', '".$_SESSION['childName']."', ".$_SESSION['childClass'].", ".$_SESSION['schoolCode'].", '".$arrayRewards[$badgeType]['logicID']."', '".$arrayRewards[$badgeType]['extraInfo']."', '',0, 0, '".$this->userID."~".$badgeType."', '".$batchtype."')";
				mysql_query($query2) or die(mysql_error());        
			}

		}

	}
	
	function updateUserBadge($badgeType)
	{
		$sq	=	"UPDATE adepts_userBadges SET notification=1 WHERE batchType='$badgeType' AND userID=".$this->userID;
		mysql_query($sq);
	}
	
	function getSparkieLevel()
	{
		$this->getSparkieLogic();
		$mySparkies = $this->getTotalSparkies();
		$milestone1 = $this->arrayRewards["mileStone1"]["sparkieNeeded"];
		$milestone2 = $this->arrayRewards["mileStone2"]["sparkieNeeded"];
		
		if($mySparkies>$milestone2)
			return 3;
		else if($mySparkies>$milestone1)
			return 2;
		else
			return 1;
	}
	
	function recentBadge()
	{
		$recentBadge	=	array();
		$query = "select distinct batchType from adepts_userBadges a join adepts_sparkieLogic b on a.batchType=b.logicType where userID=".$this->userID." and b.logicDesc='badge' and a.batchType!='sparkieStar' order by a.lastModified desc limit 3";
		$result = mysql_query($query) or die(mysql_error());
		while($line = mysql_fetch_array($result))
		{
			$recentBadge[]= $line[0];
		}
		return $recentBadge;
	}
}

?>