<?php 
	include("header.php");	
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
    if(!isset($_POST['revisionSessionID']))
	{
		header("Location:logout.php");
		exit;
	}
    $userID     = $_SESSION['userID'];
	$revisionSessionID = $_POST['revisionSessionID'];
	$studentID         = $_POST['studentID'];
	$studentName       = $_POST['studentName'];
	$childClass			= $_POST['childClass'];
	$noOfTotalQuesAttempted = $_POST['noOfTotalQuesAttempted'];		//For the mantis task 8192
	$flag = 0;
	$whichBtnActivated = isset($_POST['whichBtn'])?$_POST['whichBtn']:'3';

	
	$query  = "SELECT a.qcode, a.R, a.S, a.A, sessionID, a.clusterCode, cluster, a.srno, a.teacherTopicCode
			   FROM   adepts_revisionSessionDetails a, adepts_clusterMaster b
			   WHERE  userID=$studentID AND revisionSessionID=$revisionSessionID AND a.clusterCode=b.clusterCode";		

	if(isset($_POST['datatype']) && $_POST['datatype'] != 'all')
	{
		$query .= " and a.R = ". $_POST['datatype'];
	}	   
	$query  .=	" ORDER BY questionNo";
	$result = mysql_query($query) or die(mysql_error()."Invalid search criteria");
	$noOfRecords = mysql_num_rows($result);	//For the mantis task 8192
	$totalQuestions = mysql_num_rows($result);
	function getLongUserResponse($srno, $userID, $qcode, $sessionID,$user_ans)
	{
	    $longResponse = "";
	    $query = "SELECT userResponse FROM longUserResponse WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
	    $result = mysql_query($query);
	    if(mysql_num_rows($result) > 0)
	    {
	        $row = mysql_fetch_array($result);
	        $longResponse = $row[0];
	    }
	    else {
	        $longResponse = $user_ans;
	    }
	    /*else {
	        $query = "SELECT A FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE srno='$srno' AND userID='$userID' AND sessionID='$sessionID' AND qcode='$qcode'";
	        $result = mysql_query($query);
	        if(mysql_num_rows($result) > 0)
	        {
	            $row = mysql_fetch_array($result);
	            $longResponse = $row[0];
	        }
	    }*/
	    return $longResponse;
	}

?>

<title>Mindspark - Revision Session</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentReport.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	
	$(document).ready(function(){		// for the mantis task 8192
		$(".revisionSessionBtn").removeClass('whiteColor');
		switch(<?=$whichBtnActivated?>)
		{
			case 1:
			{
				$("#revisionSessionBtn1").addClass('whiteColor');
				break;
			}
			case 2:
			{
				$("#revisionSessionBtn2").addClass('whiteColor');
				break;
			}
			case 3:
			{
				$("#revisionSessionBtn3").addClass('whiteColor');
				break;
			}
			default:
			{
				$("#revisionSessionBtn3").addClass('whiteColor');
			}
		}
	});

	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		//$("#container").css("height",containerHeight+"px");
		//$("#trailContainer").css("height",containerHeight+"px");
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
	function loadConstrFrame(cfr)
	{
			var cfrw=cfr.contentWindow;
			// cfrw.drawcode.setDrawnShapes(cfr.getAttribute("data-response"));
			cfrw.postMessage(JSON.stringify({
				subject: 'trail',
				content: {
					type: 'display',
					trail: cfr.getAttribute("data-response"),
				},
			}), getWindowOrigin(cfr.src));
	}
	function startInteractive(frame)
	{	
	    try {
	        var win = frame.contentWindow;
	        frames.push(frame);windows.push(win);
	        //win.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
	    }
	    catch (ex) {
	        //alert('error in getting the response from interactive');
	    }
	}
	var frames=[],windows=[];
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
	// Listen to message from child window
	eventer(messageEvent, function (e) {
	    var response1 = ""; 
	    response1 = e.data;
	    //e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
	    if ($.inArray(e.source,windows)>-1){
	    	var frame=frames[$.inArray(e.source,windows)];
		   	if(response1.indexOf("loaded=1") == 0) {
				e.source.postMessage('setUserResponse='+$(frame).attr('data-response'), '*');
		   	}
		   	else if(response1.indexOf("frameHeight=") == 0) {
			  frameHeight=response1.replace('frameHeight=','');$(frame).attr('height',frameHeight);
			}
	    }
	}, false);
