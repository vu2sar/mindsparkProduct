<?php
    include("header.php");
    include("../userInterface/functions/orig2htm.php");
    include("../userInterface/classes/clsQuestion.php");
    include("../userInterface/classes/clsResearchQuestion.php");
	include("../userInterface/functions/functionsForDynamicQues.php");
	include("../userInterface/constants.php");
	
	if(!isset($_SESSION['userID']))
    {
    	header("Location:logout.php");
    	exit;
    }
    if(empty($_REQUEST))
    {
    	header("Location:teacherCommentReport.php");
    	exit;
    }
    $userID			=	$_SESSION['userID'];
    $username		=	$_SESSION['username'];
    $schoolCode		=	$_SESSION['schoolCode'];
    $category		=	$_SESSION['admin'];
    $subcategory	=	$_SESSION['subcategory'];
	$qcode			=	isset($_REQUEST["qcode"])?$_REQUEST["qcode"]:"";
	$qno			=	isset($_REQUEST["qno"])?$_REQUEST["qno"]:"";
	$type			=	isset($_REQUEST["type"])?$_REQUEST["type"]:"";
	$sessionID		=	isset($_REQUEST["sessionID"])?$_REQUEST["sessionID"]:"";
	$qno			=	isset($_REQUEST["qno"])?$_REQUEST["qno"]:"";
	$comment_srno   =   isset($_REQUEST["comment_srno"])?$_REQUEST["comment_srno"]:"";
	$quesAttemptSrno =  isset($_REQUEST["quesAttemptSrno"])?$_REQUEST["quesAttemptSrno"]:"";
	$childClass		=	isset($_REQUEST["childClass"])?$_REQUEST["childClass"]:"";
	$mode			=	isset($_REQUEST["mode"])?$_REQUEST["mode"]:"";
	$dynamicParameters = isset($_REQUEST["dynamicParameters"])?$_REQUEST["dynamicParameters"]:"";   
	if(!is_numeric($qno))
		$qno	=	"";
	/*if (!class_exists('S3')) require_once '../s3/S3.php';
		$s3 = new S3(awsAccessKey, awsSecretKey);*/
	if(isset($_POST["quesComments"]))
	{
		$quesComments	=	str_replace('~','-',$_POST["quesComments"]);

		$imgname="";
		if($_POST["base64img"]!="")
		{
			if(substr($_POST["base64img"],0,5)=="data:")
			{
				$base64img	=	substr($_POST["base64img"],22);
				$decoded = base64_decode($base64img);
				$path	=	"errorImage/";
				$imgname	=	$userID.date("YmdHis").'.png';
				if (!is_dir('errorImage')) {
					mkdir('errorImage');
				}
				file_put_contents($path.$imgname,$decoded);
			}
			else
			{
				$imgnameArr	=	explode("/",$_POST["base64img"]);
				$imgname	=	$imgnameArr[1];
			}
			/*if($s3->putObjectFile($imgname, MSMaths_BucketName, "errorimage/", S3::ACL_PUBLIC_READ))
				unlink($path.$imgname);*/
		}
		if($type!="activity" && $type!="remedial" && $type!="timedtest")
			$owner	=	getQcodeOwner($qcode, $type);
		else if($type=="activity")
			$owner	=	getActivityOwner($qcode);
		else if($type=="remedial")
			$owner	=	getRemedialOwner($qcode);
		else if($type=="timedtest")
			$owner	=	getTimedTestOwner($qcode);
		
		$quesComments = str_replace('~','-',$_POST['quesComments']);
		$quesComments	=	mysql_real_escape_string($quesComments);
		include_once("../userInterface/classes/clsbucketCommentsV2.php");
		$obj = new commentCategorization();
		$systemCategory	=	$obj->mark($quesComments);
		
		$sq	=	"INSERT INTO adepts_userComments SET userID=$userID, comment='".$quesComments."', image='$imgname', sessionID=".$sessionID.", qcode='".$qcode."',
				 category='".$systemCategory['bucket']."', systemCategory='".$systemCategory['bucket']."', commentReceivedate=now(), commentBy='Teacher', commentSource='Teacher interface', dynamicParameters='".$dynamicParameters."'";
		if(!$_REQUEST["general"])
		{
			$sq	.=	", status='In-Progress', type='".$type."', AssignTo='$owner'";
		}
		else
		{
			$sq	.=	", status='In-Progress', type='".$type."', notRelatedToQuestion=1, AssignTo='$owner'";
		}
		if($qno!="")
			$sq	.=	", questionNo=".$qno;
		mysql_query($sq) or die(mysql_error());
		$commentID	=	mysql_insert_id();
		$query	=	"INSERT INTO adepts_userCommentDetails SET srno=$commentID, comment='".mysql_real_escape_string($quesComments)."', commentDate=now(), commenter='$username', flag=1,userID=".$_SESSION["userID"].",schoolCode=".$_SESSION["schoolCode"];
		mysql_query($query) or die(mysql_error());
		header("location:studentTrail.php?mode=errorReporting&last1hour=1");
	}
