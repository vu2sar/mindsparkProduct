<?php
class User
{
	var $userID;
	var $childName;
	var $childEmail;
	var $childClass;
	var $childSection;
	var $childDob;
	var $package;
	var $panNumber;
	var $parentName;
	var $parentEmail;
	var $contactno_res;
	var $contactno_cel;
	var $city;
	var $schoolName;
	var $schoolCode;
	var $startDate;
	var $endDate;
	var $category;
	var $subcategory;
	var $subscriptionDaysRemaining;
	var $username;
	var $packageType;
	var $currentOrdertype;
	var $country;
	var $buddyID;
	var $timeAllowedPerDay;
	var $arrSubject;
	var $theme;
    var $blocked;
	var $isOffline;
	var $offlineStatus;
	var $registrationDate;
	var $classChangeHistory;
	
	function User($userID="")
	{
		$this->arrSubject = array();
		if($userID!="")
		{
			$query 	= 'SELECT a.childName, a.childEmail, a.childClass, a.childSection, date_format(a.childDob,"%d-%m-%Y") childDob,
				  a.package, a.city, a.schoolName,a.pan_number, a.startDate,date_format(a.endDate,"%d-%m-%Y") endDate, a.contactno_cel, a.contactno_res, a.parentName, a.parentEmail,
				  a.category, a.subcategory, a.schoolCode, datediff(a.endDate,current_date) days, a.username, a.comment, a.country, a.buddyID, a.timeAllowedPerDay, 
                                  a.subjects, a.theme,a.secretQues,a.secretAns, c.is_block, a.registrationDate, a.classChangeHistory 
				  FROM   adepts_userDetails a,educatio_educat.common_user_details c WHERE a.userID=c.MS_userID AND a.userID='.$userID;
			$result     = mysql_query($query);
			$line       = mysql_fetch_array($result);
                        
			$this->userID     		= $userID;
			$this->childName  		= $line['childName'];
			$this->childEmail  		= $line['childEmail'];
			$this->childClass  		= $line['childClass'];
			$this->childSection     = $line['childSection'];
			$this->childDob			= $line['childDob'];
			$this->package			= $line['package'];
			$this->parentName  		= $line['parentName'];
			$this->parentEmail 		= $line['parentEmail'];
			$this->contactno_cel	= $line['contactno_cel'];
			$this->contactno_res	= $line['contactno_res'];
			$this->city				= $line['city'];
			$this->schoolName		= $line['schoolName'];
			$this->schoolCode       = $line['schoolCode'];
			$this->startDate		= $line['startDate'];
			$this->endDate			= $line['endDate'];
			$this->panNumber		= $line['pan_number'];
			$this->category			= $line['category'];
			$this->subcategory		= $line['subcategory'];
			$this->subscriptionDaysRemaining = $line['days'];
			$this->username		    = $line['username'];
			$this->endDate			= $line['endDate'];
			$this->country          = $line['country'];
			$this->buddyID          = $line['buddyID'];
			$this->arrSubject 	    = explode(",",$line['subjects']);
			$this->theme			= $line['theme'];
			$this->secretQues			= $line['secretQues'];
			$this->secretAns			= $line['secretAns'];
            $this->blocked			= $line['is_block'];
			$this->registrationDate	= $line['registrationDate'];
			$this->classChangeHistory	= $line['classChangeHistory'];

			$this->packageType      = "All";
			if($line['comment']=="MS_DEC")
				$this->packageType = "MS_DEC";

			$currentDate = date('Y-m-d');
			$query = "SELECT order_type FROM educatio_educat.ms_orderMaster WHERE schoolCode='$this->schoolCode' AND end_date >= '$currentDate' AND start_date <= '$currentDate'";
			$result = mysql_query($query);
			$line = mysql_fetch_assoc($result);
			$this->currentOrdertype			= $line['order_type'];
                        
		}
	}

