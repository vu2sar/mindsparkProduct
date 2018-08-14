<?php 
 
error_reporting(1); 
//include_once($_SERVER['DOCUMENT_ROOT']."/connectPDO.php"); 
function spell_check($contentType,$contentCode,$array_of_strings,$nonSpellCheckFields=array(),$applyInlineCss = 0) 
{     
	$sp_errors = $outtext = $GLOBALS['ignoreWords'] = array();
 	$intext2 = move_spans($array_of_strings); 
    $sp_errors = check_spelling($intext2,$nonSpellCheckFields,$contentType,$contentCode,$applyInlineCss); 
    $intext2 = removeSpellCheckFormatting($intext2,$applyInlineCss); 
    $outtext1 = put_highlights($intext2, $sp_errors,$applyInlineCss);
	$outtext2 = put_ignoreWordsHighlights($outtext1, $GLOBALS['ignoreWords'],$nonSpellCheckFields);
	return (array($outtext2, $sp_errors));     
} 
 
function move_spans($strings) 
{    
	$new_array = array(); 
	foreach ($strings as $key => $string) 
    {     
        if($string!== null && $string!=='') 
        { 
            $string = preg_replace("/([a-z0-9]+)(<span[^>]*>)([a-z0-9]+)/","$2$1$3",$string); 
            $string = preg_replace("/([a-z0-9]+)<\/span>([a-z0-9]+)/","$1$2</span>",$string); 
        } 
        $new_array[$key] = $string; 
    } 
	return($new_array); 
} 
 
