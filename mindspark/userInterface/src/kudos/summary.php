<!doctype html>
<?php
set_time_limit(0);
include('../check.php');
checkPermission('HRP');

include('common_functions.php');
$arrKudos = getKudosSummary();
	
?>
<html>
	<head>
		<title>
			Kudos - Summary
		</title>
		<link rel="stylesheet" href="styles/style.css">
		<script language="javascript" type="text/javascript" src="../script/tablesort.js"></script>
	</head>
	<body>		
		<div id='kudos_header' class="kudos_header">
			<?php
				include('kudos_header.php');
			?>
		</div>
		<div id="kudo_summary">
			<form name="formmain" id="formmain" method="POST">
				<table border="0" align="center" width="95%" cellpadding="4" cellspacing="2">
					<?php
					if(is_array($arrKudos) && count($arrKudos)>0)
					{?>
						<thead>
							<tr bgcolor="#5ccce0" height="30px">
								<td width="5%" onclick='sortColumn(event)' type='Number'>
									<font color='#fff'>Srno</font>
								</td>
								<td onclick='sortColumn(event)' type='CaseInsensitiveString'>
									<font color='#fff'>Kudo Type</font>
								</td>
								<td onclick='sortColumn(event)' type='CaseInsensitiveString'>
									<font color='#fff'>Sender</font>
								</td>
								<td onclick='sortColumn(event)' type='CaseInsensitiveString'>
									<font color='#fff'>Receiver</font>
								</td>
								<td onclick='sortColumn(event)' type='Date'>
									<font color='#fff'>Date</font>
								</td>
								<td width="50%">
									<font color='#fff'>Message</font>
								</td>
							</tr>
						</thead>
					<?php
						$sr = 1;
						foreach($arrKudos as $kudo_id=>$kudo_details)
						{
							$type = $kudo_details['kudo_type'];
							if($type == 'Thank You')
								$bgcolor = '#fef2ba';
							elseif($type == 'Good Work')
								$bgcolor = '#ffd8bd';
							elseif($type == 'Impressive')
								$bgcolor = '#e0f2bd';
							elseif($type == 'Exceptional')
								$bgcolor = '#bbe1f4';
						?>
							<tr bgcolor="<?=$bgcolor;?>">
								<td><?=$sr++;?></td>
								<td nowrap title="Kudo Type"><?=$type;?></td>
								<td nowrap title="Sender"><?=fetchFullName($kudo_details['sender']);?></td>
								<td nowrap title="Receiver"><?=fetchFullName($kudo_details['receiver']);?></td>
								<td nowrap title="Sent Date"><?=date('d-m-Y', strtotime($kudo_details['sent_date']));?></td>
								<td title="Message" style="text-align: left !important;"><?=$kudo_details['message'];?></td>
							</tr>							 
						<?
						}
					}
					else
						echo 'No Records Found.';
					?>
				</table>
			</form>
		</div>
		<br/>
		<br/>
	</body>
</html>