<?php
	include("header.php");
	include("../slave_connectivity.php");
	include("classes/testTeacherIDs.php");
	include("../userInterface/functions/functions.php");
	error_reporting(E_ERROR);

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$exerciseCode = isset($_GET['exerciseCode'])?$_GET['exerciseCode']:"";
	$winTitle = isset($_GET['winTitle'])?$_GET['winTitle']:"";
	$schoolCode = isset($_GET['schoolCode'])?$_GET['schoolCode']:"";
	$childClass = isset($_GET['childClass'])?$_GET['childClass']:"";
	$section = isset($_GET['section'])?$_GET['section']:"";
	
	$beforeDueDate = array();
	$afterDueDate = array();
	$userArray = getStudentDetails($childClass,$schoolCode,$section);
	$userIDs = array_keys($userArray);
	
	$userIDs = implode(",",$userIDs);
	$query = "SELECT userID, IF(a.submitDate < b.deactivationDate,'YES','NO') FROM adepts_ncertHomeworkStatus a, adepts_ncertHomeworkActivation b WHERE result='SUCCESS' AND userID IN ($userIDs) AND a.exerciseCode=b.exerciseCode AND b.schoolCode='$schoolCode' AND a.exerciseCode='$exerciseCode'";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		if($row[1] == "YES")
			array_push($beforeDueDate,$row[0]);
		else
			array_push($afterDueDate,$row[0]);
	}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Homework Status</title>
<style type="text/css">
a
{
	text-decoration:none;
}
a:hover,a:active
{
	color:inherit !important;
}
#studentList
{
	float:left;
}
#notes
{
	float:right;
	border:#CCC thin dotted;
	padding:10px;
	background-color:#EEE;
	margin-top:30px;
}
</style>
<script type="text/javascript">
function showTrail(exerciseCode, userID, showTrail)
{
	if(showTrail)
	{
		document.getElementById("student_userID").value = userID;
		document.getElementById("exercise").value = exerciseCode;
		document.trailForm.submit();
		return(false);
	}
	else
	{
		return(false);
	}
}
</script>
</head>
<body>
    	<h3 align="center">Class <?=$childClass?><?php if($section!= "") echo "-$section"; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$winTitle?></h3>
        <div style="margin:0px 30px;">
            <div id="studentList">
            	<h4>Students <span class="boldText">*&nbsp;</span></h4>
                <?php
					$counter = 1;
                    foreach($userArray as $userID=>$detail)
                    {
						$showTrail = "true";
						$cssClass = "redText";
						if(in_array($userID,$beforeDueDate))
						{
							$showTrail = "true";
							$cssClass = "greenText";
						}
						else if(in_array($userID,$afterDueDate))
						{
							$showTrail = "true";
							$cssClass = "blueText";
						}
                        echo '<a href="javascript:void(0)" onclick="showTrail(\''.$exerciseCode.'\',\''.$userID.'\','.$showTrail.')" class="'.$cssClass.'">'.$counter.') '.$detail[0].'</a><br>';
						$counter++;
                    }
                ?>
            </div>
            <div id="notes">
                <span class="boldText">*&nbsp;<span style="text-decoration:underline">Notes:</span></span><br><br>
                <span class="greenText">Submitted before due date.</span><br>
                <span class="blueText">Submitted after due date.</span><br>
                <span class="redText">Not yet submitted.</span><br>
            </div>
        </div>
<form name="trailForm" method="post" action="studentTrail.php" target="_top">
	<input type="hidden" name="student_userID" id="student_userID" value="" />
	<input type="hidden" name="trailType" id="trailType" value="ncert" />
	<input type="hidden" name="exercise" id="exercise" value="" />
</form>
</body>
</html>