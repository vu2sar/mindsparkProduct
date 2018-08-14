<?php
class daTest
{
	var $qcode;
	var $questionStem;
	var $optionA;
	var $optionB;
	var $optionC;
	var $optionD;
	var $display_answerA;
	var $display_answerB;
	var $display_answerC;
	var $display_answerD;
	var $subDifficultyLevel;
	var $quesType;
	var $correctAnswer;
	var $displayAnswer;
	var $hint1;
	var $hint2;
	var $hint3;
	var $hint4;
	var $hintAvailable;
	var $clusterCode;
	var $dynamic;
	var $eeIcon;
	var $quesVoiceOver;
	var $ansVoiceOver;
	var $dropDownAns;
	var $fracBoxAns;
	var $dynamicParams;
	var $correctAnsForDisplay;
	var $context;
	var $country;
	var $noOfTrials;
	var $comments;
	var $quesCondition1;
	var $quesCondition2;
	var $quesCondition3;
	var $quesCondition4;
	var $quesCondition5;
	var $quesCondition6;
	var $quesCondition7;
	var $quesCondition8;
	var $quesAction1;
	var $quesAction2;
	var $quesAction3;
	var $quesAction4;
	var $quesAction5;
	var $quesAction6;
	var $quesAction7;
	var $quesAction8;
	var $conditionAvailable;
	var $userDaResponse;
	var $flagrecords;
	var $DaTimeRemaining;


	function daTest($qcode)
	{
		$query = "SELECT qcode, question, optiona, optionb, optionc, optiond,
				  correct_answer, qtype, a.group_id, b.group_text, b.groupname
			      FROM   educatio_educat.da_questions a LEFT JOIN educatio_educat.da_groupMaster b ON b.group_id=a.group_id
			      WHERE  qcode=$qcode";

		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$this->qcode          = $qcode;
		if ($line['group_text'])
			$line['question']=	$line['group_text'].'<br><br>'.$line['question'];

		if (strpos($line['question'],'images') !== false) {
			$question = 	str_replace("http://www.educationalinitiatives.com/detailed_assessment/images/","images/",$line['question']);
			$question = 	str_replace("http://educationalinitiatives.com/detailed_assessment/images/","images/",$question);
			if(SERVER_TYPE=='LOCAL')
				$question = str_replace("images/", "/mindspark/detailed_assessment/images/", $question);
			else	
				$question = str_replace("images/","http://www.educationalinitiatives.com/detailed_assessment/images/",$question);
		}
		else
			$question = $line['question'];	

		if (strpos($line['optiona'],'images') !== false) {
			$optiona = 	str_replace("http://www.educationalinitiatives.com/detailed_assessment/images/","images/",$line['optiona']);
			$optiona = 	str_replace("http://educationalinitiatives.com/detailed_assessment/images/","images/",$optiona);
			if(SERVER_TYPE=='LOCAL')
				$optiona = str_replace("images/", "/mindspark/detailed_assessment/images/", $optiona);
			else	
				$optiona = 	str_replace("images/","http://www.educationalinitiatives.com/detailed_assessment/images/",$optiona);
		}
		else
			$optiona = $line['optiona'];

		if (strpos($line['optionb'],'images') !== false) {
			$optionb = 	str_replace("http://www.educationalinitiatives.com/detailed_assessment/images/","images/",$line['optionb']);
			$optionb = 	str_replace("http://educationalinitiatives.com/detailed_assessment/images/","images/",$optionb);
			if(SERVER_TYPE=='LOCAL')
				$optionb = str_replace("images/", "/mindspark/detailed_assessment/images/", $optionb);
			else	
				$optionb = 	str_replace("images/","http://www.educationalinitiatives.com/detailed_assessment/images/",$optionb);
		}
		else
			$optionb = $line['optionb'];

		if (strpos($line['optionc'],'images') !== false) {
			$optionc = 	str_replace("http://www.educationalinitiatives.com/detailed_assessment/images/","images/",$line['optionc']);
			$optionc = 	str_replace("http://educationalinitiatives.com/detailed_assessment/images/","images/",$optionc);
			if(SERVER_TYPE=='LOCAL')
				$optionc = str_replace("images/", "/mindspark/detailed_assessment/images/", $optionc);
			else	
				$optionc = 	str_replace("images/","http://www.educationalinitiatives.com/detailed_assessment/images/",$optionc);
		}
		else
			$optionc = $line['optionc'];

		if (strpos($line['optiond'],'images') !== false) {
			$optiond = 	str_replace("http://www.educationalinitiatives.com/detailed_assessment/images/","images/",$line['optiond']);
			$optiond = 	str_replace("http://educationalinitiatives.com/detailed_assessment/images/","images/",$optiond);
			if(SERVER_TYPE=='LOCAL')
				$optiond = str_replace("images/", "/mindspark/detailed_assessment/images/", $optiond);
			else
				$optiond = 	str_replace("images/","http://www.educationalinitiatives.com/detailed_assessment/images/",$optiond);
		}
		else
			$optiond = $line['optiond'];

		$this->questionStem   = $question;
		$this->optionA        = $optiona;
		$this->optionB        = $optionb;
		$this->optionC        = $optionc;
		$this->optionD        = $optiond;
		$this->correctAnswer  = $line['correct_answer'];
		$this->quesType       = $line['qtype'];
	}

