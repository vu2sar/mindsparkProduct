
<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
    //error_reporting(E_ERROR);
	@include("header.php");
	include("../userInterface/classes/eipaging.cls.php");
	include("../userInterface/constants.php");
	include("../constants.php");	
	/*error_reporting(E_ALL);*/
	$clspaging = new clspaging('alltasklist');
	$clspaging->setgetvars();
	$clspaging->setpostvars();
	$basedir = "http://www.educationalinitiatives.com/mindspark/explanation_images/";
	$upload_folder = 'https://mindspark-ei.s3.amazonaws.com/';
	if(SERVER_TYPE=="LIVE")
	{
		if (!class_exists('S3')) require_once '../../s3/S3.php';
		$s3 = new S3(awsAccessKey, awsSecretKey);
	} 	
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	$mode = $_GET['mode'];
	$videono = $_GET['videono'];
	$videoName = $_GET['videoname'];
	$oneToOneComments = array();	// for the mantis task 12320
	$userid = $_SESSION['userID'];
	$username = isset($_SESSION['username'])?$_SESSION['username']:"";
	$sessionid=$_SESSION['sessionID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$category   = isset($_SESSION['admin'])?$_SESSION['admin']:"";
	$ccList = isset($_POST['ccList'])?$_POST['ccList']:"";
	$errors = "";

	$query = "SELECT srno from adepts_userComments where userID='".$userid."' and viewed=0 and status='Closed' order by srno LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	if($row = mysql_fetch_row($result))
	{
		echo "<script>
				$( document ).ready(function() {
				  window.location = '#notificationPointer'+".$row[0].";
				});
		
			</script>";
	}

	if(isset($_POST['submit2']) && $_POST['submit2'] == 'Send')
	{

		if($userView=='')
		{
			$update_query = "UPDATE adepts_userComments SET satisfy=1 ";
			
			if($rating!="")
			$update_query.=",rating=$rating ";
					
			$update_query.= "WHERE srno=$srno";
			
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
		else if($userView=='no')
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
					$sq	=	"INSERT INTO adepts_userCommentDetails SET srno=$srno,comment='".mysql_real_escape_string($recomment)."',commentDate=NOW(),commenter='".$username."',flag=($tempFlag+1),userID=".$userid.",schoolCode=".$schoolCode;
					$rs	=	mysql_query($sq);
					if($rs)
					{	
						$update_query = "UPDATE adepts_userComments SET status='Re-Open',satisfy=2 ";
						
						if($rating!="")
						$update_query.=",rating=$rating ";
						
						$update_query.= "WHERE srno=$srno";
						
						$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
					}
				}

			}
		}
		else
		{
			$update_query = "UPDATE adepts_userComments SET satisfy=0 ";
			
			if($rating!="")
			$update_query.=",rating=$rating ";
			
			$update_query.="WHERE srno=$srno";
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
			
		} 	
		header('location:Comments.php');
	}
	if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Submit')
	{

	    $max_allowed_file_size = 1024; // size in KB
        $permitArray = array("jpg", "jpeg", "gif", "bmp", "png");
	    $commentImage = "";
	    for($fileno=1; $fileno<=3; $fileno++)
	    {
	        //Get the uploaded file information
	        ${"name_of_uploaded_file".$fileno} = basename($_FILES['uploaded_file'.$fileno]['name']);
    	    if(${"name_of_uploaded_file".$fileno}!="")
    	    {
    	        $size_of_uploaded_file = $_FILES["uploaded_file".$fileno]["size"]/1024;
    	        //get the file extension of the file
	            $type_of_uploaded_file = substr(${"name_of_uploaded_file".$fileno}, strrpos(${"name_of_uploaded_file".$fileno}, '.') + 1);

        	    if($size_of_uploaded_file > $max_allowed_file_size )
            	{
            		$errors .= "<br/> Size of file should be less than $max_allowed_file_size KB";
            	}
            	$allowed_ext = false;
            	for($i=0; $i<sizeof($permitArray); $i++)
            	{
            		if(strcasecmp($permitArray[$i],$type_of_uploaded_file) == 0)
            		{
            			$allowed_ext = true;
            		}
            	}
            	if(!$allowed_ext)
            	{
            		$errors .= "<br/> The uploaded file is not a supported file type.";
            	}
            	if(empty($errors))
            	{
            		//copy the temp. uploaded file to uploads folder
            		$path_of_uploaded_file = $upload_folder . ${"name_of_uploaded_file".$fileno};
            		$tmp_path = $_FILES["uploaded_file".$fileno]["tmp_name"];

            		if(is_uploaded_file($tmp_path))
            		{
            		    if ($_FILES["uploaded_file".$fileno]["error"] > 0)
						{
							$errors = "Return Code: " . $_FILES["file"]["error"] . "<br />";
						}
						else
						{
							$fileinfo = pathinfo($_FILES["uploaded_file".$fileno]["name"]);
							$timestamp = strtotime('now');
							$newFile = $timestamp."@".$_FILES["uploaded_file".$fileno]["name"];
							if(in_array(strtolower($fileinfo["extension"]),$permitArray))
							{
								
								$documentFileName="teacherComments/".$newFile;			
								if(SERVER_TYPE=="LIVE")
								{
									if($tempWhatsNew["documentURL"]!='')
										$s3->deleteObject(MSMaths_BucketName,rawurldecode($tempWhatsNew["documentURL"]));
									$s3->putObjectFile($_FILES["uploaded_file".$fileno]['tmp_name'], MSMaths_BucketName,$documentFileName , S3::ACL_PUBLIC_READ);
								}
								/*if($editId=='')
								{
									echo "<script>alert('FAQ Updated.')</script>";
								}*/
							}
							else
							{
								$errors= "File type not allowed";
							}
						}
            		}
            		$commentImage .= $documentFileName."~";
    	        }

	        }
	    }
	    if(empty($errors))
	    {
			 include_once("classes/clsbucketCommentsV1.php");
			$obj = new commentCategorization();
			$systemCategory	=	$obj->mark($comment);
	        $commentImage = substr($commentImage,0,-1);

	       	$comment = str_replace('~','-',$comment);
			$insert_query = sprintf("INSERT INTO adepts_userComments (userID,sessionID,comment,queryType,status,imageAttachment,commentReceivedate,commentBy,category,systemCategory,assignTo)
    						 VALUES ('$userid ','$sessionid','%s','$type','Open','$commentImage',now(),'Teacher','$systemCategory','$systemCategory','anita.kamath')",
           					 mysql_real_escape_string($comment));


    		$insert_result = mysql_query($insert_query) or die(mysql_error());

    		$sq = sprintf("INSERT INTO adepts_userCommentDetails SET srno=LAST_INSERT_ID(),
    			comment='%s',commentDate=NOW(),commenter='".$username."',schoolCode=".$schoolCode.", flag=1,userID=".$userid,
           					 mysql_real_escape_string($comment));
    		$rs	=	mysql_query($sq);
    	 	$_REQUEST=NULL;
    		header("Location:Comments.php");
	    }

	}

	if(isset($_POST['ajaxForNew']) && $_POST['ajaxForNew']==1)
	{
		if(isset($_POST['srNoForNew']))
		{
			$update_query = "UPDATE adepts_userComments SET viewed=1 WHERE srno=".$_POST['srNoForNew'].";";
			$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
		}
	}
