<?php
	include("header.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$ids			=	$_GET['id'];
	$arrayDetails	=	getAnnouncements($ids);
?>

<title>Home</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		/*var fixedSideBarHeight = window.innerHeight;*/
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		/*$("#fixedSideBar").css("height",fixedSideBarHeight+"px");*/
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
	}
</script>
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

	<div id="container"><div align="center" id="pnlData"> 
<br/><br/><br/>
<div style="font-size: 18px; background-color: rgb(201, 218, 146); width: 80%; border: medium solid rgb(8, 80, 145); padding-bottom: 6px; padding-top: 6px;"><?=$arrayDetails['title']?></div>
<br><br><br>
<?php
    if($arrayDetails['category']=="Topic") {
        $clusterDetails = getClusterDetails($arrayDetails['contentId']);

?>
    <table class="tblContent" cellpadding="3" cellspacing="0">
        <tr>
            <th>Learning Unit</th>
            <th>Level</th>
        </tr>
    <?php foreach ($clusterDetails as $code=>$arrDetails) { ?>
        <tr>
            <td><?=$arrDetails["desc"]?></td>
            <td><?=$arrDetails["ms_level"]?></td>
        </tr>
    <?php } ?>
    </table>
<?php } else { ?>

<div style="font-size: 18px; width: 80%;padding: 6px;" align="left"><?=$arrayDetails['description']?></div>
<?php } ?>

</div>
</div>

<?php include("footer.php") ?>
<?php

function getAnnouncements($ids)
{
	$arrayDetails	=	array();
	$sq	=	"SELECT title,description, category, contentId FROM adepts_teacherAnnouncements WHERE id=$ids";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_assoc($rs);
	$arrayDetails['description']	=	$rw['description'];
	$arrayDetails['title']			=	$rw['title'];
	$arrayDetails['category']		=	$rw['category'];
	$arrayDetails['contentId']		=	$rw['contentId'];
	return $arrayDetails;
}

function getClusterDetails($ttCode)
{
    $arrClusterDetails = array();
    $query = "SELECT a.clusterCode, cluster, b.ms_level FROM adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
              WHERE  a.clusterCode=b.clusterCode AND b.status='live' AND a.teacherTopicCode='$ttCode'
              ORDER  BY a.flowno";
    $result = mysql_query($query);
    while ($line = mysql_fetch_array($result))
    {
        $arrClusterDetails[$line['clusterCode']]["desc"] = $line[1];
        $arrClusterDetails[$line['clusterCode']]["ms_level"] = $line[2];
    }
    return $arrClusterDetails;
}
?>