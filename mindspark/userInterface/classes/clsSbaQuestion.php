<?php
class sbaQuestion
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


	function sbaQuestion($qcode)
	{
		$query = "SELECT question, optiona, optionb, optionc, optiond, subdifficultylevel,
				  correct_answer, display_answer,question_type, clusterCode, dynamic, eeIcon, ques_voiceover, ans_voiceover 
			      FROM   adepts_sbaQuestions
			      WHERE  qcode=$qcode";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$this->qcode          = $qcode;
		$arrayHint	=	$this->getHints();
		$this->questionStem   = $line['question'];
		$this->optionA        = $line['optiona'];
		$this->optionB        = $line['optionb'];
		$this->optionC        = $line['optionc'];
		$this->optionD        = $line['optiond'];
		$this->display_answerA	= "";
		$this->display_answerB	= "";
		$this->display_answerC	= "";
		$this->display_answerD	= "";
		$this->correctAnswer  = $line['correct_answer'];
		$this->displayAnswer  = $this->replace($line['display_answer']);
		$this->clusterCode    = $line['clusterCode'];
		$this->dynamic        = $line['dynamic'];
		$this->eeIcon        = $line['eeIcon'];
		//$this->hint 		  = $line['hint'];
		$this->hint1 		  = "";
		$this->hint2 		  = "";
		$this->hint3 		  = "";
		$this->hint4 		  = "";
		$this->hintAvailable  = 0;
		$this->quesType       = $line['question_type'];
		$this->quesVoiceOver  = $line['ques_voiceover'];
		$this->ansVoiceOver   = $line['ans_voiceover'];
		$this->subDifficultyLevel  = $line['subdifficultylevel'];
		$this->context        = "";
		$this->country        = "";
		$this->noOfTrials     = "";
		$this->comments       = "";
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
		$question = orig_to_html($this->questionStem,"images","Q", $context);
		
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
}
?>