<?php
include ("header.php");
set_time_limit ( 0 ); // Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
                    // error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
include ("functions/functions.php");
include ("classes/testTeacherIDs.php");
include_once ("../userInterface/classes/clsTopicProgress.php");
// include("../slave_connectivity.php");

$userID = $_SESSION ['userID'];
$schoolCode = isset ( $_SESSION ['schoolCode'] ) ? $_SESSION ['schoolCode'] : "";
$user = new User ( $userID );
$todaysDate = date ( "d" );

if (! isset ( $_SESSION ["topicData"] )) {
	$_SESSION ["topicData"] = "";
	$_SESSION ["ttName"] = "";
}
if ($_REQUEST ["live"]) {
	$_SESSION ["topicData"] = "";
	$_SESSION ["ttName"] = "";
	$live = $_REQUEST ["live"];
} else
	$live = 0;
	// echo '<br/>Category:'. $user->category;
if (strcasecmp ( $user->category, "School Admin" ) == 0) {
	$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() 
				   AND subjects like '%" . SUBJECTNO . "%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} elseif (strcasecmp ( $user->category, "Teacher" ) == 0) {
	$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno=" . SUBJECTNO . "
				  GROUP BY class ORDER BY class, section";
} elseif (strcasecmp ( $user->category, "Home Center Admin" ) == 0) {
	$query = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
			       FROM     adepts_userDetails
			       WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 
				   AND endDate>=curdate() AND subjects like '%" . SUBJECTNO . "%'
			       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
} else {
	echo "You are not authorised to access this page!";
	exit ();
}

$classArray = $sectionArray = array ();
$hasSections = false;
$result = mysql_query ( $query ) or die ( mysql_error () );
while ( $line = mysql_fetch_array ( $result ) ) {
	array_push ( $classArray, $line [0] );
	if ($line [1] != '')
		$hasSections = true;
	$sections = explode ( ",", $line [1] );
	$sectionStr = "";
	for($i = 0; $i < count ( $sections ); $i ++) {
		$classSectionArr [] = $line [0] . $sections [$i];
		if ($sections [$i] != "")
			$sectionStr .= $sections [$i] . ",";
	}
	$sectionStr = substr ( $sectionStr, 0, - 1 );
	array_push ( $sectionArray, $sectionStr );
}
$ref = "";
if (isset ( $_GET ['ref'] ))
	$ref = $_GET ['ref'];

$logincount = 0;
$secAT = 0;
foreach ( $classArray as $classTA ) {
	$sectionsTA = $sectionArray [$secAT];
	$allSectionArrayTA = explode ( ",", $sectionsTA );
	$secAT ++;
	
	foreach ( $allSectionArrayTA as $sectionTA ) {
		$prevdate = date ( 'Ymd', strtotime ( '-7 days' ) );
		
		$query = "SELECT A.userID FROM adepts_userDetails A LEFT JOIN adepts_sessionStatus B on A.userID = B.userID and B.startTime_int >= $prevdate where A.schoolcode=$schoolCode and A.childclass = $classTA";
		if (! empty ( $sectionTA ))
			$query .= " and A.childsection = '$sectionTA'";
		$query .= " and A.enabled = 1 and A.category!= 'TEACHER' and A.endDate > CURDATE() AND B.userID is NULL";
		
		$result = mysql_query ( $query ) or die ( mysql_error () );
		$logincount += mysql_num_rows ( $result );
	}
}

$query = "select viewCount from teacherAlertLoginUser where userID = $userID and DATE(lastmodified) = DATE(NOW())";
$result = mysql_query ( $query );
if ($result) {
	while ( $line = mysql_fetch_array ( $result ) ) {
		$logincount = $line ['viewCount'];
	}
}

if (strcasecmp ( $user->category, "School Admin" ) == 0)
	$logincount = 0;

$_SESSION ["logincount"] = $logincount;

$sec = 0;
$classAll = $classArray;
$sectionAll = $sectionArray;
$totalClass = count ( $classArray );
$sectionStr = implode ( ",", $sectionArray );
$totalsection = count ( explode ( ",", $sectionStr ) );

if (isset ( $_REQUEST ["go"] )) {
	$classArray = array ();
	$sectionArray = array ();
	$childClass = $_POST ["childClass"];
	$childSection = $_POST ["childSection"];
	$classArray [] = $childClass;
	$sectionArray [] = $_POST ["childSection"];
	$_SESSION ["topicData"] = "";
	$live = 0;
}

$liveClasses = 0;
if (strcasecmp ( $user->category, "Teacher" ) == 0 && $live == 1)
	$arrayLoggedIn = getLiveClasses ( $schoolCode, $classSectionArr );
