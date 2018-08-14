<?php

 	// mysql_connect("192.168.0.7","root","") or die("Could not connect : " . mysql_error());
 	// mysql_select_db("educatio_adepts") or die("Could not select database");
	$link = mysql_connect("13.251.143.110","techm2","techmsGER5c4ZqjuSZ597") or die("Could not connect : " . mysql_error());
	mysql_select_db("educatio_adepts") or die("Could not select database");

	//$link = mysql_connect("192.168.0.7","root","") or die("Could not connect : " . mysql_error());
	//mysql_select_db("educatio_adepts",$link) or die("Could not select database");
	//$link = mysql_connect("ec2-46-137-198-206.ap-southeast-1.compute.amazonaws.com","ms_analysis","WNC001") or die("Could not connect : " . mysql_error());
	//mysql_select_db("educatio_educat",$link) or die("Could not select database");

	//$link = mysql_connect("swara","root","") or die("Could not connect : " . mysql_error());
	//mysql_select_db("educatio_adepts",$link) or die("Could not select database");

	//$link = mysql_connect("localhost","educatio_educat","ford240720") or die("Sorry, page not available temporarily ");
	//mysql_select_db("educatio_educat") or die("Could not select database");

	/*$link = mysql_connect("msserver","root","root") or die("Could not connect : " . mysql_error());
   	mysql_select_db("educatio_adepts_new",$link) or die("Could not select database");*/


?>
