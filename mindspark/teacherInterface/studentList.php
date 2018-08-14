<?php
    error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
    set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/functions/orig2htm.php");
  
    if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
    {

    	echo "You are not authorised to access this page!";
    	exit;
    }
    else
    {
    	$userID     = $_SESSION['userID'];
    	$category   = $_SESSION['admin'];
    }
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0)
	{
	
		echo "You are not authorised to access this page!";
    	exit;
	}

    $revisionSessionID = $_POST['revisionSessionID'];

    $query  = "SELECT class, section, date_format(activationDate,'%d-%m-%Y') as dt, schoolCode, teacherTopicCode
               FROM   adepts_revisionSessionMaster
               WHERE  revisionSessionID=".$revisionSessionID;
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    $class  = $line['class'];
    $schoolCode = $line['schoolCode'];
    $section = $line['section'];
    $activationDate = $line['dt'];
    $arrTeacherTopicCodes = explode(",",$line['teacherTopicCode']);
    $arrTeacherTopicDesc = array();
    $query = "SELECT teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode in ('".implode("','",$arrTeacherTopicCodes)."')";
    $tt_result = mysql_query($query);
    while($tt_line = mysql_fetch_array($tt_result))
    {
    	$arrTeacherTopicDesc[$tt_line['teacherTopicCode']] = $tt_line['teacherTopicDesc'];
    }
    $revisionSessionDetails = array();
    $query = "SELECT   a.userID,childName, a.status, a.noOfQuestions  FROM adepts_revisionSessionStatus a, adepts_userDetails b WHERE a.userID=b.userID AND revisionSessionID=$revisionSessionID ORDER BY childName";    
    $result = mysql_query($query) or die(mysql_error());
    $srno = 0;
    while ($line = mysql_fetch_array($result))
    {
        if($line['status']=="completed" && $line['noOfQuestions']==0)    //Such students will be shown in second table - students who have not completed any  cluster during the period considered for this revision session
            continue;
    	$revisionSessionDetails[$srno][0] = $line['userID'];
    	$revisionSessionDetails[$srno][1] = $line['childName'];    	
        $revisionSessionDetails[$srno][5] = $line['status'];
        $status_query = "SELECT   count(srno) as noOfQues, SUM(IF(R=1,1,0)) as correct, avg(S) as avgTime  FROM adepts_revisionSessionDetails  WHERE   revisionSessionID=$revisionSessionID AND userID=".$line['userID'];    	
    	$status_result = mysql_query($status_query);
    	$status_line   = mysql_fetch_array($status_result);
    	$revisionSessionDetails[$srno][2] = $status_line['noOfQues'];
        if($status_line['noOfQues']>0) 
        {        
        	$revisionSessionDetails[$srno][3] = round($status_line['correct']/$status_line['noOfQues']*100,1);
        	$revisionSessionDetails[$srno][4] = round($status_line['avgTime'],1);
        }
        else
        {
        	$revisionSessionDetails[$srno][3] = "";
        	$revisionSessionDetails[$srno][4] = "";        
        }
    	$srno++;
    }

    $studentsNotLoggedIn = array();
   	$query = "SELECT userID, childName FROM adepts_userDetails WHERE schoolCode=$schoolCode AND childClass='$class' ";
   	if($section!="")
   		$query .= " AND childSection='$section'";
   	$query .= " AND endDate>=curdate() AND enabled=1 And subcategory='school' AND userID NOT IN (SELECT userID FROM adepts_revisionSessionStatus WHERE revisionSessionID=$revisionSessionID)";
    $result = mysql_query($query) or die(mysql_error());
   	while ($line = mysql_fetch_array($result))
	   	array_push($studentsNotLoggedIn,$line[1]);



    $studentsNotClearedACluster = array();
    $query = "SELECT a.userID, childName FROM adepts_userDetails a, adepts_revisionSessionStatus b
              WHERE  a.userID=b.userID AND revisionSessionID=$revisionSessionID AND noOfQuestions=0 AND status='completed'";
    $result = mysql_query($query);
	while ($line = mysql_fetch_array($result))
    	array_push($studentsNotClearedACluster, $line[1]);



