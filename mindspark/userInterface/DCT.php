<?php
//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ERROR);
@include("check1.php");
if(!isset($_SESSION['userID']))
{
    echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
    exit;
}
$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
$userID = $_SESSION['userID'];
$sessionID = $_SESSION['sessionID'];
$childClass = $_SESSION["childClass"];
$childSection = $_SESSION["childSection"];
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
include("header.php") ?>

<title>Home</title>

<?php
if ($theme==1) {
?>
	<link href="css/question/lowerClass.css?ver=7" rel="stylesheet" type="text/css">
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
	<link href="css/common.css" rel="stylesheet" type="text/css">
	<link href="css/question/middleClass.css?ver=5" rel="stylesheet" type="text/css">
<?php } else if($theme==3) { ?>
	<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
	<link href="css/question/higherClass.css?ver=7" rel="stylesheet" type="text/css">
<?php }?>

<script type="text/javascript" src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/jquery_ui_touch.js"></script>
<script type="text/javascript">
var langType = '<?=$language;?>';
var arr = new Array();
var second = new Array();
var timeTaken = 0;

	function doContinue()
	{
			<?php
				//If this is preDCT then redirect to hidden Numbers Game by Submitting frmHiddenGame
				if(isset($_POST['redirect']) && $_POST['redirect'] == "1")
				{
			?>
				document.frmHiddenGame.submit();
			<?php
				}
				//If this is postDCT then redirect to Question stage by Submitting frmContinueQues
				else if(isset($_POST['redirect']) && ($_POST['redirect'] == "2" || $_POST['redirect'] == "3" || $_POST['redirect'] == "4"))
				{
			?>
				document.frmContinueQues.submit();
			<?php
				}
			?>
	}
	
function runTimer()
{
	timeTaken++;
	self.setTimeout("runTimer()",1000);
}
function checkAlreadyAttempted()
{
<?php
	if(isset($_POST['redirect']) && $_POST['redirect'] == "1")
	{
		$sql = "SELECT status FROM adepts_dctDetails WHERE ttAttemptID = '$ttAttemptID' AND status LIKE '%preDCT%'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
			echo "document.frmHiddenGame.submit();";
	}
	else if(isset($_POST['redirect']) && $_POST['redirect'] == "2")
	{
		$sql = "SELECT status FROM adepts_dctDetails WHERE ttAttemptID = '$ttAttemptID' AND status LIKE '%postDCT%'";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0)
			echo "document.frmContinueQues.submit();";
	}
