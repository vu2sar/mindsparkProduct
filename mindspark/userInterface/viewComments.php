<?php
error_reporting(E_ERROR);
@include("check1.php");
include_once("constants.php");
include_once("functions/orig2htm.php");
include_once("functions/functionsForDynamicQues.php");
include("classes/clsQuestion.php");
include("classes/clsResearchQuestion.php");
include("classes/eipaging.cls.php");
include("classes/clsUser.php");
include("../login/loginFunctions.php");
$clspaging = new clspaging('alltasklist');
$clspaging->setgetvars();
$clspaging->setpostvars();
$basedir = "http://www.educationalinitiatives.com/mindspark/explanation_images/";
if($_SESSION['userID']=="")
{
	echo "<script>window.location='logout.php'</script>";
	exit();
}
$userID      = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$sparkieImage = $_SESSION['sparkieImage'];
$keys=array_keys($_REQUEST);
foreach($keys as $key)
{
	${$key}=$_REQUEST[$key];
}
if (isset($continue) && $continue=="Continue")
{ 
	if($srnoString!="")
	{
		$update_query = "UPDATE adepts_userComments SET viewed=1 WHERE srno IN ($srnoString)";
		$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
	}
        echo "<script>setTryingToUnload();</script>";
        login(TRUE);//Internally redirected
	exit();
}
if (isset($submit) && $submit=="Submit")
{	
	
	if($studentView=='')
	{
		$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=1 ";
		
		if($rating!="")
		$update_query.=",rating=$rating ";
				
		$update_query.= "WHERE srno=$srno";
		
		$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
	}
	else if($studentView=='no')
	{
		if(trim($recomment)!="")
		{
			$recomment = str_replace('~','-',$recomment);
			$sq	=	"INSERT INTO adepts_userCommentDetails SET srno=$srno,comment='".mysql_real_escape_string($recomment)."',commentDate=NOW(),commenter='".$_SESSION['userID']."',flag=3,userID=".$_SESSION["userID"];
			if($_SESSION["schoolCode"]!="")
				$sq	.= ", schoolCode=".$_SESSION["schoolCode"];//echo $sq;
			$rs	=	mysql_query($sq);
			if($rs)
			{	
				$update_query = "UPDATE adepts_userComments SET viewed=1,status='Re-Open',satisfy=2 ";
				
				if($rating!="")
				$update_query.=",rating=$rating ";
				
				$update_query.= "WHERE srno=$srno";
				//echo $update_query;
				$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
			}
		}
		else if($commentCount==4)
		{
			$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=2 ";
			
			if($rating!="")
			$update_query.=",rating=$rating ";
				
			$update_query.= "WHERE srno=$srno";
			
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
	}
	else
	{
		$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=0 ";
		
		if($rating!="")
		$update_query.=",rating=$rating ";
		
		$update_query.="WHERE srno=$srno";
		$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		
	}
	
	
}
?>

<?php include("header.php") ?>

<title>Comment Response</title>
<?php
	 if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/viewComments/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
	<link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/viewComments/midClass.css" />
<?php } else if($theme==3) { ?>
    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
    <link href="css/viewComments/higherClass.css" rel="stylesheet" type="text/css">
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
</script>
<script>
    function load() {
	 init();
	<?php if($theme==1) { ?>	
		var a= window.innerHeight - (200);
		$('#dataTableDiv').css("height",a+"px");
		$(".forHigherOnly").remove();
	<?php } else if($theme==2){ ?>
		var a= window.innerHeight - (80 + 20 + 140 );
		$('#dataTableDiv').css("height",a+"px");
		$(".forLowerOnly").remove();
	<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#commentDivMain').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
		if(androidVersionCheck==1){
			$('#dataTableDiv').css("height","auto");
			$('#commentDivMain').css("height","auto");
			$('#main_bar').css("height",$('#commentDivMain').css("height"));
			$('#menu_bar').css("height",$('#commentDivMain').css("height"));
			$('#sideBar').css("height",$('#commentDivMain').css("height"));
		}
	}
	function goBack()
	{
		setTryingToUnload();
		window.location = 'home.php';
		//history.go(-1);
	}
	function redirect()
	{
		setTryingToUnload();
		window.location = 'whatsNew.php';
		//history.go(-1);
	}
	function init()
	{
		setTimeout("logoff()", 600000);	//log off if idle for 10 mins
	}
	var studentView=10;
	$(document).ready(function(e) {
		if (window.location.href.indexOf("localhost") > -1) {	
		    var langType = 'en-us';
		}
		i18n.init({ lng: langType,useCookie: false }, function(t) {
			$(".translation").i18n();
			$(document).attr("title",i18n.t("commentsPage.title"));
		});
		$(".studentView").change(function(){
			var studentViewId = $(this).attr('id');
			var studentViewIdArr	=	studentViewId.split("_");
			if($("#commentCount_"+studentViewIdArr[1]).val()!=4)
			{
				if($(this).val()=='no')
				{
					$(this).next().show();
					$("#rateMe_"+studentViewIdArr[1]).css("display","none");
					studentView=0;
				}else if($(this).val()=='')
				{
					$("#rateMe_"+studentViewIdArr[1]).css("display","none");
					$(this).next().hide();
					studentView=10;
				}
				else
				{
					$(this).next().hide();
					$("#rateMe_"+studentViewIdArr[1]).css("display","block");
					studentView=1;
				}
			}
		});
	});
	function getHome()
	{
		setTryingToUnload();
		window.location.href	=	"home.php";
	}
	function logoff()
	{
		setTryingToUnload();
		window.location="logout.php";
	}
	var click=0;
	function openMainBar(){
	
		if(click==0){
			if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
			$("#vertical").css("display","none");
			click=1;
		}
		else if(click==1){
			$("#main_bar").animate({'width':'26px'},600);
			$("#plus").animate({'margin-left':'7px'},600);
			$("#vertical").css("display","block");
			click=0;
		}
	}
	
	function showMessage10(){
		alert("Kindly rate/reply to the response(s) as soon as possible, they will get closed automatically within few days.");
		return true;
	}

	/*function showMessage11(){
		alert("Your comment has been responded. Please check and respond immediately as the comment will be closed in 10 days form now.");
		return true;
	}*/
	
	function validate(varr,childClass)
	{
		if(studentView==10 && $("#commentCount_"+varr).val()==2){
			alert("Please specify whether you are satisfied with the comment or not?");
			return false;
		}
		if($("#recomment"+varr).val() == "" && !($(".notSatisfy"+varr).css('display') == 'none'))
		{
			alert("Please specify the comment");
			$("#recomment"+varr).focus();
			return false;
			
		}
		setTryingToUnload();
	}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
	<div id="top_bar">
		<div class="logo">
		</div>
        
        <div id="studentInfoLowerClass" class="forLowerOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$Name?>&nbsp;</span></a></li>
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
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
        <div id="logout" onClick="logoff()" class="linkPointer hidden">
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
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <span data-i18n="commentsPage.viewComments"></span></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                	<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                    <div id="homeText"><span data-i18n="dashboardPage.home" class="linkPointer" onClick="getHome()"></span> > <font color="#606062"> <span data-i18n="commentsPage.responseInfo"></span></font></div>
				</div>
<?php if (!(isset($from) && $from=='links'))
			{
				?>
                <div id="commentInfo" data-i18n="commentsPage.CommentsInfo"></div>
<?php } ?>                
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
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">notifications</span></div>
                </div>
				<div class="arrow-right"></div>
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div>-->
		</div>
        <div id="commentDivMain">
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="NA">
					<div id="naM" class="pointed1">
					</div></br>
					COMMENT
				</div>
				<div id="A" onClick="redirect()">
					<div id="aM" class="pointed2">
					</div></br>
					WHATS NEW
				</div>
			</div>
			</div>
<?php if (!(isset($from) && $from=='links'))
		{
			?>        
		<div id="commentInfo" class="forLowerOnly" data-i18n="commentsPage.CommentsInfo"></div>
<?php } ?>        
        <div id="dataTableDiv">
<?php
		$srno_string = "";
	    $comment_query = "SELECT *, DATE_FORMAT(DATE(commentReceivedate), '%d-%m-%Y') as comment_date, viewed, category, rating FROM adepts_userComments WHERE commentBy='Student' and userID=".$_SESSION['userID']." AND status='Closed'";
		if (!(isset($from) && $from=='links'))
		{
			$comment_query .= "AND viewed=0";
		}
		$comment_result = mysql_query($comment_query) or die("<br>Error in comment query - ".mysql_error());
		if (!(isset($from) && $from=='links'))
		{
			if(mysql_num_rows($comment_result)==0)
                        {
                            echo "<script>setTryingToUnload();</script>";
                            login(TRUE);//Internally redirected
                           exit();
                        }
//				echo "<script language='JavaScript'>setTryingToUnload();window.location='controller.php?mode=login'</script>";
		}
		$clspaging->numofrecs = mysql_num_rows($comment_result);
		if($clspaging->numofrecs>0)
		{
			$clspaging->getcurrpagevardb();
		}
		if (mysql_num_rows($comment_result)==0)
		{
			echo "<center><h3>No comments found<h3></center>";
			$clspaging->numofrecs=0;
		}
		if($clspaging->numofpages > 1)
		{ ?>
			<table width="100%" align="right">
				<tr>
					<td align="left">
						<?php
							$clspaging->writeHTMLpagesrange($_SERVER['PHP_SELF']."?from=links",FALSE,"https://www.mindspark.in/mindspark/");
						?>
					</td>
				</tr>
			</table>
		<? }
	?>        
            <table width="90%" border="0" class="endSessionTbl" align="center">
                <tr class="trHead">
                    <td width="6.5%" data-i18n="common.srno"></td>
                    <td data-i18n="commentsPage.CommentsComments"></td>
                </tr>
                <tr><td colspan="2" class="yellowBackground forLowerOnly"></td></tr>
                <tr><td colspan="2" class="forLowerOnly"></td></tr>
					<?
                    $srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
                    $comment_query .= " ORDER BY srno DESC ".$clspaging->limit;
                    // echo "<br>Query is - ".$comment_query;
                    $comment_result = mysql_query($comment_query) or die("<br>Error in query - ".mysql_error());
                    while ($comment_data = mysql_fetch_array($comment_result))
                    {
                        $comment_srno	=	$comment_data['srno'];
                        $commentTrail	=	getCommentTrail($comment_srno,$_SESSION['userID'],$childClass,$_SESSION['childName']);
						$reopenCriteria = getReopenCriteria($comment_srno,$_SESSION['userID'],$childClass,$_SESSION['childName']);
						$getDaysRemaining = getDaysRemaining($comment_srno);
                        if ($comment_data['notRelatedToQuestion']==1)
                        {
                            $question = "&nbsp;";
                        }
                        else if ($comment_data['previousQuestion']==1)
                        {
                            if(count($commentTrail)==0)
                            {
                                if($childClass>=1 && $childClass<=10)
                                {																
                                    $prev_qcode_query = "SELECT qcode FROM adepts_teacherTopicQuesAttempt_class$childClass WHERE userID=".$comment_data['userID']." AND sessionID=".$comment_data['sessionID']." AND questionNo=".($comment_data['questionNo']-1);
                                    $prev_qcode_result = mysql_query($prev_qcode_query) or die("<br>Error in previous question query - ".mysql_error());
                                    $prev_qcode_data = mysql_fetch_array($prev_qcode_result);
                                    $question = getQuestion($prev_qcode_data['qcode']);
                                }
                            }
                            else
                            {
                                $question = getQuestion($comment_data['qcode']);
                            }
                        }
                        else
                        {
                        	if(strpos($comment_data['type'], 'research') !== false)
                        		$isResearchQuestion = 1;
                        	else
                        		$isResearchQuestion = 0;
	                        $question = getQuestion($comment_data['qcode'],$comment_srno,$isResearchQuestion);
                        }
                        echo "<tr>";
                        echo "<td valign='top' class='srnoText'>$srno</td>";
                        echo "<td class='commentText'><div class='quesText'>$question</div>";
                        if(count($commentTrail)==0)
                            echo '<div class="comment">'.$comment_data['comment'].'</div>';
                        else
                        {
                            echo '<div class="comment">'.$firstName." ".$commentTrail[1].'</div>';
                            if(count($commentTrail)>1)
                            {
                            	for($ind=2; $ind<=count($commentTrail); $ind++)
                            	{
                            		if($commentTrail[$ind] != "")
		                                echo '<div class="comment">'.$commentTrail[$ind].'</div>';
                            	}
                                if(count($commentTrail) == 2 && is_null($comment_data['rating']))
                                {	
                                    if($childClass<8)
                                        $replyTo	=	"Sparkie";
                                    else
                                        $replyTo	=	"Mindspark";
									echo '<form id="frmMain'.$comment_data['srno'].'" name="frmMain'.$comment_data['srno'].'" method="post" action="">';
									if(is_null($comment_data['rating']) && $reopenCriteria==0){
									echo '<div style="text-align:left;margin-top:10px;font-weight:bold;">Kindly rate/reply to this response within <span style="font-size:1.2em;">'.$getDaysRemaining.'</span> days. It will automatically get closed after that.</div>';
                                    echo '<div class="reComment">';						
                                    echo "Was your problem solved? ";
									if(count($commentTrail) == 2)
									{
										echo '<select id="studentView_'.$comment_data['srno'].'" class="studentView" name="studentView"><option value="">Select</option><option value="yes">Yes</option><option value="no">No</option></select><div class="notSatisfy'.$comment_data['srno'].'" style="display:none"><br>Reply to '.$replyTo.': <textarea cols="50" name="recomment" id="recomment'.$comment_data['srno'].'"></textarea></div><br>';
									}
                                    

									if(count($commentTrail) == 2)
									{
										/*if(count($commentTrail)==4)
										{*/
											echo'<div id="rateMe_'.$comment_data['srno'].'" style="display:none">									
											<label>Please rate the Quality of response:</label><br>
											<label>(Not Useful)&nbsp;&nbsp;</label>
										    <input type="radio" name="rating" value="1" title="(1/5)">
										    <label>1</label>
										    <input type="radio" name="rating" value="2" title="(2/5)">
										    <label>2</label>
										    <input type="radio" name="rating" value="3" title="(3/5)">
										    <label>3</label>
										    <input type="radio" name="rating" value="4" title="(4/5)">
										    <label>4</label>
										    <input type="radio" name="rating" value="5" title="(5/5)">
											<label>5</label>
											<label>&nbsp;&nbsp;(Quite Useful)</label>
											</div>';
										/*}
										else if(count($commentTrail)==2)
										{
											echo'<div id="rateMe_'.$comment_data['srno'].'" style="display:none">									
											<label>Please rate the Quality of response:</label><br>
											<label>(Not Useful)&nbsp;&nbsp;</label>
										    <input type="radio" name="rating" value="1" title="(1/5)">
										    <label>1</label>
										    <input type="radio" name="rating" value="2" title="(2/5)">
										    <label>2</label>
										    <input type="radio" name="rating" value="3" title="(3/5)">
										    <label>3</label>
										    <input type="radio" name="rating" value="4" title="(4/5)">
										    <label>4</label>
										    <input type="radio" name="rating" value="5" title="(5/5)">
											<label>5</label>
											<label>&nbsp;&nbsp;(Quite Useful)</label>
											</div>';
										}
		*/
									}
									echo'<input type="submit" name="submit" value="Submit" class="button submitBtn" onclick="return validate('.$comment_data['srno'].','.$childClass.');"><input type="hidden" name="srno" value="'.$comment_data['srno'].'"><input type="hidden" name="commentCount" id="commentCount_'.$comment_data['srno'].'" value="'.count($commentTrail).'"></div></form>';
								}
                            }
                            else
                            {
                                $srno_string .= $comment_data['srno'].",";
                            }
                        }
							/*echo'<div class="reComment">' ;
					echo"Rate the response :";
					echo"</div>";*/					
					
                        }
                        if(count($commentTrail)==0)
                            echo "<br>".$comment_data['comment_date'].": ".$comment_data['finalcomment'];
                        if ($comment_data['image']!="")
                        {
                            $imgName = $comment_data['image'];
                            $tempArray = explode(".",$imgName);
                            $extension = $tempArray[count($tempArray)-1];
                            if($extension!="swf")
                                echo "<br/><img src=\"".$basedir.$imgName."\" align=\"middle\">";
                            else
                            {
                                $imagedetails = @getimagesize($basedir."/".$imgName);
                                $width = $imagedetails[0];
                                $height = $imagedetails[1];
                                echo "<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
                                HEIGHT='$height' WIDTH='$width'>
                                <PARAM NAME=movie VALUE='".$basedir."/".$imgName."'>
                                <PARAM NAME=quality VALUE=high>
                                <PARAM name='wmode' VALUE='transparent'>
                                <PARAM name='menu' VALUE='false'>
                                <EMBED src='".$basedir."/".$imgName."'
                                quality=high
                                menu='false'
                                TYPE='application/x-shockwave-flash'
                                PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
                                WMODE='Transparent'
                                HEIGHT='$height' WIDTH='$width'
                                LOOP=true>
                                </EMBED>
                                </OBJECT>";
                            }
                        }
                        echo "</td>";
                        echo "</tr>";
                        if(count($commentTrail)==0)
                            $srno_string .= $comment_data['srno'].",";
                        $srno = $srno + 1;
                    }
                    $srno_string = substr($srno_string, 0, -1);
                
				
				?>
				
					
			
                    </td>
                </tr>
                <tr><td colspan="2" class="bottom"></td></tr>

                <form name="frmComment" method="post" action="viewComments.php">

                <tr><td colspan="2" align="center"><?php
                    if (!(isset($from) && $from=='links')){
						if($reopenCriteria==0){
							echo "<input type='submit' name='continue' id='continueBtn' onclick='setTryingToUnload();showMessage10();' class='button' value='Continue'>";
						}else{
							echo "<input type='submit' name='continue' id='continueBtn' onclick='setTryingToUnload();' class='button' value='Continue'>";
						}
					}
					else{
						if($theme==2){
							echo "<div id='backBtn' class='button' onclick='setTryingToUnload();goBack();' style='color:black !important;'>Back</div>";
						}else{
							echo "<div id='backBtn' class='button' onclick='setTryingToUnload();goBack();'>Back</div>";
						}
					}
						
                ?> </td></tr>
				<input type="hidden" name="srnoString" value="<?=$srno_string?>">
				</form>
            </table>
        </div>
        
        </div>
	</div>
    
<?php include("footer.php") ?>

<?php
function getQuestion($qcode, $commentSrNo="", $isResearchQuestion=0)
{
	if($isResearchQuestion == 1)
		$objQuestion = new researchQuestion($qcode);
	else	
    	$objQuestion = new Question($qcode);
	$objQuestion->isDynamic();
	if($objQuestion->isDynamic())
	{
		if($commentSrNo != "")
		{
			$query  = "SELECT dynamicParameters FROM adepts_userComments WHERE srno=".$commentSrNo;
			$params_result = mysql_query($query);
			$params_line   = mysql_fetch_array($params_result);
			if($params_line[0] != "")
				$objQuestion->generateQuestion("answer",$params_line[0]);
			else
				$objQuestion->generateQuestion();
		}
		else
			$objQuestion->generateQuestion();
	}
    $question = $objQuestion->getQuestion();
    if($objQuestion->quesType == 'MCQ-4')
    {
    	$question .= "<div><strong>A)</strong> ".$objQuestion->getOptionA().'</div>';
		$question .= "<div><strong>B)</strong> ".$objQuestion->getOptionB().'</div>';
		$question .= "<div><strong>C)</strong> ".$objQuestion->getOptionC().'</div>';
		$question .= "<div><strong>D)</strong> ".$objQuestion->getOptionD().'</div>';
    }
    else if($objQuestion->quesType == 'MCQ-3')
    {
    	$question .= "<div><strong>A)</strong> ".$objQuestion->getOptionA().'</div>';
		$question .= "<div><strong>B)</strong> ".$objQuestion->getOptionB().'</div>';
		$question .= "<div><strong>C)</strong> ".$objQuestion->getOptionC().'</div>';
    }
    else if($objQuestion->quesType == 'MCQ-2')
    {
    	$question .= "<div><strong>A)</strong> ".$objQuestion->getOptionA().'</div>';
		$question .= "<div><strong>B)</strong> ".$objQuestion->getOptionB().'</div>';
    }

	return $question;
}
function show_thumbnail($file)
{
    $max = 350; // Max. thumbnail width and height
    $size = getimagesize($file);
    if ( $size[0] <= $max && $size[1] <= $max )
    {
        $ret = '<img src="'.$file.'" '.$size[3].' border="0">';
    }
    else
    {
        $k = ( $size[0] >= $size[1] ) ? $size[0] / $max : $size[1] / $max;
        $ret .= '<img src="'.$file.'" width="'.floor($size[0]/$k).'" height="'.floor($size[1]/$k).'" border="0" alt="View full-size image">';
    }
    return $ret;
}

function getReopenCriteria($comment_srno,$userID,$childClass,$childName){
	$arrayComments	=	array();
	$firstName	=	explode(" ",$_SESSION['childName']);
	$firstName	=	$firstName[0];
		$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%Y%m%d') as commentDate,commenter,flag
				 FROM adepts_userCommentDetails 
				 WHERE srno=$comment_srno and flag=2";
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_array($rs))
		{
			$now = time(); // or your date as well
		     $your_date = strtotime($rw[4]);
		     $datediff = $now - $your_date;
		     $datediff =  floor($datediff/(60*60*24));
			if($datediff>=20){
				$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=2 WHERE srno=$comment_srno";
				$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
}

function getDaysRemaining($comment_srno){
	$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%Y%m%d') as commentDate,commenter,flag
				 FROM adepts_userCommentDetails 
				 WHERE srno=$comment_srno and flag=2";
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_array($rs))
		{
			$now = time(); // or your date as well
		     $your_date = strtotime($rw[4]);
		     $datediff = $now - $your_date;
		     $datediff =  floor($datediff/(60*60*24));
			return 20 - $datediff;
		}else{
			return 20;
		}
}

function getCommentTrail($comment_srno,$userID,$childClass,$childName)
{
	$arrayComments	=	array();
	$firstName	=	explode(" ",$_SESSION['childName']);
	$firstName	=	$firstName[0];
	$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%M %e, %Y %h:%i %p') AS commentDate,commenter,flag
			 FROM adepts_userCommentDetails 
			 WHERE srno=$comment_srno";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$comment = explode('~',$rw[2]);
		//if(count($comment)>1){
			$date = explode('::',$comment[count($comment)-1]);
			$commentShow = stripslashes($date[0]);
			$rw[4] = isset($date[1])?$date[1]:'-';
		/*}else{
			$commentShow = stripslashes($comment[0]);
		}*/
		 
		if($rw[6]%2 != 0)
		{
			if($commentShow != "")
				$arrayComments[$rw[6]]	=	"<b>".$firstName." (".$rw[4]."): </b>".$commentShow;
			else
				$arrayComments[$rw[6]]  =   '';
		}			
		else
		{
			if($childClass<8)
				$arrayComments[$rw[6]]	=	"<b>Sparkie (".$rw[4]."): </b>".$commentShow;
			else
				$arrayComments[$rw[6]]	=	"<b>Mindspark (".$rw[4]."): </b>".$commentShow;
		}
	}
	return $arrayComments;
}
?>