?>	

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/teacherDetails.css" rel="stylesheet" type="text/css">
<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script> -->
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> -->
  <!--<script src="http://code.jquery.com/jquery-1.9.1.js"></script>-->
  <!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <style>
  
  .question{
  	height:300px;
	padding-left: 5%;
	padding-top: 2%;
	overflow: auto;
  }
#quesNos {
	float:left;
	width:5%;
	background-color:#E7B755;
	border-radius:10px;
	min-width: 50px;
}
#quesText {
	
	<?php if($mode=="viewer") echo "width:100%;"; else echo "width:100%;";  ?>	
	height:350px;
	/*float:left;*/
	border:1px #2f99cb solid;
	border-radius:10px;
	padding:10px;
	overflow: auto;
    scrollbar-base-color:#ffeaff;
	margin-top: 5%;
	margin-bottom: 5%;
	background-color: #F0F0F0;
}
#mainDiv {
	width:90%;
	min-height:500px;
}
#commentText {
	border:1px #2f99cb solid;
	border-radius:2px;
	width:100%;
	min-height:200px;
	
	float:left;
	margin-bottom: 2%;
}
#commentTextDiv {
	width:98%;
	height:200px;
	overflow:scroll;
	background-color:#FFFFFF;
	text-align:left;
	cursor:text;
	padding:7px;
}
.qnos {
	background: url(images/buttons/question/numberButton.png) no-repeat -3px -74px;
	width: 49px;
	height: 51px;
	font-weight:bold;
	font-size:14px;
	padding-top:15px;
	cursor:pointer;
	opacity:0.3;
}
.qnos:hover {
	background-position:-2px -1px;
	width: 50px;
	height: 50px;
	opacity:1;
}
.qnoSelected {
	background-position:0 -142px;
	width: 55px;
	height: 55px;
	opacity:1;	
}
.qnoSelected:hover {
	background-position:0 -142px;
	width: 55px;
	height: 55px;
	opacity:1;	
}

.buttons{
	
	background-color: #2f99cb;
	width: 15%;
	height: 40px;
	color: #FFFFFF;
	font-size: 15px;
	margin-bottom: 3%;
	
}
#btnDiv {
	
	
}
</style>
  <script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
  });
  </script>
  <script>
$(document).ready(function(e) {

	/*if ($.browser.msie) {
		$("#uploadImage").show();
	} else {
		$("#uploadImage").hide();
	}*/
	
	$("#commentTextDiv").focus(function(){
		if($("#commentTextDiv").html()=="[Write your comment here]")
			$("#commentTextDiv").html("");
	});
});

window.addEventListener("paste", pasteHandler);
function pasteHandler(e) {
   if (e.clipboardData) {
      var items = e.clipboardData.items;
      if (items) {
         for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf("image") !== -1) {
               var base64 = items[i].getAsFile();
			   
			    var reader = new FileReader();
                reader.onload = function(event){
                    createImage(event.target.result);
                };
               reader.readAsDataURL(base64);
               createImage(base64);
            }
         }
      }
   }
}

