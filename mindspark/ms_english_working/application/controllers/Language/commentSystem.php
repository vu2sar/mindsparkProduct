<?php

Class CommentSystem extends MY_Controller
{

	//public $passageID;

	function __construct()
    {
        parent::__construct();
        //$this->load->model('Language/login_model');
        $this->load->model('Language/commentsystem_model');
    }

	function index()
	{
		$this->load->view('Language/index');
	}

	/**
	 * function role : Fetch the categories from categories table
	 * @return  category list
	 * 
	 * */


	function getCategories()
	{
		$getData = $this->commentsystem_model->getCategories();
		echo json_encode($getData);
	}

	function getSubCategories($commentCategoryID)
	{
		$getData = $this->commentsystem_model->getSubCategories($commentCategoryID);
		echo json_encode($getData);
	}

	/**
	 * function role : Insert user comments 
	 * @return  none
	 * 
	 * */

	function insertUserComments()
	{
		$this->load->model('Language/login_model');

		if( $this->login_model->isUserActive( $this->user_id, $this->session_id) )
		{
			$this->commentsystem_model->insertUserComments($this->user_id, $_POST['itemID'], $_POST['page'], $_POST['status'], $this->session_id, $_POST['commentCategoryID'], $_POST['commentSubCategoryID'], $_POST['comment']);

			echo $this->return_response( null , 'Your comment has been submitted.' , SUCCESS);	
		}
		else
			echo $this->return_response();
	}

	function checkProfanity()
	{
		$isProfanity= $this->getProfanityData($_POST['comment']);
			if($isProfanity)
				echo json_encode(true);
			else
				echo json_encode(false);
	}


	function getUserComments()
	{
		$getData = $this->commentsystem_model->getUserComments($this->user_id);

		echo json_encode($getData);
	}

	function fetchCommentDetails($commentID)
	{
		$getData = $this->commentsystem_model->fetchCommentDetails($this->user_id,$commentID);
		echo json_encode($getData);
	}

	function insertUserReplyComments()
	{
		$this->load->model('Language/login_model');
		if( $this->login_model->isUserActive( $this->user_id, $this->session_id) )
		{
			$this->commentsystem_model->insertUserReplyComments($this->user_id,$this->session_id,$_POST['comment'],$_POST['status'], $_POST['commentID'], $_POST['commentCategoryID'], $_POST['commentSubCategoryID']);

			echo $this->return_response( null , 'Your comment has been submitted.' , SUCCESS);
		}
		else
			echo $this->return_response();
	}

	function notificationCount()
	{
		$cmtNotiArr=$this->commentsystem_model->setCmntNotificationCount($this->user_id);
		//$this->session->set_userdata('totalCmntNotificaCnt',$cmtNotiCount);
		$cmtNotiCount['commentReplyRcvd'] = count($cmtNotiArr);
		echo $this->return_response(json_encode($cmtNotiCount));
		//echo json_encode($cmtNotiCount);
	}
	
}


?>
