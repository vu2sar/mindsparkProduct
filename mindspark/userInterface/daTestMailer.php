<?php
function mailSuperTestReport($userID, $daPaperCode)
{
  
        $sqUser = "SELECT IFNULL(pd.username,ud.parentEmail),ud.childName, IFNULL(pd.phoneNumber,ud.contactno_cel)
                    FROM adepts_userDetails ud 
                        LEFT JOIN parentChildMapping pc ON ud.userID=pc.childUserID LEFT JOIN parentUserDetails pd ON pc.parentUserID=pd.parentUserID
                    WHERE ud.userID=$userID";
        $rsUser = mysql_query($sqUser);
        $parentMail=array();
        $parentMob=array();
        while($rwUser = mysql_fetch_array($rsUser)){
            array_push($parentMail, $rwUser[0]);
            $par= explode(",", $rwUser[2]);
            foreach ($par as $key => $parMob) {
                $parMob=getCleanMobileNumber($parMob);
                if ($parMob!=-1) array_push($parentMob, $parMob);
            }
            $childName = ucwords(strtolower($rwUser[1]));
        }
        $today = date("d / m / Y");
        $parentMail=implode(",", $parentMail);
        $parentMob=implode(",", $parentMob);

        $sq	=	"SELECT COUNT(id), SUM(R), SUM(IF(A = '', 1,0)) AS unAnswered, paperCode FROM da_questionAttemptDetails WHERE userID=$userID AND paperCode='$daPaperCode'";
        $rs	=	mysql_query($sq) or die(mysql_error().$sq);
            
        $rw = mysql_fetch_array($rs);	
        $total = $rw[0];
        $correct = $rw[1];
        $unAnswered = $rw[2];
		
		$sqDaTopic	=	"SELECT topicName, 
            IF(ISNULL(weekNumber) AND testStartDate!='0000-00-00',testStartDate,IF(!ISNULL(weekNumber),STR_TO_DATE(CONCAT(YEAR(CURDATE()),((weekNumber+11)%52),' Friday'), '%X%V %W'),'')) testSDate, 
            IF(ISNULL(weekNumber) AND testStartDate!='0000-00-00',testEndDate,IF(!ISNULL(weekNumber),STR_TO_DATE(CONCAT(YEAR(CURDATE()),((weekNumber+11)%52)+1,' Sunday'), '%X%V %W'),'')) testEDate 
            FROM da_paperCodeMaster WHERE paperCode='$daPaperCode'";
        $rsDaTopic	=	mysql_query($sqDaTopic);
        $rwDaTopic = mysql_fetch_array($rsDaTopic);
        $daTopicName = 	$rwDaTopic['topicName'];
        $testSDate = getReadableDate($rwDaTopic['testSDate']);
        $testEDate = getReadableDate($rwDaTopic['testEDate']);
        	
        list($bestArea,$worstArea) = getPerformanceArea($daPaperCode,$userID);
    	$scoreCard = "";
    	$scoreCard = "Dear Parent,<br><br>Congratulations! ".$childName." has successfully completed the latest set of Mindspark Super Test.<br>We would like to take this opportunity to thank you for encouraging your child to participate.<br><br>";
    	
    	$scoreCard .= "<b>Super test score: </b>".round(($correct/$total)*10,1)."/10<br><br><b>Correct: </b>".$correct."/".$total."<br><br><b>Incorrect: </b>".($total - ($correct+$unAnswered))."/".$total."<br><br><b>Unanswered: </b>".$unAnswered."/".$total."<br><br><b>Best Performed Area: </b><br>".$bestArea."<br><br><b>Area(s) for improvement: </b><br>".$worstArea."<br><br>";
    	
    	$scoreCard .= "<img src='http://educationalinitiatives.com/mindspark/3rd_FB_Post_for_Super_Test.jpg' width='500px' height='500px'><br/><br/>Please share your feedback regarding the test feature.<br/>If you have any queries related to the test, do write to mindspark@ei-india.com or call us on 09725777541.<br><br>Thanks & Regards,<br/>Team Mindspark<br>www.mindspark.in<br/>www.facebook.com/mindspark.ei<br/>http://blog.mindspark.in/<br><br>";
        
        if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
          $eol="\r\n";
        } elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
          $eol="\r";
        } else {
          $eol="\n";
        }	

    	$subject = 'Super Test on '.$daTopicName.' - '.$childName."'s Scorecard";
    	$headers = "From:<mindspark@ei-india.com>".$eol;
    	$to = trim($parentMail);        	
    	$headers .= "Bcc:  saloni.arora@ei-india.com,shashanka.kundu@ei-india.com".$eol;
    	$headers .= "Reply-To:mindspark@ei-india.com".$eol;
    	$headers .= 'MIME-Version: 1.0'.$eol;
    	
    	$finalOutput = "<div align='left' style='font-family:Arial'>$scoreCard</div>";
    	$finalOutput = wordwrap($finalOutput,70);
            /*echo $subject;
            echo "<br>";
            echo $finalOutput;        	*/
        if ($correct==$total){
            require("functions/fpdf/fpdf.php");
            // fpdf object
            $pdf = new FPDF('L','mm','Letter');
                // generate a simple PDF (for more info, see http://fpdf.org/en/tutorial/)
                $pdf->SetAuthor("Mindspark - Educational Initiatives Pvt. Ltd.");
                $filename = "$daTopicName Mastery Certificate.pdf";
                $pdf->SetTitle($filename);
                $pdf->AddPage();
                $pdf->Image('assets/superTest_certificate.jpg',0,0,-300,-300);
                $pdf->SetFont("Arial","B",14);
                $pdf->SetXY(40,97);
                $pdf->Cell(120,10, $childName,0,2,"C");
                $pdf->SetXY(80,112);
                $pdf->Cell(100,8, $daTopicName,0,2,"C");
                $pdf->SetXY(96,126);
                $pdf->SetFont("Arial","",12);
                $pdf->Cell(28,8, $testSDate ,0,1,"C");
                $pdf->SetXY(140,126);
                $pdf->SetFont("Arial","",12);
                $pdf->Cell(28,8, $testEDate ,0,2,"C");
                
                // a random hash will be necessary to send mixed content
                $separator = md5(time());
                // carriage return type (we use a PHP end of line constant)
                //$eol = PHP_EOL;
                // encode data (puts attachment in proper format)
                $pdfdoc = $pdf->Output("", "S");
            $attachment = chunk_split(base64_encode($pdfdoc));
            // $inline = chunk_split(base64_encode(file_get_contents('http://educationalinitiatives.com/mindspark/3rd_FB Post_for Super Test.jpg')));

            $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol; 
            $headers .= "Content-Transfer-Encoding: 7bit".$eol;
            $headers .= "This is a MIME encoded message.".$eol.$eol;
            // message
            $headers .= "--".$separator.$eol;
            $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
            $headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
            $headers .= $finalOutput.$eol.$eol;
            
            // attachment
            $headers .= "--".$separator.$eol;
            $headers .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
            $headers .= "Content-Transfer-Encoding: base64".$eol;
            $headers .= "Content-Disposition: attachment".$eol.$eol;
            $headers .= $attachment.$eol.$eol;
            $headers .= "--".$separator."--";
        }
        else 
            $headers .= "Content-type: text/html; charset=iso-8859-1".$eol;
    	if($to!="") {                                    
    		mail($to,$subject,$finalOutput,$headers);
        }

        $childFirstName=getFirstName($childName);
        if ($parentMob!=""){
            include('sendSMS.php');
            $msg="Congratulations! $childFirstName just finished Mindspark Super Test. Score Summary: ".round(($correct/$total)*10,1)."/10  Check Parent Connect for more details. www.mindspark.in/login/";
            //echo "SMS to:".$parentMob;
            //echo "<br>Message:".$msg;
            $s=SendSMS($parentMob,$msg);
            //print_r($s);
        }
        
}
function getReadableDate($date){
    $monthArray=array('01'=>"Jan",'02'=>"Feb",'03'=>"Mar",'04'=>"Apr",'05'=>"May",'06'=>"Jun",'07'=>"Jul",'08'=>"Aug",'09'=>"Sep",'10'=>"Oct",'11'=>"Nov",'12'=>"Dec");
    $dArray=explode("-", $date);
    return $dArray[2].' '.$monthArray[$dArray[1]].', '.$dArray[0];
}
function getFirstName($name){
    $name=explode(" ", $name);$i=0;
    while($i<count($name) && str_replace(" ", "", $name[$i])=="")   $i++;
    return $name[$i];
}
function getCleanMobileNumber($mob){
    $mob=str_replace(" ", "", $mob);
    $mob=str_replace("+91", "", $mob);
    $mob=str_replace("-", "", $mob);
    $mob=preg_replace("/^0+/", "", $mob);
    if (strlen($mob)==10) return $mob;
    else return -1;
}

