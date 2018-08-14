<?php

class User_model extends CI_model
{
 	public $db_educat;
    private  $table_userdetails = '';
    private  $table_common_user_details = '';
    private  $table_adepts_user_comments = '';
    private  $table_adepts_rewardPoints = '' ;
    private  $table_adepts_sessionStatus = '';
    private  $table_accesstoken = '';
    private  $subject_code = 0;

	public function __construct() {
		
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);

		$this->db_educat = $this->load->database('database_educat',TRUE);

		$this->db_adepts = $this->load->database('database_adepts',TRUE);

        $this->table_userdetails = 'adepts_userDetails';
        $this->table_common_user_details = 'common_user_details';
        $this->table_adepts_user_comments = 'adepts_userComments';
        $this->table_notice_board_comments = 'adepts_noticeBoardComments';
        $this->subject_code = SUBJECTNO; 
        $this->table_rewardPoints 	= 'rewardPoints';
        $this->table_userBadges 	= 'userBadges';
        $this->table_sessionStatus = 'sessionStatus';
        $this->table_accesstoken = TBL_ACCESSTOKEN;
	}


	/**
	 * function description : Get user information
	 * param1   userName
	 * @return  array, user information 
	 * 
	 * */
	public function getUserData($user_id){
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('userDetails u');
		$this->dbEnglish->join('userCurrentStatus q', 'u.userID=q.userID', 'inner');
		$this->dbEnglish->where('u.userID',$user_id);
		$query = $this->dbEnglish->get();

		$userInfoArr = $query->result_array();
	

		foreach($userInfoArr[0] as $key => $value)
			$this->$key = $value;

		return $userInfoArr[0];

	}


	public function getContentFlowMaster($user_id) {

		$this->dbEnglish->Select('contentType as presentContentType,totalAttempts as totalConversationAttempts,remediationPosition,orderNo,contentAttemptCount');
		$this->dbEnglish->from('userContentAttemptDetails uc');
		$this->dbEnglish->join('userContentFlowStatus uf', 'uc.userID=uf.userID AND uc.contentType=uf.userContentFlowType', 'inner');
		$this->dbEnglish->where('uf.userID',$user_id);
		$query = $this->dbEnglish->get();
		$result = $query->row_array();		
		return $result;
	}

	public function updateUserCurrentStatusLevel($user_id)
	{
		
		$this->dbEnglish->Select('c.passageLevel,c.conversationLevel,c.freeQuesLevel,d.childClass');
		$this->dbEnglish->from('userCurrentStatus c');
		$this->dbEnglish->join('userDetails d', 'c.userID=d.userID', 'inner');
		$this->dbEnglish->where('c.userID',$user_id);
		$query = $this->dbEnglish->get();
		$userInfoArr = $query->result_array();

		$passageLevel=$userInfoArr[0]['passageLevel'];
		$conversationLevel=$userInfoArr[0]['conversationLevel'];
		$freeQuesLevel=$userInfoArr[0]['freeQuesLevel'];
		$childClass=$userInfoArr[0]['childClass'];
		$passageLevel=($passageLevel!=$childClass)?$childClass:$passageLevel;
		$conversationLevel=($conversationLevel!=$childClass)?$childClass:$conversationLevel;
		$freeQuesLevel=($freeQuesLevel!=$childClass)?$childClass:$freeQuesLevel;

		$this->dbEnglish->set('conversationLevel', $conversationLevel);
		$this->dbEnglish->set('passageLevel', $passageLevel);
		$this->dbEnglish->set('freeQuesLevel', $freeQuesLevel);
		$this->dbEnglish->where('userID',$user_id);
		$this->dbEnglish->update('userCurrentStatus');
	}




	
	/**
	 * function description : Get teacher class mapping information
	 * param1   userID
	 * @return  string, format eg: class,section~class,section;
	 * 
	 * */
	public function getTeacherClassMapping($userID){
		$this->dbEnglish->Select('*');
		$this->dbEnglish->from('teacherClassMapping');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		$userClassArr = $query->result_array();		
		$teacherClassMap="";
		 foreach($userClassArr as $value)
		 {
			$teacherClassMap.=$value["childClass"].",".$value["childSection"].'~';		 
		 }
		 $teacherClassMap=rtrim($teacherClassMap, "~");
		return $teacherClassMap;
	}

	/**
	 * function description : Get child name for the passed userID
	 * param2   userID
	 * @return  string, child name 
	 * 
	 * */

	public function getChildName($userID){
		$this->dbEnglish->Select('childName');
		$this->dbEnglish->from('userDetails');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();

		$childName = $query->result_array();
		return $childName[0]['childName'];
	}

	/**
	 * function description : update user details
	 * param1  userName
	 * param2  information array to be updated
	 * @return  none
	 * 
	 * */

	public function updateUserData($username,$updateInfoArr)
	{
		$this->dbEnglish->where('userName', $username);
		$this->dbEnglish->update('userDetails', $updateInfoArr);
	}

	public function getTotalSessionsDoneByUser()
	{
		$this->dbEnglish->where('userID', $this->session->userdata('userID'));
		$this->dbEnglish->from('sessionStatus');
		$sessionTotalCount = $this->dbEnglish->count_all_results();
		return $sessionTotalCount;
	}
	public function resetPasswordRequestCount($teacherUserID)
	{
		$where = array(
				'teacherUserID' => $teacherUserID,
				'status'	  	=> 0
			);
		$this->dbEnglish->select('*');
		$this->dbEnglish->from('forgetPassNotification');
		$this->dbEnglish->where($where);
		$this->dbEnglish->where('childUserID !=', $teacherUserID);
		$this->dbEnglish->group_by('childUserID'); 

		$request_count = $this->dbEnglish->get();
		$req_arr = $request_count->result_array();
		return count($req_arr);

	}
	public function resetPassword($id, $status)
	{
		$where = array(
				'id' => $id
			);
		$update = array(
				'status' => $status
			);
		$this->dbEnglish->where($where);
		$this->dbEnglish->update('forgetPassNotification' , $update);

		if($status != 2)
		{
			$query = $this->dbEnglish->query('select f.id, f.childUserID, u.userName, u.childName, u.childClass, u.childSection as section, f.status, f.requestDate from forgetPassNotification as f, userDetails as u where f.childUserID = u.userId and f.id='.$id);
			
			$result = $query->result_array();
			$this->changePassword( $result[0]['childUserID'] , $result[0]['userName'] );
		}

	}
	public function resetPasswordRequestUserList($teacherUserID)
	{
		
		/*$query = $this->dbEnglish->query('select f.id, f.childUserID, u.userName, u.childName, u.childClass, u.childSection as section, f.status, f.requestDate from forgetPassNotification as f, userDetails as u where f.childUserID = u.userId and f.teacherUserID = '.$teacherUserID.' and f.status = 0');*/
		$query = $this->dbEnglish->query('select f.id, f.childUserID, u.userName, u.childName, u.childClass, u.childSection as section, f.status, f.requestDate  from  (select childUserID,max(requestDate) as requestDate from forgetPassNotification group by childUserID) as child  inner join forgetPassNotification as f on child.childUserID=f.childUserID and child.requestDate=f.requestDate right join userDetails as u on f.childUserID = u.userId and f.teacherUserID = '.$teacherUserID.' and f.childUserID != f.teacherUserID where f.status = 0 group by childUserID ');

		$result = $query->result_array();
		return $result;
	}
	public function updateMSPasswordNotification($ms_userID, $status)
	{
		
		/*$query = $this->db_adepts->query("select max(id) as id from adepts_forgetPassNotification where childUserID = $ms_userID and status = 0");

		$result = $query->result_array();
		if($result[0]['id'] != NULL)
		{*/
			$query = $this->db_adepts->query("update adepts_forgetPassNotification set status = $status where childUserID = $ms_userID and status = 0");		

		//}
	}

    
	/**
		 Common User functions
	**/
	public function getCommonUserDetails($userID,$information_required=array())
    {
        $field = $this->getUserFields($information_required);
        if($field)
        {
            $information_required = $field;
        }
        else
        {
            return 0;
        }
        
        $q=$this->db_educat->query("select $information_required from $this->table_common_user_details where MSE_userID = '$userID'");
        if($q->num_rows() > 0)
        {
        	return $q->row_array();
        }
        else
        {
            return 0;
        }
    }
    /**
 	This is to decouple the column names from the field names 
 	**/
    public function getUserFields($fields) {

        $information_required = "";
        $array_maped_result = array();
        if(!empty($fields)) {
            for ($i=0; $i<count($fields); $i++) { 
                if($this->getColumnNameByFieldName($fields[$i])) {
                    $array_maped_result[] = $this->getColumnNameByFieldName($fields[$i]);             
                }
            }
            if(!empty($array_maped_result)) {
                
                return implode(',', $array_maped_result);               
            }            
        }
        return 0;
    }
    public function validOldPassword($userID, $password)
    {
    	$query=$this->db_educat->query("select * from $this->table_common_user_details where password=PASSWORD('".$password."') and MSE_userID = $userID");

    	if($query->num_rows() > 0)
    		return true;
    	else
    		return false;
    }
    public function updateChildDOBInCommon($userID, $dob)
    {
    	$query = $this->db_educat->query("update $this->table_common_user_details set dob = '$dob' where MSE_userID = $userID");
    }
    public function updateProfilePic($userID,$pictureID)
    {
    	$this->dbEnglish->set('picture' , $pictureID);
    	$this->dbEnglish->where('userID' , $userID);
    	$this->dbEnglish->update('userDetails');
    }
    public function updateAddressInCommonUser($userID, $address)
    {
    	$this->db_educat->set('address',$address);
    	$this->db_educat->where('MSE_userID' , $userID);
    	$this->db_educat->update($this->table_common_user_details);
    }
    public function updatePhoneInCommonUser($userID, $phone)
    {
    	$this->db_educat->set('contactno_res',$phone);
    	$this->db_educat->where('MSE_userID' , $userID);
    	$this->db_educat->update($this->table_common_user_details);
    }
    public function updateEmailInCommonUser($userID, $email)
    {
    	$this->db_educat->set('childEmail',$email);
    	$this->db_educat->where('MSE_userID' , $userID);
    	$this->db_educat->update($this->table_common_user_details);
    }
    public function updatePersonalInfo($userID, $personalInfo)
    {
    	$this->dbEnglish->set('comment' , $personalInfo);
    	$this->dbEnglish->where('userID' , $userID);
    	$this->dbEnglish->update('userDetails');
    }
    public function updateSecretQuestion($userID, $secretQues, $secretAns)
    {
    	$query = $this->db_educat->query("update $this->table_common_user_details set secretQues  = '$secretQues', secretAns = '$secretAns' where MSE_userID = $userID");
    }

    public function getMSUserId($userID)
    {
    	$this->db_educat->select('MS_userID');
    	$this->db_educat->from($this->table_common_user_details);
    	$this->db_educat->where('MSE_userID',$userID);

    	$result = $this->db_educat->get();
    	$result_array = $result->result_array();
		return $result_array[0]['MS_userID'];	
    }
    /* ***** */
	/**
	Function to change password
	**/
	public function changePassword($userID,$password)
	{
		//$query = $this->db->query("update $this->table_userdetails set password  = password('$password') where userID = $userID");
	    $query = $this->db_educat->query("update $this->table_common_user_details set password  = PASSWORD('$password') where MSE_userID = $userID");
	}
	   
	/** 
	Check the subscription of the user
	**/
	public function isSubscribedStudent($userID)
	{
	    $information_required = array('schoolCode','child_class','MSE_endDate','MSE_startDate','MSE_enabled','category','subcategory','is_block');
	    $todays_date = date("Y-m-d");
	    $return_array = array();
	    $get_user_data = $this->getCommonUserDetails($userID,$information_required);
	    if($get_user_data != '')
	    {
	        extract($get_user_data);
	        $student_type = $this->getStudentType($userID);
	        
	        $status = 1;

	        /**
				Enabled = 0 --> Product not taken
				Enabled = 1 --> Product taken
				Enabled = 3 --> Hide Student
			*/
	        if( ($MSE_endDate != '' && $MSE_endDate < $todays_date) || ($MSE_endDate != '' && $MSE_endDate < $todays_date) )
	        {
            	/**
				Subscription is over.
				*/
	            $status = 2;
	        }
	        else if( ($MSE_enabled == 0 && $MSE_endDate!="" && $MSE_endDate > $todays_date && strcasecmp($subcategory,"School")==0) || ($MSE_enabled == 3 && strcasecmp($subcategory,"School")==0) )
	        {
	        	/**
	    		Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.
		    	*/
	            $status = 3;
	        }
	        else if( strcasecmp($category,"STUDENT")==0 && strcasecmp($subcategory,"School")==0 && $MSE_startDate > $todays_date )
	        {
	        	/**
	    		Package not yet started for school users. 
	    		Username and password incorrect message is displayed.
		    	*/
	            $status = 0;
	        } 
	        else if ( $MSE_enabled == 1 )
	        {
	        	// Block Schools logic to be written here
	        	 $pilot_schools = array(123);
	        	// && in_array($this->schoolCode, $pilot_schools) -- Add this condition to block the pilot schools.
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

	        
	        
	        return $status;


	        /*if(!$this->is_user_blocked($userID))
	        {
	            if(strcasecmp($student_type, 'RETAIL')==0)
	            {
	                if($this->check_subscription_expire_retail($end_date,$todays_date))
	                {
	                    $return_array['status'] = 2;
	                    $return_array['message'] = 'Your subscription has been expired';
	                }
	                else
	                {
	                    $return_array['status'] = 1;
	                    $return_array['message'] = 'subscription is active';
	                }
	            }
	            else
	            {
	                if($this->check_subscription_expire_for_school($enabled,$end_date,$todays_date,$subcategory))
	                {
	                    $return_array['status'] = 2;
	                    $return_array['message'] = 'Your subscription has been expired';
	                }
	                else
	                {
	                    $return_array['status'] = 1;
	                    $return_array['message'] = 'Your subscription is active';
	                }

	            }

	        }
	        else
	        {
	            $return_array['status'] = 3;
	            $return_array['message'] = 'Your account is temporarily deactivated.<br/>Please contact your ' . (strcasecmp($student_type, 'RETAIL')==0)? 'Mindspark customer care': 'School/Mindspark customer care' . 'for more information';
	        }
	        */
	    }
	    else
	    {
	        return 0;
	    }
	}
	public function update_user_information($post_data,$userID)
	{
	    extract($post_data);
	    $update_array = array('childDob' => $dob,
	                          'gender' => $gender,
	                          'childEmail' => $email,
	                          'city' => $city,
	                          'parentName' => $father_name.','.$mother_name,
	                          'parentEmail' => $father_email.','.$mother_email,
	                          'secondaryParentEmail' => $mother_email,
	                          'contactno_res' => $residence_phone,
	                          'contactno_cel' => $father_mobile.','.$mother_mobile
	                          );
	    $this->db->update($this->table_userdetails, $update_array,array('userID'=>$userID));
	    return 1;
	}
	public function check_subscription_expire_retail($end_date,$todays_date)
	{
	   
	    if($end_date!='' && strtotime($end_date) < $todays_date)
	        {   
	               
	            return 1;
	        }
	        else
	        {
	            return 0;
	        }
	}
	public function check_subscription_expire_for_school($enabled,$end_date,$todays_date,$sub_category)
	{
	    $subscription_status = 0;
	    if(strcasecmp($sub_category, "School") == 0) {
	        if($enabled == 3) {
	            $subscription_status = 1;
	        } elseif(($enabled == 0) && ($end_date != "") && (strtotime($end_date) > $todays_date)) {
	            $subscription_status = 1;
	        }
	    }

	    return $subscription_status;
	}

	function is_user_blocked($userID)
	{      
	        $this->db_educat = $this->load->database('database_educat',TRUE);
	        $query=$this->db_educat->query("select * from $this->table_common_user_details where MS_userID = $userID");

	        if($query->num_rows() > 0)
	        {
	            $d= $query->row_array();
	            return $d['is_block'];
	        }
	        else
	        {
	            return 0;
	        }
	}

    public function is_offline_student($userID)
    {
           // to do 
    }
    public function remaining_time_of_the_day($userID)
    {
           // to do  
           // this is temporary   
           return '30';       
    }
    public function last_session_avaliable($userID)
    {
           // to do 
          // have to change
            return '0';
    }
    public function comment_message_count($userID)
    {
        $this->db->select('count(srno)');
        $query = $this->db->get_where($this->table_adepts_user_comments,array('userID' => $userID,'viewed' => '0','status' => 'Closed'));
        if($query->num_rows() > 0)
        {
            $data_row = $query->row_array();
            return $data_row['count(srno)']; 
        }
        else
        {
            return 0;
        }
    }

    public function notice_board_message_count($userID,$school_code,$child_class,$child_section)
    {
        $query = "SELECT count(srno) as count_message_notice_board FROM $this->table_notice_board_comments WHERE subjectno = $this->subject_code AND schoolCode=$school_code AND (class='$child_class' OR class='All')";
        if($child_section!="")
            $query .= " AND (section='$child_class' OR section='All')";
        $query .= " AND datediff(curdate(),date) < noOfDays ORDER BY srno";
        
        $result = $this->db->query($query);
        if($result->num_rows() > 0)
        {
            $data = $result->row_array();
            return $data['count_message_notice_board'];
        }
        else
        {
            return 0;
        }
    }
    /**
    	@EI_start : Sparkie Logic functions  -- Author Rochak
    */
    public function getSparkiesCount($userID)
    {
        $sparkie_prev = $this->getSparkiePrev($userID);
        $sparkie_today = $this->getTodaysSparkie($userID);
        return $sparkie_prev + $sparkie_today;
    }
    public function getSparkiePrev($userID)
    {
            $sq =   "SELECT sparkies FROM $this->table_rewardPoints WHERE userID=$userID";
            $rs =   $this->db->query($sq);
            if($rs->num_rows() > 0)
            {
                return $rs->row()->sparkies;
            }
            else
            {
                return 0;
            }
    }
    public function getTodaysSparkie($userID='')
    {
        
        $sq =   "SELECT SUM(noOfJumps) as sparkie FROM $this->table_sessionStatus WHERE userID= $userID";
        $rs =   $this->db->query($sq);
        if($rs->num_rows() > 0)
        {
            return $rs->row()->sparkie;
        }
        else
        {   
            return 0;
        }
        
    
    }
    public function updateSpakieCount($userID, $sparkieCount, $class)
    {
    	$whereArray = array(
    			'userID' => $userID
    		);
    	
    	$this->dbEnglish->select('sparkies');
    	$this->dbEnglish->from('rewardPoints');
    	$this->dbEnglish->where($whereArray);

    	$sparkie_object = $this->dbEnglish->get();
    	$sparkie_array = $sparkie_object->result_array();

    	if(count($sparkie_array) > 0)
    	{
    		$sparkieCount = $sparkieCount + $sparkie_array[0]['sparkies'];

	    	$updateArray = array(
	    			'sparkies' => $sparkieCount
	    		);

	    	$this->dbEnglish->where($whereArray);

	    	$this->dbEnglish->update('rewardPoints',$updateArray);
	    	
   		}
   		else
   		{
			$insert_array = array(
					'userID' => $userID,
					'startDate' => date('Y-m-d'),
					'sparkies' => $sparkieCount,
					'lastModified' => 'NOW()',
					'class' => $class
				);
			$this->dbEnglish->insert('rewardPoints', $insert_array);
		
		}
		return $sparkieCount;
    	
    }
    /**
    	@EI_start : Sparkie Logic functions 
    */
	//Added by Jayanth: begin

    public function get_child_class($userID) {
        $this->getCommonUserDetails($userID, array('child_class'));
    }

    /**
     * @param $userID
     * @return string 
     * Returns either "SCHOOL", "GUEST", "RETAIL" depending on the type of the student. Returns "NA" if user is not student
    **/
    public function getStudentType($userID) {
        $studentType = "";
        $studentTypeArr = $this->getCommonUserDetails($userID, array('category','subcategory')); 
        if(strcasecmp($studentTypeArr['category'], "STUDENT") == 0) {
            if(strcasecmp($studentTypeArr['subcategory'], "School") == 0) {
                $studentType = "SCHOOL";

            } elseif (strcasecmp($studentTypeArr['subcategory'], "Individual") == 0) {
                $studentType = "RETAIL";

            }
              elseif (strcasecmp($studentTypeArr['subcategory'], "Home Center") == 0) {
                $studentType = "HOME_CENTER";

            } else {
                $studentType = "NA";
            }

        } elseif($studentTypeArr['category'] == "GUEST") {
            $studentType = "GUEST";
        } else {
            $studentType = "NA";
        }

        return $studentType;
    }

	public function getPackageType($userID)
	{
	    $information_required = array('comment');
	    $user_data = $this->getCommonUserDetails($userID,$information_required);
	    if($user_data)
	    {
	        if($user_data['comment'] == 'MS_DEC')
	            return 'MS_DEC';
	        else
	            return 'All';
	    }
	    else
	    {
	            return 'All';
	    }
	}
	
	//Added by Jayanth: end

	private function getColumnNameByFieldName($column_name) {

        $array_mapping_field_column_name = array(
                                                'user_id' => 'MSE_userID as user_id',
                                                'child_name' => 'Name as child_name',
                                                'first_name' => 'first_name',
                                                'last_name' => 'last_name',
                                                'user_name' => 'username as user_name' ,
                                                'password' => 'password',
                                                'child_class' => 'class as child_class',
                                                'section' => 'section',
                                                'dob' => 'dob',
                                                'roll_no' => 'rollNo as roll_no',
                                                'school_code' => 'schoolCode as school_code',
                                                'school_name' => 'schoolName as school_name',
                                                'child_gender' => 'IFNULL(gender,"") as child_gender',
                                                'pan_number' => 'pan_number',
                                                'child_email' => 'childEmail as child_email',
                                                'parent_name' => 'parentName as parent_name',
                                                'additional_email' => 'additionalEmail as additional_email',
                                                'category' => 'category',
                                                'subcategory' => 'IFNULL(subcategory,"") as subcategory',
                                                'secret_question' => 'secretQues as secret_question',
                                                'secret_answer' => 'secretAns as secret_answer',
                                                'address' => 'address',
                                                'city' => 'city',
                                                'state' => 'state' ,
                                                'country' => 'country',
                                                'pincode' => 'pincode',
                                                'contact_no_res' => 'IFNULL(contactno_res,"") as contact_no_res',
                                                'contact_no_cell' => 'IFNULL(contactno_cel,"") as contact_no_cell',
                                                'MSE_enabled' => 'MSE_enabled',
                                                'MSE_activationdate' => 'MSE_activationdate',
                                                'MSE_deactivationdate' => 'MSE_deactivationdate',
                                                'exempt_unused_mse' => 'exempt_unused_mse',
                                                'MSE_startDate' => 'MSE_startDate',
                                                'MSE_endDate' => 'MSE_endDate',
                                                'MSE_registrationDate' => 'MSE_registrationDate',
                                                'is_block' => 'is_block',
                                                'class_upgrade_date' => 'classUpgradeDate as class_upgrade_date',
                                                'upgrade_month' => 'upgrade_month',
                                                'type' => 'type',
                                                'profile_picture' => 'profilePicture as profile_picture',
                                                'class_change_history' => 'classChangeHistory as class_change_history',
                                                'created_dt' => 'created_dt',
                                                'created_by' => 'created_by',
                                                'updated_dt' => 'updated_dt',
                                                'updated_by' => 'updated_by',
                                                'temp_year' => 'temp_year',
                                                'client_userid' => 'client_userid',
                                                'lastModified' => 'lastModified'
                                            );







                                             /*   'buddy_id' => 'buddyID as buddy_id' ,
                                                'child_name' => 'childName as child_name',
                                                'child_class' => 'childClass as child_class' ,
                                                'child_upgrade_date' => 'classUpgradeDate as child_upgrade_date',
                                                'upgrade_month' => 'upgrade_month',
                                                'child_section' => 'childSection as child_section' ,
                                                'child_dob' => 'childDob as child_dob',
                                                'pan_number' => 'pan_number',
                                                'school_code' => 'schoolCode as school_code',
                                                'child_email' => 'IFNULL(childEmail,"") as child_email' ,
                                                'parent_name' => 'parentName as parent_name',
                                                'parent_email' => 'parentEmail as parent_email',
                                                'secondary_parent_email' => 'IFNULL(secondaryParentEmail,"") as secondary_parent_email',
                                                'start_date' => 'startDate as start_date',
                                                'end_date' => 'endDate as end_date',
                                                'time_allowed_day' => 'timeAllowedPerDay as time_allowed_day',
                                                'school_name' => 'schoolName as school_name',
                                                'order_id' => 'orderID as order_id',
                                                'amount' => 'amount',
                                                'package' => 'package',
                                                'discount' => 'discount',
                                                'type' => 'type',
                                                'payment_mode' => 'paymentMode as payment_mode',
                                                'bank_name' => 'bankName as bank_name',
                                                'chequeno' => 'chequeno',
                                                'heard_from' => 'heardFrom as heard_from',
                                                'registration_date' => 'registrationDate as registration_date',
                                                'verified' => 'verified',
                                                'reference_code' => 'referenceCode as reference_code',
                                                'comment' => 'comment',
                                                'call_date' => 'callDate as call_date',
                                                'call_time' => 'callTime as call_time',
                                                'updated_by' => 'updated_by',
                                                'class_change_history' => 'classChangeHistory as class_change_history',
                                                'call_time' => 'callTime as call_time',
                                                'class_change_history' => 'classChangeHistory as class_change_history',
                                                'enabled' => 'enabled',
                                                'deactivation_history' => 'deactivationHistory as deactivation_history',
                                                'subjects' => 'subjects',
                                                'picture'  => 'picture',
                                                'theme' => 'theme',
                                                'board' => 'board',
                                                'home_school'=>'homeSchool as home_school',
                                                'profile_picture'=> 'IFNULL(profilePicture,"") as profile_picture',
                                                'last_modified' => 'lastModified as last_modified'
                                                );*/

        if(array_key_exists($column_name,$array_mapping_field_column_name))
        {
            return $array_mapping_field_column_name[$column_name];
        }
        else
        {
            return 0;
        }
    } 

    function updateOSIPDetails($userID, $sessionID, $IP, $OsDetails, $browserDetails)
    {
        $data = array(
            'userID' => $userID,
            'sessionID' => $sessionID
        );
        
        //$update_data = 'Browser:'.$browserDetails.' '.'IP:'.$IP.' '.'OS:'.$OsDetails;
        $update_data = $browserDetails.' '.'OS:'.$OsDetails;

        $this->dbEnglish->set('browser', "'$update_data'", FALSE);
        $this->dbEnglish->set('ipaddress', "'$IP'", FALSE);
        $this->dbEnglish->where($data);
        $this->dbEnglish->update('sessionStatus');
    }


} 
