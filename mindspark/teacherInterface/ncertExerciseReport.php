<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("header.php");
	include("../slave_connectivity.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/functions/functions.php");

	/*include("FusionCharts/Includes/FusionCharts.php");
	include("FusionCharts/Includes/FC_Colors.php");*/

	error_reporting(E_ERROR);
	//print_r($_REQUEST);

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$userID 	= $_SESSION['userID'];
	$category   = $_SESSION['admin'];
	$schoolCode = $_SESSION['schoolCode'];

	$query  = "SELECT childName,username, category, subcategory FROM adepts_userDetails WHERE userID=".$userID;
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$name 	    = $line[0];
	$loginID    = $line[1];
	$subcategory = $line[3];


	$classArray = array();
	if(isset($_REQUEST['section']))
	    $section = $_REQUEST['section'];
	else
		$section = "";


	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT distinct childClass FROM adepts_userDetails a, adepts_ncertExerciseMaster b WHERE a.childClass=b.class AND schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND endDate>=curdate() AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND childClass>5 AND status='Live' ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	else if (strcasecmp($category,"TEACHER")==0)
	{
		$query = "SELECT distinct a.class FROM adepts_teacherClassMapping a, adepts_ncertExerciseMaster b WHERE a.class=b.class AND status='Live' AND userID=$userID  AND subjectno=".SUBJECTNO;
		$result = mysql_query($query) or die("<br>Error in teacher class query - ".mysql_error());
		while($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	elseif(strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT distinct childClass FROM adepts_userDetails a, adepts_ncertExerciseMaster b WHERE a.childClass=b.class AND status='Live' schoolCode=$schoolCode AND subcategory='Home Center' AND category='STUDENT'  AND subjects like '%".SUBJECTNO."%' AND childClass>5 ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
		{
			array_push($classArray, $line[0]);
		}
	}
	else
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	$childClass = $_POST['childClass'];
	$exerciseCode = $_POST['exerciseCode'];
?>

<title>NCERT Class Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" style="text/css" href="css/colorbox.css">
<link href="css/ncertExerciseReport.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script type="text/javascript">
<?php
		for ($i=0; $i<count($classArray); $i++)
		{
			$section_array_string = "var section_".$classArray[$i]." = new Array( ";
			if(strcasecmp($category,"School Admin")==0)
			{
				$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=".$schoolCode." AND childClass='".$classArray[$i]."' AND subjects LIKE '%".SUBJECTNO."%' ORDER BY childSection";

			}
			elseif (strcasecmp($category,"TEACHER")==0)
			{
				$section_query = "SELECT DISTINCT(section) as sec FROM adepts_teacherClassMapping WHERE userID=$userID AND class=$classArray[$i] AND subjectno=".SUBJECTNO." ORDER BY section";
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
			$topic_code_array_string = "var code_".$classArray[$i]." = new Array( ";
			$topic_desc_array_string = "var desc_".$classArray[$i]." = new Array( ";
			$topic_query = "SELECT a.exerciseCode, CONCAT('Exercise ',chapterNo,'.',exerciseNo)
							FROM   adepts_ncertHomeworkActivation a, adepts_ncertExerciseMaster b
							WHERE  a.exerciseCode=b.exerciseCode AND
								   a.schoolCode=$schoolCode AND
								   a.class=$classArray[$i]
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
	
	function populateTopic()
	{
		removeAllOptions(document.getElementById('exerciseCode'));
		var childClass = document.getElementById('childClass').value;
		var topic = "<?=$exerciseCode?>";
		var Opt_New = document.createElement('option');
		Opt_New.text = "Select Topic";
		Opt_New.value = "";
		if (topic=="")
		{
			Opt_New.selected = true;
		}
		var el_Sel = document.getElementById('exerciseCode');
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
				var elSel = document.getElementById('exerciseCode');
				elSel.options.add(OptNew);
			}
		}
	}
	function populateSection()
	{
		removeAllOptions(document.getElementById('childSection'));
		var childClass = document.getElementById('childClass').value;
		var childSection = "<?=$section?>";
		if (childClass)
		{
			var x = eval('section_'+childClass);
			var Opt_New = document.createElement('option');
			var elSel = document.getElementById('childSection');
			if(x.length != 1)
			{
				var OptNew = document.createElement('option');
				OptNew.text = "Select";
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
	function submitForm(checkExercise)
	{
		if (document.getElementById('childClass').value=="")
		{
			alert('Please select class');
			document.getElementById('childClass').focus();
			return false;
		}
		var selectedSection = document.getElementById('section').value;
		if (selectedSection.length > 1)
		{
			alert('Please select section');
			document.getElementById('section').focus();
			return false;
		}
		if(checkExercise && document.getElementById('exerciseCode').value=="")
		{
			alert('Please select exercise');
			document.getElementById('exerciseCode').focus();
			return false;
		}
		document.frmNCERTReport.submit();
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
	function showTrail(userID)
	{
		document.getElementById("student_userID").value = userID;
		document.trailForm.submit();
		return(false);
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
<body class="translation" onload="load();populateSection();populateTopic();" onresize="load()">
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
					<div id="pageText">NCERT CLASS REPORT</div>
				</div>
				<input type="button" class="button" id="selectClass" name="back" value="Select Another Class" onclick="javascript:window.location='activateHomework.php';">
			</div>
			<form id="frmNCERTReport" name="frmNCERTReport" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<table id="topicDetails">
				<td width="6%"><label for="childClass">Class</label></td>
		        <td width="25%">
		            <select name="childClass" id="childClass" onchange="populateSection();populateTopic()" style="width:65%">
                        <?php
                            $class_string = "";
                            if(count($classArray) != 1)
                            	echo '<option value="">Select</option>';
                            for ($i=0;$i<count($classArray);$i++)
                            {
                                if ($classArray[$i]=="")
                                {
                                    $class_string = "";
                                    continue;
                                }
                                $class_string .= "<option value=\"".$classArray[$i]."\"";
                                if (isset($_POST['childClass']) && $_POST['childClass']==$classArray[$i])
                                {
                                    $class_string .= " selected";
                                }
                                $class_string .= " >".$classArray[$i]."</option>";
                                echo $class_string;
                                $class_string = "";
                            }
                        ?>
                    </select>
		        </td>
				<td width="6%" class="noSection"><label for="section">Section</label></td>
				<td width="25%" class="noSection">
					<select name="section" id="childSection"  style="width:65%">
                    </select>
				</td>
				<td width="6%"><label for="exercise">Exercise</label></td>
                <td width="25%">
                    <select name="exerciseCode" id="exerciseCode"  style="width:65%">
                        <option value="">Select</option>
                    </select>
                </td>
			</table>
			<table id="generateTable">
				<td id="showActivated" width="25%"><div id="checkActive"><input type="submit" class="button" name="btnGenerate" id="generate" value="Generate" onclick="return submitForm();"></div></td>			
			</table>
			<?php
			if($exerciseCode != "")
			{
				$userArray = getStudentDetails($childClass,$schoolCode,$section);
				$userIDs = array_keys($userArray);
				$userIDs = implode(",",$userIDs);
				$exerciseDetailArray = array();
				
				$sql = "SELECT chapterName, chapterNo, exerciseNo, COUNT(DISTINCT qcode), COUNT(DISTINCT b.groupID) FROM adepts_ncertExerciseMaster a, adepts_ncertQuestions b, adepts_groupInstruction c WHERE a.exerciseCode='$exerciseCode' AND b.groupID=c.groupID AND a.exerciseCode=b.exerciseCode";
				$result = mysql_query($sql);
				$row = mysql_fetch_array($result);
				$chapterName = $row[0];
				$chapterNo = $row[1];
				$exerciseNo = $row[2];
				$totalQuestions = $row[3];
				$totalGroups = $row[4];
				
				$sql = "SELECT a.userID, noOfQuesAttempted, perCorrect, COUNT(DISTINCT(groupID)), IF(a.submitDate < d.deactivationDate,'YES','NO'), a.submitDate FROM adepts_ncertHomeworkStatus a, adepts_ncertQuesAttempt b, adepts_ncertQuestions c, adepts_ncertHomeworkActivation d WHERE a.exerciseCode = d.exerciseCode AND b.qcode=c.qcode AND a.exerciseCode=b.exerciseCode AND a.ncertAttemptID=b.ncertAttemptID AND a.exerciseCode='$exerciseCode' AND a.userID IN ($userIDs) AND R!=-1 GROUP BY a.userID";
				
				$result = mysql_query($sql);
		?>
			<table id="pagingTable">
		        <td width="35%">Exercise:  <?=$chapterNo?>.<?=$exerciseNo?></td>
				<td>
					<div class="textRed">Total questions (parts): <?=$totalGroups?>&nbsp;(<?=$totalQuestions?>)</div>
				</td>
			</table>
            <table align="center" border="1" cellpadding="3" cellspacing="0" class="gridtable" width="100%">
            	<thead>
                <tr>
                    <td width="40%" onClick="sortColumn(event)" type="CaseInsensitiveString" valign="top" align="left" class="header"><strong>Student Name</strong></td>
                    <td width="15%" onClick="sortColumn(event)" type="CaseInsensitiveString" align="center" class="header"><strong>Questions Attempted *<br>(Parts)</strong></td>
                    <td width="10%" onClick="sortColumn(event)" type="CaseInsensitiveString" align="center" class="header"><strong>Accuracy (%)</strong></td>
                    <td width="25%" onClick="sortColumn(event)" type="CaseInsensitiveString" align="center" class="header"><strong>Status</strong></td>
                </tr>
                </thead>
                <tbody>
        <?php
				while($row = mysql_fetch_array($result))
				{
					$exerciseDetailArray[$row[0]]["noOfQuesAttempted"] = $row[1];
					$exerciseDetailArray[$row[0]]["perCorrect"] = $row[2];
					$exerciseDetailArray[$row[0]]["groupID"] = $row[3];
					$status = "Incomplete";
					if($row[4] == "YES" && $row[5]!="")
						$status = "Submitted in-time";
					else if($row[4] == "NO" && $row[5]!="")
						$status = "Submitted late";
					$exerciseDetailArray[$row[0]]["status"] = $status;
				}
				foreach($userArray as $userID=>$details)
				{
					if(isset($exerciseDetailArray[$userID]))
					{
		?>
                <tr>
                    <td><a href="javascript:void(0)" onClick="showTrail('<?=$userID?>')" style="text-decoration:underline;"><?=$details[0]?></a></td>
                    <td><?=$exerciseDetailArray[$userID]["groupID"]?>(<?=$exerciseDetailArray[$userID]["noOfQuesAttempted"]?>)</td>
                    <td><?=$exerciseDetailArray[$userID]["perCorrect"]?></td>
                    <td><?=$exerciseDetailArray[$userID]["status"]?></td>
                </tr>
        <?php
					}
					else
					{
		?>
                <tr>
                    <td><a href="javascript:void(0)" class="disabledLink"><?=$details[0]?></a></td>
                    <td>0(0)</td>
                    <td>N.A.</td>
                    <td>Not Started</td>
                </tr>
        <?php
					}
				}
		?>
        	</tbody>
			</table>
			<br>
          	<div  class="text1">* The number in brackets is the total number of parts of questions in the exercise.</div><br><br>
		<?php
			}
		?>
			</form>
			
		</div>
	</div>
	<div id="incompleteListDiv">
			    <ul id="incompleteListUL">
			    </ul>
			</div>
<form name="trailForm" method="post" action="studentTrail.php">
	<input type="hidden" name="student_userID" id="student_userID" value="" />
	<input type="hidden" name="trailType" id="trailType" value="ncert" />
	<input type="hidden" name="exercise" id="exercise" value="<?=$exerciseCode?>" />
</form>
<?php include("footer.php") ?>