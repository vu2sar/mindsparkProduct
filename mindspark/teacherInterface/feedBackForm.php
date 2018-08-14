<?php
	@include("../userInterface/check1.php");
	include("../userInterface/classes/clsUser.php");

	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR);

	include("classes/testTeacherIDs.php");
	include("../userInterface/constants.php");
	if(SERVER_TYPE!="LOCAL")
	{
		include('teacherforumsupport/config.php');
		include('teacherforumsupport/ajax_forum.php');
	}
	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);

	$todaysDate = date("d");
	if(!isset($_SESSION['userID']))
	{
		header("Location: logout.php");
		exit;
	}
	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}

	$userID   = $_SESSION['userID'];
	$buddy_id = $_SESSION['buddy'];
	$childClass	=	$_SESSION['childClass'];
	$feedbackType = "";

	$feedbackset = $_REQUEST["setNo"];

	$user = new User($userID);
	$feedbackFlag=$user->checkForFeedback();
	$feedbackFlag = explode("~",$feedbackFlag);
	if ($feedbackFlag[0] == 0) {
		echo '<script>window.location.href="home.php";</script>';
		exit;
	}
	else {
		$feedbackset=$feedbackFlag[1];
	}
	$query  = "SELECT questions,formName FROM adepts_userFeedbackSet WHERE setno=$feedbackset";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$qids = $line["questions"];
	$formName = $line["formName"];
	$questionArr = explode(",", $qids);
	foreach ($questionArr as $value) 
	{
		$value1 = explode("~", $value);
		$value = $value1[0];
		$query = "SELECT question, questionType, options, helpText FROM adepts_userFeedbackQuestions WHERE qid=$value";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		$question_details_array[] = array("question"=>$line["question"], "quesType"=>$line["questionType"],"options"=>json_decode(stripslashes($line["options"])), "helpText"=>$line["helpText"]);
	}			
	$arrQues = array();

?>
<!DOCTYPE HTML> 
<html>
<head>
<?php 
if(intval($_SESSION['browserVersion'])==9)
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<?php 
}
else
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?PHP 
}

?>
<meta http-equiv="Content-Type" content="text/html;">

<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Feedback Form</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/feedBackForm.css?v=3" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../userInterface/css/commonCssForNewFeedbackFormat.css"/>  <!-- Css for dynamic feedback form -->
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type='text/javascript' src='../userInterface/libs/feedbackCommonFunc.js'></script>  <!-- Common functions that are used for feedback for both student as well as students -->

<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
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
		<div id="feedbackFormMainDiv">
			<h3 id='feedBackHeader'><?php echo stripslashes($formName); ?> </h3>
			<div id="feedbackFormMain">
				<div id="feedbackInfo" class="forLowerOnly hidden">Please take out a few minutes to answer the questions below:
</div><br/><br/>
				<form name="feedbackform" id="frmFeedback" method="POST" action="saveFeedBack.php">
				</form>
		    </div>
		</div>
	</div>
<?php 
	include("footer.php");
	echo "<script type='text/javascript'>";
	echo "showFeedbackForm('".$formName."',".json_encode($question_details_array).",'".$qids."',".$feedbackset.");";
	echo "</script>";
?>