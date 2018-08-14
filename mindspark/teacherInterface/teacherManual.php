<?php include("header.php");
	include("../userInterface/constants.php");
	$baseurl = IMAGES_FOLDER."/newUserInterface/";
	$pdfurl = CLOUDFRONTURL;
	 ?>

<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
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
	});
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
			<td width="25%" class="pointer"><a href="trainingModule.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" >Training Module</div></div></div></a></td>
			<td width="25%" class="pointer"><div id="setHeight"><div class="smallCircle" onclick="openHelp1()"><div class="pointer setPosition" onclick="openHelp1()">Interface Preview</div></div></td>
			<td width="25%" class="pointer"><a href="faq.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">FAQ</div></div></div></a></td>
	        
	        <!--<td width="18%" class="pointer"><a href="teacherVideos.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">Teacher Videos</div></div></div></a></td>-->
			<td width="25%" class="pointer"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition">Mindspark Manual</div></div></div></td>
		</table>
		<div id="innerContainer">
			<table height="auto" width="80%" cellSpacing="0" cellPadding="0" align="left" bgcolor="#FFFFFF" bgColor="#FFFFFF" border="0" bordercolor="black" style="font-family: Verdana;margin-bottom:80px;">
		    <tr><td>
				<div  style="margin-left:20px;margin-top:25px;">
<b><FONT face=Verdana style="font-size:20px" font color=#088A29>	<b>Mindspark User Manual 2016</b></font><br><br>
<table class="mindsparkManual">
	<tr>
		<td>Chapter 1</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Introduction_Mindspark.pdf" target="_blank">Introduction to Mindspark</a></td>
	</tr>
	<tr>
		<td>Chapter 2</td>
		<td>Student interface in Mindspark</td>
	</tr>
	<tr>
		<td style="float:right;">2.1</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Mindspark_Student.pdf" target="_blank">Getting started with the student interface</a></td>
	</tr>
	<tr>
		<td style="float:right;">2.2</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Getting_Started_Student_Interface.pdf" target="_blank">Mindspark and the student</a></td>
	</tr>
	<tr>
		<td>Chapter 3</td>
		<td>Teacher interface in Mindspark</td>
	</tr>
	<tr>
		<td style="float:right;">3.1</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/QuickGuide_TeacherInterface_new.pdf" target="_blank">Quick access guide to the teacher interface</a></td>
	</tr>
	<tr>
		<td style="float:right;">3.2</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Customisation_new.pdf" target="_blank">Lesson Planning and Customisation</a></td>
	</tr>
	<tr>
		<td style="float:right;">3.3</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Detailed_Teacher_Report_new.pdf" target="_blank">Detailed teacher reports</a></td>
	</tr>
	<tr>
		<td>Chapter 4</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Parent_Reports.pdf" target="_blank">Parent interface and reports</a></td>
	</tr>
	<tr>
		<td>Appendix</td>
	</tr>
	<tr>
		<td style="float:right;">5.1</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Customer_Support.pdf" target="_blank">Customer Support</a></td>
	</tr>
	<tr>
		<td style="float:right;">5.2</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Hardware_Software_Requirement.pdf" target="_blank">Hardware and Software requirement</a></td>
	</tr>
	<tr>
		<td style="float:right;">5.3</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/FAQs.pdf" target="_blank">FAQs</a></td>
	</tr>
	<tr>
		<td style="float:right;">5.4</td>
		<td><a class="linkCount" href="<?=$pdfurl?>helpManual/Testimonials.pdf" target="_blank">Testimonials</a></td>
	</tr>
</table>
		</div>                       
								
			</td>
		</tr>
	</table>
		</div>
		
		<div id="innerContent">
		
		</div>
		
	</div>

<?php include("footer.php") ?>