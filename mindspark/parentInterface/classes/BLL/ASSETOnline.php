<?php

namespace BLL;

use DAL;

require_once 'BaseClass.php';
require_once DIR_DAL . 'ASSETOnline.php';

class ASSETOnline extends BaseClass {

    public $srno;
    public $username;
    public $password;
    public $fatherName;
    public $childFirstName;
    public $childLastName;
    public $class;
    public $gender;
    public $schoolName;
    public $cityName;
    public $country;
    public $dob;
    public $phoneMob;
    public $email;
    public $enteredBy;
    public $MSUserID;

    public function __construct($db, $srno = null) {
        $this->db = $db;
        if ($srno != null) {
            $assetOnlineUser = new DAL\ASSETOnline($this->db);
            $data = $assetOnlineUser->getASSETOnlineUser($srno);
            $details = $data[0];
            $this->srno = $details['srno'];
            $this->username = $details['username'];
            $this->password = $details['password'];
            $this->fatherName = $details['fatherName'];
            $this->childFirstName = $details['childName'];
            $this->childLastName = $details['childLastName'];
            $this->class = $details['category'];
            $this->dob = $details['dob'];
            $this->gender = $details['sex'];
            $this->cityName = $details['cityName'];
            $this->gender = $details['gender'];
            $this->schoolName = $details['schoolName'];
            $this->phoneMob = $details['phoneMob'];
            $this->email = $details['email'];
            $this->enteredBy = $details['enteredBy'];
        } else {
            $this->srno = null;
            $this->username = null;
            $this->password = null;
            $this->fatherName = null;
            $this->childFirstName = null;
            $this->childLastName = null;
            $this->class = null;
            $this->dob = null;
            $this->gender = null;
            $this->cityName = null;
            $this->gender = null;
            $this->schoolName = null;
            $this->phoneMob = null;
            $this->email = null;
            $this->enteredBy = null;
        }
    }

    public function getMapping($MSUserID) {
        $found = 0;
        if ($MSUserID != '') {
            $mapping = new DAL\ASSETOnline($this->db);
            $users = $mapping->getUserMapping($MSUserID);
            if (count($users) > 0) {
                $found = $users[0]['assetOnlineUserID'];
            }
        }
        return $found;
    }

    public function insertUser() {
        if ($this->username != '') {
            $user = new DAL\ASSETOnline($this->db);
            $this->username = $this->getUsername($this->username);
            $pan_number = $user->generate_PAN_number();
            $userID = $user->addASSETOnlineAccount($this->getSerialNo() + 1, $this->username, $this->password, $this->fatherName, $this->childFirstName, $this->childLastName, $this->class, $this->gender, $this->schoolName, $this->cityName, $this->country, $this->dob, $this->phoneMob, $this->email, $this->enteredBy, $pan_number);
            $user->insertUserMapping($this->MSUserID, $userID);
            $this->sendEmailRegConfirm($this->email, $this->childFirstName, $this->childLastName, $this->username, $this->password);
//            $this->sendEmailRegConfirm('ruchit.rami@ei-india.com', $this->childFirstName, $this->childLastName, $this->username, $this->password);
            return $this->username;
        }
        return false;
    }

    public function getSerialNo() {
        $user = new DAL\ASSETOnline($this->db);
        return $user->getSerialNo();
    }

    public function getUsername($username, $suffix = 0) {
        if ($this->username != '') {
            $user = new DAL\ASSETOnline($this->db);
            $check = $user->checkUsername($username . ($suffix > 0 ? $suffix : ''));
            if (count($check) > 0) {
                return $this->getUsername($username, ($suffix == '' ? 1 : $suffix + 1));
            } else {
                return $username . ($suffix > 0 ? $suffix : '');
            }
        }
    }

    private function sendEmailRegConfirm($email, $childName, $childLastName, $username, $password) {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            $eol = "\r\n";
        } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
            $eol = "\r";
        } else {
            $eol = "\n";
        }
        $subject = "ASSETOnline Registration";

        //$headers  = "MIME-Version: 1.0\r\n";
        $headers = "From: ASSETOnline<assetonline@ei-india.com>$eol";
        //$headers.= "Bcc: assetonline@ei-india.com\r\n";
        $headers.= "Content-type: text/html; charset=iso-8859-1$eol";

        $message = "<img src='http://www.assetonline.in/asset_online/images/asset-logo.gif' width='175' height='41'>";
        $message.="<br><br><br>";
        $message.="Dear <b>$childName  $childLastName,</b><br><br>";
        $message.="Thank you for registering with <a href='http://www.assetonline.in'>ASSETOnline</a>.<br>";

        $message.="Your Username:<b>$username</b><br>";
        $message.="Your Password:<b>$password</b><br><br>";

        $message.="Wish you all the best!<br><br>";
        $message.="With kind regards<br><br>";
        $message.="ASSETOnline Team";
        $message.="<br><br><br>";
        $message.="<a href='http://www.assetonline.in'>";
        $message.="<img src='http://www.assetonline.in/asset_online/images/asset-logo.gif' width='175' height='41' border='0' align='top'>";
        $message.="<img src='http://www.assetonline.in/asset_online/images/test_to_improve.jpg' width='144' height='32' border='0' align='top'>";
        $message.="</a>";

        //echo $headers."<br>";
        //echo $message."<br>";

        mail($email, $subject, $message, $headers);
    }

}
