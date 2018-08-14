<?php

	include("header.php");
	include("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");

    $userID = $_SESSION['userID'];
	$sessionID	= $_SESSION['sessionID'];
	
	$query = "insert into trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values ($userID,$sessionID,39,now())";
	mysql_query($query) or die(mysql_error());

	include("../slave_connectivity.php");
		
    if(!isset($_REQUEST['ttCode']) || !isset($_SESSION['userID']))
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    $ttCode = $_REQUEST['ttCode'];

	$query = "SELECT teacherTopicDesc, mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	$teacherTopicDesc = $line[0];
	$topicCode = $line[1];

	$cls = "";
	if(isset($_REQUEST['cls']))
		$cls = $_REQUEST['cls'];
	$showAll = isset($_POST['showAll'])?$_POST['showAll']:0;
	$flow = isset($_REQUEST['flow'])?$_REQUEST['flow']:"";
	if($flow=="MSOld")
		$flow = "";
	$clusterArray = array();
?>
<!DOCTYPE html>
<html>
<head>
<title>Sample Questions</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/sampleQuestions.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script src="../userInterface/libs/jquery.raty.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>


<script>
	var starClicked = 0;
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
	function showAllQues()
	{
		//document.getElementById('cls').value='';
		document.getElementById('showAll').value = "1";
		setTryingToUnload();
		document.getElementById('frmSampleQues').submit();
	}
	$(document).ready(function() {
	    $("#download").live("click",function(){

			$.ajax({
				url: "ajaxRequest.php",
				data: "pageId=80&mode=doasMindsparkSampleQuestion",
				type: "POST",
				async: false,
				success: function(data){
				}
			});


			if($("#forceFlow").val()=="")
			{
				alert("Sorry not able to start the session.");
				return false;
			}
			$("#mode").val("ttSelection");
			$("#userType").val("teacherAsStudent");
			$("#mindsparkTeacherLogin").attr("action", "../userInterface/controller.php");
			setTryingToUnload();
			$("#mindsparkTeacherLogin").submit();	
		});

		
});

function displayAnswerRatingForm()
{
	if($('#toDisplayAnswer').prop('checked'))
		    $(".answer").show();
	else 
	    $(".answer").hide();
	
}
function submitAjaxRating(srno)
{
	var score = $("#daRating"+srno+" > input").val();
	if((score >5 && score < 1) || score =="")
	{
		alert("Please rate this question before submitting your response.");
		return false;
	}

	$("#usefulRating"+srno).hide();
	$("#ratingLoader"+srno).show();

	var currentQcode = $("#currentQcode"+srno).val();

	if ($("#radioComment"+srno).prop('checked'))
   		var radioValue = "Comment";
	else if($("#radioExplanation"+srno).prop('checked'))
		var radioValue = "betterExplanation";
	else
		var radioValue = "-1";

	if(radioValue != "-1")
		var ratingComment = $("#ratingComment"+srno).val();
	else
		var ratingComment = "";

	var data="mode=teacherQuestionRating"+"&score="+score+"&currentQcode="+currentQcode+"&radioValue="+radioValue+"&ratingComment="+ratingComment+"&sessionID=<?=$sessionID?>&userID=<?=$userID?>";

				 $.ajax({
					 		url: "ajaxRequest.php", 
					 		type: "POST",
					 		data: data,
					 		success: function(result){
					 			if(result == 1)
					 			{
									$("#ratingLoader"+srno).hide();
									$("#successMessage"+srno).show();
					 				//alert("Response submitted successfully.");
					 			}
							}    
					    });
}

