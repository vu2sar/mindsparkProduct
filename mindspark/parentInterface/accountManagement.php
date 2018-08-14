<?php
include("header.php");
$curDate = new DateTime("now");
//        error_reporting(E_ALL);
?>

<title>Account Management</title>

<!--<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">-->
<link href="css/common.css?ver=21" rel="stylesheet" type="text/css">
<link href="css/accountManagement.css?ver=1" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<!--<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>-->
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
<script type="text/javascript" src="http://d2tl1spkm4qpax.cloudfront.net/Enrichment_Modules/html5/libs/jquery_ui.js"></script>
<script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.tabs.js"></script>
<script>
    $(document).ready(function () {
        $(".ui-tabs").tabs();
    });
    var langType = '<?= $language; ?>';
    var counter = 0;
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        
    }
    function showDropDown() {
        if (counter == 0) {
            document.getElementById("dropdown").style.display = "block";
            counter = 1;
        } else if (counter == 1) {
            document.getElementById("dropdown").style.display = "none";
            counter = 0;
        }
    }
    function topicReport(mode, childID) {
        $("#childIDvalue").attr("value", childID);
        if (mode == 2) {
            $("#reportForm").attr("action", "editDetails.php");
        } else if (mode == 3) {
            $("#reportForm").attr("action", "aqad.php");
        } else {
            $("#reportForm").attr("action", "topicUsage.php");
        }
        setTryingToUnload();
        $("#reportForm").submit();
    }
    
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
    <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
        <?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
        <?php include("topBar.php"); ?>
    </div>
    <div id="sideBar">
        <?php include("sideBar.php") ?>
    </div>
 <script>
        function assetOnlineLogin(username,userID)
    {
        $.fn.colorbox({href: '#assetOnlineLogin', inline: true, open: true, escKey: true, height: 320, width: 560});
        $('#divRedirect').hide();
        $.ajax({
            url: "/mindspark/parentInterface/assetOnlineLogin.php?username="+username+'&userID='+userID,
            type: "get",
            cache: false,
            success: function (response, textStatus, jqXHR) {
                if (textStatus === 'success')
                {
                    try {
                        if (response != 'No username provided')
                        {
                            var responseArray = $.parseJSON(response);
                            if (responseArray['sessionCookie'] != false)
                            {
                                $('#assetLoding').hide();
                                $('#assetLoginError').hide();
                                $('#sessionCookie').val(responseArray['sessionCookie']);
                                $('#divRedirect').show();
                            }
                            else
                            {
                                $('#assetLoding').hide();
                                $('#divRedirect').hide();
                                $('#assetLoginError').show();
                            }
                        }
                    }
                    catch (err)
                    {
                        console.log(err + '\n' + response);
                    }
                }
            },
            error: function (xmlHttpRequest, textStatus, errorThrown) {
                $(document).ready(function () {
                    $('#assetLoding').hide();
                    $('#assetLoginError').show();
                });
            }
        });
    }
        </script>
    <div id="container">
        <?php include('referAFriendIcon.php') ?>
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">My Account</label></a></td>
        </table>	


        <table class="headingContainer" style="width: 80%;">
            <tr>
                <td colspan="4">
                    <div class="ui-tabs">
                        <ul class="ui-tabs-nav">
                            <?php
                            for ($i = 0; $i < count($childID); $i++) {
                                $child[$i] = new user($childID[$i]);
                                ?>
                                <li><table>
                                        <tr>
                                            <td>
                                                <a href="#tabs-<?php echo $i; ?>"><?php echo $child[$i]->childName ?></a>
                                            </td>
                                            <td>
                                                <div onClick="topicReport(2,<?php echo $child[$i]->userID; ?>);"><img src="assets/common/edit_green.png" alt="Edit" style="cursor: pointer;"></div>
                                                <!--<input id="Button2" type="image" value="button" onclick="Swap(event)" />-->
                                            </td>
                                        </tr>
                                    </table></li>
                                                            <!--<td><div class="heading1"><span onclick="topicReport(2,<?= $childID[$i] ?>)" style="cursor: pointer;"><?= $child[$i]->childName ?></span><div class="editMessage">(Click on name to view details)</i></div></div></td>-->
                            <?php } ?>
                            <!--<a href="#tabs-4" style="float:right;" onclick="showDropDown()">-->
                            <div id="dropdown" style="display:none;float:right; margin-top: 37px; position: absolute; right:0px;">
                                <a href="childRegistrationInner.php" style="text-decoration:none;color:black;"><div class="dropDown1">
                                        New
                                    </div></a>
                                <a href="registerStudent.php" style="text-decoration:none;color:black;"><div class="dropDown2">
                                        Existing
                                    </div></a>
                            </div>
                            <?php if (count($childID) < 5) { ?>
                                <div id="subject" style="float:right;" onClick="showDropDown()">
                                    Register Another Child
                                </div>                  
                            <?php } ?>
                            <!--</a>-->

                        </ul>
                        <?php
                        for ($i = 0; $i < count($childID); $i++) {
                            $child[$i] = new user($childID[$i]);
                            ?>
                                    <!--<li><a href="#tabs-<?php // echo $i;  ?>"><?php // echo $child[$i]->childName ?></a></li>-->
                            <div id="tabs-<?php echo $i; ?>" class="ui-tabs-panel">
                                <div style="width: 100%; background-color: black;color: white; padding: 5px; border-radius: 3px; text-align: center;height: 25px;">
                                    <img src="assets/common/MSLogo.png" height="20px" alt="MS logo" style="float: left;" /> 
<?php
                            $endDate = new DateTime($child[$i]->endDate);
                            $subCategory = $child[$i]->subcategory;
                            $datetime1 = new DateTime();
                            $interval = $datetime1->diff($endDate);
                            $value = $interval->format('%r%a');
                            $value1 = $interval->format('%r%a') + 2;
                            if ($value > 0) {

                                $interval = $value1 . " days";
                            } else if ($value == "0") {
                                $interval = "2 days";
                            }
                            if (strpos($value, '-') !== false) {
                                $interval = "1 day";
                            }
                            $freeTrial = $childIDFree[$i];
                            if ($endDate < $curDate && $subCategory == "Individual" && $freeTrial == 0) {
                                ?>
                                    
                                <a href="http://mindspark.in/registration.php?userID=<?= $child[$i]->userID ?>" target="_blank" style="float:right;"><div class="purchaseButton" style="padding-top: 3px;height:20px;">Renew</div></a>
                                <div style="color:white;float: right;">INACTIVE</div>
                            <?php } else if ($freeTrial == 1) { ?>
                                <?php if ($value >= 0 && $value <= 5) { ?>
                                
                                    <a href="http://mindspark.in/registration.php?userID=<?= $child[$i]->userID ?>" target="_blank"><div class="purchaseButton" style="padding-top: 3px;height:20px;float:right;">Purchase</div> </a>
<div style="color:white;float: right;">(<?= $interval ?> left for free trial to expire!)</div>
                                    
                                <?php } else if ($value < 0) { ?>
                                    
                                    <a href="http://mindspark.in/registration.php?userID=<?= $child[$i]->userID ?>" target="_blank"><div class="purchaseButton" style="padding-top: 3px;height:20px;float:right;">Purchase</div> </a>
<div style="color:white;">(Free trial expired!)</div>
                                    
                                <?php } ?>
                            <?php } else { ?>
                                <div style="color:white;float: right; padding-right: 50%">ACTIVE</div>    
                            <?php } ?>
                                    <!--<a href="http://mindspark.in/registration.php?userID=<?= $child[$i]->userID ?>" target="_blank" style="text-decoration: underline;color:blue;float:right;"><div class="purchaseButton" style="padding-top: 3px;height:20px;">Purchase Now</div></a>-->
                                </div>
                                <div style="width: 100%; background-color: black;color: white; padding: 5px; border-radius: 3px; margin-top: 10px;">
                                    <img src="assets/common/as-online.png" height="20px" alt="MS logo" /> 

                                    <a href="javascript:assetOnlineLogin('<?php echo $child[$i]->username; ?>',<?php echo $child[$i]->userID; ?>);" style="float:right;"><div class="purchaseButton" style="padding-top: 3px;height:20px;">Purchase Now</div></a>
                                </div>
                                <div style="width: 100%; background-color: black;color: white; padding: 5px; border-radius: 3px;margin-top: 10px; text-align: center; height:20px;">
                                    <span style="float: left; margin-left: 10px;">ASSET QUESTION-A-DAY</span>                    
                                    <div style="color:white; padding-right: 30%; float: right;">ACTIVE (available free for 1 year!)</div>
                                </div>

                            </div>
                                                            <!--<td><div class="heading1"><span onclick="topicReport(2,<?= $childID[$i] ?>)" style="cursor: pointer;"><?= $child[$i]->childName ?></span><div class="editMessage">(Click on name to view details)</i></div></div></td>-->
                        <?php } ?>

                        <div id="tabs-4" style="display:none;" class="ui-tabs-panel">
                            <div id="divRegistration">
                                <a href="childRegistrationInner.php" style="text-decoration:none;color:black;"><div class="dropDown1">
                                        New
                                    </div></a>
                                <a href="registerStudent.php" style="text-decoration:none;color:black;"><div class="dropDown2">
                                        Existing
                                    </div></a>
                            </div>
                        </div>

                    </div>

                </td>
            </tr>
            
            
        </table>
    </div>
    <form method="post" id="reportForm" action="topicUsage.php">
        <input type="hidden" value="" id="childIDvalue" name="childSelectedID"/>
    </form>    

