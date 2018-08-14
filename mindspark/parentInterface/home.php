<?php 

include("header.php") ?>
<?php
$startDate = date('Y-m-d', strtotime("-15 days"));
$endDate = date('Y-m-d');

?>
<title>Home</title>

<link href="css/common.css?ver=21" rel="stylesheet" type="text/css">
<link href="css/home.css?ver=21" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js?ver=10"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js?ver=10"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<!--<script type="text/javascript" src="libs/BarChart.js"></script>-->
<script type="text/javascript" src="libs/home.js?ver=10"></script>
<link href="libs/css/jquery-ui-1.8.16.custom.css?ver=10" rel="stylesheet" type="text/css">
<script>
    var langType = '<?= $language; ?>';
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
    }
    function showSummary() {
        if ($('#buttonValue').attr("value") == "More >>") {
            $('#buttonValue').attr("value", "<< Less");
            $('.showFullSummary').css("display", "block");
        } else {
            $('#buttonValue').attr("value", "More >>");
            $('.showFullSummary').css("display", "none");
        }
    }
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
    <div id="container">
        <?php include('referAFriendIcon.php') ?>
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">SUMMARY</label></a></td>
        </table>
        <div id="summaryCoulumn">
            <?php include("parentReport.php") ?>
        </div>
        <table id="childDetails1">
            <td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">REPORTS</label></td>
        </table>
        <div id="reportText">Reports are displayed for last 15 days</div>
        <div>
            <div id="reportColumn1" style="float: left;width: 58%;font-weight: bold;margin-left: 10px;margin-top: 0px;">
                <div id="topicProgressMsgDiv" style="display: none;"><div style='padding-top: 24%;text-align:center;font-weight: normal;'><?= $_SESSION['childNameUsed'] ?> has not progressed much in Mindspark, in the last 15 days.</div></div>
                <div id="chart_div" style="height: 400px; text-align: left; vertical-align: center;"><img style="padding-top: 24%;margin-left: 48%;" src="assets/loader.gif" title="Please wait while the content is loading" /></div>
                <br />
		<div style="width:100%;text-align:center;padding-bottom:20px;font-weight: normal;"><a href="topicUsage.php" style="text-decoration:underline;font-size:1.3em;color:blue;">TOPIC PROGRESS</a></div>
            </div>
            <div id="reportColumn2" style="float: right; width: 40%;">
                <div id="usageMsgDiv" style="display: none;"><div style='padding-top: 24%;text-align:center;font-weight: normal;'><?= $_SESSION['childNameUsed'] ?> has not progressed much in Mindspark, in the last 15 days.</div></div>
                <div id="chart_divUsage" style="height: 400px; text-align: left; vertical-align: center;"><img style="padding-top: 38%;margin-left: 48%;" src="assets/loader.gif" title="Please wait while the content is loading" /></div>
                <br/>
                <div style="width:100%;text-align:center;padding-bottom:20px;"><a href="usage.php" style="text-decoration:underline;font-size:1.3em;color:blue;">USAGE</a></div>
            </div>
        </div>
    </div>
	
	<div style="display:none">
        <div id="freeTrialMessage" class="freeTrialMessage">
			<div style="width: 100%;height:2px;"></div>
			<p style="font-size:1.3em;"><b>Subscription about to expire!</b></p>
            <p>Your mindspark free trial is going to expire tommorow. An immediate renewal would ensure that there is no discontinuity in your Mindspark usage.</p>
			<p>
				<a href="http://mindspark.in/registration.php?userID=<?=$_SESSION['childID']?>" target="_blank" style="text-decoration: underline;color:blue;">Click here</a> to renew the subscription.
			</p>
        </div>
    </div>
	<div style="display:none">
        <div id="freeTrialMessage1" class="freeTrialMessage">
			<div style="width: 100%;height:2px;"></div>
			<p style="font-size:1.3em;"><b>Subscription expired!</b></p>
            <p>Your child's Minspark free trial has expired.</p>
			<p>
				<a href="http://mindspark.in/registration.php?userID=<?=$_SESSION['childID']?>" target="_blank" style="text-decoration: underline;color:blue;">Click here</a> to renew the subscription.
			</p>
        </div>
    </div>
	<?php 
		$user1   = new User($_SESSION['childID']);
		$endDate = new DateTime($user1->endDate);
		$datetime1 = new DateTime();
		$interval = $datetime1->diff($endDate);
		$interval = $interval->format('%R%a');
		$freeTrial=0;
		for($i=0; $i<count($childID); $i++)	{
			if($childID[$i]==$_SESSION['childID']){
				if($childIDFree[$i]==1){
					$freeTrial = 1;
				}
			}
		}
		if($interval==0  && $freeTrial==1){
	?>
	<script>
		$.fn.colorbox({'href':'#freeTrialMessage','inline':true,'open':true,'escKey':true, 'height':220, 'width':500});
	</script>
	<?php }else if($interval<0  && $freeTrial==1){ ?>
	<script>
		$.fn.colorbox({'href':'#freeTrialMessage1','inline':true,'open':true,'escKey':true, 'height':220, 'width':500});
	</script>
	<?php } ?>
    <?php include("footer.php") ?>