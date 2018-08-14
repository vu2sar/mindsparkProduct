<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	include("header.php");
	include("classes/clsTeacher.php");
	include("classes/testTeacherIDs.php");
	
	$flagForOffline = false;
	if($_SESSION['isOffline'] === true && SERVER_TYPE=='LIVE')
	{
		$flagForOffline = true;
	}
	if($flagForOffline)
		include("logDeleteQuery.php");

	$userName   = $_SESSION['username'];
	$userid 	= $_SESSION['userID'];
	$category   = $_SESSION['admin'];
	$schoolCode = $_SESSION['schoolCode'];
	$subcategory = $_SESSION['subcategory'];
	

	$query  = "SELECT distinct subjects FROM adepts_userDetails WHERE category='STUDENT' AND schoolCode=$schoolCode";
	$result = mysql_query($query) or die(mysql_error());
	$subjectArray = array();
	while ($line = mysql_fetch_array($result))
	{
		$tmpArray = explode(",",$line[0]);
		for($i=0; $i<count($tmpArray); $i++)
			array_push($subjectArray, $tmpArray[$i]);
	}
	$query  = "SELECT distinct subjectno FROM adepts_userDetails a, adepts_teacherClassMapping b WHERE a.userID=b.userID AND category='TEACHER' AND schoolCode=$schoolCode AND endDate>=curdate()";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		array_push($subjectArray, $line[0]);
	}
	$subjectArray = array_values(array_unique($subjectArray));	//array_values is used to reset the keys.
	if(strcasecmp($category,"School Admin")!=0)
	{
		echo "You are not authorised to access this page!";
		exit();
	}
	if(isset($_POST['mode']) && $_POST['mode']=="delete")
	{
		deleteTeacherID($_POST['userID'],$userName,$flagForOffline);
	}

	$teacherArrayOrder = array();
	//$query = "SELECT a.userID FROM adepts_userDetails a, adepts_teacherClassMapping b WHERE a.userID=b.userID AND schoolCode=$schoolCode AND category='TEACHER' ORDER BY class,section,childName";
	$tmpTeacherIDs = "'".implode("','",$testIDArray)."'";
	$query = "SELECT a.userID, childName, username, childEmail, contactno_res, contactno_cel, city, country
	          FROM   adepts_userDetails a LEFT JOIN adepts_teacherClassMapping b ON a.userID=b.userID
	          WHERE  schoolCode=$schoolCode AND category='TEACHER' AND username NOT in ($tmpTeacherIDs) AND enddate>=curdate()
	          ORDER BY class,section,childName";
	$result = mysql_query($query);
	$i=0;
	while($line = mysql_fetch_array($result)){
		$teacherArrayOrder[$i] = $line['userID'];
		$i++;
	}
	$teacherArrayOrder = array_keys(array_count_values($teacherArrayOrder));

	$teacherArray = array();
	/*$query  = "SELECT userID, childName, username, childEmail, contactno_res, contactno_cel, city, country
	           FROM adepts_userDetails WHERE schoolCode=$schoolCode AND category='TEACHER'  AND username NOT in ($tmpTeacherIDs)";

	$result = mysql_query($query);*/
	mysql_data_seek($result,0);
	while ($line=mysql_fetch_array($result)) {
		$tempTeacher = new teacher();
		$tempName    = explode(" ",$line['childName']);
		if(!isset($tempName[1]))
			$tempName[1] = "";
		$tempTeacher->populateDetails($line['userID'],$tempName[0],$tempName[1],$line['childEmail'],$line['contactno_res'],$line['contactno_res'],$line['city'],$line['country'],$line['username']);
		$teacherArray[$line['userID']]=$tempTeacher;
	}
?>

<title>Teacher Mapping</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/teacherDetails.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
  <script src="libs/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
  });
  </script>
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
		
		document.cookie = 'SHTS=;';
		document.cookie = 'SHTSP=;';
		document.cookie = 'SHTParams=;';
	}
	
