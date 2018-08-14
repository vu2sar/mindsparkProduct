<?php include("header.php");
	//include("../slave_connectivity.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	
	include("functions/functions.php");
	include("classes/testTeacherIDs.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include_once("../userInterface/classes/clsTeacherTopic.php");

	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

	$class	=	$_GET['cls'];
	$section	=	$_GET['section'];
	$ttCode	=	$_GET['ttCode'];
	$userDetails	=	getStudentDetails($class, $schoolCode, $section);  //arr[userID][0] => userName , arr[userID][1] => Class section
	$userIDs	=	array_keys($userDetails);
	$userIDstr	=	implode(",",$userIDs);
	$ttDetails	=	getTeacherTopicProgress($ttCode,$userIDstr,$class);
	$AvgProgress	=	$ttDetails["avgProgress"];
	unset($ttDetails["avgProgress"]);
	$studentCompleted	=	$ttDetails["totalCompleted"];
	unset($ttDetails["totalCompleted"]);
	$uniqueFlow	=	$ttDetails["flow"];
	unset($ttDetails["flow"]);
	$userInLowLevel=0;
	$lowerLevelStudent	=	array();
	$inClassStudent	=	array();
	$clusterArray	=	array();
	$sdlArray	=	array();
	foreach($uniqueFlow as $flowstr)
	{
		if($flowstr=="")
			$flowstr	=	"MS";
		$flow = str_replace(" ","_",$flowstr);
		$objTopicProgress = new topicProgress($ttCode, $class, $flow, SUBJECTNO);
		$clusterArray[$flow]	=	${"objTopicProgress".$flowStr}->clusterArray;
		$sdlArray[$flow]	=	${"objTopicProgress".$flowStr}->sdlArray;

		$ttObj = new teacherTopic($ttCode,$class,$flow);
		//$ttObj	=	${"objTopicProgress".$flowStr}->objTT;
		
		$lowerLevelClusters	=	$ttObj->getLowerLevelClusters();
		$inLevelClusters	=	$ttObj->getClustersOfLevel($class);
		for($i=$class+1;$i<=$class+3;$i++)
		{
			$arrayCluster	=	array();
			$arrayCluster	=	$ttObj->getClustersOfLevel($i);
			if(count($arrayCluster)!=0)
			{
				if(count($higherLevelCluster)==0)
					$higherLevelCluster	=	$arrayCluster;
				else
				{
					for($j=0;$j<count($arrayCluster);$j++)
					{
						$higherLevelCluster[]	=	$arrayCluster[$j];
					}
				}
			}
		}
		$higherLevelCluster	=	array_diff($higherLevelCluster,$inLevelClusters);
		if(count($lowerLevelClusters)!=0)
		{
			//students in low level
			$sq	=	"SELECT DISTINCT a.userID FROM ".TBL_CURRENT_STATUS." AS a, ".TBL_TOPIC_STATUS." AS b 
						 WHERE b.teacherTopicCode='$ttCode' AND b.ttAttemptNo=1 AND b.userID IN ($userIDstr) AND 
						 a.ttAttemptID=b.ttAttemptID AND flow='$flowstr' AND clusterCode in ('".implode("','",$lowerLevelClusters)."')";
			$rs =	mysql_query($sq) or die(mysql_error());
			while($rw=mysql_fetch_array($rs))
			{
				$lowerLevelStudent[]	=	$rw[0];
			}
		}
		if(count($inLevelClusters)!=0)
		{
						//students in level
			$sq1	=	"SELECT DISTINCT a.userID FROM ".TBL_CURRENT_STATUS." AS a, ".TBL_TOPIC_STATUS." AS b 
						 WHERE b.teacherTopicCode='$ttCode' AND b.ttAttemptNo=1 AND b.userID IN ($userIDstr) AND 
						 a.ttAttemptID=b.ttAttemptID AND flow='$flowstr' AND clusterCode in ('".implode("','",$inLevelClusters)."')";
			$rs1	=	mysql_query($sq1) or die(mysql_error());
			while($rw1=mysql_fetch_array($rs1))
			{
				$inClassStudent[]	=	$rw1[0];
			}
		}
		if(count($higherLevelCluster)!=0)
		{
						//students higher level
			$sq2	=	"SELECT DISTINCT a.userID FROM ".TBL_CURRENT_STATUS." AS a, ".TBL_TOPIC_STATUS." AS b 
						 WHERE b.teacherTopicCode='$ttCode' AND b.ttAttemptNo=1 AND b.userID IN ($userIDstr) AND 
						 a.ttAttemptID=b.ttAttemptID AND flow='$flowstr' AND clusterCode in ('".implode("','",$higherLevelCluster)."')";
			$rs2	=	mysql_query($sq2) or die(mysql_error());
			while($rw2=mysql_fetch_array($rs2))
			{
				$higherLevelStudent[]	=	$rw2[0];
			}
		}
	}
	$activeUserID	=	array_keys($ttDetails);
	$studentBelowClass	=	count($lowerLevelStudent);
	$studentInClass		=	count($inClassStudent);
	$studentHigherClass	=	count($higherLevelStudent);
	$category   = $_SESSION['admin'];
	$schoolCode = $_SESSION['schoolCode'];
	$subcategory = $_SESSION['subcategory'];
	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
				  GROUP BY class ORDER BY class, section";
	}
	elseif (strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	else
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	$classArray = $sectionArray = $topicArray = array();
	$hasSections = false;
	$result = mysql_query($query) or die(mysql_error());
	$topicArray = array();
	while($line=mysql_fetch_array($result))
	{
		array_push($classArray, $line[0]);
		if($line[1]!='')
			$hasSections = true;
		$sections = explode(",",$line[1]);
		$sectionStr = "";
		for($i=0; $i<count($sections); $i++)
		{
			if($sections[$i]!="")
				$sectionStr .= "'".$sections[$i]."',";
		}

		$sectionStr = substr($sectionStr,0,-1);

		$tempTopicArray = getActivatedTopicsForSelectedClassAndSection($schoolCode, $line[0], $sectionStr, $category);	// for the task 11269
		$topicArray += $tempTopicArray;
		array_push($sectionArray, $sectionStr);
	}
?>

<title>My Students</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicProgress.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/dashboardCommon.js"></script>  
<script src="libs/topicReport.js?ver=1"></script>	<!-- for the task 11269 -->
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	$(document).ready(function(){
		$("#myTopicUrl").attr("href","#");
        $("#topicReportUrl").attr("href", "#");

		$("#myTopicUrl").click(function(){
			<?php if($_GET['ttCode'] == ""){ ?>
				var alertMsg = "Topic Page/Research is available only for a single topic. Please select a topic and press Go.";	
				alert(alertMsg);
			<?php }else {
				echo "$('#myTopicUrl').attr('href','".getTopicPageLink($_GET["ttCode"],$_GET["cls"],$_GET["section"])."');";
			} ?>
		});

		$("#topicReportUrl").click(function(){
			if($("#lstTeacherTopic").val() != "")
			{
				var cls = $("#lstClass").val();
				var sec = $("#lstSection").val();
				var topic = $("#lstTeacherTopic").val();
				var topicName = encodeURIComponent($("#lstTeacherTopic option:selected").html());
				$("#topicReportUrl").attr("href","topicReport.php?schoolCode=<?= $schoolCode;?>&cls="+cls+"&sec="+sec+"&topics="+topic+"&mode=0&topicName="+topicName);
			}
			else
			{
				alert("Please select a topic.");
			}
		});

	});
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
	}
	function download()
	{
		frmExcel.submit();
	}
