<?php

if (!defined('BASEPATH'))
		exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
	private $table_teacherTopicMaster;
	private $table_teacherTopicStatus;
	private $table_ttUserCurrentStatus;
	private $subject_no;

	public function __construct() {
		parent::__construct();
	 	$this->load->model('user_model');
	 	$this->load->helper('teachertopic/clsTopicProgress');
	 	$this->table_teacherTopicMaster = 'adepts_teacherTopicMaster';
	 	$this->table_teacherTopicStatus = 'adepts_teacherTopicStatus';
	 	$this->table_ttUserCurrentStatus = 'adepts_ttUserCurrentStatus';
	 	$this->subject_no = SUBJECTNO;
	}

	public function get_user_topic_details($user_id) {
		
		$topic_details_arr = array();

		$student_type = $this->user_model->get_student_type($user_id);
		$class = $this->user_model->get_user_fields(array('child_class')); 
		if($student_type != "NA") {
			$topics_attempted = $this->get_topics_attempted($user_id, $this->subject_no);
			$topic_details_arr = $this->get_topic_wise_details($topics_attempted, $user_id, $class);
			$response_arr = set_model_response(1,'','success',$topic_details_arr);
		} else {
			$response_arr = set_model_response(0, "DSHB001", "User is not a student");			
		}
		return $response_arr;
	}

	public function get_topics_by_user($user_id) {

		return $this->get_topics_attempted($user_id, $this->subject_no);
	}

	public function get_user_topics($user_id, $student_type)
	{
		switch ($student_type) {
			case 'SCHOOL':
					$topic_list = get_topics_for_school_user($user_id);
				break;
			
			case 'RETAIL':
					$topic_list = get_topics_for_retail_user($user_id);
				break;

			case 'GUEST':
					$topic_list = get_topics_for_guest_user($user_id);
				break;

			case 'HOME_CENTER':
					$topic_list = get_topics_for_school_user($user_id);
				break;

			default:
					$topic_list = array();
				break;
		}

		return $topic_list;
	}

	public function get_topics_for_school_user($user_id) {
		
		$teacherTopics = array();
		$userDetailsArr = $this->user_model->get_user_details($user_id, array("child_class", "child_section", "school_code", "package"));

		$child_class = $userDetailsArr['child_class']; 
		$child_section = $userDetailsArr['child_section']; 
		$school_code = $userDetailsArr['school_code']; 
		$subjectno = SUBJECTNO; //TODO import constants.php?
		$package = $userDetailsArr['package']; 
		$showAllTopics = 0; //TODO
		$programMode="";

		$query = "SELECT teacherTopicDesc as description, a.teacherTopicCode as ttcode, activationDate, deactivationDate, priority 
				  FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
				  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND a.schoolCode='$school_code' AND b.live=1";
		
		if($child_class != "") {
			$query .= " AND a.class =$child_class";
		}
		if($child_section != "") {
			$query .= " AND a.section ='$child_section'";
		}		
		$query .= " UNION ";
		$query .= "SELECT teacherTopicDesc, a.teacherTopicCode , activationDate , deactivationDate   , priority 
				  FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
				  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND userID=$userID AND b.live=1 ORDER BY priority,deactivationDate DESC";
				  
		// $result = mysql_query($query) or die(mysql_error());
		$result = $this->db->query($query);
		if($result->num_rows >0) {
			$resultArr = $result->result_array();

			foreach ($resultArr as $line) {
			 	$teacherTopics[$line["ttcode"]]["description"]		=	$line["description"];
				$teacherTopics[$line["ttcode"]]["activationDate"]	=	$line["activationDate"];
				$teacherTopics[$line["ttcode"]]["deactivationDate"]	=	$line["deactivationDate"];
			}
		} else {
			//No topics to display
		}

	}

	//TODO verify name of the function
	public function get_topics_attempted($user_id, $subject_code) {

		$topics_attempted = array();
		$query = "SELECT distinct a.teacherTopicCode as ttcode, a.teacherTopicDesc as ttdesc
		          FROM   $this->table_teacherTopicMaster a, $this->table_teacherTopicStatus b
		          WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=$subject_code AND b.userID=$user_id";

		$result = $this->db->query($query);
		if($result->num_rows() > 0) {
			$result_array = $result->result_array();
			foreach ($result_array as $line) {
				extract($line);
				$topics_attempted[$ttcode] = $ttdesc;
			}
		} 
		return $topics_attempted; 
	}

	public function get_topic_wise_details($topics_attempted, $user_id, $class) {
		$topic_wise_details = array();
		$srno = 0;
		foreach ($topics_attempted as $ttcode => $ttdesc) {
			$total_sdls = 0;
			$cluster_array = array();
			$sdls = array();			

			$progress_details = $this->get_progress_in_topic($ttcode, $class, $user_id, "dashboard"); //TODO
			$last_attempted_on = "";
			$topic_wise_details[$ttcode]['description'] = $ttdesc;
			$topic_wise_details[$ttcode]['progress'] = $progress_details['progress'];
			$topic_wise_details[$ttcode]['higher_level'] = $progress_details['higher_level'];
			$topic_wise_details[$ttcode]['last_attempted'] = $last_attempted_on;
			$topic_wise_details[$ttcode]['no_of_attempts'] = $this->get_no_of_attempts_by_topic($user_id, $ttcode); //TODO
		}
		return $topic_wise_details;		
	}

	public function get_progress_in_topic($ttcode, $class, $user_id, $mode = "") {
		$progress_details = array();
		$progress_details['progress'] = '';	
		$this->db->select('flow,MAX(progress)');
		$this->db->distinct();
		$result = $this->db->get_where($this->table_teacherTopicStatus, array('userID' => $user_id, 'teacherTopicCode' => $ttcode));

		if($result->num_rows() > 0) {
			$row_array = $result->row_array();
			$flow = $row_array['flow'];
			$obj_topic_progress = new clsTopicProgress($ttcode, $class, $flow, $this->subject_no);
			if($mode == 'progress')
				$progress_details['progress'] = $this->get_progress_from_current_status($ttcode, $user_id); 
			else 
				$progress_details['progress'] = $obj_topic_progress->getProgressInTT($user_id); 

			$progress_details['higher_level'] = $obj_topic_progress->higherLevel;
			$progress_details['noofsdls'] = $obj_topic_progress->totalSDLs;
		} 
		return $progress_details;
	}

	public function get_progress_from_current_status($ttcode, $user_id) {
		$progress = array();
		$result = $this->db->get_where($this->table_ttUserCurrentStatus, array('progressUpdate' => 0, 'teacherTopicCode' => $ttcode, 'userID' => $user_id));
		if($result->num_rows() > 0) {
			$progress = $result->row_array();		
		}

		return $progress;
	}

	public function get_no_of_attempts_by_topic($user_id, $ttcode) {
		$no_of_attempts = 0;
		$this->db->select('count(ttAttemptID)');
		$result = $this->db->get_where($this->table_teacherTopicStatus, array('userID' => $user_id, 'teacherTopicCode' => $ttcode));
		$line = $result->row_array();
		$no_of_attempts = $line['count(ttAttemptID)'];
		return $no_of_attempts;
	}
}

/* End of file dashboard_model.php */