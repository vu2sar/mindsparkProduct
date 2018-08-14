<?php
	
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	@include("../check.php");
	
	
	
	checkPermission("ADP");
	$startDate = $endDate = "";
	
	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	/*
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	*/
	
?>
<HTML dir="ltr">
<HEAD>
<TITLE>Mail Repository</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/admin_styles.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" style="text/css" href="../css/CalendarControl.css" >
<script language="javascript" type="text/javascript" src="../js/CalendarControl.js" ></script>
<script type="text/javascript" src="../js/dateValidator.js"></script>
<script type="text/javascript" src="../js/tablesort.js"></script>
</HEAD>
<BODY>
<?php @include("../reports_top_links.php"); ?>
<h3 align="center"><u>Mail Repository</u></h3>
<?php
	$record_count=0;
	$query="select * from adepts_mail where 1";
	
	if($type != "" && $type!= 0)
	{
		$query .= "  and type='".$type."' ";
		$query_string="&type=$type";
	}
	if($sent_to != "")
	{
		$query .= "  and sent_to like '".$sent_to."%' ";
		$query_string="&sent_to=$sent_to";
	}
	if($sent_cc != "")
	{
		$query .= "  and sent_cc  like '".$sent_cc."%' ";
		$query_string="&sent_cc=$sent_cc";
	}
	if($sent_bcc != "")
	{
		$query .= "  and sent_bcc like '".$sent_bcc."%' ";
		$query_string="&sent_bcc=$sent_bcc";
	}
	if($sender != "")
	{
		$query .= "  and sender like '".$sender."'%";
		$query_string="&sender=$sender";
	}
	if($reply_to != "")
	{
		$query .= "  and reply_to like '".$reply_to."%'";
		$query_string="&reply_to=$reply_to";
	}
	if($startDate != "")
	{
		$query .= " and date_format(date,'%d-%m-%Y' ) >= '".$startDate."' and substr(date,1,10) != '0000-00-00' ";
		$query_string="&startDate=$startDate";
	}
	if($endDate != "")
	{
		$query .= " and date_format(date,'%d-%m-%Y' ) <= '".$endDate."' and substr(date,1,10) != '0000-00-00' ";
		$query_string="&endDate=$endDate";
	}
	
	$query .= " order by date desc";
	//echo "query string is ".$query_string;
	$result=mysql_query($query) or die(mysql_error());
	$record_count=mysql_num_rows($result);

