<?php			
	error_reporting(E_ERROR);
	set_time_limit(0);	
	include("/home/educatio/public_html/mindspark/userInterface/dbconf.php");
	include("/home/educatio/public_html/mindspark/userInterface/constants.php");
	include("/home/educatio/public_html/mindspark/userInterface/classes/clsUser.php");
	include_once("/home/educatio/public_html/mindspark/userInterface/functions/orig2htm.php");	
	include_once("/home/educatio/public_html/mindspark/userInterface/classes/clsTeacherTopic.php");
	include_once("/home/educatio/public_html/mindspark/userInterface/classes/clsDiagnosticTestQuestion.php");	
	include("/home/educatio/public_html/mindspark/teacherInterface/functions/topicReportFunctions.php");
	include("/home/educatio/public_html/mindspark/userInterface/functions/functionsForDynamicQues.php");
	include("/home/educatio/public_html/mindspark/teacherInterface/functions/functions.php");	
	mysql_select_db("educatio_adepts");		
	$yesterday = date("Y-m-d", strtotime("yesterday"));
    $i = 0;  
	$q = "SELECT a.schoolCode,a.class,a.teacherTopicCode,a.flow,a.section,b.childEmail,a.lastModifiedBy,deactivationDate from adepts_teacherTopicActivation a JOIN adepts_userDetails b on a.lastModifiedBy=b.childName where a.deactivationDate = '2017-08-11' and b.childEmail != '' and a.schoolCode=2387554";	
    $r = mysql_query($q);    
    while($l = mysql_fetch_array($r))
    {     	 	   
    	$schoolCode = $l[0];    	
    	$class = $l[1];    	
    	$ttCode = $l[2];    	
    	$flow = $l[3];
    	$section = $l[4];  
    	$teacherEmail = $l[5];   
    	$ttObj = new teacherTopic($ttCode,$class,$flow);    	
		$clusters =	$ttObj->getClustersOfLevel($class);		
		$clusterString = implode("','", $clusters);		
		//get cluster detail
		$clusterDetail =  getClusterDetails($clusterString);	
		$coteacherTopicFlag = checkCoteacherTopic($ttCode,$class,$clusters);			
		if($coteacherTopicFlag)
		{		
			$assessmentFlag = checkForAssessment($schoolCode,$class,$clusterString,$clusters);			
			if($assessmentFlag)
			{			
				$userDetails= getStudentDetails($class, $schoolCode, $section);
				$userIDs	= array_keys($userDetails);
				$userIDstr	= implode(",",$userIDs);
				$assessmentData =  getAssessmentDetails($ttCode,$userDetails);
				$questionData = getQuestionsForDiscussion($ttCode,$userIDstr);	
				$topicDetails = getTopicInfo($class,$section,$ttCode,$schoolCode);
				$postfix = $topicDetails['days'] > 1 ? "s" : "";
				if($topicDetails['active'])
				{
					$newDate = date("d M Y", strtotime($topicDetails['activationDate']));				
					$message1 = 'Active Since : ';
					$message2 = $topicDetails['days'] != 0 ? $newDate.' ('.$topicDetails["days"].' day'.$postfix.' ago)' : $newDate;			
				}
				else
				{
					$newDate = date("d M Y", strtotime($topicDetails['deactivationDate']));				
					$message1 = 'Deactivated on : ';
					$message2 = $topicDetails['days'] != 0 ? $newDate.' (was active for '.$topicDetails['days'].' day'.$postfix.' )' : $newDate;
				}
				$assessmentDetails[$i]['header']['name'] = $topicDetails['name'];			
				$assessmentDetails[$i]['header']['message1'] = $message1;
				$assessmentDetails[$i]['header']['message2'] = $message2;
				$assessmentDetails[$i]['header']['classSection'] = $class.$section;
				$assessmentDetails[$i]['header']['email'] = $teacherEmail;
    			$assessmentDetails[$i]['assessmentCompleted']['totalStudents'] = $assessmentData['totalStudents'];
    			$assessmentDetails[$i]['assessmentCompleted']['completedStudents'] = $assessmentData['completedStudents'];
    			$assessmentDetails[$i]['avgAccuracy'] = $assessmentData['avgAccuracy'];
    			if($assessmentData['avgAccuracy'] < 40){
                    $avgAccuracyType = "Low";
                }
                else if($assessmentData['avgAccuracy'] < 80){
                    $avgAccuracyType = "Average";
                }
                else {
                    $avgAccuracyType = "Good";
                }
    			$assessmentDetails[$i]['avgAccuracyType'] = $avgAccuracyType;
				$assessmentDetails[$i]['classAccuracy'] = $assessmentData['classAccuracyReport'];
				foreach ($questionData['cwaDetails'] as $key => $questionCategory) {
					foreach ($questionCategory as $qkey => $value) {						
						$quesDetailsArr = explode("~",$value['qcodeListData']);						
				 		$assessmentDetails[$i]['questions'][$key][$qkey]['data'] = "<div>".getDiagnosticQuestionData($quesDetailsArr[0], $quesDetailsArr[1], $quesDetailsArr[2])."<div>";
				 		$assessmentDetails[$i]['questions'][$key][$qkey]['accuracy'] = $value['accuracy'];
					}				 	
				 }					
				$i++; 
			}					
		}		
    }      
