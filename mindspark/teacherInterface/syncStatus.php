<?php include("header.php");

$schoolCode = $_SESSION['schoolCode'];
/*$schoolCode = 174756;*/
if ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']){
	header('Location: otherFeatures.php');
}
?>

<title>Sync Status</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/commonWrongAnswers.css" rel="stylesheet" type="text/css">
<link href="css/syncStatus.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/colorbox.css" />
<script src="../userInterface/libs/jquery.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />
<script src="libs/jquery-ui.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");
	}
	
	function getSyncStatus()
	{	
		var timeOutId = 0;
		var params = "";
		params+="mode=offlineSyncStatus"; 
		params+="&schoolCode=<?=$schoolCode?>";
		$.post("syncStatusAjax.php",""+params+"",function(transport){
		//alert(transport);
		data = transport.split('~');
		//$('#lastSynced').html("Last Synced at : <span style='color:black'>"+data[1]+"</span>");
		$('#syncStatus').html("Sync Status : <span style='color:black'>"+data[0]+"</span>");
		$('#nextSync').html("Next sync scheduled at : <span style='color:black'>"+data[2]+"</span>");
		if(data[0]=="In Progress")
		$('#localLiveSync').css('display','block');
		else
		$('#localLiveSync').css('display','none');
		$('#lastSyncedLocal').html("Last Synced at local : <span style='color:black'>"+data[3]+"</span>");
		$('#lastSyncedLive').html("Last Synced at live : <span style='color:black'>"+data[4]+"</span>");	
		
		 setTimeout(getSyncStatus, 10000);
		 	 
		});
	}
	
	function openSyncLogTable()
	{  
		var params = "";
		params+="mode=syncLog"; 
		params+="&schoolCode=<?=$schoolCode?>";
		$.post("syncStatusAjax.php",""+params+"",function(transport){
			$('#SyncLogTables').html(transport);
		});
		$.fn.colorbox({'href':'#SyncLogTables','inline':true,'open':true,'escKey':true, 'height':500, 'width':700});
	}
</script>

</head>
<body class="translation" onLoad="load();getSyncStatus();" onResize="load()>
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
	
	<div id="introContainer">
		<form name="frmWrongQuestion" method="post" action="<?=$_SERVER['PHP_SELF']?>" style="">

		<table  border="0" cellpadding="2" cellspacing="2" >
			<tr>
			<td class="contentTab">
			<div class="arrow-black" style="margin-top: 9%"></div>
					<div id="pageText" style="font-size:14px;font-weight:normal;color:black;margin-top:6.2%">Sync Status Report</div>
			</td>		
			</tr>
			
			<tr>
			<td>
				<span id="showSynclog" class="syncStatusMsg" style="margin-left:20%;cursor:pointer;" onclick="openSyncLogTable();"><u> Show Sync Log </u></span>	
			</td>
			</tr>
			
			</table>
			<br>
			<hr>
		</form>
	</div>
	
	<div id="topContainer" style="margin-top:3%;">
	
	<div class="syncStatusMsg" id="lastSynced">  </div> 
	<div class="syncStatusMsg" id="syncStatus"> 
	</div>
	<div class="syncStatusMsg" id="nextSync">  </div>
	<br>
	
	
	</div>
	
	<div id="bottomContainer">
	
	<div class="imageContainer left" style="margin-top:3.5%">
	<img id="localServer" src="assets/offline/localServer.jpg" alt="Local Sever" width="130" height="170" />
	</div>
	
	<div id="localLiveSync" class="left" style="margin-top: 10%;margin-left: 3%;">
	<img src="assets/offline/arrow.gif" alt="Syncing" style="margin-left:25%"/>
	</div>
	
	<div class="imageContainer" style="margin-left:43%">
	<img id="server" src="assets/offline/server.jpg" alt="Sever" width="150" height="250" />
	</div>
	
	
	<div class="syncStatusMsg left" id="lastSyncedLocal">  
	</div> 
	<div class="syncStatusMsg" style="margin-left:46%" id="lastSyncedLive">
	</div>
	
	</div>
	
	</div>
	
	<div style="display:none">
		<div id="SyncLogTables">
			
		</div>
		</div>

<?php include("footer.php") ?>