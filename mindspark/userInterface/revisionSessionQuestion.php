<?php
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

@include("check1.php");
include("functions/orig2htm.php");
include("classes/clsQuestion.php");
include("functions/functionsForDynamicQues.php");
@include_once("constants.php");


if(isset($_POST['qcode']))
	$qcode = $_POST['qcode'];
else
{
	header("Location: logout.php");
	exit;
}
//$qcode = 5638;
//print_r($_SESSION);
if(!isset($_POST['qno']))
	$currQues = 1;
else
	$currQues = $_POST['qno'];

$childClass = $_SESSION['childClass'];
$childName  = $_SESSION['childName'];

if($childClass<3)
{

	$quesClass  = "fontLowerClass";
}
else
{
	$quesClass  = "fontHigherClass";
}
$buddy_id = isset($_SESSION['buddy'])?$_SESSION['buddy']:1;
$question = new Question($qcode);
if($question->isDynamic())
	$question->generateQuestion();
$encryptedCorrectAns  = encrypt($question->correctAnswer);
$encryptedDropDownAns = encrypt($question->dropDownAns);
$ttCode = $_POST['ttCode'];

$quesVoiceOver = "";
if($question->quesVoiceOver!="" && $childClass<3)
{
	$quesVoiceOver = VOICEOVER_FOLDER."/".substr($question->clusterCode,0,3)."/$question->quesVoiceOver";
}

$revisionSessionID= $_POST['revisionSessionID'];
$userID = $_SESSION['userID'];

if($revisionSessionID)
	{
		$query = "select count(srno) from adepts_revisionSessionDetails where revisionSessionID =$revisionSessionID and userID=$userID";
		$result = mysql_query($query) or die(mysql_error());
		$line = mysql_fetch_array($result);
	}	
	$remaningquestions = $_SESSION['questioncount'] - $line[0];

	if($remaningquestions < 0)
	{
		updateRevisionSessionStatus($userID, $revisionSessionID);
		echo '<form id="frmHidForm" action="revisionSessionReport.php" method="post">';
		echo '<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="'.$revisionSessionID.'">';
		echo '</form>';
		echo "<script>
				document.getElementById('frmHidForm').submit();
			  </script>";
	}

function updateRevisionSessionStatus($userID, $revisionSessionID)
{
	$query  = "SELECT SUM(IF(R=1,1,0)),count(srno) FROM adepts_revisionSessionDetails WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$correct   = $line[0];
	$totalQues = $line[1];
	$perCorrect = 0;
	if($totalQues>0)
		$perCorrect = round($correct*100/$totalQues,2);

	$noOfSparkies = 0;
	if($totalQues>=20)
	{
		if($perCorrect>=60 && $perCorrect<=74)
			$noOfSparkies = 4;
		elseif($perCorrect>=75 && $perCorrect<=89)
			$noOfSparkies = 6;
		elseif($perCorrect>=90)
			$noOfSparkies = 10;
	}
	$query = "UPDATE adepts_revisionSessionStatus SET status='completed', perCorrect=$perCorrect, noOfSparkies=$noOfSparkies WHERE revisionSessionID=$revisionSessionID AND userID=$userID";
	mysql_query($query) or die(mysql_error());

	$query = "UPDATE ".TBL_SESSION_STATUS." SET noOfJumps=$noOfSparkies WHERE userID=$userID AND sessionID=".$_SESSION['sessionID'];
	mysql_query($query);
}
?>
<?php include("header.php"); ?>

<title>Revision Session Question</title>
	<script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="libs/combined.js"></script>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>-->
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
	<!--<script src="libs/closeDetection.js"></script>-->	
	<script>
	var langType = '<?=$language;?>';
	</script>
	<?php
	if($theme==1) { ?>
	<link href="css/revisionSessionInstructions/lowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<?php } else if($theme==2){ ?>
	<link href="css/revisionSessionInstructions/midClass.css" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<?php } else { ?>
	<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
	<link href="css/revisionSessionInstructions/higherClass.css" rel="stylesheet" type="text/css">
	<?php } ?>
    <script language="JavaScript" src="libs/gen_validatorv31.js" type="text/javascript"></script>
    <script>
		var click=0;
    	function redirect()
		{
			setTryingToUnload();
			window.location.href	=	"myDetailsPage.php";
		}
		function getHome()
		{
			setTryingToUnload();
			window.location.href	=	"home.php";
		}
    </script>

