<?php include("header.php");
	include("../userInterface/constants.php");
	$baseurl = IMAGES_FOLDER."/newUserInterface/";

	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	
	$i=0;$arrayFAQ = array();
	$matches = array();
	$query = "select `id`, `question`, `answer`, `pdf`, `video`, `lastModified`, `category`, `status` from adepts_faqQuestions WHERE `status` = 1 order by category, id";
	$result = mysql_query($query) or die(mysql_error());
	while($line=mysql_fetch_array($result)){
		$cat=$line['category'];
		if (!in_array($cat, array_keys($arrayFAQ))) 	$arrayFAQ[$cat]=array();
		$r=array();
		$r['id'] = $line['id'];
		$r['question']		=	$line['question'];
		$r['answer']		=	str_replace(']','>', str_replace('[','<img src ='.WHATSNEW.'teacherFAQ/', $line['answer']));
		$r['pdf']	=	$line['pdf'];
		$r['video']	=	$line['video'];
		array_push($arrayFAQ[$cat], $r);
		$i++;
	}
 ?>
<!--CREATE TABLE adepts_faqQuestions
(
id int NOT NULL,
question text,
answer text,
video varchar(255),
pdf varchar(255),
lastModified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (id)
);		-->
<title>Other Features</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/faq.css?ver=2" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
 function displayAnswer(value){
 	var value=value;
	if($("#q"+value).css("display")=="block"){
		 $('input[name=question]').attr('checked', false);
		$(".answer").css("display","none");
	}else{
 		$(".answer").css("display","none");
		$("#q"+value).show();
	}	
 }
 </script>
<script>
	var langType = '<?=$language;?>';
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
		function openVideo(){
			$("#mp4SRC").attr("src",$(".videoLink").attr("id"));
			$("#videoFile1000").attr("controls",true);
			$.fn.colorbox({'href':'#videoBox','inline':true,'open':true,'escKey':true, 'height':'auto', 'width':'auto'});
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
		        <td width="25%" class="pointer"><div id="setHeight"><div class="smallCircle" onClick="openHelp1()"><div class="pointer setPosition" onClick="openHelp1()">Interface Preview</div></div></td>
		        <td width="25%" class="pointer"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition" style="width:125px;">FAQ</div></div></div></td>
		        <!--<td width="18%" class="pointer"><a href="teacherVideos.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">Teacher Videos</div></div></div></a></td>-->
				<td width="25%" class="pointer"><a href="teacherManual.php"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">Mindspark Manual</div></div></div></a></td>
			</table>
		<div id="innerContainer"><br/><br/>
				<?php
					$categories=array_keys($arrayFAQ);$cnum=0;
					for ($i=0;$i<count($categories);$i++)
					{ 
						$categ=$categories[$i];$category=$arrayFAQ[$categ];
						echo '<div class="category"><div class="categoryHeader">'.substr($categ, 2).'</div>';
						for ($j=0;$j<count($category);$j++)
						{ 
							$qs=$category[$j];$cnum++;
							if ($qs['question']!="")
							{
								echo '<label style="width:100%;"><input type="radio" name="question" value="'.$cnum.'" style="float:left;" onclick="displayAnswer(value);"/><div class="questionClass">'.$qs['question'].'</div><div style="clear:both"></div></label>';
								if($qs['pdf']!=""){
									echo '<a target="_blank" href="http://docs.google.com/viewer?url='.WHATSNEW.$qs['pdf'].'" onclick="setTimeout(function(){tryingToUnloadPage=false},500);"><div class="pdf"></div> </a>';
								}
								if($qs['video']!=""){
									echo '<a onclick="openVideo()" class="videoLink" id="'.WHATSNEW.$qs['video'].'"><div class="video"></div></a>';	
								}
								echo '<div class="spacer"></div>
									<div id="q'.$cnum.'" class="answer">
									<p>'.$qs['answer'].'</p>
									</div>';
							}
						}
					}
				?>
		</div>
		
		<div id="innerContent">
		
		</div>
		
	</div>
	<div style="display:none">
    <div id="videoBox">
        <div id="videoDiv">
			<video id="videoFile1000" controls width="480" height="360"><source src="" id="mp4SRC" type="video/mp4" /></video>
        </div>
    </div>
</div>

<?php include("footer.php") ?>