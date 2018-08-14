<?php

class msLangQuestion
{
	var $tagType;
	var $qcode;
	var $qTemplate;
	var $passageType;
	var $passageID;	
	var $topic;
	var $msLevel;
	var $difficulty;
	
	var $qType;						
	var $playTitle;
	var $titleText;
	var $titleSound;			
	var $quesText;
	var $quesSound;
	var $quesSubType;
	var $quesAudioIconSound;
	var $quesImage;
	var $totalOptions;
	var $correctAnswer;
	var $optSubType;
	var $option_a;
	var $option_b;	
	var $option_c;	
	var $option_d;	
	var $option_e;	
	var $option_f;	
	var $sound_a;	
	var $sound_b;	
	var $sound_c;	
	var $sound_d;	
	var $sound_e;	
	var $sound_f;	
	var $desc_a;	
	var $desc_b;	
	var $desc_c;	
	var $desc_d;	
	var $desc_e;	
	var $desc_f;
	var $skillID;
	var $subSkillID;
	var $subSubSkillID;	
	var $explanation;
	var $misConception;
	var $paramNameStr;
	var $paramValueStr;
	var $dbConnection;
	var $curUserName;
	var $first_alloted;
	var $second_alloted;
	var $status;
	var $firstReviewerAnswer;
	var $secondReviewerAnswer;
	var $trail;
	var $submitdate;
	var $questionmaker;
	var $lastModified;
	var $currentAlloted;
	var $ignoreWords;
	var $credit;
	var $reviewerAnswer;

	/*var $wordMakingText;
	var $minwordLength;
	var $minwords;*/
	var $generatedStem;

	var $tempMakingWordStrName;
	var $tempMakingWordStrValue;

	/*var $quesSubSubType;*/
	
	
	function msLangQuestion($db)
    {
		$this->tagType='';
		$this->qcode='';
		$this->qTemplate='';
		$this->passageType='';
		$this->passageID='';	
		$this->topic='';
		$this->msLevel='';
		$this->difficulty='';
		
		$this->qType='';						
		$this->playTitle='';
		$this->titleText='';
		$this->titleSound='';			
		$this->quesText='';
		$this->quesSound='';
		$this->quesSubType='';
		$this->quesAudioIconSound='';
		$this->quesImage='';
		$this->totalOptions='';
		$this->correctAnswer='';
		$this->optSubType='';
		$this->option_a='';
		$this->option_b='';	
		$this->option_c='';	
		$this->option_d='';	
		$this->option_e='';	
		$this->option_f='';	
		$this->sound_a='';	
		$this->sound_b='';	
		$this->sound_c='';	
		$this->sound_d='';	
		$this->sound_e='';	
		$this->sound_f='';	
		$this->desc_a='';	
		$this->desc_b='';	
		$this->desc_c='';	
		$this->desc_d='';	
		$this->desc_e='';	
		$this->desc_f='';
		$this->skillID='';
		$this->subSkillID=0;
		$this->subSubSkillID=0;	
		$this->explanation='';
		$this->misConception='';
		$this->paramNameStr;
		$this->paramValueStr;
		$this->curUserName;
		$this->first_alloted="";
		$this->second_alloted="";
		$this->status=0;
		/*$this->quesSubSubType="";*/
		$this->dbConnection=$db;
		$this->firstReviewerAnswer=NULL;
		$this->secondReviewerAnswer=null;
		$this->trail="";
		$this->submitdate="";
		$this->currentAlloted="";
		$this->lastModified="";
		$this->questionmaker="";
		$this->ignoreWords="";
		$this->credit=1;
		$this->reviewerAnswer="";

		/*$this->wordMakingText="";
	    $this->minwordLength="";
		$this->minwords="";*/
		$this->tempMakingWordStrName;
		$this->tempMakingWordStrValue;
		$this->generatedStem="";
		
	}
	
	
	function setCommonPostParam()
	{
		if(isset($_REQUEST["statusToSend"])) $this->status = $_REQUEST["statusToSend"];	
		if(isset($_POST["playTitle"])) $this->playTitle = $_POST["playTitle"];
		if(isset($_POST["quesTaggedToSend"])) $this->tagType = $_POST["quesTaggedToSend"];
		if(isset($_REQUEST["qcodeSend"])) $this->qcode = $_REQUEST["qcodeSend"];
		if(isset($_POST["quesTitleToSend"])) $this->titleText = $_POST["quesTitleToSend"];		
		if(isset($_POST["titleSoundFileSend"])) $this->titleSound = $_POST["titleSoundFileSend"];
		if(isset($_POST["quesTextToSend"])) $this->quesText = $_POST["quesTextToSend"];		
		if(isset($_POST["templateToSend"])) $this->qTemplate = $_POST["templateToSend"];
		if(isset($_POST["passageIDToSend"])) $this->passageID = $_POST["passageIDToSend"];
		if(isset($_POST["passageTypeToSend"])) $this->passageType = $_POST["passageTypeToSend"];
		if(isset($_POST["skillsToSend"])) $this->skillID = $_POST["skillsToSend"];
		if(isset($_POST["subSkillsToSend"])) $this->subSkillID = $_POST["subSkillsToSend"];
		if(isset($_POST["subSubSkillsToSend"])) $this->subSubSkillID = $_POST["subSubSkillsToSend"];
		if(isset($_POST["usernameTOsend"])) $this->curUserName = $_POST["usernameTOsend"];
		if(isset($_POST["topicToSend"])) $this->topic = $_POST["topicToSend"];
		if(isset($_POST["msLevelTagToSend"])) $this->msLevel = $_POST["msLevelTagToSend"];
		if(isset($_POST["difficulty"])) $this->difficulty = $_POST["difficulty"];		
		if(isset($_POST["difficultyToSend"])) $this->difficulty = $_POST["difficultyToSend"];
		if(isset($_POST["optionsNumToSend"])) $this->totalOptions = $_POST["optionsNumToSend"];
		if(isset($_REQUEST["reviewerFirst"])) $this->first_alloted = $_REQUEST["reviewerFirst"];
		if(isset($_REQUEST["reviewerSecond"])) $this->second_alloted = $_REQUEST["reviewerSecond"];
		if(isset($_POST["explanationToSend"])) $this->explanation = $_POST["explanationToSend"];
		if(isset($_POST['ignoreWords']))  $this->ignoreWords = $_POST['ignoreWords'];
		if(isset($_REQUEST["firstReviewerAnsToSend"]) && $_REQUEST["firstReviewerAnsToSend"]!="") $this->firstReviewerAnswer = $_REQUEST["firstReviewerAnsToSend"];
		if(isset($_REQUEST["secondReviewerAnsToSend"]) && $_REQUEST["secondReviewerAnsToSend"]!="") $this->secondReviewerAnswer = $_REQUEST["secondReviewerAnsToSend"];
		if(isset($_REQUEST["reviewerAnswerToSend"]) && $_REQUEST["reviewerAnswerToSend"]!="") $this->reviewerAnswer = $_REQUEST["reviewerAnswerToSend"];
		
		if($this->subSkillID=="" || $this->subSkillID==" "){
			$this->subSkillID=0;
		}
		if($this->subSubSkillID=="" || $this->subSubSkillID==" "){
			$this->subSubSkillID=0;
		}		
	}
	function setBlankTypePostParam()
	{	 	
		
		if(isset($_POST["paramNameArrToSend"])) $this->paramNameStr = $_POST["paramNameArrToSend"];
		if(isset($_POST["paramValueArrToSend"])) $this->paramValueStr = $_POST["paramValueArrToSend"];				
		if(isset($_POST["mcqQuesSubTypeToSend"])) {			
			$this->quesSubType = $_POST["mcqQuesSubTypeToSend"]	;		
		}
		
		if(isset($_POST["mcqQuesSubType"])){
			 $this->quesSubType = $_POST["mcqQuesSubType"];
		}	
	
		if(isset($_POST["qTypeToSend"])) $this->qType = $_POST["qTypeToSend"];	
		if($this->qType=='openEnded'){
			$this->credit=0;
		}		
		if(isset($_POST["audioIconSoundFileSend"])) $this->quesAudioIconSound = $_POST["audioIconSoundFileSend"];
		
		$this->quesSound="";			
		$this->misConception="";
	}
	
