<?php
	set_time_limit(0);
	include("header.php");
	include("../slave_connectivity.php");

	include("../userInterface/functions/functions.php");
	include("../userInterface/functions/orig2htm.php");
	include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/classes/clsDiagnosticTestQuestion.php");
	include("../userInterface/constants.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("functions/topicReportFunctions.php");
	
	error_reporting(E_ERROR);
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	$animationQues	=	0;
	$totalQues	=	0;
	$userID      = $_SESSION['userID'];
	$schoolCode = $_SESSION['schoolCode'];
	$category    = $_SESSION['admin'];
	$schoolCodeArray = array();
  	$coteacherInterfaceFlag = 0;
	$class = $cls = $childClass = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$topic = $ttCode  = isset($_REQUEST['ttCode'])?$_REQUEST['ttCode']:"";
	$childSection = isset($_REQUEST['childSection'])?$_REQUEST['childSection']:"";
	$cwaType = isset($_REQUEST['cwaType'])?$_REQUEST['cwaType']:"2";	
	$class_array = array();
	if (strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Home Center Admin")==0)
	{
		$query = "SELECT DISTINCT(childClass)
		          FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND schoolCode=$schoolCode AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND endDate>=curdate()
				  ORDER BY childClass";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			if ($line[0]!="")
			{
				array_push($class_array, $line[0]);
			}
		}
	}
	elseif (strcasecmp($category,"TEACHER")==0)
	{
		$query = "SELECT DISTINCT class FROM adepts_teacherClassMapping WHERE userID=".$userID." AND subjectno=".SUBJECTNO." ORDER BY class";
		$result = mysql_query($query) or die("<br>Error in teacher class query - ".mysql_error());
		while($line=mysql_fetch_array($result))
		{
			if ($line[0]!="")
			{
				array_push($class_array, $line[0]);
			}
		}
	}
	else
	{
		echo "You are not authorised to access this page.";
		exit;
	}
	$query  = "SELECT schoolCode from adepts_rewardSystemPilot where flag=2";
	$result = mysql_query($query) or die(mysql_error());
	while($line   = mysql_fetch_array($result))
	{
	    $schoolCodeArray[] =$line[0];
	}
	if(in_array($schoolCode,  $schoolCodeArray) || empty($schoolCodeArray))
	{          
	    $coteacherInterfaceFlag = 1;
	} 

?>

<title>Common Wrong Answers</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/commonWrongAnswers.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type="text/javascript" src="../js/load.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
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
	function getMsg()
	{
		if($("#animationQues").val()>0)
		{
			if($("#totalQues").val()==$("#animationQues").val())
				alert("Please note that all the questions have animations/html5 interactives and hence will not get downloaded in the report.");
			else if($("#animationQues").val()==1)
				alert("Please note that "+$("#animationQues").val()+" question has an animation/html5 interactive and hence will not get downloaded in the report.");
			else
				alert("Please note that "+$("#animationQues").val()+" questions have animations/html5 interactives and hence will not get downloaded in the report.");
		}
		setTryingToUnload();
		$("#frmDownloadCWA").submit();
	}
	
	$(document).ready(function(e) {
		$(".cwa").each(function() {
			var details	=	$(this).attr("id");
			$.post("ajaxRequest.php","mode=commonWrongAnswer&quesDetails="+$("#"+details).next("input").val(),function(data) { 
				$("#"+details).html(data);
			});
		});
		$($('input[name=cwaType]:radio')).click(function() {
			if($(this).val()==1) {
				if(section_A[document.getElementById('childClass').value].length==0)
					$(".noSection").hide();
				else
					$(".noSection").show();
			}
			else
				$(".noSection").hide();
		});
	});

	function showAnswerPercentage(className,qcode)
	{		
		
		if($('#divAnswerPercentage'+className).text() == '+ Option-wise performance')
		{
			$('#divAnswerPercentage'+className).text('- Option-wise performance');
			$('.'+className).show();
		}else
		{
			$('.'+className).hide();
			$('#divAnswerPercentage'+className).text('+ Option-wise performance');
		}
	}
	function showMyDiv(divId)
	{
		if(divId == "all")
		{
			$('.viewallRecords').show();
			$('#paginationAll').css("border","6px solid #cccc91");
			$('#paginationbtAll').css("border","6px solid #cccc91");
			$('.paginationClass').css("border","6px solid #9ec955");
		}
		else
		{
			$('.viewallRecords').hide();
			$('.displayClass').hide();
			$('.paginationClass').css("border","6px solid #9ec955");
			$('#pagination'+divId).addClass("paginationClass");
			$('#pagination'+divId).css("border","6px solid #cccc91");
			$('#paginationbt'+divId).addClass("paginationClass");
			$('#paginationbt'+divId).css("border","6px solid #cccc91");
			$('#data'+divId).show();
			$('#data'+divId).addClass("displayClass");
			$('#datalist'+divId).show();
			$('#datalist'+divId).addClass("displayClass");
			$('#classWisePerformance'+divId).hide();
			$('#schoolAVG'+divId).hide();
			$('#nationalAVG'+divId).hide();
			$('#paginationAll').css("border","6px solid #9ec955");
			$('#paginationbtAll').css("border","6px solid #9ec955");
		}
	}
	function showclassWisePerformance(divId)
	{
		$('#classWisePerformance'+divId).show();
		$('#schoolAVG'+divId).hide();
		$('#nationalAVG'+divId).hide();
	}
	function schoolAVG(divId)
	{
		$('#schoolAVG'+divId).show();
		$('#classWisePerformance'+divId).hide();
		$('#nationalAVG'+divId).hide();
	}
	function nationalAVG(divId)
	{
		$('#nationalAVG'+divId).show();
		$('#classWisePerformance'+divId).hide();
		$('#schoolAVG'+divId).hide();
	}
	function disablebutton()
	{
		$("#btnDownload").removeAttr("onclick");
		$("#btnDownload").css('background-color','grey');
	}
</script>
<script language="javascript">
		var section_A={},code_A={},desc_A={};
		<?php
			for ($i=0; $i<count($class_array); $i++)
			{
				//$section_array_string = "var section_".$class_array[$i]." = new Array( ";
				$section_array_string = "section_A['".$class_array[$i]."'] = new Array( ";
				if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Home Center Admin")==0)
				{
					$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=".$schoolCode." AND childClass='".$class_array[$i]."' AND subjects LIKE '%".SUBJECTNO."%' AND endDate>=CURDATE() AND enabled=1 ORDER BY childSection";

				}
				elseif (strcasecmp($category,"TEACHER")==0)
				{
					$section_query = "SELECT DISTINCT(section) as sec FROM adepts_teacherClassMapping WHERE userID=$userID AND class=$class_array[$i] AND subjectno=".SUBJECTNO." ORDER BY section";
				}
				//echo "<br>Section Query is - ".$section_query;
				$section_result = mysql_query($section_query) or die("<br>Error in section query - ".mysql_error());
				$topic_code_array_string_section = array();
				$topic_desc_array_string_section = array();
				while ($section_data = mysql_fetch_array($section_result))
				{
					if ($section_data['sec']!="")
					{
						$section_array_string .= "'".$section_data['sec']."',";
						$topic_code_array_string_section[$section_data['sec']] = "code_A['".$class_array[$i]."_".$section_data['sec']."'] = new Array( ";
						$topic_desc_array_string_section[$section_data['sec']] = "desc_A['".$class_array[$i]."_".$section_data['sec']."'] = new Array( ";
					}
				}
				$section_array_string = substr($section_array_string, 0, -1);
				$section_array_string .= ");\n";
				print($section_array_string);
				$topic_code_array_string = "code_A['".$class_array[$i]."'] = new Array( ";
				$topic_desc_array_string = "desc_A['".$class_array[$i]."'] = new Array( ";
				$topic_query = "SELECT TTM.teacherTopicCode, teacherTopicDesc, TTA.section 
								FROM   adepts_teacherTopicActivation TTA, adepts_teacherTopicMaster TTM
								WHERE  TTA.teacherTopicCode=TTM.teacherTopicCode AND
								       TTA.schoolCode=$schoolCode AND
								       TTA.class=$class_array[$i]
									   AND subjectno=".SUBJECTNO."
								GROUP BY TTM.teacherTopicCode,TTA.section
								ORDER BY teacherTopicDesc";
				//echo "<br>Topic query is - ".$topic_query;
				$topic_result = mysql_query($topic_query) or die("<br>Error in topic query - ".mysql_error());
				while ($topic_data = mysql_fetch_array($topic_result))
				{
					if(strpos($topic_code_array_string,$topic_data['teacherTopicCode']) === false)
					{
						$topic_code_array_string .= "'".$topic_data['teacherTopicCode']."',";
						$topic_desc_array_string .= "'".$topic_data['teacherTopicDesc']."',";
					}
					if($topic_code_array_string_section[$topic_data['section']])
						$topic_code_array_string_section[$topic_data['section']] .= "'".$topic_data['teacherTopicCode']."',";
					if($topic_desc_array_string_section[$topic_data['section']])	
						$topic_desc_array_string_section[$topic_data['section']] .= "'".$topic_data['teacherTopicDesc']."',";
				}
				$topic_code_array_string = substr($topic_code_array_string, 0, -1);
				$topic_desc_array_string = substr($topic_desc_array_string, 0, -1);
				$topic_code_array_string .= ");\n";
				$topic_desc_array_string .= ");\n";
				print($topic_code_array_string);
				print($topic_desc_array_string);
				foreach($topic_code_array_string_section as $section1=>$topic_code_array_string_data1)
					print(substr($topic_code_array_string_data1, 0, -1).");\n");
				foreach($topic_desc_array_string_section as $section2=>$topic_code_array_string_data2)
					print(substr($topic_code_array_string_data2, 0, -1).");\n");
			}
		?>
		function validate()
		{
			if (document.getElementById('childClass').value=="")
			{
				alert('Please select class');
				document.getElementById('childClass').focus();
				return false;
			}
			if (document.getElementById('childSection').value=="All" && $("#childSection").is(":visible"))
			{
				alert('Please select section');
				document.getElementById('childSection').focus();
				return false;
			}
			if (document.getElementById('topic').value=="")
			{
				alert('Please select topic');
				document.getElementById('topic').focus();
				return false;
			}
			setTryingToUnload();
			return true;
		}
		function populateTopic()
		{
			removeAllOptions(document.frmWrongQuestion.topic);
			var childClass = document.getElementById('childClass').value;
			var childSection = document.getElementById('childSection').value;
			var topic = "<?=$topic?>";
			var Opt_New = document.createElement('option');
			Opt_New.text = "Select Topic";
			Opt_New.value = "";
			if (topic=="")
			{
				Opt_New.selected = true;
			}
			
			
			var el_Sel = document.getElementById('topic');
			el_Sel.options.add(Opt_New);
			if (childClass)
			{
				if(childSection && childSection!="All")
				{
					var x = code_A[childClass+'_'+childSection];
					var y = desc_A[childClass+'_'+childSection];
				}
				else
				{
					var x = code_A[childClass];
					var y = desc_A[childClass];					
				}
				//alert(x);
				
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					if(childSection && childSection!="All")
					{
						OptNew.text = desc_A[childClass+'_'+childSection][j];
						OptNew.value = code_A[childClass+'_'+childSection][j];
						if (code_A[childClass+'_'+childSection][j]==topic)
							OptNew.selected = true;
					}
					else
					{
						OptNew.text = desc_A[childClass][j];
						OptNew.value = code_A[childClass][j];
						if (code_A[childClass][j]==topic)
							OptNew.selected = true;
					}					
					
					var elSel = document.getElementById('topic');
					elSel.options.add(OptNew);
					
				}
			}
		}
		function populateSection()
		{
			removeAllOptions(document.frmWrongQuestion.childSection);
			var childClass = document.getElementById('childClass').value;
			var childSection = "<?=$childSection?>";
			if (childClass)
			{
				var x = section_A[childClass];
				var Opt_New = document.createElement('option');
				if (x.length != 1)
				{
					Opt_New.text = "All";
					Opt_New.value = "";
				}

				if (childSection=="")
				{
					Opt_New.selected = true;
				}
				var el_Sel = document.getElementById('childSection');
				if(x.length!=1)
				el_Sel.options.add(Opt_New);
				if(x.length==0)
					$(".noSection").hide();
				else
					$(".noSection").show();
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = section_A[childClass][j];
					OptNew.value = section_A[childClass][j];
					if (section_A[childClass][j]==childSection)
					{
						OptNew.selected = true;
					}
					var elSel = document.getElementById('childSection');
					elSel.options.add(OptNew);
				}
			}
			else
			{
				var Opt_New = document.createElement('option');
				Opt_New.text = "All";
				Opt_New.value = "";
				if (childSection=="")
				{
					Opt_New.selected = true;
				}
				var el_Sel = document.getElementById('childSection');
				el_Sel.options.add(Opt_New);
				$(".noSection").show();
				
			}
			if($('#nationalLevel').is(":checked"))
				$(".noSection").hide();
		}
		function addOption(obj, text, value, sel)
		{
			var OptNew = document.createElement('option');
			OptNew.text = text;
			OptNew.value = value;
			if(value==sel)
			 OptNew.selected = true;
			obj.options.add(OptNew);
		}
		function removeAllOptions(selectbox)
		{
			var i;
			for(i=selectbox.options.length-1;i>=0;i--)
			{
				selectbox.remove(i);
			}
		}
		function trim(str)
		{
			// Strip leading and trailing white-space
			return str.replace(/^\s*|\s*$/g, "");
		}

		function nextSDLQues(cluster,sdl,sdlqn)
		{
			document.getElementById('ques_'+cluster+'_'+sdl+'_'+sdlqn).style.display="none";
			document.getElementById('menu_'+cluster+'_'+sdl+'_'+sdlqn).style.display="none";

			var sdlqn = parseInt(sdlqn)+1;

			document.getElementById('ques_'+cluster+'_'+sdl+'_'+sdlqn).style.display="block";
			document.getElementById('menu_'+cluster+'_'+sdl+'_'+sdlqn).style.display="block";

		}

		function previousSDLQues(cluster,sdl,sdlqn)
		{
			document.getElementById('ques_'+cluster+'_'+sdl+'_'+sdlqn).style.display="none";
			document.getElementById('menu_'+cluster+'_'+sdl+'_'+sdlqn).style.display="none";

			var sdlqn = parseInt(sdlqn)-1;

			document.getElementById('ques_'+cluster+'_'+sdl+'_'+sdlqn).style.display="block";
			document.getElementById('menu_'+cluster+'_'+sdl+'_'+sdlqn).style.display="block";

		}

	</script>
