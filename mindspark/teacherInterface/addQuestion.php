<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	ob_start();
	include("header.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/constants.php");
	
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");


//for add question
$arrayTopic	=	array();

$topic	=	$_POST['topic'];
$questionClass	=	$_POST['questionClass'];
if($topic!='')
{
	$arrayLearningUnit	=	array();
	$arrayLearningUnit	=	getLearningUnit($topic,$arrayLearningUnit);
}

?>

<title>Add Question</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/addQuestion.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
		document.getElementById("correctAnswer").style.display="none";
		document.getElementById("correctAnswerText").style.display="none";		
		
	}
</script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements: "question,option_a,option_b,option_c,option_d",
		theme : "advanced",
		relative_urls : true,
		remove_script_host : false,
	    document_base_url : 'localhost/',
	    convert_urls : true,
		plugins : "images,jbimages,autolink,lists,pagebreak,layer,table,advimage,advlink,iespell,inlinepopups,media,searchreplace,contextmenu,paste,directionality,noneditable,nonbreaking,template,wordcount,advlist",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,charmap",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "right",
		theme_advanced_resizing : false,

		
	});
</script>

<script>
document.getElementById("mcq").style.display="none";


function showHideOptions(quesType)	{
	
	document.addQues.option_a.value='';
	document.addQues.option_b.value='';
	document.addQues.option_c.value='';
	document.addQues.option_d.value='';
	/*document.addQues.div_answer.value='';*/
	
	
	if(quesType=="MCQ-4")	{
		document.getElementById("optionss").style.display="";
		document.getElementById("mcq").style.display="";
		document.getElementById("rowOptionA").style.display="";
		//document.getElementById("rowOptionB").style.display="";
		document.getElementById("rowOptionC").style.display="";
		document.getElementById("rowOptionD").style.display="";
		document.getElementById("rowOptionCD").style.display="";
		document.getElementById("rowOptionCD1").style.display="";
		document.getElementById("correctAnswer").style.display="none";
		document.getElementById("correctAnswerText").style.display="none";
	}
	else if(quesType=="MCQ-3")	{
		document.getElementById("optionss").style.display="";
		document.getElementById("mcq").style.display="";
		document.getElementById("rowOptionA").style.display="";
		//document.getElementById("rowOptionB").style.display="";
		document.getElementById("rowOptionD").style.display="";
		document.getElementById("rowOptionC").style.display="";
		document.getElementById("rowOptionCD").style.display="none";
		document.getElementById("rowOptionCD1").style.display="none";
		document.getElementById("correctAnswer").style.display="none";
		document.getElementById("correctAnswerText").style.display="none";
	}
	else if(quesType=="MCQ-2")	{
		document.getElementById("optionss").style.display="";
		document.getElementById("mcq").style.display="";
		document.getElementById("rowOptionA").style.display="";
		//document.getElementById("rowOptionB").style.display="";
		document.getElementById("rowOptionC").style.display="none";
		document.getElementById("rowOptionD").style.display="none";
		document.getElementById("correctAnswer").style.display="none";
		document.getElementById("correctAnswerText").style.display="none";
	}
	else	{	//Blank type questions
		document.getElementById("optionss").style.display="none";
		document.getElementById("mcq").style.display="none";
		document.getElementById("rowOptionA").style.display="none";
		//document.getElementById("rowOptionB").style.display="none";
		document.getElementById("rowOptionC").style.display="none";
		document.getElementById("rowOptionD").style.display="none";
		document.getElementById("correctAnswer").style.display="";
		document.getElementById("correctAnswerText").style.display="";
		document.getElementById("optionss").style.display="none";
	}
}
</script>

<script>
document.getElementById("mcq").style.display="none";
function getCluster(topicCode)
{
	document.addQues.topic.value	=	topicCode;
	setTryingToUnload();
	document.addQues.submit();
}

function viewInsrtuction()
{
	document.getElementById("instructions").style.display="";
}

function hideInsrtuction()
{
	document.getElementById("instructions").style.display="none";
}


