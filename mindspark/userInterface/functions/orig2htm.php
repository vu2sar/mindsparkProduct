<?php
function pharseMatch($orig)
{
	preg_match_all("/([ ^?.,]*)([a-z0-9-]*)([ ^?.,]*)/i", $orig,$matches); //This will break into word boundaries in matches[2] with the bundaries themselves in matches[3]

	//for ($i=0; $i<str_word_count($orig); $i++)
	for ($i=0; $i<count($matches[2])-1; $i++)
	{
	    /*echo "<pre>";
	    print_r($matches);
	    echo "</pre>";*/
	    $this_word = $matches[2][$i];
	    //echo $i."-".$this_word."<br/>";

		$result = mysql_query("select original from englishWordDatabase where (US!='' or isnull(US)) AND original = '".$this_word."' OR original like '".$this_word." %' ORDER BY length(original) desc");

	    while ($line = mysql_fetch_array($result))
		{
		    $num_words = str_word_count($line['original']);
			$newword = "";
			for ($k=$i; $k<=$i+$num_words-1; $k++)
			    $newword .= $matches[2][$k]." ";
			$newword = substr($newword,0, -1);
			if ($newword == $line['original'])
			{	$i = $k-1;
				break;
			}
		}
		//echo $newword."<br>";

		if($newword!='')
			$orig = wordReplace($orig,"'".$newword."'");
	}

	return $orig;
}

function wordReplace($orig,$wordList)
{
	$pattern = array();
	$replace = array();

	$query = "select trim(original) original,trim(US) US, category from englishWordDatabase where (US!='' or isnull(US)) AND original in($wordList)";
	//echo $query."<br>";

	$wordArray = explode(",",str_replace("'","",$wordList));

	$foundWord = array();

	$result = mysql_query($query) or die($query.mysql_error());

	while ($line = mysql_fetch_array($result))
	{
		$word = strtolower($line['original']);

		$foundWord[$word][0] = $line['US'];
		$foundWord[$word][1] = $line['category'];
	}

	$i=0;
	for($j=0;$j<count($wordArray);$j++)
	{

		if(new_array_key_exists(strtolower($wordArray[$j]),$foundWord))
		{
			$pattern[$i] = "/\b(".$wordArray[$j].")\b/"; //i

			if (strcmp(strtoupper($wordArray[$j]),$wordArray[$j]) == 0)
				$replace[$i] = strtoupper($foundWord[strtolower($wordArray[$j])][0]);
			elseif(strcmp(strtolower($wordArray[$j]),$wordArray[$j]) == 0)
				$replace[$i] = strtolower($foundWord[strtolower($wordArray[$j])][0]);
			elseif(strcmp(ucfirst(strtolower($wordArray[$j])),$wordArray[$j]) == 0)
				$replace[$i] = ucfirst(strtolower($foundWord[strtolower($wordArray[$j])][0]));
			else
				$replace[$i] = $foundWord[strtolower($wordArray[$j])][0];

			$i++;
		}
	}

	$orig = preg_replace($pattern, $replace, $orig);

	return $orig;
}

function new_array_key_exists($needle,$haystack)
{
	return in_array(strtolower($needle), array_map('strtolower', array_keys($haystack)));
}

