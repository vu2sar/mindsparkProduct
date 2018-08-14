<?php
	// ----------- Include section --------------- //

	include ("../userInterface/check1.php");
	include ("../userInterface/classes/clsUser.php");
	include("loginFunctions.php");
	
	// ------------------------------------------- //

	// Session Start Section.
		session_start();
	// --------------------//


	// Create the user object.
	$objUser = new User();
	$session_user_id = '';
	$session_parent_id = '';

	if(isset($_SESSION['ms_user_id']))
		$session_user_id = $_SESSION['ms_user_id'];
	else
		$session_parent_id = $_SESSION['parentUserID'];
	
	$samePasswordFlag = isset($_SESSION['samePasswordFlag']) ? $_SESSION['samePasswordFlag'] : false;
	// Unset the same password flag.
	unset($_SESSION['samePasswordFlag']);
	$parentMaster = false;
	if(isset($_SESSION['parentMaster']))
		$parentMaster = $_SESSION['parentMaster'];
	$status = $objUser->mathsValidateLogin($session_user_id,$session_parent_id,$_SESSION['parentMaster']);
	
	// New
	if($status==1)
	{			
        //Redirection internally
		//validationProcess($objUser, $browserName, $browserVersion, $image1, $image2, $browser);
		if (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"Individual")==0)
		{	
			$objUser->updateStartDate();
		}
		//$subjectArray = $objUser->getSubjectDetails();
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
		$_SESSION['country']  = $context;
					
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

		if(SERVER_TYPE=='LIVE')
		{			
			$query1  = "select * from freeTrialDetail where status='Active' and userID=".$objUser->userID;
			$userid_result1 = mysql_query($query1) or die(mysql_error());
			if(mysql_num_rows($userid_result1)>0){
				$userid_result_fetch =  mysql_fetch_assoc($userid_result1);
				$_SESSION['freeTrial']=1;
				if($userid_result_fetch["startDate"]>="2015-04-09")
					$_SESSION['freeTrialTopics']=1;
			}else{
				$_SESSION['freeTrial']=0;
			}
		}
		else
			$_SESSION['freeTrial']=0;
			
        $query  = "SELECT userID FROM adepts_userClassUpgradeError WHERE userID=".$objUser->userID;
        $userid_result 	= mysql_query($query) or die(mysql_error());
	    if(mysql_num_rows($userid_result)>0){
	        
            echo '<script>window.location="index.php?login=2";</script>';
	    }
        else{
            createSession();
    			
                $browserCategory = checkBrowser($browserName, $browserVersion);
                if ($browserCategory=='green' && !$localStorage && !$cookies) $browserCategory='red';
                if ($browserCategory != 'green') {
                    $path = "../userInterface/browser_detect.php?image1=$image1&image2=$image2";
                    echo '<html><body>';
                    echo '<form name="frmBrowser" id="frmBrowser" method="POST" action="' . $path . '">';
                    echo '<input type="hidden" name="sessionID" id="sessionID" value="' . $_SESSION["sessionID"] . '">';
					$windowName=$_SESSION['windowName'];
                    echo "</form><script>try { if(window.sessionStorage){sessionStorage.setItem('windowName','$windowName'); } else { window.name='$windowName'; } } catch(er) { }; document.frmBrowser.submit();</script></body></html>";
                    exit;
                }
                saveBrowserInfo($browser);
				/*echo "username - ".$username."<br>";
				echo "pwd - ".$pwd."<br>";
            	exit;*/
				// If password is default, then redirect to change password page
				// should be school user of class 3 or above, prompt should be given to some schools (Piloted)
				// $pilot_schools = array(2387554, 2285293);
				$pilot_schools = array();
				if($samePasswordFlag === true && in_array($objUser->schoolCode, $pilot_schools) && strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"School")==0 && $objUser->childClass > 2)
				{
					$day_of_week = date("w");	// consider from wednesday
					if($day_of_week == 3)
						$compare_date = date('Ymd');
					else {
						if($day_of_week == 4) $minus_days = 1;
						else if($day_of_week == 5) $minus_days = 2;
						else if($day_of_week == 6) $minus_days = 3;
						else if($day_of_week == 0) $minus_days = 4;
						else if($day_of_week == 1) $minus_days = 5;
						else if($day_of_week == 2) $minus_days = 6;
						else $minus_days = 0;
						$compare_date = date('Ymd', strtotime("-".$minus_days." days"));
					}
					if(!empty($_SESSION['sessionID'])) {
						$check_first_login = "SELECT COUNT(*) FROM adepts_sessionStatus WHERE userID = ".$objUser->userID." AND startTime_int >= ".$compare_date." AND sessionID != ".$_SESSION['sessionID'];
					} else {
						$check_first_login = "SELECT COUNT(*) FROM adepts_sessionStatus WHERE userID = ".$objUser->userID." AND startTime_int >= ".$compare_date;
					}
					$exec_first_login = mysql_query($check_first_login);
					$row_first_login = mysql_fetch_array($exec_first_login);
					if($row_first_login[0] == 0) {
						echo '<script>window.location="changePassword.php";</script>';
					}
				}
				else if((strcasecmp($objUser->category,"Teacher")==0 || strcasecmp($objUser->category,"School Admin")==0))
				{
					if(($objUser->childDob == "" || $objUser->childDob == "0000-00-00"))
						echo '<script>window.location="changePassword.php?firstLogin=1&proceed=1";</script>';
					else if($samePasswordFlag === true)
						echo '<script>window.location="changePassword.php?firstLogin=0";</script>';
				}
				
                login();//Redirect internally from the function
        }
		
		
	}
	else if($status==2)
	{			
		$objUser = new User($objUser->userID);
		echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
		echo '<form name="frmRenew" id="frmRenew" method="POST" action="../userInterface/renewSubscription.php">';
	    echo '<input type="hidden" name="userID" id="userID" value="'.$objUser->userID.'">';
                    //echo '<input type="hidden" name="nextPage" id="nextPage" value="'.$nextPage.'">';
	    echo '<input type="hidden" name="category" id="category" value="'.$objUser->subcategory.'">';
	    echo '<input type="hidden" name="parentEmail" id="parentEmail" value="'.$objUser->parentEmail.'">';
	    echo '</form>';
	    echo '<script>document.frmRenew.submit();</script>';
	}	
    else if($status==3)
    {
    	/**
    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
    	*/
		$_SESSION['loginPageMsg'] = 1;
        header("Location: index.php?login=1");
        exit;
    }
	elseif($status==0)
	{
		$_SESSION['loginPageMsg'] = 1;
        header("Location: index.php?login=0");
        exit;
		/*//echo '<script>window.location="index.php?login=0";</script>';*/

	}
    elseif($status==4)
    {
        parentLogin($_SESSION['parentUserID']);//Internal page redirection
    }
    elseif($status==5)
    {
        header("Location: index.php?login=3");
        exit;
    }
    elseif($status==6)
    {
        header("Location: index.php?login=4");
        exit;
    }
    elseif($status==8)
    {
        header("Location: index.php?login=5");
        exit;
    }
    elseif($status==7)
    {
		$if_locked = "SELECT COUNT(*) FROM adepts_forgetPassNotification WHERE status = 0 AND category = 2 AND childUserID = ".$objUser->userID;
		$exec_locked = mysql_query($if_locked);
		$row_locked = mysql_fetch_array($exec_locked);
		if($row_locked[0] > 0) {
			$_SESSION['loginPageMsg'] = 1;
			header("Location: index.php?login=9");
			exit;
		}
        $_SESSION['userID'] = $objUser->userID;
		$_SESSION['theme']  = $objUser->theme;
        $_SESSION['browserName'] = $browserName;
        $_SESSION['browserVersion'] = $browserVersion;
        $_SESSION['image1'] = $image1;
        $_SESSION['image2'] = $image2;
        $_SESSION['browser'] = $browser;
        header("Location: ../userInterface/picturePassword.php");
        exit;
    }




?>
	