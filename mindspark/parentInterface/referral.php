<?php
@include("../userInterface/dbconf.php");
//include("dbconf.php");
//error_reporting(E_ALL);
//if($_SERVER['DOCUMENT_ROOT'] = "C:/xampp/htdocs"){
//	include("../website/commonfunctions.php");
//	require("../website/mail_functions.php");
//}else{
//	//include("../../commonfunctions.php");
//	//require("../../mail_functions.php");
//	include("/commonfunctions.php");
//	require("/mail_functions.php");	
//}
//for live
include("../website/commonfunctions.php");
require("../website/mail_functions.php");
//for local
//include("../website/commonfunctions.php");
//require("../website/mail_functions.php");
require_once("clsCrypt.php");
$showReferral = TRUE;
$userID = $_SESSION['childIDUsed'];
//echo $userID;
//echo $_SESSION['childIDUsed'];
$type = "R";
//if($userID=='')
//	$userID = $_REQUEST['userID'];

if($userID!='')
{
	//$crypt = new Crypt();
	//$userID = $crypt->decrypt($userID);			
}
else
	$showReferral = FALSE;
//error_reporting(E_ALL);
if(isset($_POST['referralSubmit']))
{
	$showReferral = FALSE;	
	$referralName1 = $_POST['referralName1'];
	$referralEmail1 = $_POST['referralEmail1'];
	$referralName2 = $_POST['referralName2'];
	$referralEmail2 = $_POST['referralEmail2'];
	$referralName3 = $_POST['referralName3'];
	$referralEmail3 = $_POST['referralEmail3'];
	$referralName4 = $_POST['referralName4'];
	$referralEmail4 = $_POST['referralEmail4'];
	$referralName5 = $_POST['referralName5'];
	$referralEmail5 = $_POST['referralEmail5'];	
	if($userID!='')
	{
		if($type=='N' || (strtolower($_SESSION['childSubcategory'])=="school") || $_SESSION['childFreeTrial']==1)
		{
		if($referralEmail1!='')
		{
			sendReferralWOReferenceCode($referralEmail1,$referralName1,$userID);
		}	
		if($referralEmail2!='')
		{
			sendReferralWOReferenceCode($referralEmail2,$referralName2,$userID);
		}
		if($referralEmail3!='')
		{
			sendReferralWOReferenceCode($referralEmail3,$referralName3,$userID);
		}
		if($referralEmail4!='')
		{
			sendReferralWOReferenceCode($referralEmail4,$referralName4,$userID);
		}
		if($referralEmail5!='')
		{
			sendReferralWOReferenceCode($referralEmail5,$referralName5,$userID);
		}	
		}
		else
		{			
		$referenceCode = getReferenceCode($userID);
		if($referralEmail1!='')
		{
			sendReferral($referralEmail1,$referralName1,$userID,$referenceCode);
		}	
		if($referralEmail2!='')
		{
			sendReferral($referralEmail2,$referralName2,$userID,$referenceCode);
		}
		if($referralEmail3!='')
		{
			sendReferral($referralEmail3,$referralName3,$userID,$referenceCode);
		}
		if($referralEmail4!='')
		{
			sendReferral($referralEmail4,$referralName4,$userID,$referenceCode);
		}
		if($referralEmail5!='')
		{
			sendReferral($referralEmail5,$referralName5,$userID,$referenceCode);
		}
		}
		
	}	
}

function sendReferral($emailReferred, $nameReferred, $userID,$referenceCode)
{	
	/*}*/	
	//$query = "INSERT INTO adepts_referral (name, emailReferred, referrerUserID, status, referenceCode,dateGenerated) VALUES ('$nameReferred','$emailReferred',$userID,'Pending',$referenceCode,curdate());";	
	//mysql_query($query) or die(mysql_error());
	$query = "SELECT parentName, endDate, type from adepts_userDetails WHERE userID=$userID;";
	$result = mysql_query($query);
	if($line   = mysql_fetch_array($result))
	{
		$parentName 	= $line['parentName'];
		$endDate		= $line['endDate'];
		$type			= $line['type'];
	}	
	sendReferralMail($nameReferred,$emailReferred, $parentName, $endDate, $referenceCode, $type);
	$referralInfoQuery = "INSERT INTO adepts_changeLog SET tableChanged='reference', identifier=$userID,changeComment='$nameReferred~$emailReferred~$referenceCode',modifiedBy='System';";
	/*$referralInfoQuery = "INSERT INTO adepts_referralInfo SET nameReferred='$nameReferred',emailReferred='$emailReferred',referrerUserID=$userID;";*/
	mysql_query($referralInfoQuery) or die(mysql_error());
}
function sendReferralWOReferenceCode($emailReferred, $nameReferred, $userID)
{	
	$query = "SELECT parentName from adepts_userDetails WHERE userID=$userID;";
	$result = mysql_query($query);
	if($line   = mysql_fetch_array($result))
	{
		$parentName 	= $line['parentName'];		
	}	
	sendReferralMailWOReferenceCode($nameReferred,$emailReferred,$parentName);
	$referenceCode = 0;
	$referralInfoQuery = "INSERT INTO adepts_changeLog SET tableChanged='reference', identifier=$userID,changeComment='$nameReferred~$emailReferred~$referenceCode',modifiedBy='System';";
	mysql_query($referralInfoQuery) or die(mysql_error());
}
function getReferenceCode($userID)
{
	$queryReference = "SELECT referenceCode from adepts_userDetails where userID=$userID;";
	$no_of_referrals = mysql_query($queryReference) or die(mysql_error().$queryReference);
	$no_of_referrals = mysql_fetch_array($no_of_referrals);
	if ($no_of_referrals[0]>0)
	{
		return $no_of_referrals['referenceCode'];
	}
	else
	{
		$digits = 5;
		$referenceCode = rand(pow(10, $digits-1), pow(10, $digits)-1);
		$uniqueReferenceCode = FALSE;
		if(checkDuplicateReferenceCode($referenceCode,$userID))
			$uniqueReferenceCode = TRUE;
		else
			{
				$referenceCode = rand(pow(10, $digits-1), pow(10, $digits)-1);
			}
		$queryReference = "UPDATE adepts_userDetails SET referenceCode=$referenceCode WHERE userID=$userID";
		//$queryReference = "INSERT INTO adepts_referral (name, emailReferred, referrerUserID, status, referenceCode,dateGenerated) VALUES ('','mindspark@ei-india.com',$userID,'NULL',$referenceCode,curdate());";	
		mysql_query($queryReference) or die(mysql_error());
		return $referenceCode;
	}		
}

