<?php
@include("../userInterface/check1.php");
error_reporting(E_ERROR);
$term = $_REQUEST["q"];
$class = $_REQUEST["class"];
$section = $_REQUEST["section"];
$schoolCode = $_REQUEST["schoolCode"];

$query = "SELECT childName,username,parentEmail,userID FROM adepts_userDetails WHERE schoolCode=$schoolCode AND childClass=$class AND endDate>curdate() AND enabled=1 AND childSection='$section'"
        . " AND childName like '%$term%'";
$result = mysql_query($query) or die(mysql_error());
//echo $query;
$arrChild = array();
//echo var_dump($result);
while ($student = mysql_fetch_array($result)) {
//    echo var_dump($student);
    $arrChild[] = array(
        'id' => $student["userID"],
        'name' => $student["childName"],
        'parentEmail' => $student["parentEmail"]
    );
}
//echo var_dump($arrChild);
echo json_encode($arrChild);
?>