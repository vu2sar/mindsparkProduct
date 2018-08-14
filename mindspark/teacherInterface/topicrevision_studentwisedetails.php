<?php include("header.php");
		include("../slave_connectivity.php");

    $userID     = $_SESSION['userID'];
    $category   = $_SESSION['admin'];

    if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0)
	{
		echo "You are not authorised to access this page!";
    	exit;
	}

    $studentID = isset($_POST['studentID'])?$_POST['studentID']:"";
    $topic = isset($_POST['topic'])?$_POST['topic']:"";
	
	$fromDate	=	changedateFormat($fromDate);
	$tillDate	=	changedateFormat($tillDate);
	
    $revisionDetails = array();
    if($studentID!="")
    {
    	$query  = "SELECT childName, childClass, childSection FROM adepts_userDetails where userID=$studentID";
    	$result = mysql_query($query);
    	$line   = mysql_fetch_array($result);
    	$studentName = $line[0];
    	$childClass 	= $line[1];
		$childSection	= $line[2];
    	$query  = "SELECT date_format(attemptedDate,'%d-%m-%Y') as dt, b.teacherTopicCode, teacherTopicDesc,
    	                  count(srno) as ques, sum(R) as correct, avg(S) as avgT
    	           FROM   ".TBL_TOPIC_REVISION." a, adepts_teacherTopicMaster b
    	           WHERE  a.teacherTopicCode=b.teacherTopicCode AND userID=$studentID";
    	if($topic!="")
    		$query .= " AND b.teacherTopicCode='$topic'";
        if($fromDate!="")
	        $query .= " AND attemptedDate>='$fromDate'";
	    if($tillDate!="")
	        $query .= " AND attemptedDate<='$tillDate'";
    	$query .= " GROUP BY attemptedDate, teacherTopicCode";
    	$result = mysql_query($query) or die(mysql_error());
    	$srno = 0;
    	while ($line = mysql_fetch_array($result))
    	{
    		$revisionDetails[$srno][0] = $line['dt'];
    		$revisionDetails[$srno][1] = $line['teacherTopicCode'];
    		$revisionDetails[$srno][2] = $line['ques'];
    		$revisionDetails[$srno][3] = round($line['correct']/$line['ques']*100,2);
    		$revisionDetails[$srno][4] = round($line['avgT'],1);
    		$revisionDetails[$srno][5] = $line['teacherTopicDesc'];
    		$srno++;
    	}
	}
?>
<html>
<head>
<title>Mindspark - Topic-wise Practice</title>
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicrevision_studentwisedetails.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/tablesort.js"></script>
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
<script>
function showDetails(dt, ttCode, perCorrect)
{
	document.getElementById('ttCode').value = ttCode;
	document.getElementById('dt').value = dt;
	document.getElementById('perCorrect').value = perCorrect;
	setTryingToUnload();
	document.getElementById('frmRevision').submit();
}
</script>
</head>
<body class="translation" onLoad="load();">
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
<div style="text-align:center; width:100%" >
	<div id="trailContainer">
		<div id="headerBar">
			<div class="pageName">
				<div class="arrow-black"></div>
				<div id="pageText">TOPICWISE PRACTICE</div>
			</div>
			<div class="pageName">
				<div class="arrow-black"></div>
				<div id="pageText">Name: <?=$studentName?></div>
			</div>
		</div>
		
		<table id="generateTable">
			<td width="5%">
				<label for="fromDate">Class</label>
			</td>
			<td width="8%" style="border-right:1px solid #626161;margin-left:15px;font-weight:bold;" align="left"><?=$childClass?></td>
			<td width="9%">
				<label style="margin-left:20px;" for="tillDate">Section</label>
			</td>
			<td width="15%" style="border-right:margin-left:15px;font-weight:bold;" align="left"><?=$childSection?></td>
			<td width="46%"></td>
		</table>
	</div>
</div>

<form id="frmRevision" action="topicrevision_quesDetails.php" method="POST">
<div align="center" id="pnlRevisionDetails">
	<p>
	<table class="gridtable" border="0" cellpadding="3" cellspacing="0" width="90%">
	<thead>
		<tr style="background-color: #DEB887;">
			<td class="header" width="5%" onClick="sortColumn(event)" type="Number"><strong><u>Sr. No.</u></strong></th>
			<td class="header" width="15%" align="left" onClick="sortColumn(event)" type="CaseInsensitiveString"><strong><u>Date</u></strong></td>
			<td class="header" width="35%" onClick="sortColumn(event)" type="CaseInsensitiveString"><strong><u>Topic</u></strong></td>
			<td class="header" width="12%" onClick="sortColumn(event)" type="Number"><strong><u>Total Ques</u></strong></td>
			<td class="header" width="10%" onClick="sortColumn(event)" type="Number"><strong><u>% Correct</u></strong></td>
			<td class="header" width="33%" onClick="sortColumn(event)" type="Number"><strong><u>Avg. time taken to answer<br/>(secs)</u></strong></td>
		</tr>
	</thead>
	<tbody>
<?php	for($i=0; $i<count($revisionDetails); $i++)	{	?>
		<tr>
			<td>
				<a class="user" href="javascript:showDetails('<?=$revisionDetails[$i][0]?>','<?=$revisionDetails[$i][1]?>','<?=$revisionDetails[$i][3]?>')" title="Click here to see the question wise trail">
				<?=$i + 1 ?>
				</a>
			</td>
			<td align="left"><?=$revisionDetails[$i][0]?></td>
			<td align="left"><?=$revisionDetails[$i][5]?></td>
			<td align="center"><?=$revisionDetails[$i][2]?></td>
			<td align="center"><?=$revisionDetails[$i][3]?></td>
			<td align="center"><?=$revisionDetails[$i][4]?></td>
		</tr>
<?php	}	?>
	</tbody>

	</table>
	<div style="font-size:1.2em;float:left;margin-top:20px;margin-left:25%;" class="textRed">Note: All underlined fields below can be sorted. Please click on field name to sort</div>
	<br/>
	</p>
<br/>
<br/>
<input type="hidden" id="studentID" name="studentID" value="<?=$studentID?>">
<input type="hidden" id="ttCode" name="ttCode">
<input type="hidden" id="dt" name="dt">
<input type="hidden" id="studentName" name="studentName" value="<?=$studentName?>">
<input type="hidden" id="perCorrect" name="perCorrect">
</form>
<br/><br/>

</div>
</form>
</div>
<?php include("footer.php");

function changedateFormat($date)
{
	$dateArr	=	explode("-",$date);
	return $dateArr[2]."-".$dateArr[1]."-".$dateArr[0];
}
?>