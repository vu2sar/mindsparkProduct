<?php

function fetchFullName($userName)
{	
	$fullname=" ";

	$query = "SELECT childName FROM adepts_userDetails WHERE username = '".$userName."' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$user_row=mysql_fetch_array($result);
	$fullname = $user_row["childName"];
	
/*	if($fullname == " ")
	{
		$query = "SELECT firstName,lastName FROM old_emp_master WHERE userID = '".$userID."' LIMIT 1 UNION SELECT firstName,lastName FROM old_contract_master WHERE userID = '".$userID."' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$user_row = mysql_fetch_array($result);
		$fullname = $user_row["firstName"]." ".$user_row["lastName"];
	}*/
	return $fullname;
}

function sendKudo($userName, $schoolCode, $childClass, $childSection, $category)
{
	$message = $_POST['hdnMessage'];
	$to = $_POST['hdnTo'];
	$toClass=$_POST['hdnToClass'];
	$toSection=$_POST['hdnToSection'];
	$from = $userName;
	$date = date('Y-m-d');
	$type = $_POST['hdnType'];
	$categoryDropdown=$_POST['hdnCategoryDropdown'];
	if($categoryDropdown<>'teacher')
	{$categoryDropdown='student';}
	//echo 'CATEGORY IS- '.$category."CATEGORY DROPDOWN IS -".$categoryDropdown.'CHILD CLASS IS- '.$toClass.'Section is-'.$toSection;
	//echo "MESSAGE IS".$message."TO IS- ".$to."TYPE IS-".$type." CATEGORY DROPDOWN IS-".$categoryDropdown;
		
	if($to!='' && $from!='' && $message!='' && $type!='')
	{
		$tolist = explode(",",$to);
		foreach ($tolist as $key => $value)
		{	$value = trim($value);
			if($value!='')
			{	
			
				if(($categoryDropdown=='student' && $category=='STUDENT') || ($categoryDropdown=='teacher' && $category=='STUDENT'))
				{$userIdQuery = "SELECT username FROM adepts_userDetails where childName='$value' and schoolCode='$schoolCode' and category='teacher' ";}
				else
				{if($categoryDropdown=='teacher')
				{$userIdQuery = "SELECT username FROM adepts_userDetails where childName='$value' and category='$categoryDropdown' and schoolCode='$schoolCode' ";}
				if($categoryDropdown=='student')
				{$userIdQuery = "SELECT username FROM adepts_userDetails where childName='$value' and childClass=".$toClass." AND childSection='".$toSection."' and schoolCode='$schoolCode' ";}}
				
				$result = mysql_query($userIdQuery) or die('Select HOLA Query Failed: '.mysql_error().$userIdQuery);
				$userIdRow = mysql_fetch_array($result);
				$userId = $userIdRow['username'];
				//echo $userIdQuery." ".$userId;
				if($userId!='')
				{
					$query = "INSERT INTO kudosMaster SET 
							receiver = '$userId',
							sender = '$from',
							kudo_type = '$type',
							message = '$message',
							sent_date = '$date',
							schoolCode = '$schoolCode',
							senderClass = '$childClass',
							senderSection = '$childSection',
							view = 1";
					
					$result = mysql_query($query) or die('Insert Query Failed: '.mysql_error());
					
					$kudo_id = mysql_insert_id();
					
					//if(checkIfMailExists($userId)=="YESPARENT" || checkIfMailExists($userId)=="YESCHILD" || checkIfMailExists($userId)=="YESBOTH")
					//{ sendMail($kudo_id); }	
					
					/*$query2 = "INSERT INTO adepts_userFeeds SET 
							receiver = '$userId',
							sender = '$from',
							kudo_type = '$type',
							message = '$message',
							sent_date = '$date',
							schoolCode = '$schoolCode',
							childClass = '$childClass',
							childSection = '$childSection',
							view = 1";
							
					INSERT INTO adepts_userFeeds
					(`userID`,
					`childName`,
					`childClass`,
					`schoolCode`,
					`actID`,
					`actDesc`,
					`score`,
					`timeTaken`,
					`srno`,
					`ftype`,
					`lastModified`)
					VALUES
					($userId,
					 $childName,
					 $childClass,
					 $schoolCode,
					 $kudo_id,
					 'Kudos',
					  0,
					 'Kudos',
					<{srno: }>,
					<{ftype: }>,
					<{lastModified: CURRENT_TIMESTAMP}>);
					
					$result2 = mysql_query($query2) or die('Insert Query Failed: '.mysql_error());
					*/
					
				}
			}
		}
	}		
}

