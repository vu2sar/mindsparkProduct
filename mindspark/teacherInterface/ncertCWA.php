<?php
	set_time_limit(0);
	include("header.php");
	include("../slave_connectivity.php");
	include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsNCERTQuestion.php");
    include_once("../userInterface/classes/clsTeacherTopic.php");
	error_reporting(E_ERROR);
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	$userID      = $_SESSION['userID'];
	$school_code = $_SESSION['schoolCode'];
	$category    = $_SESSION['admin'];



	$class    = isset($_POST['childClass'])?$_POST['childClass']:"";
	$topic  = isset($_POST['topic'])?$_POST['topic']:"";
	$childSection = isset($_POST['childSection'])?$_POST['childSection']:"";

	$class_array = array();
	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT distinct childClass FROM adepts_userDetails a, adepts_ncertExerciseMaster b WHERE a.childClass=b.class AND schoolCode=$school_code AND category='STUDENT' AND subcategory='School' AND endDate>=curdate() AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND childClass>5 AND status='Live' ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			array_push($class_array, $line[0]);
		}
	}
	else if (strcasecmp($category,"TEACHER")==0)
	{
		$query = "SELECT distinct a.class FROM adepts_teacherClassMapping a, adepts_ncertExerciseMaster b WHERE a.class=b.class AND status='Live' AND userID=$userID  AND subjectno=".SUBJECTNO;
		$result = mysql_query($query) or die("<br>Error in teacher class query - ".mysql_error());
		while($line=mysql_fetch_array($result))
		{
			array_push($class_array, $line[0]);
		}
	}
	else
	{
		echo "You are not authorised to access this page.";
		exit;
	}
	$childClass = isset($_REQUEST['childClass'])?$_REQUEST['childClass']:"";
?>

