<?php

		 function getKstTestStatus($userID,$ttAttemptID,$teacherTopicCode){
			global $link;
			$query = "SELECT count(*) as cnt FROM educatio_adepts.kst_subsetListForDiagnosticTest WHERE teacherTopicCode='$teacherTopicCode'";
			$result = mysql_query($query);
            if( mysql_num_rows( $result)) {
                $line = mysql_fetch_array( $result, MYSQL_ASSOC);
			}
			if($line['cnt'] > 0){
				$query = "SELECT status FROM educatio_adepts.kst_diagnosticTestAttempts WHERE ttAttemptID=$ttAttemptID and userID=$userID and testType='Pretest'";
				$result = mysql_query($query);
				if( mysql_num_rows( $result)) {
					$line = mysql_fetch_array( $result, MYSQL_ASSOC);
				}
				if(isset($line['status']) && $line['status'] == 1 ){
					return 1;
				} else {
					return 0;
				}
			} else {
				return 2;
			}
		} 

		function checkPostStatus($userID,$ttAttemptID,$teacherTopicCode){
			global $link;
			$query = "SELECT count(*) as cnt FROM educatio_adepts.kst_subsetListForDiagnosticTest WHERE teacherTopicCode='$teacherTopicCode'";
			$result = mysql_query($query);
            if( mysql_num_rows( $result)) {
                $line = mysql_fetch_array( $result, MYSQL_ASSOC);
			}
			if($line['cnt'] > 0){
				$query = "SELECT status FROM educatio_adepts.kst_diagnosticTestAttempts WHERE ttAttemptID=$ttAttemptID and userID=$userID and testType='Post Test'";
				//echo $query;die;
				$result = mysql_query($query);
				if( mysql_num_rows( $result)) {
					$line = mysql_fetch_array( $result, MYSQL_ASSOC);
				} else{
					return 3;
				}
				if(isset($line['status']) && $line['status'] == 1 ){
					return 1;
				} else {
					return 0;
				}
			} else {
				return 2;
			}
		}

		function checkForKstDiagnosticTest($teacherTopicCode,$schoolCode,$childClass){
			global $link;
			$query = "SELECT customTopic,parentTeacherTopicCode FROM educatio_adepts.adepts_teacherTopicMaster WHERE teacherTopicCode='$teacherTopicCode'";
			$result = mysql_query( $query) or die( "Invalid query".$query);
			$line = mysql_fetch_array( $result, MYSQL_ASSOC);
			$flow = $line['customTopic'];
			$pTTCode = $line['parentTeacherTopicCode'];
			if ($flow == 1) {
				$query = "SELECT * FROM educatio_adepts.adepts_actdeact_feature_schools WHERE schoolCode=$schoolCode and find_in_set('$pTTCode',teacherTopicCode) and find_in_set($childClass,childClass)";
			} else {
				$query = "SELECT * FROM educatio_adepts.adepts_actdeact_feature_schools WHERE schoolCode=$schoolCode and find_in_set('$teacherTopicCode',teacherTopicCode) and find_in_set($childClass,childClass)";
			}
			$result = mysql_query( $query) or die( "Invalid query".$query);
            if( mysql_num_rows( $result)) {
                $line = mysql_fetch_array( $result, MYSQL_ASSOC);
			}
			return $line;
		}

		function getTTID($userID, $teacherTopicCode){
			//obtain attempt id from testAttempt table
			$query = "SELECT ttAttemptID from educatio_adepts.adepts_teacherTopicStatus where userID=$userID and teacherTopicCode='$teacherTopicCode' order by lastModified desc limit 1";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			$line = mysql_fetch_array($rs, MYSQL_ASSOC);
			return	$line['ttAttemptID'];
		}

		function check_predictedQuestions_kst($userID, $teacherTopicAttemptID){
			//obtain attempt id from testAttempt table
			if(isset($_SESSION['isPostTest']) && $_SESSION['isPostTest'] == 1){
				$attemptID = getPostAttemptID($userID, $teacherTopicAttemptID);
			} else {
				$attemptID = getAttemptID($userID, $teacherTopicAttemptID);
			}
			//check for this attempt id if there are predicted questions in db or not
			$query = "SELECT count(*) as cnt FROM educatio_adepts.kst_diagnosticQuestionAttempt WHERE userID=$userID and attemptID=$attemptID and typeAsked='Predicted'";
			$rs	=	mysql_query($query) or die(mysql_error().$query);
			$row = mysql_fetch_array($rs, MYSQL_ASSOC);
			if(array_filter($row)){
				return $isPredicted = 1;
			} else {
				return $isPredicted = 0;
			}
		}

        function neat_trim($str, $n, $delim='...') {
		   $len = strlen($str);
		   if ($len > $n) {
		       preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
		       return rtrim($matches[1]) . $delim;
		   }
		   else {
		       return $str;
		   }
		}

		function cut($string, $max_length)
		{
			if (strlen($string) > $max_length)
			{
				$string = substr($string, 0, $max_length);
				$pos = strrpos($string, ' ');
				if($pos === false)
				{
					return substr($string, 0, $max_length).'...';
				}
				return substr($string, 0, $pos).'...';
			}
			else
			{
				return $string;
			}
		}

        function getDetails() {
            $userID = $_SESSION['userID'];
        	$query = 'SELECT username, secretQues, secretAns, password, childName, childClass, childSection,DATE_FORMAT(childDob,"%d-%m-%Y") as dob, childEmail,parentName, parentEmail, city,state,country,pincode,gender,
				             contactno_res, contactno_cel, schoolName, address, DATE_FORMAT(startDate,"%d-%m-%Y") as startDate, DATE_FORMAT(endDate,"%d-%m-%Y") as endDate, category, subcategory
		              FROM   adepts_userDetails WHERE userID = '.$userID;
			//echo $query;
			//exit();
        	global $link;
		
            $result = mysql_query($query, $link);
            if( mysql_num_rows( $result)) {
                $row = mysql_fetch_array( $result, MYSQL_ASSOC);
				return $row;
            }
            else
                return;
        }

        function scoreArray( $userID, $topic) {
                //  Score page - Span number must be around numbers.
                $ans = array();
                global $link;
                $query = "select * from adepts_teacherTopicQuesAttempt where userID=$userID and clusterCode='$topic'";
                $result = mysql_query( $query, $link) or die( "Invalid query".$query);
                $total = 0;
                $correct = 0;
                $ttime = 0;
                if( mysql_num_rows( $result)) {
                    while( $row = mysql_fetch_array( $result, MYSQL_ASSOC)) {
                        $total++;
                        if( $row[ 'R' ] == 1) {
                            $correct++;
                        }
                        $ttime = $row[ 'S' ];
                    }
                }
                $avg = $ttime / ( ( $total == 0)?1:$total);
                $perc = round($correct * 100 / ( ( $total == 0)?1:$total) );
                $ans[ "Questions Completed" ] = "<span class=\"number\">$total</span><span> out of </span><span class=\"number\">50</span><span>. Excellent!</span>";
                $ans[ "Average Time Taken Per Question" ] = "<span class=\"number\">$avg</span><span> seconds. Very Good!</span>";
                $ans[ "Percentage of Questions attempted that were correct" ] = "<span class=\"number\">$perc</span><span> percent, You can improve this.</span>";
                return $ans;
        }

        function awards() {
          // Topic award list - should be called with topic identifier such as id.
                $ans = array();
                $ans[ "0" ] = "Golden Cup";
                $ans[ "1" ] = "Orange Cup";
                $ans[ "2" ] = "Purple Cup";
                return $ans;
        }

        function compare_modtimes( $a, $b) {
            //return strcmp( $a[ ts ], $b[ ts ]);
            return strcmp( $a[2], $b[2]);
        }
		
        function lastsessiontopics($userID, $childClass, $arrTopicsActivated,$priorityAssigned=false) {
			
			$topicWiseDetails = $topicsAttempted = array();
			$user = new User($userID);
			$subcategory=$user->subcategory;
            if(count($arrTopicsActivated)>0)
            {
            	$topicsActivatedStr = implode("','",array_keys($arrTopicsActivated));
				if($priorityAssigned)
				{
					$i=0;
					foreach($arrTopicsActivated as $ttCode=>$description)
					{
						$i++;
						if (in_array('description', array_keys($description))) {
							$description=$description['description'];
						}
						if($i<=4)
							$topicsAttempted[$ttCode] = $description;
					}
					if(count($topicWiseDetails)<4)
					{
						$topicsAttemptedList = getRecentTopics($topicsActivatedStr, $userID, SUBJECTNO);
						$i = count($topicWiseDetails);
						foreach($topicsAttemptedList as $ttCode=>$description)
						{
							$i++;
							if (in_array('description', array_keys($description))) {
								$description=$description['description'];
							}
							if($i<=4)
								$topicsAttempted[$ttCode] = $description;
						}	
					}
				}
				else
				{
					if (!(strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0 || strcasecmp($subcategory,"Center")==0) && $childClass<=3) {
						$topicsAttempted=fetchTopicsForInOrder($userID,$childClass);
					}
					else $topicsAttempted = getRecentTopics($topicsActivatedStr, $userID, SUBJECTNO);
				}
            }
            if(count($topicsAttempted)>0)
            {
            	$topicWiseDetails = getTopicWiseDetails($topicsAttempted, $userID, $childClass);
            }
            return $topicWiseDetails;
        }
		
		function fetchTopicsForInOrder($userID,$childClass){
			//currently for Retail students <=3
			if ($childClass>3) return array();

			$ttQuery="SELECT a.teacherTopicCode, IF(b.newTTDesc='' OR ISNULL(b.newTTDesc),a.teacherTopicDesc,b.newTTDesc) ttDesc, b.position, IFNULL(e.result,1) result, e.lastModified
					FROM adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise b ON a.teacherTopicCode=b.teacherTopicCode
					LEFT JOIN 
						(
							SELECT ttS1.teacherTopicCode, IF(ISNULL(ttS1.result),0,2) result, IFNULL(ttC.lastModified,ttS1.lastModified) lastModified, ttS1.ttAttemptNo 
							FROM adepts_teacherTopicStatus ttS1 JOIN  (select teacherTopicCode, max(ttAttemptID) as maxid from adepts_teacherTopicStatus WHERE userID=$userID group by teacherTopicCode) ttS2 ON (ttS1.ttAttemptID=ttS2.maxid) LEFT JOIN (SELECT ttAttemptID, lastModified FROM adepts_ttUserCurrentStatus WHERE userID=$userID) ttC ON ttS2.maxid=ttC.ttAttemptID
							WHERE ttS1.userID=$userID 
							ORDER BY result, ttS1.lastModified DESC
						) e ON b.teacherTopicCode=e.teacherTopicCode
					WHERE  subjectno=2 AND customTopic=0 AND live=1 and ( b.class=$childClass)
					ORDER BY e.lastModified DESC, position";
			$rs = mysql_query($ttQuery);
			$topicArray=array();$countOfInProgress=0;$latestCompleted=0;
			while ($rw=mysql_fetch_array($rs)){
				if (count($topicArray)==0 && $rw['result']==2 && $latestCompleted==0) $latestCompleted=$rw['position'];
				$topicArray[$rw['position']]=array('ttCode'=>$rw['teacherTopicCode'],'ttDesc'=>$rw['ttDesc'],'ttResult'=>$rw['result']);
				if ($rw['result']==0) $countOfInProgress++;
			}
			$maxPos=max(array_keys($topicArray));
			$topicPriority=array();
			
			if ($countOfInProgress>=4){
				foreach ($topicArray as $position => $topic) {
					if (count($topicPriority)>=4) break;
					if ($topic['ttResult']!=2) {// for in-progress Topics (priority 0), and not attempted Topics (priority 1)
						$topicPriority[$topic['ttCode']]=$topic['ttDesc'];
					}
				}
			}
			else {
				if ($latestCompleted!=0){
					//if last attempted is completed then fill the FIRST slot with last Completed topic + 1th position (if not complete).. if cycleComplete then just pick next.
					$pos=$latestCompleted+1;
					$cycleComplete=false;$count=0;
					while (!((!$cycleComplete && array_key_exists($pos, $topicArray) && $topicArray[$pos]['ttResult']!=2) || ($cycleComplete && array_key_exists($pos, $topicArray)))) {
						if ($pos>$maxPos) $pos=0;
						$pos=$pos+1;
						//echo '<br>Pos:'.$pos.' TT:'.$topicArray[$pos]['ttCode'].' TT:'.$topicArray[$pos]['ttDesc'];
						if ($pos==$latestCompleted) 
							{$cycleComplete=true;$pos=$pos+1;}
					}
					$topicPriority[$topicArray[$pos]['ttCode']]=$topicArray[$pos]['ttDesc'];
					$latestCompleted=$pos;
				}
				foreach ($topicArray as $position => $topic) {
					if (count($topicPriority)>=4) break;
					if ($topic['ttResult']!=2 && !array_key_exists($topic['ttCode'], $topicPriority)) {// for in-progress Topics (priority 0), and not attempted Topics (priority 1)
						$topicPriority[$topic['ttCode']]=$topic['ttDesc'];
					}
				}

				if (count($topicPriority)<4) { //if slots still remain and no in-progress or unattempted, fill with completed topics in order after last Completed.
					while (count($topicPriority)<4){
						$pos=$latestCompleted+1;
						while (array_key_exists($topicArray[$pos], $topicPriority)){
							if ($pos>$maxPos) $pos=0;
							$pos=$pos+1;
							if ($pos==$latestCompleted) $pos=$pos+1;
						}
						$topicPriority[$topicArray[$pos]['ttCode']]=$topicArray[$pos]['ttDesc'];
						$latestCompleted=$pos;
					}
				}
			}
			return $topicPriority;
		}

		function checkForPriority($arrTopicsActivated,$schoolCode,$childClass,$childSection)
		{
			$topicsActivatedStr = implode("','",array_keys($arrTopicsActivated));
			$sq = "SELECT COUNT(srno) FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND teacherTopicCode IN ('$topicsActivatedStr') AND class=$childClass AND section='$childSection' AND priority=100 AND deactivationDate='0000-00-00'";
			$rs = mysql_query($sq);
			$rw = mysql_fetch_array($rs);
			if($rw[0]>0)
				return false;
			else
				return true;
		}

        function topicprogress() {
        	$userID = $_SESSION['userID'];

        	$user = new User($userID);

            $topicsAttempted = getTopicsAttempted($userID, SUBJECTNO);
            if(count($topicsAttempted)>0)
            {
            	$topicWiseDetails = getTopicWiseDetails($topicsAttempted, $userID, $user->childClass);
				$topicWiseDetails = SortDataSet($topicWiseDetails,2,true);
            }
            return $topicWiseDetails;
        }

        function topicset($user) {
            //$userID = $_SESSION['userID'];
            $userID = $user->userID;
            $topicsAttempted = getTopicActivated($userID,$user->childClass,$user->childSection,$user->category,$user->subcategory,$user->schoolCode, SUBJECTNO,$user->packageType);
            return $topicsAttempted;
        }

        function getSparks($userID, $childClass) {
        	$noOfSparkies = 0;
        	/*$userID = $_SESSION['userID'];
        	$childClass = $_SESSION['childClass'];*/
        	$query = "SELECT sum(noOfJumps) sparkiesCount FROM ".TBL_SESSION_STATUS." WHERE userID=$userID";
        	$result = mysql_query($query) or die(mysql_error());
        	$line = mysql_fetch_array($result);
			
        	if($line['sparkiesCount']!="")
				$noOfSparkies = $line['sparkiesCount'];
				
			$query = "SELECT SUM(sparkies) sparkiesCount FROM adepts_rewardPoints_archive WHERE userID=$userID";	
        	$result = mysql_query($query) or die(mysql_error());
        	if($line=mysql_fetch_array($result))
			{
				if($line['sparkiesCount']!="")
					$noOfSparkies += $line['sparkiesCount'];
			}
			
			// get email verification sparkie count for class 3 student
			if($childClass > 2) {
				$sparkie_check = "SELECT sparkieEarned FROM adepts_userBadges WHERE userID = ".mysql_real_escape_string($userID)." AND batchType = 'emailVarification'";
				$exec_sparkie_check = mysql_query($sparkie_check);
				if(mysql_num_rows($exec_sparkie_check) > 0) {
					$row_sparkie_check = mysql_fetch_array($exec_sparkie_check);
					if(!empty($row_sparkie_check['sparkieEarned']))
						$noOfSparkies += $row_sparkie_check['sparkieEarned'];
				}
			}

			if($childClass>=8)
				$noOfSparkies = $noOfSparkies * 10; //1 Sparkie = 10 Reward points
			return $noOfSparkies;
        }

        function getAwards() {
                return 3;
        }

        function lastSession( $userID, $sessionID) {

        	$ans = array();
			if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
			{
				$query= "SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
		             FROM     adepts_bucketClusterAttempt 
		             WHERE    userID=".$userID." AND attemptID='".$_SESSION['bucketAttemptID']."' ORDER BY lastModified,questionNo";
			}
			else
			{
				$query= "SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
						 FROM     ".TBL_QUES_ATTEMPT_CLASS."
						 WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";
			}
            $result = mysql_query($query) or die(mysql_error());

            if( $result) {
                while( $row = mysql_fetch_array( $result, MYSQL_ASSOC)) {

                    $tmp = array();
                    array_push( $tmp, $row[ 'srno' ]);
                    array_push( $tmp, $row[ 'questionNo' ]);
                    array_push( $tmp, $row[ 'qcode' ]);

                    /*$query_inside = "select correct_answer from adepts_questions where qcode = ".$row['qcode'];
                    $result_inside = mysql_query( $query_inside);
                    $row_inside = mysql_fetch_array( $result_inside, MYSQL_ASSOC);
                    array_push( $tmp, $row_inside[ 'correct_answer' ]);*/

                    //fetch correct ans for each question
					$question = new Question($row['qcode']);
					if(strpos($question->questionStem,"ADA_eqs") !== false)
						array_push( $tmp, "");
					else
						array_push( $tmp, str_replace("|",", ",html_entity_decode($row[ 'A' ])));
					//print_r($question);
					if($question->isDynamic())
					{
						$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$row[ 'srno' ];
						if(isset($_SESSION['bucketClusterCode']) && $_SESSION['bucketClusterCode']==1)
						{
							$query  .= " AND mode='bucketcluster'";
						}
						$dynamic_result = mysql_query($query);
						$dynamic_line   = mysql_fetch_array($dynamic_result);
						$question->generateQuestion("answer",$dynamic_line[0]);
					}
					array_push( $tmp, $question->getCorrectAnswerForDisplay());

                    array_push( $tmp, $row[ 'R' ]);
                    array_push( $tmp, $row[ 'S' ]);
					array_push( $tmp, $row['lastModified']);
                    array_push( $ans, $tmp);
                }
            }



			//practise test question

			$sq	=	"SELECT   id, qno, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified  
		             FROM     practiceTestAttempt 
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' AND practiceattemptID=".$_SESSION['practiseid']." ORDER BY lastModified,qno";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'id' ]);

					array_push( $tmp, $rw['qno']);

				array_push( $tmp, $rw[ 'qcode' ]);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'srno' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());
				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $ans, $tmp);
			}


			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified, questionType
		             FROM     adepts_researchQuesAttempt
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'srno' ]);
				if($rw['questionType']=="comprehensive")
					array_push( $tmp, "COMPREHENSIVE".$rw['questionNo']);
				else if($rw['questionType']=="normal")
					array_push( $tmp, "WCQ_N");
				else
					array_push( $tmp, "WCQ_R");
				array_push( $tmp, $rw[ 'qcode' ]);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'srno' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $ans, $tmp);
			}

			$sq	=	"SELECT   id, qNo, qcode, A, R, S, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, sessionID,attemptNo, a.practiseModuleId, description
		             FROM     practiseModulesQuestionAttemptDetails  a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId
					 WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY a.lastModified,qNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'id' ]);
				array_push( $tmp, 'Practice '.$rw['qNo']);
				array_push( $tmp, $rw[ 'qcode' ]);
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'id' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				if(strpos($question->questionStem,"ADA_eqs") !== false)
					array_push( $tmp, "");
				else
					array_push( $tmp, $rw[ 'A' ]);

				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $tmp, $rw[ 'practiseModuleId' ]);
				array_push( $tmp, $rw[ 'attemptNo' ]);
				array_push( $tmp, $rw[ 'description' ]);
				array_push( $ans, $tmp);
			}

			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
		             FROM     adepts_revisionSessionDetails
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";

			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'srno' ]);
				array_push( $tmp, $rw['questionNo']);
				array_push( $tmp, $rw[ 'qcode' ]);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'srno' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());
				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $ans, $tmp);
			}

			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
		             FROM     adepts_diagnosticQuestionAttempt
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw['srno']);
				array_push( $tmp, "D".$rw['questionNo']);
				array_push( $tmp, $rw['qcode']);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new diagnosticTestQuestion($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw['srno'];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $ans, $tmp);
			}
			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified
		             FROM     educatio_adepts.kst_diagnosticQuestionAttempt
		             WHERE    userID=".$userID." AND sessionID='".$sessionID."' ORDER BY lastModified,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw['srno']);
				array_push( $tmp, "D".$rw['questionNo']);
				array_push( $tmp, $rw['qcode']);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw['srno'];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());
				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $ans, $tmp);
			}

			$query = "SELECT b.sessionID
					  FROM   adepts_gamesMaster a, adepts_userGameDetails b
					  WHERE  a.gameID=b.gameID AND userID=$userID AND b.sessionID='".$sessionID."' ORDER BY b.lastModified DESC";
			$result = mysql_query($query) or die("Error in fetching game details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$query = "SELECT a.remedialItemCode, a.remedialItemDesc, result, timeTaken, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, remedialAttemptID, sessionID 
			          FROM   adepts_remedialItemMaster a, adepts_remedialItemAttempts b 
			          WHERE  a.remedialItemCode=b.remedialItemCode AND userID=$userID AND sessionID=$sessionID ORDER BY b.lastModified DESC";
			$result = mysql_query($query) or die("Error in fetching remedial details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$query = "SELECT a.timedTestCode, description, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified,linkedToCluster
			          FROM   adepts_timedTestMaster a, adepts_timedTestDetails b
			          WHERE  a.timedTestCode=b.timedTestCode AND userID=$userID AND sessionID=$sessionID ORDER BY b.lastModified DESC";
			$result = mysql_query($query) or die("Error in fetching timed test details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}
            return $ans;
        }


		function lastdaySession($userID, $date) {

        	$ans = array();
			if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
			{
				$query= "SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified,sessionID
		             FROM     adepts_bucketClusterAttempt
		             WHERE    userID=".$userID." AND attemptedDate='".$date."' ORDER BY sessionID,questionNo";
			}
			else
			{
				$query= "SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified,sessionID
						 FROM     ".TBL_QUES_ATTEMPT_CLASS."
						 WHERE    userID=".$userID." AND attemptedDate='".$date."' ORDER BY sessionID,questionNo";
			}

			/*	$query= "SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified,sessionID
						 FROM     ".TBL_QUES_ATTEMPT_CLASS."  , adepts_researchQuesAttempt
						 WHERE    userID=".$userID." AND attemptedDate='2014-08-07' ORDER BY sessionID";*/

            $result = mysql_query($query) or die(mysql_error());

            if( $result) {
                while( $row = mysql_fetch_array( $result, MYSQL_ASSOC)) {

                    $tmp = array();
                    array_push( $tmp, $row[ 'srno' ]);
                    array_push( $tmp, $row[ 'questionNo' ]);
                    array_push( $tmp, $row[ 'qcode' ]);

                    //fetch correct ans for each question
					$question = new Question($row['qcode']);
					if(strpos($question->questionStem,"ADA_eqs") !== false)
						array_push( $tmp, "");
					else
						array_push( $tmp, str_replace("|",", ",html_entity_decode($row[ 'A' ])));
					//print_r($question);
					if($question->isDynamic())
					{
						$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$row[ 'srno' ];
						if(isset($_SESSION['bucketClusterCode']) && $_SESSION['bucketClusterCode']==1)
						{
							$query  .= " AND mode='bucketcluster'";
						}
						$dynamic_result = mysql_query($query);
						$dynamic_line   = mysql_fetch_array($dynamic_result);
						$question->generateQuestion("answer",$dynamic_line[0]);
					}
					array_push( $tmp, $question->getCorrectAnswerForDisplay());

                    array_push( $tmp, $row[ 'R' ]);
                    array_push( $tmp, $row[ 'S' ]);
					array_push( $tmp, $row['lastModified']);
					array_push( $tmp, $row[ 'sessionID' ]);
                    array_push( $ans, $tmp);

                }
            }

			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified, questionType ,sessionID
		             FROM     adepts_researchQuesAttempt
					 WHERE    userID=".$userID." AND attemptedDate='".$date."' ORDER BY sessionID,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'srno' ]);
				if($rw['questionType']=="comprehensive")
					array_push( $tmp, "COMPREHENSIVE".$rw['questionNo']);
				else if($rw['questionType']=="normal")
					array_push( $tmp, "WCQ_N");
				else
					array_push( $tmp, "WCQ_R");
				array_push( $tmp, $rw[ 'qcode' ]);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'srno' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}
			$sq	=	"SELECT   id, qNo, qcode, A, R, S, date_format(a.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, sessionID, attemptNo, a.practiseModuleId, description
		             FROM     practiseModulesQuestionAttemptDetails a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId
					 WHERE    userID=".$userID." AND attemptDate='".$date."' ORDER BY sessionID,qNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'id' ]);
				array_push( $tmp, 'Practice '.$rw['qNo']);
				array_push( $tmp, $rw[ 'qcode' ]);

				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'id' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}

				if(strpos($question->questionStem,"ADA_eqs") !== false)
					array_push( $tmp, "");
				else
					array_push( $tmp, $rw[ 'A' ]);

				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $tmp, $rw[ 'practiseModuleId' ]);
				array_push( $tmp, $rw[ 'attemptNo' ]);
				array_push( $tmp, $rw[ 'description' ]);
				array_push( $ans, $tmp);
			}


			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified, sessionID
		             FROM     adepts_revisionSessionDetails 
					 WHERE    userID=".$userID." AND lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY sessionID,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw[ 'srno' ]);
				array_push( $tmp, $rw['questionNo']);
				array_push( $tmp, $rw[ 'qcode' ]);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw[ 'srno' ];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());
				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified ,sessionID
		             FROM     adepts_diagnosticQuestionAttempt
		             WHERE    userID=".$userID." AND lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY sessionID,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw['srno']);
				array_push( $tmp, "D".$rw['questionNo']);
				array_push( $tmp, $rw['qcode']);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new diagnosticTestQuestion($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw['srno'];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$sq	=	"SELECT   srno, questionNo, qcode, A, R, S, date_format(lastModified, '%d-%m-%Y %H:%i:%s') lastModified ,sessionID
		             FROM     educatio_adepts.kst_diagnosticQuestionAttempt
		             WHERE    userID=".$userID." AND lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY sessionID,questionNo";
			$rs	=	mysql_query($sq);
			while($rw=mysql_fetch_array($rs))
			{
				$tmp = array();
				array_push( $tmp, $rw['srno']);
				array_push( $tmp, "D".$rw['questionNo']);
				array_push( $tmp, $rw['qcode']);
				array_push( $tmp, str_replace("|",", ",$rw[ 'A' ]));
				$question = new Question($rw['qcode']);
				if($question->isDynamic())
				{
					$query  = "SELECT parameters FROM adepts_dynamicParameters WHERE class=".$_SESSION['childClass']." AND quesAttempt_srno= ".$rw['srno'];
					$dynamic_result = mysql_query($query);
					$dynamic_line   = mysql_fetch_array($dynamic_result);
					$question->generateQuestion("answer",$dynamic_line[0]);
				}
				array_push( $tmp, $question->getCorrectAnswerForDisplay());

				array_push( $tmp, $rw[ 'R' ]);
				array_push( $tmp, $rw[ 'S' ]);
				array_push( $tmp, $rw[ 'lastModified' ]);
				array_push( $tmp, $rw[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$query = "SELECT b.sessionID
					  FROM   adepts_gamesMaster a, adepts_userGameDetails b
					  WHERE  a.gameID=b.gameID AND userID=$userID AND b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";
			$result = mysql_query($query) or die("Error in fetching game details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}

			$query = "SELECT a.remedialItemCode, a.remedialItemDesc, result, timeTaken, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, remedialAttemptID, sessionID 
			          FROM   adepts_remedialItemMaster a, adepts_remedialItemAttempts b
			          WHERE  a.remedialItemCode=b.remedialItemCode AND userID=$userID AND b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";
			$result = mysql_query($query) or die("Error in fetching remedial details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}
			$query = "SELECT a.timedTestCode, description, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified,linkedToCluster,b.sessionID
			          FROM   adepts_timedTestMaster a, adepts_timedTestDetails b
			          WHERE  a.timedTestCode=b.timedTestCode AND userID=$userID AND  b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";

			$result = mysql_query($query) or die("Error in fetching timed test details!");
			while ($line = mysql_fetch_array($result))
			{
				$tmp = array();
				for($i = 0; $i<8 ; $i++)
				{
					array_push( $tmp, '');
				}
				array_push( $tmp, $line[ 'sessionID' ]);
				array_push( $ans, $tmp);
			}
            return $ans;
        }

        function sessiontime( $sessid) {
            global $link;
            $query = "select timediff( current_timestamp, startTime) as tdf from adepts_sessionStatus where sessionID=$sessid";
            $result = mysql_query( $query, $link);
            if( !$result) {
                die( "Incorrect query".$query);
            }
            $row = mysql_fetch_array( $result, MYSQL_ASSOC);
            $td = $row[ 'tdf' ];
            return $td;
        }
        function getRetailTopicName($topicCode, $class){

        }
		function getTopicWiseDetails($topicsAttempted, $userID, $class)
		{
			$topicWiseDetails = array();
			$srno = 0;
			foreach ($topicsAttempted as $teacherTopicCode => $desc)
			{
				$totalSDLS = 0;
	    		$clusterArray    = array();
	    		$sdls            = array();
	    		$progressDetails = getProgressInTopic($teacherTopicCode, $class, $userID);
			    //$failedClusters = getFailedClusters($userID,$teacherTopicCode);
			    //$progressHTML = getHtmlForProgress($userID,$progress,$failedClusters,$clusterArray,$sdls);
		    	//$lastAttemptedOn = getLastAttemptedOn($userID,$teacherTopicCode);
		    	$lastAttemptedOn = "";
		    	$topicWiseDetails[$srno][0] = $teacherTopicCode;
		    	$topicWiseDetails[$srno][1] = $desc;
		    	$topicWiseDetails[$srno][2] = $progressDetails["progress"];
		    	$topicWiseDetails[$srno][3] = $progressDetails["higherLevel"];
		    	//$topicWiseDetails[$srno][4] = $progressHTML;
		    	$topicWiseDetails[$srno][4] = $lastAttemptedOn;
		    	$topicWiseDetails[$srno][5] = getNoOfAttemptsOnTheTopic($userID, $teacherTopicCode);
		    	$srno++;
			}
			return $topicWiseDetails;
		}

		function getTopicWiseDetailsNew($topicsAttempted, $userID, $class)
		{
			$topicWiseDetails = array();
			$srno = 0;
			foreach ($topicsAttempted as $teacherTopicCode => $desc)
			{
				$totalSDLS = 0;
	    		$clusterArray    = array();
	    		$sdls            = array();
	    		$progressDetails = getProgressInTopic($teacherTopicCode, $class, $userID, "dashboard");

		    	$lastAttemptedOn = "";
		    	$topicWiseDetails[$teacherTopicCode][0] = $desc;
		    	$topicWiseDetails[$teacherTopicCode][1] = $progressDetails["progress"];
		    	$topicWiseDetails[$teacherTopicCode][2] = $progressDetails["higherLevel"];
		    	$topicWiseDetails[$teacherTopicCode][3] = $lastAttemptedOn;
		    	$topicWiseDetails[$teacherTopicCode][4] = getNoOfAttemptsOnTheTopic($userID, $teacherTopicCode);
		    	$topicWiseDetails[$teacherTopicCode][5] = $progressDetails["result"];
		    	$srno++;
			}

			return $topicWiseDetails;
		}

		function getProgressInTopic($teacherTopicCode, $class, $userID, $mode="")
		{
			$progressDetails = array();
			$progressDetails["progress"]	=	"";
			$query  = "SELECT DISTINCT flow,MAX(progress),result,GROUP_CONCAT(ttAttemptID) as ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";				
    		$result = mysql_query($query);
    		$line   = mysql_fetch_array($result);
    		$flow   = $line[0];
    		$result = $line[2];
    		$ttAttemptID = $line[3];
    		$objTopicProgress = new topicProgress($teacherTopicCode, $class, $flow, SUBJECTNO);

			//if($mode=="dashboard")
			// {
				$sq	=	"SELECT * FROM ".TBL_CURRENT_STATUS." WHERE progressUpdate=0 AND teacherTopicCode='$teacherTopicCode' AND userID=$userID";
				$rs	=	mysql_query($sq);
				if($rw=mysql_fetch_assoc($rs))
					$progressDetails["progress"] =	$line[1];
			// }
			if ($line[1]==100)
				$progressDetails["progress"]=100;
			if($progressDetails["progress"]=="")
				$progressDetails["progress"] = $objTopicProgress->getProgressInTT($userID);
			$progressDetails["higherLevel"] = $objTopicProgress->getHigherLevel($userID);
			$progressDetails["noofsdls"] = $objTopicProgress->totalSDLs; //used in controller.php for showing progress
			$progressDetails["result"] = $result;
			$progressDetails["ttAttemptID"] = $ttAttemptID;
			return $progressDetails;
		}

		function getNoOfAttemptsOnTheTopic($userID, $ttCode)
		{
		    $query  = "SELECT count(ttAttemptID) FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
		    $result = mysql_query($query);
		    $line   = mysql_fetch_array($result);
		    return $line[0];
		}

		function getTopicsAttempted($userID, $subjectno)
		{
			$user = new User($userID);
			$topicsAttempted = array();
			if(!(strcasecmp($user->category,"STUDENT")==0 && (strcasecmp($user->subcategory,"SCHOOL")==0 || strcasecmp($user->subcategory,"Home Center")==0)) && $user->childClass<=3) {
				$query = "SELECT distinct a.teacherTopicCode, IF(c.newTTDesc='' OR ISNULL(c.newTTDesc) ,a.teacherTopicDesc,c.newTTDesc) as newTTDesc, c.position
				          FROM   adepts_teacherTopicMaster a, ".TBL_TOPIC_STATUS." b LEFT JOIN adepts_teacherTopicFlow_classwise c ON b.teacherTopicCode=c.teacherTopicCode
				          WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=$subjectno AND b.userID=$userID AND (ISNULL(c.class) OR c.class=".$user->childClass.")";
			}
			else
				$query = "SELECT distinct a.teacherTopicCode, a.teacherTopicDesc
			          FROM   adepts_teacherTopicMaster a, ".TBL_TOPIC_STATUS." b
			          WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=$subjectno AND b.userID=$userID";

			$result = mysql_query($query);
			while ($line = mysql_fetch_array($result))
				$topicsAttempted[$line[0]] = $line[1];

			return $topicsAttempted;
		}

		function getRecentTopics($topicsActivatedStr, $userID, $subjectno)
		{
			$recentTopicsAttempted = array();
			$query = "SELECT distinct a.teacherTopicCode, a.teacherTopicDesc
			          FROM   adepts_teacherTopicMaster a, ".TBL_TOPIC_STATUS." b
			          WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=$subjectno AND b.userID=$userID AND b.teacherTopicCode in('".$topicsActivatedStr."') ORDER BY b.lastModified DESC LIMIT 4";
			$result = mysql_query($query) or die(mysql_error());
			while ($line = mysql_fetch_array($result))
				$recentTopicsAttempted[$line[0]] = $line[1];

			return $recentTopicsAttempted;
		}

		function getTopicActivated($userID, $childClass, $childSection, $category, $subcategory, $schoolCode, $subjectno, $packageType="All",$freeTrial=0)
		{
			if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0))
			{
				$query = "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode, priority, deactivationDate
					      FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
					      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND a.schoolCode='$schoolCode'";
				if($childClass != "")
				    $query .= " AND a.class =$childClass";
				if($childSection != "")
				    $query .= " AND a.section ='$childSection'";
				$query .= " UNION ";
				$query .= "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode, priority, deactivationDate
					      FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
					      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND userID=$userID ORDER BY priority,deactivationDate DESC";
				$result = mysql_query($query) or die(mysql_error());

				while ($line = mysql_fetch_array($result))
					$teacherTopics[$line[1]] = $line[0];
			}
			elseif (strcasecmp($category,"STUDENT")==0 && strcasecmp($subcategory,"Center")==0)
			{
				$query  = "SELECT DISTINCT teacherTopicDesc, a.teacherTopicCode
					       FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
					       WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND deactivationDate='0000-00-00' AND userID=$userID";
				$result = mysql_query($query) or die(mysql_error());

				while ($line = mysql_fetch_array($result))
					$teacherTopics[$line[1]] = $line[0];
			}
			else
			{
				$query  = "SELECT teacherTopicDesc, teacherTopicCode FROM adepts_teacherTopicMaster
				           WHERE  live =1 AND subjectno=".$subjectno." AND customTopic=0";
				if($packageType=="MS_DEC")
					$query .= " AND teacherTopicCode in (".MS_DEC_TOPICS.")";
				if($freeTrial==1)
				{
					$topicsForFreeTrial = getTopicsForFreeTrial($childClass);
					$query .= " AND teacherTopicCode in ('".implode("','",$topicsForFreeTrial)."')";
				}
				$query .= " ORDER BY classification, teacherTopicOrder, teacherTopicCode";
				if ($childClass<=3){
					$query="SELECT  IF(b.newTTDesc='' OR ISNULL(b.newTTDesc),a.teacherTopicDesc,b.newTTDesc) as 'teacherTopicDesc', a.teacherTopicCode,  b.position
								FROM adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise b ON a.teacherTopicCode=b.teacherTopicCode
								WHERE  subjectno=2 AND customTopic=0 AND live=1 and ( b.class=$childClass) ".($freeTrial==1?" AND a.teacherTopicCode in ('".implode("','",$topicsForFreeTrial)."')":"")."
								ORDER BY CASE WHEN b.position is null then 1 else  0 end, b.position, classification, teacherTopicOrder, a.teacherTopicCode";
				}

				$result = mysql_query($query) or die(mysql_error());
				while ($line=mysql_fetch_array($result))
				{
					$classLevel = getClassLevel($line[1]);
					if(($childClass!="" && !in_array($childClass,$classLevel) && $packageType=="All") || count($classLevel)==0)
						continue;

					$teacherTopics[$line[1]]=isset($line[2])?array("description"=>$line[0],"position"=>$line[2]):$line[0];

				}
			}
			return $teacherTopics;
		}

		function homeworkNotification($schoolCode,$childClass,$childSection)
		{
			$returnSTR = "";
			$sql = "SELECT COUNT(*) FROM adepts_ncertHomeworkActivation WHERE schoolCode='$schoolCode' AND class='$childClass' AND activationDate<='".date("Y-m-d")."'";
			if($childSection != "")
				$sql .= " AND section='$childSection'";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);
			if($row[0] > 0)
				$returnSTR = 'homeworkSelection.php?instruction=yes';
			return($returnSTR);
		}

		function getClassLevel($teacherTopicCode, $flow="MS")
		{
		    $field = "";
        	if(strcasecmp($flow,"MS")==0)
        		$field = "ms_level";
        	elseif (strcasecmp($flow,"CBSE")==0)
        		$field = "cbse_level";
        	elseif (strcasecmp($flow,"ICSE")==0)
        		$field = "icse_level";
			elseif (strcasecmp($flow,"IGCSE")==0)
        		$field = "igcse_level";
        	else
        		$field = "level";
			$query = "SELECT distinct $field
		              FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
		              WHERE  teacherTopicCode='$teacherTopicCode' AND a.clusterCode=b.clusterCode AND b.status='live' AND a.clusterCode NOT LIKE '%100'";
			$result = mysql_query($query) or die(mysql_error());
			$classes = array();
			while($user_row = mysql_fetch_array($result))
			{
				$tmpClassArr = explode(",",$user_row[0]);
				for($i=0; $i<count($tmpClassArr); $i++)
				{
					if(trim($tmpClassArr[$i])!="")
						array_push($classes,trim($tmpClassArr[$i]));
				}

			}
			$classes = array_unique($classes);
			sort($classes,SORT_NUMERIC);
			return $classes;
		}

		function SortDataSet($aArray, $sField, $bDescending = false)
		{
		    $bIsNumeric = IsNumeric($aArray);
		    $aKeys = array_keys($aArray);
		    $nSize = sizeof($aArray);

		    for ($nIndex = 0; $nIndex < $nSize - 1; $nIndex++)
		    {
		        $nMinIndex = $nIndex;
		        $objMinValue = $aArray[$aKeys[$nIndex]][$sField];

		        $sKey = $aKeys[$nIndex];

		        for ($nSortIndex = $nIndex + 1; $nSortIndex < $nSize; ++$nSortIndex)
		        {
		            if ($aArray[$aKeys[$nSortIndex]][$sField] < $objMinValue)
		            {
		                $nMinIndex = $nSortIndex;
		                $sKey = $aKeys[$nSortIndex];
		                $objMinValue = $aArray[$aKeys[$nSortIndex]][$sField];
		            }
		        }

		        $aKeys[$nMinIndex] = $aKeys[$nIndex];
		        $aKeys[$nIndex] = $sKey;
		    }

		    $aReturn = array();
		    for($nSortIndex = 0; $nSortIndex < $nSize; ++$nSortIndex)
		    {
		        $nIndex = $bDescending ? $nSize - $nSortIndex - 1: $nSortIndex;
		        $aReturn[$aKeys[$nIndex]] = $aArray[$aKeys[$nIndex]];
		    }

		    return $bIsNumeric ? array_values($aReturn) : $aReturn;
		}

		function getFailedClusters($userID, $teacherTopicCode)
		{
			//Get the failed clusters in the last completed attempt, if any, or the current attempt
			$failedClusterArray = array();
			$query  = "SELECT ttAttemptID, result, failedClusters FROM ".TBL_TOPIC_STATUS."
			           WHERE  userID=$userID AND teacherTopicCode='$teacherTopicCode' ORDER BY ttAttemptID DESC";
			$result = mysql_query($query);
			$noOfAttempts = mysql_num_rows($result);
			while ($line = mysql_fetch_array($result))
			{
				if(($line[1]!="" && $noOfAttempts>1) || ($noOfAttempts==1))
				{
					if($line[2]!="")
					{
						$tmpCluster = explode(",",$line[2]);

						for($i=0; $i<count($tmpCluster); $i++)
							array_push($failedClusterArray,trim($tmpCluster[$i]));
						break;
					}
				}
			}
			return $failedClusterArray;
		}

		function getLastAttemptedOn($userID,$teacherTopicCode)
		{
			$query  = "SELECT date_format(max(attemptedDate),'%d-%m-%Y') FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE userID=$userID AND teacherTopicCode='$teacherTopicCode'";
			$result = mysql_query($query);
			$line   = mysql_fetch_array($result);
			return $line[0];
		}

		function IsNumeric($aArray)
		{
		    $aKeys = array_keys($aArray);
		    for ($nIndex = 0; $nIndex < sizeof($aKeys); $nIndex++)
		    {
		        if (!is_int($aKeys[$nIndex]) || ($aKeys[$nIndex] != $nIndex))
		        {
		            return false;
		        }
		    }

		    return true;
		}

		function getHtmlForProgress($userID, $progress, $failedClusters, $clusterArray, $sdlArray)
		{
			$totalSDLs = 0;
			$perContribution = array();

			for($counter=0; $counter<count($clusterArray); $counter++)
			{
				$noOfSDLsInCluster = count(explode(",",$sdlArray[$clusterArray[$counter]]));
				$totalSDLs += $noOfSDLsInCluster;
			}
			for($counter=0; $counter<count($clusterArray); $counter++)
			{
				$noOfSDLsInCluster = count(explode(",",$sdlArray[$clusterArray[$counter]]));
				$perContribution[$clusterArray[$counter]] = round($noOfSDLsInCluster*100/$totalSDLs,1);
			}

			$perCovered = 0; $percent = 0; $star = '';
			for($counter=0; $counter<count($clusterArray) && $perCovered<$progress; $counter++)
			{
				$clusterCode = $clusterArray[$counter];
				$per = $perContribution[$clusterCode];

				if($perCovered + $per > 100)
					$per = 100 - $perCovered;
				if(($perCovered + $per) > $progress)
				{
					$width = $progress - $perCovered;
					if(in_array($clusterCode,$failedClusters))
					{
						$percent += $per - 2;

						$star .= '<div style="width:2%;" class="red"></div>';
					}
					else
					{
						$percent += $width;
					}
				}
				elseif(!in_array($clusterCode, $failedClusters))
				{
					$percent += $per;
				}
				else
				{
					$percent += $per- 2;

					$star .= '<div style="width:2%;" class="red"></div>';
				}
				$perCovered += $per;

			}
			$htmlForProgress = '<div class="tachometer">';
			$htmlForProgress .= '<div class="tachometer_fill" style="width: '.( ( ( 135 * $percent) / 100) + 17).'px;"></div>';
			$htmlForProgress .= $star;
			$htmlForProgress .= '</div>';
			//$htmlForProgress .= "<span>$progress%</span>";

			return $htmlForProgress;
		}

		/**
		* Function which converts dates on valid
		* [1] yyyy-mm-dd ---> dd-mm-yyyy
		* [2] dd-mm-yyyy ---> yyyy-mm-dd
		*
		* @param SRTING $DBdate IN yyyy-mm-dd
		* @return STRING IN dd-mm-yyyy
		*/
		function formatDateDB($DBdate)
		{
			if(empty($DBdate))
				return '';
			elseif($DBdate == '0000-00-00')
				return '';
			else{
				$dateParameters=explode("-",$DBdate);
				$newformat=$dateParameters[2]."-".$dateParameters[1]."-".$dateParameters[0];
				return $newformat;
			}
		}

		function checkPendingTimedTest($userID)
		{
			$timedTest = "";
			if (isDailyDrillSchool($_SESSION['childClass'])) return "";
			$rctFlag=checkForRCT($userID);		
			if(!$rctFlag)
			{
				$query  = "SELECT currentTimedTest,pendingTimedTest FROM adepts_timedTestStatus WHERE userID=$userID";
				$result = mysql_query($query);
				if($line   = mysql_fetch_array($result))
				{
					$currentTT = $line['currentTimedTest'];
					$pendingTT = $line['pendingTimedTest'];
					if($currentTT != "")
					    $timedTest = $currentTT;
					else
					{
						if($pendingTT != "")
						{
							$query  = "SELECT count(timedTestID) FROM adepts_timedTestDetails WHERE userID=$userID AND attemptedDate='".date("Y-m-d")."'";

							$result = mysql_query($query);
							$line        = mysql_fetch_array($result);
							if($line[0]==0)        //not more than one timed test given on a day
							{
								$pendingTTArray = explode(",",$pendingTT);
								$query = "SELECT max(attemptedDate) FROM adepts_timedTestDetails WHERE userID=$userID AND timedTestCode='".$pendingTTArray[0]."'";
								$result = mysql_query($query);
								$ttflag = 1;
								if($line = mysql_fetch_array($result))
								{
									$lastAttemptedDate = $line[0];
									$daysLoggedIn = getNoOfDaysLoggedIn($userID,$lastAttemptedDate);
									if($daysLoggedIn > 1)
									    $ttflag = 1;
									else
									    $ttflag = 0;
								}
								if($ttflag)
								{
									$timedTest = $pendingTTArray[0];
									$pendingTT = "";
									for($i=1; $i<count($pendingTTArray); $i++)
									    if($pendingTTArray[$i]!="")
									        $pendingTT .= $pendingTTArray[$i].",";
									$pendingTT = substr($pendingTT,0,-1);
									$query  = "UPDATE adepts_timedTestStatus SET currentTimedTest='$timedTest', pendingTimedTest='$pendingTT' WHERE userID=$userID";
									$result = mysql_query($query);
								}
							}
						}
					}
				}
			}
			return $timedTest;
		}

		function getNoOfDaysLoggedIn($userID, $fromDate="")
		{
		    $query  = "SELECT COUNT(DISTINCT cast(startTime as DATE)) as daysLoggedIn FROM ".TBL_SESSION_STATUS." WHERE userID=$userID";
		    if($fromDate!="")
		        $query .= " AND cast(startTime as Date) >'$fromDate'";
		    $result = mysql_query($query);
		    $line   = mysql_fetch_array($result);
		    return $line['daysLoggedIn'];
		}

		function isActiveCBSEModule($schoolCode, $class, $section)
		{
		    $active = 0;
		    if($schoolCode!="" && $class!="")
		    {
                $query = "SELECT count(srno) FROM adepts_CBSEPaperActivation
                          WHERE  schoolCode=$schoolCode AND class=$class AND deactivationDate='0000-00-00'";
                if($section!="")
                    $query .= " AND section='$section'";
                $result = mysql_query($query);
                $line = mysql_fetch_array($result);
                if($line[0]>0)
                    $active = 1;
		    }

            return $active;
		}

		//For now the two functions below are used in teacherTopicActivationReport.php
		function getCurrentlyActivatedTT($schoolCode,$class,$section,$subjectno='2')
		{
			$teacherTopicArray = array();
			$query = "SELECT a.teacherTopicCode, a.teacherTopicDesc FROM adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
			          WHERE  a.teacherTopicCode=b.teacherTopicCode AND deactivationDate='0000-00-00' AND b.schoolCode=$schoolCode AND b.class=$class AND subjectno LIKE '%$subjectno%'";
			if($section!="")
				$query .= " AND section='$section'";


			$result = mysql_query($query) or die("error: ".$query."  ".mysql_error());
			while ($line = mysql_fetch_array($result))
			{
				$teacherTopicArray[$line[0]] = $line[1];
			}

			return $teacherTopicArray;
		}

		function getClassesInSchool($schoolCode, $class,$section)
		{
			$query = "SELECT distinct childClass, childSection FROM adepts_userDetails
			          WHERE schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND enddate>curdate() AND childClass=$class";

			if($section!="")
				$query .= " AND childSection='$section'";

			$query .= " ORDER BY cast(childClass as unsigned), childSection";

			$result = mysql_query($query) or die(mysql_error());
			$classArray = array();
			while ($line = mysql_fetch_array($result))
			{
				array_push($classArray,$line[0]."-".$line[1]);
			}
			$classArray = array_unique($classArray);
			return $classArray;
		}

		function getTimeSpentCommon($userID, $startDate, $endDate, $mode="all")
		{
			$query = "SELECT DISTINCT sessionID, startTime, endTime FROM ".TBL_SESSION_STATUS."
		              WHERE  userID=$userID AND startTime BETWEEN '$startDate' AND '$endDate 23:59:59'";

			if($mode == "school")
				$query .= " AND (time(startTime) BETWEEN '07:00:00' AND '15:59:59' AND dayname(startTime)<>'Sunday') ";

			elseif($mode == "home")
				$query .= " AND ((cast(startTime as time)>='16:00:00' AND cast(startTime as time)<='23:59:59') OR (cast(startTime as time)>='00:00:01' AND cast(startTime as time)<'07:00:00') OR dayname(startTime)='Sunday') ";

			$time_result = mysql_query($query) or die(mysql_error());
		    $timeSpent = 0;
		    while ($time_line = mysql_fetch_array($time_result))
		    {

		        $startTime = convertToTimeCommon($time_line[1]);
		        if($time_line[2]!="")
		            $endTime = convertToTimeCommon($time_line[2]);
		        else
		        {
		            $query = "SELECT max(lastModified) FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE sessionID=".$time_line[0]." AND userID=".$userID;
					$r     = mysql_query($query);
		            $l     = mysql_fetch_array($r);
		            if($l[0]=="")
		                continue;
		            else
		                $endTime = convertToTimeCommon($l[0]);
		        }
		        $timeSpent = $timeSpent + ($endTime - $startTime);    //in secs
		    }

		    $hours = str_pad(intval($timeSpent/3600),2,"0",STR_PAD_LEFT);    //converting secs to hours.
		    $timeSpent = $timeSpent%3600;
		    $mins  = str_pad(intval($timeSpent/60),2,"0", STR_PAD_LEFT);
		    $timeSpent = $timeSpent%60;
		    $secs  = str_pad($timeSpent,2,"0",STR_PAD_LEFT);

		    return $hours.":".$mins.":".$secs;
		}

		function convertToTimeCommon($date)
		{

		    $hr   = substr($date,11,2);
		    $mm   = substr($date,14,2);
		    $ss   = substr($date,17,2);
		    $day  = substr($date,8,2);
		    $mnth = substr($date,5,2);
		    $yr   = substr($date,0,4);
		    $time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
		    return $time;
		}

		function getAttemptDetailsOnTopic($userID, $arrTopics)
		{
		    $attemptDetails = array();
		    if(count($arrTopics)>0)
		    {
    		    foreach ($arrTopics as $ttCode)
    		      $attemptDetails[$ttCode] = 0;
    		    if(count($arrTopics)>0)
    		    {
                    $topicStr       = "'".implode("','",$arrTopics)."'";
                    $query = "SELECT teacherTopicCode, count(ttAttemptID) FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode in ($topicStr) AND (result='SUCCESS' OR classLevelResult='SUCCESS') GROUP BY teacherTopicCode";                    
                    $result = mysql_query($query) or die(mysql_error());
                    while ($line = mysql_fetch_array($result))
                        $attemptDetails[$line[0]] = $line[1];
    		    }
		    }
            return $attemptDetails;
		}

		function getVideos($class,$userID)
		{
			$progressArr = array();
			$videoDetailsArr = array();
			$i=0;
			$sql = "select videoID,videoTitle,videoFile,thumb,mappingID from adepts_msVideos where mappingType='topicVideo'";
			$result=mysql_query($sql);
			while($line=mysql_fetch_array($result))
			{
				$progressArr = getProgressInTopic($line['mappingID'],$class,$userID);
				/*if($progressArr['progress']>75)
				{*/
					$videoDetailsArr[$i]['videoTitles']=$line['videoTitle'];
					$videoDetailsArr[$i]['videoFiles']=$line['videoFile'];
					$videoDetailsArr[$i]['thumbs']=$line['thumb'];
					$videoDetailsArr[$i]['mappingID']=$line['mappingID'];
					$videoDetailsArr[$i]['videoID']=$line['videoID'];

				/*}*/

				$i++;
			}
			return $videoDetailsArr;
		}

        function getActivities($class, $type="", $packageType="All")
        {
            $arrActivities = array();
            $query  = "SELECT gameID, gameDesc, topicCompletion, teacherTopicCode,type, linkedToCluster FROM adepts_gamesMaster WHERE live='Live' AND find_in_set('$class',class)>0";
            if($type!="")
                $query .= " AND type='$type'";
			else
				$query .= " AND type IN ('regular','enrichment','optional','multiplayer')";
            if($packageType=="MS_DEC")
                $query .= " AND teacherTopicCode in (".MS_DEC_TOPICS.")";
			if($_SESSION['flashContent']==0)
				$query .= " AND ver='html5'";
            $result = mysql_query($query) or die(mysql_error());
            while ($line = mysql_fetch_array($result))
            {
				//if($line[4]=="regular" && checkIfAttempted($line[0])=="1")
				//	continue;
                $arrActivities[$line[0]]["desc"] = $line[1];
                //$arrActivities[$line[0]]["timeLimit"] = $line[2];
                $arrActivities[$line[0]]["topicCompletion"] = $line[2];
                $arrActivities[$line[0]]["teacherTopicCode"] = $line[3];
                $arrActivities[$line[0]]["type"] = $line[4];
                $arrActivities[$line[0]]["linkedToCluster"] = $line[5];
            }
            return $arrActivities;
        }

        function isClusterCompletedSuccesfully($clusterCode)
        {
        	$query  = "SELECT clusterCode FROM adepts_teacherTopicClusterStatus WHERE clusterCode='$clusterCode' AND userID=".$_SESSION['userID']." AND result='SUCCESS' LIMIT 1";        	
        	$rs=mysql_query($query);
        	if($rw=mysql_fetch_array($rs) )
				return 1;
			else
				return 0;
        }
        function isClusterPassedFailedSuccesfully($clusterCode,$userID,$teacherTopicCode)
        {
        	$query  = "SELECT IF(A.result='SUCCESS' || A.result='FAILURE',1,0) as result FROM adepts_teacherTopicClusterStatus A,adepts_teacherTopicStatus B WHERE A.ttAttemptID=B.ttAttemptID and B.teacherTopicCode = '$teacherTopicCode' AND A.userID = $userID and A.clusterCode ='$clusterCode' LIMIT 1";         	        	       	              	    
        	$rs=mysql_query($query);
			if($rw=mysql_fetch_array($rs) )
				return $rw[0];
			else
				return 2;

        }
        function clusterPassedDate($clusterCode,$userID)
        {
        	$query  = "SELECT DATE_FORMAT(lastModified,'%Y-%m-%d') FROM adepts_teacherTopicClusterStatus WHERE clusterCode='$clusterCode' AND userID=".$userID." AND result='SUCCESS' LIMIT 1";

        	$rs=mysql_query($query);
        	$rw=mysql_fetch_row($rs);
        	return $rw[0];
        }
        function getDefaultSchoolFlow($schoolCode)
		{
			$sq = "SELECT settingValue as defaultFlow FROM userInterfaceSettings WHERE schoolCode=$schoolCode and settingName = 'curriculum' limit 1";

			$rs = mysql_query($sq);
			$rw = mysql_fetch_assoc($rs);
			if ($rw['defaultFlow'] == '')
			{
                $rw['defaultFlow'] = 'MS';
            }
			return $rw['defaultFlow'];
		}  
        function getActiveTopicsAndCompletedClusters($schoolCode,$class,$section,$userID,$category,$subcategory,$teacherTopicCode='')
		{
			$arrayTopicCodes = $ttClusterArray = $completedClustersArray = $completedClusters = $completedTTs = $activeTTs=array();
			$arrayClusterList =$arrayMasterClusterList= "";
			$schoolDefaultFlow = getDefaultSchoolFlow($schoolCode);
			if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0))
			{
				$sq = "SELECT DISTINCT A.teacherTopicCode, B.parentTeacherTopicCode,B.customCode,A.flow
					FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B
					 WHERE A.teacherTopicCode=B.teacherTopicCode AND A.schoolCode=$schoolCode AND A.class=$class AND A.section='$section'";
				if($teacherTopicCode != '')
				{
					$sq .=" AND A.teacherTopicCode = '$teacherTopicCode'";
				}
			}
			else
			{
			 	$sq = "SELECT teacherTopicCode, parentTeacherTopicCode,customCode FROM adepts_teacherTopicMaster WHERE subjectno=2 AND live=1 AND customTopic=0";
				if($teacherTopicCode != '')
				{
					$sq .=" AND teacherTopicCode = '$teacherTopicCode'";
				}
				$sq .=" ORDER BY classification, teacherTopicOrder, teacherTopicCode";
			}
			$rs = mysql_query($sq);
			$key = 0;
			while($rw = mysql_fetch_array($rs))
			{
				$flow = $rw["flow"];
				$fieldName = "level";
				if($flow=="MS")
					$fieldName = "ms_level";
				elseif($flow=="CBSE")
					$fieldName = "cbse_level";
				elseif($flow=="ICSE")
					$fieldName = "icse_level";
				elseif($flow=="IGCSE")
					$fieldName = "igcse_level";
				else
					$fieldName = strtolower($schoolDefaultFlow)."_level";

				if($rw[2]!="")
				{
					$sqClusterList = "SELECT clusterCodes FROM adepts_customizedTopicDetails WHERE code='".$rw[2]."'";

					$rsClusterList = mysql_query($sqClusterList);
					$rwClusterList = mysql_fetch_array($rsClusterList);

					$sqClusterList2 = "SELECT GROUP_CONCAT(A.clusterCode) FROM adepts_teacherTopicClusterMaster A, adepts_clusterMaster B
									  WHERE A.clusterCode=B.clusterCode AND FIND_IN_SET($class,$fieldName) AND teacherTopicCode='".$rw[1]."' AND status='Live'";					  
					$mClusterList = mysql_query($sqClusterList2);
					$masterClusterList = mysql_fetch_array($mClusterList);
					$ttClusterArray[$key]['custom_ttcode'] = $rw[0];
					$ttClusterArray[$key]['ttcode'] = $rw[1];
					$ttClusterArray[$key]['clusters'] = $rwClusterList[0].",".$masterClusterList[0];

					$arrayClusterList .= $rwClusterList[0].",".$masterClusterList[0].",";
				}
				else
				{
					$sqClusterList = "SELECT GROUP_CONCAT(A.clusterCode) FROM adepts_teacherTopicClusterMaster A, adepts_clusterMaster B 
									  WHERE A.clusterCode=B.clusterCode AND FIND_IN_SET($class,$fieldName) AND teacherTopicCode='".$rw[0]."' AND status='Live'";									  
					$rsClusterList = mysql_query($sqClusterList);
					$rwClusterList = mysql_fetch_array($rsClusterList);

						$ttClusterArray[$key]['custom_ttcode'] = '';
						$ttClusterArray[$key]['ttcode'] = $rw[0];
						$ttClusterArray[$key]['clusters'] = $rwClusterList[0];
						$arrayClusterList .= $rwClusterList[0].",";
				}
								
				$key++;
			}					
			// $arrayClusterList = str_replace(" ","",substr($arrayClusterList,0,-1));
			// $allClusters = $arrayMasterClusterList.$arrayClusterList;				
			$sqList = "SELECT DISTINCT(b.teacherTopicCode) as teacherTopicCode from adepts_teacherTopicStatus a left join adepts_teacherTopicMaster b on b.teacherTopicCode = a.teacherTopicCode where userID=".$userID." and progress=100.00";								 
			$rsttList = mysql_query($sqList);
			while ($line = mysql_fetch_array($rsttList))
            {
                $rwttList[] = $line['teacherTopicCode'];
            }	
			$completedTTs = array_unique($rwttList);	
			if(!empty($ttClusterArray))
			{
				foreach($ttClusterArray as $allTTs)
				{
					if(in_array($allTTs['ttcode'], $completedTTs) || in_array($allTTs['custom_ttcode'], $completedTTs))
					{
						$completedClustersArray[] = array_unique(explode(',', $allTTs['clusters']));
					}
					$activeTTs[]=$allTTs['ttcode'];				
				}	
			}			
								
			$completedClusters = array_unique(array_reduce($completedClustersArray, 'array_merge', array()));			
			$arrayTopicCodes["activeTopicClusters"] = array_unique(array_filter(explode(",",$arrayClusterList)));
			// $arrayTopicCodes["allClusters"] = array_unique(array_filter(explode(",",$allClusters)));
			$arrayTopicCodes["completedTTs"] =$completedTTs;
			$arrayTopicCodes["completedClusters"] =$completedClusters;
			$arrayTopicCodes["activeTopics"] =array_unique($activeTTs);
			return $arrayTopicCodes;
		}
		function getClusterCodeDesc($clusterCode)
		{			
			$query	=	"SELECT cluster as clsuerDesc FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
			$result = 	mysql_query($query) or die("error in query:  ".mysql_error());
			$line	=	mysql_fetch_array($result);
			return $line[0];
		}
		function getpractisetestdetails($class)
        {
            $arrActivities = array();
			$userID = $_SESSION['userID'];
            $query  = "SELECT practiceid, clusterCode, Qcodestr, status,thumbnail,topicName FROM practiceTestMaster WHERE class=$class";
          
            $result = mysql_query($query) or die(mysql_error());
            while ($line = mysql_fetch_array($result))
            {
                $arrActivities[$line[0]]["practiceid"] = $line[0];
                $arrActivities[$line[0]]["clusterCode"] = $line[1];
                $arrActivities[$line[0]]["Qcodestr"] = $line[2];
                $arrActivities[$line[0]]["status"] = $line[3];
				$arrActivities[$line[0]]["thumbnail"] = $line[4];
				$arrActivities[$line[0]]["topicName"] = $line[5];

				 $dataquery  = "SELECT status FROM practiceTestStatus WHERE practiceid=$line[0] and userID = $userID";
				 $resulttest = mysql_query($dataquery) or die(mysql_error());
				 if(mysql_num_rows ($resulttest) > 0)
				{
				  while ($data = mysql_fetch_array($resulttest))
					{
						$arrActivities[$line[0]]["clusterstatus"] = $data[0];
					}
				}else{
						$arrActivities[$line[0]]["clusterstatus"] = 0;
				}
            }
            return $arrActivities;
        }


		
		function checkIfAttempted($gameID)
		{
			$sq	=	"SELECT gameID FROM adepts_userGameDetails WHERE gameID=$gameID AND userID=".$_SESSION["userID"]." LIMIT 1";
			$rs	=	mysql_query($sq);
			if($rw=mysql_fetch_array($rs) )
				return 1;
			else
				return 0;
		}

        function getTimeSpentOnActivities($userID, $arrActivities, $fromDate, $tillDate)
        {
            $arrTimeSpent = array();
            if(count($arrActivities)>0)
            {
                $activityStr = implode(",",array_keys($arrActivities));
                $query = "SELECT gameID, sum(timeTaken) as timeSpent FROM adepts_userGameDetails WHERE userID=$userID AND gameID in ($activityStr) AND attemptedDate>='$fromDate' AND attemptedDate<='$tillDate' GROUP BY gameID";
                $result = mysql_query($query);
                while ($line = mysql_fetch_array($result)) {
                    $arrTimeSpent[$line['gameID']] = round($line['timeSpent']/60,1);    //Convert to mins
                }
            }

            return $arrTimeSpent;
        }

        function getTopicsForHomeUsage($schoolCode, $class, $section, $withClassification=0)
        {
            $arrHomeTopics = array();
            $query = "SELECT distinct a.teacherTopicCode, teacherTopicDesc, classification, b.live
                      FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
                      WHERE  a.teacherTopicCode=b.teacherTopicCode AND a.schoolCode=$schoolCode AND a.class=$class AND deactivationdate<>'0000-00-00'";
            if($section!="")
                $query .= " AND a.section='$section'";
            $query  .= " AND a.teacherTopicCode NOT IN (";  //Remove currently active topics
            $query  .= "SELECT distinct teacherTopicCode FROM   adepts_teacherTopicActivation
                      WHERE  schoolCode=$schoolCode AND class=$class AND deactivationdate='0000-00-00'";
            if($section!="")
                $query .= " AND section='$section'";
            $query .= ")";
            //echo $query;
            $result = mysql_query($query) or die(mysql_error().$query);
            $topic = "";
            while ($line = mysql_fetch_array($result))
            {
                if($withClassification)
                {
                    if($topic!=$line['classification'])
    				{
    					$topic = $line['classification'];
    					$srno=0;
    				}
    				$arrHomeTopics[$topic][$srno][0] = $line[1];
    				$arrHomeTopics[$topic][$srno][1] = $line[3];
    				$arrHomeTopics[$topic][$srno][2] = $line[0];
    				$srno++;
                }
                else
                    $arrHomeTopics[$line[0]] = $line[1];
            }


            return $arrHomeTopics;
        }

		function getTeacherTopicDesc($ttcode)
		{
			$userID=$_SESSION['userID'];
			$user=new User($userID);
			if(!(strcasecmp($user->category,"STUDENT")==0 && (strcasecmp($user->subcategory,"SCHOOL")==0 || strcasecmp($user->subcategory,"Home Center")==0)) && $user->childClass<=3) {
				$query = "SELECT IF(c.newTTDesc='' OR ISNULL(c.newTTDesc),a.teacherTopicDesc,c.newTTDesc) as 'teacherTopicDesc'
							FROM   adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise c ON a.teacherTopicCode=c.teacherTopicCode
							WHERE a.teacherTopicCode='$ttcode'";
			}
			else 
				$query	=	"SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttcode'";
			$result = 	mysql_query($query) or die("error in query:  ".mysql_error());
			$line	=	mysql_fetch_array($result);
			return $line[0];
		}

    	function getMisconceptionSdls($cls,$ttCode,$clusterCode)
    	{
    		$sdlArr = array();

    		$query = "SELECT misconception_sdls as sdlList FROM adepts_misconception WHERE teacherTopicCode='$ttCode' AND class='$cls' AND clusterCode='$clusterCode'";
    		$result = mysql_query($query) or die("error in query:  ".mysql_error());

    		while ($line=mysql_fetch_array($result)) {
    			$sdlList = $line[0];
    		}

    		if($sdlList != "")
    			$sdlArr = explode(",",$sdlList);

    		return $sdlArr;
    	}
		
		function getMisconceptionSdlsForTopic($ttCode,$class)
		{
			$sdlArr = array();

    		$query = "SELECT clusterCode, group_concat(misconception_sdls) as sdlList FROM adepts_misconception WHERE teacherTopicCode='$ttCode' AND class='$class' GROUP BY clusterCode";
    		$result = mysql_query($query) or die("error in query:  ".mysql_error());

    		while ($line=mysql_fetch_array($result)) {
    			$sdlArr[$line[0]] = $line[1];
    		}
    		return $sdlArr;
			
		}
		
		function getMisconceptionSdlsForTopic1($ttCode,$class,$para="")
		{
			$sdlArr = array();
			$qcodeList = "";
			$sq = "SELECT clusterCodes FROM adepts_customizedTopicDetails A, adepts_teacherTopicMaster B WHERE customCode=code AND teacherTopicCode='$ttCode'";
			$rs = mysql_query($sq);
			if($rw = mysql_fetch_array($rs))
			{
				$clusterCodes = str_replace(",","','",$rw[0]);
				$query = "SELECT B.clusterCode, subDifficultyLevel, A.qcode 
						  FROM adepts_questionPerformance A, adepts_questions B 
						  WHERE A.qcode=B.qcode AND class=$class AND A.majorVersion=B.majorVersion AND B.clusterCode IN ('$clusterCodes') 
						  GROUP BY B.clusterCode,subDifficultyLevel HAVING  AVG(accuracy) < 60 AND SUM(noOfAttempts) > 100 LIMIT 20";
			}
			else
			{
				$query = "SELECT B.clusterCode, subDifficultyLevel, A.qcode 
						  FROM adepts_questionPerformance A, adepts_questions B, adepts_teacherTopicClusterMaster C 
						  WHERE A.qcode=B.qcode AND B.clusterCode=C.clusterCode AND teacherTopicCode='$ttCode' AND class=$class AND A.majorVersion=B.majorVersion 
						  GROUP BY B.clusterCode,subDifficultyLevel HAVING  AVG(accuracy) < 60 AND SUM(noOfAttempts) > 100 LIMIT 20";
			}
    		$result = mysql_query($query) or die("error in query:  ".mysql_error());

    		while ($line=mysql_fetch_array($result)) { 
    			$sdlArr[$line[0]] .= $line[1].",";
				$qcodeList .= $line[2].",";
    		}
			foreach($sdlArr as $clusterCode=>$sdls)
			{
				$sdlArr[$clusterCode] = substr($sdls,0,-1);
			}
			if($para=="forDev")
				return substr($qcodeList,0,-1);
			else
    			return $sdlArr;
		}

        function getMisconceptionsTaggedInTT($ttCode)
		{
				$taggedMisconception = array();
				$query = "SELECT GROUP_CONCAT(DISTINCT misconception) FROM adepts_questions a, adepts_teacherTopicClusterMaster b WHERE a.clusterCode=b.clusterCode AND teacherTopicCode='$ttCode' AND misconception IS NOT NULL AND misconception<>'';";
				$result = mysql_query($query) or die("error in query:  ".mysql_error());
				if($line = mysql_fetch_array($result))
					{
					$CSV = $line[0];
						$taggedMisconception = explode(",",$CSV);
						$taggedMisconception = array_unique($taggedMisconception);
				}
				return $taggedMisconception;
		}
        
    	function shuffle_assoc($array) {
            $keys = array_keys($array);
            shuffle($keys);
            foreach($keys as $key) {
                $new[$key] = $array[$key];
            }
            $array = $new;
            return $array;
        }

        function isAllowedDeactivatedTopicsForHome($schoolCode,$class,$section)
        {
            $allowed = 0;
            /*$query = "SELECT allowDeactivatedTopicsAtHome FROM adepts_schoolRegistration WHERE school_code=$schoolCode";*/

            $query = "SELECT settingValue FROM userInterfaceSettings WHERE schoolCode='$schoolCode' and settingName='deactivatedTopicsAtHome' and class='$class' and section='$section'";

            $result = mysql_query($query) or die(mysql_error());
            if(mysql_num_rows($result)>0)
            {
                $line = mysql_fetch_assoc($result);
               		if($line['settingValue'] == 'CustomOn' || $line['settingValue'] == 'On')
               				$allowed =1;
            }
            return $allowed;

	
        }

        function isHomeUsage($schoolCode, $class, $year="2011")
        {
            $homeUsage = 0;
            $timingDetails = getSchoolTimings($schoolCode, $class, $year);
            $startTime = strtotime($timingDetails["startTime"]);  //School start time
            $endTime   = strtotime($timingDetails["endTime"]);  //School end time
            //$_tH = (int)date('G');
            $now = time();
            //Consider after 5 p.m. and before 7 a.m. as home usage and Sundays
            //if ((($_tH >= 17) || ($_tH < 7) ) || date("D")=="Sun")
            if ((($now < $startTime) || ($now > $endTime) ) || date("D")=="Sun")
            {
                $homeUsage = 1;
            }
			if($homeUsage==0)
			{
				if(checkOtherOff($schoolCode))
					$homeUsage = 1;
			}
            return $homeUsage;
        }
		
		function checkOtherOff($schoolCode)
		{
			$sq = "SELECT otherOff FROM educatio_educat.ms_orderMaster WHERE schoolCode = $schoolCode AND otherOff <> '' AND otherOff <> 'none' ORDER BY year DESC LIMIT 1";
			$rs = mysql_query($sq);
			if($rw = mysql_fetch_array($rs))
			{
				if($rw[0]=="saturday" && date("D")=="Sat")
					return true;
				else if($rw[0]=="2ndAnd4thSaturday")
				{
					$month = date("M");
					$year = date("Y");
					if(date('d-m-Y',strtotime("+1 week sat $month $year")) == date("d") || date('d-m-Y',strtotime("+3 week sat $month $year")) == date("d"))
						return true;
					else
						return false;	
				}
				else
					return false;
			}
			else
				return false;
		}

        function getSchoolTimings($schoolCode, $class, $year="2011")
        {
            $timingDetails = array();
            $query = "SELECT startTime, endTime FROM adepts_schoolTimingDetails WHERE schoolCode=$schoolCode AND $class BETWEEN fromClass AND toClass ORDER BY id DESC LIMIT 1";
            $result = mysql_query($query);
            if($line = mysql_fetch_array($result))
            {
                $timingDetails["startTime"] = $line[0];
                $timingDetails["endTime"]   = $line[1];
            }
            else
            {
                $timingDetails["startTime"] = "07:00:00";
                $timingDetails["endTime"]   = "16:00:00";
            }
            return $timingDetails;

        }

        function getClassesForSchool($schoolCode, $year="2011")
        {
            $clsArray = array();
            /*$query  = "SELECT DISTINCT class, section FROM educatio_educat.ms_orderMaster a, educatio_educat.ms_studentBreakup b
                       WHERE  a.order_id=b.order_id AND year='$year' AND schoolCode=$schoolCode ORDER BY class";*/
            $query = "SELECT distinct childClass, group_concat(distinct childSection ORDER BY childSection) FROM adepts_userDetails
                      WHERE  schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND endDate>curdate() GROUP BY childClass
                      ORDER BY cast(childClass as unsigned)";
            $result = mysql_query($query);
            while ($line = mysql_fetch_array($result))
                $clsArray[$line[0]] = $line[1];
            return $clsArray;
        }

        function dateDiff($dformat, $endDate, $beginDate)
        {
            $date_parts1=explode($dformat, $beginDate);
            $date_parts2=explode($dformat, $endDate);
            $start_date=gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
            $end_date=gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);

            return (($end_date - $start_date)+1);
        }

        function checkPrePostTestAvailability($teacherTopicCode, $class)
        {
            $available = array("MS"=>0, "CBSE"=>0, "ICSE"=>0, "IGCSE"=>0);
            if(strcasecmp($_SESSION['admin'],"Home Center Admin")!=0)	//Prepost test activation not to be made available for Home center
            {
	            $query  = "SELECT ms_class, cbse_class, icse_class, igcse_class FROM adepts_prepostTestMaster
	                       WHERE  teacherTopicCode='$teacherTopicCode' AND (ms_class=$class OR cbse_class=$class OR icse_class=$class OR igcse_class=$class) AND live=1";
	            $result = mysql_query($query) or die(mysql_error());
	            while($line = mysql_fetch_array($result))
	            {
	                if($line['ms_class']==$class)
	                	$available["MS"] = 1;
	                if($line['cbse_class']==$class)
	                	$available["CBSE"] = 1;
	                if($line['icse_class']==$class)
	                	$available["ICSE"] = 1;
					if($line['igcse_class']==$class)
	                	$available["IGCSE"] = 1;
	            }
            }
            return $available;
        }


        function getPrePostTestQuestions($teacherTopicCode, $type)
        {
            $context = isset($_SESSION['context'])?$_SESSION['context']:"India";
            $arrQcode = array();
            $query  = "SELECT preTestCluster, postTestCluster FROM adepts_prepostTestMaster WHERE teacherTopicCode='$teacherTopicCode'";
            $result = mysql_query($query);
            $line = mysql_fetch_array($result);
            if($type=="pretest")
                $clusterCode = $line['preTestCluster'];
            else
                $clusterCode = $line['postTestCluster'];
            $query = "SELECT qcode FROM adepts_questions WHERE clusterCode='$clusterCode' AND  AND status='3' AND context in ('Global','$context') ORDER BY subdifficultylevel";
            $result = mysql_query($query);
            while ($line = mysql_fetch_array($result))
                array_push($arrQcode, $line['qcode']);
            return $arrQcode;
        }

        function getNoOfQuesAttemptedInTheTopic($userID, $teacherTopicCode)
        {
            $quesAttemptArray = array();
            $q = "SELECT count(srno), sum(if(R=1,1,0)) FROM ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b, ".TBL_QUES_ATTEMPT_CLASS." c
                  WHERE  a.userID=b.userID AND b.userID=c.userID AND a.userID=$userID AND b.ttAttemptID=a.ttAttemptID AND b.clusterAttemptID=c.clusterAttemptID AND a.teacherTopicCode='$teacherTopicCode'";
    	    $r = mysql_query($q) or die(mysql_error());
    	    $l = mysql_fetch_array($r);
    	    $quesAttemptArray["quesAttempted"] = $l[0];
    	    if($l[0]>0)
    	        $quesAttemptArray["perCorrect"] = round($l[1]/$l[0]*100,1);
    	    else
    	        $quesAttemptArray["perCorrect"] = "";
            return $quesAttemptArray;
        }
		function getNoOfPracticeQuesAttemptedInTheTopic($userID, $teacherTopicCode)
		{
			$practiceQuesAttemptArray = array();
            $q = "SELECT count(srno) FROM ".TBL_TOPIC_REVISION."
                  WHERE  userID=$userID AND teacherTopicCode='$teacherTopicCode'";
    	    $r = mysql_query($q) or die(mysql_error().$q);
    	    if($l = mysql_fetch_array($r))
				$practiceQuesAttemptArray["quesAttempted"] = $l[0];
			else
				$practiceQuesAttemptArray["quesAttempted"] = 0;

			$query=mysql_query("SELECT GROUP_CONCAT(d.practiseModuleId),COUNT(d.practiseModuleId)
			FROM adepts_teacherTopicMaster b, adepts_customizedTopicDetails c,adepts_clusterMaster a
				INNER JOIN adepts_teacherTopicClusterStatus q ON a.clusterCode = q.clusterCode INNER JOIN practiseModuleDetails  d ON a.clusterCode=d.linkedToCluster  
			WHERE b.teacherTopicCode ='$teacherTopicCode' AND b.customCode=c.code AND  b.customTopic=1 AND FIND_IN_SET(a.clusterCode ,c.clusterCodes) AND  q.userID = $userID AND q.result='SUCCESS'");
			$pl=mysql_fetch_array($query);
			if ($pl[1]==0){
				$query = mysql_query("SELECT GROUP_CONCAT(d.practiseModuleId),count(d.practiseModuleId) FROM adepts_teacherTopicClusterMaster b inner join adepts_teacherTopicClusterStatus c on b.clusterCode = c.clusterCode  
						INNER JOIN practiseModuleDetails d ON c.clusterCode=d.linkedToCluster where c.userID = $userID AND c.result='SUCCESS' and b.teacherTopicCode = '$teacherTopicCode'");
				$pl=mysql_fetch_array($query);
			}
			$practiseModules=$pl[0];
	        $q = "SELECT count(id) FROM practiseModulesQuestionAttemptDetails
	              WHERE  userID=$userID AND FIND_IN_SET(practiseModuleId,'$practiseModules')";
		    $r = mysql_query($q) or die(mysql_error().$q);
    	    if($l = mysql_fetch_array($r))
				$practiceQuesAttemptArray["quesAttempted"] = $practiceQuesAttemptArray["quesAttempted"]+$l[0];
			else
				$practiceQuesAttemptArray["quesAttempted"] = $practiceQuesAttemptArray["quesAttempted"]+0;



			return $practiceQuesAttemptArray;

		}
		function isAttemptedByUser($userObj,$ttCode)
		{
			$boolReturn = false;
			$sql = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID='$userObj->userID'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) == 0)
				$boolReturn = true;
			return $boolReturn;
		}
		function getTopicsActivatedByLastFiveDays($userObj)
		{
			$returnArray = array();
			$last5date = date('Y-m-d',strtotime("-5 days"));
			$query = "SELECT teacherTopicCode FROM adepts_teacherTopicActivation WHERE deactivationDate='0000-00-00' AND schoolCode='$userObj->schoolCode' AND activationDate>'$last5date'";
			if($userObj->childClass != "")
				$query .= " AND class ='$userObj->childClass'";
			if($userObj->childSection != "")
				$query .= " AND section ='$userObj->childSection'";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result))
			{
				$returnArray[] = $row[0];
			}
			return $returnArray;
		}

		function getPrePostTestID($ttCode, $class, $flow)
		{
			$fieldName = "ms_class";
			if(strcasecmp($flow,"MS")==0)
				$fieldName = "ms_class";
			else if(strcasecmp($flow,"CBSE")==0)
				$fieldName = "cbse_class";
			else if(strcasecmp($flow,"ICSE")==0)
				$fieldName = "icse_class";
			else if(strcasecmp($flow,"IGCSE")==0)
				$fieldName = "igcse_class";
			$query  = "SELECT prepostTestID FROM adepts_prepostTestMaster WHERE teacherTopicCode='$ttCode' AND live=1 AND $fieldName=$class";
			$result = mysql_query($query);
			$line   = mysql_fetch_array($result);
			$prepostTestID = $line[0];
			return $prepostTestID;
		}
		
	function getStudentDetails($cls, $schoolCode, $section)
	{
		$userArray = array();
		$query = "SELECT userID, childName, concat(childClass,if(isnull(childSection),'', childSection)) as cls
				  FROM   adepts_userDetails
				  WHERE  category='STUDENT' AND endDate>curdate() AND enabled=1  AND schoolCode =$schoolCode AND childClass='$cls' AND subjects like '%".SUBJECTNO."%'";
				  
		if($section!="")
				$query .= " AND childSection = '$section'";
	
		$query .= " ORDER BY cls, childName";
		$r = mysql_query($query) or die($query."<br/>".mysql_error());
		while($l = mysql_fetch_array($r))
		{
			$userArray[$l[0]][0] = $l[1];
			$userArray[$l[0]][1] = $l[2];
		}
		/*$userArray = array();
		$userArray[100204][0] = "Manish Dariyani";
		$userArray[100204][1] = "6C";*/
		return $userArray;
	}
	
	function getThumbnailPathOfGame($gameID,$type)
	{
		if($type=="enrichment")
			$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/enrichments/".$gameID;
		else
			$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/games/".$gameID;
		$sq	=	"SELECT thumbImg FROM adepts_gamesMaster WHERE gameID=$gameID";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		if($rw[0])
			return $folderPath."/".$rw[0];
		else
			return "";
	} 
    /*function getThumbnailPathOfGame($gameID,$type)
	{
		if($type=="enrichment")
			$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/enrichments/".$gameID;
		else
			$folderPath	=	ENRICHMENT_MODULE_FOLDER."/html5/games/".$gameID;
		$sq	=	"SELECT thumbImg FROM adepts_gamesMaster WHERE gameID=$gameID";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		if($rw[0])
			return $folderPath."/".$rw[0];
		else
			return "";
	}*/


