<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comment_model extends CI_Model {
  private $table_user_comments;
  private $table_userCommentDetails;
  private $table_questions;

  public function __construct() {
    parent::__construct();
    $this->table_user_comments = TBL_USER_COMMENTS;
    $this->table_questions = TBL_QUESTIONS;
    $this->table_userCommentDetails = TBL_USER_COMMENT_DETAILS;
    $this->load->model('user_model');    

  }

  public function get_user_comments($user_id, $limit=0) {
     $data = $this->get_comment_list($user_id, $limit);
     if(!empty($data))
     {
      return set_model_response(1,'','success',$data);
     }
     else
      {
        return set_model_response(0,'COMMENT001','No comment to fetch');
      }
     
  }

  public function get_comment_list($user_id,$limit) {
   $comment_list = array();
   $query = "SELECT srno, DATE_FORMAT(DATE(lastModified), '%d-%m-%Y') as comment_date, viewed, category, rating, qcode, questionNo, sessionID FROM $this->table_user_comments WHERE userID=$user_id AND status='Closed'";
   if($limit>0) {
    $query .= " LIMIT $limit";
   }

   $result = $this->db->query($query);
   $result = $result->result_array();
   $seq_no = 1;
   $fields = array('child_name', 'child_class');
   $user_details = $this->user_model->get_ms_user_details($user_id, $fields); 

   foreach ($result as $user_comments_row) {
    $comment['sequence_no'] = $seq_no++;
    $comment_srno = $user_comments_row['srno'];
    $comment_id = $this->create_comment_id($user_comments_row);
    $comment['comment_id'] = $comment_id;
    $comment['qcode'] = $user_comments_row['qcode'];
    $comment['qtext'] = $this->get_question_text($user_comments_row['qcode']);    
    $comment['comment_trail'] = $this->get_comment_trail($comment_srno, $user_id, $user_details['child_name'], $user_details['child_class']);
    $comment_list['comments'][] = $comment;
   }
   return $comment_list;
  }

  private function create_comment_id($user_comments_row) {
    return $user_comments_row['sessionID']."-Que:".$user_comments_row['questionNo']; 
  }

  private function get_question_text($qcode) {
    //TODO this should be a part of question_model
    $question_text = ""; 
    $query = "SELECT question FROM $this->table_questions WHERE qcode = $qcode";
    $result = $this->db->query($query);
    if($result->num_rows >0) {
      $resultArr = $result->row_array();
      $question_text = $resultArr['question'];
    }

    return $question_text;
  }

  public function get_comment_trail($comment_srno, $user_id, $child_name, $child_class) {

    $comment_trail = array();
    $query = "SELECT srno,comment,image,DATE_FORMAT(commentDate, '%M %e, %Y %h:%i %p') as commentDate,commenter,flag FROM $this->table_userCommentDetails WHERE srno=$comment_srno";

    $result = $this->db->query($query);
    if($result->num_rows() > 0)
    {
      $comment_array = $result->result_array();
      foreach ($comment_array as $comment_row) {   

        $comment_details['displayName'] = $this->get_name_to_display($child_name, $child_class, $comment_row['flag']);
        $comment_details['commentDate'] = $this->get_comment_date($comment_row['commentDate'], $comment_row['comment']);
        $comment_details['commentText'] = $this->get_comment_text_from_row($comment_row['comment']);
        $comment_trail[] = $comment_details;
      }
    }

    return $comment_trail;
  }

  private function get_name_to_display($child_name, $child_class, $flag) {
    $first_name = $this->extract_first_name($child_name);
    if($flag == 1 || $flag == 3) {
      $display_name = $first_name;
    } else {
      if($child_class < 8) {
        $display_name = "Sparkie";
      } else {
        $display_name = "Mindspark";
      }
    }
    return $display_name;
  }

  private function extract_first_name($child_name) {
    $name_arr = explode(" ", $child_name);
    return $name_arr[0];
  }

  private function get_comment_text_from_row($comment) {
    $comment_text = "";
    $comment_arr = explode("~", $comment);
    if(count($comment_arr) > 1) {
      $comment_text = $comment_arr[count($comment_arr)-1];      
    } else {
      $comment_text = $comment_arr[0];
    }
    return $comment_text;    
  }

  private function get_comment_date($comment_date, $comment) {
    $comment_arr = explode("~", $comment);
    if(count($comment_arr) > 1) {
      $comment_text = $comment_arr[count($comment_arr)-1];
      $comment_date_arr = explode("::", $comment_text);
      $comment_date = $comment_date_arr[1];
      //TODO DRY for condition check - why split on '~'?
    } 
    return $comment_date;
  }
}