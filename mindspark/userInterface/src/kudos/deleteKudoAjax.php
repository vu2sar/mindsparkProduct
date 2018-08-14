<?php

include_once("../../check1.php");
// include("../../constants.php");
// include("../../classes/clsUser.php");
include('common_functions.php');
// include("../../header.php");

	$kudos_id = $_POST['kudos_id'];
	$myWall = $_POST['myWall'];
	$schoolCode = $_POST['schoolCode'];
	$childClass = $_POST['childClass'];
	$childSection = $_POST['childSection'];
	$userName = $_POST['userName'];
	$category = $_POST['category'];
	$userCategory = $_POST['userCategory'];
	$filterKudoPageFlag = $_POST['filterKudoPageFlag'];
	$result = deleteKudo($kudos_id);
	$html = '';
	if($result)
	{
		if($filterKudoPageFlag)
		{
			$query= "select kudo_id, sender, receiver, sent_date, message, kudo_type from kudosMaster where receiver in (select username from adepts_userDetails where schoolCode='". $schoolCode ."' and category='".$userCategory."' ";
		    if($userCategory=='student')
		    { 
		    	if($childSection==NULL||$childSection==''||$childSection==' ')
		    	{$query.="AND childClass=".$childClass;}
		     	else
		    	{$query.="AND childClass=".$childClass." AND childSection ='".$childSection."' "; }

		    }
		    $query.=") order by sent_date desc;";
		    $result = mysql_query($query) or die('<table id="TblKudos" align="center" width="95%" border="0"><tr><td><font style="font-size:24px;">Please Select a Class from the dropdown menu!</font></td></tr></table> ');
		    while($row = mysql_fetch_array($result))
		    {
		    	$arrKudos[$row['kudo_id']] = array('sender' => $row['sender'],
		    								'receiver' => $row['receiver'],
		    								'sent_date' => $row['sent_date'],
		    								'kudo_type' => $row['kudo_type'],
		    								'message' => $row['message']);
		    }
		}
		else
		{
			$arrKudos = getAllKudos($myWall, $schoolCode, $childClass,$childSection, $userName , $category);
		}
		if(is_array($arrKudos) && count($arrKudos)>0)
            {
                $onGoingMonth = 0;
                $onGoingYear = 0;
                $i = 0;
                $monthCnt = 0;
                foreach($arrKudos as $kudo_id=>$kudo_details)
                { 
                    $month = date('m', strtotime($kudo_details['sent_date']));                  
                    $year = date('Y', strtotime($kudo_details['sent_date']));
                    $gender = getGender($kudo_details['receiver']); 
                    $type = $kudo_details['kudo_type']; 
                    $message = $kudo_details['message'];
                    $message = preg_replace("/[\r\n]+/", " ", $message);
                    $imageSrc = preg_replace('/\s+/', '', $type);
                    $imageSrc = strtolower($imageSrc);
                    if($gender=='Boy'||$gender=='Girl'){ $genderToShow=$gender;} else {$genderToShow='noGender';}
					//echo "onGoingMonth - ".$onGoingMonth." onGoingYear - ".$onGoingYear."<br>";
                    if($onGoingMonth != $month || $onGoingYear != $year)    
                    {
                    	//echo "inside if <br>";
                        $onGoingMonth = $month;
                        $monthCnt++;
                        $onGoingYear = $year;
                        $i = 0;
                      if(!$filterKudoPageFlag)
                      {
						$html .= "<table class=\"kudoMonthYearTbl\"><tr>";	
						$html .= "<td class=\"kudoMonthYearTd\"><hr></td><td class=\"kudoMonthYearTd\">- - - -  ".date("F, Y", strtotime("01-$month-$year"))."  - - - -</td><td class=\"kudoMonthYearTd\"><hr></td>";
						$html .= "</table>";                      	
                      }
                    }       
                        $html .= "<div id=\"kudosTd-$kudo_id\" class=\"kudosTd\">";
                           $html .= "<table border=\"0\" width=\"100%\" id=\"KudoSummary\" class=\"KudoSummary\">";
                               $html .= "<tr>";
                                   $html .= "<td id=\"Figurine\">";
                                       $html .= "<img src=\"../userInterface/src/kudos/images/".$genderToShow.".png\" title=\"".fetchFullName($kudo_details['receiver'])."\" height=\"50px\" width=\"50px\"/>";
                                   $html .= "</td>";
                                   $html .= "<td class=\"kudoTitle\">";
                                       $html .= "<span style=\"color:black;font-weight:bold;\">".fetchFullName($kudo_details['receiver'])." received ".$type." from ".fetchFullName($kudo_details['sender'])."</span> <br/>";
                                  	   $html .= "<span style=\"color:#4D4D4D;\">".date('d F, Y', strtotime($kudo_details['sent_date']))."<span>";
                                   $html .= "</td>";
                                   $html .= "<td style=\"text-align: right;vertical-align:top;\">";
                                       $html .= "<img  src=\"../userInterface/src/kudos/images/".$imageSrc.".png\" height=\"100px\" width=\"100px\">";
                                   $html .= "</td>";
                                   if(($userName == $kudo_details['sender'] && strcasecmp($category, "teacher") == 0) || strcasecmp($category, "School Admin") == 0)
                                   {
                                     $html .= "<td class=\"deleteKudoTd\" style=\"text-align:right;vertical-align:top;width:16px;\">";
                                      	$html .= "<span class=\"deleteKudo\" onclick=\"deleteThisKudo(".$kudo_id.")\"></span>";
                                     		$html .= "<input type=\"hidden\" name=\"userCategory\" id=\"userCategory\" value=\"".$userCategory."\"/>";
                                     		$html .= "<input type=\"hidden\" name=\"childClass\" id=\"childClass\" value=\"".$childClass."\"/>";
                                     		$html .= "<input type=\"hidden\" name=\"childSection\" id=\"childSection\" value=\"".$childSection."\"/>";
                                     $html .= "</td>";
                                    }
                               $html .= "</tr>";
                               $html .= "<tr>";
                               	   $html .= "<td colspan=\"4\" class=\"message\">";
                               		   $html .= $message;
                               	   $html .= "</td>";                                   
                               $html .= "</tr>";
                            $html .= "</table>";
                       $html .= "</div>";
                }
            }
        echo $html;
	}
	else
		echo 0;
    
?>