function getPerformanceArea($daPaperCode,$userID)
{
        $studentworsttopicstr  = $studentbesttopic = "";
	$topicwiseperformance = $StudentWorstTopicArr = array();
	$sqReportingDetails = "SELECT qcode_list, reporting_head FROM educatio_educat.da_reportingDetails WHERE paperCode=$daPaperCode";
	$rsReportingDetails = mysql_query($sqReportingDetails);
	while($rwReportingDetails = mysql_fetch_array($rsReportingDetails))
	{
		$sqPerformance = "SELECT round((SUM(R)/COUNT(id))*100) FROM da_questionAttemptDetails
						  WHERE qcode IN (".$rwReportingDetails[0].") AND papercode=$daPaperCode AND userID=$userID";
		$rsPerformance = mysql_query($sqPerformance);
		$rwPerformance = mysql_fetch_array($rsPerformance);
		$topicwiseperformance[$rwReportingDetails[1]] = $rwPerformance[0];
	}
	$max = 2;
        $i = 1;
	foreach($topicwiseperformance as $topic => $performance) {
		if(is_numeric($performance)){			

			if(count($topicwiseperformance) != 1){ // For only one reporting head we dont need to show top performance
				if($performance > $max && $performance >= 70) {
					$studentbesttopic = $topic;
					$max = $performance;
				}
			}
			if($performance < 70){
				$StudentWorstTopicArr[] = array("srno"=>$i,"topicid"=>$topic,"performance"=>$performance);
			}			
		}
		$i++;
	}
	if(is_array($StudentWorstTopicArr) && count($StudentWorstTopicArr) > 0){

		foreach ($StudentWorstTopicArr as $key => $arrayrow) {
			$srno_arr[$key]  = $arrayrow['srno'];
			$performance_arr[$key] = $arrayrow['performance'];
		}

		array_multisort($performance_arr, SORT_ASC, $srno_arr, SORT_ASC, $StudentWorstTopicArr);

		$worsttopicdispcnt = 0;
		foreach ($StudentWorstTopicArr as $key => $arrayrow) {
			$worsttopicdispcnt++;
			$studentworsttopicstr .="- ".$arrayrow["topicid"]."<br/> ";
			if($worsttopicdispcnt == 2)
			break;
		}
	}
	if($studentworsttopicstr != "")
		$studentworsttopicstr =" <br/>".$studentworsttopicstr;
        else
                $studentworsttopicstr = "<br/>Hurray! We could not find any area for improvement for your child based on this test.";
	if($studentbesttopic != '')
		$bestArea = " <br/>- ".$studentbesttopic;
        else
                $bestArea = " <br/>Oh no! There isn't any best area of performance.";
	return array($bestArea,$studentworsttopicstr);
}
?>