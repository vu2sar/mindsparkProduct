<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Notice_model extends CI_Model {
	
    private $table_noticeBoardComments;
    private $subjectno;
    public function __construct() {
        parent::__construct();
        $this->table_noticeBoardComments = TBL_NOTICE_BOARD_COMMENTS;
        $this->subjectno = SUBJECTNO;
    }
    public function notice_board_message_count($userID,$school_code,$child_class,$child_section,$count)
    {

        $query = "SELECT  srno as notice_id,comment as notice_details,DATE_FORMAT(`date`,'%e-%m-%Y') as notice_date,DATE_FORMAT(`date`,'%W') as notice_day   FROM $this->table_noticeBoardComments WHERE subjectno = $this->subjectno AND schoolCode=$school_code AND (class='$child_class' OR class='All')";
        if($child_section!="")
            $query .= " AND (section='$child_class' OR section='All')";
        $query .= " AND datediff(curdate(),date) < noOfDays ORDER BY srno";
        $result = $this->db->query($query);
        $num_rows = $result->num_rows();
        if($count)
        {
            return $num_rows;
        }
        else
        {
            
            $data = $result->result_array();
            
            return $this->get_notice_board_data($data);
            
        }
    }

    public function get_notice_board_data($data=array())
    {
        if(!empty($data))
        {   
            $get_response =  set_model_response(1,1,'success',$data);
            return $get_response;
        }
        else
        {
            $get_response =  set_model_response(0,'NOTICE001','No data to display in notice board',$data);
            return $get_response;
        }
    }

}