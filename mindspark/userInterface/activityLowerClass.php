<?php

include("check1.php");
include("constants.php");
$level  = 1;
if(isset($_REQUEST['level']))
	$level = $_REQUEST['level'];
$gameID = isset($_REQUEST['gameID'])?$_REQUEST['gameID']:8;

//$flashFile= GAMES_FOLDER."/Blackboard_number/Board_number_level".$level.".swf";

$qcode = $currQues = $quesCategory = $showAnswer = $tmpMode = "";
if(isset($_POST['qcode']))
{
	$qcode 		  = $_POST['qcode'];
	$currQues 	  = $_POST['qno'];
	$quesCategory = $_POST['quesCategory'];
	$showAnswer   = $_POST['showAnswer'];
	$tmpMode 	  = isset($_POST['tmpMode'])?$_POST['tmpMode']:"";
	$gameID		  = $_POST['gameID'];
}

$gameDetails = getFlashFileDetails($gameID);
$noOfLevels	=	$gameDetails["noOfLevels"];
$activityType	=	$gameDetails["type"];
$levelWiseMaxScores	=	$gameDetails["levelWiseMaxScores"];
$activityFormat	=	"old";
$gameAttempt_srno = "";

if($noOfLevels>0)
{
	$activityFormat	=	"new";
	if($gameDetails["type"]=="regular")
	{
		$previousLevelLock	=	1;
		$lastLevelClearedSrno	=	getLastAttemptData($gameID,$userID,$gameDetails["noOfLevels"],"regular");
		$lastLevelClearedSrnoArr	=	explode("~",$lastLevelClearedSrno);
		$lastLevelCleared	=	$lastLevelClearedSrnoArr[0];
		if($lastLevelCleared==-1)
		{
			$gameAttempt_srno = insertAttemptDetails($userID, $gameID, $sessionID);
			insertLevelDetails($gameAttempt_srno,$noOfLevels,$activityType);
			$lastLevelCleared=0;
		}
		else
		{
			$gameAttempt_srno	=	$lastLevelClearedSrnoArr[1];
		}
	}
	else
	{
		$previousLevelLock	=	0;
		$lastLevelCleared	=	getLastAttemptData($gameID,$userID,$noOfLevels,"optional");
		$gameAttempt_srno = insertAttemptDetails($userID, $gameID, $sessionID);
		insertLevelDetails($gameAttempt_srno,$noOfLevels, $activityType);
	}
	if($gameDetails["passingParam"]!="")
	{
		$inputParameter	=	"?".$gameDetails["passingParam"]."&";
		$inputParameter	.=	"noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
	else
	{
		$inputParameter	=	"?noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
	$flashFile = ENRICHMENT_MODULE_FOLDER."/html5/".$gameDetails["flashFile"].$inputParameter;
	$params="";
}
else
{
	$srcFile = ENRICHMENT_MODULE_FOLDER."/html5/".$gameDetails["flashFile"];
	$params = "?level=".$level;
	$pairsArray = array();
	$pairsArray[0][0] = 20;
	$pairsArray[0][1] = 40;
	$pairsArray[1][0] = 20;
	$pairsArray[1][1] = 50;
	$pairsArray[2][0] = 30;
	$pairsArray[2][1] = 50;
	$pairsArray[3][0] = 30;
	$pairsArray[3][1] = 60;
	$pairsArray[4][0] = 40;
	$pairsArray[4][1] = 60;
	$pairsArray[5][0] = 40;
	$pairsArray[5][1] = 70;
	$pairsArray[6][0] = 50;
	$pairsArray[6][1] = 70;
	$pairsArray[7][0] = 50;
	$pairsArray[7][1] = 80;
	$pairsArray[8][0] = 60;
	$pairsArray[8][1] = 80;
	
	$randomNo = rand(0,count($pairsArray)-1);
	
	if(isset($_REQUEST['pair_1']) && isset($_REQUEST['pair_2']))
	{
		$pair_1 = $_REQUEST['pair_1'];
		$pair_2 = $_REQUEST['pair_2'];
	}
	else
	{
		$pair_1 = $pairsArray[$randomNo][0];
		$pair_2 = $pairsArray[$randomNo][1];
	}
	
	$params .= "&lower=".$pair_1."&upper=".$pair_2;
}




//print_r($_POST);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="cache-control" charset="utf-8">
<title>Activities Detail Page</title>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/game.js"></script>
<script type="text/javascript"  src="libs/prototype.js"></script>

<link href="css/activitiesDetailPage/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<script>

function load(){
	checkGameCompleteHTML5();
	$('#clickText').html("Activities");
	$('#clickText').css("color","blue");
	$('#clickText').css("font-size","20px");
	var a= window.innerHeight -65;
	$('#activitiesContainer').css("height",a);
}

// Create IE + others compatible event handler
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
var activityFormat	=	'<?=$activityFormat?>';
var currentLevel	=	0;
var currentPos		=	0;

// Listen to message from child window
eventer(messageEvent,function(e) {
  //console.log('Message Received By Parent:  ',e.data);
  	var dataReceived = e.data;
	if(dataReceived.indexOf('#@') === -1)
	{
	  var arrData = dataReceived.split("||");
	}
	else
	{
		var arrData = dataReceived.split("#@");
	}
<?php if($activityFormat=="old") { ?> 
	var time = arrData[0];
	var score = arrData[2];
	var extraParams =  arrData[3];
	var levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
<?php } else { ?>
	var extraParams =  arrData[0];
	var levelsAttempted	=	arrData[2];
	var levelWiseStatus	=	arrData[3];
	var levelWiseScore	=	arrData[4];
	var levelWiseTimeTaken	=	arrData[5];
	var time,score;
<?php } ?>
	if(activityFormat=="new")
	{
		var arrLevel	=	levelsAttempted.split("|");
		var arrStatus	=	levelWiseStatus.split("|");
		var arrScore	=	levelWiseScore.split("|");
		var arrTime		=	levelWiseTimeTaken.split("|");
		var arrExtraParams	=	extraParams.split("|");
	}
	if(arrData[1]!=1)
	{
		self.setTimeout("checkGameCompleteHTML5()", 1000);
		if(activityFormat=="new") //if game is of new formate find current level data
		{
			if(arrStatus[currentPos]!=0 && currentLevel>0)
			{
				<?php if($userType!="msAsStudent")
							echo "saveGameResult(score, time, arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);";?>
			}
			if(arrLevel[j]!=0)
			{
				for(var j=0;j<arrLevel.length;j++)
				{
					if(arrStatus[j]==0)
					{
						currentPos		=	j;
						currentLevel	=	arrLevel[j].replace("L","");
					}
				}
			}
		}
		
		if(parseInt(time)%30==0)
		{
			if(activityFormat=="new")
				echo "saveGameResult(score,time,arrExtraParams[j],arrLevel[j],arrStatus[j],arrScore[j],arrTime[j],currentLevel);";
		}
	}
	else
	{
		if(activityFormat=="new")
		{
			document.getElementById('completed').value = "1";
			saveGameResult(score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel);
		}
		else
		{
			document.getElementById('totalScore').value = score;
			document.getElementById('timeTaken').value = time;
			document.getElementById('btnContinue').style.display="block";
			document.getElementById('pnlMsg').style.display="block";
			setInterval('blinkIt()',500);  	
			return false;		  
		}
	}
},false);


function  checkGameCompleteHTML5()
{
	//console.log("Sending Message");
	var frame = document.getElementById("iframe");
	var win = frame.contentWindow;
	try{
		win.postMessage("checkGameComplete",'*');
	}catch(ex){
		//console.log(ex);
		alert('error');
	}
}

function saveGameResultNew(score, time, extraParams,levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken,currentLevel)
{
	var params="";
	params += "mode=saveGameDetailsAjax";
	params += "&gameAttempt_srno=" + document.getElementById('gameAttempt_srno').value;
	params += "&gameID=" + document.getElementById('gameID').value;
	params += "&totalScore=" + score;
	params += "&timeTaken=" + time;
	params += "&extraParams=" + extraParams;
	params += "&levelsAttempted=" + levelsAttempted;
	params += "&levelWiseStatus=" + levelWiseStatus;
	params += "&levelWiseScore=" + levelWiseScore;
	params += "&levelWiseTimeTaken=" + levelWiseTimeTaken;
	params += "&currentLevel=" + currentLevel;
	params += "&activityFormat=<?=$activityFormat?>";
	params += "&ttAttemptID=" + document.getElementById('ttAttemptID').value;
	params += "&completed=" + document.getElementById('completed').value;
	params += "&gameMode=<?=$gameMode?>";
	params += "&type=<?=$activityType?>";
	if(document.getElementById('gameCode'))
	{
		params += "&gameCode=" + document.getElementById('gameCode').value;
	}
	//alert(params);
	var request = new Ajax.Request('saveGameDetailsAjax.php',
		{
			method:'post',
			parameters: params,
			onSuccess: function(transport)
			{
				resp = transport.responseText;
				//alert(resp);
				if(document.getElementById('completed').value==1)
				{
					redirect();
				}

			},
			onFailure: function()
			{
				//alert('Something went wrong while saving...');
			}
		}
		);
}
</script>

</head>
<body onload="load()" onresize="load();">
<form name="frmGame" id="frmGame" method="post" action="<?=$_SERVER['PHP_SELF']?>" autocomplete='off'>
    <input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
    <input type="hidden" name="qno" id="qno" value="<?=$currQues?>">
    <input type="hidden" name="refresh" id="refresh" value="0">
    <input type="hidden" name="quesCategory" id="quesCategory" value="<?=$quesCategory?>">
    <input type="hidden" name="showAnswer" id="showAnswer" value="<?=$showAnswer?>">
    <input type="hidden" name="gameID" id="gameID" value="<?=$gameID?>">
    <input type="hidden" name="mode" id="mode">
    <input type="hidden" name="level" id="level" value="<?=$level?>">
    <input type="hidden" name="pair_1" id="pair_1" value="<?=$pair_1?>">
    <input type="hidden" name="pair_2" id="pair_2" value="<?=$pair_2?>">
    <input type="hidden" name="timeTaken" id="timeTaken">
    <input type="hidden" name="totalScore" id="totalScore">
    <input type="hidden" name="attemptCount" id="attemptCount" value="0">
    <input type="hidden" id="completed" value="0" />
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="logout">
        	<div class="logout"></div>
        	<div class="logoutText">Logout</div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar">
			<div id="topic">
				<div id="home">
					<div class="icon_text1">HOME > <font color="#606062"> ACTIVITIES</font></div>
				</div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass?>
			</div>
			<div class="Name">
				<strong><?=$Name[0]." ".$Name[1]?></strong>
			</div>
			<div id="new">
				<div class="icon_text">END SESSION</div>
				<div id="pointed"></div>
			</div>
		</div>
        <div id="activitiesContainer" align="center">
            <div class="gameswf" id="flash" style="z-index:1">
                <iframe id="iframe" src="<?=$srcFile.$params?>" height="600px" width="800px" frameborder="0" scrolling="no"></iframe>
            </div>
            <div align="center"><a href="javascript:void(0)" name="btnContinue" id="btnContinue" class="continue" onClick="showNext();"></a></div>
        </div>
    </div>

	<div id="bottom_bar">
		<div id="copyright">© 2013 Educational Initiatives Pvt. Ltd.</div>
    </div>
    </form>
	
</body>
</html>

<?php 
function getFlashFileDetails($gameID)
{
    $gameDetails = array();
    $query  = "SELECT gameFile, ver, type, timeLimit, gameDesc, live, noOfLevels, levelWiseMaxScores, passingParam
				 FROM adepts_gamesMaster WHERE gameID=".mysql_real_escape_string($gameID);
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $gameDetails["flashFile"] = $line[0];
    $gameDetails["version"]   = $line[1];
    $gameDetails["type"]      = $line[2];
    $gameDetails["timeLimit"] = $line[3];
    $gameDetails["desc"] = $line[4];
	$gameDetails["live"] = $line[5];
	$gameDetails["noOfLevels"] = $line[6];
	$gameDetails["levelWiseMaxScores"] = $line[7];
	$gameDetails["passingParam"] = $line[8];
    return $gameDetails;
}
?>