<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	include("header.php");
	include("../slave_connectivity.php");
	include("../userInterface/classes/clsTopicProgress.php");
	include("functions/functions.php");
	include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("classes/testTeacherIDs.php");
	$userID	=	$_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

	$ttCode = $_GET['ttCode'];
	$class	=	$_GET['cls'];
	$section	=	$_GET['section'];
	$mode	=	"";
	
	$query = "SELECT teacherTopicDesc, mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
	if(isset($_GET["mode"]))
		$mode	=	$_GET["mode"];
		
	//for common wrong answers
	$commonWrongAnswerHTML = "";
	$userArray = getStudentDetails($class, $schoolCode, $section);
	$userIDArray = array_keys($userArray);
	$userIDs = implode(",",$userIDArray);
	$query = "SELECT qcode, COUNT(srno) FROM ".TBL_QUES_ATTEMPT."_class$class WHERE userID in ($userIDs) AND teacherTopicCode='$ttCode' AND R=0
			  GROUP BY qcode ORDER BY COUNT(srno) DESC LIMIT 10";
	$result = mysql_query($query);
	$totalQuesDisp	=	mysql_num_rows($result);
	$i=0;
?>


<title>Topic Section Remediation</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicRemediationSection.css?ver=1" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="../js/load.js"></script>
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
		$("#click").click(function (){
                //$(this).animate(function(){
                    $('body,html').animate({
                        scrollTop: $("#misconception").offset().top
                    }, 500);
                //});
            });
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
				<td width="33%" id="sectionRemediation" class="pointer"><a href="topicRemediationSection.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=secRemediation"><div class="smallCircle red"></div></a><label class="textRed pointer" value="secRemediation"><a href="topicRemediationSection.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=secRemediation">SECTION REMEDIATION</a></label></a></td>
		        <td width="33%" id="studentRemediation" class="pointer"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention"><div class="smallCircle"></div></a><label class="pointer" value="stNeedAttention"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention">STUDENT REMEDIATION</a></label></td>
		        <td width="43%" id="classRemediation" class="pointer"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation"><div class="smallCircle"></div></a> <label class="pointer" value="clsRemediation"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation">CLASS REMEDIATION</a></label></td>
			</table>
			
			<table id="pagingTable">
		        <td width="35%">Most Problematic Questions for this section.</td>
				<td style="float:right;cursor:pointer;" id="click">MISCONCEPTIONS IN CLASS</td>
			</table>
			
			<div id="questionContainer">
				<?php
				if($totalQuesDisp==0)
				{ ?>
					<div align="center" class="question">No common wrong answers identified for the class for this topic.</div>
				<?php
				}
				else
				{
					while($row = mysql_fetch_array($result))
					{
						if($i==10)
							break;
						$qcode	=	$row[0]; ?>
						<div class="question">
										<table width="100%" border="0" cellspacing="0">
							            <tbody><tr bgcolor="">
							                <td align="center" valign="top" width="5%">
												<div class="qno"><?=$i+1?></div><br>
							                	<div class="incorrectMark"></div>
											</td>
								            <td align="left"><?php
		echo getQuestionData($qcode, $class, $section, $userIDs);
		?></td>
								        </tr>
										</tbody></table>
										</div>
						<div class="space"></div>
						<?php
						$i++;
					}
				}
				?>
				<div class="space"></div>
				<div class="heading" id="misconception"><b>MISCONCEPTIONS IN CLASS</b></div>
				<?php	
					echo misConception($ttCode,$userArray,$class);
				?>
			
			
		</div>
		</div>
	</div>

<?php include("footer.php") ?>

