<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");
include("../userInterface/constants.php");
//include("/functions/dashboardFunctions.php");
include("../slave_connectivity.php");
?>	
<script type="text/javascript">
	$(document).ready(function(e) {
		
     $('.loadSparkies').click(function (){
     	$("#content").remove();
     	var html = '<div id="content" style="display: none"><div id="content-holder"></div></div>';
     	$("#ajaxData").append(html);
     	$('#content').dialog({
             resizable: false,
             height: 500,
             width: 700,
             title: "Daywise sparkies",
             modal: true,
             create: function(event, ui) {
			  $("body").css({ overflow: 'hidden' })
			 },
			 beforeClose: function(event, ui) {
			  $("body").css({ overflow: 'inherit' })
			 }
          });
         $("#content-holder").load($(this).attr("href"));
         $("#content").dialog('open');
         return false;
     });
 });
</script>
<style>
#content table.gridtable{
	border-width:0px !important;
}
</style>
<?php
//print_r($_POST);
$schoolCode = isset($_POST['schoolCode'])?$_POST['schoolCode']:$_SESSION['schoolCode'];
$class    = isset($_POST['class'])?$_POST['class']:"";
$section  = isset($_POST['section'])?stripslashes($_POST['section']):"";
$tillDate = isset($_POST['tillDate'])?$_POST['tillDate']:date("d-m-Y");
$lastWeek = date("d-m-Y",strtotime("-1 day"));
$fromDate = isset($_POST['fromDate'])?$_POST['fromDate']:$lastWeek;
$chkTopicsAttempted = (!empty($_POST['chkTopicsAttempted']))?1:0;
$chkOtherTask=(!empty($_POST['chkOtherTask']))?1:0;
$today = date("Y-m-d");
$tillDate = date('Y-m-d', strtotime(str_replace('-', '-', $tillDate)));
$fromDate = date('Y-m-d', strtotime(str_replace('-', '-', $fromDate)));

$secArray=explode(",",$section);
$strsection= "'".implode("','", $secArray)."'";