function checkDuplicateReferenceCode($referenceCode,$userID)
{
	$queryReference = "(SELECT referenceCode from adepts_userDetails where referenceCode=$referenceCode and userID!=$userID) UNION (SELECT schoolno from educatio_educat.schools where schoolno=$referenceCode);";
	$no_of_referrals = mysql_query($queryReference);
	$no_of_referrals = mysql_fetch_array($no_of_referrals);
	if (count($no_of_referrals)>0 && $no_of_referrals[0]>0)
	{
		return FALSE;		
	}
	else
	{
		return TRUE;
	}		
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Mindspark!</title>
<link href="images/main.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stylesheet.css" rel="stylesheet" type="text/css">
<style type="text/css">
.tblData td {border: 1px solid #000000;}
<!--
body {
	background-color: #FFFFFF;
}
.style2 {font-style: italic}
-->
</style>
</head>
<body>
<table class="bg_img" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" colspan="3">
    	 <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
             <td colspan="3"></td>
          </tr>
          <tr>
		      <td width="50px">&nbsp;</td>
			  <td width="95%" valign="middle" align="center">
		      </td>
		     <td width="4%">&nbsp;</td>
		    </tr>
        </table>
     </td>
  </tr>
  <tr>
  	<td valign="top" width="50px">&nbsp;</td>
    <td valign="top" style="font-size:1.2em;">
	<div <?php if($showReferral) echo 'style="display:none;"'; ?>>
	<br/><br/><br/>
        	<h3><span style="margin-bottom: 200px;">Thank you for sharing the e-mail address of your friends who, you think, would show interest in and benefit from Mindspark.</span></h3>
			<?php if(!($type=='N' || (strtolower($_SESSION['childSubcategory'])=="school") || $_SESSION['childFreeTrial']==1)) { ?> <i>Note: You enjoy free Mindspark usage(as referral bonus) <u>only</u> if your friend registers/friends register while your account is active.</i> <?php } ?>
			<br/><br/><br/><br/><br/><br/>
		</div>	
		<div style="float: left;<?php if(!$showReferral) echo 'display: none;'; ?>">
									<div style="float: left; margin-top: 20px; margin-bottom: 110px;margin-right: 50px;">
									<form id="referralForm" method="POST">
									<input type="hidden" name="userID" id="inputUserID" value="<?= $userID ?>"/>
									<table >
									<thead>
										<tr>
											<th align="left" ><span style="margin-left: 5px;">Name</span></th>
											<th align="left" ><span style="margin-left: 5px;">Email ID</span></th>
										</tr>
									</thead>
										<tr>
											<td >
												<input type="text" name="referralName1" id="referralName1"/>
											</td>
											<td >
												<input type="text" name="referralEmail1" id="referralEmail1"/>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" name="referralName2" id="referralName2"/>
											</td>
											<td>
												<input type="text" name="referralEmail2" id="referralEmail2"/>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" name="referralName3" id="referralName3"/>
											</td>
											<td>
												<input type="text" name="referralEmail3" id="referralEmail3"/>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" name="referralName4" id="referralName4"/>
											</td>
											<td>
												<input type="text" name="referralEmail4" id="referralEmail4"/>
											</td>
										</tr>
										<tr>
											<td>
												<input type="text" name="referralName5" id="referralName5"/>
											</td>
											<td>
												<input type="text" name="referralEmail5" id="referralEmail5"/>
											</td>
										</tr>
									</table>
									<br/>
									<div align="center" ><input style="vertical-align: center" type="submit" name="referralSubmit" id="referralSubmit" value="Submit" onClick="javascript:return validate();"/></div>
									</form>
									</div>
									<div style="margin-left: 10px;">
									<h2>Refer a Friend</h2>
									Would you recommend Mindspark to your friends? If yes, please fill out the form. Team Mindspark will contact them. For every referral registration you benefit as stated in the table below:<br /><br />
									<table border=1 cellspacing=0 cellpadding=3 width="50%" align="center">
										<thead>
											<tr>
												<th>For every friend who registers</th>
												<th>You get a Mindspark extension of</th>
											</tr>
										</thead>
										<tr>
											<td>3 month subscription</td>
											<td>1 week</td>
										</tr>
										<tr>
											<td>6 month subscription</td>
											<td>15 days</td>
										</tr>
										<tr>
											<td>1 year subscription</td>
											<td>1 month</td>
										</tr>
									</table><br />
									<strong>Note: Your Mindspark subscription must be active when your friend registers/friends register for you to enjoy the benefit.</strong><br /><br />
									<!--<span style="font-size: smaller;"><i>These details will be saved in our database and maintained in confidence.</i></span>-->
									</div>
								</div>
        </td>
        <td width="20%" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td height="4" colspan="3"></td>
      </tr>
</table>
<script type="text/javascript">
function echeck(str) {
		var at="@";
		var dot=".";
		var lat=str.indexOf(at);
		var lstr=str.length;
		var ldot=str.indexOf(dot);
		if (str.indexOf(at)==-1){
		   return false;
		}
		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   return false;
		}
		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    return false;
		}
		 if (str.indexOf(at,(lat+1))!=-1){
		    return false;
		 }
		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    return false;
		 }
		 if (str.indexOf(dot,(lat+2))==-1){
		    return false;
		 }
		 if (str.indexOf(" ")!=-1){
		    return false;
		 }
 		 return true;
	}
