<?php
    function nextCluster($userID,$teacherTopicCode, $ttAttemptID, $sessionID, $clusterCode="", $result="", $clusterAttemptID="")
    {
        $childClass  = $_SESSION['childClass'];
        $query = "SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
    
        $flow_result = mysql_query($query);
        $flow_line = mysql_fetch_array($flow_result);
        $flow = $flow_line[0];
        $objTT       = new teacherTopic($teacherTopicCode,$childClass,$flow);
		
        if ($clusterCode == "")    //i.e. a new topic attempt
        {
            
            if(isset($_SESSION["customClusterCode"]) && $_SESSION["customClusterCode"] != "")
            {
                $clusterCode = $_SESSION["customClusterCode"];
                unset($_SESSION["customClusterCode"]);
            }
            else
                $clusterCode = $objTT->findFirstCluster();
            $attemptType = "N"; //Since the first time it would be a normal cluster

                insertClusterData($userID, $ttAttemptID, $clusterCode, $attemptType, $sessionID, $teacherTopicCode);
                if(($_SESSION['topicAttemptNo'] == 1  && $_SESSION['clusterAttemptNo'] == 1) || $_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent")
                    checkForComprehensiveModule($ttAttemptID, $userID, $sessionID,'Prerequisite',$clusterCode);
            return $clusterCode;
        }
        else
        {
            if($_SESSION["remedialStack"]=="")
                $remedialStack=array();
            else
                $remedialStack=split(",",$_SESSION["remedialStack"]);
            //if current cluster attempted is cleared successfully and no failed cluster pending
            if ($result == "SUCCESS" || $result == "SKIPPED") //Stack in the SESSION directly influences it
            {
                //If the clusters is existing in the failed cluster list, remove it since cleared.
                removeFromFailedClusters($ttAttemptID,$clusterCode);
                if(isEmpty($remedialStack))
                {
                    $prevCluster = $clusterCode;
                    $clusterCode = $objTT->findNextNormalCluster($clusterCode);
					
                    if($clusterCode==-1)
                        return -1;
                    else
                    {
                        $completedClassLevel = hasCompletedClassLevelClusters($objTT, $prevCluster, $clusterCode);
                        if($completedClassLevel)
                        {
                            $flag = checkClassLevelCrossedFirstTime($ttAttemptID);
                            if($flag==1)
                                return -2;
                        }
                        if($_SESSION['attemptType']=='R')
                            $_SESSION['clusterStatusPrompt'] = 4;
                        elseif(in_array($clusterCode,$_SESSION['topicWiseProgressStatus'])){
                            for ($i = 0; $i < count($_SESSION['topicWiseProgressStatus'])/3; ++$i) {
                                if($_SESSION['topicWiseProgressStatus'][$i*3]==$clusterCode){
                                    $index=$i*3;
                                }
                            }
                            if($_SESSION['topicWiseProgressStatus'][$index+2]=='FAILURE'&&$_SESSION['topicWiseProgressStatus'][$index+1]<4){
                                $_SESSION['clusterStatusPrompt'] = 4;
                            }
                            elseif($_SESSION['topicWiseProgressStatus'][$index+2]==NULL&&$_SESSION['topicWiseProgressStatus'][$index+1]>1)
                                $_SESSION['clusterStatusPrompt'] = 4;
                        }
                    }
                    $attemptType = "N";
                }
                else  //if current cluster attempted is cleared successfully and there are any failed cluster pending
                {
                    $clusterCode = array_pop($remedialStack); //Influences the stack in the SESSION directly
                    $comma_separated = implode(",", $remedialStack);
                    $_SESSION["remedialStack"] = $comma_separated;
    
                    $query = "UPDATE ".TBL_CURRENT_STATUS." SET remedialStack='".$comma_separated."' WHERE teacherTopicCode='".$teacherTopicCode."' AND userID=".$userID;
                    mysql_query($query) or die(mysql_error());
    
                    if (isEmpty($remedialStack))
                        $attemptType = "N";
                    else
                        $attemptType = "R";
                }
    
            }
            elseif ($result == "FAILURE")    // if the current cluster attempted is failed
            {
                  $noOfFailedAttemptsOnCluster = clusterFailedAttemptsCount($clusterCode, $ttAttemptID);
					if($noOfFailedAttemptsOnCluster>=2)
                    {
                        if(!isEmpty($remedialStack))	//if remedial cluster failed twice, take the one cluster lower than the last normal cluster attempted
                        {
                            $fromCluster = getLastNormalClusterAttempted($ttAttemptID, $userID);
                            $clusterCode = $objTT->getOneClusterLowerInFlow($fromCluster);
                            $_SESSION['clusterStatusPrompt'] = 3;
                        }
                        elseif ($noOfFailedAttemptsOnCluster==2)	//On two failures, take the user to one cluster lower in the flow
                        {
                            $clusterCode = $objTT->getOneClusterLowerInFlow($clusterCode);
                            $_SESSION['clusterStatusPrompt'] = 3;
                        }
                        elseif ($noOfFailedAttemptsOnCluster==3)	//On three failures, first cluster of the current level
                        {
                            $fall = 5;
                            $clusterCode = $objTT->getNLowerCluster($clusterCode,$fall);
                        }
                        elseif ($noOfFailedAttemptsOnCluster>=4)	//On fourth failure, mark the cluster as failed and give the next cluster
                        {
                            addToFailedClusters($ttAttemptID, $clusterCode);
                            $prevCluster = $clusterCode;
                            $clusterCode = $objTT->findNextNormalCluster($clusterCode);
                            if($clusterCode==-1)
                                return -1;
                            else
                            {
                                $completedClassLevel = hasCompletedClassLevelClusters($objTT, $prevCluster, $clusterCode);
                                if($completedClassLevel)
                                {
                                    $flag = checkClassLevelCrossedFirstTime($ttAttemptID);
                                    if($flag==1)
                                        return -2;
                                }
                            }
                        }
                        $attemptType = "N";
                    }
                    else {
                        $_SESSION['clusterStatusPrompt'] = 1;
                        $remedialClusterCode = $objTT->findRemedialCluster($clusterCode);
                        if($remedialClusterCode!=-1 && $clusterCode!=$remedialClusterCode)    {    // if this cluster has a remedial, add this in the failed cluster list (to be attempted later)
                            array_push($remedialStack,$clusterCode); //Influences the stack in the SESSION directly
                            $comma_separated = implode(",", $remedialStack);
                            $_SESSION["remedialStack"] = $comma_separated;
    
                            $query = "UPDATE ".TBL_CURRENT_STATUS." SET remedialStack='".$comma_separated."' WHERE teacherTopicCode='".$teacherTopicCode."' AND userID=".$userID;
                            mysql_query($query) or die(mysql_error());
                            $_SESSION['clusterStatusPrompt'] = 3;
                        }
                        $clusterCode = $remedialClusterCode;
                        if ($clusterCode == -1){
                            return -1;
                        }
                        $attemptType = "R";
                    }
    
            }
            $lowerLevel = 0;
            if(in_array($clusterCode,$objTT->getLowerLevelClusters()))
                $lowerLevel = 1;
            $_SESSION['lowerLevel'] = $lowerLevel;
            $_SESSION["attemptType"]  = $attemptType;        //No need to store this in session, done here just for the testing interface to use it.
            insertClusterData($userID, $ttAttemptID, $clusterCode, $attemptType, $sessionID, $teacherTopicCode);
            if(($_SESSION['topicAttemptNo'] == 1 && $_SESSION['clusterAttemptNo'] == 1 ) || $_SESSION["userType"]=="msAsStudent" || $_SESSION["userType"]=="teacherAsStudent")
                checkForComprehensiveModule($ttAttemptID, $userID, $sessionID,'Prerequisite',$clusterCode);                
            
            return $clusterCode;
        }
    }
    
   
    function updateClusterData($clusterAttemptID,$result,$sessionID,$perCorrect=0,$noOfQuesAttempted=0,$ttAttemptID="",$clusterCode="")
    {
        $attemptNo = getClusterAttemptNo($ttAttemptID,$clusterCode);
        if($attemptNo==2 && $result== "SUCCESS" && $_SESSION['clusterStatusPrompt']!=4)
        $_SESSION['clusterStatusPrompt'] = 2;
        elseif($attemptNo==1 && $result== "SUCCESS"&& $_SESSION['clusterStatusPrompt']!=4)
        $_SESSION['clusterStatusPrompt'] = 5;
    
        if($_SESSION['clusterStatusPrompt']==4 && $result=="FAILURE")
            $_SESSION['clusterStatusPrompt'] = 0;
    
        $query  = "UPDATE ".TBL_CLUSTER_STATUS." SET result='$result',endSessionID='$sessionID', noOfQuesAttempted=$noOfQuesAttempted, perCorrect=$perCorrect WHERE clusterAttemptID='$clusterAttemptID'";
        $result = mysql_query($query) or die(mysql_error().$query);
        $_SESSION['topicWiseProgressStatus']=array();
        $_SESSION['topicWiseProgressStatus'] = findCLustersAttendedInTopic($_SESSION['teacherTopicAttemptID']);
    }
    
    function insertClusterData($userID,$ttAttemptID, $clusterCode, $attemptType, $sessionID, $teacherTopicCode)
    {
    	$clusterAttemptNo = getClusterAttemptNo($ttAttemptID,$clusterCode);
		$clusterAttemptNo = $clusterAttemptNo+1;
        $query = "INSERT INTO ".TBL_CLUSTER_STATUS." (userID, clusterCode, clusterAttemptNo, ttAttemptID, attemptType,startSessionID)
                    VALUES ('$userID','$clusterCode',$clusterAttemptNo,$ttAttemptID,'$attemptType','$sessionID')";
        $result = mysql_query($query) or die("10".mysql_error().$query);
        $clusterAttemptID = mysql_insert_id();
        $_SESSION['topicWiseProgressStatus']=array();
        $_SESSION['topicWiseProgressStatus'] = findCLustersAttendedInTopic($_SESSION['teacherTopicAttemptID']); 
        $_SESSION['clusterAttemptID'] = $clusterAttemptID;
        $_SESSION['clusterCode']      = $clusterCode;
        $noOfQues  = loadArrays($clusterCode,$clusterAttemptID, $userID, $ttAttemptID, $_SESSION['flashContent']); //returns the count of allQuestions to be asked in this cluster along with setting session variables of attempted and non attempted
        if($noOfQues==0) //if there are no questions to be asked in this cluster go to the next one.
        {
            $result = "SKIPPED";
			updateClusterData($clusterAttemptID,$result,$sessionID,0,0,$ttAttemptID,$clusterCode);
            nextCluster($userID,$teacherTopicCode, $ttAttemptID, $sessionID, $clusterCode, $result, $clusterAttemptID);
        }
     }

    function clusterFailedAttemptsCount($clusterCode, $ttAttemptID)
    {
        //Success on a cluster wipes out the past failures
        $query  = "SELECT max(clusterAttemptID) FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND result='SUCCESS' AND clusterCode='".$clusterCode."'";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
    
        $attemptCount = 0;
        $query = "SELECT count(clusterAttemptID) FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND result='FAILURE' AND clusterCode='".$clusterCode."'";
        if($line[0]!="")
            $query .= " AND clusterAttemptID >".$line[0];
        $result = mysql_query($query) or die(mysql_error());
        if($user_row = mysql_fetch_array($result))
        {
            $attemptCount = $user_row[0];
        }
        return $attemptCount;
    }
    
    function isEmpty($stack) {
        if(count($stack)==0)
            return 1;
        else
            return 0;
    }
    
    function loadArrays($clusterCode,$clusterAttemptID, $userID, $ttAttemptID, $flashContent=true, $isNCERT=false)
    {	
        $_SESSION['sdlAttemptResult']=array();
        $_SESSION['sdlAttemptResultData']=array();
        $context            = isset($_SESSION['context'])?$_SESSION['context']:"India";
        $clusterStatusArray = array();
        $isQuesDynamicArray = array();
        $noOfAttemptsForDynamicQuesArray = array();	//no of attempts on a particular dynamic ques in this attempt.
        $allQuestionsArray  = array();
        $questionsAttemptedInCurrentClusterAttemptArray=array();
        $_SESSION["noOfAttemptsOnSdl"]	=	array();
        //Get all the questions of the cluster which are for Global context and the context of the user - currently only India
    
		if($isNCERT)
        {
            $query = "SELECT     qcode, groupNo as subdifficultylevel, '0' as dynamic
                      FROM       adepts_ncertQuestions, adepts_groupInstruction
                      WHERE      adepts_ncertQuestions.exerciseCode='$clusterCode' AND status='3' AND  adepts_ncertQuestions.groupID = adepts_groupInstruction.groupID
                      ORDER BY   groupNo, FIELD(subQuestionNo,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t', '1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19', 'ii','iii','iv','v','vi','vii','viii','ix','x','xi','xii','xiii','xiv','xv')";
        }
        else
        {
        // Ends Here..
            $query = "SELECT     qcode,subdifficultylevel, dynamic
                      FROM       adepts_questions
                      WHERE      clusterCode='$clusterCode' AND status='3'";
			if($_SESSION["msAttemptMode"]!=2)
				$query .= " AND context in ('Global','$context')";
            if(!$flashContent)
                $query .= " AND (question NOT LIKE '%[%.swf%]%')";
            $query.= " ORDER BY subdifficultylevel";
        }
        $result = mysql_query($query) or die(mysql_error());
        while($user_row = mysql_fetch_array($result))
        {
            $qcode = $user_row["qcode"];
            $sdl   = $user_row["subdifficultylevel"];
            $dynamic = $user_row["dynamic"];
    
            $isQuesDynamicArray[$qcode] = $dynamic;
            if($dynamic)
            {
                $noOfAttemptsForDynamicQuesArray[$qcode] = 0;	//Initialize the no. of attempts to 0.
            }
    
            if(!isset($allQuestionsArray[$sdl]))
            {
                $allQuestionsArray[$sdl]=array();
                $_SESSION['sdlAttemptResult'][$sdl] = 4;
                $_SESSION['sdlAttemptResultData'][$sdl] = array();
                $_SESSION["noOfAttemptsOnSdl"][$sdl]	=	0;
            }
            array_push($allQuestionsArray[$sdl],$qcode);
            if(!isset($questionsAttemptedInCurrentClusterAttemptArray[$sdl]))
                $questionsAttemptedInCurrentClusterAttemptArray[$sdl]=array();
        }
        array_push($clusterStatusArray,$allQuestionsArray);
        if(!$isNCERT) //For NCERT Homework module following section details are not needed, so skipped same...
        {
            //-----------------------------------------------------------------------------------------------------------------------------------------------------------//
            $dynamicQuesArray = array_keys($noOfAttemptsForDynamicQuesArray);
            if(count($dynamicQuesArray)>0)
            {
                $query = "SELECT qcode, count(srno) as attempts FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE clusterAttemptID=$clusterAttemptID AND qcode in (".implode(",",$dynamicQuesArray).") GROUP BY qcode";
                $result = mysql_query($query);
                while ($line = mysql_fetch_array($result))
                    $noOfAttemptsForDynamicQuesArray[$line['qcode']] = $line['attempts'];
            }
            $childClass=$_SESSION["childClass"];

            //Get the questions attempted in the current attempt, for case when a cluster is left incomplete (either due to session end or change topic)
            $sdlCount = array();
            $sdlArr = array();
            $prevSdl = "";
            $query = "SELECT DISTINCT a.qcode, subdifficultylevel, R
                      FROM   ".TBL_QUES_ATTEMPT_CLASS." a, adepts_questions b
                      WHERE  a.qcode=b.qcode AND userID=$userID AND a.clusterAttemptID=".$clusterAttemptID." ORDER BY srno";
            $result = mysql_query($query);
            $lastSDL = "";
            while ($line = mysql_fetch_array($result)) {
                $qcode    = $line["qcode"];
                $sdl      = $line["subdifficultylevel"];
                
                array_push($_SESSION['sdlAttemptResultData'][$sdl], $line['R']);
                $_SESSION["noOfAttemptsOnSdl"][$sdl]++;

                $_SESSION['sdlAttemptResult'][$sdl] = max($_SESSION['sdlAttemptResultData'][$sdl]);
                    
                if(isset($isQuesDynamicArray[$qcode]))
                {
                    if(!$isQuesDynamicArray[$qcode] || ($isQuesDynamicArray[$qcode]==1 && $noOfAttemptsForDynamicQuesArray[$qcode]==3))	//if dynamic and 5 attempts on that ques, move it to the attempted array.
                        array_push($questionsAttemptedInCurrentClusterAttemptArray[$sdl],$qcode);
                }
                if($prevSdl!=$sdl)
                    array_push($sdlArr,$sdl);
                $prevSdl = $sdl;
                $lastSDL=$lastSDL;
            }
            if ($_SESSION["childClass"]<=3 && $lastSDL!=""){
                if ($_SESSION["noOfAttemptsOnSdl"][$lastSDL]<3) $_SESSION['sdlAttemptResult'][$lastSDL] = 5;
            }

            array_push($clusterStatusArray,$questionsAttemptedInCurrentClusterAttemptArray);
    
            $questionsNeverAttemptedArray=array();
            //Get all the questions attempted by the user for this cluster, which is inturn is used to determine the questions never attempted
            $query  = "SELECT DISTINCT qcode FROM   ".TBL_QUES_ATTEMPT_CLASS." a, ".TBL_CLUSTER_STATUS." b
                       WHERE  a.userID=b.userID and a.clusterAttemptID=b.clusterAttemptID AND b.userID=$userID AND b.clusterCode = '$clusterCode'";
            $tempQcode = array();
            $result    = mysql_query($query);
            while($user_row = mysql_fetch_array($result))
                array_push($tempQcode, $user_row[0]);
    
            foreach ($allQuestionsArray as $sdl=>$qcodeArray)
            {
                foreach ($qcodeArray as $desc)
                {
                    if(!in_array($desc,$tempQcode))
                    {
                        if(!isset($questionsNeverAttemptedArray[$sdl]))
                            $questionsNeverAttemptedArray[$sdl]=array();
                        array_push($questionsNeverAttemptedArray[$sdl],$desc);
                    }
                }
            }
            array_push($clusterStatusArray, $questionsNeverAttemptedArray);
    
            //Common Instruction:
			$commonInstructionSDL = "";
            $query	=	"SELECT linkedToSDL FROM adepts_groupInstruction WHERE clusterCode='$clusterCode'";
            if(!$flashContent)
                $query .= " AND (groupText NOT LIKE '%[%.swf%]%')";
    
            $result = mysql_query($query);
			$numResults = mysql_num_rows($result);
			if($numResults>0)
			{
            	while ($line = mysql_fetch_array($result))
                	$commonInstructionSDL .= $line['linkedToSDL'].",";
          		 $commonInstructionSDL = substr($commonInstructionSDL,0,-1);
           		 $_SESSION['commonInstruction'] = $commonInstructionSDL;
				 $_SESSION['groupInstructionType'] = 'groupInstruction';
			}
			else
			{
				$query = " SELECT linkedToSDL FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode' AND live='Live' AND type='introduction' AND ver NOT IN ('as2','as3') ORDER BY linkedToSDL";
				
				 $result = mysql_query($query);
				 while ($line = mysql_fetch_array($result))
                	$commonInstructionSDL .= $line['linkedToSDL'].",";
          		 $commonInstructionSDL = substr($commonInstructionSDL,0,-1);
           		 $_SESSION['commonInstruction'] = $commonInstructionSDL;
				  $_SESSION['groupInstructionType'] = 'gamesMaster';
			}
			
			//Student Videos
			$introVidsSDL = "";
			$query = "Select linkedToSDL from adepts_msVideos WHERE mappingType='cluster' and mappingID='$clusterCode'";
			$result = mysql_query($query);
			
			while ($line = mysql_fetch_array($result))
				$introVidsSDL .= $line['linkedToSDL'].",";
			$introVidsSDL = substr($introVidsSDL,0,-1);
			$_SESSION['introVids'] = $introVidsSDL;
    
            $clusterAttemptNo = getClusterAttemptNo($ttAttemptID, $clusterCode);
            $_SESSION['clusterAttemptNo'] = $clusterAttemptNo;
            if($clusterAttemptNo==1)	//Remedial Items to be given on 1st attempt of a cluster, so check if any remedail items are mapped to this cluster
            {
                $_SESSION['remedialItems'] = loadRemedialItems($clusterCode);
            }
    
            $_SESSION["questionsAttemptedInCurrentClusterAttemptArray"] = $questionsAttemptedInCurrentClusterAttemptArray;
            $_SESSION["questionsNeverAttemptedArray"] = $questionsNeverAttemptedArray;
            $_SESSION["isQuesDynamicArray"] = $isQuesDynamicArray;
            $_SESSION["noOfAttemptsForDynamicQuesArray"] = $noOfAttemptsForDynamicQuesArray;
        }
        $_SESSION["allQuestionsArray"] = $allQuestionsArray;
        return count($_SESSION["allQuestionsArray"]);					
        /*
                Example Arrays
                SESSION allQuestionsArray=array(
                                        [1]=>array(5,7,9),
                                        [2.5]=>array(66,75,91),
                                        [3]=>array(105,100,54)
                                    );
                SELECT queries in assessment for those questions that have been attempted before atleast once
                //Optional:
                //Capture them in questionsAttempedArray
                //Then substract allQuestionsArray - questionsAttempedArray
                SESSION questionsNeverAttemptedArray=array(
                                        [1]=>array(7,9),
                                        [2.5]=>array(91),
                                        [3]=>array(105,54)
                                    );
                SESSION questionAttemptedInCurrentClusterAttemptArray=array(
                                        [1]=>array(),
                                        [2.5]=>array(),
                                        [3]=>array()
                                    );
                array_push(clusterStatusArray,allQuestionsArray)
                */
    }
    /**
     * Get the clusterAttempt no in the current topic attempt
     * @param  int teacher topic attempt id
     * @param  string cluster code
     * @return int attemptno
     */
    function getClusterAttemptNo($ttAttemptID, $clusterCode)
    {
        $clusterAttemptNo = "";
        $query  = "SELECT count(clusterAttemptID) FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND clusterCode='$clusterCode'";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $clusterAttemptNo = $line[0];
        return $clusterAttemptNo;
    }
    
    function loadRemedialItems($clusterCode)
    {
        $remedialItemArray = array();
        $query  = "SELECT remedialITemCode, SDLs FROM adepts_remedialItemMaster WHERE linkedToCluster='$clusterCode' AND status='Live'";
        if($_SESSION['flashContent']==0)
                $query .= " AND version='html5'";
        $query .= " ORDER BY remedialItemCode";
        $result = mysql_query($query);
        while ($line = mysql_fetch_array($result))
        {
            $remedialItemArray[$line[0]] = $line[1];
        }
        return $remedialItemArray;
    }
    
    function getLastNormalClusterAttempted($ttAttemptID, $userID)
    {
        $query  = "SELECT clusterCode FROM ".TBL_CLUSTER_STATUS." WHERE ttAttemptID=$ttAttemptID AND userID=$userID AND attemptType='N' ORDER BY clusterAttemptID DESC";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $clusterCode = $line[0];
        return $clusterCode;
    }
	
	function loadChallengeQuestionsNewLogic($teacherTopicCode, $userID, $ttAttemptID)
	{
        $context = isset($_SESSION['context'])?$_SESSION['context']:"India";
		$allChallengeQuestionsArray = array();
		$allCQAttempted = array();
		$sqCQAttempted = "SELECT DISTINCT qcode FROM adepts_ttChallengeQuesAttempt WHERE ttAttemptID=$ttAttemptID AND userID=$userID";
		$rsCQAttempted = mysql_query($sqCQAttempted);
		while($rwCQAttempted = mysql_fetch_assoc($rsCQAttempted))
		{
			$allCQAttempted[] = $rwCQAttempted["qcode"];
		}
		
		$sqCQ = "SELECT A.clusterCode,A.qcode FROM cqClusterMapping A, adepts_teacherTopicClusterMaster B, adepts_questions Q WHERE A.clusterCode=B.clusterCode AND teacherTopicCode='$teacherTopicCode' AND Q.qcode=A.qcode AND Q.status=3 AND context in ('Global','$context')";
		if(count($allCQAttempted)>0)
			$sqCQ .= " AND A.qcode NOT IN (".implode(",",$allCQAttempted).")";
		$rsCQ = mysql_query($sqCQ);	
		while($rwCQ = mysql_fetch_assoc($rsCQ))
		{
			if(isset($allChallengeQuestionsArray[$rwCQ["clusterCode"]]))
				$allChallengeQuestionsArray[$rwCQ["clusterCode"]][] = $rwCQ["qcode"];
			else
			{
				$allChallengeQuestionsArray[$rwCQ["clusterCode"]] = array();
				$allChallengeQuestionsArray[$rwCQ["clusterCode"]][] = $rwCQ["qcode"];
			}
		}
		$_SESSION["allCQAttempted"] = $allCQAttempted;
		return $allChallengeQuestionsArray;
	}
	
	function loadChallengeQuestionsOtherTopic($teacherTopicCode, $userID)
	{
        $context = isset($_SESSION['context'])?$_SESSION['context']:"India";
		$allChallengeQuestionsArray = array();
		$allCQAttempted = array();
		$sqCQAttempted = "SELECT DISTINCT qcode FROM adepts_ttChallengeQuesAttempt WHERE userID=$userID";
		$rsCQAttempted = mysql_query($sqCQAttempted);
		while($rwCQAttempted = mysql_fetch_assoc($rsCQAttempted))
		{
			$allCQAttempted[] = $rwCQAttempted["qcode"];
		}
		$arrayTopicAttempted = array();
		$sqTopicAttempted = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND classLevelResult='SUCCESS'";
		$rsTopicAttempted = mysql_query($sqTopicAttempted);
		while($rwTopicAttempted = mysql_fetch_array($rsTopicAttempted))
		{
			$arrayTopicAttempted[] = $rwTopicAttempted[0];
		}
		if(count($arrayTopicAttempted)>0)
		{
			$sqCQ = "SELECT A.qcode FROM cqClusterMapping A, adepts_teacherTopicClusterStatus B, adepts_questions Q WHERE A.clusterCode=B.clusterCode AND ttAttemptID IN (".implode(",",$arrayTopicAttempted).") AND Q.qcode=A.qcode AND Q.status=3 AND context in ('Global','$context')";
			if(count($allCQAttempted)>0)
				$sqCQ .= " AND A.qcode NOT IN (".implode(",",$allCQAttempted).")";
			$rsCQ = mysql_query($sqCQ);
			while($rwCQ = mysql_fetch_assoc($rsCQ))
			{
				$allChallengeQuestionsArray[] = $rwCQ["qcode"];
			}	
		}
		return $allChallengeQuestionsArray;
	}
	
	function loadAllClusterInTopic($ttCode)
	{
		$allClusterInTT = array();
		$sq = "SElECT clusterCode FROM adepts_teacherTopicClusterMaster WHERE teacherTopicCode='$ttCode' ORDER BY flowno";
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{
			$allClusterInTT[] = $rw[0];
		}
		return $allClusterInTT;
	}
	
	function loadChallengeQuestionsProblemSolving($teacherTopicCodeArray, $childClass)
	{
        $context = isset($_SESSION['context'])?$_SESSION['context']:"India";
		$allChallengeQuestionsArray = array();
		$allCQAttempted = array();
		$sqCQAttempted = "SELECT DISTINCT qcode FROM adepts_ttChallengeQuesAttempt WHERE userID=$userID";
		$rsCQAttempted = mysql_query($sqCQAttempted);
		while($rwCQAttempted = mysql_fetch_assoc($rsCQAttempted))
		{
			$allCQAttempted[] = $rwCQAttempted["qcode"];
		}

		$sqCQ = "SELECT qcode FROM adepts_questions A, adepts_teacherTopicMaster B, adepts_teacherTopicClusterMaster C WHERE A.clusterCode=C.clusterCode AND B.teacherTopicCode=C.teacherTopicCode AND B.teacherTopicCode IN ('".implode("','",$teacherTopicCodeArray)."') AND A.status=3  AND context in ('Global','$context')";
		if(count($allCQAttempted)>0)
			$sqCQ .= " AND qcode NOT IN (".implode(",",$allCQAttempted).")";
		$rsCQ = mysql_query($sqCQ);
		while($rwCQ = mysql_fetch_assoc($rsCQ))
		{
			$allChallengeQuestionsArray[] = $rwCQ["qcode"];
		}
		return $allChallengeQuestionsArray;
	}
	
    function loadChallengeQuestions($teacherTopicCode, $userID)
    {
        $context = isset($_SESSION['context'])?$_SESSION['context']:"India";
        $challengeQuestionsArray                = array();
        $challengeQuestionsCorrectArray         = array();
        $qcodeListArray = array();
        $qcodeExcludeArray = array();
    
    
        //Get the CQs excluded for this TT
        $query  = "SELECT qcode FROM adepts_CQTTMapping WHERE find_in_set('$teacherTopicCode',ttsExcluded)>0";
        $result = mysql_query($query);
        while($line = mysql_fetch_array($result))
        {
            array_push($qcodeExcludeArray, $line[0]);
        }
    
        //Get the approved CQs mapped to this TT
        $query  = "SELECT a.qcode, b.subdifficultylevel, b.flag FROM adepts_CQTTMapping a, adepts_questions b
                   WHERE  a.qcode=b.qcode AND b.status=3 AND context in ('Global','$context') AND find_in_set('$teacherTopicCode',ttsMapped)>0 
				   AND SUBSTRING(clusterCode, -3)='100'";
        if($_SESSION['flashContent'] == 0)
            $query .= " AND (question NOT LIKE '%[%.swf%]%')";
        $result = mysql_query($query);
        while ($user_row = mysql_fetch_array($result))
        {
    
            $qcode = $user_row["qcode"];
            $sdl   = $user_row["subdifficultylevel"];      //The SDL in the challenge cluster is the class level to which the CQ is mapped
            if(in_array($qcode, $qcodeExcludeArray))
                continue;
            if(!isset($challengeQuestionsArray[$sdl]))
                $challengeQuestionsArray[$sdl]=array();
            $challengeQuestionsArray[$sdl][$qcode][0] = 0;   //Initialize the attempt no for each cq
            $challengeQuestionsArray[$sdl][$qcode][1] = 1;   //Set the flag for differentiating questions mapped to TT or not, 1 is mapped to TT and 0 for MS Topic			
            $competitiveExamFlag = $user_row["flag"]==4?1:0;
            $challengeQuestionsArray[$sdl][$qcode][2] = $competitiveExamFlag;   //Set the flag for differentiating CQs from competitive exams vs regular ones
            array_push($qcodeListArray,$qcode);
        }
    
    
        //Get the topics to which this TT is mapped.
        $query  = "SELECT mappedToTopic FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$teacherTopicCode'";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        $topics = explode(",",$line[0]);
        $CQClusters = "";
        for($i=0; $i<count($topics); $i++)
            $CQClusters .= "'".$topics[$i]."100',";
        $CQClusters = substr($CQClusters,0,-1);
        //Get the challenge questions of the topic
    
        if($CQClusters!="")
        {
            //Challenge cluster code will be currently <<topic code>>100. Check if it is live
            $query  = "SELECT clusterCode,status FROM adepts_clusterMaster WHERE clusterCode in ($CQClusters) AND status='live'";
            $cq_result = mysql_query($query);
    
            while($cq_line = mysql_fetch_array($cq_result))
            {
                $clusterCode = $cq_line[0];
    
                //Fetch the CQs from the reserved pool i.e. CQs not mapped to any TT
                $query  = "SELECT a.qcode ,subdifficultylevel, ttsMapped, a.flag
                           FROM   adepts_questions a LEFT JOIN adepts_CQTTMapping b ON a.qcode=b.qcode
                           WHERE  clusterCode='".$clusterCode."'  AND status='3' AND context in ('Global','$context')";
                if($_SESSION['flashContent'] == 0)
                    $query .= " AND (question NOT LIKE '%[%.swf%]%')";
                $result = mysql_query($query);
                while($user_row = mysql_fetch_array($result))
                {
                    $qcode = $user_row["qcode"];
                    $sdl   = $user_row["subdifficultylevel"];      //The SDL in the challenge cluster is the class level to which the CQ is mapped
                    if(in_array($qcode, $qcodeExcludeArray))
                        continue;
                    $mappedToTT = $user_row["ttsMapped"];
                    if(!in_array($qcode,$qcodeListArray))
                    {
                        if(!isset($challengeQuestionsArray[$sdl]))
                            $challengeQuestionsArray[$sdl]=array();
    
                        $challengeQuestionsArray[$sdl][$qcode][0] = 0;   //Initialize the attempt no for each cq
                        if($mappedToTT=="")
                            $challengeQuestionsArray[$sdl][$qcode][1] = 0;   //Set the flag to 0 - meaning not mapped to any TT i.e. reserved pool
                        else
                            $challengeQuestionsArray[$sdl][$qcode][1] = 2;   //Set the flag to 2 - meaning mapped to some TT other than the current one
                        $competitiveExamFlag = $user_row["flag"]==4?1:0;	
                        $challengeQuestionsArray[$sdl][$qcode][2] =  $competitiveExamFlag; //Set the flag for differentiating CQs from competitive exams vs regular ones
                        array_push($qcodeListArray,$qcode);
                    }
                }
            }
        }
    
        if(count($qcodeListArray)>0)
        {
            $qcodeStr = implode(",",$qcodeListArray);
            //CQ correct
            $query = "SELECT DISTINCT a.qcode
                      FROM   adepts_ttChallengeQuesAttempt a
                      WHERE  R=1 AND qcode in ($qcodeStr) AND a.userID=".$userID;
            $result = mysql_query($query);
            while($line = mysql_fetch_array($result))
            {
                $qcode  = $line["qcode"];
                array_push($challengeQuestionsCorrectArray,$qcode);
            }
    
            //CQ Attempted
            $query = "SELECT a.qcode, subdifficultylevel, count(srno) as noofattempts
                      FROM   adepts_ttChallengeQuesAttempt a, adepts_questions b
                      WHERE  a.qcode=b.qcode AND b.qcode in ($qcodeStr) AND a.userID=".$userID."
                      GROUP BY a.qcode";
            $result = mysql_query($query);
            while($line = mysql_fetch_array($result))
            {
                $qcode  = $line["qcode"];
                $sdl    = $line["subdifficultylevel"];
                $attempts = $line['noofattempts'];
                $challengeQuestionsArray[$sdl][$qcode][0] = $attempts;
            }
        }
    
        $_SESSION['challengeQuestionsArray']      = $challengeQuestionsArray;
        $_SESSION['challengeQuestionsCorrect']    = $challengeQuestionsCorrectArray;
    
        return;
    }
    
    function hasCompletedClassLevelClusters($objTT, $clusterCode, $nextCluster)
    {
        $flag = 0;
        //$childClass  = $_SESSION['childClass'];
        //$prevClusterLevelArray = getClusterLevel($teacherTopicCode, $clusterCode);
        $prevClusterLevelArray = $objTT->getClusterLevel($clusterCode);
        if(in_array($objTT->startingLevel,$prevClusterLevelArray))
        {
            //$nextClusterMinClass = getMinLevelOfCluster($teacherTopicCode, $nextCluster);
            $nextClusterMinClass = getMinLevelOfCluster($objTT, $nextCluster);
            if($objTT->startingLevel < $nextClusterMinClass)
                $flag = 1;
        }
        return $flag;
    }
    
    //function getMinLevelOfCluster($teacherTopicCode,$clusterCode)
    function getMinLevelOfCluster($objTT,$clusterCode)
    {
        //$nextClusterLevelArray = getClusterLevel($teacherTopicCode, $clusterCode);
        $nextClusterLevelArray = $objTT->getClusterLevel($clusterCode);
        return $nextClusterLevelArray[0];
    }
    
    function checkClassLevelCrossedFirstTime($ttAttemptID)
    {
		$progressUpdateObj = new topicProgressCalculation($_SESSION['teacherTopicCode'],$_SESSION['childClass'],$_SESSION['flow'],$_SESSION["teacherTopicAttemptID"],SUBJECTNO);
		$progressUpdateObj->updateProgress();
		$_SESSION["updateProgress"] = false;
        $query  = "SELECT classLevelResult FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        if($line[0]=="")
        {
            $query = "UPDATE ".TBL_TOPIC_STATUS." SET classLevelResult='SUCCESS',progress='100' WHERE ttAttemptID=$ttAttemptID";
            mysql_query($query);
            return 1;
        }
        else
            return 0;
    }
    
    function addToFailedClusters($ttAttemptID, $clusterCode)
    {
        $query  = "SELECT failedClusters FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID=$ttAttemptID";
        $result = mysql_query($query);
        $line   = mysql_fetch_array($result);
        if($line[0]=="")
            $failedClusters = $clusterCode;
        else {
            $failedClusters = explode(",",$line[0]);
            if(!in_array($clusterCode, $failedClusters))
                array_push($failedClusters,$clusterCode);
            $failedClusters = implode(",",$failedClusters);
        }
    
        $query = "UPDATE ".TBL_TOPIC_STATUS." SET failedClusters='$failedClusters' WHERE ttAttemptID=$ttAttemptID";
        mysql_query($query);
    }
    
    function removeFromFailedClusters($ttAttemptID,$clusterCode)
    {
        //Remove the clusters code from the failed cluster list, if existing in that list.
        //The failed cluster list is a comma separated string of clustercode
        $query = "UPDATE ".TBL_TOPIC_STATUS." SET failedClusters=
                    TRIM(BOTH ',' FROM REPLACE(CONCAT(',',failedClusters,','),CONCAT(',','$clusterCode',','),','))
                  WHERE ttAttemptID=$ttAttemptID";
        mysql_query($query);
    }
	
?>