function sendKudoNewInterface($userName, $schoolCode, $childClass, $childSection, $category)
{
	$message = addslashes($_POST['hdnMessage']);
	if(get_magic_quotes_gpc())
		$message = stripslashes($message);
	set_magic_quotes_runtime(false);
	$to = $_POST['hdnTo'];
	// $toClass=$_POST['hdnToClass'];
	// $toSection=$_POST['hdnToSection'];
	$from = $userName;
	$date = date('Y-m-d');
	$type = $_POST['hdnType'];
	$categoryDropdown=$_POST['hdnCategoryDropdown'];
	if($categoryDropdown<>'teacher')
	{$categoryDropdown='student';}
	//echo 'CATEGORY IS- '.$category."CATEGORY DROPDOWN IS -".$categoryDropdown.'CHILD CLASS IS- '.$toClass.'Section is-'.$toSection;
	//echo "MESSAGE IS".$message."TO IS- ".$to."TYPE IS-".$type." CATEGORY DROPDOWN IS-".$categoryDropdown;
		
	if($to!='' && $from!='' && $message!='' && $type!='')
	{
		$tolist = explode(",",$to);
		foreach ($tolist as $key => $value)
		{	
			$value = trim($value);
			if($value!='')
			{	
				$userIdQuery = "SELECT username FROM adepts_userDetails where userID='$value'";
				$result = mysql_query($userIdQuery) or die('Select HOLA Query Failed: '.mysql_error().$userIdQuery);
				$userIdRow = mysql_fetch_array($result);
				$userId = $userIdRow['username'];
				if($userId!='')
				{
					$query = "INSERT INTO kudosMaster SET 
							receiver = '$userId',
							sender = '$from',
							kudo_type = '$type',
							message = '".mysql_real_escape_string($message)."',
							sent_date = '$date',
							schoolCode = '$schoolCode',
							senderClass = '$childClass',
							senderSection = '$childSection',
							view = 1";
					$result = mysql_query($query) or die('Insert Query Failed: '.mysql_error());
					
					$kudo_id = mysql_insert_id();
					
					//if(checkIfMailExists($userId)=="YESPARENT" || checkIfMailExists($userId)=="YESCHILD" || checkIfMailExists($userId)=="YESBOTH")
					//{ sendMail($kudo_id); }	
					
					/*$query2 = "INSERT INTO adepts_userFeeds SET 
							receiver = '$userId',
							sender = '$from',
							kudo_type = '$type',
							message = '$message',
							sent_date = '$date',
							schoolCode = '$schoolCode',
							childClass = '$childClass',
							childSection = '$childSection',
							view = 1";
							
					INSERT INTO adepts_userFeeds
					(`userID`,
					`childName`,
					`childClass`,
					`schoolCode`,
					`actID`,
					`actDesc`,
					`score`,
					`timeTaken`,
					`srno`,
					`ftype`,
					`lastModified`)
					VALUES
					($userId,
					 $childName,
					 $childClass,
					 $schoolCode,
					 $kudo_id,
					 'Kudos',
					  0,
					 'Kudos',
					<{srno: }>,
					<{ftype: }>,
					<{lastModified: CURRENT_TIMESTAMP}>);
					
					$result2 = mysql_query($query2) or die('Insert Query Failed: '.mysql_error());
					*/
					
				}
			}
		}
	}	
	header("Location: kudosHomeTeacherInterface.php");
}

