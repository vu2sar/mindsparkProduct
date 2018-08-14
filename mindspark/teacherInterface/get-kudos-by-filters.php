<?php

include_once("../userInterface/check1.php");
include('../userInterface/src/kudos/common_functions.php');

$data = json_decode(file_get_contents('php://input'));
//print_r(file_get_contents('php://input'));

$category =$data->{'category'};
$class =$data->{'class'};
$section = $data->{'section'};
$schoolCode = $data->{'schoolCode'};
$userCategory = $data->{'userCategory'};
$userName = $data->{'userName'};

    $query= "select kudo_id, sender, receiver, sent_date, message, kudo_type from kudosMaster where receiver in (select username from adepts_userDetails where schoolCode='". $schoolCode ."' and category='".$category."' ";
    if($category=='student')
    { 
    	if($section==NULL||$section==''||$section==' ')
    	{$query.="AND childClass=".$class;}
     	else
    	{$query.="AND childClass=".$class." AND childSection ='".$section."' "; }

    }
    $query.=") order by sent_date desc;";
    
    $result = mysql_query($query) or die('<table id="TblKudos" align="center" width="95%" border="0"><tr><td><font style="font-size:24px;">Please Select a Class from the dropdown menu!</font></td></tr></table> ');
    while($row = mysql_fetch_array($result))
    {
    	$arrRet[$row['kudo_id']] = array('sender' => $row['sender'],
    								'receiver' => $row['receiver'],
    								'sent_date' => $row['sent_date'],
    								'kudo_type' => $row['kudo_type'],
    								'message' => $row['message']);
    }

    //print_r($arrRet);

    $html = '';
    if(count($arrRet)==0)
    	{ 
    		if($section=='section'){ $html .='<div class="errMsg" style="font-size:24px;">Please select a Section from the dropdown menu!</div>';}
    		else{ $html .='<div class="errMsg" style="font-size:24px;text-align:center;">No one in this class has received a Kudos yet!</div>';} 
    	}

    if(is_array($arrRet) && count($arrRet)>0)
    {
        foreach($arrRet as $kudo_id=>$kudo_details)
        { 
            $month = date('m', strtotime($kudo_details['sent_date']));                  
            $year = date('Y', strtotime($kudo_details['sent_date']));
            $gender = getGender($kudo_details['receiver']); 
            $type = $kudo_details['kudo_type']; 
            $message = $kudo_details['message'];
            $message = preg_replace("/[\r\n]+/", " ", $message);
            $imageSrc = preg_replace('/\s+/', '', $type);
            $imageSrc = strtolower($imageSrc);
    		if($gender=='Boy'||$gender=='Girl')
                $genderToShow=$gender;
            else
                $genderToShow='noGender';		

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
                          //echo "username - ".$userName." sender - ".$kudo_details['sender']." userCategory - ".$category."<br>";
                          if(($userName == $kudo_details['sender'] && strcasecmp($userCategory, "teacher") == 0) || strcasecmp($userCategory, "School Admin") == 0)
                          {
                            $html .= "<td class=\"deleteKudoTd\" style=\"text-align:right;vertical-align:top;width:16px;\">";
                                $html .= "<span class=\"deleteKudo\" onclick=\"deleteThisKudo(".$kudo_id.",1)\"></span>";
                                $html .= "<input type=\"hidden\" name=\"userCategory\" id=\"userCategory\" value=\"".$category."\"/>";
                                $html .= "<input type=\"hidden\" name=\"childClass\" id=\"childClass\" value=\"".$class."\"/>";
                                $html .= "<input type=\"hidden\" name=\"childSection\" id=\"childSection\" value=\"".$section."\"/>";
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

    $html .='</table>';
    echo $html ;


?>