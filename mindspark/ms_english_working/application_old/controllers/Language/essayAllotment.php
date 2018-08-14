<?php


Class EssayAllotment extends MY_Controller
{
	function __construct(){
        parent::__construct();
		$this->load->model('Language/essayallotment_model');

		$this->load->model('Language/login_model');
		if(!$this->login_model->isUserActive( $this->user_id, $this->session_id)){
			echo $this->return_response();
			exit;
		}
	}

	/**
	 * function description : Function will return pending essay for the requested class and section using user's school code. Get value from post. 
	 * param1   childclass
	 * param2   childsection
	 * @return  json array 
	 * 
	 * */
	function getTeacherEssayAllotment(){
		$responseArr=array();
                $childclass=$_POST['childclass'];
                $childsection=$_POST['childsection'];
		$responseArr['pendingEssays']=$this->essayallotment_model->getEssaysToReview($this->user_id,$childclass,$childsection);
                $responseArr['topicsAssignByTeacher']=$this->essayallotment_model->topicsAssignByTeacher($this->user_id,$childclass,$childsection,$this->school_code);
		echo $this->return_response($responseArr);
	}

	
	function getEssay($scoreID = null, $mode = null){
		$responseArr=array();
		if($scoreID == null){
			$scoreID=$_POST['essayScoreID'];
			$mode=$_POST['mode'];	
		}
		$responseArr=$this->essayallotment_model->getEssayDetails($this->user_id,$scoreID,$mode);
		echo $this->return_response(json_encode($responseArr));
	}
	
	function saveEssay(){
		$responseArr=array();
		$status=$_POST['status'];
		$essayDetailsArr=array("scoreID" => $_POST['scoreID'],"status" => $status,"essayFeedback" =>$_POST['essayFeedback'], "essaySFB" => $_POST['essaySFB'], "essayScore" =>$_POST['essayScore'], "rubricSc" =>$_POST['rubricSc']);
		$responseArr=$this->essayallotment_model->saveReviewedEssayDetails($essayDetailsArr);
		echo json_encode($responseArr);
	}

	function saveComment(){
		$responseArr=array();
		if($scoreID == null){
			$scoreID=$_POST['essayScoreID'];
			$essaySFB=$_POST['essaySpecificFeedback'];	
		}
		$responseArr=$this->essayallotment_model->saveComment($scoreID,$essaySFB);
		echo $this->return_response(json_encode($responseArr));
	}

	function removeComment(){
		$responseArr=array();
		$scoreID=$_POST['essayScoreID'];
		$essaySFB=$_POST['essaySpecificFeedback'];	
		$responseArr=$this->essayallotment_model->removeComment($scoreID,$essaySFB);
		echo $this->return_response(json_encode($responseArr));
	}

	function saveFeedBack(){
		$responseArr=array();
		$postData=$_POST['postData'];
		//print_r($postData);
		$responseArr=$this->essayallotment_model->saveReviewedEssayDetails($postData);
		echo $this->return_response(json_encode($responseArr));
	}
        
        /**
	 * function description : Function will activate new essay. 
	 * param1   startDate
	 * param2   endDate
         * param3   childclass
         * param4   childsection
         * param5   essayName
	 * @return  json array -> already exist if topic with same date range is available or activated true. If not inserted then return mysql error.
	 * 
	 * */
        function newEssayActivationByTeacher(){
            $startDate  = date("Y-m-d", strtotime($_POST['startDate']));
            $endDate    = date("Y-m-d", strtotime($_POST['endDate']));
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection'];
            $essayName=$_POST['essayName'];
            $checkActiveTopic=$this->essayallotment_model->getActiveTopicWithinRange($childclass,$childsection,$startDate,$endDate);
            if(isset($checkActiveTopic) && !empty($checkActiveTopic)){
                $responseArr=array('alreadyExists'=>true);
            }else{
                $responseArr=$this->essayallotment_model->activateNewEssay($essayName,$startDate,$endDate,$childclass,$childsection);
                if(is_numeric($responseArr)){
                    $responseArr=array('activated'=>true);
                }
            }
            echo $this->return_response(json_encode($responseArr));
        }
        /**
	 * function description : Function will deactivate existing essay and activate new essay. 
	 * param1   startDate
	 * param2   endDate
         * param3   childclass
         * param4   childsection
         * param5   essayName
	 * @return  json array -> activated true and If not inserted then return mysql error.
	 * 
	 * */
        function deactivateExistingEssayActiveNewEssay(){
            $startDate  = date("Y-m-d", strtotime($_POST['startDate']));
            $endDate    = date("Y-m-d", strtotime($_POST['endDate']));
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection'];
            $essayName=$_POST['essayName'];
            $checkActiveTopic=$this->essayallotment_model->getActiveTopicWithinRange($childclass,$childsection,$startDate,$endDate); 
            $deactivated=$this->essayallotment_model->deactivateOrHideExistingEssay($checkActiveTopic);
            $getInsertedID = $this->essayallotment_model->activateNewEssay($essayName, $startDate, $endDate, $childclass, $childsection);
            if (is_numeric($getInsertedID)) {
                $getInsertedID = array('activated' => true);
            }
            echo $this->return_response(json_encode($getInsertedID));
        }
        /**
	 * function description : Function will deactivate existing. Require array essay id
	 * param1   essayID in array
	 * @return  json array -> return true.
	 * 
	 * */
        function deactivateExistingEssay(){
            $essayID[0]=array('essayID'=>$_POST['essayID']);
            $responseArr=$this->essayallotment_model->deactivateOrHideExistingEssay($essayID);
            echo $this->return_response(json_encode($responseArr));
        }
        /**
	 * function description : Function will return user detail on perticular topic.
	 * param1   essayID e.g ON123
         * param2   childclass
         * param3   childsection
	 * @return  json array -> student name, submission date and submission status.
	 * 
	 * */
        function getSubmissionByTopic(){
            $topicID=$_POST['essayID'];
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection']; 
            $responseArr=$this->essayallotment_model->getSubmissionByTopic($topicID,$childclass,$childsection);
            echo $this->return_response(json_encode($responseArr));
        }
         /**
	 * function description : Function will return user detail on perticular topic.
	 * param1   essayID e.g ON123
         * param2   childclass
         * param3   childsection
	 * @return  json array -> student name, submission date and submission status.
	 * 
	 * */
        function getSubmissionByStudent(){
            $topicID=$_POST['essayID'];
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection']; 
            $responseArr=$this->essayallotment_model->getSubmissionByStudents($topicID,$childclass,$childsection);
            echo $this->return_response(json_encode($responseArr));
        }
        /**
	 * function description : Function will return essay name and total submission. which is choosen by students.
	 * param1   childclass
         * param2   childsection
	 * @return  json array -> Topic Name, total submission.
	 * 
	 * */
        function getEssayChosenByStudent(){
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection']; 
            $responseArr=$this->essayallotment_model->getEssayChosenByStudent($childclass,$childsection);
            echo $this->return_response(json_encode($responseArr));
        }
        /**
	 * function description : Function will return todays active topic id, name and deactivation date in array.
	 * param1   childclass
         * param2   childsection
	 * @return  json array -> topic id, name and deactivation date.
	 * 
	 * */
        function getCurrentlyActiveTopic(){
            $childclass=$_POST['childclass'];
            $childsection=$_POST['childsection'];
            $currentDate=date('Y-m-d');
            $responseArr=$this->essayallotment_model->getActiveTopicWithinRange($childclass,$childsection,$currentDate,$currentDate);
            echo $this->return_response(json_encode($responseArr));
        }
        /**
	 * function description : Function will return class number which are primary for currently user..
	 * @return  json array -> class number.
	 * 
	 * */
        function getTeacherPrimaryMappedClass(){                        
                $teacher_class=$this->essayallotment_model->getPrimaryClassAndSection($this->user_id,false); 
		echo $this->return_response(json_encode($teacher_class));
	}
        /**
	 * function description : Function will return section of target class which are primary for currently user..
         * param1   selectedClass e.g 4,5,6
	 * @return  json array -> section. e.g A,B
	 * 
	 * */
	function getTeacherPrimaryMappedSection(){
		$selectedClass=$_REQUEST['selectedClass'];
                $teacher_class=$this->essayallotment_model->getPrimaryClassAndSection($this->user_id,$selectedClass);
		echo $this->return_response(json_encode($teacher_class));
	}
}
?>