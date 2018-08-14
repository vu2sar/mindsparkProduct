<?php
	set_time_limit(0); 
	include("header.php");	
	include("../userInterface/functions/orig2htm.php");
	include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/classes/clsDiagnosticTestQuestion.php");
	include("../userInterface/constants.php");
	include("../userInterface/functions/functions.php");
	include("../slave_connectivity.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	error_reporting(E_ERROR);

	$qcodeStr = isset($_POST['qcodeStr'])?$_POST['qcodeStr']:"";
	$ttCode = isset($_POST['ttCode'])?$_POST['ttCode']:"";
	$class = isset($_POST['class'])?$_POST['class']:"";
	$section = isset($_POST['section'])?$_POST['section']:"";
	$fileName = "CommonWrongAnswer_Class".$class.".doc";
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment; Filename=$fileName");
	
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	
	$userID      = $_SESSION['userID'];
	$school_code = $_SESSION['schoolCode'];
	$category    = $_SESSION['admin'];

	if (!(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0 || strcasecmp($category,"Home Center Admin")==0))
	{
		echo "You are not authorised to access this page!";
		exit;		
	}
	
	
	
	$topicName = getTeacherTopicDesc($ttCode);
	$schoolName = getSchoolName($school_code);	
	if($qcodeStr=="")
	{
		echo "No questions found!";
		exit;
	}
	$questionStr = "";		
	$qcodeArray = removeQuestionsWithAni($qcodeStr);
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1252">
</head>
<body>

<div id="pnlHeader">
	<div class="logo"></div>
	<div align="center">Common Wrong Answers</div>
	<br/>
	<div>
		<table border="0" width="98%" align="center">
			<tr>
				<td width="10%">Class: <?=$class.$section?></td>
				<td width="45%">School: <?=$schoolName?></td>
				<td width="45%">Topic: <?=$topicName?></td>
			</tr>
		</table>
	</div>
</div>

<?php 
	$qno = 1;	
	foreach($qcodeArray as $key => $qcodeData)
	{
		foreach($qcodeData as $qcode)
		{
			if($key == 'topic')
				$question = new Question($qcode);
			else
				$question = new diagnosticTestQuestion($qcode);
			$dynamic = 0;
	
			if($question->isDynamic())
			{
				$dynamic = 1;
				$question->generateQuestion();
			}
		
		    $question_type = $question->quesType;
		
		    $questionStr = "<div>";
		    $questionStr .= $qno.". ";
		    $questionStr .= $question->getQuestion();
		    $questionStr .= "</div>";
		
		    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
		    {
				$questionStr .= "<br/>";
		    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";
		    	$correctAns = $question->correctAnswer;
		
			    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
			        $questionStr .= "<tr>";
			            $questionStr .= "<td width='5%'><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()."</td>";
			            $questionStr .= "<td width='5%'><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()."</td>";
			        $questionStr .= "</tr>";
			    }
			    if($question_type=='MCQ-4')	{
			        $questionStr .= "<tr>";
			            $questionStr .= "<td width='5%'><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()."</td>";
			            $questionStr .= "<td width='5%'><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()."</td>";
			        $questionStr .= "</tr>";
			    }
			    if($question_type=='MCQ-3')	{
			        $questionStr .= "<tr>";
			            $questionStr .= "<td width='5%'><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()."</td>";
			            $questionStr .= "<td width='5%'><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()."</td>";
			            $questionStr .= "<td width='5%'><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()."</td>";
			        $questionStr .= "</tr>";
			    }
			    $questionStr .= "</table>";
		    }
			$qno++;		
?>
<div class='cwa_ques' style='width:90%; margin-left:25px'>
	<?php echo $questionStr; ?>
</div>
<hr/>
<?php 
	}
} ?>
</body>
</html>
<?php 
function getSchoolName($schoolCode)
{
	
	$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=$schoolCode";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	return $line[0];
}

function removeQuestionsWithAni($qcodeStr)
{
	$qcodeStr = stripslashes(html_entity_decode($qcodeStr));
	$oldQcodeArray = json_decode($qcodeStr);
	$qcodeArray = array();
	$qcodeArray['topic'] = 	$oldQcodeArray->topic;
	$qcodeArray['DTtest'] = $oldQcodeArray->DTtest;
	$topicQcodestr = implode(',', $qcodeArray['topic']);
	$DTtestQcodestr = implode(',', $qcodeArray['DTtest']); 	
	if($topicQcodestr!="")
	{
		$query = "SELECT qcode FROM adepts_questions WHERE qcode in ($topicQcodestr) AND (question LIKE '%.swf%' OR question LIKE '%.html%' ) ORDER BY subdifficultylevel";		
		$result = mysql_query($query) or die("Error in fetching question details!");
		while($line = mysql_fetch_array($result))
		{			
			unset($qcodeArray['topic'][array_search($line[0],$qcodeArray['topic'])]);	
		}		
	}
	if($DTtestQcodestr !='')
	{
		$query = "SELECT qcode FROM adepts_diagnosticTestQuestions WHERE qcode in ($DTtestQcodestr) AND (question LIKE '%.swf%' OR question LIKE '%.html%' )";
		$result = mysql_query($query) or die("Error in fetching question details!");
		while($line = mysql_fetch_array($result))
		{			
			unset($qcodeArray['DTtest'][array_search($line[0],$qcodeArray['DTtest'])]);			
		}
	}			
	return $qcodeArray;
}
?>