?>
	<form name="form1" method="post" action="">
		<table width="70%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
		<tr bgcolor="#FFFFFF">
			<td><strong>Mail Type</strong></td>
			<td>
				<select id="type" name="type">
					<option <?php if($type == 0 ) echo "selected"; ?> value='0'>Select</option>
					<option <?php if($type == 1 ) echo "selected"; ?> value="1">Welcome - verification -  only child</option>
					<option <?php if($type == 2 ) echo "selected"; ?> value="2">Renewal - both</option>
					<option <?php if($type == 3 ) echo "selected"; ?> value="3">Welcome - only parent</option>
					<option <?php if($type == 4 ) echo "selected"; ?> value="4">Change username by child (both)</option>
					<option <?php if($type == 5 ) echo "selected"; ?> value="5">Referral - only child</option>
					<option <?php if($type == 6 ) echo "selected"; ?> value="6">Qualified referral extension - only  ohild</option>
					<option <?php if($type == 7 ) echo "selected"; ?> value="7">Subscription expiry mail - (either of them)</option>
					<option <?php if($type == 8 ) echo "selected"; ?> value="8">NLI Reminder - (both)</option>
					
				</select>
			</td>
			<td><strong>Sent To</strong></td>
			<td><input type="text" id="sent_to" name="sent_to" value=<?=$sent_to;?>></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td><strong>Sent CC</strong></td>
			<td><input type="text" id="sent_cc" name="sent_cc" value=<?=$sent_cc;?>></td>
			<td><strong>Sent Bcc</strong></td>
			<td><input type="text" id="sent_bcc" name="sent_bcc" value=<?=$sent_bcc;?>></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td><strong>Sender</strong></td>
			<td><input type="text" id="sender" name="sender" value=<?=$sender;?>></td>
			<td><strong>Reply To</strong></td>
			<td><input type="text" id="reply_to" name="reply_to" value=<?=$reply_to;?>></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td><strong>From Date:</strong></td>
			<!--<td><script>DateInput('startDate',false,'YYYY-MM-DD'<?php if($startDate!="") echo ", '$startDate'" ?>)</script></td>-->
			<td><input type="text"  name="startDate" id="startDate" onFocus="showCalendarControl(this);" size="10" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);"   value="<?php if($startDate!="") echo $startDate; ?>"></td>
			<td><strong>Till Date:</strong></td>
			<!--<td><script>DateInput('endDate',false,'YYYY-MM-DD'<?php if($endDate!="") echo ", '$endDate'" ?>)</script></td>-->
			<td><input type="text" name="endDate" id="endDate" onFocus="showCalendarControl(this);" size="10" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);"   value="<?php if($startDate!="") echo $endDate; ?>"></td>
		</tr>
		<tr bgcolor="#FFFFFF">
			<td colspan=4 align="center"><input type="submit" name="submit" value="Search"></td>
			
		</tr>
		</table>
	</form>	

	<?php
	// pageing funda
	include_once("simple_paging.php");
	$paging=new CPaging(null,20,$query);
	$paging->link='http://programserver/mindspark/mail/mail.php';
	$paging->link.='?';
	$paging->link.=$query_string;
	$sql_query=$paging->get_limited_query();
	$show=$paging->show();//shows navigation bar
	$sql_query_result=mysql_query($sql_query);
	
	echo "<br><div align=center>$show</div><br>";
	?>

	<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
	<thead>
		<tr bgcolor="#FFFFFF">
	
			<td width="5%" >SR. No.</td>
			<td width="20%" class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Mail Type</u></td>
			<td width="20%" class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Sent To</u></td>
			<td width="20%" class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Sent CC</u></td>
			<td width="20%" class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Sent Date Time</u></td>
			<td width="5%"  class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Sent Bcc</u></td>
			<td width="5%"  class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Sender</u></td>
			<td width="5%"  class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Reply-to</u></td>
			<td width="5%"  class="hr" onclick="sortColumn(event)" type="CaseInsensitiveString"><u>Delivery</u></td>
		</tr>
	</thead>
	<?php
	if($_GET['from'] != "")
	{
		$i=$_GET['from'];
	}
	else
	{
		$i=1; 
	}
	while($row=mysql_fetch_array($sql_query_result)) 
	{
		if($row['type']==1)
			$mail_type="Welcome - verification -  only child";
		else if($row['type']==2)	
			$mail_type="Renewal - both";
		else if($row['type']==3)	
			$mail_type="Welcome - only parent";	
		else if($row['type']==4)	
			$mail_type="Change username by child (both)";	 
		else if($row['type']==5)	
			$mail_type="Referral - only child";
		else if($row['type']==6)	
			$mail_type="Qualified referral extension - only  ohild";	
		else if($row['type']==7)	
			$mail_type="Subscription expiry mail - (either of them)";
		else if($row['type']==8)	
			$mail_type="NLI Reminder - (both)";
		else if($row['type']==9)	
			$mail_type="Change password - child - teacher";		
		else 
			$mail_type="Report a error to developement team";	
		
		
		if($row['success']==1)	
		{
			$delivery='success';
		}
		else
		{
			$delivery='failed';
		}
		
	?>
	<tr bgcolor="#FFFFFF">
		<td align="center"><?=$i;?></td>
		<td><?=$mail_type;?></td>
		<td><?=$row['sent_to'];?></td>
		<td><?=$row['sent_cc'];?></td>
		<td><?=$row['date'];?></td>
		<td><?=$row['sent_bcc'];?></td>
		<td><?=$row['sender'];?></td>
		<td><?=$row['reply_to'];?></td>
		<td><?=$delivery;?></td>
	</tr>
	<?php
	$i++;
	}
	if($record_count==0)
	{
		echo "<tr bgcolor=#FFFFFF><td colspan=9 align=center>No data found.</td></tr> ";
	}
	?>
	</table>


</BODY>
</HTML>
