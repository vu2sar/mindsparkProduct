<?php
	include("header.php");
	include("../slave_connectivity.php");
	include("../userInterface/functions/orig2htm.php");
   	include("../userInterface/classes/clsNCERTQuestion.php");

    $userID = $_SESSION['userID'];

    if(!isset($_REQUEST['exerciseCode']) || !isset($_SESSION['userID']))
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
	$exerciseCode = $_GET['exerciseCode'];
	$exerciseName = getExerciseDetail($exerciseCode);
	$allQuestionsArray = array();
	$query = "SELECT qcode, groupNo FROM adepts_ncertQuestions, adepts_groupInstruction WHERE adepts_ncertQuestions.exerciseCode='$exerciseCode' AND  adepts_ncertQuestions.groupID = adepts_groupInstruction.groupID AND status=3 ORDER BY groupNo, FIELD(subQuestionNo,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t', '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19', 'ii','iii','iv','v','vi','vii','viii','ix','x','xi','xii','xiii','xiv','xv')";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result))
	{
		$groupNo = $row["groupNo"];
		$qcode = $row["qcode"];
		 if(!isset($allQuestionsArray[$groupNo]))
				$allQuestionsArray[$groupNo]=array();
			array_push($allQuestionsArray[$groupNo],$qcode);
	}
?>

<title>NCERT Sample Questions</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/ncertSampleQuestions.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#questionContainer").css("min-height",(containerHeight-160)+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
	function showAllQues()
	{
		//document.getElementById('cls').value='';
		document.getElementById('showAll').value = "1";
		document.getElementById('frmSampleQues').submit();
	}
	$(document).ready(function() {
	    $("#download").live("click",function(){
			if($("#forceFlow").val()=="")
			{
				alert("Sorry not able to start the session.");
				return false;
			}
			$("#mode").val("ttSelection");
			$("#userType").val("teacherAsStudent");
			$("#mindsparkTeacherLogin").attr("action", "controller.php");
			$("#mindsparkTeacherLogin").submit();	
		});
});
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
					<div id="pageText"><?=$exerciseName?></div>
				</div>
			</div>
			
			<div id="questionContainer">
				<form id="frmSampleQues" method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <div id="leftBar">
		<?php include("researchModuleLeftLinks.php"); ?>
    </div>
    <div id="rightBody">
	<input type="hidden" id="ttCode" name="ttCode" value="<?=$ttCode?>">
	<input type="hidden" id="cls" name="cls" value="<?=$cls?>">
	<input type="hidden" id="flow" name="flow" value="<?=$flow?>">
	<input type="hidden" id="showAll" name="showAll" value="<?=$showAll?>">
<?php
	
	$counter = 0;
	foreach($allQuestionsArray as $groupNo=>$qcodeArray)
	{
		$oneQuestionGroup = false;
		$sql = "SELECT groupText,groupID FROM adepts_groupInstruction WHERE groupNo='$groupNo' AND clusterCode='$exerciseCode'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$groupText = $row[0];
		$groupID = $row[1];
		
		$groupText = orig_to_html($groupText,"images");;
		
		if(count($qcodeArray) == 1)
			$oneQuestionGroup = true;
?>
     <div class="question">
     		<?php if(!$oneQuestionGroup) { ?>
	        <table width='100%' border=0 cellspacing=0>
	            <tr>
                	<td align='center' valign='top'  width='5%'><div class="qno"><?=$groupNo?></div></td>
	                <td align='left'><?=$groupText?><br/></td>
	            </tr>
            </table>
     		<?php } ?>
            <div class="groupQues">
<?php
		foreach($qcodeArray as $qcode)
		{
			$question     = new ncertQuestion($qcode);
			if($question->isDynamic())
			{
				$question->generateQuestion();
			}
			$questionType = $question->quesType;
			if(!$oneQuestionGroup)
				$showSubQuestionNo = "(".$question->subQuestionNo.")";
			else
				$showSubQuestionNo = $question->subQuestionNo;
?>
		<div class="singleQuestion column1">
        <table width='100%' border=0 cellspacing=0>
	            <tr>
	                <td align='center' valign='top'  width='5%'><div class="qno <?php if(!$oneQuestionGroup) echo " questionQ1"; ?>"><?=$showSubQuestionNo?></div>
	                </td>
	                <td align='left'><?=$question->getQuestion()?><br/></td>
                </tr>
                <?php
                if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
                ?>
                <tr  bgcolor="">
                    <td>&nbsp;</td>
                    <td>
                		<table width="100%" border="0" cellspacing="2" cellpadding="3">
                
                <?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
                        <tr valign="top">
                            <td width="5%"  class="orangeBorder" nowrap align="center" ><b>A</b></td>
                            <td width="45%" class="orangeBorder"><?php echo $question->getOptionA();?></td>
                            <td width="5%"  class="orangeBorder" nowrap align="center" ><b>B</b></td>
                            <td width="45%" class="orangeBorder"><?php echo $question->getOptionB();?></td>
                        </tr>
                <?php    }    ?>
                <?php    if($questionType=='MCQ-4')    {    ?>
                        <tr valign="top">
                            <td width="5%"  class="orangeBorder" align="center"><b>C</b></td>
                            <td width="45%" class="orangeBorder"><?php echo $question->getOptionC();?></td>
                            <td width="5%"  class="orangeBorder" align="center"><b>D</b></td>
                            <td width="45%" class="orangeBorder"><?php echo $question->getOptionD();?></td>
                        </tr>
                <?php    }    ?>
                <?php    if($questionType=='MCQ-3')    {    ?>
                        <tr valign="top">
                            <td width="3%"  class="orangeBorder" nowrap align="center"><b>A</b></td>
                            <td width="30%" class="orangeBorder"><?php echo $question->getOptionA();?></td>
                            <td width="3%"  class="orangeBorder" nowrap align="center"><b>B</b></td>
                            <td width="30%" class="orangeBorder"><?php echo $question->getOptionB();?></td>
                            <td width="3%"  class="orangeBorder" nowrap align="center"><b>C</b></td>
                            <td width="30%" class="orangeBorder"><?php echo $question->getOptionC();?></td>
                        </tr>
                <?php    }    ?>
               			</table>
                	</td>
                </tr>
                <?php    }    ?>
		</table>
        </div>
        <div style="clear:both;"></div>
<?php
		}
?>
	</div>
	</div>
<?php
	}

function getExerciseDetail($exerciseCode)
{
	$sql = "SELECT chapterNo, chapterName, exerciseNo FROM adepts_ncertExerciseMaster WHERE exerciseCode='$exerciseCode'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$chapterNo = $row['chapterNo'];
	$chapterName = $row['chapterName'];
	$exerciseNo = $row['exerciseNo'];
	$exerciseDetail = "$chapterName - Exercise $chapterNo.$exerciseNo";
	return($exerciseDetail);
}

?>
			
			
		</div>
	</div>

<?php include("footer.php") ?>