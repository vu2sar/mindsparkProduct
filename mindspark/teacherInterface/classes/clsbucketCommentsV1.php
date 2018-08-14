<?php
class commentCategorization
{
	var $patternArray;
	var $shortPatternArray;
	var $ttList;
	function commentCategorization()
	{
		$this->ttList = array();
		$this->patternArray = array();		
		$this->patternArray['image'] = array();
		$this->patternArray['sparkie'] = array();
		$this->patternArray['difficultQuestion'] = array();
		$this->patternArray['noCQ'] = array();
		$this->patternArray['submitIssue'] = array();
		$this->patternArray['autoLogout'] = array();
		$this->patternArray['easyQuestion'] = array();
		$this->patternArray['repeatedQuestion'] = array();
		$this->patternArray['topicProgress'] = array();
		$this->patternArray['markedWrong'] = array();
		$this->patternArray['internet'] = array();
		$this->patternArray['outOfSyllabus'] = array();
		$this->patternArray['positiveFeedback'] = array();
		$this->patternArray['negativeFeedback'] = array();
	
		$this->patternArray['none'] = array();
		$this->patternArray['junk'] = array();

		$this->shortPatternArray = array();
		$this->shortPatternArray['image'] = array();
		$this->shortPatternArray['sparkie'] = array();
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
		$this->makePatternArray();
		$this->makeshortPatternArray();

		$query = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE subjectno = 2 AND customTopic = 0 AND live = 1 AND teacherTopicDesc != 'Time'";
	    $result = mysql_query($query) or die("SQL error: Here I am in elsey".mysql_error());
	    while($line = mysql_fetch_array($result)) 
	    	{array_push($this->ttList, "/(\s)(".$line['teacherTopicDesc'].")((\s)Custom(\s)?(\-)?(\s)?[0-9]+)?/i");}
	}

	//php functions
	function mark($response, $arrForChanged= '')
	{ //master function that calls functions spellCheck, isJunk, and bucketize and sets the global variables $responseAfterSpellCheck, $isJunk and $bucket respectively
		//global $response, $responseAfterSpellCheck, $isJunk, $bucket;
		$responseAfterSpellCheck = $this->spellCheck($response);
		$isJunk = $this->isJunk($responseAfterSpellCheck);
		if($isJunk == 0)
			$bucket = $this->bucketize($responseAfterSpellCheck);
		else
			$bucket = 'junk';
		$systemCategory	=	array("image"=>"Images not appearing","sparkie"=>"Sparkie","difficultQuestion"=>"Difficult questions","noCQ"=>"No CQ","submitIssue"=>"Submit Issue","autoLogout"=>"Auto Logout","easyQuestion"=>"Easy questions","repeatedQuestion"=>"Repeated question","topicProgress"=>"Topic progress not moving","markedWrong"=>"Correct Answer marked wrong","internet"=>"IT/Internet","outOfSyllabus"=>"Question out of Syllabus","positiveFeedback"=>"Positive Feedback","negativeFeedback"=>"Negative Feedback","junk"=>"Junk","none"=>"Other");
			
		return $systemCategory[$bucket];
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

	$pattern = "%mindsparks?|mind-sparks?|mind(\s)sparks?%i";
	$replace = " MINDSPARK ";
	$str = preg_replace($pattern, $replace, $str, -1, $count);

	$pattern = "%sparkie|sparky|sparki%i";
	$replace = " SPARKIE ";
	$str = preg_replace($pattern, $replace, $str, -1, $count);

	$pattern = "%sparkies|sparkys|sparkis%i";
	$replace = " SPARKIES ";
	$str = preg_replace($pattern, $replace, $str, -1, $count);

	
	if(preg_match("/percentage|%|percent/i", $str) == 1)
	{
		$replace = " MS_TEACHER_TOPIC ";
		$str = preg_replace($this->ttList, $replace, $str, -1, $count);
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
		$newPatternArray[$srno] = "%".$line1['incorrectSpelling']."(\s)%i";
		$newReplacementArray[$srno] = $line1['correctSpelling']." ";
		$srno++;

	}
	$str = preg_replace($newPatternArray, $newReplacementArray, $str);

	if(preg_match("/percentage|%|percent/i", $str) == 1)
	{
		$replace = " MS_TEACHER_TOPIC ";
		$str = preg_replace($ttList, $replace, $str, -1, $count);
	}

	$pattern = "%(\s)\s++%i";
	$replace = '$1';
	$str = preg_replace($pattern, $replace, $str,-1,$count);

	return $str;
}