	function getUserDetails($sessionID)
	{

		if($this->package==15)
			$pkgStr = "15-Day Free Trial";
		elseif ($this->package==1)
			$pkgStr = "1-Month";
		elseif ($this->package==6)
			$pkgStr = "6-Months";
		elseif ($this->package==12)
			$pkgStr = "1-Year";
		else
			$pkgStr = $this->category;
		$exploded_start_date = explode("-", $this->startDate);
		$new_start_date = $exploded_start_date[2]."-".$exploded_start_date[1]."-".$exploded_start_date[0];

		$startdate_stamp = strtotime($new_start_date);
		$today_stamp = strtotime(date("Y-m-d"));

		$days_total = round(((($today_stamp-$startdate_stamp)/60)/60)/24);
		$days_total = $days_total + 1;

		$total_seconds = $this->getTimeSpent();

		$days_logged_query = "SELECT count(DISTINCT cast(startTime as date)) FROM adepts_sessionStatus WHERE userID=".$this->userID;
		$days_logged_result = mysql_query($days_logged_query);
		if (mysql_num_rows($days_logged_result)!=0)
		{
			$days_logged_data = mysql_fetch_array($days_logged_result);
			$days_logged = $days_logged_data[0];

			$logged_mins = round(($total_seconds / $days_logged), 2);
			$logged_mins = intval($logged_mins/60) .":".str_pad($logged_mins%60,2,"0",STR_PAD_LEFT);
		}
		else
		{
			$days_logged = "Not Logged In";
			$logged_mins = 0;
		}

		$days_mins = round(($total_seconds / $days_total),2);
		$days_mins = intval($days_mins/60) .":".str_pad($days_mins%60,2,"0",STR_PAD_LEFT);

		if($sessionID!="")
		{
			$query  = "SELECT browser FROM adepts_sessionStatus WHERE sessionID=".$sessionID;
			$r = mysql_query($query);
			$l   = mysql_fetch_array($r);
			$pos = strpos($l[0],"Flash:");
			$browserVer = substr($l[0],0,$pos-2);
			$flashVer   = substr($l[0],$pos+6);
		}

		$pretestPercentile = " ";
		$query  = "SELECT percentile FROM adepts_pretestMaster WHERE userID=".$this->userID;
		$result = mysql_query($query);
		if($line=mysql_fetch_array($result))
			$pretestPercentile = $line['percentile'];

		$msg  = "<table align='center' border='1' style='font-size: 10pt;'><tr>";
		$msg .= "<td>Student Name:</td><td>".$this->childName."</td>";
		$msg .= "<td>Class:</td><td>".$this->childClass."</td>";
		$msg .= "<td>DOB:</td><td>".$this->childDob."</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td>Type of subscription:</td><td>".$pkgStr."</td>";
		$msg .= "<td>School:</td><td>".$this->schoolName.", ".$this->city."</td>";
		$msg .= "<td>PAN Number:</td><td>".$this->panNumber."</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td>Start Date:</td><td>".$this->startDate."</td>";
		$msg .= "<td>End Date:</td><td>".$this->endDate."</td>";
		$msg .= "<td>Parent Mobile:</td><td>".$this->contactno_cel."</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td>Browser:</td><td>".$browserVer."</td>";
		$msg .= "<td>Flash:</td><td>".$flashVer."</td>";
		$msg .= "<td>Loading Time</td><td>&nbsp;</td>";
		$msg .= "</tr><tr>";
		$msg .= "<td>Days logged | Mins / logged day</td><td>".$days_logged." | ".$logged_mins."</td>";
		$msg .= "<td>Days total | Mins / day</td><td>".$days_total." | ".$days_mins."</td>";
		$msg .= "<td>Pretest percentile</td><td>$pretestPercentile</td>";
		$msg .= "</tr></table>";

		return $msg;
	}

	//get the amount of time spent by the user
	function getTimeSpent($day="")
	{
		$userID = $this->userID;
		if($day=="")
			$day = date("Y-m-d");
		$query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues FROM adepts_sessionStatus WHERE  userID=".$userID;

		$time_result = mysql_query($query);
		$timeSpent = 0;
		while ($time_line = mysql_fetch_array($time_result))
		{
			$startTime = $this->convertToTime($time_line[1]);
			if($time_line[2]!="")	{
				$endTime = $this->convertToTime($time_line[2]);
			}
			else
			{
				if($time_line[3]=="")
					continue;
				else
					$endTime = $this->convertToTime($time_line[3]);
			}
			$timeSpent = $timeSpent + ($endTime - $startTime);	//in secs

		}

		return $timeSpent;
	}

	function convertToTime($date)
	{
		$hr   = substr($date,11,2);
		$mm   = substr($date,14,2);
		$ss   = substr($date,17,2);
		$day  = substr($date,8,2);
		$mnth = substr($date,5,2);
		$yr   = substr($date,0,4);
		$time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
		return $time;
	}