function orig_to_html($orig,$img_folder, $pos="", $context="", $quesNo="", $teacherQuestion="")
{
	$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');

	/* code for word replacement - Added By Rahul on 15 Feb 2011
	---------------------------------------------------------------*/
	if($context=="US")
	{
		$orig = pharseMatch($orig);

		$tokens =",;?:!_^#@%$&*()[]{}+=\"1234567890\t\r\n\<>'. ";//-/

		$str_token = strtok(strip_tags($orig),$tokens);

		$wordList = "";
		while ($str_token)
		{
			$wordList .= "'".$str_token."',";
			$str_token = strtok($tokens);
		}
		$wordList = substr($wordList,0,-1);
		if($wordList!='')
		{
			$orig = wordReplace($orig,$wordList);
		}
	}

	/*----------------------------------------------------------------*/
	$baseurl = IMAGES_FOLDER;
	if ($teacherQuestion==1){
		$orig = preg_replace('/(<img[^>]+src=")Image:/is', "$1".TEACHER_IMAGES_FOLDER.'/', $orig);
	}
		
	if($quesNo=="")
		$inputID	=	"id='b\$1'";
	else if($quesNo!="")
	{
		$inputID	=	"id='txtAns".$quesNo."_\$1' class='newFont'";
		$pos=$quesNo;
	}
	$pattern[0] = "/\[blank_([1-9])\]/i";
	$replacement[0] = "<input type='text' $inputID name='blank_\$1' size='5'>";
	
//---numeric blank test
	$pattern[8] = "/\[blank_([1-9]),\s*numeric\]/i";
	$replacement[8] = "<input type='text' $inputID name='blank_\$1' class='num_blank' size='5' onKeyPress='return CheckIfNumeric(event)' onChange='return CheckIfNumeric(event)'>";
	
	$pattern[52] = "/\[blank_([1-9]),\s*numeric\,(size=[0-9]*)\]/i";
	$replacement[52] = "<input type='text' $inputID name='blank_\$1' \$2 class='num_blank' onKeyPress='return CheckIfNumeric(event)' onChange='return CheckIfNumeric(event)'>";

	$pattern[53] = "/\[blank_([1-9])(.*?),\s*numeric\,(.*?)(style=['\"](.*?)['\"])\]/i";
	$replacement[53] = "<input type='text' $inputID name='blank_\$1' \$4 class='num_blank' onKeyPress='return CheckIfNumeric(event)' onChange='return CheckIfNumeric(event)'>";
	
//---	
	
//---------added by chirag for exponent textbox	
	$pattern[1] = "/\<sup>\[blank_([1-9])\]<\/sup>/i";
	$replacement[1] = "<input type='text' class='supscpt' $inputID name='blank_\$1' size=10>";
	
	$pattern[2] = "/\<sup>\[blank_([1-9]),(size=[0-9]*)\]<\/sup>/i";
	$replacement[2] = "<input type='text' class='supscpt' $inputID name='blank_\$1' \$2 >";
	
	$pattern[3] = "/\<sup>\[blank_([1-9])(.*?),(.*?)(style=['\"](.*?)['\"])\]<\/sup>/i";
	$replacement[3] = "<input type='text' class='supscpt' $inputID name='blank_\$1' \$4 >";
	
	$pattern[4] = "/\<sup>\[blank_([1-9]),(size=[0-9]*),(maxlength=[0-9]*)\]<\/sup>/i";
	$replacement[4] = "<input type='text' class='supscpt' $inputID name='blank_\$1' \$2 \$3 >";
/////////////-----ends here--------------//

	$pattern[5] = "/\[blank_([1-9]),(size=[0-9]*)\]/i";
	$replacement[5] = "<input type='text' $inputID name='blank_\$1' \$2 >";

	$pattern[6] = "/\[blank_([1-9])(.*?),(.*?)(style=['\"](.*?)['\"])\]/i";
	$replacement[6] = "<input type='text' $inputID name='blank_\$1' \$4 >";
//chirag	
	$pattern[7] = "/\[blank_([1-9]),(size=[0-9]*),(maxlength=[0-9]*)\]/i";
	$replacement[7] = "<input type='text' $inputID name='blank_\$1' \$2 \$3 >";
	

	$pattern[60] = "/(vec{)(\w+)(})/i";
	
	$replacement[60]="<div style='display:inline-block;'>
	 <div style='margin-bottom:-8px;margin-top:-12px;'>
	 <span>&rarr;</span>
	 </div> 
 
	 <div>
	 <span>\$2</span>
	 </div>
 
	 </div>
	 </span>";
	
	$pattern[61] = "/(vec{)((\w(?!~)|\W(?!~))*)(})/i";
	
	$replacement[61]="<div style='margin-top: -75px;display:inline-block'>
	<div style='margin-bottom:-8px;margin-top:-12px;margin-right:5px;'>
	<span style='font-size:57px;'>&rarr;</span>
	</div> 
	
	<div style='margin-top:-16px'>
	<span>\$2</span>
	</div>
	</div>
	
	</span>";
	

	
	$pattern[62] = "/(vec{)(\w+)(~)(\w+)(})/i";
	
	$replacement[62]="<div style=' margin-top:-25px;display:inline-block;'>
	<div style='display:inline-block;font-size:72px;text-align:center;'>
	<span>(</span>
	</div>
	
	<div style='display:inline-block;font-size:16px;text-align: center;'> 
	<div style='margin-top:24px;'>\${2} </div>
	<div style='margin-top:10px;'> \${4} </div>
	</div>
	
	<div style='display:inline-block;font-size:72px' >
	<span>)</span>
	</div>
	
	</div>
	</span>";

	$pattern[63] = "/(vec\{)([^}]*)(~)([^}]*)(\})/i";
	
	$replacement[63]="<div style=' margin-top:-25px;display:inline-block;'>
	<div style='display:inline-block;font-size:72px;text-align:center;'>
	<span>(</span>
	</div>
	
	<div style='display:inline-block;font-size:16px;text-align: center;'> 
	<div style='margin-top:24px;'>\${2} </div>
	<div style='margin-top:10px;'> \${4} </div>
	</div>
	
	<div style='display:inline-block;font-size:72px'>
	<span>)</span>
	</div>
	
	</div>
	</span>";
	
	$pattern[20] = "/{frac([^}]*)([^<])\//";
	$replacement[20] = "{frac\$1\$2|";

	/*$pattern[21] = "/{([a-z])}/";
	$replacement[21] = "<span class=var>\$1</span>";*/

	$pattern[22] = "/{exp\(([^{}]*)\)}/";
	$replacement[22] = "<span class=exp>\$1</span>";

	$pattern[23] = "/{frac\(([0-9]*)\+(.*)\)}/";
	$replacement[23] = "\$1{frac(\$2)}";

	/*$pattern[24] = "/{frac\(([0-9A-Za-z()+-?<>_'=\/ ]*)\|([0-9A-Za-z()+-?<>_'=\/ ]*)\)}/";
	$replacement[24] = "<span name='num' class=num style=''>\$1</span><span name='den' class=den style=''>\$2</span>";*/
	$pattern[24] = "/{frac\(([0-9A-Za-z()+-?<>_'=\/&;^# ]*)\|([0-9A-Za-z()+-?<>_'=\/&;^# ]*)\)}/e";
	//$replacement[24] = "'<span class=\"math\">'.str_replace(' ','&nbsp;','\\1').' \over '.str_replace(' ','&nbsp;','\\2').'</span>'";
	$replacement[24] = "'<span class=\"math\">'.custom_replace('\\1').' \over '.custom_replace('\\2').'</span> '";

	

//added for fracbox-------	
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
$isAndroid = 0;
$IE		 = stripos($_SERVER['HTTP_USER_AGENT'],"MSIE");

if(stripos($ua,'android') !== false)
{
	$isAndroid = 1;
	$propertyPattern = "Android ([\w._]+)";
	// Identify and extract the version.
	preg_match('/'.$propertyPattern.'/is', $ua, $match);
	if(!empty($match[1]))
	{
		$ver = $match[1];
		$ver = str_replace(array('_', ' ', '/'), array('.', '.', '.'), $ver);
		$arrVer = explode('.', $ver, 2);
		$arrVer[1] = @str_replace('.', '', $arrVer[1]); // @todo: treat strings versions.
		$androidver = (float)implode('.', $arrVer);
	}
}

	$pattern[25] = "/\[eqeditor\]/i";
	/*if($androidver<3 || preg_match('/(?i)msie [2-8]/',$_SERVER['HTTP_USER_AGENT']))// || $IE)
		$replacement[25] = '';
	else*/
		$replacement[25] = '<br>Use this tool to solve your questions:<br>- Click the white space and type.<br>- Use buttons available on the right side to insert symbols. <br><iframe src="/mindspark/userInterface/equationeditor2/eqeditor1.htm" width="714px" height="406px" id="eqeditor" style="margin:0px;border:0px" class="openEnded"></iframe>';
		
	$pattern[26] = "/\[eqeditor,1\]/i";
	/*if($androidver<3 || preg_match('/(?i)msie [2-8]/',$_SERVER['HTTP_USER_AGENT']))// || $IE)
		$replacement[26] = '';
	else*/
		$replacement[26] = '<br>Use this tool to solve your questions:<br>- Click the white space and type.<br>- Use buttons available on the right side to insert symbols. <br><iframe src="/mindspark/userInterface/equationeditor2/eqeditor1.htm?mode=write" width="714px" height="406px" id="eqeditor" style="margin:0px;border:0px" class="openEnded"></iframe>';
		
	$pattern[27] = "/\[eqeditor,2\]/i";
	/*if($androidver<3 || preg_match('/(?i)msie [2-8]/',$_SERVER['HTTP_USER_AGENT']))// || $IE)
		$replacement[27] = '';
	else*/
		$replacement[27] = '<br>Use this tool to solve your questions:<br>- Click the white space and type.<br>- Use buttons available on the right side to insert symbols. <br><iframe src="/mindspark/userInterface/equationeditor2/eqeditor1.htm?mode=draw" width="714px" height="406px" id="eqeditor" style="margin:0px;border:0px" class="openEnded"></iframe>';

//fracbox without parameter
	$pattern[50] = "/\[blank_([1-9]),fracbox\]/i";
	if($isAndroid && $androidver<3)
	{		
		//$replacement[50] = "<input type='text' $inputID name='blank_\$1' size='5' class='customfrac' >";
		$replacement[50] = "<input type='text' $inputID name='blank_\$1' size='5' >";
	}
	/*else if($isiPad)
	{
		$replacement[50] = "<input type='text' $inputID name='blank_\$1' size='5' class='customfrac' >";
	}*/
	else {
		$height = 60;
		$width = 120;
		$replacement[50] = "<div style='display:inline-block;vertical-align: middle;'><iframe src='/mindspark/userInterface/equationeditor2/fracbox.html?width=120&height=60' height='$height' width='$width' id='fracB_\$1' style='margin:0px;border:0px' class='fracBox'></iframe></div>";
		/*$replacement[50] = "<div id='fracB_\$1' CONTENTEDITABLE class='fracBox'></div>";
		$replacement[50] .= "<input type='hidden' name='fracV_\$1' id='fracV_\$1'/>";
		$replacement[50] .= "<input type='hidden' name='fracS_\$1' id='fracS_\$1'/>";*/
	}

//fracbox with parameter	
	$pattern[51] = "/\[blank_([1-9]),fracbox,1\]/i";
	if($isAndroid && $androidver<3)
	{			
		$replacement[51] = "<input type='text' $inputID name='blank_\$1' size='5'>";	
	}
	/*else if($isiPad)
	{
		$replacement[51] = "<input type='text' $inputID name='blank_\$1' size='5' class='customfrac' >";
	}*/
	else {
		$height = 60;
		$width = 120;
		$replacement[51] = "<div style='display:inline-block'><iframe src='/mindspark/userInterface/equationeditor2/fracbox.html?width=120&height=60' height='$height' width='$width' id='fracB_\$1' style='margin:0px;border:0px' class='fracBox fracboxvalue'></iframe></div>";
		/*$replacement[51] = "<div id='fracB_\$1' CONTENTEDITABLE class='fracBox'></div>";
		$replacement[51] .= "<input type='hidden' name='fracV_\$1' id='fracV_\$1' class='fracboxvalue'/>";
		$replacement[51] .= "<input type='hidden' name='fracS_\$1' id='fracS_\$1'/>";*/
	}
	
//added for fracbox ends here-------

	$html_ver = preg_replace($pattern, $replacement, $orig);

	$html_ver = $orig;
	do
	{	$orig = $html_ver;
		$html_ver = preg_replace($pattern, $replacement, $orig);
	} while ($orig!=$html_ver);

//replace fracbox
	/*$fracBoxNo = 1;
	$matches = array();
	$pattern = "/\[blank_([1-9]),fracbox\]/i";
	preg_match_all($pattern,$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$replaceStr = "<div id='fracB_$fracBoxNo' contenteditable='true' class='fracBox'></div>";
		$replaceStr .= "<input type='hidden' name='fracV_$fracBoxNo' id='fracV_$fracBoxNo'  class='fracboxvalue'/>";
		$replaceStr .= "<input type='hidden' name='fracS_$fracBoxNo' id='fracS_$fracBoxNo'/>";

		$html_ver = str_replace_count($matches[$i][0],$replaceStr,$html_ver,1);
		$fracBoxNo++;
	}*/
//------------//
	$matches = array();
	preg_match_all("/\[([a-z0-9_\/]*.html)\?*([^\],]*)(\s*,\s*([0-9]+)){0,1}(\s*,\s*([0-9]+)){0,1}\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	
	for($i=0 ; $i<$cnt_matches; $i++)
	{
	    $url = $baseurl;
		$filename = $matches[$i][1];
		if($pos=="CI")
		{
			$intro_folder = ENRICHMENT_MODULE_FOLDER."/html5/introduction/";
			$parameter = isset($matches[$i][2])?$matches[$i][2]:"";
			$height = isset($matches[$i][4])?$matches[$i][4]:"";
			$width = isset($matches[$i][6])?$matches[$i][6]:"";	
			if($parameter!="")
				$filename .= "?".$parameter;
			if($width == "")
				$width = 800;
			if($height == "")
				$height = 600;
			$parameter = isset($matches[$i][2])?$matches[$i][2]:"";
			if($parameter!="")
				$filename .= "?".$parameter;
			$rep = "<iframe id='iframe' src=".$intro_folder.$filename." height=".$height."px width=".$width."px frameborder='0'></iframe>";			
		}
		else
		{
			//added by ankur for HTML5 conversation
			$parameter = isset($matches[$i][2])?$matches[$i][2]:"";
			$height = isset($matches[$i][4])?$matches[$i][4]:"";
			$width = isset($matches[$i][6])?$matches[$i][6]:"";
			$folder = substr($filename,0,3);
			$filename = substr($filename,0,-5);
			if($width == "")
				$width = 800;
			if($height == "")
				$height = 600;
			if($isAndroid && $androidver<4)
				$isValidSwf	=	checkForValidSwf($filename);
				
			if($isValidSwf)
			{
				$url = $baseurl."/$folder";
				$imgName = $filename.".swf";
				if($context=="US")
				{
					$imgName = getUSVersionImage($imgName,$url);
				}
				$imagedetails = @getimagesize($url."/".$imgName);
				$width = $imagedetails[0];
				$height = $imagedetails[1];
				
				$rep = "<OBJECT id='simplemovie$pos' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
									HEIGHT='$height' WIDTH='$width'>
									<PARAM NAME=movie VALUE='".$url."/".$imgName."'>
									<PARAM NAME=quality VALUE=high>
									<PARAM name='wmode' VALUE='transparent'>
									<PARAM name='menu' VALUE='false'>
									<PARAM name='allowScriptAccess' VALUE='always'>
									<EMBED src='".$url."/".$imgName."'
									quality=high
									menu='false'
									TYPE='application/x-shockwave-flash'
									PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
									WMODE='Transparent'
									LOOP=true
									HEIGHT='$height' WIDTH='$width'  swliveconnect='true' allowScriptAccess='always' NAME='simplemovie$pos'>
									</EMBED>
									</OBJECT>";
			}
			else
			{
				if(strpos($filename,"GEO_constr")!==false)
				{
					// $intro_folder = "/mindspark/userInterface/constructionTool/".$filename."/src/index.html?".$parameter;
					$intro_folder = HTML_QUESTIONS_FOLDER."/".$folder."/".$filename."/src/index.html?".$parameter;
					$rep = "<iframe id='quesInteractive' class='constructionTool' src='".$intro_folder."' height='".$height."px' width='".$width."px' frameborder='0' scrolling='no'></iframe>";
				}
				else
				{
					$intro_folder = HTML_QUESTIONS_FOLDER."/".$folder."/".$filename."/src/index.html?".$parameter;
					$rep = "<iframe id='quesInteractive' src='".$intro_folder."' height='".$height."px' width='".$width."px' frameborder='0' scrolling='no'></iframe>";
				}
			}
		}		
		
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}
	
	/*$matches = array();
	preg_match_all("/\[([a-z0-9_\/]*.html\?*[^\]]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
	    $url = $baseurl;
		$filename = $matches[$i][1];
		if($pos=="CI")
		{
			$intro_folder = ENRICHMENT_MODULE_FOLDER."/html5/introduction/"; 			
			$width=830;
        	$height=620;
			$rep = "<iframe id='iframe' src=".$intro_folder.$filename." height=".$height."px width=".$width."px frameborder='0'></iframe>";
		}
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}*/
	
//added by chirag--for timed test html files
	if($quesNo!='')
	{
		$matches = array();
		preg_match_all("/\[([a-z0-9_\/]*.html),([0-9]*),([0-9]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
		$cnt_matches = count($matches);
		
		for($i=0 ; $i<$cnt_matches; $i++)
		{
			$timedTestHtmlPath	=	ENRICHMENT_MODULE_FOLDER."/html5/timedtest/";
			$width=200;
			$height=150;
			$rep = "<iframe id='iframe' src=".$timedTestHtmlPath.$matches[$i][1]." height=".$matches[$i][3]."px width=".$matches[$i][2]."px frameborder='0'></iframe>";
			$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
		}
	}

	$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');

	$matches = array();
	preg_match_all("/\[([a-z0-9_]*.php)\?([^]]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$filename = $matches[$i][1];
		$parameters = $matches[$i][2];
		$rep = "<img style='padding-left:30px; padding-right:-70px;' src='areaPerimeter/".$filename."?".$parameters."'>";
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}

	$matches = array();
	preg_match_all("/\[([a-z0-9_]*.swf)\?([^,]*),([0-9]*),([0-9]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
	    $url = $baseurl;
		$imgName = $matches[$i][1];
		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		if(!$isiPad)
		{
		$rep = "<OBJECT id='simplemovie$pos' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
							HEIGHT=".$matches[$i][3]." WIDTH=".$matches[$i][4].">
							<PARAM NAME=movie VALUE='".$url."/".$imgName."'>
							<PARAM NAME='FlashVars' VALUE='".$matches[$i][2]."'>
							<PARAM NAME=quality VALUE=high>
							<PARAM name='wmode' VALUE='transparent'>
							<PARAM name='menu' VALUE='false'>
							<PARAM name='allowScriptAccess' VALUE='always'>
							<EMBED src='".$url."/".$imgName."'
							FlashVars='".$matches[$i][2]."'
							quality=high
							menu='false'
							TYPE='application/x-shockwave-flash'
							PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
							WMODE='Transparent'
							LOOP=true
							HEIGHT=".$matches[$i][3]." WIDTH=".$matches[$i][4]."  allowScriptAccess='always' swliveconnect='true' NAME='simplemovie$pos'>
							</EMBED>
							</OBJECT>";
		}
		else
		{
			$imgName = $imgName.".html";
			$rep = "<iframe src='$url"."/"."$imgName' height='".$matches[$i][3]."px' width='".$matches[$i][4]."px' frameborder='0' scrolling='no'></iframe>";
		}
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}
	$matches = array();
	preg_match_all("/\[([a-z0-9_]*.swf)\?([^]]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
	    $url = $baseurl;
		$imgName = $matches[$i][1];

		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		$imagedetails = @getimagesize($url."/".$imgName);
		$width = $imagedetails[0];
        $height = $imagedetails[1];
		if($quesNo!='')
		{
			$url = $baseurl."/timedtest";
			$width = 200;
        	$height = 150;
		}
		if(!$isiPad)
		{					
		$rep = "<OBJECT id='simplemovie$pos' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
							height='$height' width='$width'>
							<PARAM NAME=movie VALUE='".$url."/".$imgName."'>
							<PARAM NAME='FlashVars' VALUE='".$matches[$i][2]."'>
							<PARAM NAME=quality VALUE=high>
							<PARAM name='wmode' VALUE='transparent'>
							<PARAM name='menu' VALUE='false'>
							<PARAM name='allowScriptAccess' VALUE='always'>
							<EMBED src='".$url."/".$imgName."'
							FlashVars='".$matches[$i][2]."'
							quality=high
							menu='false'
							TYPE='application/x-shockwave-flash'
							PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
							WMODE='Transparent'
							LOOP=true
							height='$height' width='$width'
							swliveconnect='true' allowScriptAccess='always' NAME='simplemovie$pos'>
							</EMBED>
							</OBJECT>";
		}
		else
		{
			$imgName = $imgName.".html";
			$rep = "<iframe src='$url"."/".$imgName."?".$matches[$i][2]."' height='".$height."px' width='".$width."px' frameborder='0' scrolling='no'></iframe>";
		}
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}

	//$pattern[25] = "/\[([a-z0-9_]*.swf)\]/i";
	$matches = array();
	preg_match_all("/\[([a-z0-9_]*.swf)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		
		$imgName = $matches[$i][1];
		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		$imagedetails = @getimagesize($url."/".$imgName);
		$width = $imagedetails[0];
        $height = $imagedetails[1];
		if(!$isiPad) {		
		$rep = "<OBJECT id='simplemovie$pos' classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
							HEIGHT='$height' WIDTH='$width'>
							<PARAM NAME=movie VALUE='".$url."/".$imgName."'>
							<PARAM NAME=quality VALUE=high>
							<PARAM name='wmode' VALUE='transparent'>
							<PARAM name='menu' VALUE='false'>
							<PARAM name='allowScriptAccess' VALUE='always'>
							<EMBED src='".$url."/".$imgName."'
							quality=high
							menu='false'
							TYPE='application/x-shockwave-flash'
							PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
							WMODE='Transparent'
							LOOP=true
							HEIGHT='$height' WIDTH='$width'  swliveconnect='true' allowScriptAccess='always' NAME='simplemovie$pos'>
							</EMBED>
							</OBJECT>";
		}
		else
		{
			$imgName = $imgName.".html";
			$rep = "<iframe src='$url"."/"."$imgName' height='".$height."px' width='".$width."px' frameborder='0' scrolling='no'></iframe>";
		}
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}

	//$pattern[26] = "/\[([a-z0-9_]*.swf),([0-9]*),([0-9]*)\]/i";
	$matches = array();
	preg_match_all("/\[([a-z0-9_]*.swf),([0-9]*),([0-9]*)\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		$imgName = $matches[$i][1];
		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		if(!$isiPad)
		{			
		$rep = "<OBJECT id='simplemovie$pos'  classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0'
							HEIGHT=".$matches[$i][2]." WIDTH=".$matches[$i][3].">
							<PARAM NAME=movie VALUE='".$url."/".$imgName."'>
							<PARAM NAME=quality VALUE=high>
							<PARAM name='wmode' VALUE='transparent'>
							<PARAM name='menu' VALUE='false'>
							<PARAM name='allowScriptAccess' VALUE='always'>
							<EMBED src='".$url."/".$imgName."'
							quality='high' menu='false' HEIGHT=".$matches[$i][2]." WIDTH=".$matches[$i][3]."
							TYPE='application/x-shockwave-flash'
							PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'
							WMODE='Transparent'
							LOOP=true swliveconnect='true' allowScriptAccess='always' NAME='simplemovie$pos'>
							</EMBED>
							</OBJECT>";
		}
		else
		{
			$imgName = $imgName.".html";
			$rep = "<iframe src='$url"."/"."$imgName' height='".$matches[$i][2]."px' width='".$matches[$i][3]."px' frameborder='0' scrolling='no'></iframe>";
		}
		$html_ver = str_replace($matches[$i][0],$rep,$html_ver);
	}


	//handle image separately since fraction reg ex collides with this. Regex for frac needs to be modified.
	//$image_pattern[0] = "/\[([a-z0-9_ -\.]*)\]/i";
	$image_pattern[0] = "/\[([a-z0-9_ -]+\.[a-z]{3,})\]/i";
	$matches = array();
	preg_match_all($image_pattern[0],$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		if($quesNo!='')
		{
			$url = $baseurl."/timedtest";
		}
		$imgName = $matches[$i][1];
		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		$imagedetails = @getimagesize($url."/".$imgName);
		$width = $imagedetails[0];
        $height = $imagedetails[1];
		if($width!="" && $height!="")
			$html_ver = str_replace($matches[$i][0],"<img align='absmiddle' src='".$url."/".$imgName."' id='img7_$i' onerror='noimage(this);' height='".$height."px' width='".$width."px'>",$html_ver);
		else		
			$html_ver = str_replace($matches[$i][0],"<img align='absmiddle' src='".$url."/".$imgName."' id='img7_$i' onerror='noimage(this);'>",$html_ver);
	}

	//$image_replacement[0] = "<img src='".$url.$img_folder."/\$1'>";

	$image_pattern[1] = "/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*)\]/i";
	$matches = array();
	preg_match_all($image_pattern[1],$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		if($quesNo!='')
		{
			$url = $baseurl."/timedtest";
		}
		$imgName = $matches[$i][1];
		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}

		$html_ver = str_replace($matches[$i][0],"<img  align='absmiddle' src='".$url."/".$imgName."' height='".$matches[$i][2]."' id='img4_$i' onerror='noimage(this);'>",$html_ver);
	}


	$matches = array();
	preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),N\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		if($quesNo!='')
		{
			$url = $baseurl."/timedtest";
		}
		$imgName = $matches[$i][1];

		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		$imagedetails = @getimagesize($url."/".$imgName);
		$width = $imagedetails[0];
        $height = $imagedetails[1];
		if($width!="" && $height!="")
			$html_ver = str_replace($matches[$i][0],"<img src='".$url."/".$imgName."' id='img5_$i' onerror='noimage(this);' height='".$height."px' width='".$width."px'>",$html_ver);
		else
			$html_ver = str_replace($matches[$i][0],"<img src='".$url."/".$imgName."' id='img5_$i' onerror='noimage(this);'>",$html_ver);
	}

	$matches = array();
	preg_match_all("/\[([a-z0-9_ -\s]*[\.][a-z]*),([0-9]*),N\]/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$url = $baseurl;
		if($quesNo!='')
		{
			$url = $baseurl."/timedtest";
		}
		$imgName = $matches[$i][1];

		if($imgName[3]=="_")
		{
			$folder = substr($imgName,0,3);
			//if(is_dir("images/".$folder))
				$url = $baseurl."/$folder";
		}
		if($context=="US")
		{
		    $imgName = getUSVersionImage($imgName,$url);
		}
		$html_ver = str_replace($matches[$i][0],"<img src='".$url."/".$imgName."' height='".$matches[$i][2]."' id='img6_$i' onerror='noimage(this);'>",$html_ver);
	}

	//$image_replacement[1] = "<img src='".$url.$img_folder."/\$1' height=\$2>";
	//$html_ver = preg_replace($image_pattern, $image_replacement, $html_ver);

	//for ie6 handling of symbols like angle, perpendicular, etc.
	if(ereg("msie", strtolower($_SERVER['HTTP_USER_AGENT'])))
		$ie= true;
	else
		$ie = false;
	if($ie)
	{
		/*ereg('MSIE ([0-9]\.[0-9])',$_SERVER['HTTP_USER_AGENT'],$reg);
		if(isset($reg[1]))
		{	*/
			//$html_ver = str_replace("&cong;","<FONT FACE=\"Symbol\">&#64;</FONT>",$html_ver); //congruent
			//$html_ver = str_replace("&#8773;","<FONT FACE=\"Symbol\">&#64;</FONT>",$html_ver); //congruent
			//$html_ver = str_replace("&#8736;","<FONT FACE=\"Symbol\">&#208;</FONT>",$html_ver);//angle sign
			$html_ver = str_replace("&#8869;","<FONT FACE=\"Symbol\">&#94;</FONT>",$html_ver);//perpendicular
			$html_ver = str_replace("<span style=\"font-size:18px;\">&ne;</span>","<FONT FACE=\"Symbol\">&#185;</FONT>",$html_ver);//not equal to
			$html_ver = str_replace("&#8712;","<FONT FACE=\"Symbol\">&#206;</FONT>",$html_ver);//belongs to
			$html_ver = str_replace("&#8713;","<FONT FACE=\"Symbol\">&#207;</FONT>",$html_ver);//does not belong to
			$html_ver = str_replace("&sub;","<FONT FACE=\"Symbol\">&#204;</FONT>",$html_ver);//subset
			$html_ver = str_replace("&#8836;","<FONT FACE=\"Symbol\">&#203;</FONT>",$html_ver);//not a subset
			$html_ver = str_replace("&#8746;","<FONT FACE=\"Symbol\">&#200;</FONT>",$html_ver);//union
			$html_ver = str_replace("&#8745;","<FONT FACE=\"Symbol\">&#199;</FONT>",$html_ver);//intersection
			$html_ver = str_replace("&#8764;","<FONT FACE=\"Symbol\">&#126;</FONT>",$html_ver);//similar to
			$html_ver = str_replace("&#8741;","||",$html_ver);//parallel
		//}

	}

	$i=1;
	do
	{
		$html_ver = preg_replace("/<span class=num/","<span id=num".$i." class=num",$html_ver,1);
		$html_ver = preg_replace("/<span class=den/","<span id=den".$i." class=den",$html_ver,1);
		$i++;
	} while (strpos($html_ver, "<span class=num"));

	//for converting drop downs in the reports page
	$html_ver = str_replace("&lt;dispans&gt;","",$html_ver);
	$html_ver = str_replace("&lt;/dispans&gt;","",$html_ver);
	$matches = array();
	preg_match_all("/{drop[\s]*:(\s)*([^}]*)}/i",$html_ver,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$options = explode(",",$matches[$i][2]);
		$optArray = array();
		$replaceStr  = "<select name='lstOpt$i' id='lstOpt$i'><option value=''></option>";
		for($j=0; $j<count($options); $j++)
		{
			$options[$j] = str_replace("&nbsp;"," ",$options[$j]);
			$optStr = trim(str_replace("'","",$options[$j]));
			$optArray[$j] = $optStr;
		}
        shuffle($optArray);
		for($j=0; $j<count($optArray); $j++)
			$replaceStr  .= "<option value='$optArray[$j]'>".$optArray[$j]."</option>";
		$replaceStr  .= "</select>";
		$html_ver = str_replace_count($matches[$i][0],$replaceStr,$html_ver,1);

	}

	/* custom delimiters - Parse <equ>..text..</equ> string into jsMath parser - 4 April 2010 */
	$html_ver = str_replace("&lt;equ&gt;","<span class='math'>",$html_ver);
    $html_ver =  str_replace("&lt;/equ&gt;","</span>",$html_ver);

	$html_ver = str_replace('&#8377;', '<span class="WebRupee">Rs.</span>', $html_ver);
	
	$html_ver = preg_replace('/^\s*(?:<br\s*\/*\s*>)*/i', '', $html_ver);
	$html_ver = preg_replace('/^\s*(?:<div\s*>)(?:<br\s*\/*\s*>)*/i', '<div>', $html_ver);

	return ($html_ver);
}
function getUSVersionImage($imgName,$url)
{
    $len = strrpos($imgName,".");
    $tempImgName = substr($imgName,0,$len);
    $len = (strlen($imgName) - strrpos($imgName,"."))*-1;

    $tempImgExt = substr($imgName,$len);
    $tempImgName = $tempImgName."_US".$tempImgExt;

    /*$header_response = get_headers($url."/".$tempImgName, 1);
    if ( strpos( $header_response[0], "404" ) !== false )*/
    if(!file_exists($url."/".$tempImgName))
    {
        // FILE DOES NOT EXIST
        //implies return the original image name
    }
    else
    {
        // FILE EXISTS!!
        $imgName = $tempImgName;
    }
    return $imgName;
}
function html_to_orig($html_ver)
{
	$pattern[0] = "/<img[ ]*src[ ]*=[ ]*'([a-z0-9_\.]*)'[ ]*[ ]*height[ ]*=[ ]*([0-9]*)>/i";
	$replacement[0] = "[$1,$2]";
	$pattern[1] = "/<img[ ]*src[ ]*=[ ]*'([a-z0-9_\.]*)'>/i";
	$replacement[1] = "[$1]";
	$pattern[2] = "/<br>\n/";
	$replacement[2] = "\r\n";
	return (preg_replace($pattern, $replacement, $html_ver));
}

