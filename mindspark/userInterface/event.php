<?php
include("check1.php");
include("constants.php");

$userID	=	$_SESSION["userID"];
$mode	=	"";
$mode	=	$_POST["mode"];

if($mode=="leaveComments" && $userID!="")
{
	$commentText	=	$_POST["commentText"];
	$sessionID	=	$_SESSION["sessionID"];
	$sq	=	"INSERT INTO adepts_examCornerComments SET userID=$userID, sessionID=$sessionID, comment='$commentText'";
	$rs	=	mysql_query($sq);
}
else if($mode=="saveDownloadCount" && $userID!="")
{
	$summaryID	=	$_POST["summaryID"];
	$sq	=	"UPDATE adepts_summarySheets SET downloadCount=downloadCount + 1 WHERE summuryID=$summaryID";
	$rs	=	mysql_query($sq);
}
else if($mode=="addTodoText" && $userID!="")
{
	$todoText	=	$_POST["todoText"];
	$selDate	=	$_POST["selDate"];	
	$sq	=	"INSERT INTO adepts_todoList SET userID=$userID,eventDate='$selDate',eventText='$todoText'";
	$rs	=	mysql_query($sq);
}
else if($mode=="deleteTodoText" && $userID!="")
{
	$deleteIDs	=	substr($_POST["deleteIDs"],0,-1);
	$sq	=	"DELETE FROM adepts_todoList WHERE userID=$userID AND id IN ($deleteIDs)";
	$rs	=	mysql_query($sq);
}
else if($mode=="completeTodoText" && $userID!="")
{
	$deleteIDs	=	substr($_POST["deleteIDs"],0,-1);
	$sq	=	"UPDATE adepts_todoList SET completed=1 WHERE userID=$userID AND id IN ($deleteIDs)";
	$rs	=	mysql_query($sq);
}
else if($mode=="likeQuote" && $userID!="")
{
	$quoteID	=	$_POST["quoteID"];
	$sq	=	"UPDATE adepts_motivationalQuotes SET likeQuote=likeQuote+1 WHERE id=$quoteID";
	$rs	=	mysql_query($sq);
}
else if($mode=="disLikeQuote" && $userID!="")
{
	$quoteID	=	$_POST["quoteID"];
	$sq	=	"UPDATE adepts_motivationalQuotes SET dislikeQuote=dislikeQuote+1 WHERE id=$quoteID";
	$rs	=	mysql_query($sq);
}
else
{
	$day	=	$_POST["day"];
	$month	=	$_POST["month"]+1;
	$year	=	$_POST["year"];
	$arrayList	=	array();
	$sq	=	"SELECT id,userID,eventDate,eventText,completed FROM adepts_todoList WHERE userID=$userID";
	/*if($day!="")
		$sq	.=	" AND eventDate = '$year-$month-$day'";*/
	$sq	.=	" ORDER BY eventDate,completed";
	$rs	=	mysql_query($sq);
	$i=0;
	while($rw=mysql_fetch_array($rs))
	{
		$arrayList[$i]["date"]			=	strtotime($rw[2])*1000;
		$arrayList[$i]["completed"]		=	$rw[4];
		$arrayList[$i]["title"]			=	$rw[3];
		$arrayList[$i]["id"]			=	$rw[0];
		$arrayList[$i]["url"]			=	"";
		$i++;
	}
	
	echo json_encode($arrayList);	
}
?>