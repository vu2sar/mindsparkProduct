<?php
if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
{
	$myColor = '#36A9E1';
	$homeColor = '#4D4D4D';
	$showAllColor='#4D4D4D';
}
/*else if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='showAll')
{
	$myColor = '#4D4D4D';
	$homeColor='#4D4D4D';
	$showAllColor = '#36A9E1';
}*/
else
{
	$showAllColor = '#4D4D4D';
	$myColor = '#4D4D4D';
	$homeColor = '#36A9E1';
}

$fullname = fetchFullName($_SESSION['username']);
$fullnameArray = explode(" ",$fullname);
?>
<html>
	<head>
		<!--<link rel="stylesheet" href="styles/style.css">-->
	</head>
<body>
<table height="30px" width="95%" border="0" align="center" style="margin-top:10px;">
	<tr style="padding-bottom: 5px;">
		<!--<td align="left" width="20%">
			<img align="left" src="<?=$root?>../images/ei_logo.png" width="60" height="60">
		</td>-->		
		<td align="center" valign="bottom">
			<span id="wallOfFameButton" style="background-color: <?=$homeColor?>;color: #fff;padding: 5px 5px 5px 5px; border-right: 2px solid #fff; ">
				<a href="kudosHomeTeacherInterface.php" class="wallLinks">Wall Of Fame</a>
			</span>
			<span id="myWallButton" style="background-color: <?=$myColor?>;color: #fff;padding: 5px 5px 5px 5px; border-right: 2px solid #fff;">
				<a href="kudosHomeTeacherInterface.php?wall=my" class="wallLinks">My Wall</a>
			</span>
            <span id="showAllButton" onClick="showAllKudos();" style=" cursor:pointer; background-color: <?=$showAllColor?>;color: #fff;padding: 5px 5px 5px 5px;">
				<a class="wallLinks">Show All</a>
			</span>			
		</td>
		<!--<td align="right" width="20%" valign="bottom" >
			<table border="0" class="MainHeader" id="UserMenu" align="right">
				<tr>
					<td valign="bottom">
						<span class="UserName">
							Hi, <?php echo $fullnameArray[0];?>
						</span>
					</td>
					<td>
					 <a href='<?=$root?>/main.php'><img src="<?=$root?>../images/home.png" width="50" height="60" border="0" align="right"></a>
  					</td>
				</tr>
			</table>			
		</td>-->
	</tr>
</table>
</body>
</html>