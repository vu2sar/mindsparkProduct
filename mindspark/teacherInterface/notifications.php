<?php
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

function getCommentNotification($userID)
{
	$query = "select count(*) from adepts_userComments where userID='".$userID."' and viewed=0 and status in ('Closed','Ignored')";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_row($result);
	return $row[0];
}

function ajax_serve($username)
{
	$total_count = 0;
	$total_count = get_notification($username);
	echo  $total_count;
}


function get_member_id_from_user_id($username)
{
	$memberId = 0;
	$query = "select memberId from teacherForum.forum_member where username = '$username'";
	$result = mysql_query($query) or die(mysql_error());
	$num_rows = mysql_num_rows($result);
	if($num_rows > 0)
	{
		$row = mysql_fetch_row($result);
		$memberId = $row[0];				
	}

	return $memberId;
}
function get_notification($username)
{
	$memberId = get_member_id_from_user_id($username);
	$query = "SELECT count(a.read) as count_display FROM teacherForum.forum_activity a LEFT JOIN teacherForum.forum_member m ON (m.memberId=a.fromMemberId) WHERE (a.memberId='$memberId') AND (a.type IN ('post','groupChange','mention','privateAdd','updateAvailable','unapproved')) AND (a.read=0)";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_row($result);
	return $row[0];
}

?>