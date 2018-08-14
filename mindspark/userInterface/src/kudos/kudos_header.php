<?php

include_once("../../check1.php");

if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
{
	$myColor = '#4D4D4D';
	$homeColor = '#36A9E1';	
}
else
{
	$myColor = '#36A9E1';
	$homeColor = '#4D4D4D';
}

$fullname = fetchFullName($_SESSION['username']);
$fullnameArray = explode(" ",$fullname);

if($theme==1)
{$link="kudosHomeLowerClass.php"; }
if($theme==2)
{$link="kudosHomeMidClass.php"; }
if($theme==3)
{$link="kudosHomeHighClass.php"; }

?>
<html>
	<head>
		<link rel="stylesheet" href="styles/style.css">
	</head>
<body>

<table width="60%" border="0" align="center" style="margin-top:2%;" id="kudosHeaderTitle"> <!--style="margin-left:36%; padding-bottom:5%;">-->
	<tr style="padding-bottom: 5px;">
		<td align="center" valign="bottom">
			<span style=" font-size:24px; background-color: <?=$myColor?>;color: #fff;padding: 5px 5px 5px 5px; border-right: 2px solid #fff; ">
                <a href="<?php echo $link; ?>" class="wallLinks">Wall Of Fame</a>
            </span>
			<span style=" font-size:24px; background-color: <?=$homeColor?>;color: #fff;padding: 5px 5px 5px 5px;">
                <a href="<?php echo $link; ?>?wall=my" class="wallLinks">My Wall</a>
            </span> 
			<?php /*?><?php
			if(checkAdmin($_SESSION['username']))
			{?>
				<span style="background-color: <?=$summaryColor?>;color: #fff;padding: 5px 5px 5px 5px;">
					<a href="summary.php" class="wallLinks">Summary</a>
				</span>	
			<?
			}?>		<?php */?>
			</td>
			</tr>
			</table>			
		</td>
	</tr>
</table>
</body>
</html>
<?php /*?><?php
function checkAdmin($userID)
{
	$admin = FALSE;
	$right = 'HRP';
	$query = "SELECT appRights FROM marketing WHERE name='$userID' LIMIT 1";
	$result = mysql_query($query);
	while($user_row = mysql_fetch_array($result))
	{
		$appRights = explode(",",$user_row['appRights']);
		if(in_array($right,$appRights))
			$admin = TRUE;
	}
	return $admin;
}
?><?php */?>