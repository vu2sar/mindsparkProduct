<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller {

	public $user_id 				= '';
	public $session_id 				= '';
	public $user_name 				= '';
	public $child_name				= '';
	public $ms_level				= '';
	public $lang_level				= '';
	public $teacher_class			= '';
	public $child_class				= '';
	public $child_section			= '';
	public $skill_id 				= '';
	public $sub_skill_id			= '';
	public $school_code				= '';
	public $timeAllowedPerDay		= '';
	public $first_criteria			= 0;
	public $second_criteria			= 0;
	public $authorize			    = '';
	protected $app;
	public function MY_Controller()
	{
		parent::__construct();
		
		$this->load->helper('url');
		
		// Comment this code before sync
		if($this->session->userdata('logged_in') != 1 )
		{
			if (!$this->input->is_ajax_request()) {
			   redirect($this->config->item('login_url'));
			}
			else{
				echo $this->return_response(null, '', INVALID_SESSION);
				exit;
			}
		}

		
		// Get the session variables.

		$this->user_id 			= $this->getUserId();
		$this->session_id 		= $this->getSessionId();
		$this->user_name 		= $this->getUserName();
		$this->picture 			= $this->getProfilePic();
		$this->child_name		= $this->getChildName();
		$this->ms_level			= $this->getMsLevel();
		$this->lang_level		= $this->getLangLevel();
		$this->teacher_class	= $this->getTeacherClass();
		$this->child_class		= $this->getChildClass();
		$this->child_section	= $this->getChildSection();
		$this->skill_id			= $this->getSkillID();
		$this->sub_skill_id		= $this->getSubSkillID();
		$this->timeAllowedPerDay= $this->gettimeAllowedPerDay();
		$this->minTimeForClass  = $this->getMinTimeForClass();
		$this->school_code		= $this->getSchoolCode();
		$this->school_name		= $this->getSchoolName();
		$this->category			= $this->getCategory();
		$this->sub_category		= $this->getSubCategory();
		$this->first_criteria	= $this->getFirstSparkieCriteria();
		$this->second_criteria	= $this->getSecondSparkieCriteria();
		$this->authorize		= $this->getAuthorization();
		$this->address 			= $this->getAddress();
		$this->phone 			= $this->getPhoneNo();
		$this->child_email 		= $this->getEmail();
		$this->childDOB 		= $this->getChildDOB();
		$this->phone 			= $this->getPhoneNo();
		$this->parent_contact   = $this->getParentContact();
		$this->personal_info    = $this->getPersonalInfo();
		$this->essay_review_count    = $this->getEssayReviewCount();
		$this->secret_ques    	= $this->getSecretQues();
		$this->secret_ans    	= $this->getSecretAns();
		$this->parentEmail    	= $this->getParentEmail();
		

		// Get the header of the ajax calls to check the valid request from the user.
		// Can also check to restrict multi tab issue. For the user.

		/*$headers = array();
		$headers = $this->input->request_headers(FALSE);

		$checkAuthorization = false;
		$valid_user = false;
		foreach ($headers as $key => $value) {
			if($key == 'Authorization')
			{
				if($value != '')
				{
					$checkAuthorization = true;
					if($value == $this->authorize)
					{
						$valid_user = true;
					}
				}
			}
		}

		if(!$valid_user && $checkAuthorization)
		{
			echo $this->return_response(null, 'multitab', SUCCESS);
			exit;
		}*/
		// **************************** //


		if($this->category == "TEACHER")
			$this->setTeacherPendingEssayCount();
	}

	public function return_response($return_data=null, $msg = '', $status = SUCCESS)
	{
		$this->load->model('Language/login_model');

		$return_format = array();
		if($this->session->userdata('preview') == "on")
			$return_format['active'] = 'true';
		else{
			if( $this->login_model->isUserActive( $this->user_id, $this->session_id) )
			{
				$return_format['active'] = 'true';
			}
			else
			{
				$return_format['active'] = 'false';
			}
		}	

		$return_format['msg'] = $msg;
		$return_format['status'] = $status;
		$return_format['result_data'] = $return_data;

		return json_encode($return_format);
	}

	public function getUserId()
	{
		return $this->session->userdata('userID');
	}
	public function getProfilePic()
	{
		return $this->session->userdata('picture');
	}
	public function getSessionId()
	{
		return $this->session->userdata('sessionID');
	}
	public function getUserName()
	{
		return $this->session->userdata('userName');
	}
	public function getChildName()
	{
		return $this->session->userdata('childName');
	}
	public function getMsLevel()
	{
		return $this->session->userdata('msLevel');
	}
	public function getLangLevel()
	{
		return $this->session->userdata('childClass') - 3;
	}
	public function getTeacherClass()
	{
		return $this->session->userdata('teacherClass');
	}
	public function getChildClass()
	{
		return $this->session->userdata('childClass');
	}
	public function getChildSection()
	{
		return $this->session->userdata('childSection');
	}
	public function getChildDOB()
	{
		return $this->session->userdata('childDob');
	}
	public function getSkillID()
	{
		return $this->session->userdata('skillID');
	}
	public function setSkillID($skillId)
	{
		$this->session->set_userdata('skillID',$skillId);
		$this->skill_id = $skillId;
	}
	public function getSubSkillID()
	{
		return $this->session->userdata('subSkillID');
	}
	public function setSubSkillID($subSkillID)
	{
		$this->session->set_userdata('subSkillID',$subSkillID);
		$this->sub_skill_id = $subSkillID;
	}
	public function setChildDOB($dob)
	{
		$this->session->set_userdata('childDob',$dob);

	}
	public function getSchoolCode()
	{
		return $this->session->userdata('schoolCode');
	}
	public function getSecretQues()
	{
		return $this->session->userdata('secretQues');
	}
	public function getSecretAns()
	{
		return $this->session->userdata('secretAns');
	}
	public function getSchoolName()
	{
		return $this->session->userdata('schoolName');
	}
	public function gettimeAllowedPerDay()
	{
		return $this->session->userdata('timeAllowedPerDay');
	}
	public function getEssayReviewCount()
	{
		return $this->session->userdata('totalEssayPendingCnt');
	}
	public function getMinTimeForClass()
	{
		return $this->session->userdata('minTimeForClass');
	}
	public function getCategory()
	{
		return $this->session->userdata('category');
	}
	public function getSubCategory()
	{
		return $this->session->userdata('subcategory');
	}
	public function getFirstSparkieCriteria()
	{
		if($this->session->userdata('criteria1')==null || $this->session->userdata('criteria1') == '')
			$this->session->set_userdata('criteria1',0);

		return $this->session->userdata('criteria1');
	}
	public function getSecondSparkieCriteria()
	{
		if($this->session->userdata('criteria2')==null || $this->session->userdata('criteria2') == '')
			$this->session->set_userdata('criteria2',0);
		
		return $this->session->userdata('criteria2');
	}
	public function setSparkieLogicVariables()
	{
		$this->session->set_userdata('criteria1',$this->first_criteria);
		$this->session->set_userdata('criteria2',$this->second_criteria);
	}
	public function getAuthorization()
	{
		return $this->session->userdata('authorize');
	}
	public function getAddress()
	{
		return $this->session->userdata('address');
	}
	public function getPersonalInfo()
	{
		return $this->session->userdata('comment');
	}
	public function getPhoneNo()
	{
		return $this->session->userdata('contactno_res');
	}
	public function getParentContact()
	{
		return $this->session->userdata('contactno_cel');
	}
	public function getParentEmail()
	{
		return $this->session->userdata('parentEmail');
	}
	public function getEmail()
	{
		return $this->session->userdata('childEmail');
	}
	public function setProfilePic($picture)
	{
		$this->session->set_userdata('picture',$picture);
	}
	public function setAddress($address)
	{
		$this->session->set_userdata('address',$address);
	}
	public function setPhoneNo($phone)
	{
		$this->session->set_userdata('contactno_res',$phone);
	}
	public function setEmail($email)
	{
		$this->session->set_userdata('childEmail',$email);
	}
	public function setPersonalInfo($info)
	{
		$this->session->set_userdata('comment',$info);
	}
	public function setSecretQues($question)
	{
		return $this->session->set_userdata('secretQues',$question);
	}
	public function setSecretAns($answer)
	{
		return $this->session->set_userdata('secretAns',$answer);
	}
	/**
	 * function role : Save user content rating information for passages attempted
	 * param1 : contentID
	 * param2 : for what content the rating is given eg passage,question etc
	 * param3 : rating given by the user 
	 * param4 : comment given by the user 
	 * param5 : if the rating is given either 1 or 2 then the reason that rating
	 * @return  none
	 * 
	 * */
	function saveUserContentRating($contentID,$contentType,$rating,$comment,$ratingReasonOther)
	{
		$this->load->model('Language/rating_model');
		$this->rating_model->saveUserContentRating($this->user_id,$contentID,$contentType,$rating,$comment,$ratingReasonOther);
	}
	
	function setTeacherPendingEssayCount()
	{
		$this->load->model('Language/home_model');
		$teacherPendingEssayCount=$this->home_model->setTeacherPendingEssayCount($this->user_id);
		$this->session->set_userdata('totalEssayPendingCnt',$teacherPendingEssayCount);
	}

	function getIsJunkData($data)
	{
		$this->load->model('Language/questionspage_model');

		$isJunk=$this->questionspage_model->isitjunk($data);
		
		return $isJunk;
	}

	function getProfanityData($data)
	{
		$this->load->model('Language/questionspage_model');
		$isProfanity = $this->questionspage_model->isItProfanity($data);
		return $isProfanity;
	}

	function index()
	{
		
	}

	function print_array($arr)
	{
		echo "<pre>";
			print_r($arr);
		echo "</pre>";
	}
	
	
}
