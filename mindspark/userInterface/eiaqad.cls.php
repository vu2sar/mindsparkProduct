<?php
if(strcasecmp ( $user->category, "Teacher" ) == 0 || strcasecmp ( $user->category, "School Admin" ) == 0){
	function getAQADExplaination($date, $class, $userID=0, $isExpOfDay=0)
	{
		$sq = "SELECT * FROM educatio_educat.aqadExplaination WHERE date='$date' AND class=$class AND ".($userID>0?" studentID=$userID ": " IsExplainationOfDay=$isExpOfDay " )  ." ;";
		$rs = mysql_query($sq) or die($sq.mysql_error());
		$rw = mysql_fetch_array($rs);
		return $rw;
	}
	
	function getSystemAQADExplaination($date, $class, $userID=0, $isExpOfDay=0)
	{
		$sql = "SELECT explanation FROM educatio_educat.aqad_master WHERE date='$date' AND class=$class";
		$rss = mysql_query($sql);
		$rws = mysql_fetch_array($rss);
		return $rws;
	}
	function calculateAccuracy($Class,$schoolCode,$currentDateToFilter,$currenSection){
		$sql = "SELECT  COUNT(ud.userID) AS totalStudents,COUNT(aqad.id) AS attendedStudents, SUM(aqad.score) AS correctAnsStudents FROM educatio_adepts.adepts_userDetails ud LEFT JOIN educatio_educat.aqad_responses aqad ON ud.userID = aqad.studentId AND DATE(aqad.entered_date)= '$currentDateToFilter' WHERE ud.category = 'STUDENT' AND ud.childClass = $Class AND ud.schoolCode=$schoolCode AND ud.enabled=1 AND ud.endDate>=curDate()";
		if($currenSection != ''){
			$currenSection = str_replace("$", "','", $currenSection);
			$sql .= " AND ud.childSection IN ('$currenSection')";
		}
		$query = mysql_query($sql);
		
		$result = mysql_fetch_assoc($query);
		if($result['correctAnsStudents'] != NULL){
			$accuracy = round($result['correctAnsStudents']/$result['attendedStudents']*100);
		}
		else{
			$accuracy = 0;
		}
		return "<div style='width: 650px; text-align: center;'><span style=' font-weight: bold;'>".$result['attendedStudents']." Out of ".$result['totalStudents']." students attempted this question with an average accuracy of ".$accuracy ." %.</span></div>";
	}	
}
else {
	require_once 'functions/functions.php';	
}