function showCommentBox(srno,id)
{
	$("#ratingComment"+srno).show();
	if($("#"+id).val() == "betterExplanation")
		 $("#ratingComment"+srno).attr("placeholder", "Write your explanation here...");
	else
		 $("#ratingComment"+srno).attr("placeholder", "Please write your comment here..."); 
}
function showSubmitButton(srno)
{	
	if(starClicked)
	{
		$("#ratingSubmit"+srno).show();
		starClicked = 0;
	}
		
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
	<form target='window.parent' name="mindsparkTeacherLogin" id="mindsparkTeacherLogin" action="" method="post">
		<input type="hidden" name="mode" id="mode" value="">
		<input type="hidden" name="sessionID" id="sessionID" value="<?=$_SESSION["sessionID"]?>">
	    <input type="hidden" name="childClass" id="childClass" value="<?=$_REQUEST['cls']?>">
	    <input type="hidden" name="userType" id="userType" value="teacherAsStudent">
	    <input type="hidden" name="forceNew" id="forceNew" value="">
	    <input type="hidden" name="ttCode" id="ttCode" value="<?=$ttCode?>">
	    <input type="hidden" name="customClusterCode" id="customClusterCode" value="<?=$_REQUEST['learningunit']?>">
	    <input type="hidden" name="forceFlow" id="forceFlow" value="<?=$_REQUEST['flow']?>">
	    <input type="hidden" name="startPoint" id="startPoint" value="">
	</form>
	<div id="container">
		<div id="trailContainer">
			<div id="headerBar">
				<div id="pageName">
					<div class="arrow-black"></div>
					<div id="pageText">TOPIC RESEARCH</div>
				</div>
			</div>
			
			<table id="childDetails" align="top">
				<td width="18%" id="sampleQuestions" class="pointer"><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition" style="width:125px;">SAMPLE QUESTIONS</div></div></div></a></td>
		        <td width="18%" id="wrongAnswers" class="pointer"><a href="cwa.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">COMMON WRONG ANSWERS</div></div></div></a></td>
		        <td width="18%" id="researchStudies" class="pointer"><a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF RESEARCH STUDIES</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF STUDENT INTERVIEWS</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">MISCONCEPTION VIDEOS</div></div></div></a></td>
			</table>
			
			<table id="pagingTable">
		        <td width="80%"><?= $teacherTopicDesc?></td>
				<td class="pointer">
					<div id="download" class="textRed">Do MindSpark</div>
					<div id="downloadImage"></div>
				</td>
			</table>
			
			<div id="questionContainer">
				<form id="frmSampleQues" method="post" action="<?=$_SERVER['PHP_SELF']?>">
    <div id="leftBar">
		<?php include("researchModuleLeftLinks.php"); ?>
    </div>
    <div id="rightBody">
	<input type="hidden" id="ttCode" name="ttCode" value="<?=$ttCode?>">
	<input type="hidden" id="cls" name="cls" value="<?=$cls?>">
	<input type="hidden" id="flow" name="flow" value="<?=$flow?>">
	<input type="hidden" id="showAll" name="showAll" value="<?=$showAll?>">
<?php
if(isset($_REQUEST['learningunit']))
{
	array_push($clusterArray, $_REQUEST['learningunit']);
	$chunk = 1; //show all sample SDLs (1 question per sdl)
}
else
{
	$chunk = 2; //show one sample question per 2 SDLs
	$objTT  = new teacherTopic($ttCode, $cls, $flow);
	$qcodes = array();
	if($showAll==1)
		$clusterArray = $objTT->getClustersOfLevel("All");
	else
		$clusterArray = $objTT->getClustersOfLevel($cls);
}
$practiceClusterCodes = array();
$clusterData = array();
for($i=0; $i<count($clusterArray); $i++)
{
	$query = "SELECT clusterType FROM adepts_clusterMaster WHERE clusterCode='".$clusterArray[$i]."'";
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	if($row['clusterType'] == "practice")
	{
		array_push($practiceClusterCodes, $clusterArray[$i]);
		$query = "SELECT groupID, group_concat(qcode) FROM   adepts_questions WHERE clusterCode='".$clusterArray[$i]."' GROUP BY groupID ORDER BY groupID";
	}
	else
	{
		$query = "SELECT subdifficultylevel, group_concat(qcode) FROM   adepts_questions WHERE clusterCode='".$clusterArray[$i]."' GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
	}
	$result = mysql_query($query);
	$qcodeStr = "";
	while ($line = mysql_fetch_array($result))
		$clusterData[$clusterArray[$i]][$line[0]] = $line[1];
}
$clusters = array_keys($clusterData);
$clusterStr = '';

for($i=0; $i<count($clusters); $i++)
{

	$qcodeArray[$clusters[$i]] = array();
	$sdlArray   = $clusterData[$clusters[$i]];
	$j=1;
	foreach ($sdlArray as $sdl => $qcodeStr)
	{
		$tmpQcodeArray = explode(",",$qcodeStr);
		if($j%$chunk==0 || $j==count($sdlArray))
		{
			$randomValue = rand(0,count($tmpQcodeArray)-1);
			array_push($qcodeArray[$clusters[$i]], $tmpQcodeArray[$randomValue]);
		}
		$j++;
	}
	$clusterStr .= "'".$clusters[$i]."',";
}	
$clusterStr = substr($clusterStr,0,-1);

$timedTestArray = array();
if($cls!="" && $clusterStr!="")
{
	$query  = "SELECT timedTestCode, description, duration_cl$cls, noOfQues_cl$cls FROM adepts_timedTestMaster WHERE linkedToCluster in ($clusterStr) AND status='Live' ";
	$result = mysql_query($query) or die(mysql_error());
	$srno = 0;
	while ($line   = mysql_fetch_array($result))
	{
		$timedTestArray[$srno][0] = $line[0];
		$timedTestArray[$srno][1] = $line[1];
		$timedTestArray[$srno][2] = $line[2];
		$timedTestArray[$srno][3] = $line[3];
		$srno++;
	}
}

if($clusterStr!="")
{
	$clusterNameArray = array();
	$query = "SELECT clusterCode, cluster FROM adepts_clusterMaster WHERE clusterCode in ($clusterStr)";
	$result = mysql_query($query);
	while ($line = mysql_fetch_array($result))
	{
		$clusterNameArray[$line[0]] = $line[1];
	}
}
if(count($timedTestArray)>0)
{
?>
	<div align="right" id="timedTestText"><a href="#pnlTimedTest">Timed Test List</a></div>
<?php
}
if(count($qcodeArray)>0 && !$showAll && !isset($_REQUEST['learningunit']))
	$str = "(You are viewing sample questions of class $cls on $teacherTopicDesc. To view sample questions of ALL classes in this topic, <a href='#' onclick='javascript:showAllQues()' style='text-decoration:underline'>click here.</a>)";
elseif (count($qcodeArray)==0)
	$str = "No ques found!<br/><br/> To view sample questions of ALL classes in this topic, <a href='#' onclick='javascript:showAllQues()' style='text-decoration:underline'>click here.</a>";

if(!$showAll!="")	{
?>

<div align="center" style="font-size:1.4em;font-weight:bold;"><br/><?=$str?></div><br/>
<?php
}
$srno = 1;
foreach ($qcodeArray as $clusterCode=>$qcodes)
{
if(in_array($clusterCode,$practiceClusterCodes))
	$isPracticeCluster = true;
else
	$isPracticeCluster = false;
?>

	<div class="desk_block" align="center">
		<div class="top_left"></div>
		<div class="top_right"></div>
		<div class="top_repeat"></div>
		<div class="block mid_left"></div>
		<div class="block mid_right"></div>
		<div class="block mid_repeat">
			<div class="block_header">
				<div>
					<span class="title">Learning Unit: </span><span class="title"><?=$clusterNameArray[$clusterCode]?></span>&nbsp;&nbsp;&nbsp;&nbsp;
				</div>
				<label id="labelAnswer"><input type="checkbox" id="toDisplayAnswer" onclick="displayAnswerRatingForm();"style="cursor:pointer"/>Show Answers</label>
			</div>
		</div>
		<div class="bot_left"></div>
		<div class="bot_right"></div>
		<div class="bot_repeat"></div>
	</div>

<?php
	for($i=0; $i<count($qcodes); $i++)
	{
		if($isPracticeCluster)
		{
			$sql = "SELECT groupText, groupColumn, a.groupID FROM adepts_groupInstruction a, adepts_questions b WHERE a.groupID=b.groupID AND qcode=$qcodes[$i]";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$groupText = $row['groupText'];
			$groupColumn = $row['groupColumn'];
			$currentGroupID = $row['groupID'];
			$currentQuestions = explode(",",$clusterData[$clusterCode][$currentGroupID]);
		}
		else
		{
			$currentQuestions = array();
			array_push($currentQuestions,$qcodes[$i]);
		}
	?>

	    <div class='question'>
        <?php if($isPracticeCluster) { ?>
	        <table width='100%' border=0 cellspacing=0>
	            <tr>
                	<td align='center' valign='top'  width='5%'></td>
	                <td align='left'><?=$groupText?><br/></td>


	            </tr>
            </table>
            <div class="groupQues">
        <?php } ?>
        <?php
		$Number = 0;

       	foreach($currentQuestions as $currentQcode)
		{	
		$Number++;	
		$query = "SELECT response, comments from adepts_emotToolbarTagging where fieldID=$currentQcode and userID=$userID";
		$result1 = mysql_query($query);
		$line1 = mysql_fetch_assoc($result1);
		
		if($line1['response'] == 'Did not help at all')
			$scoreOfQuestion = 1;
		else if($line1['response'] == 'Did not help')
			$scoreOfQuestion = 2;
		else if($line1['response'] == 'Helped me a bit')
			$scoreOfQuestion = 3;
		else if($line1['response'] == 'Useful')
			$scoreOfQuestion = 4;
		else if($line1['response'] == 'Very useful')
			$scoreOfQuestion = 5;
		else
			$scoreOfQuestion = 0;

		$commentOnQuestion = $line1['comments'];
		$question     = new Question($currentQcode);

		
		if($question->isDynamic())
		{
			$question->generateQuestion();
		}
		$questionType = $question->quesType;
		?>
        <?php if($isPracticeCluster) { ?><div class="singleQuestion column<?=$groupColumn?>"><?php } ?>
	        <table width='100%' border=0 cellspacing=0>
	            <tr>
	                <td align='center' valign='top'  width='5%'><div class="qno"><?=$srno?></div>
	                </td>
	                <td align='left'><?=$question->getQuestion()?><br/></td>
	            </tr>
	<?php
	    if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
	?>
	            <tr  bgcolor="">
	                <td>&nbsp;</td>
	                <td>
	                <table width="100%" border="0" cellspacing="2" cellpadding="3">

	    <?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
	                <tr valign="top">
	                      <td width="5%"  class="orangeBorder" nowrap align="center" ><b>A</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo $question->getOptionA();?></td>
	                    <td width="5%"  class="orangeBorder" nowrap align="center" ><b>B</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo $question->getOptionB();?></td>
	                </tr>
	    <?php    }    ?>
	    <?php    if($questionType=='MCQ-4')    {    ?>
	                <tr valign="top">
	                    <td width="5%"  class="orangeBorder" align="center"><b>C</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo $question->getOptionC();?></td>
	                    <td width="5%"  class="orangeBorder" align="center"><b>D</b></td>
	                    <td width="45%" class="orangeBorder"><?php echo $question->getOptionD();?></td>
	                </tr>
	    <?php    }    ?>
	    <?php    if($questionType=='MCQ-3')    {    ?>
	                <tr valign="top">
	                    <td width="3%"  class="orangeBorder" nowrap align="center"><b>A</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo $question->getOptionA();?></td>
	                      <td width="3%"  class="orangeBorder" nowrap align="center"><b>B</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo $question->getOptionB();?></td>
	                      <td width="3%"  class="orangeBorder" nowrap align="center"><b>C</b></td>
	                      <td width="30%" class="orangeBorder"><?php echo $question->getOptionC();?></td>

	                </tr>
	            <?php    }    ?>
	                  </table>
	                </td>
	            </tr>

	<?php    }    ?>


	        </table>
	
	<div class="answer" style="display:none;">
	       <form id="frmRating" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <?php
    			$toDisplayAnswer = html_entity_decode(orig_to_html($question->displayAnswer,"images","DA",""));
    echo "<div class='answer_and_rating'><b>Answer: </b>".$toDisplayAnswer;


		    		?>
		    		<br><br>
	<?php if($scoreOfQuestion == 0)
		{
			echo "<div id='usefulRating".$srno."'><hr><b>Did you find the explanation useful?</b><br>"; 
				    echo '<div id="daRating'.$srno.'" class="daRating" style="padding: 10px;float: left;" onclick="showSubmitButton('.$srno.');"></div>
							<div id="daHint'.$srno.'" class="daHint" style="padding: 10px;float: left;"></div><br>';
	?><br><br>
		    			<input type="radio" id="radioComment<?=$srno?>" name="ratingCommentradio" value="comment" onclick="showCommentBox(<?=$srno?>,this.id);">I have some comments on the explanation.
		    			<input type="radio" id="radioExplanation<?=$srno?>" name="ratingCommentradio" value="betterExplanation"  onclick= "showCommentBox(<?=$srno?>,this.id);" >I want to contribute a better explanation.
		    			<br><br>
		    			<textarea  id="ratingComment<?=$srno?>" name="ratingComment" class="ratingComment" style="display:none;" rows="10" maxlength="255"></textarea>
		    			
		    			<input type="hidden" id="currentQcode<?=$srno?>" name="currentQcode" value="<?=$currentQcode?>">
		    			<input type="button" id="ratingSubmit<?=$srno?>" class="ratingSubmitButton" name="ratingSubmit" value="Submit" onclick="submitAjaxRating(<?=$srno?>);">
	<?php 
			}
			else
			{
				echo "<div id='usefulRating".$srno."'><hr><b>Your rating for this explanation is: </b><br>"; 
			    echo '<div id="daRating'.$srno.'" class="daRating" style="padding: 10px;float: left;"></div>
						<div id="daHint'.$srno.'" class="daHint" style="padding: 10px;float: left;"></div><br>';

				$thisComment = explode("~", $commentOnQuestion,2);
				if($thisComment[0] = "Comment" && $thisComment[1] != "")
				{
					echo "<br><br><b>Your comment on this question:</b><br>";
					echo $thisComment[1];
				}
				else if($thisComment[0] = "betterExplanation" && $thisComment[1] != "")
				{
					echo "<br><br><b>Explanation provided by you for this question:</b><br>";
					echo $thisComment[1];
				}
				echo '<br><br><div>Thank you for your feedback!</div>';
				echo
				   "<script>
				   $(document).ready(function (){
				   $('#daRating$srno').raty('click', $scoreOfQuestion);
				   $('#daRating$srno').raty('readOnly', true);
				   });
				   </script>";
			}
	?>
    			</div>
    		</div>
    	</form>
    </div>
    



    	<img src="assets/ratingLoader.gif" id="ratingLoader<?=$srno?>" style="display:none"/ >
    	<span id="successMessage<?=$srno?>" class="answer_and_rating" style="display:none;color:green;">
    		Your responses are saved successfully.
    	</span>
    <?php 	
    if($isPracticeCluster) { $srno++; ?></div><?php } 	
		}
		if($isPracticeCluster && $Number % $groupColumn == 0)
			echo '<div style="clear:both;"></div>';
		if($isPracticeCluster)
			echo '</div>';
	?>  
	        <br/>
	       
	    </div>
	    <br>
	<?php
    	if(!$isPracticeCluster)
			$srno++;
	}
}
if(count($qcodes>0))
{
	if(count($timedTestArray)>0)
	{
?>
	<p id="pnlTimedTest">
		<table align="center" class="tblContent" border="1" cellpadding="2" cellspacing="0" width="98%">
			<br/>
			<tr><caption class="desk_block" style="text-align:left">Timed Tests:</caption></tr>
			<tr>
				<th class="header">Timed Test</th>
				<th class="header">No. of questions</th>
				<th class="header">Duration (mins)</th>
			</tr>
<?php for($i=0; $i<count($timedTestArray);$i++) {?>
			<tr>
				<td><a href="../userInterface/timedTest.php?mode=sample&timedTest=<?=$timedTestArray[$i][0]?>&class=<?=$cls?>" target="_blank"><?=$timedTestArray[$i][1]?></a></td>
				<td align="center"><?=$timedTestArray[$i][3]?></td>
				<td align="center"><?=$timedTestArray[$i][2]?></td>
			</tr>
<?php } ?>
		</table>
		<br/>
	</p>
<?php
	} }?>
	</div>