	function getAnswer()
	{
		$userID = $_SESSION['userID'];
		$daPaperCode = $_SESSION['daPaperCode'];
		$qcode = $this->qcode;
		$Daquery = "SELECT A from da_questionAttemptDetails WHERE qcode=$qcode and userID = $userID and paperCode = '$daPaperCode' ";
		$Daresult = mysql_query($Daquery);
		$Daline   = mysql_fetch_array($Daresult);
		if(count($Daline) > 0)
			$this->userDaResponse = $Daline['A'];

		return $this->userDaResponse;

	}

	function getDaTimeRemaining()
	{
		$userID = $_SESSION['userID'];
		$qcode = $this->qcode;
		$daPaperCode = $_SESSION['daPaperCode'];
		$Daquery = "SELECT spendTime from da_questionTestStatus WHERE userID = $userID and paperCode = '$daPaperCode' ";
		$Daresult = mysql_query($Daquery);
		$Daline   = mysql_fetch_array($Daresult);
		if(count($Daline) > 0)
			$this->DaTimeRemaining = $Daline['spendTime'];

		return $this->DaTimeRemaining;
	}

	function isDynamic()
	{
		return $this->dynamic;
	}

	function generateQuestion($mode="", $generatedParams="")
	{
		$questionArray       = generateDynamicQues($this->questionStem, $this->optionA, $this->optionB, $this->optionC, $this->optionD, $this->correctAnswer, $this->displayAnswer, $this->quesType, $generatedParams, $mode, $this->display_answerA, $this->display_answerB, $this->display_answerC, $this->display_answerD);
		$this->questionStem  = Plural_to_Singular($questionArray[0]);
		$this->optionA       = stripslashes($questionArray[1]);
		$this->optionB       = stripslashes($questionArray[2]);
		$this->optionC       = stripslashes($questionArray[3]);
		$this->optionD       = stripslashes($questionArray[4]);
		$this->correctAnswer = stripslashes($questionArray[5]);
		$this->displayAnswer = stripslashes($questionArray[6]);
		$this->dynamicParams = stripslashes($questionArray[7]);
		$this->display_answerA	=	stripslashes($questionArray[8]);
		$this->display_answerB	=	stripslashes($questionArray[9]);
		$this->display_answerC	=	stripslashes($questionArray[10]);
		$this->display_answerD	=	stripslashes($questionArray[11]);
		$this->processAnsForDisplay();
	}

