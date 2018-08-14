<?php
	set_time_limit(0);
	include("header.php");
	include("../slave_connectivity.php");
	include("../userInterface/functions/orig2htm.php");
	include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/constants.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	
	error_reporting(E_ERROR);
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}

	$animationQues	=	0;
	$totalQues	=	0;
	$userID      = $_SESSION['userID'];
	$school_code = $_SESSION['schoolCode'];
	$category    = $_SESSION['admin'];

	$class    = isset($_POST['childClass'])?$_POST['childClass']:"";
	$topic  = isset($_POST['topic'])?$_POST['topic']:"";
	$childSection = isset($_POST['childSection'])?$_POST['childSection']:"";
	$chkPointTypeRd = isset($_POST['chkPointTypeRd'])?$_POST['chkPointTypeRd']:"";
	$classification = isset($_POST['classification'])?$_POST['classification']:"";
	$teacherTopic = isset($_POST['teacherTopic'])?$_POST['teacherTopic']:"";
	$learningUnits = isset($_POST['learningUnits'])?$_POST['learningUnits']:"";
	$class_array = array();
	if (strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Home Center Admin")==0)
	{
		$query = "SELECT DISTINCT(childClass)
		          FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND schoolCode=$school_code AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND endDate>=curdate()
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
	$childClass = isset($_REQUEST['childClass'])?$_REQUEST['childClass']:"";
	
	if(isset($_POST["selectedQcode"]))
	{
		$qcode = $_POST["selectedQcode"];
		$childClass = $_POST["selectedClass"];
		$childSection = $_POST["selectedSection"];
		
		$sq = "INSERT INTO adepts_researchQuesActivation SET code=$qcode, type='checkpoint', schoolCode=$school_code, class=$childClass, appearance='Randomly', stopingCriterion='500', activationDate=CURDATE(), addedBy='Teacher'";
		if($childSection!="")
			$sq .= ", section='$childSection'";
		$rs = mysql_query($sq);
	}
?>

<title>Check Point Question</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/checkPointQuestion.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/idletimeout.js" type="text/javascript"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script type="text/javascript" src="/mindspark/userInterface/libs/prompt.js"></script>
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
	
	function checkType()
	{
		if($("#childClass").val()!="")
		{
			$("#otherDetails").show();
			if($("input[name='chkPointTypeRd']").val()==1)
				$("#redSpan").show();
			else
				$("#redSpan").hide();
		}
	}
	
	function quit()
	{
		setTryingToUnload();
		window.location.href='checkPointQuestion.php';
	}
	
	$(document).ready(function(e) {
		$(".cwa").each(function() {
			var details	=	$(this).attr("id");
			$.post("ajaxRequest.php","mode=commonWrongAnswer&quesDetails="+$("#"+details).next("input").val(),function(data) { 
				$("#"+details).html(data);
			});
		});
		$(".checkPointQuestion").click(function(){
			var qcodeID = $(this).val();
			var qcode = qcodeID.split("_")[1];
			var htmlQues = $("#"+$(this).val()).html();
			var prompts=new Prompt({
				text:"Are you sure you want to select this question?",
				type:'confirm',
				label2:'No',
				label1:'Yes',
				func1:function(){
					$("#selectedQcode").val(qcode);
					$("#displayStatic").html(htmlQues);
					$("#selectedQuesDiv").show();
					$("#questionList").hide();
					$("#divWrongQuestion :input").attr("disabled",true);
					$("#prmptContainer_confirmQuestion").remove();
					//$("#frmTeacherTopicFlow").submit();
				},
				func2:function(){
					$(".checkPointQuestion").attr("checked",false);
					$("#prmptContainer_confirmQuestion").remove();
				},
				promptId:"confirmQuestion"
			});	
		});
	});
</script>
<script language="javascript">
		<?php
			for ($i=0; $i<count($class_array); $i++)
			{
				$section_array_string = "var section_".$class_array[$i]." = new Array( ";
				if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Home Center Admin")==0)
				{
					$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=".$school_code." AND childClass='".$class_array[$i]."' AND subjects LIKE '%".SUBJECTNO."%' AND endDate>=CURDATE() AND enabled=1 ORDER BY childSection";

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
				$topic_Master_array_string = "var masterTopic_".$class_array[$i]." = new Array( ";
				//$topic_code_array_string = "var code_".$class_array[$i]." = new Array( ";
				//$topic_desc_array_string = "var desc_".$class_array[$i]." = new Array( ";
				$topic_query = "SELECT TTM.teacherTopicCode, teacherTopicDesc, classification 
								FROM   adepts_teacherTopicActivation TTA, adepts_teacherTopicMaster TTM
								WHERE  TTA.teacherTopicCode=TTM.teacherTopicCode AND
								       TTA.schoolCode=$school_code AND
								       TTA.class=$class_array[$i]
									   AND subjectno=".SUBJECTNO."
								GROUP BY TTM.teacherTopicCode
								ORDER BY teacherTopicDesc";
				//echo "<br>Topic query is - ".$topic_query;
				$topic_result = mysql_query($topic_query) or die("<br>Error in topic query - ".mysql_error());
				$arrClassification = array();
				while ($topic_data = mysql_fetch_array($topic_result))
				{
					if(!in_array($topic_data['classification'],$arrClassification))
					{
						$arrClassification[] = $topic_data['classification'];
						$topic_code_array_string[$topic_data['classification']] = "var code_".$class_array[$i]."_".str_replace(" ","_",str_replace("-","_",$topic_data['classification']))." = new Array( ";
						$topic_desc_array_string[$topic_data['classification']] = "var desc_".$class_array[$i]."_".str_replace(" ","_",str_replace("-","_",$topic_data['classification']))." = new Array( ";
					}
					$topic_code_array_string[$topic_data['classification']] .= "'".$topic_data['teacherTopicCode']."',";
					$topic_desc_array_string[$topic_data['classification']] .= "'".$topic_data['teacherTopicDesc']."',";
				}
				$topic_Master_array_string .= "'".implode("','",$arrClassification)."'";
				$topic_Master_array_string .= ");\n";
				print($topic_Master_array_string);

				foreach($topic_code_array_string as $masterTopic=>$topic_code_array_strings)
				{
					print(substr($topic_code_array_strings, 0, -1).");\n");
				}
				foreach($topic_desc_array_string as $masterTopic=>$topic_desc_array_strings)
				{
					print(substr($topic_desc_array_strings, 0, -1).");\n");
				}
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
			if (document.getElementById('classification').value=="")
			{
				alert('Please select topic');
				document.getElementById('classification').focus();
				return false;
			}
			if (document.getElementById('teacherTopic').value=="")
			{
				alert('Please select topic');
				document.getElementById('teacherTopic').focus();
				return false;
			}
			if (document.getElementById('learningUnits').value=="")
			{
				alert('Please select topic');
				document.getElementById('learningUnits').focus();
				return false;
			}
			setTryingToUnload();
			return true;
		}
		
		function populateMasterTopic()
		{
			removeAllOptions(document.frmWrongQuestion.classification);
			var childClass = document.getElementById('childClass').value;
			var classification = "<?=$classification?>";
			var Opt_New = document.createElement('option');
			Opt_New.text = "Select Classification";
			Opt_New.value = "";
			if (teacherTopic=="")
			{
				Opt_New.selected = true;
			}
			
			var el_Sel = document.getElementById('classification');
			el_Sel.options.add(Opt_New);
			if (childClass)
			{
				var x = eval('masterTopic_'+childClass);
				var y = eval('masterTopic_'+childClass);
				
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('masterTopic_'+childClass)[j];
					OptNew.value = eval('masterTopic_'+childClass)[j];
					if (eval('masterTopic_'+childClass)[j]==classification)
					{
						OptNew.selected = true;
					}
					var elSel = document.getElementById('classification');
					elSel.options.add(OptNew);
				}
			}
		}
		
		function populateTopic()
		{
			removeAllOptions(document.frmWrongQuestion.teacherTopic);
			var childClass = document.getElementById('childClass').value;
			var classification = document.getElementById('classification').value;
			classification = classification.replace(/ /g, "_");
			var teacherTopic = "<?=$teacherTopic?>";
			var Opt_New = document.createElement('option');
			Opt_New.text = "Select Topic";
			Opt_New.value = "";
			if (teacherTopic=="")
			{
				Opt_New.selected = true;
			}
			
			var el_Sel = document.getElementById('teacherTopic');
			el_Sel.options.add(Opt_New);
			if (childClass)
			{
				var x = eval('code_'+childClass+'_'+classification);
				var y = eval('desc_'+childClass+'_'+classification);
				
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('desc_'+childClass+'_'+classification)[j];
					OptNew.value = eval('code_'+childClass+'_'+classification)[j];
					if (eval('code_'+childClass+'_'+classification)[j]==teacherTopic)
					{
						OptNew.selected = true;
					}
					var elSel = document.getElementById('teacherTopic');
					elSel.options.add(OptNew);
				}
			}
		}
		
		function populateLearningUnits()
		{
			var teacherTopicCode = $("#teacherTopic").val();
			var childClass = $("#childClass").val();
			var childSection = $("#childSection").val();
			removeAllOptions(document.frmWrongQuestion.learningUnits);
			var learningUnits = "<?=$learningUnits?>";
			var OptNew = document.createElement('option');
			if (learningUnits=="")
			{
				OptNew.selected = true;
			}
			OptNew.text = "Select Learning Units";
			OptNew.value = "";
			var elSel = document.getElementById('learningUnits');
			elSel.options.add(OptNew);
			$.post("ajaxRequest.php","mode=getClusters&childClass="+childClass+"&childSection="+childSection+"&teacherTopicCode="+teacherTopicCode,function(data) { 
				var responseArray = $.parseJSON(data);
				$.each(responseArray, function(key, value) { 
					var Opt_New = document.createElement('option');
					Opt_New.text = value;
					Opt_New.value = key;
					if (key==learningUnits)
					{
						Opt_New.selected = true;
					}
					var elSel = document.getElementById('learningUnits');
					elSel.options.add(Opt_New);
				});
			});
		}
		
		function populateSection()
		{
			removeAllOptions(document.frmWrongQuestion.childSection);
			var childClass = document.getElementById('childClass').value;
			var childSection = "<?=$childSection?>";
			if (childClass)
			{
				$("#chkPointType").show();
				var x = eval('section_'+childClass);
				var Opt_New = document.createElement('option');
				if (x.length > 0)
				{
					Opt_New.text = "All Section";
					$(".noSection").show();
					
				}
				else
				{
					Opt_New.text = "Select Section";
					$(".noSection").hide();
					
				}
				Opt_New.value = "";
				if (childSection=="")
				{
					Opt_New.selected = true;
				}
				var el_Sel = document.getElementById('childSection');
				el_Sel.options.add(Opt_New);
				//alert(x);
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
			}
			else
			{
				$("#chkPointType").hide();
				var Opt_New = document.createElement('option');
				Opt_New.text = "Select Section";
				Opt_New.value = "";
				if (childSection=="")
				{
					Opt_New.selected = true;
				}
				var el_Sel = document.getElementById('childSection');
				el_Sel.options.add(Opt_New);
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
		
		function saveCheckPoint()
		{
			setTryingToUnload();
			$("#divWrongQuestion :input").attr("disabled",false);
			$("#selectedClass").val($("#childClass").val());
			$("#selectedSection").val($("#childSection").val());
			$("#frmAddQuestion").submit();
		}

	</script>
</head>
<body class="translation" onLoad="populateSection();populateMasterTopic();populateTopic();populateLearningUnits();checkType();load()" onmousemove="reset_interval()" onclick="reset_interval()" onkeypress="reset_interval()" onscroll="reset_interval()" onResize="load()">
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
		<div id="divWrongQuestion">
			<table  border="0" cellpadding="2" cellspacing="2" >
				<tr>
				<td class="contentTab">
				<div class="arrow-black" style="margin-top: 24px;"></div>
						<div id="pageText" style="font-size:1em;font-weight:normal;color:black">Check Point Question</div>
				</td>		
				</tr>
				</table>
				<table  border="0" cellpadding="2" cellspacing="2" width="100%">
				<tr>
					<td class="SelectTab" nowrap>
						<label for="childClass"><b>Class: </b></label>
						<select name="childClass" id="childClass" onChange="populateSection();populateMasterTopic();">
							<option value="">Select Class</option>
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
					<td class="SelectTab noSection" align="left">
						<label for="childSection"><b>Section: </b></label>
						<select name="childSection" id="childSection">
							<option value="">Select Section</option>
						</select>
					</td>
					<td id="chkPointType" style="display:none;"><label><input type="radio" name="chkPointTypeRd" id="chkPointTypeRd1" <?php if($chkPointTypeRd==1) echo "checked"; ?> value="1" onClick="checkType()"><b>Learning Unit</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="chkPointTypeRd" id="chkPointTypeRd2"  <?php if($chkPointTypeRd==2) echo "checked"; ?>  value="2" onClick="checkType()"><b>Common wrong answer report</b></label></td>
	
					</tr>
				</table>
				<table id="otherDetails" border="0" cellpadding="2" cellspacing="2"  width="100%" style="display:none">
					<tr>
						<td class="contentTab noTopic">
							<label for="classification"><b>Classification: </b></label>
							<select name="classification" id="classification" onChange="populateTopic()">
								<option value="">Select Classification</option>
							</select>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<label for="teacherTopic"><b>Topic:</b></label>
							<select name="teacherTopic" id="teacherTopic" onChange="populateLearningUnits()">
								<option value="">Select Topic</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="SelectTab noTopic">
							<label for="learningUnits"><b><span id="redSpan" style="color:#F00">*</span>Learning Units:</b></label>
							<select name="learningUnits" id="learningUnits" >
								<option value="">Select Learning Units</option>
							</select>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input style="margin-left: 15px;" type="submit" name="btnSubmit" value="Submit" onClick="return validate();" class="buttons">
						</td>
					</tr>
			</table>
		</div>
	<hr>
</form>
	<?php
		if (isset($_POST['btnSubmit']) && $_POST['btnSubmit']=="Submit")
		{
			if($chkPointTypeRd==2)
			{
				if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0)
					$subcategory = "School";
				else if(strcasecmp($category,"Home Center Admin")==0)
					$subcategory = "Home Center";
				/* Fetch all the attempted user */
				$userIdArray = array();
				$attemptedUserQuery = "SELECT userID FROM adepts_userDetails
									   WHERE  schoolcode = $school_code AND category='STUDENT' AND subcategory='$subcategory' AND endDate>=curdate() 
											 AND childClass = $class";
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
				if($learningUnits!="")
					$clusterArray[] = $learningUnits;
				else
					$clusterArray = getClustersOfTopic($teacherTopic);
				$noofsdls = 0;
	
				/* Fetched all the SDLs of the clusters */
				//while($clusters = mysql_fetch_array($result1))
				foreach ($clusterArray as $val)
				{
					//$clusterCode[$k] = $clusters[0];
					$clusterCode[$k] = $val;
	
					$query = "SELECT a.clusterCode, subdifficultylevel, count(srno), SUM(R), group_concat(distinct q.qcode), count(distinct a.userID) as distinct_users
							  FROM  ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q, ".TBL_CLUSTER_STATUS." cs, ".TBL_TOPIC_STATUS." ts
							  WHERE a.clusterAttemptID = cs.clusterAttemptID AND
									cs.userID = ts.userID AND
									ts.userID IN ($userIdStr) AND
									a.qcode = q.qcode AND
									cs.ttAttemptID = ts.ttAttemptID AND
									cs.clusterCode='$clusterCode[$k]' AND
									ts.teacherTopicCode = '$teacherTopic'
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
	
						$clusterAtttempt_query = "SELECT clusterAttemptID FROM ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b WHERE a.ttAttemptID=b.ttAttemptID AND a.userID in ($userIdStr) AND teacherTopicCode='$topic' AND clusterCode='$currentTempCluster'";
						$clusterAttempt_result = mysql_query($clusterAtttempt_query);
						$clusterAttemptStr  = "";
						while ($clusterAttempt_line = mysql_fetch_array($clusterAttempt_result))
						   $clusterAttemptStr .= $clusterAttempt_line[0].",";
						$clusterAttemptStr = substr($clusterAttemptStr,0,-1);
	
						$student_name_string = "";
						$neverRightStudent = 0;
						if($clusterAttemptStr!="")
						{
							$student_name_query = "SELECT u.userID, childName, sum(R), count(srno) as cnt
												   FROM   adepts_userDetails u, ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q
												   WHERE  u.userID IN ($userIdStr) AND
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
	
							$SDL_question_str = $SDL_question_str.'<div id="ques_'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'" '.$divDisplayStr.'>'.$question.'<br/></div>';
	
							$menuDisplayStr = '';
							if($SDLsrno!=1)
								$menuDisplayStr = 'style="display:none" ';
							$SDL_temp_menu_btn = '';
							if($SDL_questions_num>1)
							{
								//$SDL_menu = '<table align="center" border="1"><tr id="'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'_menu" >';
								$SDL_menu = '<div id="'.$currentTempCluster.'_'.$currentTempSDL.'_'.$SDLsrno.'_menu" align="center">';
								//$SDL_menu = $SDL_menu.'<td align="center">&nbsp;&nbsp;';
	
								if($SDLsrno!=1)
								{
									$SDL_temp_menu_btn = $SDL_temp_menu_btn.'<a href="javascript:previousSDLQues(\''.$currentTempCluster.'\',\''.$currentTempSDL.'\',\''.$SDLsrno.'\');"><u>Previous</u></a>&nbsp;&nbsp;&nbsp;&nbsp;';
								}
								if($SDLsrno!=$SDL_questions_num)
								{
									$SDL_temp_menu_btn = $SDL_temp_menu_btn.'<a href="javascript:nextSDLQues(\''.$currentTempCluster.'\',\''.$currentTempSDL.'\',\''.$SDLsrno.'\');"><u>Next</u></a>&nbsp;&nbsp;';
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
							<div class="block mid_repeat" style="background-color:#D6D6D6;">
								<div class="block_header">
									<div>
										<span class="title"><b>Distinct Students:</b> </span><span><?=$finalSDLdisUserAtmpArray[$i]?></span>&nbsp;&nbsp;&nbsp;&nbsp;
										<span class="title"><b>Correct Attempts:</b> </span><span><?=$finalSDLCorrectAttemptArray[$i]?></span>&nbsp;&nbsp;&nbsp;&nbsp;
										<span class="title"><b>% correct:</b> </span><span><?=number_format($finalSDLPerArray[$i], 2, '.', '')?>%</span>&nbsp;&nbsp;&nbsp;&nbsp;
	
									</div>
									<?php
									if($neverRightStudent!=0)
										
										echo '<div><b><span class="title" title="Child Name (Total Attempts)">Students who never got this question type correct: </span><span></b>'.$student_name_string.'</span></div>';
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
						<hr/>
	<?php 
	
					}
				}
				else
				{
					echo "<div align='center'><h3>No student of class $class has attempted questions of this topic.</h3></div>";
				}
			}
			else
			{
				echo "<div id='questionList'>";
				$sq = "SELECT qcode FROM adepts_questions WHERE clusterCode='$learningUnits' GROUP BY subDifficultyLevel ORDER BY subDifficultyLevel";
				$rs = mysql_query($sq);
				$j=0;
				while($rw = mysql_fetch_array($rs))
				{
					$j++;
					$question = getQuestionData($rw[0], $school_code, $class, $childSection, $j, $userIdStr,1);

					$divDisplayStr = '';
					$SDL_question_str = '<div id="ques_'.$rw[0].'">'.$question.'<br/></div>';
					echo "<div style='float:left'>$j<br><input type='radio' class='checkPointQuestion' name='checkPointQuestion[]' id='checkPointQuestion_'".$rw[0]." value='ques_".$rw[0]."'></div><div class='cwa_ques' style='float:left'>";
					echo $SDL_question_str;
					echo "</div><div style='clear:both'></div>";
?>

					<hr/>
<?php						
				}
				echo "</div>";
			}
		}
	?>
	<div id="selectedQuesDiv">
		<div id="displayStatic"></div>
		<form name="frmAddQuestion" id="frmAddQuestion" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="text" name="selectedQcode" id="selectedQcode" value="<?=$qcode?>">
			<input type="text" name="selectedClass" id="selectedClass" value="">
			<input type="text" name="selectedSection" id="selectedSection" value="">
			<input style="margin-left: 15px;width:160px" type="button" name="btnAddQues" onClick="saveCheckPoint()" value="Add question" class="buttons">
			<input style="margin-left: 15px;" type="button" name="btnQuit" value="Quit" onClick="quit()" class="buttons">
		</form>
	</div>
	</div>
	<script>
	</script>

<?php
function getQuestionData($qcode, $schoolCode, $class, $section, $qsrn, $userIDStr,$noramlQues=0)
{
	global $animationQues;
	global $totalQues;
    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new Question($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

	if((strpos($question->getQuestion(), ".html") !== false || strpos($question->getQuestion(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false) && $qsrn==1)
		$animationQues++;
	if($qsrn==1)
		$totalQues++;
		
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
	//$questionStr .= "<div id='cwa_$qcode' class='cwa'><span class='title'>Most common wrong answer: </span>Loading...<img src='assets/ajax-loader.gif' height='14' width='15'></div><input type='hidden' value='".$qcode.'#'.$dynamic.'#'.$showMostCommonWrongAns.'#'.$question_type.'#'.$question->correctAnswer.'#'.$class.'#'.$userIDStr."'>";

    return $questionStr;
}

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
?>

<?php include("footer.php") ?>