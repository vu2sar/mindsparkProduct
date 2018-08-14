<?php include("header.php");
include("../slave_connectivity.php"); 
?>
<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
include("../userInterface/classes/clsTeacherTopic.php");
include("functions/functions.php");

//Fetch Data From Session
//*********************//
$userID = $_SESSION['userID'];
$schoolCode = $_SESSION['schoolCode'];
//$schoolCode	=	1752;
//$ttCode = $_GET['ttCode'];
$class	=	$_GET['cls'];
$section	=	$_GET['section'];
//*********************//



/*$link = mysql_connect("ec2-54-251-4-141.ap-southeast-1.compute.amazonaws.com","ms_analysis","ARE001") or die("Could not connect : " . mysql_error());
mysql_select_db("educatio_adepts",$link) or die("Could not select database");*/

$userRequireAttentionArray = array();
$users = getStudentDetails($class, $schoolCode, $section);
$userIDs = array_keys($users);
$userIn2LowLevel	=	array();
$userIn1LowLevel	=	array();

foreach($userIDs as $userID)
{
	$ttObj="";
	$levelArray	=	array();
	$sq	=	"SELECT clusterCode,teacherTopicCode FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND status=1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$cluster	=	$rw[0];
	$ttCode	=	$rw[1];
	$sqFlow	=	"SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode' LIMIT 1";
	$rsFlow	=	mysql_query($sqFlow);
	$rwFlow	=	mysql_fetch_array($rsFlow);
	$flow	= str_replace(" ","_",$rwFlow[0]);
	$ttObj = new teacherTopic($ttCode,$class,$flow);
	$levelArray = $ttObj->getClusterLevel($cluster);
	if($levelArray[0]!='')
	{
		if(!in_array($class,$levelArray))
		{
			if(in_array($class-2,$levelArray))
				$userIn2LowLevel[]	=	$userID;
			else if(in_array($class-1,$levelArray))
				$userIn1LowLevel[]	=	$userID;
		}
	}
}
$userRequireAttentionArray	=	array_merge($userIn2LowLevel,$userIn1LowLevel);
?>

<title>Student need attention</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentNeedAttention.css" rel="stylesheet" type="text/css">
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
		$("#container").css("height",containerHeight+"px");
		$("#trailContainer").css("height",containerHeight+"px");
		$("#classes").css("font-size","1.6em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
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
				<div id="tabcls1" class="classTab">
					<div id="circle"> 
						<div id="triangle" style="" > </div>
					</div>
				</div>
				<div class="classTabTriangle" id="trianglecls1" style=""> </div>
				<div id="pageName">
					<div id="pageText">This is a live session</div>
				</div>
				<div id="classTopic">
				</div>
			</div>
			
			
			<div id="dataContainer">
			
			<table align="center" class="gridtable"  border="0" align="center">
  <tr>
    <td>Class: <?=$class.$section?></td>
    <!--<td align="center">Topic: <?=getTopicName($ttCode)?></td>-->
   <td align="center"></td>
  </tr>
</table>

			
				<?php
					if(count($userRequireAttentionArray)==0)
					{
						echo '<div align="center" style="color:red">*Great job! The class is doing just fine.</div>';
						exit;
					}
					?>
					<table cellpadding="5" width="60%" align="center" class="gridtable">
						<thead>
							<tr>
								<th scope="col" colspan="2">Student</th>
								
								<th scope="col" width="15%">Addressed</th>
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
								<td><?=$name?></td>
								
								<td align="center"><input  name="addressed" type="checkbox" id="addressed_<?=$userID?>" value="<?=$userID?>" class="checkBox" /></td>
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
	</div>

<?php include("footer.php") ?>