?>
}
$(document).ready(function() { 
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		if (window.location.href.indexOf("localhost") > -1) {	
		    var langType = 'en-us';
		}
		$(".translation").i18n();
	});
	
	checkAlreadyAttempted();
	runTimer();
	/* see if anything is previously checked and reflect that in the view*/
	//Below line has browser compatibility issue...
	$("td").css("background","#999");
	$("td").css("border","1px solid #000000");

	/* handle the user selections */
	$(".checklist li").click(
		function(event) {
			$(this).siblings("li").removeClass("selected");
			event.preventDefault();
			$(this).addClass("selected");
			$(this).find(":radio").attr("checked","checked");
		}
	);

	$("#sendThis").click(function(){
			var preStr = "";
			var unAns = findUnanswered();
			if(unAns != 0)
				preStr = "You have not answered "+unAns+" questions. ";
	        var ans = confirm(preStr+"Are you sure you want to submit your responses?");
	        if(ans)
			    showErrors();
		});
	// Function for calculating corrects and showing red or green based on answers..
	function showErrors()
	{
		var answers = new Array(0,0,0,0,0,0,0);
		var radioName = "";
		var strArr = "";
		var i = 0;
		var resultStr = "";
		$('input[name^="jqdemo"]').each(function(){
				if(radioName == $(this).attr("name"))
				{
					strArr += ","+$(this).val();
					arr[i] = strArr;
					i++;
				}
				else
				{
					strArr = +$(this).val();
				}
				radioName = $(this).attr("name");
		});
		i=0;
		radioName = "";
		$('input[name^="jqdemo"]').each(function(){
				if(radioName != $(this).attr("name") && radioName != "")
					i++;
				radioName = $(this).attr("name");
				if($('input[name="'+radioName+'"]:checked').val() == Math.max.apply( Math, arr[i].split(",") ))
				{
					var questionTypeNo = $(this).attr("name").split("_");
					answers[questionTypeNo[1]] += 1;
					$(this).parents("td:first").css("background","url(assets/correct.png) repeat");
				}
				else
				{
					$(this).parents("td:first").css("background","url(assets/error.png) repeat");
				}
		});
		$("#sendThis").hide();
		for(var result=1;result<7;result++)
		{
			answers[result] /= 2;
			resultStr += answers[result]+",";
		}
		//Save Data completely in DCT Tables through AJAX
		resultStr = resultStr.substring(0, resultStr.length - 1);
		$.post('ajaxSaveDCT.php',{"userID":<?=$userID?>,"timeTaken":timeTaken,"dctStage":<?=$_POST['redirect']?>,"sessionID":<?=$sessionID?>,"attemptedDate":'<?=date("Y-m-d")?>',"results":resultStr,"ttAttemptID":<?=$ttAttemptID?>},function(data){
				//Redirect To Hidden Numbers Game
				if($.trim(data) == "true")
				{
					//Hide Submit function & display continue button..
					// Hide message that says to submit after attempting 30 Question & display Correct & Wrong flags..
					$("#sendThis").hide();
					$("#message1").hide();
					$("#continueButton").show();
					$("#message2").show();
					window.setTimeout("doContinue()",60000);
				}
				else
					alert("Some error occured while processing");
		});
	}
});


function findUnanswered()
{
	var counter = 0;
	var radioName = "";
	$('input[name^="jqdemo"]').each(function(){
		if(radioName == $(this).attr("name"))
			return;
		radioName = $(this).attr("name");
		if(!$("input[name="+radioName+"]:checked").val())
			counter++;
	});
	return(counter);
}
function logout()
{
   window.location="logout.php";
}
</script>
<style type="text/css">
h3{
	font-size:12px;
	margin-top:10px;
	cursor:pointer;
}
form {
	margin: 0 0 30px 0;
}
legend {
	font-size: 17px;
}
fieldset {
	border: 0;
}
tr  {
	padding-bottom: 4em;
}
.checklist {
	list-style: none;
	margin: 0;
	padding: 5px 0 0 0;
}
.checklist li {
	cursor: hand;
	float: left;
	margin-right: 5px;
	margin-left: 5px;
	background: url(assets/checkboxbg.png) no-repeat 0 0;
	width: 105px;
	height: 40px;
}
.checklist li.selected {
	background-position: -105px 0;
}
.checklist li.selected .checkbox-select {
	display: none;
}
.checkbox-select {
	display: block;
	float: left;
	position: absolute;
	top: 8px;
	left: 10px;
	width: 85px;
	height: 23px;
	text-indent: -9999px;
}
.checklist li input {
	display: none;
}
a.checkbox-deselect {
	display: none;
	color: white;
	font-weight: bold;
	text-decoration: none;
	position: absolute;
	top: 120px;
	right: 10px;
}
.checklist li.selected a.checkbox-deselect {
	display: block;
}

