<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include_once("../../check1.php");
	include("../../classes/clsUser.php");
	include("../../constants.php");
	include("../../classes/clsRewardSystem.php");
	
	if(!isset($_SESSION['userID']))
	{
		header("Location:../../logout.php");
		exit;
	}
	$userID = $_SESSION['userID'];
	$objUser = new User($userID);
	$currentYear =date('Y');
	$currentMonth=date('m');
	
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;
	$schoolCode    = $objUser->schoolCode;
	$registrationDate = $objUser->registrationDate;
	$startDate = $objUser->startDate;
	$endDate = $objUser->endDate;
	$classChangeHistory = $objUser->classChangeHistory;
	$startYear = substr($objUser->startDate,0,4);
	$currentYear = substr($registrationDate,0,4);
	$sparkieInformation = new Sparkies($userID);
	$sparkieLogic = $sparkieInformation->getSparkieLogic('badge');
	$badges = array_values($sparkieLogic);
	$badgesArray = $sparkieInformation->checkForBadgesEarned();
	$prevBadgesArray = $sparkieInformation->previousBadgesEarned();
	
	$timelineSparkie1="";
	$sparkieLogic = $sparkieInformation->getSparkieLogic();
	$sparkiesEarned = $sparkieInformation->getTotalSparkies();

	if(!$classChangeHistory){ $classesSoFar=array();$classChangeHistory="";}
	else $classesSoFar=explode(",", $classChangeHistory);
	$clsStartDate=$startDate;
	$sparkiesYW=array();
	$totalSparkies=0;//var_dump($classesSoFar);echo $startDate;
	for ($l=0; $l <count($classesSoFar) ; $l++) { 
		$thisClass=explode("~", $classesSoFar[$l]);
		
		$query = 'select sparkies,class,year(startDate) from adepts_rewardPoints_archive where startDate >= "'.$clsStartDate.'" and startDate<"'.$thisClass[1].'" and class="'.$thisClass[0].'" and userID='.$userID;//echo $query;
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result)>0){
			while($line = mysql_fetch_array($result))
			{
				$sparkiesYW[$line[1].'~'.$line[2]]=$line[0];
				$classHistory[$i]=$line[1];
				$totalSparkies = $totalSparkies + $line[0];

				$sparkies[$line[1].'~'.$line[2]] = $line[0];
				$superSparkies[$line[1].'~'.$line[2]]=0;
				$megaSparkies[$line[1].'~'.$line[2]]=0;
				$extraSparkies[$line[1].'~'.$line[2]]=0;

				if($line[0] > $sparkieLogic['mileStone1']['sparkieNeeded']){
					$sparkies[$line[1].'~'.$line[2]] = 0;
					$superSparkies[$line[1].'~'.$line[2]] = $line[0];
					if($line[0] > $sparkieLogic['mileStone2']['sparkieNeeded']){
						$superSparkies[$line[1].'~'.$line[2]]=0;
						$megaSparkies[$line[1].'~'.$line[2]] = $line[0];
						if($line[0] > $sparkieLogic['mileStone3']['sparkieNeeded']){
							$extraSparkies[$line[1].'~'.$line[2]] = $line[0];
							$extraSparkieCount++;
							$megaSparkies[$line[1].'~'.$line[2]] = 0;
						}
					}
				}
			}
		}
		else {
			$sparkiesYW[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))]=0;
			$sparkies[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))] = 0;
			$superSparkies[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))] = 0;
			$megaSparkies[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))] = 0;
			$extraSparkies[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))] = 0;
		}

		for($i=0;$i<sizeOf($prevBadgesArray);$i++){
			foreach ($badges as $j) {
				if(isset($prevBadgesArray[$i][$j['logicType']])){
					if ($prevBadgesArray[$i][$j['logicType']]['batchDate']>= "$clsStartDate" && $prevBadgesArray[$i][$j['logicType']]['batchDate']<"$thisClass[1]"){
						$showBadges[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))][$j['logicType']]+=1;
						$showBadgesUnlocked[$thisClass[0].'~'.date("Y",strtotime($clsStartDate))][$j['logicType']]=1;
					}
				}
			}
		}
		$clsStartDate=$thisClass[1];
	}
	
	$query = 'select sparkies,year(startDate) from adepts_rewardPoints where userID='.$userID;
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result)>0){
		while($line = mysql_fetch_array($result))
		{
			$sparkiesYW[$childClass.'~'.$line[1]]=$line[0];
			$classHistory[$i]=$childClass;
			$totalSparkies = $totalSparkies + $line[0];

			$sparkies[$childClass.'~'.$line[1]] = $line[0];
			$superSparkies[$childClass.'~'.$line[1]]=0;
			$megaSparkies[$childClass.'~'.$line[1]]=0;
			$extraSparkies[$childClass.'~'.$line[1]]=0;
			if($line[0] > $sparkieLogic['mileStone1']['sparkieNeeded']){
				$sparkies[$childClass.'~'.$line[1]] = 0;
				$superSparkies[$childClass.'~'.$line[1]] = $line[0];
				if($line[0] > $sparkieLogic['mileStone2']['sparkieNeeded']){
					$superSparkies[$childClass.'~'.$line[1]]=0;
					$megaSparkies[$childClass.'~'.$line[1]] = $line[0];
					if($line[0] > $sparkieLogic['mileStone3']['sparkieNeeded']){
						$extraSparkies[$childClass.'~'.$line[1]] = $line[0];
						$extraSparkieCount++;
						$megaSparkies[$childClass.'~'.$line[1]] = 0;
					}
				}
			}
		}
	}
	else {
		$sparkiesYW[$childClass.'~'.date("Y",strtotime($clsStartDate))]=0;
		$sparkies[$childClass.'~'.date("Y",strtotime($clsStartDate))] = 0;
		$superSparkies[$childClass.'~'.date("Y",strtotime($clsStartDate))] = 0;
		$megaSparkies[$childClass.'~'.date("Y",strtotime($clsStartDate))] = 0;
		$extraSparkies[$childClass.'~'.date("Y",strtotime($clsStartDate))] = 0;
	}
	for($i=0;$i<sizeOf($badgesArray);$i++){
		foreach ($badges as $j) {
			if(isset($badgesArray[$i][$j['logicType']])){
				if ($badgesArray[$i][$j['logicType']]['batchDate']>= "$clsStartDate"){
					$showBadges[$childClass.'~'.date("Y",strtotime($clsStartDate))][$j['logicType']]+=1;
					$showBadgesUnlocked[$childClass.'~'.date("Y",strtotime($clsStartDate))][$j['logicType']]=1;
				}
			}
		}
	}

	$timelineSparkie1="";
	$sparkieLogic = $sparkieInformation->getSparkieLogic();
	$sparkiesEarned = $sparkieInformation->getTotalSparkies();
	
	if($sparkiesEarned > $sparkieLogic['mileStone1']['sparkieNeeded']){
		$timelineSparkie1 = "milestone1";
		if($sparkiesEarned > $sparkieLogic['mileStone2']['sparkieNeeded']){
			$timelineSparkie1 = "milestone2";
			if($sparkiesEarned > $sparkieLogic['mileStone3']['sparkieNeeded']){
				$timelineSparkie1 = "milestone3";
			}
		}
	}
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("../../header.php");?>