</script>

<script>
function submitform(data,whichBtnActivated){
		setTryingToUnload();
		document.getElementById("datatype").value = data;
		document.getElementById("whichBtn").value = whichBtnActivated;
	    document.getElementById("frmRegnDetails").submit();
}
</script>


</head>
<body class="translation" onLoad="load()" onResize="load()">
<form id="frmRegnDetails" method="post" action="<?=$_SERVER['PHP_SELF']?>">
	<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="<?php echo $revisionSessionID ?>">
	<input type="hidden" name="studentID" id="studentID" value="<?php echo $studentID ?>">
	<input type="hidden" name="studentName" id="studentName" value="<?php echo $studentName ?>">
	<input type="hidden" name="childClass" id="childClass" value="<?php echo $childClass ?>">
	<input type="hidden" name="noOfTotalQuesAttempted" id="noOfTotalQuesAttempted" value="<?php echo $noOfTotalQuesAttempted ?>">
	<td>
		<input type="hidden" name="datatype" id="datatype" value="">
		<input type="hidden" name="whichBtn" id="whichBtn" value="">
	</td>
</form>
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
					<div id="pageText">REVISION SESSION</div>
				</div>
				<div id="classTopic">
					<div class="arrow-black1"></div>
					<div id="classText">TOTAL QUESTIONS ATTEMPTED : <span style="color:#E75903;"><?=$noOfTotalQuesAttempted?></span></div>	<!-- For the mantis task 8192 -->
				</div>
			</div>
			
			<table id="pagingTable">
		        <td width="35%"><?=$studentName?></td>
			</table>
			
			<div id="questionContainer">
			<div align="center" style="font-size:1.3em;">

			<div class='revisionSessionBtn' id='revisionSessionBtn1' style='width: 310px;' onclick="javascript:submitform('1','1')">	<!-- // for the mantis task 8192 -->
				<img style='position:relative;top:5px;' src="assets/Small_Green.gif" width="20" height="30"> 
				<span style='position:relative;top:-8px;'>-></span>
				<img style='position:relative;top:-5px;' src='assets/wrong.gif'> 
				<span style='position:relative;top:-7px;'>in MS sessions</span> 
				<img style='position:relative;top:-1px;' src='assets/right.gif'>
				<span style='position:relative;top:-6px;'> in revision session</span>
			</div>
			<div class='revisionSessionBtn' id='revisionSessionBtn2' style='width: 310px;' onclick="javascript:submitform('0','2')">	<!-- // for the mantis task 8192 -->
				<img style='position:relative;top:5px;' src="assets/Small_Red.gif" width="20" height="30">
				<span style='position:relative;top:-8px;'>-></span>
				<img style='position:relative;top:-5px;' src='assets/wrong.gif'> 
				<span style='position:relative;top:-7px;'>in MS sessions</span> 
				<img style='position:relative;top:-1px;' src='assets/wrong.gif'>
				<span style='position:relative;top:-6px;'> in revision session</span>
			</div>
			<div class='revisionSessionBtn whiteColor' id='revisionSessionBtn3' style='top: 7px;line-height: 35px;width: 150px;' onclick="javascript:submitform('all','3')">Show all Questions</div>	<!-- // for the mantis task 8192 -->