	function validateLogin($username, $pwd, $masterPwd = false, $parentLogin=false)
	{
		$data = array();

		$data['ms_user_id']  = 0;
		$data['mse_user_id'] = 0;
		$data['parent_user_id'] = 0;
		$data['status'] 	 = 0;
		

		$query  = "SELECT c.id, c.MS_userID, c.MSE_userID, c.MS_enabled, c.MSE_enabled, c.is_block, if(c.endDate > CURDATE(),1,0) as MATHSDATE, if(c.MSE_endDate > CURDATE(),1,0) as ENGDATE,c.category,c.subcategory
		                   FROM educatio_educat.common_user_details c WHERE c.category<>'External' AND c.category<>'IQS' AND c.username='".$username."'";
		if($masterPwd === false)
			$query  .= " AND (IF(c.category='STUDENT',IF(c.class>2, c.password=password('".$pwd."'), 1=1),c.password=password('".$pwd."')))";

      	$result 	= mysql_query($query) or die(mysql_error());
      	if(mysql_num_rows($result) > 0 && $parentLogin === false)
      	{
      		$user_details 	= mysql_fetch_array($result);

			// User have access to both English and Maths product.
      		if( $user_details['MSE_enabled'] == 1 && $user_details['MS_enabled'] == 1) 
      		{
      			// Show the landing page to the user for product selection.
      			// Redirect to landing page.
      			if(SERVER_TYPE == 'LOCAL') // Condition added to handle the users with subscription end for offline.
      			{
      				if($user_details['MATHSDATE'] == 1 && $user_details['ENGDATE'] == 0)
      				{
      					$data['ms_user_id']  = $user_details['MS_userID'];
			  			$data['mse_user_id'] = 0;
						$data['parent_user_id'] = 0;
						$data['status'] 	 = 1;
					}
					else
					{
						$data['ms_user_id']  = $user_details['MS_userID'];
		      			$data['mse_user_id'] = $user_details['MSE_userID'];
			  			$data['parent_user_id'] = 0;
		      			$data['status'] 	 = 3;
					}
      			}
      			else
      			{
                            //check if user is admin with all school rights. if yes then redirect him/her to getSchoolDetails.php page
                            if(strcasecmp($user_details['category'],"School Admin")==0 && strcasecmp($user_details['subcategory'],"All")==0){
                                $data['ms_user_id']  = $user_details['MS_userID'];
	      			$data['mse_user_id'] = $user_details['MSE_userID'];
		  			$data['parent_user_id'] = 0;
	      			$data['status'] 	 = 1;
                            }else{
	      			$data['ms_user_id']  = $user_details['MS_userID'];
	      			$data['mse_user_id'] = $user_details['MSE_userID'];
		  			$data['parent_user_id'] = 0;
	      			$data['status'] 	 = 3;
                            }
      			}
      		}
      		// User have access to mindspark maths only.
      		else if( $user_details['MSE_enabled'] != 1 && $user_details['MS_enabled'] == 1) 
      		{
      			// Redirect the user to mindspark maths interface. 
      			// -- Initialize all the variables and the parameters needed in the Maths product.
      			$data['ms_user_id']  = $user_details['MS_userID'];
      			$data['mse_user_id'] = 0;
	  			$data['parent_user_id'] = 0;
      			$data['status'] 	 = 1;
      			/*$_SESSION['ms_user_id']  = $user_details['MS_userID'];
      			header("Location: mindsparkMaths.php");*/
      		}
      		// User have access to mindspark english only.
      		else if( $user_details['MSE_enabled'] == 1 && $user_details['MS_enabled'] != 1)
      		{
      			// Redirect the user to mindspark English interface.
      			// -- Initialize all the variables and the parameters needed in the English product.
      			$data['ms_user_id']  = 0;
      			$data['mse_user_id'] = $user_details['MSE_userID'];
	  			$data['parent_user_id'] = 0;
      			$data['status'] 	 = 2;
      			
      			/*$_SESSION['mse_user_id'] = $user_details['MSE_userID'];
      			// Set the user name and password for login check for english.
      			header("Location: ../../mindspark/ms_english/Language/login/index/".$user_details['MSE_userID']);*/

      		} 
      		// Mindspark user id is present and english user id is 0
      		else if( $user_details['MS_userID'] != 0 && $user_details['MS_enabled'] == 0)
      		{
      			// Redirect the user to mindspark maths interface. 
      			// -- Initialize all the variables and the parameters needed in the Maths product.
      			$data['ms_user_id']  = $user_details['MS_userID'];
      			$data['mse_user_id'] = 0;
	  			$data['parent_user_id'] = 0;
      			$data['status'] 	 = 1;
      			/*$_SESSION['ms_user_id']  = $user_details['MS_userID'];
      			header("Location: mindsparkMaths.php");
      			exit;	*/
      		}
      		else if( $user_details['MSE_userID'] != 0 && $user_details['MSE_enabled'] == 0)
      		{
      			// Redirect the user to mindspark English interface.
      			// -- Initialize all the variables and the parameters needed in the English product.
      			$data['ms_user_id']  = 0;
      			$data['mse_user_id'] = $user_details['MSE_userID'];
	  			$data['parent_user_id'] = 0;
      			$data['status'] 	 = 2;
      			/*$_SESSION['mse_user_id'] = $user_details['MSE_userID'];
      			// Set the user name and password for login check for english.
      			header("Location: ../../mindspark/ms_english/Language/login/index/".$user_details['MSE_userID']);
      			exit;	*/
      		}
      		else if( $user_details['MS_userID'] != 0 && $user_details['MS_enabled'] == 0 && $user_details['MSE_userID'] != 0 && $user_details['MSE_enabled'] == 0  && $user_details['MATHSDATE'] == 1 && $user_details['ENGDATE'] == 1)
      		{
      			// Show the landing page to the user for product selection.
      			// Redirect to landing page.
      			if(SERVER_TYPE == 'LOCAL') // Condition added to handle the users with subscription end for offline.
      			{
      				if($user_details['MATHSDATE'] == 1 && $user_details['ENGDATE'] == 0)
      				{
      					$data['ms_user_id']  = $user_details['MS_userID'];
			  			$data['mse_user_id'] = 0;
						$data['parent_user_id'] = 0;
						$data['status'] 	 = 1;
					}
					else
					{
						$data['ms_user_id']  = $user_details['MS_userID'];
		      			$data['mse_user_id'] = $user_details['MSE_userID'];
			  			$data['parent_user_id'] = 0;
		      			$data['status'] 	 = 3;
					}
      			}
      			else
      			{
	      			$data['ms_user_id']  = $user_details['MS_userID'];
	      			$data['mse_user_id'] = $user_details['MSE_userID'];
		  			$data['parent_user_id'] = 0;
	      			$data['status'] 	 = 3;
      			}
      		}
      	}
      	else
      	{
      		$query  = "SELECT * FROM parentUserDetails WHERE username='$username' ";
      		if($masterPwd === false)
				$query  .= " AND password=password('".$pwd."')";
			/*if($pwd!="eimasterpwd2009")
		   		$query  .= " AND password=password('".$pwd."')";*/
			$result 	= mysql_query($query) or die(mysql_error());
            if(mysql_num_rows($result)>0)
			{
                $today = date("Y-m-d");
				$line 	= mysql_fetch_array($result);
                $_SESSION['parentUserID'] = $line['parentUserID'];
				/*$enabled = $line['enabled'];
                $verified = $line['verified'];
                $type = $line['loginType'];
                if($enabled==1 && $verified==1)
                    $status = 4;
                else if($verified==0)
                    $status=5;
                else if($enabled==0)
                    $status=6;
                else if($type!=1)
                    $status=8;
				*/
                $data['ms_user_id']  = 0;
	  			$data['mse_user_id'] = 0;
	  			$data['parent_user_id'] = $_SESSION['parentUserID'];
	  			$data['status'] 	 =  4;
            }
            else
            {
	      		$data['ms_user_id']  = 0;
	  			$data['mse_user_id'] = 0;
	  			$data['parent_user_id'] = $_SESSION['parentUserID'];
	  			$data['status'] 	 = 0;
            }
      	}
		
      	return $data;
	}
	//return values: 0 - Invalid login, 1 - valid login, 2 - package expired
	