?>

<title>Comments</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/myClasses.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/Comments.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>

	var langType = '<?=$language;?>';
	function load(){
		var mode = '<?=$mode;?>';
		var videono = '<?=$videono;?>';
		var videoName = '<?=$videoName;?>';
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		
		if(mode=="addacomment"){
			hideContectTable();
			$("#videoBlock").attr("selected","true");
			$("#comment").text("Comment on Misconception Video : "+videoName);
		}
		/*$("#container").css("height",containerHeight+"px");*/
	}
	$(document).ready(function(e) {
		i18n.init({ lng: langType,useCookie: false }, function(t) {
			$(".translation").i18n();
			$(document).attr("title",i18n.t("Comments"));

		});
		var userView=10;
		$('.userView').each(function() {
			
		    var userViewId = $(this).attr('id');
			var userViewIdArr	=	userViewId.split("_");
		//	alert($('.userView').attr('id'));
			if($('.userView').val()=='')
				{
					$("#rateMe_"+userViewIdArr[1]).css("display","none");
					$('.userView').next().hide();
					userView=10;
				}
				else if($('.userView').val()=='no')
				{
					$('.userView').next().show();
					$("#rateMe_"+userViewIdArr[1]).css("display","none");
					userView=0;
				}
				else
				{
					$('.userView').next().hide();
					$("#rateMe_"+userViewIdArr[1]).css("display","block");
					userView=1;
				}
		});

		$(".userView").change(function(){
			var userViewId = $(this).attr('id');
			var userViewIdArr	=	userViewId.split("_");
			
				if($(this).val()=='')
				{
					$("#rateMe_"+userViewIdArr[1]).css("display","none");
					$(this).next().hide();
					userView=10;
				}
				else if($(this).val()=='no')
				{
					$(this).next().show();
					$("#rateMe_"+userViewIdArr[1]).css("display","none");
					userView=0;
				}
				else
				{
					$(this).next().hide();
					$("#rateMe_"+userViewIdArr[1]).css("display","block");
					userView=1;
				}
			
		});
	

		$(".more-less").click(function () {

			var srno = $(this).parent().find("input[name='srno']").val();
			$.ajax({
	                type        : 'POST',
	                url         : 'Comments.php',
	                data        : {srNoForNew:srno,ajaxForNew:1},
	                dataType    : 'json',
	                encode      : true
	        });
		    $header = $(this);
		    //getting the next element
		    $content = $header.next();
		    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
		    $content.slideToggle(500, function () {
		        //execute this after slideToggle is done
		        //change text of header based on visibility of content div
		        $header.text(function () {
		            //change text based on condition
		            return $content.is(":visible") ? "Hide" : "See more!";
		        });
		    });

		});
	});

