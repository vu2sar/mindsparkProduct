<?php
 	
error_reporting(0);
Class Mydictionary_model extends MY_Model
{

	
	public function __construct() 
	{
		parent::__construct();			 
					 
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	}

	function saveUserSelectedWord($data)
	{
		if(!empty($data) && count($data) > 0)
		{
			$this->dbEnglish->_protect_identifiers = FALSE;
			$this->dbEnglish->Select('sr_no, userID');
			$this->dbEnglish->from('user_dictionary');
			$this->dbEnglish->where('userID',$data['userID']);
			$this->dbEnglish->where('word',$data['word']);
			$query = $this->dbEnglish->get();
			$getExistingWord = $query->result_array();

			if(count($getExistingWord) == 0)
			{
				$saveWord = preg_replace('/\s+/', '', $data['word']);

				$data_insert = array(
				   'referenceID'   => $data['referenceID'],
				   'referenceType' => $data['referenceType'],
				   'childClass'    => $data['childClass'],
				   'sessionID'     => $data['sessionID'],
				   'userID'        => $data['userID'],
				   'word'          => $saveWord,
				);

				$this->dbEnglish->insert('user_dictionary', $data_insert);
			}
		}
	}

	function getUserWordWithMeaning($userID, $alphabet, $offset, $limit, $count)
	{
		$limit = 20;

		/*if($count == 'count')
			$this->dbEnglish->Select('count(distinct(ud.word)) as total_rows');
		else*/
		$this->dbEnglish->Select('distinct(ud.word) as user_word, ld.wordType, ld.definition');
		$this->dbEnglish->from('user_dictionary ud');
		//$this->dbEnglish->join('largedictionary ld', 'ld.word = ud.word', 'left');
		$this->dbEnglish->join('dictionary ld', 'ld.word = ud.word', 'left');
		$this->dbEnglish->where('userID', $userID);
		$this->dbEnglish->where('ud.word LIKE "'.$alphabet.'%"');
		$this->dbEnglish->order_by('ud.word');
		
		if($count !== 'count')
		{
			if($limit == '' && $offset == '')
				$this->dbEnglish->limit($limit, 0);
			else
				$this->dbEnglish->limit($limit, $offset);
		}
		
		$query = $this->dbEnglish->get();

		if($count == 'count')
		{
			$get_result                = $query->result_array();
			$getData['completeResult'] = $get_result;
			$getData['total_rows']     = count($get_result);
		}
		else
		{
			$getData = $query->result_array();
		}
		//echo "<pre>";print_r($this->dbEnglish->last_query());echo "</pre>";
		/*foreach ($getData as $key => $value) 
		{
			$getData[$key]['tooltip'] = $value['user_word'].'|'.$value['wordType'].'|'.$value['definition'];
		}*/
		return $getData;
	}

	function getWordMeaning($searchWord)
	{
		//$this->dbEnglish = $this->load->database('local_database',TRUE);
		$this->dbEnglish->Select('ud.word as user_word, ud.wordType, ud.definition');
		//$this->dbEnglish->from('largedictionary ud'); //change the table to ei_dictionary once implement the fields in it
		$this->dbEnglish->from('dictionary ud');
		$this->dbEnglish->where('word', $searchWord);
		//$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		return $getData;
	}

	function getWordMeaningUser($searchWord, $userID)
	{
		$this->dbEnglish->Select('ud.word as user_word, d.wordType, d.definition');
		$this->dbEnglish->from('user_dictionary ud');
		$this->dbEnglish->join('dictionary d', 'd.word = ud.word');
		$this->dbEnglish->where('ud.word', $searchWord);
		$this->dbEnglish->where('userID', $userID);
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		return $getData;
	}

	function getWords($word)
	{
		$result = array();
		$this->dbEnglish->Select('word');
		$this->dbEnglish->from('dictionary');
		$this->dbEnglish->where('type', 't:dict_word');
		$this->dbEnglish->like('word', $word);
		$query = $this->dbEnglish->get();
		$getData = $query->result_array();
		if(!empty($getData) && count($getData) > 0)
		{
			foreach ($getData as $key) 
			{
				array_push($result, $key['word']);
			}
		}
		return $result;
	}

}

?>
