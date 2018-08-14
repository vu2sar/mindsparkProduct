<?php
include("../userInterface/check1.php");
include("../userInterface/constants.php");
// Unset all of the session variables.
mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endTime=now(), endType=concat_ws(',',endType,'7') WHERE sessionID=".$_SESSION['sessionID']);
session_unset();
// Finally, destroy the session.
session_destroy();
//print_r( $_SESSION);
//Created a hidden form/html to prevent the users accessing the cached page by pressing back button after logout.
/*echo "<html><body><form id='frmHidForm' action=\"../index.php\">";*/
echo "<html><body><form id='frmHidForm' action=\"../login/index.php\">"; //change while going live
echo "<script>document.getElementById('frmHidForm').submit();</script>";
echo "</form></body></html>";
//echo "<script>window.location=\"http://www.mindspark.in/login\"</script>";
exit();

?>