</script>
<script>
	var gradeArray   = new Array();
	var sectionArray = new Array();
	var topicCodeArray   = new Array();
	var topicArray   = new Array();
	var loadPageFlag = <?php echo isset($_GET['loadPageFlag'])?$_GET['loadPageFlag']:0?>;
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
			echo "gradeArray.push($classArray[$i]);\r\n";
			echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
			$temptopicCode = array_keys($topicArray[$classArray[$i]]);
			$tempCodeStr = $tempTopicStr = '';
			for($j=0; $j<count($temptopicCode); $j++)
			{
				$tempCodeStr .= "'".$temptopicCode[$j]."',";
				$tempTopicStr .= "'".$topicArray[$classArray[$i]][$temptopicCode[$j]]."',";

			}
			$tempCodeStr = substr($tempCodeStr,0,-1);
			$tempTopicStr = substr($tempTopicStr,0,-1);
			echo "topicArray[$i] = new Array($tempTopicStr);\r\n";
			echo "topicCodeArray[$i] = new Array($tempCodeStr);\r\n";
		}
	?>
	function validate()
	{
		if(document.getElementById('lstClass').value=="")
		{
			alert("Please select a class!");
			document.getElementById('lstClass').focus();
			return false;
		}
		if(document.getElementById('lstTeacherTopic').value=="")
		{
			alert("Please select a topic!");
			$("#topicReportUrl").attr("href","topicReport.php?schoolCode=<?= $schoolCode;?>&cls=<?=$cls?>&sec=<?=$section?>&startDate=<?=date('Y-m-d')?>&endDate=<?=date('Y-m-d')?>&mode=1");	// for the task 11269
			$("#myTopicUrl").attr("href","");	// For the task 11269
			document.getElementById('lstTeacherTopic').focus();
			return false;
		}
		if(document.getElementById('lstTeacherTopic').value!="" && document.getElementById('lstClass').value!=""){
			setTryingToUnload();
			window.location.href="topicProgress.php?cls="+document.getElementById('lstClass').value+"&section="+document.getElementById('lstSection').value+"&ttCode="+document.getElementById('lstTeacherTopic').value;
		}
	}
	function setSection(sec)
{
	var cls = document.getElementById('lstClass').value;

	if(document.getElementById('lstSection'))
	{
		var obj = document.getElementById('lstSection');
		removeAllOptions(obj);
		if(cls=="")
		{
			$(".noSection").show();
			document.getElementById('lstSection').style.display = "inline";
			document.getElementById('lstSection').selectedIndex = 0;
		}
		else
		{
			for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
			if(sectionArray[i].length>0)
			{
				for (var j=0; j<sectionArray[i].length; j++)
				{
					OptNew = document.createElement('option');
					OptNew.text = sectionArray[i][j];
					OptNew.value = sectionArray[i][j];
					if(sec==sectionArray[i][j])
					OptNew.selected = true;
					obj.options.add(OptNew);
				}
				$(".noSection").show();
				document.getElementById('lstSection').style.display = "inline";
				document.getElementById('lblSection').style.display = "inline";
			}
			else
			{
				$(".noSection").hide();
				document.getElementById('lstSection').style.display = "none";
				document.getElementById('lblSection').style.display = "none";
			}
		}
	}

}

