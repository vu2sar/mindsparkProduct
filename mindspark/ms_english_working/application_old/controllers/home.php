<?php


Class Home extends MY_Controller
{
	function __construct(){
        parent::__construct();
	}
	function index()
	{	
		$this->load->view('Language/index.html');
		// $data['userInfo'] = $this->getUserInfo('kushal.shah');
		// $data['sidebarInfo'] = $this->getSidebarInfo();
		// $data['skillOMeterInfo'] = $this->getSkillOMeterInfo();
	}


	/**
	 * function role : Listing all items of sidebar menu
	 * @return : json object, Side bar menu information
	 * 
	 * */

	function getSidebarInfo() {
		$sideBar = array();
		array_push($sideBar, array('optionName' => 'Home Page', 'icon' => 1)) ;
		array_push($sideBar, array('optionName' => 'The Classroom', 'icon' => 2));
		array_push($sideBar, array('optionName' => 'The Grounds', 'icon' => 3));
		array_push($sideBar, array('optionName' => 'Trophy Room', 'icon' => 4));
		array_push($sideBar, array('optionName' => 'Essay Writer', 'icon' => 5));
		// array_push($sideBar, array('optionName' => 'Practice', 'icon' => 6));
		echo json_encode($sideBar);
	}
    
    /**
     * function role : Get information related to all home page components
     * @param1   userID
     * @return : json object,home page information
     * 
     * */

     //function getHomePageInfo($userID){
     function getHomePageInfo(){
        $this->load->model('Language/home_model');

        //echo json_encode($this->home_model->getHomePageInfo($userID) );
        echo json_encode($this->home_model->getHomePageInfo($this->user_id) );
    }

	/**
	 * function role : Skill meter generation head function
	 * @param1   userID
	 * @return : json object, skill-o-meter information
	 * 
	 * */

	//function getSkillOMeterInfo($userID) {
	function getSkillOMeterInfo() {
		$this->load->model('Language/home_model');
		//echo json_encode($this->home_model->buldSkillTree($userID) );
		echo json_encode($this->home_model->buldSkillTree($this->user_id) );
	}
	
	/**
	 * function role : fetch essay topics based on mslevel of user
	 * @return  topics list
	 * 
	 * */

	//function getEssayTopics($userID,$msLevel)
	function getEssayTopics()
	{
		//$this->output->enable_profiler(TRUE);
		//$userID=1;
		//$msLevel=1;

		$this->load->model('Language/essay_model');
		//$topicArr = $this->essay_model->getEssayTopics($userID,$msLevel);
		
		$topicArr = $this->essay_model->getEssayTopics($this->user_id,$this->lang_level);
		echo json_encode($topicArr);
	}	
	
	/**
	 * function role : Save essay details attempted by user, update if attempted earlier else insert
	 * @return  none
	 * 
	 * */

	function saveEssayDetails()
	{
		 $_POST = array('userID' => 51,'essayID'=>1);
		 $_POST['info']['userResponse'] = 'Anand testing code';
		 $_POST['status'] = 1;
		
		$this->load->model('Language/essay_model');
		//$this->essay_model->saveEssayDetails($_POST['userID'],$_POST['essayID'],$_POST['info']['userResponse'],$_POST['status'],$sessionID);
		$this->essay_model->saveEssayDetails($this->user_id,$_POST['essayID'],$_POST['info']['userResponse'],$_POST['status'],$this->session_id);
	}

	/**
	 * function role : Fetch feedback posted by evaluaters on essay attempted by user
	 * param1 : userID
	 * param2 : unique essayID
	 * @return  POST, essay feedback
	 * 
	 * */

	//function fetchEssayFeedback($userID,$essayID)
	function fetchEssayFeedback($essayID)
	{
		$this->load->model('Language/diagnostic_model');
		//$essayFeedback = $this->diagnostic_model->fetchEssayFeedback($userID,$essayID,1);
		$essayFeedback = $this->diagnostic_model->fetchEssayFeedback($this->user_id,$essayID,1);
		echo $essayFeedback;
	}

}

?>