<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	include("header.php");
	include("../slave_connectivity.php");
	/*$link = mysql_connect("ec2-54-251-4-141.ap-southeast-1.compute.amazonaws.com","ms_analysis","ARE001") or die("Could not connect : " . mysql_error());
	mysql_select_db("educatio_adepts",$link) or die("Could not select database");*/
	
	include("../userInterface/functions/functions.php");
	include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
    include("../userInterface/classes/clsNCERTQuestion.php");
	include("classes/testTeacherIDs.php");
	
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	
	/*$userID =  28869;
	$schoolCode = 2387554;*/
	
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

	$userDetails	=	getStudentDetails($class, $schoolCode, $section);
	$userIDs	=	array_keys($userDetails);
	$userIDstr	=	implode(",",$userIDs);
	
	$allTTAttempts	=	array();
	$ttAttemptArray = array();
	$failedClusterArrayByUserID = array();
	$clusterMap = array();
	$commonLearningGaps = "";
	$allTTAttemptsStr	=	"";
	$attempt_query = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE userID IN ($userIDstr) AND teacherTopicCode='$ttCode' ORDER BY ttAttemptID";
	$attempt_result = mysql_query($attempt_query);
	if(mysql_num_rows($attempt_result)!=0)
	{
		while($attempt_line=mysql_fetch_array($attempt_result))
		{
			array_push($allTTAttempts,$attempt_line[0]);
		}
		$allTTAttemptsStr = implode(",",$allTTAttempts);
		$clAttemptQuery	=	"SELECT a.clusterCode, COUNT(a.clusterCode) AS failed, cluster FROM ".TBL_CLUSTER_STATUS." a,".TBL_TOPIC_STATUS." b,
							 adepts_clusterMaster c WHERE a.ttAttemptID=b.ttAttemptID AND a.result='FAILURE' AND b.ttAttemptID IN ($allTTAttemptsStr) AND 
							 a.clusterCode=c.clusterCode GROUP BY a.clusterCode ORDER BY failed DESC";
		$clAttemptResult = mysql_query($clAttemptQuery) or die(mysql_error());
		while ($clAttemptLine = mysql_fetch_array($clAttemptResult))
		{
			$commonLearningGaps[$clAttemptLine[0]] = $clAttemptLine[2];
		}
	}
	$query = "SELECT teacherTopicDesc, mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
?>