<script type="text/javascript">
// set the starting datestamp;
var closeSession = true;
var result;
if(document.addEventListener)
{
	document.addEventListener("keydown", my_onkeydown_handler,true);
}
else if (document.attachEvent)
{
	document.attachEvent("onkeydown", my_onkeydown_handler)
}

function backButtonPressed(arg)
{
	if(arg == false && closeSession == true)
	{
		location.replace('error.php');
	}
	else if(arg == true || closeSession == false)
	return false;
}
var plstart = new Date();
var isIpad = false;
if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
{
    isIpad = true;
}
if(!isIpad)
{
    window.history.forward(1);    
}
function my_onkeydown_handler(ev)
{
	var ev = ev || window.event;
	switch (ev.keyCode)
	{
		case 13: //enter
		closeSession = false;
		break;
	}
}
</script>
<script type="text/javascript" src="libs/niftyplayer.js"></script>
<script type="text/javascript" src="libs/monthly_revision.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script>

document.onkeypress = checkEnterKeyPress;
if (document.layers) document.captureEvents(Event.KEYPRESS);
<!--

var message="Sorry, right-click has been disabled";
function clickIE() {if (document.all) {(message);return false;}}
function clickNS(e) {
        if(document.layers||(document.getElementById&&!document.all)) {
                if (e.which==2||e.which==3) {(message);return false;}
        }
}
if (document.layers)        {
        document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;
}
else        {
        document.onmouseup=clickNS;
        document.oncontextmenu=clickIE;
}
document.oncontextmenu=new Function("return false")
// -->
function playme(mode){
	try{
    	niftyplayer('niftyPlayer1').stop();
		var soundFile = "";
        if(mode=='Q')
            soundFile = document.getElementById('quesVoiceOver').value;
        else
            soundFile = document.getElementById('ansVoiceOver').value;

		niftyplayer('niftyPlayer1').loadAndPlay(soundFile);
    }catch(err){}
}

function checkImagesLoaded() {

    document.getElementById('btnSubmit').disabled=true;
    checkLoadingComplete();
    if(document.getElementById('quesVoiceOver').value!='')
        niftyplayer('niftyPlayer1').load(document.getElementById('quesVoiceOver').value);
    document.onselectstart = function () { return false; } // ie
    //document.onmousedown = function () { return false; } // mozilla
    setTimeout('logoff()', 600000);        //log off if idle for 10 mins
    // set the end date stamp
}

function checkAnswer()
{
	try {
        document.getElementById('btnSubmit').disabled=true;
        var quesType = document.getElementById('quesType').value;
        if(quesType=='D')
        {
            result = checkDropDownAns();
			document.getElementById('result').value = result;
			calcAnswer(result);
        }
        else if(quesType!='I')
		{
            result = evaluateResponse();
			document.getElementById('result').value = result;
			calcAnswer(result);
		}
        else
        {
			 if (typeof $("#quesInteractive").attr("src") != "undefined") {
                    try {
                        var frame = document.getElementById("quesInteractive");
                        var win = frame.contentWindow;
                        win.postMessage("checkAnswer", '*');
                    }
                    catch (ex) {
                        alert('error in getting the response from interactive');
                    }
                }
                else 
				{
		            var flashMovie=getFlashMovieObject("simplemovieQ");
		            var result = flashMovie.GetVariable("answer");
		            document.getElementById('result').value = result;	
					calcAnswer(result);
			    }
        }
    }
    catch(err)
    {
        alert("Unable to process your request. Please submit your answer again.");
        document.getElementById('btnSubmit').disabled=false;
    }	
}

function calcAnswer(result)
{
	if(result==2) {
		alert("Please specify your answer!");
		document.getElementById('btnSubmit').disabled=false;
		return false;
	}
	else {
		stopTheTimer();
		document.getElementById('secsTaken').value = secs;
		document.getElementById('refresh').value = 1;
		if(document.getElementById('mode'))
		{
			document.getElementById('mode').value = "revisionSession";
		}
		document.getElementById('btnSubmit').disabled=true;
		setTryingToUnload();
		document.getElementById('quesform').submit();
	}
}