if(isset($_POST['class']))
{
	//echo "save is pressed";
	//exit;
	
	$today = date("Y-m-d");
	//$tillDate = date('Y-m-d', strtotime(str_replace('-', '-', $tillDate)));
	//$fromDate = date('Y-m-d', strtotime(str_replace('-', '-', $fromDate)));
	if($fromDate < $startDate)
		echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>Invalid Date - The program started only on ".substr($startDate,8,2)."-".substr($startDate,5,2)."-".substr($startDate,0,4)."!</span>";
	elseif($tillDate > $today)
		echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>Invalid Date - The date cannot be greater than the current date!</span>";
	elseif($fromDate > $tillDate)
		echo "<span style='color:red; font-weight:bold;margin-left: 33%;'>Invalid Date - From date cannot be greater than To date!</span>";
	else
	{
		$canAccess = true;
		if(!$canAccess) {
			echo "<table id='pagingTable'><tr><td width='35%'>CLASS ".$class.$section." <!--(<b style='text-decoration:underline;font-size:0.9em;'><a href='javascript:download();'>Download Table In Excel</a></b>)--></td><td><div class='textRed'>NOTE : All underlined fields below can be sorted. Click on field name to sort.</div></td></tr><tr><td colspan='2'><span style='color:red; font-weight:bold;margin-left: 23%;'>Sorry, you are not authorised to access this report. Please select an appropriate class/section!</span></td></tr></table>";
			}
		else
		{
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
				$query .= " AND childSection IN ($strsection)";
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
		
		//echo $query;
		//exit;
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
				if($class >= 6)
					$studentDetails[$userID][12] = getNCERTQuestion($userID, $fromDate, $tillDate);
				else
					$studentDetails[$userID][12] = 0;
				$studentDetails[$userID][16] = getExamCornerQuestion($userID,$fromDate,$tillDate);
				$studentDetails[$userID][20] = 0;
			}
		
			if(mysql_num_rows($result)==0)
			{
				echo "<table id='pagingTable'></tr><tr><td colspan='2'><div id='noRecords'><center>No records found!</center></div></td></tr></table>";
			}
			else
			{
		
			$userIDArray = array_keys($studentDetails);
		
			//For highlighting students answering randomly: %correct < 75 & avg. time taken per question < 30th percentile for the school/class combo & attempted more than 50 questions.
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

			$result = mysql_query($query) or die(mysql_error());
			
			$colSpan = $class<3?4:5;
			/*echo "My SQL FETCH RECORDS";
			echo "<pre>";
				while($row = mysql_fetch_assoc($result))
				{
					
					$userID = $row['userID'];
					echo '<pre/>';
					$result_array[$userID][] = $row;
   					
				}
				print_r($result_array);
				echo mysql_num_rows($result) . ":Count";*/
		?>

		<p>
		<?php
			ob_start();
			$disp="none";
			//echo "Topics Attempted ".$chkTopicsAttempted;
			if($chkTopicsAttempted)
				$colspan=10;
			else $colspan=7;
			if($chkOtherTask)
				$disp="table-cell";
		?>
		
				<div class="flipped" style="overflow-y:auto;">
				<table id="gridtable_<?=$class.$section?>" border="1" align="center" cellpadding="3" cellspacing="0" class="gridtable flipped"  width="92%">
					<thead>
					<tr>
						<td class="header">&nbsp;</td>

						<td id="rgQues" colspan="7" align="center" class="header"><label>Regular Questions</label></td>
						<td id="othrTask" style="display:<?=$disp?>" colspan="<?=$colSpan?>" align="center" class="header othertask"><label>Other Tasks</label></td>
						<td id="lasthd" class="header" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Sr.<br/>No.</u></td>
						<td class="header" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Name</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Class</u></td>
						<td class="header">No of days <br>Logged in <br>(Sessions)</td>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="ttProgress"><u>Login<br />time<br />(home)</u></td>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="ttProgress"><u>Login<br />time<br />(school)</u></td>
						<td class="header" onclick="sortColumn(event)" type="ttProgress"><u>Total Login<br/>Time <br>(hh:mm:ss)</u></td>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="ttProgress"><u>Ques<br />attempted<br />(home)</u></td>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="ttProgress"><u>Ques<br />attempted<br />(school)</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Topic Ques</u></td>
						
						
						
							<?php if($chkTopicsAttempted) { ?>
						<td class="header topicattempt" rowspan="<?=$rowSpan?>" width="62px">Topics<br/>attempted</td>
						<td class="header topicattempt"  type="Number">Specific Topic Question</td>
						<td class="header topicattempt"  type="Number">Current Topic Progress</td>
					<?php } ?>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="Number"><u>%<br />correct<br />(home)</u></td>
						<td class="header homeSchoolUsage" style="display:none" onclick="sortColumn(event)" type="Number"><u>%<br />correct<br />(school)</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>total<br />% Correct</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Avg Time Taken<br/>to ans<br/>(sec)</u></td>
						
					

						<?php if($class>=3) { //CQ applicable only for class 3 onwards?>
						<td class="header othertask" style="display:<?=$disp?>" onclick="sortColumn(event)" type="Number"><u>C.Q.</u></td>
						<?php } ?>
						<td class="header othertask" style="display:<?=$disp?>" onclick="sortColumn(event)" type="Number"><u>Practice Ques</u></td>
						<?php if($class>=6) {?>
						<td class="header othertask" style="display:<?=$disp?>" onclick="sortColumn(event)" type="Number"><u>NCERT<br>Ques</u></td>
						<?php } ?>
						<td class="header othertask" style="display:<?=$disp?>" onclick="sortColumn(event)" type="Number" title="Diagnostic test questions"><u>Other Ques</u></td>
						<td class="header othertask" style="display:<?=$disp?>" onclick="sortColumn(event)" type="Number"><u>Timed Tests</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>No. of activities</u></td>
						
						<?php if($class>=8) {?>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>No. of ques Exam Corner</u></td>
						<?php } ?>
						
						<!--<td class="header" onclick="sortColumn(event)" type="Number"><u>Remedial Item</u></td>--><!-- Uncomment once remedial item report is added -->
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Total Ques</u></td>
						<td class="header" onclick="sortColumn(event)" type="Number"><u>Sparkies</u></td>
						
					</tr>
					</thead>

			<?php
				$topicsAttemptedArray = array();
				$max_ques = $max_correct = $max_avgTime = $max_loginTime = 0;
		
				$max_ques_srno = $max_correct_srno = $max_avgTime_srno = $max_loginTime_srno = "";
				$min_ques = $min_correct = $min_avgTime = 100000;
				$min_loginTime = 100000;
				$userTimeSpentArray = array();
				
				$unique_topic_record=array();
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
										LEFT JOIN (SELECT srno, sessionID FROM ".TBL_TOPIC_REVISION." m WHERE m.userID = ".$line['userID']." UNION SELECT id as srno, sessionID FROM practiseModulesQuestionAttemptDetails  n WHERE n.userID = ".$line['userID'].") f on z.sessionID=f.sessionID)
										LEFT JOIN adepts_remedialItemAttempts d on z.sessionID=d.sessionID
								  WHERE z.userID = ".$line['userID']." AND startTime_int >= $fromDateInt AND startTime_int <= $tillDateInt GROUP BY userID";
						
						$other_result = mysql_query($query) or die(mysql_error());
						$other_line   = mysql_fetch_array($other_result);


						$studentDetails[$line['userID']][8] = $other_line['totalcq'];
						$studentDetails[$line['userID']][9] = $other_line['totTmTst'];
						$studentDetails[$line['userID']][10] = $other_line['tottopicrev'];
						$studentDetails[$line['userID']][11] = $other_line['remedialAttempts'];
						$studentDetails[$line['userID']][15] = getGameDetails($line['userID'],$fromDate,$tillDate);
						$studentDetails[$line['userID']][17] = getOtherQuesDetails($line['userID'],$fromDate,$tillDate);
						$studentDetails[$line['userID']][18] = getHomeSchoolUsage($line['userID'],$fromDate,$tillDate);
						$studentDetails[$line['userID']][19] = getHomeSchoolQuesPerCorrect($line['userID'],$fromDate,$tillDate,$class);
					}
					$studentDetails[$line['userID']][13] = getNoOfSparkies($line['userID'], $class, $fromDateInt, $tillDateInt);
					
					$timeSpentArray = explode(":",$timeSpent);
					$timeInSecs = (3600*intval($timeSpentArray[0])) + (60*intval($timeSpentArray[1])) + intval($timeSpentArray[2]);
					$timeInSecs = intval($timeInSecs);
					//echo "Time Second ".$timeInSecs;
					//$color_class=categorizeUsage($timeInSecs,$fromDate,$tillDate);
					//echo $color_class;
					//exit;
					

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
				} // end of while
				
				//echo "Attempted Array Topics <pre>".count($topicsAttemptedArray);
				//print_r($topicsAttemptedArray);
				/*echo "PRint of array";
				print_r($studentDetails[$line['userID']]);
				*/

				$topicsAttemptedArray = array_unique($topicsAttemptedArray);
				//echo "Topis Attempted Array".count($topicsAttemptedArray);;
				//print_r($topicsAttemptedArray);

				if(count($topicsAttemptedArray)>0)
				{
					$topicArray = array();
					$query  = "SELECT teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode in ('".implode("','", $topicsAttemptedArray)."')";
					$result = mysql_query($query) or die(mysql_error());
					while ($line = mysql_fetch_array($result)) {
						$topicArray[$line[0]] = $line[1];
					}
				}
		/*echo "<pre>";
		echo "Topics Name";		
		print_r($topicArray);
*/
		
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
						//$color_class=categorizeUsage($timeInSecs,$fromDate,$tillDate);
			
			//echo $timeSpent."From Date".$fromDate."Till Date".$tillDate;
			// band Colors 
			$color_class=categorizeUsage($timeInSecs,$fromDate,$tillDate);
			
