<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include("header.php");
	include("../slave_connectivity.php");
	include("../userInterface/constants.php");
	
	$userID     = $_SESSION['userID'];
	$username   = $_SESSION['username'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";

//for view question
$arrayQuestions	=	array();

$arrayQuestions	=	getTeacherQuestion($arrayQuestions,$userID, $username);
?>

<title>View Questions</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/viewQues.css" rel="stylesheet" type="text/css">
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
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<div style="margin-right: 35%;">View Questions</div>
				
				
			</div>
			<div style="margin-top: 3%;margin-bottom: 1%;"> </div>
		
		
			<table cellpadding="5" cellspacing="5" align="center" width="60%" bgcolor="White" style="font-size: 11px">
	<tr >
		<th width="5%">S.No.</th>
		<th width="10%">Code</th>
		<th width="60%">Question</th>
		<th width="15%">Topic</th>
		<th width="10%">Class</th>
	</tr>
<?php $i=1; foreach ($arrayQuestions as $code=>$detail) { ?>
	<tr >
		<td align="center"><?=$i?></td>
		<td align="center"><?=$code?></td>
		<td align="left"><?=$detail['question']?></td>
		<td align="center"><?=$detail['topic']?></td>
		<td align="center"><?=$detail['class']?></td>
	</tr>
<?php $i++; } ?>
</table>
		<?php	
		?>
				
		</div>
		
		
		
		
	</div>

<?php

function getTeacherQuestion($arrayQuestions,$userID, $username)
{
	$sq	=	"SELECT D.qcode,D.question,D.clusterCode,D.class,A.topic
			 FROM adepts_teacherQuestion D , adepts_subTopicMaster C , adepts_clusterMaster B , adepts_topicMaster A
			 WHERE D.questionmaker='$username' AND B.clusterCode=D.clusterCode AND C.subTopicCode=B.subTopicCode AND A.topicCode=C.topicCode";
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_assoc($rs))
	{
		$qcode	=	$rw['qcode'];
		$arrayQuestions[$qcode]['question']	=	$rw['question'];
		$arrayQuestions[$qcode]['class']	=	$rw['class'];
		$arrayQuestions[$qcode]['topic']	=	$rw['topic'];
	}
	return $arrayQuestions;
	/*$sq	=	"SELECT qcode,question,clusterCode,class FROM adepts_teacherQuestion WHERE questionmaker='$userID'";
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_assoc($rs))
	{
		$qcode			=	$rw['qcode'];
		$clusterCode	=	$rw['clusterCode'];

		$sq1	=	"SELECT A.topic
					 FROM adepts_subtopicmaster C , adepts_clusterMaster B , adepts_topicMaster A
					 WHERE B.clusterCode='$clusterCode' AND C.subTopicCode=B.subTopicCode AND A.topicCode=c.topicCode";
		$rs1	=	mysql_query($sq1);
		$rw1	=	mysql_fetch_assoc($rs1);
		$arrayQuestions[$qcode]['question']	=	$rw['question'];
		$arrayQuestions[$qcode]['class']	=	$rw['class'];
		$arrayQuestions[$qcode]['topic']	=	$rw1['topic'];
	}*/
}
?>

<?php include("footer.php") ?>