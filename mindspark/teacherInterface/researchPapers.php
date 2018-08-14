<?php
    
    
    include("header.php");
    include("../slave_connectivity.php");
    set_time_limit(0);
    include("../userInterface/functions/functions.php");
    error_reporting(E_ERROR);
    
        $userID = $_SESSION['userID'];
    
        if(!isset($_REQUEST['ttCode']) || !isset($_SESSION['userID']))
        {
            echo "You are not authorised to access this page!";
            exit;
        }
    
    $keys = array_keys($_REQUEST);
    foreach($keys as $key)
    {
        ${$key} = $_REQUEST[$key] ;
    }
    
        $query = "SELECT teacherTopicDesc, mappedToTopic, customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
        $result = mysql_query($query);
        $line = mysql_fetch_array($result);
        $teacherTopicDesc = $line[0];
        $topicCode = $line[1];
        $customTopic = $line[2];
        $parentTeacherTopicCode = $line[3];
    
    //echo  "cls == ".$cls." section == ".$section."  ttCode == ".$ttCode."  clusterCode == ".$clusterCode;
    
?>


<title>Research Papers</title>
<meta charset="UTF-8">

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/researchPapers.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
    var langType = '<?=$language;?>';
    function load(){
        var fixedSideBarHeight = window.innerHeight;
        var sideBarHeight = window.innerHeight-95;
        var containerHeight = window.innerHeight-115;
        $("#fixedSideBar").css("height",fixedSideBarHeight+"px");
        $("#sideBar").css("height",sideBarHeight+"px");
        $("#questionContainer").css("min-height",(containerHeight-160)+"px");
        $("#classes").css("font-size","1.4em");
        $("#classes").css("margin-left","40px");
        $(".arrow-right").css("margin-left","10px");
        $(".rectangle-right").css("display","block");
        $(".arrow-right").css("margin-top","3px");
        $(".rectangle-right").css("margin-top","3px");
    }
