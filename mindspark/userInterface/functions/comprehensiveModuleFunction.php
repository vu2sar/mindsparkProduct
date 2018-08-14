<?php
// Check comprehensive module mapped
function checkForComprehensiveModule($ttAttemptID, $userID, $sessionID,$testType,$clusterCode="")	
{	
	$querySettings = "SELECT * from userInterfaceSettings a where a.schoolCode=".$_SESSION['schoolCode']." and a.class=".$_SESSION['childClass']." and a.settingName='comprehensiveModuleActivation' and a.settingValue=1";		
	$settingsResult = mysql_query($querySettings);	
	if((mysql_num_rows($settingsResult) > 0) || ($_SESSION['admin']=="GUEST") )
	{
		$settingsLine = mysql_fetch_array($settingsResult);
		$insertData = array();
		$_SESSION['comprehensiveModule']	=	"";
		$_SESSION['comprehensiveModule_srno']	=	"";
		$_SESSION['diagnosticTestType'] = "";
		$clusters = "";
		$clusterArray = getClusterList($_SESSION['teacherTopicCode'],$_SESSION['flow']);
		if($clusterCode !="")
		{
			if(in_array($clusterCode, $clusterArray))
				$clusters = $clusterCode;
		}
		else
			$clusters = implode("','", $clusterArray);
		if($clusters != "")
		{
			$query="SELECT clusterAttemptID,clusterCode from adepts_teacherTopicClusterStatus  where ttAttemptID=$ttAttemptID and userID=$userID AND clusterCode IN('$clusters') group by clusterCode";				
			$result = mysql_query($query);
			if(mysql_num_rows($result) > 0)
			{
				$j=0;
				while($line=mysql_fetch_array($result))
				{
					$cmQuery = "SELECT a.comprehensiveModuleCode,a.linkedToDiagnosticTest from adepts_comprehensiveModuleMaster a JOIN adepts_diagnosticTestMaster b ON a.linkedToDiagnosticTest=b.diagnosticTestID and b.linkToCluster='$line[1]' where FIND_IN_SET('$line[1]',a.linkedToCluster) and a.status='Live' and b.status = 1 and b.testType='$testType'";		
					$cmResult = mysql_query($cmQuery);
					if(mysql_num_rows($cmResult) > 0)
					{
						while($cmLine=mysql_fetch_array($cmResult))
						{
							$insertData[$j]['userID'] = $userID;
							$insertData[$j]['ttAttemptID'] = $ttAttemptID;
							$insertData[$j]['clusterAttemptID'] = $line[0];
							$insertData[$j]['sessionID'] = $sessionID;
							$insertData[$j]['comprehensiveModuleCode'] = $cmLine[0];
							$insertData[$j]['currentActivityCode']=$cmLine[1];
							$insertData[$j]['currentActivityType']='Diagnostic';
							$insertData[$j]['status']=0;

							$comprehensiveModuleArray[] = $cmLine[0];
							$j++;
						}
					}
				}
			}

			// check if entries are already made
			$allComprehensiveModule = implode("','",$comprehensiveModuleArray);
			$checkQuery = "SELECT count(a.srno) from adepts_comprehensiveModuleAttempt a where a.comprehensiveModuleCode IN ('".$allComprehensiveModule."') and a.userID=$userID and a.ttAttemptID=$ttAttemptID";		
			$checkResult = mysql_query($checkQuery);
			while($checkLine=mysql_fetch_array($checkResult))
			{
				if($checkLine[0] == 0)
				{
					if(!empty($insertData))
					{
					    foreach ($insertData as $key => $value)
					    {
					    	// insert all comprehensive modules mapped to cluster into adepts_comprehensiveModuleAttempt 
					    	$insertCMQuery = "INSERT INTO adepts_comprehensiveModuleAttempt (userID,ttAttemptID,clusterAttemptID,sessionID,comprehensiveModuleCode,currentActivityCode,currentActivityType,status) VALUES (" . $value['userID'] . ", " . $value['ttAttemptID'] . ", " . $value['clusterAttemptID'] . ", " . $value['sessionID'] . ", '" . $value['comprehensiveModuleCode'] . "', '" . $value['currentActivityCode'] . "', '" . $value['currentActivityType'] . "', " . $value['status'] . ")";	    	
							mysql_query($insertCMQuery);
							$insert_id = mysql_insert_id();

							// insert diagnostic tests into adepts_diagnosticTestAttempts
							$insertDTQuery="INSERT INTO adepts_diagnosticTestAttempts (userID,diagnosticTestID,ttAttemptID,srno,status) VALUES (" . $value['userID'] . ", '" .$value['currentActivityCode']. "', " . $value['ttAttemptID'] . ", " . $insert_id . ",  " . $value['status'] . ")";				
							mysql_query($insertDTQuery);

							// add first comprehensiveModule srno in session
							if($key == 0)
							{
								$_SESSION['comprehensiveModule_srno']	=	$insert_id;
								$_SESSION['comprehensiveModule']	=	$value['comprehensiveModuleCode'];
								$_SESSION['diagnosticTest'] = $value['currentActivityCode'];
								$_SESSION['diagnosticTestType'] = $testType;
							}
						}
					}
				}
			}	
		}
		
	}
					    	  
}

function getClusterList($teacherTopicCode,$flow)
{
	$clusterCodeArray = array();
	if($flow=="MS" || $flow=="CBSE" || $flow=="ICSE" || $flow=="IGCSE" || $flow=="")
	{
		$query = "SELECT a.clusterCode FROM adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
	              	  WHERE  a.clusterCode=b.clusterCode AND a.teacherTopicCode='".$teacherTopicCode."' AND b.status='live' AND
	              		     a.clusterCode NOT LIKE '%100' ORDER BY a.flowno";
		$result = mysql_query($query) or die(mysql_error());
		while($line   = mysql_fetch_array($result))
		{
			array_push($clusterCodeArray, $line['clusterCode']);
		}
	}
	else if(strcasecmp(substr($flow,0,6),"Custom")==0)
	{
		$customizedFlow = substr($flow,9);
		$query   = "SELECT clustercodes FROM adepts_customizedTopicDetails WHERE code=$customizedFlow";
		$result  = mysql_query($query) or die("Error in getting the details of customization for the topic!");		 
		if($line = mysql_fetch_array($result))
		{
			$clusterCodeArray = explode(',', $line['clustercodes']);
		}
	}
	return $clusterCodeArray;
}