function sendMail($kudo_id)
{
	$query = 'SELECT sender, receiver, message, kudo_type, sent_date FROM kudosMaster WHERE kudo_id = '.$kudo_id.' LIMIT 1';
	$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$from = $row['sender'];
		$type = $row['kudo_type'];
		$to = $row['receiver'];
		$sent_date = $row['sent_date'];
		$messagePer = $row['message'];
	}
	
	$checkIfMailExists=checkIfMailExists($to);
	
	if($checkIfMailExists=="YESPARENT" || $checkIfMailExists=="YESBOTH"){
	$parentEmail = getParentEmail($to);}
     
	if($checkIfMailExists=="YESCHILD" || $checkIfMailExists=="YESBOTH"){
	$recEmail = getMailAddress($to);}
	
	$recName = fetchFullName($to);
	$sendEmail = getMailAddress($from);
	$sendName = fetchFullName($from);
	
	//generateCertificate($kudo_id);
	if($type == 'Thank You')
	{
		$backColor = '#FCD210';
	}
	elseif($type == 'Good Work')
	{
		$backColor = '#FF9146';
	}
	elseif($type == 'Impressive')
	{
		$backColor = '#B1DE5D';
	}
	elseif($type == 'Exceptional')
	{
		$backColor = '#36A9E1';
	}
	
	$message = "Dear $recName,<br/><br/>";
	
	$message .= "I would like to award you <b>$type</b> Kudos. <br/><br/>";
	
	$message .= '<table border="0" cellpadding="0" cellspacing="0" width="90%" bgcolor="#bcbcbc" background="http://www.educationalinitiatives.com/kudos/images/background/bg.png" style="padding: 10px;">
					<tr>
						<td valign="bottom">
							<img src="http://www.educationalinitiatives.com/kudos/images/ei_logo.png" height="100px" width="100px"/>
						</td>
						<td colspan="2" rowspan="2" align="right" valign="bottom">
							<img src="http://www.educationalinitiatives.com/kudos/images/ei_icon.png" height="150px" width="150px"/>		
						</td>
					</tr>
					<tr>
						<td>
							<font face="helvetica, sans serif" color="#4d4d4d" size="10px">'.$type.'</font>
						</td>
					</tr>
					<tr height="400px">
						<td colspan="3" bgcolor="'.$backColor.'" align="center" style="-webkit-border-radius: 60px;
			-moz-border-radius: 60px;
			border-radius: 60px;">
							<font face="helvetica, sans serif" color="#ffffff" size="5px">
								This certificate of <b>'.$type.'</b> is awarded to
								<br/>
								'.$recName.'
								<br/>
								for
								<br/>
								'.$messagePer.'
							</font>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="right">
							<font face="helvetica, sans serif" color="#4d4d4d" size="4px">
								<b>'.$sendName.'</b>
							</font>
							<br/>
							<font face="helvetica, sans serif" color="#4d4d4d" size="3px">
								'.date('d F, Y',strtotime($sent_date)).'
							</font>
						</td>
					</tr>
				</table>';
				
	$message .= "<br/><br/>Regards,<br/>$sendName";
	
	$headers = "From:".$sendEmail."\r\n";
			
	$headers .= "Cc:".$parentEmail."\r\n";
				
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	
	$subject = $recName.' just received a kudos from '.$sendName;
	
	/*echo '<pre>';
	print_r($message);	
	echo "<br/>headers: ".$headers;
	echo "<br/>subject: ".$subject;
	echo "<br/>to: ".$recEmail;
	echo "<br/>mgr mgr: ".$recMgr;
	echo "<br/>mgr email: ".$recMgrEmail;
	echo '</pre>';*/
	
	//mail($parentEmail, $subject, $message, $headers);
	//echo "MESSAGE IS - <br>".$message;

	/*if(mail($recEmail, $subject, $message, $headers))
	{
		echo "<pre>Mail Sent</pre>";
	}	
	else
	{
		echo "<pre>Mail Sending Failed</pre>";
	}*/             
}

