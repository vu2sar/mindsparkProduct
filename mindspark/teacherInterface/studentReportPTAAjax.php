<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	@include("../userInterface/check1.php");
	include("../userInterface/constants.php");
	include("../userInterface/classes/clsTopicProgress.php");
	include("../slave_connectivity.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	
	$schoolCode	=	$_POST["schoolCode"];
	$class		=	$_POST["class"];
	$section	=	$_POST["section"];
	$date1 = $_POST['dateTo'];
	$tillDate = date('Y-m-d', strtotime(str_replace('/', '-', $date1)));
	$limit = isset($_POST['limit'])?$_POST['limit']:'';
	
	$date2 = $_POST['dateFrom'];
	$fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $date2)));
	
	
	$scoreOutOf = isset($_POST['scoreOutOf'])?$_POST['scoreOutOf']:10;
    $gracePeriod = isset($_POST['gracePeriod'])?$_POST['gracePeriod']:0;
	$showMax  = $_POST['showMax'];
	$showAvg  = $_POST['showAvg'];
	$curDate = date("Y-m-d");
	/*echo '<script type="text/javascript">alert("' .$_POST['dateTo']. '"); </script>';
	echo '<script type="text/javascript">alert("' .$_POST['dateFrom']. '"); </script>';*/
	$scoreStr = "";
	if($limit=="5") {
		$_SESSION["scoreStr"]	=	"";
		$_SESSION["srno"]	=	"";	
	}
	if(!isint($scoreOutOf))
	   $scoreOutOf = 10;
	if($class!="")
	{
		ob_start();
	    $ttdifficultyarray = getTimePerSdl($class);

		$query = "SELECT userID,username,childName, childClass, childSection, childDob FROM adepts_userDetails WHERE schoolCode=".$schoolCode." AND childClass='".$class."'";

		if($section!='')
			$query .= " AND childSection='".$section."' ";

		$query .= " AND category='STUDENT' AND subcategory='School' AND endDate>=curdate() AND enabled=1 ORDER BY childSection, childName";
		if($limit<>'')
			$query .= " LIMIT $limit";

		$usersResult = mysql_query($query) or die(mysql_error());

		$stud = 0;
		$userArray = array();
		$childmpiArrayOverall = $childmpiArrayForThePeriod = array();
		$progressArr = array();
		$classArray = $dobArray = array();

		$maxAvgAccuracyArr = array();
		$maxAvgProgressArr = array();
		$accuracyArr = array();
		$timePerSdlArr = array();
		$failedClusterArr = array();
		$userNameArr = array();
		$topicsBelowAverage = $topicsAboveAverage = array();
		
		if($usersLine = mysql_num_rows($usersResult))
		{
		while($usersLine=mysql_fetch_array($usersResult))
		{
			$stud++;
			$userArray[$usersLine['userID']]   = $usersLine['childName'];
			$classArray[$usersLine['userID']]  = $usersLine['childClass'].$usersLine['childSection'];
			$userNameArr[$usersLine['userID']] = $usersLine['username'];
			$dobArray[$usersLine['userID']]    = $usersLine['childDob']=="0000-00-00"?"N.A.":$usersLine['childDob'];
		}

		$topicsActivated = getTopicsActivated($schoolCode, $class, $section, $fromDate, $tillDate);
        //$arrUsageDays    = getNoOfDaysFromStart($userArray);
		
		foreach ($topicsActivated as $ttCode=>$ttDesc)
		{
		    $ttdifficulty = $ttdifficultyarray[$ttCode];
			if ($ttdifficulty == 0) $ttdifficulty = 2;
			$maxProgress = $avgProgress = $maxAccuracy = $avgAccuracy = 0;
			
			$sq	=	"SELECT DISTINCT flow FROM adepts_teacherTopicStatus
					 WHERE userID IN (".implode(",",array_keys($userArray)).") AND teacherTopicCode='$ttCode'";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$flow	=	$rw[0];
				if($flow=="")
					$flowstr	=	"MS";
				$flowstr = str_replace(" ","_",$flow);
				${"objTopicProgress".$flowstr} = new topicProgress($ttCode, $class, $flow, SUBJECTNO);
			}
			foreach ($userArray as $userID=>$name)
			{
				$progressArr[$ttCode][$userID][0] = 0;
				$progressArr[$ttCode][$userID][1] = 0;
				$accuracyArr[$ttCode][$userID] = 0;
				$progressFailClusterArr = array();
				//$failedClusterArr[$ttCode][$userID] = "";

				$progressFailClusterArr = getProgress($ttCode, $userID, $class, $fromDate, $tillDate, $ttdifficulty,$gracePeriod,$scoreOutOf);
				$progress = $progressFailClusterArr[0];
				//$failedClusterArr[$ttCode][$userID] = $progressFailClusterArr[1];

				$accuracy = getPercentAccuracy($ttCode,$userID, $class);

				$progressArr[$ttCode][$userID][0] = $progress;
				$progressArr[$ttCode][$userID][1] = $progressFailClusterArr[2]; //progress index for the period
				$progressArr[$ttCode][$userID][2] = $progressFailClusterArr[3]; //progress index till date

				$accuracyArr[$ttCode][$userID] = $accuracy;

				if($maxProgress<$progress)
					$maxProgress = $progress;

				if($maxAccuracy<$accuracy)
					$maxAccuracy = $accuracy;

				$avgAccuracy += $accuracy;
				$avgProgress += $progress;

			}

			$avgProgress = round($avgProgress/$stud, 1);
			$avgAccuracy = round($avgAccuracy/$stud, 1);
			$maxAvgProgressArr[$ttCode][0] = $maxProgress;
			$maxAvgProgressArr[$ttCode][1] = $avgProgress;
			$maxAvgAccuracyArr[$ttCode][0] = $maxAccuracy;
			$maxAvgAccuracyArr[$ttCode][1] = $avgAccuracy;
		}

		$arrStudentWiseQuesDataTillDate     = getStudentWiseQuesData($userArray,$schoolCode, $class, $section);
		$arrStudentWiseQuesDataForThePeriod = getStudentWiseQuesData($userArray,$schoolCode, $class,$section,$fromDate, $tillDate);
		//Calculate avg mpi for the period
		$avgmpiForThePeriod = $maxmpiForThePeriod = $avgmpiOverall = $maxmpiOverall = 0;

		foreach ($userArray as $userID=>$childName)
		{
		    $childmpiArrayForThePeriod[$userID] = $childmpiArrayOverall[$userID] = 0;
		   /*  foreach ($topicsActivated as $ttCode=>$ttName)
		    {
                $childmpiArrayForThePeriod[$userID] += $progressArr[$ttCode][$userID][1];
                $childmpiArrayOverall[$userID] += $progressArr[$ttCode][$userID][2];
		    } */
            $childmpiArrayForThePeriod[$userID] = explode("~",newScore($userID, $fromDate,$tillDate, $class,$gracePeriod));
            
		}

		$avgmpiForThePeriod = round(array_sum($childmpiArrayForThePeriod)/count($childmpiArrayForThePeriod), 1);
		$maxmpiForThePeriod = max($childmpiArrayForThePeriod);

		$avgmpiOverall = round(array_sum($childmpiArrayOverall)/count($childmpiArrayOverall), 1);
		$maxmpiOverall = max($childmpiArrayOverall);
        
		$totalStudents = count($arrStudentWiseQuesData);

		$UserCount = 1;

		$tillDateForDisplay = date("F d, Y");
		$periodForDisplay = date("F d, Y",mktime(0,0,0,substr($fromDate,5,2),substr($fromDate,8,2),substr($fromDate,0,4)))." - ".date("F d, Y",mktime(0,0,0,substr($tillDate,5,2),substr($tillDate,8,2),substr($tillDate,0,4)));
?>
		<!--<div id="print" align="right"><input type="button" value="Print this page" onclick="window.print();return false;" class="button" id="btnPrint"/></div>-->
		<!--<div align="left"> <b>Select All</b> <input type="checkbox" class="selectAll" checked></div>-->
<?php
        $srno =1;
        $xmlPdf	=	"";
        $xmlPdf	.=	"<student>";
		foreach ($userArray as $userID=>$childName)
		{
			
			$xmlPdf	.=	"<detail>";
			$xmlPdf	.=	"<userid>".$userID."</userid>";
			$xmlPdf	.=	"<name>".$childName."</name>";
  			$xmlPdf	.=	"<username>".$userNameArr[$userID]."</username>";
			$count = 1;
			?>		
		<div class="contentSection">
                        
			<div class="printHeader" align='left'>   <img src="assets/common/logo_header.png"> <br/><br/> </div>
			<!--<div id="hrline"><hr/></div>-->
			
		        <div  id="info" align="center" style="width:100%;">			
				<div style="">
        				<table id="topicDetail" style="border-top: 2px solid black;border-bottom: 2px solid black;border-left: 2px solid black;border-right: 2px solid black">
                                            <tr>
                				    <td width="16%" style="border-right:1px solid #626161" align='left'><label>Name: </label><?=$childName?></td>				
                				    <td width="22%" style="border-right:1px solid #626161"><label style="margin-left:10px;">Username: </label><?=$userNameArr[$userID]?></td>	
                				    <td width="18%" style="border-right:1px solid #626161"><label style="margin-left:10px;">Class: </label><?=$classArray[$userID]?></td>				
                				    <td width="18%"><label style="margin-left:10px;">School: </label><?=getSchoolName($schoolCode)?></td>				
                                            </tr>
        			        </table>
			        </div>
		        </div>
                        <br/>

                        <div align="left" style="width:90%;">
            	            <span style="font-size: 16px">Dear Parent,</span><br/><br/>
            	<?php if(!isset($arrStudentWiseQuesDataTillDate[$userID]["TotalQues"]) || $arrStudentWiseQuesDataTillDate[$userID]["TotalQues"]==0) {
            				$xmlPdf	.=	"<quesAttempted>0</quesAttempted>";
            				$xmlPdf	.=	"</detail>";
            		?>
            	            <span style="font-size: 16px"><?=$childName?> has not used Mindspark.</span>                            
                        </div>
                    </div>
            	<?php continue;} else {?>
            	            <span style="font-size: 16px">This is the progress report of <?=$childName?> in this academic year. This report has two parts - the first has details about the child's topic-wise and overall progress in Mindspark from the beginning of Mindspark program in this academic year; and the second has details of the child's progress for the period mentioned.</span>
            	<?php
            	$xmlPdf	.=	"<quesAttempted>".$arrStudentWiseQuesDataTillDate[$userID]["TotalQues"]."</quesAttempted>";
  				//$xmlPdf	.=	"<usageDays>".$arrUsageDays[$userID]."</usageDays>";
            	} ?>
            	                <br/><br/>			
                        </div>
                        
                        <div align="left" style="width:90%">
                            <strong><span style="font-size: 16px">Student Report as of <?=$tillDateForDisplay?></strong></span>
                        </div>
                        <br/>

			<table align="center" class="tblContent" border="0" cellspacing="0" cellpadding="3" width="90%" style="clear:both;">
        			<tr>
        			    <th colspan="4" align="left" class="header">
        			    <strong>Total number of questions attempted</strong>: <?=isset($arrStudentWiseQuesDataTillDate[$userID]["TotalQues"])?$arrStudentWiseQuesDataTillDate[$userID]["TotalQues"]:"0"?>
        			    </th>
        			</tr>
        			<tr>
        			    <th width="5%" class="header" rowspan="2">Sr.No.</th>
        				<th width="25%" class="header" align="left" rowspan="2">Topic Name</th>
        				<th width="25%" class="header" align="left" colspan="2">Metrics (A: Average of class, M: Maximum in class)</th>
        			</tr>
        			<tr>
                                        <th class="header">Progress</th>
                                        <th class="header">Accuracy</th>				        
			        </tr>
			<?php
			foreach ($topicsActivated as $ttCode=>$ttName)
			{
				    $xmlPdf	.=	"<topic>";
        			    if($maxAvgProgressArr[$ttCode][1]==0)    //implies avg progress 0 i.e. no students have done the topic
        			    {
        			    	$xmlPdf	.=	"<maxAvgProgressArr>0</maxAvgProgressArr>";
        			    	$xmlPdf	.=	"</topic>";
        			    	continue;
        			    }
			?>
				<tr>
					<td align="center"><?=$count?></td>
					<td align="left" width="40%"><a href='studentTrail.php?user_passed_id=<?=$userID?>&topic=<?=$ttCode?>' target="_blank" title="Click here to access the question-wise trail"><?=$ttName?></a></td>
					<td align="left" width="30%">
					    <img style="vertical-align:middle" src='graph.php?actual=<?=$progressArr[$ttCode][$userID][0]?>&avg=<?=$maxAvgProgressArr[$ttCode][1]?>&max=<?=$maxAvgProgressArr[$ttCode][0]?>&scale=100&width=150&height=40&showMax=<?=$showMax?>&showAvg=<?=$showAvg?>'> <?=$progressArr[$ttCode][$userID][0]?>%
					</td>
					<td align="left" width="30%">
					    <img style="vertical-align:middle" src='graph.php?actual=<?=$accuracyArr[$ttCode][$userID]?>&avg=<?=$maxAvgAccuracyArr[$ttCode][1]?>&max=<?=$maxAvgAccuracyArr[$ttCode][0]?>&scale=100&width=150&height=40&showMax=<?=$showMax?>&showAvg=<?=$showAvg?>'> <?=$accuracyArr[$ttCode][$userID]?>%<?php /*if($failedClusterArr[$ttCode][$userID] != "") echo "<br/>Concepts that need clarity:<br/>".$failedClusterArr[$ttCode][$userID]*/?>&nbsp;
					</td>						
				</tr>
				<?php
				$xmlPdf	.=	"<topicName>".$ttName."</topicName>";
				$xmlPdf	.=	"<topicProg>".$progressArr[$ttCode][$userID][0]."</topicProg>";
    			        $xmlPdf	.=	"<topicAccu>".$accuracyArr[$ttCode][$userID]."</topicAccu>";
				$xmlPdf	.=	"<topicProgMax>".$maxAvgProgressArr[$ttCode][0]."</topicProgMax>";
        		    	$xmlPdf	.=	"<topicAccuMax>".$maxAvgAccuracyArr[$ttCode][0]."</topicAccuMax>";
        		    	$xmlPdf	.=	"<topicProgAvg>".$maxAvgProgressArr[$ttCode][1]."</topicProgAvg>";
        		    	$xmlPdf	.=	"<topicAccuAvg>".$maxAvgAccuracyArr[$ttCode][1]."</topicAccuAvg>";
        		    	$xmlPdf	.=	"</topic>";
				$count++;
			}
			$UserCount++;
                        //$studentScore = getScore($childmpiArrayForThePeriod[$userID][1],$avgmpiForThePeriod,$maxmpiForThePeriod,$scoreOutOf);
                        if($scoreOutOf>0)
                            $studentScore = $childmpiArrayForThePeriod[$userID][1]*($scoreOutOf/100);
                        else{
                                $scoreOutOf=10;
                                $studentScore = $childmpiArrayForThePeriod[$userID][1]*(10/100);
                         }
                        $studentScore=round($studentScore,1);
			$xmlPdf	.=	"<childmpiArrayOverall>".$childmpiArrayOverall[$userID]."</childmpiArrayOverall>";
        	    	$xmlPdf	.=	"<maxmpiOverall>".$maxmpiOverall."</maxmpiOverall>";
        	    	$xmlPdf	.=	"<avgmpiOverall>".$avgmpiOverall."</avgmpiOverall>";
			?>
			
		    </table>
		        <div style="width:90%; font-size:0.8em" align="left"><span style="font-size: 16px">Note: Max possible for progress and accuracy is 100%</span></div>
			<br/>
        		<div align="left" style="width:90%">
                                    <strong><span style="font-size: 16px">Student Report (<?=$periodForDisplay?>)</span></strong>
                        </div>
                        <br/>
		        <table align="center" class="tblContent" border="0" cellspacing="0" cellpadding="3" width="90%" style="clear:both;">
        			<tr>
        			    <th colspan="3" align="left" class="header">
        			         <strong>Total number of questions attempted</strong>: <?=isset($arrStudentWiseQuesDataForThePeriod[$userID]["TotalQues"])?$arrStudentWiseQuesDataForThePeriod[$userID]["TotalQues"]:"0"?>
        			    </th>
        			</tr>
        			<tr>
        			    <td width="5%">&nbsp;</td>
        			    <td align="left" width="40%">Mindspark Progress Index in the period mentioned above</td>
        			    <td><?=$childmpiArrayForThePeriod[$userID][1]?></td>
        			</tr>
        			<tr>
        			    <td>&nbsp;</td>
        			    <td align="left" width="40%">Total Score</td>
        			    <td><?=$studentScore?> (Out of <?=$scoreOutOf?>)</td>
        			</tr>
			</table>
			
        		<div style="font-size:0.9em; width:90%" align="justify">
                                        <span style="font-size: 16px">
                                        Progress indicates where the student is in the topic when compared to his/her peers<br/>
                                        Accuracy indicates on an average how accurate the student is in answering the questions and also shows a comparison with his/her peers<br/>
                                        Mindspark Progress Index is calculated using the child's progress, accuracy and speed in each topic he/she has attempted in Mindspark.
                                        </span>
        		</div>
		        <br/>
		</div>
		
			<?php
			$xmlPdf	.=	"<childmpiArrayForThePeriod>".$childmpiArrayForThePeriod[$userID]."</childmpiArrayForThePeriod>";
			$xmlPdf	.=	"<avgmpiForThePeriod>".$avgmpiForThePeriod."</avgmpiForThePeriod>";
			$xmlPdf	.=	"<maxmpiForThePeriod>".$maxmpiForThePeriod."</maxmpiForThePeriod>";
			$xmlPdf	.=	"<studentWiseQuesDataForThePeriod>".$arrStudentWiseQuesDataForThePeriod[$userID]["TotalQues"]."</studentWiseQuesDataForThePeriod>";
			$xmlPdf	.=	"<studentScore>".$studentScore."</studentScore>";
			$xmlPdf	.=	"</detail>";
			$_SESSION["scoreStr"] .= $_SESSION["srno"]."~".$childName."~".$class.$section."~".$dobArray[$userID]."~".$studentScore.",";
                        $_SESSION["paramDetails"] .=$childmpiArrayForThePeriod[$userID][2]."--";
			$_SESSION["srno"]++;
		}
		$xmlPdf	.=	"</student>";
		//$_SESSION["scoreStr"] = substr($_SESSION["scoreStr"],0,-1);
		}
		if($limit=="45,100" || $limit=="60") {
?>
        <form id="frmDownloadExcel" target="_blank" method="post" action="student_score_excel_download.php">
            <input type="hidden" name="scoreStr" id="scoreStr" value="<?=$_SESSION["scoreStr"]?>">
            <input type="hidden" name="paramDetails" id="paramDetails" value="<?=$_SESSION["paramDetails"]?>">
            <input type="hidden" name="cls" id="cls" value="<?=$class.$section?>">
            <input type="hidden" name="title" id="title" value="<?=$periodForDisplay?>">
            <input type="hidden" name="school" id="school" value="<?=$schoolName?>">
            <input type="hidden" name="tillDateForDisplay" id="tillDateForDisplay" value="<?=$tillDateForDisplay?>">
            <input type="hidden" name="scoreOutOf" id="scoreOutOf" value="<?=$scoreOutOf?>">
            <input type="hidden" name="showMax" id="showMax" value="<?=$showMax?>">
            <input type="hidden" name="showAvg" id="showAvg" value="<?=$showAvg?>">
            <input type="hidden" name="scoreOutOf" id="scoreOutOf" value="<?=$scoreOutOf?>">
            <input type="hidden" name="xmlPdf" id="xmlPdf" value="<?=$xmlPdf?>">
            <input type="hidden" name="userString" id="userString" value="">
            <!--<input type="button" name="btnDownloadPdf" id="btnDownloadPdf" value="Download scores in pdf">-->
            <!--<input type="submit" name="btnDownloadExcel" id="btnDownloadExcel" value="Download scores in excel">-->
        </form>
		<div style="display:none">
		<div id="MPITable">
			<table class="hor-zebra" style="display:table">	
				<thead>
				<tr>
				<th> Name </th>
				<th> Class </th>
				<th> Score </th>
				<th> Out Of </th>
				</tr>
				</thead>
				
				<tbody>
				<?php
				$scoreStr =  $_SESSION["scoreStr"];
				$scoreStr = substr($scoreStr,0,-1);
				$scoreArray = explode(",",$scoreStr);
				foreach ($scoreArray as $val)
				{
					$detailsArr = explode("~",$val);
					if($detailsArr[0]%2==0)
					echo "<tr class='odd'>";
					else
					echo "<tr class='even'>";
					
					echo "<td>";
					echo $detailsArr[1];
					echo "</td>";
					
					echo "<td>";
					echo $detailsArr[2];
					echo "</td>";
					
					echo "<td>";
					echo $detailsArr[4];
					echo "</td>";
					
					echo "<td>";
					echo $scoreOutOf;
					echo "</td>";
					
					echo "</tr>";
				
				}
				
				?>
			  </tbody>
		</table>
		</div>
		</div>
		
<?php	unset($_SESSION["scoreStr"],$_SESSION["srno"]); }
	$PTADATA	=	 ob_get_contents();
	ob_end_clean();
	echo $PTADATA;
	}
