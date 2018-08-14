 <?php
//include("header.php");
//@include("../userInterface/dbconf.php");
session_start();
//error_reporting(E_ALL);
//@include("../userInterface/dbconf.php");
//include_once "../userInterface/classes/clsUser.php";
if (!isset($_SESSION['openIDEmail'])) {
//    echo 'called';
    header("Location:../login/logout.php");
    exit;
}
if (!class_exists('db')) {
    require_once('../parentInterface/classes/class.db.php');
}
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$msg = '';
//$registrationError = false;
//$redirect = false;
$innerStudentMapPage = FALSE;

$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($userAgent,'android')!==false || strpos($userAgent,'ipad')!==false || strpos($userAgent,'iphone')!==false || strpos($userAgent,'ipod')!==false || strpos($userAgent,'blackberry')!==false || strpos($userAgent,'opera mobile')!==false)
{$parentApp=1;}

if($parentApp)
{     $changePasswordParentApp=1; 
      $parentAppStyles='<title>Change password</title>
        <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=9">
        <meta
            name="viewport"
            content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
            
        <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta name="description" content="">
                <meta name="author" content="Educational Initiatives Pvt. Ltd.">
                <meta name="keyword" content="Maths learning, Mindspark, Online Maths learning, Adaptive logic math learning">
                <link rel="shortcut icon" href="img/favicon.png">
                <title>Mindspark - Parent Interface</title>
                <!-- Bootstrap core CSS -->
                <link href="../parentApp/css/bootstrap.min.css" rel="stylesheet">
                <link href="../parentApp/css/bootstrap-reset.css" rel="stylesheet">
                <!--external css-->
                <link href="../parentApp/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
                <!-- Custom styles for this template -->
                <link href="../parentApp/css/style.css" rel="stylesheet">
                <link href="../parentApp/css/style-responsive.css" rel="stylesheet" />
                <link href="../parentApp/css/custom.css" rel="stylesheet" type="text/css" />
                <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
                <!--[if lt IE 9]>
                  <script src="js/html5shiv.js"></script>
                  <script src="js/respond.min.js"></script>
                <![endif]-->
        </head>

        <script src="libs/jquery-1.9.1.min.js"></script>
        <script src="libs/jquery-ui.min.js"></script>
        <script type="text/javascript" src="libs/i18next.js"></script>
        <script type="text/javascript" src="libs/translation.js"></script>
        <script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
        <script type="text/javascript" src="../common/libs/closeDetection.js?interface=parent"></script>
        <section id="container">';    
        echo $parentAppStyles;
        include '../parentApp/header.php'; 
        

}
else
{
    $parentInterfaceStyles = '<title>Change password</title>
    <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=9">
    <meta
        name="viewport"
        content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <script src="libs/jquery-1.9.1.min.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css" />

    <script src="libs/jquery-ui.min.js"></script>
    <!--<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">-->
    <link href="css/common.css?ver=4" rel="stylesheet" type="text/css">
    <!--<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>-->
    <script type="text/javascript" src="libs/i18next.js"></script>
    <script type="text/javascript" src="libs/translation.js"></script>
    <script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
    <script type="text/javascript" src="../common/libs/closeDetection.js?interface=parent"></script>
    </head>
    <body class="translation" style="height: 100%; vertical-align: central" height="100%">
    
    <div class="logo1">
    </div>';
    echo $parentInterfaceStyles;
    include("eiColors.php");
} 

?>
    
    <script>
        var tryingToUnloadPage = true; //TO CHECK
        if (<?= ($redirect == true ? 'true' : 'false') ?>)
        {
            window.setTimeout(function() {
				setTryingToUnload();
                window.location = 'home.php';
            }, 7000);
        }
    </script>


    <?php 

    if($parentApp)
    {
        $changePasswordParentApp=1;   
        include('changePasswordParentApp.php'); 
        echo '</section>';

    } 
    else
    {
        include('changePassword.php');
        echo '<div id="bottom_bar" style="position:fixed; width:100%; padding:0px; bottom:0px;">
        <div id="copyright" data-i18n="[html]common.copyright">&copy; 2009-2015, Educational Initiatives Pvt. Ltd.</div>
        </div></body>';
    }

    ?>
    
</html>