.checklist li label {
	display: block;
	text-align: center;
	padding: 4px;
}
.q1 {
	width: 49px;
	height: 50px;
	font-weight: bold;
	font-size: 14px;
	padding-top: 7px;
	padding-bottom: 10px;
}
.q1_inner {
	padding-top:10px;
	width: 35px;
	height: 25px;
	border-radius: 20px;
	background: #a1d4f5;
	background-image: -webkit-linear-gradient(top, #a1d4f5, #65bbf0);
	background-image: -moz-linear-gradient(top, #a1d4f5, #65bbf0);
	background-image: -ms-linear-gradient(top, #a1d4f5, #65bbf0);
	background-image: -o-linear-gradient(top, #a1d4f5, #65bbf0);
	background-image: linear-gradient(to bottom, #a1d4f5, #65bbf0);
}
#message1 {
	font-size: 20px;
    padding: 30px;
}
.button1 {
    background-color: transparent;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    border: 1px solid #2f99cb;
    display: inline-block;
    color: #2f99cb;
    font-size: 1.1em;
    margin-top: 10px;
    margin-left: 15px;
    padding: 6px 24px;
    text-decoration: none;
    cursor: pointer;
}

.button1:active {
    /*position:relative;
	top:4px;*/
    cursor: pointer;
}
#submit_bar {
	-webkit-transform: scale(1,0.85); /* Saf3.1+, Chrome */
	-moz-transform: scale(1,0.85); /* FF3.5+ */
	-ms-transform: scale(1,0.85); /* IE9 */
	-o-transform: scale(1,0.85); /* Opera 10.5+ */
	transform: scale(1,0.85);
	-webkit-transform-origin: left top;
	-moz-transform-origin: left top;
	-ms-transform-origin: left top;
	-o-transform-origin: left top;
	transform-origin: left top;
}

</style>
</head>
<body>
    <div id="top_bar" class="top_bar_part3">
        <div class="logo">
        </div>
        <?php if(in_array($_SESSION['schoolCode'],$budddySchoolArr) && $Android === false && $iPad === false && $IE === false) { ?>

        <div id="ichar">
            <canvas id="buddyCanvas" width="200px" height="240px">HTML5 canvas not supported</canvas>
            <div id="closeBtn">
                <a class="arrow" onClick="closeBtnFunc('hide')"> </a></div>
        </div>
        <div id="showBuddyDiv" onClick="closeBtnFunc('show')" style="display:none"></div>
        <?php } ?>
        <div id="studentInfoLowerClass" class="forLowerOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id="cssmenu">
                        <ul>
                            <li class="has-sub "><a href="javascript:void(0)"><span id="nameC"><?=$Name?>&nbsp;</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="studentInfoLowerClass" class="forHighestOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id="cssmenu">
                        <ul>
                            <li class="has-sub "><a href="javascript:void(0)"><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
									<li><a href='logout.php'><span data-i18n="common.logout">Logout</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>

        <div id="help" style="visibility:hidden;">
            <div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" class="linkPointer hidden" onClick="logout();">
            <div class="logout"></div>
            <div class="logoutText">Logout</div>
        </div>

        <div id="whatsNew" style="visibility:hidden;">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div> <!-- End top_bar -->


<div align="center" id="message1">Click on the greater number in each pair and submit after completing all 30 questions.</div>
<div align="center" id="message2" style="display:none;">
	<img src="assets/correct.png" height="15" width="35" style="margin-right:5px;" />
    <h4 style="display:inline;">Correct</h4>
    <img src="assets/error.png" height="15" width="35" style="margin-right:5px;margin-left:40px;" />
    <h4 style="display:inline;">Wrong</h4>
</div>
<div align="center">
<table>
<tr>
<?php

for($qNo=1;$qNo<31;$qNo++)
{
$questionStr = generateQuestion();
$questionArr = explode(",",$questionStr);

?>
	<!--<td align="center" title="Question Type : Group <?php echo $questionArr[2]; ?>">-->
	<td align="center">
		<div style="width:282px;text-align:center;margin:auto;">
        <div class="q1" name="q1" align="center" style="float:left;">
			<div class="q1_inner"><?=$qNo?></div>
		</div>
		<ul class="checklist">
			<li>
				<input name="jqdemo<?php echo $qNo; ?>_<?php echo $questionArr[2]; ?>" value="<?php echo $questionArr[0]; ?>" type="radio" id="<?php echo $qNo; ?>_a" />
				<label for="<?php echo $qNo; ?>_a"><h3><?php echo $questionArr[0]; ?></h3></label>
				<a class="checkbox-select" href="javascript:void(0)"></a>
			</li>
			<li>
				<input name="jqdemo<?php echo $qNo; ?>_<?php echo $questionArr[2]; ?>" value="<?php echo $questionArr[1]; ?>" type="radio" id="<?php echo $qNo; ?>_b" />
				<label for="<?php echo $qNo; ?>_b"><h3><?php echo $questionArr[1]; ?></h3></label>
				<a class="checkbox-select" href="javascript:void(0)"></a>
			</li>
		</ul>
		</div>
	</td>
<?php

if(($qNo)%3 == 0)
	echo "\n</tr>\n<tr>";
}
?>
</tr>
</table>
</div>
	<!--<div align="center">
    	<input type="button" class="submit" name="submitbutton" id="sendThis" value="" title="Submit">
    	<input type="image" src="assets/continue_button.png" id="continueButton" name="continueButton" onClick="doContinue();" style="display:none">
   </div>-->
<?php if($theme==1) { ?>   
   <div id="submit_bar1" class="forLowerOnly">
		<div id="sendThis" class="button">Submit</div>
		<div id="continueButton" class="button" onClick="doContinue();" style="display:none">Continue</div>
	</div>
<?php } else if($theme==2 || $theme==3) { ?>	
   <div id="submit_bar">
		<div id="sendThis" class="button">Submit</div>
		<div id="continueButton" class="button" onClick="doContinue();" style="display:none">Continue</div>
	</div>
<?php } ?>

<form name="frmContinueQues" id="frmContinueQues" action="question.php" method="post">
<input type="hidden" name="qode" id="qode" value="<?=$_SESSION['qcode']?>">
<?php
$qNo = isset($_SESSION['qno'])?$_SESSION['qno']:"1";
?>
<input type="hidden" name="qno" id="qno" value="<?=$qNo?>">
<input type="hidden" name="quesCategory" id="quesCategory" value="normal">
<input type="hidden" name="showAnswer" id="showAnswer" value="1">
</form>
<form name="frmHiddenGame" id="frmHiddenGame" action="enrichmentModule.php" method="post">
<input type="hidden" name="gameID" id="gameID" value="23">
<input type="hidden" name="gameMode" id="gameMode" value="DCTstage">
</form>
</body>
</html>
<?php
function generateQuestion()
{
	global $globalQuestionTypeCounter;
	$str = "";
	$questionType = "";
	$i = 0;
	while(1)
	{
		$questionType = getRandomDigit(1,6);
		if($globalQuestionTypeCounter[$questionType -1] != 5)
			break;
	}
	if($questionType == 1)
	{
		$str = getTypeOnePair();
	}
	else if($questionType == 2)
	{
		$str = getTypeTwoPair();
	}
	else if($questionType == 3)
	{
		$str = getTypeThreePair();
	}
	else if($questionType == 4)
	{
		$str = getTypeFourPair();
	}
	else if($questionType == 5)
	{
		$str = getTypeFivePair();
	}
	else if($questionType == 6)
	{
		$str = getTypeSixPair();
	}
	$str = randomizeOptions($str);
	$str .= ",".$questionType;
	$globalQuestionTypeCounter[$questionType-1] += 1;
	return($str);
}
function randomizeOptions($string)
{
	if(rand(1,9) > 5)
	{
		$arr = explode(",",$string);
		$temp = $arr[0];
		$arr[0] = $arr[1];
		$arr[1] = $temp;
		$string = implode(",",$arr);
	}
	return($string);
}
function getTypeSixPair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$A1 = getRandomDigit(3,9);
	$B1 = getRandomDigit(1,$A1-2);
	$pairA += $A1 / 10;
	$pairB += $B1 / 10;

	$A2 = getRandomDigit(1,8);
	$B2 = getRandomDigit($A2+1,9);
	$pairA += $A2 / 100;
	$pairB += $B2 / 100;

	$A3 = getRandomDigit(1,8);
	$B3 = getRandomDigit($A3+1,9);
	$pairA += $A3 / 1000;
	$pairB += $B3 / 1000;

	return("".$pairA.",".$pairB."");
}
function getTypeFivePair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$A1 = getRandomDigit(2,9);
	$B1 = getRandomDigit(1,$A1-1);
	$pairA += $A1 / 10;
	$pairB += $B1 / 10;

	$A2 = getRandomDigit(1,9);
	$B2 = getRandomDigit(1,9);
	$pairA += $A2 / 100;
	$pairB += $B2 / 100;

	return("".$pairA.",".$pairB."");
}
function getTypeFourPair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$A1 = $B1 = getRandomDigit(1,8);
	$pairA += $A1 / 10;
	$pairB += $B1 / 10;

	$A2 = $B2 = getRandomDigit(1,8);
	$pairA += $A2 / 100;
	$pairB += $B2 / 100;

	$A3 = getRandomDigit(2,4);
	$B3 = getRandomDigit(1,$A3-1);
	$pairA += $A3 / 1000;
	$pairB += $B3 / 1000;

	$A4 = getRandomDigit(1,9);
	$pairA += $A4 / 10000;

	return("".$pairA.",".$pairB."");
}
function getTypeThreePair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$A1 = getRandomDigit(1,8);
	$pairA += $A1 / 10;

	$A2 = getRandomDigit(1,9);
	$B2 = getRandomDigit($A1+1,9);
	$pairA += $A2 / 100;
	$pairB += $B2 / 100;

	$B3 = getRandomDigit(1,9);
	$pairB += $B3 / 1000;

	return("".$pairA.",".$pairB."");
}
function getTypeTwoPair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$i =0;
	while(1){
		$i++;
		if($i==100)
			break;
		$A1 = getRandomDigit(1,9);
		$B1 = getRandomDigit(1,9);
		if(!($A1 >= $B1 +1))
			continue;
		else

			$pairA += $A1 / 10;
			$pairB += $B1 / 10;

			$B2 = getRandomDigit(1,4);
			$A2 = getRandomDigit(1,9);
			$pairA += $A2 / 100;
			$pairB += $B2 / 100;

			$A3 = getRandomDigit(1,9);
			$pairA += $A3 / 1000;
			break;
	}
	return("".$pairA.",".$pairB."");
}
function getTypeOnePair()
{
	$pairA = 0;
	$pairB = 0;

	$B0 = $A0 = getRandomDigit(1,9);
	$pairA += $A0;
	$pairB += $B0;

	$i =0;
	while(1){
		$A1 = getRandomDigit(1,9);
		$B1 = getRandomDigit(1,9)+1;
		if($A1 > ($B1+1))
		{
			$pairA += $A1 / 10;
			$pairB += $B1 / 10;

			$A2 =  getRandomDigit(1,9);
			$B2 =  getRandomDigit(1,9);
			$pairA += $A2 / 100;
			$pairB += $B2 / 100;

			$B3 =  getRandomDigit(1,9);
			$pairB += $B3 / 1000;

			break;
		}
		else if($A1==($B1+1))
		{
			$pairA += $A1 / 10;
			$pairB += $B1 / 10;

			$A2 =  getRandomDigit(1,9);
			$B2 =  getRandomDigit(1,4);
			$pairA += $A2 / 100;
			$pairB += $B2 / 100;

			$B3 =  getRandomDigit(1,9);
			$pairB += $B3 / 1000;

			break;
		}
		$i++;
		if($i==100)
			break;
	}

	return("".$pairA.",".$pairB."");
}
function getRandomDigit($start,$end)
{
	return rand($start,$end);
}
?>