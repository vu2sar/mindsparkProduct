<?php
class teacher
{
	var $userID;
	var $firstName;
	var $lastName;
	var $emailID;
	var $contactno_res;
	var $contactno_cel;
	var $city;
	var $country;
	var $schoolCode;
	var $username;
	var $password;


	function teacher()
	{
		$this->userID     		= "";
		$this->firstName  		= "";
		$this->lastName   		= "";
		$this->emailID    		= "";
		$this->contactno_res  	=	"";
		$this->contactno_cel  	=	"";
		$this->city       		= "";
		$this->country    		= "";
		$this->schoolCode 		= "";
		$this->username   		= "";
	}

	function setPostVariables()
	{
		$this->userID 	 	 = $_POST['userID'];
		$this->firstName 	 = $_POST['firstName'];
		$this->lastName  	 = $_POST['lastName'];
		$this->emailID   	 = $_POST['emailID'];
		$this->contactno_res = $_POST['contactno_res'];
		$this->contactno_cel = $_POST['contactno_cel'];
		$this->city      	 = $_POST['city'];
		$this->country    	 = $_POST['country'];

	}

	function populateDetails($userID, $firstName, $lastName, $emailID, $contactno_res, $contactno_cel, $city, $country, $username)
	{
		$this->userID    = $userID;
		$this->firstName = $firstName;
		$this->lastName  = $lastName;
		$this->emailID   = $emailID;
		$this->contactno_res = $contactno_res;
		$this->contactno_cel = $contactno_cel;
		$this->city      = $city;
		$this->country   = $country;
		$this->username  = $username;
	}

	function getDetails($userID)
	{
		$query  = "SELECT userID, childName, username, childEmail, contactno_res, contactno_cel, city, country FROM adepts_userDetails WHERE userID=$userID";
		$result = mysql_query($query);
		$line=mysql_fetch_array($result);
		$tempName = explode(" ",$line['childName']);

		$this->populateDetails($userID, $tempName[0], $tempName[1],$line['childEmail'],$line['contactno_res'],$line['contactno_cel'],$line['city'],$line['country'],$line['username']);

	}

	function insertDetails($schoolCode, $grade, $section, $subj)
	{
		$msg = "";
		//Get start and end date (from the school admin id)
		$query  = "SELECT startDate, endDate FROM adepts_userDetails WHERE userID=".$_SESSION['userID'];
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$startDate = $line[0];
		$endDate   = $line[1];
		$name = ucfirst($this->firstName)." ".ucfirst($this->lastName);
		$password  = strtolower(str_replace(" ","",$this->firstName));

    	$q = "SELECT schoolName FROM educatio_educat.schools WHERE schoolno=$schoolCode";
		$r = mysql_query($q);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];

		if(strtolower(str_replace(" ","",$this->lastName))!="")
					$password .= ".".strtolower(str_replace(" ","",$this->lastName));
		
		/*
		Check if user with same username already exist. If exists, try to generate unique username for that user
		*/
		$check_username = $password;
		$username_changed = false;
		for($i=1; $i<=6; $i++) {
			$check_user = "SELECT id FROM educatio_educat.common_user_details WHERE username = '".mysql_real_escape_string($check_username)."'";
			$exec_user = mysql_query($check_user);
			if(mysql_num_rows($exec_user) > 0) {
				$check_username = $password.$i;
				$username_changed = true;
			} else {
				$password = $check_username;
				break;
			}
		}
		
		$this->password = $password;
		
//		$query = "INSERT INTO adepts_userDetails(username, password, childName, childEmail, category, schoolCode, city, country, contactno_res, contactno_cel, startDate, endDate, schoolName)
//				  VALUES ('".$password."',password('".$password."'),'".$name."','".$this->emailID."','TEACHER',$schoolCode,'".$this->city."','$this->country','".$this->contactno_res."','".$this->contactno_cel."','".$startDate."','".$endDate."','".$schoolName."')";
                $query = "INSERT INTO educatio_educat.common_user_details(username, password,Name, first_name,last_name, childEmail, category, schoolCode, city, country, contactno_res, contactno_cel, startDate, endDate, schoolName, MS_enabled, MS_activationdate)
				  VALUES ('".$password."',password('".$password."'),'$name','".ucfirst($this->firstName)."','".ucfirst($this->lastName)."','".$this->emailID."','TEACHER',$schoolCode,'".$this->city."','$this->country','".$this->contactno_res."','".$this->contactno_cel."','".$startDate."','".$endDate."','".$schoolName."',1,curdate())";
				  
		//echo $query;
		mysql_query("begin");
		mysql_query($query);