function getTimedTestAttemptedInSession($userID,$sessionID)
{
	$timedTestArray = array();
	$query = "SELECT a.timedTestCode, description, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified,linkedToCluster
	          FROM   adepts_timedTestMaster a, adepts_timedTestDetails b
	          WHERE  a.timedTestCode=b.timedTestCode AND userID=$userID AND sessionID=$sessionID ORDER BY b.lastModified DESC";

	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$timedTestArray[$srno]["timedTestCode"] = $line[0];
		$timedTestArray[$srno]["description"] = $line[1];
		$timedTestArray[$srno]["quesCorrect"] = $line[2];
		$timedTestArray[$srno]["timeTaken"] = $line[3];
		$timedTestArray[$srno]["perCorrect"] = $line[4];
		$timedTestArray[$srno]["noOfQuesAttempted"] = $line[5];
		$timedTestArray[$srno]["attemptedOn"] = $line[6];
		$timedTestArray[$srno]["cluster"] = $line[7];
		$srno++;
	}
	return $timedTestArray;
}

function getTimedTestAttemptedIndaySession($userID,$date)
{
	$timedTestArray = array();
	$query = "SELECT a.timedTestCode, description, quesCorrect, timeTaken, perCorrect, noOfQuesAttempted, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified,linkedToCluster,b.sessionID
	          FROM   adepts_timedTestMaster a, adepts_timedTestDetails b
	          WHERE  a.timedTestCode=b.timedTestCode AND userID=$userID AND  b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";

	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$timedTestArray[$srno]["timedTestCode"] = $line[0];
		$timedTestArray[$srno]["description"] = $line[1];
		$timedTestArray[$srno]["quesCorrect"] = $line[2];
		$timedTestArray[$srno]["timeTaken"] = $line[3];
		$timedTestArray[$srno]["perCorrect"] = $line[4];
		$timedTestArray[$srno]["noOfQuesAttempted"] = $line[5];
		$timedTestArray[$srno]["attemptedOn"] = $line[6];
		$timedTestArray[$srno]["cluster"] = $line[7];
		$timedTestArray[$srno]["sessionID"] = $line[8];
		$srno++;
	}
	return $timedTestArray;
}

