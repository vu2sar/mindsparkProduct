<?php
	include("header.php");
	include("../slave_connectivity.php");
	if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
	{
		echo "You are not authorised to access this page!";
		exit;
	}
	
	
	
	$userID      = $_SESSION['userID'];
	$category    = $_SESSION['admin'];
	$subcategory = $_SESSION['subcategory'];
	$school_code = $_SESSION['schoolCode'];


	$class    = isset($_REQUEST['childClass'])?$_REQUEST['childClass']:"";
	$timedtest  = isset($_REQUEST['timedtest'])?$_REQUEST['timedtest']:"";

	$childsubmitSection = isset($_REQUEST['childSection'])?$_REQUEST['childSection']:"";

	$class_array = array();
	if (strcasecmp($category,"School Admin")==0)
	{
		$query = "SELECT DISTINCT(childClass)
		          FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND subcategory='School' AND schoolCode=$school_code AND endDate>=curdate() AND enabled=1
				  ORDER BY cast(childClass as unsigned)";
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
		$query = "SELECT DISTINCT class FROM adepts_teacherClassMapping WHERE userID=".$userID." ORDER BY class";
		$result = mysql_query($query) or die("<br>Error in teacher class query - ".mysql_error());
		while($line=mysql_fetch_array($result))
		{
			if ($line[0]!="")
			{
				array_push($class_array, $line[0]);
			}
		}
	}
	elseif (strcasecmp($category,"Home Center Admin")==0)
	{
		$query = "SELECT DISTINCT(childClass)
		          FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND subcategory='Home Center' AND schoolCode=$school_code AND endDate>=curdate() AND enabled=1
				  ORDER BY cast(childClass as unsigned)";
		$result = mysql_query($query);
		while ($line=mysql_fetch_array($result))
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
?>

<title>Timed Test Report</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/timedTestReport.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>	
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
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
<script language="javascript">
	<?php
	for ($i=0; $i<count($class_array); $i++)
	{
		/* Fetch all the timed test for a class */
		$timeTestCode_array_string = "var testcode_".$class_array[$i]." = new Array( ";
		$timeTestDesc_array_string = "var testdesc_".$class_array[$i]." = new Array( ";

		$userIdArray = array();

		$attemptedUserQuery = "SELECT userID
							   FROM   adepts_userDetails
							   WHERE  category='STUDENT' AND schoolcode = $school_code AND childClass = '".$class_array[$i]."'";

		if(strcasecmp($category,"School Admin")==0)
			$attemptedUserQuery .= " AND subcategory='School'";
		if(strcasecmp($category,"Home Center Admin")==0)
			$attemptedUserQuery .= " AND subcategory='Home Center'";
		$userIdResult = mysql_query($attemptedUserQuery) or die(mysql_error());
		while($userIdRow = mysql_fetch_array($userIdResult))
		{
			array_push($userIdArray, $userIdRow[0]);
		}
		$userIdStr = implode(",", $userIdArray);
		if($userIdStr != "")
		{
			$timeTestQuery = "SELECT DISTINCT TTM.timedTestCode, TTM.description
							  FROM   adepts_timedTestMaster TTM, adepts_timedTestDetails TTD
							  WHERE  TTM.status='Live' AND TTD.userID IN ($userIdStr) AND TTD.timedTestCode = TTM.timedTestCode
							  ORDER BY TTM.description";
			$timeTest_result = mysql_query($timeTestQuery) or die("<br>Error in Time test query - ".mysql_error());
			while ($timeTest_data = mysql_fetch_array($timeTest_result))
			{
				$timeTestCode = $timeTest_data['timedTestCode'];
				$timeTestDesc = $timeTest_data['description'];
				$timeTestCode_array_string .= "'".$timeTestCode."',";
				$timeTestDesc_array_string .= "'".$timeTestDesc."',";
			}			
		}

		$timeTestCode_array_string = substr($timeTestCode_array_string, 0, -1);
		$timeTestCode_array_string .= ");\n";
		$timeTestDesc_array_string = substr($timeTestDesc_array_string, 0, -1);
		$timeTestDesc_array_string .= ");\n";
		print($timeTestCode_array_string);
		print($timeTestDesc_array_string);
		${"class_".$class_array[$i]."sections"} = "";
		$section_array_string = "var section_".$class_array[$i]." = new Array( ";
		if(strcasecmp($category,"School Admin")==0)
		{
			$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND subcategory='School' AND schoolCode=".$school_code." AND childClass='".$class_array[$i]."' ORDER BY childSection";
		}
		if (strcasecmp($category,"TEACHER")==0)
		{
			$section_query = "SELECT DISTINCT(section) as sec FROM adepts_teacherClassMapping WHERE userID=$userID AND class=$class_array[$i] ORDER BY section";
		}
		if(strcasecmp($category,"Home Center Admin")==0)
		{
			$section_query = "SELECT DISTINCT(childSection) as sec FROM adepts_userDetails WHERE category='STUDENT' AND subcategory='Home Center' AND schoolCode=".$school_code." AND childClass='".$class_array[$i]."' ORDER BY childSection";
		}
		//echo "<br>Section Query is - ".$section_query;
		$section_result = mysql_query($section_query) or die("<br>Error in section query - ".mysql_error());
		while ($section_data = mysql_fetch_array($section_result))
		{
			$childSection = $section_data['sec'];
			if ($childSection!="")
			{
				$section_array_string .= "'".$childSection."',";
				${"class_".$class_array[$i]."sections"} .= "'".$childSection."',";
			}
		}
		${"class_".$class_array[$i]."sections"} = substr(${"class_".$class_array[$i]."sections"}, 0, -1);
		$section_array_string = substr($section_array_string, 0, -1);
		$section_array_string .= ");\n";
		print($section_array_string);
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
		if (document.getElementById('timedtest').value=="")
		{
			if(document.getElementById('testDisplay').style.visibility!="hidden"){
				alert('Please select a timed test');
				document.getElementById('timedtest').focus();
				return false;
			}else{
				alert('No timed test for the selected class!');
				return false;
			}
			
		}
		setTryingToUnload();
		return true;
	}

	function populateTimedTest()
	{
		removeAllOptions(document.frmTimedTest.timedtest);
		var childClass = document.getElementById('childClass').value;
		var timedtest = "<?=$timedtest?>";

		var Opt_New = document.createElement('option');
		Opt_New.text = "Select Timed test";
		Opt_New.value = "";
		$(".noSection1").css("visibility","hidden");
		/*document.getElementById('timedtest').disabled = true;*/
		if (timedtest=="")
		{
			Opt_New.selected = true;
		}
		var el_Sel = document.getElementById('timedtest');
		el_Sel.options.add(Opt_New);
		if (childClass)
		{
			var x = eval('testcode_'+childClass);
			var y = eval('testdesc_'+childClass);

			if(x.length>0)
			{
				$(".noSection1").css("visibility","visible");
				/*document.getElementById('timedtest').disabled = false;*/
				for (j=0; j<x.length; j++)
				{
					var OptNew = document.createElement('option');
					OptNew.text = eval('testdesc_'+childClass)[j];
					OptNew.value = eval('testcode_'+childClass)[j];
					if (eval('testcode_'+childClass)[j]==timedtest)
					{
						OptNew.selected = true;
					}
					var elSel = document.getElementById('timedtest');
					elSel.options.add(OptNew);
				}
			}
			else
			{
				$(".noSection1").css("visibility","visible");
				/*document.getElementById('timedtest').disabled = true;*/
			}
		}
	}

	function populateSection()
	{
		$("#childSection").html("");
		var childClass = document.getElementById('childClass').value;
		var childSection = "<?=$childsubmitSection?>";
		if (childClass)
		{
			var x = eval('section_'+childClass);
			var Opt_New = document.createElement('option');
			if (x.length != 1)
			{
				Opt_New.text = "All";
				Opt_New.value = "all";
				Opt_New.selected = true;
				var el_Sel = document.getElementById('childSection');
				el_Sel.options.add(Opt_New);
				/*document.getElementById('childSection').disabled = false;*/
				$(".noSection").show();
				$(".noClass").css("border-right","1px solid #626161");
			}
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
			if(x.length == 1)
			{
				$($("#childSection option")[1]).attr("selected","selected");
			}
		}
		else
		{
			var Opt_New = document.createElement('option');
			Opt_New.text = "All";
			Opt_New.value = "";
			Opt_New.selected = true;
			var el_Sel = document.getElementById('childSection');
			el_Sel.options.add(Opt_New);
			/*document.getElementById('childSection').disabled = true;*/
			$(".noSection").hide();
			$(".noClass").css("border-right","0px solid #626161");
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
	function showHideSummary() {
		var obj = document.getElementById('pnlSummary');
		if (obj.style.display=="none") {
			obj.style.display="block";
			document.getElementById('download').innerHTML = "Hide Summary";
		}
		else {
			obj.style.display="none";
			document.getElementById('download').innerHTML = "Show Summary";
		}
	}

</script>
</head>
<body class="translation" onload="load();populateSection();populateTimedTest();" onresize="load()">
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
					<div id="pageText">TIMED TEST REPORT</div>
				</div>
				<div id="classTopic">
				</div>
			</div>
			<form name="frmTimedTest" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<table id="topicDetails">
				<td width="5%" class=""><label for="childClass">Class</label></td>
		        <td width="25%" style="border-right:1px solid #626161" class="noClass">
		            <select name="childClass" id="childClass" onchange="populateSection();populateTimedTest()" style="width:65%">
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
				<td width="6%" class="noSection"><label for="childSection"  style="margin-left:20px;">Section</label></td>
		        <td width="25%" style="border-right:1px solid #626161" class="noSection">
		            <select name="childSection" id="childSection" onchange="populateTimedTest();" style="width:65%">
					</select>
		        </td>
				<td width="10%" class="noSection1" id="testDisplay"><label for="timedtest" style="margin-left:10px;">Timed Test</label></td>
		        <td width="25%" class="noSection1">
		            <select name="timedtest" id="timedtest" style="width:85%">
						<option value="">Select Timed Test</option>
					</select>
		        </td>
			</table>
			
			<table id="generateTable">
		        <td width="24%"><input type="submit" class="button" name="btnSubmit" id="btnGo" value="Generate" onclick="return validate();"></td>
			</table>
			</form>
				<?php
		if (isset($_POST['btnSubmit']) && $_POST['btnSubmit']=="Generate")
		{
			$userIdArray = array();
			$attemptedUserQuery = "SELECT userID
								   FROM adepts_userDetails
								   WHERE category='STUDENT' AND subcategory='School' AND schoolcode = $school_code ";
			if(strcasecmp($category,"School Admin")==0)
				$attemptedUserQuery .= " AND subcategory='School'";

			if(strcasecmp($category,"Home Center Admin")==0)
				$attemptedUserQuery .= " AND subcategory='Home Center'";

			if($childsubmitSection!='')
			{
				if($childsubmitSection != 'all')
				    $attemptedUserQuery .=  " AND childSection = '$childsubmitSection' ";
				else
				    $attemptedUserQuery .=  " AND childSection IN (${"class_".$class."sections"})";
			}
			$attemptedUserQuery .=	"AND childClass = '$class'";
			$userIdResult = mysql_query($attemptedUserQuery) or die(mysql_error());
			while($userIdRow = mysql_fetch_array($userIdResult))
			{
				array_push($userIdArray, $userIdRow[0]);
			}
			$userIdStr = implode(",", $userIdArray);

			$totalQuestion = getNoOfQuesInTimedTest($timedtest,$class);

			$query  = "SELECT count(timedTestID) noofattempts, avg(timeTaken), avg(perCorrect), count(distinct b.userID)
			           FROM   adepts_timedTestDetails a, adepts_userDetails b
			           WHERE  a.userID=b.userID AND schoolCode=$school_code AND childClass='$class' AND
			                  category='STUDENT' AND timedTestCode='$timedtest'";
			if(strcasecmp($category,"School Admin")==0)
				$query .= " AND subcategory='School'";
			if(strcasecmp($category,"Home Center Admin")==0)
				$query .= " AND subcategory='Home Center'";

			$result = mysql_query($query);
			$line   = mysql_fetch_array($result);
			$totalAttempts = $line['noofattempts'];
			if($totalAttempts==0)
				echo "<div align='center'>No attempts on this timed test!</div>";
			else
			{
				$avgTimeTaken  = round($line[1],1);
				$avgPerCorrect = round($line[2],1);
				$studentsCleared = getNoOfStudentsClearedTimedTest($school_code,$class,$timedtest);
				$totalStudents = getNoOfStudents($school_code,$class);
				$totalStudentsAttempted = $line[3];
				$studentsNotCleared = getStudentsNotClearedTimedTest($school_code,$class,$timedtest);
				if($studentsNotCleared!="")
					$noOfStudentsNotCleared = count(explode(",",$studentsNotCleared));
				else
					$noOfStudentsNotCleared = 0;
				$perStudentsCleared = "";
				if($totalStudents>0)
					$perStudentsCleared = round($studentsCleared/$totalStudentsAttempted*100,0);
	?>
			<script>
				var html="<div class='arrow-black1'></div><div id='classText'>Total Questions: <span style='color:#E75903;'><?=$totalQuestion?></span></div>";
				$("#classTopic").html(html);
			</script>
			<table id="pagingTable">
				<td class="textRed">*n : Total number of students in the class</td>
		        <td class="pointer" onclick='showHideSummary()' width="160px">
					<div id="download" class="textBlue">Hide Summary</div>
					<div id="downloadImage"></div>
				</td>
			</table>
			<div id="pnlSummary" align="center">
			<table class="gridtable" cellpadding="3" cellspacing="0" border="1" width="100%" align="center">
				<tr>
					<th width="8%" class="header">Section (n)</th>
					<th width="15%" class="header">Avg. time taken per student (secs)</th>
					<th width="15%" class="header">Avg. % correct</th>
					<th width="10%" class="header">Total Attempts</th>
					<th width="10%" class="header">No. of students who attempted</th>
					<th width="12%" class="header">% of students who cleared</th>
					<th width="40%" class="header">Students who have attempted but never cleared</th>
				</tr>
				<tr>
					<td align="left" nowrap><strong>Overall</strong> (<?=$totalStudents?>)</td>
					<td align="center"><?=$avgTimeTaken?></td>
					<td align="center"><?=$avgPerCorrect?></td>
					<td align="center"><?=$totalAttempts?></td>
					<td align="center"><?=$totalStudentsAttempted?></td>
					<td align="center"><?=$perStudentsCleared?></td>
					<td align="center"><?=$noOfStudentsNotCleared?> student(s)</td>
				</tr>
	<?php
		//Section wise summary
		if(${"class_".$class."sections"}!="")
		{

			$sectionArray = explode(",",${"class_".$class."sections"});
			for($counter=0; $counter<count($sectionArray); $counter++)
			{
				$section = str_replace("'","",$sectionArray[$counter]);
				$avgTimeTaken = $avgPerCorrect = $totalAttempts = "";
				$query  = "SELECT count(timedTestID) noofattempts, avg(timeTaken), avg(perCorrect), count(distinct b.userID)
			           	   FROM   adepts_timedTestDetails a, adepts_userDetails b
			               WHERE  a.userID=b.userID AND schoolCode=$school_code AND childClass='$class' AND childSection='$section' AND timedTestCode='$timedtest'";

				if(strcasecmp($category,"School Admin")==0)
					$query .= " AND subcategory='School'";
				if(strcasecmp($category,"Home Center Admin")==0)
					$query .= " AND subcategory='Home Center'";

				$result = mysql_query($query);
			    $line   = mysql_fetch_array($result);
			    $totalAttempts = $line['noofattempts'];
			    $avgTimeTaken  = round($line[1],1);
				$avgPerCorrect = round($line[2],1);
				$studentsCleared = getNoOfStudentsClearedTimedTest($school_code,$class,$timedtest,$section);
				$totalStudents = getNoOfStudents($school_code,$class,$section);
				$totalStudentsAttempted = $line[3];
				$studentsNotCleared = getStudentsNotClearedTimedTest($school_code,$class,$timedtest,$section);
				$perStudentsCleared = "";
				if($totalStudentsAttempted>0)
					$perStudentsCleared = round($studentsCleared/$totalStudentsAttempted*100,0);
	?>
				<tr>
					<td align="left" nowrap><strong><?=$section?></strong> (<?=$totalStudents?>)</td>
					<td align="center"><?=$avgTimeTaken?></td>
					<td align="center"><?=$avgPerCorrect?></td>
					<td align="center"><?=$totalAttempts?></td>
					<td align="center"><?=$totalStudentsAttempted?></td>
					<td align="center"><?=$perStudentsCleared?></td>
					<td align="left"><?=$studentsNotCleared?></td>
				</tr>
	<?php
			}
		}
	?>
			</table>
			<br/>
			</div>
		<div align="center">
	<?php
		$query  =  "SELECT a.userID,childName,";
		if($childsubmitSection == 'all')
			$query .= "  childSection,";
		$query .= " date_format(attemptedDate,'%d-%m-%Y') dt, sessionID, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted
				    FROM   adepts_userDetails a, adepts_timedTestDetails b
				    WHERE  a.userID=b.userID AND b.userID IN ($userIdStr) AND timedTestCode='$timedtest'
				    ORDER BY childSection, childName, userID, attemptedDate";
		//echo $query;
		$result = mysql_query($query) or die("No records found!");
		if(mysql_num_rows($result)==0)
		{
			echo "No attempts on this timed test!";
		}
		else {
	?>
		<table class="gridtable" cellspacing="0" cellpadding="3" border="1" align="center" width="100%">
    		<tr>
				<th class="header">Sr.No.</th>
				<th align="left" class="header">Name</th>
				<?php if($childsubmitSection == 'all') echo '<th>Section</th>';	?>
				<th class="header">Date</th>
				<th class="header">Session ID</th>
				<th class="header">Attempt No</th>
				<th class="header">Time Taken (secs)</th>
				<th class="header">Ques Attempted</th>
				<th class="header">% correct</th>
			</tr>
			<?php

				$srno   = 1;
				$prevUserID="";
				while ($line=mysql_fetch_array($result))
				{
					if($line['userID']!=$prevUserID)
					{
						$attemptNo = 1;
						$prevUserID = $line['userID'];
					}
					else
						$attemptNo++;
					$quesAttempted = $line['noOfQuesAttempted']==""?"N.A.":$line['noOfQuesAttempted'];
			?>
			<tr>
				<td width="5%"><?=$srno?></td>
				<td width="25%" align="left"><?=$line['childName']?></td>
				<?php
					if($childsubmitSection == 'all')
						echo '<td width="5%">'.$line['childSection'].'</td>';
				?>
				<td nowrap width="15%" align="center"><?=$line['dt']?></td>
				<td width="10%" align="center"><?=$line['sessionID']?></td>
				<td align="center" width="8%"><?=$attemptNo?></td>
				<td align="center" width="8%"><?=$line['timeTaken']?></td>
				<td align="center" width="8%"><?=$quesAttempted?></td>
				<td align="center" width="8%"><?=$line['perCorrect']?></td>
			</tr>
		<?php
			$srno++;
		}
		?>
		</table>
		<span class="textRed1">*N.A. indicates data not available</span>
	<?php
			}
			}
		}
	?>
	</div>
			
		</div>
	</div>

<?php include("footer.php") ?>
<?php
function getNoOfQuesInTimedTest($timedtest, $class)
{
	$timetestquesQuery = "SELECT noOfQues_cl".$class." FROM adepts_timedTestMaster WHERE timedTestCode='$timedtest'";
	$result = mysql_query($timetestquesQuery);
	$line   = mysql_fetch_array($result);
	return $line[0];
}
function getNoOfStudents($school_code, $class, $section="")
{
	/*$query  = "SELECT count(distinct a.userID) FROM adepts_userDetails a, adepts_timedTestDetails b
	           WHERE  a.userID=b.userID AND schoolCode=$school_code AND childClass='$class' AND category='STUDENT' AND subcategory='School' AND timedTestCode='$timedtest'";*/
	$query  = "SELECT count(userID) FROM adepts_userDetails
	           WHERE  schoolCode=$school_code AND childClass='$class' AND enabled=1 AND endDate>=curdate() AND category='STUDENT'";

	if(strcasecmp($category,"School Admin")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";

	if($section!="")
		$query .= " AND childSection='$section'";
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	$totalStudents = $line[0];
	return $totalStudents;
}
function getNoOfStudentsClearedTimedTest($school_code, $class, $timedtest, $section="")
{
	//Query for determining % of students who cleared the timed test in the class. (i.e. scored >= 75%)
	$query  = "SELECT count(distinct a.userID) noOfUsers
	           FROM   adepts_timedTestDetails a, adepts_userDetails b
	           WHERE  a.userID=b.userID AND schoolCode=$school_code AND childClass='$class' AND timedTestCode='$timedtest' AND perCorrect>=75";

	if(strcasecmp($category,"School Admin")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";

	if($section!="")
		$query .= " AND childSection='$section'";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	return $line[0];
}
function getStudentsNotClearedTimedTest($school_code, $class, $timedtest, $section="")
{
	$userIDArray = array();
	$studentList = "";
	$query  = "SELECT distinct a.userID
	           FROM   adepts_timedTestDetails a, adepts_userDetails b
	           WHERE  a.userID=b.userID AND schoolCode=$school_code AND childClass='$class' AND timedTestCode='$timedtest' AND perCorrect>=75";

	if(strcasecmp($category,"School Admin")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";

	if($section!="")
		$query .= " AND childSection='$section'";
	$result = mysql_query($query) or die(mysql_error());
	while($line   = mysql_fetch_array($result))
		array_push($userIDArray,$line[0]);

	$query = "SELECT distinct a.childName FROM adepts_userDetails a, adepts_timedTestDetails b
	          WHERE  a.userID=b.userID AND category='STUDENT' AND
	                 schoolCode=$school_code AND childClass='$class' AND timedTestCode='$timedtest'";

	if(strcasecmp($category,"School Admin")==0)
		$query .= " AND subcategory='School'";
	if(strcasecmp($category,"Home Center Admin")==0)
		$query .= " AND subcategory='Home Center'";

	if($section!="")
		$query .= " AND childSection='$section'";
	if(count($userIDArray)>0)
		$query .= " AND a.userID NOT IN (".implode(",",$userIDArray).")";
	$query .= "ORDER BY childName";
		$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		$studentList .= $line['childName'].", ";
	}
	$studentList = substr($studentList,0,-2);
	return $studentList;
}
?>