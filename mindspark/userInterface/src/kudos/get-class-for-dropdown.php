<?php 

include_once("../../check1.php");
//include("../../constants.php");
//include("../../classes/clsUser.php");
//include('common_functions.php');

$schoolCode = $_POST['schoolCode'];
$query = 'select distinct childClass from adepts_userDetails where schoolCode= '.$schoolCode.' AND childClass IS NOT NULL AND category="STUDENT" AND subcategory="School" AND enabled=1 AND endDate>=curdate()';
$result = mysql_query($query) or die(mysql_error());


while ($row = mysql_fetch_assoc($result)) {
      $rows[]['childClass'] = $row['childClass'];
    }
    
echo (json_encode($rows));
	

?>