<?php include("header.php");
	include("../slave_connectivity.php");
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    include("../userInterface/functions/orig2htm.php");
    error_reporting(E_ERROR);

    if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    $userID     = $_SESSION['userID'];
    $category   = $_SESSION['admin'];
    $schoolCode = $_SESSION['schoolCode'];

	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   childClass, group_concat(distinct childSection ORDER BY childSection)
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects LIKE '%".SUBJECTNO."%'
		           GROUP BY childClass ORDER BY cast(childClass as unsigned), childSection";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		$query = "SELECT   class, group_concat(distinct section ORDER BY section)
				  FROM     adepts_teacherClassMapping
				  WHERE    userID = $userID AND subjectno='".SUBJECTNO."'
				  GROUP BY class ORDER BY class, section";
	}
	else
	{
		echo "You are not authorised to access this page!";
    	exit;
	}

	$classArray = $sectionArray = array();
	$hasSections = false;
	$result = mysql_query($query) or die(mysql_error());
	$topic_code_array_string = $topic_desc_array_string = "";
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
		array_push($sectionArray, $sectionStr);

		//Get the list of activated topics

		$topic_code_array_string .= "var code_".$line[0]." = new Array( ";
		$topic_desc_array_string .= "var desc_".$line[0]." = new Array( ";
		$topic_query = "SELECT distinct b.teacherTopicCode, teacherTopicDesc
						FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
						WHERE  a.teacherTopicCode=b.teacherTopicCode AND
							   a.schoolCode=$schoolCode AND
						       a.class = $line[0] AND subjectNo=".SUBJECTNO."
				        ORDER BY teacherTopicDesc";
		$topic_result = mysql_query($topic_query) or die("<br>Error in topic query - ".mysql_error().$topic_query);
		while ($topic_data = mysql_fetch_array($topic_result))
		{
			$topic_code_array_string .= "'".$topic_data['teacherTopicCode']."',";
			$topic_desc_array_string .= "'".$topic_data['teacherTopicDesc']."',";
		}
		$topic_code_array_string = substr($topic_code_array_string, 0, -1);
		$topic_desc_array_string = substr($topic_desc_array_string, 0, -1);
		$topic_code_array_string .= ");\n";
		$topic_desc_array_string .= ");\n";
	}

	//print_r($_POST);

	$cls     = isset($_REQUEST['cls'])?$_REQUEST['cls']:"";
	$section = isset($_REQUEST['section'])?$_REQUEST['section']:"";
	$topic   = isset($_REQUEST['topic'])?$_REQUEST['topic']:"";
	$tillDate = isset($_POST['tillDate'])?$_POST['tillDate']:date("Y-m-d");
	$fromDate = isset($_POST['fromDate'])?$_POST['fromDate']:date("Y-m-d",mktime(0,0,0,date("m"),1,date("Y")));

	$todaysDate = date("d");
	$studentDetails = array();
	if($cls!="")
	{
		$query = "SELECT a.userID, a.childName, a.childClass, a.childSection, count(b.srno) as totalq, sum(R) as correct, count(distinct teacherTopicCode) as nooftopics
		          FROM   adepts_userDetails a LEFT JOIN ".TBL_TOPIC_REVISION." b ON (a.userID=b.userID";
		if($fromDate!="")
	        $query .= " AND attemptedDate>='$fromDate'";
	    if($tillDate!="")
	        $query .= " AND attemptedDate<='$tillDate'";
		$query .= ")
		          WHERE  category='STUDENT' AND subcategory='School' AND enabled=1 AND enddate>=curdate() AND
		                 schoolCode=$schoolCode AND childClass='$cls'";
		if($section!="")
			$query .= " AND childSection='$section' ";
		if($topic!="")
			$query .= " AND b.teacherTopicCode='$topic' ";

		$query .= " GROUP BY a.userID ORDER BY childClass, childSection, childName";
		//echo $query;
		$result = mysql_query($query) or die("Error in query!".$query.mysql_error());
		$srno = 0;
		while ($line = mysql_fetch_array($result))
		{
			$studentDetails[$srno][0] = $line['userID'];
			$studentDetails[$srno][1] = $line['childName'];
			$studentDetails[$srno][2] = $line['childClass'].$line['childSection'];
			$studentDetails[$srno][3] = $line['totalq'];
			$studentDetails[$srno][4] = "";
			if($line['totalq']!=0)
				$studentDetails[$srno][4] = round($line['correct']/$line['totalq']*100,2);
			else
				$studentDetails[$srno][4] = "&nbsp;";
			$studentDetails[$srno][5] = $line['nooftopics'];
			$srno++;
		}
	}


?>
<html>
<head>
<title>Mindspark - Topic-wise Practice</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicrevesion_summary.css" rel="stylesheet" type="text/css">
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<script src="libs/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<script src="libs/jquery-ui.js"></script>
<script>
$(function() {
	$( ".datepicker" ).datepicker({ dateFormat: 'yy/mm/dd' });
});
</script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
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
	var gradeArray   = new Array();
    var sectionArray = new Array();
	<?php
		for($i=0; $i<count($classArray); $i++)
		{
		    echo "gradeArray.push($classArray[$i]);\r\n";
		    echo "sectionArray[$i] = new Array($sectionArray[$i]);\r\n";

		}
		print($topic_code_array_string);
		print($topic_desc_array_string);
	?>

    function validate()
    {
        if(document.getElementById('fromDate').value=='')
        {
        	alert("Please specify the activation date!");
        	return false;
        }
		setTryingToUnload();
    }
    function trim(str) {
        // Strip leading and trailing white-space
        return str.replace(/^\s*|\s*$/g, "");
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
	    	        document.getElementById('lstSection').style.display = "inline";
	    	        document.getElementById('lblSection').style.display = "inline";
		        }
		        else
		        {
		        	document.getElementById('lstSection').style.display = "none";
				    document.getElementById('lblSection').style.display = "none";
		        }
		    }
		}
	}
	function populateTopic(topic)
	{
		var obj = document.getElementById('lstTopic');
		removeAllOptions(obj);
		var childClass = document.getElementById('lstClass').value;
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
				obj.options.add(OptNew);
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

	function showReport(userID)
	{
		document.getElementById('studentID').value = userID;
		document.getElementById('frmRevision').action = "topicrevision_studentwisedetails.php";
		setTryingToUnload();
		document.getElementById('frmRevision').submit();
	}
	function validate()
	{
		var cls = document.getElementById('lstClass').value;
		if(cls=="")
		{
			alert("Please select a class");
			return false;
		}
		setTryingToUnload();
		return true;
	}


</script>
</head>
<body class="translation" onLoad="load(); setSection('<?=$section?>'); populateTopic('<?=$topic?>')">
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
<form id="frmRevision" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<div style="text-align:center; width:100%" >
	<div id="trailContainer">
		<table id="topicDetails">
			<td width="5%"><label for="lstClass">Class:</label></td>
			<td width="25%" style="border-right:1px solid #626161">
				<select id="lstClass" name="cls" onChange="setSection(''); populateTopic('');">
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
			<td width="6%"><label for="lstSection" id="lblSection" style="margin-left:20px;">Section</label></td>
			<td width="25%" style="border-right:1px solid #626161">
				<select name="section" id="lstSection" style="width:65%;">
					<option value="">All</option>
				</select>
			</td>
			<td width="15%"><label for="lstTopic" id="lblTopic">Topic:</label></td>
			<td width="20%">
				<select id="lstTopic" name="topic">
					<option value="">All</option>
				</select>
			</td>
		</table>
		
		<table id="generateTable">
			<td width="5%">
				<label for="fromDate">FROM</label>
			</td>
			<td width="25%" style="border-right:1px solid #626161"><input type="text" name="fromDate" value="<?=$fromDate?>" class="datepicker floatLeft" id="dateFrom" value="" autocomplete="off" size="20"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div></td>
			<td width="6%">
				<label style="margin-left:20px;" for="tillDate">TO</label>
			</td>
			<td width="25%" style="border-right:1px solid #626161"><input type="text" name="tillDate" value="<?=$tillDate?>" class="datepicker floatLeft" id="dateTo" value="" autocomplete="off" size="20"/><div class="calenderImage linkPointer" id="to" onClick="openCalender(id)"></div></td>
			<td width="24%"><input type="submit" class="button" value="Go" id="btnGo" name="btnGo" onClick="return validate();"></td>
		</table>
	</div>
</div>
<?php
if($cls!="")
{
?>
<div align="center" id="pnlRevisionDetails">
<?php if(count($studentDetails)>0) { ?>
<table class="tblContent gridtable" border="0" cellpadding="3" cellspacing="0">
		<tr>
			<th class="header">Sr.No.</th>
			<th class="header">Name</th>
			<th class="header">Class</th>
			<th class="header">Total Ques</th>
			<th class="header">% Correct</th>
<?php if($topic=="") { ?>
			<th class="header">No. of topics revised</th>
<?php } ?>
		</tr>
<?php	for($i=0; $i<count($studentDetails); $i++)	{	?>
		<tr>
			<td><?=$i + 1 ?></td>
<?php if($studentDetails[$i][3]==0) { ?>
			<td align="left"><?=$studentDetails[$i][1]?></td>
<?php }  else { ?>
			<td align="left"><a href="javascript:showReport(<?=$studentDetails[$i][0]?>)"><?=$studentDetails[$i][1]?></a></td>
<?php } ?>
			<td><?=$studentDetails[$i][2]?></td>
			<td><?=$studentDetails[$i][3]?></td>
			<td><?=$studentDetails[$i][4]?></td>
<?php if($topic=="") { ?>
			<td><?=$studentDetails[$i][5]?></td>
<?php } ?>
		</tr>
<?php	}	?>
	</table>
<br/><br/>

<?php } else echo "No students have attempted the revision module for this topic!";?>
</div>
<?php } ?>
<input type="hidden" id="studentID" name="studentID">
</form>
</div>
</body>
</html>