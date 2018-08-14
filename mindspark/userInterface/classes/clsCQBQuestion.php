<?php

class cqbQuestion
{
	var $qcode;
	var $questionStem;
	var $optionA;
	var $optionB;
	var $optionC;
	var $optionD;
	var $quesType;		
	var $correctAnswer;	
	var $subsubtopic;
	var $dynamic;
	var $fracBoxAns;
	var $dropDownAns;
	var $correctAnsForDisplay;	


	function cqbQuestion($qcode)
	{	
		$query = "SELECT question, optiona, optionb, optionc, optiond, correct_answer,question_type, subsubtopic, questionmaker
			      FROM   educatio_educat.common_question_bank
			      WHERE  qcode=$qcode";			      
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		if (strpos($line['question'],'images') !== false) {
			$question = 	str_replace("http://www.educationalinitiatives.com/quesbank/images/","images/",$line['question']);
			$question = 	str_replace("http://educationalinitiatives.com/quesbank/images/","images/",$question);
			if(SERVER_TYPE=='LOCAL')
				$question = str_replace("images/", "/mindspark/quesbank/images/", $question);
			else	
				$question = str_replace("images/","http://www.educationalinitiatives.com/quesbank/images/",$question);
		}
		else
			$question = $line['question'];	

		if (strpos($line['optiona'],'images') !== false) {
			$optiona = 	str_replace("http://www.educationalinitiatives.com/quesbank/images/","images/",$line['optiona']);
			$optiona = 	str_replace("http://educationalinitiatives.com/quesbank/images/","images/",$optiona);
			if(SERVER_TYPE=='LOCAL')
				$optiona = str_replace("images/", "/mindspark/quesbank/images/", $optiona);
			else	
				$optiona = 	str_replace("images/","http://www.educationalinitiatives.com/quesbank/images/",$optiona);
		}
		else
			$optiona = $line['optiona'];

		if (strpos($line['optionb'],'images') !== false) {
			$optionb = 	str_replace("http://www.educationalinitiatives.com/quesbank/images/","images/",$line['optionb']);
			$optionb = 	str_replace("http://educationalinitiatives.com/quesbank/images/","images/",$optionb);
			if(SERVER_TYPE=='LOCAL')
				$optionb = str_replace("images/", "/mindspark/quesbank/images/", $optionb);
			else	
				$optionb = 	str_replace("images/","http://www.educationalinitiatives.com/quesbank/images/",$optionb);
		}
		else
			$optionb = $line['optionb'];

		if (strpos($line['optionc'],'images') !== false) {
			$optionc = 	str_replace("http://www.educationalinitiatives.com/quesbank/images/","images/",$line['optionc']);
			$optionc = 	str_replace("http://educationalinitiatives.com/quesbank/images/","images/",$optionc);
			if(SERVER_TYPE=='LOCAL')
				$optionc = str_replace("images/", "/mindspark/quesbank/images/", $optionc);
			else	
				$optionc = 	str_replace("images/","http://www.educationalinitiatives.com/quesbank/images/",$optionc);
		}
		else
			$optionc = $line['optionc'];

		if (strpos($line['optiond'],'images') !== false) {
			$optiond = 	str_replace("http://www.educationalinitiatives.com/quesbank/images/","images/",$line['optiond']);
			$optiond = 	str_replace("http://educationalinitiatives.com/quesbank/images/","images/",$optiond);
			if(SERVER_TYPE=='LOCAL')
				$optiond = str_replace("images/", "/mindspark/quesbank/images/", $optiond);
			else
				$optiond = 	str_replace("images/","http://www.educationalinitiatives.com/quesbank/images/",$optiond);
		}
		else
			$optiond = $line['optiond'];


		$this->qcode          = $qcode;
		$this->questionStem   = $question;
		$this->optionA        = $optiona;
		$this->optionB        = $optionb;
		$this->optionC        = $optionc;
		$this->optionD        = $optiond;
		$this->correctAnswer  = $line['correct_answer'];		
		$this->subsubtopic 	  = $line['subsubtopic'];
		$this->quesType       = $line['question_type'];						
		$this->fracBoxAns     = "";
		$this->processAnsForDisplay();
           
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

	function getQuestion()
	{		
		return orig_to_html($this->questionStem,"images","Q");
	}
	function getQuestionForDisplay($eeresponse)
	{
		$eeResponseArray = explode("@$*@",$eeresponse);
		$question = orig_to_html($this->questionStem,"images","Q");		
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