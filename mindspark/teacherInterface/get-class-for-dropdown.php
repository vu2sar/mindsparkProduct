<?php 

include_once("../userInterface/check1.php");
//include("../../constants.php");
//include("../../classes/clsUser.php");
//include('common_functions.php');
$userID = $_POST['userID'];
$category = $_POST['category'];
$schoolCode = $_POST['schoolCode'];
$id = $_POST['id'];
$checkArr = array();

if(strcasecmp("School admin", $category) == 0)
{
	$query = 'select distinct childClass,childSection from adepts_userDetails where schoolCode= '.$schoolCode.' AND childClass IS NOT NULL AND category="STUDENT" AND subcategory="School" AND enabled=1 AND endDate>=curdate()';
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		if($id == 3)
		{
			if(!empty($row['childSection']))
	     		$rows[]['childClass'] = $row['childClass']."-".$row['childSection'];
	  		else
	  			$rows[]['childClass'] = $row['childClass'];
		}
		else
		{
			if(!in_array($row['childClass'], $checkArr))
			{
		  		$rows[]['childClass'] = $row['childClass'];							
				array_push($checkArr, $row['childClass']);
			}
		}
    }
}
else if(strcasecmp("Teacher", $category) == 0)
{
	$query = 'select distinct class,section from adepts_teacherClassMapping where userID='.$userID.' and class IS NOT NULL';
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		  if($id == 3 || $id == 2)
		  {
		      if(!empty($row['section']))
			     	$rows[]['childClass'] = $row['class']."-".$row['section'];
			  else
			  		$rows[]['childClass'] = $row['class'];		  	
		  }
		  else
		  {
		  	if(!in_array($row['class'], $checkArr))
		  	 	$rows[]['childClass'] = $row['class'];	
		  	 	array_push($checkArr, $row['class']);
		  }
	    }
}
    
echo (json_encode($rows));
	

?>