<title>Rewards Central</title>
	<script src="../../libs/jquery.js"></script>
	<?php
	if($theme==2) { ?>
	<link href="../../css/historyDashboard/midClass.css?ver=1" rel="stylesheet" type="text/css">
    <link href="../../css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		var portraitCounter=0;
		function load(){
			portraitCounter++;
			$(".notAttempted").show();
			$("#largeContainer").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			var a= window.innerHeight - (100+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
			$('input[name=check]').click(showCheckedValues);
			if(portraitCounter==1){
				clearCanvas();
				var badgesArray=[];
				var badgesArray1=[];
				var badgesArray2=[];
				var imgArray =['../../assets/rewards/badgesMyHistorySection/Level 0 - 250.png','../../assets/rewards/badgesMyHistorySection/Level 1 - 750.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500+.png'];
				var i=4;
				<?php 
					foreach ($badges as $j){
				?>
					imgArray[i]= '../../assets/rewards/badgesMyHistorySection/'+<?php echo json_encode($j['logicType']); ?>+'.png';
					i++;
				<?php 
					}
				?>
				var angleLength = 360/imgArray.length;
				var data = new Array();
				for(var k=0;k<imgArray.length;k++){
					data[k] = angleLength;
				}
				CreatePieChart("historyPie",data,badgesArray,badgesArray1,badgesArray2,imgArray);
			}
		}
		function showHideBar(){
			if (infoClick==0){
				$("#hideShowBar").text("+");
				$('#info_bar').animate({'height':'75px'},600);
				$('#topic').animate({'height':'55px'},600);
				$('#clickText').animate({'margin-top':'1px'},600);
				$('#sparkieBarMid').hide();
				$('.Name').hide();
				$('.class').hide();
				var a= window.innerHeight -150 -45;
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=1;
			}
			else if(infoClick==1){
				$("#hideShowBar").text("-");
				$('#info_bar').animate({'height':'140px'},600);
				$('#topic').animate({'height':'115px'},600);
				$('#clickText').animate({'margin-top':'10px'},600);
				$('.Name').show();
				$('#sparkieBarMid').show(500);
				$('.class').show();
				var a= window.innerHeight - (100+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=0;
			}
		}
	</script>
	<?php } else if($theme==3) { ?>
	    <link href="../../css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="../../css/historyDashboard/higherClass.css" rel="stylesheet" type="text/css">
		<script>
		var portraitCounter=0;
		function load(){
			portraitCounter++;
			var a= window.innerHeight - (170);
			$('#activitiesContainer').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			var ua1 = navigator.userAgent;
			if( ua1.indexOf("Android") >= 0 )
			{
				$(".circleContainer").css("margin-bottom","50px");
				$(".circleContainer").css("margin-right","50px");
				$("#activitiesContainer").css("height","auto");
				$('#menu_bar').css("height",$('#activitiesContainer').css("height"));
				$("#sideBar").css("height",$("#activitiesContainer").css("height"));
				$("#main_bar").css("height",$("#activitiesContainer").css("height"));
			}
			$('input[name=check]').click(showCheckedValues);
			if(portraitCounter==1){
				clearCanvas();
				var badgesArray=[];
				var badgesArray1=[];
				var badgesArray2=[];
				var imgArray =['../../assets/rewards/badgesMyHistorySection/Level 0 - 250.png','../../assets/rewards/badgesMyHistorySection/Level 1 - 750.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500+.png'];
				var i=4;
				<?php 
					foreach ($badges as $j){
				?>
					imgArray[i]= '../../assets/rewards/badgesMyHistorySection/'+<?php echo json_encode($j['logicType']); ?>+'.png';
					i++;
				<?php 
					}
				?>
				var angleLength = 360/imgArray.length;
				var data = new Array();
				for(var k=0;k<imgArray.length;k++){
					data[k] = angleLength;
				}
				CreatePieChart("historyPie",data,badgesArray,badgesArray1,badgesArray2,imgArray);
			}
		}
	</script>
	<?php } ?>
	<script type="text/javascript" src="../../libs/i18next.js"></script>
	<script type="text/javascript" src="../../libs/translation.js"></script>
	<script type="text/javascript" src="../../libs/closeDetection.js"></script>
    <script>
	var langType	=	'<?=$language?>';
	var click=0;
	function getHome()
	{
		setTryingToUnload();
		window.location.href	=	"../../home.php";
	}
	function logoff()
	{
		setTryingToUnload();
		window.location="../../logout.php";
	}
	function openMainBar(){
	
		if(click==0){
			if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
			$("#vertical").css("display","none");
			click=1;
		}
		else if(click==1){
			$("#main_bar").animate({'width':'26px'},600);
			$("#plus").animate({'margin-left':'7px'},600);
			$("#vertical").css("display","block");
			click=0;
		}
	}
    </script>
	<script>
	function showCheckedValues() {
		var checked = $('input[name=check]:checked').map(function() {return this.value;}).get();
		var ch=checked.length;
		if(checked.length>3){
			alert("You can select a maximum of 3 grades.");
			return false;
		}
		clearCanvas();
		$("#yearsText").html("");
		$("#bar1").hide();		$("#badgeText1").hide();
		$("#bar2").hide();		$("#badgeText2").hide();$("#bar2").css("background-color","#9ec956");
		$("#bar3").hide();		$("#badgeText3").hide();$("#bar3").css("background-color","#2f99cb");

		var imgArray =['../../assets/rewards/badgesMyHistorySection/Level 0 - 250Locked.png','../../assets/rewards/badgesMyHistorySection/Level 1 - 750Locked.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500Locked.png','../../assets/rewards/badgesMyHistorySection/Level 2 - 1500+Locked.png']
		var i=4;
		<?php 
			foreach ($badges as $j){ ?>
				imgArray[i]= '../../assets/rewards/badgesMyHistorySection/'+<?php echo json_encode($j['logicType']); ?>+'Locked.png';
				i++;
			<?php }
		?>

		var badgesArray=[[],[],[]]; 

		var showingYears=[];
		var classyears=[];var clsSparkies={};var badgeNames=[];
		<?php foreach ($sparkies as $key => $value) { ?>
			classyears.push('<?php echo $key; ?>');
			clsSparkies['<?php echo $key; ?>']=[<?php echo json_encode($sparkies[$key]); ?>,<?php echo json_encode($superSparkies[$key]); ?>,<?php echo json_encode($megaSparkies[$key]); ?>,<?php echo json_encode($extraSparkies[$key]); ?>];
			var m=4;
			<?php 
			foreach ($badges as $j){
			?>
				clsSparkies['<?php echo $key; ?>'][m]=<?php echo json_encode($showBadges[$key][$j['logicType']]?$showBadges[$key][$j['logicType']]:0); ?>;
				badgeNames[m]='<?php echo $j["logicType"] ?>';
				m++;
			<?php 
			}
		}	?>
		for (var i = 0; i<checked.length; i++) {
			showingYears.push(checked[i].replace(/([0-9]*)\~([0-9]*)/g,"$2(Grade $1)"));
			$("#bar"+(i+1)).show();$("#badgeText"+(i+1)).html($("#"+checked[i].replace('~',"_")).html());$("#badgeText"+(i+1)).show();

			badgesArray[i]=clsSparkies[checked[i]];
			if(badgesArray[i][1]!=0){
				imgArray[0]=(imgArray[0].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 0 - 250.png':imgArray[0];
			}else if(badgesArray[i][2]!=0){
				imgArray[0]=(imgArray[0].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 0 - 250.png':imgArray[0];
				imgArray[1]=(imgArray[1].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 1 - 750.png':imgArray[1];
			}else if(badgesArray[i][3]!=0){
				imgArray[0]=(imgArray[0].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 0 - 250.png':imgArray[0];
				imgArray[1]=(imgArray[1].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 1 - 750.png':imgArray[1];
				imgArray[2]=(imgArray[2].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 2 - 1500.png':imgArray[2];
				imgArray[3]=(imgArray[3].indexOf('Locked')>0)?'../../assets/rewards/badgesMyHistorySection/Level 2 - 1500+.png':imgArray[3];
			}
			for (var m = 4; m < badgesArray[i].length; m++) {
				if(badgesArray[i][m]!=0){
					imgArray[m]= '../../assets/rewards/badgesMyHistorySection/'+badgeNames[m]+'.png';
				}else{
					imgArray[m]= '../../assets/rewards/badgesMyHistorySection/'+badgeNames[m]+'Locked.png';
				}
			}
		};
		$("#yearsText").html(showingYears.join(', '));
	   	
		var angleLength = 360/imgArray.length;
		var data = new Array();
		for(var k=0;k<imgArray.length;k++){
			data[k] = angleLength;
		}
		CreatePieChart("historyPie",data,badgesArray[0],badgesArray[1],badgesArray[2],imgArray);
						
		// do something with values array
	}
			
	function clearCanvas(){
		var canvas = document.getElementById("historyPie");
		var ctx = canvas.getContext("2d");
		ctx.clearRect(0,0,500,500);
	}
	var extraSparkieCheck=<?php echo json_encode($extraSparkieCount); ?> + 1;
	var badgesArr=[];
    function CreatePieChart(canvas,data,badgesArray,badgesArray1,badgesArray2,imgArray)
    {
		var canvasName = canvas;
		var data = data;
		var imgArray=imgArray;
		var counter=0;
		var counter1=0;
		var counter2=0;
		var radiusBadge =0;
        var canvas = document.getElementById(canvasName);
		var ctx = canvas.getContext("2d");
		var lastend = 0;
		var myTotal = 0;
		for(var e = 0; e < data.length; e++)
		{
		  myTotal += data[e];
		}
		var imageObj = new Array();
		var positionX = new Array();
		var positionY = new Array();
		var positionX1 = new Array();
		var positionY1 = new Array();
		var positionX2 = new Array();
		var positionY2 = new Array();
		var radius1 = new Array();
		var angle=0;
		var currentYear=<?php echo json_encode($currentYear); ?>;
		var badgesArray=badgesArray;
		var badgesArray1=badgesArray1;
		var badgesArray2=badgesArray2;
		badgesArr=[badgesArray,badgesArray1,badgesArray2];
		for (var i = 0; i < badgesArr.length; i++) {
			if (badgesArr[i].length<data.length) continue;

		};
		for (var i = 0; i < data.length; i++) 
		{
			positionX[i] = 0;positionY[i] = 0;
			positionX1[i] = 0;positionY1[i] =0;
			positionX2[i] = 0;positionY2[i] = 0;
		    //ctx.fillStyle = myColor[i];
		    ctx.beginPath();
		    ctx.lineTo(canvas.width/2,canvas.height/2);
			ctx.strokeStyle = '#B3B3B3';
			ctx.lineWidth = 1;
			ctx.stroke();
			ctx.closePath();
		    // Arc Parameters: x, y, radius, startingAngle (radians), endingAngle (radians), antiClockwise (boolean)
			if(badgesArray[i]!=0 || badgesArray[i]!=""){
		        var radius = 9;
				var radians = angle / 180 * Math.PI;
				if(i==0){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/400)*badgesArray[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/400)*badgesArray[i];
				}else if(i==1){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/950)*badgesArray[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/950)*badgesArray[i];
				}else if(i==2){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray[i];
				}else if(i==3){
					if(badgesArray[i]>1500){
						var endX = canvas.width/2 + canvas.width/2 * (extraSparkieCheck-1) * ((Math.cos(radians)/2100)*2000)/extraSparkieCheck;
		 				var endY = canvas.height/2 - canvas.height/2 * (extraSparkieCheck-1) * ((Math.sin(radians)/2100)*2000)/extraSparkieCheck;
					}else{
						var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray[i];
		 				var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray[i];
					}
					
				}else{
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/15)*badgesArray[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/15)*badgesArray[i];
				}
				positionX[i] = endX;
				positionY[i] = endY;
			}
			if(badgesArray1[i]!=0 || badgesArray1[i]!="" ){
		        var radius = 9;
				var radians = angle / 180 * Math.PI;
				if(i==0){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/400)*badgesArray1[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/400)*badgesArray1[i];
				}else if(i==1){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/950)*badgesArray1[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/950)*badgesArray1[i];
				}else if(i==2){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray1[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray1[i];
				}else if(i==3){
					if(badgesArray1[i]>1500){
						var endX = canvas.width/2 + canvas.width/2 * (extraSparkieCheck-2) * ((Math.cos(radians)/2100)*2000)/extraSparkieCheck;
		 				var endY = canvas.height/2 - canvas.height/2 * (extraSparkieCheck-2) * ((Math.sin(radians)/2100)*2000)/extraSparkieCheck;
					}else{
						var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray1[i];
		 				var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray1[i];
					}
				}else{
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/15)*badgesArray1[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/15)*badgesArray1[i];
				}
				positionX1[i] = endX;
				positionY1[i] = endY;
			}
			if(badgesArray2[i]!=0 || badgesArray2[i]!=""){
		        var radius = 9;
				var radians = angle / 180 * Math.PI;
				if(i==0){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/400)*badgesArray2[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/400)*badgesArray2[i];
				}else if(i==1){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/950)*badgesArray2[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/950)*badgesArray2[i];
				}else if(i==2){
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray2[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray2[i];
				}else if(i==3){
					if(badgesArray2[i]>1500){
						var endX = canvas.width/2 + canvas.width/2 * (extraSparkieCheck-3) * ((Math.cos(radians)/2100)*2000)/extraSparkieCheck;
		 				var endY = canvas.height/2 - canvas.height/2 * (extraSparkieCheck-3) * ((Math.sin(radians)/2100)*2000)/extraSparkieCheck;
					}else{
						var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/2100)*badgesArray2[i];
		 				var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/2100)*badgesArray2[i];
					}
				}else{
					var endX = canvas.width/2 + canvas.width/2 * (Math.cos(radians)/15)*badgesArray2[i];
			 		var endY = canvas.height/2 - canvas.height/2 * (Math.sin(radians)/15)*badgesArray2[i];
				}
				positionX2[i] = endX;
				positionY2[i] = endY;
			}
			ctx.closePath();
			ctx.strokeStyle = '#B3B3B3';
			ctx.beginPath();
			ctx.moveTo(canvas.width/2,canvas.height/2);
		    ctx.arc(canvas.width/2,canvas.height/2,canvas.height/2-50,lastend,lastend+(Math.PI*2*(data[i]/myTotal)),false);
			var radians = angle / 180 * Math.PI;
			imageObj[i] = new Image();
			imageObj[i].X = (canvas.width/2-30) + (canvas.width/2-50) * Math.cos(radians);
			imageObj[i].Y = (canvas.height/2-30) - (canvas.height/2-50) * Math.sin(radians);
			imageObj[i].src = imgArray[i];
		    imageObj[i].onload = function() {
		       ctx.drawImage(this, this.X, this.Y);
		    };
			ctx.strokeStyle = '#B3B3B3';
			angle = angle + data[0];
		    ctx.lineTo(canvas.width/2,canvas.height/2);
			ctx.lineWidth = 1;
			ctx.stroke();
			ctx.closePath();
			
		    lastend += Math.PI*2*(data[i]/myTotal);
		}
		ctx.beginPath();
		ctx.moveTo(canvas.width/2,canvas.height/2);
		if(positionX.length!=""){
			for(i=0;i<positionX.length;i++){
				if(positionX[i]!=0 && counter==0){
					counter=i+1;
					ctx.moveTo(positionX[i],positionY[i]);
				}
				if(positionX[i]!=0){
					ctx.strokeStyle = '#e75903';
					ctx.lineTo(positionX[i],positionY[i]);
					ctx.lineWidth = 1;
					ctx.stroke();
				}
			}
			ctx.lineTo(positionX[counter-1],positionY[counter-1]);
			ctx.lineWidth = 1;
			ctx.stroke();
			ctx.closePath();
			ctx.beginPath();
			ctx.moveTo(canvas.width/2,canvas.height/2);
			for(i=0;i<positionX.length;i++){
				if(positionX[i]!=0 && counter==0){
					counter=i+1;
					ctx.moveTo(positionX[i],positionY[i]);
				}
				if(positionX[i]!=0){
					ctx.strokeStyle = '#666666';
					ctx.lineTo(canvas.width/2,canvas.height/2);
					ctx.lineTo(positionX[i],positionY[i]);
					ctx.lineWidth = 1;
					ctx.stroke();
				}
			}
			ctx.closePath();
			for(i=0;i<positionX.length;i++){
				if(positionX[i]!=0){
					ctx.strokeStyle = '#e75903';
					ctx.beginPath();
					if(i==1 || i==2|| i==0|| i==3){
						radius=14;
					}else{
						radius=9;
					}
			        ctx.arc(positionX[i], positionY[i], radius, 0, 2 * Math.PI);
					ctx.fillStyle = '#ffffff';
					ctx.fill();
					ctx.lineWidth = 2;
			        ctx.stroke();
				    ctx.closePath();
					ctx.lineWidth = 1;
				}
			}
			for(i=0;i<positionX.length;i++){
				if(positionX[i]!=0){
					ctx.strokeStyle = '#e75903';
					ctx.beginPath();
					ctx.fillStyle = '#000';
					if(i==1 || i==2|| i==0|| i==3){
						ctx.fillText(badgesArray[i],positionX[i]-11,positionY[i]+3);
					}else{
						ctx.fillText(badgesArray[i],positionX[i]-4,positionY[i]+3);
					}
					
					ctx.fill();
					ctx.lineWidth = 2;
			        ctx.stroke();
				    ctx.closePath();
					ctx.lineWidth = 1;
				}
			}
		}
		if(positionX1.length!=""){
		for(i=0;i<positionX1.length;i++){
			if(positionX1[i]!=0 && counter1==0){
				counter1=i+1;
				ctx.moveTo(positionX1[i],positionY1[i]);
			}
			if(positionX1[i]!=0){
				ctx.strokeStyle = '#9ec956';
				ctx.lineTo(positionX1[i],positionY1[i]);
				ctx.lineWidth = 1;
				ctx.stroke();
			}
		}
		ctx.lineTo(positionX1[counter1-1],positionY1[counter1-1]);
		ctx.lineWidth = 1;
		ctx.stroke();
		ctx.closePath();
		ctx.beginPath();
			ctx.moveTo(canvas.width/2,canvas.height/2);
			for(i=0;i<positionX1.length;i++){
				if(positionX[i]!=0 && counter==0){
					counter=i+1;
					ctx.moveTo(positionX1[i],positionY1[i]);
				}
				if(positionX1[i]!=0){
					ctx.strokeStyle = '#666666';
					ctx.lineTo(canvas.width/2,canvas.height/2);
					ctx.lineTo(positionX1[i],positionY1[i]);
					ctx.lineWidth = 1;
					ctx.stroke();
				}
			}
			ctx.closePath();
		for(i=0;i<positionX1.length;i++){
			if(positionX1[i]!=0){
				ctx.strokeStyle = '#9ec956';
				ctx.beginPath();
				if(i==1 || i==2|| i==0 || i==3){
					radius=14;
				}else{
					radius=9;
				}
		        ctx.arc(positionX1[i], positionY1[i], radius, 0, 2 * Math.PI);
				ctx.fillStyle = '#ffffff';
				ctx.fill();
				ctx.lineWidth = 2;
		        ctx.stroke();
			    ctx.closePath();
				ctx.lineWidth = 1;
			}
		}
		for(i=0;i<positionX1.length;i++){
			if(positionX1[i]!=0){
				ctx.strokeStyle = '#9ec956';
				ctx.beginPath();
				ctx.fillStyle = '#000';
				if(i==1 || i==2|| i==0 || i==3){
					ctx.fillText(badgesArray1[i],positionX1[i]-11,positionY1[i]+3);
				}else{
					ctx.fillText(badgesArray1[i],positionX1[i]-4,positionY1[i]+3);
				}
				ctx.fill();
				ctx.lineWidth = 2;
		        ctx.stroke();
			    ctx.closePath();
				ctx.lineWidth = 1;
			}
		}
		}
		if(positionX2.length!=0){
		for(i=0;i<positionX2.length;i++){
			if(positionX2[i]!=0 && counter2==0){
				counter2=i+1;
				ctx.moveTo(positionX2[i],positionY2[i]);
			}
			if(positionX2[i]!=0){
				ctx.strokeStyle = '#2f99cb';
				ctx.lineTo(positionX2[i],positionY2[i]);
				ctx.lineWidth = 1;
				ctx.stroke();
			}
		}
		ctx.lineTo(positionX2[counter2-1],positionY2[counter2-1]);
		ctx.lineWidth = 1;
		ctx.stroke();
		ctx.closePath();
		ctx.beginPath();
			ctx.moveTo(canvas.width/2,canvas.height/2);
			for(i=0;i<positionX2.length;i++){
				if(positionX2[i]!=0 && counter==0){
					counter=i+1;
					ctx.moveTo(positionX2[i],positionY2[i]);
				}
				if(positionX2[i]!=0){
					ctx.strokeStyle = '#666666';
					ctx.lineTo(canvas.width/2,canvas.height/2);
					ctx.lineTo(positionX2[i],positionY2[i]);
					ctx.lineWidth = 1;
					ctx.stroke();
				}
			}
			ctx.closePath();
		for(i=0;i<positionX2.length;i++){
			if(positionX2[i]!=0){
				ctx.strokeStyle = '#2f99cb';
				ctx.beginPath();
				if(i==1 || i==2|| i==0 || i==3){
					radius=14;
				}else{
					radius=9;
				}
		        ctx.arc(positionX2[i], positionY2[i], radius, 0, 2 * Math.PI);
				ctx.fillStyle = '#ffffff';
				ctx.fill();
				ctx.lineWidth = 2;
		        ctx.stroke();
			    ctx.closePath();
				ctx.lineWidth = 1;
			}
		}
		for(i=0;i<positionX2.length;i++){
			if(positionX2[i]!=0){
				ctx.strokeStyle = '#2f99cb';
				ctx.beginPath();
				ctx.fillStyle = '#000';
				if(i==1 || i==2|| i==0 || i==3){
					ctx.fillText(badgesArray2[i],positionX2[i]-11,positionY2[i]+3);
				}else{
					ctx.fillText(badgesArray2[i],positionX2[i]-4,positionY2[i]+3);
				}
				ctx.fill();
				ctx.lineWidth = 2;
		        ctx.stroke();
			    ctx.closePath();
				ctx.lineWidth = 1;
			}
		}
		}
    }