</head>
<body class="translation" onLoad="populateSection();populateTopic();load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()" onResize="load()">
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

	<div id="container" style="font-size: 1.3em;">
	
	<form name="frmWrongQuestion" method="post" action="<?=$_SERVER['PHP_SELF']?>" style="">

		<table width="90%"  border="0" cellpadding="2" cellspacing="2" >
			<tr>
			<td class="contentTab">
			<div class="arrow-black" style="margin-top: 24px;"></div>
			<h3 class="pageTitle">Common Wrong Answers</h3>
			</td>
			<td class="contentTab">
			<div title="Common wrong answers in your class" style="font-size:1em;font-weight:normal;color:black"><input type="radio" name="cwaType" id="myClass" <?php if($cwaType==1) echo "checked" ?> value="1"><label for="myClass">Class Level</label></div>
			</td>
			<td class="contentTab">
			<div title="Common wrong answers based on data of all schools which took Mindspark" style="font-size:1em;font-weight:normal;color:black"><input type="radio" name="cwaType" id="nationalLevel" value="2" <?php if($cwaType==2) echo "checked" ?>><label for="nationalLevel">National Level</label></div>
			</td>
			<td class="contentTab" style="width: 25%;">
				<?php
		if (isset($ttCode) && $ttCode!="")
		{
			?>
				<div id="topicPageLink">
					<div class="pageText" ><h3><a href="<?=getTopicPageLink($ttCode,$class,$section);?>" style="text-decoration:none;">Topic Page / Research</a></h3></div>
				</div>
		<?php } ?>
			</td>
			</tr>
			</table>
			<hr>
			<table  border="0" cellpadding="2" cellspacing="2" style="max-width:10px">
			<tr>
				<td class="contentTab"><label for="childClass">Class</label></td>
				<td class="SelectTab">
					<select name="cls" id="childClass" onChange="populateSection();populateTopic()">
						<?=(count($class_array)!=1) ? '<option value="">Select</option>' : ''?>
						<?php
						for ($i=0; $i<count($class_array); $i++)
						{
							echo "<option value=\"$class_array[$i]\"";
							if ($class_array[$i]==$childClass)
							{
								echo " selected";
							}
							echo ">$class_array[$i]</option>";
						}
						?>
					</select>
				</td>
				<td class="contentTab noSection"><label for="childSection">Section</label></td>
				<td class="SelectTab noSection">
					<select name="childSection" id="childSection" onChange="populateTopic()">
						<option value="">All</option>
					</select>
				</td>
				<td class="contentTab noTopic"><label for="topic">Topic:</label></td>
				<td class="SelectTab noTopic">
					<select name="ttCode" id="topic" >
						<option value="">Select Topic</option>
					</select>
				</td>
				<td class="contentTab">
				<input style="margin-left: 15px;" type="submit" name="btnSubmit" id='btnSubmit' value="Submit" onClick="return validate();">
				</td>
				<td>
				<input type="button" title="Download all the questions of Common Wrong Answer Report" id="btnDownload" value="Download" onClick="getMsg()">
				</td>
				</tr>
		</table>
	
	
	<hr>