?>

<?php

function getStudentWiseQuesData($userArray,$schoolCode, $class, $section, $fromDate="",$tillDate="")
{
    $arrStudentWiseQuesData = array();

    $query  = "SELECT a.userID, SUM(totalQ) as totalQ
               FROM   adepts_userDetails a, adepts_sessionStatus b
               WHERE  a.userID IN (".implode(",",array_keys($userArray)).") AND a.userID=b.userID AND category='STUDENT' AND subcategory='School' AND schoolCode=$schoolCode AND childClass='$class'";

    if($section!="")
        $query .= " AND childSection='$section' ";
    if($fromDate!="")
        $query .= " AND startTime_int>=".str_replace('-','',$fromDate)." ";
    if($tillDate!="")
        $query .= " AND startTime_int<=".str_replace('-','',$tillDate)." ";
	if($fromDate=="")
        $query .= " AND startTime_int>=DATE_FORMAT(registrationDate,'%Y%m%d') ";
	

    $query .= "  GROUP BY a.userID";
	
    //echo $query."<br>";
    $result = mysql_query($query) or die(mysql_error().$query);
    while ($line = mysql_fetch_array($result))
    {
        $arrStudentWiseQuesData[$line['userID']]["TotalQues"] = $line['totalQ'];
        //$arrStudentWiseQuesData[$line['userID']]["timePerQues"] = round($line['avgTime'],1);
    }
    //print_r($arrStudentWiseQuesData);
    return $arrStudentWiseQuesData;
}

