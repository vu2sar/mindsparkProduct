<?php

Class contentflow_model extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		$this->load->library('session');
		$this->load->model('Language/passage_model','passage_model');
		$this->load->model('Language/setquestiongivingflowarray_model','setquestiongivingflowarray_model');	
		
	}

	public function defaultContentFlowOrder() {
		$this->dbEnglish->Select('contentType,contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentStatus','Yes');
		$this->dbEnglish->order_by('contentOrder','ASC');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->result_array();
	}

	public function getUserLastAttmpt($userID) {
		$questionAttemptClassTbl=$this->getQuesAttemptClass();
		$query = "SELECT b.passageTypeName as passageType FROM ".$questionAttemptClassTbl." a,questions b where a.qcode=b.qcode and a.userID='$userID' group by  b.passageID,b.passageTypeName order by MAX(a.attemptedDate) desc limit 2";
		$getLastAttemptByUser = $this->dbEnglish->query($query);
		return $getLastAttemptByUser->result_array();
	}
 	
	function getMaxContentOrders(){                
		  $contentFlowArray = $this->defaultContentFlowOrder();
		  $newdata = array();
		  foreach ($contentFlowArray as $key => $contenFlow) :
			 $content = $contenFlow->contentType[0];
		  if($content!='f') :
			for($i=0; $i < $contenFlow->contentQuantity; $i++) :
							$newdata[] = ucfirst($content); 
			endfor;
			 else :
				  $newdata[] = ucfirst($content); 
				endif;
		 endforeach;
		 return  $newdata;     
	}
  
	function contentType($type){
		if($type=='0'){
		  return 'F';     
		}else if($type=='Textual'){
		  return 'R';   
		}else if($type=='Illustrated'){
		  return 'R';   
		}else if($type=='Conversation'){
		  return 'C';   
		}
	}
	
	//function to get content  quantity of free question
	public function getQuantityOfFreeQue($userID){
		$this->dbEnglish->Select('contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentType','freeques');
		$this->dbEnglish->limit(1);
		$query = $this->dbEnglish->get();
		return  $query->row();
	}
	
	//function to get content quantity
	public function getContentQuantity($newIndex){
		$this->dbEnglish->Select('contentQuantity');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentOrder',$newvalue);
		$query = $this->dbEnglish->get();
		echo $this->dbEnglish->last_query();exit;
		return  $query->row();
	}
	
	//function to get the content flow details
    	public function GetContentFlowOrder($userID,$currentContentType){
    		$this->dbEnglish->Select('contentType,contentQuantity,contentOrder');
		$this->dbEnglish->from('contentFlowMaster');
		$this->dbEnglish->where('contentStatus','Yes');
		$this->dbEnglish->order_by('contentOrder','ASC');
		$query = $this->dbEnglish->get();
		return  $query->result_array();
	}
	
}
