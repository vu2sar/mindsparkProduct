<?php

class ncertQuestion
{
	var $qcode;
	var $questionStem;
	var $optionA;
	var $optionB;
	var $optionC;
	var $optionD;
	var $quesType;
	var $subQuestionNo;
	var $eeIcon;
	var $correctAnswer;
	var $displayAnswer;
	var $exerciseCode;
	var $dynamic;
	var $fracBoxAns;
	var $dropDownAns;
	var $correctAnsForDisplay;
	var $groupID;


	function ncertQuestion($qcode)
	{

		$query = "SELECT question, optiona, optionb, optionc, optiond, correct_answer, display_answer, question_type, subQuestionNo, eeIcon, exerciseCode, questionmaker, groupID
			      FROM   adepts_ncertQuestions
			      WHERE  qcode=$qcode";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);

		$this->qcode          = $qcode;
		$this->questionStem   = $line['question'];
		$this->optionA        = $line['optiona'];
		$this->optionB        = $line['optionb'];
		$this->optionC        = $line['optionc'];
		$this->optionD        = $line['optiond'];
		$this->correctAnswer  = $line['correct_answer'];
		$this->displayAnswer  = $this->replace($line['display_answer']);
		$this->exerciseCode    = $line['exerciseCode'];
		$this->quesType       = $line['question_type'];
		$this->subQuestionNo  = $line['subQuestionNo'];
		$this->eeIcon        = $line['eeIcon'];
		$this->groupID       = $line['groupID'];
		$this->fracBoxAns       = "";
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
		if($this->eeIcon == "1")
			$this->questionStem .= '<div class="eqEditorConatiner" style="display:none;margin-top:10px;">[eqeditor]</div>';
	}
	function isDynamic()
	{
		return false;
	}

	function generateQuestion($mode="")
	{
		$questionArray       = generateDynamicQues($this->questionStem, $this->optionA, $this->optionB, $this->optionC, $this->optionD, $this->correctAnswer, $this->displayAnswer, $this->quesType, $mode);
		$this->questionStem  = Plural_to_Singular($questionArray[0]);
		$this->optionA       = stripslashes($questionArray[1]);
		$this->optionB       = stripslashes($questionArray[2]);
		$this->optionC       = stripslashes($questionArray[3]);
		$this->optionD       = stripslashes($questionArray[4]);
		$this->correctAnswer = stripslashes($questionArray[5]);
		$this->displayAnswer = stripslashes($questionArray[6]);
		$this->processAnsForDisplay();
	}

	function getQuestion()
	{
		/*if($this->quesType=="Open Ended")
		{
			$this->questionStem .= '<div style="clear:both;"></div><br><textarea class="openEnded" placeholder="Specify your answer here." name="text" cols="" rows="" id="b1"></textarea><br><div style="clear:both;"></div>';
		}*/
		return orig_to_html($this->questionStem,"images","Q");
	}
	function getQuestionForDisplay($eeresponse)
	{
		$eeResponseArray = explode("@$*@",$eeresponse);
		$question = orig_to_html($this->questionStem,"images","Q");
		if($this->quesType=="Open Ended" || $this->eeIcon == "1")
		{
			// $replaceStr = '<div style="width:600px; height:400px;margin:0px;border:0px; background-color:white;border:1px solid black; background-image:url('.$eeResponseArray[1].')" id="eqeditor">';
			// $replaceStr .= $eeResponseArray[0];
			// $replaceStr .= "</div>";
			$eeResponseArray[0] = str_replace('"', '&quot;', $eeResponseArray[0]);
			$eeResponseArray[0] = str_replace('\'', '\\\'', $eeResponseArray[0]);
			$replaceStr = '<iframe src="../userInterface/equationeditor2/eqeditor1.htm" width="714px" height="406px" id="eqeditor" onload="this.contentWindow.editable.innerHTML = decodeURIComponent(encodeURIComponent(\''.$eeResponseArray[0].'\').replace(/%C3%A2%E2%82%AC%C2%8D/g, \'\')); this.contentWindow.restoreImage(\''.$eeResponseArray[1].'\');" style="margin:0px;border:0px" class="openEnded"></iframe>';
			$question = preg_replace('/<iframe.*?>/', $replaceStr, $question);
			//$question = str_replace($iframeStr,$replaceStr,$question);
		}
		return $question;
		
	}
	function getOptionA()
	{
		return orig_to_html($this->optionA,"images","A");
	}

	function getOptionB()
	{
		return orig_to_html($this->optionB,"images","B");
	}

	function getOptionC()
	{
		return orig_to_html($this->optionC,"images","C",$context);
	}

	function getOptionD()
	{
		return orig_to_html($this->optionD,"images","D");
	}
	
	function getDisplayAnswer()
	{
		
		/*if($this->quesType=="Open Ended")
		{
			return ("Answer for this type of question will be reviewed by teacher.");
		}
		else
		{*/
			return orig_to_html($this->displayAnswer,"images","DA");
		/*}*/
	}

	function getCorrectAnswerForDisplay()
	{
		return $this->correctAnsForDisplay;
	}
	function getQuestionType()
	{
		return $this->quesType;
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
				$optStr = trim(str_replace("'","",$options[0]));
				$correct_answer .= $optStr."| ";
			}
			$correct_answer = substr($correct_answer,0,-2);
		}
		
		/*if($this->quesType == "Open Ended")
		{
			$this->correctAnsForDisplay = "Your answer will be reviewed by teacher.";
		}
		else */
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