function getGamesAttemptedInSession($userID,$sessionID)
{
	$gamesArray = array();
	$query = "SELECT a.gameID, a.gameDesc, score, timeTaken, gameLevel, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, linkedToCluster, srno
	          FROM   adepts_gamesMaster a, adepts_userGameDetails b
	          WHERE  a.gameID=b.gameID AND userID=$userID AND sessionID=$sessionID order by b.lastModified desc";
	
	$result = mysql_query($query) or die("Error in fetching game details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{		
			$gamesArray[$srno]["gameID"] = $line[0];
			$gamesArray[$srno]["description"] = $line[1];
			$gamesArray[$srno]["score"] = $line[2];
			$gamesArray[$srno]["timeTaken"] = $line[3];
			$gamesArray[$srno]["level"] = $line[4];
			$gamesArray[$srno]["attemptedOn"] = $line[5];
			$gamesArray[$srno]["cluster"] = $line[6];
			$gamesArray[$srno]["srno"] = $line[7];
			$gameId=$line[0];

			$levelArr = activityLevelDetail($line[7]);
			$levelArray = explode('~', $levelArr);
			
			$gamesArray[$srno]["totalLevel"] = $levelArray[0];
			$gamesArray[$srno]["levelCleared"] = $levelArray[1];
			$gamesArray[$srno]["activityTimeTaken"] = $levelArray[2];			
			
			$srno++;	
	}	
	return $gamesArray;
}