	function mathsValidateLogin($userId,$parentId,$masterPwd = false)
	{
		if($userId != '')
		{
			// Add fields for Mindspark
			$status = 0;
			$query  = "SELECT a.userID,a.username,a.buddyID,a.category,a.subcategory,a.childClass,a.childDob,a.endDate,a.startDate,a.timeAllowedPerDay,a.package,a.childName,a.schoolCode, 
	                    a.subjects, a.country, a.theme, c.is_block, a.childSection, a.enabled
			                   FROM   adepts_userDetails a,educatio_educat.common_user_details c WHERE 
			                    a.category<>'External' 
			                    AND a.category<>'IQS' 
	                            AND a.userID=$userId 
	                            AND a.userID = c.MS_userID";
	                      /* echo $query;
	                       exit;*/
	       $userid_result 	= mysql_query($query) or die(mysql_error());
			if(mysql_num_rows($userid_result)>0)
			{
				

				$today = date("Y-m-d");
				$userID_line 	= mysql_fetch_array($userid_result);
				$this->userID 		= $userID_line['userID'];
				$this->username 	= $userID_line['username'];
				$this->category		= $userID_line['category'];
				$this->subcategory  = $userID_line['subcategory'];
				$this->childDob     = $userID_line['childDob'];
				$this->endDate		= $userID_line['endDate'];
				$this->startDate	= $userID_line['startDate'];
				$this->package	    = $userID_line['package'];
				$this->schoolCode   = $userID_line['schoolCode'];
				$this->country      = $userID_line['country'];
				$this->buddyID   		 = $userID_line['buddyID'];
				$this->childClass        = $userID_line['childClass'];
				$this->childSection        = $userID_line['childSection'];
				$this->enabled        = $userID_line['enabled'];
				$this->timeAllowedPerDay = $userID_line['timeAllowedPerDay'];
				$this->childName         = $userID_line['childName'];
				$this->schoolCode        = $userID_line['schoolCode'];
				$this->arrSubject        = explode(",",$userID_line['subjects']);
				$this->theme        	= $userID_line['theme'];
	            $this->blocked			= $userID_line['is_block'];
				$this->isOfflineSchool(); //check for offline schools
				
				/**
					Enabled = 0 --> Product not taken
					Enabled = 1 --> Product taken
					Enabled = 3 --> Hide Student
				*/
				if(($this->endDate!="" && $this->endDate < $today  && $this->enabled == 1) || ($this->endDate!="" && $this->endDate < $today  && $this->enabled == 0))
				{
					/**
					Subscription is over.
					*/
					$status = 2;
				}
				elseif(($this->enabled == 0 && $this->endDate!="" && $this->endDate > $today && strcasecmp($this->subcategory,"School") == 0) || ($this->enabled == 3 && strcasecmp($this->subcategory,"School")==0))
				{
					/**
		    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
			    	*/
					$status = 3;
				}
				else if (strcasecmp($this->category,"STUDENT")==0 && strcasecmp($this->subcategory,"School")==0 && $this->startDate>$today)	
				{
			    	/**
		    		Package not yet started for school users. 
		    		Username and password incorrect message is displayed.
			    	*/
					$status = 0;                        
				}	
				else if($this->enabled == 1)
				{
					//$pilot_schools = array(2387554);
					$pilot_schools = array(123);
					if(strcasecmp($this->category,"STUDENT")==0 &&  strcasecmp($this->subcategory,"School")==0 && $this->childClass < 3 && in_array($this->schoolCode, $pilot_schools))
					{
						/**
			    		Your Mindspark account has been locked because you failed to select the correct picture password. A request has been sent to your teacher. Please be a little patient.
				    	*/
						$status = 7;
					}
					else {
						if($this->childClass < 3) {
							$get_correct_entry = "SELECT COUNT(*) FROM adepts_userDetails WHERE ".($masterPwd===false?"password=password('$pwd')":'1=1') ." AND userID=".$this->userID;
							$exec_correct_entry = mysql_query($get_correct_entry);
							$row_correct_entry = mysql_fetch_array($exec_correct_entry);
							if($row_correct_entry[0] == 1)
							{
								/**
					    		Valid Users.
						    	*/
								$status = 1;
							}
							else
							{
								/**
					    		Username and password incorrect message is displayed.
						    	*/
								$status = 0;
							}
						} else {
							/**
				    		Valid Users.
					    	*/
							$status = 1;
						}
					}
				}
				if($this->blocked==1)
					/**
		    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
			    	*/
					$status = 3;
			}
		}
		else
		{
			$query  = "SELECT * FROM parentUserDetails WHERE parentUserID='$parentId' ";
			if($masterPwd === false)
				$query  .= " AND password=password('".$pwd."')";
			$result 	= mysql_query($query) or die(mysql_error().$query);
			if(mysql_num_rows($result)>0)
			{
				$today = date("Y-m-d");
				$line  = mysql_fetch_array($result);
				$_SESSION['parentUserID'] = $line['parentUserID'];
				$enabled = $line['enabled'];
				$verified = $line['verified'];
				$type = $line['loginType'];
				if($enabled==1 && $verified==1)
					$status = 4;
				else if($verified==0)
					$status=5;
				else if($enabled==0)
					$status=6;
				else if($type!=1)
					$status=8;
			}

			// *********  mine  ************//
            /*$query  = "SELECT * FROM parentUserDetails WHERE username='$username' ";
			if($pwd!="eimasterpwd2009")
		   		$query  .= " AND password=password('".$pwd."')";
			$result 	= mysql_query($query) or die(mysql_error());
            if(mysql_num_rows($result)>0)
			{
                $today = date("Y-m-d");
				$line 	= mysql_fetch_array($result);
                $_SESSION['parentUserID'] = $line['parentUserID'];
				$enabled = $line['enabled'];
                $verified = $line['verified'];
                $type = $line['loginType'];
                if($enabled==1 && $verified==1)
                    $status = 4;
                else if($verified==0)
                    $status=5;
                else if($enabled==0)
                    $status=6;
                else if($type!=1)
                    $status=8;
            }*/
        }
		return $status;
	}

