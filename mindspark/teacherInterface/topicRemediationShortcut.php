<?php
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
include("header.php");
include("../slave_connectivity.php");
include("../userInterface/classes/clsTopicProgress.php");
if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
{
	echo "You are not authorised to access this page!";
	exit;
}

/*$keys = array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key} = $_REQUEST[$key] ;
}*/

$userID     = $_SESSION['userID'];
$category   = $_SESSION['admin'];
$schoolCode = $_SESSION['schoolCode'];
$subcategory = $_SESSION['subcategory'];
if(strcasecmp($category,"School Admin")==0)
{
	$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		       FROM     adepts_userDetails
		       WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
		       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
}
elseif (strcasecmp($category,"Teacher")==0)
{
	$query = "SELECT   class, group_concat(distinct section ORDER BY section)
			  FROM     adepts_teacherClassMapping
			  WHERE    userID = $userID AND subjectno=".SUBJECTNO."
			  GROUP BY class ORDER BY class, section";
}
elseif (strcasecmp($category,"Home Center Admin")==0)
{
	$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		       FROM     adepts_userDetails
		       WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND endDate>=curdate() AND subjects like '%".SUBJECTNO."%'
		       GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
}
else
{
	echo "You are not authorised to access this page!";
	exit;
}
$classArray = $sectionArray = $topicArray = array();
$hasSections = false;
$result = mysql_query($query) or die(mysql_error());
while($line=mysql_fetch_array($result))
{
	array_push($classArray, $line[0]);
	if($line[1]!='')
		$hasSections = true;
	$sections = explode(",",$line[1]);
	$sectionStr = "";
	for($i=0; $i<count($sections); $i++)
	{
		if($sections[$i]!="")
			$sectionStr .= "'".$sections[$i]."',";
	}

	$sectionStr = substr($sectionStr,0,-1);

	if (strcasecmp($category,"Home Center Admin")==0)
	{
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
		          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
			      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND b.schoolCode=$schoolCode AND b.class=".$line[0];
	}
	else
	{
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
		          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
			      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND b.schoolCode=$schoolCode AND b.class=".$line[0];
	}

	if($sectionStr!="")
		$query .= " AND section in ($sectionStr)";
	$query .= " ORDER BY teacherTopicDesc";
	$topic_result = mysql_query($query) or die(mysql_error());
	while ($topic_line=mysql_fetch_array($topic_result))
	{
		$topicArray[$topic_line['class']][$topic_line['teacherTopicCode']] = $topic_line['teacherTopicDesc'];
	}
	array_push($sectionArray, $sectionStr);
}

$cls = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
$section = isset($_REQUEST['section'])?$_REQUEST['section']:"";
$ttCode = isset($_REQUEST['ttCode'])?$_REQUEST['ttCode']:"";


/*$userList = isset($_REQUEST['userList_pass'])?$_REQUEST['userList_pass']:"";

$userPassedArr = array();
if($userList != "")
	$userPassedArr = explode(",",$userList);
$flagUsersPassed = 0;

if(count($userPassedArr)>0)
	$flag = 1;
else
	$flag = 0;
*/
?>