<title>NCERT Common Wrong Answers</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" style="text/css" href="css/colorbox.css">
<link href="css/ncertCWA.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
	<script language="javascript">
		<?php
			for ($i=0; $i<count($class_array); $i++)
			{
				$section_array_string = "var section_".$class_array[$i]." = new Array( ";
				if(strcasecmp($category,"School Admin")==0)
				{
					$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=".$school_code." AND childClass='".$class_array[$i]."' AND subjects LIKE '%".SUBJECTNO."%' ORDER BY childSection";

				}
				elseif (strcasecmp($category,"TEACHER")==0)
				{
					$section_query = "SELECT DISTINCT(section) as sec FROM adepts_teacherClassMapping WHERE userID=$userID AND class=$class_array[$i] AND subjectno=".SUBJECTNO." ORDER BY section";
				}
				//echo "<br>Section Query is - ".$section_query;
				$section_result = mysql_query($section_query) or die("<br>Error in section query - ".mysql_error());
				while ($section_data = mysql_fetch_array($section_result))
				{
					if ($section_data['sec']!="")
					{
						$section_array_string .= "'".$section_data['sec']."',";
					}
				}
				$section_array_string = substr($section_array_string, 0, -1);
				$section_array_string .= ");\n";
				print($section_array_string);
				$topic_code_array_string = "var code_".$class_array[$i]." = new Array( ";
				$topic_desc_array_string = "var desc_".$class_array[$i]." = new Array( ";
				$topic_query = "SELECT a.exerciseCode, CONCAT('Exercise ',chapterNo,'.',exerciseNo)
								FROM   adepts_ncertHomeworkActivation a, adepts_ncertExerciseMaster b
								WHERE  a.exerciseCode=b.exerciseCode AND
								       a.schoolCode=$school_code AND
								       a.class=$class_array[$i]
								GROUP BY a.exerciseCode
								ORDER BY chapterNo,exerciseNo";
				//echo "<br>Topic query is - ".$topic_query;
				$topic_result = mysql_query($topic_query) or die("<br>Error in topic query - ".mysql_error());
				while ($topic_data = mysql_fetch_array($topic_result))
				{
					$topic_code_array_string .= "'".$topic_data[0]."',";
					$topic_desc_array_string .= "'".$topic_data[1]."',";
				}
				$topic_code_array_string = substr($topic_code_array_string, 0, -1);
				$topic_desc_array_string = substr($topic_desc_array_string, 0, -1);
				$topic_code_array_string .= ");\n";
				$topic_desc_array_string .= ");\n";
				print($topic_code_array_string);
				print($topic_desc_array_string);
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
			if (document.getElementById('topic').value=="")
			{
				alert('Please select topic');
				document.getElementById('topic').focus();
				return false;
			}
			return true;
		}
		function populateTopic()
		{
			removeAllOptions(document.frmWrongQuestion.topic);
			var childClass = document.getElementById('childClass').value;
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
				var x = eval('code_'+childClass);
				var y = eval('desc_'+childClass);
				//alert(x);
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('desc_'+childClass)[j];
					OptNew.value = eval('code_'+childClass)[j];
					if (eval('code_'+childClass)[j]==topic)
					{
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
				var x = eval('section_'+childClass);
				var Opt_New = document.createElement('option');
				var elSel = document.getElementById('childSection');
				if(x.length != 1)
				{
					var OptNew = document.createElement('option');
					OptNew.text = "All sections";
					OptNew.value = "";
					Opt_New.selected = true;
					elSel.options.add(OptNew);
				}
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('section_'+childClass)[j];
					OptNew.value = eval('section_'+childClass)[j];
					if (eval('section_'+childClass)[j]==childSection)
					{
						OptNew.selected = true;
					}
					var elSel = document.getElementById('childSection');
					elSel.options.add(OptNew);
				}

				if(x.length >= 1)
					$(".noSection").show();
				else
					$(".noSection").hide();
			}
			else
			{
				var Opt_New = document.createElement('option');
				Opt_New.text = "Select";
				Opt_New.value = "";
				if (childSection=="")
				{
					Opt_New.selected = true;
				}
				var el_Sel = document.getElementById('childSection');
				el_Sel.options.add(Opt_New);
				/*document.getElementById('childSection').disabled = true;*/
				$(".noSection").hide();
			}
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
</script>
</head>
<body class="translation" onload="load();populateSection();populateTopic();" onresize="load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()">
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
		<div id="trailContainer">
			<div id="headerBar">
				<div id="pageName">
					<div class="arrow-black"></div>
					<div id="pageText">NCERT COMMON WRONG ANSWERS</div>
				</div>
				<input type="button" class="button" id="selectClass" name="back" value="Select Another Class" onclick="javascript:window.location='activateHomework.php';">
			</div>
			<form id="frmWrongQuestion" name="frmWrongQuestion" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<table id="topicDetails">
				<td width="6%"><label for="childClass">Class</label></td>
		        <td width="25%">
		            <select name="childClass" id="childClass" onchange="populateSection();populateTopic()" style="width:65%">               
						<?php
						if(count($class_array) != 1)
							echo '<option value="">Select</option>';
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
				<td width="6%" class="noSection"><label for="childSection">Section:</label></td>
				<td width="25%" class="noSection">
					<select name="childSection" id="childSection" style="width:65%">
                    </select>
				</td>
				<td width="6%"><label for="topic">Exercise:</label></td>
                <td width="25%">
                    <select name="topic" id="topic"  style="width:65%">
                        <option value="">Select Topic</option>
                    </select>
                </td>
			</table>
			<table id="generateTable">
				<td id="showActivated" width="25%"><div id="checkActive"><input type="submit" class="button" name="btnGenerate" id="generate" value="Generate" onclick="return validate();"></div></td>			
			</table>
			</form>
			<?php
		if (isset($_POST['btnGenerate']) && $_POST['btnGenerate']=="Generate")
		{
			/* Fetch all the attempted user */
			echo "<div id='questionContainer'>";
			$userIdArray = array();
			$attemptedUserQuery = "SELECT userID FROM adepts_userDetails
			                       WHERE  schoolcode = $school_code AND category='STUDENT' AND subcategory='School'
										 AND childClass = '$class'";
			if (isset($childSection) && $childSection!="")
			{
				$attemptedUserQuery .= " AND childSection = '$childSection'";
			}

			$userIdResult = mysql_query($attemptedUserQuery) or die(mysql_error());
			while($userIdRow = mysql_fetch_array($userIdResult))
			{
				array_push($userIdArray, $userIdRow[0]);
			}

			$userIdStr = implode(",", $userIdArray);


			$k=0;
			$clusterArray = array($topic);
			$noofsdls = 0;

			/* Fetched all the SDLs of the clusters */
			//while($clusters = mysql_fetch_array($result1))
			foreach ($clusterArray as $val)
			{
				//$clusterCode[$k] = $clusters[0];
				$clusterCode[$k] = $val;
				
				$query = "SELECT a.exerciseCode, groupNo, count(srno), SUM(R), group_concat(distinct q.qcode), count(distinct a.userID) as distinct_users
						  FROM  adepts_ncertQuesAttempt a, adepts_ncertQuestions q, adepts_groupInstruction g
					      WHERE a.userID IN ($userIdStr) AND
					            a.qcode = q.qcode AND
					            g.groupID = q.groupID AND
					            a.exerciseCode='$clusterCode[$k]' AND
								R != -1 AND R != 3
							HAVING COUNT(srno)>0";
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
			/*END  fetched all the SDLs of attempted clusters */


			if($noofsdls > 0)
			{
				$finalSDLClusterArray = array();     // Contain Cat1 and Cat2 clusters
				$finalSDLArray = array();            // Contain Cat1 and Cat2 SDLs
				$finalSDLQuesArray = array();		 // Contain the questions of that SDL
				$finalSDLattemptsArray = array();
				$finalSDLCorrectAttemptArray = array();
				$finalSDLPerArray = array();
				$finalSDLdisUserAtmpArray = array();

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
						array_push($finalSDLPerArray, $tempSDLRhtPer[$i]);
						array_push($finalSDLdisUserAtmpArray, $tempSDLDdisUserAtmp[$i]);
					}
					else
					{
						if($harderSDLCat2num<5 && $tempSDLRhtPer[$i]<=60 && $tempSDLTotal[$i]>40)  // Select 5 Sdls for Cat2
						{
							array_push($finalSDLClusterArray, $tempCluster[$i]);
							array_push($finalSDLArray, $tempSDL[$i]);
							array_push($finalSDLQuesArray, $tempSDLQcodes[$i]);
							array_push($finalSDLattemptsArray, $tempSDLTotal[$i]);
							array_push($finalSDLCorrectAttemptArray, $tempSDLRight[$i]);
							array_push($finalSDLPerArray, $tempSDLRhtPer[$i]);
							array_push($finalSDLdisUserAtmpArray, $tempSDLDdisUserAtmp[$i]);
							$harderSDLCat2num++;
						}
						elseif (!$harderSDLCat2num<5)
							break;
					}
					$harderSDLnum++;
				}
				/* Show all the Sdls of Cat1 and Cat2 */
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
					

					$student_name_string = "";
					$neverRightStudent = 0;
					if($userIdStr!="")
					{
    					$student_name_query = "SELECT u.userID, childName, sum(R), count(srno) as cnt
    										   FROM   adepts_userDetails u, adepts_ncertQuesAttempt a, adepts_ncertQuestions q, adepts_groupInstruction g
    										   WHERE  u.userID IN ($userIdStr) AND
    										          a.userID= u.userID AND
    										          q.groupID= g.groupID AND
    										          q.exerciseCode='$currentTempCluster' AND
    										          g.groupNo=$currentTempSDL AND
    										          a.exerciseCode = q.exerciseCode AND
													  R!=-1 AND R!=3 AND
    										          q.qcode = a.qcode";
    					if (isset($childSection) && $childSection!="")
    					{
    						$student_name_query .= " AND childSection = '$childSection'";
    					}
    					/*$student_name_query .= " AND teacherTopicCode='$topic'
    											 GROUP BY u.userID";*/
    					$student_name_query .= " GROUP BY u.userID";
    					//echo $student_name_query."<br/>";
    					$student_name_result = mysql_query($student_name_query) or die("Error in student name query - ".mysql_error());
    					$total_count = mysql_num_rows($student_name_result);

    					while($student_name_data=mysql_fetch_array($student_name_result))
    					{
    						if ($student_name_data['sum(R)']==0)  //Show only the list of students who have not got it correct even once
    						{
    							$student_name_string .= $student_name_data['childName']." (".$student_name_data['cnt']."), ";
    							$neverRightStudent++;
    						}
    					}
    					$student_name_string = substr($student_name_string, 0, -2);
					}
					foreach ($SDLQuesArray as $j => $value)
					{
						$question = getQuestionData($SDLQuesArray[$j], $school_code, $class, $childSection, $SDLsrno, $userIdStr);
						
						$divDisplayStr = '';
						if($SDLsrno!=1)
							$divDisplayStr = 'style="display:none" ';

						$SDL_question_str = $SDL_question_str.'<div class="question" id="ques_'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'" '.$divDisplayStr.'>'.$question.'<br/></div>';

						$menuDisplayStr = '';
						if($SDLsrno!=1)
							$menuDisplayStr = 'style="display:none" ';
						$SDL_temp_menu_btn = '';
						if($SDL_questions_num>1)
						{
							//$SDL_menu = '<table align="center" border="1"><tr id="'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'_menu" >';
							$SDL_menu = '<div id="'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'_menu">';
							//$SDL_menu = $SDL_menu.'<td align="center">&nbsp;&nbsp;';

							if($SDLsrno!=1)
							{
								$SDL_temp_menu_btn = $SDL_temp_menu_btn.'<a class="previous" href="javascript:previousSDLQues(\''.$currentTempCluster.'\',\''.$currentTempSDL.'\',\''.$SDLsrno.'\');">< Previous</a>&nbsp;&nbsp;&nbsp;&nbsp;';
							}
							if($SDLsrno!=$SDL_questions_num)
							{
								$SDL_temp_menu_btn = $SDL_temp_menu_btn.'<a class="next" href="javascript:nextSDLQues(\''.$currentTempCluster.'\',\''.$currentTempSDL.'\',\''.$SDLsrno.'\');">Next ></a>&nbsp;&nbsp;';
							}

							$SDL_menu_btn = $SDL_menu_btn.'<div id="menu_'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'" '.$menuDisplayStr.'>'.$SDL_temp_menu_btn.'</div>';

						}
						array_push($displayed_question, $SDLQuesArray[$j]);
						$SDLsrno = $SDLsrno + 1;
					}
					//echo '<tr>';
					//echo '<td align="center">'.($i+1).'</td>';
					//echo "<td>".$SDL_question_str;
					echo "<div class='cwa_ques'>";
					echo $SDL_question_str;
					if($SDL_menu!='')
					{
						echo $SDL_menu.$SDL_menu_btn.'</div>';
					}
					echo "</div>";
?>
					<br/>
					<br/>
					<br/>
					<br/>
					<div class="desk_block">
						<div class="top_left">
			            </div>
			            <div class="top_right">
			            </div>
			            <div class="top_repeat">
			            </div>
			            <div class="block mid_left">
			            </div>
			            <div class="block mid_right">
			            </div>
			        	<div class="block mid_repeat">
			        		<div class="block_header">
								<table width="100%">
									<td class="title">Distinct Students : <span class="textBlock"><?=$finalSDLdisUserAtmpArray[$i]?></span></td>
									<td class="title">Correct Attempts : <span class="textBlock"><?=$finalSDLCorrectAttemptArray[$i]?></span></td>
									<td class="title">% correct: <span class="textBlock"><?=number_format($finalSDLPerArray[$i], 2, '.', '')?>%</span></td>
									<td width="20%"></td>
								</table>
								<br/>
								<?php
								if($neverRightStudent!=0)
									echo '<div><span class="title" title="Child Name (Total Attempts)">Students who never got this question type correct : </span><span class="textBlock">'.$student_name_string.'</span></div>';
								?>
			        		</div>
			        	</div>
			        	<div class="bot_left">
						</div>
			            <div class="bot_right">
			            </div>
			            <div class="bot_repeat">
			            </div>
			        </div>
<?php
					/*echo "</td>";
					echo "<td align=\"center\">".$finalSDLdisUserAtmpArray[$i]."</td>";
					echo "<td align=\"center\">".$finalSDLCorrectAttemptArray[$i]."</td>";
					echo "<td align=\"center\">".number_format($finalSDLPerArray[$i], 2, '.', '')."</td>";
					echo '</tr>';*/

				}
			}
			else
			{
				echo "<div align='center'><h3>No student of class $class has attempted questions of this exercise.</h3></div>";
			}
			echo "</div>";
		} 
	?>
		</div>
	</div>

