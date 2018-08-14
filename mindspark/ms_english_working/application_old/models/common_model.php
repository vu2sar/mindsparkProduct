<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        //Do your magic here
    }

    function getTimeAgo($dateTime) {
        $ptime = strtotime($dateTime);
        $etime = time() - $ptime;
        if ($etime < 1) {
            return '0 seconds';
        }

        $interval = array(12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ($r > 1 ? 's' : '');
            }
        }
    }

    public function set_response_array($eiCode, $eiErrorCode, $eiMsg,$data=array()) {
        if($eiCode)
        {
            $responseArr = array(
            'eiCode' => $eiCode,
            'eiMsg'  => $eiMsg
            );

            if(!empty($data))
            {
              $responseArr = array_merge($responseArr,$data);
            }
        }
        else
        {
            $responseArr = array(
            'eiCode' => $eiErrorCode,
            'eiMsg' => $eiMsg
            );
        }
        
        return $responseArr;        
    }
}

/* End of file general_model.php */