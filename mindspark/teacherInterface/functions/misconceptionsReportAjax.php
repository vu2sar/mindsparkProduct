<?php
	include_once('dashboardFunctions.php');
	error_reporting(E_ALL & ~E_DEPRECATED);
	
	$retArr = array();
	$schoolCode = $_POST['schoolCode'];
	$class = $_POST['class'];
	$section = isset($_POST['section'])?$_POST['section']:'';
	$topic = $_POST['topic'];

	$retArr = getMisconceptionsReport($schoolCode, $class, $section, array($topic));
	if(sizeof($retArr) == 0) {
		echo "<p style=\"text-align: center;\"> No misconceptions were identified for this topic.";
		exit;
	}

	$html = "<ol>";
	foreach ($retArr as $key => $value) {
		$html .=	"<li>" . $value['description'] . "</li>";		
	}
	$html .= "</ol>";
	echo $html;
?>