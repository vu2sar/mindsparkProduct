<?php
	use BLL;
        require_once '/classes/BLL/parentDetails.php';
	//@include("connectivity.php");
//	require("mail_functions.php");
	require_once("clsCrypt.php");
	$emailID = $_GET['emailID'];
	if($emailID=="")
	{
		echo "<center><strong>You are not authorised to access this page.</strong></center>";
		exit;
	}
	$crypt = new Crypt();
	$emailID=  urldecode($emailID);
	$emailID = $crypt->decrypt($emailID);
        $parentDetails = new BLL\ParentDetails();
        $result = $parentDetails->getParentDetailsByEmail($emailID);
        if(count($result)>0)
        {
            $success = $parentDetails->verifyParentUsername($result[0]['username']);            
        }
        
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Mindspark!</title>
<link href="/mindspark/website/images/main.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="stylesheet.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	background-color: #FFFFFF;
}
.style2 {font-style: italic}
-->
</style>
<style>
	.mandatory { font-size: 10px; color:#FF0000;vertical-align: text-top;}
	
</style>
<body>
<table class="bg_img" width="1004" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" colspan="3">
    
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	      <tr>
	        <td colspan="3">
	        	<?php include("logo.php");	?>
	        </td>
	      </tr>      
	      <tr>
	      	<td width="14%">&nbsp;</td>
	        <td width="73%" align="center" valign="middle">		        	
	     		<?php include('menu.php'); ?>
	     	</td>
	     	<td width="13%">&nbsp;</td>
	      </tr>
	    </table>
	 </td>
	 </tr>

	 <tr>
      	<td width="14%">&nbsp;</td>
        <td valign="top">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
	          <tr>
	            <td width="100%" valign="top">&nbsp;</td>            
	          </tr>
          	  <tr>
                <td valign="top" class="middle_text_fundamantals"> 					   
                      <p class="middle_text_fundamantals">
                      <?php if($flag==1)	{	?>
                      Thank you for your subscription!<br><br/>The username and password have been e-mailed to the registered id (<?=$childEmail?>).<br/><br/>
		              Your subscription details are as follows:
		              <table border="0" width="60%" cellspacing="0" style="border: 1px solid #000000;">
		                      		<tr >
		                      			<th style="border-right: 1px solid #000000;">Package</th>
		                      			<th style="border-right: 1px solid #000000;">Start Date</th>
		                      			<th >End Date</th>
		                      		</tr>
		                      		<tr>
		                      			<td align="center" style="border-right: 1px solid #000000;border-top: 1px solid #000000;"><?=$pkgStr?></td>
		                      			<td align="center" style="border-right: 1px solid #000000;border-top: 1px solid #000000;"><?=substr($startDate,8,2)."-".substr($startDate,5,2)."-".substr($startDate,0,4)?></td>
		                      			<td align="center" style="border-top: 1px solid #000000;"><?=substr($endDate,8,2)."-".substr($endDate,5,2)."-".substr($endDate,0,4)?></td>
		                      		</tr>
		                      		
		                      	</table>
		                      	
                       <br/><br/>
                       Hope you have a wonderful Mindspark experience!<br/><br/>
		               For any queries, write to <a href="mailto:mindspark@ei-india.com">mindspark@ei-india.com</a>
		               <br/><br/><br/><br/>
					  <?php	}	else	{	?>							
					  <br/>You have already verified the email id, the details would have been mailed to this registered id.<br/>
					  <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
					  <?php	}	?>
					  </p>
              </td>
          </tr>
        </table>
        </td>
        <td width="15%" valign="top">&nbsp;</td>
      </tr>
      <tr>
        <td height="4" colspan="3"></td>
      </tr>
      <tr>
        <td colspan="3"><?php include("footer.php");	?></td>
      </tr>
    
</table>
</body>

</html>

