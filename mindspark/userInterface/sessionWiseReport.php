<?php
    
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include("check1.php");
	include_once("constants.php");
	include("classes/clsUser.php");
	
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$userID = $_SESSION['userID'];
	
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	
	$user = new User($userID);

	$childName 	   = $user->childName;
	$schoolCode    = $user->schoolCode;
	$childClass    = $user->childClass;
	$childSection  = $user->childSection;
	$category 	   = $user->category;
	$subcategory   = $user->subcategory;

	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	
	$arrSessionDetails	=	getSessionReport($userID);
	$totalDuration	=	array_pop($arrSessionDetails);

	
?>

<?php include("header.php"); ?>

<title>Session Wise Report</title>
<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/sessionWiseReport/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/sessionWiseReport/midClass.css" />
<?php } else if($theme==3) { ?>
    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
    <link href="css/sessionWiseReport/higherClass.css" rel="stylesheet" type="text/css">
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
function load(){
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (220 +65);
	$('#dataTableDiv').css("height",a+"px");
	$(".forHigherOnly,.forHighestOnly").remove();
<?php } else if($theme==2){ ?>
	/*var a= window.innerHeight - (237 +80);
	$('#endSessionDataDivMain').css("height",a+"px");
	$(".forLowerOnly,.forHighestOnly").remove();*/
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (150);
			var b= window.innerHeight - (610);
			$('#dataTableDiv').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
			$(".forLowerOnly,.hidden").remove();
		<?php } ?>
		if(androidVersionCheck==1){
			$('#dataTableDiv').css("height","auto");
			$('#endSessionDataDivMain').css("height","auto");
			$('#main_bar').css("height",$('#endSessionDataDivMain').css("height"));
			$('#menu_bar').css("height",$('#endSessionDataDivMain').css("height"));
			$('#sideBar').css("height",$('#endSessionDataDivMain').css("height"));
		}
}
function showReport(sessionID, reportDate)
{
	document.getElementById("sessionID").value = sessionID;
	document.getElementById("reportDate").value = reportDate;
	setTryingToUnload();
	document.frmReport.submit();
}

$(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		$(".translation").i18n();
		$(document).attr("title",i18n.t("sessionWiseReportPage.title"));
	});
});

function logoff()
{
	setTryingToUnload();
	window.location="logout.php";
}

