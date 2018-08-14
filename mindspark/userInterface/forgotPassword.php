<?php
		require_once("dbconf.php");
        @include_once("mail/insert_mail.php");
        require_once("functions/functions.php");
        require_once 'constants.php';
       /*
        echo '<pre>';
        echo print_r($_POST);
        echo '</pre>';   */

        $question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');

        $flag = 0;
		$requesttype = '';

        if(isset($_POST['resetPassword']) && $_POST['resetPassword']=="Reset Password")
		{
			if(strcasecmp($_POST['username'],"demo@mindspark.in")==0)
				$flag = 4;
			else
			{
				$query="SELECT userID, childEmail, childDob, childName, userName, secretQues, secretAns, schoolCode FROM adepts_userDetails WHERE username='".$_POST['username']."'";
				$result = mysql_query($query) or die("Query failed : " . mysql_error());			
				if(mysql_num_rows($result)==1)
				{
					$user_row = mysql_fetch_array($result, MYSQL_ASSOC);
					
								
				/*echo '<pre>';
				print_r($user_row);			
				echo '<pre>';
				print_r($_POST);*/
				
        			$dob = formatDateDB($_POST['birthdate']);// get DOB converted
					
					if($user_row['schoolCode'] != '')
						$requesttype = 'true';

        			$secretQuestion = $_POST['secretQuestion'];
					$secretAnswer = strtolower($_POST['secretAnswer']);
			
					if ($user_row['secretQues']=="" && $user_row['secretAns']=="")
					{
						$flag = 5;
						//echo 'ggggggggggg';
					} 
					else if($dob==$user_row['childDob'])
					{
						if (strtolower($secretQuestion)==strtolower($user_row['secretQues']))
						{
                            if (strtolower($secretAnswer)==strtolower($user_row['secretAns']))
							{
								/*$childName 	 = $user_row['childName'];
								$temp = explode(" ", $childName);
								$password  = strtolower(str_replace(" ","",$temp[0]));
								if(strtolower(str_replace(" ","",$temp[1]))!="")
									$password .= ".".strtolower(str_replace(" ","",$temp[1]));*/
								$password  = strtolower($user_row['userName']);
//								$query = "UPDATE adepts_userDetails SET password=password('".$password."') WHERE userID=".$user_row['userID'];
                                                                $query = "UPDATE educatio_educat.common_user_details SET password=password('".$password."') WHERE MS_userID=".$user_row['userID'];
								$result = mysql_query($query) or die("Query failed : " . mysql_error());
								$emailID = $user_row['childEmail'];
								if ($emailID!="")
								{
									mailPassword($emailID, $password, $user_row['userName'], $childName);
								}
								echo "<script>alert('Your password has been reset successfully.');window.location.href='/mindspark/login/index.php';</script>";
								$flag = 1;
							}
							else
							{
								$flag = 7;
							}
						}
						else
						{
							$flag = 6;
						}
					}
					else
						$flag = 2;
				}
				else
					$flag=3;
			}
		}
		

//updated by Jigar

		if(isset($_POST['writetous']))
		{	

			
			$query="SELECT userID, childEmail, childDob, childName, userName, secretQues, secretAns , childClass, childSection, schoolCode FROM adepts_userDetails WHERE username='".$_POST['username']."'";
			$result = mysql_query($query) or die("Query failed : " . mysql_error());			

			if(mysql_num_rows($result)==1)
			{
				$user_row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				$schoolCode = $user_row['schoolCode'];
				$childClass = $user_row['childClass'];
				$childsection = $user_row['childSection'];
				$emailID = $user_row['childEmail'];
				
				if ($schoolCode == "")
				{
					$password = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 7)), 0, 7);
					$query = "UPDATE educatio_educat.common_user_details SET password=password('".$password."') WHERE MS_userID=".$user_row['userID'];
					$result = mysql_query($query) or die("Query failed : " . mysql_error());
					mailPassword($emailID, $password, $user_row['userName'], $user_row['childName']);
					echo "<script>alert('Your password has been Reset successfully. Email sent to  ".$emailID." ');window.location.href='/mindspark/login/index.php';</script>";
				}
				else
				{
					$subquery = "select userID from adepts_userDetails WHERE schoolCode=".$schoolCode." and category = 'teacher'";
					$result = mysql_query($subquery) or die("Query failed : " . mysql_error());
					if(mysql_num_rows($result)==1)
					{
						$singleteacher = mysql_fetch_array($result, MYSQL_ASSOC);
						$teacheruserid = $singleteacher['userID'];
					}
					else
					{
						$userstring = '(';
						while($row = mysql_fetch_array($result))
						{
							$userstring .= $row['userID'].",";
						}
						$userstring = rtrim($userstring, ",");
						$userstring .= ')';

						$subquery = "select userID from adepts_teacherClassMapping where userID IN ".$userstring." and class = ".$childClass." and section = '".$childsection."'";
						$result = mysql_query($subquery) or die("Query failed : " . mysql_error());
						$userwithschool = mysql_fetch_array($result, MYSQL_ASSOC);
						$teacheruserid = $userwithschool['userID'];
					}

					// category = 1 , for forgot password change Request

					$sql=mysql_query("insert into adepts_forgetPassNotification (childUserID, teacherUserID, category, status, requestDate, lastModified) values (".$user_row['userID'].",'".$teacheruserid."','1','1',NOW(),NOW())");
					if($sql)
					{
						echo "<script>alert('Notification sent to Teacher for update your password');window.location.href='/mindspark/login/index.php';</script>";
					}
				}
			}
		}