	function setMcqTypePostParam()
	{		
				
		if(isset($_POST["qTypeToSend"])) $this->qType = $_POST["qTypeToSend"];			
	
		if(isset($_POST["mcqQuesSubTypeToSend"])) {			
			$this->quesSubType = $_POST["mcqQuesSubTypeToSend"]	;		
		}
		
		if(isset($_POST["mcqQuesSubType"])){
			 $this->quesSubType = $_POST["mcqQuesSubType"];
		}		
		if(isset($_POST["quesSoundFileSend"])) $this->quesSound = $_POST["quesSoundFileSend"];			
		if(isset($_POST["audioIconSoundFileSend"])) $this->quesAudioIconSound = $_POST["audioIconSoundFileSend"];		
		if(isset($_POST["quesImageFileSend"])) $this->quesImage = $_POST["quesImageFileSend"];		
		if(isset($_POST["optionsNum"])) $this->totalOptions = $_POST["optionsNum"];		
		if(isset($_POST["mcqOptSubTypeToSend"])) $this->optSubType = $_POST["mcqOptSubTypeToSend"];		
		
		if(isset($_POST["correctAnsToSend"])) $this->correctAnswer = $_POST["correctAnsToSend"];
		
		$this->misConception="";		
		$optArr=array('a','b','c','d','e','f');	
		for($i=1; $i<=6; $i++)
		{
				${"tmpOption_".$optArr[$i-1]}="";
				${"tmpSound_".$optArr[$i-1]} ="";
				${"tmpDesc_".$optArr[$i-1]}="";
		}	
		for($i=1; $i<=$this->totalOptions; $i++)
		{				
			if($this->optSubType=="text"){
				if(isset($_POST["option".$i."textToSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."textToSend"];
			}else if($this->optSubType=="image"){
				if(isset($_POST["option".$i."imageFileSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."imageFileSend"];					
			}
			
			if(isset($_POST["option".$i."audioFileSend"])) ${"tmpSound_".$optArr[$i-1]} = $_POST["option".$i."audioFileSend"];
			if(isset($_POST["option".$i."descToSend"])) ${"tmpDesc_".$optArr[$i-1]} = $_POST["option".$i."descToSend"];
					
			
		}		
		
				
				
		$this->option_a = $tmpOption_a;
		$this->option_b = $tmpOption_b;
		$this->option_c = $tmpOption_c;
		$this->option_d = $tmpOption_d;
		$this->option_e = $tmpOption_e;
		$this->option_f = $tmpOption_f;
		$this->sound_a = $tmpSound_a;
		$this->sound_b = $tmpSound_b;
		$this->sound_c = $tmpSound_c;
		$this->sound_d = $tmpSound_d;
		$this->sound_e = $tmpSound_e;
		$this->sound_f = $tmpSound_f;
		$this->desc_a = $tmpDesc_a;
		$this->desc_b = $tmpDesc_b;
		$this->desc_c = $tmpDesc_c;
		$this->desc_d = $tmpDesc_d;
		$this->desc_e = $tmpDesc_e;
		$this->desc_f = $tmpDesc_f;
	}
	
	function setSequenceTypePostParam()
	{		
		
				
		//if(isset($_POST["qTypeToSend"])) $this->qType = $_POST["qTypeToSend"];			
	
		if(isset($_POST["commonQuesSubTypeToSend"])) {			
			$this->quesSubType = $_POST["commonQuesSubTypeToSend"]	;		
		}
		
			
		if(isset($_POST["quesSoundFileSend"])) $this->quesSound = $_POST["quesSoundFileSend"];			
		if(isset($_POST["audioIconSoundFileSend"])) $this->quesAudioIconSound = $_POST["audioIconSoundFileSend"];		
		if(isset($_POST["quesImageFileSend"])) $this->quesImage = $_POST["quesImageFileSend"];		
		if(isset($_POST["optionsNum"])) $this->totalOptions = $_POST["optionsNum"];		
		if(isset($_POST["commonOptSubTypeToSend"])) $this->optSubType = $_POST["commonOptSubTypeToSend"];		
		
		//if(isset($_POST["correctAnsToSend"])) $this->correctAnswer = $_POST["correctAnsToSend"];
		
		$this->misConception="";		
		$optArr=array('a','b','c','d','e','f');	
		for($i=1; $i<=6; $i++)
		{
				${"tmpOption_".$optArr[$i-1]}="";
				${"tmpSound_".$optArr[$i-1]} ="";
				
		}	
		for($i=1; $i<=$this->totalOptions; $i++)
		{				
			if($this->optSubType=="text"){
				if(isset($_POST["option".$i."textToSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."textToSend"];
			}else if($this->optSubType=="image"){
				if(isset($_POST["option".$i."imageFileSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."imageFileSend"];					
			}else if($this->optSubType=="textAndImage"||$this->optSubType=="textandimage"){
				if(isset($_POST["option".$i."textToSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."textToSend"];	
			}
			
			if(isset($_POST["option".$i."audioFileSend"])) ${"tmpSound_".$optArr[$i-1]} = $_POST["option".$i."audioFileSend"];
			if(isset($_POST["option".$i."descToSend"])) ${"tmpDesc_".$optArr[$i-1]} = $_POST["option".$i."descToSend"];
					
			
		}		
		
				
				
		$this->option_a = $tmpOption_a;
		$this->option_b = $tmpOption_b;
		$this->option_c = $tmpOption_c;
		$this->option_d = $tmpOption_d;
		$this->option_e = $tmpOption_e;
		$this->option_f = $tmpOption_f;
		$this->sound_a = $tmpSound_a;
		$this->sound_b = $tmpSound_b;
		$this->sound_c = $tmpSound_c;
		$this->sound_d = $tmpSound_d;
		$this->sound_e = $tmpSound_e;
		$this->sound_f = $tmpSound_f;
		
		
	}	
	
	function setMatchTypePostParam()
	{		
		
		if(isset($_POST["matchQuesSubTypeToSend"])) $this->quesSubType = $_POST["matchQuesSubTypeToSend"];
		if(isset($_POST["audioIconSoundFileSend"])) $this->quesAudioIconSound = $_POST["audioIconSoundFileSend"];		
		if(isset($_POST["quesSoundFileSend"])) $this->quesSound = $_POST["quesSoundFileSend"];			
		if(isset($_POST["quesImageFileSend"])) $this->quesImage = $_POST["quesImageFileSend"];	
		if(isset($_POST["matchOptSubTypeToSend"])) $this->optSubType = $_POST["matchOptSubTypeToSend"];		
		if(isset($_POST["correctAnsToSend"])) $this->correctAnswer = $_POST["correctAnsToSend"];
		
		$misConception="";		
			
		$optArr=array('a','b','c','d','e','f');
			
		for($i=1; $i<=6; $i++)
		{
			${"tmpOption_".$optArr[$i-1]}="";
		}
			
		for($i=1; $i<=$this->totalOptions; $i++)
		{	
			if(isset($_POST["option".$i."textToSend"])) ${"tmpOption_".$optArr[$i-1]} = $_POST["option".$i."textToSend"];
		}			
	
		$this->option_a = $tmpOption_a;
		$this->option_b = $tmpOption_b;
		$this->option_c = $tmpOption_c;
		$this->option_d = $tmpOption_d;
		$this->option_e = $tmpOption_e;
		$this->option_f = $tmpOption_f;

	
	}

	function setMakingWordTypePostParam()
	{		
		/*if(isset($_POST["wordMakingTextToSend"])) $this->wordMakingText         = $_POST["wordMakingTextToSend"];			
		if(isset($_POST["minwordLengthToSend"])) $this->minwordLength             = $_POST["minwordLengthToSend"];		
		if(isset($_POST["minwordsToSend"])) $this->minwords                       = $_POST["minwordsToSend"];		*/
		if(isset($_POST["tempMakingWordStrName"])) $this->tempMakingWordStrName   = $_POST["tempMakingWordStrName"];
		if(isset($_POST["tempMakingWordStrValue"])) $this->tempMakingWordStrValue = $_POST["tempMakingWordStrValue"];
		if(isset($_POST["generatedStemToSend"])) $this->generatedStem             = $_POST["generatedStemToSend"];		
	}
		
	function insertMcqValuesAndGetQcode()
	{	
		$this->submitdate=date("Y-m-d H:i:s");
		$this->lastModified=date("Y-m-d H:i:s");
		$this->questionmaker=$_SESSION['username'];
		$this->trail=$_SESSION['username'];
		$this->currentAlloted=$_SESSION['username'];
		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
		
			$stmt=$this->dbConnection->prepare("insert into questions(tagType, passageTypeName, passageID,topicID ,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				
			$fill_array = array($this->tagType,$this->passageType,$this->passageID,$this->topic,$this->qTemplate,$this->qType,$this->playTitle,$this->titleText,$this->titleSound,$this->quesSubType,$this->quesText,$this->quesSound,$this->quesImage,$this->quesAudioIconSound,$this->totalOptions,$this->optSubType,$this->option_a,$this->option_b,$this->option_c,$this->option_d,$this->option_e,$this->option_f,$this->sound_a,$this->sound_b,$this->sound_c,$this->sound_d,$this->sound_e,$this->sound_f,$this->desc_a,$this->desc_b,$this->desc_c,$this->desc_d,$this->desc_e,$this->desc_f,$this->explanation,$this->correctAnswer,$this->misConception ,$this->skillID,$this->subSkillID,$this->subSubSkillID,$this->msLevel,$this->difficulty,$this->status,$this->submitdate,$this->lastModified,$this->questionmaker,$this->trail,$this->currentAlloted);
				
			$stmt->execute($fill_array);
				
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$this->qcode=$this->getLastQcodeInserted();
				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo,false);
				$arr=array("success"=>1);
				echo $this->qcode;					
			}else{
				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function insertBlankValuesAndGetQcode()
	{	
		$this->submitdate=date("Y-m-d H:i:s");
		$this->lastModified=date("Y-m-d H:i:s");
		$this->questionmaker=$_SESSION['username'];
		$this->trail=$_SESSION['username'];
		$this->currentAlloted=$_SESSION['username'];
		try
		{ 
			//echo 't1';
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();				
			$stmt=$this->dbConnection->prepare("INSERT INTO questions(tagType, passageTypeName, passageID,topicID ,qTemplate,playTitle,qType,titleText,titleSound,quesText,quesSubType,quesAudioIconSound,explanation,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,questionmaker,trail,currentAlloted,credit)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				
			
				$fill_array = array($this->tagType,$this->passageType,$this->passageID, $this->topic,$this->qTemplate, $this->playTitle,$this->qType,$this->titleText,$this->titleSound,$this->quesText,$this->quesSubType,$this->quesAudioIconSound,$this->explanation,$this->misConception,$this->skillID,$this->subSkillID, $this->subSubSkillID, $this->msLevel,$this->difficulty, 0,date("Y-m-d H:i:s"),$this->curUserName,$this->curUserName,$this->curUserName,$this->credit);
				//echo $this->tagType,$this->passageType,$this->passageID, $this->topic,$this->qTemplate, $this->playTitle,$this->titleText,$this->titleSound,$this->quesText,$this->explanation,$this->misConception,$this->skillID,$this->subSkillID, $this->subSubSkillID, $this->msLevel,$this->difficulty,0,date("Y-m-d H:i:s"),$this->curUserName,$this->curUserName,$this->curUserName;
				$stmt->execute($fill_array);				
				$idQcode= $this->dbConnection->lastInsertId("qcode");
				$idQcode =trim($idQcode);
				
				if($this->qType != 'openEnded')
				{	
					$stmtDelParamArchiveQues=$this->dbConnection->prepare("DELETE FROM questionParametersArchive where qcode='$idQcode'");
					$stmtDelParamArchiveQues->execute();
					
					//echo "DELETE FROM questionParametersArchive where qcode='$idQcode'<br><br><br><br><br>";
					
					$stmtInsParamArchiveQues=$this->dbConnection->prepare("INSERT INTO questionParametersArchive (qcode, paramName, paramValue) SELECT qcode, paramName, paramValue from questionParameters where qcode='$idQcode'");
					 $stmtInsParamArchiveQues->execute();
					
					//echo "INSERT INTO questionParametersArchive (qcode, paramName, paramValue) SELECT qcode, paramName, paramValue from questionParameters where qcode='$idQcode'<br><br><br><br><br>";
					$stmtDelParamQues=$this->dbConnection->prepare("DELETE FROM questionParameters where qcode='$idQcode'");
					$stmtDelParamQues->execute();
					
					//echo "DELETE FROM questionParameters where qcode='$idQcode'<br><br><br><br><br>";
					//echo $_POST["paramNameArrToSend"].'<br><br><br><br><br>';
					//echo $_POST["paramValueArrToSend"].'<br><br><br><br><br>';
					
					$paramNameArr = explode("~",$this->paramNameStr);
					$paramValueArr = explode("~",$this->paramValueStr);	
					for($i=0; $i<count($paramNameArr); $i++)
					{
						$paramNameArr[$i]=trim($paramNameArr[$i]);
						$paramValueArr[$i]=trim($paramValueArr[$i]);
						$paramNameArr[$i]=stripslashes($paramNameArr[$i]);
						$paramValueArr[$i]=stripslashes($paramValueArr[$i]);
						$stmtInsParamQues=$this->dbConnection->prepare("INSERT INTO questionParameters (qcode, paramName, paramValue)values(?,?,?)");							
							$fill_array = array($idQcode,$paramNameArr[$i],$paramValueArr[$i]);
							$stmtInsParamQues->execute($fill_array);
							//echo $paramNameArr[$i].'='.$paramValueArr[$i].'<br>';
							
					}
				}
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				$this->qcode=$this->getLastQcodeInserted();
				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo,false);
				echo $idQcode;
		
			
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>'insertBlankValuesInDataBase Error',"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}
	
	function insertMatchValuesAndGetQcode()
	{	
		$this->submitdate=date("Y-m-d H:i:s");
		$this->lastModified=date("Y-m-d H:i:s");
		$this->questionmaker=$_SESSION['username'];
		$this->trail=$_SESSION['username'];
		$this->currentAlloted=$_SESSION['username'];
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
		
			$stmt=$this->dbConnection->prepare("insert into questions(tagType, passageTypeName, passageID,topicID ,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				
			$fill_array = array($this->tagType,$this->passageType,$this->passageID, $this->topic,$this->qTemplate,"", $this->playTitle,$this->titleText,$this->titleSound,$this->quesSubType,$this->quesText,$this->quesSound,$this->quesImage,$this->quesAudioIconSound,$this->totalOptions,$this->optSubType,$this->option_a,$this->option_b,$this->option_c,$this->option_d,$this->option_e,$this->option_f,"","","","","","","","","","","","",$this->explanation,$this->correctAnswer,$this->misConception,$this->skillID,$this->subSkillID, $this->subSubSkillID, $this->msLevel,$this->difficulty, 0,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$this->curUserName,$this->curUserName,$this->curUserName);
				
			$stmt->execute($fill_array);				
			$idQcode= $this->dbConnection->lastInsertId("qcode");
			echo trim($idQcode);
			
			if($stmt->rowCount()==1){				
				$this->dbConnection->commit();
				$this->qcode=$this->getLastQcodeInserted();
				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo,false);
				$arr=array("success"=>1);					
			}else{				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function insertSequenceValuesAndGetQcode()
	{	
	
		//remove desc and not required parameters to be inserted;
		$this->submitdate=date("Y-m-d H:i:s");
		$this->lastModified=date("Y-m-d H:i:s");
		$this->questionmaker=$_SESSION['username'];
		$this->trail=$_SESSION['username'];
		$this->currentAlloted=$_SESSION['username'];
		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
		
			$stmt=$this->dbConnection->prepare("insert into questions(tagType, passageTypeName, passageID,topicID ,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,explanation,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				
			$fill_array = array($this->tagType,$this->passageType,$this->passageID,$this->topic,$this->qTemplate,$this->qType,$this->playTitle,$this->titleText,$this->titleSound,$this->quesSubType,$this->quesText,$this->quesSound,$this->quesImage,$this->quesAudioIconSound,$this->totalOptions,$this->optSubType,$this->option_a,$this->option_b,$this->option_c,$this->option_d,$this->option_e,$this->option_f,$this->sound_a,$this->sound_b,$this->sound_c,$this->sound_d,$this->sound_e,$this->sound_f,$this->explanation,$this->misConception ,$this->skillID,$this->subSkillID,$this->subSubSkillID,$this->msLevel,$this->difficulty,$this->status,$this->submitdate,$this->lastModified,$this->questionmaker,$this->trail,$this->currentAlloted);
				
				
			$stmt->execute($fill_array);
				
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$this->qcode=$this->getLastQcodeInserted();
				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo,false);
				$arr=array("success"=>1);
				echo $this->qcode;					
			}else{
				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}

	function insertMakingWordValuesAndGetQcode()
	{	
		$this->submitdate=date("Y-m-d H:i:s");
		$this->lastModified=date("Y-m-d H:i:s");
		$this->questionmaker=$_SESSION['username'];
		$this->trail=$_SESSION['username'];
		$this->currentAlloted=$_SESSION['username'];
		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
		
			$stmt=$this->dbConnection->prepare("insert into questions(tagType, passageTypeName, passageID,topicID ,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesImage,totalOptions,optSubType,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				
			$fill_array = array($this->tagType,$this->passageType,$this->passageID,$this->topic,$this->qTemplate,$this->qType,$this->playTitle,$this->titleText,$this->titleSound,$this->quesSubType,$this->generatedStem,$this->quesImage,$this->totalOptions,$this->optSubType,$this->correctAnswer,$this->misConception ,$this->skillID,$this->subSkillID,$this->subSubSkillID,$this->msLevel,$this->difficulty,$this->status,$this->submitdate,$this->lastModified,$this->questionmaker,$this->trail,$this->currentAlloted);
				
			$stmt->execute($fill_array);
				
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$this->qcode=$this->getLastQcodeInserted();

				if($this->qcode != '')
				{
					$paramMakingWordStrNameArr  = explode("~",$this->tempMakingWordStrName);
					$paramMakingWordStrValueArr = explode("~",$this->tempMakingWordStrValue);	

					$paramMakingWordStrNameArr  = array_filter($paramMakingWordStrNameArr);
					$paramMakingWordStrValueArr = array_filter($paramMakingWordStrValueArr);

					for($i=0; $i<count($paramMakingWordStrNameArr); $i++)
					{
						$paramMakingWordStrNameArr[$i]  = trim($paramMakingWordStrNameArr[$i]);
						$paramMakingWordStrValueArr[$i] = trim($paramMakingWordStrValueArr[$i]);
						$paramMakingWordStrNameArr[$i]  = addslashes($paramMakingWordStrNameArr[$i]);
						$paramMakingWordStrValueArr[$i] = addslashes($paramMakingWordStrValueArr[$i]);
						$paramMakingWordStrNameArr[$i]  = stripslashes($paramMakingWordStrNameArr[$i]);
						$paramMakingWordStrValueArr[$i] = stripslashes($paramMakingWordStrValueArr[$i]);
						

						$stmtInsParamQuesMakingWords=$this->dbConnection->prepare("INSERT INTO questionParameters (qcode, paramName, paramValue)values(?,?,?)");							
						$fill_array_makingword = array($this->qcode,$paramMakingWordStrNameArr[$i],$paramMakingWordStrValueArr[$i]);
						$stmtInsParamQuesMakingWords->execute($fill_array_makingword);
					}
				}

				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo,false);
				$arr=array("success"=>1);
				echo $this->qcode;					
			}else{
				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
	function updateMcqValuesInDataBase()
	{		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			
			$stmt=$this->dbConnection->prepare("UPDATE questions SET tagType='$this->tagType', passageTypeName='$this->passageType', passageID='$this->passageID', topicID='$this->topic', qTemplate='$this->qTemplate', qType='$this->qType',playTitle='$this->playTitle',titleText='$this->titleText',titleSound='$this->titleSound',quesSubType='$this->quesSubType',quesText='$this->quesText',quesSound='$this->quesSound',quesImage='$this->quesImage',quesAudioIconSound='$this->quesAudioIconSound',totalOptions='$this->totalOptions',optSubType='$this->optSubType',option_a='$this->option_a',option_b='$this->option_b',option_c='$this->option_c',option_d='$this->option_d',option_e='$this->option_e',option_f='$this->option_f',sound_a='$this->sound_a',sound_b='$this->sound_b',sound_c='$this->sound_c',sound_d='$this->sound_d',sound_e='$this->sound_e',sound_f='$this->sound_f',desc_a='$this->desc_a',desc_b='$this->desc_b',desc_c='$this->desc_c',desc_d='$this->desc_d',desc_e='$this->desc_e',desc_f='$this->desc_f',explanation='$this->explanation',correctAnswer='$this->correctAnswer', misConception='$this->misConception', skillID='$this->skillID', subSkillID='$this->subSkillID', subSubSkillID='$this->subSubSkillID', msLevel='$this->msLevel', difficulty='$this->difficulty' WHERE qcode='$this->qcode' ");
			$stmt->execute();
			
			$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
			$getQcodeDataSql->execute();
			$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);
			$this->status=$qcodeData['status'];
			$this->submitdate=$qcodeData['submitdate'];
			$this->questionmaker=$qcodeData['questionmaker'];
			$this->trail=$qcodeData['trail'];
			$this->currentAlloted=$qcodeData['currentAlloted'];
			$this->lastModified=$qcodeData['lastModified'];
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				$varsionNo=$this->getQuestionVersion($this->qcode,1);
				$this->modifyQuestionVersion($varsionNo,false);
				echo json_encode($arr);
			}else{
				//$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}
	
	
	function updateBlankValuesInDataBase()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			
			 $stmt=$this->dbConnection->prepare("UPDATE questions SET tagType='$this->tagType', passageTypeName='$this->passageType', passageID='$this->passageID', topicID='$this->topic', qTemplate='$this->qTemplate',qType='$this->qType' ,playTitle='$this->playTitle',titleText='$this->titleText',titleSound='$this->titleSound',quesText='$this->quesText',quesSubType='$this->quesSubType',quesAudioIconSound='$this->quesAudioIconSound',explanation='$this->explanation',misConception='$this->misConception', skillID='$this->skillID', subSkillID='$this->subSkillID', subSubSkillID='$this->subSubSkillID', msLevel='$this->msLevel', difficulty='$this->difficulty', credit='$this->credit' WHERE qcode='$this->qcode' ");
			
			
			$stmt->execute();		
			
			$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
			$getQcodeDataSql->execute();
			$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);
			$this->status=$qcodeData['status'];
			$this->submitdate=$qcodeData['submitdate'];
			$this->questionmaker=$qcodeData['questionmaker'];
			$this->trail=$qcodeData['trail'];
			$this->currentAlloted=$qcodeData['currentAlloted'];
			$this->lastModified=$qcodeData['lastModified'];
			
			if($this->qType != 'openEnded')
			{	
				$stmtDelParamArchiveQues=$this->dbConnection->prepare("DELETE FROM questionParametersArchive where qcode='$this->qcode'");
				$stmtDelParamArchiveQues->execute();
				
				$stmtInsParamArchiveQues=$this->dbConnection->prepare("INSERT INTO questionParametersArchive (qcode, paramName, paramValue) SELECT qcode, paramName, paramValue from questionParameters where qcode='$this->qcode'");
				 $stmtInsParamArchiveQues->execute();
				
				$stmtDelParamQues=$this->dbConnection->prepare("DELETE FROM questionParameters where qcode='$this->qcode'");
				$stmtDelParamQues->execute();
				
				$paramNameArr = explode("~",$this->paramNameStr);
				$paramValueArr = explode("~",$this->paramValueStr);	
				for($i=0; $i<count($paramNameArr); $i++)
				{
					$paramNameArr[$i]=trim($paramNameArr[$i]);
					$paramValueArr[$i]=trim($paramValueArr[$i]);
					$paramNameArr[$i]=stripslashes($paramNameArr[$i]);
					$paramValueArr[$i]=stripslashes($paramValueArr[$i]);
					$stmtInsParamQues=$this->dbConnection->prepare("INSERT INTO questionParameters (qcode, paramName, paramValue)values(?,?,?)");
						$fill_array = array($this->qcode,$paramNameArr[$i],$paramValueArr[$i]);
						$stmtInsParamQues->execute($fill_array);
						
						
				}
			}
			$this->dbConnection->commit();
			$arr=array("success"=>1);
			$varsionNo=$this->getQuestionVersion($this->qcode,1);
			$this->modifyQuestionVersion($varsionNo,false);
			echo json_encode($arr);
			
			
		}catch(PDOException $pe)
		{
			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}
	
	function updateMatchValuesInDataBase()
	{	
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();
		
			$stmt=$this->dbConnection->prepare("UPDATE questions SET tagType='$this->tagType', passageTypeName='$this->passageType', passageID='$this->passageID', topicID='$this->topic', qTemplate='$this->qTemplate',qType='',playTitle='$this->playTitle',titleText='$this->titleText',titleSound='$this->titleSound',quesSubType='$this->quesSubType',quesText='$this->quesText',quesSound='$this->quesSound',quesImage='$this->quesImage',quesAudioIconSound='$this->quesAudioIconSound',totalOptions='$this->totalOptions',optSubType='$this->optSubType',option_a='$this->option_a',option_b='$this->option_b',option_c='$this->option_c',option_d='$this->option_d',option_e='$this->option_e',option_f='$this->option_f',sound_a='',sound_b='',sound_c='',sound_d='',sound_e='',sound_f='',desc_a='',desc_b='',desc_c='',desc_d='',desc_e='',desc_f='',explanation='$this->explanation',correctAnswer='$this->correctAnswer', misConception='$this->misConception', skillID='$this->skillID', subSkillID='$this->subSkillID', subSubSkillID='$this->subSubSkillID', msLevel='$this->msLevel', difficulty='$this->difficulty' WHERE qcode='$this->qcode' ");
	
			$stmt->execute();	

			$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
			$getQcodeDataSql->execute();
			$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);
			$this->status=$qcodeData['status'];
			$this->submitdate=$qcodeData['submitdate'];
			$this->questionmaker=$qcodeData['questionmaker'];
			$this->trail=$qcodeData['trail'];
			$this->currentAlloted=$qcodeData['currentAlloted'];
			$this->lastModified=$qcodeData['lastModified'];
			
			$idQcode= $this->dbConnection->lastInsertId("qcode");
			echo trim($idQcode);
			if($stmt->rowCount()==1){				
				$this->dbConnection->commit();
				$varsionNo=$this->getQuestionVersion($this->qcode,1);
				$this->modifyQuestionVersion($varsionNo,false);
				$arr=array("success"=>1);					
			}else{				
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{			
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			
			echo json_encode($arr);
			exit;
		}
	}
	function updateSequenceValuesInDataBase()
	{		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			
			$stmt=$this->dbConnection->prepare("UPDATE questions SET tagType='$this->tagType', passageTypeName='$this->passageType', passageID='$this->passageID', topicID='$this->topic', qTemplate='$this->qTemplate', qType='$this->qType',playTitle='$this->playTitle',titleText='$this->titleText',titleSound='$this->titleSound',quesSubType='$this->quesSubType',quesText='$this->quesText',quesSound='$this->quesSound',quesImage='$this->quesImage',quesAudioIconSound='$this->quesAudioIconSound',totalOptions='$this->totalOptions',optSubType='$this->optSubType',option_a='$this->option_a',option_b='$this->option_b',option_c='$this->option_c',option_d='$this->option_d',option_e='$this->option_e',option_f='$this->option_f',sound_a='$this->sound_a',sound_b='$this->sound_b',sound_c='$this->sound_c',sound_d='$this->sound_d',sound_e='$this->sound_e',sound_f='$this->sound_f',explanation='$this->explanation', misConception='$this->misConception', skillID='$this->skillID', subSkillID='$this->subSkillID', subSubSkillID='$this->subSubSkillID', msLevel='$this->msLevel', difficulty='$this->difficulty' WHERE qcode='$this->qcode' ");
			$stmt->execute();
			
			$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
			$getQcodeDataSql->execute();
			$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);
			$this->status=$qcodeData['status'];
			$this->submitdate=$qcodeData['submitdate'];
			$this->questionmaker=$qcodeData['questionmaker'];
			$this->trail=$qcodeData['trail'];
			$this->currentAlloted=$qcodeData['currentAlloted'];
			$this->lastModified=$qcodeData['lastModified'];
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				$varsionNo=$this->getQuestionVersion($this->qcode,1);
				$this->modifyQuestionVersion($varsionNo,false);
				echo json_encode($arr);
			}else{
				//$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}

	function updateMakingWordInDataBase()
	{		
		
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	


			$generatedStem = stripslashes($this->generatedStem);
			$generatedStem = strip_tags($generatedStem);

			$stmt=$this->dbConnection->prepare("UPDATE questions SET tagType='$this->tagType', passageTypeName='$this->passageType', passageID='$this->passageID', topicID='$this->topic', qTemplate='$this->qTemplate', qType='$this->qType',playTitle='$this->playTitle',titleText='$this->titleText',titleSound='$this->titleSound',quesSubType='$this->quesSubType',quesText='".$generatedStem."',quesSound='$this->quesSound',quesImage='$this->quesImage',totalOptions='$this->totalOptions',optSubType='$this->optSubType',explanation='$this->explanation',correctAnswer='$this->correctAnswer', misConception='$this->misConception', skillID='$this->skillID', subSkillID='$this->subSkillID', subSubSkillID='$this->subSubSkillID', msLevel='$this->msLevel', difficulty='$this->difficulty' WHERE qcode='$this->qcode' ");
			$stmt->execute();
			
			$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
			$getQcodeDataSql->execute();
			$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);
			$this->status=$qcodeData['status'];
			$this->submitdate=$qcodeData['submitdate'];
			$this->questionmaker=$qcodeData['questionmaker'];
			$this->trail=$qcodeData['trail'];
			$this->currentAlloted=$qcodeData['currentAlloted'];
			$this->lastModified=$qcodeData['lastModified'];

			//insert/update in questionParameters table.

			$stmtDelParamArchiveQuesMakingWord=$this->dbConnection->prepare("DELETE FROM questionParametersArchive where qcode='$this->qcode'");
			$stmtDelParamArchiveQuesMakingWord->execute();
			
			$stmtInsParamArchiveQuesMakingWord=$this->dbConnection->prepare("INSERT INTO questionParametersArchive (qcode, paramName, paramValue) SELECT qcode, paramName, paramValue from questionParameters where qcode='$this->qcode'");
			 $stmtInsParamArchiveQuesMakingWord->execute();
			
			$stmtDelParamQuesMakingWord=$this->dbConnection->prepare("DELETE FROM questionParameters where qcode='$this->qcode'");
			$stmtDelParamQuesMakingWord->execute();

			$paramMakingWordStrNameArr  = explode("~",$this->tempMakingWordStrName);
			$paramMakingWordStrValueArr = explode("~",$this->tempMakingWordStrValue);	

			$paramMakingWordStrNameArr  = array_filter($paramMakingWordStrNameArr);
			$paramMakingWordStrValueArr = array_filter($paramMakingWordStrValueArr);
			

			for($i=0; $i<count($paramMakingWordStrNameArr); $i++)
			{
				$paramMakingWordStrNameArr[$i]  = trim($paramMakingWordStrNameArr[$i]);
				$paramMakingWordStrValueArr[$i] = trim($paramMakingWordStrValueArr[$i]);
				$paramMakingWordStrNameArr[$i]  = addslashes($paramMakingWordStrNameArr[$i]);
				$paramMakingWordStrValueArr[$i] = addslashes($paramMakingWordStrValueArr[$i]);
				$paramMakingWordStrNameArr[$i]  = stripslashes($paramMakingWordStrNameArr[$i]);
				$paramMakingWordStrValueArr[$i] = stripslashes($paramMakingWordStrValueArr[$i]);


				$stmtUpParamQuesMakingWords=$this->dbConnection->prepare("INSERT INTO questionParameters (qcode, paramName, paramValue)values(?,?,?)");							
				$fill_array_makingwordUpdate = array($this->qcode,$paramMakingWordStrNameArr[$i],$paramMakingWordStrValueArr[$i]);
				$stmtUpParamQuesMakingWords->execute($fill_array_makingwordUpdate);
			}
			
			if($stmt->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				$varsionNo=$this->getQuestionVersion($this->qcode,1);
				$this->modifyQuestionVersion($varsionNo,false);
				echo json_encode($arr);
			}else{
				//$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
			
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}
	}
	
	public function getLastQcodeInserted()
	{
		$getlastinsqcodesql=$this->dbConnection->prepare("select qcode from questions where questionmaker='".$_SESSION['username']."' order by qcode desc");
		$getlastinsqcodesql->execute();
		$row =$getlastinsqcodesql->fetch();
		$qcode=$row['qcode'];
		return $qcode;
	}
	
	public function assignQuesVerReviewers()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			
			$assignQuesVerReviewersSql=$this->dbConnection->prepare("UPDATE questionVersion SET first_alloted='$this->first_alloted',second_alloted='$this->second_alloted' where qcode='$this->qcode'");
			
			$assignQuesVerReviewersSql->execute();
			$this->dbConnection->commit();
		}
		catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>'UpdateAssignQuestionVersionReviewer Error',"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
	
   public function setReviewerAnswer()
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			
			
			if($this->status==1){
				$setReviewerAnsSql=$this->dbConnection->prepare("UPDATE questions SET firstReviewerAnswer='$this->reviewerAnswer' WHERE qcode='$this->qcode'");
			}else{
				$setReviewerAnsSql=$this->dbConnection->prepare("UPDATE questions SET secondReviewerAnswer='$this->reviewerAnswer' WHERE qcode='$this->qcode'");
			}
			
			$setReviewerAnsSql->execute();
			if($setReviewerAnsSql->rowCount()==1){
				$this->dbConnection->commit();
				$arr=array("success"=>1);
				//$stmt=$db->prepare("select * from questions WHERE qcode='$qcode'");
				//$stmt->execute();
				//$qcodeDataArr=$stmt->fetch();
				$versionNo=$this->getQuestionVersion($this->qcode,0);
				$this->modifyQuestionVersion($versionNo);
				
				//echo $arr;
			}else{
				$this->dbConnection->rollBack();
				$arr=array("success"=>0);				
				echo json_encode($arr);
			}
		}catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>2,"exception"=>"exception ".$pe);
			echo json_encode($arr);
			//echo $("#showMsg").append('<br>'.json_encode($arr).'</br>');			
			exit;
		}	
    }
	  
    public function addIgnoreWordsForQuestion($ignoreWords)
	{
	
		$curdate = date('Y-m-d H:i:s');
		$ignoreWordsArr = explode('~~',$ignoreWords);
		
	
		foreach($ignoreWordsArr as $key=>$val)
		{
			if($val!="" && $val!=null)
			{
				
				$ignoreWordContentID=$this->dbConnection->prepare("select contentID,word from ignoreWordsList where word='".$val."' and contentType='question' order by sno desc limit 1");
				
				
				$ignoreWordContentID->execute();
				$querryResultArr=$ignoreWordContentID->fetch();
				if($ignoreWordContentID->rowCount() == 1)
				{	
					if($querryResultArr['contentID'] == -1)
					{
						$updateIgnoreWords=$this->dbConnection->prepare("Update ignoreWordsList set contentID=".$this->qcode." where contentType='question' and word='".$val."'");
						$updateIgnoreWords->execute();			
						
					}
					else if($querryResultArr['contentID'] > 0 && $querryResultArr['contentID']!=$this->qcode && $querryResultArr['contentID']!=$val)
					{
						/*$updateIgnoreWords=$this->dbConnection->prepare("Update ignoreWordsList set contentID = CONCAT(contentID,',',".$this->qcode.") where word=".$val);
						$updateIgnoreWords->execute();	*/
						
						$ignoreWordQuery=$this->dbConnection->prepare("select contentID,word from ignoreWordsList where word='".$val."' and contentType='question' and contentID=".$this->qcode);
						$ignoreWordQuery->execute();
						$querryResArr=$ignoreWordQuery->fetch();
						if($ignoreWordQuery->rowCount() == 0)
						{	
								
							$insertQuestionVersion=$this->dbConnection->prepare("Insert into ignoreWordsList(word,contentID,contentType,productName,addedBy,addedOn) values(?,?,?,?,?,?)");
							$insertValues = array($val,$this->qcode,'question','MSE',$_SESSION['username'],$curdate);
							
							$insertQuestionVersion->execute($insertValues);
						}	
					
						
						
						
					}	
				}
			}
		}
	}
	
	public function getQuestionVersion($qcode,$upgradeVersionFlag)
	{
		$this->qcode=$qcode;
		if($this->ignoreWords!="")
		$this->addIgnoreWordsForQuestion($this->ignoreWords);
		
		
		$getQcodeVersionSql=$this->dbConnection->prepare("select a.*,b.*,a.status as questionstatus from questions a, questionVersion b where a.qcode='$qcode' and a.qcode=b.qcode order by b.qcodeVersionNo DESC LIMIT 1");
		$getQcodeVersionSql->execute();
		$qcodeVersionData=$getQcodeVersionSql->fetch();
		
		if($getQcodeVersionSql->rowCount() == 1)
		{
			if($upgradeVersionFlag)
			{	
				$qcodeVersionData['qcodeVersionNo']+=1;
			}
			return $qcodeVersionData['qcodeVersionNo'];
		}
		else
		{
			return false;
		}	
	}
	
	function modifyQuestionVersion($versionNo,$newEntry)
	{
		try
		{ 
			$this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->dbConnection->beginTransaction();	
			$startDate = date("Y-m-d H:i:s");
			
			$qcodeDataArr = array($this->qcode,$this->tagType,$this->passageType,$this->passageID,$this->topic,$this->qTemplate,$this->qType,$this->playTitle,$this->titleText,$this->titleSound,$this->quesSubType,stripslashes($this->quesText),$this->quesSound,$this->quesImage,$this->quesAudioIconSound,$this->totalOptions,$this->optSubType,stripslashes($this->option_a),stripslashes($this->option_b),stripslashes($this->option_c),stripslashes($this->option_d),stripslashes($this->option_e),stripslashes($this->option_f),$this->sound_a,$this->sound_b,$this->sound_c,$this->sound_d,$this->sound_e,$this->sound_f,stripslashes($this->desc_a),stripslashes($this->desc_b),stripslashes($this->desc_c),stripslashes($this->desc_d),stripslashes($this->desc_e),stripslashes($this->desc_f),stripslashes($this->explanation),$this->correctAnswer,$this->misConception ,$this->skillID,$this->subSkillID,$this->subSubSkillID,$this->msLevel,$this->difficulty,$this->status,$this->submitdate,$this->lastModified,$this->questionmaker,$this->trail,$this->currentAlloted,$this->credit);
			
			if(!$versionNo||$newEntry)     
			// very first version entry sql for the qcode
			{
				if(!$versionNo){
					$versionNo=1;
				}				
				$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
				$getQcodeDataSql->execute();
				
				
				if($getQcodeDataSql->rowCount()==1)
				{
					$qcodeDataArr=$getQcodeDataSql->fetch(PDO::FETCH_NUM);
					$quesVersionDataArr=array_merge($qcodeDataArr,array($versionNo,$startDate,"",$_SESSION['username'])); 
					
					$insertQuestionVersion=$this->dbConnection->prepare("insert into questionVersion (qcode,tagType,passageTypeName,passageID,topicID,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,credit,questionmaker,currentAlloted,first_alloted,second_alloted,firstReviewerAnswer,secondReviewerAnswer,liveOn,status,trail,submitdate,lastModified,lastModifiedBy,qcodeVersionNo,startDate,endDate,modifiedBy)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
					
					$insertQuestionVersion->execute($quesVersionDataArr);
				
				}		
				else
				{		
					$quesVersionDataArr=array_merge($qcodeDataArr,array($versionNo,$startDate,"",$_SESSION['username'])); 
					
					$insertQuestionVersion=$this->dbConnection->prepare("insert into questionVersion (qcode,tagType,passageTypeName,passageID,topicID,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted,credit,qcodeVersionNo,startDate,endDate,modifiedBy)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
					
					$insertQuestionVersion->execute($quesVersionDataArr);
				}
			}
			else
			// operations require after the very first entry of the qcode. 
			{
				$getQcodeVersionDataSql=$this->dbConnection->prepare("select * from questionVersion where qcode=".$this->qcode ." and qcodeVersionNo=".$versionNo);
				$qcodeVersionData=$getQcodeVersionDataSql->execute();
				
				$getQcodeDataSql=$this->dbConnection->prepare("select * from questions where qcode=".$this->qcode);
				$getQcodeDataSql->execute();
				$qcodeData=$getQcodeDataSql->fetch(PDO::FETCH_ASSOC);

				if($getQcodeVersionDataSql->rowCount()==1)  
				//if existing quesVersion entry need to be updated.(i.e= if question status updates)				
				{
					
					// Handle first reviewer answer/second reviewer answer entry in question version table
					
					if($qcodeData["firstReviewerAnswer"]==null && $qcodeData["secondReviewerAnswer"]==null)
						$reviewerQryText=",";
					else if($qcodeData["firstReviewerAnswer"]!=null && $qcodeData["secondReviewerAnswer"]==null)
						$reviewerQryText=",firstReviewerAnswer='".$qcodeData['firstReviewerAnswer']."',";
					else
						$reviewerQryText=",firstReviewerAnswer='".$qcodeData['firstReviewerAnswer']."',secondReviewerAnswer='".$qcodeData['secondReviewerAnswer']."',";
					
					
					if($qcodeData['status'] == 6) // Update Live On field in question version table when status is 6
						$liveOnQryText=", liveOn='".$qcodeData['liveOn']."'";
					else
						$liveOnQryText="";
					
					$updatequesVersionStatusSql=$this->dbConnection->prepare("UPDATE questionVersion SET status=".$qcodeData['status'].",currentAlloted='".$qcodeData['currentAlloted']."',trail='".$qcodeData['trail']."'".$reviewerQryText."submitdate='".$qcodeData['submitdate']."',credit='".$qcodeData['credit']."',first_alloted='".$qcodeData['first_alloted']."',msLevel='".$qcodeData['msLevel']."',second_alloted='".$qcodeData['second_alloted']."'".$liveOnQryText." WHERE qcode=".$this->qcode ." and qcodeVersionNo=".$versionNo);
					
					$updatequesVersionStatusSql->execute();
				}
				else     									
				// if new questionversion entry need to entered.(i.e= if questiondata updates)
				{
					$updateQuesPreVerendDateSql=$this->dbConnection->prepare("UPDATE questionVersion SET endDate='".date('Y-m-d H:i:s')."' WHERE qcode=".$this->qcode ." ORDER BY qcodeVersionNo desc LIMIT 1");
					$updateQuesPreVerendDateSql->execute();
					
					$quesVersionDataArr=array_merge($qcodeDataArr,array($this->first_alloted,$this->second_alloted,$this->firstReviewerAnswer,$this->secondReviewerAnswer,$versionNo,$startDate,"",$_SESSION['username']));
					
					$insertQuestionVersion=$this->dbConnection->prepare("insert into questionVersion (qcode,tagType,passageTypeName,passageID,topicID,qTemplate,qType,playTitle,titleText,titleSound,quesSubType,quesText,quesSound,quesImage,quesAudioIconSound,totalOptions,optSubType,option_a,option_b,option_c,option_d,option_e,option_f,sound_a,sound_b,sound_c,sound_d,sound_e,sound_f,desc_a,desc_b,desc_c,desc_d,desc_e,desc_f,explanation,correctAnswer,misConception,skillID,subSkillID,subSubSkillID,msLevel,difficulty,status,submitdate,lastModified,questionmaker,trail,currentAlloted,credit,first_alloted,second_alloted,firstReviewerAnswer,secondReviewerAnswer,qcodeVersionNo,startDate,endDate,modifiedBy)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
					$insertQuestionVersion->execute($quesVersionDataArr);
				}	
			}
			
			$this->dbConnection->commit();
		}
		catch(PDOException $pe)
		{
			$this->dbConnection->rollBack();
			$arr=array("success"=>'modifyQuestionVersion Error',"exception"=>"exception ".$pe);
			echo json_encode($arr);
			exit;
		}
	}
}