</div>
<div style="display:none">
    <div id="freeTrialMessage2" class="freeTrialMessage">
        <div style="width: 100%;height:2px;"></div>
        <p style="font-size:1.3em;"><b>Welcome to Mindspark Parent Connect!</b></p>
        <p>Parent Connect is our tool for parents to access instant, online, timely and detailed report about their child’s work in Mindspark.</p>
        <p>Your child’s Mindspark login credentials are emailed to you, please check your email.</p>
        <p><b>What information can be found on Parent Connect?</b></p>
        <p><b>Summary Report:</b> Summary of the child’s activities in Mindspark in fortnight gone by of activities done in Mindspark. This can be found on homepage.</p>
        <p><b>Topic Progress report:</b> Graphical representation of topic progress for the fortnight and also, view the topic progress till date Lists your student’s class schedule, assignments and grades.</p>
        <p><b>Usage Report:</b> usage details of the child week-wise and month-wise.</p>
        <p><b>AQAD:</b> ASSET Question-A-Day, critical thinking questions to trigger your child’s knowledge.</p>
        <p><b>User Manual:</b> This manual will help you to get started and also to understand various features of Mindspark. This is available under ‘Help’ section.</p>
        <p><b>Register another child:</b> You can register another child for free trial from here. </p>
        <p>For any query, you can write to mindspark@ei-india.com</p>
        <p>Regards,<br/>Team Mindspark</p>
    </div>        
