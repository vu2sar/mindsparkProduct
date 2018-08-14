<?php
@include("check1.php");
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include("functions/prePostTest_functions.php");
include_once("classes/clsTopicProgress.php");
if( !isset($_SESSION['userID'])) {
	header( "Location: error.php");
}
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];

$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$category 	   = $objUser->category;
$subcategory   = $objUser->subcategory;

//$baseurl = "assets/examTips/";
$baseurl = "https://mindspark-ei.s3.amazonaws.com/content_images/newUserInterface/examTips/";
$sparkieImage = $_SESSION['sparkieImage'];
?>
<?php include("header.php"); ?>
<title>EXAM TIPS</title>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/examTips/higherClass.css" />
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type='text/javascript' src='libs/combined.js'></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
var click=0;
function load(){
	var a= window.innerHeight - (170);
	$('#topicInfoContainer').css({"height":a+"px"});
	$('#menuBar').css({"height":a+"px"});
	$('#main_bar').css({"height":a+"px"});
	$('#sideBar').css({"height":a+"px"});
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
		$("#main_bar").animate({'width':'22px'},600);
		$("#plus").animate({'margin-left':'4px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
    <div id="top_bar">
        <div class="logo"> </div>
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC">
                                <?=$objUser->childName?>
                                &nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.myDetails"></span></a></li>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.changePassword"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass">
                    <?=$childClass.$childSection?>
                    </span></div>
            </div>
        </div>
        <div id="studentInfoLowerClass" class="forHighestOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $Name ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
                                 <!--   <li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
                                    <li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass">
                    <?=$childClass.$childSection?>
                    </span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
            <div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff();" class="hidden">
            <div class="logout"></div>
            <div class="logoutText" data-i18n="common.logout"></div>
        </div>
        <div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
    <div id="container">
        <div id="info_bar" class="forLowerOnly hidden">
            <div id="blankWhiteSpace"></div>
            <div id="home">
                <div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly">&ndash;&nbsp;<span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                <div class="clear"></div>
            </div>
        </div>
        <div id="info_bar" class="forHigherOnly">
            <div id="topic">
                <div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
                <div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase"><a href="examCorner.php">EXAM CORNER</a></span></div>
                </div>
                <div class="arrow-right"></div>
                <div id="competitiveExamText" class="textUppercase">EXAM TIPS</div>
                <div class="clear"></div>
            </div>
            <div class="class hidden"> <strong><span data-i18n="common.class">Class</span> </strong>
                <?=$childClass.$childSection?>
            </div>
            <div class="Name hidden"> <strong>
                <?=$Name?>
                </strong> </div>
            <div class="clear"></div>
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0) { ?>
            <div id="viewAllTopics">
                <input type="checkbox" id="chkAllTopics" name="chkAllTopics" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>
                Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
                <?php } ?>
            </div>
            <a href="sessionWiseReport.php" class="removeDecoration hidden">
            <div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div>
            </a> </div>
        <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div>
        <div id="main_bar" class="forHighestOnly">
            <div id="drawer1"> <a href="activity.php" style="text-decoration:none;color:inherit">
                <div id="drawer1Icon"></div>
                ACTIVITIES </div>
            </a> <a href="dashboard.php">
            <div id="drawer2">
                <div id="drawer2Icon"></div>
                DASHBOARD </div>
            </a> <a href="home.php">
            <div id="drawer3">
                <div id="drawer3Icon"></div>
                HOME </div>
            </a>
            <a href="explore.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
            <div id="plus" onClick="openMainBar();">
                <div id="vertical"></div>
                <div id="horizontal"></div>
            </div>
            <a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
			<div id="drawer5"><div id="drawer5Icon" style='<?php if($_SESSION['rewardSystem']!=1) { echo 'position: absolute;background: url("assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;";';} ?>' class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>
            <!--<a href="viewComments.php?from=links&mode=1">
            <div id="drawer6">
                <div id="drawer6Icon"></div>
                NOTIFICATIONS </div>
            </a>--> </div>
        <div id="tableContainerMain">
            <div id="menuBar" class="forHighestOnly">
                <div id="sideBar"> </div>
            </div>
            <div id="topicInfoContainer">
				<div class="head"></div>
                <table width="90%" border="0" cellspacing="3" cellpadding="3">
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img1.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Schedule your sleep</div></div></td>
                    </tr>
                    <tr>
						<td valign="top"><div class="examTips"><div class="examTipsText"> Sleep is an important component of our daily routine which keeps our mind and body healthy. You perfrom much better in exams if your mental state is good, and sleep is essential for this.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img2.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Exercise</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Exercise helps in enhancing and maintaining physical fitness and mental health. As some great philosophers have said that a sound mind lives in a sound body. So go out and play some games, a brisk walk can also relieve your stress.
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img3.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Prioritize</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Remember that you have limited time and to effectively manage this time, divide your tasks and plan where you want to spend more time. Choose what chapters you want to study and know them well.
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img4.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Drink water often</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Drink plenty of water while preparing for your exams, especially when you feel dull and lazy. Coffee may help you to stay awake, but it may increase the anxiety - use it in optimal amounts. 
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img5.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Don't get too comfortable in your chair</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Choose a comfortable chair that supports your back, but not too comfortable. While studying, the body should be relaxed, so that all your energy goes to your brain.
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img6.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Clear your desk of everything you don't need</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Have everything you need on the desk. Put away what you do not need for the study session.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img7.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Don't plan to study non-stop</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Studying non-stop is not advisable, you will become stressed and may not perform well in exams. While making your study plan, include short study breaks to refresh yourself. You will feel better and you will be able to concentrate more.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img8.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Take breaks every hour</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">To maintain high concentration level, take breaks. If the work is not going too well and you are facing difficulties in concentrating, you may need a long break and go back to it later.
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img9.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Stretch during your breaks</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">When you sit for long study sessions, gravity draws the blood to the lower part of your body. Whenever you take a break, take a few deep breaths, it will increase the blood supply to your brain. Try walking around and doing some light stretching for a few minutes. It helps to release stress in your body, and maintains the blood circulation.
</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img10.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Study at the same time and same place</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Fix a place devoted to study only. This reduces distraction and help you to associate the time and place with studying and concentrating. Soon you will realise that you are getting into a habit of studying as soon as you sit down. 
</div></div></td>
                    </tr>
					<tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img11.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Eat plenty of healthy food</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">It is important to keep your body supplied with nutrients during the period before the exam. Do not skip meals to devote time to studying- this will only hurt your grades in the exam.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img12.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Spend as much time as possible with your teachers</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Teachers are often ready to help students up to the last minute before exams. Make the best use of this time- ask all your doubts and solve past papers with your teacher to understand how to respond to questions.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img13.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Show up 15 minutes before time</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">You will not be allowed to enter any exam after it has started. Leave your home early and make sure you leave time for traffic and other delays. It is better to arrive early and wait than to arrive late and miss the exam altogether.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img14.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Set your alarm, and have a backup alarm</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">After all the hard work that you have put into studying for the exam, you don't want to miss it simply because you didn’t wake up. Make sure you have at least two alarms set before you go to sleep the previous night.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img15.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Visit the washroom before the exam</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Some exams may require you to stay in the exam hall for three or four hours at a stretch, without visiting the washroom. Be prepared for this so that you can concentrate on your paper during the exam.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img16.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Solve past papers</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">For board exams and standardized tests, the style of questions stays the same over the years. You should solve past papers to familiarize yourself with the kind of questions that will be asked and to reinforce revision.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img17.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Prepare a checklist of materials required</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">The night before the exam, write down a list of all the materials that will be required for the exam (pens, pencils, calculators, etc.). Go through this list before you leave home on exam day.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img18.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Have backup copies of your admission ticket</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Your admission ticket is the only way you will be admitted into the exam room. Have multiple copies in different places, as you may not be able to print it off at the exam center. You will not be permitted to enter the exam hall without an admission ticket.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img19.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Bring a wristwatch</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Even though your exam room may have a wall clock, having a wristwatch will let you save time by not having to look up  every few minutes. Also, many watches come with the stopwatch feature, which can be useful for timing yourself on different sections in the exam</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img20.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Dress comfortably</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">You won't be able to concentrate on your paper if you are distracted by how hot or how cold you feel. Dress according to the weather and wear whatever you are most comfortable with. If a school uniform is mandatory, ask your teacher if an exception can be made for you on exam day.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img21.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Check that your calculator works properly</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">In exams with calculators, it is your most important tool. Make sure that you are familiar with all the functions of your calculator. Keep spare batteries with you in the exam hall.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img22.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Study with friends</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Group study can be highly beneficial if everyone is helping each other out. Identify your weak areas and look for a friend who is strong in those areas. You will also be bored less often if you are studying in a group.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img23.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Do the easier questions first</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">If your exam is not structured according to difficulty, you may find it useful to do the easier questions first. You don't want to spend time on the difficult ones and miss the easy ones where you could have scored marks.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img24.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Stay focused on your paper</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Don't look around unnecesarily as the supervisor may think that you are cheating, and this can seriously hurt your grades. Focus on your own paper and make the best use of your time.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img25.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Don't panic</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">This is easier said than done. If you find yourself panicking during the exam, stop, take a few breaths, and continue. If a question is becoming increasingly stressful, leave it and come back to it later.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img26.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Read the whole question</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Don't be in a hurry to start answering the question. Read each question at least twice to ensure that you don't miss out on key words. You may end up answering another question altogether.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img27.jpg' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">The exam is not a race</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Don't be in a hurry to finish the paper if someone else has finished. Use the entire amount of time allotted. If you finish early, check your paper for any careless errors that you may have made.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img28.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Write legibly</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Write in clear and neat handwriting. If the examiner is not able to read what you have written, they will mark your answer as incorrect, even if you had written the correct answer. Improve your handwriting by practicing regularly. </div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img29.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Double check all your details</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Ensure that you have written your name, registration number, etc. correctly. If there is a mistake here, you may be awarded incorrect or even no marks. Check you details once you have finished writing the exam. Ensure that what you have written matches your admission ticket exactly.</div></div></td>
                    </tr>
                    
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img30.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">After the exams, relax</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">You have gone through a lot of stress and your body and mind need to rest. Do something that you enjoy doing- go for a movie or read a book. Let yourself unwind so that you are prepared for the next challenge.</div></div></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td rowspan="2"><img src='<?=$baseurl?>img31.png' width="120"></td>
                        <td><div class="examTipsHead"><div class="examTipsHeadText">Exams are not the end of the world</div></div></td>
                    </tr>
                    <tr>
                        <td valign="top"><div class="examTips"><div class="examTipsText">Do not fear exams. Even if you don't get the grades you dreamed of, remember that no exam can decide whether you are successful in life or not. Your exam result is just a number, so don't let it dishearten you.</div></div></td>
                    </tr>
                    
                </table>
                <!--<a href="competitiveExam.php">
                <div class="topicProgressInnerDiv">
                    <div class="topicName">COMPETITIVE EXAMS</div>
                </div>
                </a>
                <div class="topicProgressInnerDiv">
                    <div class="topicName">TOPIC SUMMARIES (Coming soon....)</div>
                </div>
                <div class="topicProgressInnerDiv">
                    <div class="topicName">IMPROVE YOUR CONCEPTS (Coming soon....)</div>
                </div>
                <div class="topicProgressInnerDiv">
                    <div class="topicName">EXAM TIPS (Coming soon....)</div>
                </div>
                <div id="textAtLast">"We cannot become what we need to be by remaining what we are"</div>-->
            </div>
        </div>
    </div>
    <input type="hidden" name='mode' id="mode">
    <input type="hidden" name="postTestFlag" id="postTestFlag" value="<?=$_SESSION['prePostTestFlag']?>">
    <input type="hidden" name="postTestTopic" id="postTestTopic" value="<?=$_SESSION['prePostTestTopic']?>">
    <input type="hidden" name='ttCode' id="ttCode">
    <input type="hidden" name='topicDesc' id="topicDesc">
    <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
    <input type="hidden" name="cls" id="cls" value="<?=$user->childClass?>">
</form>
<?php include("footer.php") ?>