/* function getTopicsAttempted($userID)
{
	$topicsAttempted = array();
	$query = "SELECT distinct a.teacherTopicCode, a.teacherTopicDesc
	          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicStatus b
	          WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=2 AND b.userID=$userID";
	$result = mysql_query($query);
	while ($line = mysql_fetch_array($result))
		$topicsAttempted[$line[0]] = $line[1];

	return $topicsAttempted;
} */

function getProgress($ttCode, $userID, $class, $fromDate, $tillDate, $ttdifficulty,$gracePeriod,$scoreOutOf)
{
    $curDate = date("Y-m-d");
	$progressFailClusterArr = array();
	$progress = $topicindexBefore = $topicindexAfter = 0;
	$failedClusterDesc = "";
	$query  = "SELECT DISTINCT flow FROM adepts_teacherTopicStatus WHERE userID=$userID AND teacherTopicCode='$ttCode'";

	$result = mysql_query($query);
    if($line   = mysql_fetch_array($result))
	{
		$flow   = $line[0];
		if($flow=="")
			$flowstr	=	"MS";
		$flowstr = str_replace(" ","_",$flow);
		
		global ${"objTopicProgress".$flowstr};
		//$objTopicProgress = new topicProgress($ttCode, $class, $flow, SUBJECTNO);
		$progress = ${"objTopicProgress".$flowstr}->getProgressInTT($userID);


		//$objTT = new teacherTopic($ttcode,$class,$flow);     //create the object for TTcode, class, flow combination:  and childSection = '$childsection'
		$sdlNumberArray = ${"objTopicProgress".$flowstr}->objTT->getSDLNumberDetails();
		$minm = ${"objTopicProgress".$flowstr}->objTT->getMinLevelOfClass(${"objTopicProgress".$flowstr}->objTT->startingLevel);
		$max  = ${"objTopicProgress".$flowstr}->objTT->getMaxLevelOfClass(${"objTopicProgress".$flowstr}->objTT->startingLevel);

		$topicindexBefore = $topicindexAfter = $topicIndexTillDate = 0;
		$topicindexBefore    =  progressIndex($userID,$fromDate,$ttCode, $flow, $class, $ttdifficulty, $minm, $max,$sdlNumberArray);
		$topicindexAfter     =  progressIndex($userID,$tillDate." 23:59:59",$ttCode, $flow, $class, $ttdifficulty, $minm, $max,$sdlNumberArray);
        
		if($tillDate==$curDate)
			$topicIndexTillDate = $topicindexAfter;
		else
			$topicIndexTillDate  =  progressIndex($userID,$curDate." 23:59:59",$ttCode, $flow, $class, $ttdifficulty, $minm, $max,$sdlNumberArray);
       
	}
    $topicIndexForThePeriod = $topicindexAfter - $topicindexBefore;
    //$topicIndexForThePeriod = newScore($userID, $fromDate,$tillDate, $class,$gracePeriod);
    if($topicIndexForThePeriod<0)
        $topicIndexForThePeriod = 0;    //If negative, make it 0 (case where student has moved to lower level - so not made a progress but attempted questions)

	$progressFailClusterArr[0] = $progress;
	$progressFailClusterArr[1] = $failedClusterDesc;
	$progressFailClusterArr[2] = $topicIndexForThePeriod;
	$progressFailClusterArr[3] = $topicIndexTillDate;


	return $progressFailClusterArr;
}



