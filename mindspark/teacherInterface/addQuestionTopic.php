<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	ob_start();
	include("header.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/constants.php");

	$userID     = $_SESSION['userID'];
	$username	= $_SESSION['username'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");

	$arrayTopic	=	array();
	$topic	=	$_POST['topic'];
	$questionClass	=	$_POST['questionClass'];
	$qcode=(isset($_GET['qc']))?$_GET['qc']:0;
	$tab=isset($_GET['tab'])?$_GET['tab']:'1';
	$questionmaker=$username;
	$quesPullType=0;
	if(isset($_GET['qc']))
	{
		$qid	=	$_GET['qc'];
		$listDetails	=	getTeacherQuestion($qid);
		if ($listDetails==-1) {$qcode=0;return;}
		list($question,$question_type,$optiona,$optionb,$optionc,$optiond,$correct_answer,$remarks,$questionmaker)	=	explode("~-~", $listDetails);
	}
	if(isset($_POST['Submit'])) // Handling things after page submits...
	{
		$questionTopic='';
		$questionClass='';
		$qcode			=	$_POST['qcode'];
		$question_type	=	$_POST['question_type'];
		$question		=	str_replace(TEACHER_IMAGES_FOLDER."/", "Image:", $_POST['div_question']);
		$correct_answer	=	$_POST['correct_answer'];
		$optiona		=	$_POST['div_option_a'];
		$optionb		=	$_POST['div_option_b'];
		$optionc		=	$_POST['div_option_c'];
		$optiond		=	$_POST['div_option_d'];
		$div_answer		=	$_POST['div_answer'];
		$remarks		=	$_POST['remark'];
		//$questionmaker	=	$userID;
		$questionmaker  =   $_POST['questionmaker'];
		$submitdate		=	date("Y-m-d");
		$status         =   'Received';

		if($question_type=='Blank')
			$correct_answer	=	$div_answer;
		else
			$correct_answer	=	$correct_answer;

		if($qcode!=0)
		{
			$sq = "UPDATE adepts_teacherQuestion SET question = '".$question."', question_type = '".$question_type."', optiona = '".$optiona."', optionb = '".$optionb."', optionc = '".$optionc."', optiond = '".$optiond."', correct_answer='".$correct_answer."', remarks='".$remarks."' WHERE qcode=$qcode";
			$rs	=	mysql_query($sq);
			if($rs) $quesPullType=2;
		}
		else
		{	
	    	$sq	= "INSERT INTO adepts_teacherQuestion (question, question_type, optiona, optionb, optionc, optiond, correct_answer, questionmaker, remarks, submitdate, status) VALUES ('$question','$question_type','$optiona','$optionb','$optionc','$optiond','$correct_answer','$questionmaker','$remarks','$submitdate','$status')";
	    	$rs	=	mysql_query($sq);
		    if($rs) $quesPullType=1;
		    $sq	= "SELECT qcode FROM adepts_teacherQuestion WHERE questionmaker='$questionmaker' ORDER BY qcode DESC LIMIT 1";
		    $rs	=	mysql_query($sq);$line=mysql_fetch_array($rs);$qcode=$line[0];
	    }
	    $listDetails	=	getTeacherQuestion($qcode);
	    if ($listDetails==-1) {$qcode=0;return;}
	    list($question,$question_type,$optiona,$optionb,$optionc,$optiond,$correct_answer,$remarks,$questionmaker)	=	explode("~-~", $listDetails);
	}
	if($topic!='')
	{
		$arrayLearningUnit	=	array();
		$arrayLearningUnit	=	getLearningUnit($topic,$arrayLearningUnit);
	}
	
	if(!isset($question_type))
		{ $question_type = "MCQ-4";}

	function getTeacherQuestion($qid)
	{
		$sq	=	"SELECT question,question_type,optiona,optionb,optionc,optiond,correct_answer,remarks, questionmaker FROM adepts_teacherQuestion WHERE qcode='$qid'";
		$rs	=	mysql_query($sq);
		if (mysql_num_rows($rs)==0) return -1;
		list($question,$question_type,$optiona,$optionb,$optionc,$optiond,$correct_answer,$remarks,$questionmaker)=	mysql_fetch_array($rs);
		$question	=	removeTags($question);
		$optiona	=	removeTags($optiona);
		$optionb	=	removeTags($optionb);
		$optionc	=	removeTags($optionc);
		$optiond	=	removeTags($optiond);
		return $question.'~-~'.$question_type.'~-~'.$optiona.'~-~'.$optionb.'~-~'.$optionc.'~-~'.$optiond.'~-~'.$correct_answer.'~-~'.$remarks.'~-~'.$questionmaker;
	}

	function removeTags($string,$defTab=0)
	{
		$allow = '<abbr><acronym><address><applet><area><b><base><basefont><bdo><big><blockquote><body><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><frame><frameset><h1><h2><h3><h4><h5><h6><head><hr><html><i><iframe><input><ins><isindex><kbd><label><legend><li><link><map><menu><meta><noframes><noscript><object><ol><optgroup><option><p><param><pre><q><s><samp><script><select><small><span><strike><strong><style><sub><sup><table><tbody><td><textarea><tfoot><th><thead><title><tr><tt><u><ul><img>';
		$imgSource	=	preg_match('/<img[^>]+src="(.+)"[^>]+>/', $string, $match);
		$imgSource	=	explode('"', $match[1]);
		if ($defTab==1){
			$newImgSrc	=	preg_replace("/.+\//","Image:",$imgSource[0]);
			$newString	=	str_replace($match[0],"[".$newImgSrc."] ", $string);
			return strip_tags($newString,$allow);
		}
		else {
			$modString = preg_replace('/(<img[^>]+src=")Image:/is', "$1".TEACHER_IMAGES_FOLDER.'/', $string);
			return strip_tags($modString,$allow);
		}
	}

	function getQuestionsAddedDetail($question_maker)
	{
		if(strcasecmp(trim($_SESSION['admin']),"School Admin")!=0)
			$query="Select * from adepts_teacherQuestion where questionmaker='".$question_maker."' order by qcode DESC";
		else 
			{$query="Select a.* from adepts_teacherQuestion a, adepts_userDetails b where b.schoolCode=".$_SESSION['schoolCode']." AND questionmaker=b.username order by qcode DESC";}
		$result= mysql_query($query);
		$rs = mysql_num_rows($result);
		$rows = array();

		while(($row = mysql_fetch_array($result))) {
		    $rows[] = $row;
		}
		return $rows;
	}
?>
<title><?php if($tab==2) echo 'View Questions'; else echo 'Add/Edit Question';?></title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/addQuestion.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/introjs.css" />
<link rel="stylesheet" href="css/colorbox.css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/intro.js"></script>
<!-- <script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script> -->
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tinymce/jscripts/tiny_mce_3x/tiny_mce.js"></script>
 
<!-- <script type="text/javascript" src="libs/tinymce/jscripts/tiny_mce/plugins/mindsparkToolbar/editor_plugin.js"></script>  -->
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript">
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		//$("#container").css("height",containerHeight+"px");
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}

	// To intialize 
	$(document).ready(function(e) {
		<?php if ($tab==1) echo "showHideOptions('$question_type');";?>
		<?php if ($quesPullType==1) echo '$("#thanks").show();setTimeout(function(){$("#thanks").fadeOut();},4000);';
			  else if ($quesPullType==2) echo '$("#thanks_edit").show();setTimeout(function(){$("#thanks_edit").fadeOut();},4000);';
		?>
		$(".smallCircle").click(function() {
			//$(".questionContainer").hide();
			$("#"+$(this).closest("td").attr("class")).show();
			//$(this).closest("label").addClass("textRed");
			$(".smallCircle").removeClass("red");
			$(".pointer").removeClass("textRed");
			$(this).addClass("red");
			$(this).next().addClass("textRed");
			
	    });    
	});

	function startIntro(){
        var intro = introJs();
        var steps = [
				{
					element: document.querySelector('#addquestion'),
					intro: "Click here to add Question."
				},
				{
					element: document.querySelector('#viewquestion'),
					intro: "Click here to view/edit Questions added by you.",
				},
				{
					element: document.querySelector('#question_type'),
					intro: "Choose the type of question you want to add.",
				},
				{
					element: document.querySelector('#question_caption'),
					intro: "Enter your question here.To insert a blank anywhere, just click on [Blank] button in the toolbar above.",
				},
				{
					element: document.querySelector('#div_options'),
					intro: "Enter your options here and choose the correct answer by clicking on the radio button next to the correct option.",
				}, 
				{
					element: document.querySelector('#correctAnswer'),
					intro: "Enter the correct answer here.",
				},
				{
					element: document.querySelector('#remarks'),
					intro: "You can leave a remark/comment for reference.",
				},
				{
					element: document.querySelector('#buttonSection'),
					intro: "You can submit the question by clicking on Add Question or Cancel.",
				},
				{
					element: document.querySelector('#question_code'),
					intro: "You can quote this code for reference.",
				},
				{
					element: document.querySelector('#question_content'),
					intro: "Click here to preview the question as a student.",
				},
				{
					element: document.querySelector('#edit_link'),
					intro: "Click here to edit the question. The changes will be overwritten.",
				},
				{
					element: document.querySelector('#question_status'),
					intro: "This tells you the status of the question.",
				}
			];		

	 	var filteredSteps = new Array();
	    for (var i=0; i<steps.length; i++) {
	      if($(steps[i].element).is(':visible')) filteredSteps.push(steps[i]);
	    }
	    intro.setOptions({steps: filteredSteps});
	    intro.start();
	}
	function triggerEditor(element) {	
	    tinyMCE.triggerSave();
	}

	function showHideOptions(quesType)
	{
		if(quesType=="MCQ-4")	{
			document.getElementById("mcq").style.display="";
			$("#rowOptionC").parent("tr").show();
			document.getElementById("rowOptionA").style.visibility="visible";
			document.getElementById("rowOptionB").style.visibility="visible";
			document.getElementById("rowOptionC").style.visibility="visible";
			document.getElementById("rowOptionD").style.visibility="visible";
			document.getElementById("correctAnswer").style.display="none";
		}
		else if(quesType=="MCQ-3")	{
			document.getElementById("mcq").style.display="";
			$("#rowOptionC").parent("tr").show();
			document.getElementById("rowOptionA").style.visibility="visible";
			document.getElementById("rowOptionB").style.visibility="visible";
			document.getElementById("rowOptionC").style.visibility="visible";
			document.getElementById("rowOptionD").style.visibility="hidden";
			document.getElementById("correctAnswer").style.display="none";
		}
		else if(quesType=="MCQ-2")	{
			document.getElementById("mcq").style.display="";
			//alert()
			$("#rowOptionC").parent("tr").css("display","none");
			document.getElementById("rowOptionA").style.visibility="visible";
			document.getElementById("rowOptionB").style.visibility="visible";
			document.getElementById("rowOptionC").style.visibility="hidden";
			document.getElementById("rowOptionD").style.visibility="hidden";
			document.getElementById("correctAnswer").style.display="none";
		}
		else{	//Blank type questions
			document.getElementById("mcq").style.display="none";
			document.getElementById("rowOptionA").style.visibility="hidden";
			document.getElementById("rowOptionB").style.visibility="hidden";
			document.getElementById("rowOptionC").style.visibility="hidden";
			document.getElementById("rowOptionD").style.visibility="hidden";
			document.getElementById("correctAnswer").style.display="block";
		}
	}

	function submit_que(context)
	{
	    var optionA,optionB,optionC,optionD,answer,questype,ques;

	    if(tinyMCE.get("div_question").getContent()=="")
		{
			alert("Please enter the question!!");return false;
		}
		else
			var ques= tinyMCE.get("div_question").getContent();

		optionA	=	tinyMCE.get('div_option_a').getContent();
		optionB	=	tinyMCE.get('div_option_b').getContent();
		optionC	=	tinyMCE.get('div_option_c').getContent();
		optionD	=	tinyMCE.get('div_option_d').getContent();
		answer	=	document.addQues.div_answer.value;
	    
		if(document.addQues.question_type[3].checked)	//i.e.Blank
			questype = "Blank";
		else
			questype = "MCQ";

		var regex_blank = /\[blank_(\d*)[^\]]*\]/g;

		if(questype=="Blank")
		{
			var matches = ques.match(regex_blank);
			if (!regex_blank.test(ques))
				{alert("Please specify one blank in the question!");return false;}
			else if (matches.length>1)
				{alert("Please specify only one blank in the question!");return false;}
			else if (answer=='')
				{alert("Please enter the correct answer!!");return false;}
			optionA='';optionB='';optionC='';optionD='';
		}
		else
		{
			if (regex_blank.test(ques) || regex_blank.test(optionA) || regex_blank.test(optionB) || regex_blank.test(optionC) || regex_blank.test(optionD)){
				alert("You cannot add a blank in a multiple-choice question.");return false;
			}
			if (document.addQues.question_type[0].checked || document.addQues.question_type[1].checked || document.addQues.question_type[2].checked)
			{
				if(optionA=='' || optionB=='')
					{alert("Please fill in all the options");return false;}
				else if(optionA==optionB)
					{alert("Two options can not be same");return false;}
			}
			if (document.addQues.question_type[1].checked || document.addQues.question_type[2].checked)
			{
				if(optionC=='')
					{alert("Please fill in all the options");return false;}
				else if(optionA==optionC || optionB==optionC)
					{alert("Two options can not be same");return false;}
			}

			if (document.addQues.question_type[2].checked)
			{
				if(optionD=='')
					{alert("Please fill in all the options");return false;}
				else if(optionA==optionD || optionB==optionD || optionC==optionD)
					{alert("Two options can not be same");return false;}
			}

			if(!(document.addQues.correct_answer[0].checked) && !(document.addQues.correct_answer[1].checked) && !(document.addQues.correct_answer[2].checked) && !(document.addQues.correct_answer[3].checked))
				{alert("Please select a correct answer");return false;}
		}
	}

	function hidebutton(ele)
	{
		if(ele=='2')
		{ document.getElementById('containerBody2').style.display = 'none';
		  document.getElementById('containerBody3').style.display = 'block';	
		}
		if(ele=='1')
		{ document.getElementById('containerBody2').style.display = 'block';
		  document.getElementById('containerBody3').style.display = 'none';	
		}
	}

	function submitfrm(ele)
	{
		if(ele=='2')
			window.location="addQuestionTopic.php?tab=2";
	  	if(ele=='1')
	  		window.location="addQuestionTopic.php?tab=1";		
	}

	function viewNewInterface(qcode)
	{ 
		window.open('viewtempQuestionInterface.php?qcode='+qcode+'&theme=2&teacherQuestion=1','_newtab');
	}

	function image_selected(name,textEI)
	{
	    if (typeof(tinyMCE) !== "undefined") {
	        tinyMCE.execCommand('mceInsertContent',false,name);
	    } else {
			var getpos = document.selection.createRange();
			getpos.pasteHTML(name);
	    }
	}