function getHome()
{
	setTryingToUnload();
	window.location.href	=	"home.php";
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
<form method="POST" action="endSessionReport.php" id="frmReport" name="frmReport">
	<div id="top_bar">
		<div class="logo">
		</div>
        
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a class="removeDecoration" href='javascript:void(0)'><span><?=$Name?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span  id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <div id="home">
                <div id="homeIcon" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration textUppercase" href="dashboard.php" data-i18n="dashboardPage.dashboard"></a> - <font color="#606062"><span data-i18n="sessionWiseReportPage.sessionWiseReport"></span></font></div>
                <div class="clear"></div>
            </div>

        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                  	<div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="forHigherOnly"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> &#10140; &nbsp;<font color="#606062"><a class="removeDecoration textUppercase" href="dashboard.php" data-i18n="dashboardPage.dashboard"></a></font> &#10140; &nbsp; <font color="#606062"> <span data-i18n="sessionWiseReportPage.sessionWiseReport"></span></font></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>
			</div>
            
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
            
            <div class="clear"></div>
		</div>
        <div id="info_bar" class="forHighestOnly">
				<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div id="sessionHeading">SESSION - WISE REPORT</div>
				<div class="clear"></div>
			</div>
			<table width="90%" border="0" id= "table_head" class="endSessionTbl higherAllign forHigherOnly" align="center">
                    <tr class="trHead colorClass">
                        <td width="7%" data-i18n="common.srno"></td>
                        <td width="20%" data-i18n="common.sessionID"></td>
                        <td width="15%" data-i18n="topicWiseQuesTrailPage.startTime" class="hidden"></td>
                        <td width="15%" data-i18n="sessionWiseReportPage.endTime" class="hidden"></td>
						<td width="30%" data-i18n="sessionWiseReportPage.startAndDuration" class="forHighestOnly" colspan="2"></td>
                       <td width="7%" class="hidden">Duration</td>
                        <td width="35%" data-i18n="sessionWiseReportPage.sessionDetails"></td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="6" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="6"></td></tr>
				</table>
        <div id="endSessionDataDivMain">
            <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="current">CURRENT SESSION</div>
			</div>
			</div>
			<table width="90%" border="0" class="endSessionTbl higherAllign forLowerOnly" align="center">
                    <tr class="trHead colorClass">
                        <td width="10%" style="border: 1px solid gray;" data-i18n="common.srno"></td>
                        <td width="30%" style="border: 1px solid gray;" data-i18n="common.sessionID"></td>
                        <td width="12%" style="border: 1px solid gray;" data-i18n="topicWiseQuesTrailPage.startTime" class="hidden"></td>
                        <td width="23%" style="border: 1px solid gray;" data-i18n="sessionWiseReportPage.endTime" class="hidden"></td>
						<td width="30%" style="border: 1px solid gray;" data-i18n="sessionWiseReportPage.startAndDuration" class="forHighestOnly" colspan="2"></td>
                        <td width="10%" style="border: 1px solid gray;" data-i18n="[html]sessionWiseReportPage.duration" class="hidden"></td>
                        <td width="35%" style="border: 1px solid gray;" data-i18n="sessionWiseReportPage.sessionDetails"></td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="6" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="6"></td></tr>
				</table>
            <div id="dataTableDiv">
                
                <table width="90%" border="0" class="endSessionTbl media1024" align="center">
				<?php foreach($arrSessionDetails as $sessionID=>$sessionDetails) { ?> 
                    <tr>
                       <td <?php if($theme==2) echo 'width="7%"';else echo 'width="10%"';?>><?=++$i?></td>
                        <td <?php if($theme==2) echo 'width="20%"';else echo 'width="30%"';?> class="blue"><a class="removeDecoration" style="text-decoration: underline;" href="javascript:showReport('<?=$sessionID?>','<?=$sessionDetails["starttime"]?>')"><?=$sessionID?></a></td>
                        <td <?php if($theme==2) echo 'width="15%"';else echo 'width="12%"';?> class="hidden"><?=$sessionDetails["starttime"]?></td>
						<td width="55%" class="forHighestOnly">Start Time :</br><?=$sessionDetails["starttime"]?></td>
                        <td <?php if($theme==2) echo 'width="15%"';else echo 'width="25%"';?> class="hidden"><?=$sessionDetails["endtime"]?></td>
						<td <?php if($theme==2) echo 'width="7%"';else echo 'width="10%"';?> class="hidden forLowerOnly"><?=$sessionDetails["duration"]?></td>
						<td class="hidden forHigherOnly">
							<?php 
							$hrs = substr($sessionDetails["duration"],0,2);
							$mns = substr($sessionDetails["duration"],3,2);
							if($hrs == '01')
									$mns = $mns+60;
							elseif($hrs == '02')
									$mns = $mns+120;
							elseif($hrs >= '03')
									$mns = $mns+180;

							if($mns == '' || $mns == 0)
								$mns = 00;

							if($mns < 2)
								{
									$mntmssg = 'min'; 
									$mns= ($mns*10)/10;
						}
							else{
								$mntmssg = 'mins';
								$mns= ($mns*10)/10;
							}

							if($mns < 60) { ?>
								<div id="text"><?=$mns."&nbsp;".$mntmssg?>

							<?php } else {
										if($mns > 180)
											$pixel = 38;
										else
											$pixel = 28;
							?>
									<div id="text"><?=$mns."&nbsp;mins"?>
									<?php if($mns > 180) {$mns = 180;} ?>
																<?php } ?>
						</td>
                        <td class="forHighestOnly">
							<?php 
							$hrs = substr($sessionDetails["duration"],0,2);
							$mns = substr($sessionDetails["duration"],3,2);
							if($hrs == '01')
									$mns = $mns+60;
							elseif($hrs == '02')
									$mns = $mns+120;
							elseif($hrs >= '03')
									$mns = $mns+180;

							if($mns == '' || $mns == 0)
								$mns = 00;

							if($mns <= 2)
								$mntmssg = 'Min';
							else
								$mntmssg = 'Mins';

							if($mns < 60) { ?>
								<div id="text" style="margin-left:<?=substr($sessionDetails["duration"],3,2)?>px">Duration &nbsp;&nbsp;<?=$mns."&nbsp;".$mntmssg?><div id="pointer" style="margin-left:<?=21+substr($sessionDetails["duration"],3,2)*2.0?>px"></div></div><div id="numberLine"></div>

							<?php } else {
										if($mns > 180)
											$pixel = 38;
										else
											$pixel = 28;
							?>
									<div id="text" style="margin-left:<?=  $pixel ?>px">Duration &nbsp;&nbsp;<?=$mns."&nbsp;Mins"?>
									<?php if($mns > 180) {$mns = 180;} ?>
									<div id="pointer" style="margin-left:<?=  $mns-5?>px"></div></div><div id="numbergap"></div>
							<?php } ?>
						</td>
                        <td <?php if($theme==2) echo 'width="35%"';else echo 'width="30%"';?>class="blue"><?=$sessionDetails["allDetails"]?></td>
                    </tr>
                <?php } ?>
                	<tr id="totalTime">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="black" align="right" style="font-size:16px">Total Time (hh:mm)</td>
                        <td class="black" align="left" style="font-size:16px"><?=$totalDuration?></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
	</div>
<input type="hidden" name="mode" id="mode" value="<?=$mode?>">
<input type="hidden" name="sessionID" id="sessionID">
<input type="hidden" name="reportDate" id="reportDate">
</form>

<?php include("footer.php"); ?>

<?php
function getSessionReport($userID)
{

	$wildcardArray = array();
	$sq	=	"SELECT sessionID,COUNT(srno) FROM adepts_researchQuesAttempt
             WHERE userID=".$userID." AND questionType IN ('normal','research') GROUP BY sessionID ORDER BY lastModified desc limit 30";             
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		// array_push($wildcardArray,$rw[0]);
		$wildcardArray[$rw[0]] = $rw[1];
	}

	$practiseModuleQuestions = array();
	$sq	=	"SELECT sessionID, count(*) FROM practiseModulesQuestionAttemptDetails
             WHERE userID=".$userID." GROUP BY sessionID ORDER BY lastModified desc limit 30";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$practiseModuleQuestions[$rw[0]]=$rw[1];
	}
	
	$arrSessionDetails	=	array();
	$sq = "SELECT ss.sessionID,date_format(ss.startTime,'%d-%b-%Y %H:%i') starttime,date_format(ss.endTime,'%d-%b-%Y %H:%i') endtime,date_format(ss.tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(ss.endTime>ifnull(ss.tmLastQues,0),ss.endTime,ss.tmLastQues),ss.startTime),'%H:%i:%s') duration, ss.totalQ,ss.totalTopRevQ";
	if(SUBJECTNO==2)
		$sq .= ",ss.totalCQ,ss.totalTmTst,ss.totalGms,ss.totalMonRevQ";
	$sq .= " ,(Select count(*) from ". TBL_REVISION_SESSION ." rsd where rsd.sessionID = ss.sessionID) as quesCount FROM ".TBL_SESSION_STATUS." ss LEFT JOIN ". TBL_REVISION_SESSION ." a_rsd on ss.sessionId = a_rsd.sessionId WHERE ss.userID=".$userID." group by ss.sessionId ORDER BY ss.sessionID DESC limit 30";
	$rs	=	mysql_query($sq);
	$totalMinutes = 0;
	$totalSeconds = 0;
	while($rw=mysql_fetch_array($rs))
	{
		$totalQ = 0;
		$duration = $rw['duration'];
		if(trim($duration) != "")
		{
			$tempArray = explode(":",$duration);
			$totalMinutes += $tempArray[0];
			$totalSeconds += $tempArray[1];
		}
		$arrSessionDetails[$rw[0]]["starttime"]	=	$rw['starttime'];
		if($rw['endtime'] > $rw['tmLastQues'])
			$arrSessionDetails[$rw[0]]["endtime"]	=	$rw['endtime'];
		else
			$arrSessionDetails[$rw[0]]["endtime"]	=	$rw['tmLastQues'];
		$arrSessionDetails[$rw[0]]["duration"]	=	$duration;
		
		$sqComprehensive	=	"SELECT COUNT(srno) FROM adepts_researchQuesAttempt
				 WHERE userID=".$userID." AND sessionID=".$rw['sessionID']." AND questionType='comprehensive'";				 
		$rsComprehensive	=	mysql_query($sqComprehensive);
		$rwComprehensive =	mysql_fetch_array($rsComprehensive);
		
		$sqTotalQuesAttempt = "SELECT COUNT(srno) FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE sessionID=".$rw['sessionID'];
		$rsTotalQuesAttempt = mysql_query($sqTotalQuesAttempt);
		$rwTotalQuesAttempt = mysql_fetch_array($rsTotalQuesAttempt);
		$totalQ = $rwTotalQuesAttempt[0];
		$str="";					
		if(array_key_exists($rw['sessionID'],$wildcardArray))
		{
			if($wildcardArray[$rw['sessionID']]>1)
				$str .= "<span data-i18n='".$wildcardArray[$rw['sessionID']]." Wild card Questions'></span>";
			else
				$str .= "<span data-i18n='".$wildcardArray[$rw['sessionID']]." Wild card Question'></span>";
		}
		$thisSessionPractise=0;
		if(array_key_exists($rw['sessionID'], $practiseModuleQuestions)){
			if ($practiseModuleQuestions[$rw['sessionID']]>1) $q='questions';
			else $q='question';
			if ($str!="") $str.=', ';
			$str .= "<span data-i18n='".$practiseModuleQuestions[$rw['sessionID']]." Practise $q'></span>";
			$thisSessionPractise=$practiseModuleQuestions[$rw['sessionID']];
		}		
		$totalQ += $rwComprehensive[0];
		if($totalQ>0 && $totalQ!="")
		{
			if ($str!="") $str.=', ';
			if($totalQ > 1)
				$str .= $totalQ." <span data-i18n='sessionWiseReportPage.quesPlural'></span>";
			else
				$str .= $totalQ." <span data-i18n='sessionWiseReportPage.quesSingular'></span>";
		}
		
		
		
		if(SUBJECTNO==2)
		{
			if($rw['quesCount']>0)
			{
				if($str != "")
				{
					$str .= ", ".$rw['quesCount'];
					if($rw['quesCount'] > 1)
						$str .= " Revision questions";
					else
						$str .= " Revision question";
				}
				else
				{
					$str = $rw['quesCount'];
					if($rw['quesCount'] > 1)
						$str .= " Revision questions";
					else
						$str .= " Revision question";
				}
			}
			if($rw['totalCQ']>0)
			{
				if($str !="")
				{
					$str .= ", ";
				}
					if($rw['totalCQ'] > 1)
						$str .= $rw['totalCQ']." <span data-i18n='sessionWiseReportPage.challengePlural'></span>";
					else
						$str .= $rw['totalCQ']." <span data-i18n='sessionWiseReportPage.challengeSingular'></span>";
			}
			if($rw['totalTmTst']>0)
			{
				if($str !="")
				{
					$str .= ", ";
				}
					$tmTestDesc = "";
					$tmTestDesc_result = mysql_query("SELECT description FROM adepts_timedTestMaster a,adepts_timedTestDetails b WHERE a.timedTestCode=b.timedTestCode AND sessionID=".$rw['sessionID']) or die(mysql_error());
					while ($tmTestDesc_line = mysql_fetch_array($tmTestDesc_result)) {
						$tmTestDesc .= $tmTestDesc_line['description'].", ";
					}
					$tmTestDesc = substr($tmTestDesc,0,-2);
					$tmTestDesc= mysql_escape_string($tmTestDesc); 
					$tmTestDesc=htmlentities($tmTestDesc); 
					$tmTestDesc=stripslashes($tmTestDesc);
					if($rw['totalTmTst'] > 1)
						$str .= '<b title="'.$tmTestDesc.'">'.$rw['totalTmTst'].'</b> <span data-i18n="sessionWiseReportPage.timedTestPlural"></span>';
					else
						$str .= '<b title="'.$tmTestDesc.'">'.$rw['totalTmTst'].'</b> <span data-i18n="sessionWiseReportPage.timedTestSingular"></span>';
			}
			if($rw['totalGms']>0)
			{
				if($str !="")
				{
					$str .= ", ";
				}
					$gameDesc = "";
					$gameDesc_result = mysql_query("SELECT DISTINCT gameDesc FROM adepts_gamesMaster a,adepts_userGameDetails b WHERE a.gameID=b.gameID AND sessionID=".$rw['sessionID']) or die(mysql_error());
					while($gameDesc_line = mysql_fetch_array($gameDesc_result))
					{
						$gameDesc .= $gameDesc_line['gameDesc'].", ";
					}
					$gameDesc = substr($gameDesc,0,-2);
					$gameDesc= mysql_escape_string($gameDesc); 
					$gameDesc=htmlentities($gameDesc); 
					$gameDesc=stripslashes($gameDesc);
					if($rw['totalGms'] > 1)
						$str .= '<b title="'.$gameDesc.'">'.$rw['totalGms'].'</b> <span data-i18n="sessionWiseReportPage.gamesPlural"></span>';
					else
						$str .= '<b title="'.$gameDesc.'">'.$rw['totalGms'].'</b> <span data-i18n="sessionWiseReportPage.gameSingular"></span>';
			}
			if($rw['totalMonRevQ']>0)
			{
				if($str !="")
				{
					$str .= ", ";
				}
					if($rw['totalMonRevQ'] > 1)
						$str .= $rw['totalMonRevQ']." <span data-i18n='sessionWiseReportPage.revQuesPlural'></span>";
					else
						$str .= $rw['totalMonRevQ']." <span data-i18n='sessionWiseReportPage.revQuesSingular'></span>";
			}
		}
		if($rw['totalTopRevQ']-$thisSessionPractise>0)
		{
			if($str !="")
				{
					$str .= ", ";
				}
				if($rw['totalTopRevQ'] > 1)
					$str .= ($rw['totalTopRevQ']-$thisSessionPractise)." <span data-i18n='sessionWiseReportPage.revTopicPlural'></span>";
				else
					$str .= ($rw['totalTopRevQ']-$thisSessionPractise)." <span data-i18n='sessionWiseReportPage.revTopicSingular'></span>";
		}
		$strD="";
		$sqDignostict	=	"SELECT COUNT(*) as totalDignosticQ FROM adepts_diagnosticQuestionAttempt WHERE sessionID=".$rw['sessionID'];		
		$rsDignostict	=	mysql_query($sqDignostict);
		$rwDignostict=mysql_fetch_array($rsDignostict);
		
		if($rwDignostict['totalDignosticQ']>1)
			$strD = "".$rwDignostict['totalDignosticQ']." <span>Diagnostic test questions</span>";
		else if($rwDignostict['totalDignosticQ']==1)
			$strD = "".$rwDignostict['totalDignosticQ']." <span>Diagnostic test question</span>";
		if($str=="")
			$str = $strD;
		else if ($strD=="")
			$str = $str;
		else
			$str = $str.", ".$strD;
		
		$arrSessionDetails[$rw[0]]["allDetails"]	=	$str;
	}
	$totalMinutes += floor($totalSeconds/60);
	$totalSeconds = $totalSeconds%60;
	
	$totalSeconds = str_pad($totalSeconds, 2, '0', STR_PAD_LEFT);
	$totalMinutes = str_pad($totalMinutes, 2, '0', STR_PAD_LEFT);
	
	$totalDuration = $totalMinutes.":".$totalSeconds;
	$totalHours = 0;
	if($totalMinutes > 60)
	{
		$totalHours += floor($totalMinutes/60);
		$totalMinutes = $totalMinutes%60;
	
		$totalSeconds = str_pad($totalSeconds, 2, '0', STR_PAD_LEFT);
		$totalMinutes = str_pad($totalMinutes, 2, '0', STR_PAD_LEFT);
		$totalHours = str_pad($totalHours, 2, '0', STR_PAD_LEFT);
		
		$totalDuration = $totalHours.":".$totalMinutes.":".$totalSeconds;
			}
	$arrSessionDetails[]	=	$totalDuration;
	return $arrSessionDetails;
}
?>