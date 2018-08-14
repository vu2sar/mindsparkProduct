<?php

function get_ip_address() {
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        $ip = trim($ip); // just to be safe

                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            return $ip;
                        }
                    }
                }
            }
        }

function saveParentStatus($parentEmail,$provider,$students,$startTime){
	$IPaddress = get_ip_address();
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$query	=	"INSERT into adepts_parentSessionStatus SET openID='$parentEmail', provider='$provider', IPaddress='$IPaddress', userAgent='$userAgent',students='$students', startTime='$startTime'";
	$result = mysql_query($query);
	$sessionID = mysql_insert_id();
	$_SESSION['sessionID']=$sessionID;
}
?>