</div>
<div style="display: none;">
    <div id="assetOnlineLogin" class="freeTrialMessage" style="text-align: center;padding-top: 90px;">
        <div id="assetLoding">
            <h3>Please wait...</h3>
            <img src="assets/common/ajax-loader-bar.gif" alt="Loading..." />
        </div>
        <form id="assetOnlineForm" method="post" target="__blank" action="http://www.assetonline.in/asset_online/remoteLogin.php">
            <input type="hidden" name="sessionCookie" id="sessionCookie" value="">
            <input type="hidden" name="page" id="page" value="Asset_online_order_exams.php">
        </form>
        <div id="assetLoginError" style="display: none;">
            <h2 style="color: red;">There was an error while loggin in. Please try again later.</h2>
        </div>
        <div id="divRedirect" style="text-align: center;">
            <span>ASSETOnline is India's leading diagnostic test which actually helps students improve. Unlike regular tests which try only to find out how much a child knows (or has memorized), this test measures how well a student has understood concepts and gives detailed feedback on the same, to help him/her improve.</span>
                <div class="purchaseButton" id="assetRedirect" onClick="$('#assetOnlineForm').submit();$('#assetOnlineLogin').colorbox.close();" style="text-align: center;width:150px; margin-left: 35%;">Go ahead with purchase</div>
                </div>
    </div>
</div>
<?php if (isset($_GET['showMessage'])) { ?>
    <script>
        $.fn.colorbox({'href': '#freeTrialMessage2', 'inline': true, 'open': true, 'escKey': true, 'height': 520, 'width': 760});
    </script>	
<?php } ?>
<?php include("footer.php") ?>