function generateAQADtemplate($day, $cls, $userResponse='',$userID=0,$isTeacher = 0, $classSectionArray = NULL,$schoolCode =NULL) {
    $message = "";
    $qno = 0;
    $subject_arr = array("", "English", "Maths", "Science", "Social Studies");
    $today = date("Y-m-d");
    $current_time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    $passedDateToTime = mktime(0, 0, 0, substr($day, 5, 2), substr($day, 8, 2), substr($day, 0, 4));
    # figure out what 1 days is in seconds
    $one_day = 1 * 24 * 60 * 60;
    $two_day = 2 * 24 * 60 * 60;
    $three_day = 3 * 24 * 60 * 60;
	$four_day = 4 * 24 * 60 * 60;
    $title = "Yesterday's Question";
    # make last day date based on a past timestamp
    if ($cls == "3" || $cls == "4" || $day <= '2010-06-28') {
       	if (date("D", $current_time) == "Mon") {
            $yesterday = date("Y-m-d", ( $current_time - $three_day));
			$dayBeforeYes = date("Y-m-d", ( $current_time - $four_day));
            $title = "Friday's Question";
            $title1 = "Thursday's Question";
        } else {
            $yesterday = date("Y-m-d", ( $current_time - $one_day));
			$dayBeforeYes = date("Y-m-d", ( $current_time - $two_day));
            $title = "Yesterday's Question";
			$title1 = "Day before yesterday's Question";
        }
    } else {
        if (date("D", $current_time) == "Mon") {
            $yesterday = date("Y-m-d", ( $current_time - $two_day));
			$dayBeforeYes = date("Y-m-d", ( $current_time - $three_day));
            $title = "Saturday's Question";
			$title1 = "Friday's Question";
        } else {
            $yesterday = date("Y-m-d", ( $current_time - $one_day));
			$dayBeforeYes = date("Y-m-d", ( $current_time - $two_day));
            $title = "Yesterday's Question";
			$title1 = "Day before yesterday's Question";
        }
    }

	$today_question_query = "SELECT id,papercode,qno FROM aqad_master where date='" . $day . "' and class='" . $cls . "'";
    $resultQuestionQuery = mysql_query($today_question_query) or die(mysql_error());
    $lineQuestionQuery = mysql_fetch_array($resultQuestionQuery);
    $papercode = $lineQuestionQuery['papercode'];
    $qno = $lineQuestionQuery['qno'];
    $srno = $lineQuestionQuery['id'];

	$strqry = "SELECT q.qcode,papercode,class,subjectno,qno,question,optiona,optionb,optionc,optiond,correct_answer
				FROM questions q, paper_master pm WHERE q.qcode=pm.qcode and papercode='" . $papercode . "' AND qno=" . $qno;

    $resultQuery = mysql_query($strqry);
    $line = mysql_fetch_array($resultQuery);

    $grpquery = "SELECT aqad_group_text FROM aqad_grouptext WHERE qcode = '" . $line["qcode"] . "' ";
    $resultgrp = mysql_query($grpquery);
    $grouptext = mysql_fetch_array($resultgrp);
    $class = substr($line['papercode'], 1, 1);

    $round = substr($line['papercode'], 2, 1);

    $subjectno = $line['subjectno'];
    // Appending the group text to the question //
    if ($grouptext != "")
        $question = $grouptext . "<br>" . $line['question'];
    else
        $question = $line['question'];

    $question = str_replace("^", "'", $question);
    $optiona = $line['optiona'];
    $optiona = str_replace("^", "'", $optiona);
    $optionb = $line['optionb'];
    $optionb = str_replace("^", "'", $optionb);
    $optionc = $line['optionc'];
    $optionc = str_replace("^", "'", $optionc);
    $optiond = $line['optiond'];
    $optiond = str_replace("^", "'", $optiond);

    $message.="<br>";
    $message.="<table width=\"60%\"  style=\"border: 1px solid rgb(0, 0, 0); font-family: Arial; font-size: 12pt;\" align=\"center\" bgcolor=\"#ffffff\" border=\"0\" cellspacing=\"0\">";
    $allClassCommonArray = $classArray != NULL ? $classArray : NULL;
    $accuracyToPass = $schoolCode != NULL ? $schoolCode : NULL;
    $classSectionArray = $classSectionArray!= NULL ? $classSectionArray:'""';
    if($isTeacher == 1){
    	$encodedClassSectionArray = json_decode($classSectionArray);
    	if($encodedClassSectionArray != NULL){
    		$accuracyToPass = $schoolCode;
    		$techerClasses = "<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>";
    		foreach($encodedClassSectionArray as $classKey => $sectionValue){
    			if($classKey >=3 && $classKey <10){
    				if($classKey == $cls){
    					$currenSection = $sectionValue;
    				}
    				$CurrenActiveClass = $cls == $classKey ? "active-link" : "" ;
    				$techerClasses .="<span class='day $CurrenActiveClass'  onclick='filterByDate($classKey,\"$day\",$classSectionArray,$isTeacher,$accuracyToPass,\"$userResponse\")'> Class ".$classKey."</span>";    				
    			}
    		}
    		$techerClasses .="<div style='clear:both'></div></div>";
    	}
    	$calander="<input type='text' name='fromDate' class='datepicker floatLeft datepicker-hidden-input' id='".$cls."' autocomplete='off' maxlength='10' size='20' value='".$day."' readonly/>";
    	if((date("D", $passedDateToTime) != "Sun") && ((date("D", $passedDateToTime) != "Sat") || $cls > 4)){
				$accuracy = calculateAccuracy($cls,$schoolCode,$day,$currenSection);
    	}
    	$noteFotTeachers = '<div style="width: 100%; text-align: center;"><span style="color: gray; font-weight: bold; font-size: 12px; display: inline-block; text-align: left; width: 620px;">Note:<br />ASSET Question-a-day are thought provoking questions designed by a team of educational experts at ASSET which aims at providing greater exposure to application oriented questions to students on a daily basis.</span><br /><span style="font-weight: bold; font-size: 13px;width: 620px;">Please encourage students to login to Mindspark, every day at home, and attempt these questions.</span></div><br />';
    }
    $todayActiveClass = $today == $day ? 'active-link':'';
    $yesterdayActiveClass = $yesterday == $day ? 'active-link':'';
    $daybeforeActiveClass = $dayBeforeYes == $day ? 'active-link':'';
	if($cls>4){
		if (date("D", $current_time) == "Tue"){
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day yesterdat-link $yesterdayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$yesterday\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Yesterday</div>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
			
		}else if(date("D", $current_time) == "Mon"){
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day daybefore-link $yesterdayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$yesterday\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Day-before</div>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
		}else{
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day daybefore-link $daybeforeActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$dayBeforeYes\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Day-before</div>
							<div class='day yesterdat-link $yesterdayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$yesterday\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Yesterday</div>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
		}
	}else{
		if (date("D", $current_time) == "Tue"){
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day yesterdat-link $yesterdayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$yesterday\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Yesterday</div>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
		}else if(date("D", $current_time) == "Mon"){
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
		}else{
			$message.="<tr><td colspan=\"3\">
			<div align=center><img src='assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div><br/>
			<div id='dayDiv' style='text-decoration:underline;color:gray;cursor:pointer;width:650px;text-align: center;'>
							<div class='day daybefore-link $daybeforeActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$dayBeforeYes\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Day-before</div>
							<div class='day yesterdat-link $yesterdayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$yesterday\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Yesterday</div>
							<div class='day today-link  $todayActiveClass' id='dayBefore' onclick='filterByDate($cls,\"$today\",$classSectionArray,$isTeacher,\"$accuracyToPass\",\"$userResponse\")'>Today</div>".$calander."
							<a href='http://www.ei-india.com/asset-question-a-day-aqad/' style='float: right; padding-right: 5px;text-decoration:underline;;color:blue;' target='_blank'>What is AQAD?</a>
							<div style='clear:both'></div>
						</div></br>".$techerClasses."<br />".$accuracy."<br />".$noteFotTeachers."<div id='11'><BLOCKQUOTE> <P><font face=\"Arial\">";
		}
	}
	if (date("D", $passedDateToTime) == "Sun" && $cls != "3" && $cls != "4") {
		$message .= "<tr><td colspan=\"3\"><div style='font-size: medium; text-align: center; padding-top: 20px;font-weight: bold;'>AQAD is not available on Sunday. It is available only from Monday to Saturday.</div></td></tr>";
		$footer = "</table><br>";
		
		$message.=$footer;
		return $message;
		return;
		//continue;
	}else if (date("D", $passedDateToTime) == "Sun" && ($cls == "3" || $cls == "4")) {
		$message .="<tr><td colspan=\"3\"><div style='font-size: medium; text-align: center; padding-top: 20px;font-weight: bold;'>AQAD is not available on Sunday. It is available only from Monday to Friday.</div></td></tr>";
		$footer = "</table><br>";
		
		$message.=$footer;
		return $message;
		return;
		//continue;
	}
	if(!$lineQuestionQuery && $cls <= 4){
		$message .="<tr><td colspan=\"3\"><div style='font-size: medium; text-align: center; padding-top: 20px;font-weight: bold;'>AQAD is not available on Saturday for class 3 and class 4.</div></td></tr>";
		$footer = "</table><br>";
		
		$message.=$footer;
		return $message;
		return;
	}
	
    if ($subjectno == 1)
        $imgfolder = "Round" . $round . "/english_images/class" . $class;
    elseif ($subjectno == 2)
        $imgfolder = "Round" . $round . "/MATHS_IMAGES/class" . $class;
    elseif ($subjectno == 3)
        $imgfolder = "Round" . $round . "/sci_images/class" . $class;
    elseif ($subjectno == 4)
        $imgfolder = "Round" . $round . "/social_images/class" . $class;

    $question = orig_to_html($line['question'], $imgfolder);
    $question = str_replace("^", "'", $question);


    $optiona = orig_to_html($line['optiona'], $imgfolder);
    $optiona = str_replace("^", "'", $optiona);
    $optionb = orig_to_html($line['optionb'], $imgfolder);
    $optionb = str_replace("^", "'", $optionb);
    $optionc = orig_to_html($line['optionc'], $imgfolder);
    $optionc = str_replace("^", "'", $optionc);
    $optiond = orig_to_html($line['optiond'], $imgfolder);
    $optiond = str_replace("^", "'", $optiond);

    $displayDate  = mktime(0, 0, 0, substr($day, 5, 2), substr($day, 8, 2), substr($day, 0, 4));
    $fullDateToDisplay =date("jS F, Y",$displayDate );
    $header = "<center> $fullDateToDisplay | Class $class | $subject_arr[$subjectno]</center><br>";

    $header .= $question . "<br><br>";
	if($userResponse==""){
    if (strpos($optiona, ".jpg") || strpos($optionb, ".jpg") || strpos($optionc, ".jpg") || strpos($optiond, ".jpg")) {
    	if($isTeacher == 1  || $day != $today){
    		$header .= "<table style='font-family: Arial; font-size: 12pt;' width=596><tr><td><label for='option1'>A. " . $optiona . "</label></td><td><label for='option2'>B. " . $optionb . "</label></td></tr><tr><td><label for='option3'>C. " . $optionc . "</label></td><td><label for='option4'>D. " . $optiond . "</label></td></tr></table><br>";    		
    	}else{
    		$header .= "<table style='font-family: Arial; font-size: 12pt;' width=596><tr><td><input type='radio' name='userAnswer' id='option1' value='A'/><label for='option1'>A. " . $optiona . "</label></td><td><input type='radio' name='userAnswer' id='option2' value='B'/><label for='option2'>B. " . $optionb . "</label></td></tr><tr><td><input type='radio' name='userAnswer' id='option3' value='C'/><label for='option3'>C. " . $optionc . "</label></td><td><input type='radio' name='userAnswer' id='option4' value='D'/><label for='option4'>D. " . $optiond . "</label></td></tr></table><br>";    		
    	}
    } else {
    	if($isTeacher == 1 || $day != $today){
    		if($optiona != '' && $optionb != '' && $optionc != '' && $optiond != ''){
    			$header .= "<label for='option1'>A. " . $optiona . "</label><br>";
    			$header .= "<label for='option2'>B 3. " . $optionb . "</label><br>";
    			$header .= "<label for='option3'>C. " . $optionc . "</label><br>";
    			$header .= "<label for='option4'>D. " . $optiond . "</label><br><br><br>";
    		}
    		
    	}else{
    		$header .= "<input type='radio' name='userAnswer' id='option1' value='A'/><label for='option1'>A. " . $optiona . "</label><br>";
    		$header .= "<input type='radio' name='userAnswer' id='option2' value='B'/><label for='option2'>B. " . $optionb . "</label><br>";
    		$header .= "<input type='radio' name='userAnswer' id='option3' value='C'/><label for='option3'>C. " . $optionc . "</label><br>";
    		$header .= "<input type='radio' name='userAnswer' id='option4' value='D'/><label for='option4'>D. " . $optiond . "</label><br><br><br>";    		
    	}
    }
	}else{
		if (strpos($optiona, ".jpg") || strpos($optionb, ".jpg") || strpos($optionc, ".jpg") || strpos($optiond, ".jpg")) {
        $header .= "<table style='font-family: Arial; font-size: 12pt;' width=596><tr><td>A. " . $optiona . "</td><td>B. " . $optionb . "</td></tr><tr><td>C. " . $optionc . "</td><td>D. " . $optiond . "</td></tr></table><br>";
	    } else {
	        $header .= "A 1. " . $optiona . "<br>";
	        $header .= "B. " . $optionb . "<br>";
	        $header .= "C. " . $optionc . "<br>";
	        $header .= "D. " . $optiond . "<br>";
	    }
	}
	if($userResponse==""){
		if($line['correct_answer']=="A"){
			$corr = 1;
		}else if($line['correct_answer']=="B"){
			$corr = 2;
		}else if($line['correct_answer']=="C"){
			$corr = 3;
		}else if($line['correct_answer']=="D"){
			$corr = 4;
		}
		if($isTeacher != 1){
			if($day == $today){
				$header .="<div id='papercode' style='display:none'>".$line['papercode']."</div>";
				$header .="<div id='divExplain' style='text-align: center; width: 100%;' class='spanExplainNote'>You may explain your answer below (optional)<br/><textarea name='aqadExplaination' id='aqadExplaination' rows='7' style='width: 75%;'></textarea><br/><span class='spanExplainNote'>One good explanation will be published tomorrow with the name of the student.</span></div>";
				$header .= "<div style='width:100%;align-content: center; text-align: center;'><div class='submitButtonAQAD' onclick='sendAQADResponse(".$line["qcode"].",".$corr.")'>Submit</div></div>";
				$header .= "<div id='userResponse'><b>Your Answer: ".$userResponse."<br/><br/>Come back tomorrow to see the correct answer</b></div>";
			}
		}
	}else{
		if($isTeacher != 1){
			if($day == $today){
				$header .= "<div><b>Your Answer: ".$userResponse."</b></div>";
				$studentExplaination = getAQADExplaination($day, $cls, $userID, 0);
				if(count($studentExplaination)>0 && array_key_exists('explaination', $studentExplaination))
					$header .= " <br/><b>Your explanation: </b>".$studentExplaination['explaination']."<br/>";
				$header.="<br/>Come back tomorrow to see the correct answer";
			}			
		}
	}
	$header .= "<input type='hidden' name='aqadDate' id='aqadDate' value='".$day."'/>";
    $message.=$header;

    $message.="</FONT></P></BLOCKQUOTE></div>";
    $message.="</td>";
    $message.="</tr>";


    	if($today != $day){
    		
    		$dateWiseheader ="<tr> ";
    		$dateWiseheader.="<td colspan=\"3\">";
    		$dateWiseheader.="<BLOCKQUOTE><P><font face=\"Arial\"><BR>";
    		$dateWiseheader .= "<br><div><b>Correct Answer: " . $line['correct_answer'] . "</b></div>";
    		$dateWiseExplainationOfTheDay = getAQADExplaination($day, $cls, 0, 1);
    		$dateWiseSystemExplaination = getSystemAQADExplaination($day, $cls, 0, 1);
    		if(count($dateWiseExplainationOfTheDay)>0 && array_key_exists('explaination', $dateWiseExplainationOfTheDay))
    		{
    			mysql_select_db("educatio_adepts");
    			$objUser = new User($dateWiseExplainationOfTheDay['studentId']);
    			$dateWiseheader .= " <br/><div><b>Explanation of the day:</b><div style='border: 1px solid #fbd212; padding: 10px;min-height:30px;'><div style='background:url(\"assets/rewards/tokenLandingScreen/L.00.png\") no-repeat 0 0; width: 30px;height:49px;background-size: 30px 30px; float:left'></div><span style='line-height:30px; margin-left:10px;'>".$dateWiseExplainationOfTheDay['explaination']."</span></div><i style='font-size:small;float:right;'>".$objUser->childName.", ".$objUser->schoolName."</i><br/></div>";
    			mysql_select_db("educatio_educat");
    		}
    		else if(count($dateWiseSystemExplaination)>0 && array_key_exists('explanation', $dateWiseSystemExplaination))
    		{
    			$dateWiseheader .= " <br/><div><b>Explanation of the day:</b><div style='border: 1px solid #fbd212; padding: 10px;min-height:30px;'><div style='background:url(\"assets/rewards/tokenLandingScreen/L.00.png\") no-repeat 0 0; width: 30px;height:49px;background-size: 30px 30px; float:left'></div><span style='line-height:30px; margin-left:10px;'>".$dateWiseSystemExplaination['explanation']."</span></div></div>";
    		}
    		$dateWiseheader.="</FONT></P></BLOCKQUOTE><br></td></tr>";
    	}
    $message.=$dateWiseheader;
    $footer = "</table><br>";
    
    $message.=$footer;
    return $message;
}

function orig_to_html($orig, $img_folder) {
    $pattern[0] = "/\[([a-z0-9_\.]*)\]/i";
    $replacement[0] = "<img src='http://www.assetonline.in/asset_online/$img_folder/\$1'>";
    //$pattern[1] = "/\[([a-z0-9_\.]*)[ ]*,[ ]*(.*)\]/i";
    $pattern[1] = "/\[([a-z0-9_\.]*)[ ]*,[ ]*(.[^\]]*)\]/i";
    $replacement[1] = "<img src='http://www.assetonline.in/asset_online/$img_folder/\$1' height=\$2>";
    $pattern[2] = "/\r\n/";
    $replacement[2] = "<br>\n";
    $html_ver = preg_replace($pattern, $replacement, $orig);
    return ($html_ver);
}
function formatDate($oldformat) // function which converts yyyy-mm-dd to dd/mm/yyyy format
	{
	
		$dateParameters=explode("-",$oldformat);
	
		$newformat=$dateParameters[2]."-".$dateParameters[1]."-".$dateParameters[0];
	
		return $newformat;
	
	}
?>