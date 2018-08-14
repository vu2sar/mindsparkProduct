<?php
//V2 contains code for Junk Detection for markedWrong
//V3 contains i added category called 'IT' and some improvements on other categories based on 1000 manual bucketing
class commentCategorization
{
	var $patternArray;
	var $shortPatternArray;
	var $ttList;
	var $three_word_frequency;
	function commentCategorization($source='')
	{
		if($source =='markedWrong')
			$this->source = 'markedWrong';
		else 
			$this->source = 'commentCategorization';

		$this->ttList = array();
		$this->patternArray = array();		
		$this->patternArray['image'] = array();
		$this->patternArray['sparkie'] = array();
		$this->patternArray['difficultQuestion'] = array();
		$this->patternArray['doubtAboutQuestion'] = array();
		$this->patternArray['noCQ'] = array();
		$this->patternArray['submitIssue'] = array();
		$this->patternArray['autoLogout'] = array();
		$this->patternArray['easyQuestion'] = array();
		$this->patternArray['repeatedQuestion'] = array();
		$this->patternArray['topicProgress'] = array();
		$this->patternArray['markedWrong'] = array();
		$this->patternArray['internet'] = array();
		$this->patternArray['outOfSyllabus'] = array();
		$this->patternArray['negativeFeedback'] = array();
		$this->patternArray['positiveFeedback'] = array();
		$this->patternArray['it'] = array();
	
		$this->patternArray['none'] = array();
		$this->patternArray['junk'] = array();

		$this->shortPatternArray = array();
		$this->shortPatternArray['image'] = array();
		$this->shortPatternArray['difficultQuestion'] = array();
		$this->shortPatternArray['doubtAboutQuestion'] = array();
		$this->shortPatternArray['noCQ'] = array();
		$this->shortPatternArray['submitIssue'] = array();
		$this->shortPatternArray['autoLogout'] = array();
		$this->shortPatternArray['easyQuestion'] = array();
		$this->shortPatternArray['repeatedQuestion'] = array();
		$this->shortPatternArray['topicProgress'] = array();
		$this->shortPatternArray['markedWrong'] = array();
		$this->shortPatternArray['internet'] = array();
		$this->shortPatternArray['outOfSyllabus'] = array();
		$this->shortPatternArray['negativeFeedback'] = array();
		$this->shortPatternArray['positiveFeedback'] = array();
		$this->shortPatternArray['it'] = array();
		$this->shortPatternArray['sparkie'] = array();
		$this->makePatternArray();
		$this->makeshortPatternArray();

		$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE subjectno = 2 AND customTopic = 0 AND live = 1 AND teacherTopicDesc != 'Time'";
	    $result = mysql_query($query) or die("SQL error: Here I am in elsey".mysql_error());
	    while($line = mysql_fetch_array($result)) 
	    	{array_push($this->ttList, "/(\s)(".$line['teacherTopicDesc'].")((\s)Custom(\s)?(\-)?(\s)?[0-9]+)?/i");}
	}

	//php functions
	function mark($response, $arrForChanged= '')
	{ 	

		//junk check by Sourabh's logic
		$junkCheck1=$this->isJunkComment($response)==1?'Junk':'Not Junk';
		/*if ($junkCheck1==1)
			return 1;*/

		//master function that calls functions spellCheck, isJunk, and bucketize and sets the global variables $responseAfterSpellCheck, $isJunk and $bucket respectively
		//global $response, $responseAfterSpellCheck, $isJunk, $bucket;
		//$source can take 2 values now - 'commentCategorization' and 'markedWrong'
		$responseAfterSpellCheck = $this->spellCheck(strip_tags($response));
		$isJunk = $this->isJunk($responseAfterSpellCheck, preg_match_all('/\s/', trim($response), $matches));
		if($this->source == 'markedWrong')
		{
			if($isJunk == 1)
				return 'Junk';
			else 
				return 'Other';
		}

		if($isJunk == 0)
			$bucket = $this->bucketize($responseAfterSpellCheck);
		else
			$bucket = 'junk';
		$systemCategory	=	array("image"=>"Images not appearing","sparkie"=>"Sparkie","difficultQuestion"=>"Difficult questions","doubtAboutQuestion"=>"Doubt about the question","noCQ"=>"No CQ","submitIssue"=>"Submit Issue","autoLogout"=>"Auto Logout","easyQuestion"=>"Easy questions","repeatedQuestion"=>"Repeated question","topicProgress"=>"Topic progress not moving","markedWrong"=>"Correct Answer marked wrong","internet"=>"Internet","it"=>"IT","outOfSyllabus"=>"Question out of Syllabus","positiveFeedback"=>"Positive Feedback","negativeFeedback"=>"Negative Feedback","junk"=>"Junk","none"=>"Other");
			
		return array('bucket'=>$systemCategory[$bucket],'isJunk2'=>$junkCheck1);
	}