</form>
	<?php
	if (isset($ttCode) && $ttCode!="")
	{			
		?>
		<?php
		if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0)
			$subcategory = "School";
		else if(strcasecmp($category,"Home Center Admin")==0)
			$subcategory = "Home Center";
		/* Fetch all the attempted user */
		$userIdArray = array();
		$attemptedUserQuery = "SELECT userID FROM adepts_userDetails
							   WHERE  schoolcode = $schoolCode AND category='STUDENT' AND subcategory='$subcategory' AND endDate>=curdate() AND enabled=1 AND subjects like '%".SUBJECTNO."%'
									 AND childClass = $class";										 							
		if (isset($childSection) && $childSection!="")
		{			
			$attemptedUserQuery .= " AND childSection ='$childSection'";
		}		
		$userIdResult = mysql_query($attemptedUserQuery) or die(mysql_error());
		while($userIdRow = mysql_fetch_array($userIdResult))
		{
			array_push($userIdArray, $userIdRow[0]);
		}
		$userIDstr = implode(",", $userIdArray);
		if($cwaType==1)
		{
			$clusters = getClustersOfTopic($topic);
			if($coteacherInterfaceFlag == 1)
			{				
				$cwaDetails = getCommonWrongAnswer($clusters,$ttCode,$userIDstr,$class,$childSection,$userIdArray);								
				if(!empty($cwaDetails['cwaDetails']))
				{				
					$jCnt = 1;
					$SDLsrno = 1;				
					$countOfQueNo  =  count($cwaDetails['cwaDetails']);
					$SDL_questions_num = count($cwaDetails['cwaDetails']);
					$SDL_question_str = '';
					$SDL_menu = '';
					$SDL_menu_btn = '';
					$displayed_question = array();
					$qcodeStrForDownload = $cwaDetails['downloadStr'];
					$animationQues = $cwaDetails['animatedQuestions'];
					echo "<center><h3>Common wrong answer - Class level</h3></center><br>";
					echo "<center>";
					echo "<div style='width:100%;'>";
					for($t = 1 ; $t <= $countOfQueNo ; $t++)
					{
						if($t == 1)
							echo "<font class='paginationClass' id='pagination".$t."' style='width: 10px;padding: 4px;border: 6px solid #cccc91;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
						else
							echo "<font id='pagination".$t."' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
					}
					if($countOfQueNo > 1)
						echo "<font id='paginationAll' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv('all')>View All</font>";

					echo "</center>";
					echo "<br>";
					if($countOfQueNo == 0)
					{
						echo "<script>disablebutton();</script>";
					}	
					foreach($cwaDetails['cwaDetails'] as $data)
					{																	
						$question = $data['question'];
						$finalSDLPerSchool = $data['schoolAVG'];
						$finalSDLNational = $data['nationalAVG'];
						$finalSDLPerClass = $data['classWisePerformance'];
						$student_name_string = implode(',', $data['failedStudentList']);
						$quesDetailsArr = explode("~",$data['qcodeListData']);    	
						if($data['mode'] == 'getQuestion')
						{
							$question = getQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2], $quesDetailsArr[3], $quesDetailsArr[4], $quesDetailsArr[5],$cwaType,$quesDetailsArr[6],1);
						}
						else
						{
							$question = getDiagnosticQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2],1);
						}
						$SDL_question_str = '<div>'.$question.'<br/></div><hr>';
						$SDL_menu = '<div align="center">';						
						$SDLsrno = $SDLsrno + 1;
						if($jCnt == 1)
							echo "<div id='data".$jCnt."' class='displayClass viewallRecords' style='float:left; width:75%;'>";
							else
								echo "<div id='data".$jCnt."' class='viewallRecords' style='display:none;float:left; width:75%;'>";
							echo "<div class='ques_no'>".$jCnt."</div>";	
							echo "<div class='cwa_ques'>";
							echo $SDL_question_str;
						if($SDL_menu!='')
						{
							echo $SDL_menu.'</div>';
						}
						echo "</div>";
						echo "</div>";
						if($jCnt == 1)
							echo '<div id="datalist'.$jCnt.'" class="displayClass viewallRecords" style="float:right; width:20%; margin-left:10px;">';
						else
							echo '<div id="datalist'.$jCnt.'" class="displayClass viewallRecords" style="float:right; width:20%; margin-left:10px;display:none;">';
						echo "<table cellspacing='5'>";
						echo "<tr ><td title='Click here to see the average performance of this question, for all sections of this grade' style='color: white;cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888' bgcolor='#2f99cb' onclick='schoolAVG(".$jCnt.")'>School Average</td><td title='Click here to see the average performance of this question, across the nation.' bgcolor='#e75903' style='cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888;color:#FFF' onclick='nationalAVG(".$jCnt.")'>National Average</td></tr>";				
							echo "<tr><td align='center' title='Click here to see class performance for chosen section(s).' colspan=2 style='cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888' onclick='showclassWisePerformance(".$jCnt.")' bgcolor='#fbd212'>Class Performance</td></tr>";
						
						echo "</table>";
						echo "<br>";

						
						echo '<div style="display:none;background-color:#e75903;border-radius: 19px;text-align: center;color:#FFF" id="nationalAVG'.$jCnt.'"><b>% correct:</b> </span><span>'.$finalSDLNational.'%</div>';
						echo '<div style="color: white;display:none;background-color:#2f99cb;border-radius: 19px;text-align: center;" id="schoolAVG'.$jCnt.'"><b>% correct:</b> </span><span>'.$finalSDLPerSchool.'%</div>';
									
						if(!empty($student_name_string))
							echo '<div style="display:none;background-color:#fbd212;border-radius: 19px;text-align: center; padding: 4px;" id="classWisePerformance'.$jCnt.'"><span class="title" title="Child Name (Total Attempts)"><b>Avg % correct = </b>'.$finalSDLPerClass.'% <br><br> <b>Children who did not get it correct:</b> </span><span>'.$student_name_string.'</span></div>';
						else
							echo '<div style="display:none;background-color:#fbd212;border-radius: 19px;text-align: center; padding: 4px;" id="classWisePerformance'.$jCnt.'"><span class="title" title="Child Name (Total Attempts)"><b>Avg % correct</b> = '.$finalSDLPerClass.'%  </span></div>';				
						echo '</div>';
						$jCnt++;
					}																									
					echo '<br><br>';							
					
					?>
					<div align="center" style="margin-bottom:2%; width:100%;clear: both;">
						<form id="frmDownloadCWA" method="POST" target="_blank" action="downloadCWA.php">
							<input  type="hidden" name="qcodeStr" id="qcodeStr" value='<?=$qcodeStrForDownload?>'/>
							<input  type="hidden" name="class"  value="<?=$class?>"/>
							<input  type="hidden" name="section"  value="<?=$childSection?>"/>
							<input  type="hidden" name="ttCode"  value="<?=$topic?>"/>
							<input  type="hidden" name="totalQues" id="totalQues" value="<?=$totalQues?>"/>
							<input  type="hidden" name="animationQues" id="animationQues" value="<?=$animationQues?>"/>
							<?php
							
								echo "<br>";
								for($t = 1 ; $t <= $countOfQueNo ; $t++)
								{
									if($t == 1)
										echo "<font class='paginationClass' id='paginationbt".$t."' style='width: 10px;padding: 4px;border: 6px solid #cccc91;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
									else
										echo "<font id='paginationbt".$t."' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
								}
								if($countOfQueNo > 1)
									echo "<font id='paginationbtAll' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv('all')>View All</font>";
							?>
							
						</form>
					</div>				
						<?php
				}
				else
				{
					echo "<div align='center'><h3>There are no Common Wrong Answers. The class is doing well.</h3></div>";
				}
			}
			else
			{
				$noofsdls = 0;
				$k=0;
				/* Fetched all the SDLs of the clusters */				
				foreach ($clusters as $val)
				{
					$clusterCode[$k] = $val;
	
					$query = "SELECT a.clusterCode, subdifficultylevel, count(srno), SUM(R), group_concat(distinct q.qcode), count(distinct a.userID) as distinct_users
							  FROM  ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q, ".TBL_CLUSTER_STATUS." cs, ".TBL_TOPIC_STATUS." ts
							  WHERE a.clusterAttemptID = cs.clusterAttemptID AND
									cs.userID = ts.userID AND
									ts.userID IN ($userIDstr) AND
									a.qcode = q.qcode AND
									cs.ttAttemptID = ts.ttAttemptID AND
									cs.clusterCode='$clusterCode[$k]' AND
									ts.teacherTopicCode = '$topic'
							  GROUP BY subdifficultylevel";
					$sdl_result = mysql_query($query) or die("Error in SDL query - ".mysql_error());
					while($sdl_row = mysql_fetch_array($sdl_result))
					{
						$tempCluster[$noofsdls]   =  $sdl_row[0];
						$tempSDL[$noofsdls]       = 	$sdl_row[1];
						$tempSDLTotal[$noofsdls]  =  $sdl_row[2];
						$tempSDLRight[$noofsdls]  =  $sdl_row[3];
						$tempSDLWrong[$noofsdls]  =  $tempSDLTotal[$noofsdls]-$tempSDLRight[$noofsdls];
						$tempSDLRhtPer[$noofsdls] =  ($tempSDLRight[$noofsdls]*100)/$tempSDLTotal[$noofsdls];
						$tempSDLQcodes[$noofsdls] = $sdl_row[4];
						$tempSDLDdisUserAtmp[$noofsdls] = $sdl_row[5];
						$noofsdls++;
					}
				}				
				if($noofsdls > 0)
				{
					$finalSDLClusterArray = array();     // Contain Cat1 and Cat2 clusters
					$finalSDLArray = array();            // Contain Cat1 and Cat2 SDLs
					$finalSDLQuesArray = array();		 // Contain the questions of that SDL
					$finalSDLattemptsArray = array();
					$finalSDLCorrectAttemptArray = array();
					$finalSDLPerClassArray = array();
					$finalSDLdisUserAtmpArray = array();
					$finalSDLPerSchoolArray = array();
					$finalSDLPerNationalArray = array();
					arsort($tempSDLWrong);
					
					$harderSDLnum = 0;
					$harderSDLCat2num = 0;
					foreach ($tempSDLWrong as $i => $value)
					{
						if($harderSDLnum<5) 			// Select 5 SDLs for Cat1
						{
							array_push($finalSDLClusterArray, $tempCluster[$i]);
							array_push($finalSDLArray, $tempSDL[$i]);
							array_push($finalSDLQuesArray, $tempSDLQcodes[$i]);
							array_push($finalSDLattemptsArray, $tempSDLTotal[$i]);
							array_push($finalSDLCorrectAttemptArray, $tempSDLRight[$i]);
							array_push($finalSDLPerClassArray, $tempSDLRhtPer[$i]);
							array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$tempCluster[$i],$tempSDL[$i],$class));
							array_push($finalSDLPerNationalArray, getNationalAvg($tempCluster[$i],$tempSDL[$i],$class));
							array_push($finalSDLdisUserAtmpArray, $tempSDLDdisUserAtmp[$i]);
						}
						else
						{
							if($harderSDLCat2num<5 && $tempSDLRhtPer[$i]<=60 && $tempSDLTotal[$i]>40)
							{  // Select 5 Sdls for Cat2
								array_push($finalSDLClusterArray, $tempCluster[$i]);
								array_push($finalSDLArray, $tempSDL[$i]);
								array_push($finalSDLQuesArray, $tempSDLQcodes[$i]);
								array_push($finalSDLattemptsArray, $tempSDLTotal[$i]);
								array_push($finalSDLCorrectAttemptArray, $tempSDLRight[$i]);
								array_push($finalSDLPerClassArray, $tempSDLRhtPer[$i]);
								array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$tempCluster[$i],$tempSDL[$i],$class));
								array_push($finalSDLPerNationalArray, getNationalAvg($tempCluster[$i],$tempSDL[$i],$class));
								array_push($finalSDLdisUserAtmpArray, $tempSDLDdisUserAtmp[$i]);
								$harderSDLCat2num++;
							}
							elseif ($harderSDLCat2num>=5)
								break;
						}
						$harderSDLnum++;
					}
					$countOfQueNo =  count($finalSDLClusterArray);
					echo "<center><h3>Common wrong answer - Class level</h3></center><br>";
					echo "<center>";
					for($t = 1 ; $t <= $countOfQueNo ; $t++)
					{
						if($t == 1)
							echo "<font class='paginationClass' id='pagination".$t."' style='width: 10px;padding: 4px;border: 6px solid #cccc91;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
						else
							echo "<font id='pagination".$t."' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
					}
					if($countOfQueNo > 1)
						echo "<font id='paginationAll' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv('all')>View All</font>";
	
					echo "</center>";
					echo "<br>";
				
					/* Show all the Sdls of Cat1 and Cat2 */
					$qcodeStrForDownload = array();
					$jCnt = 1;				
					echo "<div style='width:100%;'>";
					
					if($countOfQueNo == 0)
					{
						echo "<script>disablebutton();</script>";
					}
					foreach ($finalSDLClusterArray as $i => $value)
					{
	
						$currentTempCluster = $finalSDLClusterArray[$i];
						$currentTempSDL = $finalSDLArray[$i];
						$currentTempSDLQues = $finalSDLQuesArray[$i];
						$SDLQuesArray = array();
						$SDLQuesArray = explode(',',$currentTempSDLQues);

						if($currentTempSDL=="")		//For practice cluster, sdl will be blank - currently ignore such questions
							continue;
	
						$SDLsrno = 1;
						$displayed_question = array();
	
						$SDL_questions_num = count($SDLQuesArray);
						$SDL_question_str = '';
						$SDL_menu = '';
						$SDL_menu_btn = '';
	
						$clusterAtttempt_query = "SELECT clusterAttemptID FROM ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b WHERE a.ttAttemptID=b.ttAttemptID AND a.userID in ($userIDstr) AND teacherTopicCode='$topic' AND clusterCode='$currentTempCluster'";
						$clusterAttempt_result = mysql_query($clusterAtttempt_query);
						$clusterAttemptStr  = "";
						while ($clusterAttempt_line = mysql_fetch_array($clusterAttempt_result))
						   $clusterAttemptStr .= $clusterAttempt_line[0].",";
						$clusterAttemptStr = substr($clusterAttemptStr,0,-1);
	
						$student_name_string = "";
						$neverRightStudent = 0;
						if($clusterAttemptStr!="")
						{
							$student_name_query = "SELECT u.userID, childName, childClass, childSection, sum(R), count(srno) as cnt
												   FROM   adepts_userDetails u, ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q
												   WHERE  u.userID IN ($userIDstr) AND
														  a.userID= u.userID       AND
														  a.clusterAttemptID in ($clusterAttemptStr) AND
														  q.clusterCode='$currentTempCluster' AND
														  q.subdifficultylevel=$currentTempSDL AND
														  a.clusterCode = q.clusterCode AND
														  q.qcode = a.qcode";
							if (isset($childSection) && $childSection!="")
							{
								$student_name_query .= " AND childSection = '$childSection'";
							}
							
							$student_name_query .= " GROUP BY u.userID";							
							$student_name_result = mysql_query($student_name_query) or die("Error in student name query - ".mysql_error());
							$total_count = mysql_num_rows($student_name_result);
	
							while($student_name_data=mysql_fetch_array($student_name_result))
							{
								if ($student_name_data['sum(R)']==0 && $student_name_data['cnt'] >= 3)  //Show only the list of students who have not got it correct even once
								{
									$classSectionStr = $student_name_data['childClass'].$student_name_data['childSection'];
									if($student_name_data['childSection'] != "")
										$student_name_string .= $student_name_data['childName']." (".$classSectionStr."), ";
									else
										$student_name_string .= $student_name_data['childName'].", ";
									$neverRightStudent++;
								}
							}
							$student_name_string = substr($student_name_string, 0, -2);
						}
						$qcodeArray = array();
						foreach ($SDLQuesArray as $j => $value)
						{
							array_push($qcodeArray,$SDLQuesArray[$j]);
						}
						$QcodeString = implode(',',$qcodeArray);
						foreach ($SDLQuesArray as $j => $value)
						{
							$leastPerformancequery = "SELECT sum(R)/COUNT(srno) as accr , qcode FROM ".TBL_QUES_ATTEMPT."_class$class 
														WHERE userID IN ($userIDstr) AND qcode in ($QcodeString) AND teacherTopicCode='$ttCode' group by qcode order by accr";

							$Qcode_result = mysql_query($leastPerformancequery);
							$qcodeList = mysql_fetch_row($Qcode_result);
						}
						$leastPerformedQcode = $qcodeList[1];											
						
						$question = getQuestionData($leastPerformedQcode, $schoolCode, $class, $childSection, $SDLsrno, $userIDstr, $cwaType,$ttCode,1);
						
						$divDisplayStr = '';
						if($SDLsrno!=1)
							$divDisplayStr = 'style="display:none" ';
						else
							$qcodeStrForDownload['topic'][] = $leastPerformedQcode;	//Show only the first question of each SDL for download

						$SDL_question_str = $SDL_question_str.'<div id="ques_'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'" '.$divDisplayStr.'>'.$question.'<br/></div><hr>';
						

						$menuDisplayStr = '';
						if($SDLsrno!=1)
							$menuDisplayStr = 'style="display:none" ';
						$SDL_temp_menu_btn = '';
						if($SDL_questions_num>1)
						{
						
							$SDL_menu = '<div id="'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'_menu" align="center">';
							if($SDLsrno!=1)
							{
								$SDL_temp_menu_btn = $SDL_temp_menu_btn.'<a href="javascript:previousSDLQues(\''.$currentTempCluster.'\',\''.$currentTempSDL.'\',\''.$SDLsrno.'\');"><u>Previous</u></a>&nbsp;&nbsp;&nbsp;&nbsp;';
							}								
							$SDL_menu_btn = $SDL_menu_btn.'<div id="menu_'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'" '.$menuDisplayStr.'>'.$SDL_temp_menu_btn.'</div>';

						}
						array_push($displayed_question, $SDLQuesArray[$j]);
						$SDLsrno = $SDLsrno + 1;
					
						if($jCnt == 1)
							echo "<div id='data".$jCnt."' class='displayClass viewallRecords' style='float:left; width:75%;'>";
						else
							echo "<div id='data".$jCnt."' class='viewallRecords' style='display:none;float:left; width:75%;'>";
						echo "<div class='ques_no'>".$jCnt."</div>";	
						echo "<div class='cwa_ques'>";
						echo $SDL_question_str;
						if($SDL_menu!='')
						{
							echo $SDL_menu.$SDL_menu_btn.'</div>';
						}
						echo "</div>";
						?>						
						</div>
						<?php						
						if($jCnt == 1)
							echo '<div id="datalist'.$jCnt.'" class="displayClass viewallRecords" style="float:right; width:20%; margin-left:10px;">';
						else
							echo '<div id="datalist'.$jCnt.'" class="displayClass viewallRecords" style="float:right; width:20%; margin-left:10px;display:none;">';
						echo "<table cellspacing='5'>";
						echo "<tr ><td title='Click here to see the average performance of this question, for all sections of this grade' style='color: white;cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888' bgcolor='#2f99cb' onclick='schoolAVG(".$jCnt.")'>School Average</td><td title='Click here to see the average performance of this question, across the nation.' bgcolor='#e75903' style='cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888;color:#FFF' onclick='nationalAVG(".$jCnt.")'>National Average</td></tr>";
						
							echo "<tr><td align='center' title='Click here to see class performance for chosen section(s).' colspan=2 style='cursor:pointer;border-radius: 19px;text-align: center;box-shadow:2px 4px 2px #888888' onclick='showclassWisePerformance(".$jCnt.")' bgcolor='#fbd212'>Class Performance</td></tr>";
					
						echo "</table>";
						echo "<br>";

						
						echo '<div style="display:none;background-color:#e75903;border-radius: 19px;text-align: center;color:#FFF" id="nationalAVG'.$jCnt.'"><b>% correct:</b> </span><span>'.$finalSDLPerNationalArray[$i].'%</div>';
						echo '<div style="color: white;display:none;background-color:#2f99cb;border-radius: 19px;text-align: center;" id="schoolAVG'.$jCnt.'"><b>% correct:</b> </span><span>'.$finalSDLPerSchoolArray[$i].'%</div>';
					
					
						if(!empty($student_name_string))
							echo '<div style="display:none;background-color:#fbd212;border-radius: 19px;text-align: center; padding: 4px;" id="classWisePerformance'.$jCnt.'"><span class="title" title="Child Name (Total Attempts)"><b>Avg % correct = </b>'.number_format($finalSDLPerClassArray[$i], 1, '.', '').'% <br><br> <b>Children who did not get it correct:</b> </span><span>'.$student_name_string.'</span></div>';
						else
							echo '<div style="display:none;background-color:#fbd212;border-radius: 19px;text-align: center; padding: 4px;" id="classWisePerformance'.$jCnt.'"><span class="title" title="Child Name (Total Attempts)"><b>Avg % correct</b> = '.number_format($finalSDLPerClassArray[$i], 1, '.', '').'%  </span></div>';
					
						echo '</div>';
						$jCnt++;
					}
					echo '</div>';				
					echo '<br><br>';
					$qcodeStrForDownloadStr =json_encode($qcodeStrForDownload);
						?>
					<div align="center" style="margin-bottom:2%; width:100%;	clear: both;">
						<form id="frmDownloadCWA" method="POST" target="_blank" action="downloadCWA.php">
							<input  type="hidden" name="qcodeStr" id="qcodeStr" value='<?=$qcodeStrForDownloadStr?>'/>
							<input  type="hidden" name="class"  value="<?=$class?>"/>
							<input  type="hidden" name="section"  value="<?=$childSection?>"/>
							<input  type="hidden" name="ttCode"  value="<?=$topic?>"/>
							<input  type="hidden" name="totalQues" id="totalQues" value="<?=$totalQues?>"/>
							<input  type="hidden" name="animationQues" id="animationQues" value="<?=$animationQues?>"/>
							<?php
							
								echo "<br>";
								for($t = 1 ; $t <= $countOfQueNo ; $t++)
								{
									if($t == 1)
										echo "<font class='paginationClass' id='paginationbt".$t."' style='width: 10px;padding: 4px;border: 6px solid #cccc91;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
									else
										echo "<font id='paginationbt".$t."' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv(".$t.")>".$t."</font>";
								}
								if($countOfQueNo > 1)
									echo "<font id='paginationbtAll' style='width: 10px;padding: 4px;border: 6px solid #9ec955;cursor:pointer;' onclick=showMyDiv('all')>View All</font>";
							?>
							
						</form>
					</div>
					<?php
				}
				else
				{
					echo "<div align='center'><h3>No student of class $class has attempted questions of this topic.</h3></div>";
				}
			}			
		}
		else if($cwaType==2)
		{
			$qcodeStrForDownload = array();
			$totalQues = 0;
			$SDLsrno = 1;
			echo "<center><h3>Common wrong answer - National level</h3></center>";
			$SDLArray = getMisconceptionSdlsForTopic1($ttCode,$class);
			$noofclusters = count($SDLArray);
			if($noofclusters > 0)
			{
				echo "<div id='questionContainer'><span style='margin-left: 10px;font-size: 1.2em;margin-top: 2px;float: left;'>This is based on the response data of all schools which took Mindspark.</span>";
				foreach($SDLArray as $clusterCode=>$sdlList)
				{
					$currentTempCluster = $clusterCode;
					$finalSDLArray = explode(",",$sdlList);
					foreach($finalSDLArray as $currentTempSDL)
					{
						$query = "SELECT subdifficultylevel, qcode as sdlquestions FROM adepts_questions
								  WHERE  subdifficultylevel=$currentTempSDL AND clusterCode='$currentTempCluster' AND context<>'US' ORDER BY RAND() LIMIT 1";
		
						$result = mysql_query($query);
						$currentTempSDLQues = "";
						while ($line=mysql_fetch_array($result))
						{
							$currentTempSDLQues=$line[1];
						}
						$SDLQuesArray = array();
						if($currentTempSDLQues != "")
						{
							$SDLQuesArray = explode(',',$currentTempSDLQues);
							$qcode = $SDLQuesArray[array_rand($SDLQuesArray,1)];								
							$qcodeStrForDownload['topic'][]= $qcode;
							$totalQues++;
							$question = getQuestionData($qcode, $schoolCode, $class, $section, $SDLsrno, $userIDstr, $cwaType,$ttCode,1);
							$misconceptionStatement = getMisconceptionStatement($qcode);
							if($misconceptionStatement!="")
								$misconceptionStatement = "<br><b>Misconception:</b> ".$misconceptionStatement;
							$SDL_question_str = '<div class="question">'.$question.$misconceptionStatement.'<br/></div>';
							echo $SDL_question_str;
						}
					}
				}
				echo "</div>";
				$qcodeStrForDownloadStr = json_encode($qcodeStrForDownload);
				?>
				<form id="frmDownloadCWA" method="POST" target="_blank" action="downloadCWA.php">
					<input type="hidden" name="qcodeStr" id="qcodeStr" value='<?=$qcodeStrForDownloadStr?>'/>
					<input type="hidden" name="class"  value="<?=$class?>"/>
					<input type="hidden" name="section"  value="<?=$childSection?>"/>
					<input type="hidden" name="ttCode"  value="<?=$ttCode?>"/>
					<input type="hidden" name="totalQues" id="totalQues" value="<?=$totalQues?>"/>
					<input type="hidden" name="animationQues" id="animationQues" value="<?=$animationQues?>"/>
				</form>
				<?php					
			}
			else
			{
				echo "<div align='center'><br /><br /><h3>No data found.</h3></div>";
			}
		}
	}