?>
		<tr  <?php if($studentDetails[$userID][4]>=50 && $studentDetails[$userID][5]<75 && $studentDetails[$userID][6]<$compareTime) echo " class=\"purple\"";?>>
						<td><?=$srno?></td>
						<td nowrap align="left"><a href="studentWiseReport.php?cls=<?=$class?>&section=<?=$childSection[$userID]?>&studentID=<?=$userID?>&tillDate=<?=$tillDate?>&fromDate=<?=$fromDate?>" style="text-decoration:underline;" onclick="setTryingToUnload();"><?=$studentDetails[$userID][0]?></a></td>
						<td><?=$studentDetails[$userID][1]?></td>
						<td><?=$studentDetails[$userID][2]?></td>
						<td class="homeSchoolUsage" style="display:none"><?php echo (isset($studentDetails[$userID][18]["home"]) && $studentDetails[$userID][18]["home"]!="") ? $studentDetails[$userID][18]["home"] : "00:00:00" ;?></td>
						<td class="homeSchoolUsage" style="display:none"><?php echo (isset($studentDetails[$userID][18]["school"]) && $studentDetails[$userID][18]["school"]!="") ? $studentDetails[$userID][18]["school"] : "00:00:00" ;?></td>
						<td id="time<?=$srno?>" class="<?=$color_class;$clsTime?>"><?=$timeSpent?></td>
						<td class="homeSchoolUsage" style="display:none"><?=$studentDetails[$userID][19]["home"]["questotal"];?></td>
						<td class="homeSchoolUsage" style="display:none"><?=$studentDetails[$userID][19]["school"]["questotal"];?></td>
						<td id="ques<?=$srno?>" class="<?=$clsQues?>"><?=$studentDetails[$userID][4]?></td>
                       <?php if($chkTopicsAttempted) {  // Topic Listing ?>
						<td align="left" class="topicattempt" colspan="3" style="padding:0px; margin:0px;">
						<?php
						echo "<table id=\"topiclisting\" class=\"topics_list\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
								
							if($studentDetails[$userID][7]=="")	{ 
							echo "<tr><td class=\"nonempty\">&nbsp;</td><td class=\"nonempty\">&nbsp;</td>";
							echo "<td class=\"nonempty\">&nbsp;</td></tr>";
						    }
							else
							{ 
								$tmpArray = explode(",",$studentDetails[$userID][7]);
								$get_all_topi_details=array();
								$get_all_topi_details=get_topic_status($userID,$class,$tmpArray,$fromDate,$tillDate);
								
								if(count($tmpArray)>0)
								{
								
								for($i=0; $i<count($tmpArray); $i++)
									{
										echo "<tr><td align=\"left\" class=\"nonempty \" style=\"border-left:0px\">".$topicArray[$get_all_topi_details[$i]['topic_id']]."</td>";
										echo "<td class=\"nonempty\">".$get_all_topi_details[$i]['noq']."</td>";
										echo "<td class=\"nonempty\">".$get_all_topi_details[$i]['progress']."</td>";
										echo "</tr>";
									}
								
								}
								else
								{
									echo "<tr><td></td><td></td><td></td></tr>";

								}
								
							}
							echo "</table>";
						?>
						
						</td>
						
							<?php } ?> <!-- end of topic listing -->
                        <?php 
						
						if(($studentDetails[$userID][19]["home"]["perRight"]>0 && $studentDetails[$userID][19]["school"]["perRight"]>0) && ($studentDetails[$userID][19]["school"]["perRight"]-$studentDetails[$userID][19]["home"]["perRight"]>=15 || $studentDetails[$userID][19]["home"]["perRight"]-$studentDetails[$userID][19]["school"]["perRight"]>=15))
						{
						?>
						<td class="homeSchoolUsage" title="Students whose accuracy difference between home and school is greater than 15%" style="display:none;   border:solid 2px #FF0000;  border-right:none;  "><?=$studentDetails[$userID][19]["home"]["perRight"];?></td>
						<td class="homeSchoolUsage" title="Students whose accuracy difference between home and school is greater than 15%" style="display:none;  border:solid 2px #FF0000; border-left:none;  "><?=$studentDetails[$userID][19]["school"]["perRight"];?></td>
                        <?php 
						}
						else
						{
						?>
                        <td class="homeSchoolUsage" style="display:none"><?=$studentDetails[$userID][19]["home"]["perRight"];?></td>
						<td class="homeSchoolUsage" style="display:none"><?=$studentDetails[$userID][19]["school"]["perRight"];?></td>
                        <?php 
						}
						?>
						<td id="correct<?=$srno?>" class="<?=$clsCorrect?>"><?=$studentDetails[$userID][5]?></td>
						<td id="avgTime<?=$srno?>" class="<?=$clsAvgTime?>"><?=$studentDetails[$userID][6]?></td>
						
						
						
						<?php if(SUBJECTNO==2) {
						$studentDetails[$userID][20] = $studentDetails[$userID][4] + $studentDetails[$userID][8] + $studentDetails[$userID][10] + $studentDetails[$userID][17]["diagnosticTest"] + $studentDetails[$userID][12] + $studentDetails[$userID][17]["revisionSession"] + $studentDetails[$userID][17]["All"];
						?>
						<?php if($class>=3) { ?>
						<td class="othertask" style="display:<?=$disp?>"><?=$studentDetails[$userID][8]?></td>
						<?php } ?>
						<td class="othertask" style="display:<?=$disp?>"><?=$studentDetails[$userID][10]?></td>
						<?php if($class>=6) {?>
						<td class="othertask" style="display:<?=$disp?>"><?=$studentDetails[$userID][12]?></td>
						<?php } ?>
						<td class="othertask" style="display:<?=$disp?>"><?=$studentDetails[$userID][17]["diagnosticTest"]+$studentDetails[$userID][17]["revisionSession"]+$studentDetails[$userID][17]["All"]?></td>
						<td class="othertask" style="display:<?=$disp?>"><?=$studentDetails[$userID][9]?></td>
						<td><?=$studentDetails[$userID][15]?></td>
						<?php if($class>=8) {?>
						<td><?=$studentDetails[$userID][16]?></td>
						<?php } ?>
						
						<?php } ?>
						<td><?=$studentDetails[$userID][20]?></td>
						<?php if($studentDetails[$userID][13] != 0){ ?>
						<td><a href="viewSparkies.php?userID=<?=$userID?>&fromdate=<?=$fromDate?>&tilldate=<?=$tillDate?>" class="btn btn-default loadSparkies"><?=$studentDetails[$userID][13]?></a></td>
						<?php } else { ?>
						<td><?=$studentDetails[$userID][13]?></td>
						<?php } ?>
					</tr>
			<?php
					$srno++;
				}
			?>
				</table>
				</div>
				<div class="legends_top">
				<table  cellspacing="0" cellpadding="2" width="92%" class="legendtable">
				<tr>
				<td><div id="green_legend"></div></td><td>Students with <span class="greenleg">GREAT</span> usage</td>
				<td><div id="purple_legend"></div></td><td>Students answering very quickly and with high error percentage  
				they may be pressing randomly.</td>
				</tr>
				<tr>
				<td><div id="blue_legend"></div></td><td>Students with <span class="blueleg">GOOD</span> usage</td>
				<td><div id="red_legend"></div></td><td>Students whose accuracy difference between home and school is greater than 15%.</td>
				</tr>

				<tr>
				<td><div id="yellow_legend"></div></td><td>Students with <span class="yellowleg">AVERAGE</span> usage</td>
			     <td><div id="fluro_greenlegend"></div></td><td>Indicates highest value of the column selected.</td>
				</tr>
				
				<tr>
				<td><div id="orange_legend"></div></td><td>Students with <span class="orangeleg">LOW</span> usage</td>
				<td><div id="fluro_redlegend"></div></td><td>Indicates lowest value of the column selected.</td>
				</tr>

				<tr>
				<td><div id="grey_legend"></div></td><td>Students with <span class="greyleg">ZERO</span> usage</td>
				<td></td><td>NOTE : Other Ques includes revision session and diagnostic test question.</td>
				</tr>

				<tr>
				<td colspan="3"></td>
				<td>NOTE : All underlined fields below can be sorted. Click on field name to sort.</td>
				</tr>

				</table>	
				</div>  			

				
		<?php
			$table = ob_get_contents();
		
			ob_end_clean();
		
			echo $table."<div style='clear:both'><br><hr /><br>";
		?>
		<!--<form name='frmExcel' target="" action="export.php" method="POST">
			<input type="hidden" name="content" value='<?=strip_tags($table,"<table><tr><td>")?>'>
		</form>-->
		
		</p>
		
		<?php
			}
		}
	}
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

