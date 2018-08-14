<?php
Class Login extends CI_Controller
{
	var $user_name = '';
	var $logOutReasonFlag = 0;
	function __construct(){
       
        parent::__construct();
        $this->osDetails      = '';
        $this->browserName    = '';
        $this->browserVersion = '';
    }
	//function index($user_id)
	function index($user_id, $os, $browserName, $browserVersion,$targetSchoolCode=null)
	{
            $this->load->model('Language/User_model','user_model');
            $sessionData=$this->session->userdata('userName');
		if(isset($sessionData) && !empty($sessionData) && $targetSchoolCode!=null){
                    $this->session->set_userdata('schoolCode', $targetSchoolCode);
					$this->session->set_userdata('targetSchoolCode', $targetSchoolCode);
                    redirect(base_url() . "Language/session/");
                    exit;
                }else{
                    $this->osDetails      = $os;
                    $this->browserName    = $browserName;
                    $this->browserVersion = $browserVersion;
                    //$this->setCmntNotificationTotalCount($user_id);
                    //checking if user login from Super admin id if yes then setting session
                    if(isset($targetSchoolCode) && $targetSchoolCode!=null){
                        $this->session->set_userdata('targetSchoolCode', $targetSchoolCode);
					}
					
                    $this->session->set_userdata('osDetails', $this->osDetails);
                    $this->session->set_userdata('browserName', $this->browserName);
                    $this->session->set_userdata('browserVersion', $this->browserVersion);
                    
                    $parameters = $_POST;

                    if($this->session->userdata('userID') != null && $this->session->userdata('sessionID') !=null) 
                    {
                            $logOutBtnClick = 'false';

                            $this->load->library('session');
                            $this->load->model('Language/login_model');
                            $this->login_model->updateUserEndTime($this->session->userdata('userID'),$this->session->userdata('sessionID'), $this->session->userdata('childClass'), $logOutBtnClick,$this->logOutReasonFlag);
                          
                    }
               }

		$this->user_id = $user_id;
		//$password  = $parameters['password'];
		$result = array();  
		$this->load->model('Language/login_model');
		
		//Check for the subscription for the user logged in.

		$status = $this->user_model->isSubscribedStudent($user_id);
		
		if($status==0)
		{
			$_SESSION['loginPageMsg'] = 1;
	        header("Location: ".$this->config->item('login_url')."?login=10");
	        exit;
		}
		else if($status == 1)
		{
			//Valid user set the session variables and proceed.
			if($this->loadSessionDetails())
			{
				redirect(base_url() . "Language/session/");
				exit;
				//header("Location: " . base_url() . "Language/session/");
			}
			else
			{
				redirect(base_url() . "Language/session/");
				exit;
				//header("Location: " . base_url() . "Language/session/");
			}
		}
		else if($status == 2)
		{
			header("Location: ".$this->config->item('login_url')."?login=11");
    		exit;
		}
		else if($status == 3)
		{
			header("Location: ".$this->config->item('login_url')."?login=12");
			exit;
		}
		// status == 4 is for parent login.
		else if($status == 5)
	    {
	        header("Location: ".$this->config->item('login_url')."?login=3");
    		exit;
	    }
	    else if($status == 6)
	    {
	        header("Location: ".$this->config->item('login_url')."?login=4");
    		exit;
	    }
	    else if($status == 8)
	    {
	        header("Location: ".$this->config->item('login_url')."?login=5");
    		exit;
	    }
	 
		// --- Can be removed ---------------- //
		$blockHomeUsageArr = array(365783,3149549);
		if($isValid)
		{
			if($this->login_model->isSyncDne($this->session->userdata('schoolCode')) == 'Allowed')
			{
				// User is valid and the session details are set.
				$this->loadSessionDetails();
				if (in_array($this->session->userdata('schoolCode'), $blockHomeUsageArr)) {
					$result['allow'] = 3;
					//echo json_encode($result);
					return;
				}
				// Redirect to the Home page and return.
				header("Location: ".base_url() . "Language/session/");
				exit;
			}
			else
			{
				$result['allow'] = 2; 
				//header("Location: ".$this->config->item('login_url').'?error_para=2');
			}

		}
		else
		{

			header("Location: ".$this->config->item('login_url').'?login=0');
			exit;
			//$result['allow'] = 1;
		}
		
		echo json_encode($result);
		
	}

	/**
	 * function role : Loading user details, browser details in session
	 * @return   redirection, MS English home page
	 * 
	 * */
	function updatePasswordEncryption()
	{
		$this->load->model('Language/user_model');
		$this->load->model('Language/login_model');

		
		$userInfo = $this->user_model->getUserData($this->user_id);

	}
	function loadSessionDetails()
	{
		$this->load->model('Language/user_model');
		$this->load->model('Language/login_model');
		
		//$this->session->userdata('userName',$this->user_name);
		$userInfo = $this->user_model->getUserData($this->user_id);

		$this->session->set_userdata('userID',$this->user_id);
		$this->session->set_userdata('logged_in',true);
		if( !$this->login_model->getLastLogoutStatus($this->user_id) )		
		{
			$this->session->set_userdata('already_logged','0');
			$this->startSession();
			return true;
		}
		else
		{
			$this->session->set_userdata('already_logged','1');
			return false;
		}
	}
	function startSession()
	{
		$this->load->model('Language/user_model');
		$this->load->model('Language/login_model');
		$this->load->model('Language/freeques_model');
		$this->load->model('Language/studentsessionflow_model');

		$this->browserRestrict();

		$userInfo = $this->user_model->getUserData($this->session->userdata('userID'));

		$this->session->set_userdata($userInfo);
		$contentFlowMaster = $this->user_model->getContentFlowMaster($this->session->userdata('userID'));
		$this->session->set_userdata($contentFlowMaster);
		$this->user_model->updateUserCurrentStatusLevel($this->session->userdata('userID'));

		$childClass = $this->session->userdata('childClass');
		$schoolCode = $this->session->userdata('schoolCode');
		$groupSkillID = $this->session->userdata('groupSkillID');
		$this->session->set_userdata('logged_in',true);
		$this->session->set_userdata('preview',"off");
		
	           if($this->session->userdata('targetSchoolCode')){
	                    $this->session->set_userdata('schoolCode',$this->session->userdata('targetSchoolCode'));
	           }     

	          $getSchoolContentFlowOrder = $this->studentsessionflow_model->getSchoolContentFlowOrder($this->session->userdata('schoolCode'),$childClass);
	          $this->session->set_userdata('templateID', $getSchoolContentFlowOrder['templateID']);
	          $this->session->set_userdata('remediationTemplateID', $getSchoolContentFlowOrder['remediationTemplateID']);

	          /*Added new constant value from code*/
	          $getCosntantvalue = $this->studentsessionflow_model->getCosntantvalue($this->session->userdata('remediationTemplateID'));
	          $this->session->set_userdata($getCosntantvalue);
	          /*End constant code value*/
	           $checkContentFlowOrder = $this->studentsessionflow_model->checkContentFlowOrder($this->session->userdata('userID'));


		if($this->session->userdata('category')=="TEACHER" || $this->session->userdata('category')=="School Admin"){
			$userClass = $this->user_model->getTeacherClassMapping($this->session->userdata('userID'));
			$this->session->set_userdata('teacherClass', $userClass);
		}
		else
		{
			$this->session->set_userdata('teacherClass',"1,A~1,B");
		}
		
		$this->load->helper('url');
		$this->load->library('user_agent');

		/*IP DETAILS HERE*/
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];   //Needed since apps 
	   	if(strpos($ip,',') !== false) {
	       $ip = substr($ip,0,strpos($ip,','));
	   	}
		
		$this->session->set_userdata('clientBrowser', $this->browserName.' '.$this->browserVersion.' OS:'.$this->osDetails );


		$this->session->set_userdata('sessionID', $this->login_model->logUserSession($this->session->userdata('userID') , $this->session->userdata('clientBrowser'), $ip));
		// Log the login Time
		$this->session->set_userdata('authorize', $this->session->userdata('userID').rand());
		$this->session->set_userdata('session_start_time', date('Y-m-d H:i:s'));
	}


	function browserRestrict()
	{
		//$this->load->library('user_agent');
		$this->load->library('Mobile_Detect');
		$detect = new Mobile_Detect;

		$isMobile       = $detect->isMobile();
		$isTablet       = $detect->isTablet();

		$firefoxVersion = $detect->version('Firefox');
		$chromeVersion  = $detect->version('Chrome');
		$ieVersion      = $detect->version('IE');

		$isAndroid      = $detect->isAndroidOS();
		$androidVersion = $detect->version('Android');

		$isIOS          = $detect->isiOS();
		$iosVersion     = $detect->version('iPad');

		if(!$isMobile && !$isTablet)
		{
			if($firefoxVersion != '')
			{
				if($firefoxVersion < 35)
				{
					$this->session->set_userdata('browserRestrict', '1');
				}
				else
				{
					$this->session->set_userdata('browserRestrict', '0');	
				}
			}
			elseif($chromeVersion != '')
			{
				if($chromeVersion < 38)
				{
					$this->session->set_userdata('browserRestrict', '1');
				}
				else
				{
					$this->session->set_userdata('browserRestrict', '0');
				}
			}
			elseif($ieVersion != '')
			{
				if($ieVersion < 10)
				{
					$this->session->set_userdata('browserRestrict', '1');
				}
				else
				{
					$this->session->set_userdata('browserRestrict', '0');
				}
			}

		}
		else
		{
			if($isIOS != '')
			{
				if($iosVersion < 6)
				{
					$this->session->set_userdata('browserRestrict', '1');
				}
				else
				{
					$this->session->set_userdata('browserRestrict', '0');

					//check chrome version here also
					/*if($chromeVersion != '')
					{
						if($chromeVersion < 38)
						{
							echo 'less than 38';
							$this->session->set_userdata('browserRestrict', '1');
						}
						else
						{
							echo 'equal or higer then 38';
							$this->session->set_userdata('browserRestrict', '0');
						}
					}*/
				}
			}
			elseif($isAndroid != '')
			{
				if($androidVersion < 4)
				{
					$this->session->set_userdata('browserRestrict', '1');
				}
				else
				{
					$this->session->set_userdata('browserRestrict', '0');

					//check chrome version here also
					/*if($chromeVersion != '')
					{
						if($chromeVersion < 38)
						{
							$this->session->set_userdata('browserRestrict', '1');
						}
						else
						{
							$this->session->set_userdata('browserRestrict', '0');
						}
					}*/
				}
			}
		}
		
	}
	function checkPreviousLoginActive()
	{
		$this->load->model('Language/login_model');
		$user_id = $this->session->userdata('userID');

		$status = $this->login_model->getLastLogoutStatus($user_id);

		if(!$status)
		{
			echo "true";
		}
		else
		{
			echo "false";
		}

	}
	public function getUserContentAttemptDetails($userID){
		$this->dbEnglish->Select('userContentFlowType,contentAttemptCount,orderNo');
		$this->dbEnglish->from('userContentAttemptLog');
		$this->dbEnglish->where('userID',$userID);
		$query = $this->dbEnglish->get();
		return  $query->result_array();

	}


	function logoutOtherSessions($osDetails,$browserName,$browserVersion)
	{
		$this->osDetails      = $osDetails;
		$this->browserName    = $browserName;
		$this->browserVersion = $browserVersion;
		$this->logOutReasonFlag = 8;

		$this->load->model('Language/login_model');
		$this->load->model('Language/user_model');
		$user_id = $this->session->userdata('userID');
		$childClass = $this->session->userdata('childClass');
		
		
		$this->login_model->updateLastLogoutStatus($user_id, $childClass,$this->logOutReasonFlag);

		/*$this->session->unset_userdata('userID');
		$this->session->unset_userdata('already_logged');
		*/
		

		$this->startSession();

		$this->session->unset_userdata('already_logged');
		$this->session->set_userdata('already_logged','0');
		
		redirect(base_url() . "Language/session/");
	}
	function logoutCurrentSessions($osDetails,$browserName,$browserVersion)
	{
		$this->osDetails      = $osDetails;
		$this->browserName    = $browserName;
		$this->browserVersion = $browserVersion;
		$data = array( 
            'userID' => ''  
        );  
        $this->session->set_userdata($data);
        $this->session->sess_destroy();
        $this->session->unset_userdata();
        redirect($this->config->item('login_url'));
	}
	/**
	 * function role : Update User end time
	 * @return : none
	 * 
	 * */

	function updateEndTime()
	{
		$this->load->library('session');
		$this->load->model('Language/login_model');
		$logOutBtnClick = $this->input->post('logoutTime');
		$this->logOutReasonFlag = $this->input->post('logoutReason');
		/*if(!$this->logOutReasonFlag)
			$this->logOutReasonFlag = 1;*/
		$this->login_model->updateUserEndTime($this->session->userdata('userID'),$this->session->userdata('sessionID'), $this->session->userdata('childClass'), $logOutBtnClick,$this->logOutReasonFlag);
		$data = array( 
            'userID' => ''  
        );  
        $this->session->set_userdata($data);
        $this->session->sess_destroy();
        $this->session->unset_userdata();
        $data = array( 
            'logged_in' => 0  
        );  
        $this->session->set_userdata($data);
		echo json_encode(array('redirect' => true));

	}
        
        function destroyLimitedSession()
	{
		$this->load->library('session');
                //saving temp session data require to reassign
        $tempSessionData=array('userID'=>$this->session->userdata('userID'),
                                'clientBrowser'=>$this->session->userdata('clientBrowser'),
                                'sessionID'=>$this->session->userdata('sessionID'),
                                'session_start_time'=>$this->session->userdata('session_start_time'),
                                'osDetails'=>$this->session->userdata('osDetails'),
                                'browserName'=>$this->session->userdata('browserName'),
                                'browserVersion'=>$this->session->userdata('browserVersion'),
                                '__StartTime'=>time(),
                                'sessionStartTime'=>$this->session->userdata('session_start_time'),
                                
        );
            echo json_encode(array('redirect' => true));

	}

	function temp()
	{

		$this->load->model('Language/login_model');
		$this->login_model->temp();
		
	}

	function timeTakenInClassroom()
	{
		$this->load->library('session');
		$this->load->model('Language/login_model');
		$sessionID = $this->session->userdata('sessionID');
		$totalTimeTakenClassroom = $this->input->post('timeTakenInClassroom');
		$this->logOutReasonFlag = $this->input->post('logoutReason');

		
		$this->login_model->updateTimeTakenInClassroom($this->session->userdata('userID'),$this->session->userdata('sessionID'), $totalTimeTakenClassroom);
		
		//echo json_encode(array('redirect' => true));
	}

	/*function setCmntNotificationTotalCount($userID)
	{

		$this->load->model('Language/commentsystem_model');
		$cmtNotiCount=$this->commentsystem_model->setCmntNotificationCount($userID);
		$this->session->set_userdata('totalCmntNotificaCnt',$cmtNotiCount);
	}*/

}



?>