function removeAllOptions(selectbox)
{
	var i;
	for(i=selectbox.options.length-1;i>0;i--)
	{
		selectbox.remove(i);
	}
}

function populateTopic(topicCode)
{
	var cls = document.getElementById('lstClass').value;

	var obj = document.getElementById('lstTeacherTopic');
	removeAllOptions(obj);
	if(cls!="")
	{
		for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		if(topicCodeArray[i].length>0)
		{
			for (var j=0; j<topicCodeArray[i].length; j++)
			{
				OptNew = document.createElement('option');
				OptNew.text = topicArray[i][j];
				OptNew.value = topicCodeArray[i][j];
				if(topicCode==topicCodeArray[i][j])
				{
					OptNew.selected = true;
				}
				obj.options.add(OptNew);
			}
		}
	}
}

function populateTopicWhenSectionChange() // for the task 11269
{
	var dataArr =$("#container :input");
	var schoolCode = <?=$schoolCode?>;
	var schoolCodeNode = document.createElement('input'); // adding schoolCode to 
	schoolCodeNode.setAttribute('type','hidden');
	schoolCodeNode.setAttribute('name','schoolCode');
	schoolCodeNode.setAttribute('id','schoolCode');
	schoolCodeNode.setAttribute('value',schoolCode);
	dataArr.push(schoolCodeNode);
	var category = '<?=$category?>';
	var categoryNode = document.createElement('input'); // adding category to 
	categoryNode.setAttribute('type','hidden');
	categoryNode.setAttribute('name','category');
	categoryNode.setAttribute('id','category');
	categoryNode.setAttribute('value',category);
	dataArr.push(categoryNode);

	var obj = document.getElementById('lstTeacherTopic');
	var lstTeacherTopicVal = $("#lstTeacherTopic").val();
	removeAllOptions(obj);

	ajaxRequestForActivatedTopicsForSelectedClassAndSection(dataArr, obj, '<?=$ttCode?>');
}