?>	
<?php 
if(!empty($assessmentDetails))
{
	foreach($assessmentDetails as $key => $value) 
	{
		$mailBody = '<style type="text/css">	
		h2
		{
			display: inline;
		}
		.btn-success
		{
			background: #2962FF;
		    border: none;
		    color: #fff;
		    padding: 15px;
		    cursor: pointer;
		}
		.title
		{
			background: #2962FF;
			color: #fff;
			font-size: 20px;
		}
		.low-details
		{
			color: #FD6634;
		}
		.average-details
		{
			color: #FFBF66;
		}
		.high-details
		{
			color: #79E75B;
		}
		.sub-title
		{
			font-size: 16px;
		}
		.correct
		{
			background: #2962FF;
		    color: #fff;
		    padding: 5px;	   
		    border-radius: 20px;
		    text-align: center;
		}
		.assessment-data-card {
		    border: 1px solid #FFB856;
		}
		.progress-card {
		    padding: 16px;	   
		    border-radius: 3px;	
		    margin : 10px 0px;  
		}
		.progress-card .card-heading {
		    font-weight: bold;	  
		    color: #FFB856 	    
		}
		.Low{
	    background: #FEE3D7;
	    color: #FD6634 !important;
	    border : 1px solid;
	    padding: 6px 20px;
	    border-radius: 3px;
	    font-size: 1.3em !important;
	    font-weight: bolder;
	    text-align: center;
		}
		.Average{
		    background: #FFF1DE;
		    color: #FFBF66 !important;
		    border : 1px solid;
		    padding: 6px 20px;
		    border-radius: 3px;
		    font-size: 1.3em !important;
		    font-weight: bolder;
		    text-align: center;
		}
		.Good{
		    background: #E8F9DF;
		    color: #79E75B !important;
		    border : 1px solid;
		    padding: 6px 20px;
		    border-radius: 3px;
		    font-size: 1.3em !important;
		    font-weight: bolder;
		    text-align: center;
		}
		.bold-font
		{
			font-weight: bold;
		}	
		.border-bottom
		{
			border-bottom: 1px solid #a0a0a0;
			display: block;
			height: 5px;
		}
		.padding-bottom
		{
			padding-bottom: 20px;
		}
		.padding-bottom-less
		{
			padding-bottom: 10px;
		}
		.padding-top
		{
			padding-top: 10px;
		}
		</style>';     
		
		$mailBody .= '<table width="100%" border="0" cellspacing="5">
		<tr >
			<td>
				<table width="100%" class="title" cellpadding="15" border="0" cellspacing="5">
					<tr align="center">
						<td><img src="/mindspark/teacherInterface/assets/co-teacher/Mindsparklogo.svg"/></td>
					</tr>
					<tr align="center">
						<td><h3>Mindspark Maths Assessment Report for '.$value['header']['classSection'].'</h3></td>	
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="5" >
					<tr>
						<td width="4%" > Topic : </td><td class="bold-font">'.$value['header']['name'].'</td>
						<td width="4%"> Class : </td><td class="bold-font" width="10%">'.$value['header']['classSection'].'</td>
						<td width="10%">'.$value['header']['message1'].'</td><td class="bold-font">'.$value['header']['message2'].'</td>
					</tr>
				</table>			
			</td>		
		</tr>	
		<tr class="border-bottom"></tr>
		<tr>
			<td>		
				<table width="20%" border="0" class="assessment-data-card progress-card" cellpadding="5">
					<tr >
						<td class="card-heading"><h3>Assessment Completed</h3></td>
					</tr>
					<tr>
						<td><h2>'.$value['assessmentCompleted']['completedStudents'].'</h2>/'.$value['assessmentCompleted']['totalStudents'].' Students</td>
					</tr>
				</table>					
			</td>
		</tr>
		<tr class="border-bottom"></tr>
		<tr><td class="padding-top"> <h2>Class Accuracy </h2></td></tr>
		<tr><td class="'.$value['avgAccuracyType'].'"> '.$value['avgAccuracy'].'% Accuracy </td></tr>
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="5" >
					<tr>
						<td valign="top">
							<table cellspacing="5" class="padding-bottom">
								<tr><td><h2>'.count($value['classAccuracy'][0]).'</h2> Students</td></tr>
								<tr><td class="low-details">LOW(0 - 40%)</td></tr>
							</table>
							<table>';
							foreach($value['classAccuracy'][0] as $lowStudents){
								$mailBody .= '<tr><td>'.$lowStudents['name'].'</td></tr>
								<tr><td class="low-details padding-bottom-less">'.$lowStudents['accuracy'].'%</td></tr>';
								} 							
							$mailBody .= '</table>
						</td>
						<td valign="top">
							<table cellspacing="5"  class="padding-bottom">
								<tr><td><h2>'.count($value['classAccuracy'][1]).'</h2> Students</td></tr>
								<tr><td class="average-details padding-bottom-less">AVERAGE(40 - 80%)</td></tr>
							</table>
							<table>';
							foreach($value['classAccuracy'][1] as $averageStudents){
									$mailBody .= '<tr><td>'.$averageStudents['name'].'</td></tr>
									<tr><td class="average-details padding-bottom-less">'.$averageStudents['accuracy'].'%</td></tr>';
								}	
								
							$mailBody .= '</table>
						</td>
						<td valign="top">
							<table cellspacing="5"  class="padding-bottom">
								<tr><td><h2>'.count($value['classAccuracy'][2]).'</h2> Students</td></tr>
								<tr><td class="high-details">HIGH(80 - 100%)</td></tr>
							</table>
							<table>';
							foreach($value['classAccuracy'][2] as $highStudents){
									$mailBody .= '<tr><td>'.$highStudents['name'].'</td></tr>
									<tr><td class="high-details">'.$highStudents['accuracy'].'%</td></tr>';
								}						
							$mailBody .= '</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>		
		<tr><td><h2>Question for Class Discussion</h2></td></tr>
		<tr><td class="sub-title"><h3>Critical (Accuracy: < 40%)</h3></td></tr>';
		if(isset($value['questions']['critical']))
		{    $i=1;
			foreach($value['questions']['critical'] as $question)
			{			
				$mailBody .= '<tr>
					<td><table width="100%"><tr><td width="2%" valign="top">'.$i.'. </td><td>'.$question['data'].'</td></tr></table></td>
				</tr>
				<tr>
					<td>
						<table width="20%">
							<tr>
								<td class="correct">% correct: '.$question['accuracy'].'</td>
							</tr>
						</table>
					</td>		
				</tr>';
				 $i++;
			}
		} 
		else
		{ 
			$mailBody .= '<tr><td>Looks like your students have a great understanding in this topic as of now. No critical questions found for class discussion !</td></tr>';
		} 
		
		$mailBody .= '<tr><td class="sub-title"><h3>Recommended (Accuracy: 40 - 80%)</h3></td></tr>';
		if(isset($value['questions']['recommended']))
		{	$i=1;
			foreach($value['questions']['recommended'] as $question)
			{			
				$mailBody .= '<tr>
					<td><table width="100%"><tr><td width="2%" valign="top">'.$i.'. </td><td>'.$question['data'].'</td></tr></table></td>
				</tr>
				<tr>
					<td>
						<table width="20%">
							<tr>
								<td class="correct">% correct: '.$question['accuracy'].'</td>
							</tr>
						</table>
					</td>		
				</tr>';
				$i++;
			}
		}
		else
		{ 
			$mailBody .= '<tr><td>No recommended questions found for class discussion !</td></tr>';
		 } 	
		$mailBody .= '<tr><td class="sub-title"><h3>Perfomed Well(Accuracy: 80 - 100%)</h3></td></tr>';
		if(isset($value['questions']['performed']))
		{	$i=1;
			foreach($value['questions']['performed'] as $question)
			{
				$mailBody .= '<tr>
					<td><table width="100%"><tr><td width="2%" valign="top">'.$i.'. </td><td>'.$question['data'].'</td></tr></table></td>
				</tr>
				<tr>
					<td>
						<table width="20%">
							<tr>
								<td class="correct">% correct: '.$question['accuracy'].'</td>
							</tr>
						</table>
					</td>		
				</tr>';
				$i++;
			}
		} 
		else
		{ 
			$mailBody .= '<tr><td>We have not found any questions where students have performed well.</td></tr>';
		}
		$mailBody .= '<tr class="border-bottom"></tr>
		<tr><td class="sub-title"><h3>Assessment not completed</h3></td></tr>
		<tr><td><h2>'.count($value['classAccuracy'][3]).'</h2> Students</td></tr>';
		
		$i = 1;
		foreach($value['classAccuracy'][3] as $incompleteStudents)
		{
				$mailBody .= '<tr><td>'.$i.". ".$incompleteStudents['name'].'</td></tr>';					
			$i++ ;
			 } 	
		$mailBody .= '<tr align="center">
			<td><button class="btn btn-success" onclick="window.open(\'https://www.mindspark.in/mindspark/login/\',\'_blank\')">Go to Mindspark now!</button></td>
		</tr>
		<tr align="center"><td>We\'d love to hear from you. Write to us at <a href="mailto:mindspark@ei-india.com" >mindspark@ei-india.com</a></td></tr>
		</table>';	
		$emailID = $value['header']['email'];
		$emailID = 'preksha.shah@ei-india.com';
		$subject = "Mindspark Maths Assessment Report for".$value['header']['classSection'];		
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";		
		$headers .= "From:<mindspark@ei-india.com>\r\n";
		$send_mail = mail($emailID, $subject, $mailBody, $headers);
	} 
	
}
?>