</script>
<script type="text/javascript">
    $(document).ready(function (e) {
        $(".titleLink,.downloadLink").colorbox({ inline: true, width: "80%", height: "95%",
            onClosed: function () {
                $("#iFrame").attr("src", "");
            },
			onOpen: function(){
				tryingToUnloadPage = false;				
			}
        });
    });
    function setURL(linkText, titleText, moduleno) {
			$.ajax('ajaxRequest.php?mode=researchPaperCounter&moduleID=' + moduleno,
						{
							method: 'get',
							success: function (transport) {
								console.log(transport);
	
							}
						}
						);
			if (linkText != "")
				linkText = "http://docs.google.com/viewer?url=" + encodeURI(linkText) + "&embedded=true";
			$("#iFrame").attr("src", linkText);
			$("#modelTitleText").text(titleText);
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
        <div id="trailContainer">
            <div id="headerBar">
                <div id="pageName">
                    <div class="arrow-black"></div>
                    <div id="pageText">TOPIC RESEARCH</div>
                </div>
            </div>

            <table id="childDetails" align="top">
				<td width="18%" id="sampleQuestions" class="pointer"><a href="sampleQuestions.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition" style="width:125px;">SAMPLE QUESTIONS</div></div></div></a></td>
		        <td width="18%" id="wrongAnswers" class="pointer"><a href="cwa.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>&section=<?=$_REQUEST['section'];?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">COMMON WRONG ANSWERS</div></div></div></a></td>
		        <td width="18%" id="researchStudies" class="pointer"><a href="researchPapers.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle red"><div class="textRed pointer setPosition">SUMMARY OF RESEARCH STUDIES</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="studentInterviews.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">SUMMARY OF STUDENT INTERVIEWS</div></div></div></a></td>
				<td width="22%" id="studentInterviews" class="pointer"><a href="misconceptionVideos.php?ttCode=<?=$ttCode?>&cls=<?=$cls?>&flow=<?=$flow?>"><div id="setHeight"><div class="smallCircle"><div class="pointer setPosition">MISCONCEPTION VIDEOS</div></div></div></a></td>
			</table>

            <table id="pagingTable">
                <td width="35%"><?= $teacherTopicDesc?></td>
            </table>

            <div id="questionContainer">
                <div id="rightBody">
                    <h2 align="center">Summary of Research Studies</h2>
                    <?php
                        if($customTopic==1){
                            $ttCode = $parentTeacherTopicCode;
                        }
                        $sql = "SELECT moduleID, title, description, author, link1, link2 FROM adepts_researchModules WHERE FIND_IN_SET('$ttCode',mappedTTs)";
                        $result = mysql_query($sql);
                        if(mysql_num_rows($result) > 0)
                        {
                        	$idcountforrefferal = 1;
                            while($row = mysql_fetch_assoc($result))
                            {
                                $title = addslashes($row['title']);
                                $description = $row['description'];
                                $author = $row['author'];
                                $link1 = addslashes($row['link1']);
                                $link2 = addslashes($row['link2']);
                                $moduleid = $row['moduleID'];
                        
                                // Prepare author string..
                                $authorSTR = $author;
                                if($author != "")
                                {
                                    $authorSTR = "";
                                    $authors = explode(",",$author);
                        
                                    if(count($authors) > 0)
                                    {
                                        for($i=0;$i<count($authors);$i++)
                                        {
                                            if($i == count($authors)-2)
                                                $authorSTR .= $authors[$i]." and ";
                                            else
                                                $authorSTR .= $authors[$i].", ";
                                        }
                                        $authorSTR = substr($authorSTR,0,-2);
                                    }
                                    else
                                        $authorSTR = $authors[0];
                                }
                        
                    ?>
                    <div class="researchModule">
						<?php if(SERVER_TYPE=='LIVE') { ?>
                        <a href="#modelView" onClick="setURL('<?=rsrPaperFullPath.$link1?>','<?=$title?>','<?=$moduleid?>')" class="titleLink" id="mytopicpopup-<?=$idcountforrefferal;?>">Summary - <?=stripslashes($title)?></a>
						<?php } else { ?>
						<a href="<?=rsrPaperFullPath.$link1?>" class="titleLink" id="mytopicpopup-<?=$idcountforrefferal;?>" target="_blank">Summary - <?=stripslashes($title)?></a>
						<?php } ?>
                        <br />
                        <span class="descText"><?=$description?></span>
                        <br />
                        <?php if($link2 != "") { ?>
						<?php if(SERVER_TYPE=='LIVE') { ?>
                        <a href="#modelView" onClick="setURL('<?=rsrPaperFullPath.$link2?>','<?=$title?>','<?=$moduleid?>')" class="downloadLink">View the actual research paper</a>
						<?php } else { ?>
						<a href="<?=rsrPaperFullPath.$link2?>" class="downloadLink" target="_blank">View the actual research paper</a>
						<?php } ?>
                        <span class="authorText"> written by <?=$authorSTR?></span>
                        <?php } ?>
                    </div>
                    <?php
                    $idcountforrefferal ++;
                            }
                        }
                        else
                        {
                    ?>
                    <h3>No Records Found!</h3>
                    <?php
                        }
                    ?>
                </div>
                <div style="display:none">
                    <div id="modelView">
                        <div id="modelTitleText" style="float:left; width:100%;">
                        </div>
                        <div style="clear:both"></div>
                        <div style="float:left; width:100%; height:85%">
                            <iframe id="iFrame" class="iFrame" height="85%" width="100%" src="" allowtransparency="true"></iframe>
                        </div>
                        <div style="clear:both"></div>
                        <div id="embedText" style="float:left; width:100%;">
            Disclaimer : Please note that Mindspark does not necessarily use research findings from these or other papers. These are shared just for information.
                            <br />
                            <span class="contactText">For any queries or suggestions mail to maulik.shah@ei-india.com</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php 
if(isset($_REQUEST["mytopicpagerefferal"])){
	echo '<script>';
	echo '$(window).load(function(){$("#mytopicpopup-1").trigger("click");});' ;
	echo '</script>';
}
?>
    <?php include("footer.php") ?>
