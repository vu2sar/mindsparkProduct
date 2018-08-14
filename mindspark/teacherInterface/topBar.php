<?php
	include("classes/testTeacherIDs.php");
	include("../userInterface/constants.php");
	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	$todaysDate = date("d");

	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0)	{
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}
	$baseurl = IMAGES_FOLDER."/newUserInterface/";

?>
<div id="redirectMsg" style="display: none"> 
	<p> </p>
</div>
<script src="libs/jquery-ui-1.11.2.js"></script>
<link rel="stylesheet" href="/mindspark/userInterface/libs/css/jquery-ui.css" media="all" />

<script>

$(document).ready(function(e) {
	<?php if($_SESSION["logincount"] == 0){ ?>
				$( "#noticenos" ).hide();
			<?php } ?>

	 });

function openHelp()
{
	setTryingToUnload();
	window.location = "help.php";
}

</script>
	<a href="home.php?live=2"><div class="logo">
	</div></a>

	<div id="aqad" class="hidden" onclick="showAQAD()" <?php if(SERVER_TYPE=="LOCAL") { ?> style="visibility:hidden"	<?php } ?>>
		<div class=""><img src="images/aqad-logo-2.png" style="width: 50px; margin-top: 12px;"></div>
		<div class="logoutText">AQAD</div>
	</div>
	
	<span id="kudos-top-div"><a href="kudosHomeTeacherInterface.php"><img alt="" src="images/thumpUp.png"></a></span>
	<div id="infoBarMiddle">
		<div id="interfaceDetails"><?php if (strcasecmp($user->category,"TEACHER")==0)	{	?>
                    <div align="center" class="label_title_top">Teacher interface
                    </div>
                <?php } elseif (strcasecmp($user->category,"School Admin")==0)	{	$userCategory='Admin';?>
                    <div align="center" class="label_title">&nbsp;&nbsp;Administrator interface</div>
                <?php
                  }
                ?>
		</div>
		<div id="schoolDetails"><?php if (strcasecmp($user->category,"TEACHER")==0)	{	?>
                    <div align="center" class="label_title_top">
                    <?php if(!in_array(strtolower($user->username),$testIDArray)) { ?>
                    &nbsp;School: <?=$schoolName?>
                    <?php } ?>
                    </div>
                <?php } elseif (strcasecmp($user->category,"School Admin")==0)	{	$userCategory='Admin';?>
                    <div align="center" class="label_title">&nbsp;&nbsp;<?=$schoolName?></div>
                <?php
                  }
                ?>
		</div>
		
	</div>

	<?php
		if(isset($_SESSION["logincount"]))
		{
			$logincount = $_SESSION["logincount"];
		}
		if($logincount == 0)
		{
			$logincount = '';
			$image ="Notification-Icon-grey.png";
		}else
			$image ="Notification-Icon.png";
	?>
	
	<?php if (strcasecmp($user->category,"School Admin")!=0) { ?>
	<div id="alertteacherCount">
	<a href='alertTeacher.php' style='text-decoration:none;'>
	<div id="noticenos" class="noticenos"><b><? echo $logincount; ?></b></div></a>
	<a href='alertTeacher.php'>
	<div id="noticeicons" class="noticeicons"><img id="imagetag" src="assets/<? echo $image;?>" style='border:0;'></div></a>
	</div>
	<?php } ?>
	<div id="studentInfoLowerClass">
			
    	<div id="nameIcon"></div>
    	<div id="infoBarLeft">
        	<div id="nameDiv">
                <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><div id="nameC">Welcome <?=$user->childName?>&nbsp;&#9660;</div></a>
                                <ul>
                                	<li><a href='../login/changePassword.php?proceed=1&fromTI=1'><span>Change Password</span></a></li>
									<?php
									   if(strcasecmp($user->category,"School Admin")==0 )
									   		echo "<li><a href='settings.php'><span>My Settings</span></a></li>";
									?>
                                	<li><a href='whatsNew.php'><span>What's New</span></a></li>
                                	<li><a href='javascript:void(0)' onClick="openHelp()"><span>Help</span></a></li>
                                	<li><a href='logout.php'><span>Logout</span></a></li>
                                    <!-- <li><a href='Comments.php'><span>Comments</span></a></li>-->
                                </ul>
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </div>