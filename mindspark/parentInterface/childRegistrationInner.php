<?php
include("header.php");
//error_reporting(E_ALL);
?>
<title>Register Another Child</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
    }
</script>
</head>
<body  onload="load()" onresize="load()">
        <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
<?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
<?php include("topBar.php") ?>
    </div>
    <div id="sideBar">
<?php include("sideBar.php") ?>
    </div>
    <div id="container">        
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class=""><div class="smallCircle red"></div><label class="textRed" value="secRemediation">REGISTER ANOTHER CHILD</label></a></td>
        </table>
        <?php include('referAFriendIcon.php') ?>
        <?php
        $innerStudentMapPage = TRUE;
        include("childRegistration.php");
        ?>
    </div>

<?php include("footer.php") ?>