function createImage(source) {
   var pastedImage = new Image();
   pastedImage.onload = function() {
      // You now have the image!
   }
   pastedImage.src = source;
   if(source.substring(0,5)=="data:")
   {
		if($("#commentTextDiv").html()=="[Write your comment here]")
			$("#commentTextDiv").html("");
		if($("#commentTextDiv").find("img").attr("src"))
			alert("Paste only one image at a time.");
		else
			$("#commentTextDiv").append("<img src='"+pastedImage.src+"' width='90%'>");
   }
}
function popup()
{
	if($("#commentTextDiv").html()=="[Write your comment here]")
		$("#commentTextDiv").html("");
	var childWindow = window.open('errorImageUpload.php','errorImageUpload','width=500,height=500');
	return false;
}
function GetValueFromChild(myVal)
{
	if($("#commentTextDiv").find("img").attr("src"))
		alert("Paste only one image at a time.");
	else
		$("#commentTextDiv").append("<img src='"+myVal+"' width='90%'>");
}
function submitForm(){
	setTryingToUnload();
	if($.trim($("#commentTextDiv").html())!="" && $("#commentTextDiv").html()!="[Write your comment here]")
		{
			$("#commentTextDiv").find("img:eq(0)").before("|--image--|");
			if($("#commentTextDiv").find("img").attr("src"))
			{
				$("#base64img").val($("#commentTextDiv").find("img").attr("src"));
				$("#commentTextDiv").find("img").remove();
			}
			$("#quesComments").val($.trim($("#commentTextDiv").html()));
			$("#frmMain").submit();
		}
		else
		{
			alert("Please enter comments.");
			return false;
		}
	
}
function goBack(){
	setTryingToUnload();
	history.go(-1);
}
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
</head>
<body class="translation" onLoad="load()" onResize="load()" >
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
		        <td width="34%" id="studentRemediation" class="activateTopicAll"><a href="teacherCommentReport.php" style="text-decoration:none;"><div id="actTopicCircle2" class="smallCircle" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer">Error reporting</div></a></td>
                        <td width="33%" id="studentRemediation" class="activateTopicAll"><a href="studentComments.php" style="text-decoration:none;"><div id="actTopicCircle3" class="smallCircle red" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer textRed">Student Comment Summary</div></a></td>    
	</table>
		<div id="innerContainer">
			<?php if($mode!='studentComment') { ?>                 
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Error Reporting</span>
			</div>
                        <?php } ?>
			<div id="containerBody">
			<div id="mainDiv">
    <div id="quesText">
    	<?php     	
    	$errorType=""; if($type=="normal" || $type=="challenge" || strpos($type, 'wildcard') !== false || $type=="bonusCQ" || $type=="practiseModule") {
			$errorType="question";
			if(strpos($type, "research") !== false)
				$question     = new researchQuestion($qcode);
			else
				$question     = new Question($qcode);					
			if($question->isDynamic())
			{
				if($comment_srno != '')
					$queryDynamicParam  = "SELECT dynamicParameters FROM adepts_userComments WHERE srno=$comment_srno";	
				else
					$queryDynamicParam  = "SELECT parameters FROM adepts_dynamicParameters WHERE mode='$type' AND class=$childClass AND quesAttempt_srno= $quesAttemptSrno";								
					
				$resultDynamicParam = mysql_query($queryDynamicParam);
				if(mysql_num_rows($resultDynamicParam)>0)
				{
					$line   = mysql_fetch_array($resultDynamicParam);
					$dynamicParameters = $line[0];
					$question->generateQuestion("answer",$dynamicParameters);
					
				}
				else
				{
					if($type=="practiseModule")
					{
						$qnoQuery = "SELECT a.id FROM practiseModulesQuestionAttemptDetails a WHERE a.sessionID=".$sessionID." AND a.qno=".$qno." AND a.qcode=".$qcode;
					}
					else {
						$qnoQuery = "SELECT a.srno FROM adepts_teacherTopicQuesAttempt_class".$childClass." a WHERE a.sessionID=".$sessionID." AND a.questionNo=".$qno." AND a.qcode=".$qcode;
					}
					$qnoRes = mysql_query($qnoQuery);
					if (mysql_num_rows($qnoRes)>0)
					{
						$srno=mysql_fetch_array($qnoRes);
						$srno=$srno[0];
						$queryDynamicParam1  = "SELECT parameters FROM adepts_dynamicParameters WHERE mode='$type' AND class=$childClass AND quesAttempt_srno= $srno";
						$resultDynamicParam1 = mysql_query($queryDynamicParam1);
						if (mysql_num_rows($resultDynamicParam1)>0){
							$line   = mysql_fetch_array($resultDynamicParam1);
							$dynamicParameters = $line[0];
							$question->generateQuestion("answer",$dynamicParameters);
						}
						else 
						{
							$question->generateQuestion();
						}
					}
					else
					{
						$question->generateQuestion();
					}
				}
			}
			$question1 = $question->getQuestion();
			$questionType = $question->quesType;
			$correct_answer = $question->getCorrectAnswerForDisplay();
			if($correct_answer=="A")
				$optiona_bgcolor="#00FF00";
			if($correct_answer=="B")
				$optionb_bgcolor="#00FF00";
			if($correct_answer=="C")
				$optionc_bgcolor="#00FF00";
			if($correct_answer=="D")
				$optiond_bgcolor="#00FF00";
				?>
            <div class='question'>
            <table width='100%' border=0 cellspacing=0>
                <tr  bgcolor="" >
                    <td align='left'><?=$question1?><br/></td>
                </tr>
				<?php
                if($questionType=='MCQ-4' || $questionType=='MCQ-3' || $questionType=='MCQ-2')    {
                ?>
                <tr bgcolor="">
                    <td>
                        <table width="100%" border="0" cellspacing="2" cellpadding="3">
                            <?php     if($questionType=='MCQ-4' || $questionType=='MCQ-2')    {    ?>
                            <tr valign="top">
                                <td width="5%"  nowrap bgcolor='<?=$optiona_bgcolor?>' align="center" ><b>A</b></td>
                                <td width="45%"><?php echo $question->getOptionA();?></td>
                                <td width="5%" nowrap bgcolor='<?=$optionb_bgcolor?>' align="center" ><b>B</b></td>
                                <td width="45%"><?php echo $question->getOptionB();?></td>
                            </tr>
                            <?php    }    ?>
                            <?php    if($questionType=='MCQ-4')    {    ?>
                            <tr valign="top">
                                <td width="5%" bgcolor='<?=$optionc_bgcolor?>' align="center"><b>C</b></td>
                                <td width="45%"><?php echo $question->getOptionC();?></td>
                                <td width="5%" bgcolor='<?=$optiond_bgcolor?>' align="center"><b>D</b></td>
                                <td width="45%"><?php echo $question->getOptionD();?></td>
                            </tr>
                            <?php    }    ?>
                            <?php    if($questionType=='MCQ-3')    {    ?>
                            <tr valign="top">
                                <td width="3%" nowrap bgcolor='<?=$optiona_bgcolor?>' align="center"><b>A</b></td>
                                <td width="30%"><?php echo $question->getOptionA();?></td>
                                <td width="3%" nowrap bgcolor='<?=$optionb_bgcolor?>' align="center"><b>B</b></td>
                                <td width="30%"><?php echo $question->getOptionB();?></td>
                                <td width="3%" nowrap bgcolor='<?=$optionc_bgcolor?>' align="center"><b>C</b></td>
                                <td width="30%"><?php echo $question->getOptionC();?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
                <?php  } ?>
                <tr bgcolor="">
                    <td align='left'><br /><b>Display Answer:</b><br /><?php echo $question->getDisplayAnswer();?></td>
                </tr>
            </table>
		</div>
    <?php }
	else if($type=="timedtest") { $errorType="timed test"; echo "<b>Timed test : </b><a target='_blank' href='timedTest.php?timedTest=".$qcode."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getTimedTest($qcode)."</a>"; }
	else if($type=="activity") { $errorType="activity"; echo "<b>Activity : </b><a target='_blank' href='enrichmentModule.php?gameID=".$qcode."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getActivity($qcode)."</a>"; }
	else if($type=="remedial") { $errorType="remedial"; echo "<b>Remedial : </b><a target='_blank' href='remedialItem.php?qcode=".$qcode."&tmpMode=sample' onclick='setTimeout(function(){tryingToUnloadPage=false},500);'>".getRemedial($qcode)."</a>"; }
	?>
    </div>
    <?php if($mode!="viewer" && $mode!="studentComment") { ?>
    <form method="POST" id="frmMain" name="frmMain">
    <input type="checkbox" name="general" id="general" /><b>General comment (not related to this <?=$errorType?>)</b>
    <div id="commentText">
        
            <input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>" />
            <input type="hidden" name="type" id="type" value="<?=$type?>" />
            <input type="hidden" name="sessionID" id="sessionID" value="<?=$sessionID?>" />
            <input type="hidden" name="qno" id="qno" value="<?=$qno?>" />
            <input type="hidden" name="quesComments" id="quesComments" value="<?=$quesComments?>" />
            <input type="hidden" name="dynamicParameters" id="dynamicParameters" value="<?=$dynamicParameters?>" />
            <div id="commentTextDiv" contenteditable="">[Write your comment here]</div>
            <input type="hidden" name="base64img" id="base64img" value="" />
            
    </div>
	<div id="btnDiv" align="center">
                <input type="button"  name="submitComment" id="submitComment" value="Submit" class="buttons" onClick="submitForm();"/>
                <input type="button"  name="discardComment" class="buttons" onClick="goBack();" id="discardComment" value="Discard" />
                <input type="button" class="button" name="uploadImage" id="uploadImage" value="Upload Image" style="display:none" onClick="popup();" />
            </div>
    </form>
    <?php } ?>
</div>
			</div>
			
		
		</div>
		
		
	</div>

<?php
function getQcodeOwner($qcode, $type)
{
	if(strpos($type, "research") !== false)
	{
		$sq	=	"SELECT owner1,owner2 FROM adepts_topicMaster A , adepts_subTopicMaster B , adepts_clusterMaster C , adepts_researchQuestions D 
				 WHERE B.subTopicCode=C.subTopicCode AND A.topicCode=B.topicCode AND C.clusterCode=D.clusterCode AND D.qcode=$qcode";
	}
	else
	{
		$sq	=	"SELECT owner1,owner2 FROM adepts_topicMaster A , adepts_subTopicMaster B , adepts_clusterMaster C , adepts_questions D 
				 WHERE B.subTopicCode=C.subTopicCode AND A.topicCode=B.topicCode AND C.clusterCode=D.clusterCode AND D.qcode=$qcode";
	}
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else
		return $rw[1];
}
function getActivityOwner($gameID)
{
	$sq	=	"SELECT B.owner1,B.owner2,owner FROM adepts_gamesMaster A , adepts_topicMaster B WHERE A.topicCode=B.topicCode AND A.gameID=$gameID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
	else if($rw[2]!="")
		return $rw[2];
}
function getRemedialOwner($remedialItemCode)
{
	$sq	=	"SELECT A.owner1,A.owner2,D.owner1 FROM adepts_topicMaster A , adepts_subTopicMaster B , adepts_clusterMaster C , adepts_remedialItemMaster D 
			 WHERE B.subTopicCode=C.subTopicCode AND A.topicCode=B.topicCode AND C.clusterCode=D.linkedToCluster AND D.remedialItemCode='$remedialItemCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
	else if($rw[2]!="")
		return $rw[2];
}
function getTimedTestOwner($timedTestCode)
{
	$sq	=	"SELECT A.owner1,A.owner2 FROM adepts_topicMaster A , adepts_subTopicMaster B , adepts_clusterMaster C , adepts_timedTestMaster D 
			 WHERE B.subTopicCode=C.subTopicCode AND A.topicCode=B.topicCode AND C.clusterCode=D.linkedToCluster AND D.timedTestCode='$timedTestCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
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
?>

<?php include("footer.php") ?>