function check_spelling($strings,$nonSpellCheckFields,$contentType,$contentCode,$applyInlineCss) 
{    // This function will receive an array of strings that need to be spell checked. 
    // What it does: 1. concatenate the strings 2. remove the duplicates 3. remove the quicklist words. 4. Run the remaining words through the correct_spelling function and collect and return the array of those misspelled words. 
 
    static $quicklist = array(".",",","!","?","\"",":","'","-","(","/",")",";","^","*","the","is","a","to","The","of","in","answer","that","and", "was","not","about","with","are","again","word","for","be","d","Which","you","it","correct","your","Think","on","an","this","means","his", "sentence","her","he","can","from","I","something","at","because","does","In","or","following","by","has","used","have","He","as","which","A", "What","she","passage","This","will","words","had","would","She","when","they","one","their","It","If","We","do","were","To","like","up","very", "conversation","out","all","someone","people","we","did","use","They","who","make","us","but","time","these","him","so","could","get","says","my", "two","person","Read","BEST","how","no","been","You","other","more","them","should","mean","think","what","blank","Fill","show","When","place","go", "Why","being","many","say","new","sound","There","cannot","Choose","me","before","want","school","than","made","if","need","after","How","wanted", "only","right",         "there","into","here","angry","friends","story","sentences","friend","way","give","take","got","first","question","things","shows","part","poem",         "NOT","mother","some","best","always","just","day","different","describe","below","Listen","too","boy","going","fits","help","down","tells","work",         "form","good","refers","talking","given","money","line","long","My","options","around","teacher","That","end","underlined","write","verb","any",         "Because","example","its","describes","At","house","information","phrase","play","don't","while","Based","lot","see","Correct","tone","much",         "know","beginning","excited","home","By","same","name","away","food","scared","water","Although","poet","said","car","through","meaning","thought",         "little","An","Here","For","dog","above","over","most","students","Since","wants","complete","went","happy","off","another","where","well",         "room","back","makes","author","heard","mentions","find","wrong","never","From","Ram","carefully","movie","According","towards","As","subject",         "must","father","eat","since","each","told","books","night","situation","last","doesn't","come","animals","our","life","together","Her","speech",         "case","old","family","woman","option","asked","making","found","few","speaker","read","talk","without","Where","using","B","book","So","lines",         "letter","girl","group","didn't","between","described","suggests","completes","children","even","may","tell","article","look","feel","Mr",         "couldn't","sounds","felt","kind","today","city","man","years","sister","now","calm","own","class","trying","stop","during","His","ask","box",         "cold","become","mention","object","bad","needs","idea","paragraph","sure","uses","difficult","both","parents","done","replace","important",         "taking","act","audio","true","blanks","lost","mom","One","put","change","replaced","statements","After","worried","written","problem","action",         "thinks","big","Who","every","accident","light","really","fit","great","context","also","office","am","sad","answering","Thanks","adjective",         "Beethoven","hear","under","left","statement","eyes","called","started","writing","letters","While","late","playing","answers","please","cake",         "loud","party","usually","fact","often","announcement","better","morning","nervous","then","report","learn","three","adverb","according",         "correctly","fight","head","himself","fast","reason","seen","Do","test","rhymes","speak","gave","TRUE","brother","short","surprised","purpose",         "taken","feeling","implies","buy","girls","narrator","past","took","asking","against","tall","quickly","Both","keep","near","based","Something",         "hard","anything","train","begins","face","extremely","happened","watch","small","knew","All","basketball","Rahul","comparison","turn","trees",         "herself","asks","preposition","happen","advice","call","believe","still","common","move","curious","talks","lives","can't","tense","child",         "instead","quite","Mary","meet","Can","Hence","cause","order","Dev","compared","Jack","And","event","large","four","why","giving","understand",         "simple","building","game","Maya","fisherman","able","mentioned","movies","present","already","news","black","chocolate","add","rhyme","music",         "refer","land","visit","else","drive","states","serious","sea","plural","Wrong","actions","I'm","others","ate","having","opposite","working",         "flight","five","thing","agree","strong","famous","sometimes","world","Many","times","writers","Mihir","saw","live","No","along","speaking","men",         "soon","getting","hence","incorrect","weather","happening","secret","bus","stopped","animal","studying","once","seems","description","indifferent"); 
     
		$ci = & get_instance();
        //$stmt=$ci->db->query("select word from ignoreWordsList where contentType='".$contentType."' and contentID in (".$contentCode.")"); 
        //$reviewEssayIDsRes = $stmt->result_array();
		
		//foreach($reviewEssayIDsRes as $row)
        //{ 
            //$GLOBALS['ignoreWords'][] = $row['word']; 
        //}
	
	static $split_chars = "/([_=?,\\\[\]\.\/()<>{}!*%&+;\"@#:^])|(?:[ \f\n\r\t])/";    //" The tilde character is not there currently 
    $errors = array(); 
 
    $strings = removeNonSpellCheckFields($strings,$nonSpellCheckFields); 
    $checktext = implode(" ",$strings); 
 
    $checktext = rip_tags($checktext); 
    $checktext = preg_replace("/([a-z])'s\b/i", "$1"." ANAPOSTROPHES", $checktext); 
	//$checktext = preg_replace("/([a-z])'s\b/i", "$1"." ANAPOSTROPHES", $checktext); 

	//print $output;
    
	// Split check into words 
    $check_words = preg_split($split_chars, $checktext,-1,PREG_SPLIT_DELIM_CAPTURE); 
    //print_r($check_words);
	//print_r($check_words);
	
	$k=0;
	foreach($check_words as $this_word)
	{
		 $this_word=preg_replace("/'([^']*)'/i", "$1", $this_word);
		 $check_words[$k]=$this_word;
		 $k++;
	}
	
	//print_r($check_words);
	
	$unique_words = array_unique($check_words); 
	//print_r($unique_words);	
	//exit;
    
	$i = 1; 
    foreach ($unique_words as $this_word) 
    {    
		$i++; 
 
		if ((ord($this_word[0]) == 0) or ($this_word[0] == " ")) 
        {    array_splice($unique_words,$i,1); 
            continue; 
        } 
 
        if ($this_word == "") continue;  
         
        // if (substr($this_word,0,1) == "'") $this_word = substr($this_word,1); 
        if (substr($this_word,0,1) == "'") 
        {    $this_word = substr($this_word,1); 
            if ($this_word == '') continue; 
        } 
        if (substr($this_word,-1) == "'") $this_word = substr($this_word, 0,-1); 
 
        $this_word = trim($this_word, "'"); 
		if ((in_array($this_word,$quicklist)) or (in_array($this_word,$GLOBALS['ignoreWords'])) or (correct_spelling($this_word))) 
        {    continue; 
        } 
        else 
        {     
            // Put this only if in quote 
            if(strlen($this_word)>1) 
                $errors[] = $this_word; 
        } 
    } 
 
    return ($errors); 
} 
 
