<?php

set_time_limit(0);
include("header.php");
include("../slave_connectivity.php");
include("../userInterface/functions/functions.php");
include("../userInterface/functions/orig2htm.php");
include("../userInterface/classes/clsQuestion.php");
include("../userInterface/functions/functionsForDynamicQues.php");
error_reporting(E_ERROR);

$keys = array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key} = $_REQUEST[$key] ;
}



	$query = "SELECT teacherTopicDesc, mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
	$topicCode = $line[1];
	$class	=	$_GET['cls'];
//echo  "cls == ".$cls." section == ".$section."  ttCode == ".$ttCode."  clusterCode == ".$clusterCode;

?>

<title>Common Wrong Answers</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/commonWrongAnswers.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
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
					<div id="pageText">TOPIC RESEARCH</div>
				</div>
			</div>
			
			<table id="childDetails" align="top">
				<td width="18%" id="sampleQuestions" class="pointer"><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">SAMPLE QUESTIONS</div></div></div></a></td>
		        <td width="18%" id="wrongAnswers" class="pointer"><a href="commonWrongAnswers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition">COMMON WRONG ANSWERS</div></div></div></a></td>
		        <td width="18%" id="researchStudies" class="pointer"><a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF RESEARCH STUDIES</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF STUDENT INTERVIEWS</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">MISCONCEPTION VIDEOS</div></div></div></a></td>
			</table>
			
			<table id="pagingTable">
		        <td width="35%"><?=$teacherTopicDesc?></td>
			</table>
			
<?php

	if (isset($ttCode))
	{
		$SDLArray = getMisconceptionSdlsForTopic($ttCode,$class);
		$noofclusters = count($SDLArray);
		if($noofclusters > 0)
		{
			echo "<div id='questionContainer'><span style='margin-left: 10px;font-size: 1.2em;margin-top: 2px;float: left;'>This is based on the response data of all schools which took Mindspark.</span>";
			foreach($SDLArray as $clusterCode=>$sdlList)
			{
				$currentTempCluster = $clusterCode;
				$finalSDLArray = explode(",",$sdlList);
				foreach($finalSDLArray as $currentTempSDL)
				{
					$query = "SELECT subdifficultylevel, group_concat(qcode) as sdlquestions FROM adepts_questions
							  WHERE  subdifficultylevel=$currentTempSDL AND clusterCode='$currentTempCluster' AND context<>'US' GROUP BY subdifficultylevel";
	
					$result = mysql_query($query);
					$currentTempSDLQues = "";
					while ($line=mysql_fetch_array($result))
					{
						$currentTempSDLQues=$line[1];
					}
					$SDLQuesArray = array();
					if($currentTempSDLQues != "")
					{
						$SDLQuesArray = explode(',',$currentTempSDLQues);
						$qcode = $SDLQuesArray[array_rand($SDLQuesArray,1)];
						$question = getQuestionData($qcode,$class,$section);
						$SDL_question_str = '<div class="question">'.$question.'<br/></div>';
						echo $SDL_question_str;
					}
				}
			}
			echo "</div>";
		}
		else
		{
			echo "<div align='center'><br /><br /><h3>No data found.</h3></div>";
		}
	}

?>
			
			
		</div>
	</div>

<?php include("footer.php") ?>


<?php


function getQuestionData($qcode,$class,$section)
{

    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new Question($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

    $questionStr .= "<p>";
        //$questionStr .= $qsrn.". ".$question->getQuestion()."<br/>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";

    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{
    	$questionStr .= "<table width='96%' border='0' class='optionTable'>";
    	$correctAns = $question->correctAnswer;

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr valign='top'>";
	            $questionStr .= "<td width='45%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="A")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>A.</b></div> <div>".$question->getOptionA()."</div></td>";
	            $questionStr .= "<td width='45%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="B")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>B.</b></div> <div>".$question->getOptionB()."</div></td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr valign='top'>";
	            $questionStr .= "<td width='45%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="C")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>C.</b></div> <div>".$question->getOptionC()."</div></td>";
	            $questionStr .= "<td width='45%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="D")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>D.</b></div> <div>".$question->getOptionD()."</div></td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr valign='top'>";
	            $questionStr .= "<td width='33%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="A")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>A.</b></div> <div>".$question->getOptionA()."</div></td>";
	            $questionStr .= "<td width='33%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="B")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>B.</b></div> <div>".$question->getOptionB()."</div></td>";
	            $questionStr .= "<td width='33%' nowrap='' bgcolor='' align='center'><div class='";
	            if($correctAns=="C")
	            	$questionStr .= "optionCorrect ";
	            $questionStr .= "option'><b>C.</b></div> <div>".$question->getOptionC()."</div></td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }

    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title textRed'>Answer : ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."</span><br/><br/>";
    	else
    		$questionStr .= "<br/></span>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<span class='title textRed'> Answer : ".$question->getCorrectAnswerForDisplay()."</span><br/>";

    if(!$dynamic)
    {
	    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{
	        $query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class a, adepts_userDetails u
	                  WHERE a.userID=u.userID AND category='STUDENT' AND childClass='$class' AND qcode=".$qcode;
	        if($section!="")
	            $query .= " AND childSection = '$section'";
	        $query .= " GROUP BY A";
	        //echo $query;
	        $result = mysql_query($query) or die(mysql_error());
	        $totalAttempts = 0;
	        $optionsData = array();
	        while ($line = mysql_fetch_array($result)) {
	            $optionsData[$line[0]] = $line[1];
	            $totalAttempts += $line[1];
	        }
	        $max = 0;

	        foreach ($optionsData as $opt => $val)
	        {
	            $percentageOpted = $val/$totalAttempts*100;
	            if($percentageOpted>$max && $opt!=$question->correctAnswer)
	            {
	                $mostCommonWrongAnswer = $opt;
	                $max = $percentageOpted;
	            }
	        }
	    }
	    elseif ($question_type=="Blank")
	    {

	        $query = "SELECT A,count(*) FROM ".TBL_QUES_ATTEMPT."_class$class a, adepts_userDetails u
	                  WHERE  a.userID=u.userID AND childClass='$class' AND category='STUDENT' AND R=0 AND qcode=".$qcode;
			if($section!="")
	            $query .= " AND childSection = '$section'";
			$query .= " GROUP BY A ORDER BY 2 DESC limit 1";
			$result = mysql_query($query) or die(mysql_error());
			$line = mysql_fetch_array($result);
			$mostCommonWrongAnswer = $line[0];
	    }
    }

    if($mostCommonWrongAnswer!="")
    {
        $questionStr .= "<br/><div><span class='title'>Most common wrong answer : $mostCommonWrongAnswer</span></div><br/><br/>";
    }
    if($dynamic)
    {
    	$questionStr .= "<br/><div class='legend'>Note: This is a dynamically generated question. Students might not have got the same question.</div><br/>";
    }
    return $questionStr;
}



?>