</div>
			<?php
				if($noOfRecords==0)
				{
					echo '<div class="question" style="border-bottom:0px;"><center>No question found!</center></div>';
					exit();
				}
			?>
				
			<?php
				$srno = 1;
				while ($line=mysql_fetch_array($result))
				{
					$question     = new Question($line['qcode']);
					if($question->isDynamic())
					{
						$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$studentID AND class=".$childClass." AND mode='revision' AND quesAttempt_srno=".$line['srno'];
						$param_result = mysql_query($query);
						$param_line   = mysql_fetch_array($param_result);
						$question->generateQuestion("answer",$param_line[0]);
					}
				
					$questionType = $question->quesType;
					$timeTaken    = $line['S'];
					$response     = $line['R'];
					$user_ans     = $line['A'];
					if($user_ans=="")
						$user_ans = "N.A.";
					$clusterCode  = $line['clusterCode'];
					$clusterDesc  = $line['cluster'];
					$ttCode       = $line['teacherTopicCode'];
				
					$clusterAttemptIDs = "";
					$query = "SELECT clusterAttemptID FROM adepts_teacherTopicStatus a, adepts_teacherTopicClusterStatus b
							  WHERE a.userID=b.userID AND a.userID=$studentID AND teacherTopicCode='$ttCode' AND clusterCode='$clusterCode'";
					$cl_result = mysql_query($query);
					while ($cl_line = mysql_fetch_array($cl_result))
						$clusterAttemptIDs .= $cl_line[0].",";
					$clusterAttemptIDs = substr($clusterAttemptIDs,0,-1);
				
					$flag = 0 ;
					if($clusterAttemptIDs!="")
					{
						$qcodeStr = "";
						$query = "SELECT qcode FROM adepts_questions WHERE clusterCode='$clusterCode' AND subdifficultylevel=".$question->subDifficultyLevel;
						$ques_result = mysql_query($query);
						while($ques_line = mysql_fetch_array($ques_result))
							$qcodeStr .= $ques_line[0].",";
						$qcodeStr = substr($qcodeStr,0,-1);
						if($qcodeStr!="")
						{
							$query = "SELECT count(srno)  FROM   ".TBL_QUES_ATTEMPT."_class$childClass
									  WHERE  userID=$studentID AND clusterAttemptID in ($clusterAttemptIDs) AND qcode IN ($qcodeStr) AND R=0";
							//echo $query."<br/>";
							$quesWrong_result = mysql_query($query) or die(mysql_error());
							$quesWrong_line   = mysql_fetch_array($quesWrong_result);
				
							if($quesWrong_line[0]>=2 && $response==1)
								$flag = 1;	//Sdls for which the student had got two or more pools wrong in the normal session, and in which the student got right in the revision session to be highlighted green
							elseif ($quesWrong_line[0]>=2 && $response==0)
								$flag = 2;	//Sdls for which the student had got two or more pools wrong in the normal session, and in which the student made a mistake in the revision session also to be highlighted red
						}
					}

					$correct_answer = $question->getCorrectAnswerForDisplay();
				
				
					$optiona_bgcolor="";
					$optionb_bgcolor="";
					$optionc_bgcolor="";
					$optiond_bgcolor="";
				
					if($user_ans=="A")
						$optiona_bgcolor="optionIncorrect";
					if($user_ans=="B")
						$optionb_bgcolor="optionIncorrect";
					if($user_ans=="C")
						$optionc_bgcolor="optionIncorrect";
					if($user_ans=="D")
						$optiond_bgcolor="optionIncorrect";
				
					if($correct_answer=="A")
						$optiona_bgcolor="optionCorrect";
					if($correct_answer=="B")
						$optionb_bgcolor="optionCorrect";
					if($correct_answer=="C")
						$optionc_bgcolor="optionCorrect";
					if($correct_answer=="D")
						$optiond_bgcolor="optionCorrect";
				
					if(strpos($question->questionStem,"ADA_eqs") !== false) {
						$longResponse = getLongUserResponse($line['srno'], $userID, $line['qcode'], $line['sessionID'], $user_ans);
						$user_ans='';
					}
					$cls = "";
					if($response==1) $cls="correctMark"; else $cls = "incorrectMark";
				?>

				

				<div class="question">	
					<div class="<?=$cls?>"> <br><br>
					<?php if($flag == 1 || $response==1) {?>
						<img width="20" height="30" style='padding-left:10px;' src="assets/Small_Green.gif">
					<?php } if($flag == 2 || $response!=1) { ?>
						<img width="20" height="30" style='padding-left:10px;' src="assets/Small_Red.gif"> <?php } ?> </div>
					<div class="<?=$flagcls?>"></div>
					<table width="100%" border="0" cellspacing="0">
						<tbody>
						<tr bgcolor="">
							<td align="center" valign="top" width="5%"><div id="" class="qno"><?=$srno?></div><br></td>
							<td align="left"><?=(strpos($question->questionStem,"ADA_eqs") === false)?$question->getQuestion():$question->getQuestionForDisplay($longResponse,2)?><br></td>
						</tr>
					<?php
						if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
					?>
						<tr bgcolor="">
							<td>&nbsp;</td>
							<td>
								<table width="96%" border="1" cellspacing="2" cellpadding="3" class="optionTable">
									<tbody>
									<?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
                <tr valign="center">
                    <td width="5%" nowrap align="left" ><div class="option <?=$optiona_bgcolor?>"><b>A</b></div></td>
                    <td width="45%" class='optionText'><?php echo $question->getOptionA();?></td>
                    <td width="5%" nowrap align="left" ><div class="option <?=$optionb_bgcolor?>"><b>B</b></div></td>
                    <td width="45%" class='optionText'><?php echo $question->getOptionB();?></td>
                </tr>
    <?php    }    ?>
    <?php    if($questionType=='MCQ-4')    {    ?>
                <tr valign="center">
                    <td width="5%" align="left"><div class="option <?=$optionc_bgcolor?>"><b>C</b></div></td>
                    <td width="45%" class='optionText'><?php echo $question->getOptionC();?></td>
                    <td width="5%" align="left"><div class="option <?=$optiond_bgcolor?>"><b>D</b></div></td>
                    <td width="45%" class='optionText'><?php echo $question->getOptionD();?></td>
                </tr>
    <?php    }    ?>
    <?php    if($questionType=='MCQ-3')    {    ?>
                <tr valign="center">
                	<td width="3%" nowrap align="left"><div class="option <?=$optiona_bgcolor?>"><b>A</b></div></td>
                    <td width="30%" class='optionText'><?php echo $question->getOptionA();?></td>
                    <td width="3%" nowrap align="left"><div class="option <?=$optionb_bgcolor?>"><b>B</b></div></td>
                    <td width="30%" class='optionText'><?php echo $question->getOptionB();?></td>
                    <td width="3%" nowrap align="left"><div class="option <?=$optionc_bgcolor?>"><b>C</b></div></td>
                    <td width="30%" class='optionText'><?php echo $question->getOptionC();?></td>
                </tr>
            <?php    }    ?>
									</tbody>
								</table>
							</td>
						</tr>
						<?php }  ?>
						</tbody>
					</table>
					<div class="desk_block">
						<div class="block mid_repeat">
							<div class="block_header">
								<div>
									<span>User Response: </span>&nbsp;&nbsp;
									<span class="title"><?=$user_ans;?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php if($correct_answer!="")    {    ?><span>Correct answer: </span><span class="title"><?=$correct_answer?></span>&nbsp;&nbsp;&nbsp;&nbsp;<?php }  ?>
									<span>Time taken: </span><span class="title"><?=$timeTaken?> secs</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</div><br/><br/>
							</div>
						</div>
					</div>
				</div>
<?php    $srno++;}    ?>
			</div>
		</div>
	</div>

<?php include("footer.php") ?>