function getTopicsActivated($schoolCode, $cls, $section, $fromDate, $tillDate)
{
	$activatedTopics = array();

	$query = "SELECT distinct a.teacherTopicCode, b.teacherTopicDesc
	          FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
	          WHERE  a.teacherTopicCode=b.teacherTopicCode AND
	                 a.schoolcode=$schoolCode AND
	                 a.class=$cls";
	if($section!="")
	    $query .= " AND a.section ='$section'";
	if($fromDate!="" && $tillDate!="")
	{
		$query .= " AND ((activationDate BETWEEN '$fromDate' AND '$tillDate') OR
		            (deactivationDate BETWEEN '$fromDate' AND '$tillDate') OR
		            (activationDate<='$tillDate' AND deactivationdate>='$tillDate') OR
		            (activationDate<='$tillDate' AND deactivationdate='0000-00-00'))";
	}

	//echo $query;

	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		$activatedTopics[$line[0]] = $line[1];
	}
	return $activatedTopics;
}

function getPercentAccuracy($ttCode,$userID, $class)
{
	$query	=	"SELECT SUM(perCorrect)/COUNT(ttAttemptID) as correct FROM adepts_teacherTopicStatus
				 WHERE userID=$userID AND teacherTopicCode='$ttCode'";
	$result = mysql_query($query) or die(mysql_error());

	if($line = mysql_fetch_array($result))
	{
		$accuracy = round($line[0], 1);
	}
	else
		$accuracy = 0;

	return $accuracy;
}