function setComprehensiveFlowForKst($misconceptionsGenerated,$userID,$testType,$teacherTopicCode,$teacherTopicAttemptID,$sessionID)
{
	$currentFlowID  = "";
	$misconceptionCodesStr	=	$misconceptionsGenerated;
	$currentActivityCode = "";
	$currentActivityType = "";
	//This query returns only the submodule code in most case it will only be one code.
	$sqModule	=	"SELECT moduleCode FROM educatio_adepts.kst_SubModuleMaster WHERE misconceptionCode IN ('$misconceptionCodesStr') ORDER BY flowNo";
	$rsModule	=	mysql_query($sqModule);
	if(mysql_num_rows($rsModule)>0)
	{
		$i=0;
		while($rwModule=mysql_fetch_array($rsModule))
		{
			$sqFlow	=	"SELECT flowID,moduleType,activityCode,adaptiveRule FROM educatio_adepts.kst_ModuleFlow WHERE moduleCode='".$rwModule[0]."' ORDER BY flowNo";
			$rsFlow	=	mysql_query($sqFlow);
			while($rwFlow=mysql_fetch_array($rsFlow, MYSQL_ASSOC)){
				$subModuleDetails[] = $rwFlow;
				$moduleType = $rwFlow['moduleType'];
				$flowID = $rwFlow['flowID'];
				$sqSetFlow	=	"INSERT INTO educatio_adepts.kst_userFlowDetails (userID, flowIDAssign, moduleType, status)
								SELECT * FROM (SELECT $userID, $flowID, '$moduleType', 0) AS tmp
								WHERE NOT EXISTS (SELECT userID FROM educatio_adepts.kst_userFlowDetails WHERE userID = $userID and flowIDAssign = $flowID) LIMIT 1";
				mysql_query($sqSetFlow) or die(mysql_error());
				if($i==0)
				{
					$currentFlowID = 	mysql_insert_id();
					$currentActivityCode	=	$rwFlow['activityCode'];
					$currentActivityType	=	$moduleType;
				}
				$i++;
			}
		}
	$_SESSION['subModuleDetails'] = $subModuleDetails;
	$misconceptionCodesStr = "'".implode(",",$misconceptionsGenerated)."'";
	$sqStart = "INSERT INTO `educatio_adepts`.`kst_ModuleAttempt` (userID, ttAttemptID, sessionID, currentAttemptID, currentActivityCode, currentActivityType,status) VALUES ($userID,$teacherTopicAttemptID,$sessionID,$currentFlowID,'$currentActivityCode','$currentActivityType',0)";
	mysql_query($sqStart) or die(mysql_error());
	$_SESSION['subModule_srno'] = mysql_insert_id();
}
}


function setComprehensiveFlow($misconceptionsGenerated,$userID,$diagnosticTestID,$testType,$teacherTopicCode,$clusterCode)
{
	$misconceptionCodesStr	=	implode("','",$misconceptionsGenerated);
	$currentActivityCode = "";
	$currentActivityType = "";
	$sqModule	=	"SELECT moduleCode,flowNo FROM adepts_comprehensiveSubModuleMaster
					 WHERE misconceptionCode IN ('$misconceptionCodesStr') AND comprehensiveModuleCode='".$_SESSION['comprehensiveModule']."' ORDER BY flowNo";
	$rsModule	=	mysql_query($sqModule);
	if(mysql_num_rows($rsModule)>0)
	{
		$i=0;
		while($rwModule=mysql_fetch_array($rsModule))
		{
			$sqFlow	=	"SELECT flowID,flowNo,modleType,activityCode FROM adepts_comprehensiveModuleFlow WHERE moduleCode='".$rwModule[0]."' ORDER BY flowNo";
			$rsFlow	=	mysql_query($sqFlow);
			while($rwFlow=mysql_fetch_array($rsFlow))
			{
				if(checkIfExist($userID,$_SESSION['comprehensiveModule_srno'],$rwFlow[0]))
				{
					$sqSetFlow	=	"INSERT INTO adepts_userComprehensiveFlow
									 SET userID=$userID, srno=".$_SESSION['comprehensiveModule_srno'].", flowIDAssign=$rwFlow[0], moduleType='".$rwFlow[2]."', status=0";
					$rsSetFlow	=	mysql_query($sqSetFlow);
					if($i==0)
					{
						$currentFlowID = 	mysql_insert_id();
						$currentActivityCode	=	$rwFlow[3];
						$currentActivityType	=	$rwFlow[2];
					}
					$i++;
				}
			}
		}
		$sqStart	=	"UPDATE adepts_comprehensiveModuleAttempt SET ";
		if($testType == "Prerequisite")
		 	$sqStart .= " preDiagnostic='".implode(",",$misconceptionsGenerated)."',";
		 else
		 	$sqStart .= " postDiagnostic='".implode(",",$misconceptionsGenerated)."',";

		 $sqStart .= " currentAttemptID=".$currentFlowID.", currentActivityCode='$currentActivityCode', 
						 currentActivityType='$currentActivityType' WHERE srno=".$_SESSION['comprehensiveModule_srno'];

		$rsStart	=	mysql_query($sqStart);
		if($testType == "Prerequisite")
		{
			$_SESSION["currentFlowID"]	=	$currentActivityCode;
			if(strtolower($currentActivityType)=="timedtest")
			{
				$_SESSION["quesCategory"] = "comprehensive";
				$_SESSION['timedTest'] = $currentActivityCode;
			}
			else if(strtolower($currentActivityType)=="activity")
			{
				$_SESSION['gameID']	=	 $currentActivityCode;
			}
		}	
		if($currentActivityCode=="" && $currentActivityType=="")				
			return completeComprehensiveFlow($misconceptionsGenerated,$userID,$diagnosticTestID,$testType,$clusterCode,$teacherTopicCode);					
		else 
			return "nextcluster~1~$clusterCode~$testType";		;
	}
	else	
		return completeComprehensiveFlow($misconceptionsGenerated,$userID,$diagnosticTestID,$testType,$clusterCode,$teacherTopicCode);	
}
function setComprehensiveFlowAssessment($userID,$ttAttemptID,$teacherTopicCode,$clusterCode,$testType)
{	
	$query = "SELECT a.srno from adepts_comprehensiveModuleAttempt a where a.ttAttemptID=$ttAttemptID and a.userID=$userID and a.status=0 limit 1";	
	$result = mysql_query($query);
	if($line=mysql_fetch_array($result))
	{
		$sq = "SELECT A.flowAttemptID, B.flowID, B.moduleType, B.activityCode FROM adepts_userComprehensiveFlow A, adepts_comprehensiveModuleFlow B
			 WHERE A.srno=".$line['srno']." AND A.flowIDAssign=B.flowID AND status=0 ORDER BY A.flowAttemptID LIMIT 1";
		$rs	=	mysql_query($sq);
		if($rw=mysql_fetch_array($rs))
		{
			$currentActivityCode = $rw[3];
			$currentActivityType = $rw[2];
			$_SESSION["currentFlowID"]	=	$rw[0];
			if(strtolower($currentActivityType)=="timedtest")
			{
				$_SESSION["quesCategory"] = "comprehensive";
				$_SESSION['timedTest'] = $currentActivityCode;
			}
			else if(strtolower($currentActivityType)=="activity")
			{
				$_SESSION['gameID']	=	 $currentActivityCode;
			}
		}	
	}	
	$queryDT = "SELECT GROUP_CONCAT(a.srno) from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno JOIN adepts_diagnosticTestMaster c ON c.diagnosticTestID=b.diagnosticTestID where a.ttAttemptID=$ttAttemptID and a.userID=$userID and c.testType ='$testType'";
	$resultDT = mysql_query($queryDT);
	if($lineDT = mysql_fetch_array($resultDT))
	{
		$queryDQ = "SELECT SUM(R),count(a.srno) from adepts_diagnosticQuestionAttempt a where a.attemptID IN($lineDT[0])";
		$resultDQ = mysql_query($queryDQ);
		if($lineDQ = mysql_fetch_array($resultDQ))					
			$message  = makeMessage($lineDQ[0],$lineDQ[1]);									
	}			
	if($currentActivityCode=="" && $currentActivityType=="")
		return "Saved~3~$teacherTopicCode~$testType~$message";	
	else
		return "nextcluster~3~$teacherTopicCode~$testType~$message";
}
function makeMessage($totalCorrect,$totalAttempt){
	$accuracy = round(($totalCorrect*100/$totalAttempt),2);
	$Name = trim($_SESSION['childName']) != ''? ", ".ucfirst(strtolower($_SESSION['childName'])) : '';	
	$message = "";
	$message = "You have answered $totalCorrect out of $totalAttempt questions correctly$Name.<br>";
	if($accuracy >= 80)
		$message .= " Great going!";
	else if($accuracy <80 && $accuracy >=40)
		$message .= " We appreciate your efforts in learning the topic. Keep up the good work!";
	else
		$message .= " We appreciate your efforts in learning the topic. Recommend revising the topic to get better at it!";

	return $message;
}
function completeComprehensiveFlow($misconceptionsGenerated,$userID,$diagnosticTestID,$type,$clusterCode,$teacherTopicCode)
{
	$_SESSION["comprehensiveModule"] = $clusterCode= "";
	$sqStart	=	"UPDATE adepts_comprehensiveModuleAttempt SET";
	if($type=="Prerequisite")
		$sqStart	.=	" preDiagnostic='".implode(",",$misconceptionsGenerated)."',";
	else
		$sqStart	.=	" postDiagnostic='".implode(",",$misconceptionsGenerated)."',";
	$sqStart	.=	" currentAttemptID=0, status=1 WHERE srno=".$_SESSION['comprehensiveModule_srno']."";
	if($rsStart=mysql_query($sqStart))
	{
		$_SESSION['comprehensiveModule_srno'] = $_SESSION["comprehensiveModule"] = "";
		
		if($type=="Prerequisite")
			return "Saved~1~$clusterCode~$type";				
	}
}

