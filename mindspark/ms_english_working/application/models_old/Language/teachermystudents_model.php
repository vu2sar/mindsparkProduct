<?php

Class teachermystudents_model extends CI_model
{

	public function __construct() 
	{
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	}

	/**
	 * function description : Get user information
	 * param1   userName
	 * @return  array, user information 
	 * 
	 * */
	public function getUserData($data){

		$section_array = array();
		
		$this->dbEnglish->Select('userID, userName, childName, childClass, childSection, childDob, childEmail, parentEmail, password');
		$this->dbEnglish->from('userDetails');
		
		if($data['class'] != '')
			$this->dbEnglish->where('childClass',$data['class']);
		if($data['section'] != '' && $data['section'] != 'all')
			$this->dbEnglish->where('childSection',$data['section']);
		if($data['childName'] != '')
			$this->dbEnglish->like('childName', $data['childName'], 'both'); 

		if($data['section'] == 'all' && strtolower($this->category)=='teacher')
		{
			$array = explode('~', $this->session->userdata('teacherClass'));
			foreach ($array as $key) 
			{
				$onlysection = explode(',', $key);
				
				if($onlysection[0] == $data['class'])
					array_push($section_array, $onlysection[1]);
			}
			$this->dbEnglish->where_in('childSection',array_unique($section_array));				
		}

		if($data['class'] == '' && $data['section'] == '' && strtolower($this->category)=='teacher')
		{
			$array = explode('~', $this->session->userdata('teacherClass'));
			$where = "(" ;
			foreach($array as &$value){	
				$value = explode(',', $value);
				$where .= "(childClass='$value[0]' AND childSection='$value[1]') OR ";		
			}	

			$this->dbEnglish->where(trim(substr($where,0,-3)).")");
		}
		$this->dbEnglish->where('schoolcode',$data['school_code']);
		$this->dbEnglish->where('category', 'STUDENT');
		$this->dbEnglish->where('enabled', '1');
		$query = $this->dbEnglish->get();
		$userInfoArr = $query->result_array();
		//echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
		return $userInfoArr;
		
		/*foreach($userInfoArr[0] as $key => $value)
			$this->$key = $value;

		return $userInfoArr[0];*/
	}

	/**
	 * function role : Changing time format
	 * param1 : time
	 * @return  time
	 * 
	 **/
	function convertToTime($time)
	{
		$hr   = substr($time,11,2);
	    $mm   = substr($time,14,2);
	    $ss   = substr($time,17,2);
	    $day  = substr($time,8,2);
	    $mnth = substr($time,5,2);
	    $yr   = substr($time,0,4);
	    $time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
	    return $time;
	}

	public function updateUserDetails($data)
	{
		$this->dbCommonUser = $this->load->database('database_educat',TRUE);

		if($data['DOB'] == "N.A." || $data['DOB']=="")
		{
			$data['DOB'] = "0000-00-00";
		}
		else
		{
			$data['DOB'] = date("Y-m-d",strtotime($data['DOB']));
		}
		
		$nameArray = explode(' ',$data['childName']);
        $firstName = trim($nameArray[0]);
        $lastName = '';
        if(count($nameArray)>1)
            $lastName = trim($nameArray[1]);

		$query = "UPDATE educatio_educat.common_user_details SET username = '".trim($data['userName'])."',first_name = '$firstName', last_name='$lastName', Name='".$data['childName']."', childEmail = '".trim($data['childEmail'])."', dob = '".$data['DOB']."', additionalEmail = '".trim($data['parentEmail'])."', section = '".$data['childSection']."', updated_by='".$_SESSION['username']."'";
			if($data['password']==1) //implies reset the pwd to username
				$query .= ", password=password('".trim($data['userName'])."')";
			elseif($data['password']==2) //implies reset the pwd to blank
				$query .= ", password=''";
		$query .= " WHERE MSE_userID = '".$data['userID']."'";
		//echo "<pre>";print_r($query);echo "</pre>";
		$this->dbCommonUser->query($query);
	}

	public function checkUserDetails($data)
	{
		if(trim($data['userName']) != '')
		{
			$this->dbCommonUser = $this->load->database('database_educat',TRUE);	
			$query = "SELECT * from educatio_educat.common_user_details WHERE username = '".trim($data['userName'])."' AND MSE_userID != '".$data['userID']."'";
			$check = $this->dbCommonUser->query($query);
			return $check->result_array();
		}
	}

	function getGroupSkillMaster()
	{
		$this->dbEnglish->Select('distinct(name),groupSkillID');
		$this->dbEnglish->from('groupSkillIDMaster');
		$getGroupSkillsSql = $this->dbEnglish->get();
		$groupSkillData = $getGroupSkillsSql->result_array();
		return $groupSkillData;
	}

	function saveCurrentStatusSkill($data)
	{
		$this->dbEnglish->Select('a.userID');
		$this->dbEnglish->from('userCurrentStatus a');
		$this->dbEnglish->join('userDetails b','a.userID=b.userID');
		$this->dbEnglish->where('b.schoolcode', $data['school_code']);
		$this->dbEnglish->where('b.category', 'STUDENT');
		$this->dbEnglish->where('b.enabled', '1');
		$this->dbEnglish->where('b.childClass', $data['grade']);
		$this->dbEnglish->where('b.childSection', $data['section']);
		$query = $this->dbEnglish->get();
		$get_rows =  $query->result_array(); 
		$get_row_count_rows = count($get_rows);

		if($get_row_count_rows > 0)
		{
			foreach ($get_rows as $key) 
			{
				$query = "UPDATE userCurrentStatus SET startSkillDate = '".date("Y-m-d", strtotime($data['start_date']))."', endSkillDate='".date("Y-m-d", strtotime($data['end_date']))."', groupSkillID = '".$data['groupSkillID']."'";
				$query .= " WHERE userID = '".$key['userID']."'";
				$this->dbEnglish->query($query);				
			}
		}
	}
	
	function getAdminClassArr($schoolCode)
	{
		$admin_classes_arr=array();
		$sql="select distinct(childClass) as adminClasses from userDetails where schoolCode=".$schoolCode." and enabled=1 and category='Student'";		
		//print $sql;
		$getAdminSchoolClassesSql = $this->dbEnglish->query($sql);
		$resultArr = $getAdminSchoolClassesSql->result_array();
		
		foreach($resultArr as $key=>$val)
		{
			$class=$val['adminClasses'];
			array_push($admin_classes_arr,$class);
		}
		
		return $admin_classes_arr;
	}
	
	function getAdminSectionArr($selectedClass,$schoolCode)
	{
		$admin_section_arr=array();
		$sql="select distinct(childSection) as classSections from userDetails where schoolCode=".$schoolCode." and childClass=".$selectedClass." and enabled=1 and category='Student'";		
		//print $sql;
		$getAdminSchoolSectionsSql = $this->dbEnglish->query($sql);
		$resultArr = $getAdminSchoolSectionsSql->result_array();
		
		foreach($resultArr as $key=>$val)
		{
			$class=$val['classSections'];
			array_push($admin_section_arr,$class);
		}
		
		return $admin_section_arr;
	}
	

	function saveChangeReason($data)
	{
		if(!empty($data) && count($data) > 0)
		{
			$data_insert = array(
			   'userID'       => $data['userID'],
			   'changeReason' => $data['changeReason']
			);

			$this->dbEnglish->insert('userDetailChangeReason', $data_insert);
		}
	}

	/**
	 * function description : Get teacher information
	 * param1   
	 * @return  array, user information 
	 * 
	 * */
	public function getTeacherData($userID,$school_code){

		$sql="select ud.userID, ud.childClass, ud.childSection,ud.schoolCode, ud.childName, CONCAT(tm.childClass,tm.childSection) as class, GROUP_CONCAT(CONCAT(tm.childClass,tm.childSection) SEPARATOR ', ') as combinedClass,GROUP_CONCAT(CONCAT(tm.childClass,'-',tm.childSection) SEPARATOR ', ') as combinedClass1,ud.userName,CONCAT(ud.childClass,ud.childSection) as primaryClass, ud.category from userDetails ud LEFT JOIN teacherClassMapping tm  on ud.userID = tm.userID where (ud.category = 'TEACHER' OR ud.category = 'ADMIN' OR ud.category = 'School Admin') and ud.enabled=1 and ud.userID != $userID and ud.schoolCode = $school_code  group by ud.userID order by ud.childName ASC";		
		
		$query = $this->dbEnglish->query($sql);
		$teacherInfoArr = $query->result_array();

		$sql1 = "select userID,GROUP_CONCAT(CONCAT(childClass,childSection) SEPARATOR ', ') as actualPrimaryClass  from teacherClassMapping where isPrimaryClass = '1' and schoolcode = $school_code group by userID";
		$query1 = $this->dbEnglish->query($sql1);
		$teacherInfoArr1 = $query1->result_array();

		$allClasses = array();
		foreach ($teacherInfoArr as $key => $row) 
		{
			array_push($allClasses, $row['class']);
			
			foreach ($teacherInfoArr1 as $key2 => $row2) {
	            if ($row['userID'] == $row2['userID']) 
	            {
	            	$teacherInfoArr[$key]['actualPrimaryClass'] = $row2['actualPrimaryClass'];
	            }
	        }
		}

		$sql2 = "select DISTINCT(CONCAT(childClass,childSection)) as class from teacherClassMapping where schoolCode = $school_code ";
		$query3 = $this->dbEnglish->query($sql2);
		$classesArr = $query3->result_array();
		
		$classesArrArr = array();
		foreach ($classesArr as $key => $value) 
		{
			array_push($classesArrArr, $value['class']);
		}
		$allClass = implode(',', array_unique($classesArrArr));
		foreach ($teacherInfoArr as $key => $value) {
			$teacherInfoArr[$key]['allClasses'] = $allClass;
		}
		//echo "<pre>";print_r($teacherInfoArr);echo "</pre>";
		

		return $teacherInfoArr;
	}

	function updateTeacherDetails($data)
	{
		
		$teacherIdArr = array();
		$schoolcode   = $this->school_code;

		foreach ($data as $key => $valueMain) 
		{

			$userid       = $valueMain['userID'];
			array_push($teacherIdArr,$userid);

			/*INSERT THE CLASS ASSIGNED AND ACCORDINGLY PRIMARY TEACHER*/
			if(isset($valueMain['classAssignedArr']))
			{
				$this->dbEnglish->where('userID', $userid);
				$this->dbEnglish->where('schoolCode', $schoolcode);
				$this->dbEnglish->delete('teacherClassMapping');

				foreach (array_unique($valueMain['classAssignedArr']) as $key => $value) 
				{
					$childClass = explode('-', $value);
					
					if(!empty($valueMain['primaryClass']))
					{
						if (in_array($valueMain['classAssigned'][$key], $valueMain['primaryClass']))
							$isPrimaryClass = '1';
						else
							$isPrimaryClass = '0';
					}
					else					
						$isPrimaryClass = '0';

                                        //This block will check if there are more than 1 hyphen then it will character from first hyphen and add it to child class and remaining will be imploded by hyphen in section.
                                        if(count($childClass)>2){
                                            $tempClass=$childClass; 
                                            unset($tempClass[0]);
                                            $childClass[1]= implode('-', $tempClass);
                                            $childClass[1]=ltrim($childClass[1],'-');
                                        }
					$data_insert = array(
					  	'userID'       => $userid,
					   	'childClass'   => $childClass[0],
					   	'childSection' => $childClass[1],
					   	'schoolCode'   => $schoolcode,
						'isPrimaryClass' => $isPrimaryClass					   
					);
					
					$this->dbEnglish->insert('teacherClassMapping', $data_insert);
				}
			}
		}


		/*SAVE ESSAY ALLOTMENT*/
		foreach ($data as $key => $valueMainEssay)
		{
			$userid   = $valueMainEssay['userID'];
			if(isset($valueMainEssay['primaryClass']))
			{
				foreach ($valueMainEssay['primaryClass'] as $key => $value) 
				{
					$getUserIdsql = "select e.scoreID,e.evaluatorID,e.evaluatorLog from ews_essayScoring e join userDetails ud on ud.userID = e.userID where CONCAT_WS('', ud.childClass, ud.childSection) = '$value' and ud.schoolCode=$schoolcode";	
					
					$query        = $this->dbEnglish->query($getUserIdsql);
					$userIdOf     = $query->result_array();	
					foreach ($userIdOf as $key => $essayValue) 
					{
						$dataUpdateTeacher = array('evaluatorID' => $userid);
                                                if($essayValue['evaluatorID']!=$userid){
                                                    $evaluatorLog=json_decode($essayValue['evaluatorLog'],true);                                         
                                                    if(!is_array($evaluatorLog)){
                                                        $evaluatorLog=array();
                                                    }
                                                    array_push($evaluatorLog,array('prevEvaluatorID'=>$essayValue['evaluatorID'],'updatedOn'=>date('Y-m-d H:i:s')));
                                                    $dataUpdateTeacher['evaluatorLog']=json_encode($evaluatorLog);
                                                }
						$this->dbEnglish->where('scoreID', $essayValue['scoreID']);
						$this->dbEnglish->update('ews_essayScoring', $dataUpdateTeacher);
					}

					/*UPDATE PASSWORD NOTIFICATION*/
					$getUserIdsql = "select e.id from forgetPassNotification e join userDetails ud on ud.userID = e.childUserID where CONCAT_WS('', ud.childClass, ud.childSection) = '$value' and e.status=0";	
					
					$query        = $this->dbEnglish->query($getUserIdsql);
					$userIdOf     = $query->result_array();	

					foreach ($userIdOf as $key => $essayValue) 
					{
						$dataUpdateTeacher = array('teacherUserID' => $userid);
						$this->dbEnglish->where('id', $essayValue['id']);
						$this->dbEnglish->update('forgetPassNotification', $dataUpdateTeacher);
						
					}
					/*END*/
                                        
                                        //Updateing user in Essay Master
                                        $getPrevUserIdsql = "select srno,essayID from teacherEssayActivation where CONCAT_WS('', class,section) = '$value' and schoolCode='".$schoolcode."'";	
					$getPrevUserIdsqlQuery        = $this->dbEnglish->query($getPrevUserIdsql);
					$getPrevUserIduserIdOf     = $getPrevUserIdsqlQuery->result_array();	
					foreach ($getPrevUserIduserIdOf as $key => $essayValue) 
					{
                                                $this->dbEnglish->Select("userID,essayAssignedLog");
                                                $this->dbEnglish->from('essayMaster');
                                                $this->dbEnglish->where('essayID', $essayValue['essayID']);
                                                $query = $this->dbEnglish->get();
                                                $queryArr = $query->result_array();
                                                
                                                $essayMasterUpdate=array('userID' => $userid);
                                                if($queryArr[0]['userID']!=$userid){
                                                    $essayAssignedLog=json_decode($queryArr[0]['essayAssignedLog'],true);
                                                    if(!is_array($essayAssignedLog)){
                                                        $essayAssignedLog=array();
                                                    }
                                                    array_push($essayAssignedLog,array('essayAssignedBy'=>$queryArr[0]['userID'],'updatedOn'=>date('Y-m-d H:i:s')));
                                                    $essayMasterUpdate['essayAssignedLog']=json_encode($essayAssignedLog);
                                                }
						$this->dbEnglish->where('essayID', $essayValue['essayID']);
						$this->dbEnglish->update('essayMaster', $essayMasterUpdate);
					}
                                        
				}
			}
		}
		/*END*/
	}
        /**
	 * function description : This function will get the class and section name which does not have primary teachers.
	 * param1   
	 * @return  array, user information 
	 * 
	 * */
        function getAlertForNotPrimaryTeacherAvailable($schoolCode){
            $this->dbEnglish->Select("CONCAT(childClass,childSection) as class",false);
            $this->dbEnglish->from('userDetails');
            $this->dbEnglish->where('schoolCode', $schoolCode);
            $this->dbEnglish->where('enabled', '1');
            $this->dbEnglish->where('endDate >=CURDATE()');
            $this->dbEnglish->where('category', "STUDENT");
            $this->dbEnglish->where('subcategory', "School");
            $this->dbEnglish->where('childSection !=', "");
            $this->dbEnglish->where('childClass !=', "");
            $this->dbEnglish->group_by('childClass,childSection');
            $userQuery = $this->dbEnglish->get();
            $userDetails = $userQuery->result_array();
            $simplifiedUserDetails=array();
             foreach($userDetails as $userDetail){
                    $simplifiedUserDetails[]=$userDetail['class'];
            }
            $this->dbEnglish->Select("CONCAT(childClass,childSection) as class,isPrimaryClass",false);
            $this->dbEnglish->from('teacherClassMapping');
            $this->dbEnglish->where('schoolCode', $schoolCode);
            $this->dbEnglish->where('isPrimaryClass', '1');
            $this->dbEnglish->group_by('childClass,childSection');
            $query = $this->dbEnglish->get();
            $queryArr = $query->result_array();
            $primaryClasses=array();
            sort($queryArr);
            $remainingPrimaryClasses=array();
            foreach($queryArr as $query){
                if($query['isPrimaryClass']==1){
                    $primaryClasses[]=$query['class'];
                }
            }
            foreach($simplifiedUserDetails as $simplifiedUserDetail){
                if(!in_array($simplifiedUserDetail,$primaryClasses)){
                    $remainingPrimaryClasses[]= $simplifiedUserDetail;
                }
            }
            if(empty($remainingPrimaryClasses)){
                $remainingPrimaryClasses=0;
            }
           return $remainingPrimaryClasses;
        }
        
}

?>