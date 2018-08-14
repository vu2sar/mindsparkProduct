<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="cache-control" charset="utf-8">
<title>Activities Detail Page</title>
<?php if($cls<=3) { ?>
<link href="css/activitiesDetailPage/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		$('#clickText').html("Activities");
		$('#clickText').css("color","blue");
		$('#clickText').css("font-size","20px");
		var a= window.innerHeight -65;
		$('#activitiesContainer').css("height",a);
	}
</script>
<?php } else { ?>
<link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
<link href="css/activitiesDetailPage/midClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		var a= window.innerHeight -230;
		$('#activitiesContainer').css("height",a);
	}
</script>
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>


</head>

<body onload="load()" onresize="load();">
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
				<div id="pointed">
				</div>
			</div>
		</div>
	<div id="activitiesContainer" align="center">
		<form name="frmEnrichmentModule" id="frmEnrichmentModule" action="" method="post" autocomplete="off">
            <input type="hidden" id="ttAttemptID" value="<?php echo $ttAttemptID; ?>" /><?php
            if(isset($gameCode) && $gameCode!= "")
            { ?>
                <input type="hidden" id="gameCode" value="<?php echo $gameCode; ?>" />
            <?php  }
            if($_SESSION["comprehensiveModule"]!="") { ?>
                <input type="hidden" name="mode" id="mode" value="comprehensiveAfterActivity" />
            <?php } ?>
            <input type="hidden" id="completed" value="-1" />
            <input type="hidden" name="gameID" id="gameID" value="<?=$gameID?>">
            <input type="hidden" name="swfversion" id="swfversion" value="<?=$activityDetails["version"]?>">
            <input type="hidden" name="gameType" id="gameType" value="<?=$activityDetails["type"]?>">
            <input type="hidden" name="activityAttempt_srno" id="activityAttempt_srno" value="<?=$activityAttempt_srno?>">    
            