function checkEnterKeyPress(e) {
    var keyPressed = e ? e.which : window.event.keyCode;
    if(keyPressed == 13)        //13 implies enter key
    {
        if(document.getElementById('result').value=="")
            checkAnswer();
    }
}

</script>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="makeQuestionVisible();checkImagesLoaded();"  class="translation content">
<div id="top_bar" class="top_bar_part4">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $childName ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='logout.php'><span data-i18n="common.logout">Logout</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class">Class</span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText">Logout</div>		
        </div>
    </div>
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<div id="homeIcon" style="cursor: default;"></div>
				<div class="icon_text2">- Revision Session</font></div>
				<div class="arrow-right" style='width:100%'><font style='color:red;padding-left: 215px;'><?php echo $remaningquestions; ?> Questions left to answer.</font></div>
				<div class="clear"></div>
			</div>
			<div id="topic">
				<div id="home">
				</div>
				<div class="icon_text1">HOME > <font color="#606062"> Revision Session</font></div>
				<div class="arrow-right" style='width:100%'><font style='color:red;padding-left: 215px;'><?php echo $remaningquestions; ?> Questions left to answer.</font></div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="dob" style="visibility:hidden">Date of Birth - <?=$childDob?>
			</div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Revision Session</span></div>
                </div>
				<div class="arrow-right" style='width:100%'><font style='color:red;padding-left:300px;'><?php echo $remaningquestions; ?> Questions left to answer.</font></div>
				<div class="clear"></div>
		</div>
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
			</div>
			<div id="topic_name"></div>
	<div id="formContainer">
		<form name="quesform" id="quesform" method="post" action="controller.php" autocomplete=off onSubmit="return checkSubmit();">
        <input type="hidden" name="qcode" id="qcode" value="<?=$question->qcode?>">
        <input type="hidden" name="correctAnswer" id="correctAnswer" value="<?=$encryptedCorrectAns?>">
        <input type="hidden" name="mode" id="mode" value="<?=$mode?>">
        <input type="hidden" name="quesType" id="quesType" value="<?=$question->quesType?>">
        <input type="hidden" name="secsTaken" id="secsTaken" value="">
        <input type="hidden" name="result" id="result" value="">
        <input type="hidden" name="qno" id="qno" value="<?=$currQues?>">
        <input type="hidden" name="clusterCode" id="clusterCode" value="<?=$question->clusterCode?>">
        <input type="hidden" name="refresh" id="refresh" value="0">
        <input type="hidden" name="quesVoiceOver" id="quesVoiceOver" value="<?=$quesVoiceOver?>">
		<input type="hidden" name="dropdownAns" id="dropdownAns" value="<?=$encryptedDropDownAns?>">
		<input type="hidden" name="userResponse" id="userResponse" value="">
		<input type="hidden" name="dynamicQues" id="dynamicQues" value="<?=$question->isDynamic()?>">
		<input type="hidden" name="dynamicParams" id="dynamicParams" value="<?=$question->dynamicParams?>">
		<input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
		<input type="hidden" name="sdl" id="sdl" value="<?=$sdl?>">
		<input type="hidden" name="revisionSessionID" id="revisionSessionID" value="<?=$revisionSessionID?>">