function trim(str) {
		// Strip leading and trailing white-space
		return str.replace(/^\s*|\s*$/g, "");
	}
	function validate()
{
	var errmsg ='';
	var referralName1  = trim(document.getElementById('referralName1').value);	
	var referralName2  = trim(document.getElementById('referralName2').value);	
	var referralName3  = trim(document.getElementById('referralName3').value);	
	var referralName4  = trim(document.getElementById('referralName4').value);	
	var referralName5  = trim(document.getElementById('referralName5').value);	
	
	var referralEmail1  = trim(document.getElementById('referralEmail1').value);
	var referralEmail2  = trim(document.getElementById('referralEmail2').value);
	var referralEmail3  = trim(document.getElementById('referralEmail3').value);
	var referralEmail4  = trim(document.getElementById('referralEmail4').value);
	var referralEmail5  = trim(document.getElementById('referralEmail5').value);
	
	if(trim(referralEmail1)!='' && echeck(referralEmail1)==false)
	{
		errmsg += 'Please provide valid e-mail address for friend 1.\n';
		if(trim(referralName1)=='')
			errmsg += 'Please provide name of friend 1.\n';
	}
	else if(trim(referralEmail1)=='' && referralName1=='')
		errmsg += 'You have not entered any details.';
	else if(trim(referralEmail1)=='' && referralName1!='')
		errmsg += 'Please provide valid e-mail address for friend 1.\n';
	else if(trim(referralEmail1)!='' && referralName1=='')
		errmsg += 'Please provide name of friend 1.\n';
		
	if(trim(referralEmail2)!='' && echeck(referralEmail2)==false)
	{
		errmsg += 'Please provide valid e-mail address for friend 2.\n';
		if(trim(referralName2)=='')
			errmsg += 'Please provide name of friend 2.\n';
	}
	else if(trim(referralEmail2)!='' && trim(referralName2)=='')
			errmsg += 'Please provide name of friend 2.\n';
		
	if(trim(referralEmail3)!='' && echeck(referralEmail3)==false)
	{
		errmsg += 'Please provide valid e-mail address for friend 3.\n';						
		if(trim(referralName3)=='')
			errmsg += 'Please provide name of friend 3.\n';
	}
	else if(trim(referralEmail3)!='' && trim(referralName3)=='')
			errmsg += 'Please provide name of friend 3.\n';
	
	if(trim(referralEmail4)!='' && echeck(referralEmail4)==false)
	{
		errmsg += 'Please provide valid e-mail address for friend 4.\n';						
		if(trim(referralName4)=='')
			errmsg += 'Please provide name of friend 4.\n';
	}
	else if(trim(referralEmail4)!='' && trim(referralName4)=='')
			errmsg += 'Please provide name of friend 4.\n';
	
	if(trim(referralEmail5)!='' && echeck(referralEmail5)==false)
	{
		errmsg += 'Please provide valid e-mail address for friend 5.\n';	
		if(trim(referralName5)=='')
			errmsg += 'Please provide name of friend 5.\n';
	}
	else if(trim(referralEmail5)!='' && trim(referralName5)=='')
			errmsg += 'Please provide name of friend 5.\n';
						
	if(errmsg!="")
		{
			alert(errmsg);
			return false;
		}
	setTryingToUnload();
}
</script>
</body>
</html>