function checkPendingComprehensiveModule($ttAttemptID)
{

	$sq = "SELECT comprehensiveModuleCode, currentActivityCode, currentActivityType, currentActivityDetail, currentAttemptID, a.srno  FROM adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b on a.srno=b.srno WHERE a.ttAttemptID=$ttAttemptID AND a.status=0 and b.status=0 AND a.userID=".$_SESSION['userID']." limit 1";
	$rs	=	mysql_query($sq);	
	if(mysql_num_rows($rs) == 0)
	{
		$sq	=	"SELECT comprehensiveModuleCode, currentActivityCode, currentActivityType, currentActivityDetail, currentAttemptID, srno FROM adepts_comprehensiveModuleAttempt WHERE ttAttemptID=$ttAttemptID AND status=0 AND userID=".$_SESSION['userID']." limit 1";
		$rs	=	mysql_query($sq);			
	}					
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION['comprehensiveModule_srno']	=	$rw[5];
		$_SESSION["currentFlowID"]	=	$rw[4];

		if($rw[2] == 'Diagnostic')
		{
			$query = "SELECT a.linkToCluster,a.testType from adepts_diagnosticTestMaster a where a.diagnosticTestID='".$rw[1]."'";
			$result	=	mysql_query($query);
			if($line=mysql_fetch_array($result))
			{
				$_SESSION['diagnosticTestClusterCode'] = $line[0];
				$_SESSION['diagnosticTestType'] = $line[1];
			}			
			
		}				
		return $rw[0]."$".$rw[1]."$".$rw[2]."$".$rw[3];
	}
	else
		return "";
}

function getNextComprehensiveInflow()
{
	$sq	=	"SELECT A.flowAttemptID, B.flowID, B.moduleType, B.activityCode FROM adepts_userComprehensiveFlow A, adepts_comprehensiveModuleFlow B
			 WHERE A.srno=".$_SESSION['comprehensiveModule_srno']." AND A.flowIDAssign=B.flowID AND status=0 ORDER BY A.flowAttemptID LIMIT 1";			
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION["currentFlowID"]	=	$rw[0];
		$sqStart	=	"UPDATE adepts_comprehensiveModuleAttempt SET currentAttemptID=".$_SESSION["currentFlowID"].", currentActivityCode='".$rw[3]."', 
						 currentActivityType='".$rw[2]."' WHERE srno=".$_SESSION['comprehensiveModule_srno']."";
		$rsStart	=	mysql_query($sqStart);
	}
	else
	{		
		$sqStart	=	"UPDATE adepts_comprehensiveModuleAttempt SET status=1 WHERE srno=".$_SESSION['comprehensiveModule_srno'];
		$rsStart    =    mysql_query($sqStart);	
		
		if($_SESSION['diagnosticTestType'] == 'Assessment')
			$_SESSION['choiceScreenFlagDT'] =  "3~".$_SESSION['teacherTopicCode']."~".$_SESSION["teacherTopicAttemptID"];
		if($_SESSION['diagnosticTestType'] == 'Prerequisite')
			$_SESSION["comprehensiveModuleCompleted"] =1;						
		$_SESSION['comprehensiveModule_srno'] = $_SESSION["comprehensiveModule"] =$_SESSION['diagnosticTestType'] = "";
	}	
}

