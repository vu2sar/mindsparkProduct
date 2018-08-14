<?php

Class Preview extends CI_Controller
{

	/**
	 * function role : MS English home page redirection
	 * @return   redirection, MS English home page
	 * 
	 * */
    
	function __construct(){
       
        parent::__construct();
    }
	function index()
	{
		$this->load->model('Language/user_model');
		$this->load->model('Language/login_model');
		$userInfo = $this->user_model->getUserData(449839);
		
		$this->session->set_userdata($userInfo);
		$this->session->set_userdata('already_logged','0');
		$this->session->set_userdata('logged_in',true);
		$this->session->set_userdata('preview',"on");
		if(isset($_GET['liveEditPrev']) && $_GET['liveEditPrev']=="liveEditPreview"){
			$this->session->set_userdata('liveEditPreview',"liveEditPreview");
		}else{
			$this->session->unset_userdata('liveEditPreview');
		}		
		//$this->session->set_userdata('sessionID', $this->login_model->logUserSession($this->session->userdata('userID') , "Testing Chrome"));
		$data = array();
		$data['user_theme'] = 'default';
		$this->load->view('Language/index.php',$data);
	}
}

?>