function getGamesAttemptedInDAYSession($userID,$date)
{
	$gamesArray = array();
	$query = "SELECT a.gameID, a.gameDesc, score, timeTaken, gameLevel, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, linkedToCluster, srno , b.sessionID
	          FROM   adepts_gamesMaster a, adepts_userGameDetails b
	          WHERE  a.gameID=b.gameID AND userID=$userID AND b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";
	$result = mysql_query($query) or die("Error in fetching game details!");
	
	
	$srno = 0;	
	$sessionId=0;
	
	
	while ($line = mysql_fetch_array($result))
	{		
			$gamesArray[$srno]["gameID"] = $line[0];
			$gamesArray[$srno]["description"] = $line[1];
			$gamesArray[$srno]["score"] = $line[2];
			$gamesArray[$srno]["timeTaken"] = $line[3];
			$gamesArray[$srno]["level"] = $line[4];
			$gamesArray[$srno]["attemptedOn"] = $line[5];
			$gamesArray[$srno]["cluster"] = $line[6];
			$gamesArray[$srno]["srno"] = $line[7];
			$gamesArray[$srno]["sessionID"] = $line[8];
			$sessionId=$line[8];
			$gameId=$line[0];
			$levelArr = activityLevelDetail($line[7]);
			$levelArray = explode('~', $levelArr);
			
			$gamesArray[$srno]["totalLevel"] = $levelArray[0];
			$gamesArray[$srno]["levelCleared"] = $levelArray[1];
			$gamesArray[$srno]["activityTimeTaken"] = $levelArray[2];
			$srno++;		
	}	
	return $gamesArray;
}