function convertDropDowns($question,$quesNo="")
{
	$question = str_replace("&lt;dispans&gt;","",$question);
	$question = str_replace("&lt;/dispans&gt;","",$question);
	$matches = array();
	preg_match_all("/{drop[\s]*:(\s)*([^}]*)}/i",$question,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	$correct_answer = "";
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$options = explode(",",$matches[$i][2]);
		$optArray = array();
		if($quesNo!="")
			$replaceStr  = "<select name='lstOpt".$quesNo."_".$i."' id='lstOpt".$quesNo."_".$i."' class='dropDown'><option value=''></option>";
		else 
			$replaceStr  = "<select name='lstOpt$i' id='lstOpt$i'><option value=''></option>";
		for($j=0; $j<count($options); $j++)
		{
			$options[$j] = str_replace("&nbsp;"," ",$options[$j]);
			$options[$j] = str_replace("&amp;","&",$options[$j]);
			$optStr = trim(str_replace("'","",$options[$j]));
			$optArray[$j] = $optStr;
		}
		$correctAnsStr = $optArray[0];
		shuffle($optArray);
		$correct_answer .= array_search($correctAnsStr,$optArray) + 1 ."|";
		for($j=0; $j<count($optArray); $j++)
			$replaceStr  .= "<option value='$optArray[$j]'>".$optArray[$j]."</option>";
		$replaceStr  .= "</select>";

		//$question = preg_replace($matches[$i][0],$replaceStr,$question,1);
		$question = str_replace_count($matches[$i][0],$replaceStr,$question,1);

	}
	$correct_answer = substr($correct_answer,0,-1);
	$questionArray = array();
	$questionArray[0] = $question;
	$questionArray[1] = $correct_answer;
	return $questionArray;
}