</script>
<script>
	function hideContectTable(){
		setTimeout(function(){tryingToUnloadPage=false},500);
		document.getElementById('detail').style.display='none';
		document.getElementById('dlgTecherComment').style.display='inline';
		document.getElementById('submit').value="Submit";
		document.getElementById('add').style.display='none';
		document.getElementById('addComment').style.display='none';
		document.getElementById('line').style.display='none';

	}
	function hideCommentDialog(){
		document.getElementById('detail').style.display='inline';
		document.getElementById('dlgTecherComment').style.display='none';
		 var add = document.getElementById("add");
		 var addComment = document.getElementById("addComment");
		 var line = document.getElementById("line");
		  add.style.display = "block";
		  addComment.style.display = "block";
		  line.style.display = "block";
		//document.getElementById('Submit').value="";
		//document.frmCommentDisplay.submit.value="";
		//opener.location.reload(true);window.close();
	}
	function submitForm(){
		document.getElementById('frmMain').submit();
	}
	function validation(){
	var error = "";
	if(document.getElementById('comment').value=="")
		error += "Please enter Comment\r\n";
	if(error != "")
	{
		alert(error);
		return false;
	}
	document.frmCommentDisplay.action="Comments.php";
	//document.frmCommentDisplay.actiontodo.value="Save";
	setTryingToUnload();
	document.frmCommentDisplay.submit();
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
			<td width="33%" id="sectionRemediation" class="activatedTopic"><div id="actTopicCircle1" class="smallCircle red" style="cursor:pointer;"></div><div id="1" style="cursor:pointer;" class="pointer textRed">Comments</div></td>
		        <td width="33%" id="studentRemediation" class="activateTopicAll"><a href="teacherCommentReport.php" style="text-decoration:none;"><div id="actTopicCircle2" class="smallCircle" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer">Error reporting</div></a></td>
                        <td width="34%" id="studentComments" class="activateTopicAll"><a href="studentComments.php" style="text-decoration:none;"><div id="actTopicCircle3" class="smallCircle" style="cursor:pointer;"></div><div id="2" style="cursor:pointer;" class="pointer">Student Comment Summary</div></a></td>
	</table>
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Comment</span>
			</div>
			
			<a href="javascript:void(0)" onClick="hideContectTable();"><div id="add">
				<div id="circle">
				<div id="plushorizontal"> </div>
				<div id="plusVertical"> </div>
				 </div>
				 
				 
			</div></a>
			
			<div id="addComment"> Add a Comment </div>
		</div>
		<div id="line"> </div>
		<div id="containerBody">
		<?php if($errors!="")	    {	        echo "<div align='center' style='font-weight:bold'>$errors</div>";	    }?>
<!-- <form name="frmCommentDisplay" method="POST" action="Comments.php" enctype="multipart/form-data"> -->
	<p>
	<div name="detail" id="detail">
	
	
	<br/>
	<?php
	

		$comment_query = "SELECT a.*, DATE_FORMAT(DATE(a.commentReceivedate), '%d-%m-%Y') as comment_date 
		FROM adepts_userComments a, adepts_userCommentDetails b
		WHERE a.srno = b.srno and a.commentBy='Teacher' and queryType is not null and a.userID=".$userid." GROUP BY a.srno ORDER BY MAX(b.lastModified) desc";
		$comment_result = mysql_query($comment_query) or die("<br>Error in comment query - ".mysql_error());
		$clspaging->numofrecs = mysql_num_rows($comment_result);
		if($clspaging->numofrecs>0)
		{
			$clspaging->getcurrpagevardb();
		}
		if (mysql_num_rows($comment_result)==0)
		{
			echo "<div align='center'><h3>No comments found!<h3></div>";
			$clspaging->numofrecs=0;
		}
		else
		{

	?>


		<div align="center" style="width:90%;margin-bottom: 4px;">
			<br>In last few days, you have commented, or reported problem, or given suggestion. Here are the response(s) to those comment(s).</div>


				<?php
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
						<?php }
				?>
				<table border="0" cellpadding="5" cellspacing="0" width="90%" class="tblContent" align="center">
					<tr>
						<td align="center" width="5%" class="header"><b>Sr.No.</b></td>
						<!-- <td align="center" width="15%" class="header"><b>Comment Date</b></td> -->
						<td align="center" class="header"><b>Comment - Response</b></td>
						<!-- <td align="center" class="header" style="max-width:400px;"><b>Response</b></td> -->
						<!-- <td align="center" width="10%" class="header"><b>Type</b></td>
						<td align="center" width="10%" class="header"><b>Status</b></td> -->
					</tr>
					<?php
						$srno=($clspaging->currentpage-1)*$clspaging->numofrecsperpage+1;
						$comment_query .= $clspaging->limit;
						//echo "<br>Query is - ".$comment_query;
	    				$comment_result = mysql_query($comment_query) or die("<br>Error in query - ".mysql_error());
						while ($comment_data = mysql_fetch_array($comment_result))
						{	$comment_srno	=	$comment_data['srno'];
							$commentTrail	=	getCommentTrail($comment_srno,$userid,$_SESSION['childName']);
							//print_r($commentTrail);
							$reopenCriteria = getReopenCriteria($comment_srno,$userid,$_SESSION['childName']);
							$getDaysRemaining = getDaysRemaining($comment_srno);
							
							if($comment_data['comment']==""){
								$comment_query1 = "SELECT comment,flag FROM adepts_userCommentDetails WHERE srno=".$comment_data['srno'];
								$comment_result1 = mysql_query($comment_query1);
								while($comment_data1 = mysql_fetch_array($comment_result1)){
									if($comment_data1['flag']==1 || $comment_data1['flag']==3){
										$comment_data['comment'] = stripslashes($comment_data1['comment']);
									}else{
										$comment_data['finalcomment'] = stripslashes($comment_data1['comment']);
									}
								}	
							}
							echo "<tr>";
								echo "<td align=\"center\" id='notificationPointer".$comment_data['srno']."'>".$srno."</td>";
							
								echo "<td>";
								$tempStatus = ($comment_data['status'] != "Ignored") ? $comment_data['status'] : "Acknowledged";
								if($tempStatus == "Open")
									$tempStatus .= " (we will get back to you soon!)";
								echo "<p style='float:left; margin-right:20px;'><b>Type:</b> ".$comment_data['queryType']."</p><p style='float:left'> <b>Status:</b> ".$tempStatus."</p>";
								
								//echo $commentTrail['imageAttachment'];
								echo '<br/>';
								echo '<div class="from-me"><p>'.$commentTrail[1];
								echo '<br/><br/>';
								if($comment_data['imageAttachment']!="")
								{
								    $attachedImages = explode("~",$comment_data['imageAttachment']);
								    echo "<p class='tag' style='float:right; display:block;'>";
								    foreach ($attachedImages as $imgName){
										$imgNameShow = explode("@",$imgName);
										echo "<a style='float:right; color:white;margin-left:10px;' href='$upload_folder".$imgName."' target='_blank'><img style='width:130px;height:100px;' src='$upload_folder".$imgName."' title='$imgNameShow[1]'/></a>";
									} 
									echo "</p>";
									/*if(count($attachedImages)==3)
										echo "<br><br><br><br><br><br><br><br>";
									else if(count($attachedImages)==2)
										echo "<br><br><br><br><br><br><br><br>";
									else
										echo "<br><br><br><br><br><br><br><br>";*/
								}
								// echo "<br><br>";

								echo '</p></div><div class="clear"><br></div>';
								if($commentTrail[2] != "")
								{
									if($comment_data['viewed']==0 && ($comment_data['status']=="Closed" || $comment_data['status']=="Ignored" || $comment_data['status']=="Re-open") && $getDaysRemaining > 0 && count($commentTrail) <= 2)	// For the mantis task 12320
									{
										$oneToOneComments[] = $comment_data['srno'];
										echo '<label class="notification-container "><span class="notification-counter-inner notification-counter-inner1">New</span></label>';
									}
									echo '<div class="from-them"><p>'.$commentTrail[2].'</p></div><div class="clear"><br></div>';
								}

								if($comment_data['viewed']==0 && ($comment_data['status']=="Closed" || $comment_data['status']=="Ignored") && $getDaysRemaining > 0 && count($commentTrail) > 2)	// For the mantis task 12320
									echo '<label class="notification-container"><span class="notification-counter-inner">New</span></label>';
								if(count($commentTrail) > 2){
									echo '<div class="more-less"><label>See more!</label></div>';

							    }
								echo '<div class="show">';
								for($i=3;$i<count($commentTrail)+1;$i++){

									if($i%2!=0 && $commentTrail[$i] != "")
										echo '<div class="from-me"><p>'.$commentTrail[$i].'</p></div><div class="clear"><br></div>';
									else if($i%2==0 && $commentTrail[$i] != "")
										echo '<div class="from-them"><p>'.$commentTrail[$i].'</p></div><div class="clear"><br></div>';
								}
								echo "</div>";
								

								//$comment_data['comment'];
								
								
								if(count($commentTrail)%2 == 0) 
                                {	
                                	$replyTo	=	"Mindspark";
                                	echo '<form id="frmMain'.$comment_data['srno'].'" name="frmMain'.$comment_data['srno'].'" method="post" class="frmMain" action="">';
									if($comment_data['rating']=="" && $reopenCriteria==0 && $comment_data['status'] !="Ignored")
									{
										echo '<div id="formCover"><div style="margin-top:10px;">Kindly rate/reply to this response within <span style="font-size:1.2em;">'.$getDaysRemaining.'</span> days. It will automatically get closed after that.</div>';
	                                    echo '<div class="reComment">';
	                                    echo "<br>Was your problem solved? ";

										if(count($commentTrail)%2==0)
										{
											echo '<select id="userView_'.$comment_data['srno'].'" class="userView" name="userView"><option value="">Select</option><option value="yes">Yes</option><option value="no">No</option></select><div class="notSatisfy'.$comment_data['srno'].'" style="display:none"><br>Reply to '.$replyTo.': <textarea style="vertical-align: top;" cols="50" name="recomment" id="recomment'.$comment_data['srno'].'"></textarea></div><br>';
										}
	                                    
										if(count($commentTrail)%2==0)
										{
											echo'<div id="rateMe_'.$comment_data['srno'].'">									
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
										}	
										echo'<br><input type="submit" id="submit2" name="submit2" value="Send" class="button submitBtn" onClick="submitForm();"/></div></div>';
									}
										echo '<input type="hidden" name="srno" value="'.$comment_data['srno'].'"><input type="hidden" name="commentCount" id="commentCount_'.$comment_data['srno'].'" value="'.count($commentTrail).'">';
										echo '</form>';
                                }
								
											echo "</td>";
								/*echo "<td align=\"justify\" style=\"max-width:400px;\">";
								if($comment_data['status']=="Closed")
								{
								    echo $comment_data['finalcomment'];
								    if ($comment_data['image']!="")
								    {
									   echo "<br/><img src=\"".$basedir.$comment_data['image']."\" align=\"middle\">";
								    }
								}
								echo "</td>";*/
							echo "</tr>";
							$srno = $srno + 1;
						}
						if(count($oneToOneComments) > 0)
						{
							$oneToOneCommentsSrNo = implode(",", $oneToOneComments);
							$update_query = "UPDATE adepts_userComments SET viewed=1 WHERE srno in (".$oneToOneCommentsSrNo.");";
							$update_result = mysql_query($update_query) or die("<br>Error in update query - ".mysql_error());
						}
					?>
				</table>
		</div>
	<?php } ?>
	</div>
	</p>
	<form name="frmCommentDisplay" method="POST" action="Comments.php" enctype="multipart/form-data">	
	<div id="dlgTecherComment" name="dlgTecherComment" style="display:none;">
	<br/>
	<br/>
	<table align="center" cellpadding="5">
		<tr>
			<td><label for="teacherID">Comment ID</label></td>
			<td><input type="textbox" name="teacherID" id="teacherID" disabled size="30" value="<?php echo $sessionid;?>"/></td>
		</tr>
		<tr>
			<td><label for="type">Type</label></td>
			<td>
				<select name="type" id="type">
					<option value="Suggestion" selected="Suggestion">Suggestion</option>
					<option value="Doubt" >Doubt</option>
					<option value="Complaint" >Complaint</option>
					<option value="Other">Other</option>
					<option value="Video" id="videoBlock">Video</option>
				</select>
			</td>
		</tr>

		<tr>
			<td><label for="comment">Comment</label></td>
			<td><textarea id="comment" name="comment" rows="10" cols="58"></textarea></td>
		</tr>
		<tr>
		    <td><label>Attach File</label></td>
		    <td align="left"> 
		    	<?php if(SERVER_TYPE == 'LIVE'){ ?>
		        <label>File 1:</label> <input type="file" name="uploaded_file1" id="upload_file1"/><br>
		        <label>File 2:</label> <input type="file" name="uploaded_file2" id="upload_file2"/><br>
		        <label>File 3:</label> <input type="file" name="uploaded_file3" id="upload_file3"/>
		        <?php } else { echo '*This feature is not available in offline mode.';}
		        ?>
		    </td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type='submit' name="submit" id= "submit" value='Submit' onClick="return validation();" class="buttons">&nbsp;&nbsp;&nbsp;
				<input type="button"  value="Cancel" onClick="hideCommentDialog();" class="buttons">
			</td>
		</tr>
		</table>
		<?php if(SERVER_TYPE == 'LIVE') {?>
			<div style="padding-left:50px;">
	            <label>Note:</label>
	            <ul>
	                <li>The size of the attachment should be less than 1 MB </li>
	                <li>Allowed file types: jpg, jpeg, bmp, png, gif</li>
	            </ul>
	        </div>
		<?php } ?>
		
	</div>