function kudosModal($kudo_id)
{
	
	$query = 'SELECT sender, receiver, message, kudo_type, sent_date FROM kudosMaster WHERE kudo_id = '.$kudo_id.' LIMIT 1';
	$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$from = $row['sender'];
		$type = $row['kudo_type'];
		$to = $row['receiver'];
		$sent_date = $row['sent_date'];
		$messagePer = $row['message'];
	}
	
	if($type == 'Thank You')
	{
		$backColor = '#FCD210';
		$image= 'thankyou';
	}
	elseif($type == 'Good Work')
	{
		$backColor = '#FF9146';
		$image= 'goodwork';
	}
	elseif($type == 'Impressive')
	{
		$backColor = '#B1DE5D';
		$image= 'impressive';
	}
	elseif($type == 'Exceptional')
	{
		$backColor = '#36A9E1';
		$image= 'exceptional';
	}
	
	$recName = fetchFullName($to);
	$sendName = fetchFullName($from);
		
	$message = '<table border="0" cellpadding="0" cellspacing="0" bgcolor="#bcbcbc" height="513px" width="1000px" background="http://www.educationalinitiatives.com/kudos/images/background/bg.png" style="padding: 10px;">
					<tr>
						<td valign="bottom">
							<img src="http://www.educationalinitiatives.com/kudos/images/ei_logo.png" height="100px" width="100px"/>
						</td>
						<td colspan="2" rowspan="2" align="right" valign="bottom">
							<img src="http://www.educationalinitiatives.com/kudos/images/ei_icon.png" height="150px" width="150px"/>		
						</td>
					</tr>
					<tr>
						<td>
							<font face="helvetica, sans serif" color="#4d4d4d" size="10px">'.$type.'</font>
						</td>
					</tr>
					<tr height="400px">
						<td colspan="3" bgcolor="'.$backColor.'" align="center" style="-webkit-border-radius: 60px;
			-moz-border-radius: 60px;
			border-radius: 60px;">
							<table align="center"><tr align="center" >
							<td align="center"><img style="alignment-adjust:center;" src="images/'.$image.'.png"/></td>
							</tr><tr>
							<td align="center"><font face="helvetica, sans serif" color="#ffffff" size="5px">
								This certificate of <b>'.$type.'</b> is awarded to
								<br/>
								'.$recName.'
								<br/>
								for
								<br/>
								'.$messagePer.'
							</font></td></tr></table>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="right">
							<font face="helvetica, sans serif" color="#4d4d4d" size="4px">
								<b>'.$sendName.'</b>
							</font>
							<br/>
							<font face="helvetica, sans serif" color="#4d4d4d" size="3px">
								'.date('d F, Y',strtotime($sent_date)).'
							</font>
						</td>
					</tr>
				</table>';
				
				
				return $message;
					
}


function checkIfMailExists($userId)
{
	$emailExists = 'NO';	
	
	$query = "SELECT childEmail, parentEmail FROM adepts_userDetails WHERE userID = '".$userID."' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_array($result))
	{
		$childEmail = $row["childEmail"];
		$parentEmail= $row["parentEmail"];
	}
	if (strpos($parentEmail,'@') != false && strpos($childEmail,'@') != false) 
	{
		$emailExists="YESBOTH";
		return $emailExists;
	}
	else if (strpos($childEmail,'@') != false) 
	{
    	$emailExists="YESCHILD";
		return $emailExists;
	}
	
	else if (strpos($parentEmail,'@') != false) 
	{
		$emailExists="YESPARENT";
		return $emailExists;
	}
	else
	{
		return $emailExists;
	
	}
	
}

function getMailAddress($userName)
{
	$email = '';	
	$query = "SELECT childEmail FROM adepts_userDetails WHERE userID = '".$userName."' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_array($result))
	{
		$email = $row["childEmail"];
	}
	
	return $email;
}

function getParentEmail($userName)
{
	$parentEmail = '';	
	$query = "SELECT parentEmail FROM adepts_userDetails WHERE username = '".$userName."' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	while($row=mysql_fetch_array($result))
	{
		$parentEmail = $row["parentEmail"];
	}
	return $parentEmail;
}

function getGender($receiverUserName)
{	
	$gender = '';	
	$query = "SELECT gender FROM adepts_userDetails WHERE username = '".$receiverUserName."' LIMIT 1";	
	$result = mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($result)>0)
	{
		while($user_row=mysql_fetch_array($result))
		{
			$gender = $user_row["gender"];
		}
	}
	
	/*else
	{
		$query = "SELECT IF(title='Ms.' OR title='Ms', 'Female', 'Male') AS gender FROM contract_master WHERE userID = '".$userID."' LIMIT 1";
		$query .= " UNION SELECT IF(title='Ms.' OR title='Ms', 'Female', 'Male') AS gender FROM old_contract_master WHERE userID = '".$userID."' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		while($user_row=mysql_fetch_array($result))
		{
			$gender = $user_row["gender"];
		}
	}*/
	
	return $gender;
}