function submit_que()
{
	var textQuestion = tinyMCE.get('question').getContent();
	if(document.addQues.questionClass.value=='')
	{
		alert("Please select a Class!!");
		document.addQues.questionClass.focus();
		return false;
	}

	if(document.addQues.questionTopic.value=='')
	{
		alert("Please select a Topic!!");
		document.addQues.questionTopic.focus();
		return false;
	}

	if(document.addQues.learningunit.value=='')
	{
		alert("Please select a Learning Unit!!");
		document.addQues.learningunit.focus();
		return false;
	}
	if(textQuestion=="")
	{
		alert("Please enter the question!!");
		return false;
	}

	var optionA;
	var optionB;
	var optionC;
	var optionD;
	var answer;

	optionA	=	tinyMCE.get('option_a').getContent();
	optionB	=	tinyMCE.get('option_b').getContent();
	optionC	=	tinyMCE.get('option_c').getContent();
	optionD	=	tinyMCE.get('option_d').getContent();
	answer	=	document.addQues.div_answer.value;

	//if blank type then check whether the answer is entered or not
	if(document.addQues.question_type[3].checked)
	{
		if(answer=='')
		{
			alert("Please enter the correct answer!!");
			return false;
		}
	}

	else
	{
		if (document.addQues.question_type[0].checked || document.addQues.question_type[1].checked || document.addQues.question_type[2].checked)
		{
			if(optionA=='' || optionB=='')
			{
				alert("Please fill in all the options");
				return false;
			}
			else if(optionA==optionB)
			{
				alert("Option A and B can not be same");
				return false;
			}
		}

		if (document.addQues.question_type[1].checked || document.addQues.question_type[2].checked)
		{
			if(optionC=='')
			{
				alert("Please fill in all the options");
				return false;
			}
			else if(optionA==optionC || optionB==optionC)
			{
				alert("Two options can not be same");
				return false;
			}
		}

		if (document.addQues.question_type[2].checked)
		{
			if(optionD=='')
			{
				alert("Please fill in all the options");
				return false;
			}
			else if(optionA==optionD || optionB==optionD || optionC==optionD)
			{
				alert("Two options can not be same");
				return false;
			}
		}

		if(!(document.addQues.correct_answer[0].checked) && !(document.addQues.correct_answer[1].checked) && !(document.addQues.correct_answer[2].checked) && !(document.addQues.correct_answer[3].checked))
		{
			alert("Please select a correct answer");
			return false;
		}
	}
	setTryingToUnload();
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Add Question</span>
				<div id="instructs" style=""> <span onclick="viewInsrtuction()" style="cursor:pointer;text-decoration:underline;color:brown">View instruction for adding a question</span> </div>
			</div>
			<div id="containerBody">
			<form name="addQues" id="addQues" enctype="multipart/form-data" action="" method="POST">
			<div id="instructions" style="display:none;">
					
						<li>Ensure the topic is selected before submiting the question.</li>
						<li>Any image or file can be uploaded by clicking on the upload image option(max file size 500KB).</li>
						<li>Please use the toolbar to insert mathematical symbols.</li>
						<li>The question is as far as possible, test a single skill.</li>
						<li>In case of any remarks please enter in a remarks box and not in the question box.</li>
						<li>In case of image description, please enter in remarks.
						<span onclick="hideInsrtuction()" style="cursor:pointer;text-decoration:underline;color:brown;">hide</span>
						</li>
						<hr>
				</div>
			<table  id="quetbl"> <tr> <td>
			<div  class="dropdowns" style="float: left;">
				<div>Class</div>
				<select name="questionClass" id="questionClass">
				<option value="">Select Class</option>
				<option value="1" <?php if($questionClass==1) echo "selected"?>>1</option>
				<option value="2" <?php if($questionClass==2) echo "selected"?>>2</option>
				<option value="3" <?php if($questionClass==3) echo "selected"?>>3</option>
				<option value="4" <?php if($questionClass==4) echo "selected"?>>4</option>
				<option value="5" <?php if($questionClass==5) echo "selected"?>>5</option>
				<option value="6" <?php if($questionClass==6) echo "selected"?>>6</option>
				<option value="7" <?php if($questionClass==7) echo "selected"?>>7</option>
				<option value="8" <?php if($questionClass==8) echo "selected"?>>8</option>
				<option value="9" <?php if($questionClass==9) echo "selected"?>>9</option>
				<option value="10" <?php if($questionClass==10) echo "selected"?>>10</option>
			</select>
			</div>	
			
			<div> 
				<div>Topic</div>
				<?php $arrayTopic	=	getTeacherTopics($arrayTopic); ?>
				<select name="questionTopic" id="questionTopic" onchange="getCluster(this.value)">
				<option value="">Select Topic</option>
				<?php foreach ($arrayTopic as $topicCode=>$topicName)
				{ $selected	=	'';
					if($topic==$topicCode)
						$selected	=	"selected";
					echo "<option value=".$topicCode." ".$selected.">".$topicName."</option>";
				} ?></select><input type="hidden" name="topic" id="topic">
			</div>
			
			<div style="margin-top:1%;"> 
				<div>Learning Unit</div>
				<select name="learningunit" id="learningunit">
				<option value="">Select Learning Unit</option>
				<?php foreach ($arrayLearningUnit as $clusterCode=>$clusterName)
				{
					echo "<option value=".$clusterCode.">".$clusterName."</option>";
				} ?></select>
			</div>
			
			
			 </td> </tr>
		
				<tr>
			<td ><b>Question Type</b></td>
				</tr>
			<tr>
			<td><input type="radio" name="question_type" id="quesTypeMCQ2" onclick="showHideOptions(this.value)" value="MCQ-2">MCQ-2
	          	<input  type="radio" name="question_type" id="quesTypeMCQ3" onclick="showHideOptions(this.value)" value="MCQ-3" style="margin-left: 20px;">MCQ-3
	          	<input  type="radio" name="question_type" id="quesTypeMCQ4" onclick="showHideOptions(this.value)" value="MCQ-4" checked style="margin-left: 20px;">MCQ-4
	          	<input  type="radio" name="question_type" id="quesTypeBlank" onclick="showHideOptions(this.value)" value="Blank" style="margin-left: 20px;">Blank</td>
		</tr>
	<tr> <td> <div style="margin-top:2%;"> <b>Questions</b> </div></td> </tr>
	<tr>
		
		<td><textarea name="question" id="question" cols="80" rows="6"></textarea>			</td>
	</tr>
	<tr id="optionss"> <td  > <div  style="margin-top:2%; margin-bottom: 1%;"> <b>Options</b> </div></td> </tr>
	<tr id='mcq'>
        	
          	<td>
		  	  <table border="0">
          	  	<tr id="rowOptionA"><td><input type="radio" name="correct_answer" value="A"><strong> A.</strong></td><td width="90%"><textarea name="option_a" id="option_a" cols="35" rows="1"></textarea></td><!--</tr>
			  	<tr id="rowOptionB">--><td><input type="radio" name="correct_answer" value="B"><strong> B.</strong></td><td><textarea name="option_b" id="option_b" cols="35" rows="1"></textarea></td></tr>
			  	<tr id="rowOptionD"><td id="rowOptionC"><input type="radio" name="correct_answer" value="C"><strong> C.</strong></td><td><textarea name="option_c" id="option_c" cols="35" rows="1"></textarea></td><!--</tr>
			  	<tr id="rowOptionD">--><td id="rowOptionCD"><input type="radio" name="correct_answer" value="D"><strong> D.</strong></td><td id="rowOptionCD1"><textarea name="option_d" id="option_d" cols="35" rows="1"></textarea></td></tr>
			  </table>
	      	</td>
        </tr>
		
		<tr id="correctAnswerText"> <td ><div style="margin-top:2%; margin-bottom: 1%;"><strong>Answer</strong> </div></td> </tr>
		<tr id="correctAnswer">
         
          <td>
          	<input type="text" name="div_answer" id="div_answer" size="60" style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:40px; width:380px"/>
          </td>
        </tr>
	
	<tr><td><div style="margin-top:2%; margin-bottom: 1%;"><b>Remarks</b></div></td></tr>
	<tr>
		
		<td><textarea name="remark" id="remark" cols="40" rows="5"></textarea></td>
	</tr>
	<tr> <td>
	<div id="buttonSection">
				 <input type="submit" name="Submit" id="Submit" value="Add Question" class="buttons" onclick="return submit_que()">
				  <a href="otherFeatures.php" style="text-decoration:none"><input type="button" name="cancel" id="cancel" class="buttons" value="Cancel" onclick=""></a>
				 </div>
	</td></tr>
			</table>
			
				 
			</form>
			<table border="0" cellpadding="10" cellspacing="10" width="80%" align="center" id="thanks" style="display:none">
<tr><td>&nbsp;</td></tr>
<tr>
	<td align="center"><h2>Thanks for the contribution. We will surely work on the recommendation. Hope to receive such a participation in future too!!</h2></td>
</tr>
<tr>
<td align="center"><input type="button" value="Ok" class="buttons" id="continue" name="continue" onclick="setTryingToUnload();window.location.href='home.php'"/></td>
</tr>
</table>
		</div>
		
	</div>
	</div>
	
<?php

if(isset($_REQUEST['Submit']))
{
	$questionClass	=	$_POST['questionClass'];
	$questionTopic	=	$_POST['questionTopic'];
	$learningunit	=	$_POST['learningunit'];
	$question_type	=	$_POST['question_type'];
	$question		=	$_POST['question'];
	$correct_answer	=	$_POST['correct_answer'];
	$option_a		=	$_POST['option_a'];
	$option_b		=	$_POST['option_b'];
	$option_c		=	$_POST['option_c'];
	$option_d		=	$_POST['option_d'];
	$div_answer		=	$_POST['div_answer'];
	$remarks		=	$_POST['remark'];
	//$questionmaker	=	$userID;
	$questionmaker  =   $_SESSION['username'];
	$submitdate		=	date("Y-m-d");

	if($question_type=='Blank')
		$correctAnswer	=	$div_answer;
	else
		$correctAnswer	=	$correct_answer;

	//restricting page to re enter the same data
	$sqchk	=	"SELECT qcode FROM adepts_teacherQuestion WHERE question='$question' AND correct_answer='$correctAnswer' AND questionmaker='$questionmaker'";
	$rschk	=	mysql_query($sqchk);
	$count	=	mysql_num_rows($rschk);

	$sq	=	"INSERT INTO adepts_teacherQuestion
			 (question,question_type,optiona,optionb,optionc,optiond,correct_answer,clusterCode,ttCode,class,questionmaker,remarks,submitdate) VALUES
			 ('$question','$question_type','$option_a','$option_b','$option_c','$option_d','$correctAnswer','$learningunit','$questionTopic','$questionClass','$questionmaker','$remarks','$submitdate')";
	if($count=='')
		$rs	=	mysql_query($sq);
	else
		header("location:home.php");
	if($rs)
	{
		echo '<script> document.getElementById("quetbl").style.display="none"; document.getElementById("thanks").style.display=""; </script>';
	}
?>


<?php
}

function getTeacherTopics($arrayTopic)
{
	$sq	=	"SELECT teacherTopicCode,teacherTopicDesc FROM adepts_teacherTopicMaster WHERE live='1' AND customTopic=0 AND subjectno=".SUBJECTNO." AND customTopic=0 ORDER BY teacherTopicDesc";
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_assoc($rs))
	{
		$teacherTopicCode				=	$rw['teacherTopicCode'];
		$arrayTopic[$teacherTopicCode]	=	$rw['teacherTopicDesc'];
	}
	return $arrayTopic;
}

function getLearningUnit($topic,$arrayLearningUnit)
{
	$sq	=	"SELECT A.cluster,A.clusterCode
			 FROM adepts_clusterMaster A , adepts_teacherTopicClusterMaster B
			 WHERE B.teacherTopicCode='$topic' AND A.clusterCode=B.clusterCode AND status='Live'
			 ORDER BY A.cluster";
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_assoc($rs))
	{
		$clusterCode						=	$rw['clusterCode'];
		$arrayLearningUnit[$clusterCode]	=	$rw['cluster'];
	}
	return $arrayLearningUnit;
}

?>

<?php include("footer.php") ?>