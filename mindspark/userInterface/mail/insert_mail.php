<?php
//echo "inside insert_mail1.php";

//include_once("check1.php");
//exit;
function insert($type,$to,$cc,$bcc,$from,$reply_to,$success)
{

	$time_stamp=time();
	$query=" insert into adepts_mail set ";
	$query.= " type = '".$type."', ";
	$query.= " sent_to = '".$to."', ";
	$query.= " sent_cc = '".$cc."', ";
	$query.= " sent_bcc = '".$bcc."', ";
	$query.= " sender = '".$from."', ";
	$query.= " reply_to = '".$reply_to."', ";
	$query.= " success = '".$success."' ";
	
	//echo $query;
	@mysql_query($query) or die(mysql_error());

}
?>