<?php
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
//error_reporting(E_ERROR);

@include("check1.php");
include("constants.php");
include("functions/comprehensiveModuleFunction.php");
$debug = (isset($_REQUEST['debug']) && $_REQUEST['debug']!="")?$_REQUEST['debug']:false;
if($debug == false)
{
	if(!isset($_SESSION['userID']) || (!isset($_REQUEST['diagnosticTestID']) && !isset($_SESSION['diagnosticTest'])))
	{
	    echo "You are not authorised to access this page! (URL copy pasted in the browser!)";
	    exit;
	}
}
if(isset($_POST['qcode']))
{
	$nextQcode 	  = $_POST['qcode'];
	$currQues 	  = $_POST['qno'];
	$quesCategory = $_POST['quesCategory'];
	$showAnswer   = $_POST['showAnswer'];
	
}

$userID = $_SESSION['userID'];
$sessionID = $_SESSION['sessionID'];
$childClass	  =	$_SESSION['childClass'];

$Name = explode(" ",$_SESSION['childName']);
if(isset($_SESSION['diagnosticTest']))
	$diagnosticTestID = $_SESSION['diagnosticTest']; 
else
	$diagnosticTestID = $_REQUEST['diagnosticTestID'];

$sql = "SELECT title, description FROM adepts_diagnosticTestMaster WHERE diagnosticTestID='$diagnosticTestID' AND status=1";
$result = mysql_query($sql);
if(mysql_num_rows($result) == 0)
{
	echo "Test not found!!";
	header("location:home.php");
	exit;
}
else
{
	$row = mysql_fetch_array($result);
	$diagnosticTestName = $row[0];
	$diagnosticTestDescription = $row[1];
}
$totalQuestionArray = array();
$allQuestionArray = array();
$sql = "SELECT A.groupID, noOfQuestions FROM adepts_diagnosticTestGroupCond A, adepts_diagnosticTestQuestions B WHERE A.diagnosticTestID='$diagnosticTestID' AND A.groupID=B.groupID AND A.noOfQuestions<>0 GROUP BY A.groupID HAVING COUNT(qcode)<>0";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
	$totalQuestionArray[$row[0]] = $row[1];
}
$groupIDStr = implode(",",array_keys($totalQuestionArray));
$sql = "SELECT qcode, dynamic, groupID FROM adepts_diagnosticTestQuestions WHERE groupID IN ($groupIDStr)";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
	if(!is_array($allQuestionArray[$row[2]]))
		$allQuestionArray[$row[2]] = array();
	array_push($allQuestionArray[$row[2]],$row[0]);
	if($row[1] == "1")//Dynamic question can be generated maximum 5 times..
	{
		array_push($allQuestionArray[$row[2]],$row[0]);
		array_push($allQuestionArray[$row[2]],$row[0]);
		array_push($allQuestionArray[$row[2]],$row[0]);
		array_push($allQuestionArray[$row[2]],$row[0]);
	}
}
$generatedQuestionArray = generateRandomQuestions($totalQuestionArray,$allQuestionArray);
shuffle($generatedQuestionArray);
if(count($generatedQuestionArray) == 0)
{
	echo "Question generation failed..";
	exit();
}
include("classes/clsDiagnosticTestQuestion.php");
include("functions/orig2htm.php");
include("functions/functionsForDynamicQues.php");
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="cache-control" charset="utf-8">
        <title>Questions</title>
        <link href="css/question/common.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/mindspark/js/load.js"></script>
        <script type="text/javascript">
			var debug = '<?=$debug?>';
			var timeTaken = 0;
		</script>
        <?php 
        if ($theme==1) { ?>
            <link href="css/question/lowerClass.css" rel="stylesheet" type="text/css">
            <link href="css/common_lowerclass.css" rel="stylesheet" type="text/css">
            <script>$(".forHigherOnly").remove();</script>
<?php } else { ?>
            <link href="css/question/middleClass.css" rel="stylesheet" type="text/css">
            <link href="css/common.css" rel="stylesheet" type="text/css">
            <script>$(".forLowerOnly").remove();</script>
<?php } ?>
        <script type="text/javascript" src="libs/i18next.js"></script>
        <script type="text/javascript" src="libs/translation.js"></script>
        <script type="text/javascript" src="libs/diagnosticTest/diagnosticTestScript.js?ver=1"></script>

        <script>
            var langType = '<?=$language?>';
			var infoClick	=	0;
			function load() {
				 init();
			<?php if($theme==1) { ?>	
				var a= window.innerHeight - (170);
				$('#scroll').css("height",a+"px");
				$(".forHigherOnly").remove();
			<?php } else { ?>
				var a= window.innerHeight - (80 + 20 + 140 );
				$('#topicInfoContainer').css("height",a+"px");
				$(".forLowerOnly").remove();
			<?php } ?>	
			}
			function init()
			{
				setTimeout("logoff()", 600000);	//log off if idle for 10 mins
			}
			function logoff()
			{
				window.location="logout.php";
			}
			function hideBar(){
				if (infoClick==0){
					$("#hideShowBar").text("+");
					$('#topic_name').animate({'margin-top':'-10px'},600);
					$('#correct_bar').fadeOut(300);
					$('#endSession').fadeOut(300);
					$('#session').animate({'top':'44px','margin-left':'300px'},600);
					
					$('#info_bar').animate({'height':'60px'},600);
					var a= window.innerHeight -130 -27;
					$('#pnlQuestion').animate({'height':a},600);
					$('#scroll').css("height",a);
					var b= window.innerHeight -257 -200 -17 -140;
					var c= window.innerHeight - 80 - 137 - 80;
					$('#question').animate({'min-height':c+"px"},600);
					infoClick=1;
				}
				else if(infoClick==1){
					$("#hideShowBar").html("&ndash;");
					$('#correct_bar').fadeIn(600);
					$('#endSession').fadeIn(600);
					$('#session').animate({'top':'110px','margin-left':'10px'},600);
					$('#info_bar').animate({'height':'130px'},600);
					var a= window.innerHeight -210 -17;
					var b= window.innerHeight -257 -210 -17 -140;
					var c= window.innerHeight - 80 - 140 - 80 - 67;
					$('#pnlQuestion').animate({'height':a},600);
					$('#question').animate({'min-height':c+"px"},600);
					$('#scroll').animate({"height":a},600);
					infoClick=0;
				}
			}
        </script>
        
        <style>
        .correct {
			display:block;
			margin:auto;
			width:35px;
			margin-top:-3px;
			height:25px;
			background:url(assets/wrong_right.png) no-repeat -30px 0;
		}
		.wrong {
			display:block;
			margin:auto;
			width:25px;
			height:25px;
			background:url(assets/wrong_right.png) no-repeat 0 0;
		}
		<?php if($childClass<=3) { ?>
		#pnlQuestion {
			background: none repeat scroll 0 0 #FFFFFF;
			margin-top: 65px;
			width: 96%;
			padding-top:20px;
			padding-bottom:20px;
			display:block;
			-moz-border-radius: 20px;
			-webkit-border-radius: 20px;
			border-radius: 0px 20px 20px 20px;
		}
		<?php } ?>
		.button {
			position:static;
		}
		.button:active {
			position:static;
		}
        </style>
    </head>
    <body onresize="load();" onLoad="load();" class="translation">
        <span class='math' style='display:none'>{1 \over 2}</span>

        <div id="top_bar" class="top_bar_part4">
            <div class="logo">
            </div>

            <div id="help" style="visibility:hidden;">
                <div class="help"></div>
                <div class="helpText" data-i18n="common.help"></div>
            </div>
            <div id="logout" class="linkPointer" onClick="logoff();">
                <div class="logout"></div>
                <div class="logoutText" data-i18n="common.logout"></div>
            </div>
            <!--<div id="logout">
                <div class="logout">
                </div>
                <div class="logoutText">
                    <div id="nameDiv">
                        <div id='cssmenu'>
                            <ul>
                                <li class='has-sub '><a href='javascript:void(0)' onclick="endsession();"><span data-i18n="common.logout">&nbsp;&#9660;</span></a>
                                    <ul>
                                        <li><a href='javascript:void(0)'><span data-i18n="questionPage.endSession"  onclick="endsession();"></span></a></li>
                                        <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>-->
            <div id="whatsNew" style="visibility:hidden;">
                <div class="whatsNew"></div>
                <div class="whatsNewText" data-i18n="common.whatsNew"></div>
            </div>

            <div id="sparkieContainer">

                <div id="noOfSparkie">
                </div>
                <div class="sparkie">
                </div>
            </div>
        </div> <!-- End top_bar -->

        <div id="container">
            <div id="endSessionClick" class="linkPointer" onclick="endSession();">
                <div id="topicText" data-i18n="questionPage.endSessionText"></div>
                <div class="button1" data-i18n="common.yes" onclick="finalSubmit(1);"></div><br/>
                <div class="button1 textUppercase" onclick="cancel();" data-i18n="common.cancel"></div>
            </div>
            <div id="info_bar">
                <div id="lowerClassProgress">
                </div>
                <div id="topic">
                    <div id="topic_name"><?php echo $diagnosticTestName; ?></div>

                    <div id="session">
                        <span data-i18n="questionPage.session" class="labelText"></span><span style='color:#39a9e0' ><?php echo $sessionID; ?></span>
                    </div>
                </div>
                <?php if($quesCategory == "NCERT") { ?>
                <div id="topic_ncert" onclick="hideBar();">
                    <div id="home">
                        <div class="icon_text11">HOME > <font color="#606062"> NCERT EXERCISE > <?=$_POST['exerciseName']?></font></div>
                    </div>
                    <div id="commentError" data-i18n="questionPage.commentError">
                    </div>
                </div>
                <?php  } ?>
                <div id="student">
                </div>
                <div class="class">
                    <strong data-i18n="common.class"></strong> <?php echo $childClass; ?>
                </div>
                <div class="Name"><?php echo $Name[0] ?></div>
                
                <div id="endSession" class="endSession_blue" onclick="endSession();">
                    <div class="icon_text" data-i18n="questionPage.endSession"></div>
                    <div id="pointed">
                    </div>
                </div>
            </div>
            <div id="hideShowBar" class="forHigherOnly" onclick="hideBar();">-</div>
            <div id="pnlQuestion">
                <div id="scroll">
                    <div id="question">
						<?php
        $Number = 0;
        $allCA = "";
        $allDDA = "";
        $allQT = "";
        $correct_answer = "";
        $dropdown_ans = "";
        $answerToShowAll = "";
        foreach($generatedQuestionArray as $id=>$detailArr)
        {
            $groupID = $detailArr[1];
            $qcode = $detailArr[0];
            $sql = "SELECT groupNo FROM adepts_groupInstruction WHERE groupID=$groupID";
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $groupNo = $row[0];
            $objQuestion = new diagnosticTestQuestion($qcode);
            if($objQuestion->isDynamic())
                $objQuestion->generateQuestion();
                
            $correct_answer = encrypt($objQuestion->correctAnswer);
            $dropdown_ans = encrypt($objQuestion->dropDownAns);
            $displayAnswer = encrypt($objQuestion->getDisplayAnswer());
            $question_type  = $objQuestion->quesType;
            $allCA .= $correct_answer."##";
            $allDDA .= $dropdown_ans."##";
            $allQT .= $question_type."##";
            if($question_type=='MCQ-4' || $question_type=='MCQ-3' || $question_type=='MCQ-2')
                $randomOptions = randomizeOptions($objQuestion);
            $Number++;
        ?>
                <div class="singleQuestion column1" data-group-no="<?=$groupNo?>" data-qcode="<?=$qcode?>" data-display-ans="<?=$displayAnswer?>" title="<?php if($debug == true) echo "GroupNo:$groupNo, QCode:$qcode";?>">
                <table width="100%" border="0" cellspacing="1" cellpadding="3">
                    <tr>
                        <td width="40px">
                            <div></div>
                        </td>
                        <td width="3%" align="left" valign="top">
                            <div id="q1" name="q1" align="center"><?=$Number?></div>
                        </td>
                        <td valign="top" align="left">
                            <p><div id="quesDetail" class="quesDetails"><?php echo $objQuestion->getQuestion()?></div></p>
                        </td>
                    </tr>
                </table>
                 <?php
                    if($question_type=='MCQ-4' || $question_type=='MCQ-3' || $question_type=='MCQ-2')
                    {
                 ?>
    
                  <table width="100%" border="0" cellspacing="2" cellpadding="3">
                    <?php	if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{	?>
                    <tr valign="top">
                      <td align="center" class="blankSpace">&nbsp;</td>
                      <td width="6%" nowrap><b>A</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioA<?=$Number?>" value="<?=$randomOptions[0][0]?>"></td>
                      <td width="43%" align="left" valign="middle"><label class="quesDetails" for="ansRadioA<?=$Number?>"><?=$randomOptions[0][1]?></label></td>
                      <td width="6%" nowrap><b>B</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioB<?=$Number?>" value="<?=$randomOptions[1][0]?>"></td>
                      <td width="42%" align="left" valign="middle"><label class="quesDetails" for="ansRadioB<?=$Number?>"><?=$randomOptions[1][1]?></label></td>
                    </tr>
                    <?php	}	?>
                    <?php	if($question_type=='MCQ-4')	{	?>
                    <tr valign="top">
                      <td align="center" valign="top" class="blankSpace">&nbsp;</td>
                      <td width="6%" nowrap><b>C</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioC<?=$Number?>" value="<?=$randomOptions[2][0]?>"></td>
                      <td width="43%" valign="middle"><label class="quesDetails" for="ansRadioC<?=$Number?>"><?=$randomOptions[2][1]?></label></td>
                      <td width="6%" nowrap><b>D</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioD<?=$Number?>" value="<?=$randomOptions[3][0]?>"></td>
                      <td width="42%" valign="middle"><label class="quesDetails" for="ansRadioD<?=$Number?>"><?=$randomOptions[3][1]?></label></td>
                    </tr>
                    <?php	}	?>
                    <?php	if($question_type=='MCQ-3')	{	?>
                    <tr valign="top">
                      <td align="center" class="blankSpace">&nbsp;</td>
                      <td width="6%" nowrap><b>A</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioA<?=$Number?>" value="<?=$randomOptions[0][0]?>"></td>
                      <td width="28%" valign="middle"><label class="quesDetails" for="ansRadioA<?=$Number?>"><?=$randomOptions[0][1]?></label></td>
                      <td width="6%" nowrap><b>B</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioB<?=$Number?>" value="<?=$randomOptions[1][0]?>"></td>
                      <td width="26%" valign="middle"><label class="quesDetails" for="ansRadioB<?=$Number?>"><?=$randomOptions[1][1]?></label></td>
                      <td width="6%" nowrap><b>C</b><input type="radio" name="ansRadio_<?=$Number?>" id="ansRadioC<?=$Number?>" value="<?=$randomOptions[2][0]?>"></td>
                      <td width="26%" valign="middle"><label class="quesDetails" for="ansRadioC<?=$Number?>"><?=$randomOptions[2][1]?></label></td>
                    </tr>
                    <?php	}	?>
                  </table>
        <?php
                /*if($Number % $groupColumn == 0)
                    echo '<div style="clear:both;"></div>';*/
                    }
                echo '</div>';
        }
        if(strlen($correct_answer) > 2)
            $correct_answer = substr($correct_answer,0,strlen($correct_answer)-2);
        if(strlen($question_type_all) > 2)
            $question_type_all = substr($question_type_all,0,strlen($question_type_all)-2);
        if(strlen($dropdown_ans) > 2)
            $dropdown_ans = substr($dropdown_ans,0,strlen($dropdown_ans)-2);
        ?>
                    </div> <!-- end question div -->
                    <div id="submit_bar" class="top_bar_part4 forHigherOnly">
                    
                        <div id="arrow" onclick="top1()"></div>
                        
                        <div id="submitBtn" class="button" onClick="return submitAnswers();" data-i18n="common.submit"></div>
                        <div id="continueBtn" class="button" onClick="redirect();" data-i18n="common.continue" style="display:none"></div>
                    </div>
                    <div id="submit_bar1">
                    <div id="arrow" onclick="top1()"></div>
                    <div id="submitBtn" class="button" onClick="return submitAnswers();" data-i18n="common.submit"></div>
                    <div id="continueBtn" class="button" onClick="redirect();" data-i18n="common.continue" style="display:none"></div>
                </div>
                </div> <!-- end scroll div -->
                
            </div> <!-- end pnlQuestion div -->
            <div id="pnlLoading" name="pnlLoading" style="display:none;height:300px">
                <div align="center" class="quesDetails"><br/><br/><br/><br/><p>Loading, please wait...<br/><img src="assets/loader.gif"></p></div>
            </div>
        </div>    <!-- end container div -->
		<form id="diagnosticTest" name="diagnosticTest" method="post" autocomplete='off' onkeypress="return checkEnter(event)">
            <input type="hidden" name="nextQcode" id="nextQcode" value="<?=$nextQcode?>">
            <input type="hidden" name="qno" id="qno" value="<?=$currQues?>">
            <input type="hidden" name="quesCategory" id="quesCategory" value="comprehensive">
            <input type="hidden" name="showAnswer" id="showAnswer" value="<?=$showAnswer?>">
            <input type="hidden" name="remedialMode" id="remedialMode" value="0">
            <input type="hidden" name="timedtestMode" id="timedtestMode" value="0">
            <input type="hidden" name="timedTest" id="timedTest" value="0">
            <input type="hidden" name="activityMode" id="activityMode" value="0">
            <input type="hidden" name="clusterMode" id="clusterMode" value="0">
            <input type="hidden" name="gameID" id="gameID" value="">
            <input type="hidden" name="qcode" id="qcode" value="0">
            <input type="hidden" name="mode" id="mode">
    	</form>        
            
            <input type="hidden" name="correctAnswer" id="correctAnswer" value="<?=$allCA?>">
            <input type="hidden" name="quesType" id="quesType" value="<?=$allQT?>">
            <input type="hidden" name="result" id="result" value="">
            <input type="hidden" name="dropdownAns" id="dropdownAns" value="<?=$allDDA?>">
            <input type="hidden" name="userResponse" id="userResponse" value="">
            <input type="hidden" name="dynamicQues" id="dynamicQues" value="">
            <input type="hidden" name="dynamicParams" id="dynamicParams" value="">
            <input type="hidden" name="diagnosticTestID" id="diagnosticTestID" value="<?=$diagnosticTestID?>">