	function getQuestion()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->questionStem,"images","Q", $context);
	}

	function getQuestionForDisplay($eeresponse="")
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";

		if($eeresponse != '')
		{
			$data = $eeresponse;

			$data =  str_replace('"<', '&quot;<', $data);
			$data =  str_replace('>"', '>&quot;', $data);
			$data =  str_replace('"', '\&quot;', $data);

			$iframeStr =  '<iframe width="680px" height="550px" class="cFrame" onload="loadConstrFrame(this);" data-response="'.$data.'" src="'.HTML_QUESTIONS_FOLDER.'/GEO/GEO_constr/src/index.html"></iframe>';

			$question = orig_to_html($this->questionStem,"images","Q", $context);
			$question = preg_replace('/<iframe.*?\/iframe>/i','', $question);
			$question .= $iframeStr;
		}else{
			$question = orig_to_html($this->questionStem,"images","Q", $context);
		}

		
		if(($this->quesType=="Open Ended" || $this->eeIcon == "1") && $eeresponse!="")
		{
			$eeResponseArray = explode("@$*@data",$eeresponse);
			$iframeStr = '<iframe src="equationeditor/eqeditor.htm" width="714px" height="406px" id="eqeditor" style="margin:0px;border:0px" class="openEnded"></iframe>';
			$replaceStr = '<div style="width:714px; height:406px;margin:0px;border:0px; background-color:white;border:1px solid black;padding:5px; background-image:url(data'.$eeResponseArray[1].')" id="eqeditor">';
			$replaceStr .= $eeResponseArray[0];
			$replaceStr .= "</div>";
			$question = str_replace($iframeStr,$replaceStr,$question);
		}
		return($question);
	}

	function getOptionA()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionA,"images","A", $context);
	}

	function getOptionB()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionB,"images","B",$context);
	}

	function getOptionC()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionC,"images","C",$context);
	}

	function getOptionD()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionD,"images","D",$context);
	}
	
	function getDisplayAnswerA()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->display_answerA,"images","CDA",$context);
	}
	
	function getDisplayAnswerB()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->display_answerB,"images","CDB",$context);
	}
		
	function getDisplayAnswerC()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->display_answerC,"images","CDC",$context);
	}
	
	function getDisplayAnswerD()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->display_answerD,"images","CDD",$context);
	}
	
	function getDisplayAnswer()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return html_entity_decode(orig_to_html($this->displayAnswer,"images","DA",$context));
	}

	/*function getHint()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->hint,"images","H",$context);
	}*/
	
	function getHint1()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->hint1,"images","H",$context);
	}
	function getHint2()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->hint2,"images","H",$context);
	}
	function getHint3()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->hint3,"images","H",$context);
	}
	function getHint4()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->hint4,"images","H",$context);
	}
	
	function getCorrectAnswerForDisplay()
	{
		return $this->correctAnsForDisplay;
	}

	function processAnsForDisplay()
	{
		$correct_answer = "";
		if($this->quesType=="D")
		{
			preg_match_all("/{drop[\s]*:(\s)*([^}]*)}/i",$this->questionStem,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
			{
				$options = explode(",",$matches[$i][2]);
				//First option is the correct answer for drop down.
				$options[0] = str_replace("&nbsp;"," ",$options[0]);
				$options[0] = str_replace("&amp;","&",$options[0]);
				$optStr = trim(str_replace("'","",$options[0]));
				$correct_answer .= $optStr."| ";
			}
			$correct_answer = substr($correct_answer,0,-2);
		}

		if($this->correctAnswer!="")
		{
			$tempArray = explode("|",$this->correctAnswer);
			$answer="";
			$tempStr="";
			for($iterator=0; $iterator<count($tempArray); $iterator++)	{
				$tempStr = explode("~",$tempArray[$iterator]);
				$answer .= $tempStr[0]." |";
			}
			if($correct_answer!="")
				$correct_answer .= "| ";
			$correct_answer .= substr($answer,0,-2);
		}

		$this->correctAnsForDisplay = $correct_answer;
	}

	function hasExplanation()
	{
		$hasExpln = 0;
		$noOfImages = $this->getNoOfImages($this->displayAnswer);
		if($noOfImages>0)
			$hasExpln = 1;
		else
		{
			if($this->count_words($this->displayAnswer)>12)
				$hasExpln = 1;
		}
		return $hasExpln;
	}

	function replace($string)
	{
        $pattern[0] = "/\<div[^>]*\>(.*?)\<\/div\>/i";
        $replacement[0] = "\$1<br/>";
        $string = preg_replace($pattern,$replacement,$string);
        return $string;
	}

	function count_words($str)
	{
		$str = strip_tags($str);
		$pattern[0] = "/\[([a-z0-9_ \.()-\s]*)\]/i";
		$replacement[0] = "";
		//$pattern[3] = "/\[([a-z0-9_ \.()-\s]*)[ ]*,[ ]*(.*)\]/i";
		$pattern[1] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*)\]/i";
		$replacement[1] = "";

		$pattern[2] = "/&nbsp;/i";
		$replacement = " ";
		$pattern[3] = "/\[([a-z0-9_]*.swf),([0-9]*),([0-9]*)\]/i";
		$replacement[3] = "";

		$pattern[4] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*),N\]/i";
		$replacement[4] = "";

		$pattern[5] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),N\]/i";
		$replacement[5] = "";

		$str= preg_replace($pattern, $replacement, $str);
		$pat[0]="/[0-9]+/";
		$rep[0]="a";
		$str= preg_replace($pat, $rep, $str);
		//echo $str;
		$str = strip_tags($str);
		$str = str_replace("'","^",$str) ;
		$str = str_replace("\\","^",$str) ;
		return str_word_count($str);
	}

	function getNoOfImages($str)
	{
	    	$cnt_matches = 0;
	    	$matches = "";
			preg_match_all("/\[([a-z0-9_ -\.]*)\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			$matches = "";
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*)\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_matches += count($matches);
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*),N\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_matches += count($matches);
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),N\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_matches += count($matches);
			
			preg_match_all("/\[([a-z0-9_\/]*.html)\?*([^\],]*)(\s*,\s*([0-9]+)){0,1}(\s*,\s*([0-9]+)){0,1}\]/i",$str,$matches, PREG_SET_ORDER);
			$cnt_matches += count($matches);

			return $cnt_mathes;
	}
	
	function getHints()
	{
		$arrayHint	=	array();
		$sq	=	"SELECT hint,hintNo FROM adepts_hints WHERE qcode='".$this->qcode."' AND status='Live' ORDER BY hintNo";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$arrayHint[$rw[1]]	=	$rw[0];
		}
		return $arrayHint;
	}
	function getAllConditions()
	{
		$quesConditionArray	=	array();
		$sq	=	"SELECT quesCondition,srNo FROM adepts_conditionalAlert WHERE qcode='".$this->qcode."' and status='Live'";
		$rs	=	mysql_query($sq);
		$i=1;
		while($rw=mysql_fetch_array($rs))
		{
			$quesConditionArray[$i]	=	$rw[0];
			$i++;
		}
		return $quesConditionArray;
	}

	function getAllActions()
	{
		$quesActionArray	=	array();
		$sq	=	"SELECT quesAction,srNo FROM adepts_conditionalAlert WHERE qcode='".$this->qcode."' and status='Live'";
		$rs	=	mysql_query($sq);
		$i=1;
		while($rw=mysql_fetch_array($rs))
		{
			$quesActionArray[$i]	=	$rw[0];
			$i++;
		}
		return $quesActionArray;
	}
	
	/*function getAllSwfInQcode()
	{
		$patterns = "/\[([a-z0-9_\/]*.swf)\?*([^\],]*)(\s*,\s*([0-9]+)){0,1}(\s*,\s*([0-9]+)){0,1}\]/i";
			
			//Questions
			preg_match_all($patterns,$this->questionStem,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "Q";
			
			//display answer
			preg_match_all($patterns,$this->displayAnswer,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "DA";
				
			//Option A
			preg_match_all($patterns,$this->optionA,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "A";				
			
			//Option B
			preg_match_all($patterns,$this->optionB,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "B";					
			
			//Option C
			preg_match_all($patterns,$this->optionB,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "C";
			
			//Option D
			preg_match_all($patterns,$this->optionB,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "D";						
			
			//hint
			preg_match_all($patterns,$this->hint,$matches, PREG_SET_ORDER);
			$cnt_matches = count($matches);
			for($i=0 ; $i<$cnt_matches; $i++)
				$this->swfArrForHtml5[substr($matches[$i][1],0,-4)]["type"] = "H";							
				
			$swfStr = '';
			foreach($this->swfArrForHtml5 as $key=>$arr)
			{
				$swfStr .="'".$key."',"; 
			}
			$swfStr = substr($swfStr,0,-1);
			$query = "SELECT * FROM adepts_swfHtmlParam WHERE swfFileName IN (".$swfStr.")";
			$result = mysql_query($query);
			
			while($line = mysql_fetch_array($result))
			{
				if($line['param'] != "")
					$this->swfArrForHtml5[$line["swfFileName"]]["param"] = $line['htmlFileName'].".html?".$line['param'].",".$line['height'].",".$line['width'];				
				else
					$this->swfArrForHtml5[$line["swfFileName"]]["param"] = $line['htmlFileName'].".html,".$line['height'].",".$line['width'];			
				$this->swfArrForHtml5[$line["swfFileName"]]["htmlFileName"] = $line['htmlFileName'];			
			}
			
	}
	
	function changeSwfWithHtml()
	{
		
		foreach($this->swfArrForHtml5 as $key=>$arr)
		{			
			if($arr['type'] == 'Q' && isset($arr['htmlFileName']))
				$this->questionStem = $this->replaceSwfWithHtml($this->questionStem,$key,$arr['param']);			
			
			if($arr['type'] == 'A' && isset($arr['htmlFileName']))
				$this->optionA = $this->replaceSwfWithHtml($this->optionA,$key,$arr['param']);			
			
			if($arr['type'] == 'B' && isset($arr['htmlFileName']))
				$this->optionB = $this->replaceSwfWithHtml($this->optionB,$key,$arr['param']);					
				
			if($arr['type'] == 'C' && isset($arr['htmlFileName']))
				$this->optionC = $this->replaceSwfWithHtml($this->optionC,$key,$arr['param']);			
			
			if($arr['type'] == 'D' && isset($arr['htmlFileName']))
				$this->optionD = $this->replaceSwfWithHtml($this->optionD,$key,$arr['param']);				
			
			if($arr['type'] == 'DA' && isset($arr['htmlFileName']))
				$this->displayAnswer = $this->replaceSwfWithHtml($this->displayAnswer,$key,$arr['param']);				
			
			if($arr['type'] == 'H' && isset($arr['htmlFileName']))
				$this->hint = $this->replaceSwfWithHtml($this->hint,$key,$arr['param']);			
		}	
			
	}*/
}
?>