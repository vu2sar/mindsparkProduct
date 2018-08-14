<?php
    include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
    $userID     = $_SESSION['userID'];
    if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    if(!isset($_POST['studentID']))
	{
		header("Location:logout.php");
		exit;
	}
	$studentID   = $_POST['studentID'];
	$dt          = substr($_POST['dt'],6,4)."-".substr($_POST['dt'],3,2)."-".substr($_POST['dt'],0,2);
	$ttCode      = $_POST['ttCode']  ;
	$studentName = $_POST['studentName'];
	$perCorrect  = $_POST['perCorrect'];
	$query  = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$teacherTopicName = $line[0];
	$query  = "SELECT qcode, R, S, A, sessionID, srno, teacherTopicCode
           FROM   ".TBL_TOPIC_REVISION."
           WHERE  userID=$studentID AND attemptedDate='$dt' AND teacherTopicCode='$ttCode'
 		   ORDER BY srno";
$result = mysql_query($query) or die(mysql_error()."Invalid search criteria");
$noOfRecords = mysql_num_rows($result);
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
<html>
<head>
<head>
<title>Mindspark - Topic-wise Practice</title>
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentTrail.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
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
</head>
<body class="translation" onLoad="load();">
<?php include("eiColors.php") ?>
<div id="fixedSideBar">
	<?php include("fixedSideBar.php") ?>
</div>
<div id="topBar">
	<?php include("topBar.php"); ?>
</div>
<div id="sideBar">
	<?php include("sideBar.php") ?>
</div>
<div id='container'>
<?php

if($noOfRecords==0)
{
    echo "<center>No ques found!</center>";
    exit();
}

$totalQuestions = mysql_num_rows($result);
?>
<div align='center'>
	<span class="title">Name:</span> <?=$studentName?> &nbsp;&nbsp;&nbsp;&nbsp;
	<span class="title">Topic: </span><span><?=$teacherTopicName?></span>&nbsp;&nbsp;&nbsp;&nbsp;
	<span class="title">Total Questions:</span> <?=$totalQuestions?>&nbsp;&nbsp;&nbsp;&nbsp;<span class="title">% correct:</span> <?=$perCorrect?>%
</div>
<br/>
<?php
$srno = 1;
while ($line=mysql_fetch_array($result))
{
	$question     = new Question($line['qcode']);
	if($question->isDynamic())
	{
		$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE userID=$studentID AND class=".$_SESSION['childClass']." AND mode='topicRevision' AND quesAttempt_srno=".$line['srno'];
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
    $ttCode       = $line['teacherTopicCode'];
    $correct_answer = $question->getCorrectAnswerForDisplay();


    $optiona_bgcolor="";
    $optionb_bgcolor="";
    $optionc_bgcolor="";
    $optiond_bgcolor="";

    if($user_ans=="A")
        $optiona_bgcolor="#FFA500";
    if($user_ans=="B")
        $optionb_bgcolor="#FFA500";
    if($user_ans=="C")
        $optionc_bgcolor="#FFA500";
    if($user_ans=="D")
        $optiond_bgcolor="#FFA500";

    if($correct_answer=="A")
        $optiona_bgcolor="#00FF00";
    if($correct_answer=="B")
        $optionb_bgcolor="#00FF00";
    if($correct_answer=="C")
        $optionc_bgcolor="#00FF00";
    if($correct_answer=="D")
        $optiond_bgcolor="#00FF00";
    if(strpos($question->questionStem,"ADA_eqs") !== false) {
	    $longResponse = getLongUserResponse($line['srno'], $userID, $line['qcode'], $line['sessionID'], $user_ans);
	    $user_ans='';
    }

    $cls = "";
	if($response==1) $cls="correct_mark"; else $cls = "incorrect_mark";

?>
	<div class='question'>
        <table width='100%' border="0" cellspacing="0">
            <tr>
                <td align="center" valign='top' width="5%">
                	<div class="qno"><?=$srno?></div><br/>
					<div class="<?=$cls?>"></div>
                </td>
                <td align='left'><?=(strpos($question->questionStem,"ADA_eqs") === false)?$question->getQuestion():$question->getQuestionForDisplay($longResponse,2)?><br/></td>
            </tr>
			<?php
			    if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
			?>
            <tr  bgcolor="">
                <td valign="top">&nbsp;</td>
                <td>
                	<table width="100%" border="0" cellspacing="2" cellpadding="3">

			    		<?php if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
			                <tr valign="top">
			                    <td width="5%" nowrap bgcolor='<?=$optiona_bgcolor?>' align="center" ><b>A</b></td>
			                    <td width="45%"><?php echo $question->getOptionA();?></td>
			                    <td width="5%"  nowrap bgcolor='<?=$optionb_bgcolor?>' align="center" ><b>B</b></td>
			                    <td width="45%"><?php echo $question->getOptionB();?></td>
			                </tr>
			    		<?php } ?>
			    		<?php if($questionType=='MCQ-4')    {    ?>
			                <tr valign="top">
			                    <td width="5%"  bgcolor='<?=$optionc_bgcolor?>' align="center"><b>C</b></td>
			                    <td width="45%"><?php echo $question->getOptionC();?></td>
			                    <td width="5%"  bgcolor='<?=$optiond_bgcolor?>' align="center"><b>D</b></td>
			                    <td width="45%"><?php echo $question->getOptionD();?></td>
			                </tr>
					    <?php } ?>
					    <?php if($questionType=='MCQ-3')    {    ?>
			                <tr valign="top">
			                    <td width="3%"  nowrap bgcolor='<?=$optiona_bgcolor?>' align="center"><b>A</b></td>
			                    <td width="30%"><?php echo $question->getOptionA();?></td>
			                    <td width="3%"  nowrap bgcolor='<?=$optionb_bgcolor?>' align="center"><b>B</b></td>
			                    <td width="30%"><?php echo $question->getOptionB();?></td>
			                    <td width="3%"  nowrap bgcolor='<?=$optionc_bgcolor?>' align="center"><b>C</b></td>
			                    <td width="30%"><?php echo $question->getOptionC();?></td>
			                </tr>
			            <?php } ?>
                	</table>
                </td>
            </tr>

			<?php } else if($correct_answer!="") { ?>
            <tr>
            	<td class=greytxtNormal colspan='5' align='center'>
            		<br><b>Correct answer : <?=$correct_answer?></b>
            	</td>
            </tr>
            <?php } ?>
        </table>
        <br/>
        <div align="center">[<span class="title">User Response</span>: <?=$user_ans?>&nbsp;&nbsp;&nbsp;&nbsp; <span class="title">Time Taken:</span> <?=$timeTaken?> secs]</div>
    </div>
    <hr/>
<?php    $srno++;}    ?>

</div>
<div style="display:none">
        <div id="openHelp">
			<h2 align="center">Quick Tutorial</h2>
            <iframe id="iframeHelp" width="960px" height="440px" scrolling="no"></iframe>
        </div>
    </div>
<div id="bottom_bar">
    <div id="copyright" data-i18n="[html]common.copyright"></div>
</div>
</body>
</html>
