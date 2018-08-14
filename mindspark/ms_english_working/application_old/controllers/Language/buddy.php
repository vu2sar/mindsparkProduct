<?php

Class Buddy extends MY_Controller
{
	function __construct(){
        parent::__construct();
	}
    
	function index()
	{
		$this->load->view('Language/index.html');
	}

	/**
	 * function role : Get buddy details
	 * param1 : user name
	 * @return  string, buddy name
	 * 
	 * */

	//function getBuddyDetails($username)
	function getBuddyDetails()
	{
		$this->load->model('Language/user_model');
		//$userDetails = $this->user_model->getUserData($username);
		$userDetails = $this->user_model->getUserData($this->user_id);
		echo $this->return_response(json_encode($userDetails['buddy']));
	}

	/**
	 * function role : Set buddy details
	 * param1 : user name
	 * param2 : buddy name
	 * @return  none
	 * 
	 * */

	//function setBuddyDetails($username,$type)
	function setBuddyDetails($type)
	{
		$this->load->model('Language/user_model');

		$updateInfoArr = array(
			'buddy' => $type
			);

		//$this->user_model->updateUserData($username,$updateInfoArr);
		$this->user_model->updateUserData($this->user_name,$updateInfoArr);
		echo $this->return_response();
	}

}



?>