</script>
</head>
<body onLoad="load();" onResize="load();" class="translation">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='../../myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='../../changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='../../whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='../../logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<div id="home" class="linkPointer" onClick="getHome()"></div>
				<div class="icon_text2"> - <span class="textUppercase">Rewards Central</span></font></div>
			</div>
			<div id="topic">
					<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase">Rewards Central</span></font></div>
			</div>
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="locked" onClick="attempt(id)" class="forLowerOnly">
				<div class="icon_text" id="lck" data-i18n="activityPage.locked"></div>
				<div id="pointed" class="lckPoited">
				</div>
			</div>
			<div id="new">
				<div class="icon_text" data-i18n="activityPage.new"></div>
				<div id="pointed">
				</div>
			</div>
		</div>
		<div id="hideShowBar" class="forHigherOnly hidden" onClick="showHideBar()">-</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard">
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Rewards Central</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="sparkieBar" class="forHighestOnly">
					<div id="leftSparkieBar">
						<div id="textMain">YOUR REWARDS HISTORY</div>
					</div>
					<div id="rightSparkieBar">
			<div id="textMain1">&nbsp;</div>
			<?php if($timelineSparkie1==""){ ?>
				<div id="sparkieCounter" style="float:left">0</div>
				<div id="spakieImage" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone1"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==400){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div class="sparkieImage1" style="margin-left:<?=(400-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone2"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==1000){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div class="sparkieImage1" style="margin-left:<?=(1000-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone3']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone3"){?>
				<div id="sparkieCounter" style="float:left"><?=1500*(intval($sparkiesEarned/1500))?></div>
				<div id="spakieImage" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=1500*(intval($sparkiesEarned/1500) + 1)?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } ?>	
			</div>
				</div>
				<div class="clear"></div>
				
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="../../dashboard.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			DASHBOARD
			</div></a>
			<a href="../../examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="../../home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="../../explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="../../activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
			<div id="drawer5" style="font-size:1.4em;">
			<div id="drawer5Icon"></div>
			ACTIVITIES
			</div></a>
			<!--<a href="../../viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<a href="rewardsDashboard.php">
		<div id="rewards" class="hidden forHigherOnly">
            <span id="classText">Rewards</span>
            <div id="rM" class="pointed1">
            </div>
        </div></a>
        <div id="history" class="hidden forHigherOnly">
            <span id="classText">Sparkie Bank</span>
            <div id="hM" class="pointed2">
            </div>
        </div>
		<a href="themesDashboard.php">
		<div id="themes" class="hidden forHigherOnly">
            <span id="classText">Themes</span>
            <div id="tM" class="pointed3">
            </div>
        </div></a>
		<a href="classLeaderBoard.php">
		<div id="leaderBoard" class="hidden forHigherOnly">
			<div id="leaderBoardImage"></div>
            <span id="classText">Class LeaderBoard</span>
            <div id="lM" class="pointed4">
            </div>
        </div></a>
		<div id="sparkieBarMid" class="forHigherOnly hidden">
			<div id="leftSparkieBar">
				<div id="textMain">YOUR REWARDS HISTORY</div>
			</div>
			<!--<div id="rightSparkieBar">
				<div id="textMain1">&nbsp;</div>
				<?php if($timelineSparkie1==""){
					?>
					<div id="sparkieCounter" style="position: relative;float:right"></div>
					<div id="sparkieIndicator" style="margin-left:<?=$sparkiesEarned*220/$sparkieLogic['mileStone1']['sparkieNeeded']?>px"><div class="sparkieCount"><?=$sparkiesEarned?></div></div>
				<?php } else if($timelineSparkie1=="milestone1"){
					?>
					<div id="sparkieCounter"></div>
					<div id="superSparkieContainer" style="position: relative;float:right">2</div>
					<div id="themeContainer" style="margin-left:<?=400*110/($sparkieLogic['mileStone1']['sparkieNeeded'])?>px"><div id="themeText">Theme Unlock!</div></div>
					<div id="sparkieIndicator" style="margin-left:<?=$sparkiesEarned*110/$sparkieLogic['mileStone1']['sparkieNeeded']?>px"><div class="sparkieCount"><?=$sparkiesEarned?></div></div>
				<?php }else if($timelineSparkie1=="milestone2"){ ?>
					<div id="superSparkieContainer" style="margin-left:0px">2</div>
					<div id="megaSparkieContainer" style="position: relative;float:right">3</div>
					<div id="themeContainer" style="margin-left:<?=1000*180/($sparkieLogic['mileStone2']['sparkieNeeded'])?>px"><div id="themeText">Theme Unlock!</div></div>
					<div id="superSparkieIndicator" style="margin-left:<?=$sparkiesEarned*180/($sparkieLogic['mileStone2']['sparkieNeeded'])?>px"><div class="sparkieCount"><?=$sparkiesEarned?></div></div>
				<?php }else if($timelineSparkie1=="milestone3" && $sparkiesEarned<5000){ ?>
					<div id="megaSparkieContainer" style="margin-left:0px">3</div>
					<div id="superSparkieIndicator" style="margin-left:<?=$sparkiesEarned*120/($sparkieLogic['mileStone3']['sparkieNeeded'])?>px"><div class="sparkieCount"><?=$sparkiesEarned?></div></div>
				<?php }else if($timelineSparkie1=="milestone3" && $sparkiesEarned>=5000){ ?>
					<div id="megaSparkieContainer" style="margin-left:0px">3</div>
					<div id="superSparkieIndicator" style="margin-left:350px"><div class="sparkieCount"><?=$sparkiesEarned?></div></div>
				<?php } ?>
				<div id="gradientSparkie"></div>
			</div>-->
			<div id="rightSparkieBar">
			<div id="textMain1">&nbsp;</div>
			<?php if($timelineSparkie1==""){ ?>
				<div id="sparkieCounter" style="float:left">0</div>
				<div id="spakieImage" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone1"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==400){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div class="sparkieImage1" style="margin-left:<?=(400-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone2"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==1000){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div class="sparkieImage1" style="margin-left:<?=(1000-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone3']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone3"){?>
				<div id="sparkieCounter" style="float:left"><?=1500*(intval($sparkiesEarned/1500))?></div>
				<div id="spakieImage" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=1500*(intval($sparkiesEarned/1500) + 1)?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } ?>	
			</div>
		</div>
        <div id="lock" onClick="attempt(id);">
            <span id="classText" data-i18n="activityPage.locked"></span>
        </div>
<form name="frmActivitySelection" id="frmActivitySelection" method="POST" action="enrichmentModule.php">
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<a href="classLeaderBoard.php">
				<div id="report">
					<span id="reportText">Your Class LeaderBoard</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<a href="rewardsDashboard.php">
				<div id="rewards" class="forHighestOnly">
					<div id="naM" class="pointed1">
					</div></br>
					Rewards
				</div></a>
				
				<div id="history" onClick="redirect()" class="forHighestOnly">
					<div id="aM" class="pointed2">
					</div></br>
					Sparkie Bank
				</div>
				<a href="themesDashboard.php">
				<div id="themes" onClick="redirect()" class="forHighestOnly">
					<div id="aM" class="pointed3">
					</div></br>
					Themes
				</div></a>
			</div>
			</div>
	<div id="activitiesContainer">
		<div id="performanceDiv">
			<div id="yearText">
			<font color="red">Total sparkies : </font><?=$totalSparkies?>
			</div><br/>
			<div id="yearText">
				<?php if($theme==2){
				?>
					Select year(s) to see your performance
				<?php
				}else{
				?>
					Select year(s) to<br/> view performance
				<?php } ?>
			</div>
			<div id="yearDiv"><br/>
			<?php 
				$i=0;
				foreach ($sparkies as $key => $value) {
				//for($i=1;$i<=sizeOf($classHistory);$i++){
				//if($classHistory[$i]!=-1){
					$k=explode("~",$key);
			?>
				<input id="check<?=$i+1?>" type="checkbox" name="check" value="<?php echo $key; ?>"><label for="check<?=$i+1?>" id="<?php echo str_replace("~", "_", $key); ?>">Grade <?=$k[0]?> - Year <?=$k[1]?></label><br/>
			<?php 
				$i++;
				} 
				//} 
			?>
			</div>
			<div id="rewardsText1">
			<?php if($theme==2){
			?>
				You are viewing your rewards for
			<?php
			}else{
			?>
				Your rewards for
			<?php } ?>
			</div>
			<div id="yearsText">
			</div><br/>
			<div id="labelsDiv">
				<div class="circleMarker"></div>
				<div class="markerText1">Number of badges</div><br/>
				<div class="bar" id="bar1"></div>
				<div class="markerText" id="badgeText1"></div>
				<div class="bar" id="bar2"></div>
				<div class="markerText" id="badgeText2"></div>
				<div class="bar" id="bar3"></div>
				<div class="markerText" id="badgeText3"></div>
			</div>
		</div>
		<div class="circleContainer"><canvas id="historyPie" width="590px" height="590px" style="margin-left:-50px;margin-top:-50px;" /></div>
	</div>
</form>		
	</div>
  <?php
include("/mindspark/userInterface/classes/clsRewardSystem.php");
$userID = $_SESSION['userID'];
$sparkieInformation = new Sparkies($userID);
$rewardTheme = $sparkieInformation->rewardTheme;
?>
<?php if($rewardTheme!="default") { ?>
<?php if($theme==2) { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/midClass/<?php echo $rewardTheme; ?>.css" />
<?php } else if($theme==3) { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css" />
<?php } }?>  
<?php include("../../footer.php");?>