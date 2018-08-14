<?php

Class Essayallotment_model extends MY_Model
{
	
	public $returnArr;
	
	public function __construct() {
		 parent::__construct();
 		 $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		 $this->load->library('session');
		 $this->load->helper('spell_check');
		 $this->returnArr=array();
	}

	/**
	 * function description : Get essays for the requested list type
	 * param1   userID
	 * param2   Child Class
         * param3   Child Section
	 * @return  array, of requested listType essays with EssayID, EssayTitle, ScoreID, Author, Class, Section, alloted date & pending
	 * 
	 * */
	 
	public function getEssaysToReview($userID,$childclass,$childsection){
		$this->returnArr=array();
		$getReviewEssayIDsSql=$this->dbEnglish->query("SELECT * FROM ews_essayScoring e,userDetails u WHERE e.userID<>0 and e.evaluatorID = $userID and e.status=0 and u.userID=e.userID and u.childClass='$childclass' and u.childSection='$childsection' order by e.allotedOn asc");
		//print $getReviewEssayIDsSql->num_rows(); 

		if($getReviewEssayIDsSql->num_rows() > 0){
			$reviewEssayIDsRes = $getReviewEssayIDsSql->result_array();
			$i=0;
			foreach($reviewEssayIDsRes as $row){
				$essayID=$row['essayID'];
				
				$getEssayDetailSql=$this->dbEnglish->query("SELECT b.essayTitle as title, a.startTime, a.timeTaken, b.essayID as topicID FROM ews_essayDetails a, essayMaster b WHERE a.topicID=b.essayID and a.essayID=$essayID");
				if($getEssayDetailSql->num_rows() > 0){

					$essayDetailArr = $getEssayDetailSql->row();
					$this->returnArr[$i]['essayID']    = $row['essayID'];
					$this->returnArr[$i]['essayTitle'] = $essayDetailArr->title;
					$this->returnArr[$i]['scoreID']    = $row['scoreID'];
					$this->returnArr[$i]['Author']     = $row['childName'];
					$this->returnArr[$i]['childclass'] = $row['childClass'].$row['childSection'];
					$this->returnArr[$i]['topicID']    = $essayDetailArr->topicID;
					$this->returnArr[$i]['allotedOn'] = date("d-m-Y", strtotime($row['allotedOn']));
	                $this->returnArr[$i]['pendingsince'] = $this->time_elapsed_string($row['allotedOn'], false);
					$i++;
				}				
			}
		}
                
                
		return $this->returnArr;
	}
	
	/**
	 * function description : Get essay details for the requested scoreID
	 * param1   userID
	 * param2   scoreID of essay
	 * @return  array, of requested scoreID essay details 
	 * 
	 * */
	
	
	public function getEssayDetails($userID,$scoreID,$mode){
		
		switch ($mode){
			case 0:{
				$this->retreiveEvaluatorFeedback($scoreID,true); 
				return $this->returnArr;
				break;
			}
			case 1:{
				$this->retreiveEvaluatorFeedback($scoreID); 
				return $this->returnArr;
				break;
			} 
		}	
	}
	
	/**
	 * function description : sets the return array with essay details for the passed scoreID with spell check flag enabled or disabled 
	 * param1   eID
	 * param2   spell check flag , true if spell check need to be applied and false if not
	 * @return  none 
	 * 
	 * */
	
	public function retreiveEvaluatorFeedback($eID,$spellingCheck=false){
		$eID=$eID*1;
		$getEssayIDSql=$this->dbEnglish->query("SELECT * FROM ews_essayScoring WHERE scoreID=$eID");
		if ($getEssayIDSql->num_rows() > 0){
			$essayIDArr = $getEssayIDSql->row();
			$essayID=$essayIDArr->essayID;
			$getEssayDetailSql=$this->dbEnglish->query("SELECT * FROM ews_essayDetails WHERE essayID=$essayID");
			if ($getEssayDetailSql->num_rows() > 0){
				$returnDataArr = $getEssayDetailSql->row();
				//$getChildClassQuery = "SELECT class FROM ews_userDetails WHERE userID=".$returnDataArr->writerID;
				if (strpos($returnDataArr->writerID, '-') !== FALSE){
					$returnDataArr->writerID = str_replace("-","",$returnDataArr->writerID);					
					$getChildClassQuery = "SELECT childClass as class,childName as author FROM userDetails WHERE userID=".$returnDataArr->writerID;
				}
				$getChildClassSql=$this->dbEnglish->query($getChildClassQuery);
				$writerDetails=$getChildClassSql->row();
				$authorClass=$writerDetails->class;
				//$returnDataArr->plainEssay=preg_replace("/\h+/", " " , $returnDataArr->essay);
				//$returnDataArr->plainEssay = preg_replace("/\\n\s/", "\\n", $returnDataArr->plainEssay);
				//$returnDataArr->plainEssay=preg_replace("/^\\s+/m","", $returnDataArr->plainEssay);
				//$returnDataArr->essay = preg_replace("/\h+/", "<br>", $returnDataArr->essay);
				//$returnDataArr->essay = preg_replace("/<br>\\s+/", "<br>", $returnDataArr->essay);
				//$returnDataArr->essay = preg_replace("/\\s+<br>/", "<br>", $returnDataArr->essay);
				$returnDataArr->plainEssay=$returnDataArr->essay;
				//$returnDataArr->essay = preg_replace("/\\s+/", " " , $returnDataArr->essay);
				$returnDataArr->report=$essayIDArr;
				$returnDataArr->writerClass=$authorClass;
				$returnDataArr->author=$writerDetails->author;
				$returnDataArr->submittedOn = date("d-m-Y", strtotime($returnDataArr->submittedOn));
				//print $returnDataArr->essay;
				if($spellingCheck && $returnDataArr->report->selectFeedback == "" && !$essayIDArr->isFeedbackSaved)
				{	
					//print $returnDataArr->report->selectFeedback;
					$spellCheckFields=array();
					$nonSpellCheckFields=array();
					array_push($spellCheckFields,$returnDataArr->essay);
					$spellCheckResult = spell_check("",0,$spellCheckFields,$nonSpellCheckFields,0);
					//$returnDataArr->essay=$spellCheckResult[0][0];
					$returnDataArr->essaySpellingErrors=$spellCheckResult[1];
					//print strpos($returnDataArr->plainEssay,$returnDataArr->essaySpellingErrors[2]);
				}
				$this->returnArr['data']=(array)$returnDataArr;
			}
		}
	}
	
	/**
	 * function description : save the essay information submitted by evaluator
	 * param1   essayDetailsArr ,  array of submitted details
	 * @return  array, success or failure message of the request;
	 * 
	 * */
	 
	public function saveReviewedEssayDetails($essayDetailsArr){
		$submitE="";
		$scoreID=$essayDetailsArr['essayScoreID']*1; 
		
		if ($essayDetailsArr['status']==1) {
			$submitE=", submittedOn=CURRENT_TIMESTAMP";
		}
		
		$this->dbEnglish->query("UPDATE ews_essayScoring SET score=".$essayDetailsArr['essayScore'].", feedback='".addslashes($essayDetailsArr['generalFeedback'])."', selectFeedback='".mysql_real_escape_string($essayDetailsArr['essaySpecificFeedback'])."', status=".$essayDetailsArr['status'].$submitE.", rubricSc='".$essayDetailsArr['rubricSc']."', isFeedbackSaved='".$essayDetailsArr['isFeedbackSaved']."' WHERE scoreID=".$essayDetailsArr['essayScoreID']);						
		
		$this->returnArr['data']='success';
		if ($essayDetailsArr['status']==1) {
			$this->returnArr['message']='Feedback submitted successfully!';
			$this->setEssayStatus($scoreID);
		}
		else $this->returnArr['message']='Feedback saved successfully!';
		return $this->returnArr;
	}
	
	/**
	 * function description : save the essay status for the submit essay request
	 * param1   scoreID ,  scoreID of essay
	 * @return  none;
	 * 
	 * */
	
	function setEssayStatus($scID){
		$getEssayDetailSql=$this->dbEnglish->query("SELECT essayID, COUNT(*)-SUM(status) as urv, SUM(status) as rv, 
				GROUP_CONCAT(score ORDER BY status DESC, score DESC) as gc,  
				GROUP_CONCAT(evaluatorID) as gcs 
				FROM ews_essayScoring GROUP BY essayID
				HAVING essayID IN (SELECT essayID FROM ews_essayScoring WHERE scoreID=$scID)");
		 
		if ($getEssayDetailSql->num_rows() > 0){
			$resultArr=$getEssayDetailSql->row_array();
			$esID=$resultArr['essayID'];
			$unrev=$line['urv'];$rev=$resultArr['rv'];$scrs=$resultArr['gc'];$evs=$resultArr['gcs'];
			//if ($rev<2) return;		
			
			$setStatusSql=$this->dbEnglish->query("UPDATE ews_essayDetails SET STATUS=2 WHERE essayID=$esID");
			
			$getWriterSql=$this->dbEnglish->query("SELECT writerID FROM ews_essayDetails WHERE essayID=$esID");
			
			if ($getWriterSql->num_rows() > 0){
				$writerDetails=$getWriterSql->row_array();
				//alertStudent($line['writerID']);
			}
			
		}
	}

	/**
	 * function description : save the comments in the ews_essayScoring 
	 * param1   scoreID ,  of the save comment request essay
	 * param2   essaySFB ,  save essay comment string 
	 * @return  array, success or failure message of the save comment request;
	 * 
	 * */
	
	function saveComment($scoreID,$essaySFB){
		$this->dbEnglish->query("UPDATE ews_essayScoring SET selectFeedback='".mysql_real_escape_string($essaySFB)."' WHERE scoreID=".$scoreID);						

		if ($this->dbEnglish->affected_rows() > 0){
			$this->returnArr['data']='success';
			$this->returnArr['message']='Comment saved successfully!';
		}
		else{
			$this->returnArr['data']='failed';
			$this->returnArr['message']='Failed to save comment!';
		}

		return $this->returnArr;

	}

	/**
	 * function description : remove the essay comment for the requested essay
	 * param1   scoreID ,  array of submitted details
	 * param2   essaySFB ,  save essay comment string 
	 * @return  array, success or failure message of the request;
	 * 
	 * */
	
	function removeComment($scoreID,$essaySFB){
		$this->dbEnglish->query("UPDATE ews_essayScoring SET selectFeedback='".$essaySFB."' WHERE scoreID=".$scoreID);						
					
		if ($this->dbEnglish->affected_rows() > 0){
			$this->returnArr['data']='success';
			$this->returnArr['message']='Comment saved successfully!';
		}
		else{
			$this->returnArr['data']='failed';
			$this->returnArr['message']='Failed to save comment!';
		}

		return $this->returnArr;

	}

	function alertStudent($student){
		$getUserDetailsSql=$this->dbEnglish->query("SELECT fname, email from ews_userDetails WHERE userID=$student");
		if ($getUserDetailsSql->num_rows() > 0){
			$line=$getUserDetailsSql->row_array();
			$message = "<p>Dear ".$line['fname'].",	<br><br>Your essay has been reviewed by our evaluators and their feedback is now available for your to see.<br><br>
					Please log into http://www.mindspark.in/login using your username and password.<br><br>
					For any query, write to essay.admin@ei-india.com.<br><br>Regards,<br>Admin<br>Mindspark Essay Writer<br><i>This is a system-generated email.</i></p>";
			$message = "<html>
					<head>
					<title>Essay Reviewed!</title>
					</head>
					<body>".$message."</body>
					</html>";
			$this->alertStudentMail($message,$line['email']);		


			//$to = ($line['email']=="")?"essay.admin@ei-india.com":$line['email'];
			// subject
			//$subject = 'Essay Reviewed - Mindspark Essay Writer';
			//$headers  = 'MIME-Version: 1.0' . "\r\n";
			//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//$headers .= 'From: <essay.admin@ei-india.com>' . "\r\n";
			//sendEmail($to, $subject, $message, $headers);
			//$retA=array('to'=>$to, 'subject'=>$subject, 'message'=>$message, 'headers'=>$headers);
			
			//$resp=cURL("sendMail.php",0,NULL,"to=".$to."&subject=".$subject."&message=".$message."&headers=".$headers);
		}
	}
        /**
	 * function description : This function will activate/insert new essay.
	 * param1   Essay Name
	 * param2   Essay Start Date
         * param3   Essay End Date
         * param4   Grade
         * param5   Section
	 * @return  return inserted id.
	 * 
	 * */
        function activateNewEssay($essayName,$startDate,$endDate,$grade,$section){
            $userID=$this->user_id;
            $schoolCode=$this->school_code;
            $abbreviation=$this->getAbbreviationOfSchool();
            //inserting into master table
            $data = array(                    
                    'essayTitle'  => $essayName,
                    'msLevel' => 0,
                    'wordLimit' => 100,
                    'userID' => $this->user_id,
            );
            $this->dbEnglish->insert('essayMaster', $data);
            $lastID=$this->dbEnglish->insert_id();
            $this->dbEnglish->set('essayID', $abbreviation.$lastID);
            $this->dbEnglish->where('srno',$lastID);
            $this->dbEnglish->update('essayMaster');
            //inserting into teacherEssayActivation
            $activationData = array(                    
                    'essayID'  => $abbreviation.$lastID,
                    'activationDate' => $startDate,
                    'deactivationDate' => $endDate,
                    'class' => $grade,
                    'section' => $section,
                    'schoolCode' => $schoolCode,
            );
            $this->dbEnglish->insert('teacherEssayActivation', $activationData);
            return $this->dbEnglish->insert_id();
        }
        /**
	 * function description : Get Abbreviation of school. This is need to create topic id. E.g ON123, GA432.
	 * @return  return Abbreviation of school. if not offline school then return ON
	 * 
	 * */
        function getAbbreviationOfSchool() {
            $this->dbEnglish->Select('abbreviation', false);
            $this->dbEnglish->from("adepts_offlineSchools");
            $this->dbEnglish->where('schoolCode',$this->school_code); //need to check.
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            if (isset($userDataArr[0]) && $userDataArr[0]['abbreviation'] != "") {
                return $userDataArr[0]['abbreviation'];
            } else {
                return 'ON';
            }
        }
        /**
	 * function description : This function will deactivate essay with topic/Essay ID and checking if the activationDate is greater than current date  if the topic will be shown in future and then teacher deactivate it then we are setting isActive to 2 instead of 0. and isActive 2 will not show in teacher interface.
	 * param1   EssayID
	 * @return  return true/false
	 * 
	 * */
        function deactivateOrHideExistingEssay($essayID){
            $updated=false;
                foreach($essayID as $id){                    
                    $this->dbEnglish->Select('activationDate', false);
                    $this->dbEnglish->from("teacherEssayActivation");
                    $this->dbEnglish->where('essayID',$id['essayID']);
                    $query = $this->dbEnglish->get();
                    $essayData = $query->result_array();
                    $isActive = ($essayData[0]['activationDate']>date('Y-m-d')) ? 2 : 0; 
                    $this->dbEnglish->set('forceDeactivateDate',date('Y-m-d H:i:s'));
                    $this->dbEnglish->set('isActive',$isActive);
                    $this->dbEnglish->where('essayID',$id['essayID']);
                    $updated= $this->dbEnglish->update('teacherEssayActivation');
                }
            return $updated;
        }
        
        /**
	 * function description : This function will show the primary class and section of requested teacher/user
	 * param1   UserID
         * param2   Class = by default false
	 * @return  return class if no class param pass. return section if class pass.
	 * 
	 * */
        function getPrimaryClassAndSection($userID,$class){
            $this->dbEnglish->Select('childClass,childSection', false);
            $this->dbEnglish->from("teacherClassMapping");
            if($class!=false){
                $this->dbEnglish->where('childClass', $class);
            }
            $this->dbEnglish->where('userID', $userID);
            $this->dbEnglish->where('isPrimaryClass', '1');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            $classArr=array();
            foreach ($userDataArr as $key => $cls_section) {
                 if ($class != false) {
                    $classArr[] = $cls_section['childSection'];
                } else {
                    $classArr[] = $cls_section['childClass'];
                }
            }
            $classArr= array_unique($classArr);
            sort($classArr);
            return $classArr;
        }
        /**
	 * function description : This function will show the topic name and submission on that topic which created by Teacher.
	 * param1   UserID
         * param2   Class
         * param3   section
         * param4   schoolCode
	 * @return  return essay id,srno,title,activation date, deactivation date, class, section, schoolcode,userid,total reviewed and total pending in array
	 * 
	 * */
        function topicsAssignByTeacher($userID,$childClass,$childSection,$schoolCode){
            $this->dbEnglish->Select('em.essayID, em.srno, em.essayTitle, tea.activationDate, tea.deactivationDate, tea.forceDeactivateDate,tea.isActive, tea.class, tea.section, tea.schoolCode, em.userID', false);
            $this->dbEnglish->from("teacherEssayActivation tea");
            $this->dbEnglish->join('essayMaster em','tea.essayID = em.essayID','INNER');
            $this->dbEnglish->where('tea.class',$childClass);
            $this->dbEnglish->where('tea.section',$childSection);	
            $this->dbEnglish->where('tea.schoolCode',$schoolCode);
            $this->dbEnglish->where('tea.isActive !=',2);
            $this->dbEnglish->where('em.userID',$userID);
            $this->dbEnglish->order_by('tea.srno DESC');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            
            foreach ($userDataArr as $key => $valueArr) {
                $userDataArr[$key]['activationDate']=date('d-m-Y', strtotime($valueArr['activationDate']));
                $userDataArr[$key]['deactivationDate']=date('d-m-Y', strtotime($valueArr['deactivationDate']));
                if($valueArr['deactivationDate'] < date('Y-m-d')){
                    $userDataArr[$key]['isActive']=0;
                }
                if(isset($valueArr['forceDeactivateDate'])){
                    $userDataArr[$key]['deactivationDate']=date('d-m-Y g:i A', strtotime($valueArr['forceDeactivateDate'])); // checking if forcefully deactivated then we are replacing deactivate with forceDeactivateDate so the teacher will be able to see the when that topic was deactivated.
                }
                //total submission. Teacher topic will be always unique, So No required to add class,section & school conditions.
                $this->dbEnglish->select('IFNULL(SUM(IF(ed.status=1,1,0)),0) pending ,IFNULL(SUM(IF(ed.status!=0,1,0 )),0) submission',FALSE);
		$this->dbEnglish->from('ews_essayDetails ed');
                $this->dbEnglish->where('ed.topicID',$valueArr['essayID']);
                $totalQuery = $this->dbEnglish->get();
                $totalQueryResult = $totalQuery->result_array();
                $userDataArr[$key]['submission']=$totalQueryResult[0]['submission'];
                $userDataArr[$key]['pending']=$totalQueryResult[0]['pending'];
                //end
            }
            return $userDataArr;
        }
        /**
	 * function description : This show the student name with the status of their submission on requested topic id.
	 * param1   topic id
         * param2   Class
         * param3   section
	 * @return  return scoreid, essayid, topic id, userid,username,submitted date, status, and review/pending in array
	 * 
	 * */
        function getSubmissionByTopic($topicID,$childclass,$childsection){
            $schoolCode=$this->school_code;            
            $userID=$this->user_id;
            $this->dbEnglish->Select('es.scoreID,ed.essayID,ed.topicID, ed.userID, ud.childName, ed.submittedOn, ed.status', false);
            $this->dbEnglish->from("ews_essayDetails ed");
            $this->dbEnglish->join('userDetails ud','ed.userID = ud.userID','INNER');
            $this->dbEnglish->join('ews_essayScoring es','es.essayID = ed.essayID','INNER');
            $this->dbEnglish->where('ed.status !=','0');
            $this->dbEnglish->where('ed.topicID',$topicID);
            $this->dbEnglish->where('ud.childClass',$childclass);
            $this->dbEnglish->where('ud.childSection',$childsection);
            $this->dbEnglish->where('ud.schoolCode',$schoolCode);
            $this->dbEnglish->order_by('ud.childName ASC');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            foreach ($userDataArr as $key => $valueArr) {
                $userDataArr[$key]['submittedOn']=date('d-m-Y', strtotime($valueArr['submittedOn']));
                if($valueArr['status']==1){
                    $userDataArr[$key]['status']='Pending';
                }elseif($valueArr['status']==2){
                    $userDataArr[$key]['status']='Reviewed';
                }
            }
            return $userDataArr;
        }
         /**
	 * function description : This show the student name with the status of their submission on requested topic id.
	 * param1   topic id
         * param2   Class
         * param3   section
	 * @return  return scoreid, essayid, topic id, userid,username,submitted date, status, and review/pending in array
	 * 
	 * */
        function getSubmissionByStudents($topicID,$childclass,$childsection){
            $schoolCode=$this->school_code;                        
            $studentsArrs=$this->getStudentDetailsBySection($schoolCode,$childclass,$childsection,"assoc");
            if(!empty($studentsArrs)){
            $studentsArr=$this->array_column($studentsArrs, 'userID');
            $this->dbEnglish->Select('es.scoreID,ed.essayID, ed.topicID, ud.userID, ud.childName, ed.submittedOn,IFNULL(ed.status, 0) AS status, IF( ed.status>0,"Submitted","Not Submitted") AS submitted', false);
            $this->dbEnglish->from("userDetails ud");
            $this->dbEnglish->join('ews_essayDetails ed','ed.userID = ud.userID AND ed.topicID ="'.$topicID.'"','LEFT');
            $this->dbEnglish->join('ews_essayScoring es','es.essayID = ed.essayID','LEFT');
            $this->dbEnglish->where_in('ud.userID',$studentsArr);
            $this->dbEnglish->order_by('ud.childName ASC');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            }else{
                $userDataArr=array('noUserFound'=>true);
            }
            return $userDataArr;            
        }
        /**
	 * function description : This function will show the essay list which chosen by student
	 * param1   class
         * param2   section
	 * @return  return topic name, and total count of submission on that topic.
	 * 
	 * */
        function getEssayChosenByStudent($childclass,$childsection){
            $userID=$this->user_id;
            $schoolCode=$this->school_code;
             $this->dbEnglish->Select('COUNT(ed.topicID) AS \'submissions\',em.essayTitle, ed.essayID, ed.topicID', false);
            $this->dbEnglish->from("ews_essayDetails ed");
            $this->dbEnglish->join('userDetails ud','ed.userID = ud.userID','INNER');
            $this->dbEnglish->join('essayMaster em','ed.topicID = em.essayID','INNER');
            $this->dbEnglish->join('ews_essayScoring es','es.essayID = ed.essayID','INNER');
            $this->dbEnglish->where('ud.childClass',$childclass);
            $this->dbEnglish->where('ud.childSection',$childsection);
            $this->dbEnglish->where('ed.status !=','0');
            $this->dbEnglish->where('em.userID !=',$userID);
            $this->dbEnglish->where('ud.schoolCode',$schoolCode);
            $this->dbEnglish->where('ud.category','STUDENT');
            $this->dbEnglish->where('es.evaluatorID',$userID);
            $this->dbEnglish->group_by('ed.topicID');
            $this->dbEnglish->order_by('ed.submittedOn DESC');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            return $userDataArr;
        }
        /**
	 * function description : This function return the essayid,title and deactivate date which come between start date and end date.
	 * param1   class
         * param2   section
         * param3   start date
         * param4   end date
	 * @return  return essayID,  title, deactivation date in array
	 * 
	 * */
        function getActiveTopicWithinRange($childclass,$childsection,$startDate,$endDate){
            $userID=$this->user_id;
            $schoolCode=$this->school_code;
            $this->dbEnglish->Select('tea.essayID, em.essayTitle, tea.deactivationDate', false);
            $this->dbEnglish->from("teacherEssayActivation tea");
            $this->dbEnglish->join('essayMaster em','tea.essayID = em.essayID','INNER');
            $this->dbEnglish->where('(tea.activationDate BETWEEN "'.$startDate.'" AND "'.$endDate.'" OR 
tea.deactivationDate BETWEEN "'.$startDate.'" AND "'.$endDate.'" OR "'.$startDate.'" BETWEEN tea.activationDate AND tea.deactivationDate)');
            $this->dbEnglish->where('tea.isActive','1');
            $this->dbEnglish->where('tea.class',$childclass);
            $this->dbEnglish->where('tea.section',$childsection);
            $this->dbEnglish->where('tea.section',$childsection);
            $this->dbEnglish->where('tea.schoolCode',$schoolCode);
            $this->dbEnglish->where('em.userID',$userID);
            $this->dbEnglish->order_by('tea.deactivationDate ASC');
            $query = $this->dbEnglish->get();
            $userDataArr = $query->result_array();
            if(isset($userDataArr[0]) && !empty($userDataArr)){
                $userDataArr[0]['deactivationDate']= date("d-m-Y", strtotime($userDataArr[0]['deactivationDate']));
            }else{
                $userDataArr='';
            }
           
            return $userDataArr;
        }
        

}	
