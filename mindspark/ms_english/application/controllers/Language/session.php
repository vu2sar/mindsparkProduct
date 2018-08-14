<?php


Class Session extends MY_Controller
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
		if( $this->session->userdata('browserRestrict') == 1 )
		{
			$this->load->view('Language/browser');
		}
		elseif( $this->session->userdata('already_logged') == 0 )
		{
			$data = array();
			$data['authorize'] = $this->authorize;
			$data['category'] = $this->category;
			//$data['user_theme'] = 'christmas';
			$data['user_theme'] = 'default';
			$this->load->view('Language/index',$data);
		}
		else if($this->session->userdata('already_logged') == 1 )
		{
			$this->load->view('Language/alreadyLoggedIn');
		}
	}
	public function multitab()
	{
		$this->load->view('Language/multitab');
	}
	
}

?>