function getRemedialItemAttemptsInDaySession($userID,$date)
{
	$gamesArray = array();
	$query = "SELECT a.remedialItemCode, a.remedialItemDesc, result, timeTaken, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, remedialAttemptID, sessionID 
	          FROM   adepts_remedialItemMaster a, adepts_remedialItemAttempts b 
	          WHERE  a.remedialItemCode=b.remedialItemCode AND userID=$userID AND b.lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY b.lastModified DESC";
	$result = mysql_query($query) or die("Error in fetching game details!");
	
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$remedialItemAttemptsArray[$srno]["remedialItemCode"] = $line[0];
		$remedialItemAttemptsArray[$srno]["description"] = $line[1];
		$remedialItemAttemptsArray[$srno]["result"] = $line[2];
		$remedialItemAttemptsArray[$srno]["timeTaken"] = $line[3];
		$remedialItemAttemptsArray[$srno]["attemptedOn"] = $line[4];
		$remedialItemAttemptsArray[$srno]["srno"] = $line[5];
		$remedialItemAttemptsArray[$srno]["sessionID"] = $line[6];

		$levelArr = activityLevelDetail($line[5]);
		$levelArray = explode('~', $levelArr);
		
		$remedialItemAttemptsArray[$srno]["totalLevel"] = $levelArray[0];
		$remedialItemAttemptsArray[$srno]["levelCleared"] = $levelArray[1];
		$remedialItemAttemptsArray[$srno]["activityTimeTaken"] = $levelArray[2];
		$srno++;

		
	}
	return $remedialItemAttemptsArray;
}