<?php
function getQuestionData($qcode, $class, $section, $userIDStr)
{
    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new Question($qcode);
    $dynamic = 0;
	$charLenLess15=1;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

    $questionStr .= "<p>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";
    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{
			//$questionStr .= "<table width='96%' cellspacing='2' cellpadding='3' class='optionTable'>";
	    	$correctAns = $question->correctAnswer;

		    
		    if($question_type=='MCQ-4')	{
		        $questionStr .='<div id="pnlOptions">
	                <div class="option" id="optionA"><div class="optionX optionInactive';
					if($correctAns=="A")
					$questionStr .= ' optionCorrect';
					$questionStr .='">A</div>
	                    <div class="optionText" id="pnlOptionTextA">'.$question->getOptionA().'
	                    </div>
	                </div>
	                <div class="option" id="optionB"><div class="optionX optionInactive';
					if($correctAns=="B")
					$questionStr .= ' optionCorrect';
					$questionStr .='">B</div><div class="optionText" id="pnlOptionTextB">'.$question->getOptionB().'</div>
	                </div>
	                <div class="option clear" id="optionC"><div class="optionX optionInactive';
					if($correctAns=="C")
					$questionStr .= ' optionCorrect';
					$questionStr .='">C</div><div class="optionText" id="pnlOptionTextC">'.$question->getOptionC().'</div>
	                </div>
	                <div class="option" id="optionD"><div class="optionX optionInactive';
					if($correctAns=="D")
					$questionStr .= ' optionCorrect';
					$questionStr .='">D</div>
	                    <div class="optionText" id="pnlOptionTextD">'.$question->getOptionD().'
	                    </div>
	                </div>
	                <div class="clear"></div>
	            </div><br/><br/>';
		    }
		    else if($question_type=='MCQ-3')	{
		        $questionStr .='<div id="pnlOptions">
	                <div class="option" id="optionA" style="width:30% !important"><div class="optionX optionInactive';
					if($correctAns=="A")
					$questionStr .= ' optionCorrect';
					$questionStr .='">A</div>
	                    <div class="optionText" style="width:75% !important" id="pnlOptionTextA">'.$question->getOptionA().'
	                    </div>
	                </div>
	                <div class="option" id="optionB" style="width:30% !important"><div class="optionX optionInactive';
					if($correctAns=="B")
					$questionStr .= ' optionCorrect';
					$questionStr .='">B</div><div class="optionText" style="width:75% !important" id="pnlOptionTextB">'.$question->getOptionB().'</div>
	                </div>
	                <div class="option" id="optionC" style="width:30% !important"><div class="optionX optionInactive';
					if($correctAns=="C")
					$questionStr .= ' optionCorrect';
					$questionStr .='">C</div><div class="optionText" style="width:75% !important" id="pnlOptionTextC">'.$question->getOptionC().'</div>
	                </div><br/><br/>';
		    }
			else if($question_type=='MCQ-2')	{
		        $questionStr .='<div id="pnlOptions">
	                <div class="option" id="optionA"><div class="optionX optionInactive';
					if($correctAns=="A")
					$questionStr .= ' optionCorrect';
					$questionStr .='">A</div>
	                    <div class="optionText" id="pnlOptionTextA">'.$question->getOptionA().'
	                    </div>
	                </div>
	                <div class="option" id="optionB"><div class="optionX optionInactive';
					if($correctAns=="B")
					$questionStr .= ' optionCorrect';
					$questionStr .='">B</div><div class="optionText" id="pnlOptionTextB">'.$question->getOptionB().'</div>
	                </div><br/><br/>';
		    }
		    //$questionStr .= "</table>";
    	
    }

    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><br/><span class='title'>Answer : ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."</span><br/><br/>";
    	else
    		$questionStr .= "<br/></span>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<span class='title'> Answer : ".$question->getCorrectAnswerForDisplay()."</span><br/>";

    $showMostCommonWrongAns = 1;
	/*$_tH = (int)date('G');
    if (!(($_tH >= 14) || ($_tH < 10) ))
    {
        $showMostCommonWrongAns = 0;    //Finding, common wrong answer for a question/class combination being heavy, right now stopped during peak hrs.
    }*/
    if(!$dynamic && $showMostCommonWrongAns)
    {
	    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{

	        $query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class 
	                  WHERE  userID in ($userIDStr) AND qcode=".$qcode;
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
	        $query = "SELECT A,count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class 
	                  WHERE  userID in ($userIDStr) AND R=0 AND qcode=".$qcode;
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

function misConception($ttCode,$userArray,$class)
{
	$userIDArray = array_keys($userArray);
	$userIDs = implode(",",$userIDArray);
	$sql = "SELECT GROUP_CONCAT(misconception) FROM adepts_questions a, ".TBL_QUES_ATTEMPT."_class$class b WHERE a.qcode=b.qcode AND R=0 AND teacherTopicCode='$ttCode' AND userID IN($userIDs) AND misconception<>'' AND !isNull(misconception)";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$misconceptionIDsCSV = $row[0];
	if(!($misconceptionIDsCSV == "" || is_null($misconceptionIDsCSV)))
	{
		$misconceptionArray = explode(",",$misconceptionIDsCSV);
		$topMisconception = array_count_values($misconceptionArray);
		arsort($topMisconception);
		$topMisconception = array_slice($topMisconception, 0, 2,true);
		$misconceptionHTML = '';
		foreach($topMisconception as $misconceptionID=>$occurances)
		{
			$sql = "SELECT description FROM educatio_educat.misconception_master WHERE id='$misconceptionID'";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$misconceptionStatement = $row['description'];
			
			$sql = "SELECT GROUP_CONCAT(DISTINCT userID) FROM adepts_questions a, ".TBL_QUES_ATTEMPT."_class$class b WHERE a.qcode=b.qcode AND teacherTopicCode='$ttCode' AND userID IN($userIDs) AND FIND_IN_SET($misconceptionID,misconception)";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			$studentAttemptedCSV = $row[0];

			$sql = "SELECT GROUP_CONCAT(DISTINCT userID) FROM adepts_questions a, ".TBL_QUES_ATTEMPT."_class$class b WHERE a.qcode=b.qcode AND R=1 AND teacherTopicCode='$ttCode' AND userID IN($userIDs) AND FIND_IN_SET($misconceptionID,misconception)";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			$studentGotRightCSV = $row[0];

			$studentAttemptedArray = explode(",",$studentAttemptedCSV);	
			$studentGotRightArray = explode(",",$studentGotRightCSV);	
			
			$studentNeverGotRight = array();
			$studentNeverGotRight  = array_diff($studentAttemptedArray,$studentGotRightArray);

			$misconceptionHTML .= '<div class="misconception"><div class="two" style="padding:5px; margin:5px;">';
			$misconceptionHTML .= "$misconceptionStatement";		
			$misconceptionHTML .= '</div></div>';
			$misconceptionHTML .= '<div class="two">
				Students who never got it right&nbsp;:&nbsp;
				<span style="font-weight:bold;">';
			$studentNameStr = "";
			foreach($studentNeverGotRight as $userID)
			{
				$studentNameStr .= $userArray[$userID][0].", ";
			}
			$misconceptionHTML .= ($studentNameStr == "")?"":substr($studentNameStr,0,strlen($studentNameStr)-2);
			$misconceptionHTML .= '</span>
			</div>';
		}
	}
	else
	{
		$misconceptionHTML = '<div class="two">No misconception found</div>';	
	}
	return $misconceptionHTML;
}
?>