?>
	
	</div>

<?php

function getClustersOfTopic($ttCode)
{
    $clusterArray = array();
    $query  = "SELECT customTopic, customCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);

    $customTopic = $line[0];
    $customCode  = $line[1];
    if(!$customTopic)
    {
        $clusterQuery = "SELECT a.clusterCode FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
    					 WHERE  a.clusterCode=b.clusterCode AND teacherTopicCode='$ttCode' AND b.status='live' ORDER BY a.flowno";
    	$result       = mysql_query($clusterQuery) or die(mysql_error());
    	while ($line = mysql_fetch_array($result)) {
    		array_push($clusterArray,$line[0]);
    	}
    }
    else
    {
        $query = "SELECT clusterCodes FROM adepts_customizedTopicDetails where code=$customCode";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $clusterArray = explode(",",$line[0]);
    }
    return $clusterArray;
}
function getDefaultFlowForTheSchool($schoolCode){

	$defaultFlow = 'MS';

	$flow_query  = "SELECT settingValue FROM userInterfaceSettings WHERE schoolCode='$schoolCode' and settingName='curriculum' limit 1";

	$flow_result = mysql_query($flow_query);

	if($flow_line=mysql_fetch_assoc($flow_result))
	{
				$defaultFlow = $flow_line['settingValue'];		
	}
	
	return $defaultFlow;

}