else if (isset ( $_REQUEST ["go"] )) {
	$classSectionArrNew [] = $childClass . $childSection;
	if ($live == 1)
		$arrayLoggedIn = getLiveClasses ( $schoolCode, $classSectionArrNew );
} else if ($live == 1)
	$arrayLoggedIn = getLiveClasses ( $schoolCode );

foreach ( $arrayLoggedIn as $childClassLive => $childSectionArr ) {
	foreach ( $childSectionArr as $childSectionLive => $noOfLoggedIn ) {
		$userIDArrayLive = getStudentDetails1 ( $childClassLive, $schoolCode, $childSectionLive );
		$userArrayClass [$childClassLive . $childSectionLive] = $userIDArrayLive;
		if (round ( $noOfLoggedIn / count ( $userIDArrayLive ) ) >= 0.5) {
			$classStatus [$childClassLive . $childSectionLive] = 1;
			$liveClasses ++;
		} else if ($live != 1)
			$classStatus [$childClassLive . $childSectionLive] = 0;
	}
}
$sec = 0;
$classCount = 0;

if ($_SESSION ["topicData"] == "") {
	if ($live != 1) {
		if (($classAll + $sectionAll) > 2) {
			$otherClassDisp = 2 - $liveClasses;
			if ($otherClassDisp < 0)
				$otherClassDisp = 0;
		}
	}
	
	foreach ( $classArray as $class ) {
		$sections = $sectionArray [$sec];
		$allSectionArray = explode ( ",", $sections );
		$sec ++;
		foreach ( $allSectionArray as $section ) {
			if (! $classStatus [$class . $section] && $live == 1)
				continue;
			else if ($live != 1) {
				if ($classCount > $otherClassDisp) {
					if (! $classStatus [$class . $section])
						continue;
				}
			}
			$teacherTopicActivated = array ();
			$userIDArray = array ();
			$teacherTopicActivated = getTTsActivated1 ( $class, $schoolCode, $section );
			if ($userArrayClass [$class . $section])
				$userIDArray = $userArrayClass [$class . $section];
			else {
				$userIDArray = getStudentDetails1 ( $class, $schoolCode, $section );
				$userArrayClass [$class . $section] = $userIDArray;
			}
			
			if (count ( $userIDArray ) == 0)
				continue;
			else if (count ( $teacherTopicActivated ) == 0)
				continue;
			else
				$classCount ++;
			$classSection = $class . "|" . $section;
			$userIDs = array_keys ( $userIDArray );
			$userIDstr = implode ( ",", $userIDArray );
			
			foreach ( $teacherTopicActivated as $ttCode => $ttName ) {
				$ttnameArray [$ttCode] = $ttName;
				if ($classStatus [$class . $section] == 1)
					$topicRemediation [$classSection] ["priority"] = 1;
				else
					$topicRemediation [$classSection] ["priority"] = 0;
				$topicRemediation [$classSection] [$ttCode] ['lowLevel'] = 0;
				$topicRemediation [$classSection] [$ttCode] ['clusterFailed'] = 0;
				
				// select ttattempt ids
				$attempt_query = "SELECT failedClusters,flow 
									 FROM " . TBL_TOPIC_STATUS . " WHERE userID IN (" . implode ( ",", $userIDArray ) . ") AND 
									 teacherTopicCode='$ttCode'";
				$attempt_result = mysql_query ( $attempt_query );
				if (mysql_num_rows ( $attempt_result ) == 0)
					continue;
				$failedCluster = array ();
				$flowArray = array ();
				while ( $attempt_line = mysql_fetch_array ( $attempt_result ) ) {
					$flowArray [] = $attempt_line [1];
					if ($attempt_line [0] != "")
						$failedCluster [] = $attempt_line [0];
				}
				$failedClusterStr = implode ( ",", $failedCluster );
				$topicRemediation [$classSection] [$ttCode] ['clusterFailed'] = count ( explode ( ",", $failedClusterStr ) );
				
				$flowUniqueArray = array_unique ( $flowArray );
				$userInLowLevel = 0;
				foreach ( $flowUniqueArray as $flowstr ) {
					$lowerLevelClusters = array ();
					$flow = str_replace ( " ", "_", $flowstr );
					$ttObj = new teacherTopic ( $ttCode, $class, $flow );
					$lowerLevelClusters = $ttObj->getLowerLevelClusters ();
					if (count ( $lowerLevelClusters ) != 0) {
						$query = "SELECT COUNT(DISTINCT a.userID) FROM " . TBL_CLUSTER_STATUS . " AS a, " . TBL_TOPIC_STATUS . " AS b 
									 WHERE b.teacherTopicCode='$ttCode' AND b.ttAttemptNo=1 AND b.userID IN ($userIDstr) AND 
									 a.ttAttemptID=b.ttAttemptID AND a.attemptType='N' AND flow='$flowstr' AND 
									 clusterCode in ('" . implode ( "','", $lowerLevelClusters ) . "')";
						$r2 = mysql_query ( $query ) or die ( mysql_error () );
						$rw = mysql_fetch_array ( $r2 );
						$userInLowLevel += $rw [0];
					}
				}
				$topicRemediation [$classSection] [$ttCode] ['lowLevel'] = $userInLowLevel;
			}
			if ($classCount > 2 && $live != 1)
				break;
		}
		if ($classCount > 2 && $live != 1)
			break;
	}
	$topicRemediationTemp = $topicRemediation;
	$topicRemediation = array ();
	foreach ( $topicRemediationTemp as $clsSec => $details ) {
		if ($details ["priority"] == 1)
			$topicRemediation [$clsSec] = $details;
	}
	foreach ( $topicRemediationTemp as $clsSec => $details ) {
		if ($details ["priority"] != 1)
			$topicRemediation [$clsSec] = $details;
	}
	$_SESSION ["topicData"] = $topicRemediation;
	$_SESSION ["ttName"] = $ttnameArray;
	$_SESSION ["liveSession"] = $classStatus;
}
$topicRemediation = $_SESSION ["topicData"];
$ttnameArray = $_SESSION ["ttName"];
$classStatus = $_SESSION ["liveSession"];