	function englishValidateLogin($userId, $masterPwd = false)
	{
		if($userId != '')
		{
			// Add fields for Mindspark
			$status = 0;
			$query  = "SELECT a.userID,a.username,a.endDate,a.startDate,a.childName,a.schoolCode, c.is_block, a.childSection, a.enabled,a.subcategory,a.category FROM   educatio_msenglish.userDetails a,educatio_educat.common_user_details c WHERE a.userID=$userId AND a.userID = c.MSE_userID";
	                      
	       $userid_result 	= mysql_query($query) or die(mysql_error());
			if(mysql_num_rows($userid_result)>0)
			{

				$today         = date("Y-m-d");
				$userID_line   = mysql_fetch_array($userid_result);
				$userID        = $userID_line['userID'];
				$endDate   = $userID_line['endDate'];
				$enabled   = $userID_line['enabled'];
				$subcategory   = $userID_line['subcategory'];
				$category      = $userID_line['category'];
				$startDate = $userID_line['startDate'];
				$status        = 1;
				/**
					Enabled = 0 --> Product not taken
					Enabled = 1 --> Product taken
					Enabled = 3 --> Hide Student
				*/
				if(($endDate!="" && $endDate < $today  && $enabled == 1) || ($endDate!="" && $endDate < $today  && $enabled == 0))
				{
					/**
					Subscription is over.
					*/
					$status = 2;
				}
				 else if( ($enabled == 0 && $endDate!="" && $endDate > $today && strcasecmp($subcategory,"School")==0) || ($enabled == 3 && strcasecmp($subcategory,"School")==0) )
				{
					/**
		    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
			    	*/
					$status = 3;
				}
				 else if( strcasecmp($category,"STUDENT")==0 && strcasecmp($subcategory,"School")==0 && $startDate > $today )	
				{
			    	/**
		    		Package not yet started for school users. 
		    		Username and password incorrect message is displayed.
			    	*/
					$status = 0;                        
				}	
				else if ( $enabled == 1 )
				{
					 if(strcasecmp($category,"STUDENT") == 0 &&  strcasecmp($subcategory,"School") == 0 && in_array($this->school_code, $pilot_schools))
		            {
		            	/**
			    		Your Mindspark account has been locked because you failed to select the correct picture password. A request has been sent to your teacher. Please be a little patient.
				    	*/
		                $status = 7;
		            }
				}
				if($is_block == 1)
		        {
		        	/**
		    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
			    	*/
		            $status = 3;
		        }
			}
		}
		else
			$status = 0;
		return $status;
	}

