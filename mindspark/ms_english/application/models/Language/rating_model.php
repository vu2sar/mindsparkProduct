<?php

Class Rating_model extends CI_model
{

	
	public function __construct() {

	  $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	  $this->Companies_db = $this->dbEnglish;

	  // Pass reference of database to the CI-instance
	  $CI =& get_instance();
	  $CI->Companies_db =& $this->Companies_db; 
	}

	/**
	 * function role : Save user content rating information for passages attempted
	 * param1 : userID
	 * param2 : contentID
	 * param3 : for what content the rating is given eg passage,question etc
	 * param4 : rating given by the user 
	 * param5 : comment given by the user 
	 * param6 : if the rating is given either 1 or 2 then the reason that rating
	 * @return  none
	 * 
	 * */

	function saveUserContentRating($userID,$contentID,$contentType,$rating,$comment, $ratingReasonOther)
	{
		$this->dbEnglish->Select('id');
		$this->dbEnglish->from('userContentRating');
		$this->dbEnglish->where('contentID', $contentID);
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$checkContentRating = $query->result_array();

		if(count($checkContentRating)>0 && $contentType!='gre')   // my code change condition here for gre 
		{
			$data = array(
				'userID'            => $userID,
				'contentID'         => $contentID,
				'contentType'       => $contentType,
				'rating'            => $rating,
				'comment'           => $comment,
				'ratingReasonOther' => $ratingReasonOther
			);

			$this->dbEnglish->where('userID', $userID);
			$this->dbEnglish->where('contentID', $contentID);
			$this->dbEnglish->update('userContentRating', $data);

			$this->userContentRatingID = $checkContentRating[0]['id'];
		}
		else
		{
			$data = array(
				'userID'            => $userID,
				'contentID'         => $contentID,
				'contentType'       => $contentType,
				'rating'            => $rating,
				'comment'           => $comment,
				'ratingReasonOther' => $ratingReasonOther
			);
			$this->dbEnglish->insert('userContentRating', $data);
			$this->userContentRatingID = $this->dbEnglish->insert_id();
		}

	}
	 // my code fo rating count in activity return true if count less then 2 Aditya
	function checkUserActivityRatingCount($userID,$contentID,$contentType){
		$this->dbEnglish->Select('id');
		$this->dbEnglish->from('userContentRating');
		$this->dbEnglish->where('contentType','gre');
		$this->dbEnglish->where('contentID', $contentID);
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$checkRatingCount = $query->result_array();
		if(count($checkRatingCount)>=2)
			return false;
		else
			return true;

	}
}

?>