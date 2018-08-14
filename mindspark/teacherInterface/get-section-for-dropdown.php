<?php 

include_once("../userInterface/check1.php");

$schoolCode = $_POST['schoolCode'];
$userID = $_POST['userID'];
$category = $_POST['category'];
$childClassSelected = $_POST['childClassSelected'];

if(strcasecmp("School Admin", $category) == 0)
{
	if(!empty($schoolCode) && !empty($childClassSelected))
	{
		$query = 'select distinct childSection from adepts_userDetails where schoolCode= '.$schoolCode.' and childClass='.$childClassSelected.' and category= "STUDENT" AND subcategory="School" AND enabled=1 AND endDate>=curdate()';
		$result = mysql_query($query) or die(mysql_error());


		while ($row = mysql_fetch_assoc($result)) {
		      $rows[]['childSection'] = $row['childSection'];
		    }
		echo (json_encode($rows));
	}
	else
	{
		echo "0";	
	}	
}
else if(strcasecmp("Teacher", $category) == 0)
{
	if(!empty($childClassSelected))
	{
		$query = 'select distinct section from adepts_teacherClassMapping where userID='.$userID.' and class='.$childClassSelected.' and section is not null';
		$result = mysql_query($query) or die(mysql_error());

		while ($row = mysql_fetch_assoc($result)) {
		      $rows[]['childSection'] = $row['section'];
		    }
		echo (json_encode($rows));
	}
}

?>