function getRemedialItemAttempts($userID, $sessionID)
{
	$remedialItemAttemptsArray = array();
	$query = "SELECT a.remedialItemCode, a.remedialItemDesc, result, timeTaken, date_format(b.lastModified, '%d-%m-%Y %H:%i:%s') lastModified, remedialAttemptID, sessionID 
	          FROM   adepts_remedialItemMaster a, adepts_remedialItemAttempts b 
	          WHERE  a.remedialItemCode=b.remedialItemCode AND userID=$userID AND sessionID=$sessionID ORDER BY b.lastModified DESC";
	$result = mysql_query($query) or die("Error in fetching timed test details!");
	$srno = 0;
	while ($line = mysql_fetch_array($result))
	{
		$remedialItemAttemptsArray[$srno]["remedialItemCode"] = $line[0];
		$remedialItemAttemptsArray[$srno]["description"] = $line[1];
		$remedialItemAttemptsArray[$srno]["result"] = $line[2];
		$remedialItemAttemptsArray[$srno]["timeTaken"] = $line[3];
		$remedialItemAttemptsArray[$srno]["attemptedOn"] = $line[4];
		$remedialItemAttemptsArray[$srno]["srno"] = $line[5];
		$remedialItemAttemptsArray[$srno]["sessionID"] = $line[6];

		$levelArr = activityLevelDetail($line[5]);
		$levelArray = explode('~', $levelArr);
		
		$remedialItemAttemptsArray[$srno]["totalLevel"] = $levelArray[0];
		$remedialItemAttemptsArray[$srno]["levelCleared"] = $levelArray[1];
		$remedialItemAttemptsArray[$srno]["activityTimeTaken"] = $levelArray[2];
		$srno++;

		
	}
	return $remedialItemAttemptsArray;
}

