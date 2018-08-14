<?php
//sendSMS


function SendSMS($mobile, $msg,$msg_type='', $countrycode='91'){
    if($mobile == "") return;
    $phonetype = "";
    if (substr($mobile,4)=='9193') {$phonetype="CDMA";}
    $request = ""; //initialize the request variable
    $param["user"] = "EI-SMS"; //this is the username of your account signup at http://sms.sms2india.info [^]
    $param["password"] = "3701#471"; //this is the password of your account
    $param["text"] = $msg; 
    //this is the message that we want to send
    $param["PhoneNumber"] = $mobile; //these are the recipients of the message ALONG WITH 91
    $param['countrycode'] = $countrycode;
    $param["sender"] = "EDUIND";//this is your sender which is approved, 

    if ($phonetype=="CDMA"){
        $param["sender"] = "9860609000";//this is your FIXED sender which is approved
    }
    if($countrycode == '+91' || $countrycode == '91')
        $param["gateway"] = 'UES3B2ZX';
    else
        $param["gateway"] = 'NXGYHDJO';
     //traverse through each member of the param array
    foreach($param as $key=>$val){
        $request.= $key."=".urlencode($val); //we have to urlencode the values
        $request.= "&"; //append the ampersand (&) sign after each paramter/value pair
    }

    $request = substr($request, 0, strlen($request)-1); //remove the final ampersand sign from the request
        
    //First prepare the info that relates to the connection
    $host = "bulksms.sms2india.info";
    $script = "/sendsmsv1.php";
    $request_length = strlen($request);
    $method = "POST"; // must be POST if sending multiple messages
    if($method == "GET"){
        $script .= "?$request";
    }

    //Now comes the header which we are going to post.
    $header = "$method $script HTTP/1.1\r\n";
    $header .= "Host: $host\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: $request_length\r\n";
    $header .= "Connection: close\r\n\r\n";
    $header .= "$request\r\n";
    //echo $header;
    if($msg_type==''){
        $trace = debug_backtrace();
        $msg_type= isset($trace[1]['function'])?$trace[1]['function']:pathinfo($trace[0]['file'], PATHINFO_FILENAME);
    }
    # OPENING CONNECTION
    $socket = @fsockopen($host, 80, $errno, $errstr);
    if ($socket){
        fputs($socket, $header); // send the details over
        while(!feof($socket)){
            $output[] = fgets($socket); //get the results
        }
        fclose($socket);
        $message_output = (isset($output) && is_array($output) && isset($output[13]))?$output[13]:"";
        logSMS($mobile, $msg,$msg_type,$message_output);
    }
    return $output;
}

function logSMS($mob,$msg,$msg_type,$msg_output){
    /*include "/home/educatio/public_html/connect.php";
    mysql_select_db ("educatio_adepts", $link)  or die ("Could not SELECT database");*/
    $mobN=explode(",", $mob);
    $query="INSERT INTO smsLog (mobile_no,message,message_type, message_response) VALUES ";
    $mobNumArray=array();
    foreach ($mobN as $key => $mobno) {
        $mobNumArray[]="('$mobno','".addslashes($msg)."','$msg_type', '$msg_output')";
    }
    if (count($mobN)>0){
        $query.=implode(",", $mobNumArray);
        mysql_query($query) or die(mysql_error());
    }
}
?>