function getTimePerSdl($childClass)
{
	$timePerSdlArr = array();

	$query = "SELECT teacherTopicCode,timePerSdl FROM adepts_timepersdl WHERE class=$childClass ORDER BY teacherTopicCode";
	$result = mysql_query($query) or die(mysql_error());
	while ($l = mysql_fetch_array($result))
	{
		$timePerSdlArr[$l[0]] = $l[1];
	}

	return $timePerSdlArr;
}

/* function getNoOfDaysFromStart($userArray)
{
    $arrUsageDays = array();
    if(count($userArray)>0)
    {
        $userIDStr = implode(",",array_keys($userArray));
        $query  = "SELECT a.userID, datediff(curdate(),min(startTime)) as noOfDays FROM adepts_userDetails a, adepts_sessionStatus b where a.userID=b.userID and a.userID in ($userIDStr) AND startTime_int>DATE_FORMAT(registrationDate,'%Y%m%d') GROUP BY userID";
        $result = mysql_query($query);
        while ($line = mysql_fetch_array($result))
        {
            $arrUsageDays[$line['userID']] = $line['noOfDays'];
        }
    }
    return $arrUsageDays;
} */

function progressIndex($userid, $fromDate, $ttcode, $flow, $class, $ttdifficulty, $minm, $max, $sdlNumberArray)
{

	/*$objTT = new teacherTopic($ttcode,$class,$flow);     //create the object for TTcode, class, flow combination:  and childSection = '$childsection'
	$sdlNumberArray = $objTT->getSDLNumberDetails();
	$minm = $objTT->getMinLevelOfClass($objTT->startingLevel);
	$max = $objTT->getMaxLevelOfClass($objTT->startingLevel);*/
	$noofsdls = $max - $minm + 1;

	//echo "$ttcode - $flow - $minm - $max - $noofsdls";

    $ttattempts = 1;
    $prevttaid = 0;
    $correctqs = 0;
    $accuracy = 0;
    $speed = 0;
    $progressindex = 0;
    $currprogress = 0;
    $oldsec = 0;
    $totsec = 0;
    $maxprog = $minm;
    $prog = $minm-1;
    $starttime = 0;
    $startsdl = $minm;
    $endsdl = $minm;
    $endsdl1 = $minm;
    $endsdl2 = $minm;
    $noofqs = 0;
    $currprogress = 0;


    $query = "SELECT a.clusterCode, c.ttAttemptID as ttaid, c.result as cres, b.subdifficultylevel, s, R, a.lastModified as lm
              FROM   adepts_teacherTopicQuesAttempt_class$class a, adepts_questions b, adepts_teacherTopicClusterStatus c
              WHERE  a.qcode = b.qcode AND a.clusterCode = b.clusterCode AND a.clusterAttemptID = c.clusterAttemptID AND c.result is not null AND a.userID = $userid AND a.teacherTopicCode = '$ttcode' AND a.lastModified < '$fromDate'
              ORDER BY a.lastModified";
    $result2 = mysql_query($query);
    $qs = mysql_num_rows($result2);
    //echo "<br> $ttcode - $userid - $qs";

    if ($qs>1)
    {
	    while($line2 = mysql_fetch_array($result2))
	    {
			list($yyyy, $mon, $dd, $hh, $min, $ss) = sscanf($line2['lm'],"%d-%d-%d %d:%d:%d");
	        $currsec = (mktime($hh,$min,$ss,$mon,$dd,$yyyy));
	        $diff = ($currsec - $oldsec)/60;
	        $soltime = $line2['s']/60;
	        if ($diff>5) $diff = $soltime+.16;
	        $oldsec = $currsec;
	        $totsec = round(($totsec + $diff),1);
	        $r = $line2['R'];
	        if ($r == 1) $correctqs += 1;

	        $ttaid = $line2['ttaid'];
	        $cresult = $line2['cres'];
	        $sdl = $line2['subdifficultylevel'];
	        $prog = $sdlNumberArray[$line2["clusterCode"]][$line2["subdifficultylevel"]];
	        if ($maxprog <= $prog and $cresult == 'SUCCESS') $maxprog = $prog;
	        $noofqs += 1;
	        if ($noofqs == 1)
	        {
	            $startsdl = $prog;
	            $starttime = $line2['lm'];
	            $prevttaid = $ttaid;
	        }
	        $endsdl1 = $maxprog;
	        //if ($noofqs == $qs) $sdlsdone = $maxprog;
	        if ($ttaid>$prevttaid and $maxprog >= $max)
	        {
	            $ttattempts += 1;
	            $prevttaid = $ttaid;
	            $endsdl2 = $maxprog;
	            if ($ttattempts>1) $maxprog = $minm;
	        }
	        //echo "<br>$flow - $ttaid - $ccode - $caid - $cresult - $noofqs - $correctqs - $totsec - $r - $sdl - $prog - $maxprog - $max - $ttattempts";
	        if ($ttattempts>3) break;
        }
        if ($ttattempts >1)
        {
            $maxprogress = round((($endsdl2 - $minm + 1)/$noofsdls)*100, 1);
            $endsdl = $endsdl2;
        }
        else
        {
            $endsdl = $endsdl1;
            $maxprogress = round((($maxprog-$minm + 1)/$noofsdls)*100, 1);
        }
        if ($maxprogress >= 100) $maxprogress = 100;
        $accuracy = round(($correctqs/$noofqs)*100, 1);
        $speed = round(((($ttattempts-1)*$noofsdls)+($maxprog-$minm+1))/($totsec/30), 1);
        $progressindex = round(($maxprogress*.7+.15*$accuracy+.15*$speed),1);
        $ttwtprogindex = round(($progressindex*$noofsdls*$ttdifficulty)/200, 1);
        $childtopicindex = $ttwtprogindex;
    }
    return $childtopicindex;
}
/* 
function getScore($childmpi, $avgmpi, $maxmpi, $scoreOutOf)
{
    $childscore = 0;
    if ($avgmpi <= 0)
        $childscore = 0;
    elseif ($childmpi<.3*$avgmpi)
	{
		if ($childmpi < 0)
		{
			$childscore = 0;
		}
		else
		{
			$childscore = round(($childmpi/(.3*$avgmpi))*3, 1);
		}
	}
	elseif ($childmpi<3*$avgmpi)
	{
		if ($maxmpi <3*$avgmpi)
		{
			$childscore = 3 + 7*round(($childmpi - (.3*$avgmpi))/($maxmpi - (.3*$avgmpi)), 1);
		}
		else
		{
			$childscore = 3 + 5*round(($childmpi - (.3*$avgmpi))/(3*$avgmpi - .3*$avgmpi), 1);
		}
	}
	else
	{
		$childscore = 8 + 2*round(($childmpi - 3*$avgmpi)/($maxmpi - 3*$avgmpi), 1);
	}

	//The child score obtained is by default out of 10 - convert it on the scale of score out of variable
	$childscore = round($scoreOutOf*$childscore/10);
	return $childscore;
} */

