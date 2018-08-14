<?php include("header.php");
include("../userInterface/constants.php");
$pdfurl = CLOUDFRONTURL;
?>

<title>Home</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
    var langType = '<?= $language; ?>';
    var interface = 2;
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
    }
</script>
<script type="text/javascript" src="../userInterface/libs/linkCount.js"></script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
        <?php include('referAFriendIcon.php') ?>
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">HELP</label></a></td>
        </table>
        <table height="auto" width="80%" cellSpacing="0" cellPadding="0" align="left" bgcolor="#FFFFFF" bgColor="#FFFFFF" border="0" bordercolor="black" style="font-family: Verdana">
            <tr>
                <td>
                    <div style="margin-left:45px;">
                        <FONT face=Verdana style="font-size:20px" font color=#088A29>	<b>Mindspark User Manual 2014</b></font><br><br><br>
                        <FONT face=Verdana style="font-size:12px"><b>Chapter 1 <a class="linkCount" href="<?=$pdfurl?>helpManual/Introduction_Mindspark.pdf" style="text-decoration:underline" target="_blank">Introduction to Mindspark</a></b></font><br><br><br>
                        <FONT face=Verdana style="font-size:12px"><b>Chapter 2 Student interface in Mindspark</b></font><br>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>2.1 <a class="linkCount" href="<?=$pdfurl?>helpManual/Mindspark_Student.pdf" style="text-decoration:underline" target="_blank">Getting started with the student interface</a></b></li></ul></font>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>2.2 <a class="linkCount" href="<?=$pdfurl?>helpManual/Getting_Started_Student_Interface.pdf" style="text-decoration:underline" target="_blank">Mindspark and the student</a></b></li></ul></font><br>
                        <FONT face=Verdana style="font-size:12px"><b>Chapter 3 <a class="linkCount" href="<?=$pdfurl?>helpManual/PARENT INTERFACE AND REPORTS.pdf" style="text-decoration:underline" target="_blank">Parent interface and reports</a></b></font><br><br><br>
                        <FONT face=Verdana style="font-size:12px"><b>Appendix</b><br>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>4.1 <a class="linkCount" href="<?=$pdfurl?>helpManual/Customer_Support.pdf" style="text-decoration:underline" target="_blank">Customer Support</a></b></li></ul></font>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>4.2 <a class="linkCount" href="<?=$pdfurl?>helpManual/Hardware_Software_Requirement.pdf" style="text-decoration:underline" target="_blank">Hardware and Software requirement</a></b></li></ul></font>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>4.3 <a class="linkCount" href="<?=$pdfurl?>helpManual/FAQs.pdf" target="_blank" style="text-decoration:underline">FAQs</a></b></li></ul></font>
                        <FONT face=Verdana style="font-size:12px"><ul><li><b>4.4 <a class="linkCount" href="<?=$pdfurl?>helpManual/Testimonials.pdf" target="_blank" style="text-decoration:underline">Testimonials</a></b></li></ul></font>
                    </div>						
                </td>
            </tr>
        </table>

    </div>

    <?php include("footer.php") ?>