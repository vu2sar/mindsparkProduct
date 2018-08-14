<?php

	$file = "MS_REPORT.xls";
	$content = strip_tags($_REQUEST['content'],"<table><tr><td>");	
	header("Expires: 0");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Content-type: application/vnd.ms-excel;charset:UTF-8");
	header("Content-length: ".strlen($content));
	header("Content-disposition: attachment; filename=".basename($file));
	// output all contents
	echo stripcslashes($content);
	exit; // If any tags/things not supported by excel will output then it will try to //open in office word

?>