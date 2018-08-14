<?php
@include("../userInterface/check1.php");
//@include("../slave_connectivity.php");
include("../userInterface/constants.php");
include("../userInterface/classes/clsUser.php");
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../login/logout.php");
    exit;
}

/* header('Content-Type:text/html; charset=UTF-8'); */
?>
<!DOCTYPE HTML>
<html>
    <head>
        <script src="../userInterface/libs/css_browser_selector.js" type="text/javascript"></script>
		<script src="libs/jquery.js"></script>
		<script type="text/javascript" src="../common/libs/closeDetection.js?interface=parent"></script>
        <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
        <meta
            name="viewport"
            content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
        <!--<meta content="text/html; charset=UTF-8"/>-->
        <!--<meta http-equiv="Content-Type" content="text/html" />-->