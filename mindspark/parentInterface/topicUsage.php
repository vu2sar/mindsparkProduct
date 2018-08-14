<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	@include("header.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include_once("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/functions/functions.php");
?>

<title>Topic Progress</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicUsage.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
	}
</script>
</head>
<body class="translation" onLoad="load();" onResize="load()">
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
<?php include('referAFriendIcon.php') ?>
		<div id="trailContainer">			
			<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="pointer"><a href='topicUsage.php'><div class="smallCircle red"></div></a><label class="textRed pointer" value="secRemediation"><a href='topicUsage.php'>TOPIC PROGRESS</a></label></td></a>
				<td width="33%" id="sectionRemediation" class="pointer"><a href='usage.php'><div class="smallCircle"></div></a><label class="pointer" value="secRemediation"><a href='usage.php'>USAGE</a></label></td>
		</table>
			<table cellpadding="5" border="1" width="100%" align="center" class="gridtable">
	<thead>
        <tr>
        	<th scope="col" width="10%">Sr No</th>
            <th scope="col">Topic</th>
            <th scope="col" width="20%">Progress</th>
			<th scope="col">No. of Attempts</td>
            <th scope="col">No. of Ques Attempted</td>
            <th scope="col">% Correct</td>
            <th scope="col">Trail</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $i=0;
	$user   = new User($_SESSION['childID']);
	$schoolCode = $user->schoolCode;
	$schoolName = $user->schoolName;
	$class	=	$user->childClass;
	$section	=	$user->childSection;
	$studentID	=	$user->userID;
	$teacherTopicActivated	=	getTTsActivated($studentID);
	$userArray[]	=	$studentID;
/*	foreach ($teacherTopicActivated as $ttCode=>$ttName)
	{
		$progresstt	=	array();
		$progresstt	=	getStudentProgress($ttCode,$userArray,$class);
		$studentProgress[$ttCode]	=	$progresstt[$studentID];
	}*/
	$studentProgress	=	getTeacherTopicProgress($teacherTopicActivated,$studentID,$class);
    foreach ($teacherTopicActivated as $ttCode=>$ttName) {
		$quesAttemptedArray	=	getNoOfQuesAttemptedInTheTopic1($studentID, $ttCode, $class);
		$noOfAttempts = getNoOfAttemptsOnTheTopic($studentID, $ttCode);
    	$i++;
    	?>
        <tr>
        	<td><?=$i?></td>
            <td><?=$ttName?></td>
            <td><div style="text-align:left;width: 100px;height: 20px;border: 1px solid black;background-color: white;float:left;">
            <div style='width:<?php if($studentProgress[$ttCode]!="") echo $studentProgress[$ttCode]; else echo "0";?>px; height:20px; background-color:#32CD32; font-size:0.8em;'></div></div>
            <?php if($studentProgress[$ttCode]!="") echo $studentProgress[$ttCode]; else echo "0";?>%</td>
			<td><?=$noOfAttempts?></td>
            <td><?=$quesAttemptedArray["quesAttempted"]?></td>
            <td><?=$quesAttemptedArray["perCorrect"]?></td>
            <td><a class="buttonLink" href="studentTrail.php?topic_passed_id=<?=$ttCode?>&user_passed_id=<?=$studentID?>" target="_blank" style="text-decoration:underline;color:blue;">trail</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
			
		</div>
	</div>

<?php include("footer.php") ?>
<?php
function studentName($userID)
{
	$sq	=	"SELECT childName FROM adepts_userDetails WHERE userID='$userID'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function getTeacherTopicProgress($teacherTopicActivated,$userID,$class)
{
	$teacherTopicDetails	=	array();
	foreach($teacherTopicActivated as $ttCode=>$ttName)
	{
		$query  = "SELECT DISTINCT flow,MAX(progress) FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$flow   = $line[0];
		
		$sq	=	"SELECT * from ".TBL_CURRENT_STATUS." WHERE progressUpdate=0 AND teacherTopicCode='$ttCode' AND userID=$userID"; // * from was missing
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_assoc($rs))
			$progress =	$line[1];
		else
		{
			$objTopicProgress = new topicProgress($ttCode, $class, $flow, SUBJECTNO);
			$progress = $objTopicProgress->getProgressInTT($userID);
		}
		$teacherTopicDetails[$ttCode]	=	round($progress);
	}
	return $teacherTopicDetails;
}

function getTTsActivated($studentID)
{
    $ttAttemptedArray = array();
    $query = "SELECT distinct a.teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicStatus a, adepts_teacherTopicMaster b 
	          WHERE  a.teacherTopicCode=b.teacherTopicCode AND userID=$studentID";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result))
    {
		$ttAttemptedArray[$line[0]]	= $line[1];
    }
    return $ttAttemptedArray;
}

function getNoOfQuesAttemptedInTheTopic1($userID, $teacherTopicCode, $class)
{
    $quesAttemptArray = array();
    $q = "SELECT count(srno), sum(if(R=1,1,0)) FROM adepts_teacherTopicStatus a, adepts_teacherTopicClusterStatus b, adepts_teacherTopicQuesAttempt_class$class c
          WHERE  a.userID=b.userID AND b.userID=c.userID AND a.userID=$userID AND b.ttAttemptID=a.ttAttemptID AND b.clusterAttemptID=c.clusterAttemptID AND a.teacherTopicCode='$teacherTopicCode'";
	$r = mysql_query($q) or die(mysql_error());
	$l = mysql_fetch_array($r);
	$quesAttemptArray["quesAttempted"] = $l[0];
	if($l[0]>0)
	    $quesAttemptArray["perCorrect"] = round($l[1]/$l[0]*100,1);
	else
	    $quesAttemptArray["perCorrect"] = "";
    return $quesAttemptArray;
}
?>