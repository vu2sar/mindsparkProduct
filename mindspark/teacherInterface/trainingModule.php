<?php include("header.php");
	include("../userInterface/constants.php");
	$baseurl = IMAGES_FOLDER."/newUserInterface/";
	$pdfurl = CLOUDFRONTURL;
	 ?>
<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/help.css?ver=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
        var interface = 1;
	$('document').ready(function(){
		$('.linkCount').click(function(){
			setTimeout(function(){tryingToUnloadPage=false},500);
		});
		
		$('.nav li:first a').addClass('active');
	    $('.tab-content:not(:first)').hide();
	    $('.nav li a').click(function (event) {
	        event.preventDefault();
	        var content = $(this).attr('href');
	        $(".trainingOptions").removeClass('active');
	        $(this).addClass('active');	        
	        $(content).show();
	        $(content).siblings('.tab-content').hide();	   		
		});
		var image = new Image();
		image.onload = function(){
			$("#errorMessage").hide();
		};
		image.onerror = function(){
			$("#errorMessage").show();
		};
		image.src = "http://youtube.com/favicon.ico";
	});
	function playVideo(videoType,url)
	{
		$("iframe").attr('src','');
		$("#"+videoType+" iframe").attr("src",url);
	    insertCounter(videoType);
	}
	function insertCounter(pageUrl)
	{
		$.ajax({
        url: "ajaxRequest.php",
        type: "post",
        data: {'mode': 'trackInterface','pageUrl':pageUrl,'type':'teacherInterface','userID':<?=$_SESSION['userID']?>,'sessionID':<?=$_SESSION['sessionID']?> },
        success: function (response) {                       

        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }


    });
		
	}
	function blinker() {
		$('.blinking').fadeOut(500);
		$('.blinking').fadeIn(500);
	}
	function load(){
		/*var fixedSideBarHeight = window.innerHeight;*/
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		/*$("#fixedSideBar").css("height",fixedSideBarHeight+"px");*/
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#features").css("font-size","1.em");
		$("#features").css("margin-left","40px");
		$(".arrow-right-blue").css("margin-left","10px");
		$(".rectangle-right-blue").css("display","block");
		$(".arrow-right-blue").css("margin-top","3px");
		$(".rectangle-right-blue").css("margin-top","3px");

	}
		function openHelp1(){
			$(".tab-content iframe").each(function()
			{				
				var src = $(this).attr('src');				
				$(this).attr('src','');
				$(this).attr("src",src);
			});			
			var k = window.innerWidth;
			var helpSource= "<?=$baseurl?>theme5/help.html";
			if(k>1024)
			{
				$("#iframeHelp").attr("height","440px");
				$("#iframeHelp").attr("width","960px");
				$("#iframeHelp").attr("src",helpSource);
				$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':570, 'width':1024});
			}
			else
			{
				$("#iframeHelp").attr("height","390px");
				$("#iframeHelp").attr("width","700px");
				$("#iframeHelp").attr("src",helpSource);
				$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
			}
			
		}
