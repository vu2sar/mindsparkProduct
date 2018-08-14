<?php

namespace BLL;
use DAL;
require_once 'BaseClass.php';
require_once DIR_DAL . 'ParentFeedback.php';

class ParentFeedback extends BaseClass {

    public $parentUserID;
    public $studentID;
    public $subject;
    public $message;

    public function __construct($db) {
        $this->db = $db;
        $this->parentUserID = null;
        $this->studentID = null;
        $this->subject = null;
        $this->message = null;
    }

    public function addParentFeedback() {
        $parentFeedback = new DAL\ParentFeedback($this->db);
        return $parentFeedback->addParentFeedback($this->parentUserID, $this->studentID, $this->subject, $this->message);        
    }

}