</script>
<script>
	history.forward();
	
	function logoff()
	{
		setTryingToUnload();
		window.location="logout.php";
	}
	function trim(str) {
		// Strip leading and trailing white-space
		return str.replace(/^\s*|\s*$/g, "");
	}

	function submitForm(userID)
	{
		<?php
			if($offlineMode === true)
			{				
				echo "alert('This feature is not available in offline mode.')";
			}
			else
			{
		?>
				document.getElementById('userID').value = userID;
				setTryingToUnload();
				document.getElementById('frmTeacherDetails').submit();
		<?php } ?>
	}
	function deleteUser(userID, name)
	{
		<?php
			if($offlineMode === true)
			{				
				echo "alert('This feature is not availbale in offline mode.')";
			}
			else
			{
		?>
				var ans = confirm("Are you sure you want to remove " + name + "'s name?");
				if(ans)
				{
					document.getElementById('userID').value = userID;
					document.getElementById('mode').value = "delete";
					document.getElementById('frmTeacherDetails').action = "teacherDetails.php";
					$(".checkDelete").attr("onclick","");
					setTryingToUnload();
					document.getElementById('frmTeacherDetails').submit();
				}
		<?php } ?>
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
				<span>Teacher Mapping</span>
			</div>
			<div id="containerBody">
			<?php
			if(isset($_SESSION['notification'])) {
				echo $_SESSION['notification'];
				unset($_SESSION['notification']);
			}
			?>
			<form id="frmTeacherDetails" method="POST" action="addEditTeacherDetails.php">

<div>
<?php if(count($teacherArray)>0)	{	?>
<table align="center" cellpadding="3" cellspacing="0" class="tblContent">
	<tr>
		<th align="left" class="header">Sr.No.</th>
		<th align="left" class="header">Name</th>
		<th align="left" class="header">Login ID</th>
		<th align="left" class="header">Email ID</th>
	<?php if(count($subjectArray)==1) { ?>
		<th align="left" nowrap class="header">Classes Mapped</th>
	<?php } else {
		for($j=0; $j<count($subjectArray); $j++)
		{
			echo '<th align="left" class="header">Classes Mapped<br/>';
			if($subjectArray[$j]==2)
				echo "(Maths)";
			if($subjectArray[$j]==3)
				echo "(Science)";
			echo '</th>';
		}
	}
	?>
	<?php if(!($category=="School Admin" && $subcategory=="All")) { ?>
		<th class="header">&nbsp;</th>
		<th class="header">&nbsp;</th>
	<?php } ?>
	</tr>

<?php } ?>
<?php	for($i=0; $i<count($teacherArray); $i++)	{	?>
	<tr>
		<td><?=$i + 1?></td>
		<?php $index = $teacherArrayOrder[$i];?>
		<td align="left"><?=$teacherArray[$index]->firstName." ". $teacherArray[$index]->lastName?></td>
		<td align="left"><?=$teacherArray[$index]->username?></td>
		<td align="left"><?=$teacherArray[$index]->emailID?></td>

		<?php
			for($j=0; $j<count($subjectArray); $j++)
			{
				$class  = "";
				$query  = "SELECT class,section FROM adepts_teacherClassMapping WHERE userID=".$teacherArray[$index]->userID." AND subjectno=".$subjectArray[$j]." ORDER BY class,section";
				$result = mysql_query($query);
				while ($line=mysql_fetch_array($result))
						$class .= $line[0].$line[1].", ";
				$class = substr($class,0,-2);
				echo '<td align="left">'.$class.'</td>';
			}
		?>

	<?php if(!($category=="School Admin" && $subcategory=="All")) { ?>
		<td align="left"><a href="#" onClick="submitForm('<?=$teacherArray[$index]->userID?>');" ><u>Edit</u></a></td>
		<td align="left"><a href="#" onClick="deleteUser('<?=$teacherArray[$index]->userID?>','<?=$teacherArray[$index]->firstName?>');" class="checkDelete" ><u>Delete</u></a></td>
	<?php } ?>
	</tr>
<?php	}	?>
<?php if(count($teacherArray)>0)	{	?></table><?php } ?>
</div>
<?php
	if($offlineMode === true)
	{				
		echo "<script>alert('This feature is not available in offline mode.')</script>";
	}
	else
	{
?>
		<div align="center" style="margin-top:10px;">
			<input type="button" name="createTeacher" id="btnCreateTeacher" value="Add Teacher" class="buttons" onClick="submitForm('')" <?php if($category=="School Admin" && $subcategory=="All") echo " disabled"; ?>>
		</div>
<?php } ?>
<input type="hidden" name="userID" id="userID" value="">
<input type="hidden" name="mode" id="mode" value="">

</form>
			</div>
			
		
		</div>
		
		
	</div>

<?php include("footer.php") ?>

<?php 
function deleteTeacherID($userID,$userName,$flagForOffline)
{
	mysql_query("BEGIN");
	mysql_query("DELETE from adepts_timedTestStatus WHERE userID=".$userID) or die(mysql_error());
	mysql_query("DELETE from adepts_timedTestDetails WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_dynamicParameters WHERE userID='".$userID."'") or die(mysql_error());
	for($class=1; $class<=10; $class++)
	{
		if($flagForOffline)
			logDeleteQuery("DELETE from adepts_teacherTopicQuesAttempt_class$class WHERE userID=$userID",'adepts_teacherTopicQuesAttempt_class$class',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID");
		mysql_query("DELETE from adepts_teacherTopicQuesAttempt_class$class WHERE userID='".$userID."'") or die(mysql_error());		
	}
	if($flagForOffline)
	{
		logDeleteQuery("DELETE from adepts_timedTestStatus WHERE userID=$userID",'adepts_timedTestStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_timedTestDetails WHERE userID=$userID",'adepts_timedTestDetails',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_dynamicParameters WHERE userID=$userID",'adepts_dynamicParameters',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_userGameDetails WHERE userID=$userID",'adepts_userGameDetails',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_userComments WHERE userID=$userID",'adepts_userComments',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_userCommentDetails WHERE userID=$userID",'adepts_userCommentDetails',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_gameStatus WHERE userID=$userID",'adepts_gameStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_ttChallengeQuesAttempt WHERE userID=$userID",'adepts_ttChallengeQuesAttempt',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_teacherTopicClusterStatus WHERE userID=$userID",'adepts_teacherTopicClusterStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_remedialItemAttempts WHERE userID=$userID",'adepts_remedialItemAttempts',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_ttUserCurrentStatus WHERE userID=$userID",'adepts_ttUserCurrentStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_rewardPoints WHERE userID=$userID",'adepts_rewardPoints',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_userBadges WHERE userID=$userID",'adepts_userBadges',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_topicRevisionDetails WHERE userID=$userID",'adepts_topicRevisionDetails',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_revisionSessionStatus WHERE userID=$userID",'adepts_revisionSessionStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_revisionSessionDetails WHERE userID=$userID",'adepts_revisionSessionDetails',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_teacherTopicStatus WHERE userID=$userID",'adepts_teacherTopicStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_sessionStatus WHERE userID=$userID",'adepts_sessionStatus',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from adepts_teacherClassMapping WHERE userID=$userID",'adepts_teacherClassMapping',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID",1);
		logDeleteQuery("DELETE from educatio_educat.common_user_details WHERE MS_userID=$userID",'educatio_educat.common_user_details',$_SESSION["schoolCode"],array('ms_userID'=>$userID),"ms_userID=$userID",'',1);
	}
	mysql_query("DELETE from adepts_userGameDetails WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_userComments WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_userCommentDetails WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_gameStatus WHERE userID='".$userID."'") or die(mysql_error());	
	mysql_query("DELETE from adepts_ttChallengeQuesAttempt WHERE userID='".$userID."'") or die(mysql_error());	    	
	mysql_query("DELETE from adepts_teacherTopicClusterStatus WHERE userID='".$userID."'") or die(mysql_error());					
	mysql_query("DELETE from adepts_remedialItemAttempts WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_ttUserCurrentStatus WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_rewardPoints WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_userBadges WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_topicRevisionDetails WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_revisionSessionStatus WHERE userID='".$userID."'") or die(mysql_error());
	mysql_query("DELETE from adepts_revisionSessionDetails WHERE userID='".$userID."'") or die(mysql_error());
	
	mysql_query("INSERT into adepts_changeLog(tableChanged,identifier,changeComment,modifiedBy) values('adepts_userDetails','".$userID."','delete teacher','".$userName."')") or die(mysql_error());
	
	mysql_query("DELETE from adepts_teacherTopicStatus WHERE userID='".$userID."'") or die(mysql_error());
	$query = "DELETE FROM adepts_sessionStatus WHERE userID=".$userID;
	mysql_query($query)  or die(mysql_error());        
	$query = "DELETE FROM adepts_teacherClassMapping WHERE userID=".$userID;
	mysql_query($query)  or die(mysql_error());    
	$query = "DELETE FROM educatio_educat.common_user_details WHERE MS_userID=".$userID;
	mysql_query($query) or die(mysql_error());         
	mysql_query("COMMIT");
}
?>