    function updateDoB($birthDate, $secretQuestion, $secretAnswer)
    {
//        $query = "UPDATE adepts_userDetails SET childDob='$birthDate', secretQues='$secretQuestion', secretAns='".mysql_escape_string($secretAnswer)."' WHERE userID=".$this->userID;
        $query = "UPDATE educatio_educat.common_user_details SET dob='$birthDate', secretQues='$secretQuestion', secretAns='".mysql_escape_string($secretAnswer)."' WHERE MS_userID=".$this->userID;
		mysql_query($query) or die(mysql_error());
    }
    function updateParentEmail($parentEmail)
    {
        $query = "UPDATE educatio_educat.common_user_details SET additionalEmail='$parentEmail' WHERE MS_userID=".$this->userID;
		mysql_query($query) or die(mysql_error());
    }
    function updateEmail($Email)
    {
        $query = "UPDATE educatio_educat.common_user_details SET childEmail='$Email' WHERE MS_userID=".$this->userID;
		mysql_query($query) or die(mysql_error());
    }
	function updateStartDate()
	{
		$today = date("Y-m-d");
		if($this->startDate > $today)
		{
			if ($this->package==7)
			{
				$endDate = "date_sub(date_add('$today', INTERVAL $this->package DAY), INTERVAL 1 DAY)";
			}
			else
			{
				$endDate   = "date_sub(date_add('$today', INTERVAL $this->package MONTH), INTERVAL 1 DAY)";
			}

//			$update_date_query = 'UPDATE adepts_userDetails SET startDate=\''.$today.'\', endDate='.$endDate.' WHERE userID='.$this->userID;
                        $update_date_query = 'UPDATE educatio_educat.common_user_details SET startDate=\''.$today.'\', endDate='.$endDate.' WHERE MS_userID='.$this->userID;
			$update_date_result = mysql_query($update_date_query) or die(mysql_error());
		}
	}

	function getSubjectDetails()
	{
		if(strcasecmp($this->category,"TEACHER")==0)
		{
			$query  = 'SELECT distinct subjectno FROM adepts_teacherClassMapping WHERE userID='.$this->userID;
			$result = mysql_query($query) or die(mysql_error());
			while ($line = mysql_fetch_array($result))
				array_push($this->arrSubject, $line[0]);
		}
		else if(strcasecmp($this->category,"School Admin")==0 && $this->schoolCode!="")
		{
			$query  = 'SELECT distinct subjects FROM adepts_userDetails WHERE category=\'STUDENT\' AND schoolCode='.$this->schoolCode;
			$result = mysql_query($query) or die(mysql_error());
			while ($line = mysql_fetch_array($result))
			{
				$tmpArray = explode(",",$line[0]);
				for($i=0; $i<count($tmpArray); $i++)
					array_push($this->arrSubject, $tmpArray[$i]);
			}
		}
                   $this->arrSubject = array_values(array_unique($this->arrSubject));	//array_values is used to reset the keys.
		return $this->arrSubject;
	}

	function getNoOfDaysLoggedIn($fromDate="")
	{
	    $query  = 'SELECT COUNT(DISTINCT startTime_int) as daysLoggedIn FROM '.TBL_SESSION_STATUS.' WHERE userID='.$this->userID;
	    if($fromDate!="")
		{
			$fromDate = str_replace("-","",$fromDate);
		    $query .= " AND startTime_int >=$fromDate";
		}
	    $result = mysql_query($query);
	    $line   = mysql_fetch_array($result);
	    return $line['daysLoggedIn'];
	}

