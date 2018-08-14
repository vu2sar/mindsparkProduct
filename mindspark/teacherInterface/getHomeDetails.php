<?php
set_time_limit ( 0 );
// include ("header.php");
@include("../userInterface/check1.php");
include("../userInterface/constants.php");
include("../userInterface/classes/clsUser.php");
include ("functions/functions.php");
include ("classes/testTeacherIDs.php");
include_once ("../userInterface/classes/clsTopicProgress.php");
include("../slave_connectivity.php");
require_once 'common-code.php';
require_once 'functions/dashboardFunctions.php';
$endDate = date ( "Y-m-d",strtotime("-1 day") );
$startDate = date ( "Y-m-d" ,strtotime("-7 day"));
$mode = $_POST["mode"];
session_write_close();

switch ($mode){
	case "strength-weakness":
			$finalArray = $finalDataArray = $classMainArray = array();			
			for($i = 0 ; $i < count($classArray) ; $i++){				
				$currenSections = explode(",",$sectionArray[$i]);
				foreach($currenSections as $currenSection){
					$sectionStudentDetails= getStudentDetailsBySection($schoolCode,$classArray[$i], $currenSection);
					$studentsArr = array_column($sectionStudentDetails, 'userID');

					#Begin: Added by Jayanth as fix for mantis id 10249 
					$ctArr = getClustersAttemptedWithinDateRange($studentsArr, $startDate, $endDate, $classArray[$i]);
					$clusterCodeArr = array_keys($ctArr);
					$acu= getAccuracyAndUsageForClusters2($ctArr, $studentsArr, $classArray[$i], $startDate, $endDate, sizeof($studentsArr));
					#End: Added by Jayanth as fix for mantis id 10249 

					$returnKey = array_keys($acu);
					foreach($returnKey as $k){
					$currenSectionToShow = $currenSection != "" ? "-".$currenSection : "";
						$acu[$k]['clssec']= $classArray[$i] . $currenSectionToShow  ;
					}					
					$classMainArray[] = trim($currenSection) != "" ? trim($classArray[$i])."-".trim($currenSection) :  trim($classArray[$i]);											
					$finalArray = array_merge($finalArray, $acu);
				}
			}
			// echo "<pre>";
			// print_r($finalArray);
			if(!empty($finalArray))
			{
				foreach($finalArray as $farray){
					
					if($farray['category'] != 'notEnoughUsage'){						
						if($farray['accuracy'] >= '80'){
							$topArray[] = $farray;
						}
							
						if($farray['accuracy'] <= '40'){		// for the task 12358							
							$botomArray[] = $farray;
						}	
					}
				}
				
				usort($topArray, 'usort_callback');							
				if(!empty($topArray))
				{
					foreach($topArray as $key=>$t)
					{
						$finalDataArray[$t['clssec']]['strength'][$key]['topicName'] = $t['topicName'];
						$finalDataArray[$t['clssec']]['strength'][$key]['topicCode'] =$t['topicCode'];
						$finalDataArray[$t['clssec']]['strength'][$key]['name'] =$t['name'];
					}
				}
				
				usort($botomArray, 'usort_callbac1');				
								
					if(!empty($botomArray))
					{
						foreach($botomArray as $key=>$w)
						{
							$finalDataArray[$w['clssec']]['weakness'][$key]['topicName'] = $w['topicName'];
							$finalDataArray[$w['clssec']]['weakness'][$key]['topicCode'] =$w['topicCode'];
							$finalDataArray[$w['clssec']]['weakness'][$key]['name'] =$w['name'];						
						}

					}
				}					
				foreach($classMainArray as $key=>$classes)
				{									
					if(!in_array($classes,array_keys($finalDataArray)))
					{												
						$finalDataArray[$classes] = array();							
					}					
					
				}	
				if(!empty($finalDataArray))
				{
					$accuracyUsageArray = getAccuracyUsageSectiowise($schoolCode,$startDate, $endDate);

					foreach ($finalDataArray as $key1 => $value1)
					{
						foreach ($accuracyUsageArray as $key2 => $value2) {	

							if(strcmp($key1,$value2['classsection']) == 0)
							{										
								$usage= 0;
								$usage = ROUND($value2['usage']/60);
								if($usage < 60)
								{
									$usageValue = $usage." min(s)";
								}
								else
								{								
									$usage = ROUND($usage/60,2);																
									$usageValue = $usage." hour(s)";																
								}
								
								if($usage == 0)
									$usageCategory = 'Zero';
								else
									$usageCategory = ucfirst(categorizeUsageSectionwise($value2['usage']))." <br/>(".$usageValue.")";

								$finalDataArray[$key1]['classAccuracy'] = ucfirst(categorizeAccuracy($value2['accuracy']))." (".ROUND($value2['accuracy'])."%)";
								$finalDataArray[$key1]['classUsage'] = $usageCategory;
							}
						}
						
					}				
						
					ksort($finalDataArray);				
					$returnContent = '';
					$returnContent .='<table width="95%" class="newHomeTable" border="1" cellpadding="5" cellspacing="0">
						<tr>
						<th width="4%">Class</th>
						<th width="10%" title="Accuracy of all questions attempted in previous 7 days" >Accuracy</th>
						<th width="12%" title="Average time spent per student per week">Usage</th>
						<th width="34%">Strengths</th>
						<th width="34%">Weaknesses</th>
						</tr>';								
					foreach($finalDataArray as $key => $dataArray)
					{					
						$returnContent .= "<tr><td align='center'>";
						$returnContent .= str_replace("-","",$key);
						$returnContent .= "</td><td align='center'>";
						if(!empty($dataArray['classAccuracy']))
						{
							$returnContent .= $dataArray['classAccuracy'] ;
						}
						else
						{
							$returnContent .= 'NA';
						}
						$returnContent .= "</td><td align='center'>";
						if(!empty($dataArray['classUsage']))
						{
							$returnContent .=$dataArray['classUsage'] ;
						}
						else
						{
							$returnContent .= 'NA';
						}
						$returnContent .="</td><td class='strengthTd'>";
						
						if(!empty($dataArray['strength']))
						{
							$strengthArray = array_slice($dataArray['strength'], 0, 3);
							$i = 0;
							foreach($strengthArray as $strength)
							{
								$clsSecToShow = explode("-",$key);
								$returnContent .="<a style='text-decoration:none;' href='topicReport.php?schoolCode=".$schoolCode."&cls=".$clsSecToShow[0]."&sec=".$clsSecToShow[1]."&topics=".$strength['topicCode']."&mode=0&topicName=".rawurlencode($strength['topicName'])."&fromHomePage=1' title='".$strength['topicName']." '>".$strength['name']."</a>";

								$i++;			
								if($i != count($strengthArray))
								$returnContent .=",";

								$returnContent .="<br/>";
							}						
						}	
						else
						{

						 $returnContent .="No data available with accuracy higher than 80%" ;
						}
						$returnContent .="</td><td class='weaknessTd'>";
						if(!empty($dataArray['weakness']))
						{
							$weaknessArray = array_slice($dataArray['weakness'], 0, 3);
							$i = 0;
							
							foreach($weaknessArray as $weakness)
							{				

								$clsSecToShow = explode("-",$key);
								$returnContent .="<a style='text-decoration:none;' href='topicReport.php?schoolCode=".$schoolCode."&cls=".$clsSecToShow[0]."&sec=".$clsSecToShow[1]."&topics=".$weakness['topicCode']."&mode=0&topicName=".rawurlencode($weakness['topicName'])."&fromHomePage=1' title='".$weakness['topicName']." '>".$weakness['name']."</a>";												
								$i++;			
								if($i != count($weaknessArray))
								$returnContent .=",";
								$returnContent .="<br/>";
								
							}
						}
						else
						{
							$returnContent .="No data available with accuracy lower than 40%" ;
						}
						$returnContent .="</td></tr>";
					}

					$returnContent .='</table>';
				}
				else
				{
					$returnContent .= "No data found.";
				}
			echo $returnContent;
		break;
	
	case "alerts" :
		
		function formatMissedSessions($sessionDetails,$classSectionArr)
		{
			$i = $k = 0;
			$arrayMissedSessionInfo["noOfMissed"] = 0;
			if(trim($sessionDetails)!="")
			{
				$missedSessionList = "";
				$sessionDetailsArr = explode("~",$sessionDetails);
				$classWiseSessionArr = json_decode($sessionDetailsArr[1]);
				foreach($classWiseSessionArr as $classes=>$sessions)
				{
					if(in_array($classes, $classSectionArr)){
						$k++;
						if($sessions==0)
						{
							$missedSessionList .= $classes.", ";
							$i++;
						}
					}
						
				}
				if($missedSessionList!="")
				{
					$missedSessionList = substr($missedSessionList,0,-2);
					$arrayMissedSessionInfo["missedSessionsInfo"] = "<ul><li>School missed sessions of class ".$missedSessionList."</ul>";
				}
			}
			else
				$arrayMissedSessionInfo["missedSessionsInfo"] = "";
			if($k>0)
			{
				$arrayMissedSessionInfo["noOfMissed"] = $i."/".$k;
				$arrayMissedSessionInfo["missedSessionPer"] = round(($i/$k)*100);
			}
			else
			{
				$arrayMissedSessionInfo["noOfMissed"] = 0;
				$arrayMissedSessionInfo["missedSessionPer"] = 0;
			}
			return $arrayMissedSessionInfo;
		}
		function formatTopicsActivatedDetails($topicsActivatedDetails,$classSectionArr)
		{
			$arrTopicsActivatedDetails["totalCases"] = 0;
			$arrTopicsActivatedDetails["caseDetails"] = "";
			$arrTopicsActivatedDetails["allClasses"] = "";
			$topicsActivatedClassWise = "";
			$topicsActivatedDetailsArr = explode(",",$topicsActivatedDetails);
			foreach($topicsActivatedDetailsArr as $details)
			{
				$detailsArr = explode("~",$details);
				if(in_array($detailsArr[0], $classSectionArr)){
					if($detailsArr[1]>6)
					{
						$arrTopicsActivatedDetails["totalCases"]++;
						$topicsActivatedClassWise .= "<li>".$detailsArr[1]." Topics activated in class ".$detailsArr[0]."</li>";
						$arrTopicsActivatedDetails["allClasses"] .= $detailsArr[0].", ";
					}
				}
		
			}
			$arrTopicsActivatedDetails["allClasses"] = substr($arrTopicsActivatedDetails["allClasses"],0,-2);
			if($topicsActivatedClassWise!="")
				$arrTopicsActivatedDetails["caseDetails"] = "<ul>".$topicsActivatedClassWise."</ul>";
			else
				$arrTopicsActivatedDetails["caseDetails"] = "";
			return $arrTopicsActivatedDetails;
		}
		function formatTopicProgressDetails($topicProgressDetails,$classSectionArr)
		{
			$arrTopicProgressDetailsClassWise["totalCases"] = 0;
			$arrTopicProgressDetailsClassWise["caseDetails"] = "";
			$topicProgressDetailsClassWise = "";
			$topicProgressDetailsArr = explode(",",$topicProgressDetails);
			$totalTopicGreenRegion = 0;
			$totalTopicGreenRegionPassed = 0;
			foreach($topicProgressDetailsArr as $details)
			{
				$detailsArr = explode("(",$details);
				$classAvg = explode("@",$detailsArr[0]);
				$cls = $classAvg[0];
				if(in_array($cls, $classSectionArr)){
					$avgProgress = $classAvg[1];
					$detailsArr[1] = str_replace(")","",$detailsArr[1]);
					$detailsNewArr = explode("#",$detailsArr[1]);
					$str = "";
					foreach($detailsNewArr as $detailsNew)
					{
						$detailsNewProgressArr = explode("@",$detailsNew);
						if($detailsNewProgressArr[0]!="" && $detailsNewProgressArr[1]<40 && $detailsNewProgressArr[2]>=40)
						{
							$arrTopicProgressDetailsClassWise["totalCases"]++;
							$str .= getTeacherTopicName($detailsNewProgressArr[0])." - ".round($detailsNewProgressArr[1],1)."%, ";
						}
						if($detailsNewProgressArr[2]>=15)
						{
							$totalTopicGreenRegion++;
							if($detailsNewProgressArr[1]>=75)
								$totalTopicGreenRegionPassed++;
						}
					}
					if($str!="")
					{
						$str = substr($str,0,-2);
						$topicProgressDetailsClassWise .= "<li>Class ".$cls." has low topic progress in ".$str."</li>";
					}
				}
		
			}
			if($topicProgressDetailsClassWise!="")
				$arrTopicProgressDetailsClassWise["caseDetails"] = "<ul>".$topicProgressDetailsClassWise."</ul>";
			else
				$arrTopicProgressDetailsClassWise["caseDetails"] = "";
			if($totalTopicGreenRegion>0)
				$arrTopicProgressDetailsClassWise["totalProgressPer"] = round(($totalTopicGreenRegionPassed/$totalTopicGreenRegion)*100,2);
			else
				$arrTopicProgressDetailsClassWise["totalProgressPer"] = 0;
			return $arrTopicProgressDetailsClassWise;
		}
		function formatTopicClassDifferenceDetails($topicClassDifferenceDetails,$classSectionArr)
		{
			$arrTopicClassDifference["totalCases"] = 0;
			$arrTopicClassDifference["caseDetails"] = "";
			$topicClassDifferenceDetailsClassWise = "";
			$topicClassDifferenceDetailsArr = explode(",",$topicClassDifferenceDetails);
			foreach($topicClassDifferenceDetailsArr as $details)
			{
				$detailsArr = explode("(",$details);
				$cls = $detailsArr[0];
				if(in_array($cls, $classSectionArr)){
				$detailsNewArr = explode("~",$detailsArr[1]);
				foreach($detailsNewArr as $detailsNew)
				{
					$detailsNew = str_ireplace(")","",$detailsNew);
					$detailsAll = explode("@",$detailsNew);
					if(trim($detailsAll[0])!="")
					{
						$arrTopicClassDifference["totalCases"]++;
						if($detailsAll[1] != $detailsAll[2])
								$topicClassDifferenceDetailsClassWise .= "<li>Topic <b>".getTeacherTopicName($detailsAll[0])."</b> of Class ".$detailsAll[1]."-".$detailsAll[2]." is activated in ".$cls." class </li>";
						else
							$topicClassDifferenceDetailsClassWise .= "<li>Topic <b>".getTeacherTopicName($detailsAll[0])."</b> of Class ".$detailsAll[1]." is activated in ".$cls." class </li>";
					}
				}
			}
		
			}
			if($topicClassDifferenceDetailsClassWise!="")
				$arrTopicClassDifference["caseDetails"] = "<ul>".$topicClassDifferenceDetailsClassWise."</ul>";
			else
				$arrTopicClassDifference["caseDetails"] = "";
			return $arrTopicClassDifference;
		}
		function formatTeacherUsageDetails($teacherUsageDetails)
		{
			$arrTopicClassDifference["totalCases"] = 0;
			$arrTopicClassDifference["caseDetails"] = "";
			$teacherNameString = "";
			$totalTeachers = 0;
			$totalUsage = 0;
			if($teacherUsageDetails!="")
			{
				$teacherUsageDetailsArr = explode(",",$teacherUsageDetails);
				foreach($teacherUsageDetailsArr as $details)
				{
					$teacherDetailsArr = 	explode("~",$details);
					$totalTeachers++;
					$totalUsage += $teacherDetailsArr[2];
					if($teacherDetailsArr[2] < 300 )
					{
						$sq = "SELECT GROUP_CONCAT(CONCAT(class,'',section)) FROM adepts_teacherClassMapping WHERE userID=".$teacherDetailsArr[0];
						$rs = mysql_query($sq);
						if($rw = mysql_fetch_array($rs))
						{
							$arrTopicClassDifference["totalCases"]++;
							if($rw[0]!="")
								$teacherNameString .= "<li>Low teacher usage of <b>".$teacherDetailsArr[1]."</b> of class ".$rw[0]."</li>";
							else
								$teacherNameString .= "<li>Low teacher usage of <b>".$teacherDetailsArr[1]."</b></li>";
						}
					}
				}
				if($teacherNameString!="")
				{
					$arrTopicClassDifference["caseDetails"] = "<ul>".str_replace(",",", ",substr($teacherNameString,0,-2))."</ul>";
				}
			}
			else
				$arrTopicClassDifference["caseDetails"] = "";
			$arrTopicClassDifference["teacherUsagePer"] = round(($totalUsage/$totalTeachers)*100);
			return $arrTopicClassDifference;
		}
		
		function getInteractionHistory($schoolCode,$row_count=20) {
			//sales reporting
			$count=0;
		
			$sales_query="SELECT schoolCode, calledBy, calledDate, contactPerson, nextCalldate, interaction FROM educatio_educat.msiInteractions
			WHERE schoolCode='$schoolCode' ORDER BY calledDate DESC LIMIT $row_count";
			$sales_result=mysql_query($sales_query) or die("getPastHistory Telecalling_query".mysql_error());
			while($sales_row=mysql_fetch_assoc($sales_result)) {
		
				$tempArray[$count]['visit_date']=$sales_row['calledDate'];
				$tempArray[$count]['visited_by']=$sales_row['calledBy'];
				$tempArray[$count]['year'] = 2014;
				$tempArray[$count]['contact_person']=$sales_row['contactPerson'];
				$tempArray[$count]['medium']='';
				$tempArray[$count]['interaction']=$sales_row['interaction'];
				$tempArray[$count]['system']='Telecalling';
				$count++;
			}
		
			$sales_query="SELECT * FROM educatio_educat.sales_visits WHERE schoolCode='$schoolCode' AND product='mindspark' AND medium='call' ORDER BY visit_date DESC LIMIT $row_count";
			$sales_result=mysql_query($sales_query) or die("getPastHistory sales_query".mysql_error());
			while($sales_row=mysql_fetch_array($sales_result)) {
		
				$tempArray[$count]['visit_date']=$sales_row['visit_date'];
				$tempArray[$count]['visited_by']=$sales_row['visited_by'];
				$tempArray[$count]['year'] = $sales_row['year'];
				$tempArray[$count]['contact_person']=$sales_row['contact_person'];
				$tempArray[$count]['medium']=$sales_row['medium'];
				$tempArray[$count]['interaction']=$sales_row['interaction'];
				$tempArray[$count]['system']='Sales Reporting';
				$count++;
			}
		
			//telecalling
			$tele_query="SELECT * FROM educatio_educat.telecalling_school_details WHERE schoolno='$schoolCode' ORDER BY entry_date DESC LIMIT $row_count";
			$tele_result=mysql_query($tele_query) or die("getPastHistory tele_query".mysql_error());
			while($tele_row=mysql_fetch_array($tele_result)) {
		
				$visitDate=explode(" ",$tele_row[entry_date]);
				$tempArray[$count]['visit_date']=$visitDate[0];
				$tempArray[$count]['visited_by']=$tele_row[telecaller];
				$tempArray[$count]['year']=$tele_row[year];
				$tempArray[$count]['product']='';
				$tempArray[$count]['contact_person']=$tele_row[contacted];
				$tempArray[$count]['medium']='Call';
				$tempArray[$count]['interaction']=$tele_row[comments];
				$tempArray[$count]['activity']='';
				$tempArray[$count]['next_action']='';
				$tempArray[$count]['concern']='';
				$tempArray[$count]['system']='Telecalling';
				$count++;
			}
		
			//school interaction
			$school_query="SELECT * FROM educatio_educat.school_interactions WHERE school_code='$schoolCode' ORDER BY interaction_date  DESC LIMIT $row_count ";
			$school_result=mysql_query($school_query) or die("getPastHistory school_query".mysql_error());
			while($school_row=mysql_fetch_array($school_result)) {
		
				$tempArray[$count]['visit_date']=$school_row[interaction_date];
				$tempArray[$count]['visited_by']=$school_row[userID];
				$product='';
				if($school_row[purpose_asset]==1) $product.='ASSET,';  if($school_row[purpose_mindspark]==1) $product.='MS,'; if($school_row[purpose_cce]==1) $product.='CCE';
				$tempArray[$count]['product']=substr($product,0,-1);
				$tempArray[$count]['medium']=$school_row['contact_medium'];
				$tempArray[$count]['interaction']=$school_row['interaction_desc'];
				$tempArray[$count]['activity']='';
				$tempArray[$count]['next_action']=$nextAction['next_action'];
				$tempArray[$count]['next_action_date']=$nextAction['next_action_date'];
				$tempArray[$count]['stage']='';
				$tempArray[$count]['concern']='';
				$tempArray[$count]['buying_temperature']='';
				$tempArray[$count]['exit_reason']='';
				$tempArray[$count]['system']='School Interaction';
		
				$count++;
			}
		
			//new telecalling
			$tele_query="SELECT * FROM educatio_educat.telecalling_interactions a LEFT JOIN educatio_educat.telecalling_interactions_product b ON a.id=b.interaction_id WHERE schoolCode='$schoolCode' ORDER BY date DESC LIMIT $row_count";
			$tele_result=mysql_query($tele_query) or die("getPastHistory tele_query".mysql_error());
			while($tele_row=mysql_fetch_array($tele_result)) {
		
				$visitDate=explode(" ",$tele_row[date]);
				$tempArray[$count]['visit_date']=$visitDate[0];
				$tempArray[$count]['visited_by']=$tele_row[telecaller];
				$tempArray[$count]['product']=$tele_row[product];
				$tempArray[$count]['contact_person']=$tele_row[contact_person];
				$tempArray[$count]['medium']=$tele_row[medium];
				$tempArray[$count]['interaction']=$callResponse[$tele_row[call_response]]."<br>".$tele_row[interaction];
				$tempArray[$count]['activity']='';
				$next_action_date=explode(" ",$tele_row[next_action_date]);
				$tempArray[$count]['concern']='';
				$tempArray[$count]['system']='Telecalling';
				$count++;
			}
		
			for ($i=0;$i<$count;$i++) {
				$dateArray[$i]=$tempArray[$i][visit_date];
			}
		
			if(sizeof($dateArray)>0) {
				arsort($dateArray);
				$j=0;
				foreach ($dateArray as $key => $val) {
					$indexArray[$j]=$key;
					$j++;
					if($j==$row_count) break;
				}
			}
		
			for ($i=0;$i<sizeof($indexArray);$i++) {
				$historyArray[$i]['visit_date']=$tempArray[$indexArray[$i]]['visit_date'];
				$historyArray[$i]['visited_by']=$tempArray[$indexArray[$i]]['visited_by'];
				$historyArray[$i]['year']=$tempArray[$indexArray[$i]]['year'];
				$historyArray[$i]['product']=$tempArray[$indexArray[$i]]['product'];
				$historyArray[$i]['contact_person']=$tempArray[$indexArray[$i]]['contact_person'];
				$historyArray[$i]['medium']=$tempArray[$indexArray[$i]]['medium'];
				$historyArray[$i]['interaction']=$tempArray[$indexArray[$i]]['interaction'];
				$historyArray[$i]['activity']=$tempArray[$indexArray[$i]]['activity'];
				$historyArray[$i]['next_action']=$tempArray[$indexArray[$i]]['next_action'];
				$historyArray[$i]['next_action_date']=$tempArray[$indexArray[$i]]['next_action_date'];
				$historyArray[$i]['stage']=$tempArray[$indexArray[$i]]['stage'];
				$historyArray[$i]['concern']=$tempArray[$indexArray[$i]]['concern'];
				$historyArray[$i]['buying_temperature']=$tempArray[$indexArray[$i]]['buying_temperature'];
				$historyArray[$i]['exit_reason']=$tempArray[$indexArray[$i]]['exit_reason'];
				$historyArray[$i]['system']=$tempArray[$indexArray[$i]]['system'];
			}
		
			return $historyArray;
		}
		function getTeacherTopicName($ttCode)
		{
			$sq = "SELECT teacherTopicDesc FROM educatio_adepts.adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
			$rs = mysql_query($sq);
			$rw = mysql_fetch_assoc($rs);
			return $rw["teacherTopicDesc"];
		}
		$sqno=0;
 		//$link1 = mysql_connect("122.248.246.221","ms_analysis","sl@vedb@e!") or die("Could not connect 1: " . mysql_error());
 		//mysql_select_db("educatio_educat") or die("Could not select database");
		$alertsSQL = "select * from educatio_educat.msiAlerts where schoolCode = $schoolCode order by checkDateInt desc limit 1";
		$rs = mysql_query($alertsSQL) or die(mysql_error().$alertsSQL);
		while($rw = mysql_fetch_array($rs)) {
			$sqno++;
			$pointsBreakup = explode("|",$rw["pointsBreakup"]);
			$sessionDetailsArr = formatMissedSessions($rw["sessionMissed"],$classSectionArr);
			$sessionDetails = $sessionDetailsArr["missedSessionsInfo"];
			$topicsActivatedDetailsArr = formatTopicsActivatedDetails($rw["topicsActivated"],$classSectionArr);
			$topicsActivatedDetails = $topicsActivatedDetailsArr["caseDetails"];
			$topicProgressDetailsArr = formatTopicProgressDetails($rw["topicProgress"],$classSectionArr);
			$topicProgressDetails = $topicProgressDetailsArr["caseDetails"];
			$topicClassDifferenceDetailsArr = formatTopicClassDifferenceDetails($rw["topicClassDifference"],$classSectionArr);
			$topicClassDifferenceDetails = $topicClassDifferenceDetailsArr["caseDetails"];
			$teacherUsageDetailsArr = formatTeacherUsageDetails($rw["teacherUsage"],$classSectionArr);
			$teacherUsageDetails = $teacherUsageDetailsArr["caseDetails"];
			$magerIssuesDescArr = array();
			if(trim($sessionDetails)!="")
					$magerIssuesDescArr[$pointsBreakup[0]]["MS"] = "<h3>Missed Sessions (".$sessionDetailsArr["noOfMissed"].")</h3><div><p>".$sessionDetails."</p></div>";
				if(trim($topicsActivatedDetails)!="")
					$magerIssuesDescArr[$pointsBreakup[1]]["TMAT"] = "<h3>Too Many Active Topics</h3><div><p>".$topicsActivatedDetails."</p></div>";
				if(trim($topicProgressDetails)!="")
					$magerIssuesDescArr[$pointsBreakup[2]]["LTP"] = "<h3>Low Topic Progress</h3><div><p>".$topicProgressDetails."</p></div>";
				if(trim($topicClassDifferenceDetails)!="")
					$magerIssuesDescArr[0]["OGA"] = "<h3>Other grades activation</h3><div><p>".$topicClassDifferenceDetails."</p></div>";
				if(trim($teacherUsageDetails)!="" && $user->category == "School Admin")
					$magerIssuesDescArr[$pointsBreakup[3]]["LTU"] = "<h3>Low Teacher Usage</h3><div><p>".$teacherUsageDetails."</p></div>";
		krsort($magerIssuesDescArr);
			foreach($magerIssuesDescArr as $points=>$magerIssuesD) { foreach($magerIssuesD as $val) { echo $val; } }
		}
		if(isset($_SESSION["logincount"]) && $_SESSION["logincount"] !=''){
			if($_SESSION["logincount"] == 1){
				$grammerToAdd = "student has not ";
			}else{
				$grammerToAdd = "students have not ";
			}
			echo "<h3><a href='alertTeacher.php'> ".$_SESSION['logincount']."</a> ".$grammerToAdd."logged in from past 7 days.</h3>";
		}
 		//mysql_close($link1);
		break;
	

	case "class-data":
		$accuracyUsageArray = getAccuracyUsageSectiowise($schoolCode,$startDate, $endDate);
	
		foreach($accuracyUsageArray as $k => $a)
		{
			$overallAccuracySummary[$k]['classAvg'] = categorizeAccuracy($a['accuracy']);
	 		$overallAccuracySummary[$k]['accuracy'] = $a['accuracy'];
			$overallAccuracySummary[$k]['classsection'] = $a['classsection'];
		}		
	 	uasort($overallAccuracySummary, "sortByAccuracyHelper");		 	
		function usort_greataccuracy($a, $b)
		{
			if ( $a['accuracy'] == $b['accuracy'] )
				return 0;
		
			return ( $a['accuracy'] > $b['accuracy'] ) ? -1 : 1;
		}
		function usort_lowaccuracy($a, $b)
		{
			if ( $a['accuracy'] == $b['accuracy'] )
				return 0;
		
			return ( $a['accuracy'] > $b['accuracy'] ) ? 1 : -1;
		}
		$lowAccuracy = Array();
		$greatAccuracy = Array();

		foreach($overallAccuracySummary as $summary){
			if($summary['classAvg'] == 'low'){
				$lowAccuracy[] = $summary;
			}
			if($summary['classAvg'] == 'great'){
				$greatAccuracy[] = $summary;
			}
		}			
		usort($greatAccuracy, usort_greataccuracy);		
		usort($lowAccuracy, usort_lowaccuracy);		
		$goodAccuracyArray = array_slice($greatAccuracy,0,3);		
		$lowAccuracyArray = array_slice($lowAccuracy ,0,3);
		$returnContent = "";
		
		$returnContent .= '<div class="container-left">
			<label>Classes with good accuracy: </label>
			<ol>';
			
		if(!empty($goodAccuracyArray)){
			foreach($goodAccuracyArray as $top){
				$clssectoshowhere = explode("-",$top['classsection']);
				if($top['classAvg'] == 'great'){
					$returnContent .= "<a href='javascript:void(0)' onclick='generateReport($schoolCode,$clssectoshowhere[0],\"$clssectoshowhere[1]\")'><li>".$top['classsection']."</a></li>";					
				}
			}
		}
		else{
			$returnContent .= "No classes with accuracy higher than 80%.";
		}
		
		$returnContent .='		</ol>
		</div>
		<div class="container-right">
			<label>Classes with low accuracy: </label>
			<ol>';
		if(!empty($lowAccuracyArray)){
			foreach($lowAccuracyArray as $bottom){
				$clssectoshowhere = explode("-",$bottom['classsection']);
				if($bottom['classAvg'] == 'low'){
					$returnContent.= "<li><a href='javascript:void(0)' onclick='generateReport($schoolCode,$clssectoshowhere[0],\"$clssectoshowhere[1]\")'>".$bottom['classsection']."</a></li>";
				}
			}
		}
		else{
			$returnContent.= "No classes with accuracy lower than 40%.";	// for the task 12358
		}
		$returnContent .='		</ol>	</div>';
		foreach($accuracyUsageArray as $k => $b)
		{			
			$overallUsageSummary[$k]['classAvg'] = categorizeUsageSectionwise($b['usage']);
			 $overallUsageSummary[$k]['usage'] = $b['usage'];			 
			$overallUsageSummary[$k]['classsection'] = $b['classsection'];
		}	
		
		uasort($overallUsageSummary, "sortByUsageHelper");	
			 
		$zeroArray = array();
		$lowArray = array();
		$goodArray = array();
		$greatArray = array();		
		
		foreach($overallUsageSummary as $summary){
			if($summary['classAvg'] == "zero"){
				$zeroArray[] = $summary;
			}
			if($summary['classAvg'] == "low"){
				$lowArray[] = $summary;
			}
			if($summary['classAvg'] == "good"){
				$goodArray[] = $summary;
			}
			if($summary['classAvg'] == "great"){
				$greatArray[] = $summary;
			}
		}
		
		function usort_greatusageGreatGood($a, $b)
		{
			if ( $a['usage'] == $b['usage'] )
				return 0;
		
			return ( $a['usage'] > $b['usage'] ) ? -1 : 1;
		}
		
		function usort_lowaccuracyLowZero($a, $b)
		{
			if ( $a['usage'] == $b['usage'] )
				return 0;
		
			return ( $a['usage'] > $b['usage'] ) ? 1 : -1;
		}
				
		usort($greatArray, usort_greatusageGreatGood);
		usort($goodArray, usort_greatusageGreatGood);
		usort($zeroArray, usort_lowaccuracyLowZero);
		usort($lowArray, usort_lowaccuracyLowZero);

		$arrArraysForlowUsage = array_merge($zeroArray,$lowArray);
		
		$arrArraysForgoodUsage = array_merge($greatArray,$goodArray);		
		foreach ($arrArraysForgoodUsage as $arrArraysForgood){
				$arrArraysForgoodUsageToShow[] = $arrArraysForgood;
		}
		
		foreach ($arrArraysForlowUsage as $arrArraysForlow){
				$arrArraysForlowToShow[] = $arrArraysForlow;
		}
		
		$arrArraysForlowUsage = array_slice($arrArraysForlowToShow, 0,3);
		$arrArraysForgoodUsage = array_slice($arrArraysForgoodUsageToShow, 0, 3);		
		$returnContent .= '<div class="container-left">
			<label>Classes with good usage: </label>
			<ol>';			
				if(!empty($arrArraysForgoodUsage)){
					foreach($arrArraysForgoodUsage as $top){
						$clssectoshowhere = explode("-",$top['classsection']);
						$returnContent .= "<li><a href='javascript:void(0)' onclick='generateReport($schoolCode,$clssectoshowhere[0],\"$clssectoshowhere[1]\")'>".$top['classsection']."</a></li>";
					}	
				}
				else{
					$returnContent .= "No class has done Mindspark for more than 1 hour.";
				}
				
				$returnContent .='		</ol>
		</div>
		<div class="container-right">
			<label>Classes with low usage: </label>
			<ol>';			
				if(!empty($arrArraysForlowUsage)){
					foreach($arrArraysForlowUsage as $bottom){
						$clssectoshowhere = explode("-",$bottom['classsection']);
						$returnContent.= "<li><a href='javascript:void(0)' onclick='generateReport($schoolCode,$clssectoshowhere[0],\"$clssectoshowhere[1]\")'>".$bottom['classsection']."</a></li>";
					}	
				}
				else{
					$returnContent.= "No class has done Mindspark for less than 30 minutes.";
				}
				$returnContent .='		</ol>	</div>';
		
		echo $returnContent;
		break;
	case "mindsparkinnumbers" :
		$totalStudents =0;
		$totalQuestionsAttemptedClassWise=0;
		for($i = 0 ; $i < count($classArray) ; $i++){
			$currenSections = explode(",",$sectionArray[$i]);
			foreach($currenSections as $currenSection){
				$sectionStudentDetails= getStudentDetailsBySection($schoolCode,$classArray[$i], $currenSection);
				$studentsArr = array_column($sectionStudentDetails, 'userID');
				$totalQuestionsAttempted = getTotalQuestionsAttemptedByClass($studentsArr, $startDate, $endDate);
				$numOfStudents = count($studentsArr);
				$totalStudents += $numOfStudents ;
				$totalQuestionsAttemptedClassWise += $totalQuestionsAttempted;
// 				$classAttemptAverage = ($numOfStudents > 0)? round($totalQuestionsAttempted/$numOfStudents) : "NA";
				$impactSummaryDetails['questions'] = array("total" => $totalQuestionsAttempted, "average" => $classAttemptAverage);
				$impactSummaryDetails['remedials'] = getTotalRemedialsClearedByClass($studentsArr, $startDate, $endDate);
				$impactSummaryDetails['activities'] = getTotalActivitiesAttemptedByClass($studentsArr, $startDate, $endDate);
				$higherLevelArray = getTopicProgressSummaryDetails ($schoolCode, $classArray[$i], $currenSection, $startDate, $endDate) ;
				$impactSummaryDetails['higherLevel']=$higherLevelArray ['totalHigherLevelReached'];
				$mindsparkInNumbersArray[] = $impactSummaryDetails;
			}
			
		}
		$questions = 0;
		$average = 0;
		$remedials = 0;
		$activities = 0;
		$higherLevel = 0;
// 		echo $totalStudents.' / '.$totalQuestionsAttemptedClassWise;
		$average = round($totalQuestionsAttemptedClassWise/$totalStudents);
		foreach($mindsparkInNumbersArray as $mindsparkInNumbers){
			$questions +=  $mindsparkInNumbers['questions']['total'];
// 			$average +=  $mindsparkInNumbers['questions']['average'];
			$remedials += $mindsparkInNumbers['remedials'];
			$activities += $mindsparkInNumbers['activities'];
			$higherLevel += $mindsparkInNumbers['higherLevel'];
		}
		$mnumbersContent = '<li class="li-1">
									<div class="li-left">
										<img width="65" height="65" src="images/q.svg">
										<h3>Questions</h3>
									</div>
									<div class="li-right">
									  Total: '.$questions.' <br />
									  Average:'.$average.'
									</div>
					            </li>';
			
		$mnumbersContent .= '<li class="li-2">
									<div class="li-left">
										<img width="65" height="65" src="images/favorite5.svg">
										<h3>Higher Level reached</h3>
									</div>
									<div class="li-right">
									  Total: '.$higherLevel.' <br />
									</div>
					            </li>';
		$mnumbersContent .= '<li class="li-3">
									<div class="li-left">
										<img width="65" height="65" src="images/thought.svg">
										<h3>Activities attempted</h3>
									</div>
									<div class="li-right">
									  Total: '.$activities.' <br />
									</div>
					            </li>';
		if($remedials != 0){
			$mnumbersContent .= '<li class="li-4">
									<div class="li-left">
										<img width="65" height="65" src="images/flag4.svg">
										<h3>Misconceptions remediated</h3>
									</div>
									<div class="li-right">
									  Total: '.$remedials.' <br />
									</div>
					            </li>';
		}
		echo $mnumbersContent;
		break;
	
}