function getDisplayAnswer($question, $correct_answer)
{
	$displayAns = strbet($question,"&lt;dispans&gt;","&lt;/dispans&gt;");
	if($displayAns=="")
		return "";
	$matches = array();
	preg_match_all("/{drop(\s)*:(\s)*([^}]*)}/i",$displayAns,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);

	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$options = explode(",",$matches[$i][3]);
		$replaceStr  = " <span id='spnOpt$i' style='border-bottom: 1px solid;'><strong>".str_replace("'","",$options[0])."</strong></span>";
		//$displayAns = preg_replace("/".$matches[$i][0]."/",$replaceStr,$displayAns,1);
		$displayAns = str_replace_count($matches[$i][0],$replaceStr,$displayAns,1);
	}

	$matches = array();

	preg_match_all("/\[blank_(\d)[^]]*\]/i",$displayAns,$matches, PREG_SET_ORDER);
	$cnt_matches = count($matches);
	$tempArray = explode("|",$correct_answer);
	for($iterator=0; $iterator<count($tempArray); $iterator++)	{
		$tempStr = explode("~",$tempArray[$iterator]);
		$tempArray[$iterator] = $tempStr[0];
	}
	for($i=0 ; $i<$cnt_matches; $i++)
	{
		$blankNo = $matches[$i][1];

		$replaceStr  = " <span id='spnBlank$i' style='border-bottom: 1px solid;'><strong>".$tempArray[$blankNo - 1]."</strong></span>";

		$displayAns = str_replace_count($matches[$i][0],$replaceStr,$displayAns,1);
	}
	return $displayAns;
}