	function checkForFeedback()
	{
		$todayDate = date("Y-m-d");
		$this->childClass = $this->childClass?$this->childClass:0;
		$isFeedBackDue = 0;
		//Procedure For checking pilot school, set bool variable $isPilotSchool with value.
		$isPilotSchool = false;
		if($this->currentOrdertype == "pilot")
			$isPilotSchool = true;
		if($isPilotSchool)		// checks whether feedback is available to pilot user or not
		{			
			if(!$this->isFeedBackGiven("", ""))		// checks whether user has given feedback previously or not
			{
				$query  = 'SELECT count(sessionID) FROM '.TBL_SESSION_STATUS.' WHERE userID='.$this->userID;
				$result = mysql_query($query) or die($query);
				$line   = mysql_fetch_array($result);
				if($line[0] > 2)
					$isFeedBackDue = 1;
				if($isFeedBackDue)
				{
					$userCategory = "pilot".$this->category;
					if(strcasecmp($userCategory, "PILOTTEACHER")==0)
					{
						$clsAndSecArr = $this->getTeacherClassMapping($this->userID);	// gives class,section of teacher.
						$setStr = array();
						foreach ($clsAndSecArr as $key => $value) 	// makes an array having set number as key and lastmodified as value. Used to get the recent feedback in case if more then 1 feedback is available
						{
							$query = "SELECT setNo,lastModified FROM adepts_userFeedbackSet WHERE category = '".ucwords(strtolower($userCategory))."' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and FIND_IN_SET($key,class) and status = 'Live'";
							$result = mysql_query($query);							
							while($line = mysql_fetch_array($result)) 
							{
								$setNo = $line['setNo'];
								$setStr["$setNo"] = strtotime($line["lastModified"]);								
							}							
						}
						arsort($setStr);	// sorts array according to lastModifed in descending order
						if(count($setStr)>0)
							$finalFeedbackDueWithSet = "1~".reset(array_keys($setStr));		// array_keys gives array of keys i.e setNo and reset gives first element of an array
						else
							$finalFeedbackDueWithSet = "0~0";
					}
					else if(strcasecmp($userCategory, "PILOTSCHOOL ADMIN") ==0) 	// shows feedback to schooladmin which are available for teachers
					{
						$query = "SELECT setNo FROM adepts_userFeedbackSet WHERE category = 'pilotTeacher' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and status = 'Live' ORDER BY lastModified desc limit 1";
						$result = mysql_query($query);
						$line   = mysql_fetch_array($result);
						if(mysql_num_rows($result)>0)
							$finalFeedbackDueWithSet = "1~".$line["setNo"];
						else
							$finalFeedbackDueWithSet = "0~0";
					}
					else
					{
						$query = "SELECT setNo FROM adepts_userFeedbackSet WHERE category = '".ucWords(strtolower($userCategory))."' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and FIND_IN_SET('".$this->childClass."',class) and status = 'Live' ORDER BY lastModified desc limit 1";						
						$result = mysql_query($query);
						$line   = mysql_fetch_array($result);
						if(mysql_num_rows($result)>0)
							$finalFeedbackDueWithSet = "1~".$line["setNo"];
						else
							$finalFeedbackDueWithSet = "0~0";
					}
				}
			}
		}
		else
		{
			$currentMonth  = date("m");
			$currentYear   = date("Y");
            if(!$this->isFeedBackGiven($currentYear, $currentMonth))   //Check if the feedback already given
			{
				$prevMonth = date("Y-m",mktime(0,0,0,$currentMonth-1,date("d"),$currentYear));
				//Check if the user has attempted at least one session in the last month
				$query  = 'SELECT count(sessionID) FROM '.TBL_SESSION_STATUS.'
						   WHERE userID='.$this->userID.' AND date_format(startTime,"%Y-%m")="'.$prevMonth.'"';
				$result = mysql_query($query);
				$line   = mysql_fetch_array($result);
				if($line[0] >= 2)
					$isFeedBackDue = 1;
				if($isFeedBackDue)
				{
					$userCategory = $this->category;
					if(strcasecmp($userCategory, "TEACHER")==0 )
					{
						$clsAndSecArr = $this->getTeacherClassMapping($this->userID);
						foreach ($clsAndSecArr as $key => $value) 	// same as above pilot case
						{
							$query = "SELECT setNo,lastModified FROM adepts_userFeedbackSet WHERE category = '".ucwords(strtolower($userCategory))."' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and FIND_IN_SET($key,class) and status = 'Live'";
							$result = mysql_query($query);
							while($line = mysql_fetch_array($result)) 
							{
								$setNo = $line['setNo'];
								$setStr["$setNo"] = strtotime($line["lastModified"]);								
							}	
						}
						arsort($setStr);
						if(count($setStr)>0)
							$finalFeedbackDueWithSet = "1~".reset(array_keys($setStr));		// array_keys gives array of keys i.e setNo and reset gives first element of an array
						else
							$finalFeedbackDueWithSet = "0~0";
					}
					else if(strcasecmp($userCategory, "SCHOOL ADMIN") ==0)
					{
						$query = "SELECT setNo FROM adepts_userFeedbackSet WHERE category = 'Teacher' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and status = 'Live' ORDER BY lastModified desc limit 1";
						$result = mysql_query($query);
						$line   = mysql_fetch_array($result);
						if(mysql_num_rows($result)>0)
							$finalFeedbackDueWithSet = "1~".$line["setNo"];
						else
							$finalFeedbackDueWithSet = "0~0";					
					}
					else
					{
						$query = "SELECT setNo FROM adepts_userFeedbackSet WHERE category = '".ucwords(strtolower($userCategory))."' and '".$todayDate."' >= startDate and '".$todayDate."' <= endDate and (FIND_IN_SET('".$this->schoolCode."',schoolCode) OR schoolCode = 'ALL') and FIND_IN_SET('".$this->childClass."',class) and status = 'Live' ORDER BY lastModified desc limit 1";						
						$result = mysql_query($query);
						$line   = mysql_fetch_array($result);
						if(mysql_num_rows($result)>0)
							$finalFeedbackDueWithSet = "1~".$line["setNo"];
						else
							$finalFeedbackDueWithSet = "0~0";	
					}
				}
			}          
		}
		return $finalFeedbackDueWithSet;
	}

	//check if feedback already given for the month
	function isFeedBackGiven($year,$month)
	{
		$patchDate = "2012-02-15"; //Date when new feedback logic to be updated..
		if($year == "" && $month == "")
			$query  = "SELECT count(qid) FROM adepts_feedbackresponse WHERE  userID=$this->userID AND type='' AND feedbackdate>'$patchDate'";
		else
			$query = "SELECT count(qid) FROM adepts_feedbackresponse WHERE  userID=$this->userID AND date_format(feedbackdate,'%Y-%m') = '$year-$month'";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		if($line[0]>0)
		    return true;
		else
		    return false;
	}
	function getTeacherClassMapping($userID)
	{
		$query = "SELECT class,section FROM adepts_teacherClassMapping WHERE userID=$userID";
		$result = mysql_query($query) or die(mysql_error());
		while ($line = mysql_fetch_array($result))
		{
			$classAndSectionArr[] = $line;
		}
		for($i=0; $i<count($classAndSectionArr); $i++)
		{			
			if($i != 0 && ($classAndSectionArr[$i]['class'] == $classAndSectionArr[$i-1]['class']))
			{
				$returnArr[$classAndSectionArr[$i]['class']] = $classAndSectionArr[$i-1]['section'].",".$classAndSectionArr[$i]['section'];
			}
			else
			{
				$returnArr[$classAndSectionArr[$i]['class']] = $classAndSectionArr[$i]['section'];				
			}
		}
		return $returnArr;
	}
	