$teacherForumPrompt = checkForTeacherForumPrompt($_SESSION ['username'], $_SESSION ['schoolCode']);
?>

<title>Landing Screen</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet"	type="text/css">
<link href="css/common.css?ver=8" rel="stylesheet" type="text/css">
<link href="css/landingScreen.css" rel="stylesheet" type="text/css">
<link href="css/popupwindow.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<!-- <script type="text/javascript" src="libs/jquery-ui.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/popupwindow.js"></script>

<script type="text/javascript"
	src="../userInterface/libs/closeDetection.js"></script>
<script>
	function load()
	{
		var sideBarHeight = window.innerHeight-95;
		$("#sideBar").css("height",sideBarHeight+"px");
	}
	
	function resetKudosCounter()
	{
	username='<?= $_SESSION['username'] ?>';
	//alert('AJAX STARTS');		
	$.ajax({
	type:'POST',
    url: "../userInterface/kudosAjax.php",
    data: {resetKudos: "YES",userName: username},
	success: function(){console.log("In RESET");} // or function=date if you want date
    });
	
	//alert("AJAX CALL FINISHED");
	
	}
	
$(function() {
<?php
        if($teacherForumPrompt=="1" || $teacherForumPrompt=="2") {        
?>         
            $('#iLoginPrompt').popUpWindow({
                action: "open", // open or close
                modal: true, // modal mode
                size: "medium" // large, medium or large
            });
<?php
        } 
?>     
});
function trackTeacherForum()
{
	$.ajax({
			  url: 'ajaxRequest.php',
			  type: 'post',
			  data: {'mode': 'teacherForum'},
			  success: function(response) {  }
          });				  
}	
</script>

<script>
var gradeArray   = new Array();
var sectionArray = new Array();
<?php
for($i = 0; $i < count ( $classAll ); $i ++) {
	$sectionStr = str_replace ( ",", "','", $sectionAll [$i] );
	echo "gradeArray.push($classAll[$i]);\r\n";
	echo "gradeArray[$classAll[$i]] = new Array('$sectionStr');\r\n";
}
?>
	$(document).ready(function(e) {
		$(".techerUpperLink").change(function() {
			var val	=	$(this).val();
			changeMode(val);
		});
    });
	