</script>
<script type="text/javascript">
	$(function() {
		tinyMCE.init({
			// General options
			mode : "specific_textareas",
			editor_selector: "mceEditor",
			theme : "advanced",
			relative_urls : false,
			remove_script_host : false,
			document_base_url : 'mindspark/teacherInterface',
			body_class: "mceQuesPage",

			plugins : "paste,table,inlinepopups,mindsparkToolbar,jbimages,media,placeholder,advimage",
			theme_advanced_buttons1 : "bold,italic,underline,forecolor,backcolor,|,fontsizeselect,|,sub,sup,|,charmap,mindsparkToolbarBlank,mindsparkToolbarAbyB,mindsparkToolbarXpowerY,mindsparkToolbarPi,jbimages",
			theme_advanced_buttons2 :  "tablecontrols,|,charmap,|,mindsparkToolbarBlank,mindsparkToolbarEqu,mindsparkToolbarAbyB,mindsparkToolbarXpowerY,mindsparkToolbarBmatrix,mindsparkToolbarPmatrix,mindsparkToolbarPi,,mindsparkToolbarRupee,mindsparkToolbarVector,mindsparkToolbarRupee",

			theme_advanced_buttons3 : "",
			theme_advanced_layout_manager : "SimpleLayout",
			theme_advanced_toolbar_location : "external",

			theme_advanced_resizing : true,
			theme_advanced_statusbar_location: 'none',
			setup : function(ed) { 
				ed.onLoadContent.add(function(ed,o){
					ed.execCommand('getPlaceHolder',ed);
				});
			},
		});
	});