<title>Topic Remediation</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicRemediationShortcut.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var gradeArray   = new Array();
	var sectionArray = new Array();
	var topicCodeArray   = new Array();
	var topicArray   = new Array();
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
			echo "gradeArray.push($classArray[$i]);\r\n";
			echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";
			$temptopicCode = array_keys($topicArray[$classArray[$i]]);
			$tempCodeStr = $tempTopicStr = '';
			for($j=0; $j<count($temptopicCode); $j++)
			{
				$tempCodeStr .= "'".$temptopicCode[$j]."',";
				$tempTopicStr .= "'".$topicArray[$classArray[$i]][$temptopicCode[$j]]."',";
			}
			$tempCodeStr = substr($tempCodeStr,0,-1);
			$tempTopicStr = substr($tempTopicStr,0,-1);
			echo "topicArray[$i] = new Array($tempTopicStr);\r\n";
			echo "topicCodeArray[$i] = new Array($tempCodeStr);\r\n";
		}
	?>
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
		$("#trailContainer").css("height",containerHeight+"px");
		$("#classes").css("font-size","1.4em");
		$("#classes").css("margin-left","40px");
		$(".arrow-right").css("margin-left","10px");
		$(".rectangle-right").css("display","block");
		$(".arrow-right").css("margin-top","3px");
		$(".rectangle-right").css("margin-top","3px");
	}
	function validate()
	{
		if(document.getElementById('lstClass').value=="")
		{
			alert("Please select a class!");
			document.getElementById('lstClass').focus();
			return false;
		}
		if(document.getElementById('lstTeacherTopic').value=="")
		{
			alert("Please select a topic!");
			document.getElementById('lstTeacherTopic').focus();
			return false;
		}
		if(document.getElementById('lstTeacherTopic').value!="" && document.getElementById('lstClass').value!=""){
			setTryingToUnload();
			window.location.href="topicRemediationSection.php?cls="+document.getElementById('lstClass').value+"&section="+document.getElementById('lstSection').value+"&ttCode="+document.getElementById('lstTeacherTopic').value+"&mode=secRemediation";
		}
	}
	function setSection(sec)
{
	var cls = document.getElementById('lstClass').value;

	if(document.getElementById('lstSection'))
	{
		var obj = document.getElementById('lstSection');
		removeAllOptions(obj);
		if(cls=="")
		{
			$(".noSection").show();
			document.getElementById('lstSection').style.display = "inline";
			document.getElementById('lstSection').selectedIndex = 0;
		}
		else
		{
			for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
			if(sectionArray[i].length>0)
			{
				for (var j=0; j<sectionArray[i].length; j++)
				{
					OptNew = document.createElement('option');
					OptNew.text = sectionArray[i][j];
					OptNew.value = sectionArray[i][j];
					if(sec==sectionArray[i][j])
					OptNew.selected = true;
					obj.options.add(OptNew);
				}
				$(".noSection").show();
				document.getElementById('lstSection').style.display = "inline";
				document.getElementById('lblSection').style.display = "inline";
			}
			else
			{
				$(".noSection").hide();
				document.getElementById('lstSection').style.display = "none";
				document.getElementById('lblSection').style.display = "none";
			}
		}
	}

}

function removeAllOptions(selectbox)
{
	var i;
	for(i=selectbox.options.length-1;i>0;i--)
	{
		selectbox.remove(i);
	}
}

function populateTopic(topicCode)
{
	var cls = document.getElementById('lstClass').value;

	var obj = document.getElementById('lstTeacherTopic');
	removeAllOptions(obj);
	if(cls!="")
	{
		for(var i=0; i<gradeArray.length && gradeArray[i]!=cls; i++);
		if(topicCodeArray[i].length>0)
		{
			for (var j=0; j<topicCodeArray[i].length; j++)
			{
				OptNew = document.createElement('option');
				OptNew.text = topicArray[i][j];
				OptNew.value = topicCodeArray[i][j];
				if(topicCode==topicCodeArray[i][j])
				OptNew.selected = true;
				obj.options.add(OptNew);
			}
		}
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

	<div id="container">
		<div id="trailContainer">
			
			<table id="topicDetails">
				<td width="5%"><label for="lstClass">Class</label></td>
		        <td width="25%" style="border-right:1px solid #626161">
		            <select name="cls" id="lstClass"  onchange="setSection('');populateTopic('')" style="width:65%;">
						<option value="">Select</option>
						<?php
							for ($i=0;$i<count($classArray);$i++)
							{
								echo "<option value='".$classArray[$i]."'";
								if ($cls==$classArray[$i])
								{
									echo " selected";
								}
								echo ">".$classArray[$i]."</option>";
							}
						?>
					</select>
		        </td>
				<td width="6%" class="noSection"><label for="lstSection" id="lblSection" style="margin-left:20px;">Section</label></td>
		        <td width="25%" style="border-right:1px solid #626161" class="noSection">
		            <select name="section" id="lstSection" style="width:65%;">
						<option value="">All</option>
					</select>
		        </td>
				<td width="15%"><label for="lstTeacherTopic" style="margin-left:10px;">Topic</label></td>
		        <td width="20%">
		            <select name="ttCode" id="lstTeacherTopic" style="width:65%;">
					   <option value=''>Select</option>
				    </select>
		        </td>
			</table>
			
			<table id="generateTable">
		        <td width="24%"><input type="submit" class="button" name="generate" id="btnGo" value="Generate" onClick="return validate();"></td>
			</table>
	</div>

<?php include("footer.php") ?>