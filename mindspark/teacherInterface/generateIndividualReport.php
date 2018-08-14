<?php
	//2D array to store the student details:
	//	0-> childname  1->childClass-section 2->days(sessions) 3->login time 4->total ques 5->% correct 6-> avg time to ans 7-> Topics attempted during that period
	$studentDetails = array();
	$fromDateInt = str_replace("-","",$fromDate);
	$tillDateInt = str_replace("-","",$tillDate);
	
	$query = "SELECT userID, childName, childClass, childSection FROM adepts_userDetails
			  WHERE  schoolCode=$schoolCode AND category='STUDENT'";

	if(strcasecmp($category,"School Admin")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";

	$query .= " AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%' ";

	if($class!="")
		$query .= " AND childClass = '".$class."'";
	if($section!="")
		$query .= " AND childSection = '".$section."'";
	elseif($hasSections)
	{
	   if($class!="")
	   {
	       for($j=0; $j<count($classArray) && $classArray[$j]!=$class; $j++);
	       $secStr = $sectionArray[$j];
	       if($secStr!="")
	       	$query .= " AND childSection in (".$sectionArray[$j].")";
	   }
	}
	$query .= " ORDER BY childClass, childSection, childName ";

	$result = mysql_query($query);

	while($line=mysql_fetch_array($result))
	{
		$userID                     = $line['userID'];
		$studentDetails[$userID][0] = $line['childName'];
		$studentDetails[$userID][1] = $line['childClass'].$line['childSection'];
		$childSection[$userID] = $line['childSection'];
		$studentDetails[$userID][2] = "0 (0)";
		$timeSpent = getTimeSpent($line['userID'], $fromDateInt, $tillDateInt, $line['childClass']);
        $studentDetails[$line['userID']][3] = $timeSpent;
		//$studentDetails[$userID][3] = "00:00:00";
		$studentDetails[$userID][4] = "";
		$studentDetails[$userID][5] = "";
		$studentDetails[$userID][6] = "";
		$studentDetails[$userID][7] = "";
		$studentDetails[$userID][8] = "0";
		$studentDetails[$userID][9] = "0";
		$studentDetails[$userID][10] = "0";
		$studentDetails[$userID][11] = "0";
		$studentDetails[$userID][12] = getNCERTQuestion($userID, $fromDate, $tillDate);
	}

	if(mysql_num_rows($result)==0)
	{
		echo "<div id='noRecords'><center>No records found!</center></div>";
	}
	else
	{

	$userIDArray = array_keys($studentDetails);

	//For highlighting students answering randomly: %correct < 75 & avg. time taken per question < 30th percentile for the school/class combo & attempted more than 50 questions.

	/*$count_query = "SELECT count(srno) FROM ".TBL_SESSION_STATUS." b, ".TBL_QUES_ATTEMPT." c
	                WHERE  b.userID=c.userID AND b.sessionID=c.sessionID AND b.userID in (".implode(",",$userIDArray).")
	 						AND startTime between '$fromDate' AND '$tillDate 23:59:59'";

	$count_result = mysql_query($count_query);
	$count_line   = mysql_fetch_array($count_result);
	$totalRecords = $count_line[0];

	//Fetch the 30th percentile based on no. of records
	$percentile = 30;
	$recordToFetch = round($totalRecords*$percentile/100);

	$percentile_query = "SELECT S FROM ".TBL_SESSION_STATUS." b, ".TBL_QUES_ATTEMPT." c
	                     WHERE  b.userID=c.userID AND b.sessionID=c.sessionID AND b.userID in (".implode(",",$userIDArray).")
	  							AND startTime between '$fromDate' AND '$tillDate 23:59:59'";
	$percentile_query .= " ORDER BY S limit $recordToFetch,1";

	$percentile_result = mysql_query($percentile_query) or die(mysql_error());
	$percentile_line   = mysql_fetch_array($percentile_result);
	$compareTime = $percentile_line[0];*/
	$compareTime = 5;
	//echo $compareTime;	

	$query = "SELECT b.userID, count(c.srno) questotal, sum(S) avgTime, sum(if(R=1,1,0)) perRight,
					 count(distinct startTime_int) days, count(distinct b.sessionID) sessions";
	if($chkTopicsAttempted)
	   $query .= " , group_concat(distinct teacherTopicCode ORDER BY srno DESC) as topicsAttempted";
  	$query.=" FROM 	 ".TBL_SESSION_STATUS." 		b LEFT JOIN
					 ".TBL_QUES_ATTEMPT."_class$class c
			  ON     b.userID=c.userID AND b.sessionID=c.sessionID
			  WHERE  b.userID in (".implode(",",$userIDArray).")
					 AND startTime_int >= $fromDateInt AND startTime_int <= $tillDateInt";
	$query .= " GROUP BY userID ";

	//echo $query;
	//exit;
	$result = mysql_query($query) or die(mysql_error());

	$colSpan = $class<3?3:4;
?>
<p>
<?php
	ob_start();
?>
	
		<table id="pagingTable">
	        <td width="35%">CLASS <?=$class?><?=$section?> (<b style="text-decoration:underline;font-size:0.9em;"><a href='javascript:download();'>Download Table In Excel</a></b>)</td>
			<td>
				<div class="textRed">NOTE : All underlined fields below can be sorted. Click on field name to sort.</div>
			</td>
		</table>
		<table border="1" align="center" cellpadding="3" cellspacing="0" class="gridtable"  width="92%">
			<thead>
			<tr>
				<td class="header">&nbsp;</td>
				<td colspan="7" align="center" class="header"><label>Regular Questions</label></td>
				<td colspan="<?=$colSpan?>" align="center" class="header"><label>Other Tasks</label></td>
				<td class="header">&nbsp;</td>
			</tr>
			<tr>
			  	<td class="header" onclick="sortColumn(event)" type="Number"><u>Sr.<br/>No.</u></td>
			    <td class="header" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Name</u></td>
				<td class="header" onclick="sortColumn(event)" type="Number"><u>Class</u></td>
				<td class="header">No of days <br>Logged in <br>(Sessions)</td>
				<td class="header" onclick="sortColumn(event)" type="ttProgress"><u>Total Login<br/>Time <br>(hh:mm:ss)</u></td>
			    <td class="header" onclick="sortColumn(event)" type="Number"><u>Total Ques<br/>Attempted</u></td>
			    <td class="header" onclick="sortColumn(event)" type="Number"><u>% Correct</u></td>
			    <td class="header" onclick="sortColumn(event)" type="Number"><u>Avg Time Taken<br/>to ans<br/>(sec)</u></td>
			    <?php if($chkTopicsAttempted) { ?>
			    <td class="header" rowspan="<?=$rowSpan?>">Topics<br/>attempted</td>
			    <?php } ?>
		  		<?php if($class>=3) { //CQ applicable only for class 3 onwards?>
		  		<td class="header" onclick="sortColumn(event)" type="Number"><u>C.Q.</u></td>
		  		<?php } ?>
		  		<td class="header" onclick="sortColumn(event)" type="Number"><u>Practice Ques</u></td>
		  		<td class="header" onclick="sortColumn(event)" type="Number"><u>Timed Tests</u></td>
                <td class="header" onclick="sortColumn(event)" type="Number"><u>No. of activities</u></td>
		  		<td class="header" onclick="sortColumn(event)" type="Number"><u>NCERT<br>Homework</u></td>
		  		<!--<td class="header" onclick="sortColumn(event)" type="Number"><u>Remedial Item</u></td>--><!-- Uncomment once remedial item report is added -->
				<td class="header" onclick="sortColumn(event)" type="Number"><u><?php if($class>=8) echo "Reward Points"; else echo "Sparkies"; ?></u></td>
		  	</tr>
		  	</thead>
	<?php
		$topicsAttemptedArray = array();
		$max_ques = $max_correct = $max_avgTime = $max_loginTime = 0;

		$max_ques_srno = $max_correct_srno = $max_avgTime_srno = $max_loginTime_srno = "";
		$min_ques = $min_correct = $min_avgTime = 100000;
		$min_loginTime = 100000;
	    $userTimeSpentArray = array();
		while ($line=mysql_fetch_array($result))
		{
			$studentDetails[$line['userID']][2] = $line['days']." (".$line['sessions'].")";
		    /*$timeSpent = getTimeSpent($line['userID'], $fromDate, $tillDate);
            $studentDetails[$line['userID']][3] = $timeSpent;*/
		    $timeSpent = $studentDetails[$line['userID']][3];
			$studentDetails[$line['userID']][4] = $line['questotal'];
			if($line['questotal']>0)
			{
			    $studentDetails[$line['userID']][5] = round($line['perRight']/$line['questotal']*100,2);
			    $studentDetails[$line['userID']][6] = round($line['avgTime']/$line['questotal'],1);
			}
			if($chkTopicsAttempted) {
    			$studentDetails[$line['userID']][7] = $line['topicsAttempted'];
    			$topicsAttemptedArray = array_merge($topicsAttemptedArray, explode(",",$line['topicsAttempted']));
			}

			if(SUBJECTNO==2)
			{
				//Get the details of other things done during this time:
				$query = "SELECT  z.userID, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct f.srno) tottopicrev, count(remedialAttemptID) remedialAttempts
				   		  FROM (((".TBL_SESSION_STATUS." z
								LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
								LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
								LEFT JOIN ".TBL_TOPIC_REVISION." f on z.sessionID=f.sessionID)
								LEFT JOIN adepts_remedialItemAttempts d on z.sessionID=d.sessionID
						  WHERE z.userID = ".$line['userID']." AND startTime_int >= $fromDateInt AND startTime_int <= $tillDateInt GROUP BY userID";
				$other_result = mysql_query($query) or die(mysql_error());
				$other_line   = mysql_fetch_array($other_result);
				$studentDetails[$line['userID']][8] = $other_line['totalcq'];
				$studentDetails[$line['userID']][9] = $other_line['totTmTst'];
				$studentDetails[$line['userID']][10] = $other_line['tottopicrev'];
				$studentDetails[$line['userID']][11] = $other_line['remedialAttempts'];
				$studentDetails[$line['userID']][15] = getGameDetails($line['userID'],$fromDate,$tillDate);
			}
			$studentDetails[$line['userID']][13] = getNoOfSparkies($line['userID'], $class, $fromDateInt, $tillDateInt);
			
			$timeSpentArray = explode(":",$timeSpent);
			$timeInSecs = (3600*intval($timeSpentArray[0])) + (60*intval($timeSpentArray[1])) + intval($timeSpentArray[2]);
			$timeInSecs = intval($timeInSecs);

			if($max_loginTime < $timeInSecs)
			{
				$max_loginTime = $timeInSecs;
			}
			if($min_loginTime > $timeInSecs)
			{
				$min_loginTime = $timeInSecs;
			}
			if($max_ques < $line['questotal'])
			{
				$max_ques = $line['questotal'];
			}
			if($max_correct<$studentDetails[$line['userID']][5])
			{
				$max_correct = $studentDetails[$line['userID']][5];
			}
			if($max_avgTime<$studentDetails[$line['userID']][6])
			{
				$max_avgTime = $studentDetails[$line['userID']][6];
			}
			if($min_ques>$line['questotal'])
			{
				$min_ques = $line['questotal'];
			}
			if($min_correct>$studentDetails[$line['userID']][5])
			{
				$min_correct = $studentDetails[$line['userID']][5];
			}
			if($min_avgTime>$studentDetails[$line['userID']][6])
			{
				$min_avgTime = $studentDetails[$line['userID']][6];
			}
		}
		$topicsAttemptedArray = array_unique($topicsAttemptedArray);
		if(count($topicsAttemptedArray)>0)
		{
			$topicArray = array();
			$query  = "SELECT teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode in ('".implode("','", $topicsAttemptedArray)."')";
			$result = mysql_query($query) or die(mysql_error());
			while ($line = mysql_fetch_array($result)) {
				$topicArray[$line[0]] = $line[1];
			}
		}


		$srno = 1;
		for($studentCounter=0; $studentCounter<count($userIDArray); $studentCounter++)
		{
		    $userID = $userIDArray[$studentCounter];
		    $timeSpent = $studentDetails[$userID][3];
			$timeSpentArray = explode(":",$timeSpent);

			$timeInSecs = (3600*intval($timeSpentArray[0])) + (60*intval($timeSpentArray[1])) + intval($timeSpentArray[2]);
			$timeInSecs = intval($timeInSecs);
			$clsTime = $clsQues = $clsCorrect = $clsAvgTime = "";
			if($max_loginTime!="" && $timeInSecs==$max_loginTime)
			    $clsTime = "highest";
			if($min_loginTime!="" && $timeInSecs==$min_loginTime)
			    $clsTime = "lowest";
			if($max_ques!="" && $studentDetails[$userID][4]==$max_ques)
			    $clsQues = "highest";
			if($min_ques!="" && $studentDetails[$userID][4]==$min_ques)
			    $clsQues = "lowest";
			if($max_correct!="" && $studentDetails[$userID][5]==$max_correct)
			    $clsCorrect = "highest";
			if($min_correct!="" && $studentDetails[$userID][5]==$min_correct)
			    $clsCorrect = "lowest";
			if($max_avgTime!="" && $studentDetails[$userID][6]==$max_avgTime)
			    $clsAvgTime = "lowest";
			if($min_avgTime!="" && $studentDetails[$userID][6]==$min_avgTime)
			    $clsAvgTime = "highest";




	?>
			<tr <?php if($studentDetails[$userID][4]>=50 && $studentDetails[$userID][5]<75 && $studentDetails[$userID][6]<$compareTime) echo " class=\"purple\""?>>
				<td><?=$srno?></td>
				<td nowrap align="left"><a href="studentWiseReport.php?cls=<?=$class?>&section=<?=$childSection[$userID]?>&studentID=<?=$userID?>" style="text-decoration:underline;"><?=$studentDetails[$userID][0]?></a></td>
				<td><?=$studentDetails[$userID][1]?></td>
				<td><?=$studentDetails[$userID][2]?></td>
				<td id="time<?=$srno?>" class="<?=$clsTime?>"><?=$timeSpent?></td>
				<td id="ques<?=$srno?>" class="<?=$clsQues?>"><?=$studentDetails[$userID][4]?></td>
				<td id="correct<?=$srno?>" class="<?=$clsCorrect?>"><?=$studentDetails[$userID][5]?></td>
				<td id="avgTime<?=$srno?>" class="<?=$clsAvgTime?>"><?=$studentDetails[$userID][6]?></td>
				<?php if($chkTopicsAttempted) { ?>
				<td align="left">
				<?php
					if($studentDetails[$userID][7]=="")	echo "&nbsp;";
					else
					{
						$tmpArray = explode(",",$studentDetails[$userID][7]);

						if(count($tmpArray)==1)
						{
							echo $topicArray[$tmpArray[0]];
						}
						else
						{
							echo "<img src=\"assets/expand.gif\" id=\"img".$userID."\" onclick=\"showHideTopics(".$userID.")\" style=\"cursor:hand;\" title=\"Click to see all topics done\"> ";
							echo "<span id=\"pnlDefaultTopic$userID\">".$topicArray[$tmpArray[count($tmpArray)-1]]."</span>";	//show last topic attempted by default

							echo "<div id=\"pnlAttemptedTopics".$userID."\" style=\"display:none;\">";
	    					echo "<ol>";
	    					for($i=0; $i<count($tmpArray); $i++)
							{
	    					    echo "<li>".$topicArray[$tmpArray[$i]]."</li>";
	    					}
	    					echo "</ol>";
	    					echo "</div>";
						}
					}
				?>
				</td>
				<?php } ?>
				<?php if(SUBJECTNO==2) { ?>
				<?php if($class>=3) { ?>
				<td><?=$studentDetails[$userID][8]?></td>
				<?php } ?>
				<td><?=$studentDetails[$userID][10]?></td>
				<td><?=$studentDetails[$userID][9]?></td>
                <td><?=$studentDetails[$userID][15]?></td>
				<td><?=$studentDetails[$userID][12]?></td>
				<?php } ?>
				<td><?=$studentDetails[$userID][13]?></td>
			</tr>
	<?php
			$srno++;
		}
	?>
		</table>
		<div class="textPurpleBelow"><div id="squarePurple"></div>Student answering very quickly and having high error percentage - may be pressing randomly.</div>
<?php
	$table = ob_get_contents();

	ob_end_clean();

	echo $table;
?>
<form name='frmExcel' target="" action="export.php" method="POST">
	<input type="hidden" name="content" value='<?=strip_tags($table,"<table><tr><td>")?>'>
</form>

</p>

<?php
	}


function convertToTime($date)
{

    $hr   = substr($date,11,2);
    $mm   = substr($date,14,2);
    $ss   = substr($date,17,2);
    $day  = substr($date,8,2);
    $mnth = substr($date,5,2);
    $yr   = substr($date,0,4);
    $time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
    return $time;
}

function getNCERTQuestion($userID, $startDate, $endDate)
{
	$query = "SELECT COUNT(srno) FROM adepts_ncertQuesAttempt WHERE userID='$userID' AND attemptedDate BETWEEN '$startDate' AND '$endDate 23:59:59' AND R!=-1";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$count = $row[0];
	return($count);
}
function getTimeSpent($userID, $startDate, $endDate, $class)
{
    $query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues FROM ".TBL_SESSION_STATUS."
              WHERE  userID=$userID AND startTime_int >= $startDate AND startTime_int <= $endDate";

    $time_result = mysql_query($query) or die(mysql_error());
    $timeSpent = 0;
    while ($time_line = mysql_fetch_array($time_result))
    {

        $startTime = convertToTime($time_line[1]);
        if($time_line[2]!="")
            $endTime = convertToTime($time_line[2]);
        elseif ($time_line[3]!="")
            $endTime = convertToTime($time_line[3]);
        else
        {
            $query = "SELECT max(lastModified) FROM ".TBL_QUES_ATTEMPT."_class$class WHERE sessionID=".$time_line[0]." AND userID=".$userID;
            $r     = mysql_query($query);
            $l     = mysql_fetch_array($r);
            if($l[0]=="")
                continue;
            else
                $endTime = convertToTime($l[0]);
        }
        $timeSpent = $timeSpent + ($endTime - $startTime);    //in secs
    }

    $hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);    //converting secs to hours.
    $timeSpent = $timeSpent%3600;
    $mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
    $timeSpent = $timeSpent%60;
    $secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);

    return $hours.":".$mins.":".$secs;
}

function getSparks($userID, $childClass) 
{
	$noOfSparkies = 0;
	$query = "SELECT sum(noOfJumps) sparkiesCount FROM ".TBL_SESSION_STATUS." WHERE userID=$userID";
	$result = mysql_query($query) or die(mysql_error());
	$line = mysql_fetch_array($result);
	
	if($line['sparkiesCount']!="")
		$noOfSparkies = $line['sparkiesCount'];
		
	$query = "SELECT sum(noOfJumps) sparkiesCount FROM ".TBL_SESSION_STATUS."_archive WHERE userID=$userID";
	$result = mysql_query($query) or die(mysql_error());
	if($line=mysql_fetch_array($result))
	{
		if($line['sparkiesCount']!="")
			$noOfSparkies += $line['sparkiesCount'];
	}
	
	// get email verification sparkie count for class 3 student
	if($childClass > 2) {
		$sparkie_check = "SELECT sparkieEarned FROM adepts_userBadges WHERE userID = ".mysql_real_escape_string($userID)." AND batchType = 'emailVarification'";
		$exec_sparkie_check = mysql_query($sparkie_check);
		if(mysql_num_rows($exec_sparkie_check) > 0) {
			$row_sparkie_check = mysql_fetch_array($exec_sparkie_check);
			if(!empty($row_sparkie_check['sparkieEarned']))
				$noOfSparkies += $row_sparkie_check['sparkieEarned'];
		}
	}
	//if($childClass>=8)
		//$noOfSparkies = $noOfSparkies * 10; //1 Sparkie = 10 Reward points
	return $noOfSparkies;
}

function getNoOfSparkies($userID, $class, $fromDateInt, $tillDateInt)
{
	$noOfSparkies  = 0;
	$query = "SELECT sum(noOfJumps) FROM ".TBL_SESSION_STATUS." WHERE userID=$userID AND startTime_int>=$fromDateInt AND startTime_int <= $tillDateInt";
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result))
	{
		$noOfSparkies = $line[0];
		$sq = "SELECT SUM(sparkieEarned) FROM adepts_userBadges WHERE batchType!='topicCompletion'";
		$rs = mysql_query($sq);
		$rw = mysql_fetch_array($rs);
		$noOfSparkies += $rw[0];
		//if($class>=8)
			//$noOfSparkies = $noOfSparkies*10;	//1 sparkie = 10 reward points	
	}
	return $noOfSparkies;
}

function getGameDetails($userID,$fromDate,$tillDate)
{
	$sq	=	"SELECT COUNT(*) FROM adepts_userGameDetails WHERE userID=$userID AND attemptedDate BETWEEN '$fromDate' AND '$tillDate 23:59:59'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}
?>