function getNextKstSubModuleInflow()
{
	
	$sq	=	"SELECT A.flowAttemptID, B.flowID, B.moduleType, B.activityCode FROM  educatio_adepts.kst_userFlowDetails A, educatio_adepts.kst_ModuleFlow B
			 WHERE A.flowIDAssign=B.flowID AND status=0 ORDER BY A.flowAttemptID LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION["currentFlowID"]	=	$rw[0];
		$sqStart	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentAttemptID=".$_SESSION["currentFlowID"].", currentActivityCode='".$rw[3]."', 
						 currentActivityType='".$rw[2]."' WHERE srno=".$_SESSION['subModule_srno']."";
		$rsStart	=	mysql_query($sqStart);
	}
	else
	{
		$sqStart	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET status=1 WHERE srno=".$_SESSION['subModule_srno'];
		$rsStart    =    mysql_query($sqStart);	

		if($_SESSION['kstdiagnosticTest']['featureType'] == 'Pretest with prerequisite modules and post test')
			$_SESSION['choiceScreenFlagDT'] =  "3~".$_SESSION['teacherTopicCode']."~".$_SESSION["teacherTopicAttemptID"];
		if($_SESSION['kstdiagnosticTest']['featureType'] == 'Pretest and Prerequisite')
			$_SESSION["subModuleCompleted"] =1;
		$_SESSION['subModule_srno'] = $_SESSION["subModule"] = $_SESSION['timedTest']  = "";
	}
}
function getPostDiagnostic()
{
	$sq	=	"SELECT diagnosticTestID FROM adepts_diagnosticTestAttempts WHERE srno=".$_SESSION['comprehensiveModule_srno'];
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function getComphrensiveQcode($clusterCode)
{
	$sq	=	"SELECT adaptiveRule,A.flowAttemptID FROM adepts_userComprehensiveFlow A, adepts_comprehensiveModuleFlow B
			 WHERE srno=".$_SESSION['comprehensiveModule_srno']." AND A.flowIDAssign=B.flowID AND activityCode='".$clusterCode."' AND status=0 ORDER BY flowAttemptID LIMIT 1";			
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION["qcode"]	=	0;
		$_SESSION["currentSDL"]	=	"";
		$_SESSION["flowAttemptID"]	=	$rw[1];		
		$_SESSION['sdlAttemptResult']=array();
        $_SESSION['sdlAttemptResultData']=array();
		$sqGetQcode	=	"SELECT currentActivityDetail FROM adepts_comprehensiveModuleAttempt WHERE srno=".$_SESSION['comprehensiveModule_srno'];
		$rsGetQcode	=	mysql_query($sqGetQcode);			
		if($lineGetQcode	=	mysql_fetch_array($rsGetQcode))
		{
			$qcodeStr	= explode("~",$lineGetQcode[0]);			
			$_SESSION["qcode"]	=	$qcodeStr[0];
			$_SESSION["currentSDL"]	=	$qcodeStr[1];			
		}		
		$sqQcode	=	"SELECT qcode,subdifficultylevel,dynamic FROM adepts_questions WHERE clusterCode='$clusterCode'";
		if($rw[0]!="all")
			 $sqQcode	.=	" AND subdifficultylevel IN (".$rw[0].")";
		if($_SESSION["currentSDL"]!="")
			 $sqQcode	.=	" AND subdifficultylevel >= ".$_SESSION["currentSDL"];
		if($_SESSION['flashContent'] == 0)
				$sqQcode .= " AND (question NOT LIKE '%[%.swf%]%')";
		$sqQcodeAttempted	=	"SELECT GROUP_CONCAT(activityDetail) FROM adepts_userComprehensiveFlowDetails WHERE flowAttemptID=$rw[1]";
		$rsQcodeAttempted	=	mysql_query($sqQcodeAttempted);
		$rwQcodeAttempted	=	mysql_fetch_array($rsQcodeAttempted);
		if($rwQcodeAttempted[0] != "")
		{
			$sqQcode	.=	" AND qcode NOT IN (".$rwQcodeAttempted[0].")";
		}
		$sqQcode	.=	" ORDER BY subdifficultylevel";
		$rsQcode	=	mysql_query($sqQcode);		
		$k=0;
		setProgressBar($rw[1]);
		while($rwQcode = mysql_fetch_array($rsQcode))
		{
			$qcode = $rwQcode["qcode"];
			$sdl   = $rwQcode["subdifficultylevel"];
			if($k==0)
			{
				if($_SESSION["qcode"]=="")
				{
					$_SESSION["qcode"]	=	$qcode;
					$_SESSION["currentSDL"]	=	$sdl;
				}
				$k++;
				continue;
			}
			else if($_SESSION["qcode"]==$qcode)
				continue;
			
			$dynamic = $rwQcode["dynamic"];

			if($dynamic)
			{
				for($d=0;$d<3;$d++)
				{
					if(!isset($allQuestionsArray[$sdl]))
					{
						$allQuestionsArray[$sdl]=array();											
						$_SESSION['sdlAttemptResult'][$sdl] = 4;
		                $_SESSION['sdlAttemptResultData'][$sdl] = array();
		                $_SESSION["noOfAttemptsOnSdl"][$sdl]	=	0;
					}					
					array_push($allQuestionsArray[$sdl],$qcode);
				}
			}
			else
			{
				if(!isset($allQuestionsArray[$sdl]))
				{
					$allQuestionsArray[$sdl]=array();						                				
					$_SESSION['sdlAttemptResult'][$sdl] = 4;
	                $_SESSION['sdlAttemptResultData'][$sdl] = array();
	                $_SESSION["noOfAttemptsOnSdl"][$sdl]	=	0;
				}				
				array_push($allQuestionsArray[$sdl],$qcode);
			}
		}
		$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;					
		$sqSetQcode	=	"UPDATE adepts_comprehensiveModuleAttempt SET currentActivityDetail='".$_SESSION["qcode"]."~".$_SESSION["currentSDL"]."'
						 WHERE srno=".$_SESSION['comprehensiveModule_srno'];
		$rsSetQcode	=	mysql_query($sqSetQcode);
		return $_SESSION["qcode"];
	}
}

function getKstQcode($clusterCode)
{
	$sq	=	"SELECT adaptiveRule,A.flowAttemptID FROM educatio_adepts.kst_userFlowDetails A, educatio_adepts.kst_ModuleFlow B
			 WHERE srno=".$_SESSION['subModule_srno']." AND A.flowIDAssign=B.flowID AND activityCode='".trim($clusterCode)."' AND status=0 ORDER BY flowAttemptID LIMIT 1";

	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION["qcode"]	=	0;
		$_SESSION["currentSDL"]	=	"";
		$_SESSION["flowAttemptID"]	=	$rw[1];	
		$_SESSION['sdlAttemptResult']=array();
        $_SESSION['sdlAttemptResultData']=array();
		$sqGetQcode	=	"SELECT currentActivityDetail FROM educatio_adepts.kst_ModuleAttempt WHERE srno=".$_SESSION['subModule_srno'];
		$rsGetQcode	=	mysql_query($sqGetQcode);
		if($lineGetQcode	=	mysql_fetch_array($rsGetQcode))
		{
			$qcodeStr	= explode("~",$lineGetQcode[0]);
			$_SESSION["qcode"]	=	$qcodeStr[0];
			$_SESSION["currentSDL"]	=	$qcodeStr[1];
		}		
		$sqQcode	=	"SELECT qcode,subdifficultylevel,dynamic FROM adepts_questions WHERE clusterCode='$clusterCode'";
		if($rw[0]!="all")
			 $sqQcode	.=	" AND subdifficultylevel IN (".$rw[0].")";
		if($_SESSION["currentSDL"]!="")
			 $sqQcode	.=	" AND subdifficultylevel >= ".$_SESSION["currentSDL"];
		if($_SESSION['flashContent'] == 0)
				$sqQcode .= " AND (question NOT LIKE '%[%.swf%]%')";
		$sqQcodeAttempted	=	"SELECT GROUP_CONCAT(activityDetail) FROM educatio_adepts.kst_userFlowDetails WHERE flowAttemptID=$rw[1]";
		$rsQcodeAttempted	=	mysql_query($sqQcodeAttempted);
		$rwQcodeAttempted	=	mysql_fetch_array($rsQcodeAttempted);
		if($rwQcodeAttempted[0] != "")
		{
			$sqQcode	.=	" AND qcode NOT IN (".$rwQcodeAttempted[0].")";
		}
		$sqQcode	.=	" ORDER BY subdifficultylevel";
		$rsQcode	=	mysql_query($sqQcode);		
		$k=0;
		setProgressBarForKst($rw[1]);
		while($rwQcode = mysql_fetch_array($rsQcode))
		{
			$qcode = $rwQcode["qcode"];
			$sdl   = $rwQcode["subdifficultylevel"];
			if($k==0)
			{
				if($_SESSION["qcode"]=="")
				{
					$_SESSION["qcode"]	=	$qcode;
					$_SESSION["currentSDL"]	=	$sdl;
				}
				$k++;
				continue;
			}
			else if($_SESSION["qcode"]==$qcode)
				continue;
			
			$dynamic = $rwQcode["dynamic"];

			if($dynamic)
			{
				for($d=0;$d<3;$d++)
				{
					if(!isset($allQuestionsArray[$sdl]))
					{
						$allQuestionsArray[$sdl]=array();											
						$_SESSION['sdlAttemptResult'][$sdl] = 4;
		                $_SESSION['sdlAttemptResultData'][$sdl] = array();
		                $_SESSION["noOfAttemptsOnSdl"][$sdl]	=	0;
					}					
					array_push($allQuestionsArray[$sdl],$qcode);
				}
			}
			else
			{
				if(!isset($allQuestionsArray[$sdl]))
				{
					$allQuestionsArray[$sdl]=array();						                				
					$_SESSION['sdlAttemptResult'][$sdl] = 4;
	                $_SESSION['sdlAttemptResultData'][$sdl] = array();
	                $_SESSION["noOfAttemptsOnSdl"][$sdl]	=	0;
				}				
				array_push($allQuestionsArray[$sdl],$qcode);
			}
		}
		$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;					
		$sqSetQcode	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='".$_SESSION["qcode"]."~".$_SESSION["currentSDL"]."'
						 WHERE srno=".$_SESSION['subModule_srno'];
		$rsSetQcode	=	mysql_query($sqSetQcode);
		return $_SESSION["qcode"];
	}
}