function getExamCornerQuestion($userID,$startDate,$endDate)
{
	$query = "select count(*) from adepts_competitiveExamQuesAttempt where userID=$userID and date(lastModified) between '$startDate' AND '$endDate'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$count1 = $row[0];
	
	$query = "select count(*) from adepts_bucketClusterAttempt where userID=$userID and date(lastModified) between '$startDate' AND '$endDate'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$count2 = $row[0];
	
	return $count1+$count2;
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

function getNoOfSparkies($userID, $class, $fromDateInt, $tillDateInt)
{
	$noOfSparkies  = 0;
	$query = "SELECT sum(noOfJumps) FROM ".TBL_SESSION_STATUS." WHERE userID=$userID AND startTime_int>=$fromDateInt AND startTime_int <= $tillDateInt";
	$result = mysql_query($query);
	// echo $query;die;
	if($line = mysql_fetch_array($result))
	{
		$noOfSparkies = $line[0];
		$sq = "SELECT userID,SUM(sparkieEarned) FROM adepts_userBadges
				 WHERE userID=$userID AND batchType!='topicCompletion' AND DATE(lastModified)>=
				 $fromDateInt AND DATE(lastModified)<=$tillDateInt group by userID";	
		$rs = mysql_query($sq);
		$rw = mysql_fetch_array($rs);			 
		if(!empty($rw)){
			$noOfSparkies += $rw[1];		
		}		
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

function getOtherQuesDetails($userID,$fromDate,$tillDate)
{
	$otherQuesArray = array();
	$otherQuesArray["diagnosticTest"] = 0;
	$otherQuesArray["revisionSession"] = 0;
	$otherQuesArray["All"] = 0;
	$sq = "SELECT COUNT(srno) AS totalQues,questionType FROM adepts_researchQuesAttempt
			 WHERE userID=$userID AND attemptedDate BETWEEN '$fromDate' AND '$tillDate 23:59:59' AND questionType!='' GROUP BY questionType";
	$rs = mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$otherQuesArray["All"] = $rw["totalQues"];
	}
	$sq = "SELECT COUNT(srno) AS totalQues FROM adepts_diagnosticQuestionAttempt 
			 WHERE userID=$userID AND lastModified BETWEEN '$fromDate' AND '$tillDate 23:59:59'";
	$rs = mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
		$otherQuesArray["diagnosticTest"] = $rw["totalQues"];
		
	$sq = "SELECT COUNT(srno) AS totalQues FROM adepts_revisionSessionDetails 
			 WHERE userID=$userID AND lastModified BETWEEN '$fromDate' AND '$tillDate 23:59:59'";
	$rs = mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
		$otherQuesArray["revisionSession"] = $rw["totalQues"];
		
	return $otherQuesArray;
}

function getHomeSchoolUsage($userID,$fromDate,$tillDate)
{
	$arrHomeSchoolUsage["home"] = "00:00:00";
	$arrHomeSchoolUsage["school"] = "00:00:00";
	$fromDate_int = str_ireplace("-","",$fromDate);
	$tillDate_int = str_ireplace("-","",$tillDate);
	$sq = "SELECT flag,SUM(timeSpent) FROM adepts_homeSchoolUsage WHERE userID=$userID AND startTime_int>=$fromDate_int AND startTime_int<=$tillDate_int GROUP BY flag";
	$rs = mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$timeSpent = $rw[1];
		$hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);    //converting secs to hours.
		$timeSpent = $timeSpent%3600;
		$mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
		$timeSpent = $timeSpent%60;
		$secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);
		$arrHomeSchoolUsage[$rw["flag"]] = $hours.":".$mins.":".$secs;
	}
	return $arrHomeSchoolUsage;
}