function strbet($inputStr, $delimeterLeft, $delimeterRight) {
    $posLeft=strpos($inputStr, $delimeterLeft);
    if ( $posLeft===false ) {
        return "";
    }
    $posLeft+=strlen($delimeterLeft);
    $posRight=strpos($inputStr, $delimeterRight, $posLeft);
    if ( $posRight===false ) {
        return "";
    }
    return substr($inputStr, $posLeft, $posRight-$posLeft);
}

function str_replace_count($search,$replace,$subject,$times) {
	$subject_original=$subject;
	$len=strlen($search);
	$pos=0;
	for ($i=1;$i<=$times;$i++) {
		$pos=strpos($subject,$search,$pos);
		if($pos!==false) {
			$subject=substr($subject_original,0,$pos);
			$subject.=$replace;
			$subject.=substr($subject_original,$pos+$len);
			$subject_original=$subject;
		}
		else {
			break;
		}
	}
	return($subject);
}

function custom_replace($str)
{
	$str = str_replace(' ','&nbsp;',$str);
	$pattern[0] = "/#([^#]*)\#/i";
	$replacement[0] = "{\$1}";

	$str = preg_replace($pattern,$replacement,$str);

	return $str;
}

function checkForValidSwf($filename)
{
	$sq	=	"SELECT htmlFileName, swfFileName FROM adepts_swfHtmlParam WHERE htmlFileName='$filename' AND param=''";
	$rs	=	mysql_query($sq);
	if(mysql_num_rows($rs)==1)
	{
		$rw	=	mysql_fetch_array($rs);
		if($rw[0]==$rw[1])
			return 1;
		else
			return 0;
	}
	else
		return 0;
}

?>
