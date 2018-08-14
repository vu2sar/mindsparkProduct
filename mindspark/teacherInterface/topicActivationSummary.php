<?php
	include("header.php");
	include("../slave_connectivity.php");
    include("../userInterface/classes/clsTeacherTopic.php");
	include("../userInterface/functions/functions.php");


    $userID = $_SESSION['userID'];
    $schoolCode = $_SESSION['schoolCode'];
    $category = $_SESSION['admin'];
    $subcategory = $_SESSION['subcategory'];
	

    if(!isset($_SESSION['userID']) || strcasecmp($category, "School Admin")!=0)
    {
    	echo "You are not authorised to access this page!";
    	exit;
    }
	$user   = new User($userID);
	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}
    $clsArray = getClassesForSchool($schoolCode);
    $class = isset($_POST['class'])?$_POST['class']:"";
?>

<title>Topic Activation Summary</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/topicActivationSummary.css" rel="stylesheet" type="text/css">
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
		$("#features").css("font-size","1.4em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
	function showTrail(){
		if(document.getElementById('lstClass').value=="")
	    {
	        alert("Please select a class!");
	        return false;
	    }
		else{
			setTryingToUnload();
			return true;
		}
	}
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
					<div id="pageText">Topic Activation Summary</div>
				</div>
			</div>
			<form id="frmTopicActivationSummary" name="frmTopicActivationSummary" method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<table id="topicDetails">
				<td width="10%"><label>Class</label></td>
		        <td width="35%">
		            <select name="class" id="lstClass" style="width:68%;">
					   <option value="">Select</option>
					<?php foreach ($clsArray as $key=>$val) { ?>
					   <option value="<?=$key?>" <?php if($key==$class) echo " selected";?>><?=$key?></option>
					<?php } ?>
					</select>
		        </td>
				<td width="55%"><input type="submit" class="button" name="btnGenerate" id="generate" value="Go" onclick="return showTrail();"></td>
			</table>
			<?php
			if($class!="")
			{
			    $sectionArray = explode(",",$clsArray[$class]);
			    $allTopicsArray = getAllTopics($schoolCode,SUBJECTNO);
			    $topicActivationDetails = getTopicActivationDetails($schoolCode, $class, SUBJECTNO);
			?>
			<table cellspacing="0" cellpadding="3" class="gridtable" border="1" width="100%">
				<tr>
					<th align="left" class="header" width="15%">&nbsp;</th>
					<th align="center" id="hdMS" class="header">Topic</th>
				<?php foreach ($sectionArray as $val) { ?>
					<th align="center" class="header"><?=$class.$val?></th>
			    <?php } ?>

				</tr>
			<?php
			    $arrActivatedTopics = array_keys($topicActivationDetails);
			    foreach ($allTopicsArray as $classification=>$topicDetails) {
			        $noOfTopics[$classification] = 0;

			        foreach ($topicDetails as $ttCode=>$arrDetails)
			        if(in_array($ttCode,$arrActivatedTopics) || in_array($class,$arrDetails[1]))
			            $noOfTopics[$classification]++;
			    }
			    //$i=0;
			    //print_r($noOfTopics);exit;
			    foreach ($allTopicsArray as $classification=>$topicDetails) {
			        if($noOfTopics[$classification]==0)
			            continue;
			        $arrTemp = array_keys($topicDetails);
			        $cnt = 0;

			       for($i=0;$i<count($arrTemp); $i++) {
			           if(!(in_array($arrTemp[$i],$arrActivatedTopics) || in_array($class,$topicDetails[$arrTemp[$i]][1])))
			               continue;

			?>
				<tr>
			    <?php if($cnt==0) { ?>
					<td align="left" rowspan="<?=$noOfTopics[$classification]?>"><?=$classification?></a></td>
				<?php } ?>
					<td align="left"><?=$topicDetails[$arrTemp[$i]][0]?></td>
					<?php foreach ($sectionArray as $val) { ?>
					<td align="center" title="<?=$topicActivationDetails[$arrTemp[$i]][$val]["activationPeriod"]?>"
			        <?php
			            if($topicActivationDetails[$arrTemp[$i]][$val]["currentlyActive"]==1) echo "style='background-color:green'";
			            else if($topicActivationDetails[$arrTemp[$i]][$val]["days"]>0) echo "style='background-color:red'";
			        ?>
					>&nbsp;</th> <!--<?=$topicActivationDetails[$arrTemp[$i]][$val]["days"]?>-->
			        <?php } ?>
				</tr>

			<?php $cnt++;} } ?>
			</table>
			<div class="textBelow"><div id="squareGreen"></div>Currently Active</div>
			<div class="clear"></div>
			<div class="textBelow"><div id="squareRed"></div>Now deactivated</div>
			<div class="clear"></div>
			<div class="textBelow"><div id="squareGray"></div>Not activated yet</div>
			<div class="clear"></div>
			<div class="textBelow1">Move the mouse on a cell for actual dates of activation or deactivation</div>
			<div class="clear"></div>
			<!--<div class="legend">
			    <br/>
				<table width="30%" class="tblContent" cellspacing="0" cellpadding="2">
				  <tr>
				      <td style="background-color:green">&nbsp;</td>
				      <td>Currently Active</td>
				  </tr>
				  <tr>
				      <td style="background-color:red">&nbsp;</td>
				      <td>Now deactivated</td>
				  </tr>
				  <tr>
				      <td style="background-color:#E6E6E6">&nbsp;</td>
				      <td>Not activated yet</td>
				  </tr>
				</table>
				<br/>
				<span>Move the mouse on a cell for actual dates of activation or deactivation</span>
			</div>-->
			<?php }  ?>

			</form>
			
			<!--<table class="gridtable" border="1" width="100%">
				<tr>
					<th width="25%"></th>
					<th width="45%">TOPIC</th>
					<th width="15%">2A</th>
					<th width="15%">2B</th>
				</tr>
				<tr>
					<td width="25%" rowspan="4" valign="top">ALGEBRA</td>
					<td width="45%">Shape and Space</td>
					<td width="15%"><div class="red"></div></td>
					<td width="15%"><div class="green"></div></td>
				</tr>
				<tr>
					<td width="45%">Time</td>
					<td width="15%"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Adition upto 999(with regrouping)</td>
					<td width="15%"><div class="red"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Introdection to function</td>
					<td width="15%"><div class="green"></div></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="25%" rowspan="7" valign="top">NUMBERS</td>
					<td width="45%">Shape and Space</td>
					<td width="15%"><div class="red"></div></td>
					<td width="15%"><div class="green"></div></td>
				</tr>
				<tr>
					<td width="45%">Time</td>
					<td width="15%"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Adition upto 999(with regrouping)</td>
					<td width="15%"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Introdection to function</td>
					<td width="15%"><div class="green"></div></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Time</td>
					<td width="15%"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Adition upto 999(with regrouping)</td>
					<td width="15%"></td>
					<td width="15%"></td>
				</tr>
				<tr>
					<td width="45%">Introdection to function</td>
					<td width="15%"><div class="green"></div></td>
					<td width="15%"></td>
				</tr>
			</table>-->
			
			
		</div>
	</div>

<?php include("footer.php") ?>

<?php
function getTopicActivationDetails($schoolCode, $class, $subjectno)
{
    $topicActivationDetails = array();
    $query  = "SELECT section, a.teacherTopicCode, activationDate, deactivationDate
	           FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
	           WHERE  a.schoolCode=$schoolCode AND a.class=$class AND a.teacherTopicCode=b.teacherTopicCode AND subjectno=$subjectno
	           ORDER BY activationDate ";

    $result = mysql_query($query) or die("Error in fetching data!".$query);
    while ($line=mysql_fetch_array($result))
	{
	    if(!isset($topicActivationDetails[$line['section']][$line['teacherTopicCode']]))
	    {
		    $topicActivationDetails[$line['teacherTopicCode']][$line['section']]["days"] = "0";
		    $topicActivationDetails[$line['teacherTopicCode']][$line['section']]["activationPeriod"] = "";
	    }
	    $activationPeriod = "From: ".substr($line['activationDate'],8,2)."-".substr($line['activationDate'],5,2)."-".substr($line['activationDate'],0,4);
	    $startDate = $line['activationDate'];
	    $currentlyActive =0;
		if($line['deactivationDate']!="0000-00-00")
		{
			$activationPeriod .= " to ".substr($line['deactivationDate'],8,2)."-".substr($line['deactivationDate'],5,2)."-".substr($line['deactivationDate'],0,4);
			$endDate = $line['deactivationDate'];
		}
		else
		{
			$activationPeriod .= " till date";
			$endDate = date("Y-m-d");
			$currentlyActive = 1;
		}
		$activationPeriod .= "\n";
		$topicActivationDetails[$line['teacherTopicCode']][$line['section']]["activationPeriod"] .= $activationPeriod;
		$topicActivationDetails[$line['teacherTopicCode']][$line['section']]["days"] += dateDiff("-",$endDate, $startDate);
		$topicActivationDetails[$line['teacherTopicCode']][$line['section']]["currentlyActive"] = $currentlyActive;
	}

	return $topicActivationDetails;
}

function getDefaultFlowForTheSchool($schoolCode){

	$defaultFlow = 'MS';

	$flow_query  = "SELECT settingValue FROM userInterfaceSettings WHERE schoolCode='$schoolCode' and settingName='curriculum' limit 1";

	$flow_result = mysql_query($flow_query);

	if($flow_line=mysql_fetch_assoc($flow_result))
	{
				$defaultFlow = $flow_line['settingValue'];		
	}
	
	return $defaultFlow;

}


function getAllTopics($schoolCode, $subjectno)
{
	
    $allTopicsArray = array();
    /*$defaultFlow = "MS";
	$flow_query  = "SELECT defaultFlow FROM adepts_schoolRegistration WHERE school_code=$schoolCode";
	$flow_result = mysql_query($flow_query);
	if($flow_line=mysql_fetch_array($flow_result))
	{
		$defaultFlow = $flow_line[0];
	}*/
	
	$defaultFlow = getDefaultFlowForTheSchool($schoolCode);


    $query  = "SELECT teacherTopicDesc, classification,teacherTopicCode FROM adepts_teacherTopicMaster
	           WHERE  live=1 AND subjectno=$subjectno ORDER BY classification, teacherTopicOrder";

    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result)) {
    	$classLevel = getClassLevel($line['teacherTopicCode'],$defaultFlow);
    	$allTopicsArray[$line['classification']][$line['teacherTopicCode']][0] = $line['teacherTopicDesc'];
    	$allTopicsArray[$line['classification']][$line['teacherTopicCode']][1] = $classLevel;
    }
    return $allTopicsArray;
}
?>