function isint( $mixed )
{
    return ( preg_match( '/^\d*$/'  , $mixed) == 1 );
}
function getSchoolName($schoolCode)
{
    $query  = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=$schoolCode";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    return $line[0];
}
function newScore($userid, $fromDate,$tillDate, $class,$gracePeriod){
    $calculationFactot=array();
    $param=array();
    $param[0]=explode("~",getTimeSpent($userid, $fromDate, $tillDate, $tableExtension=""));
    $param[1]=explode("~",totalQues($userid,$class, $fromDate, $tillDate, $tableExtension=""));
    $param[2]=explode("~",noOfTopicandProgress($userid,$fromDate,$tillDate));
    $param[3]=explode("~",activityDetails($userid,$fromDate,$tillDate));

    $datediff = strtotime($tillDate) - strtotime($fromDate);
    $days= floor($datediff/(60*60*24));
    $noOfMonths=$days/30;
    $gracePeriod=$gracePeriod/30;
    $gracePeriod=round($gracePeriod,1);
    $noOfMonths=round($noOfMonths,1);
    $totPeriod=$noOfMonths-$gracePeriod;

    $targetPerMonth=array();
    $targetPerMonth['time']=28800*$totPeriod;//in seconds
    $targetPerMonth['question']=240*$totPeriod;
    $targetPerMonth['topics']=2*$totPeriod;
    $targetPerMonth['activities']=6*$totPeriod;
    $targetPerMonth['activitiesTime']=1800*$totPeriod;//in seconds
    $totalTime=$param[0][2];
    if($totalTime>$targetPerMonth['time']){
        $totalTime=$targetPerMonth['time']+($totalTime-$targetPerMonth['time'])*0.5;
    }
    $calculationFactot[0]=$totalTime/$targetPerMonth['time'];
    $calculationFactot[0]=min($calculationFactot[0],3);

    $totalQuestions=$param[1][0];
    if($totalQuestions>$targetPerMonth['question']){
        $totalQuestions=$targetPerMonth['question']+($totalQuestions-$targetPerMonth['question'])*0.5;
    }
    $calculationFactot[1]=$totalQuestions/$targetPerMonth['question'];
    $calculationFactot[1]=min($calculationFactot[1],3);
    $accuracy=$param[1][1]/$param[1][0];
    $calculationFactot[2]=$accuracy/0.75;

    $totalTopics=$param[2][0];
    if($totalTopics>$targetPerMonth['topics']){
        $totalTopics=$targetPerMonth['topics']+($totalTopics-$targetPerMonth['topics'])*0.5;
    }
    $calculationFactot[3]=$totalTopics/$targetPerMonth['topics'];
    $calculationFactot[3]=min($calculationFactot[3],3);
    $calculationFactot[4]=$param[2][1]/60;
    
    $totalActivities=$param[3][0];
    if($totalActivities>$targetPerMonth['activities']){
        $totalActivities=$targetPerMonth['activities']+($totalActivities-$targetPerMonth['activities'])*1;
    }
    $calculationFactot[5]=$totalActivities/$targetPerMonth['activities'];
    $calculationFactot[5]=min($calculationFactot[5],3);
    $totalActivitiesTime=$param[3][1];
    if($totalActivitiesTime>$targetPerMonth['activitiesTime']){
        $totalActivitiesTime=$targetPerMonth['activitiesTime']+($totalActivitiesTime-$targetPerMonth['activitiesTime'])*1;
    }
    $calculationFactot[6]=$totalActivitiesTime/$targetPerMonth['activitiesTime'];
    $calculationFactot[6]=min($calculationFactot[6],3);

    $score=$calculationFactot[0]*20+$calculationFactot[1]*20+$calculationFactot[2]*20+$calculationFactot[3]*10+$calculationFactot[4]*10+$calculationFactot[5]*10+$calculationFactot[6]*10;
    if($score>100)
    {
        $score2=100;
    }
    else
        $score2=$score;
    $score2=round($score2,1);
    $score=round($score,1);
    $excelArr=array();
    $excelArr=$targetPerMonth."==".$calculationFactot."==".$param;
    //print_r($userid."~~".$days."~~".$noOfMonths."~~".$gracePeriod);
    //print_r($targetPerMonth);
    //print_r($calculationFactot);
    //print_r($param);
    //print_r("~~".$score2."~~~");
    //exit;
    return $score."~".$score2."~".$excelArr;
    
}
function getTimeSpent($userID, $startDate, $endDate, $tableExtension="")
{

	$noOfSessions = 0;
	$dateArray  = array();

	$query = "SELECT DISTINCT sessionID, startTime, endTime FROM adepts_sessionStatus  WHERE  userID=".$userID;

	if($startDate!="")
	{
		//$startDate = substr($startDate,6,4)."-".substr($startDate,3,2)."-".substr($startDate,0,2);
		$query .= " AND cast(startTime as date) >='$startDate'";
	}
	if($endDate!="")
	{
		//$endDate   = substr($endDate,6,4)."-".substr($endDate,3,2)."-".substr($endDate,0,2);
		$query .= " AND cast(startTime as date) <='$endDate'";
	}
    $time_result = mysql_query($query);
    
	$timeSpent = 0;
	while ($time_line = mysql_fetch_array($time_result))
	{
		$noOfSessions++;
		$dateStr = substr($time_line[1],0,10);
		if(!in_array($dateStr,$dateArray))
			array_push($dateArray, $dateStr);
		$startTime = convertToTime($time_line[1]);
		if($time_line[2]!="")
			$endTime = convertToTime($time_line[2]);
		else
		{
			$query = "SELECT max(lastModified) FROM adepts_teacherTopicQuesAttempt$tableExtension WHERE sessionID=".$time_line[0]." AND userID=".$userID;

			$r     = mysql_query($query);
			$l     = mysql_fetch_array($r);
			if($l[0]=="")
				continue;
			else
				$endTime = convertToTime($l[0]);
		}
		$timeSpent = $timeSpent + ($endTime - $startTime);	//in secs
	}

	//$hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);	//converting secs to hours.
	//$timeSpent = $timeSpent%3600;
	//$mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
	//$timeSpent = $timeSpent%60;
	//$secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);

	$daysLoggedIn = count($dateArray);
	return $daysLoggedIn."~".$noOfSessions."~".$timeSpent;
}
function totalQues($userID,$childClass,$fromdate,$tilldate,$tableExtension=""){
        $query  = "SELECT count(srno), sum(if(R=1,1,0)) FROM adepts_teacherTopicQuesAttempt$tableExtension"."_class".$childClass." WHERE userID = ".$userID;
    	if($fromdate!="")
    		$query .= " AND attemptedDate>='".$fromdate."'";
    	if($tilldate!="")
    		$query .= " AND attemptedDate<='".$tilldate."'";
       $ques_result = mysql_query($query) or die(mysql_error());
    	if($ques_line=mysql_fetch_array($ques_result))
    	{
    		$totalQuest = $ques_line[0];
    		$correctques = $ques_line[1];
    	}
        return $totalQuest."~".$correctques;
}
function noOfTopicandProgress($userID,$fromDate,$tillDate){
    $query  = "SELECT COUNT(a.ttAttemptId),AVG(a.progress) FROM adepts_teacherTopicStatus a WHERE a.userID=$userID AND a.lastModified>='$fromDate' AND a.lastModified<='$tillDate'";
    	$ques_result = mysql_query($query) or die(mysql_error());
    	if($ques_line=mysql_fetch_array($ques_result))
    	{
    		$topicAttempted = $ques_line[0];
    		$avgTopicProgress = $ques_line[1];
    	}
        return $topicAttempted."~".$avgTopicProgress;
}
function activityDetails($userID,$fromdate,$tillDate){
    
    $query  = "select count(distinct gameid), sum(timeTaken) from adepts_userGameDetails where userid=$userID and attemptedDate>='$fromdate' AND attemptedDate<='$tillDate'";
    	$ques_result = mysql_query($query) or die(mysql_error());
    	if($ques_line=mysql_fetch_array($ques_result))
    	{
    		$gameCount = $ques_line[0];
    		$avgActivityTime = $ques_line[1];
    	}
        return $gameCount."~".$avgActivityTime;
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
?>