</script>
</script>
<body class="translation" onload="load()" onresize="load()">
	<table  id="thanks" border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="display:none;position:fixed;">
		<tr width="100%">
			<td><h2  align="center">Thank you for contributing to Mindspark. We'll go through your question and revert back to you. You can check the status of your question in <a href="#" onClick="submitfrm('2')">'view questions'</a> tab. </h2></td>
		</tr>
	</table>
	<table  id="thanks_edit" border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="display:none;position:fixed;">
		<tr width="100%">
			<td><h2  align="center">You have updated this question in Mindspark. We'll go through your question and revert back to you. You can check the status of your question in <a href="#" onClick="submitfrm('2')">'view questions'</a> tab. </h2></td>
		</tr>
	</table>

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
    
    <!-- <div id="trailContainer"> -->

	    <div id="containerHeader">	
		    <table id="childDetails">
				<tr>
					<td width="33%" id="addquestion" class="activatedTopic">
						<div id="actTopicCircle1" class="smallCircle <?php if ($tab==1) echo 'red'; ?>" style="cursor:pointer;" onClick="submitfrm('1')"></div>
						<label id="1" style="cursor:pointer;" class="pointer <?php if ($tab==1) echo 'textRed'; ?>" onClick="submitfrm('1')">Add a question 
						<span style="color:red"></span></label>
					</td>

					<td width="40%" id="viewquestion" class="activateTopicAll">
						<div id="actTopicCircle2" class="smallCircle <?php if ($tab!=1) echo 'red'; ?>" style="cursor:pointer;" onClick="submitfrm('2')" ></div>
						<label id="2" style="cursor:pointer;" class="pointer <?php if ($tab!=1) echo 'textRed'; ?>" onClick="submitfrm('2')">View questions<span style="color:red"></label>
					</td>

					<td width="43%" id="classRemediation" class="activateTopics">
						<div id="actTopicCircle3" style="cursor:pointer;">
							<a class="intro-launch-help" onclick="startIntro()" title="Help" style="cursor: help;"></a>
						<div>
					</td>
				</tr>
		    </table>
		</div>
		<?php if ($tab==1){ ?>
		<div id="containerBody2" style="display:block">
			<form name="addQues" method="post" onsubmit="return submit_que()">
				<input type="hidden" name="qcode" value="<?=$qcode?>">
				<input type="hidden" name="questionmaker" value="<?=$questionmaker?>">
				<table id="question_table_content" style="margin-top:20px;" width="90%">
					<tr>
						<td  id="question_type">
							<div><b>Select type of question :</b></div>	
							<input type="radio" name="question_type" id="quesTypeMCQ2"  <?php if($question_type=='MCQ-2') echo "checked";?>  onclick="showHideOptions(this.value)" value="MCQ-2">MCQ-2 (2 Options)
							<input  type="radio" name="question_type" id="quesTypeMCQ3" <?php if($question_type=='MCQ-3') echo "checked";?> onclick="showHideOptions(this.value)" value="MCQ-3" style="margin-left: 20px;">MCQ-3 (3 Options)
							<input  type="radio" name="question_type" id="quesTypeMCQ4" <?php if($question_type=='MCQ-4') echo "checked";?>  onclick="showHideOptions(this.value)" value="MCQ-4"  style="margin-left: 20px;">MCQ-4 (4 Options)
							<input  type="radio" name="question_type" id="quesTypeBlank" <?php if($question_type=='Blank') echo "checked";?> onclick="showHideOptions(this.value)" value="Blank" style="margin-left: 20px;">Blank Type		
						</td>
					</tr>
					<tr>
						<td id="question_caption" colspan="4">
							<div style="margin-top:2%;"><strong>Question :</strong></div> 
							<textarea class="mceEditor" id="div_question" name="div_question"  style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:150px; width:620px" onclick='triggerEditor(this);' class="quesdetails richEditor" placeholder="Enter question here.."><?=$question?></textarea> <!-- html_entity_decode -->
						</td>
				    </tr>

			 		<tr><td colspan="4"><div style="height:30px"></div></td></tr>
					<tr id='mcq'>
			          	<td colspan="4" id="div_options">
			          		<div ><strong>Options :</strong></div>
							<table border="0" width="100%">
								<tr width="100%">
									<td  id="rowOptionA" width="40%" >
										<table border="0"  width="100%" >
										<tr>
										<td><input type="radio" name="correct_answer" value="A" <?php echo $correct_answer=='A' ? "checked" : ""?>><strong> A.</strong></td>
										<td><textarea class="mceEditor" id="div_option_a" name="div_option_a" onclick="triggerEditor(this)" style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:40px; width:350px" ONKEYUP='insert_table(this);' class="quesdetails richEditor" placeholder="Enter option A here.."><?=$optiona?></textarea></td>
										</tr>
										</table>
									</td>

									<td id="rowOptionB"  width="40%">
										<table border="0"  width="100%" >
										<tr>
										<td><input type="radio" name="correct_answer" value="B" <?php echo $correct_answer=='B' ? "checked" : ""?>><strong> B.</strong></td>
										<td><textarea class="mceEditor" id="div_option_b" name="div_option_b" onclick="triggerEditor(this)" style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:40px; width:350px" ONKEYUP='insert_table(this);' class="quesdetails richEditor" placeholder="Enter option B here.."><?=$optionb?></textarea></td>
										</tr>
										</table>
									</td>
								</tr>

			          	  		<tr><td colspan="2">&nbsp;</td></tr>

			          	  		<tr>
									<td  id="rowOptionC"  width="40%">
										<table border="0"  width="100%" >
										<tr>
										<td><input type="radio" name="correct_answer" value="C" <?php echo $correct_answer=='C' ? "checked" : ""?>><strong> C.</strong></td>
										<td><textarea class="mceEditor" id="div_option_c" name="div_option_c" onclick="triggerEditor(this)" style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:40px; width:350px" ONKEYUP='insert_table(this);' class="quesdetails richEditor" placeholder="Enter option C here.."><?=$optionc?></textarea></td>
										</tr>
										</table>
									</td>
			          	        
									<td  id="rowOptionD"  width="40%">
										<table border="0"  width="100%">
										<tr>
										<td><input type="radio" name="correct_answer" value="D" <?php echo $correct_answer=='D' ? "checked" : ""?>><strong> D.</strong></td>
										<td><textarea class="mceEditor" id="div_option_d" name="div_option_d" onclick="triggerEditor(this)" style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:40px; width:350px" ONKEYUP='insert_table(this);' class="quesdetails richEditor" placeholder="Enter option D here.."><?=$optiond?></textarea></td>
										</tr>
										</table>
									</td>
			          	  		</tr>
						  	</table>
				      	</td>
					</tr>

					<tr id="correctAnswer" style="display:none">
			   			<td colspan="4">
			    			<div><strong>Answer :</strong></div>
			 				<input id="div_answer" name="div_answer"  placeholder="Enter correct answer..." style="border-style:solid; border: solid 1px #CCBBAA; background:white; overflow:auto; height:50px; width:350px" value="<?=$correct_answer?>"/>
						</td>
			    	</tr>
				
					<tr><td colspan="4">&nbsp;</td></tr>
			    	
			    	<tr id="remarks">
					    <td colspan="4">
							<div><strong>Any Remark/Comment :</strong></div> 
							<textarea name="remark" id="remark" cols="80" rows="5" placeholder="You can share how you got the idea for this question or what you think the performance on this question will be, etc.."><?=$remarks?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<div id="buttonSection" >
				    			<input type="submit" name="Submit" id="Submit" value="<?php if ($question!='') echo 'Update Question'; else echo 'Add Question';?>" class="buttons">
								<a href="otherFeatures.php" style="text-decoration:none">
									<input type="button" name="cancel" id="cancel" class="buttons" value="Cancel" onclick="">
								</a>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div> <!-- end of container body 2 -->
		<?php } else {
			$questionDetail=array();
			$questionDetail=getQuestionsAddedDetail($username);
			?>
		<div id="containerBody3">
			<table id="gridtable" class="gridtable flipped" width="100%" border="1" align="center" cellspacing="0" cellpadding="3">
				<thead>
					<tr>
						<td class="header" ><u>Sr.<br>No.</u></td>
						<td class="header" id="question_code"><u>Question  Code</u></td>
						<td class="header" id="question_content"><u>Question (Click on question  to preview)</u></td>
						<td class="header" id="edit_link"><u>Edit & Update the question</u></td>
						<?php if(strcasecmp(trim($_SESSION['admin']),"School Admin")==0) {?><td class="header" id="addedBy"><u>Added By</u></td><?php }?>
						<td class="header"><u>Date added </u></td>
						<td class="header" id="question_status"><u>Status </u></td>
					</tr>
					<?php $no=1;
					foreach($questionDetail as $key=>$row)
					{
					  $tmpDispAns = str_replace("&nbsp;","",removeTags($row['question'],1));

					?>
					<tr class="header_row">
						<td align="center"><?=$no++?></td>
						<td  align="center"><?=$row['qcode']?></td>

						<td><a href="#" onclick="viewNewInterface('<?=$row['qcode']?>')"><? if(strlen($tmpDispAns)>65){ echo substr($tmpDispAns,0,65)."...."; } else echo $tmpDispAns;?></a></td>
						<td  align="center"><a href="addQuestionTopic.php?qc=<?=$row['qcode']?>">Edit</a></td>
						<?php if(strcasecmp(trim($_SESSION['admin']),"School Admin")==0){echo '<td aling="center">'.$row['questionmaker'].'</td>'; }?>
						<td  align="center"><?=$row['submitdate']?></td>
						<td  align="center"><?=$row['status']?></td>
					</tr>
					<?php } ?>
				</thead>
			</table>
		</div> <!-- end of container body 3-->
		<?php } ?>
	</div>
<?php include("footer.php") ?>