<?php if($allowed==1)
	  { ?>
        	<div align="center" id="askFeedbackDiv">
                <h3>Do you want to give feedback on this game ?</h3>
                <input type="button" name="feedbackYes" value="Yes" id="feedbackYes" />
                <input type="button" name="feedbackNo" value="Skip" id="feedbackNo" />
            </div>
            <br /> 
			<?php $userType="msAsStudent"; if($userType=="msAsStudent") { ?>
        <div id="devParameter">
        	<h5>Parameters</h5>
			<?php if($activityFormat=="old") { ?>
                <span><b>Total Score:</b></span>&nbsp;<span id="devScore">0</span><br>
                <span><b>Time Taken:</b></span>&nbsp;<span id="devTime">0</span><br>
            <?php } else { ?>
                <span><b>Levels Attempted:</b></span>&nbsp;<span id="devlevelsAttempted">0</span><br>
                <span><b>Level Wise Status:</b></span>&nbsp;<span id="devlevelWiseStatus">0</span><br>
                <span><b>Level Wise Score:</b></span>&nbsp;<span id="devlevelWiseScore">0</span><br>
                <span><b>Level Wise Time Taken:</b></span>&nbsp;<span id="devlevelWiseTimeTaken">0</span><br>
            <?php } ?>    
                <span><b>Extra Params:</b></span>&nbsp;<span id="devExtra">0</span><br>
                <span><b>Completed:</b></span>&nbsp;<span id="devComplete">0</span><br><br><br>
            <?php
            if(($_SESSION["userType"]=="msAsStudent" || $userType=="msAsStudent") && $activityDetails["live"]=="Live") { ?>
                <span><input type="button" onclick="showTagBox('tagMsgBox', '', '<?=$gameID?>');" value="Need to modify" id="tagtoModify" name="tagModify"></span>
            <?php } ?>
        </div>
	<?php } ?>

        	<div class="gameswf" id="flash" style="z-index:1">
			<?php if($activityDetails["version"]=="html5") { ?>
                <iframe id="iframe" src="<?=$flashFile.$gameCode?>" height="<?=$height?>px" width="<?=$width?>px" frameborder="0" scrolling="no"></iframe>
            <?php } else  {?>
                <OBJECT id="activitySwf" height="<?=$height?>px" width="<?=$width?>px" classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
                    <param name="movie" value="<?=$flashFile.$gameCode?>">
                    <PARAM NAME="quality" VALUE="high">
                    <param name="allowScriptAccess" value="always" />
                    <param name="wmode" value="transparent" />
                    <PARAM name='menu' VALUE='false'>
                    <EMBED src="<?=$flashFile.$gameCode?>" swliveconnect="true" wmode="transparent" menu='false' quality=high allowScriptAccess="always" WIDTH="<?=$width?>px" HEIGHT="<?=$height?>px" NAME="activitySwf" id="activitySwf" ALIGN="center" type="application/x-shockwave-flash"></EMBED>
                </OBJECT>
            <?php } ?>
        </div>
        
			<?php if($userID!=""){ ?>
<div class="feedBackForm" align="center">
	<!--<a href="javascript:void(0)" id="gameCommentFormLink">Add comment</a>-->
    <div id="gameCommentForm" style="display:none;">
<?php
//If feedback is not given on this game...
$sql = "SELECT COUNT(qid) FROM adepts_feedbackresponse WHERE userID='$userID' AND type='$gameID' GROUP BY qid";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$rows = $row[0];
if($rows < 3)
{
	$feedBackBool = "false";
	/*echo $sql;
	echo $rows;*/
?>
	<?php if($userObj->childClass < 6) { $formType="type1"; ?>
    <br><br>
	<h4 class="feedbackHeader">Had fun playing the game</h4>
    <p style="margin-left:20px;">
        <table cellpadding="1" cellspacing="4" class="funRatingTable radioEnabled">
            <tr>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="checkbox" class="radio" name="funRadio" value="Yes" id="funRadio_0" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel" for="funRadio_0">Yes</label>
                </td>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="checkbox" class="radio" name="funRadio" value="No" id="funRadio_1" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel" for="funRadio_1">No</label>
                </td>
            </tr>
        </table>
    </p>
	<h4 class="feedbackHeader">Write about the game.</h4>
    	<textarea name="gameFeedbackText1" id="gameFeedbackText1" class="gameFeedbackText" rows="4" data-maxsize="250" wrap="virtual"style="width:85%; border:none;"></textarea>
        <br>
    	<input name="gameFeedbackButton" id="gameFeedbackButton" class="<?=$formType?>" src="images/submit_feedback.png" type="image" value="Submit" />
	<?php } else { $formType="type2"; ?>
	<h3 align="center">Add comment</h3>
	<table class="ratingTable radioEnabled" cellpadding="1" cellspacing="0">
      <tr>
        <th scope="col" class="rowTH">&nbsp;</th>
        <th scope="col" class="colTH">Poor</th>
        <th scope="col" class="colTH">Bad</th>
        <th scope="col" class="colTH">Average</th>
        <th scope="col" class="colTH">Good</th>
        <th scope="col" class="colTH">Excellent</th>
      </tr>
     <!-- <tr>
        <th scope="row" class="rowTH">Game Concept</th>
        <td>&nbsp;<input name="gameConcept" type="checkbox" id="gameConceptPoor" class="radio" value="Poor" />&nbsp;</td>
        <td>&nbsp;<input name="gameConcept" type="checkbox" id="gameConceptBad" class="radio" value="Bad" />&nbsp;</td>
        <td>&nbsp;<input name="gameConcept" type="checkbox" id="gameConceptAverage" class="radio" value="Average" />&nbsp;</td>
        <td>&nbsp;<input name="gameConcept" type="checkbox" id="gameConceptGood" class="radio" value="Good" />&nbsp;</td>
        <td>&nbsp;<input name="gameConcept" type="checkbox" id="gameConceptExcellent" class="radio" value="Excellent" />&nbsp;</td>
      </tr>-->
      <tr class="odd">
        <th scope="row" class="rowTH">Design/Sound</th>
        <td>&nbsp;<input name="designSound" type="checkbox" id="designSoundPoor" class="radio" value="Poor" />&nbsp;</td>
        <td>&nbsp;<input name="designSound" type="checkbox" id="designSoundBad" class="radio" value="Bad" />&nbsp;</td>
        <td>&nbsp;<input name="designSound" type="checkbox" id="designSoundAverage" class="radio" value="Average" />&nbsp;</td>
        <td>&nbsp;<input name="designSound" type="checkbox" id="designSoundGood" class="radio" value="Good" />&nbsp;</td>
        <td>&nbsp;<input name="designSound" type="checkbox" id="designSoundExcellent" class="radio" value="Excellent" />&nbsp;</td>
      </tr>
      <!--<tr>
        <th scope="row" class="rowTH">Fun Factor</th>
        <td>&nbsp;<input name="funFactor" type="checkbox" id="funFactorPoor" class="radio" value="Poor" />&nbsp;</td>
        <td>&nbsp;<input name="funFactor" type="checkbox" id="funFactorBad" class="radio" value="Bad" />&nbsp;</td>
        <td>&nbsp;<input name="funFactor" type="checkbox" id="funFactorAverage" class="radio" value="Average" />&nbsp;</td>
        <td>&nbsp;<input name="funFactor" type="checkbox" id="funFactorGood" class="radio" value="Good" />&nbsp;</td>
        <td>&nbsp;<input name="funFactor" type="checkbox" id="funFactorExcellent" class="radio" value="Excellent" />&nbsp;</td>
      </tr>-->
      <tr class="odd">
        <th scope="row" class="rowTH">Interesting</th>
        <td>&nbsp;<input name="interesting" type="checkbox" id="interestingPoor" class="radio" value="Poor" />&nbsp;</td>
        <td>&nbsp;<input name="interesting" type="checkbox" id="interestingBad" class="radio" value="Bad" />&nbsp;</td>
        <td>&nbsp;<input name="interesting" type="checkbox" id="interestingAverage" class="radio" value="Average" />&nbsp;</td>
        <td>&nbsp;<input name="interesting" type="checkbox" id="interestingGood" class="radio" value="Good" />&nbsp;</td>
        <td>&nbsp;<input name="interesting" type="checkbox" id="interestingExcellent" class="radio" value="Excellent" />&nbsp;</td>
      </tr>
      <tr>
        <th scope="row" class="rowTH">Useful for Maths</th>
        <td>&nbsp;<input name="maths" type="checkbox" id="mathsPoor" class="radio" value="Poor" />&nbsp;</td>
        <td>&nbsp;<input name="maths" type="checkbox" id="mathsBad" class="radio" value="Bad" />&nbsp;</td>
        <td>&nbsp;<input name="maths" type="checkbox" id="mathsAverage" class="radio" value="Average" />&nbsp;</td>
        <td>&nbsp;<input name="maths" type="checkbox" id="mathsGood" class="radio" value="Good" />&nbsp;</td>
        <td>&nbsp;<input name="maths" type="checkbox" id="mathsExcellent" class="radio" value="Excellent" />&nbsp;</td>
      </tr>
    </table>
	<br><br>
    <h4 class="feedbackHeader">Suggestions/Comments :</h4>
    <p>
    	<textarea name="gameFeedbackText2" id="gameFeedbackText2" class="gameFeedbackText" rows="4" style="width:400px;"></textarea>
    </p>
    <p align="center">
    	<input name="gameFeedbackButton" id="gameFeedbackButton" class="<?=$formType?>" type="button" value="Submit">
    </p>
	<?php } ?>
<?php
}
else
{
	$feedBackBool = "true";
?>
   	  <h4>You already commented 3 times!</h4>
<?php
}
?>
</div>
</div>
<?php
} ?>
        
<?php  } else  { ?>
    		<div align="center">You seem to have crossed the maximum time allowed for the activities. Please go to a topic and continue.</div>
<?php  } ?>
		</form>
	</div>

    <div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
        <table>
            <tr><td><span id="showTaggedQcode"></span><br><strong>Comment:</strong></td></tr>
            <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
            <tr><td align="center"><input type="submit" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onclick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
        </table>
    </div>
    
    <form name="frmContinueQues" id="frmContinueQues" action="<?php if($gameMode=="researchModule") echo "researchModule.php"; else echo "question.php";?>" method="post">
        <input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['qcode']?>">
        <?php $qNo = isset($_SESSION['qno'])?$_SESSION['qno']:"1"; ?>
        <input type="hidden" name="qno" id="qno" value="<?=$qNo?>">
        <input type="hidden" name="quesCategory" id="quesCategory" value="normal">
        <input type="hidden" name="showAnswer" id="showAnswer" value="1">
    </form>

	<div id="bottom_bar">
		<div id="copyright">© 2013 Educational Initiatives Pvt. Ltd.
		</div>
    </div>
	<script type="text/javascript">
		var feedbackBool = <?=($feedBackBool !="")?$feedBackBool:'null';?>;
	</script>
</body>
</html>