</script>
</head>
<body class="translation" onLoad="load();setSection('<?=$section?>');populateTopicWhenSectionChange();" onResize="load()">
	<?php include("eiColors.php") ?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php") ?>
	</div>
	<div id="topBar">
		<?php include("topBar.php") ?>
	</div>
	<div id="sideBar">
		<?php include("sideBar.php") ?>
	</div>

	<div id="container">
		<div id="trailContainer">
		<table id="childDetails">
		<tbody>
			<tr>
				<td width="33%" class="activatedTopic" ><a id="myTopicUrl" href="<?=getTopicPageLink($ttCode,$class,$section);?>" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle" ></div> <label class="pointer" style="cursor:pointer;">Topic Page / Research</label></a></td>
	        	<td width="33%" class="activateTopicAll" ><a id="topicReportUrl" href="topicReport.php?schoolCode=<?= $schoolCode;?>&cls=<?=$cls?>&sec=<?=$section?>&topics=<?=$ttCode?>&mode=0&topicName=<?= rawurlencode(getTopicName($ttCode));?>" style="text-decoration: none;"><div  style="cursor:pointer;" class="smallCircle" ></div><label class="pointer" style="cursor:pointer;">Topic Report</label></a></td>
	        	<td width="33%" class="activateTopics" ><div style="cursor:pointer;" class="smallCircle red" ></div><label class="pointer textRed" style="cursor:pointer;">Topic Progress Report</label></td>
			</tr> 
		</tbody>
	</table>
			<!-- <div class="headerBar">
				<div class="classTabTriangle" id="trianglecls1" style=""> </div>
				<div class="pageName">Topicwise class performance</div>
				<div class="classTabTriangle" id="trianglecls2" style=""> </div>
				<div id="classTopic">
					<div class="classText"><span>Class: <?=$class.$section?> : </span><span id="topicRed"><?=getTopicName($ttCode)?></span></div>
				</div>
			</div>
			 -->
			<!-- <div class="headerBar">
				<div id="resetPassword2">
					<div class="resetPassword2"></div>
					<div class="pageText" ><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&cls=<?=$class?>&flow=<?=$uniqueFlow[0]?>">Topic Research</a></div>
				</div>
				<div id="noticeBoard">
					<div class="noticeBoard"></div>
					<div class="pageText"><a href="topicRemediationSection.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>">Topic Remediation </a></div>
				</div>
				<div id="editDetails">
					<div class="editDetails"></div>
					<div class="pageText"><a href='javascript:download();'>Download Table In Excel</a></div>
				</div>
			</div>  -->
			<table id="topicDetails">
				<td width="7%"><label for="lstClass">Class</label></td>
		        <td width="20%" style="border-right:1px solid #626161">
		            <select name="class" id="lstClass"  onchange="setSection('');populateTopic('');" style="width:80%;">
						<option value="">Select</option>
						<?php
							for ($i=0;$i<count($classArray);$i++)
							{
								echo "<option value='".$classArray[$i]."'";
								if ($cls==$classArray[$i])
								{
									echo " selected";
								}
								echo ">".$classArray[$i]."</option>";
							}
						?>
					</select>
		        </td>
				<td width="6%" class="noSection"><label for="lstSection" id="lblSection" style="margin-left:20px;">Section</label></td>
		        <td width="22%" style="border-right:1px solid #626161" class="noSection">
		            <select name="section" id="lstSection" onChange="populateTopicWhenSectionChange();" style="width:80%;">
						<option value="">All</option>
					</select>
		        </td>
				<td width="7%"><label for="lstTeacherTopic" style="margin-left:10px;">Topic</label></td>
		        <td width="22%">
		            <select name="ttCode" id="lstTeacherTopic" style="width:80%;">
					   <option value=''>Select</option>
				    </select>
		        </td>
				<td width="24%">
					<input type="submit" class="button" name="generate" id="btnGo" value="Go" onClick="return validate();">
					<input type="button" class="button"  value="Download" onClick="javascript:download();">
				</td>
			</table>
			<br />
			<?php 
				if(!empty($ttCode))	// For the task 11269
				{
			?>
					<div class="headerBar3">
						<div id="avgClassProgress">Average Progress of class : <?=round($AvgProgress,1)?>%</div>
						<div id="noOfStudent">Number of students completed the topic : <?=$studentCompleted?></div>
						<div class="clear"></div>
						<!--<div id="studentDistribution">
							<input type="hidden" class="below" value="<?=implode(",",$lowerLevelStudent)?>" />
							<input type="hidden" class="in" value="<?=implode(",",$inClassStudent)?>" />
							<input type="hidden" class="above" value="<?=implode(",",$higherLevelStudent)?>" />
							Student distribution - Below Class : 
		                    <span class="studentsBunch" id="below"><?=$studentBelowClass?></span>
		                     &nbsp; In Class : <span class="studentsBunch" id="in"><?=$studentInClass?></span>
		                     &nbsp; Above Class : <span class="studentsBunch" id="above"><?=$studentHigherClass?></span>
		                     &nbsp; <span class="studentsBunch" id="all" style="color:#F00">All</span>
						</div>-->
					</div>
					
					<div id="topicDetailsDiv">
					<?
						ob_start();
					?>
						<table cellpadding="5" width="100%" border="0" align="center" class="tableReport">
							<thead>
							<tr>
								<td scope="col" onClick="sortColumn(event)" type="Number" width="5%"><u>S. No</u></td>
								<td scope="col" onClick="sortColumn(event)" type="CaseInsensitiveString" width="20%"><u>Students</u></td>
								<td scope="col" onClick="sortColumn(event)"  type="ttProgress" width="20%"><u>Progress</u></td>
								<td width="7%" onClick="sortColumn(event)" type="Number"  scope="col"><u>Total Qs</u></td>
								<td width="7%" onClick="sortColumn(event)" type="Number"  scope="col"><u>% Correct</u></td>
								<!--<th scope="col">Time(In sec)</th>-->
								<td width="9%" onClick="sortColumn(event)" type="Number"  scope="col"><u>Total Attempts</u></td>
								<td width="35%" onClick="sortColumn(event)" type="CaseInsensitiveString" scope="col"><u>Learning units not cleared</u></td>
								<td width="7%" scope="col">Trail</td>
							</tr>
							</thead>
							<tbody>
						    <?php 
						    $i=0;
						    foreach ($userDetails as $studentID=>$users) { 
								$currentStatusArray	=	getCurrentStatus($studentID,$ttCode);
								$detail	=	$ttDetails[$studentID];
						    	$i++;
						    	?>
						        <tr id="student<?=$studentID?>" class="studentData">
									<td valign="middle"><div class="studentSrno"><?=$i?>.</div></td>
									<td valign="middle"><div class="studentname"><?=$users[0]?></div></td>
						        	<td valign="middle">
									<?php if($detail["progress"]!="") { 
												$flowstr	=	$ttDetails[$studentID]["flow"];
												if($flowstr=="")
													$flowstr	=	"MS";
												$flow = str_replace(" ","_",$flowstr);
									?>
						            <div class="topicProgress">
						         	<?php 
									
									echo showTopicProgress($studentID,$detail["progress"],$detail["failedCluster"],$clusterArray[$flow], $sdlArray[$flow], $currentStatusArray["currentCluster"], $currentStatusArray["currentSdl"],$class,$detail["result"]); ?>
						            <?php if(in_array($studentID,$lowerLevelStudent)) { ?>
						                <img src="assets/red_star.gif" alt="Red Star" height="20" width="20" style="clear:left;">
						            <?php } ?>
						            <?php if($detail["higherLevel"]==1) { ?>
						                <img src="assets/green_star.gif" alt="Green Star" height="20" width="20" style="clear:left;">
						            <?php }
									} else { echo $detail["progress"]==""?"&nbsp;":$detail["progress"]; } ?>
									</div>
									</td>
						            <td align="center"><?php if($detail["totalQuesAttmpt"]!="") echo $detail["totalQuesAttmpt"]; else echo "0";?></td>
						            <td align="center"><?php if($detail["accuracy"]!="") echo round($detail["accuracy"],1); else echo "0";?></td>
						            <td align="center"><?=$detail["attempt"]?></td>
						            <td><?=str_replace("'","&quot;",getClusterName($detail["failedCluster"]))?></td>
						            <td align="center"><a class="buttonLink" href="studentTrail.php?topic_passed_id=<?=$ttCode?>&user_passed_id=<?=$studentID?>" target="_blank" style="text-decoration:underline;color:red;">Trail</a></td>
						        </tr>
						    <?php } ?>
							</tbody>
						</table>
						<?
							$table = ob_get_contents();
						
							ob_end_clean();
						
							echo $table;
						?>				
						<form name='frmExcel' target="" action="export.php" method="POST">
							<input type="hidden" name="content" value='<?=strip_tags(addslashes($table),"<table><tr><td>")?>'>
						</form>
					</div>
					<div align="center" class='legend'>
						<img src="assets/green_star.gif" alt="Green Star" height="20" width="20"> Gone to a higher level &nbsp;&nbsp;
						<img src="assets/red_star.gif" alt="Green Star" height="20" width="20"> CURRENTLY in a lower level
						<br/>
						<span style="font-size:20px; font-weight:bold">&darr;</span> indicates current position of students who have fallen back in the topic in the first attempt &nbsp;&nbsp;
						<span style="font-size:15px; font-weight:bold">&darr;<sub>R</sub></span> indicates current position of students in case of repeat attempt in the topic
						<br/>(Note: This indication is not available to the student)
					</div>
			<?php 
				}
				else
				{
			?>
					<script type='text/javascript'>		//For the task 11269
							$('#btnGo').trigger('click');							
					</script>
			<?php
				}
			?>
			
		</div>
	</div>

