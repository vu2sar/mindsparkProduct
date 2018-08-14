<?php
include ("check1.php");
include ("classes/clsUserdms.php");
//error_reporting(E_ALL);
if(isset($_SESSION['userID'])){
    session_unset();
	session_destroy();
	session_start();
}
if(isset($_GET['image1'])){
    $image1 = $_GET['image1'];
}
if(isset($_GET['image2'])){
    $image2 = $_GET['image2'];
}

$username = isset($_REQUEST['username'])?$_REQUEST['username']:"";
$pwd      = isset($_REQUEST['password'])?$_REQUEST['password']:"";
if($pwd=='eimasterpwd2009'&&!isset($_REQUEST['usernameei'])){
//header("Location: masterPasswordUse.php?image1=".$image1."&image2=".$image2);
    echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
    echo '<form name="frmSession" id="frmSession" method="POST" action="' . 'masterPasswordUse.php?image1='.$image1.'&image2='.$image2 . '">';
    echo '<input type="hidden" name="password" id="password" value="eimasterpwd2009"/>';
    echo '<input type="hidden" name="username" id="username" value="'.$username.'"/>';
    echo '</form>';
    echo '<script>document.frmSession.submit();</script>';
    exit();    
}
    
?>

<html>
<head>
<!--<link href="css/define/style.css" rel="stylesheet" type="text/css" />-->
<script>

	function renew(userID)
	{
		document.getElementById('userID').value = userID;
		document.getElementById('frmRenew').action = "https://www.mindspark.in/renew.php";
		document.getElementById('frmRenew').submit();
	}


</script>
</head>
<body>
	<div class="left_cover" id="left_cover">
        <div class="top_bar">
        </div>
        <div class="mid_bar">
        </div>
        <div class="bot_bar" id="b_bar_left">
        </div>
    </div>
    <div class="right_cover" id="right_cover">
                        <div class="top_bar">
                        </div>
                        <div class="mid_bar">
                        </div>
                        <div class="bot_bar" id="b_bar_right">
                        </div>
    </div>
    <div class="container" id="container">
                        <div class="navbar">
                                <a class="logo">
                                </a>                                
                        </div>
                        <div class="nav_cont_separ">
                        </div>
                        <div class="content" id="content">	
<?php	
	
		$objUser = new User();
		
		$status = $objUser->validateLogin($username, $pwd);
		if($status==1)
		{						
			if (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"Individual")==0)
			{	
				$objUser->updateStartDate();
			}
			$subjectArray = $objUser->getSubjectDetails();
			if(count($subjectArray)>1)
				$nextPage = "../subject_selection.php";
			else
			{
				$_SESSION['subjectno'] = $subjectArray[0];
				$theme	=	$objUser->theme;

				$sq	=	"SELECT * FROM adepts_newInterface WHERE schoolCode=".$objUser->schoolCode;
				$rs	=	mysql_query($sq);
				if($rw=mysql_fetch_array($rs))
				{
					$nextPage = "controller.php";
				}
				else if(strcasecmp($objUser->category,"School Admin")==0 || strcasecmp($objUser->category,"TEACHER")==0 || strcasecmp($objUser->category,"Home Center Admin")==0)
				{
					$nextPage = "controller.php";
				}
				else
				{
					if($theme==0 || $theme==4)
						$nextPage = "../4am/mindspark/controller.php";
					else
						$nextPage = "controller.php?mode=imageMode&image1=$image1&image2=$image2";
				}
			}
			
			$_SESSION['buddy']		 = $objUser->buddyID;
			$_SESSION['admin']       = $objUser->category;
			$_SESSION['subcategory'] = $objUser->subcategory;
			$_SESSION['userID']      = $objUser->userID;
			$_SESSION['username']	 = $objUser->username;
			$_SESSION['childClass']  = $objUser->childClass;
			$_SESSION['childSection']  = $objUser->childSection;
			$_SESSION['timePerDay']  = $objUser->timeAllowedPerDay;
			$_SESSION['childName']   = $objUser->childName;
			$_SESSION['schoolCode']  = $objUser->schoolCode;
			$_SESSION['theme']  = $objUser->theme;
			$context= $objUser->country=="US"?"US":"India";
			$_SESSION['context']  = $context;
						
			$_SESSION['isOffline'] = $objUser->isOffline; //for offline
			if($_SESSION['isOffline'])
			{				
				$_SESSION['offlineStatus'] = $objUser->offlineStatus;
				if($_SESSION['offlineStatus']==5)
				{
					echo '<script>window.location="index.php?login=4";</script>';
					exit();
				}
			}
			
            $query  = "SELECT userID FROM adepts_userClassUpgradeError WHERE userID=".$objUser->userID;
            $userid_result 	= mysql_query($query) or die(mysql_error());
		    if(mysql_num_rows($userid_result)>0){
		        //echo "<strong>Sorry for the inconvenience! You won't be able to login temporary due to class up-gradation in progress. Please try again later.<br>(If you are not able to login after 24 hours, please contact your teacher or customer support)</strong>";
                echo '<script>window.location="index.php?login=2";</script>';
		    }
            else{
                echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
			    echo '<form name="frmSession" id="frmSession" method="POST" action="'.($nextPage).'">';
			    echo '<input type="hidden" name="userID" id="userID" value="'.$objUser->userID.'">';
                            echo '<input type="hidden" name="nextPage" id="nextPage" value="'.$nextPage.'">';
			    echo '<input type="hidden" name="subjects" id="subjects" value="'.implode(",",$subjectArray).'">';
			    echo '<input type="hidden" name="mode" id="mode" value="createSession">';
			    echo '</form>';
			    echo '<script>document.frmSession.submit();</script>';				
            }
			
			
		}
		else if($status==2)
		{			
			echo '<form name="frmRenew" id="frmRenew" method="POST">';
		    echo '<h3>Your subscription period has ended.<br/>Hope you enjoyed working on Mindspark.<br/> Thank You!!</br></h3>';		
		    echo '<br/><br/>';
		    if ($objUser->category=="STUDENT" && strcasecmp($objUser->subcategory,"Individual")==0) {
		    	echo '<a href="javascript:renew(\''.$objUser->userID.'\')">Click here to renew.</a>';
			} 
			echo '<input type="hidden" name="userID" id="userID" value="'.$objUser->userID.'">';
			echo '</form>';	
		}	
		else if($status==3)
		{
			header("Location: index.php?login=1");
			exit;
		}
		elseif($status==0)
		{
			echo '<script>window.location="index.php?login=0";</script>';
		}
	
?>
		</div>
	</div>
</body>
</html>
