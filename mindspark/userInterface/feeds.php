<?php
	error_reporting(E_ALL);
	$sep = "/";
	include("check1.php");
	include("../slave_connectivity.php");
	mysql_select_db ("educatio_adepts")  or die ("Could not select database".mysql_error());

	set_time_limit(0);
	
	$isSchoolUser=(isset($_REQUEST['isSchoolUser']))?$_REQUEST['isSchoolUser']:0;
	$schoolCode=(isset($_REQUEST['schoolCode']) && $isSchoolUser==1)?$_REQUEST['schoolCode']:0;
	$stClass=(isset($_REQUEST['stClass']))?$_REQUEST['stClass']:0;
	$type=(isset($_REQUEST['type']))?$_REQUEST['type']:1;
	$newestFeed=(isset($_REQUEST['newestFeed']))?$_REQUEST['newestFeed']:0;
	$oldestFeed=(isset($_REQUEST['oldestFeed']))?$_REQUEST['oldestFeed']:0;
	$onlyMe=(isset($_REQUEST['onlyMe']))?$_REQUEST['onlyMe']:0;

	$scText=' AND schoolCode='.$schoolCode;
	if ($schoolCode==0) $scText='';

	if ($type==1){
		if ($newestFeed==0) $lfText=' ORDER BY feedID DESC LIMIT 10';
		else $lfText=' AND feedID>'.$newestFeed.' ORDER BY feedID DESC ';
	}
	else {
		$lfText=' AND feedID<'.$oldestFeed.'  ORDER BY feedID DESC LIMIT 5';
	}
	$omText='';
	if ($onlyMe!=0) $omText=' AND userID ='.$onlyMe;
	
	$query='SELECT feedID,userID,studentIcon,childName,childClass,schoolCode,actID,actDesc,actIcon,score,timeTaken,lastModified,ftype FROM adepts_userFeeds WHERE childClass='.$stClass.' '.$omText.' '.$scText.' '.$lfText;
	$result = mysql_query($query) or die ( mysql_error());
	$feedsArr=array();
	while ($line=mysql_fetch_array($result)){
		$feedsArr[$line[0]]=array('userID'=>$line['userID'],'studentIcon'=>$line['studentIcon'],'childName'=>$line['childName'],'childClass'=>$line['childClass'],'schoolCode'=>$line['schoolCode'],'actID'=>$line['actID'],'actDesc'=>$line['actDesc'],'actIcon'=>$line['actIcon'],'score'=>$line['score'],'timeTaken'=>$line['timeTaken'],'lastModified'=>$line['lastModified'],'ftype'=>$line['ftype']);
	}
	echo json_encode($feedsArr, JSON_FORCE_OBJECT);
	exit;
?>