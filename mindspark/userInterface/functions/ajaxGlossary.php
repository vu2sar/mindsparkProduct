<?php
include("../check1.php");
include("../constants.php");

$term	=	$_GET['term'];
$qcode	=	$_GET['qcode'];

if($term!='')
{
	$sq	=	"SELECT glossaryTerm FROM adepts_glossaryTerms WHERE  glossaryTerm like '$term%'";

	$rs	=	mysql_query($sq);

	while($rw=mysql_fetch_array($rs))
	{
		$glossaryTerm[]=$rw['glossaryTerm'];
	}
	echo json_encode($glossaryTerm);
}
else if($fetchSingleTerm != "")
{
	$sq	=	"SELECT glossaryTerm,description,glossaryRelated FROM adepts_glossaryTerms WHERE  glossaryTerm like '$fetchSingleTerm%'";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$glossaryTerm[trim($rw[0])]['description']	=	trim(getImage($rw[1]));
		$glossaryTerm[trim($rw[0])]['mainTerm']	=	trim($rw[0]);
		$glossaryTerm[trim($rw[0])]['relatedTerms']	=	trim($rw[2]);
	}
	echo json_encode($glossaryTerm);
}
else
{
    $ttCode	=	$_SESSION['teacherTopicCode'];
	$glossaryArrayDB = array();

	/*$sq_ques	=	"SELECT question, display_answer FROM adepts_questions WHERE  qcode=$qcode";
	$rs_ques	=	mysql_query($sq_ques);
	$rw_ques	=	mysql_fetch_array($rs_ques);
	$str	=	$rw_ques[0]." ".$rw_ques[1];

	$$glossaryArray = array();

	$str	=	str_replace("&nbsp;"," ",$str);
	$str	=	str_replace("-"," ",$str);
	preg_match_all("/([ ^?.,]*)([a-z0-9-]*)([ ^?.,]*)/i", $str,$matches);
	$strArray	=	$matches[2];
	$modifiedSTR = implode(" ",$strArray);


	foreach($strArray as $word)
	{
		$word = strtolower($word);*/
		//$sq	=	"SELECT CONCAT_WS(',',glossaryTerm,glossaryVariants),description,glossaryTerm,glossaryRelated FROM adepts_glossaryTerms WHERE (glossaryTerm LIKE '$word' OR glossaryTerm LIKE '$word %') AND status=1";
        $sq	=	"SELECT CONCAT_WS(',',glossaryTerm,glossaryVariants),description,glossaryTerm,glossaryRelated FROM adepts_glossaryTerms a, adepts_questionGlossaryTermMapping b
                 WHERE a.glossaryID=b.glossaryID AND b.qcode=$qcode AND status=1 AND find_in_set('$ttCode',mappedTTs)>0";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$allVariants = explode(',',$rw[0]);
			$mainTerm = $rw[2];
			$relatedTerms = $rw[3];
			foreach($allVariants as $term)
			{
				if($term != "")
				{
					$glossaryArrayDB[trim($term)]['description']	=	trim(getImage($rw[1]));
					$glossaryArrayDB[trim($term)]['mainTerm']	=	trim($mainTerm);
					$glossaryArrayDB[trim($term)]['relatedTerms']	=	trim($relatedTerms);
					$glossaryArrayDB[trim($term)]['length']	=	strlen(trim($term));
				}
			}
		}
	/*}
	foreach($glossaryArrayDB as $term=>$array)
	{
		if(stripos($modifiedSTR,$term) !== false)
		{
			$glossaryArray[$term] = $array;
		}
	}*/
	if(!empty($glossaryArrayDB))
	{
		uksort($glossaryArrayDB,"onStrlen");
	}
	//	uksort($glossaryArray,"onStrlen");
	echo str_replace('\r\n','',json_encode($glossaryArrayDB));
}
function onStrlen($a, $b)
{
	return (strlen($a) >= strlen($b))?-1:1;
}

function getImage($data)
{
	$path	=	IMAGES_FOLDER."/Glossary/";
	$a	=	explode('[',$data);
	//preg_match_all('/\[gls_(.*)\]/i', $data, $matches, PREG_SET_ORDER);
	preg_match_all('/\[([a-z0-9_ -\.]*)\]/i', $data, $matches, PREG_SET_ORDER);

	foreach ($matches as $key)
	{
		//$key[1]	=	str_replace("gls_","",$key[1]);

		$imagePath	=	$path.$key[1];
		$image	=	"<img src='$imagePath'>";
		$data	=	str_replace($key[0],$image,$data);
	}
	return $data;
}
?>
