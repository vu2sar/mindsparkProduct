<?php include("header.php") ?>

<title>Landing Screen</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/landingScreen_old.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	var click=0;
	var set =0;
	
	
	function classSlide(id){
		
	
	  	if($("#flipContent").height()==0)
		{	
			
	 	   $("#flipContent").animate({height:"60%"});
		  
			$("#tab"+id).show();
			$("#triangle"+id).show();
			set = id;
				
		}
		
		else{
				if(set == id)
				{
					$("#flipContent").animate({height:"0%"});
		  
					$("#tab"+id).hide();
					$("#triangle"+id).hide();
		
				}	
		}
	   	
	}
	
	
	
	function load(){
		$(".classTab").hide();
		$(".classTabTriangle").hide();
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
	}
	function openMainBar(){
		if(click==0){
			$("#container").animate({'width':'89.5%','margin-left':'135px'},600);
			$("#sideBar").animate({'width':'2%'},600);
			$("#plus").animate({'margin-left':'4px'},600);
			$(".sideMenu").fadeOut(300);
			$("#home").animate({'margin-top':'-20px','margin-left':'45px'},400);
			$("#home").animate({'margin-left':'45px'},400);
			$("#home").animate({'border-bottom':''},400);
			$("#vertical").css("display","block");
			click=1;
		}
		else if(click==1){
			$("#container").animate({'width':'72.5%','margin-left':'27%'},600);
			$("#sideBar").animate({'width':'20%'},600);
			$("#plus").animate({'margin-left':'227px'},600);
			$(".sideMenu").fadeIn(500);
			$("#vertical").css("display","block");
			$(".sideMenu").css("display","block");
			$("#home").css({'margin-left':'15%'});
			$("#home").animate({'margin-top':'90px'},600);
			$("#vertical").css("display","none");
			$("#home").animate({'border-bottom':'1px solid #4E4D50'},600);
			click=0;
		}
	}
</script>
</head>
<body class="translation" onload="load()" onresize="loaded()">
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
	
	
		<div id="flipContent" style="">
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="clipFrame">
			<div id="innerClipFrame">
			
			</div>
			</div>
				
			</div>
			
			
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="clipFrame">
			<div id="innerClipFrame">
			
			</div>
			</div>
				
			</div>
			
			
			<div id="topicBox">
			<div id="topicBoxDesc">
				<div id="topicBoxDescTopic">
				Addition upto 999
				</div>
				<div id="topicBoxDescInfo1">
				Activated on 24/03/2013
				</div>
				<div id="topicBoxDescInfo2">
				Active since 28 Days
				</div>
			</div>
			
			
			<div id="clipFrame">
			<div id="innerClipFrame">
			
			</div>
			</div>
				
			</div>
		</div>
		
		<div id="tabcls1" class="classTab">
				<div id="circle"> 
					<div id="triangle" style="" > </div>
				</div>
			</div>
			<div class="classTabTriangle" id="trianglecls1" style=""> </div>
		<div id="flip">
			
			<div id="classTriangle" style="" > </div>
			<div class="text" id="cls1" onclick="classSlide(id)"> CLASS  3 </div>
			<div id="line"> </div>
		</div>
	
		<!--<div id="flipContent" style="">
			<div id="topicBox">
				
			</div>
		</div>-->
		
		<div class="classTab" id="tabcls2">
				<div id="circle"> 
					<div id="triangle" style="" > </div>
				</div>
			</div>
			<div class="classTabTriangle" id="trianglecls2" style=""> </div>
		
		<div id="flip">
			
			<div id="classTriangle" style="" > </div>
			<div class="text" id="cls2" onclick="classSlide(id)"> CLASS  5 </div>
			<div id="line"> </div>
		</div>
	</div>

<?php include("footer.php") ?>