function changeMode(val)
{
	if(val=="students")
	{
		setTryingToUnload();
		window.location.href	=	"classStudent.php";
	}
	else if(val=="classes")
	{
		setTryingToUnload();
		window.location.href	=	"home.php";
	}
	else
	{
		$("#remediationDetails").hide();
		$("#myAccount").hide();
		$("#otherFeatures").show();
		$("#studentLinks").hide();
		$("#classStudents").hide();
		$("#clasSectionDrop").hide();
		$("#moreClass").hide();
	}
}
function viewSection(grade,sec)
{
	var allsectionArray	=	new Array();
	allsectionArray	=	gradeArray[grade];
	var obj = document.getElementById('childSection');
	removeAllOptions(obj);
	
	if(gradeArray[grade] == "")
	{
		$(".noSection").hide();
		/*document.getElementById("childSection").disabled=true;*/
		$(".noClass").css("border-right","0px solid #626161");
	}
	else
	{
		$(".noSection").show();
		$(".noClass").css("border-right","1px solid #626161");
		/*document.getElementById("childSection").disabled=false;*/
		for (var j=0; j<allsectionArray.length; j++)
		{
			OptNew = document.createElement('option');
			OptNew.text = allsectionArray[j];
			OptNew.value = allsectionArray[j];
			if(sec==allsectionArray[j])
			OptNew.selected = true;
			obj.options.add(OptNew);
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
<?php
if ($ref == "feature")
	echo "changeMode('$ref')";
if ($childClass != "") {
	?>
$(document).ready(function(e) {
	
	viewSection(<?=$childClass?>,'<?=$childSection?>');
});
<?php

}
/*
 * if($_SESSION["countDown"]==1) { ?>
 * $(document).ready(function(e) {
 * $("#countDown").show();
 * });
 * <?php }
 */
?>
</script>

<style>
#countDown {
	width: 100%;
	position: absolute;
	z-index: 1000;
	background-color: #FFFFFF;
	display: none;
	text-align: center;
}
</style>
</head>
<body class="translation" onLoad="load()" onResize="load()"
	style="overflow: auto">
	<!--<div id="countDown"><img src="http://d2tl1spkm4qpax.cloudfront.net/content_images/newUserInterface/teasers/countDown.gif" width="401" height="401"></div>-->
<?php include("eiColors.php")?>
<div id="fixedSideBar">
	<?php include("fixedSideBar.php")?>
</div>
	<div id="topBar">
	<?php include("topBar.php"); ?>
</div>
	<div id="sideBar">
	<?php include("sideBar.php")?>
	<div class="blackboard_screen">
			<div style="padding-top: 10px; text-align: center;">
				<span style="font-size: 1.2em;"><b>Announcements</b></span>
			</div>
			<div class='announcement_message'
				style="margin-left: 10px; margin-right: 10px; margin-top: 10px; margin-bottom: 10px; height: 300px;">
				<br>
		<?php
		if (strcasecmp ( $user->category, "School Admin" ) == 0) {
			$query = "SELECT COUNT(*) FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%" . SUBJECTNO . "%'";
		} else {
			$query = "SELECT COUNT(*) FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.teacherUserID=$userID AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%" . SUBJECTNO . "%'";
		}
		$result = mysql_query ( $query ) or die ( mysql_error () );
		$row = mysql_fetch_array ( $result );
		if ($row [0] > 0) {
			if ($row [0] == 1)
				echo "<ul><li style='color:#f00;'><a href='updateStudentPassword.php'>" . $row [0] . " request is pending for password reset / account unlock.</a></li></ul>";
			else
				echo "<ul><li style='color:#f00;'><a href='updateStudentPassword.php'>" . $row [0] . " requests are pending for password reset / account unlock.</a></li></ul>";
		}
		?>
		<?php
		$currentDate = date ( "Y-m-d" );
		$announcementsArray = getAnnouncements1 ( $currentDate, $schoolCode, $userID, $userCategory );
		if (count ( $announcementsArray ) <= 3) {
			foreach ( $announcementsArray as $id => $details ) {
				if ($id != 1) {
					?>
						<ul>
					<li>
							<?php } if(!empty($details['contentId'])) { ?>
							<a href='<?=$details['link']?>' class='anouncementLink'><?=$details['title']?></a>
							<?php
				
} else {
					echo $details ['title'];
				}
				if ($id != 1) {
					?>
							</li>
				</ul>
					<?php
				
}
			}
		} else {
			?>
			<marquee scrollamount=3 onMouseOver="this.stop()"
					onMouseOut="this.start()" direction="up" height="300">
			<?php
			foreach ( $announcementsArray as $id => $details ) {
				?>
			<ul>
						<li>
				<?php if(!empty($details['contentId'])) { ?>
				<a href='<?=$details['link']?>' class='anouncementLink'><?=$details['title']?></a>
				<?php
				
} else {
					echo $details ['title'];
				}
				?>
				</li>
					</ul>
			<?php } } ?>
			</marquee>
			</div>
		</div>
	</div>

	<div id="container">
		<form id="frmMain" action="" method="post"
			onSubmit="setTryingToUnload();">
			<table id="topicDetails">
				<tr>
					<td width="6%"><label>Class</label></td>
					<td width="27.6%" style="border-right: 1px solid #626161"
						class="noClass"><select name="childClass"
						onChange="viewSection(this.value,'')">
							<option value="">Select Class</option>
				<?php
				foreach ( $classAll as $class ) {
					$selected = "";
					if ($childClass == $class)
						$selected = "selected";
					?>
				<option value="<?=$class?>" <?=$selected?>><?=$class?></option>
				<?php } ?>
			</select></td>
					<td width="6%" class="noSection"><label style="margin-left: 20px;">Section</label></td>
					<td width="35%" class="noSection"><select name="childSection"
						id="childSection">
							<option value="">Select Section</option>
					</select></td>
					<td width="20%"><input type="submit" name="go" id="go" value="Go" /></td>
				
				
				<tr>
			
			</table>
			<!--<table id="pagingTable">
    <td>TOPIC RECOMMENDED FOR REMEDIATION</td>
</table>-->
		</form>

<?php
if (count ( $topicRemediation ) != 0) {
	echo "<table id='pagingTable'>
	<tr><td>TOPICS RECOMMENDED FOR REMEDIATION</td></tr>
</table>";
	foreach ( $topicRemediation as $classSec => $remadiationArray ) {
		$classSecArr = explode ( "|", $classSec );
		$class = $classSecArr [0];
		$section = $classSecArr [1];
		$topicNeedAttention = array ();
		$lowLevelArray = array ();
		$clusterFailArray = array ();
		$ttCodeArray = array ();
		foreach ( $remadiationArray as $ttCode => $details ) {
			$lowLevelArray [$ttCode] = $details ['lowLevel'];
			$clusterFailArray [$ttCode] = $details ['clusterFailed'];
		}
		array_multisort ( $lowLevelArray, SORT_DESC, $clusterFailArray, SORT_DESC, $remadiationArray );
		foreach ( $remadiationArray as $ttCode => $detail ) {
			if ($detail ['lowLevel'] == 0 && $detail ['clusterFailed'] == 0)
				continue;
			else {
				$topicNeedAttention [] = $ttnameArray [$ttCode];
				$ttCodeArray [] = $ttCode;
			}
		}
		?>
	<!--Live symbol-->
	
	<?php if($classStatus[$class.$section]==1) { ?>
	<div id="tabcls1" class="classTab">
			<a
				href="studentNeedAttention.php?cls=<?=$class?>&section=<?=$section?>"
				title="Live session details">
				<div id="circle">
					<div id="triangle" style=""></div>
				</div>
			</a>
		</div>
		<div class="classTabTriangle" id="trianglecls1" style=""></div>
	<?php } ?>	
	
	<!--Live symbol end-->

		<div id="flip">
			<div id="classTriangle" style=""></div>
			<table id="pagingTable">
				<td><a href="myClasses.php?cls=<?=$class?>&section=<?=$section?>">CLASS
			<?=$class.$section?></a></td>
			<?php
		
if ($class > 3) {
			?>
				<td align="right"><a target="_blank"
					href="classLeaderBoard.php?class=<?=$class?>&section=<?=$section?>"
					style="text-decoration: none;"
					onClick="setTimeout(function(){tryingToUnloadPage=false},500);"><div
							id="classLeaderBoardIcon"></div></a></td>
				<td id='topicActivated' width="200px;"><a target="_blank"
					href="classLeaderBoard.php?class=<?=$class?>&section=<?=$section?>"
					style="text-decoration: none;"
					onClick="setTimeout(function(){tryingToUnloadPage=false},500);">Class
						Leaderboard</a></td>
			<?php } ?>
		</table>
			<div id="line" style="margin-top: 0px;"></div>
		</div>
	<?php if($ttCodeArray[0]) { ?>
	<div id="flipContent" style="">
		
		<?php
			for($g = 0; $g < 3; $g ++) {
				if ($ttCodeArray [$g]) {
					?>
		<div id="topicBox">
				<div id="topicBoxDesc">
					<div id="topicBoxDescTopic">
						<u> <b><a
								href="topicProgress.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCodeArray[$g]?>">
					<?=$topicNeedAttention[$g]?>
					</a> </b></u>
					</div>
				<?php $activationDate	=	getTopicDetails($schoolCode,$ttCodeArray[$g],$class,$section);?>
				<div id="topicBoxDescInfo1"> Activated on <?=setDateFormate($activationDate)?> </div>
				<?php $activeSince	=	getDaysTillActivated($activationDate); ?>
				<div id="topicBoxDescInfo2">
						<span
							<?php if($activeSince>30) echo "style='color:red;cursor:pointer;' title='It is not advisable to have a topic active for more than 30 days.'"; ?>>Active since <?=$activeSince; if($activeSince==1 || $activeSince==0) echo " Day"; else echo " Days"?>  <?php if($activeSince>30){ ?><sup>
								?</sup><?php } ?></span>
					</div>
				</div>
			<?php $userIDClassArray	=	$userArrayClass[$class.$section]; $topicProgress	=	getTeacherTopicProgress($ttCodeArray[$g],$userIDClassArray,$class);?>
			<div id="outerCircle" class="outerCircle"
					title="Average topic progress of class">
					<div id="percentCircle"
						class="progressCircle forHighestOnly circleColor<?=round($topicProgress/10)?>"><?=round($topicProgress,1);?>%</div>
				</div>
			</div>
		<?php }	} ?>
	</div>
	<?php } ?>
	<?php } ?>
	<?php

} else {
	
	if ($live == 1)
		echo "<table id='pagingTable'>
	    <td>There is no live class going on currently. </td>
	</table>";
	else
		echo "<table id='pagingTable'>
    <td>NO TOPICS FOR REMEDIATION</td>
</table>";
}

?>
</div>
    <?php if($teacherForumPrompt > 0) { ?>      
	<!-- added for first time login promt -->        
	<div style="display: none">
                <div id="iLoginPrompt" class="pop-up-display-content">
			<div id="login_prompt">

				<center>
                                        <?php if($teacherForumPrompt=="2") { ?>                        
					<h2><?=$header_text?></h2>
					
					<a href="/mindspark/teacherInterface/teacherforum/" class="img-class" style='text-decoration: none' onclick="trackTeacherForum();">
						<img style=" padding-left: 9px; border: 0px;"	src="<?=$image_path?>"/>
					</a> 
                                    	<br><br>
					<a	href="/mindspark/teacherInterface/teacherforum/" style='text-decoration: none' onclick="trackTeacherForum();"><span	class="forumbutton"><?=$button_text?></span></a>
					<a	href="javascript:void(0);" onclick="$('#iLoginPrompt').popUpWindow({action: 'close'});"  style='text-decoration: none'><span	class="forumbutton">Skip</span></a>
                                        <?php } else if($teacherForumPrompt=="1") {  $imageName=CLOUDFRONTURL.'teacherForum/countDownImages/countdown_'.date("dmY").".png"; ?>
                                                <br/>
                                                <img style=" padding-left: 9px; border: 0px;"	src="<?=$imageName?>"/>
                                                <br/><br/>
                                                <a	href="javascript:void(0);" onclick="$('#iLoginPrompt').popUpWindow({action: 'close'});"  style='text-decoration: none'><span	class="forumbutton">Close</span></a>
                                        <?php } ?>
			        </center>
			</div>
	    </div>
	</div>        
    <?php }  ?>      
<?php include("footer.php")?>
<?php

function getTTsActivated1($cls, $schoolCode, $section, $limit = 0, $activationPeriod = 0) {
	$ttAttemptedArray = array ();
	$query = "SELECT A.teacherTopicCode,teacherTopicDesc FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section='$section' AND A.teacherTopicCode=B.teacherTopicCode AND ISNULL(deactivationDate)";
	if ($activationPeriod != 0) {
		$lastDate = date ( 'Y-m-d', strtotime ( "-20 days" ) );
		$query .= " AND A.activationDate<'$lastDate'";
	}
	$query .= " ORDER by A.activationDate desc";
	if ($limit != 0)
		$query .= " LIMIT $limit";
	$result = mysql_query ( $query ) or die ( mysql_error () );
	while ( $line = mysql_fetch_array ( $result ) ) {
		$ttAttemptedArray [$line [0]] = $line [1];
	}
	return $ttAttemptedArray;
}
function getStudentDetails1($cls, $schoolCode, $section) {
	$category = $_SESSION ['admin'];
	$userArray = array ();
	$query = "SELECT userID
              FROM   adepts_userDetails
              WHERE  category='STUDENT' AND endDate>=curdate() AND enabled=1  AND schoolCode =$schoolCode AND childClass='$cls' AND subjects like '%" . SUBJECTNO . "%'";
	if (strcasecmp ( $category, "School Admin" ) == 0 || strcasecmp ( $category, "TEACHER" ) == 0)
		$query .= " AND subcategory='School'";
	if (strcasecmp ( $category, "Home Center Admin" ) == 0)
		$query .= " AND subcategory='Home Center'";
	if ($section != "")
		$query .= " AND childSection ='$section'";
	$r = mysql_query ( $query ) or die ( $query . "<br/>" . mysql_error () );
	while ( $l = mysql_fetch_array ( $r ) ) {
		$userArray [] = $l [0];
	}
	return $userArray;
}
function getFailedClusters($ttAttemptArray) {
	$ttAttemptArray = array_values ( $ttAttemptArray );
	$ttAttemptID = implode ( ",", $ttAttemptArray );
	
	// Get the failed clusters in the last completed attempt, if any, or the current attempt
	$failedClusterArray = array ();
	$query = "SELECT ttAttemptID, result, failedClusters FROM " . TBL_TOPIC_STATUS . " WHERE ttAttemptID in ($ttAttemptID) ORDER BY ttAttemptID DESC";
	$result = mysql_query ( $query );
	$noOfAttempts = mysql_num_rows ( $result );
	while ( $line = mysql_fetch_array ( $result ) ) {
		if ($line ["failedClusters"] != '')
			$failedClusterArray [] = $line ["failedClusters"];
	}
	$failedCluster = implode ( ",", $failedClusterArray );
	$failedClusterArray = explode ( ",", $failedCluster );
	return $failedClusterArray;
}
function getLiveClasses($schoolCode, $classSectionArr = 0) {
	$arrayLoggedIn = array ();
	
	$sq = "SELECT b.userID,childClass,childSection,time_to_sec(timediff(now(),starttime))/60 minlogged,schoolCode FROM adepts_sessionStatus a,adepts_userDetails b 
			 WHERE isnull(endtime) AND logout_flag=0 AND a.userid=b.userid AND category='STUDENT' AND startTime_int=" . date ( "Ymd" ) . " 
			 HAVING minlogged<45";
	$rs = mysql_query ( $sq );
	while ( $rw = mysql_fetch_array ( $rs ) ) {
		if ($rw [4] == $schoolCode) {
			if ($classSectionArr == 0)
				$arrayLoggedIn [$rw [1]] [$rw [2]] ++;
			else if (in_array ( $rw [1] . $rw [2], $classSectionArr ))
				$arrayLoggedIn [$rw [1]] [$rw [2]] ++;
		}
	}
	return $arrayLoggedIn;
}
function getAnnouncements1($currentDate, $schoolCode, $userID, $userCategory) {
	$userName = $_SESSION ['username'];
	
	$currentKudosCount = newKudosCounter ( $userName );
	if ($currentKudosCount > 0) {
		$kudosNotification = 1;
	}
	
	$announcementsArray = array ();
	
	// Added for temporary announcement of parent email notification feature
	if (! ($_SESSION ['isOffline'] === true && ($_SESSION ['offlineStatus'] == 1 || $_SESSION ['offlineStatus'] == 2))) {
		$checkValidSchool = "SELECT schoolCode FROM parentEmailSchools WHERE schoolCode = " . mysql_real_escape_string ( $schoolCode );
		$execValidSchool = mysql_query ( $checkValidSchool );
		if (mysql_num_rows ( $execValidSchool ) == 0) {
			$notification_to_show = '<b>New Parent Portal Feature</b><br /><br />';
			$notification_to_show .= 'Teachers can now directly mail parents if parent email ids are mapped to the student\'s account.<br />';
			$notification_to_show .= '<a href="parentEmailAnnouncement.php">Click</a> to know more or disallow this.<br/><br/>';
			$announcementsArray [0] = array (
					"contentId" => "",
					"link" => "",
					"title" => $notification_to_show 
			);
		}
	}
	$notification_to_show = "";
	if ($kudosNotification == 1) {
		$notification_to_show .= '<div style="border-style:none !important; border:none; cursor:pointer;" onClick="resetKudosCounter();"><table><tr><td><a href="kudosHomeTeacherInterface.php?wall=my" ><img style="border-style:none !important; border:none;" src="../userInterface/assets/kudosNew.png" width=43px height=43px /></a></td><td><h2>You have received ' . $currentKudosCount . ' new Kudos!</h2></td></tr></table></div>';
	} 
// 	else {
// 		$notification_to_show .= '<table><tr><td><a href="kudosHomeTeacherInterface.php"><img  style="border-style:none !important; border:none;"  src="../userInterface/assets/kudosNew.png" width=43px height=43px /></a></td><td><h2>Send your students a kudos!</h2></td></tr></table>';
// 	}
	$announcementsArray [1] = array (
			"contentId" => "",
			"link" => "",
			"title" => $notification_to_show 
	);
	
	$sq = "SELECT id,contentId,title,category,schoolCode,class,status FROM adepts_teacherAnnouncements WHERE status='Approved' AND '$currentDate' BETWEEN fromDate AND tillDate";
	$rs = mysql_query ( $sq );
	while ( $rw = mysql_fetch_assoc ( $rs ) ) {
		$schoolcodeAnnounce = $rw ['schoolCode'];
		$id = $rw ['id'];
		$class = $rw ['class'];
		
		// check class belong to teacher or not
		if ($class == 'All' || $userCategory == 'Admin')
			$classValidity = 'valid';
		else
			$classValidity = checkTeacherClass ( $class, $userID );
			
			// check for schoolcode
		if ($schoolcodeAnnounce == 'All')
			$schoolcodeValidity = 'valid';
		else
			$schoolcodeValidity = checkTeacherSchoolCode ( $schoolcodeAnnounce, $schoolCode );
		
		if ($classValidity == '' || $schoolcodeValidity == '')
			continue;
		
		if ($rw ['category'] != 'Other') {
			if ($rw ['category'] == 'Topic') {
				$announcementsArray [$id] ['link'] = "announcements.php?id=" . $rw ['id'];
			} 

			else if ($rw ['category'] == 'Remedial') {
				$announcementsArray [$id] ['link'] = "../userInterface/remedialItem.php?qcode=" . $rw ['contentId'];
			} 

			else if ($rw ['category'] == 'Cluster') {
				$ttCode = getTTCode ( $rw ['contentId'] );
				$announcementsArray [$id] ['link'] = "sampleQuestions.php?ttCode=$ttCode&learningunit=" . $rw ['contentId'];
			} 

			else if ($rw ['category'] == 'Timed test') {
				$announcementsArray [$id] ['link'] = "../userInterface/timedTest.php?timedTest=" . $rw ['contentId'] . "&tmpMode=sample";
			} 

			else if ($rw ['category'] == 'Games') {
				$announcementsArray [$id] ['link'] = "../userInterface/enrichmentModule.php?gameID=" . $rw ['contentId'];
			}
			
			$announcementsArray [$id] ['title'] = $rw ['title'];
			$announcementsArray [$id] ['contentId'] = $rw ['contentId'];
			// display title on the page
		} else if ($rw ['category'] == 'Other' and $rw ['status'] == 'Approved') {
			$announcementsArray [$id] ['title'] = $rw ['title'];
			$announcementsArray [$id] ['class'] = $rw ['class'];
			if ($rw ['id'] == 43)
				$announcementsArray [$id] ['link'] = "feedbackform_content.php";
			else
				$announcementsArray [$id] ['link'] = "announcements.php?id=" . $rw ['id'];
		}
	}
	return $announcementsArray;
}
function checkTeacherSchoolCode($schoolcodeAnnounce, $schoolCode) {
	$valid = '';
	$schoolcodeAnnounce = ',' . $schoolcodeAnnounce . ',';
	$chkschool = stripos ( $schoolcodeAnnounce, ',' . $schoolCode . ',' );
	
	if ($chkschool !== false) {
		$valid = "valid";
	}
	return $valid;
}
function checkTeacherClass($class, $userID) {
	$validClass = '';
	$sq = "SELECT DISTINCT class FROM  adepts_teacherClassMapping WHERE userID='$userID' AND subjectNo=" . SUBJECTNO;
	$rs = mysql_query ( $sq );
	while ( $rw = mysql_fetch_assoc ( $rs ) ) {
		$classNew = $rw ['class'];
		if (stripos ( ',' . $class . ',', ',' . $classNew . ',' ) !== false) {
			$validClass = "valid";
			break;
		}
	}
	return $validClass;
}
function getTopicDetails($schoolCode, $ttCode, $class, $section) {
	$sq = "SELECT activationDate FROM adepts_teacherTopicActivation WHERE schoolcode='$schoolCode' AND class='$class' AND section='$section' AND teacherTopicCode='$ttCode'
			 ORDER BY lastModified DESC LIMIT 1";
	$rs = mysql_query ( $sq );
	$rw = mysql_fetch_array ( $rs );
	return $rw [0];
}
/*
 * function getTeacherTopicProgress($ttCode,$userIDArray)
 * {
 * $userIDstr = implode(",",$userIDArray);
 *
 * $sq = "SELECT userID,MAX(progress) FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode'
 * AND userID IN ($userIDstr) GROUP BY userID";
 * $rs = mysql_query($sq);
 * $userttProgress = array();
 * while($rw=mysql_fetch_array($rs))
 * {
 * $userttProgress[] = $rw[1];
 * }
 * return round(array_sum($userttProgress)/count($userIDArray),2);
 * }
 */
function getTeacherTopicProgress($ttCode, $userIDArray, $cls) {
	$userIDstr = implode ( ",", $userIDArray );
	$q = "SELECT distinct flow FROM " . TBL_TOPIC_STATUS . " WHERE  userID in (" . $userIDstr . ") AND teacherTopicCode='" . $ttCode . "'";
	$r = mysql_query ( $q );
	while ( $l = mysql_fetch_array ( $r ) ) {
		$flowN = $l [0];
		$flowStr = str_replace ( " ", "_", $flowN );
		${"objTopicProgress" . $flowStr} = new topicProgress ( $ttCode, $cls, $flowN, SUBJECTNO );
	}
	
	$sq = "SELECT userID,MAX(progress),flow FROM " . TBL_TOPIC_STATUS . " WHERE teacherTopicCode='$ttCode'
			 AND userID IN ($userIDstr) GROUP BY userID";
	$rs = mysql_query ( $sq );
	$userttProgress = array ();
	
	while ( $rw = mysql_fetch_array ( $rs ) ) {
		$sqProgress = "SELECT srno FROM " . TBL_CURRENT_STATUS . " WHERE progressUpdate=0 AND teacherTopicCode='$ttCode' AND userID=" . $rw [0];
		$rsProgress = mysql_query ( $sqProgress );
		if ($rwProgress = mysql_fetch_assoc($rsProgress))
			$userttProgress[]	=	$rw[1];
		else
		{
			$flowK	=	$rw[2];
			$flowK	=	str_replace(" ","_",$flowK);
			$userttProgress[] = ${"objTopicProgress".$flowK}->getProgressInTT($rw[0]);
		}
	}
	return round(array_sum($userttProgress)/count($userIDArray),2);
}
include_once ("../userInterface/commonAQAD.php");
?>
