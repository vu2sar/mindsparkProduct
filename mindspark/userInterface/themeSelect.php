<?php
include("classes/clsRewardSystem.php");

$userID = $_SESSION['userID'];
$sparkieInformation = new Sparkies($userID);
$rewardTheme = $sparkieInformation->rewardTheme;
$addressString = $_SERVER['REQUEST_URI'];
$address = explode("/",$addressString);
$pageAddress =$address[3];
?>
<?php if($rewardTheme!="default") { ?>
<?php if($theme==2 && $pageAddress!="question.php") { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/midClass/<?php echo $rewardTheme; ?>.css" />
<?php } else if($theme==3 && $pageAddress!="question.php") { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css" />
<?php }else if($theme==3 && $pageAddress=="question.php" && ($rewardTheme=="girl" || $rewardTheme=="boy")) { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css" />
	<link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/question/<?php echo $rewardTheme; ?>.css" />
<?php } }?>