function setProgressBar($flowAttemptID)
{
	$sqQcode = "SELECT DISTINCT a.qcode, b.subdifficultylevel, a.R  FROM  adepts_researchQuesAttempt a JOIN adepts_questions b on a.qcode=b.qcode JOIN adepts_userComprehensiveFlowDetails c ON c.activityAttemptID=a.srno WHERE c.flowAttemptID=$flowAttemptID ORDER BY srno";
	$rsQcode	=	mysql_query($sqQcode);		
	while($rwQcode = mysql_fetch_array($rsQcode))
	{
		if(!isset($_SESSION['sdlAttemptResult'][$rwQcode[1]]))
		{
			$sdlArray[] = $rwQcode[1];
			$_SESSION['sdlAttemptResult'][$rwQcode[1]] = 4; 
			$_SESSION['sdlAttemptResultData'][$rwQcode[1]] = array();
			$_SESSION["noOfAttemptsOnSdl"][$rwQcode[1]] = 0;
		}
		if($rwQcode[2] == 1)
		{
			array_push($_SESSION['sdlAttemptResultData'][$rwQcode[1]], 1);
		}
		else
		{
			array_push($_SESSION['sdlAttemptResultData'][$rwQcode[1]], 0);
		}
		$_SESSION["noOfAttemptsOnSdl"][$rwQcode[1]]++ ;		

	}
	foreach($sdlArray as $sdls)
	{
		if ($_SESSION["childClass"]>3 || $_SESSION["noOfAttemptsOnSdl"][$sdls]>=3) 
			$_SESSION['sdlAttemptResult'][$sdls] = max($_SESSION['sdlAttemptResultData'][$sdls]);
	}
}

function setProgressBarForKst($flowAttemptID)
{
	$sqQcode = "SELECT DISTINCT a.qcode, b.subdifficultylevel, a.R  FROM  adepts_researchQuesAttempt a JOIN adepts_questions b on a.qcode=b.qcode JOIN educatio_adepts.kst_userFlowDetails c ON c.activityAttemptID=a.srno WHERE c.flowAttemptID=$flowAttemptID ORDER BY srno";
	$rsQcode	=	mysql_query($sqQcode);		
	while($rwQcode = mysql_fetch_array($rsQcode))
	{
		if(!isset($_SESSION['sdlAttemptResult'][$rwQcode[1]]))
		{
			$sdlArray[] = $rwQcode[1];
			$_SESSION['sdlAttemptResult'][$rwQcode[1]] = 4; 
			$_SESSION['sdlAttemptResultData'][$rwQcode[1]] = array();
			$_SESSION["noOfAttemptsOnSdl"][$rwQcode[1]] = 0;
		}
		if($rwQcode[2] == 1)
		{
			array_push($_SESSION['sdlAttemptResultData'][$rwQcode[1]], 1);
		}
		else
		{
			array_push($_SESSION['sdlAttemptResultData'][$rwQcode[1]], 0);
		}
		$_SESSION["noOfAttemptsOnSdl"][$rwQcode[1]]++ ;		

	}
	foreach($sdlArray as $sdls)
	{
		if ($_SESSION["childClass"]>3 || $_SESSION["noOfAttemptsOnSdl"][$sdls]>=3) 
			$_SESSION['sdlAttemptResult'][$sdls] = max($_SESSION['sdlAttemptResultData'][$sdls]);
	}
}

