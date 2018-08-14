<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");
include("../userInterface/constants.php");
//include("/functions/dashboardFunctions.php");
include("../slave_connectivity.php");
$userID="";
if(isset($_GET["userID"]) && $_GET["userID"]!="")
{
	$userID = $_GET["userID"];
	$fromDate = str_replace("-","",$_GET["fromdate"]);
	$tillDate = str_replace("-","",$_GET['tilldate']);
	// $sq = "SELECT sparkies FROM adepts_rewardPoints WHERE userID=$userID";
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $sparkieTillYesterday = $rw[0];
	
	// $sq = "SELECT SUM(noOfJumps) FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int=".date("Ymd");
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $sparkieToday = $rw[0];

	// $sq = "SELECT SUM(sparkieConsumed) FROM adepts_userBadges WHERE userID=$userID AND DATE(lastModified)=CURDATE()";
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $sparkieConsumedToday = $rw[0];
	
	// $sq = "SELECT SUM(noOfJumps) FROM adepts_sessionStatus WHERE userID=$userID";
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $totalSparkieEarned = $rw[0];

	// $sq = "SELECT SUM(sparkieEarned) FROM adepts_userBadges WHERE userID=$userID";
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $totalSparkieEarned1 = $rw[0];

	// $sq = "SELECT SUM(sparkieConsumed) FROM adepts_userBadges WHERE userID=$userID";
	// $rs = mysql_query($sq);
	// $rw = mysql_fetch_array($rs);
	// $totalSparkieConsumed = $rw[0];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sparkie Description</title>
</head>

<body>

<?php 

if($userID!="")
{
	$sq1 = "SELECT username, childClass, childSection, schoolCode, schoolName, childName FROM adepts_userDetails WHERE userID=$userID";
	$rs1 = mysql_query($sq1);
	$rs1=mysql_fetch_array($rs1);
	
	$noOfSparkies  = 0;
	
	$query2 = "SELECT sum(noOfJumps) FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int<$fromDate";	
	$result2 = mysql_query($query2);	
	if($line2 = mysql_fetch_array($result2))
	{
		$noOfSparkies = $line2[0];
		$sq2 = "SELECT userID,SUM(sparkieEarned),SUM(sparkieConsumed) FROM adepts_userBadges
				 WHERE userID=$userID AND batchType!='topicCompletion' AND DATE(lastModified)<$fromDate group by userID";				 
		$rs2 = mysql_query($sq2);
		$rw2 = mysql_fetch_array($rs2);
		if(!empty($rw2)){
			$noOfSparkies += $rw2[1];
			$noOfSparkies -= $rw2[2];
		}		
	}
	$sparkiesTillToday = 0;
	$query3 = "SELECT sum(noOfJumps) FROM adepts_sessionStatus WHERE userID=$userID AND DATE(startTime_int)<=CURDATE()";	
	$result3 = mysql_query($query3);	
	if($line3 = mysql_fetch_array($result3))
	{
		$sparkiesTillToday = $line3[0];
		$sq3= "SELECT userID,SUM(sparkieEarned),SUM(sparkieConsumed) FROM adepts_userBadges
				 WHERE userID=$userID AND batchType!='topicCompletion' AND DATE(lastModified)<=CURDATE() group by userID";				 
		$rs3 = mysql_query($sq3);
		$rw3 = mysql_fetch_array($rs3);
		if(!empty($rw3)){					
			$sparkiesTillToday += $rw3[1];			
			$sparkiesTillToday -= $rw3[2];
		}		
	}
	$sq="SELECT a.*, ifnull(b.e,'') sparkieEarned, ifnull(b.f,'') sparkieConsumed,
	ifnull(b.t,'') batchType FROM (SELECT DATE(startTime) dayS,COUNT(sessionID) sessions, 
	SUM(noOfJumps) sparkiesThatDay FROM adepts_sessionStatus WHERE userID=$userID 
 AND startTime_int>=$fromDate AND startTime_int <= $tillDate
  GROUP BY dayS) a LEFT OUTER JOIN 
  (SELECT DATE(lastModified) batchDate, GROUP_CONCAT(sparkieEarned) e,
   GROUP_CONCAT(sparkieConsumed) f, 
	GROUP_CONCAT(batchType) t FROM adepts_userBadges WHERE
	userID=$userID AND batchType !='topicCompletion' GROUP BY DATE(lastModified)) b ON a.dayS=b.batchDate
	UNION 
	SELECT b.batchDate, 0, 0,ifnull( b.e,'') sparkieEarned,ifnull(b.f,'') sparkieConsumed,
	ifnull(b.t,'') batchType FROM (SELECT DATE(startTime) dayS, COUNT(sessionID) sessions,
 SUM(noOfJumps) sparkiesThatDay FROM adepts_sessionStatus WHERE userID=$userID AND 
startTime_int>=$fromDate AND startTime_int <= $tillDate GROUP BY dayS) a 
JOIN (SELECT DATE(lastModified) batchDate, GROUP_CONCAT(sparkieEarned) e,
 GROUP_CONCAT(sparkieConsumed) f,batchType t FROM adepts_userBadges WHERE 
  userID=$userID AND batchType !='topicCompletion' GROUP BY DATE(lastModified)) b ON a.dayS=b.batchDate WHERE a.dayS is NULL ORDER BY dayS";
	//echo $sq;
	$rs = mysql_query($sq);

	
	?>
	<table class="gridtable" border="1" align="center" cellpadding="3" cellspacing="0" width="100%">
		
		<tr>
		<td colspan="4"class="header" >
		<?php echo 'Student Name: '.$rs1[5]; ?>
		</td></tr>
		<tr>				
		<td colspan="4"class="header" >
		<?php echo 'Total Sparkie Count till today : '.$sparkiesTillToday; ?>
		</td>
		</tr>
		<tr >
		<td class="header">Date</td>
		<!-- <td>#Sessions</td> -->
		<td class="header" >Sparkie earned in the Day</td>
		<!-- <td class="header" >Badge-sparkies earned</td>-->
		<td class="header" >Sparkie consumed by theme purchase</td> 
		<td class="header" >Total Sparkie Count</td>
		</tr>
		<?php
		$sp=$noOfSparkies ;
		
		
		while($rw = mysql_fetch_array($rs))
		{ 
		
			$sparkiesData = array();
			$Earned = array();
			$Consumed = array();
			$sEarned=$sConsumed= '';
			$sparkeis = 0;	
			$sparkeisEarned = explode(',', $rw[3]);
			$sparkiesConsumed = explode(',',$rw[4]);
			$sparkiesBadgeType = explode(',',$rw[5]);					
			if(!empty($sparkiesBadgeType[0]))
			{							
				foreach($sparkiesBadgeType as $key=>$types)
				{										
						$sp+= $sparkeisEarned[$key]-$sparkiesConsumed[$key];
						$sparkeis +=$sparkeisEarned[$key];													
					// if($sparkeisEarned[$key] != 0){
					// 	$Earned[] = $types.'('.$sparkeisEarned[$key].')';				
					// }
					if($sparkiesConsumed[$key] != 0){
						$Consumed[] = $types.'('.$sparkiesConsumed[$key].')';			
					}
				}
			}			
				$sparkeis +=$rw[2];
				$sp+=$rw[2];
			
			// if(!empty($Earned)){
			// 	$sEarned = implode(',',$Earned);
			// }
			if(!empty($Consumed)){
				$sConsumed = implode(',',$Consumed);
			}

		?>
			<tr >	
			<?php  $date=date_create($rw[0]);?>	
				<td><?=date_format($date,"d-m-Y")?></td>				
				<td><?=$sparkeis?></td>
				<!-- <td style="max-width:210px;word-wrap:break-word"><?=$sEarned?></td>-->
				<td style="max-width:210px;word-wrap:break-word"><?=$sConsumed?></td> 			
				<td><?=$sp?></td>
			</tr> 
			
		<?php } ?>
	</table>
	<?php 
		
// $sq2="SELECT a.*,b.sparkieConsumed sparkieConsumed,b.batchType batchType
//  FROM (SELECT DATE(startTime) dayS FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int>=$fromDate 
//  AND startTime_int <= $tillDate ) a JOIN (SELECT DATE(lastModified) batchDate,sparkieConsumed, batchType
//  FROM adepts_userBadges WHERE userID=$userID and sparkieConsumed!=0) b ON a.dayS=b.batchDate
//  UNION
//   SELECT b.batchDate,b.sparkieConsumed sparkieConsumed, b.batchType batchType
//   FROM (SELECT DATE(startTime) dayS FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int>=$fromDate 
//  AND startTime_int <= $tillDate GROUP BY dayS) a JOIN (SELECT DATE(lastModified) batchDate,sparkieConsumed,batchType
//  FROM adepts_userBadges WHERE userID=$userID and sparkieConsumed!=0 ) b ON a.dayS=b.batchDate
//   WHERE a.dayS is NULL ORDER BY batchType";
// 		$rs2 = mysql_query($sq2);
// 		$sparkiesConsumedNew = array();	
// 		while($rw2 = mysql_fetch_array($rs2))
// 		{ 										
// 			$sparkiesConsumedNew[] = $rw2;			
// 		}
// 		if(!empty($sparkiesConsumedNew))
// 		{
			?>
<!-- 			<table class="gridtable" border="1" align="center" cellpadding="3" cellspacing="0" width="100%" style="margin-top:20px;">
				<tr>
				<td class="header" colspan="4">
					Themes
				</td>
				</tr>		
					<?php					
					foreach ($sparkiesConsumedNew as $key => $value)
					 {				
					?>
						<tr>
						<td>
							<?php echo $value[0]; ?>
						</td>						
						<td>
							<?php echo $value[2]; ?>
						</td>
						
						<td>
							<?php echo $value[1]; ?>
						</td>
						</tr>	
				<?php } ?>
		</table>
 -->			<?php 
		// }	?>
		<div style="margin-top:10px;">
			<a href="faq.php" target="_blank">
			FAQs on Sparkie Logic
			</a>
		</div>
<?php }
?>
</body>
</html>