<?php
include("check1.php");

if (isset($_POST["interface"]) && isset($_POST["fileName"])) {
    $interface = $_POST["interface"];
    $fileName = $_POST["fileName"];
    $countQuery = "INSERT INTO adepts_linkCounter(interface,fileName,count) VALUES($interface,'$fileName',1) ON DUPLICATE KEY UPDATE count=count+1;";
    $rs = mysql_query($countQuery) or die(mysql_error());
    mysql_close();
}
?>