<span class='math' style='display:none;'>{1 \over 2}</span>
<div class="content">
	<div name="pnlQuestion" id="question">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td align="center" valign="top">
				<table width="100%" border="0" cellspacing="2" cellpadding="3">
	      			<tr>
	          			<td width="5%" align="center" valign="top" class="greyBorder">
	          				<p align="center" class="<?=$quesClass?>">
	          				<font size="3">
	          					<?php echo $currQues."."; ?>
	          				</font>
	          				<br/><br/>
	          				<?php   if(trim($quesVoiceOver)!="" && $childClass<3)	{      ?>
	      					<a href="javascript:playme('Q')"><img src="assets/play_btn.png" border=0 height='50px'></a>
	      					<?php		}	?>
	         				</p>
	         			</td>
	          			<td valign="top" align="left">
	                  		<p><span class="<?=$quesClass?> <?=$cls?>"><?php echo $question->getQuestion()?></span><br></p>
	                    </td>
	        		</tr>
	      		</table>
	      		<br/>
		     <?php
		     	if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-3' || $question->quesType=='MCQ-2')
		        {
		     ?>

				<table width="100%" border="0" cellspacing="2" cellpadding="3">
	            <?php        if($question->quesType=='MCQ-4' || $question->quesType=='MCQ-2')        {        ?>
	          		<tr valign="top">
						<td width="5%" align="center">&nbsp;</td>
			          	<td width="5%"><b>A</b><input type="radio" name="ansRadio" id="ansRadioA" value="A"></td>
			          	<td width="43%" class="<?=$quesClass?>" align="left" onClick="setOpt('A')"><?php echo $question->getOptionA();?></td>
			          	<td width="5%"><b>B</b><input type="radio" name="ansRadio" id="ansRadioB" value="B"></td>
			          	<td width="42%" class="<?=$quesClass?>" align="left" onClick="setOpt('B')"><?php echo $question->getOptionB();?></td>
	          		</tr>
	        	<?php        }        ?>
	        	<?php        if($question->quesType=='MCQ-4')        {        ?>
	          		<tr valign="top">
		          		<td width="5%" align="center" valign="top">&nbsp;</td>
		          		<td width="5%"><b>C</b><input type="radio" name="ansRadio" id="ansRadioC" value="C"></td>
		          		<td width="43%" class="<?=$quesClass?>" onClick="setOpt('C')"><?php echo $question->getOptionC();?></td>
		          		<td width="5%"><b>D</b><input type="radio" name="ansRadio" id="ansRadioD" value="D"></td>
		          		<td width="42%" class="<?=$quesClass?>" onClick="setOpt('D')"><?php echo $question->getOptionD();?></td>
	          		</tr>
	        	<?php        }        ?>
	        	<?php        if($question->quesType=='MCQ-3')        {        ?>
	          		<tr valign="top">
	              		<td width="5%" align="center">&nbsp;</td>
	              		<td width="5%"><b>A</b><input type="radio" name="ansRadio" id="ansRadioA" value="A"></td>
	              		<td width="28%" class="<?=$quesClass?>" onClick="setOpt('A')"><?php echo $question->getOptionA();?></td>
	              		<td width="5%"><b>B</b><input type="radio" name="ansRadio" id="ansRadioB" value="B"></td>
	          	  		<td width="26%" class="<?=$quesClass?>" onClick="setOpt('B')"><?php echo $question->getOptionB();?></td>
	          	  		<td width="5%"><b>C</b><input type="radio" name="ansRadio" id="ansRadioC" value="C"></td>
	              		<td width="26%" class="<?=$quesClass?>" onClick="setOpt('C')"><?php echo $question->getOptionC();?></td>
	          		</tr>
	        	<?php        }        ?>
	        	</table>
	     	<?php  }  ?>
				<div>
			    	<input type='button' class='buttonRevision' value='Submit' name='btnSubmit' id="btnSubmit" onClick='checkAnswer();' disabled<?php if(strpos($question->questionStem,"ADA_eqs")!==false) echo ' style="display: none;"';?>>
			    </div>
       		</td>
       	</tr>
	</table>
	</div>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="1" height="1" id="niftyPlayer1" align="">
		<param name=movie value="assets/niftyplayer.swf">
		<param name=quality value=high>
		<param name=bgcolor value=#FFFFFF>
		<embed src="assets/niftyplayer.swf" quality=high bgcolor=#FFFFFF width="0" height="0" name="niftyPlayer1" align="" type="application/x-shockwave-flash" swLiveConnect="true" pluginspage="http://www.macromedia.com/go/getflashplayer">
		</embed>
	</object>
</div>
</form>
	</div>
</div>


<div id="bottom_bar">
    <div id="copyright" data-i18n="[html]common.copyright"></div>
</div>

</body>
</html>

<?php
function encrypt($str_message) {
    $len_str_message=strlen($str_message);
    $Str_Encrypted_Message="";
    for ($Position = 0;$Position<$len_str_message;$Position++)        {
        $Byte_To_Be_Encrypted = substr($str_message, $Position, 1);
        $Ascii_Num_Byte_To_Encrypt = ord($Byte_To_Be_Encrypted);

               $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt + 5;
               $Ascii_Num_Byte_To_Encrypt = $Ascii_Num_Byte_To_Encrypt * 2;

        $Str_Encrypted_Message .= $Ascii_Num_Byte_To_Encrypt."-";
    }
    $Str_Encrypted_Message = substr($Str_Encrypted_Message,0,-1);
    return $Str_Encrypted_Message;
} //end function
?>