		$err = mysql_errno();
		if($err=="" || $err==0)
		{
			$this->userID = mysql_insert_id();
			$this->username = $this->emailID;
			$this->schoolCode = $schoolCode;
			$errmsg = $this->addClassDetails($this->userID, $grade, $section, $subj);

			if(!$errmsg)
			{

				mysql_query("rollback");
				$msg = "A teacher for the specified class already exists! Kindly save the details again";
				$this->userID="";
			}
			else {
				mysql_query("commit");
                                $queryUpdate = "UPDATE educatio_educat.common_user_details SET MS_userID=".$this->userID." WHERE id=".$this->userID;
                                $result = mysql_query($queryUpdate) or die($queryUpdate.  mysql_error());
                                //echo var_dump($result);
			}
		}
		else
		{

			if($err==1062)
				$msg = "The user ID already exists!";
			else
				$msg = "Some error occured in processing!";

		}
		
		if($msg == "" && $username_changed) $msg = "-9";

		return $msg;
	}

	function addClassDetails($userID, $grade, $section, $subj)
	{
		$insert = false;
		$query = "INSERT INTO adepts_teacherClassMapping(userID, class, section, subjectno) VALUES ";
		for($i=0; $i<count($grade) ; $i++)
		{
			$tempSection = isset($section[$i])?$section[$i]:"";
			$tmpSubj     = isset($subj[$i])?$subj[$i]:2;
			if($grade[$i]=="")
				continue;
			/*elseif ($this->existTeacher($grade[$i], $tempSection, $subj[$i]))
				continue;*/

			$query .= "($userID, $grade[$i], '$tempSection', '$tmpSubj'),";
			$insert = true;
		}
		$query = substr($query,0,-1);
		//echo $query;exit;

		if($insert)
		{
			mysql_query($query);
			if(mysql_error()=="")
				return true;
			else
				return false;

		}
		else {
			return true;
		}

	}

	function existTeacher($grade, $section, $subjectno)
	{
		$userID = $_SESSION['userID'];
		$query  = "SELECT schoolCode FROM adepts_userDetails WHERE userID=".$userID;
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$schoolCode = $line[0];
		$query  = "SELECT count(*) FROM adepts_userDetails a, adepts_teacherClassMapping b
				   WHERE  a.userID=b.userID AND subjectno=$subjectno AND schoolCode=".$schoolCode." AND class=".$grade;
		if($section!="")
			$query .= " AND section='$section'";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		if($line[0]==0)
			return false;
		else
			return true;
	}

	function updateDetails($grade, $section, $subj)
	{
		$name = ucfirst($this->firstName)." ".ucfirst($this->lastName);
//		$query = "UPDATE adepts_userDetails SET childName='".$name."', childEmail='".$this->emailID."',
//						 contactno_res='".$this->contactno_res."',contactno_cel='".$this->contactno_cel."',
//						 city='".$this->city."', country='".$this->country."'
//				  WHERE  userID=".$this->userID;
                $query = "UPDATE educatio_educat.common_user_details SET Name='".$name."',first_name='".ucfirst($this->firstName)."', last_name='".ucfirst($this->lastName)."',"
                        . " childEmail='".$this->emailID."', contactno_res='".$this->contactno_res."',contactno_cel='".$this->contactno_cel."',"
			. " city='".$this->city."', country='".$this->country."' WHERE  MS_userID=".$this->userID;
		mysql_query($query) or die(mysql_error());
		$msg = "";
		if(count($grade)>0)
		{
			if($this->addClassDetails($this->userID, $grade, $section, $subj))
				$msg = "";
			else
				$msg = "A teacher for the specified class already exists!";

		}
		return $msg;

	}

	function removeClassMapping($grade, $section, $subj, $userID,$flagForOffline)
	{
	    $query = "DELETE FROM adepts_teacherClassMapping WHERE userID=$userID AND subjectno=$subj AND class=$grade AND section='$section'";
		if($flagForOffline)
	    	logDeleteQuery("DELETE FROM adepts_teacherClassMapping WHERE userID=$userID AND subjectno=$subj AND class=$grade AND section='$section'",'adepts_teacherClassMapping',$_SESSION["schoolCode"],array('userID'=>$userID), '',"userID=$userID");
	    mysql_query($query) or die("Error in deleting the class mapping!");
	}

	function valid_email_address() {
		$email_address = $this->emailID;
	    if(trim($email_address) == '' || !strstr($email_address, '@')) :
	        return false;
	    endif;

	    list($username, $domain) = explode('@', $email_address);

	    return (function_exists('getmxrr') && getmxrr($domain, $mxhost)) || fsockopen($domain, 25, $errno, $errstr, 30);
	}



}

?>
