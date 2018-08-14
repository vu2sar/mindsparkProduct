<?php

class teacherQuestion
{
	var $qcode;
	var $questionStem;
	var $optionA;
	var $optionB;
	var $optionC;
	var $optionD;
	var $subDifficultyLevel =0;
	var $quesType;
	var $correctAnswer;
	var $displayAnswer = "";
	var $hintAvailable = "";
	var $clusterCode = "";
	var $dynamic=0;
	var $quesVoiceOver="";
	var $ansVoiceOver="";
	var $dropDownAns="";
	var $fracBoxAns="";
	var $dynamicParams="";
	var $correctAnsForDisplay="";
	var $context="Global";
	var $country ="" ;
	var $noOfTrials=1;
	var $comments;
	var $questionmaker;

	function teacherQuestion($qcode)
	{
		$query = "SELECT qcode,question, question_type,optiona, optionb, optionc, optiond, correct_answer, clusterCode, ttCode,questionmaker,remarks
			      FROM adepts_teacherQuestion WHERE  qcode=$qcode";

		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$questionText = $line['question'];
		$optionA        = $line['optiona'];
		$optionB        = $line['optionb'];
		$optionC        = $line['optionc'];
		$optionD        = $line['optiond'];
		if(SERVER_TYPE=='LOCAL')
		{
			$questionText = str_replace("http://www.educationalinitiatives.com", "/mindspark", $questionText);
			$questionText = str_replace("http://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $questionText);
			$questionText = str_replace("https://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $questionText);
			$questionText = str_replace("http://mindspark-ei.s3.amazonaws.com", "/mindspark", $questionText);
			
			$optionA = str_replace("http://www.educationalinitiatives.com", "/mindspark", $optionA);
			$optionA = str_replace("http://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionA);
			$optionA = str_replace("https://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionA);
			$optionA = str_replace("http://mindspark-ei.s3.amazonaws.com", "/mindspark", $optionA);
			
			$optionB = str_replace("http://www.educationalinitiatives.com", "/mindspark", $optionB);
			$optionB = str_replace("http://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionB);
			$optionB = str_replace("https://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionB);
			$optionB = str_replace("http://mindspark-ei.s3.amazonaws.com", "/mindspark", $optionB);
			
			$optionC = str_replace("http://www.educationalinitiatives.com", "/mindspark", $optionC);
			$optionC = str_replace("http://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionC);
			$optionC = str_replace("https://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionC);
			$optionC = str_replace("http://mindspark-ei.s3.amazonaws.com", "/mindspark", $optionC);
			
			$optionD = str_replace("http://www.educationalinitiatives.com", "/mindspark", $optionD);
			$optionD = str_replace("http://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionD);
			$optionD = str_replace("https://d2tl1spkm4qpax.cloudfront.net", "/mindspark", $optionD);
			$optionD = str_replace("http://mindspark-ei.s3.amazonaws.com", "/mindspark", $optionD);
		}
		
		$this->qcode          = $qcode;
		$this->questionStem   = $questionText;
		$this->optionA        = $optionA;
		$this->optionB        = $optionB;
		$this->optionC        = $optionC;
		$this->optionD        = $optionD;
		$this->correctAnswer  = $line['correct_answer'];
	//	$this->displayAnswer  = $this->replace($line['display_answer']);
		$this->clusterCode    = $line['clusterCode'];
	//	$this->dynamic        = $line['dynamic'];
		$this->quesType       = $line['question_type'];
	//	$this->quesVoiceOver  = $line['ques_voiceover'];
	//	$this->ansVoiceOver   = $line['ans_voiceover'];
	//	$this->subDifficultyLevel  = $line['subdifficultylevel'];
	//	$this->context        = $line['context'];
		$this->country        = isset($_SESSION['country'])?$_SESSION['country']:"";
	//	$this->noOfTrials     = $line['trials'];
		$this->comments       = $line['remarks'];
		$this->questionmaker  = $line['questionmaker'];
	//	$this->fracBoxAns       = "";
		$this->processAnsForDisplay();


        $tmpDispAns = str_replace("&nbsp;","",strip_tags($this->displayAnswer));
        $tmpDispAns = str_replace("&#13;&#10;","",$tmpDispAns);
        if(trim($tmpDispAns)=="")
	       	$this->displayAnswer = getDisplayAnswer($this->questionStem, $line['correct_answer']);
	    if($this->quesType=='D')
        {
        	$tmpArray = convertDropDowns($this->questionStem);
        	$this->questionStem    = $tmpArray[0];
        	$this->dropDownAns     = $tmpArray[1];
        }

        $pattern = "/fracbox\s*\[([^\]]+)\]/i";
		preg_match_all($pattern,$this->questionStem,$matches, PREG_SET_ORDER);
		$cnt_matches = count($matches);
		for($i=0 ; $i<$cnt_matches; $i++)
		{
			$this->fracBoxAns .= $matches[$i][1]."|";
		}
		$this->fracBoxAns = substr($this->fracBoxAns,0,-1);
	}

	function isDynamic()
	{
		return $this->dynamic;
	}

	function generateQuestion($mode="", $generatedParams="")
	{
		$questionArray       = generateDynamicQues($this->questionStem, $this->optionA, $this->optionB, $this->optionC, $this->optionD, $this->correctAnswer, $this->displayAnswer, $this->quesType, $generatedParams, $mode);
		$this->questionStem  = Plural_to_Singular($questionArray[0]);
		$this->optionA       = stripslashes($questionArray[1]);
		$this->optionB       = stripslashes($questionArray[2]);
		$this->optionC       = stripslashes($questionArray[3]);
		$this->optionD       = stripslashes($questionArray[4]);
		$this->correctAnswer = stripslashes($questionArray[5]);
		$this->displayAnswer = stripslashes($questionArray[6]);
		$this->dynamicParams = stripslashes($questionArray[7]);
		$this->processAnsForDisplay();
	}

	function getQuestion()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->questionStem,"images","Q", $context, "", 1);
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
		return orig_to_html($this->optionA,"images","A", $context, "", 1);
	}

	function getOptionB()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionB,"images","B",$context, "", 1);
	}

	function getOptionC()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionC,"images","C",$context, "", 1);
	}

	function getOptionD()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->optionD,"images","D",$context, "", 1);
	}
	
	function getDisplayAnswer()
	{
	    $context = "";
	    if($this->country=="US" && $this->context!="US")   //Apply auto-replacement to questions which are not already marked for US content and the user's country is US
	       $context = "US";
		return orig_to_html($this->displayAnswer,"images","DA",$context, "", 1);
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
				$answer .= $tempStr[0]."| ";
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
	    	$cnt_mathes = 0;
	    	$matches = "";
			preg_match_all("/\[([a-z0-9_ -\.]*)\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_mathes = count($matches);
			$matches = "";
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*)\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_mathes += count($matches);
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*),N\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_mathes += count($matches);
			preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),N\]/i", $str, $matches, PREG_SET_ORDER);
			$cnt_mathes += count($matches);

			return $cnt_mathes;
	}
}
?>