function getAllKudos($myWall,$schoolCode, $childClass, $childSection, $userName, $category)
{
	$condition = '';
	if($myWall)
	{
		$condition = " WHERE receiver='$userName' AND schoolCode=$schoolCode";
		/*if($category=="STUDENT")
		{$condition.=" AND childClass=$childClass"; }
		*/
		$arrRet = array();
		$query = "SELECT kudo_id, sender, receiver, sent_date, message, kudo_type FROM kudosMaster $condition ORDER BY sent_date DESC, lastModified DESC";		//echo $query."  ".$category;
		$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
		while($row = mysql_fetch_array($result))
		{
			$arrRet[$row['kudo_id']] = array('sender' => $row['sender'],
										'receiver' => $row['receiver'],
										'sent_date' => $row['sent_date'],
										'kudo_type' => $row['kudo_type'],
										'message' => $row['message']);
		}
	}	
	else
	{
		$currMon = date('m');
		$currMonYear = date('Y');
		/*$currMonLastDay = date('t');*/
		$lastMon = $currMon - 1;
		if($currMon == 1)
			$lastMon = 12;
		$lastMonYear = $currMonYear;
		if($currMonYear == 1)
			$lastMonYear = $currMonYear - 1;
		/*$minDate = date('Y-m-d', strtotime("01-$lastMon-$lastMonYear"));
		$maxDate = date('Y-m-d', strtotime("$currMonLastDay-$currMon-$currMonYear"));*/		
		
		$condition = " WHERE DATE_FORMAT(sent_date,'%m%Y') = $currMon$currMonYear AND schoolCode=$schoolCode";
		if($category=="STUDENT")
		{$condition.=" AND receiver IN (SELECT username from adepts_userDetails where childClass=$childClass OR category='teacher') "; }
			
		$arrRet = array();
		$query = "SELECT kudo_id, sender, receiver, sent_date, message, kudo_type FROM kudosMaster $condition ORDER BY sent_date DESC, lastModified DESC LIMIT 16"; //echo"<br>".$query;
		$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
		while($row = mysql_fetch_array($result))
		{
			$arrRet[$row['kudo_id']] = array('sender' => $row['sender'],
										'receiver' => $row['receiver'],
										'sent_date' => $row['sent_date'],
										'kudo_type' => $row['kudo_type'],
										'message' => $row['message']);
		}
		
		$condition = " WHERE DATE_FORMAT(sent_date,'%m%Y') = $lastMon$lastMonYear AND schoolCode=$schoolCode";
		if($category=="STUDENT")
		{$condition.=" AND receiver IN (SELECT username from adepts_userDetails where childClass=$childClass OR category='teacher')";}
				
		$query = "SELECT kudo_id, sender, receiver, sent_date, message, kudo_type FROM kudosMaster $condition ORDER BY sent_date DESC, lastModified DESC LIMIT 16";//echo"<br>".$query;
		$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
		while($row = mysql_fetch_array($result))
		{
			$arrRet[$row['kudo_id']] = array('sender' => $row['sender'],
										'receiver' => $row['receiver'],
										'sent_date' => $row['sent_date'],
										'kudo_type' => $row['kudo_type'],
										'message' => $row['message']);
		}
	}	
	return $arrRet;
}

function getKudosSummary()
{
	$query = "SELECT kudo_id, sender, receiver, sent_date, message, kudo_type FROM kudosMaster ORDER BY kudo_type, sent_date DESC, lastModified DESC";
	$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$arrRet[$row['kudo_id']] = array('sender' => $row['sender'],
									'receiver' => $row['receiver'],
									'sent_date' => $row['sent_date'],
									'kudo_type' => $row['kudo_type'],
									'message' => $row['message']);
	}
	return $arrRet;
}

function checkAdmin($userID)
{
	$admin = FALSE;
	$right = 'HRP';
	$query = "SELECT appRights FROM marketing WHERE name='$userID' LIMIT 1";
	$result = mysql_query($query);
	while($user_row = mysql_fetch_array($result))
	{
		$appRights = explode(",",$user_row['appRights']);
		if(in_array($right,$appRights))
			$admin = TRUE;
	}
	return $admin;
}

function newkudosCounter()
{
	$query="Select count(*) as newKudosCount from kudosMaster where view=1";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		$newKudosCount=$row['newKudosCount'];		
	}
	
	return $newKudosCount;
	
}

function deleteKudo($kudo_id)
{
	$query = "DELETE FROM kudosMaster WHERE kudo_id=".$kudo_id;
	$result = mysql_query($query) or die("Could not delete Kudo");

	if($result)
		return 1;
	else
		return 0;
}
?>