function getHomeSchoolQuesPerCorrect($userID,$fromDate,$tillDate,$class)
{
	$arrHomeSchoolQues["home"]["questotal"] = 0;
	$arrHomeSchoolQues["home"]["perRight"] = 0;
	$arrHomeSchoolQues["school"]["questotal"] = 0;
	$arrHomeSchoolQues["school"]["perRight"] = 0;
	$fromDate_int = str_ireplace("-","",$fromDate);
	$tillDate_int = str_ireplace("-","",$tillDate);
	
	$sq = "SELECT flag,count(B.srno) questotal, sum(S) avgTime, sum(if(R=1,1,0)) perRight FROM adepts_homeSchoolUsage A, ".TBL_QUES_ATTEMPT."_class$class B 
			WHERE A.sessionID=B.sessionID AND A.userID=$userID AND startTime_int>=$fromDate_int AND startTime_int<=$tillDate_int GROUP BY flag";
	$rs = mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrHomeSchoolQues[$rw["flag"]]["questotal"] = $rw["questotal"];
		$arrHomeSchoolQues[$rw["flag"]]["perRight"] = round(($rw["perRight"]/$rw["questotal"])*100,1);
	}
	return $arrHomeSchoolQues;
}
function categorizeUsage($timeSpentInSecs, $startDate, $endDate) {

	$category = '';
	$datediff = strtotime($endDate) - strtotime($startDate);
	$numDays = floor($datediff/(60*60*24)) + 1;
	$avgTimeSpentPerDay = $timeSpentInSecs/$numDays;

	if($avgTimeSpentPerDay == 0) {
		$category = 'zero';
	} elseif($avgTimeSpentPerDay < 257) {
		$category = 'low';
	} elseif ($avgTimeSpentPerDay>=257 && $avgTimeSpentPerDay<514) {
		$category = 'average';
	} elseif ($avgTimeSpentPerDay>=514 && $avgTimeSpentPerDay<1028) {
		$category = 'good';
	} elseif ($avgTimeSpentPerDay>=1028) {
		$category = 'great';
	} 
	return $category;
}

function get_topic_status($userId,$class,$topics,$fromDate,$tillDate)
{

 $topicArray = array();
$query2="select teacherTopicCode, SUM(noOfQuesAttempted) as noOfQuesAttempted, MAX(progress) as prg from adepts_teacherTopicStatus where teacherTopicCode IN ('".implode("','", $topics)."') AND userID=".$userId." GROUP BY teacherTopicCode";
	

	//echo $query2;
					$result = mysql_query($query2) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result)) {
						$teacherTopicCode = $row['teacherTopicCode'];
						$sq = "select count(srno) from ".TBL_QUES_ATTEMPT."_class$class where userID= $userId AND teacherTopicCode='$teacherTopicCode' AND attemptedDate>='$fromDate' AND attemptedDate<='$tillDate'";
						$rs = mysql_query($sq);
						$rw = mysql_fetch_array($rs);						
						$topicArray[]=array("topic_id"=>$row['teacherTopicCode'],"noq"=>$rw[0],"progress"=>$row['prg']);
					
					}
					return $topicArray;
					//unset($topicArray);

}
?>
