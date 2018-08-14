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

    {
        $parentAppStyles= '<title>Parent e-mail registration</title>
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
            <script src="libs/jquery-1.9.1.min.js"></script>
            <script src="libs/jquery-ui.min.js"></script>
            <script type="text/javascript" src="libs/i18next.js"></script>
            <script type="text/javascript" src="libs/translation.js"></script>
            <script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
            <script type="text/javascript" src="../common/libs/closeDetection.js?interface=parent"></script>';
        echo $parentAppStyles;

        }
        else
        {
            $parentInterfaceStyles='
            <title>Parent e-mail registration</title>
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
            <script type="text/javascript" src="../common/libs/closeDetection.js?interface=parent"></script>';

            echo $parentInterfaceStyles;

        }

?>

<script>
    var langType = '<?= $language; ?>';
    function redirect(URL)
    {
		setTryingToUnload();
        window.location = URL;
    }
    $(function() {
        $(".datepicker").datepicker({changeMonth: true, changeYear: true, yearRange: '1995:<?= date("Y") ?>', dateFormat: 'dd-mm-yy'});
    });
    function openCalender(id) {
        var id = id;
        if (id == "from") {
            $("#dob").focus();
        }
    }
    function DateFormat(txt, keyCode)
    {
        if (keyCode == 16)
            isShift = true;
        //Validate that its Numeric
        if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
                keyCode <= 37 || keyCode <= 39 ||
                (keyCode >= 96 && keyCode <= 105)) && isShift == false)
        {
            if ((txt.value.length == 2 || txt.value.length == 5) && keyCode != 8)
            {
                txt.value += seperator;
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    function validate()
    {
        errorMsg = '';
        if (document.getElementById('userName').value == '')
            errorMsg += 'Please provide the username.\n';
        if (document.getElementById('selGrade').value == '')
            errorMsg += 'Please provide the class.\n';
        if (document.getElementById('dob').value == '')
            errorMsg += 'Please provide the date of birth.\n';
        else
        {
            var dobValidation = isDate(document.getElementById('dob').value);
            var isDobValid = (dobValidation === 'true');
            if (!isDobValid)
                errorMsg += dobValidation + ' for date of birth.\n';
            var dob = getDateObject(document.getElementById('dob').value, "-");
            var currentDate = new Date();
            if (currentDate < dob)
                errorMsg += 'Date of birth can not be greater than current date.';
        }
        if (errorMsg != '')
        {
            alert(errorMsg);
            return false;
        }
		setTryingToUnload();
        document.getElementById('formVerification').submit();
    }
    function getDateObject(dateString, dateSeperator)
    {
        //This function return a date object after accepting a date string and dateseparator as arguments
        var curValue = dateString;
        var sepChar = dateSeperator;
        var curPos = 0;
        var cDate, cMonth, cYear;
        //extract date portion
        curPos = dateString.indexOf(sepChar);
        cDate = parseInt(dateString.substring(0, curPos), 10);

        //extract month portion
        endPos = dateString.indexOf(sepChar, curPos + 1);
        cMonth = parseInt(dateString.substring(curPos + 1, endPos), 10);

        //extract date portion
        curPos = endPos;
        //endPos=curPos+5;
        cYear = parseInt(curValue.substring(curPos + 1), 10);

        //Create Date Object
        dtObject = new Date();
        dtObject.setFullYear(cYear, cMonth - 1, cDate);

        return dtObject;
    }
</script>

</head>

<?php 
if($parentApp)
{
    $childVerificationParentApp=1;
    include '../parentApp/header.php';
    echo '<section id="container">';    
}
else
{
    echo '<body class="translation" style="height: 100%; vertical-align: central" height="100%"><div class="logo1"></div>';
    include("eiColors.php");
}

?>

<script>
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
        {include('childVerificationParentApp.php'); echo '</section>';} 

        else 
        {include('childVerification.php'); echo '<div id="bottom_bar" style="position:fixed; width:100%; padding:0px; bottom:0px;">
        <div id="copyright" data-i18n="[html]common.copyright">&copy; 2009-2017, Educational Initiatives Pvt. Ltd.</div></div> </body>';} 
    ?> 

</html>