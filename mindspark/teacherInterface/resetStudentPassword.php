	 <?php
	error_reporting(E_ALL);
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	include("header.php");
	include_once("functions/functions.php");
	
	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	
	$userid = $_SESSION['userID'];
	$user   = new User($userid);
	$uname  = $user->username;
	$schoolCode = $_SESSION['schoolCode'];
	$user_category = $_SESSION['admin'];
	$user_subcategory = $_SESSION['subcategory'];	

	$arrClassSection = getClassesAssigned($schoolCode, $user_category, $userid);
	$arrStudentDetails = $arrTeacherDetails = array();
	foreach ($arrClassSection as $key => $value) {
		$arrTemp = getStudentDetails($value['class'],$schoolCode,$value['section']);
		foreach($arrTemp as $k=>$v)
		{
			$arrStudentDetails[$k] = $v[0]." (".$v[1].")";
		}
	}
	function sendPasswordChangeMail($parentName, $username, $newPwd, $ip, $parentEmail, $mode)
	{
		$subject = "Mindspark Password";
		$headers = "From:<notification@ei-india.com>\r\n";
		//$headers .= "To:".$parentEmail."\r\n";
		$headers .= "Bcc:notification@ei-india.com\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$body = "Dear Parent,<br/><br/>";
		if ($mode=='child')
			$body .= "Your child's password has been reset. Following are the login credentials:<br/><br/>";
		$body .= "Username: ". $username ."<br>Password: ". $newPwd ."<br/><br/>";
		$body .= "(Password was changed from: ".$ip.")<br/><br/>";

		
		$body .= "With warm regards,<br/>Team Mindspark<br/>";
		$body .= "Toll Free no: 1800 102 8885<br/>www.mindspark.in<br/>www.facebook.com/mindspark.ei<br/>http://blog.ei-india.com/";

		//echo $body;
		$success=0;
		if($parentEmail != "")
		{
			$success=mail($parentEmail, $subject,$body,$headers,"-f bounce@ei-india.com");
		}
	}
	if($user_category=="School Admin")
	{
		$arrTeacherDetails = getTeacherIDs($schoolCode);
	}
	if (isset($submit) && $submit=="Reset Password")
	{
		
		$child_query = "SELECT childName, schoolCode, subcategory, childClass, childSection, category, username FROM adepts_userDetails WHERE userID='".$reset_userID."'";
		$child_result = mysql_query($child_query) or die(mysql_error());
		if (mysql_num_rows($child_result)>0)
		{	
			
			$child_data = mysql_fetch_array($child_result);
			if ($category != strtolower($child_data['category']))
			{	
				
				$message = $category=="student"?"<big>Please enter valid student name</big>":"<big>Please enter valid teacher name</big>";
			}
			else if (strcasecmp($child_data['category'], "STUDENT")==0 && strcasecmp($child_data['subcategory'],"School")!=0)
			{
				$message = "<BIG>Sorry, you are not authorized to reset the password of ".$child_data['childName'].".</BIG>";
			}
			else
			{	
				
				    $canReset = false;
										
				    if ($schoolCode==$child_data['schoolCode'] && strcasecmp($user_category,"School Admin")==0)
					{
						$canReset = true;
			
					}
				        
				    elseif(strcasecmp($user_category,"Teacher")==0 && $schoolCode==$child_data['schoolCode'])
				    {
				    	foreach ($arrStudentDetails as $key => $value) {
				    		if($reset_userID==$key)
				    		{
				    			$canReset = true;
				    			break;
				    		}
				    	}				        
				    }
					
					if ($canReset)
					{
						
						if ($resetScheme=="blank")
						{
							$newPwd = "";
						}
						else if ($resetScheme=="default")
						{
							$newPwd = strtolower($child_data['username']);
						}
						
                        mysql_query("UPDATE educatio_educat.common_user_details SET password=password('".$newPwd."'), updated_by='".$uname."' WHERE MS_userID='".$reset_userID."'");
						mysql_query("UPDATE adepts_forgetPassNotification SET status = 1 WHERE childUserID='".$reset_userID."'");

						if ($resetScheme=="default")
						{
							$message = "<BIG>Password of <b>".$child_data['childName']."</b> has been reset to <b>$newPwd</b></BIG>";
						}
						else if ($resetScheme == "blank")
						{
							$message = "<BIG>Password of <b>".$child_data['childName']."</b> has been reset to blank</BIG>";
						}

						$query = "SELECT ud.username, childClass, category, subcategory, childName,ud.userID, IFNULL(pd.username,ud.parentEmail) 'email', IFNULL(CONCAT(pd.firstName,' ',pd.lastName),ud.parentName) 'parentName'
						            FROM adepts_userDetails ud LEFT JOIN parentChildMapping pc ON ud.userID=pc.childUserID LEFT JOIN parentUserDetails pd ON pc.parentUserID=pd.parentUserID
						            WHERE ud.userID='".$reset_userID."' AND category='STUDENT'";
						$result = mysql_query($query);
						if (mysql_num_rows($result) != 0) {
						    $line = mysql_fetch_array($result);
							$ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];   //Needed since apps behind a load balancer
							if(strpos($ip,',') !== false) {
								$ip = substr($ip,0,strpos($ip,','));
							}
						    sendPasswordChangeMail($line['parentName'],$line['username'], $newPwd, $ip,$line['email'],'child');
						}
					}
					else
					{	
						$message = "<BIG>Sorry, you are not authorized to reset the password of ".$child_data['childName'].".</BIG>";
					}				
			}
		}
		else
		{
			$message = "<BIG>Please enter a valid name.</BIG>";
		}
	}
		