<?php include("footer.php") ?>

<?php

function getFailedClusters($ttAttemptID, $class, $objTopicProgress)
{
	//Get the failed clusters in the last completed attempt, if any, or the current attempt
	$failedClusterArray = array();
	$query  = "SELECT ttAttemptID, result, failedClusters FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID in ($ttAttemptID) ORDER BY ttAttemptID DESC";
	$result = mysql_query($query);
	$noOfAttempts = mysql_num_rows($result);
	while ($line = mysql_fetch_array($result))
	{
		if(($line[1]!="" && $noOfAttempts>1) || ($noOfAttempts==1))
		{
			if($line[2]!="")
			{
				$tmpCluster = explode(",",$line[2]);
				for($i=0; $i<count($tmpCluster); $i++)
				{
					$clusterCode = trim($tmpCluster[$i]);
					$levelArray = $objTopicProgress->objTT->getClusterLevel($clusterCode);
					if($levelArray[0] <= $class )	//Do not show  the clusters failed of  a higher level.
						array_push($failedClusterArray,trim($tmpCluster[$i]));
				}
			}
			break;
		}
	}
	return $failedClusterArray;
}

function getTeacherTopicProgress($ttCode,$userIDstr,$cls)
{
	$progress	=	array();
	$flowN	=	array();
	$total	=	0;
	
	$totalSDLS = 0;
    $clusterArray    = array();
    $sdls            = array();
    $clusterLevelArray      = array();
	$userTopicAttemptArray = array();

	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";
    $r = mysql_query($q);
    while($l = mysql_fetch_array($r))
    {
    	$flowN = $l[0];
    	$flowStr = str_replace(" ","_",$flowN);
    	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
    }
	
	$sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
			 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow ,result
			 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID IN ($userIDstr) GROUP BY userID";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$flowK	=	$rw[6];
    	$flowK	=	str_replace(" ","_",$flowK);
		//$teacherTopicDetails[$rw[0]]["progress"]	=	$rw[1];
		$teacherTopicDetails[$rw[0]]["progress"]	=	max($rw[1],${"objTopicProgress".$flowK}->getProgressInTT($rw[0]));
		//$teacherTopicDetails[$rw[0]]["higherLevel"] = ${"objTopicProgress".$flowK}->higherLevel;
		$teacherTopicDetails[$rw[0]]["higherLevel"] = ${"objTopicProgress".$flowK}->getHigherLevel($rw[0]);
		$arrayQuesDetails	=	getQuesAccuracy($rw[0],$ttCode,$cls);
		$teacherTopicDetails[$rw[0]]["totalQuesAttmpt"]	=	$arrayQuesDetails["totalQ"];
		$teacherTopicDetails[$rw[0]]["accuracy"]	=	$arrayQuesDetails["accuracy"];			
		$teacherTopicDetails[$rw[0]]["attempt"]	=	$rw[4];
		$teacherTopicDetails[$rw[0]]["failedCluster"]	=	getFailedClusters($rw[5], $cls, ${"objTopicProgress".$flowK});
		$teacherTopicDetails[$rw[0]]["flow"]	=	$rw[6];
		$teacherTopicDetails[$rw[0]]["result"]	=	$rw[7];
		$progress[]	=	$teacherTopicDetails[$rw[0]]["progress"];
		$flow[]	=	$rw[6];
		if(round($rw[1])==100)
			$total++;
	}
	$totalProgress	=	round(array_sum($progress)/(substr_count($userIDstr,",")+1),2);	
	$teacherTopicDetails["avgProgress"]	=	$totalProgress;
	$teacherTopicDetails["totalCompleted"]	=	$total;
	$flow	=	array_unique($flow);
	$teacherTopicDetails["flow"]	=	$flow;
	return $teacherTopicDetails;
}

?>