// Jigar update end
?>

<?php include("header.php");?>
<title>Login</title>

<link href="css/login/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/generic/midClass.css" />
<script>
var langType = 'en';
</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>

<link rel="stylesheet" type="text/css" href="css/CalendarControl.css" >

<script type="text/javascript" src="libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>

<script language"javascript">
				function validateDate(field)
				{
				 val = field;
					if(val != ""){
						date = val.split("-");
						if(!isNaN(date[0]) && !isNaN(date[1]) && !isNaN(date[2])){
							if(!((date[2] > 1900 && date[2] < 2100) && (date[1] > 0 && date[1] < 13))){              					return 0;//false
							}
							return 1;
						}
						else{
							return 0;//false
						}        
					}              
					
				}

				function saveSubmit(mode)
				{
					birthdate = document.getElementById("birthdate").value;
					//isDOBvalida = validateDate(birthdate)
									
					if(mode=="Cancel")	{
						document.frmResetPwd.action = "/mindspark/login/index.php";
						return true;
					}
					else
					{ 
						if(document.frmResetPwd.username.value == "")
						{
							alert("Please specify the username.");
							return false;
						}
						if(document.getElementById("birthdate").value=="")
						{
							alert("Please enter birthdate in dd-mm-yyyy format.");
							return false;
						}
						else if(validateDate(document.getElementById("birthdate").value)==0)
						{
							alert("Please enter valid birthdate in dd-mm-yyyy format.");
							return false;
						}
						
						if(document.getElementById("secretQuestion").value=="")
						{
							alert("Please select secret question.");
							return false;
						}
						if (document.getElementById("secretAnswer").value=="")
						{
							alert("Please enter secret answer.");
							return false;
						}
						document.frmResetPwd.resetPassword.value="Reset Password";
						return true;
					}
				}
				$("input[type=text]").live("keydown",function(e){
					if($(this).attr("id")=="birthdate"){
					e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
					//alert(e.keyCode);
					if(e.keyCode == 13) {
						//alert($(this).attr('id'));
						var value = $(this).val();
						if (value == "") {
							//alert("null value checking");
							showPrompt(miscArr['misc103']);
						}
						else {
							checkAnswer(value);
						}					
						return false;
					}
					else {
						if((e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode!=45 && e.keyCode!=46 && e.keyCode!=189) {
							return false;
						}
					}
				}
				if($(this).attr("id")=="p1"){
					if(e.keyCode == 9) {
						$("#birthdate").focus();
					}
				}
			});
			var isShift=false;
var seperator = "-";
var k;
			
			function DateFormat(txt , keyCode)
{
    if(keyCode==16)
        isShift = true;
    //Validate that its Numeric
    if(((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
         keyCode <= 37 || keyCode <= 39 ||
         (keyCode >= 96 && keyCode <= 105)) && isShift == false && txt.value.length<10)
    {
        if ((txt.value.length == 2 || txt.value.length==5) && keyCode != 8)
        {
            txt.value += seperator;
        }
        return true;
    }
    else
    {
        return false;
    }
}	
				</script>
</head>

<body>

<div id="header">
	<div id="eiColors">
    	<div id="orange"></div>
        <div id="yellow"></div>
        <div id="blue"></div>
        <div id="green"></div>
    </div>
</div>

<div id="continer">
	<div id="leftDiv">
    	<div id="logo"></div>
        <div id="forgotPasswordDiv">
        	<form name= "frmResetPwd" id="frmResetPwd" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
                <div id="pnlContainer">
                    <div id="formContainer">
                            <div class="message">
                                <br/>
                                <span data-i18n="getBirthDate.userName">Username</span>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="username" id="p1" tabindex="1" size="30" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>">
                                <?php if(isset($flag) && $flag==3) echo '&nbsp;&nbsp;<span class="red">Invalid username!</span><br>'; ?>
                            </div>
												
                            <span data-i18n="getBirthDate.DoB">Birthdate</span>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="birthdate" id="birthdate" onFocus="showCalendarControl(this,'<?=$childClass?>');" size="10" onKeyUp="showCalendarControl(this);" onkeydown="return DateFormat(this, event.keyCode)"  onBlur="validateDate(this);"> (dd-mm-yyyy) <?php if(isset($flag) && $flag==2)	echo '<span class="red">Birthdate doesnot match!</span>';?>
                            <br/>
                            <br/>
                            <span data-i18n="getBirthDate.secretQuestion">Secret Question</span>:&nbsp;
                            <select name="secretQuestion" id="secretQuestion">
                                <option value="">Select</option>
                                <?php
                                    for($i=0; $i<count($question_array); $i++)
                                    {
                                        echo "<option value=\"$question_array[$i]\">$question_array[$i]</option>";
                                    }
                                ?>
                            </select>
                            <?php if(isset($flag) && $flag==5) echo '&nbsp;&nbsp;<span class="red" id="spnErrOld">Please select the correct secret question!</span>';
								  else if(isset($flag) && $flag==6)	echo '&nbsp;&nbsp;<span class="red" id="spnErrOld">Selected secret question does not match!</span>'; ?>
                            <br/><br/>
                            <span data-i18n="getBirthDate.secretAnswer">Secret Answer</span>:&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="secretAnswer" id="secretAnswer" size="20" maxlength="50">
                            <?php if (isset($flag) && $flag==7)	echo '<span class="flashMsg red" id="spnErrOld">Secret answer does not match!</span>'; ?>
                            <br/><br/>
                            <input type="submit" name="continue" class="button1" tabindex="4" value="Reset Password" onclick="return saveSubmit('Reset Password')" class="submit_button">
                            <input type="button" name="continue" class="button1" tabindex="4" value="Cancel" onclick="javascript:window.location.href='/mindspark/login/index.php'" class="submit_button">
                            <input type="hidden" name="resetPassword" value="">
                    </div>
                <?php
				if ($flag==7 || $flag==6 || $flag==2 || $flag ==5) { ?>
				<br>
				<div id='writetous'>
					<span class="flashMsg red" id="spnErrOld">Information provided does not match!</span>
					<?php
						if($requesttype != '')
							$message = "Request Teacher";
						else
							$message = "Write To us";
					?>
					 <input type="submit" name="writetous" class="button1" tabindex="4" value="<?php echo $message; ?>"  class="submit_button">
				</div>
				<?php }  ?>
			</div>
            </form>
        </div>
    </div>
 </div>
<div class="clear"></div>
<?php
	if($flag==1)
		echo "<div style='margin-left:100px;font-size:18px;'>".$paswrodChangeMsg."</div>";
?>
<?php include("footer.php"); 

function mailPassword($emailID, $password, $username, $childName, $ccList="")
{
    if($emailID!="")
    {
    	$subject = "Mindspark - Login Details";
    	$body = "Dear $childName<br/><br/>";
    	$body .= "Your password has been reset, following are your login details: <br/><br/>";

    	$body .= "Username: ".$username."<br/>";
    	$body .= "Password: ".$password;
    	$body .= "<br/><br/>";
    	$body .= "(Password was changed from: ".$_SERVER['REMOTE_ADDR'].")";
    	$body .= "<br/><br/>";
    	$body .= "Mindspark";
		$body = wordwrap($body,70);
    	$headers = "From:<mindspark@ei-india.com>\r\n";
    	/*if($ccList!="")
       	    $headers .= "Cc:$ccList\r\n";*/
    	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    	//echo $headers."<hr/>".$body;

    	//mail($emailID,$subject,$body, $headers);
    	$success=0;
    	$success=mail($emailID,$subject,$body, $headers);
    	if($success)
    	{
    		insert(9,$emailID,"$ccList","","mindspark@ei-india.com","",1);
    	}
    	else
    	{
    		insert(9,$emailID,"$ccList","","mindspark@ei-india.com","",0);
    	}
    }
}
?>



