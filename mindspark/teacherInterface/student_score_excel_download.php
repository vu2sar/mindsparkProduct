<?php
include("../userInterface/check1.php");
require("excel_functions.php");
$scoreStr = $_POST['scoreStr'];
$class = $_POST['cls'];
$title = $_POST['title'];
$scoreStr = substr($scoreStr,0,-1);

$fileName = "MS_score_class".$class.".xls";
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");;
header("Content-Disposition: attachment;filename= $fileName");
header("Content-Transfer-Encoding: binary ");

xlsBOF();
xlsWriteLabel(1,0,"Student Report ($title)");

xlsWriteLabel(3,0,"Sr. No.");
xlsWriteLabel(3,1,"Name");
xlsWriteLabel(3,2,"Class");
xlsWriteLabel(3,3,"Date of Birth");
xlsWriteLabel(3,4,"Score");


$xlsRow = 4;
$scoreArray = explode(",",$scoreStr);
foreach ($scoreArray as $val)
{
    $detailsArr = explode("~",$val);
	xlsWriteNumber($xlsRow,0,$detailsArr[0]);
	xlsWriteLabel($xlsRow,1,$detailsArr[1]);
	xlsWriteLabel($xlsRow,2,$detailsArr[2]);
	xlsWriteLabel($xlsRow,3,$detailsArr[3]);
	xlsWriteNumber($xlsRow,4,$detailsArr[4]);
	$xlsRow++;
}
$xlsRow++;
xlsEOF();
exit();
?>