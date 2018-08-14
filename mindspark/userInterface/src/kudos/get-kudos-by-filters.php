<?php

include_once("../../check1.php");
include('common_functions.php');

$data = json_decode(file_get_contents('php://input'));
//print_r(file_get_contents('php://input'));

$category =$data->{'category'};
$class =$data->{'class'};
$section = $data->{'section'};
$schoolCode = $data->{'schoolCode'};

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

$dataToReturn='<table id="TblKudos" align="center" width="95%" border="0">';
if(count($arrRet)==0)
	{ 
		if($section=='section'){ $dataToReturn.='<tr><td><font style="font-size:24px;">Please select a Section from the dropdown menu!</font></td></tr>';}
		else{ $dataToReturn.='<tr><td><font style="font-size:24px;">No one in this class has received a Kudos yet!</font></td></tr>';} 
	}

if(is_array($arrRet) && count($arrRet)>0)
{
$i=0;
foreach($arrRet as $kudo_id=>$kudo_details)
                    { 
                        $month = date('m', strtotime($kudo_details['sent_date']));                  
                        $year = date('Y', strtotime($kudo_details['sent_date']));
                        $gender = getGender($kudo_details['receiver']); 
                        $type = $kudo_details['kudo_type']; 
                        $imageSrc = preg_replace('/\s+/', '', $type);
                        $imageSrc = strtolower($imageSrc);
						if($gender=='Boy'||$gender=='Girl'){ $genderToShow=$gender;} else {$genderToShow='noGender';}
						
					if($i%3==0)
                     {$dataToReturn.= '<tr>';}
                      $dataToReturn.='<td style="cursor:pointer" title="'.$kudo_details['message'].'" width="33%">
					  <div id="kudosTd" onClick="showCertificateModal('.$kudo_id.')">
                                <table border="0" width="100%" id="KudoSummary">
                                    <tr>
                                        <td rowspan="2" id="Figurine">
                                            <img src="images/'.$genderToShow.'.png" title="'.fetchFullName($kudo_details['receiver']).'" height="50px" width="50px"/>
                                        </td>
                                        <td colspan="2" style="text-align: left;">
                                            '.fetchFullName($kudo_details['receiver']).' received '.$type.' from '.fetchFullName($kudo_details['sender']).'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="Date">
                                            '.date('d F, Y', strtotime($kudo_details['sent_date'])).'
                                        </td>
                                        <td width="40%" style="text-align: right;">
                                            <img  src="images/'.$imageSrc.'.png" height="100px" width="100px">
                                        </td>
                                    </tr>
                                </table></div></td>';  
						
						
						//if(count($arrRet)==0){ $dataToReturn.='<td>No one in this class has received a Kudos yet.</td>';}
						if(count($arrRet)==1){ $dataToReturn.='<td>&nbsp;</td><td>&nbsp;</td>';}
						if(count($arrRet)==2){ $dataToReturn.='<td>&nbsp;</td>';}
                        if($i%3==2)
                     	{$dataToReturn.= '</tr>'; }
						$i++;
                    }
//$dataToReturn.='</table>';
//echo $dataToReturn;
//echo "TEST";
//exit;

}

$dataToReturn.='</table>';
echo $dataToReturn;


?>