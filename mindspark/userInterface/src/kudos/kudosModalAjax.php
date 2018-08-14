<?php

include_once("../../check1.php");
include("../../constants.php");
include("../../classes/clsUser.php");
include('common_functions.php');
include("../../header.php");

$kudos_id=$_POST['kudos_id'];
echo kudosModal($kudos_id);
    
?>
            
	   


