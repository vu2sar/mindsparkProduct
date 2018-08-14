<?php
$msSessionCookie = $_POST["msSessionCookie"];
setcookie("PHPSESSID",$msSessionCookie,time()+3600,"/");
if(isset($_POST["cwa"]) && trim($_POST["cwa"]) != "")
{
	include("dbconf.php");
	include("functions/functions.php");
	$SDLArray = getMisconceptionSdlsForTopic1($_POST["ttCode"],$_POST["class"],"forDev");
	echo $SDLArray;
}
else if(isset($_POST["mode"]) && trim($_POST["mode"]) == "teacherforum")
{
	//echo '<a href="../teacherInterface/teacherforum/index.php"  title="Teacher Forum">Teacher Forum</a>';
        echo "<script>window.location.href='../teacherInterface/teacherforum/?username=$username&msSessionCookie=$msSessionCookie'</script>";
	exit();
}
else if(!isset($_POST["page"]) || trim($_POST["page"]) == "")
{
	echo "<script>window.location.href='activity.php?userType=msAsStudent'</script>";
}
else
{
	echo '<form id="frmHidForm" action="'.$_POST["page"].'" method="post">';
	foreach($_POST as $name=>$value)
	{
		if($name != "msSessionCookie" && $name != "page")
		echo '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'">';
	}
	echo '</form>';
	echo "<script>
				  document.getElementById('frmHidForm').submit();
			  </script>";
	echo '</body>';
	echo '</html>';
	exit();
}
?>
