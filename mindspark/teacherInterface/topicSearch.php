<form name="frmMain" id="frmMain" method="post" action="myClasses.php">
			
<section class="hidden" id="searchSection">
<div id="close" onclick="closeSearch();" style=""></div>
<article class="popup" style="margin-left:400px;margin-top:150px;">
   
<p>
<input type="text" id="name" name="name" style="width:500px;"/>

<br>
<br>
<div id="searchResults" style="font-size:13px;color:white;width: 636px;">
<br>
		
	<!--Display Search Results-->
	<?php 
	$clusterSearchTerm = array();
	$i=0;

	// Dual connection for update
	
	$isCluster = findIfCluster($searchTerm);
	
	if($isCluster)
	{
		$searchFrequencyQuery = "update adepts_clusterMaster set noOfTimesSearched = noOfTimesSearched + 1 where cluster='$searchTerm'";
		$result = mysql_query($searchFrequencyQuery);
	}

	$query = "select teacherTopicDesc from adepts_teacherTopicClusterMaster a, adepts_clusterMaster b,adepts_teacherTopicMaster c where b.cluster='$searchTerm' and a.clusterCode=b.clusterCode and a.teacherTopicCode=c.teacherTopicCode and b.status='Live'";
	
	$result = mysql_query($query);

	while ($line = mysql_fetch_array($result))
	{
		$clusterSearchTerm[$i] = $line[0];
		$i++;
		
	}
			
	/*Never activated topics*/
	foreach($teacherTopicNeverActivatedSearch as $ttCode=>$ttDetails)
	{
    	$i++;
		
		if($ttDetails[0]==$searchTerm) { 
			$gradeArr = explode("-",$ttDetails[3]);
			$minGrade = $gradeArr[0];
			$maxGrade = $gradeArr[count($gradeArr)-1];
			?>
			<div class="topicContainer" id="topicContainer<?=$i?>" style="background-color:rgb(43, 41, 41);">
			
				<div class="actionsTab linkPointer" id="actionTab1<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span>
				</div>
				
				<div id="topicBoxDescTopic">
					<a class="topicName" href="sampleQuestions.php?ttCode=<?=$ttDetails[2]?>&cls=<?=$class?>&flow=<?=$teacherTopicNeverActivatedSearch[$ttDetails[2]][1]?>"><?=$ttDetails[0]?></a>
					<?php if($ttDetails['isCoteacher'] == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImgSearch" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div>
								<?php } ?>
				</div>
				
				<div id="topicBoxDescInfo1">
					Grade: <?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>
				</div>
				
				<div id="topicBoxDescInfo2">
					<?php if($ttDetails[4]!=""){
						?>
					Customized By : <?=$ttDetails[4]?>
					<?php } ?>
				</div>
				
				<div class="arrow-black-side actionTab1<?=$i?>">
					<div id="activateTab">
						<div class="activate"></div>
						<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
							<div class="bulkADMain"></div>
							<div class="bulkADHelp"></div>
							<div class="bulkADActionButton"></div>
						</div>
						<div id="activate" class="tabText linkPointer" <?php if($ttDetails[3]==0 || checkForClusters($ttCode,$ttDetails[1])==0) echo "style='visibility:hidden'";?>>
							<?php
							if($currentActivated>=15) { 
								if(stripos($ttDetails[1],"Custom")===false) { 
								?>	
									<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">See/Activate</span>
								<?php }
								else { ?>
									<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">Activate</span>
								<?php	
								}
						
							}
							else if($class+2 <= $minGrade || $class-2 >= $maxGrade) { ?>
								<span class="activateSpan" onClick="topicClassDifference(<?=$class?>,'<?=$ttDetails[3]?>')" id="<?=$ttDetails[2]?>">See/Activate</span>
							<?php } 
							else if(stripos($ttDetails[1],"Custom")===false)	{ ?>
									<span class="activateSpan" onClick="activateDeactivateTopic('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>')" id="<?=$ttDetails[2]?>" style="float: left;width: 100%;">See/Activate/customize</span>
							<?php } else { 

								$activatedForSections=array();
								foreach ($ttDetails['activatedForSections'] as $key => $value) {
									$activatedForSections[]=str_replace(" ", "", $value);
								}
								global $sectionArray;
								$thisClassSections=$sectionArray[$class];
								if(strlen($thisClassSections)==0) $thisClassSections="''";
								$thisClassSections=explode(",", $thisClassSections);
								$availableForSections=array();
								foreach ($thisClassSections as $key => $value) {
									if(!in_array(str_replace("'", "", $value), $activatedForSections))
										$availableForSections[]=str_replace("'", "", $value);
								}
								if ((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''"){
								?>
								<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								else{
									?>
								<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
									<?php
								}
								?>
							<?php } ?>
						</div>
						<?php 
						if(stripos($ttDetails[1],"Custom")!==false)	{
						?>
						<div class="customize"></div>
						<div id="customize" class="tabText linkPointer">
							<span onClick="showMapping('<?=$ttDetails[2]?>','<?=$class?>','<?=$section?>','<?=$ttDetails[1]?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
						</div>
						<?php } ?>
						<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttDetails[2]?>','<?=$teacherTopicNeverActivated[$ttDetails[2]][1]?>')" <?php if($ttDetails[3]==0) echo "style='visibility:hidden'";?>><span class="doAsStudent">Do MindSpark</span></div>
					</div>
				</div>
				
			</div>
			<br>
		<?php }
		
		if($topicFlag!=1 && $topicFlag!=2)
			for($i=0;$i<count($clusterSearchTerm);$i++)
				if ($ttDetails[0]==$clusterSearchTerm[$i]) {
					$topicFlag = 1;
					?>
				
					<div id="topicLabel1"><label style="font-size: 18px;margin-left: 15px;"> Topics containing the learning unit,<span style="color:rgb(248, 208, 148);"><b>"<?=$searchTerm?>"</b></span></label>
					</div>
				
					<?php 
					break;
				} ?>
		
		<?php 
		for($i=0;$i<count($clusterSearchTerm);$i++)
			if ($ttDetails[0]==$clusterSearchTerm[$i]) {
				$gradeArr = explode("-",$ttDetails[3]);
				$minGrade = $gradeArr[0];
				$maxGrade = $gradeArr[count($gradeArr)-1];
				?>
				
				<div class="topicContainer" id="topicContainer<?=$i?>" style="background-color:rgb(43, 41, 41);">
					<div class="actionsTab linkPointer" id="actionTab1<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span>
					</div>
					
					<div id="topicBoxDescTopic">
						<a class="topicName" href="sampleQuestions.php?ttCode=<?=$ttDetails[2]?>&cls=<?=$class?>&flow=<?=$teacherTopicNeverActivatedSearch[$ttDetails[2]][1]?>"><?=$ttDetails[0]?></a>
						<?php if($ttDetails['isCoteacher'] == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImgSearch" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div> 
								<?php } ?>
					</div>
					
					<div id="topicBoxDescInfo1">
						Grade: <?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>
					</div>
					
					<div id="topicBoxDescInfo2">
						<?php if($ttDetails[4]!=""){
							?>
						Customized By : <?=$ttDetails[4]?>
						<?php } ?>
					</div>
					
					<div class="arrow-black-side actionTab1<?=$i?>">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer" <?php if($ttDetails[3]==0) echo "style='visibility:hidden'";?>>
								<?php if($currentActivated>=15) { ?>
								<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttDetails[2]?>">Activate</span>
								<?php } else if($class+2 <= $minGrade || $class-2 >= $maxGrade) { ?>
									<span class="activateSpan" onClick="topicClassDifference(<?=$class?>,'<?=$ttDetails[3]?>')" id="<?=$ttDetails[2]?>">See/Activate</span>
								<?php } else if(stripos($ttDetails[1],"Custom")===false) { ?>
									<span class="activateSpan" onClick="activateDeactivateTopic('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>')" id="<?=$ttDetails[2]?>" style="float: left;width: 100%;">	See/Activate/customize</span>
								<?php } else { 

									$activatedForSections=array();
									foreach ($ttDetails['activatedForSections'] as $key => $value) {
										$activatedForSections[]=str_replace(" ", "", $value);
									}
									global $sectionArray;
									$thisClassSections=$sectionArray[$class];
									if(strlen($thisClassSections)==0) $thisClassSections="''";
									$thisClassSections=explode(",", $thisClassSections);
									$availableForSections=array();
									foreach ($thisClassSections as $key => $value) {
										if(!in_array(str_replace("'", "", $value), $activatedForSections))
											$availableForSections[]=str_replace("'", "", $value);
									}
									if ((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''"){
									?>
										<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
										<?php
									}
									else{
										?>
										<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttDetails[2]?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$ttDetails[1]?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
										<?php
									}
									?>
								<?php } ?>
							</div>
							
							<div class="customize"></div>
							
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttDetails[2]?>','<?=$class?>','<?=$section?>','<?=$ttDetails[1]?>')">See/Customize</span>
							</div>
							
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttDetails[2]?>','<?=$teacherTopicNeverActivatedSearch[$ttDetails[2]][1]?>')" <?php if($ttDetails[3]==0) echo "style='visibility:hidden'";?>><span class="doAsStudent">Do MindSpark</span></div>
						</div>
						
					</div>
					
				</div>
				<br>
				<?php 
			} 
	}
			/*End never activated topics*/
			
	?>
	
	<!--teacher topic all-->
	<?php 
	$i=0;
	foreach($teacherTopicActivatedAllSearch as $ttCode=>$ttDetails)
	{
		$i++;
		$ttName	=	$ttDetails["ttName"];
		$flow	=	$ttDetails["flow"];
		$isCoteacher = $ttDetails["isCoteacher"];
		if($ttName==$searchTerm) { ?>
			<div class="topicContainer" id="deactive<?=$i?>" style="background-color:rgb(43, 41, 41);">
				<div class="actionsTab linkPointer" id="actionTab1<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span>
				</div>
				
				<div id="outerCircle" class="outerCircle" title="Average topic progress of class">
					<div id="percentCircle" class="progressCircle forHighestOnly circleColor<?=round($ttProgress[$ttCode]/10)?>"><?=round($ttProgress[$ttCode],1)?>%</div>
				</div>
				
				<div id="topicBoxDescTopic">
					<a class="topicName" href="topicProgress.php?ttCode=<?=$ttCode?>&cls=<?=$class?>&section=<?=$section?>"><?=$ttName?></a>
					<?php if($isCoteacher == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImgSearch" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div>
								<?php } ?>
				</div>
				
				<div id="topicBoxDescInfo1">
					Grade: <?=$ttDetails["grade"]?>
				</div>
				
				<div id="topicBoxDescInfo1">
					<!--Activated on <?=setDateFormate($ttDetails["activationDate"])?>-->
					<?=$studentAttempted[$ttCode]?> out of <?=count($userIDs)?> students attempting
				</div>
				<?php $activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
				<div id="topicBoxDescInfo2" <?php if($activeSince>30) echo "style='margin-top:2.1%;cursor:default;'";else echo "style='cursor:default;'";  ?>>
					<?php if($ttDetails["deactivationDate"] !=""){ 
					
						$activeSince1	=	getDaysTillActivatedDeactive($ttDetails["activationDate"],$ttDetails["deactivationDate"]); ?>
						<!--Deactivated on <?=setDateFormate($ttDetails["deactivationDate"])?>-->
					Was active for <?php echo $activeSince1;  if($activeSince1==1 || $activeSince1==0) echo " Day"; else echo " Days"?>
					<?php } else{
					$activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
					<span <?php if($activeSince>30) echo "style='color:red;cursor:pointer;' title='Activated on ".setDateFormate($ttDetails['activationDate']).". It is not advisable to have a topic active for more than 30 days.'"; ?>>Active since <?=$activeSince; if($activeSince==1 || $activeSince==0) echo " Day"; else echo " Days"?>  <?php if($activeSince>30){ ?><sup> ?</sup><?php } ?></span>
					<?php } ?>
				</div>
				
				<?php if($ttDetails["deactivationDate"] == "") { ?>
					<div class="arrow-black-side actionTab1<?=$i?>"
						style="margin-right: -145px;">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer">
								<?php
								$activatedForSections=array();
								foreach ($ttDetails['activatedForSections'] as $key => $value) {
									$activatedForSections[]=str_replace(" ", "", $value);
								}
								if (count($activatedForSections)==1 && $activatedForSections[0]==$section){
									?>
								<span class="activateSpan deactivateSpan" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>')" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								else{
									?>
								<span class="activateSpan deactivateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>','<?=implode(',',$activatedForSections)?>','bulkADPrompt<?=$i?>');" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								?>
							</div>
							<div class="customize"></div>
							<div id="customize" class="tabText linkPointer">
								<span
									onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
							</div>
					
							<div id="download" class="tabText linkPointer"
								onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')">
								<span class="doAsStudent">Do MindSpark</span>
							</div>
					
						</div>
					</div>
					<?php
					
					} else {
						?>
					
					<div class="arrow-black-side actionTab1<?=$i?>"
						style="margin-right: -145px;">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer"
								<?php if(checkForClusters($ttCode,$flow)==0) echo "style='visibility:hidden'";?>>
												<?php if($currentActivated>=15) { ?>
												<span class="activateSpan" onClick="activateLimitOver()"
									id="<?=$ttDetails[2]?>">See/Activate</span>
												<?php
						
					} else if (stripos ( $ttName, "Custom" ) === false) {
							?>
													<span class="activateSpan"
									onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>')"
									id="<?=$ttDetails[2]?>">See/Activate</span>
											<?php } else { 

						$activatedForSections=array();
						foreach ($ttDetails['activatedForSections'] as $key => $value) {
							$activatedForSections[]=str_replace(" ", "", $value);
						}
						global $sectionArray;
						$thisClassSections=$sectionArray[$class];
						if(strlen($thisClassSections)==0) $thisClassSections="''";
						$thisClassSections=explode(",", $thisClassSections);
						$availableForSections=array();
						foreach ($thisClassSections as $key => $value) {
							if(!in_array(str_replace("'", "", $value), $activatedForSections))
								$availableForSections[]=str_replace("'", "", $value);
						}
						if ((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''"){
						?>
						<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
							<?php
						}
						else{
							?>
						<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>')" id="<?=$ttDetails[2]?>">Activate</span>
							<?php
						}
						?>
											<?php } ?>
												</div>
							<div class="customize"></div>
							<div id="customize" class="tabText linkPointer">
								<span
									onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>')">See/Customize</span>
							</div>
							<div id="download" class="tabText linkPointer"
								onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')">
								<span class="doAsStudent">Do MindSpark</span>
							</div>
						</div>
					</div>
					<?php } ?>
			</div>
			<br>
		<?php } 
		else{
		
		}
		if($topicFlag!=1 && $topicFlag!=2)
			for($i=0;$i<count($clusterSearchTerm);$i++)
				if ($ttName==$clusterSearchTerm[$i]) {
					$topicFlag = 2;
					?>
					<div id="topicLabel2"><label style="font-size: 14px;margin-left: 15px;"> Topics containing the learning unit,<span style="color:rgb(248, 208, 148);"><b>"<?=$searchTerm?>"</b></span></label></div>
	 				<?php 
					break;
				} 
			
		for($i=0;$i<count($clusterSearchTerm);$i++)
			if ($ttName==$clusterSearchTerm[$i]) { ?>
				<div class="topicContainer" id="deactive<?=$i?>" style="background-color:rgb(43, 41, 41);">
					<div class="actionsTab linkPointer" id="actionTab1<?=$i?>"><span id="actionsText" class="linkPointer">Actions</span></div>
					
					<div id="outerCircle" class="outerCircle" title="Average topic progress of class">
						<div id="percentCircle" class="progressCircle forHighestOnly circleColor<?=round($ttProgress[$ttCode]/10)?>"><?=round($ttProgress[$ttCode],1)?>%</div>
					</div>
					
					<div id="topicBoxDescTopic">
						<a class="topicName" href="topicProgress.php?ttCode=<?=$ttCode?>&cls=<?=$class?>&section=<?=$section?>"><?=$ttName?></a>
						<?php if($isCoteacher == 1)
							{ ?>
								<img src='assets/co-teacher/co_teacher_tag.png' class="coTeacherImgSearch" id="tooltipTab<?=$i?>">
								<div class="arrow-white-side tooltipTab<?=$i?>" style="margin-right:-145px;">
									<div id="activateTab">
										Empowered for students to learn the topic on their own.
									</div>
								</div>
								<?php } ?>
					</div>
					
					<div id="topicBoxDescInfo1">
						Grade: <?=$ttDetails["grade"]?>
					</div>
					
					<div id="topicBoxDescInfo1">
						<!--Activated on <?=setDateFormate($ttDetails["activationDate"])?>-->
						<?=$studentAttempted[$ttCode]?> out of <?=count($userIDs)?> students attempting
					</div>
					<?php $activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
					
					
					<div id="topicBoxDescInfo2" <?php if($activeSince>30) echo "style='margin-top:2.1%;cursor:default;'";else echo "style='cursor:default;'";  ?>>
						<?php if($ttDetails["deactivationDate"] !=""){ 
						
							$activeSince1	=	getDaysTillActivatedDeactive($ttDetails["activationDate"],$ttDetails["deactivationDate"]); ?>
						<!--Deactivated on <?=setDateFormate($ttDetails["deactivationDate"])?>-->
						Was active for <?php echo $activeSince1;  if($activeSince1==1 || $activeSince1==0) echo " Day"; else echo " Days"?>
						<?php } else{
						$activeSince	=	getDaysTillActivated($ttDetails["activationDate"]); ?>
						<span <?php if($activeSince>30) echo "style='color:red;cursor:pointer;' title='Activated on ".setDateFormate($ttDetails['activationDate']).". It is not advisable to have a topic active for more than 30 days.'"; ?>>Active since <?=$activeSince; if($activeSince==1 || $activeSince==0) echo " Day"; else echo " Days"?>  <?php if($activeSince>30){ ?><sup> ?</sup><?php } ?></span>
						<?php } ?>
					</div>
					
					<?php if($ttDetails["deactivationDate"] == "") { ?>
					<div class="arrow-black-side actionTab1<?=$i?>" style="margin-right:-145px;">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							<div id="activate" class="tabText linkPointer">
								<?php
								$activatedForSections=array();
								foreach ($ttDetails['activatedForSections'] as $key => $value) {
									$activatedForSections[]=str_replace(" ", "", $value);
								}
								if (count($activatedForSections)==1 && $activatedForSections[0]==$section){
									?>
								<span class="activateSpan deactivateSpan" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>')" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								else{
									?>
								<span class="activateSpan deactivateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','deactivate','deactive<?=$i?>','<?=implode(',',$activatedForSections)?>','bulkADPrompt<?=$i?>');" id="<?=$ttCode?>">Deactivate</span>
									<?php
								}
								?>
							</div>
							
							<div class="customize"></div>
							
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>')">See/Customize</span>
							</div>
							
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')"><span class="doAsStudent">Do MindSpark</span></div>
							
						</div>
					</div>
					<?php } 
					else { ?>
					
					<div class="arrow-black-side actionTab1<?=$i?>" style="margin-right:-145px;">
						<div id="activateTab">
							<div class="activate"></div>
							<div class="bulkActivateDeactivatePrompt" id="bulkADPrompt<?=$i?>">
								<div class="bulkADMain"></div>
								<div class="bulkADHelp"></div>
								<div class="bulkADActionButton"></div>
							</div>
							
							<div id="activate" class="tabText linkPointer" <?php if(checkForClusters($ttCode,$flow)==0) echo "style='visibility:hidden'";?>>
								<?php if($currentActivated>=15) { ?>
								<span class="activateSpan" onClick="activateLimitOver()" id="<?=$ttCode?>">Activate</span>
								<?php } else if(stripos ( $ttName, "Custom" ) === false) { ?>
								<span class="activateSpan" onClick="activateDeactivateTopic('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','seeActivate','topicContainer<?=$i?>')" id="<?=$ttCode?>">See/Activate</span>
								<?php } 
								else { 
									$activatedForSections=array();
									foreach ($ttDetails['activatedForSections'] as $key => $value) {
										$activatedForSections[]=str_replace(" ", "", $value);
									}
									global $sectionArray;
									$thisClassSections=$sectionArray[$class];
									if(strlen($thisClassSections)==0) $thisClassSections="''";
									$thisClassSections=explode(",", $thisClassSections);
									$availableForSections=array();
									foreach ($thisClassSections as $key => $value) {
										if(!in_array(str_replace("'", "", $value), $activatedForSections))
											$availableForSections[]=str_replace("'", "", $value);
									}
									if ((count($availableForSections)==1 && $availableForSections[0]==$section) || count($thisClassSections)==1 && $thisClassSections[0]=="''"){
									?>
									<span class="activateSpan" onClick="openDateOfTopicPrompt('<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','bulkADPrompt<?=$i?>')" id="<?=$ttCode?>">Activate</span>
										<?php
									}
									else{
										?>
									<span class="activateSpan" rel="openBulkPopup" onClick="openActivateDeactivatePrompt(this,'<?=$ttCode?>',<?=$schoolCode?>,<?=$class?>,'<?=$section?>','<?=$flow?>','<?=ucfirst($user->childName)?>','<?php if($ttDetails[3]==0) echo "-"; else echo $ttDetails[3];?>','activate','topicContainer<?=$i?>','<?=implode(',',$availableForSections)?>','bulkADPrompt<?=$i?>')" id="<?=$ttCode?>">Activate</span>
										<?php
									}
									?>
								<?php } ?>
							</div>
							
							<div class="customize"></div>
							
							<div id="customize" class="tabText linkPointer">
								<span onClick="showMapping('<?=$ttCode?>','<?=$class?>','<?=$section?>','<?=$flow?>')">See/Customize</span>
							</div>
							
							<div id="download" class="tabText linkPointer" onClick="doAsAStudent('<?=$ttCode?>','<?=$flow?>')"><span class="doAsStudent">Do MindSpark</span></div>
						</div>
					</div>
					
					<?php } ?>
				</div>
				<br>
			<?php } 
			else{
			
			}
	} ?> 

	<!--End teacher topic all-->
	</div>
	
	<div id="noTopics">
	<span style="font-size:30px;color: rgb(0, 255, 51);margin-left:40%;">No topics found

	</span>
   </div>
  </p>
	</article>
</section>
	 
	<!-- End Display Search Results-->
			<input type="hidden" id="searchingTerm" name="searchingTerm">
			<table id="topicDetails">
				<td width="5%"><label>Class</label></td>
		        <td width="22%" style="border-right:1px solid #626161">
		            <select name="cls" id="lstClass"  onchange="setSection('');" style="width:65%;">
		            <?=(count($classArray)!=1)?'<option value="">Select</option>':''?>
						<?php
							for ($i=0;$i<count($classArray);$i++)
							{
								echo "<option value='".$classArray[$i]."'";
								if ($cls==$classArray[$i])
								{
									echo " selected";
								}
								echo ">".$classArray[$i]."</option>";
							}
						?>
					</select>
		        </td>
				
				<input type="hidden" name="openTab" id="openTab" value="<?= isset($_REQUEST['openTab']) && $_REQUEST['openTab'] != "" ?$_REQUEST['openTab'] : '1'; ?>"/>
				<td width="7%" class="noSection"><label style="margin-left:20px;" id="lblSection" >Section</label></td>
				
		        <td width="22%" class="noSection" style="border-right:1px solid #626161" >
		            <select name="section" id="lstSection" style="width:85%;">
						<option value="">Select</option>
					</select>
		        </td>
				<?php 
					$pos = strpos($_SERVER['SCRIPT_NAME'], "mytopics.php");
					if ($pos === false) {
						?>
							<td width="20%"><label style="margin-left:10px;">Master Topic</label></td>
				
					        <td width="20%">
					            <select name="masterTopic" id="masterTopic">
						            <option value="" <?php if($masterTopic=="") echo "selected";?>>All</option>
						        <?php 
						            $sq	=	"SELECT DISTINCT classification FROM adepts_teacherTopicMaster WHERE subjectno LIKE '%".SUBJECTNO."%'
											 ORDER BY classification";
						            $rs	=	mysql_query($sq);
						            while($rw=mysql_fetch_array($rs))
						            {
						                $selected	=	"";
						                if($masterTopic==$rw[0])
						                    $selected	=	"selected";
						                echo "<option value='$rw[0]' $selected>$rw[0]</option>";
						            }
						        ?>
						        </select>
						        <input type="hidden" name="schoolCode" id="schoolCode" value="<?=$schoolCode?>" />
					        </td>
						<?php  
					}
					else{
						?>
						<td width="20%">
							<label style="margin-left:10px;">Topic Category</label>
						</td>
						<td width="20%">
							<select name="topicCategory" id="topicCategory" onChange="handleCategoryChange(this.value)">
								<option value="0">Select</option>
								<option value="1" <?= isset($_REQUEST['openTab']) && $_REQUEST['openTab'] == '1'?"selected='selected'":""; ?> >Currently Active Topics</option>
								<option value="2" <?= isset($_REQUEST['openTab']) && $_REQUEST['openTab'] == '2'?"selected='selected'":""; ?>>All Active & Deactivated Topic</option>
								<option value="3" <?= isset($_REQUEST['openTab']) && $_REQUEST['openTab'] == '3'?"selected='selected'":""; ?>>Activate a Topic</option>
							</select>
							
						</td>
				<?php 					
					}
				?>
				<td>
					<input type="button" class="button" id="generate" value="Go" onClick="filterTopicWise('false');">
				</td>
				
			  </table>
			  <input type="hidden" name="checkflag" id="checkflag" value="">
			</form>

<?php

function findIfCluster($searchTerm)
{
	$sql = "Select * from adepts_clusterMaster where cluster='$searchTerm'";
	$result = mysql_query($sql);
	$total = mysql_num_rows($result);
	if($total>0)
	return 1;
	else
	return 0; 
	
}

?>
