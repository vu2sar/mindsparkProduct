<?php

Class MyDictionary extends MY_Controller
{

	public $passageID;

	function __construct()
    {
        parent::__construct();
        $this->load->model('Language/login_model');
        $this->load->model('Language/mydictionary_model');
    }

	function index()
	{
		$this->load->view('Language/index');
	}

	/**
	 * function role : Insert the word into the user_dictionary table
	 * @return  none
	 * 
	 * */

	function saveUserWord()
	{
		$tempArr = array(
			"referenceID"   => $_POST['referenceID'], 
			"referenceType" => $_POST['referenceType'], 
			"childClass"    => $this->child_class, 
			"sessionID"     => $this->session_id, 
			"userID"        => $this->user_id, 
			"word"          => $_POST['word'], 
			);
		$this->mydictionary_model->saveUserSelectedWord($tempArr);
	}

	function getMeanings($alphabet, $offset, $count)
	{
		$userID = $this->user_id;
		$getData = $this->mydictionary_model->getUserWordWithMeaning($userID, $alphabet, $offset, '', $count);
		echo json_encode($getData);
		//echo "<pre>";print_r(json_encode($getData));echo "</pre>";
	}

	function searchMeanings($searchWord)
	{
		$userID = $this->user_id;
		$getSearchData = $this->mydictionary_model->getWordMeaning($searchWord);
		$getSearchData = $getSearchData[0];
		echo json_encode($getSearchData);
		//echo "<pre>";print_r(json_encode($getSearchData));echo "</pre>";
	}

	function searchMeaningsUser($searchWord)
	{
		$userID = $this->user_id;
		$getSearchData = $this->mydictionary_model->getWordMeaningUser($searchWord, $userID);
		$getSearchData = $getSearchData[0];
		echo json_encode($getSearchData);
	}

	function getDicWords()
	{
		$word = $_POST['word'];
		$getSearchDataDic = $this->mydictionary_model->getWords($word);
		echo json_encode($getSearchDataDic);
	}

	
}


?>