</form>
		</div>
	</div>

<?php
function getCommentTrail($comment_srno,$userID,$childName)
{
	$arrayComments	=	array();
	$firstName	=	explode(" ",$childName);
	$firstName	=	$firstName[0];

	$sq	=	"SELECT a.id,a.srno,a.comment,b.finalcomment,a.image,DATE_FORMAT(a.commentDate, '%M %e, %Y %h:%i %p') as commentDate,a.commenter,a.flag,b.status
			 FROM adepts_userCommentDetails a, adepts_userComments b 
			 WHERE a.srno=$comment_srno and a.srno=b.srno";
	$rs	=	mysql_query($sq);
    $status='';
	while($rw=mysql_fetch_array($rs))
	{
		$status =$rw['status'];
		$comment = explode("~",str_replace("\\","",$rw["comment"]));
		$finalcomment = str_replace("\\", "", $rw["finalcomment"]);
		//if(count($comment)>1){
			$commentShow = $comment[count($comment)-1];
			$date = explode('::',$comment[count($comment)-1]);
			$commentShow =nl2br($date[0]);
		/*}else{
			$commentShow = nl2br($comment[0]);
		}*/
		if($rw['flag'] == 2)
		{
			 $q = "SELECT image from adepts_userComments where srno=".$rw['srno'];
		  	$r	=	mysql_query($q);
		  	$photo=mysql_fetch_array($r);
		  	if($photo['image'] !="")	
		    	$image="<br><a href='http://www.educationalinitiatives.com/mindspark/explanation_images/".$photo['image']."' target='_blank'><img class='imageFromThem' src='http://www.educationalinitiatives.com/mindspark/explanation_images/".$photo['image']."' style='width:200px;height:200px;' id='pic'/></a><br>";
	    	else
	    		$image="";
	    	$arrayComments[$rw['flag']]	=	$commentShow."<br>".$image."<p style='font-size:8pt; float:right;'>".$rw['commentDate']."</p>";
	    }	
		else
			if($commentShow != "")
				$arrayComments[$rw['flag']]	=	$commentShow."<br><p style='font-size:8pt; float:right;'>".$rw['commentDate']."</p>";
			else
				$arrayComments[$rw['flag']]	=	'';
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
		if($rw['flag']%2 !=0)
			return 20;
		else
			return 20 - $rw[5];
	}
	else
	{
		return 20;
	}
}
?>

<?php include("footer.php") ?>