<?php 

include_once("../../check1.php");

$schoolCode = $_POST['schoolCode'];
$childClassSelected = $_POST['childClassSelected'];

$query = 'select distinct childSection from adepts_userDetails where schoolCode= '.$schoolCode.' and childClass='.$childClassSelected.' and category= "STUDENT" AND subcategory="School" AND enabled=1 AND endDate>=curdate()';

$result = mysql_query($query) or die(mysql_error());


while ($row = mysql_fetch_assoc($result)) {
      $rows[]['childSection'] = $row['childSection'];
    }
    
echo (json_encode($rows));
	

?>