function getNextComprehensiveClusterQuetion($lastqcode,$responseResult,$QAsrno)
{
	$sqQcodeAttempted = "INSERT INTO adepts_userComprehensiveFlowDetails SET activityAttemptID = $QAsrno, activityDetail = $lastqcode, flowAttemptID=".$_SESSION["flowAttemptID"].", userID=".$_SESSION["userID"];
	mysql_query($sqQcodeAttempted);


	$_SESSION["qcode"] = 0;
	$allQuestionsArray	=	$_SESSION["allQuestionsArray"];

	if($responseResult==1)
		unset($allQuestionsArray[$_SESSION["currentSDL"]]);
	if($responseResult == 1)
			array_push($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]], 1);
	else	
			array_push($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]], 0);

	$_SESSION["noOfAttemptsOnSdl"][$_SESSION["currentSDL"]]++ ;

	if ($_SESSION["childClass"]>3 || $_SESSION["noOfAttemptsOnSdl"][$_SESSION["currentSDL"]]>=3)
		$_SESSION['sdlAttemptResult'][$_SESSION["currentSDL"]] = max($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]]);


	foreach($allQuestionsArray as $sdl=>$totalQcodes)
	{
		if($sdl==$_SESSION["currentSDL"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
		}
		else if($sdl > $_SESSION["currentSDL"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
			$_SESSION["currentSDL"]	=	$sdl;
		}
		if(count($totalQcodes)==1)
			unset($allQuestionsArray[$sdl]);
		else
			unset($allQuestionsArray[$sdl][array_search($totalQcodes[0],$allQuestionsArray[$sdl])]);
		break;
	}


	$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;

	if($_SESSION["qcode"]==0)
	{
		$sqTimeTaken	=	"SELECT SUM(A.s) FROM adepts_researchQuesAttempt A , adepts_userComprehensiveFlowDetails B
							 WHERE flowAttemptID=".$_SESSION["flowAttemptID"]." AND A.srno=B.activityAttemptID";
		$rsTimeTaken	=	mysql_query($sqTimeTaken);
		$rwTimeTaken	=	mysql_fetch_array($rsTimeTaken);
		
		$sqFinish	=	"UPDATE adepts_userComprehensiveFlow SET status = 1, timeTaken='".$rwTimeTaken[0]."' WHERE flowAttemptID=".$_SESSION["flowAttemptID"];
		$rsFinish	=	mysql_query($sqFinish);
		
		$sqSetCurrentActivityDetail	=	"UPDATE adepts_comprehensiveModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['comprehensiveModule_srno'];
		$rsSetCurrentActivityDetail	=	mysql_query($sqSetCurrentActivityDetail);
		$_SESSION["currentSDL"] = "";
	}
	else
	{
		$sqSetQcode	=	"UPDATE adepts_comprehensiveModuleAttempt SET currentActivityDetail='".$_SESSION["qcode"]."~".$_SESSION["currentSDL"]."'
						 WHERE srno=".$_SESSION['comprehensiveModule_srno'];
		$rsSetQcode	=	mysql_query($sqSetQcode);
	}
	return $_SESSION["qcode"]."~0";
}
function getnextKstModuleQuestion($lastqcode,$responseResult,$QAsrno)
{
	$sqQcodeAttempted = "UPDATE educatio_adepts.kst_userFlowDetails SET activityAttemptID = $QAsrno, activityDetail = $lastqcode WHERE flowAttemptID=".$_SESSION["flowAttemptID"].", userID=".$_SESSION["userID"];
	mysql_query($sqQcodeAttempted);

	$_SESSION["qcode"] = 0;
	$allQuestionsArray	=	$_SESSION["allQuestionsArray"];

	if($responseResult==1)
		unset($allQuestionsArray[$_SESSION["currentSDL"]]);
	if($responseResult == 1)
		array_push($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]], 1);
	else
		array_push($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]], 0);

	$_SESSION["noOfAttemptsOnSdl"][$_SESSION["currentSDL"]]++ ;

	if ($_SESSION["childClass"]>3 || $_SESSION["noOfAttemptsOnSdl"][$_SESSION["currentSDL"]]>=3)
		$_SESSION['sdlAttemptResult'][$_SESSION["currentSDL"]] = max($_SESSION['sdlAttemptResultData'][$_SESSION["currentSDL"]]);


	foreach($allQuestionsArray as $sdl=>$totalQcodes)
	{
		if($sdl==$_SESSION["currentSDL"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
		}
		else if($sdl > $_SESSION["currentSDL"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
			$_SESSION["currentSDL"]	=	$sdl;
		}
		if(count($totalQcodes)==1)
			unset($allQuestionsArray[$sdl]);
		else
			unset($allQuestionsArray[$sdl][array_search($totalQcodes[0],$allQuestionsArray[$sdl])]);
		break;
	}


	$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;

	if($_SESSION["qcode"]==0)
	{
		$timeTaken = array_sum($_SESSION['timeTakenForCluster']);
		$sqFinish	=	"UPDATE educatio_adepts.kst_userFlowDetails SET status = 1, timeTaken='".$timeTaken."' WHERE flowAttemptID=".$_SESSION["flowAttemptID"];
		mysql_query($sqFinish);
		unset($_SESSION['timeTakenForCluster']);
		
		$sqSetCurrentActivityDetail	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['subModule_srno'];
		mysql_query($sqSetCurrentActivityDetail);
		$_SESSION["currentSDL"] = "";
	}
	else
	{
		$sqSetQcode	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='".$_SESSION["qcode"]."~".$_SESSION["currentSDL"]."'
						 WHERE srno=".$_SESSION['subModule_srno'];
		mysql_query($sqSetQcode);
	}
	return $_SESSION["qcode"]."~0";
}

function redirectToFlow()
{
	$_SESSION['comprehensiveModule'] = "";
	$_SESSION['comprehensiveCluster'] = 0;
	$_SESSION['comprehensiveModule_srno'] = "";
	$comprehensiveModuleDetails	=	checkPendingComprehensiveModule($_SESSION['teacherTopicAttemptID']);	
	if($comprehensiveModuleDetails=="")
	{
		return;
	}
	$comprehensiveModuleDetailsArr	=	explode("$",$comprehensiveModuleDetails);
	$_SESSION['comprehensiveModule']	=	$comprehensiveModuleDetailsArr[0];
	
	if($comprehensiveModuleDetailsArr[2]=="remedial")
	{
		$nextPage     = "remedialItem.php";
		$quesCategory = "comprehensive";
		$qcode	=	$comprehensiveModuleDetailsArr[1];
	}
	else if(strtolower($comprehensiveModuleDetailsArr[2])=="timedtest")
	{
		$nextPage     = "timedTest.php";
		$_SESSION["quesCategory"] = $quesCategory = "comprehensive";
		$_SESSION['timedTest']	=	$comprehensiveModuleDetailsArr[1];
	}
	else if(strtolower($comprehensiveModuleDetailsArr[2])=="activity")
	{
		$_SESSION['gameID']	=	$comprehensiveModuleDetailsArr[1];
		$nextPage     = "enrichmentModule.php";
		$mode	=	"comprehensive";
	}
	else if(strtolower($comprehensiveModuleDetailsArr[2])=="diagnostic")
	{
		$_SESSION['diagnosticTest']	=	$comprehensiveModuleDetailsArr[1];
		$quesCategory = "diagnosticTest";
		$nextPage     = "question.php";
	}
	else if(strtolower($comprehensiveModuleDetailsArr[2])=="cluster")
	{
		$_SESSION['clusterCode']	=	$comprehensiveModuleDetailsArr[1];
		$_SESSION['comprehensiveCluster'] = 1;
		$qcode	=	getComphrensiveQcode($_SESSION['clusterCode']);
		$quesCategory = "normal";
		$nextPage     = "question.php";
		$showAnswer = 1;
		if(!isset($_SESSION["qno"]))
		{
			$quesno=1;
			$_SESSION["qno"] = $quesno;
		}
		else
			$quesno = $_SESSION["qno"];
	}
	else
	{
		$_SESSION['comprehensiveModule'] = "";
		$_SESSION['comprehensiveCluster'] = 0;
		$_SESSION['comprehensiveModule_srno'] = "";
	}
	if($_SESSION['comprehensiveModule']!="")
	{
		echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
		echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
		echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
		echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
		echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
		if($comprehensiveModuleDetailsArr[2]=="activity")
			echo '<input type="hidden" name="mode" id="mode" value="comprehensive">';
		else if ($_SESSION['game']==true)
			echo '<input type="hidden" name="mode" id="mode" value="game">';
		echo '</form>';
		echo "<script>
				 document.getElementById('frmHidForm').submit();
			  </script>";
		echo '</body>';
		echo '</html>';
	}
}

function getTestType($srno)
{
	$sq	=	"SELECT testType FROM adepts_diagnosticTestAttempts WHERE srno=$srno AND status=0";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function diagnosticTestQuestions($firstQuestion=0,$ttAttemptID)
{
	$quesAttemptedArray	= array();
	$totalQuestionArray = array();
	$allQuestionArray = array();
	$groupAttemptedArray = array();
	// $testType	=	getTestType($_SESSION['comprehensiveModule_srno']);
	$testType	= $_SESSION['diagnosticTestType'];
	$comprehensiveModule_srno = $_SESSION['comprehensiveModule_srno']; 
	
	$queryCM = "SELECT GROUP_CONCAT(a.srno) as srno from adepts_comprehensiveModuleAttempt a where a.ttAttemptID=$ttAttemptID and a.status=0";		
	$resultCM = mysql_query($queryCM);
	if($lineCM = mysql_fetch_row($resultCM))
	{
		$comprehensiveModule_srno = $lineCM[0];
		$queryDT = "SELECT GROUP_CONCAT(a.diagnosticTestID) from adepts_diagnosticTestAttempts a where a.ttAttemptID=$ttAttemptID and status=0 and srno IN($comprehensiveModule_srno)";			
		$resultDT = mysql_query($queryDT);
		if($lineDT = mysql_fetch_row($resultDT))
		{
			$diagnosticTestID = str_replace(',', "','", $lineDT[0]);
		}
	}			
	
	if($firstQuestion==1)
	{
		$sq	=	"SELECT qcode,groupID FROM adepts_diagnosticQuestionAttempt WHERE attemptID IN($comprehensiveModule_srno)";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$quesAttemptedArray[$rw[0]]	=	$rw[1];
			if(!$groupAttemptedArray[$rw[1]])
				$groupAttemptedArray[$rw[1]]=0;

			//counting questions attempted for each group
			$groupAttemptedArray[$rw[1]]++;	
		}
	}	
	$sql = "SELECT A.groupID, noOfQuestions,A.diagnosticTestID FROM adepts_diagnosticTestGroupCond A, adepts_diagnosticTestQuestions B WHERE A.diagnosticTestID IN('$diagnosticTestID') AND A.groupID=B.groupID AND A.noOfQuestions<>0 GROUP BY A.groupID HAVING COUNT(qcode)<>0";
	
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
		if($groupAttemptedArray[$row[0]])
			$totalQuestionArray[$row[2]][$row[0]] = $row[1] - $groupAttemptedArray[$row[0]];
		else
			$totalQuestionArray[$row[2]][$row[0]] =	$row[1];

		$totalQuestions+=$totalQuestionArray[$row[2]][$row[0]];
		$groupArray[] = $row[0];
	}	
	$groupIDStr = implode(",",$groupArray);
	$sql = "SELECT qcode, dynamic, groupID,diagnosticTestID FROM adepts_diagnosticTestQuestions WHERE groupID IN ($groupIDStr)";
	$result = mysql_query($sql);	
	while($row = mysql_fetch_array($result))
	{
		if($quesAttemptedArray[$row[0]] && $row[1]==0)
			continue;
		if(!is_array($allQuestionArray[$row[2]]))
			$allQuestionArray[$row[2]] = array();
		array_push($allQuestionArray[$row[2]],$row[0]);
		if($row[1] == "1")
		{
			array_push($allQuestionArray[$row[2]],$row[0]);
			array_push($allQuestionArray[$row[2]],$row[0]);
			array_push($allQuestionArray[$row[2]],$row[0]);
			array_push($allQuestionArray[$row[2]],$row[0]);
		}
	}
	$generatedQuestionArray = generateRandomQuestions($totalQuestionArray,$allQuestionArray);	
	$_SESSION["diagnosticTestQuestions"]	=	$generatedQuestionArray;
	$_SESSION["diagnosticTestTotalQuestions"] = $totalQuestions;	
}
function generateRandomQuestions($totalQuestionArray,$allQuestionArray)
{	
	foreach($totalQuestionArray as $diagnosticTestId => $groupIDArray)
	{
		$generatedQuestionArray[$diagnosticTestId] = array();
		foreach($groupIDArray as $groupID=>$maxQuestions)
		{
			if($maxQuestions<1)
				continue;
			$randomKeys = array_rand($allQuestionArray[$groupID],$maxQuestions);
			if(is_array($randomKeys))
			{
				foreach($randomKeys as $key)
				{
					array_push($generatedQuestionArray[$diagnosticTestId],array($allQuestionArray[$groupID][$key],$groupID));
				}
				shuffle($generatedQuestionArray[$diagnosticTestId]);
			}
			else
			{
				array_push($generatedQuestionArray[$diagnosticTestId],array($allQuestionArray[$groupID][$randomKeys],$groupID));
				shuffle($generatedQuestionArray[$diagnosticTestId]);
			}
		}
	}
	return ($generatedQuestionArray);
}
function getDiagnosticQcode($diagnosticTestId)
{
	$diagnosticTestQuestions	=	$_SESSION["diagnosticTestQuestions"];
	$diagnosticTestQuestions	=	array_values($diagnosticTestQuestions[$diagnosticTestId]);
	$qcode	=	$diagnosticTestQuestions[0][0];
	$_SESSION["diagnosticTestQuestions"][$diagnosticTestId]	=	$diagnosticTestQuestions;
	return $qcode;
}