</script>
<script type="text/javascript" src="../userInterface/libs/linkCount.js"></script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
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
	
		<table id="childDetails" align="top">
			<td width="25%" class="pointer"><a href="trainingModule.php"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition" >Training Module</div></div></div></a></td>
			<td width="25%" class="pointer"><div id="setHeight"><div class="smallCircle" onclick="openHelp1()"><div class="pointer setPosition" onclick="openHelp1()">Interface Preview</div></div></td>
			<td width="25%" class="pointer"><a href="faq.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">FAQ</div></div></div></a></td>
	        
	        <!--<td width="18%" class="pointer"><a href="teacherVideos.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">Teacher Videos</div></div></div></a></td>-->
			<td width="25%" class="pointer"><a href="teacherManual.php"><div id="setHeight"><div class="smallCircle"><div class=" pointer setPosition">Mindspark Manual</div></div></div></a></td>
		</table>
		<div id="innerContainer" class="trainingModule">
		<div class="leftPane">
			<ul class="nav" role="tablist">
	            <li role="presentation" ><a href="#introductionVideo" aria-controls="introductionVideo" role="tab" data-toggle="tab" class="trainingOptions"  onclick="playVideo('introductionVideo','https://www.youtube.com/embed/wr5dUwPRA04?rel=0')">Introduction to Mindspark</a></li>
	            <li role="presentation" ><a href="#useMindsparkVideo" aria-controls="useMindsparkVideo" role="tab" data-toggle="tab" class="trainingOptions" onclick="playVideo('useMindsparkVideo','https://www.youtube.com/embed/KBWX8ogQvjg?rel=0')" >How to use Mindspark</a></li>
	            <li role="presentation" ><a href="#userWorksheetVideo" aria-controls="userWorksheetVideo" role="tab" data-toggle="tab" class="trainingOptions" onclick="playVideo('userWorksheetVideo','https://www.youtube.com/embed/iy1Hr8G6xTc?rel=0')" >How to create Worksheets?</a></li>
	            <li role="presentation" ><a href="#worksheetFeatures" aria-controls="worksheetFeatures" role="tab" data-toggle="tab" class="trainingOptions" onclick="playVideo('worksheetFeatures','https://www.youtube.com/embed/BySNQNM3gdM?rel=0')" >Worksheets - Report and other features</a></li>
	            <li role="presentation" ><a href="#otherFeaturesVideo" aria-controls="otherFeaturesVideo" role="tab" data-toggle="tab" class="trainingOptions" onclick="playVideo('otherFeaturesVideo','https://www.youtube.com/embed/x--fR_SV8wE?rel=0')">Other Features</a></li>
	           <!--  <li role="presentation" ><a href="#testimonials" aria-controls="testimonials" role="tab" data-toggle="tab" class="trainingOptions" onclick="playVideo('testimonials','https://www.youtube.com/embed/Xh4j4yvoVgw?rel=0')">Testimonials</a></li> -->
	        </ul>
	    </div>	       
    	<div class="rightPane">
            <div role="tabpanel" class="tab-content" id="introductionVideo"> 
                 	           
                <iframe width="560" height="315" src="https://www.youtube.com/embed/wr5dUwPRA04?rel=0" frameborder="0"  type="text/html" allowscriptaccess="always" allowfullscreen="true" >
				</iframe>
               
            </div>
            <div role="tabpanel" class="tab-content" id="useMindsparkVideo">                   	  	  
            	<iframe width="560" height="315" src="" frameborder="0" type="text/html" allowscriptaccess="always" allowfullscreen="true">
					</iframe>
            </div>
             <div role="tabpanel" class="tab-content" id="userWorksheetVideo">                   	  	  
            	<iframe width="560" height="315" src="" frameborder="0" type="text/html" allowscriptaccess="always" allowfullscreen="true">
					</iframe>
            </div>
             <div role="tabpanel" class="tab-content" id="worksheetFeatures">                   	  	  
            	<iframe width="560" height="315" src="" frameborder="0" type="text/html" allowscriptaccess="always" allowfullscreen="true">
					</iframe>
            </div>
            <div role="tabpanel" class="tab-content" id="otherFeaturesVideo">                   	  	
              <iframe width="560" height="315" src="" frameborder="0" type="text/html" allowscriptaccess="always" allowfullscreen="true">
					</iframe>
            </div>
           <!--  <div role="tabpanel" class="tab-content" id="testimonials"> 
                <iframe width="560" height="315" src="" frameborder="0" id="testimonialsVideo" type="text/html" allowfullscreen>
					</iframe>
            </div> -->
            <div style="display: none;" id="errorMessage" class="errorMessage">
        		Looks like Youtube is blocked on this system, please unblock Youtube to watch the videos.
        	</div>
        </div>

	</div>
		
		<div id="innerContent">
		
		</div>
		
	</div>

<?php include("footer.php") ?>