<?php include("footer.php") ?>

<?php
function getQuestionData($qcode, $schoolCode, $class, $section, $qsrn, $userIDStr)
{
    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new ncertQuestion($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;
	$questionStr .= "<p>";
   	$sql = "SELECT groupText FROM adepts_groupInstruction WHERE groupID=$question->groupID";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$questionStr .= orig_to_html($row[0],"images");
    $questionStr .= "</p>";
    $questionStr .= "<p>";
        //$questionStr .= $qsrn.". ".$question->getQuestion()."<br/>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";

    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{
    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";
    	$correctAns = $question->correctAnswer;

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="A")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()."</td>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="B")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="C")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()."</td>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="D")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="A")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()."</td>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="B")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()."</td>";
	            $questionStr .= "<td width='5%'";
	            if($correctAns=="C")
	            	$questionStr .= " bgColor='#00FF00'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()."</td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }

    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title'>Answer</span>: ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."<br/>";
    	else
    		$questionStr .= "<br/>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<br/><span class='title'> Answer</span>: ".$question->getCorrectAnswerForDisplay()."<br/>";

    $showMostCommonWrongAns = 1;
	$_tH = (int)date('G');
    if (!(($_tH >= 14) || ($_tH < 10) ))
    {
        $showMostCommonWrongAns = 0;    //Finding, common wrong answer for a question/class combination being heavy, right now stopped during peak hrs.
    }
    if(!$dynamic && $showMostCommonWrongAns)
    {
	    if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	{

	        $query = "SELECT A, count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class
	                  WHERE  userID in ($userIDStr) AND qcode=".$qcode;
	        $query .= " GROUP BY A";
	        //echo $query;
	        $result = mysql_query($query) or die(mysql_error());
	        $totalAttempts = 0;
	        $optionsData = array();
	        while ($line = mysql_fetch_array($result)) {
	            $optionsData[$line[0]] = $line[1];
	            $totalAttempts += $line[1];
	        }
	        $max = 0;

	        foreach ($optionsData as $opt => $val)
	        {
	            $percentageOpted = $val/$totalAttempts*100;
	            if($percentageOpted>$max && $opt!=$question->correctAnswer)
	            {
	                $mostCommonWrongAnswer = $opt;
	                $max = $percentageOpted;
	            }
	        }
	    }
	    elseif ($question_type=="Blank")
	    {
	        $query = "SELECT A,count(srno) FROM ".TBL_QUES_ATTEMPT."_class$class
	                  WHERE  userID in ($userIDStr) AND R=0 AND qcode=".$qcode;
			$query .= " GROUP BY A ORDER BY 2 DESC limit 1";
			$result = mysql_query($query) or die(mysql_error());
			$line = mysql_fetch_array($result);
			$mostCommonWrongAnswer = $line[0];
	    }
    }

    if($mostCommonWrongAnswer!="")
    {
        $questionStr .= "<div><span class='title'>Most common wrong answer: </span>$mostCommonWrongAnswer</div>";
    }
    if($dynamic)
    {
    	$questionStr .= "<div class='legend'>Note: This is a dynamically generated question. Students might not have got the same question.</div>";
    }
    return $questionStr;
}
?>