	function spellCheck($response)
	{	
		$str = $response; 
		$newPatternArray = array();
		$newReplacementArray = array();
			//convert to lowercase	
			//$str = strtolower($response);

			//remove chars that are repeating more than 2 times consecutively
		$pattern = '%([^0-9])\\1{2,}%i';
		$replace = '$1'.'$1';
		$str = preg_replace($pattern, $replace, $str);

		  	//remove consecutive repeat words
		do {
			$q1=$str;
			$pattern = "/\b(\w+)\s+\\1+\b/i";
			$replace = '$1';
			$str = preg_replace($pattern, $replace, $str);                
		}
		while($q1 != $str);

		  	//find email ids
		$pattern = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i';
		$replace = 'Email';
		$str = preg_replace($pattern, $replace, $str);

		  	//replace "v." with "very"
		$pattern = '/v(\.)/i';
		$replace = 'very ';
		$str = preg_replace($pattern, $replace, $str);

		  	//add space around all the non alphanumeric characters
		$pattern = "%([^a-z0-9\s])%i";
		$replace = '$1';
		$str = preg_replace($pattern, " ".$replace." ", $str, -1, $count);

		  	//add space between a string of chars and a string of numbers
		$pattern = "%([a-z]+)([0-9]+)%i";
		$replace = '$1 $2';
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		  	//add space between a string of numbers and a string of numbers
		$pattern = "%([0-9]+)([a-z]+)%i";
		$replace = '$1 $2';
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		$pattern = '%(\s)\s+%i';
		$replace = '$1';
		$str = preg_replace($pattern, $replace, $str);

		$pattern = "%(:(\s)(\-)(\s)(\))(\s))+%i";
		$replace = "SMILEY ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		$pattern = "%(:(\s)(\-)(\s)(\()(\s))+%i";
		$replace = "SAD_SMILEY ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		$pattern = "%mindsparks?|mind(\-|\s)sparks?|mind(\s)sparks?|minds park%i";
		$replace = " MINDSPARK ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		$pattern = "%sparkie|sparky|sparki%i";
		$replace = " SPARKIE ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		$pattern = "%sparkies|sparkys|sparkis%i";
		$replace = " SPARKIES ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		if($this->source == 'commentCategorization')
		{
			if(preg_match("/percentage|%|percent/i", $str) == 1)
			{
				$replace = " MS_TEACHER_TOPIC ";
				$str = preg_replace($this->ttList, $replace, $str, -1, $count);
			}
		}
		     //remove extra spaces
		$pattern = "%(\s)\s++%i";
		$replace = '$1';
		$str = preg_replace($pattern, $replace, $str,-1,$count);

		    //add a space at the end so that words like cant, wont, etc can be made even if they appear at the end
		$str.= " ";

		    //make words like cant, dont,didnt
		$pattern = "%(can|don|didn|isn|doesn|haven|won|hasn|wouldn|shouldn|couldn|ain|aren|wasn|weren|hadn)(\s('\s)?t\s)%i";
		$replace = strtolower("$1")."'t ";
		$str = preg_replace($pattern, $replace, $str, -1, $count);

		    //correct misspellings using look up table
		$srno = 0;
		$wordArray = str_word_count($str,1);  	
		$commaSeparated = implode("\", \"", $wordArray);
		$commaSeparated = strtolower($commaSeparated);
		$query1 = "SELECT incorrectSpelling, correctSpelling FROM lookupmaster WHERE incorrectSpelling IN (\"".$commaSeparated."\")";
		$result1 = mysql_query($query1) or die("SQL error: Here I am".mysql_error());
		while($line1 = mysql_fetch_array($result1))
		{
			$newPatternArray[$srno] = "%(\b|_).".$line1['incorrectSpelling']."(\s)%i";
			$newReplacementArray[$srno] = " ".$line1['correctSpelling']." ";
			$srno++;

		}
		$str = preg_replace($newPatternArray, $newReplacementArray, $str);

		if($this->source == 'commentCategorization')
		{
			if(preg_match("/percentage|%|percent/i", $str) == 1)
			{
				$replace = " MS_TEACHER_TOPIC ";
				$str = preg_replace($this->ttList, $replace, $str, -1, $count);
			}
		}
		$pattern = "%(\s)\s++%i";
		$replace = '$1';
		$str = preg_replace($pattern, $replace, $str,-1,$count);

		return $str;
	}

	function isJunk($response, $noOfSpaces)
	{
		$extra = 0;
		$response = trim($response);
		$wordArray = str_word_count($response, 1);
		$lengthOfComment = count($wordArray);
		$commaSeparated = implode("\", \"", $wordArray);
		$query1 = "SELECT word FROM educatio_educat.dictionary WHERE word IN (\"".$commaSeparated."\")";
		$result1 = mysql_query($query1) or die("SQL error: Here I am in 2222".mysql_error());
		//while($line12=mysql_fetch_array($result1)){echo "<br>".$line12['word'];}
		$num_rows = mysql_num_rows($result1);

		$pattern = '/(PartOfExpression|Commentator|Email|mindspark|mindsparks|mind spark|sparkie|sparkies|sparky|sparkys|sparki|sparkis|SMILEY|SAD_SMILEY|MS_TEACHER_TOPIC)/i';
		$extra = preg_match_all($pattern, $response, $matches);
		      //print_r($matches);
		      //echo "Extra:".$extra."end";
	
		switch ($this->source) {
			case 'commentCategorization':
				if(preg_match('/(SELECT.*INTO)|(INSERT.*VALUES)/i', $response))
					return 0;
				else if(preg_match('/fuck|(\b|_)sex(\b|_)|asshole|(\b|_)ass(\b|_)|bitch|kutte/i', $response))
					return 1;
					$pattern = '/(good|nice|poor|great|super|bad|sucks|lame|awesome|useful|love|like|best|worst|worse|hate|waste|easy|confused|confusing|simple|image|picture|pics|difficult|hard|tough|help|bullseye|how|bor(ing|ed)|slow|:\-\)|:\-\()/i';
					$validSmallWords = preg_match_all($pattern, $response, $matches);

					if(strlen($response) <= 9 && $validSmallWords == 0)
					{
						return 1;
					}
					else if(($num_rows+$extra) < 0.5 *$lengthOfComment)
					{	
					    	//echo "JUNKy";
						return 1;
					}
					else
						return 0;
				break;
			
			case 'markedWrong':
				$pattern = '/(confused|confusing|simple|image|picture|pics|hard|help|bullseye|sorry|type|wrote|written|write|how|bor(ing|ed)|:\-\)|:\-\()/i';
				$validSmallWords = preg_match_all($pattern, $response, $matches);
				//added only for marked wrong
				$pattern = '/(no|not)/i';
				$validSmallWords += preg_match_all($pattern, $response, $matches);
				$pattern = '/([0-9]+)(\s)?(\+|\-|\*|x|\/|%|=)/i';
				$expr = preg_match_all($pattern, $response, $matches);
				//echo $expr;
				$pattern = '/([0-9]+)/i';
				$numbers = preg_match_all($pattern, $response, $matches);
				if(strlen($response) <= 9 && $validSmallWords == 0 && $expr == 0)
				{
					return 1;
				}
				else if($expr == 0 && (($num_rows+$extra+$numbers) < 0.5 *$lengthOfComment))
				{	
				    	//echo "JUNKy";
					return 1;
				}
				else if((int)$noOfSpaces == 0 && $expr == 0)
				{	
				    	//echo "JUNKy";
					return 1;
				}
				else if($noOfSpaces != 0 && strlen($response)/(int)$noOfSpaces >= 30)
				{	
				    	//echo "JUNKy";
					return 1;
				}
				else
					return 0;
				break;
		}
		

	}

	function bucketize($response)
	{	
		$bucket = 'none';

		foreach($this->patternArray as $bucketKey => $patternForBucket)
		{
			foreach ($patternForBucket as $index => $pattern)
			{
				if(preg_match($pattern, $response) == 1)
				{
					$bucket = $bucketKey;
		          	//echo "Lpattern:".$pattern;
					break;
				}
			}
			if(strcmp($bucket, "none") != 0)
				{
					break;
			}

		}
		if($bucket == "none" && str_word_count($response) <7)
		{
			foreach($this->shortPatternArray as $bucketKey => $patternForBucket)
			{
				foreach ($patternForBucket as $index => $pattern)
				{
					if(preg_match($pattern, $response) == 1)
					{
						$bucket = $bucketKey;
						//echo "pattern:".$pattern;
						break;
					}				
				}
				if(strcmp($bucket, "none") != 0)
					{
						break;

					}
			}
		}
		return $bucket;
	}

	function makePatternArray()
	{
	    //image not loading
		array_push($this->patternArray['image'], "/(didn't |did not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not |rarely |hardly |half )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |shown |com(es?|ing) |came )(\w+ ){0,4}(animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)/i"); // not getting image
		array_push($this->patternArray['image'], "/(\w+ ){0,5}(animations? |pics? |pictures? |images? |figures? |diagrams? |charts? |visuals? |jpgs? |pngs? |protractors? |patterns? |number(\s)?lines? |shapes? |scales? )(\w+ ){0,3}(can|is|does|are|did)?(\s)?(nt |n't |not? |rarely |hardly |never |half )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?|there|found|visible|clear)(\s)?/i"); //image not showing
		array_push($this->patternArray['image'], "/(can|is|does)?(\s)?(nt |n't |not? |rarely |hardly |never |half )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|visible|giv(s|en?|ing)|insert(s?|ed|ing)?)(\s)(\w+ ){0,3}(animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //not showing images
		array_push($this->patternArray['image'], "/(\w+ ){0,5}(no )(\w+ ){0,3}(animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //no image
		array_push($this->patternArray['image'], "/(\w+ ){0,5}(give|send|load|show|display|insert|put)(\s)(\w+ ){0,3}(animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //no image

		//regex for no sparkies
		array_push($this->patternArray['sparkie'], "/(\w+ ){0,5}(no |lost |less )(\w+ ){0,3}(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //no sparkie
		array_push($this->patternArray['sparkie'], "/(\w+ ){0,5}(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not |hardly |rarely )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |giv(es?|ing|en) |gave |receiv(es?|ing|ed)? )(\w+ ){0,4}(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //not getting sparkie
		array_push($this->patternArray['sparkie'], "/(\w+ ){0,3}(see(ing)? |shown |view(ing)? |get(ting)? |got |had |have |receiv(e|ing|ed)? |gave |giv(es?|ing) )(\w+ ){0,4}(only )?(only |\d+ |0 |zero |one )(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //got 0 sparkie
		array_push($this->patternArray['sparkie'], "/(only )?(only |\d+ |0 |zero |one )(sparkie?s?|sparkys?|sparks)(\w+ ){0,3}(display(ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s|ed|ing)?|there|found)(\s)?(\w+ ){0,4}/i");//only 1 sparkie
		array_push($this->patternArray['sparkie'], "/(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}(didn't |doesn't |does not |did not |don't |do not? |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+){0,3}(com(es?|ing)|mov(es?|ing|ed)|go(es?|ing)|increas(es?|ed|ing))/i"); //sparkie (count) not moving up

		//regex for diff questions
		array_push($this->patternArray['difficultQuestion'], "/(why|what|when|where|how)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(tough|difficult|hard|tricky|incomprehensible)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //why give easy questions
		array_push($this->patternArray['difficultQuestion'], "/(^(make|give|send)|(want|need|where))(\s)(\w+ ){0,3}(eas(y|ier)|simple)(\s)(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)?/i"); //give easy questions
		array_push($this->patternArray['difficultQuestion'], "/(^(make|give|send)|(want|need|where))(\s)(\w+ ){0,3}(its?|this|that|those|they|them|something)(\s)(\w+ ){0,2}(eas(y|ier)|simple)/i"); //give it easy
		array_push($this->patternArray['difficultQuestion'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)(is|are)?(\s)?(\w+ ){0,2}(very|too|soo?)?(\s)?(tough|difficult|hard|tricky|incomprehensible)/i"); //questions are tough
		array_push($this->patternArray['difficultQuestion'], "/(tough|difficult|hard|tricky|incomprehensible)(\s)(qs?|questions?|sums?|problems?)(\s)(\w+ ){0,2}(coming|appearing)/i"); //difficult questions coming
		array_push($this->patternArray['difficultQuestion'], "/(mindspark|ms|you)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(tough|difficult|hard|tricky|incomprehensible)(\s)(qs?|questions?|sums?|problems?)/i"); //giving difficult questions 
		array_push($this->patternArray['difficultQuestion'], "/(don)?(not|n't|nt)(\s)(give|send|make)(\s)(very|too|so)?(\s)?(\w+ ){0,2}(tough|difficult|hard|tricky)(\s)(qs? |questions?|sums?|problems?)/i"); //dont give tough questions
		array_push($this->patternArray['difficultQuestion'], "/(fac(ing|ed?)|get(ting)?|got)(\s)(\w+ ){0,2}(very|too|so|much)?(\s)?(\w+ ){0,3}(difficulty)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems?)/i"); //facing difficulty in questions
		array_push($this->patternArray['difficultQuestion'], "/(difficulty)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems?)(\s)(\w+ ){0,3}(much|lot)?(\s)?/i"); //difficulty is a lot



		array_push($this->patternArray['doubtAboutQuestion'], "/(difficulty?|hard)(\s)(\w+ )?(understand(ing)?|answer(ing)?)/i");//diff to understand
		array_push($this->patternArray['doubtAboutQuestion'], "/(couldn't |could not |didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely |hardly )(\w+ ){0,3}((understand(ing)?)|understood|sense|clear|know how|catch)/i"); //not understanding
		array_push($this->patternArray['doubtAboutQuestion'], "/(whats?|(\b|_)hows?|whys?)(\s)(\w+ ){0,4}(ans(wers?)?|formula(e)?(s)?)/i"); //whats the answer
		array_push($this->patternArray['doubtAboutQuestion'], "/^(?!.*(n't|(\b|_)not)).*(give|send|want|need|mention).*(explanations?|reasons?).*$/i"); //give explanation
		array_push($this->patternArray['doubtAboutQuestion'], "/(help|explain|explanation|confus(ed?|ing)|doubts?)(\s)(\w+ ){0,4}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|how|why|answer)/i"); //help with question
		array_push($this->patternArray['doubtAboutQuestion'], "/((\b|_)how)(\s)(\w+ ){0,3}(solve|do|attempt|calculate|find)(\s)(\w+ ){0,3}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)?/i"); //how to do this
		array_push($this->patternArray['doubtAboutQuestion'], "/(check|rectify|change)(\s)(\w+ ){0,3}(answer|mistake|issue)/i"); //check answer
		array_push($this->patternArray['doubtAboutQuestion'], "/(answer|solution)(\s)(\w+ ){0,3}(given|written)(\s)(\w+ ){0,2}(wrong)/i"); //check answer
		array_push($this->patternArray['doubtAboutQuestion'], "/(given|written)(\s)(\w+ ){0,3}(answer|solution)(\s)(\w+ ){0,2}(wrong)/i"); //check answer
		array_push($this->patternArray['doubtAboutQuestion'], "/(((\b|_)how)|(^how))(\s)(\w+ ){0,5}(solved?|do|attempt|calculated?|find|found)/i"); //how to do this
		array_push($this->patternArray['doubtAboutQuestion'], "/(questions?|ques|(?:^|\s)qs?|sums?|problems?)(\s)(is|are|was|were)?(\s)(\w+ )?(wrong|incomplete|incorrect|illogical|not correct|not proper|improper|confusing)/i"); //question is wrong
		array_push($this->patternArray['doubtAboutQuestion'], "/(wrong|incomplete|incorrect|illogical|not correct|not proper|improper|confusing)(\s)(question|ques|(\b|_)q(\b|_)|sums?|problems?)/i"); //question is wrong
		array_push($this->patternArray['doubtAboutQuestion'], "/(i)(\s)(\w+ ){0,2}(confused)/");//i am confused
		array_push($this->patternArray['doubtAboutQuestion'], "/(make|help)(\s)(\w+ ){0,2}(understand)/");

			      //regex for no cq)
		array_push($this->patternArray['noCQ'], "/(didn't |did not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not |rarely |hardly )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |shown)(\w+ ){0,4}(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs))/i");//not getting cq
		array_push($this->patternArray['noCQ'], "/(((challeng(e|ing)|bonus|super) (qs?|questions?|sums?|problems?)|cqs))(\s)(\w+ ){0,3}(can|is|does|are)?(\s)?(nt |n't |not? |rarely |hardly )(\w+ ){0,2}(appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found)(\s)?(\w+ ){0,4}/i"); //cq not showing
		array_push($this->patternArray['noCQ'], "/(can|is|does)?(nt |n't |not? |rarely |hardly )(\w+ ){0,2}(appear(s|ing|ed)?|show(s|ing|ed|n)?) (\w+ ){0,3}(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs))/i"); //not showing cq
		array_push($this->patternArray['noCQ'], "/(no)(\s)(\w+ ){0,4}(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?))|cqs)(\s)?/i");//no cq
		array_push($this->patternArray['noCQ'], "/(give|send|want|need|where)(\s)(\w+ ){0,2}(more )?(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs))/i");//give more cq


		//regex for submit issue
		array_push($this->patternArray['submitIssue'], "/(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+ ){0,4}(submit(ing|ed)?)/i"); //cannot submit
		array_push($this->patternArray['submitIssue'], "/(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+ ){0,4}(submit|next|button)(\s)(\w+ ){0,2}(button)/i"); //cannot click on next
		array_push($this->patternArray['submitIssue'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(going|scrolling)(\s)(\w+ ){0,3}(next|new)(\s)(qs? |questions?|sums?|problems?)/i"); //not going to new question
		array_push($this->patternArray['submitIssue'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(going|scrolling)(\s)(down|below)/i"); //not scrolling down
		array_push($this->patternArray['submitIssue'], "/(next|new)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems?)(\s)(\w+ ){0,3}(can|is|does|are)?(\s)?(nt |n't |not? )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)|insert(s?|ed|ing)?)/i"); //next question not coming
		array_push($this->patternArray['submitIssue'], "/(next|submit|continue)(\s)(\w+ ){0,3}(buttons?)?(\s)?(\w+ ){0,3}(can|is|does|are)?(\s)?(nt |n't |not? )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)?|insert(s?|ed|ing)?)/i"); //next button not coming
		array_push($this->patternArray['submitIssue'], "/(buttons?)?(\s)(\w+ ){0,3}(next|submit|continue)(\s)?(\w+ ){0,3}(can|is|does|are)?(\s)?(nt |n't |not? )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)?|insert(s?|ed|ing)?)/i"); // button next not coming
		array_push($this->patternArray['submitIssue'], "/(can|is|does|are)?(\s)?(nt |n't |not? )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen?|com(es?|ing)|there|found|work(ed|s|ing)?|insert(s?|ed|ing)?)(\s)?(\w+ ){0,3}(next|submit|continue)(\s)(\w+ ){0,3}(buttons?)?/i"); //next button not coming
		array_push($this->patternArray['submitIssue'], "/(can|is|does|are)?(\s)?(nt |n't |not? )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)?|insert(s?|ed|ing)?)(\s)?(\w+ ){0,3}(buttons?)?(\s)(\w+ ){0,3}(next|submit|continue)/i"); // button next not coming
		array_push($this->patternArray['submitIssue'], "/(unable|not able|not?)(\s)(\w+ ){0,2}(process(ing)? )(\w+ ){0,2}(request)/i"); //next button not coming

		//regex for auto logout
		array_push($this->patternArray['autoLogout'], "/^(?!.*(not|n't|nt))(sessions? )(\w+ ){0,4}(end(s|ing|ed)?|(gets|got) over|finish(es|ed|ing)?|complet(es|ed|e|ing)|expir(ed|e|es|ing))(\s)(\w+ ){0,2}(automatically|by itself)$/i"); //session ends automatically

		array_push($this->patternArray['autoLogout'], "/(sessions? )(\w+ ){0,3}(automatically |by itself )(\w+ ){0,4}(end(s|ing|ed)?|(gets|got) over|finish(es|ed|ing)?|complet(es|ed|e|ing)|expir(e|es|ed|ing))/i"); //session automatically ends
		array_push($this->patternArray['autoLogout'], "/(logging)(\s)(\w+ ){0,3}(out)/i"); //logging out
		array_push($this->patternArray['autoLogout'], "/(session)(\s)(\w+ ){0,2}(expir(e|es|ed|ing))/i"); //session expires

		//regex for easy questions
		array_push($this->patternArray['easyQuestion'], "/(why|what|when|where|how)(\s)(\w+ ){0,3}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(eas(y|ier)|simple)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //why give easy questions
		array_push($this->patternArray['easyQuestion'], "/(eas(y|ier)|simple)(\s)(qs? |questions?|sums?|problems)(\s)(\w+ ){0,3}(not|n't|nt)(\s)(\w+ ){0,3}(com(ing|e)|show(ing)?)/i"); //why give easy questions
		array_push($this->patternArray['easyQuestion'], "/(mindspark|ms|you)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(eas(y|ier)|simple)(\s)(qs?|questions?|sums?|problems?)/i"); //giving difficult questions 
		array_push($this->patternArray['easyQuestion'], "/(^(make|give|send)|(want|need|where))(\s)(\w+ ){0,3}(difficult|hard|tough)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //give difficult questions
		array_push($this->patternArray['easyQuestion'], "/(^(make|give|send)|(want|need|where))(\s)(\w+ ){0,3}(its?|this|that|those|they|them)(\s)(\w+ ){0,2}(difficult|hard|tough)/i"); //give it difficult
		array_push($this->patternArray['easyQuestion'], "/(its?|this|that|those|they|qs?|questions?|sums?|problems?)(\s)(is|are)?(\s)?(very|too|so)?(\s)?(easy|simple)/i"); //questions are easy
		array_push($this->patternArray['easyQuestion'], "/(don?)?(not|n't|nt)(\s)(give|send|make)(\s)(very|too|so)?(\s)?(easy|simple)(\s)(qs? |questions?|sums?|problems?)/i"); //dont give easy questions


		//regex for repeated questions
		array_push($this->patternArray['repeatedQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(different|new)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //give different questions
		array_push($this->patternArray['repeatedQuestion'], "/(change)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems)(\s)?/i"); //give different questions
		array_push($this->patternArray['repeatedQuestion'], "/(don?)?(not|n't|nt)(\s)(give |send|(\b|_)make |want |need )?(\w+ ){0,2}(one type|same|repeat(ed|ing)?)(\s)(\w+ ){0,2}(qs? |questions?|sums?|problems?)/i"); //dont give repeated questions
		array_push($this->patternArray['repeatedQuestion'], "/(qs? |questions?|sums?|problems)(\s)(\w+ ){0,3}((repeat(ed(ly)?|ing)?)|(again and again)|many times|lot of times)/i"); //dont give repeated questions
		array_push($this->patternArray['repeatedQuestion'], "/(don?)?(not|n't|nt)(\s)(repeat)/i"); //dont repeat
		array_push($this->patternArray['repeatedQuestion'], "/(its?|this|that|these|those|they|qs?|questions?|sums?|problems?|ms|mindspark|mind(\-|\s)spark|mindsparks)(\s)(\w+ ){0,3}(repeat(ed(ly)?|ing)?)/i"); //questions repeating
		array_push($this->patternArray['repeatedQuestion'], "/(repeat(ed|ing)?|same|one type|again and again|many times|lot of times)(\s)(\w+ ){0,3}(qs?|questions?|sums?|problems?)/i"); //repeating questions
		array_push($this->patternArray['repeatedQuestion'], "/(repeat(ed|ing)?)(\s)(\w+ ){0,3}(its?|this|that|these|those|they|them)/i"); //repeating questions
		array_push($this->patternArray['repeatedQuestion'], "/(get(ting)?|receiv(e|ing)|got|had|coming)(\w+ ){0,2}(one type|same|repeat(ed|ing)?)(\s)(\w+ ){0,2}(qs? |questions?|sums?|problems?)/i"); //got repeat ques


		//regex for topic progress not increasing
		array_push($this->patternArray['topicProgress'], "/(progress|percent(age)?|%)(\s)(\w+ ){0,3}((MS_TEACHER_TOPIC)(\s)(\w+ ){0,2})?(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |hasn't |has not |never |aren't |are not |isn't |is not |rarely |hardly |(\b|_)not )(\w+ ){0,3}((mov(es?|ed|ing))|(increas(es?|ed|ing))|(progress(ed|es|ing)?)|(go(ing|es)?)|(rais(es?|ed|ing))|(chang(es?|ing|ed)))/i"); //progress not increasing
		array_push($this->patternArray['topicProgress'], "/(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |hasn't |has not |never |aren't |are not |isn't |is not |rarely |hardly |(\b|_)not )(\w+ )(progress|percent(age)?|%)(\s)(\w+ ){0,3}((mov(es?|ed|ing)|increas(es?|ing|ed)|go(ing|es)? (up|ahead|above))|rais(ed?|es|ing)|progress(ing|ed|es)?|(chang(es?|ing|ed)))/i"); // isnt % increasing
		array_push($this->patternArray['topicProgress'], "/(topic|MS_TEACHER_TOPIC)(\s)(\w+ ){0,3}(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |hasn't |has not |never |aren't |are not |isn't |is not |rarely |hardly |(\b|_)not )(\w+ ){0,3}((mov(es?|ing)|increas(es?|ing|ed)|go(ing|es)? (up|ahead|above))|rais(es?|ed|ing)|progress(es|ed|ing)?|(chang(es?|ing|ed)))/i"); // progress not increasing

		array_push($this->patternArray['topicProgress'], "/(mov(es?|ed|ing)|increas(es?|ed|ing)|(chang(es?|ing|ed))|go(ing|es)? (up|ahead|above))(\s)(\w+ ){0,3}(progress(ing|ed|es)?|percent(age)?|%)/i"); //increase (my) percentage
		array_push($this->patternArray['topicProgress'], "/(stuck|stick)(\s)(\w+ ){0,2}(progress|percent(age)?|%)/i"); //stuck at 1%
		array_push($this->patternArray['topicProgress'], "/(progress|percent(age)?|%)(\s)(\w+ ){0,2}(stuck|stick|(remains (\w+ ){0,2}same))/i"); //progress is stuck

		//regex for MS marked me wrong
		array_push($this->patternArray['markedWrong'], "/((correct|right)(ly)?)(\s)(\w+ ){0,5}((\b|_)mark(s|ed)?|got|get(ting)?|giv(en?|ing)|show(es|ed|ing|n)?|appear(s|ed|ing)?|com(es?|ing)|came)(\s)(\w+ ){0,3}(wrong|incorrect)/i"); //correct marked wrong
		array_push($this->patternArray['markedWrong'], "/(answer)(\s)(\w+ ){0,3}((correct|right)(ly)?)(\s)(\w+ ){0,5}((\b|_)mark(s|ed)?|got|get(ting)?|giv(e|ing)|show(es|ed|ing|n)?|appear(s|ed|ing)?|com(es?|ing)|came)(\s)?(\w+ ){0,3}(wrong|incorrect)/i"); //correct marked wrong
		array_push($this->patternArray['markedWrong'], "/(mindspark|ms|mind(\-|\s)spark|mindsparks|sparkies?|sparkys?|sparkis?|computer|you)(\s)(\w+ ){0,3}(wrong|incorrect)/i"); //mindpark (is) wrong
		array_push($this->patternArray['markedWrong'], "/((\b|_)mark(s|ed)?)(\s)(\w+ ){0,3}(wrong|incorrect)/i"); //mindpark (is) wrong
		array_push($this->patternArray['markedWrong'], "/(answer|i)(\s)(\w+ ){0,2}(gave|submit(ted)?|send|sent|mark(s|ed)?|click(ed)?|given|written|wrote|write|type(d)?|choo?sen?|select(ing|ed|s)?)(\s)(\w+ ){0,2}(right|same|correct)/i"); //answer (i) gave (is) right
		array_push($this->patternArray['markedWrong'], "/(answer)(\s)(\w+ ){0,2}(is|was)(\s)(\w+ ){0,2}(right|same|correct)/i"); //answer (i) gave (is) right
		array_push($this->patternArray['markedWrong'], "/(gave|submit(ted)?|send|sent|mark(s|ed|ing)?|click(ed)?|given|written|wrote|write|type(d)?|choo?sen?|select(ing|ed|s)?)(\s)(\w+ ){0,2}(correct|same|right)(\s)(\w+ ){0,4}((\b|_)mark(s|ed|ing)?|got|get(ting)?|giv(e|ing)|show(s|ed|ing|n)?|appear(s|ed|ing)?|com(es?|ing)|came)(\s)(\w+ ){0,2}(wrong|incorrect)/");

		//internet issues
		array_push($this->patternArray['internet'], "/((inter)?net|(up)?load(s|ing)|connection|site|software)(\s)(\w+ ){0,3}(slow|not fast)/i"); //internet slow
		array_push($this->patternArray['internet'], "/(slow(ly)?|late)(\s)(\w+ ){0,3}((inter)?net|(up)?load(s|ing)?|connection)/i"); //slow internet
		array_push($this->patternArray['internet'], "/(data|mindspark|mind(\-|\s)spark|mindsparks|qs?|questions?|its?|animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?|MS_TEACHER_TOPIC|topic)(\s)((\w|\-|:)+ ){0,3}(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+ ){0,3}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)|happen(ed|ing)?|insert(s?|ed|ing)?)/i"); //question not coming
		array_push($this->patternArray['internet'], "/(data|mindspark|mind(\-|\s)spark|mindsparks|qs?|questions?|its?|animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?|MS_TEACHER_TOPIC|topic)(\s)((\w|\-|:)+ ){0,3}(display(s|ing|ed)?|(up)?(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)(\w+ ){0,3}(slow(ly)?|late(r)?)/i"); //question loading slowly
		array_push($this->patternArray['internet'], "/(tak(es?|ing))(\s)(\w+ ){0,3}(time|long)(\s)(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)?/i"); //taking time to load
		array_push($this->patternArray['internet'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)/i"); //taking time to load


		//out of syllabus
		array_push($this->patternArray['outOfSyllabus'], "/(not|n't|out of)(\s)(\w+ ){0,3}(syllabus|course|portion)/i"); //out of syllabus
		array_push($this->patternArray['outOfSyllabus'], "/(not|n't)(\s)(\w+ ){0,3}(learn(ed|t)?|taught|done|cover(s|ed)?|teach|stud(y|ied)|took|tak(e|ing))(\s)(\w+ ){0,3}(class)?/i"); //havent taught in class
		//array_push($this->patternArray['outOfSyllabus'], "/(not|n't)(\w+ ){0,3}(syllabus|course)/i"); //out of syllabus

		//negative feedback
		array_push($this->patternArray['negativeFeedback'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind(\-|\s)spark|mindsparks)(\s)(\w+ ){0,3}(bad|worse|worse|sucks|lame|stupid|dumb|poor|boring|irritating|annoying|not (nice|good|great|awesome|interesting|exciting|fun|superb?|impressive|attractive))/i"); //ms (is) bad
		array_push($this->patternArray['negativeFeedback'], "/(no)(\s)(nice|good|great|awesome|interesting|exciting|fun|superb?|cool|impressive|attractive)(\s)(qs?|questions?|sums?|problems?|activit(y|ies)|images?|animations?|pictures?)/i"); //no nice activity
		array_push($this->patternArray['negativeFeedback'], "/((did|do|can|doesn)?(\s)?(not|nt|n't)(\s)(like|enjoy(ed|s|ing)?|love)|hate|dislike|ashamed)(\s)(\w+ ){0,3}(mindspark|ms|mind(\-|\s)spark|mindsparks|question|ques?|q |sums?|problems?|images?|animations?|pictures?)/i"); //hate (this) question
		array_push($this->patternArray['negativeFeedback'], "/(i )(\w+ ){0,3}(bored)/i"); //i (am) bored
		array_push($this->patternArray['negativeFeedback'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(interesting|exciting|good|nice|cool|better)(\s)(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindsparks?|mind(\-|\s)spark|images?|animations?|pictures?)(\s)?/i"); //give easy questions
		array_push($this->patternArray['negativeFeedback'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?|images?|animations?|pictures?)(\s)(\w+ ){0,2}(interesting|exciting|fun|cool|better)/i"); //give it easy
		array_push($this->patternArray['negativeFeedback'], "/(don)?(not|n't|nt)(\s)(make|give|send|want|need)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?)(\s)?/i"); //give it easy
		array_push($this->patternArray['negativeFeedback'], "/(don)?(not|n't|nt)(\s)(want|wish|feel)(\s)(\w+ ){0,3}(do|attempt|answer|solve|work|study|calculate|find)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?|mindspark|mind(\-|\s)spark|mindsparks)(\s)?/i"); //dont want to do ms


		//positive feedback
		array_push($this->patternArray['positiveFeedback'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind(\-|\s)spark|mindsparks|images?|animations?|pictures?)(\s)(\w+ ){1,3}(good|best|awesome|excellent|wonderful|outstanding|mindblowing|great|cool|nice|interesting|exciting|wonderful|fun|superb?|favorite|impress(ed|ive)|attractive)/i"); //ms is good
		array_push($this->patternArray['positiveFeedback'], "/(love|(\b|_)like|enjoy(ing)?|nice|good|like|best|awesome|excellent|wonderful|superb?|oustanding|mindblowing|interesting|cool|favorite|impress(ed|ive)|attractive)(\s)(\w+ ){0,3}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindsparks?|mind(\-|\s)sparks?|images?|animations?|pictures?)(\s)?/i"); //ms is good
		array_push($this->patternArray['positiveFeedback'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind(\-|\s)spark|mindsparks)(\s)(\w+ ){0,3}(improves)(\s)(\w+ ){0,3}(maths?|mathematics|english)/i"); //ms improves math

		array_push($this->patternArray['it'], "/(INSERT(\s)INTO(\s)adepts.*VALUES)/i");
		array_push($this->patternArray['it'], "/(SELECT.*FROM adepts)/i");
	}

    function makeShortPatternArray()
	{
		//image not loading
		array_push($this->shortPatternArray['image'], "/(animations?|(\b|_)pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)/i"); // not getting image

		//regex for no sparkies
		array_push($this->shortPatternArray['sparkie'], "/(sparkie?s?|sparkys?|sparks)/i"); //no sparkie

		//regex for doubt about question
		array_push($this->shortPatternArray['doubtAboutQuestion'], "/(check)(\s)(\w+ ){0,2}(answer)/i"); //check answer
		array_push($this->shortPatternArray['doubtAboutQuestion'], "/(answer)(\s)(\w+ ){0,3}(wrong)/i"); //check answer
		array_push($this->shortPatternArray['doubtAboutQuestion'], "/explain|how|confus(ed|ing|e)|help|what is/i"); //explain
		array_push($this->shortPatternArray['doubtAboutQuestion'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely |hardly )(\w+ ){0,3}((understand(ing)?)|understood|sense|clear|know)/i"); //dont know
		array_push($this->shortPatternArray['doubtAboutQuestion'], "/(what|how)(\s)(\w+ ){0,3}(do|solve|proceed)/");

		//regex for diff questions
		array_push($this->shortPatternArray['difficultQuestion'], "/(you|mindsparks?|mind(\-|\s)sparks?)(\s)(give|send|make)(\s)(\w+ )?(tough|difficult|hard|tricky|incomprehensible)/i");
		array_push($this->shortPatternArray['difficultQuestion'], "/^(?!.*(give|send|make|want|need|where)).*(tough|difficult|hard|tricky|incomprehensible)/i"); 
		array_push($this->shortPatternArray['difficultQuestion'], "/^(give|send|make|want|need|where)(.)*(eas(y|ier)|simple)/i");
		array_push($this->shortPatternArray['difficultQuestion'], "/(why|what|when|how|where|which).*(giv(es?|ing)|send(ing)?|mak(es?|ing))(.)*(tough|difficult|hard|tricky|incomprehensible)/i"); //give difficult questions

		//regex for no cq
		array_push($this->shortPatternArray['noCQ'], "/(challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs/i");//not getting cq

		//regex for submit issue
		array_push($this->shortPatternArray['submitIssue'], "/(submit|next|continue)(\s)(button)/i"); //cannot click on next

		//regex for auto logout
		array_push($this->shortPatternArray['autoLogout'], "/^(?!.*(not|nt|n't)).*(sessions? )(end(s|ing|ed)?|((gets|got) over)|finish(es|ed|ing)?|complet(es|ed|e|ing)).*$/i"); //session ends automatically

		//regex for easy questions
		array_push($this->shortPatternArray['easyQuestion'], "/(you|mindsparks?|mind(\-|\s)sparks?)(\s)(give|send|make)(\s)(\w+ )?(eas(y|ier)|simple)/i");
		array_push($this->shortPatternArray['easyQuestion'], "/^(?!.*(give|send|make|want|need|where)).*(eas(y|ier)|simple).*$/i"); //give difficult questions
		array_push($this->shortPatternArray['easyQuestion'], "/^(give|send|make|want|need|where)(.)*(tough|difficult|hard|tricky|incomprehensible)/i"); //give difficult questions
		array_push($this->shortPatternArray['easyQuestion'], "/(why|what|when|how|where|which).*(giv(e|es|ing)|send(ing)?|mak(es?|ing))(.)*(eas(y|ier)|simple)/i"); //give difficult questions

		//regex for repeated questions
		array_push($this->shortPatternArray['repeatedQuestion'], "/(same|repeat(ed(ly)?|ing)?)/i"); //give different questions

		//regex for topic progress not increasing
		array_push($this->shortPatternArray['topicProgress'], "/(progress(ing|ed|es)?|percent(age)?|%|mov(es?|ed|ing)|increas(es?|ed|ing)|rais(es?|ing|ed)|stuck)/i"); //progress not increasing

		//regex for MS marked me wrong
		array_push($this->shortPatternArray['markedWrong'], "/(marked|wrong|was (\w+ ){0,1}correct)/i"); //correct marked wrong

		//internet issues
		array_push($this->shortPatternArray['internet'], "/(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)(w+ ){0,3}(slow|fast|late)/i"); //internet slow
		array_push($this->shortPatternArray['internet'], "/^(?!.*(games?|hints?|activit(y|ies)|sparki?e?y?s?)).*(not|n't|nt|never|rarely|hardly)(\s)(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)/i"); //internet slow
		array_push($this->shortPatternArray['internet'], "/((inter)?net|(up)?load(s|ing)?|connection)(\s)(slow|late)/i"); //internet slow
		array_push($this->shortPatternArray['internet'], "/(very|too|damn)(\s)(slow|late)/i");
		array_push($this->shortPatternArray['internet'], "/(tak(es?|ing)|took)(\s)(\w+ ){0,3}(time|long)/i");

		//negative feedback
		array_push($this->shortPatternArray['negativeFeedback'], "/((not|nt|n't)(\s)(so|too|very|v|much)?(\s)?(like |nice|good|great|awesome))/i"); //hate (this) question
		array_push($this->shortPatternArray['negativeFeedback'], "/((\b|_)hate(\b|_)|dislike|bad|sucks|poor|waste|worse|worst|lame|bor(ing|ed))/i"); //hate (this) question
		array_push($this->shortPatternArray['negativeFeedback'], "/(SAD_SMILEY)/i"); //hate (this) question

		//positive feedback
		array_push($this->shortPatternArray['positiveFeedback'], "/(good|great|super|nice|awesome|excellent|love|like|useful|best|SMILEY|impressed|impressive)/i"); //ms is good 

	}

	function isWordJunk($word){
		if((strlen($word) < 3) OR (strcasecmp($word,"AQAD") == 0) OR (strcasecmp($word,"yes") == 0) OR (strcasecmp($word,"bad") == 0) OR (strcasecmp($word,"nil") == 0) OR (strcasecmp($word,"okay") == 0)){
			return 0;
		}
		else{
			
			$check_numeric = 0;
			$one_word = $word;
			$array_non_letters = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9','fuck', 'bitch','sex');
			foreach($array_non_letters as $each_non_letter){
				if(stripos($one_word,$each_non_letter)==true){
					$check_numeric += 1;
				}
			}
			if(($check_numeric>0) OR (strlen($one_word) > 18)){
				return 1;
			}
			else{
				$this->three_word_frequency;
				$frequency_lower_limit = array(0,0,0,50,50,100,100,200,400,800,1200,1800,2400,2800,3000,3400,3500,4400,4600);
				$word_frequency_sum = 0;
				$each_word_array = array();
				for ($i = 0; $i <= ((strlen($one_word))-3); $i++){
					array_push($each_word_array,substr($one_word, $i, 3));
				}
				foreach($each_word_array as $part_each_word_all){
					$part_each_word = strtolower($part_each_word_all);
					$temp_count = isset($frequency_lower_limit[strlen($one_word)])?$this->three_word_frequency[$part_each_word]:0;
					$word_frequency_sum += $temp_count;
				}
				
				if(isset($frequency_lower_limit[strlen($one_word)]) && $word_frequency_sum <= $frequency_lower_limit[strlen($one_word)]) {
					return 1;
				}
				else{
					return 0;
				}
			}
		}
	}
	function isJunkComment($submittedcomment){
    	$query_get_entropy = "SELECT a.threeLetterWord, a.frequency from threeletterwordfrequency a order by a.threeLetterWord asc";
		$data_get_entropy = mysql_query($query_get_entropy) or die(mysql_error());
		while ($selected_three_word = mysql_fetch_array($data_get_entropy)){
			$this->three_word_frequency[$selected_three_word[0]] = $selected_three_word[1];
		}
		$comment_category = "";
		$one_comment = $submittedcomment;
		
		if ((strlen($one_comment)<3) AND (strcasecmp($one_comment,"ok") != 0) AND (strcasecmp($one_comment,"no") != 0) AND (strcasecmp($one_comment,"NA") != 0)) {
			$comment_category = "Junk";
			return 1;
		}
		if((strlen($one_comment)>=3)){
			$response = trim($one_comment);
			$wordArray = preg_split('/\s+/',$response, -1, PREG_SPLIT_NO_EMPTY);
			$lengthOfComment = count($wordArray);
			//echo "after trimming, we have:".$response." and the total number of words are ".$lengthOfComment." <br>";
			$num_row = 0;
			$total_lower_limit = 0;
			foreach($wordArray as $each_word){
				$num_row += $this->isWordJunk($each_word);
			}
			if($num_row >= (0.6*$lengthOfComment)){
				$comment_category = "Junk";
				return 1;
			}
		}
		//if ($comment_category == ""){
			$comment_category = "Non-junk";
			return 0;
		//}
	}

}