<?php
include("footer.php");

function randomizeOptions($objQuesition)
{
	$tempArray = array();
	$question_type = $objQuesition->quesType;
	$tempArray[] = array("A",$objQuesition->getOptionA());
	$tempArray[] = array("B",$objQuesition->getOptionB());
	if($question_type=='MCQ-3' || $question_type=='MCQ-4')
	{
		$tempArray[] = array("C",$objQuesition->getOptionC());
	}
	if($question_type=='MCQ-4')
	{
		$tempArray[] = array("D",$objQuesition->getOptionD());
	}
	shuffle($tempArray);
	return($tempArray);
}
function generateRandomQuestions($totalQuestionArray,$allQuestionArray)
{
	$generatedQuestionArray = array();
	foreach($totalQuestionArray as $groupID=>$maxQuestions)
	{
		$randomKeys = array_rand($allQuestionArray[$groupID],$maxQuestions);
		if(is_array($randomKeys))
		{
			foreach($randomKeys as $key)
			{
				/*if(!is_array($generatedQuestionArray[$groupID]))
					$generatedQuestionArray[$groupID] = array();*/
				array_push($generatedQuestionArray,array($allQuestionArray[$groupID][$key],$groupID));
			}
		}
		else
		{
			array_push($generatedQuestionArray,array($allQuestionArray[$groupID][$randomKeys],$groupID));
		}
	}
	return($generatedQuestionArray);
}
function findGroupNo($groupID)
{
	$sql = "SELECT groupNo FROM adepts_groupInstruction WHERE groupID='$groupID'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	return $row['groupNo'];
}
function encrypt($str_message) {
    $len_str_message=strlen($str_message);
    $Str_Encrypted_Message="";
    for ($Position = 0;$Position<$len_str_message;$Position++)	{
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