<title>Topic Class Remediation</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicRemediationClass.css?ver=1" rel="stylesheet" type="text/css">
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
		        <td width="33%" id="studentRemediation" class="pointer"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention"><div class="smallCircle"></div></a><label class="pointer" value="stNeedAttention"><a href="topicRemediationStudent.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=stNeedAttention">STUDENT REMEDIATION</a></label></td>
		        <td width="43%" id="classRemediation" class="pointer"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation"><div class="smallCircle red"></div></a> <label class="textRed pointer" value="clsRemediation"><a href="topicRemediationClass.php?cls=<?=$class?>&section=<?=$section?>&ttCode=<?=$ttCode?>&mode=clsRemediation">CLASS REMEDIATION</a></label></td>
			</table>
			
			<!--<div id="questionContainer">
				<div class="question">
			        <table width="100%" border="0" cellspacing="0">
			            <tbody><tr bgcolor="">
			                <td align="center" valign="top" width="5%"><div id="normal_30208_13145334_7" class="qno">7</div><br>
			                <div class="incorrect_mark"></div>
							                </td>
			                <td align="left"><div>Observe the pattern below:</div>
			<div><img align="absmiddle" src="http://d2tl1spkm4qpax.cloudfront.net/content_images/ALG/ALG_qcode_30208_1.png" id="img7_0" onerror="noimage(this);" height="40px" width="410px"></div>
			<div>Every 4<sup>th</sup> shape in the pattern is <img align="absmiddle" src="http://d2tl1spkm4qpax.cloudfront.net/content_images/ALG/ALG_qcode_30208_2.png" id="img7_1" onerror="noimage(this);" height="35px" width="35px">. So, the 4<sup>th</sup>, 8<sup>th</sup>, 12<sup>th</sup>, 16<sup>th</sup>,... (and so on) shapes will&nbsp;all be <img align="absmiddle" src="http://d2tl1spkm4qpax.cloudfront.net/content_images/ALG/ALG_qcode_30208_2.png" id="img7_1" onerror="noimage(this);" height="35px" width="35px">.</div>
			<div>Which of the following shapes will be <img align="absmiddle" src="http://d2tl1spkm4qpax.cloudfront.net/content_images/ALG/ALG_qcode_30208_2.png" id="img7_1" onerror="noimage(this);" height="35px" width="35px">?&nbsp;</div><br></td>
			            </tr>
			            <tr bgcolor="">
			                <td>&nbsp;</td>
			                <td>
			                <table width="96%" border="1" cellspacing="2" cellpadding="3" class="optionTable">

			                    <tbody><tr valign="top">
			                    <td width="45%" nowrap="" bgcolor="" align="center" ><div class="option"><b>A</b></div>
			                    <div>30<sup>th</sup></div></td>
			                    <td width="45%" nowrap="" bgcolor="" align="center" ><div class="option optionCorrect"><b>B</b></div>
			                    <div>32<sup>nd</sup></div></td>
			                </tr>
			                        <tr valign="top">
			                    <td width="45%" nowrap="" bgcolor="" align="center" ><div class="option"><b>C</b></div>
			                    <div>34<sup>th</sup></div></td>
			                    <td width="45%" nowrap="" bgcolor="" align="center" ><div class="option"><b>D</b></div>
			                    <div>38<sup>th</sup></div></td>
			                </tr>
			                          </tbody></table>
			                </td>
			            </tr>



			        </tbody></table>

			    </div>
			</div>-->
			
			<table class="pagingTables">
			<tr>
			<td width="35%">Learning Unit which Needs Your Attention : </td>
			</tr>
			
			</table>
			
			<?php
			$k=0;
			foreach($commonLearningGaps as $clusterCode=>$cluster)
			{ 
			if($k==2)
				break;
			$k++;
			?>
			<table id="pagingTable">
		        <td width="35%"><?=" - " . $cluster?></td>
			</table>
			
			<?php }
			?>
			
			<?php 
			if($k==0) {
			?>
			<table id="pagingTable">
		        <td width="35%"><?=" - " . None?></td>
			</table>
			<?php }
			?>
			
			<br>
			<br>
			
			<?php
			$j=0;
			foreach($commonLearningGaps as $clusterCode=>$cluster)
			{ 
			if($j==2)
				break;
			$j++;
			?>
			<table id="pagingTable">
		        <td width="35%"><?=$cluster?></td>
			</table>
			<?php 
				$qcodeArray	=	array();
				$qcodeArray	=	getQcodes($clusterCode);
				$i=0;
				foreach($qcodeArray as $qcode) { $i++;?>
						<div id="questionContainer">
						<div class="question">
						<table width="100%" border="0" cellspacing="0">
			            <tbody><tr bgcolor="">
			                <td align="center" valign="top" width="5%">
								<div class="qno"><?=$i?></div><br>
			                	<div class="incorrect_mark"></div>
							</td>
				            <td align="left"><?=getQuestionData($qcode,$class,$section)?></td>
				        </tr>
						</tbody></table>
						</div>
						</div>
			<?php } ?>
			<br />
			<?php }
			?>
			
			
		</div>
	</div>

<?php include("footer.php") ?>
<?php

function getQcodes($clusterCode)
{
	$qcodeArray	=	array();
	$sq	=	"SELECT qcode FROM adepts_questions WHERE clusterCode='$clusterCode' ORDER BY RAND() LIMIT 10";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$qcodeArray[]	=	$rw[0];
	}
	return $qcodeArray;
}

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
    	$questionStr .= "<br/><span class='title'>Answer : ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."</span><br/><br/>";
    	else
    		$questionStr .= "<br/></span>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<span class='title'> Answer : ".$question->getCorrectAnswerForDisplay()."</span><br/>";

    if(!$dynamic)
    {
	    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{
	        $query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class a, adepts_userDetails u
	                  WHERE a.userID=u.userID AND category='STUDENT' AND childClass='$class' AND R=0 AND qcode=".$qcode;
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