?>

<title>Reset Password</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/resetStudentPassword.css?ver=2" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<script type='text/javascript' src="libs/jquery-ui.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest1.js"></script>
<script language="javascript" type="text/javascript" src="libs/suggest2.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
		if(document.getElementById('rdStudent').checked == true){
			showDiv('student');
		}else{
			showDiv('teacher');
		}		
		suggestList();		
	}

	function suggestList()
	{
		if(document.getElementById('rdStudent').checked == true)
			var obj1 = new actb(document.getElementById('studentname'),userArray);
		else
			var obj1 = new actb(document.getElementById('teachername'),teacherArray);	
	}
function validate()
{
	var selected_name = "";
	var userID ="-1";
	if (document.getElementById('rdTeacher') && document.getElementById('rdTeacher').checked)
	{
		selected_name = document.getElementById('teachername').value;
		if (selected_name=="")
		{
			alert("Please select the teacher name.");
			document.getElementById('teachername').focus();
			return false;
		}		
		$.each(teacherJson, function(k, v) {			    
		    if(v==selected_name)
		    {
		    	userID=k;
		    }
		});
		if(userID==-1)
		{
			alert("Please select a valid teacher name.");
			document.getElementById('teachername').focus();
			return false;
		}	
	}

	else if (document.getElementById('rdStudent').checked)
	{
		var selected_name = document.getElementById('studentname').value;
		if (selected_name=="")
		{
			alert("Please select the child name.");
			document.getElementById('studentname').focus();
			return false;
		}
		$.each(userJson, function(k, v) {			    
		    if(v==selected_name)
		    {
		    	userID=k;
		    }
		});
		if(userID==-1)
		{
			alert("Please select a valid child name.");
			document.getElementById('studentname').focus();
			return false;
		}
	}
	
	document.getElementById('reset_userID').value = userID;

	if (document.getElementById('rdBlank').checked)
	{		
		var decision = confirm("This will reset the password to blank.\nAre you sure?");
		if (!decision)
		{
			return false;
		}
	}
	else
	{
		var decision = confirm("This will reset the password to its default value, same as username.\nAre you sure?");
		if (!decision)
		{
			return false;
		}
	}
	
		
	setTryingToUnload();
}
function showDiv(tblName)
{
	if (tblName=="teacher")
	{
		document.getElementById('tblStudent').style.display = "none";
		document.getElementById('tblTeacher').style.display = "inline";
		document.getElementById('studentname').value = "";
		document.getElementById('rdDefault').checked = true;
		document.getElementById('rdBlank').checked = false;
		document.getElementById('rdTeacher').checked = true;
	}
	if (tblName=="student")
	{
		document.getElementById('tblTeacher').style.display = "none";
		document.getElementById('tblStudent').style.display = "inline";
		document.getElementById('teachername').value = "";
		document.getElementById('rdStudent').checked = true;
	}
	suggestList();
}
var userJson = <?php echo json_encode($arrStudentDetails); ?>;
var teacherJson = <?php echo json_encode($arrTeacherDetails); ?>;
var userArray = Object.keys(userJson).map(function(k) {return userJson[k] }); //convert to Array
var teacherArray = Object.keys(teacherJson).map(function(k) { return teacherJson[k] }); //convert to Array
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
<form name="frmResetPassword" id="frmResetPassword" method="POST" action="resetStudentPassword.php">
	<div id="container">
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Reset Student Password</span>
			</div>
		<?php
			if ($user_category!="TEACHER")
			{
			?>
				<div class="pwd1">
				<input type="radio" name="category" id="rdTeacher" value="teacher" onClick="showDiv('teacher')" <? if (isset($category) && $category=="teacher") echo "checked";?>>
				<span id="pwdtext"> <a href='#' onClick="showDiv('teacher')" style="text-decoration:none;">RESET TEACHER PASSWORD </a></span>
				</div>
				<div id="plusVertical"> &nbsp;</div>
				<div class="pwd2">
				<input type="radio" name="category" id="rdStudent" value="student" onClick="showDiv('student')" <? if (!(isset($submit) && $submit=="Reset Password")) echo "checked"; else if (isset($category) && $category=="student") echo "checked";?>>
				<span id="pwdtext"> <a href='#' onClick="showDiv('student')" style='text-decoration:none;'>RESET STUDENT PASSWORD</a> </span>
				</div>
			<?php
			}
			else
			{
				?>
				<div style="margin-top:1%;"> <span id="pwdtext"> PROVIDE THE FOLLOWING DETAILS TO RESET STUDENT PASSWORD </span> </div>
				<b style="display:none"><input type="radio" name="category" id="rdStudent" value="student" checked></b>
				<?
			}
			?>
		</div>
		<div id="line"> </div>
		
				<?php
				if (isset($submit) && $submit=="Reset Password")
				{ ?>
				<div align='center' style="font-size:16px;margin-top: 2%;"><?php	echo "$message";  ?></div>
				<?php }		?>
				<fieldset style="display:inline" id="tblStudent" class="changePasswordFomatting">
					<legend><b><span style="color:black;">Change Student Password</span></b></legend>
					<table border="0" cellpadding="2" cellspacing="5">	
						<tr><td colspan="2">&nbsp;</td></tr>									
						<tr>
							<td colspan="2">
								<span style="color:black;">Child's name:</span>
								<input type="text" name="studentname" id="studentname" autocomplete="off" size="30">
							</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<td nowrap>
								<input type="radio" name="resetScheme" id="rdBlank" value="blank" <? if (isset($resetScheme) && $resetScheme=="blank") echo "checked";?>>
								<span style="color:black;"><a href='#' style='text-decoration:none;' onclick='document.getElementById("rdBlank").checked=1;'>Reset to blank (Remove Password)</a></span>
							</td>
							<td nowrap>
								<input type="radio" name="resetScheme" id="rdDefault" value="default" <? if (!(isset($submit) && $submit=="Reset Password")) echo "checked"; else if (isset($resetScheme) && $resetScheme=="default") echo "checked";?>>
								<span style="color:black;"> <a href='#' style='text-decoration:none;' onclick='document.getElementById("rdDefault").checked=1;'>Reset to default (same as username)</a><span>
							</td>
						</tr>
					</table>
				</fieldset>
			
				<fieldset style="display:none" id="tblTeacher" class="changePasswordFomatting">
					<legend><b><span style="color:black;">Change Teacher Password<span></b></legend>
					<table border="0" cellpadding="2" cellspacing="2">
						<tr><td colspan='4'>&nbsp;</td></tr>
						<tr>
							<td>
								<span style="color:black;">Teacher's name:</span>
								<input type="text" name="teachername" id="teachername" size="30" autocomplete="off">
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>Note: Teacher's password will be reset to default (same as username)</td>
						</tr>
					</table>
				</fieldset>
				<div>
				<input type='hidden' name='reset_userID' id='reset_userID' value=''>	
				<input type="submit" name="submit" id="submit" class="buttons" value="Reset Password" onClick="return validate();" <?php if($user_category=="School Admin" && $user_subcategory=="All") echo " disabled";?>>
				</div>
							
	</div>
</form>
<?php include("footer.php") ?>