function getAllAttemptedGamesByUser($userID,$childClass)
{
	$arrGameAttempted = array();
	$query	=	"SELECT DISTINCT a.gameID FROM adepts_userGameDetails a, adepts_gamesMaster b
				 WHERE a.gameID=b.gameID AND userID=$userID AND live='Live' AND find_in_set('$childClass',class)>0";
	$result = mysql_query($query);
	while($line = mysql_fetch_array($result))
	{
		$arrGameAttempted[] = $line['gameID'];
	}
	return $arrGameAttempted;
}

function getNewActivities($userID,$childClass)
{
	$arrGameAttempted = array();
	$query	=	"SELECT DISTINCT a.gameID FROM adepts_userGameDetails a, adepts_gamesMaster b
				 WHERE a.gameID=b.gameID AND userID=$userID AND live='Live' AND find_in_set('$childClass',class)>0 AND liveDate > DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 7 DAY)";
	$result = mysql_query($query);
	while($line = mysql_fetch_array($result))
	{
		$arrGameAttempted[] = $line['gameID'];
	}
	return $arrGameAttempted;
}

function validateOldPassword($userID,$oldPassword)
{
	if($userID){
	$query = "SELECT userID FROM adepts_userDetails WHERE
				password = password('".mysql_escape_string($oldPassword)."')
			 AND userID = $userID ";

	$result = mysql_query($query) or die("# $query #".mysql_error());// get res

    //var_dump(mysql_error());
    if(mysql_num_rows($result)){
    	return true;
     }
     else return false;
	}
}

function getParentTeacherTopic($ttCode)
{
	$parentTT = "";
	$query = "SELECT customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	if($line['customTopic']==1)
		$parentTT = $line['parentTeacherTopicCode'];

	return $parentTT;
}

function getTopicActivatedTillDate($userID, $childClass, $childSection, $category, $subcategory, $schoolCode, $subjectno, $packageType="All", $showAllTopics=0,$programMode="")
{
	if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"SCHOOL")==0 || strcasecmp($subcategory,"Home Center")==0))
	{
		$query = "SELECT teacherTopicDesc, a.teacherTopicCode , activationDate , deactivationDate , priority 
				  FROM   adepts_teacherTopicActivation a, adepts_teacherTopicMaster b
				  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND a.schoolCode='$schoolCode' AND b.live=1";
		if($childClass != "")
			$query .= " AND a.class =$childClass";
		if($childSection != "")
			$query .= " AND a.section ='$childSection'";
		$query .= " UNION ";
		$query .= "SELECT teacherTopicDesc, a.teacherTopicCode , activationDate , deactivationDate   , priority 
				  FROM   adepts_studentTopicActivation a, adepts_teacherTopicMaster b
				  WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".$subjectno." AND userID=$userID AND b.live=1 ORDER BY priority,deactivationDate DESC";
				  
		$result = mysql_query($query) or die(mysql_error());

		while ($line = mysql_fetch_array($result))
		{
			if(isset($teacherTopics[$line[1]]) && $line[3]!="0000-00-00" && $teacherTopics[$line[1]]["deactivationDate"]=="0000-00-00")    //This condition is added to correct the issue where the same topic is deactivated in past and active currently  and because of order by priority, it treats it as deactivated.
				continue;
			$teacherTopics[$line[1]]["description"]		=	$line[0];
			$teacherTopics[$line[1]]["activationDate"]	=	$line[2];
			$teacherTopics[$line[1]]["deactivationDate"]	=	$line[3];
		}
	}
	else
	{
		$query  = "SELECT teacherTopicCode, teacherTopicDesc  FROM adepts_teacherTopicMaster
				   WHERE  subjectno=".$subjectno." AND customTopic=0";
		if($packageType=="MS_DEC")
			$query .= " AND live=1 AND teacherTopicCode in (".MS_DEC_TOPICS.")";
		/*else if($programMode=="summerProgram" && $childClass==6)
			$query .= " AND teacherTopicCode in ('TT11027','TT11080','TT11073')";
		else if($programMode=="summerProgram" && $childClass==7)
			$query .= " AND teacherTopicCode in ('TT11062','TT11081','TT11074','TT11075')";
		else if($programMode=="summerProgram" && $childClass==8)
			$query .= " AND teacherTopicCode in ('TT11097','TT11082','TT11076','TT11067')";*/
		else
			$query .= " AND live=1";
		$query .= " ORDER BY classification, teacherTopicOrder, teacherTopicCode";

		if ($childClass<=3){
			/*$query="SELECT DISTINCT a.teacherTopicCode, IF(b.newTTDesc='' OR ISNULL(b.newTTDesc),a.teacherTopicDesc,b.newTTDesc)  as 'teacherTopicDesc', IF(b.class=$childClass,b.position,null) pos
					FROM adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise b ON a.teacherTopicCode=b.teacherTopicCode
					WHERE  subjectno=2 AND customTopic=0 AND live=1 ".($showAllTopics==1?"":"AND  b.class=$childClass")." 
					ORDER BY CASE WHEN pos is null then 1 else  0 end, b.position, classification, teacherTopicOrder, a.teacherTopicCode";*/
			$query="SELECT topics.* FROM 
						(SELECT a.teacherTopicCode, IF(b.newTTDesc='' OR ISNULL(b.newTTDesc),a.teacherTopicDesc,b.newTTDesc)  as 'teacherTopicDesc', IF(b.class=$childClass,b.position,null) pos, b.class
						FROM adepts_teacherTopicMaster a LEFT JOIN adepts_teacherTopicFlow_classwise b ON a.teacherTopicCode=b.teacherTopicCode
						WHERE  subjectno=2 AND customTopic=0 AND live=1 ".($showAllTopics==1?"":"AND  b.class=$childClass")."
						ORDER BY CASE WHEN pos is null then 1 else  0 end, pos, classification, teacherTopicOrder, a.teacherTopicCode, b.class DESC) topics
					GROUP BY topics.teacherTopicCode
					ORDER BY CASE WHEN topics.pos is null then 1 else  0 end, topics.pos";
		}		
		$result = mysql_query($query) or die(mysql_error());
		if($showAllTopics==1)
			$childClass="";
		while ($line=mysql_fetch_array($result))
		{
			$classLevel = getClassLevel($line[0]);
			// echo "<pre>";
			// print_r($classLevel);
			//if($childClass!="" && !in_array($childClass,$classLevel) && $packageType=="All" && $programMode!="summerProgram")
			if(($childClass!="" && !in_array($childClass,$classLevel) && $packageType=="All") || count($classLevel)==0)
				continue;
			$desc=$line[1];
			$orderNo=isset($line[2])?$line[2]:0;

			$teacherTopics[$line[0]]["description"]		=	$desc;
			$teacherTopics[$line[0]]["position"]		=	$orderNo;
			
		}
	}
	return $teacherTopics;
}

function getTopicName($ttCode)
{
    $query  = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    return $line[0];
}

function disableLiveClusters($ttCode,$schoolCode,$class,$section,$current_flow)
{
	$liveTopicList = array();
	$liveTopicFlow = array();
	$liveClusterList = "";
	$i=0;

	$sql = "Select teacherTopicCode,flow from adepts_teacherTopicActivation where schoolCode=$schoolCode AND class=$class AND section='$section' and deactivationDate = '0000-00-00'";
	$result	=	mysql_query($sql);
	while($row=mysql_fetch_array($result))
	{
		array_push($liveTopicList,$row[0]);
		array_push($liveTopicFlow,$row[1]);
	}
	foreach($liveTopicList as $values)
	{	
	
		$flow = $liveTopicFlow[$i];
		$i++;
		if(substr($flow,0,6)=="Custom") 
		{
			$code   = trim(substr($flow,9));
			$sq = "Select clusterCodes from adepts_customizedTopicDetails where code=$code";
			$results	=	mysql_query($sq);
			while($rows=mysql_fetch_array($results))
			{
				$liveClusterList	=	$liveClusterList.$rows[0].",";
			}
			continue ;
		} 
		/*if($flow != $current_flow && $_REQUEST['flow'] != "") continue ;*/
		$flow_level = $flow."_level";
		$liveTopicList[$values] = array();
		$sql = "Select clusterCode from adepts_teacherTopicClusterMaster where teacherTopicCode='$values'";
		$result	=	mysql_query($sql);
		while($row=mysql_fetch_array($result))
		{
			array_push($liveTopicList[$values],$row[0]);
		}
		
			foreach($liveTopicList[$values] as $val)
			{				
				$sq = "Select clusterCode from adepts_clusterMaster where FIND_IN_SET($class,$flow_level) and clusterCode='$val'"; 
				$results	=	mysql_query($sq);
				while($rows=mysql_fetch_array($results))
				{
					$liveClusterList	=	$liveClusterList.$rows[0].",";
				}
			}
	}
	$liveClusterList	=	substr($liveClusterList,0,-1);
	$liveClusterList	=	"'".str_replace(",","','",$liveClusterList)."'";
	return $liveClusterList;
}

function notifyTeacher($userID, $schoolCode, $category)
{
    $subquery = "select userID from adepts_userDetails WHERE schoolCode=" . $schoolCode . " and category = 'teacher'";
    $result = mysql_query($subquery) or die("Query failed : " . mysql_error());
    if (mysql_num_rows($result) == 1) {
        $singleteacher = mysql_fetch_array($result, MYSQL_ASSOC);
        $teacheruserid = $singleteacher['userID'];
    } else {
        $userstring = '(';
        while ($row = mysql_fetch_array($result)) {
            $userstring .= $row['userID'] . ",";
        }
        $userstring = rtrim($userstring, ",");
        $userstring .= ')';

        $subquery = "select userID from adepts_teacherClassMapping where userID IN " . $userstring . " and class = " . $childClass . " and section = '" . $childsection . "'";
        $result = mysql_query($subquery) or die("Query failed : " . mysql_error());
        $userwithschool = mysql_fetch_array($result, MYSQL_ASSOC);
        $teacheruserid = $userwithschool['userID'];
    }
    $sql = mysql_query("insert into adepts_forgetPassNotification (childUserID, teacherUserID, category, status, requestDate, lastModified) values (" . $user_row['userID'] . ",'" . $teacheruserid . "','password change','1',NOW(),NOW())");
}

function newkudosCounter($userName)
{
	//echo " USERNAME IS- ". $_SESSION['username'];
	$query="Select count(*) as newKudosCount from kudosMaster where view=1 and receiver='".$userName."'";
	$result = mysql_query($query);
	if($row = mysql_fetch_array($result))
	{
		$newKudosCount=$row['newKudosCount'];		
	}
	return $newKudosCount;	
}