	function isJunk($response)
{
	$extra =0;
	$wordArray = str_word_count($response, 1);
	$lengthOfComment = count($wordArray);
	$commaSeparated = implode("\", \"", $wordArray);
	$query1 = "SELECT word FROM educatio_educat.dictionary WHERE word IN (\"".$commaSeparated."\")";
	$result1 = mysql_query($query1) or die("SQL error: Here I am in 2222".mysql_error());
	//while($line12=mysql_fetch_array($result1)){echo $line12['word'];}
	$num_rows = mysql_num_rows($result1);

	$pattern = '/(PartOfExpression|Commentator|Email|mindspark|mindsparks|mind spark|sparkie|sparkies|sparky|sparkys|sparki|sparkis|SMILEY|SAD_SMILEY|MS_TEACHER_TOPIC)/i';
	$extra = preg_match_all($pattern, $response, $matches);
	      //print_r($matches);
	      //echo "Extra:".$extra."end";
	$pattern = '/(good|nice|poor|great|super|bad|sucks|awesome|easy|confused|confusing|simple|image|picture|pics|hard|help|bullseye|how|bor(ing|ed)|:\-\)|:\-\()/i';
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
	          	//echo "pattern:".$pattern;
				break;
			}
		}
		if(strcmp($bucket, "none") != 0)
				break;
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
	      array_push($this->patternArray['image'], "/(didn't |did not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not |rarely |half )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |shown |com(es?|ing) |came )(\w+ ){0,4}(animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)/i"); // not getting image
	      array_push($this->patternArray['image'], "/(\w+ ){0,5}(animations? |pics? |pictures? |images? |figures? |diagrams? |charts? |visuals? |jpgs? |pngs? |protractors? |patterns? |number(\s)?lines? |shapes? |scales? )(\w+ ){0,3}(can|is|does|are|did)?(\s)?(nt |n't |not? |rarely |never |half )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?|there|found|visible|clear)(\s)?/i"); //image not showing
	      array_push($this->patternArray['image'], "/(can|is|does)?(\s)?(nt |n't |not? |rarely |never |half )(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|visible|giv(s|en?|ing)|insert(s?|ed|ing)?)(\s)(\w+ ){0,3}(animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //not showing images
	      array_push($this->patternArray['image'], "/(\w+ ){0,5}(no )(\w+ ){0,3}(animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //no image
	      array_push($this->patternArray['image'], "/(\w+ ){0,5}(give|send|load|show|display|insert|put)(\s)(\w+ ){0,3}(animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)?/i"); //no image
	      
	      //regex for no sparkies
	      array_push($this->patternArray['sparkie'], "/(\w+ ){0,5}(no |lost )(\w+ ){0,3}(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //no sparkie
	      array_push($this->patternArray['sparkie'], "/(\w+ ){0,5}(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |giv(es?|ing|en) |gave |receiv(es?|ing|ed)? )(\w+ ){0,4}(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //not getting sparkie
	      array_push($this->patternArray['sparkie'], "/(\w+ ){0,3}(see(ing)? |shown |view(ing)? |get(ting)? |got |had |have |receiv(e|ing|ed)? |gave )(\w+ ){0,4}(only )?(only |\d+ |0 |zero |one )(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}/i"); //got 0 sparkie
	      array_push($this->patternArray['sparkie'], "/(only )?(only |\d+ |0 |zero |one )(sparkie?s?|sparkys?|sparks)(\w+ ){0,3}(display(ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s|ed|ing)?|there|found)(\s)?(\w+ ){0,4}/i");//only 1 sparkie
	      array_push($this->patternArray['sparkie'], "/(sparkie?s?|sparkys?|sparks)(\s)?(\w+ ){0,4}(didn't |doesn't |does not |did not |don't |do not? |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+){0,3}(com(es?|ing)|mov(es?|ing|ed)|go(es?|ing)|increas(es?|ed|ing))/i"); //sparkie (count) not moving up

	      //regex for diff questions
	      array_push($this->patternArray['difficultQuestion'], "/(why|what|when|where|how)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(tough|difficult|hard|tricky|incomprehensible)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //why give easy questions
	      array_push($this->patternArray['difficultQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(eas(y|ier)|simple)(\s)(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)?/i"); //give easy questions
	      array_push($this->patternArray['difficultQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(its?|this|that|those|they|them)(\s)(\w+ ){0,2}(eas(y|ier)|simple)/i"); //give it easy
	      array_push($this->patternArray['difficultQuestion'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)(is|are)?(\s)?(very|too|so)?(\s)?(tough|difficult|hard|tricky|incomprehensible)/i"); //questions are tough
	      array_push($this->patternArray['difficultQuestion'], "/(tough|difficult|hard|tricky|incomprehensible)(\s)(qs?|questions?|sums?|problems?)(\s)(\w+ ){0,2}(coming|appearing)/i"); //difficult questions coming
	      array_push($this->patternArray['difficultQuestion'], "/(mindspark|ms)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(tough|difficult|hard|tricky|incomprehensible)(\s)(qs?|questions?|sums?|problems?)/i"); //giving difficult questions 
	      array_push($this->patternArray['difficultQuestion'], "/(don)?(not|n't|nt)(\s)(give|send|make)(\s)(very|too|so)?(\s)?(tough|difficult|hard|tricky)(\s)(qs? |questions?|sums?|problems?)/i"); //dont give tough questions
	      array_push($this->patternArray['difficultQuestion'], "/(fac(ing|ed?)|get(ting)?|got)(\s)(\w+ ){0,2}(very|too|so|much)?(\s)?(\w+ ){0,3}(difficulty)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems?)/i"); //facing difficulty in questions
	      array_push($this->patternArray['difficultQuestion'], "/(difficulty)(\s)(\w+ ){0,3}(qs? |questions?|sums?|problems?)(\s)(\w+ ){0,3}(much|lot)?(\s)?/i"); //difficulty is a lot
	      
	      

	      array_push($this->patternArray['doubtAboutQuestion'], "/(difficulty?|hard)(\s)(\w+ )?(understand(ing)?|answer(ing)?)/i");//diff to understand
	      array_push($this->patternArray['doubtAboutQuestion'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely )(\w+ ){0,3}((understand(ing)?)|understood|sense|clear|know how|catch)/i"); //not understanding
	      array_push($this->patternArray['doubtAboutQuestion'], "/(whats?|[^a-z]hows?|whys?)(\s)(\w+ ){0,4}(ans(wers?)?|formula(e)?(s)?)/i"); //whats the answer
	      array_push($this->patternArray['doubtAboutQuestion'], "/^(?!.*(n't|[^a-z]not)).*(give|send|want|need).*(explanations?).*$/i"); //give explanation
	      array_push($this->patternArray['doubtAboutQuestion'], "/(help|explain)(\s)(\w+ ){0,4}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)/i"); //help with question
	      array_push($this->patternArray['doubtAboutQuestion'], "/([^a-z]how)(\s)(\w+ ){0,3}(solve|do|attempt|calculate|find)(\s)(\w+ ){0,3}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?)(\s)?/i"); //how to do this
	      array_push($this->patternArray['doubtAboutQuestion'], "/(check)(\s)(\w+ ){0,3}(answer)/i"); //check answer
	      array_push($this->patternArray['doubtAboutQuestion'], "/(answer|solution)(\s)(\w+ ){0,3}(given|written)(\s)(\w+ ){0,2}(wrong)/i"); //check answer
	      array_push($this->patternArray['doubtAboutQuestion'], "/(given|written)(\s)(\w+ ){0,3}(answer|solution)(\s)(\w+ ){0,2}(wrong)/i"); //check answer
	      array_push($this->patternArray['doubtAboutQuestion'], "/(([^a-z]how)|(^how))(\s)(\w+ ){0,3}(solve|do|attempt|calculate|find)/i"); //how to do this
  
	      	      //regex for no cq)
	      array_push($this->patternArray['noCQ'], "/(didn't |did not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |are not |isn't |is not |rarely )(\w+ ){0,3}(see(ing)? |view(ing)? |get(ting)? |got |shown)(\w+ ){0,4}(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs))/i");//not getting cq
	      array_push($this->patternArray['noCQ'], "/(((challeng(e|ing)|bonus|super) (qs?|questions?|sums?|problems?)|cqs))(\s)(\w+ ){0,3}(can|is|does|are)?(\s)?(nt |n't |not? |rarely )(\w+ ){0,2}(appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found)(\s)?(\w+ ){0,4}/i"); //cq not showing
	      array_push($this->patternArray['noCQ'], "/(can|is|does)?(nt |n't |not? |rarely )(\w+ ){0,2}(appear(s|ing|ed)?|show(s|ing|ed|n)?) (\w+ ){0,3}(((challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs))/i"); //not showing cq
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
	      array_push($this->patternArray['easyQuestion'], "/(mindspark|ms)(\s)(\w+ ){0,2}(mak(es?|ing)|giv(es?|ing)|send(ing)?)(\s)(\w+ ){0,3}(eas(y|ier)|simple)(\s)(qs?|questions?|sums?|problems?)/i"); //giving difficult questions 
	      array_push($this->patternArray['easyQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(difficult|hard|tough)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //give difficult questions
	      array_push($this->patternArray['easyQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(its?|this|that|those|they|them)(\s)(\w+ ){0,2}(difficult|hard|tough)/i"); //give it difficult
	      array_push($this->patternArray['easyQuestion'], "/(its?|this|that|those|they|qs?|questions?|sums?|problems?)(\s)(is|are)?(\s)?(very|too|so)?(\s)?(easy|simple)/i"); //questions are easy
	      array_push($this->patternArray['easyQuestion'], "/(don?)?(not|n't|nt)(\s)(give|send|make)(\s)(very|too|so)?(\s)?(easy|simple)(\s)(qs? |questions?|sums?|problems?)/i"); //dont give easy questions
	      

	      //regex for repeated questions
	      array_push($this->patternArray['repeatedQuestion'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(different|new)(\s)(qs? |questions?|sums?|problems)(\s)?/i"); //give different questions
	      array_push($this->patternArray['repeatedQuestion'], "/(don?)?(not|n't|nt)(\s)(give |send|[^a-z]make |want |need )?(\w+ ){0,2}(one type|same|repeat(ed|ing)?)(\s)(\w+ ){0,2}(qs? |questions?|sums?|problems?)/i"); //dont give repeated questions
	      array_push($this->patternArray['repeatedQuestion'], "/(don?)?(not|n't|nt)(\s)(repeat)/i"); //dont repeat
	      array_push($this->patternArray['repeatedQuestion'], "/(its?|this|that|these|those|they|qs?|questions?|sums?|problems?|ms|mindspark|mind-spark|mindsparks)(\s)(\w+ ){0,3}(repeat(ed|ing)?)/i"); //questions repeating
	      array_push($this->patternArray['repeatedQuestion'], "/(repeat(ed|ing)?|same|one type)(\s)(\w+ ){0,3}(qs?|questions?|sums?|problems?)/i"); //repeating questions
	      array_push($this->patternArray['repeatedQuestion'], "/(repeat(ed|ing)?)(\s)(\w+ ){0,3}(its?|this|that|these|those|they|them)/i"); //repeating questions
	      array_push($this->patternArray['repeatedQuestion'], "/(get(ting)?|receiv(e|ing)|got|had)(\w+ ){0,2}(one type|same|repeat(ed|ing)?)(\s)(\w+ ){0,2}(qs? |questions?|sums?|problems?)/i"); //got repeat ques


	       //regex for topic progress not increasing
	      array_push($this->patternArray['topicProgress'], "/(progress|percent(age)?|%)(\s)(\w+ ){0,3}((MS_TEACHER_TOPIC)(\s)(\w+ ){0,2})?(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely |[^a-z]not )(\w+ ){0,3}(mov(es?|ing)|increas(es?|ing)|go(ing|es)? (up|ahead|above))|rais(e|ing)|progress(ing)?/i"); //progress not increasing
	      array_push($this->patternArray['topicProgress'], "/(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely |[^a-z]not )(\w+ )(progress|percent(age)?|%)(\s)(\w+ ){0,3}(mov(es?|ing)|increas(es?|ing)|go(ing|es)? (up|ahead|above))|rais(e|ing)|progress(ing)?/i"); // isnt % increasing
	       array_push($this->patternArray['topicProgress'], "/(topic|MS_TEACHER_TOPIC)(\s)(\w+ ){0,3}(didn't |doesn't |does not |did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely |[^a-z]not )(\w+ ){0,3}(mov(es?|ing)|increas(es?|ing)|go(ing|es)? (up|ahead|above))|rais(e|ing)|progress(ing)?/i"); // progress not increasing

	      array_push($this->patternArray['topicProgress'], "/(mov(es?|ing)|increas(es?|ing)|go(ing|es)? (up|ahead|above))(\s)(\w+ ){0,3}(progress|percent(age)?|%)/i"); //increase (my) percentage
	      array_push($this->patternArray['topicProgress'], "/(stuck|stick)(\s)(\w+ ){0,2}(progress|percent(age)?|%)/i"); //stuck at 1%
	      array_push($this->patternArray['topicProgress'], "/(progress|percent(age)?|%)(\s)(\w+ ){0,2}(stuck|stick)/i"); //progress is stuck

	      //regex for MS marked me wrong
	      array_push($this->patternArray['markedWrong'], "/((correct|right)(ly)?)(\s)(\w+ ){0,5}([^a-z]mark(s|ed)?|got|get(ting)?|giv(e|ing)|show(es|ed|ing|n)?|appear(s|ed|ing)?|com(es?|ing)|came)(\s)(\w+ ){0,3}(wrong)/i"); //correct marked wrong
	      array_push($this->patternArray['markedWrong'], "/(answer)(\s)(\w+ ){0,3}((correct|right)(ly)?)(\s)(\w+ ){0,5}([^a-z]mark(s|ed)?|got|get(ting)?)(\s)?(\w+ ){0,3}(wrong)/i"); //correct marked wrong
	      array_push($this->patternArray['markedWrong'], "/(mindspark|ms|mind-spark|mindsparks|sparkies?|sparkys?|sparkis?|computer|you)(\s)(\w+ ){0,3}(wrong)/i"); //mindpark (is) wrong
	      array_push($this->patternArray['markedWrong'], "/([^a-z]mark(s|ed)?)(\s)(\w+ ){0,3}(wrong)/i"); //mindpark (is) wrong
	      array_push($this->patternArray['markedWrong'], "/(answer)(\s)(\w+ ){0,2}(gave|submit(ted)?|send|sent|mark(s|ed)?|click(ed)?|given|written|wrote|write|type(d)?)(\s)(\w+ ){0,2}(right|same|correct|)/i"); //answer (i) gave (is) right
	      array_push($this->patternArray['markedWrong'], "/(answer)(\s)(\w+ ){0,2}(is|was)(\s)(\w+ ){0,2}(right|same|correct)/i"); //answer (i) gave (is) right

	      //internet issues
	      array_push($this->patternArray['internet'], "/((inter)?net|(up)?load(s|ing)|connection|site|software)(\s)(\w+ ){0,3}(slow|not fast)/i"); //internet slow
	      array_push($this->patternArray['internet'], "/(slow(ly)?|late)(\s)(\w+ ){0,3}((inter)?net|(up)?load(s|ing)|connection)/i"); //slow internet
	      array_push($this->patternArray['internet'], "/(data|mindspark|mind-spark|mindsparks|qs?|questions?|its?|animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)(\w+ ){0,3}(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not )(\w+ ){0,3}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|there|found|work(ed|s|ing)|happen(ed|ing)?|insert(s?|ed|ing)?)/i"); //question not coming
	      array_push($this->patternArray['internet'], "/(data|mindspark|mind-spark|mindsparks|qs?|questions?|its?|animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)(\s)(\w+ ){0,3}(display(s|ing|ed)?|(up)?(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)(\w+ ){0,3}(slow(ly)?|late(r)?)/i"); //question loading slowly
	      array_push($this->patternArray['internet'], "/(tak(es?|ing))(\s)(\w+ ){0,3}(time)(\s)(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)?/i"); //taking time to load

	       //out of syllabus
	      array_push($this->patternArray['outOfSyllabus'], "/(not|n't|out of)(\s)(\w+ ){0,3}(syllabus|course)/i"); //out of syllabus
	      array_push($this->patternArray['outOfSyllabus'], "/(not|n't)(\s)(\w+ ){0,3}(taught|done|teach|stud(y|ied)|took|tak(e|ing))(\s)(\w+ ){0,3}(class)/i"); //havent taught in class
	      //array_push($this->patternArray['outOfSyllabus'], "/(not|n't)(\w+ ){0,3}(syllabus|course)/i"); //out of syllabus

	        //negative feedback
	      array_push($this->patternArray['negativeFeedback'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind-spark|mindsparks)(\s)(\w+ ){0,3}(bad|sucks|poor|boring|irritating|annoying|not (nice|good|great|awesome|interesting|exciting|fun|superb))/i"); //ms (is) bad
	      array_push($this->patternArray['negativeFeedback'], "/(no)(\s)(nice|good|great|awesome|interesting|exciting|fun|superb)(\s)(qs?|questions?|sums?|problems?|activit(y|ies))/i"); //no nice activity
	      array_push($this->patternArray['negativeFeedback'], "/((did|do|can|doesn)?(\s)?(not|nt|n't)(\s)(like|enjoy(ed|s|ing)?|love)|hate|dislike|ashamed)(\s)(\w+ ){0,3}(mindspark|ms|mind-spark|mindsparks|question|ques?|q |sums?|problems?)/i"); //hate (this) question
	      array_push($this->patternArray['negativeFeedback'], "/(i )(\w+ ){0,3}(bored)/i"); //i (am) bored
	      array_push($this->patternArray['negativeFeedback'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(interesting|exciting|good|nice)(\s)(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind-spark|mindsparks)(\s)?/i"); //give easy questions
	      array_push($this->patternArray['negativeFeedback'], "/(make|give|send|want|need|where)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?)(\s)(\w+ ){0,2}(interesting|exciting|fun)/i"); //give it easy
	      array_push($this->patternArray['negativeFeedback'], "/(don)?(not|n't|nt)(\s)(make|give|send|want|need)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?)(\s)?/i"); //give it easy
	      array_push($this->patternArray['negativeFeedback'], "/(don)?(not|n't|nt)(\s)(want|wish|feel)(\s)(\w+ ){0,3}(do|attempt|answer|solve|work|study|calculate|find)(\s)(\w+ ){0,3}(its?|this|that|those|they|them|qs?|questions?|sums?|problems?|mindspark|mind-spark|mindsparks)(\s)?/i"); //dont want to do ms

	      //positive feedback
	      array_push($this->patternArray['positiveFeedback'], "/(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind-spark|mindsparks)(\s)(\w+ ){1,3}(good|awesome|excellent|wonderful|outstanding|mindblowing|great|nice|interesting|exciting|wonderful|fun|superb)/i"); //ms is good
	      array_push($this->patternArray['positiveFeedback'], "/(love|[^a-z]like|enjoy(ing)?)(\s)(\w+ ){0,3}(its?|this|that|those|these|they|qs?|questions?|sums?|problems?|mindspark|mind-spark|mindsparks)(\s)?/i"); //ms is good

	    }

	    function makeShortPatternArray()
		{
	     //image not loading
	      array_push($this->shortPatternArray['image'], "/(animations?|[^a-z]pics?|pictures?|images?|figures?|diagrams?|charts?|visuals?|jpgs?|pngs?|protractors?|patterns?|number(\s)?lines?|shapes?|scales?)/i"); // not getting image
	      
	      //regex for no sparkies
	      array_push($this->shortPatternArray['sparkie'], "/(sparkie?s?|sparkys?|sparks)/i"); //no sparkie

	      //regex for doubt about question
	      array_push($this->shortPatternArray['doubtAboutQuestion'], "/(check)(\s)(\w+ ){0,2}(answer)/i"); //check answer
	      array_push($this->shortPatternArray['doubtAboutQuestion'], "/(answer)(\s)(\w+ ){0,3}(wrong)/i"); //check answer
	      array_push($this->shortPatternArray['doubtAboutQuestion'], "/explain|how|confus(ed|ing|e)|help|what is/i"); //explain
	      array_push($this->shortPatternArray['doubtAboutQuestion'], "/(didn't | did not |don't |do not |not? |am not |cannot |can't |unable |not able |have not |haven't |never |aren't |are not |isn't |is not |rarely )(\w+ ){0,3}((understand(ing)?)|understood|sense|clear|know)/i"); //dont know
	  
	      //regex for diff questions
	      array_push($this->shortPatternArray['difficultQuestion'], "/^(?!.*(give|send|make|want|need|where)).*(tough|difficult|hard|tricky|incomprehensible).*$/i"); 
	      array_push($this->shortPatternArray['difficultQuestion'], "/(give|send|make|want|need|where)(.)*(eas(y|ier)|simple)/i");
	      array_push($this->shortPatternArray['difficultQuestion'], "/(why|what|when|how|where|which).*(giv(es?|ing)|send(ing)?|mak(es?|ing))(.)*(tough|difficult|hard|tricky|incomprehensible)/i"); //give difficult questions
	      array_push($this->shortPatternArray['difficultQuestion'], "/(tough|difficult|hard|tricky|incomprehensible)/i"); //very hard

	      //regex for no cq
	      array_push($this->shortPatternArray['noCQ'], "/(challeng(e|ing)|bonus|super) (qs? |questions?|sums?|problems?)|cqs/i");//not getting cq

	      //regex for submit issue
	       array_push($this->shortPatternArray['submitIssue'], "/(submit|next|continue)(\s)(button)/i"); //cannot click on next
	  
	       //regex for auto logout
	      array_push($this->shortPatternArray['autoLogout'], "/^(?!.*(not|nt|n't)).*(sessions?|end(s|ing|ed)?|((gets|got) over)|finish(es|ed|ing)?|complet(es|ed|e|ing)).*$/i"); //session ends automatically

	       //regex for easy questions
	      array_push($this->shortPatternArray['easyQuestion'], "/^(?!.*(give|send|make|want|need|where)).*(eas(y|ier)|simple).*$/i"); //give difficult questions
	      array_push($this->shortPatternArray['easyQuestion'], "/(give|send|make|want|need|where)(.)*(tough|difficult|hard|tricky|incomprehensible)/i"); //give difficult questions
	      array_push($this->shortPatternArray['easyQuestion'], "/(why|what|when|how|where|which).*(giv(e|es|ing)|send(ing)?|mak(es?|ing))(.)*(eas(y|ier)|simple)/i"); //give difficult questions
	      array_push($this->shortPatternArray['easyQuestion'], "/(eas(y|ier)|simple)/i"); //give difficult questions

	      //regex for repeated questions
	      array_push($this->shortPatternArray['repeatedQuestion'], "/(same|repeat(ed|ing)?)/i"); //give different questions
	 
	       //regex for topic progress not increasing
	      array_push($this->shortPatternArray['topicProgress'], "/(progress(ing)?|percent(age)?|%|mov(es?|ing)|increas(es?|ing)|rais(e|ing)|stuck)/i"); //progress not increasing

	      //regex for MS marked me wrong
	      array_push($this->shortPatternArray['markedWrong'], "/(marked|wrong|was (\w+ ){0,1}correct)/i"); //correct marked wrong

	      //internet issues
	      array_push($this->shortPatternArray['internet'], "/(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)(\s)(w+ ){0,3}(slow|fast|late)/i"); //internet slow
	      array_push($this->shortPatternArray['internet'], "/^(?!.*(games?|hints?|activit(y|ies)|sparki?e?y?s?)).*(not|n't|nt|never|rarely)(\s)(\w+ ){0,2}(display(s|ing|ed)?|(up)?load(s|ing|ed)?|appear(s|ing|ed)?|show(s|ing|ed|n)?|seen|com(es?|ing)|came|insert(s?|ed|ing)?)/i"); //internet slow
	      array_push($this->shortPatternArray['internet'], "/((inter)?net|(up)?load(s|ing)?|connection|slow|late)/i"); //internet slow

	      //negative feedback
	      array_push($this->shortPatternArray['negativeFeedback'], "/((not|nt|n't)(\s)(so|too|very|v|much)?(\s)?(like |nice|good|great|awesome))/i"); //hate (this) question
	      array_push($this->shortPatternArray['negativeFeedback'], "/([^a-z]hate[^a-z]|dislike|bad|sucks|poor|bor(ing|ed))/i"); //hate (this) question
	      array_push($this->shortPatternArray['negativeFeedback'], "/(SAD_SMILEY)/i"); //hate (this) question

	      //positive feedback
	      array_push($this->shortPatternArray['positiveFeedback'], "/(good|awesome|excellent|love|like|SMILEY)/i"); //ms is good 

	    }

}