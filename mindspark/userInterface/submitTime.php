<?php

   	include("check1.php");
	if(isset($_SESSION['sessionID']) && $_SESSION['sessionID']!="")
		$sessionID	=	$_SESSION['sessionID'];
	else
		$sessionID	=	0;
	$time		=	$_GET['loadingTime'];
    $grePath	=	$_GET['src'];
    $pathArr	=	getCode($grePath);
  
	$sq	=	"INSERT INTO adepts_loadingTime 
			 SET sessionID=$sessionID, contentSource='$grePath', type='".$pathArr["greType"]."', contentID='".$pathArr["greID"]."',timeTaken ='$time'";
	mysql_query($sq);
	
	function getCode($string)
	{
		$pathArr	=	array();
		$source=parse_url($string);
		$sourceArray=explode("/",$source["path"]);
		$pathArr["greType"]=$sourceArray[3];
		if($pathArr["greType"]=="questions")
			$pathArr["greID"]=$sourceArray[4]."/".$sourceArray[5];
		else
			$pathArr["greID"]=$sourceArray[4];
		return $pathArr;
	}
?>