	function isSetNewInterface()
	{
		$newInterface = 0;
		$sq	=	"SELECT interfaceFlag FROM adepts_teacherInterfaceScreen WHERE userID=".$this->userID;
		$rs	=	mysql_query($sq);
		if(mysql_num_rows($rs)!=0)
		{
			$rw	=	mysql_fetch_array($rs);
			if($rw[0]!=0)
				$newInterface = 1;
			else
				$newInterface = 2;
		}
		else
		{

			//$sq	=	"INSERT INTO adepts_teacherInterfaceScreen SET userID=".$this->userID.", schoolCode=".$this->schoolCode;
			//if($rs=mysql_query($sq))
				$newInterface = 3;

		}
		return $newInterface;
	}
	
	function isOfflineSchool()
	{
		//1 => sync happened - online mode , 2 => sync not happened - online mode , 3 => sync happened - offline mode , 4 => sync not happened - offline mode, 5 => online mode is not available for this school, 6 => Don't block anything if sync not happened-online mode, 7 => Don't block anything if sync not happened-offline mode
		if($_SERVER['SERVER_NAME'] == "www.mindspark.in" || $_SERVER['SERVER_NAME'] == "mindspark.in" || $_SERVER['SERVER_NAME'] == "122.248.236.40")
			$mode="online";
		else
			$mode="offline";
		$sq	=	"SELECT abbreviation,syncTimeMorning,syncTimeEvening,isOnlySchoolUsage,unsynchedStatus FROM adepts_offlineSchools WHERE schoolCode=".$this->schoolCode;
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_assoc($rs))
		{
			$this->isOffline = true;
			$abbreviation = $rw["abbreviation"];
			$syncTimeMorning = $rw["syncTimeMorning"];
			$syncTimeEvening = $rw["syncTimeEvening"];
			$isOnlySchoolUsage = $rw["isOnlySchoolUsage"];
			$unsynchedStatus = $rw["unsynchedStatus"];
			$currentTime = date("H:i:s");
			if($isOnlySchoolUsage==1 && $mode=="online")
			{
				$this->offlineStatus = 5;
			}
			else if($unsynchedStatus=="Allowed" && $mode=="online")
			{
				$this->offlineStatus = 6;
			}
			else if($unsynchedStatus=="Allowed")
			{
				$this->offlineStatus = 7;
			}
			else
			{
				$sqSync	= "SELECT lastSyncDate FROM adepts_sync_status WHERE schoolCode=".$this->schoolCode;
				if($currentTime >= $syncTimeMorning && $currentTime < $syncTimeEvening)
					$sqSync	.= " AND lastSyncDate>='".date("Y-m-d")." ".$syncTimeMorning."'";
				else if($currentTime < $syncTimeMorning)
					$sqSync	.= " AND lastSyncDate>='".date("Y-m-d", strtotime('-1 day', strtotime(date("Y-m-d"))))." ".$syncTimeEvening."'";
				else
					$sqSync	.= " AND lastSyncDate>='".date("Y-m-d")." ".$syncTimeEvening."'";
				$rsSync	=	mysql_query($sqSync);
				if(mysql_num_rows($rsSync)>0)
				{
					if($mode == "online")
						$this->offlineStatus = 1;
					else
						$this->offlineStatus = 3;
				}
				else
				{
					if($mode == "online")
						$this->offlineStatus = 2;
					else
						$this->offlineStatus = 4;
				}
			}
		}
		else
		{
			$this->isOffline = false;
		}
	}

	/*function getTopicActivated($subjectno)
	{
		if($this->category=="STUDENT" && (strcasecmp($this->subcategory,"SCHOOL")==0 || strcasecmp($this->subcategory,"Home Center")==0))
		{
			$query = "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode
				      FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
				      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND a.schoolCode=$this->schoolCode";
			if($this->childClass != "")
			    $query .= " AND a.class =$this->childClass";
			if($this->childSection != "")
			    $query .= " AND a.section ='$this->childSection'";
			$query .= " UNION ";
			$query .= "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode
				      FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
				      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND userID=$this->userID";
			$result = mysql_query($query) or die(mysql_error());

			while ($line = mysql_fetch_array($result))
				$teacherTopics[$line[1]] = $line[0];
		}
		elseif ($this->category=="STUDENT" && strcasecmp($this->subcategory,"Center")==0)
		{
			$query  = "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode
				       FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
				       WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND userID=$this->userID";
			$result = mysql_query($query) or die(mysql_error());

			while ($line = mysql_fetch_array($result))
				$teacherTopics[$line[1]] = $line[0];
		}
		else
		{
			$query  = "SELECT teacherTopicDesc, teacherTopicCode FROM adepts_teacherTopicMaster
			           WHERE  live =1 AND subjectno=".$subjectno." AND customTopic=0";
			if($this->packageType=="MS_DEC")
				$query .= " AND teacherTopicCode in (".MS_DEC_TOPICS.")";
			$query .= " ORDER BY classification, teacherTopicOrder, teacherTopicCode";
			$result = mysql_query($query) or die(mysql_error());

			while ($line=mysql_fetch_array($result))
			{
				$classLevel = getClassLevel($line[1]);
				if($this->childClass!="" && !in_array($this->childClass,$classLevel) && $this->packageType=="All")
					continue;
				$teacherTopics[$line[1]] = $line[0];
			}
		}
		return $teacherTopics;
	}*/


}

?>