function newKudosCounterInSession($userID, $userName, $sessionID, $childClass)
{
	//echo " USERNAME IS- ". $_SESSION['username'];
	
	$condition='select lastModified from adepts_teacherTopicQuesAttempt_class'.$childClass.' where userID='.$userID.' and questionNo=1 and sessionID='.$sessionID.' limit 1';
	
	$query="Select count(*) as newKudosCountInSession from kudosMaster where view=1 and receiver='".$userName."' and lastModified>(".$condition.")";
	$result = mysql_query($query);
	if($row = mysql_fetch_array($result))
	{
	    $newKudosCountInSession=$row['newKudosCountInSession'];		
	}
	//echo $query;
	return $newKudosCountInSession;	
}

function resetKudosCounter($userName)
{
	//echo "IN RESET KUDOS COUNTER";
    //echo " USERNAME IS - ".$userName;
	
	$query = "UPDATE educatio_adepts.kudosMaster set view=0 where view=1 and receiver='".$userName."'";
	$result = mysql_query($query) or die(' Update Kudos Counter Query Failed: '.mysql_error());
		
}

function getCustomizedTopics($teacherTopicCode,$schoolCode,$childClass)
{
	$arrayCustomTopic = array();
	$sq = "SELECT teacherTopicCode FROM adepts_teacherTopicMaster WHERE parentTeacherTopicCode='$teacherTopicCode' AND schoolCode=$schoolCode AND class=$childClass";	
	$rs = mysql_query($sq);
	while($rw = mysql_fetch_array($rs))
	{
		$arrayCustomTopic[] = $rw[0];
	}
	return $arrayCustomTopic;
}

function getProgressOfCustoms($customTopicArray,$userID)
{
	$progress = 0;
	$customTopicStr = implode("','",$customTopicArray);
	$sq = "SELECT MAX(progress) FROM adepts_teacherTopicStatus WHERE teacherTopicCode IN ('$customTopicStr') AND userID=$userID";	
	$rs = mysql_query($sq);
	if($rw = mysql_fetch_array($rs))
	{
		$progress = $rw[0];
	}
	return $progress;
}

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

function addAQADExplaination($date, $class, $userID, $explaination)
{
	$explaination = str_replace("'","&#39;",$explaination);
	$query = "INSERT INTO educatio_educat.aqadExplaination(date,class,studentId,explaination,submittedOn) VALUES('$date',$class,$userID,'$explaination',NOW())";

    $result = mysql_query($query);
    if($result!==false)
        return true;
    else
        return FALSE;
}

function getMisconceptionStatement($qcode)
{
	$misconceptionStatement = "";
	$sq = "SELECT B.description FROM adepts_questions A, educatio_educat.misconception_master B WHERE A.misconception=id AND qcode=$qcode";
	$rs = mysql_query($sq);
	if($rw = mysql_fetch_array($rs))
	{
		$misconceptionStatement = $rw[0];		
	}
	return $misconceptionStatement;
}

function getTopicsForFreeTrial($childClass)
{
	$sq = "SELECT teacherTopicCode FROM freeTrialTopicList WHERE class=$childClass";
	$rs	= mysql_query($sq);
	$rw = mysql_fetch_array($rs);
	return explode(",",$rw[0]);
}

function sortArrayByArray($array, $orderArray) 
{
    $ordered = array();
    foreach($orderArray as $key) {
        if(array_key_exists($key,$array)) {
            $ordered[$key] = $array[$key];
            unset($array[$key]);
        }
    }
    return $ordered + $array;
}

function isUserFirstTimeLoggedIn($userID)
{
	$query="select count(sessionID) as totalDays from adepts_sessionStatus where userID=".$userID;
		
	$noOfLoginsResult = mysql_query($query);
	
	$noOfLoginsCount = mysql_fetch_array($noOfLoginsResult);
	
	if($noOfLoginsCount['totalDays']==1)
		return 1;
	else
		return 0;
}
function isUserFirstTimeLoggedInToday($userID)
{
	$query="select count(sessionID) as totalLogins from adepts_sessionStatus where startTime>CURDATE() AND userID=".$userID;
		
	$noOfLoginsResult = mysql_query($query);
	
	$noOfLoginsCount = mysql_fetch_array($noOfLoginsResult);
	
	if($noOfLoginsCount['totalLogins']==1)
		return 1;
	else
		return 0;
}

function isNextPageIsMyDetail($userCategory,$userSubCategory,$userClass,$isUserFirstTimeLogin)
{
	if(((strcasecmp($userCategory, "STUDENT") == 0 && strcasecmp($userSubCategory,"School")==0 && $userClass > 3) || (((strcasecmp($userCategory, "STUDENT") == 0 && strcasecmp($userSubCategory,"Individual")==0 )|| strcasecmp($userCategory,"GUEST")==0)) ) && $isUserFirstTimeLogin )
		return 1;
	else
		return 0;
}

function activityLevelDetail($srno)
{
	$totalLevel	=	0;
	$levelCleared	=	0;
	$timeTaken	=	0;
	$sq	=	"SELECT level,status,timeTaken FROM adepts_activityLevelDetails WHERE srno=$srno";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs) > 0)
	{
		while($rw=mysql_fetch_array($rs))
		{
			$timeTaken	+=	$rw[2];
			$totalLevel++;
			if($rw[1]==1)
				$levelCleared++;
		}
	}
	else
	{
		$totalLevel	=	1;
		$levelCleared	=	1;
	}
	return $totalLevel."~".$levelCleared."~".$timeTaken;
}

function getSuperTestTopic($childClass)
{
	$sq = "SELECT topicName FROM da_paperCodeMaster WHERE class=$childClass ORDER BY testEndDate DESC LIMIT 1";
	$rs = mysql_query($sq);
	$rw = mysql_fetch_array($rs);
	return $rw[0];
}

function isChoiceScreenSchool($class=0){return 1;
	$choiceScreenSchools=array();	
	if ($class>=4) 
		return 1;
	else	
		return 0;
}
function checkForRCT($userID)
{
	$rctFlag = 0;
	$sq = "select rctID from rctTrialUsers where userID=$userID and startDate<=CURDATE() and endDate>=CURDATE()";
	$rs = mysql_query($sq);	
	if(mysql_num_rows($rs)>0)
		$rctFlag=1;
	return $rctFlag;
}
function isDailyDrillSchool($class=0){
	$dailyDrillClasses=array(4,5,6,7,0);
	$userID = $_SESSION["userID"];	
	$rctFlag=checkForRCT($userID);
	if (in_array($class, $dailyDrillClasses) && !$rctFlag)
		return 1;
	else	
		return 0;
}

function isNewAdaptiveLogicSchool(){
	$dailyDrillSchools=array(2387554,3216130,34736,504488,252071,384445,23246,3286324,420525,524522,411876,2386588,11215,651378,2530147,1752,2962987,20191,22355);
	if (!in_array($_SESSION['schoolCode'], $dailyDrillSchools)) 
		return 0;
	else	
		return 1;
}
function getLoginDaysSince($dateSince = "20160102")
{
	$sq = "SELECT count(sessionID) FROM adepts_sessionStatus WHERE userID=".$_SESSION["userID"]." AND startTime_int>'$dateSince' GROUP BY startTime_int";
	$rs = mysql_query($sq);
	$numLoginDays = mysql_num_rows($rs);
	return $numLoginDays;
}
function getTopicsForOtherGrades($childClass,$topicWiseDetails,$packageType)
{
		$userID = $_SESSION['userID'];
		$teacherTopics =  array();
		$query  = "SELECT teacherTopicCode, teacherTopicDesc  FROM adepts_teacherTopicMaster
				   WHERE  subjectno=".SUBJECTNO." AND customTopic=0 AND live=1";
		if($packageType=="MS_DEC")
			$query .= "  AND teacherTopicCode in (".MS_DEC_TOPICS.")";

		$query .= " ORDER BY classification, teacherTopicOrder, teacherTopicCode";
		$result = mysql_query($query) or die(mysql_error());
		while ($line=mysql_fetch_array($result))
		{
			$classLevel = getClassLevel($line[0]);
			if(count($classLevel)==0 || in_array($childClass,$classLevel))
				continue;
			$teacherTopics[$line[0]][0]		=	$line[1];				
			$teacherTopics[$line[0]][1] = $topicWiseDetails[$line[0]][1] != '' ? $topicWiseDetails[$line[0]][1] : 0;
			$teacherTopics[$line[0]][2] = $topicWiseDetails[$line[0]][2] != '' ? $topicWiseDetails[$line[0]][2]:0;
			$max_grade = max($classLevel);
			$min_grade = min($classLevel);
			if ($max_grade==$min_grade)
			{
				$teacherTopics[$line[0]][3] = $min_grade;
			}
			else
			{
				$teacherTopics[$line[0]][3] = $min_grade."-".$max_grade;
			}
			$teacherTopics[$line[0]][4] = $topicWiseDetails[$line[0]][5];
		}

		return $teacherTopics;
}

function getCompletedDailyPractise($userID)
{
	$practiseModuleIdCompletedStr = '';
	$query = "SELECT DISTINCT a.practiseModuleId from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.userID='$userID' and a.status='completed' and (a.lastModified) >CURDATE() -INTERVAL 3 MONTH";

 	$result = mysql_query ( $query );
	while ( $line = mysql_fetch_array ( $result ))
	{
		$practiseModuleIdCompleted[] = $line[0];
	}
	$practiseModuleIdCompletedStr = "'".implode("','", $practiseModuleIdCompleted)."'" ;
	return $practiseModuleIdCompletedStr;
}

function get_current_cluster($teacherTopicCode,$userID){
	$query = "SELECT clusterCode from educatio_adepts.adepts_ttUserCurrentStatus where teacherTopicCode='$teacherTopicCode' and userID='$userID' ";
	$result = mysql_query ($query);
	$clusterCodeRes = mysql_fetch_array( $result, MYSQL_ASSOC);
	return $clusterCodeRes['clusterCode'];
}

function get_selectedAndListOfClusters($teacherTopicCode){
	$clusters = "select currently_selectedClusters,list_of_clusters from educatio_adepts.kst_subsetListForDiagnosticTest where teacherTopicCode = '$teacherTopicCode' ";
	$result = mysql_query( $clusters) or die( "Invalid query".$clusters);
	$clusterRes = mysql_fetch_array( $result, MYSQL_ASSOC);
	return 	$clusterRes;
}

function  get_cluster_flow($teacherTopicCode,$fClusterList) {
	$clusterArr = array();
	$query = "select clusterCode from educatio_adepts.adepts_teacherTopicClusterMaster where teacherTopicCode = '$teacherTopicCode' and clusterCode in ($fClusterList)  order by flowno ";
	$result = mysql_query( $query) or die( "Invalid query".$query);
	while($line = mysql_fetch_array($result)){
		$clusterArr[]=$line['clusterCode'];
	}	
		return $clusterArr;
}

function  get_learning_objective($clusterCode) {
	$query = "select learning_objective_qcode from educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions where mapped_cluster = '$clusterCode'";
	$result = mysql_query( $query) or die( "Invalid query".$query);
	while($line = mysql_fetch_array($result)){
	$learningObjCodes[]="'".$line['learning_objective_qcode']."'";
	}
	return $learningObjCodes;
}

function parent_learning_obj($learning_objective_qcode,$all_pre_req){
        $qcode = "'" . implode( "','",$learning_objective_qcode) . "'";
        $query = "SELECT parent_learning_obj from educatio_adepts.kst_skill_tree_tuple where child_learning_obj in ($qcode)";
		//echo $query;
		$result = mysql_query($query) or die("Invalid query".$query);
		$pre_req =array();

		if(mysql_num_rows($result) > 0){
			while($row=mysql_fetch_array($result)){
				$pre_req[] = $row['parent_learning_obj'];
				$all_pre_req[] = $row['parent_learning_obj'];
			}
			parent_learning_obj($pre_req,$all_pre_req);
		} 
		else{
			$_SESSION['all_pre_req'] = $all_pre_req;
			return $_SESSION['all_pre_req'];
		}
}

function getClusterAndMisconception($userID,$attemptID){
	if(isset($_SESSION['misconceptionCodeForKst']) && count($_SESSION['misconceptionCodeForKst']>0)){
		//this means session variable has value and no need to take from DB
	} else {
		$query = "select finalMisconception from educatio_adepts.kst_diagnosticTestAttempts WHERE userID=$userID and status = 1 and attemptID =$attemptID";
		$result = mysql_query($query);
		$_SESSION['misconceptionCodeForKst'] = $misconceptionCodeForKst = mysql_fetch_array($result);
	}
	$learningObjCodes = array();
	$clusterAndLearningObjective = array();
	$parentCodes = array();
	$all_pre_req = array();
	$clusterCode = $_SESSION['current_cluster'];
	$learningObjCodes = get_learning_objective($clusterCode);

	$learningObjCodesRes = implode(",",$learningObjCodes);
	//get first level of parents
	$query = "select parent_learning_obj from educatio_adepts.kst_skill_tree_tuple where child_learning_obj in($learningObjCodesRes) ";
	$result = mysql_query( $query) or die( "Invalid Query".$query);
	while($parentCodeRes = mysql_fetch_array($result)){
		$parentCodes[]= $parentCodeRes['parent_learning_obj'];
	}
	//misconception code in first level
	$commonMiscCode1 = array_intersect($_SESSION['misconceptionCodeForKst'],$parentCodes);
	//remove first level misconception from main array
	foreach($commonMiscCode1 AS $key=> $value){
		unset($_SESSION['misconceptionCodeForKst'][$key]);
	}
	if(count($_SESSION['misconceptionCodeForKst']) != 0){
		//check all level of parent codes
		parent_learning_obj($parentCodes,$all_pre_req);
		$all_parent_level_codes = array_unique($_SESSION['all_pre_req']);
		//misconception codes in lower level
		$commonMiscCode2 = array_intersect($_SESSION['misconceptionCodeForKst'],$all_parent_level_codes);
		$_SESSION['misconceptionCodeForKst'] = array_diff($_SESSION['misconceptionCodeForKst'],$all_parent_level_codes);
		$misconception_arr = $_SESSION['misconceptionCodeForKst'];
		$misconceptionArray = implode(",",$misconception_arr);
		if($_SESSION['misconceptionCodeForKst']!= $misconceptionArray ){
			$update_query  = "UPDATE educatio_adepts.kst_diagnosticTestAttempts SET finalMisconception ='$misconceptionArray' WHERE userID=$userID  and status = 1 and attemptID =$attemptID ";
			mysql_query($update_query);
		}
		//merge both level of misconception codes
		$final_misc_code_flow = array_merge($commonMiscCode2,$commonMiscCode1);
		$learning_objective_code =  implode("','",$final_misc_code_flow);
	} else {
		$learning_objective_code = implode("','",$commonMiscCode1);
	}
	return $learning_objective_code;
}
?>