?>
<title>Revision Session Summary</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/studentList.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
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
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
</script>
<script type="text/javascript" src="libs/tablesort.js"></script>
<script>
function showDetails(revisionSessionID, userID, name, childClass, noOfTotalQuesAttempted)
{
	document.getElementById('revisionSessionID').value = revisionSessionID;
	document.getElementById('studentID').value = userID;
	document.getElementById('studentName').value = name;
	document.getElementById('childClass').value = childClass;
	document.getElementById('noOfTotalQuesAttempted').value = noOfTotalQuesAttempted;	//For the mantis task 8192
	setTryingToUnload();
	document.getElementById('frmRevisionSession').submit();
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<div style="float:left;margin-right: 35%;">REVISION SESSION</div>
				
				<div id="triangle"> </div>
				<span>CLASS <?=$class?> <span style="color:#2f99cb;"> : ACTIVATION DATE <?=$activationDate?></span> </span>
			</div>
			<div style="margin-top: 1%;margin-bottom: 1%;text-align:center;font-size:1.2em;"><span style="color:red;font-size: 1.1em;">NOTE : All underlined fields below can be sorted. Click on field name to sort. </span> </div>
		
		<form id="frmRevisionSession" action="studentReport.php" method="POST">
		<table class="tblContent" border="0" cellpadding="3" cellspacing="0" width="100%">
	<thead>
		<tr>
			<td class="header" width="10%" onClick="sortColumn(event)" type="Number"><strong><u>Sr. No.</u></strong></th>
			<td class="header" width="35%" align="left" onClick="sortColumn(event)" type="CaseInsensitiveString"><strong><u>Name</u></strong></td>
			<td class="header" width="20%" onClick="sortColumn(event)" type="Number"><strong><u>No. of questions<br/>attempted</u></strong></td>
			<td class="header" width="15%" onClick="sortColumn(event)" type="Number"><strong><u>% Correct</u></strong></td>
			<td class="header" width="20%" onClick="sortColumn(event)" type="Number"><strong><u>Avg. time taken to answer<br/>(secs)</u></strong></td>
			<td class="header" width="20%" onClick="sortColumn(event)" type="CaseInsensitiveString"><strong><u>Status</u></strong></td>
		</tr>
	</thead>
	<tbody>
<?php	for($i=0; $i<count($revisionSessionDetails); $i++)	{	?>
		<tr>
			<td><?=$i + 1 ?></td>
			<td align="left">
				<a class="user" href="javascript:showDetails(<?=$revisionSessionID?>,<?=$revisionSessionDetails[$i][0]?>,'<?=$revisionSessionDetails[$i][1]?>','<?=$class?>','<?=$revisionSessionDetails[$i][2]?>')" title="Click here to see the question wise trail">		<!-- For the mantis task 8192 -->
					<span style="color: #2F99CB"><b><u><?=$revisionSessionDetails[$i][1]?></u></b></span>
				</a>
			</td>
			<td align="center"><?=$revisionSessionDetails[$i][2]?></td>
			<td align="center"><?=$revisionSessionDetails[$i][3]?></td>
			<td align="center"><?=$revisionSessionDetails[$i][4]?></td>
			<td align="center"><?=$revisionSessionDetails[$i][5]?></td>
		</tr>
<?php	}	?>
	</tbody>

	</table>
	<?php if(count($arrTeacherTopicDesc)>0) { ?>
	<p style="font-size: 14px"><strong>Topics included in this revision session:<br/></strong>
		<ul style='font-size:14px'>
	<?php
		foreach($arrTeacherTopicDesc as $code=>$desc) {
			echo "<li>$desc</li>";
		}	
	?>
		</ul>
	</p>
	 <?php } ?>
	<?php if(count($studentsNotClearedACluster)>0) {?>
	<div style="margin-top: 5%;">
	<p style="font-size: 14px"><strong>Student(s) who have not completed any learning unit from the revision session topics:<br/></strong>	<!--  For the mantis task 8192 -->
	<table class="tblContent" border="0" cellpadding="3" cellspacing="0" width="100%">
		<tr align="left">
			<th align="left" class="header">Sr. No.</th>
			<th align="left" class="header">Name</th>
		</tr>

	<?php for($j=0; $j<count($studentsNotClearedACluster); $j++) { ?>
		<tr>
			<td><?=($j + 1)?></td>
			<td><?=$studentsNotClearedACluster[$j]?></td>
		</tr>
	<?php } ?>
	</table>
	</p>
	</div>
<?php	}	?>
<?php if(count($studentsNotLoggedIn)>0) {?>
	<div style="margin-top: 5%;">
	<p style="font-size: 14px"><strong>Student(s) who have not logged in after the revision session was activated:<br/></strong>
	<table class="tblContent" cellpadding="3" cellspacing="0" width="100%">
		<tr align="left">
			<th align="left" class="header">Sr. No.</th>
			<th align="left" class="header">Name</th>
		</tr>

	<?php for($j=0; $j<count($studentsNotLoggedIn); $j++) { ?>
		<tr>
			<td><?=($j + 1)?></td>
			<td><?=$studentsNotLoggedIn[$j]?></td>
		</tr>
	<?php } ?>
	</table>
	</p>
	</div>
<?php	}	?>

<input type="hidden" id="revisionSessionID" name="revisionSessionID">
<input type="hidden" id="studentID" name="studentID">
<input type="hidden" id="studentName" name="studentName">
<input type="hidden" id="childClass" name="childClass">
<input type="hidden" id="noOfTotalQuesAttempted" name="noOfTotalQuesAttempted">		<!-- For the mantis task 8192 -->
		</form>
		
		
		</div>
		

	</div>

<?php include("footer.php") ?>