function getTopicPageLink($passedttCode,$passedClass,$passedSection){
	$schoolCode = $_SESSION['schoolCode'];

	/*$flow_query  = "SELECT defaultFlow, allowDeactivatedTopicsAtHome FROM adepts_schoolRegistration WHERE school_code=$schoolCode";
	$flow_result = mysql_query($flow_query);
	if($flow_line=mysql_fetch_array($flow_result))
	{
		$defaultFlow = $flow_line[0];
	}*/

	$defaultFlow = getDefaultFlowForTheSchool($schoolCode);

	$sql = "select a.teacherTopicDesc,b.teacherTopicCode,b.flow,a.parentTeacherTopicCode,a.customCode,a.customTopic from adepts_teacherTopicMaster a left join adepts_teacherTopicActivation b on a.teacherTopicCode=b.teacherTopicCode and b.class=$passedClass and b.section='$passedSection' and  b.schoolCode=$schoolCode where a.teacherTopicCode='$passedttCode'";
	$query =  mysql_query($sql)or die(mysql_error());
	$result=mysql_fetch_row($query);

	$flowToPass = $result[2] == ''? $defaultFlow : $result[2];
	$flow = $flowToPass;
	if($result[5] != 0){
		$clsLevelArray = getClassLevel($result[3],$defaultFlow);
		if($result[1] == ''){
			$flow = 'Custom - '.$result[4];
		}
	}
	else{
		$clsLevelArray = getClassLevel($passedttCode,$flowToPass);
	}

	$activeMode ="";
	if($result[1] == '' && $result[5] == 0){
		$activeMode = "&activateMode=yes";
	}


	$clsLevel = "";
	if(count($clsLevelArray)>0)
		$clsLevel = implode(",",$clsLevelArray);
	$class_explode = explode(",",$clsLevel);
	$max_grade = max($class_explode);
	$min_grade = min($class_explode);
	for($a=$min_grade;$a<=$max_grade;$a++){
		if($a==$cls){
			$pos=1;
		}
	}
	if ($max_grade==$min_grade)
	{
		$grade = $min_grade;
	}
	else
	{
		$grade = $min_grade."-".$max_grade;
	}
	if($max_grade=="" && $min_grade==""){
		$grade = $cls;
	}
	if ($max_grade == $cls && $min_grade == $cls)
	{
		$grade = $cls;
	}
	return "mytopics.php?ttCode=$passedttCode&cls=$passedClass&section=$passedSection&flow=$flow&interface=new&gradeRange=".$grade.$activeMode;
}

?>

<?php include("footer.php") ?>