function saveDignosticTest($diagnosticTestID,$attemptID,$userID,$testType,$teacherTopicAttemptID,$teacherTopicCode)
{
	$data	=	array();
	$timeTaken = 0;
	$i=0;
	// $testType	=	getTestType($_SESSION['comprehensiveModule_srno']);
	$testType = $_SESSION['diagnosticTestType'];
	$sq	=	"SELECT qcode,A,S,R,groupID FROM adepts_diagnosticQuestionAttempt 
			 WHERE diagnosticTestID='$diagnosticTestID' AND userID=$userID AND attemptID=$attemptID ";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$data[$i]["groupNo"]	=	getGroupNo($rw[0]);
		$data[$i]["qcode"]	=	$rw[0];
		$data[$i]["result"]	=	$rw[3];
		$data[$i]["userAnswer"]	=	$rw[1];
		$timeTaken	+=	$rw[2];
		$i++;
	}
	$groupVizArr = array();
	$misconceptionsCode = array();
	$misconceptionsGenerated = array();
	$isAECode = true;
	$totalCorrect = 0;
	$totalAttempt = 0;
	$responseSave = array();
	foreach($data as $details)
	{
		if($details["result"] == 0)
			$isAECode = false;
		else
			$totalCorrect++;
		$totalAttempt++;
		$groupNo = $details["groupNo"];
		$qcode = $details["qcode"];
		if(!isset($groupVizArr[$groupNo]))
		{
			$groupVizArr[$groupNo] = array();
			$groupVizArr[$groupNo]["Corrects"] = 0;
			$groupVizArr[$groupNo]["Value"] = array();
		}
		array_push($responseSave,"(".$qcode."-".$details["result"]."-".$details["userAnswer"].")");
		$groupVizArr[$groupNo]["Corrects"] += $details["result"];
		array_push($groupVizArr[$groupNo]["Value"],$details["userAnswer"]);
	}
	$accuracy = round(($totalCorrect*100/$totalAttempt),2);	
	
	$query = "SELECT a.linkToCluster from adepts_diagnosticTestMaster a where a.diagnosticTestID=  '$diagnosticTestID'";
	$result	=	mysql_query($query);
	if($line=mysql_fetch_array($result))
	{
		$clusterCode=$line[0];
		$cluster = getClusterDetails($clusterCode,"",1);
	}
	
	foreach($groupVizArr as $groupNo=>$details)
	{
		$valueArr = $details["Value"];
		if(count($valueArr) == 0)
		{
			$groupVizArr[$groupNo]["Value"] = "";
		}
		else if(count($valueArr) == 1)
		{
			$groupVizArr[$groupNo]["Value"] = $groupVizArr[$groupNo]["Value"][0];
		}
		else
		{
			$count = array_count_values($valueArr);
			arsort($count);
			$groupVizArr[$groupNo]["Value"] = "";
			foreach($count as $val=>$getResp)
			{
				if(round(($getResp/count($valueArr))*100,2) > 50)
					$groupVizArr[$groupNo]["Value"] = $val;
			}
		}
	}
	$sql = "SELECT misconceptionCode, conditionText FROM adepts_diagnosticMisconceptionsCond WHERE diagnosticTestID='$diagnosticTestID'";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
		$evalStr = str_replace("~"," ",$row[1]);
		$evalStr = preg_replace("/Group ([0-9]+) (Value)/i",'$groupVizArr[$1]["Value"]',$evalStr);
		$evalStr = preg_replace("/Group ([0-9]+) (Corrects)/i",'$groupVizArr[$1]["Corrects"]',$evalStr);
		$evalStr = preg_replace("/Group ([0-9]+),/i",'$groupVizArr[$1]["Corrects"] + ',$evalStr);
		$evalStr = preg_replace("/== ([^\s]) /i",'== "$1"',$evalStr);
		$misconceptionsCode[$row[0]] = trim($evalStr);
	}
	foreach($misconceptionsCode as $code=>$condition)
	{
		try
		{
			$bool = false;
			eval('$bool = ('.$condition.');');
			if($bool == true)
				array_push($misconceptionsGenerated,$code);
		}
		catch(Exception $ex)
		{
			echo $ex->getMessage();
			exit();
		}
	}
	$Name = trim($_SESSION['childName']) != ''? ", ".ucfirst(strtolower($_SESSION['childName'])) : '';	
	if($accuracy == 0)
		$message = "You have answered none of the questions correctly$Name.<br>Let’s warm up a bit before we start learning about $cluster.";
	else 
	{
		$message =	"You have answered $totalCorrect out of $totalAttempt questions correctly$Name.<br>";
		if(count($misconceptionsGenerated) > 0)
			$message .= " Let’s warm up a bit before we start learning about $cluster.";
		else
			$message .= " Let us start the topic.";
	}
	
	if($isAECode)
		array_push($misconceptionsGenerated,"AE");	

	if(count($misconceptionsGenerated) == 0)
		array_push($misconceptionsGenerated,"UN");

	$sql	=	"UPDATE adepts_diagnosticTestAttempts SET misconceptionCodes='".mysql_escape_string(implode(",",$misconceptionsGenerated))."',
				 accuracy='".$accuracy."', timeTaken='".$timeTaken."', studentResponse='".implode(",",$responseSave)."', status=1, attemptedDate='".date('Y-m-d h:i:s')."' 
				 WHERE userID=".$_SESSION["userID"]." AND diagnosticTestID='$diagnosticTestID' AND srno=".$_SESSION['comprehensiveModule_srno'];
	if(mysql_query($sql)) { }
	else
	{
		$sqError = "INSERT INTO adepts_errorLogs SET bugType='Diagnostic',bugText='".json_encode($sql).",".$currentSDL."',qcode=".$_SESSION["qcode"].",userID=".$_SESSION['userID'].",sessionID=".$_SESSION['sessionID'].",schoolCode=".$_SESSION['schoolCode'];
		mysql_query($sqError);
	}

	$_SESSION['diagnosticTest'] = "" ;
	unset($_SESSION["diagnosticTestQuestions"][$diagnosticTestID]);
	if($testType=="Prerequisite")	
		return setComprehensiveFlow($misconceptionsGenerated,$_SESSION["userID"],$diagnosticTestID,$testType,$teacherTopicCode,$clusterCode)."~".$message;		
	else
	{		
		setComprehensiveFlow($misconceptionsGenerated,$_SESSION["userID"],$diagnosticTestID,$testType,$teacherTopicCode,$clusterCode);
		$allDiagnosticTests = array_keys($_SESSION["diagnosticTestQuestions"]);	
		updateSessionVariables($allDiagnosticTests[0],$_SESSION['userID'],$teacherTopicAttemptID)	;

		if(count($_SESSION["diagnosticTestQuestions"]) == 0)
			return setComprehensiveFlowAssessment($_SESSION["userID"],$teacherTopicAttemptID,$teacherTopicCode,$clusterCode,$testType);
		else
			return getDiagnosticQcode($_SESSION['diagnosticTest']);		
	}
}
function updateSessionVariables($diagnosticTestId,$userID,$teacherTopicAttemptID)
{
	$_SESSION['diagnosticTest']= $diagnosticTestId;
	$sql = "SELECT a.srno,b.comprehensiveModuleCode from adepts_diagnosticTestAttempts a JOIN adepts_comprehensiveModuleAttempt b ON a.srno=b.srno where a.diagnosticTestID='$diagnosticTestId' and a.userID=$userID and a.ttAttemptID=$teacherTopicAttemptID";
	$result = mysql_query($sql);
	if($line = mysql_fetch_array($result))
	{
		$_SESSION['comprehensiveModule_srno'] = $line[0];
		$_SESSION['comprehensiveModule'] = $line[1];
	}
}
function getGroupNo($qcode)
{
	$sq	=	"SELECT groupNo from adepts_diagnosticTestQuestions A, adepts_groupInstruction B WHERE qcode=$qcode AND A.groupID=B.groupID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function getdignosicScore($diagnosticTestID,$attemptID)
{
	$sq	=	"SELECT count(qcode),SUM(R) FROM adepts_diagnosticQuestionAttempt WHERE diagnosticTestID='$diagnosticTestID' AND attemptID=$attemptID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
}

function checkIfExist($userID,$comprehensiveModule_srno,$flowNo)
{
	$sq	=	"SELECT COUNT(flowAttemptID) as cnt FROM adepts_userComprehensiveFlow WHERE userID=$userID AND srno=$comprehensiveModule_srno AND flowIDAssign=$flowNo";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_assoc($rs);
	if($rw["cnt"]==0)
		return true;
	else
		return false;
}


function redirectToKstFlow()
{
	$comprehensiveModuleDetails	=	checkPendingSubModuleForKst($_SESSION['teacherTopicAttemptID']);
	if($comprehensiveModuleDetails=="")
	{
		return;
	}
	$comprehensiveModuleDetailsArr	=	explode("$",$comprehensiveModuleDetails);
	$_SESSION['subModule']	=	$comprehensiveModuleDetailsArr[0];
	if($comprehensiveModuleDetailsArr[1]=="remedial")
	{
		$nextPage     = "remedialItem.php";
		$quesCategory = "comprehensive";
		$qcode	=	$comprehensiveModuleDetailsArr[0];
	}
	else if(strtolower($comprehensiveModuleDetailsArr[1])=="timedtest")
	{
		$nextPage     = "timedTest.php";
		$_SESSION["quesCategory"] = $quesCategory = "subModuleKst";
		$_SESSION['timedTest']	=	$comprehensiveModuleDetailsArr[0];
	}
	else if(strtolower($comprehensiveModuleDetailsArr[1])=="activity")
	{
		$_SESSION['gameID']	=	$comprehensiveModuleDetailsArr[0];
		$nextPage     = "enrichmentModule.php";
		$mode	=	"comprehensive";
	}
	
	else if(strtolower($comprehensiveModuleDetailsArr[1])=="cluster")
	{
		$_SESSION['clusterCode']	=	$comprehensiveModuleDetailsArr[0];
		$_SESSION['subModuleCluster'] = 1;
		$qcode	=	getKstQcode($_SESSION['clusterCode']);
		$quesCategory = "normal";
		$nextPage     = "question.php";
		$showAnswer = 1;
		if(!isset($_SESSION["qno"]))
		{
			$quesno=1;
			$_SESSION["qno"] = $quesno;
		}
		else
			$quesno = $_SESSION["qno"];
	}
	else
	{
		$_SESSION['subModule'] = "";
		$_SESSION['subModuleCluster'] = 0;
		$_SESSION['subModule_srno'] = "";
	}
	if($_SESSION['subModule']!="")
	{
		echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
		echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
		echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
		echo '<input type="hidden" name="quesCategory" id="quesCategory" value="'.$quesCategory.'">';
		echo '<input type="hidden" name="showAnswer" id="showAnswer" value="'.$showAnswer.'">';
		if($comprehensiveModuleDetailsArr[1]=="activity")
			echo '<input type="hidden" name="mode" id="mode" value="comprehensive">';
		else if ($_SESSION['game']==true)
			echo '<input type="hidden" name="mode" id="mode" value="game">';
		echo '</form>';
		echo "<script>
				 document.getElementById('frmHidForm').submit();
			  </script>";
		echo '</body>';
		echo '</html>';
	}
}

function checkPendingSubModuleForKst($ttAttemptID)
{
	$sq	=	"SELECT currentActivityCode, currentActivityType, currentActivityDetail, currentAttemptID, srno FROM educatio_adepts.kst_ModuleAttempt WHERE ttAttemptID=$ttAttemptID AND status=0 AND userID=".$_SESSION['userID']." limit 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$_SESSION['subModule_srno']	=	$rw[4];
		$query = "update educatio_adepts.kst_userFlowDetails set srno = $rw[4] where flowAttemptID = $rw[3]";
		mysql_query($query);
		$_SESSION["currentFlowID"]	=	$rw[3];
		return $rw[0]."$".$rw[1]."$".$rw[2]."$".$rw[3];
	}
	else
		return "";
}

?>