</form>
			
			</div>
		</div>
	</div>

<?php include("footer.php") 

?>
<script>
$.fn.raty.defaults.path = '../userInterface/assets/raty';
for(var i=1; i<= <?=$srno?>;i++)
{
	var starColor;
	$('#daRating'+i).raty({
		mouseover: function(score, evt) {
			var rating = this.id;
			var hint = rating.replace("daRating", "daHint");
			/*alert('ID: ' + this.id + "\nscore: " + score + "\nevent: " + evt.type);*/
			if(score <=2)
				$('#'+hint).css("color","#E77817");
			else if(score < 4)
				$('#'+hint).css("color","#FFC107");
			else if(score<=5)
				$('#'+hint).css("color","#84C225");

			},
		mouseout: function(score, evt) {
			var rating = this.id;
			var hint = rating.replace("daRating", "daHint");
	   		 if(score <=2)
				$('#'+hint).css("color","#E77817");
			else if(score < 4)
				$('#'+hint).css("color","#FFC107");
			else if(score<=5)
				$('#'+hint).css("color","#84C225");
	  	},
		click: function(score, evt) {
			var rating = this.id;
			var hint = rating.replace("daRating", "daHint");
			starClicked = 1;
			if(score <=2)
				$('#'+hint).css("color","#E77817");
			else if(score < 4)
				$('#'+hint).css("color","#FFC107");
			else if(score<=5)
				$('#'+hint).css("color","#84C225");
		},
			hints : [['Did not help at all'], ['Did not help'], ['Helped me a bit'], ['Useful'], ['Very useful']],
			target : '#daHint'+i,
			targetKeep : true,
			iconRange : [
					{ range: 1, on: '1.png', off: '0.png' },
					{ range: 2, on: '1.png', off: '0.png' },
					{ range: 3, on: '2.png', off: '0.png' },
					{ range: 4, on: '3.png', off: '0.png' },
					{ range: 5, on: '3.png', off: '0.png' }
				],
			iconRangeSame: true
	});

}


</script>