function removeNonSpellCheckFields($array_of_strings,$nonSpellCheckFields) 
{ 
    if(count($nonSpellCheckFields)>0) 
    { 
        foreach($nonSpellCheckFields as $key=>$val) 
        unset($array_of_strings[$val]); 
    } 
    return $array_of_strings; 
} 
 
function removeSpellCheckFormatting($array_of_strings,$applyInlineCss) 
{ 
   foreach($array_of_strings as $key=>$text) 
    {

if(get_magic_quotes_gpc() == 1)
    $text = stripslashes($text);
 
        if($applyInlineCss == 1) 
        { 
            $text = preg_replace('/(<span class="for_err" style="background-color:cyan;">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
            $text = preg_replace('/(<span class="sp_err" style="background-color:yellow;">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
             
            $text = preg_replace('/(<span class="for_err" style="background-color: cyan;">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
            $text = preg_replace('/(<span class="sp_err" style="background-color: yellow;">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
         
             
        } 
        else 
        { 
            $text = preg_replace('/(<span class="for_err">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
            $text = preg_replace('/(<span class="sp_err">)(\s{0,}|\S{0,})(<\/span>)/','$2',$text,-1,$count); 
        }


        $array_of_strings[$key] = $text; 
    }
	return $array_of_strings; 
     
} 
 
function correct_spelling($string) 
{ 
        // Changed words array to static 
        static $words = array(); 
    // Get all the about 57K words from the dictionary into array $words 
    if (!isset($words[0])) 
    { 
     
        $ci = & get_instance();
        $stmt=$ci->db->query("select word, type, subtype from dictionary"); 
        $reviewEssayIDsRes = $stmt->result_array();
		
		$i=0; 
        foreach($reviewEssayIDsRes as $line1) 
        { 
            $words[] = $line1['word']; 
            if ($line1['type'] != "t:dict_word") $type[$i] = $line1['type']; 
            if ($line1['subtype'] != "") $subtype[$i] = $line1['subtype']; 
            $i++; 
        } 
     
/*/////////////////////////////////////////////////////////////////////////////////////////////// 
        $result1 = mysqli_query($local, "select word, type, subtype from dictionary"); 
/////////////////////////////////////////////////////////////////////////////////////////////// 
        $i=0; 
        while ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) 
        {    $words[] = $line1['word']; 
            if ($line1['type'] != "t:dict_word") $type[$i] = $line1['type']; 
            if ($line1['subtype'] != "") $subtype[$i] = $line1['subtype']; 
            $i++; 
        }*/ 
    } 
 
    if (preg_match("/[0-9]/",$string)) 
    {    if (preg_match("/^[0-9]*(([0-9])|(11th)|(12th)|(13th)|(1st)|(2nd)|(3rd)|([4-90]th)|([0-9]s)|((kg)|(cm)|m|l|(ml)|g|%|(lb)))$/", $string)) 
        {    return (true); 
        } 
 
        if (preg_match("/^[\xc2]{0,1}[\xa3\x24\xa2\xa5\x80][0-9]+$/", $string))    // Check for various currencies prefixing an amount 
        {    return (true); 
        } 
    }  
 
    if (!(in_array(strtolower($string),$words) || in_array($string,$words) || ($string == "ANAPOSTROPHES") || ((strtoupper($string) == $string) && (in_array(ucfirst(strtolower($string)),$words))))) 
    {    // If a hyphenated word with both / all parts in the dictionary do not report 
        if (strpos($string,"-")) 
        {    $pieces = explode("-", $string); 
            foreach($pieces as $piece) 
            {    if (!correct_spelling($piece)) 
                {    return(false); 
                } 
            } 
            return(true); 
        } 
 
        // If a single character symbol, then do not report 
        if (!((strpbrk($string, "'()[]%$#/\";,=.+-?:&_!abcdABCD") && strlen($string)==1)))    //" 
        {    return(false); 
        } 
    } 
    return(true); 
 
 
} 
 
function rip_tags($string) { 
     // ----- remove HTML TAGs. Replace some like div, p, br and sup with spaces, but others with no space ----- 
    $string = preg_replace ('/<div[^>]*>/', ' ', $string); 
    $string = preg_replace ('/<p[^>]*>/', ' ', $string); 
    $string = preg_replace ('/<br[^>]*>/', ' ', $string); 
    $string = preg_replace ('/<sup[^>]*>/', ' ', $string); 
 
    $string = preg_replace ('/<[^>]*>/', '', $string); 
 
    // ----- remove contents of square and curly brackets [...] {...} ----- 
    $string = preg_replace ('/\[[^\]]*\]/', ' ', $string); 
    $string = preg_replace ('/{[^}]*}/', ' ', $string); 
 
    // ----- replace &nbsp; with space, &microtimes; with x and \x10 and \x13 ----- 
    $string = preg_replace ('/&nbsp;/', ' ', $string); 
    $string = preg_replace ('/&times;/', ' x ', $string); 
    $string = preg_replace ('/&#13;/', ' ', $string); 
    $string = preg_replace ('/&#10;/', ' ', $string); 
    $string = preg_replace ('/&#8203;/', '', $string);              // Added 31-12-2015
    $string = preg_replace ('/(&rdquo;)|(&ldquo;)/', '"', $string); 
    $string = preg_replace ('/(&rsquo;)|(&lsquo;)/', "'", $string); 
    $string = preg_replace ('/(&ndash;)|(&mdash;)/', ' - ', $string); 
 
    // ----- remove any other HTML entity (for spell check purposes - should not do for Named Entity Recognition or parsing) ----http://www.w3.org/wiki/Common_HTML_entities_used_for_typography 
    $string = preg_replace ('/&[a-zA-Z0-9]+;/', ' ', $string); 
 
    // ----- replace backtick and tilde with apostrophe and space respectively ----- 
    $string = preg_replace ('/`/', "'", $string); 
    $string = preg_replace ('/~/', " ", $string); 
 
    // ----- remove control characters ----- 
    $string = str_replace("\r", '', $string);    // --- replace with empty space 
    $string = str_replace("\n", ' ', $string);   // --- replace with space 
    $string = str_replace("\t", ' ', $string);   // --- replace with space 
 
    // ----- replace rich single and double, left and right quotes (8216-8222) with simple versions ----- 
    $string = preg_replace('/\xe2\x80\xa6/', "...", $string); 
    $string = preg_replace('/\xe2\x80\x9d/', '"', $string); 
    $string = preg_replace('/\xE2\x80\x9C/', '"', $string); 
    $string = preg_replace('/\xe2\x80\x8b/', '', $string);        // Added 31-12-2015
    $string = preg_replace('/\xE2\x80\x99/', "'", $string); 
    $string = preg_replace('/\xE2\x80\x98/', "'", $string); 
    $string = preg_replace('/\xe2\x80\x94/', ' - ', $string); 
    $string = preg_replace('/\xE2\x80\x93/', " - ", $string); 
    $string = preg_replace('/\xE2\x80\x92/', " - ", $string); 
    $string = preg_replace('/\xE2\x80\xA2/', "-", $string);    // Replace a bullet symbol with a - 
    $string = preg_replace('/\x97/', '"', $string); 
    $string = preg_replace('/\x96/', " - ", $string); 
    $string = preg_replace('/\x94/', '"', $string); 
    $string = preg_replace('/\x93/', '"', $string); 
    $string = preg_replace('/\x92/', "'", $string); 
    $string = preg_replace('/\x91/', "'", $string); 
    $string = preg_replace('/\x85/', "...", $string); 
 
    // ----- remove multiple spaces (keep at end) ----- 
    $string = trim(preg_replace('/ {2,}/', ' ', $string)); 

    // issue was happening with the words having quotes  eg'wasn't
    $string = stripslashes($string);
 
    return $string; 
} 
 
function put_highlights($string_array, $errors,$applyInlineCss) 
{     
    $new_array = array(); 
    foreach ($string_array as $key => $string) 
    {     
        if($string!==null && $string!=="") 
        { 
            foreach ($errors as $error) 
            {     
				
                if($applyInlineCss==1) 
                    //$string = preg_replace("/\b".$error."\b/","<span class='sp_err' style='background-color:yellow;'>".$error."</span>",$string); 
					$string = preg_replace("/(?<!\\w)".$error."(?!\\w)/","<span class='sp_err' style='background-color:yellow;'>".$error."</span>",$string); 
                else{ 
                    //$string = preg_replace("/\b".$error."\b/","<span class='sp_err'>".$error."</span>",$string);
					$string = preg_replace("/(?<!\\w)".$error."(?!\\w)/","<span class='sp_err'>".$error."</span>",$string); 
                } 
                     
            } 
             
             
             
            $string = preg_replace('/ (\.|,|!|\?|:|;|\))/', "&nbsp;$1", $string); 
            $string = preg_replace('/  /', "&nbsp;&nbsp;", $string); 
            $string = preg_replace('/ &nbsp;/', "&nbsp;&nbsp;", $string); 
             
             
             
            if($applyInlineCss==1) 
            { 
                $string = preg_replace('/&nbsp;(\.|,|!|\?|:|;|\))/', "<span class='for_err' style='background-color:cyan;'> $1</span>", $string); 
                $string = preg_replace('/((&nbsp;){2,})/', "<span class='for_err' style='background-color:cyan;'>$1</span>", $string); 
                 
            } 
            else 
            { 
                $string = preg_replace('/&nbsp;(\.|,|!|\?|:|;|\))/', "<span class='for_err'> $1</span>", $string); 
                $string = preg_replace('/((&nbsp;){2,})/', "<span class='for_err'>$1</span>", $string); 
            } 
             
        } 
         
        // print "<pre>";
		// print $string;
		// print "</pre>";
		$new_array[$key] = $string; 
    } 
    return($new_array); 
} 

function put_ignoreWordsHighlights($stringArray,$ignoreWords,$nonSpellCheckFields){
	
	//$stringArray is the string to find the word which will be replaced with span
	
	//$ignoreWords are the words that we need to replace it with span enclosure.
	
	//nonspellcheckfields are the array of fields that need not to consider for replacement
	
	$new_array = array(); 
	
	// $strings = removeNonSpellCheckFields($stringArray,$nonSpellCheckFields); 
    // $checktext = implode(" ",$strings); 
 
	// $checktext = rip_tags($checktext);  // It will remove tags from checktext string
	// $checktext = preg_replace("/([a-z])'s\b/i", "$1"." ANAPOSTROPHES", $checktext); 
 
    // static $split_chars = "/([_=?,\\\[\]\.\/()<>{}!*%&+;\"@#:^])|(?:[ \f\n\r\t])/";    //" The tilde character is not there currently 
	// // Split check into words 
    // $check_words = preg_split($split_chars, $checktext,-1,PREG_SPLIT_DELIM_CAPTURE); 
  
	// $unique_words = array_unique($check_words); 
	 $ignoreWords = array_unique($ignoreWords);
	
	foreach ($stringArray as $key => $string) 
    {   	
		if($string!==null && $string!=="") 
        { 
			foreach ($ignoreWords as $ignoreWord) 
            {  
				if(preg_match("/\b(" .$ignoreWord. ")(?=[^\w-]|$)/i",$string))
				{	
					$replacement = '<span class="clsIgnoreWord">'.$ignoreWord.'</span>';
					$pattern = "/\b(".$ignoreWord.")(?=[^\w-]|$)/i";
					$string = preg_replace($pattern, $replacement, $string);
				}
			}
			$stringArray[$key] = $string;
		}
	}
	return($stringArray);
	
}

?>