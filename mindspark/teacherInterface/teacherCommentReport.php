<?php
    include("header.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("../userInterface/classes/eipaging.cls.php");		//this is path on internet
	$clspaging = new clspaging('alltasklist');
	$clspaging->setgetvars();
	$clspaging->setpostvars();
	$arrayForUpdatingViewed = array();
	

	if(!isset($_SESSION['userID']))
    {
    	header("Location:logout.php");
    	exit;
    }
    $userID      = $_SESSION['userID'];
    $schoolCode  = $_SESSION['schoolCode'];
    $category    = $_SESSION['admin'];
    $subcategory = $_SESSION['subcategory'];
    $rating="";
    $keys=array_keys($_REQUEST);
    foreach($keys as $key)
    {
    	${$key}=$_REQUEST[$key];
    }
	if(isset($_REQUEST["go"]))
	{
		$selectStatus	=	$_REQUEST['selectStatus'];
	}
	else if (isset($_REQUEST['submit']) && $_REQUEST['submit']=="Submit")
	{
		if($studentView=='')
		{
			$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=1 WHERE srno=$srno";
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
		else if($studentView=='no')
		{
			if(trim($recomment)!="")
			{	
				$query = "SELECT flag, comment from adepts_userCommentDetails where srno=$srno order by flag desc limit 1";
				$result=	mysql_query($query);
				if($data = mysql_fetch_assoc($result)){
					$tempFlag = $data['flag'];
					$tempComment = $data['comment'];
					
				}
				if(trim($recomment) !== trim($tempComment))
				{
					$recomment = str_replace('~','-',$recomment);
					$sq	=	"INSERT INTO adepts_userCommentDetails SET srno=$srno,comment='".mysql_real_escape_string($recomment)."',commentDate=NOW(),commenter='".$_SESSION['username']."',flag=($tempFlag+1),userID=".$_SESSION["userID"].",schoolCode=".$_SESSION["schoolCode"];
					$rs	=	mysql_query($sq);
					if($rs)
					{	
						$update_query = "UPDATE adepts_userComments SET viewed=1,status='Re-Open'";
						
						if($studentView=='no')
							$update_query .= ", satisfy=2";
						
						if($rating!="")
						$update_query.=", rating=$rating ";
						
						$update_query.= " WHERE srno=$srno";
						$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
					}
				}
			}
		}
		else if($studentView=='yes')
		{
			$update_query = "UPDATE adepts_userComments SET satisfy=0,viewed=1 ";
			
			if($rating!="")
			$update_query.=",rating=$rating ";
			
			$update_query.="WHERE srno=$srno";
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
		else
		{
			if($studentView=='seek')
			{
				$recomment = str_replace('~','-',$recomment);
				$sq	=	"INSERT INTO adepts_userCommentDetails SET srno=$srno,comment='$recomment',commentDate=NOW(),commenter='".$_SESSION['userID']."',flag=3,userID=".$_SESSION["userID"].", schoolCode=".$_SESSION["schoolCode"];
				$rs	=	mysql_query($sq);
				$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=5,status='Re-Open' WHERE srno=$srno";
			}
			else
			{
				$update_query = "UPDATE adepts_userComments SET viewed=1,satisfy=0 WHERE srno=$srno";
			}
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
		header("location: teacherCommentReport.php");
		exit;
	}
	else
	{
		if(isset($_REQUEST["status"]))
			$selectStatus = $_REQUEST["status"];
	}
?>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/teacherDetails.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<link rel="stylesheet" href="/resources/demos/style.css" />
  
  
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<style>
table.tblContent {
    background      :   #E6E6E6;
    padding         :   0px;
    font-size		: 	14px;
	margin-top		:   3%;
	margin-bottom		:   3%;
	font-family: 'Conv_HelveticaLTStd-Roman';
}

table.tblContent tr th, table.tblContent tr td {
    border-width    :   3px;
    border-style    :   solid;
    border-color    :   white;
    border-spacing  :   0px;
    color           :   rgb(102, 84, 84);
    font-weight     :   bold;
}

table.tblContent tr td {
    font-weight     :   normal;
}
</style>
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
$(document).ready(function(e) {
	if($("#selectStatus").val()=="")
		$(".statusColumn").show();
	else
		$(".statusColumn").hide();
	$(".studentView").change(function() {
			var userViewId = $(this).attr('id');
			var srno	=	userViewId.split("_")[1];
			if($(this).val()== '')
			{
				$("#rateMe_"+srno).css("display","none");
				$(this).next().hide();
			}
			else if($(this).val()=='no')
			{
				$(this).next().show();
				$("#rateMe_"+srno).css("display","none");
			}
			else
			{
				$(this).next().hide();
				$("#rateMe_"+srno).css("display","block");
			}
	});
});
function goBack()
{
	setTryingToUnload();
	window.location.href	=	"teacherInterface/home.php";
}

function checkdetails()
{
	var returnVar	=	true;
	$(".selectChecked").each(function() {
        if($(this).is(":checked"))
		{
			if($(this).parent("td").find("select").val()==2 && $(this).parent("td").find("textarea").val()=="")
			{
				alert("Please enter comment if you are nor satisfy with response.");
				returnVar = false;
			}
		}
    });
	if(returnVar === false)
		return false
}
function submitTeacherCommentForm(qcode, qtype, qno, comment_srno, childClass)
{
	document.getElementById("qcode").value = qcode;
	document.getElementById("type").value = qtype;
	document.getElementById("qno").value = qno;
	document.getElementById("comment_srno").value = comment_srno;
	document.getElementById("teacherComment").submit();
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
	<table id="childDetails" style="float:none;">
			<td width="33%" id="sectionRemediation" class="activatedTopic"><a href="Comments.php" style="text-decoration:none;"><div id="actTopicCircle1" class="smallCircle" style="cursor:pointer;"></div><div id="1" style="cursor:pointer;" class="pointer">Comments</div></a></td>
		        <td width="34%" id="studentRemediation" class="activateTopicAll"><div id="actTopicCircle2" class="smallCircle red" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer textRed">Error reporting</div></td>
                        <td width="33%" id="studentComments" class="activatedTopic"><a href="studentComments.php" style="text-decoration:none;"><div id="actTopicCircle3" class="smallCircle" style="cursor:pointer;"></div><div id="1" style="cursor:pointer;" class="pointer">Student Comment Summary</div></a></td>
	</table>
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span style="float: left;">Errors Reported</span>
				<a href="studentTrail.php?mode=errorReporting&last1hour=1" style="text-decoration:none;"><div id="add">
				<div id="circle">
				<div id="plushorizontal"> </div>
				<div id="plusVertical"> </div>
				 </div>
				 
				 
			</div>
			
			<span id="addComment">Report Error</span></a>
			<div style="clear:both"></div>
			</div>
			
			<div id="containerBody">

<div align="center">
	<form id="frmMain" name="frmMain" method="post">
        <strong>Status:</strong> <select id="selectStatus" name="selectStatus">
                <option value="" <?php if($selectStatus=="") echo "selected"?>>All</option>
                <option value="unresolved" <?php if($selectStatus=="unresolved") echo "selected"?>>Unresolved</option>
                <option value="responded" <?php if($selectStatus=="responded") echo "selected"?>>Responded</option>
                <option value="closed" <?php if($selectStatus=="closed") echo "selected"?>>Closed</option>
                <option value="ignored" <?php if($selectStatus=="ignored") echo "selected"?>>Acknowledged</option>
            </select>&nbsp;&nbsp;
            <input type="submit" class="button" value="Go" name="go" onClick="setTryingToUnload();"/>
	</form>
	<form id='teacherComment' name='teacherComment' method="post" target="_blank" action="teacherComment.php">
		<input type='hidden' name='mode' id='mode' value='viewer'/>
		<input type='hidden' name='type' id='type' value/>
		<input type='hidden' name='qcode' id='qcode' value/>
		<input type='hidden' name='qno' id='qno' value/>
        <input type='hidden' name='comment_srno' id='comment_srno' value/>
	</form>	
</div>
<br />
<div >
<form name="frmComment" method="post" action="teacherCommentReport.php">
	<table border="0" width="100%">
		<tr>
			<td>
				<?php
					$srno_string = "";
					$comment_query = "SELECT *, DATE_FORMAT(DATE(commentReceivedate), '%d-%m-%Y') as comment_date  FROM adepts_userComments WHERE queryType is null and userID=".$_SESSION['userID'];
					if($selectStatus=="unresolved")
					{
						$comment_query .= " AND status<>'Closed'";
					}
					else if($selectStatus=="responded")
					{
						$comment_query .= " AND status='Closed' AND viewed=0";
					}
					else if($selectStatus=="closed")
					{
						$comment_query .= " AND status='Closed' AND viewed=1";
					}
					else if($selectStatus=="ignored")
					{
						$comment_query .= " AND status='Ignored' AND viewed=1";
					}
					$comment_result = mysql_query($comment_query) or die("<br>Error in comment query - ".mysql_error());

					if (!(isset($from) && $from=='links'))
					{
						if(mysql_num_rows($comment_result)==0)
						{
							echo "<div align='center'>No comments found</div>";
							exit();
						}
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
					{?>
				        <table>
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
				<table border="0" align="center" cellpadding="10" cellspacing="0" id="tblContent" class="tblContent" width="100%">
                	<thead>
                        <tr>
                            <td width="25px" align="center"><b>Sr. No.</b></td>
                            <td align="center"><b>Comment detail</b></td>
<?php if($selectStatus=="") { ?><td align="center" width="10%"><b>Status</b></td><?php } ?>
                        </tr>
                    </thead>
					<?
						$srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
						$comment_query .= " ORDER BY srno DESC ".$clspaging->limit;
						//echo "<br>Query is - ".$comment_query;
	    				$comment_result = mysql_query($comment_query) or die("<br>Error in query - ".mysql_error());
						while ($comment_data = mysql_fetch_array($comment_result))
						{
							$dispStatus	=	"";
							if($comment_data['status']=="In-Progress" || $comment_data['status']=="Open" || $comment_data['status']=="Re-Open")
								$dispStatus	=	"Unresolved";
							else if($comment_data['status']=="Closed" && $comment_data['viewed']==1)
								$dispStatus	=	"Closed";
							else if($comment_data['status']=="Ignored" && $comment_data['viewed']==1)
								$dispStatus	=	"Acknowledged";
							else
								$dispStatus	=	"Responded";
							$comment_srno	=	$comment_data['srno'];
							$quesNo = isset($comment_data['questionNo']) && $comment_data['questionNo'] != null ? $comment_data['questionNo']:"";
							$commentTrail	=	getCommentTrail($comment_srno,$_SESSION['userID']);
							$reopenCriteria = getReopenCriteria($comment_srno,$_SESSION['userID'],$_SESSION['childName']);
							$getDaysRemaining = getDaysRemaining($comment_srno);
							if ($comment_data['notRelatedToQuestion']==1)
								$question = "<b>General comment : </b>";
							else if($comment_data["type"]=="normal" || $comment_data["type"]=="challenge" || $comment_data["type"]=="prepostTestQues" || strpos($comment_data["type"], "wildcard")!==false)
								$question = "<a target='_blank' href='javascript:void(0);' style='color:blue' onclick='tryingToUnloadPage=false;submitTeacherCommentForm(".$comment_data["qcode"].",\"".$comment_data["type"]."\",\"".$quesNo."\",".$comment_srno.");'>Question link</a>";
							else if($comment_data["type"]=="timedtest")
								$question = "<b>Timed test : </b><u><a target='_blank' href='../userInterface/timedTest.php?timedTest=".$comment_data["qcode"]."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getTimedTest($comment_data["qcode"])."</a></u>";
							else if($comment_data["type"]=="activity")
								$question = "<b>Activity : </b><u><a target='_blank' href='../userInterface/enrichmentModule.php?gameID=".$comment_data["qcode"]."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getActivity($comment_data["qcode"])."</a></u>";
							else if($comment_data["type"]=="remedial")
								$question = "<b>Remedial : </b><u><a target='_blank' href='../userInterface/remedialItem.php?qcode=".$comment_data["qcode"]."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getRemedial($comment_data["qcode"])."</a></u>";

							echo "<tr>";
								echo "<td>$srno</td>";
								echo "<td align=\"left\">$question";
								if($comment_data['viewed'] == 0 && ($comment_data['status']=="Closed" || $comment_data['status']=="Ignored") && $getDaysRemaining>0)
								{
									echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									echo "<span style='font-size:14px;background-color:rgba(212, 19, 13, 1);border-radius: 3px;padding-left: 3px;padding-right: 4px;padding-top: 2px;padding-bottom: 2px;color:white;'>New</span><br/>";
									$oneToOneComments[] = $comment_data['srno'];
								}
								else
									echo "<br/>";


								if(count($commentTrail)==0)
									echo $comment_data['comment'];
								else
								{
									echo '<hr><div class="firstComment">'.$commentTrail[1].'</div>';
								}
								//echo "</td><td align=\"left\" valign='top'>";
								if(count($commentTrail)==0)
									echo $comment_data['finalcomment'];
								else
								{
									if(strtolower($comment_data['status']) == "ignored" && count($commentTrail) ==2)
									{
										echo '<hr><b>Mindspark : </b>'.$comment_data['finalcomment'];
									}
									else
									{
										echo '<hr><div class="firstResponse">'.$commentTrail[2];
									}									
								}
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
								if(count($commentTrail)>0)
									echo '</div>';
								for($index=3; $index<=count($commentTrail); $index++)
								{
									if($commentTrail[$index] != "")
									{
										if($index == count($commentTrail))
										{
											if(strtolower($comment_data['status']) == "ignored" || strtolower($comment_data['status']) == "closed" )
											{
												echo "<hr>";
												echo '<b>Mindspark : </b>'.$comment_data['finalcomment'];
											}
											else if($index%2!=0)
											{
												echo "<hr>";
												echo $commentTrail[$index];
											}
										}
										else
										{
											echo "<hr>";
											echo $commentTrail[$index];										
										}										
									}
								}
								if((count($commentTrail)%2 == 0) && $comment_data['status']=="Closed" && is_null($comment_data['rating']) && $reopenCriteria==0 && $getDaysRemaining>0)
								{
									echo "<hr>";
									if($comment_data['satisfy']==4)
									{
										echo '<input type="hidden" id="studentView'.$comment_data['srno'].'" class="studentView" name="studentView" value="seek"><div class="notSatisfy"><br>Seek clarification : <br><textarea cols="50" name="recomment" id="recomment'.$comment_data['srno'].'" placeholder="Write your clarification here"></textarea></div><input type="submit" name="submit" value="Submit" onclick="setTryingToUnload();"><input type="hidden" id="srno" name="srno" value="'.$comment_data['srno'].'"><input type="hidden" name="commentCount" id="commentCount" value="'.count($commentTrail).'"><input type="hidden" name="satisfyFlag'.$comment_data['srno'].'" id="satisfyFlag'.$comment_data['srno'].'" value="'.$comment_data['satisfy'].'">';
									}
									else
									{
										echo "Kindly rate/reply to this response within <span style='font-size:1.2em;'>".$getDaysRemaining."</span> days. It will automatically get closed after that.";
										echo "<br><br>Was your problem solved? ";
										echo '<input type="hidden" name="srno" id="srno" value="'.$comment_data['srno'].'">
											<input type="hidden" name="satisfyFlag" id="satisfyFlag'.$comment_data['srno'].'" value="'.$comment_data['satisfy'].'">
											<input type="hidden" name="commentCount" id="commentCount" value="'.count($commentTrail).'">
											<select id="studentView_'.$comment_data['srno'].'" class="studentView" name="studentView">
												<option value="">Select</option>
												<option value="yes">Yes</option>
												<option value="no">No</option>
												</select>
												<div class="notSatisfy" style="display:none"><br>Reply to mindspark : <br><textarea cols="50" name="recomment" id="recomment'.$comment_data['srno'].'"></textarea></div>';												
										echo '<div id="rateMe_'.$comment_data['srno'].'" style="display:none;">				
											<label><br>Please rate the Quality of response:</label><br>
											<label>(Not Useful)&nbsp;&nbsp;</label>
										    <input type="radio" name="rating" value="1" title="(1/5)">
										    <label>1</label>
										    <input type="radio" name="rating" value="2" title="(2/5)">
										    <label>2</label>
										    <input type="radio" name="rating" value="3" title="(3/5)">
										    <label>3</label>
										    <input type="radio" name="rating" value="4"  title="(4/5)">
										    <label>4</label>
										    <input type="radio" name="rating" value="5" title="(5/5)">
											<label>5</label>
											<label>&nbsp;&nbsp;(Quite Useful)</label>
											</div>';
										echo '<input type="submit" name="submit" value="Submit" onclick="setTryingToUnload();">';
									}
								}

								echo "</td>";
								if($selectStatus=="") echo '<td align="center">'.$dispStatus.'</td>';
							echo "</tr>";
							$srno_string .= $comment_data['srno'].",";
							$srno = $srno + 1;
						}
						$srno_string = substr($srno_string, 0, -1);
						if(count($oneToOneComments) > 0)
						{
							$oneToOneCommentsSrNo = implode(",", $oneToOneComments);
							$update_query = "UPDATE adepts_userComments SET viewed=1 WHERE srno in (".$oneToOneCommentsSrNo.");";
							$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
						}
					?>
				</table>
			</td>
		</tr>
	</table>
    <br />
	<input type="hidden" name="srnoString" value="<?=$srno_string?>">
	</form>
</div>
			</div>
			
		
		</div>
		
		
	</div>

<?php

function getCommentTrail($comment_srno,$userID)
{
	$arrayComments	=	array();
	$firstName	=	explode(" ",$_SESSION['childName']);
	$firstName	=	$firstName[0];
	$sq	=	"SELECT a.id,a.srno,a.comment,a.image,DATE_FORMAT(a.commentDate, '%M %e, %Y %h:%i %p') as commentDate,a.commenter,a.flag,b.status
			 FROM adepts_userCommentDetails a, adepts_userComments b
			 WHERE a.srno=$comment_srno AND a.srno=b.srno";

	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$status =$rw['status'];
		$comment = explode("~",str_replace("\\","",$rw["comment"]));
		$finalcomment = str_replace("\\", "", $rw["finalcomment"]);
		$commentShow = $comment[count($comment)-1];
		$date = explode('::',$comment[count($comment)-1]);
		$commentShow =nl2br($date[0]);
		if($rw['comment'] != "")
		{
			if($rw[6]%2 != 0)
				$arrayComments[$rw[6]]	=	"<b>".$firstName." (".$rw[4]."): </b>".$commentShow;
			else
			{
				$arrayComments[$rw[6]]	=	"<b>Mindspark (".$rw[4]."): </b>".$commentShow;
			}			
		}
		else
			$arrayComments[$rw[6]] = '';
	}
	if($status == 'In-Progress' || $status == "Ignored")
	{ 
		for ($i=1; $i <count($arrayComments); $i++) { 
			$tmp[$i] = $arrayComments[$i];
		}
		if($status == "Ignored")
		{
			array_push($tmp, $finalcomment);
		}
		$arrayComments = $tmp;
	}
	return $arrayComments;
}

function getQuestion($qcode)
{
    $objQuestion = new Question($qcode);
    $question = $objQuestion->getQuestion();
	return $question;
}

function getTimedTest($qcode)
{
	$sq	=	"SELECT description FROM adepts_timedTestMaster WHERE timedTestCode='$qcode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}
function getActivity($qcode)
{
	$sq	=	"SELECT gameDesc FROM adepts_gamesMaster WHERE gameID=$qcode";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}
function getRemedial($qcode)
{
	$sq	=	"SELECT remedialItemDesc FROM adepts_remedialItemMaster WHERE remedialItemCode='$qcode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}
function getReopenCriteria($comment_srno,$userID,$childName){
	$arrayComments	=	array();
	$firstName	=	explode(" ",$childName);
	$firstName	=	$firstName[0];
		$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%Y%m%d') as commentDate,commenter,flag
				 FROM adepts_userCommentDetails 
				 WHERE srno=$comment_srno and flag % 2 =0 order by commentDate desc";
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
	$sq	=	"SELECT id,srno,comment,image,DATE_FORMAT(commentDate, '%Y%m%d') as commentDate,datediff(NOW(), commentDate),commenter,flag
				 FROM adepts_userCommentDetails 
				 WHERE srno=$comment_srno order by flag desc";
	$rs	=	mysql_query($sq);

	if($rw=mysql_fetch_array($rs))
	{
			if($rw['flag'] %2 !=0)
				return 20;
	   		else
				return 20 - $rw[5];
	}else{
		return 20;
	}
}
?>

<?php include("footer.php") ?>
