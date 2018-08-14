<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
include("header.php");
include("../slave_connectivity.php");
/*$link = mysql_connect("ec2-46-137-198-206.ap-southeast-1.compute.amazonaws.com","ms_analysis","WNC001") or die("Could not connect : " . mysql_error());
mysql_select_db("educatio_adepts",$link) or die("Could not select database");*/
include("../userInterface/functions/functions.php");
include("../userInterface/classes/clsTeacherTopic.php");
//Fetch Data From Session
//*********************//
$userID = $_SESSION['userID'];
$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
$user   = new User($userID);
if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0)	{
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}
$ttCode = $_GET['ttCode'];
$class	=	$_GET['cls'];
$section	=	$_GET['section'];
if(isset($_GET["mode"]))
	$mode	=	$_GET["mode"];

//*********************//

$userRequireAttentionArray = array();
$users = getStudentDetails($class, $schoolCode, $section);
$userIDs = array_keys($users);
$userIn2LowLevel	=	array();
$userIn1LowLevel	=	array();

$sqFlow	=	"SELECT userID,flow FROM ".TBL_TOPIC_STATUS." WHERE userID IN (".implode(",",$userIDs).") AND teacherTopicCode='$ttCode'";
$rsFlow	=	mysql_query($sqFlow);
while($rwFlow=mysql_fetch_array($rsFlow))
{
	$flowArray[$rwFlow[0]]	=	$rwFlow[1];
	$flowUnique[]	=	$rwFlow[1];
}
$flowUnique	=	array_unique($flowUnique);

foreach($flowUnique as $flow)
{
	if($flow=="")
		$flow="MS";
	$flow	= str_replace(" ","_",$flow);
	${"ttObj".$flow}	=	new teacherTopic($ttCode,$class,$flow);
}
foreach($userIDs as $userID)
{
	if(!$flowArray[$userID])
		continue;
	$ttObj="";
	$levelArray	=	array();
	$sq	=	"SELECT clusterCode FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode' LIMIT 1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$cluster	=	$rw[0];
	if($flowArray[$userID]=="")
		$flow	=	"MS";
	else
		$flow	=	$flowArray[$userID];
	$flow	= str_replace(" ","_",$flow);
	$levelArray = ${"ttObj".$flow}->getClusterLevel($cluster);
	if($levelArray[0]!='' && $levelArray[0]<=$class)
	{
		if(!in_array($class,$levelArray))
		{
			if(!in_array($class-1,$levelArray))
				$userIn2LowLevel[]	=	$userID;
			else
				$userIn1LowLevel[]	=	$userID;
		}
	}
}
$userRequireAttentionArray	=	array_merge($userIn2LowLevel,$userIn1LowLevel);
$query = "SELECT teacherTopicDesc, mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
$result = mysql_query($query);
$line = mysql_fetch_array($result);
$teacherTopicDesc = $line[0];
?>

<title>Topic Remediation Section</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicRemediationStudent.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
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
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
			<div id="headerBar">
				<div id="pageName">
					<div class="arrow-black"></div>
					<div id="pageText">TOPIC REMEDIATION</div>
				</div>
				<div id="classTopic">
					<div class="arrow-black1"></div>
					<div id="classText">Class <?=$class?><?=$section?> : <span style="color:#E75903;"><?=$teacherTopicDesc?></span></div>
				</div>
			</div>
			
			<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="pointer"><a href="topicRemediationSection.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=secRemediation"><div class="smallCircle"></div></a><label class="pointer" value="secRemediation"><a href="topicRemediationSection.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=secRemediation">SECTION REMEDIATION</a></label></a></td>
		        <td width="33%" id="studentRemediation" class="pointer"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention"><div class="smallCircle red"></div></a><label class="textRed pointer" value="stNeedAttention"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention">STUDENT REMEDIATION</a></label></td>
		        <td width="43%" id="classRemediation" class="pointer"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation"><div class="smallCircle"></div></a> <label class="pointer" value="clsRemediation"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation">CLASS REMEDIATION</a></label></td>
			</table>
			
			<table id="pagingTable">
		        <td width="35%">CLASS <?=$class?><?=$section?></td>
				<td>
					<div id="download" class="textRed">The following students are either not working or are stuck on a problem and need help</div>
				</td>
			</table>

			<?php
			if(count($userRequireAttentionArray)==0)
			{
				echo '<div align="center" id="noRecords" style="color:red">*Great job! The class is doing just fine.</div></div></div>';
				include("footer.php");
				exit;
			}
			?>
			<table cellpadding="5" align="center" class="gridtable" border="1" width="100%">
				<thead>
			        <tr>
						<th width="8%">Sr No.</td>
			            <th scope="col" colspan="2" align="left">Student Name</th>
			            <!--<th scope="col" width="15%">Comments</th>
			            <th scope="col" width="15%">Assign</th>-->
			        </tr>
			    </thead>
			    <tbody>
			<?php
					$srNo = 0;
					foreach($userRequireAttentionArray as $userID)
					{
						$srNo++;
						$name = $users[$userID][0];
			?>
			        <tr>
			            <td width="8%"><?=$srNo?></td>
			            <td align="left"><?=$name?></td>
			            <!--<td><textarea name="comments" cols="20" rows="3"></textarea></td>
			            <td><input name="addressed" type="checkbox" id="addressed_<?=$userID?>" value="<?=$userID?>" class="checkBox" /></td>-->
			        </tr>
			<?php
						if($srNo == 5)
							break;
					}
			?>
			    </tbody>
			</table>
	</div>
	</div>
<?php include("footer.php") ?>