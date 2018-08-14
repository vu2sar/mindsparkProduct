<?php
//$Sing2Plural = array('car'=>'cars', 'ball'=>'balls', 'apple'=>'apples', 'banana'=>'bananas', 'mango'=>'mangoes', 'grape'=>'grapes', 'tomato'=>'tomatoes', 'potato'=>'potatoes', 'boat'=>'boats', 'kite'=>'kites', 'cap'=>'caps', 'butterfly'=>'butterflies', 'flower'=>'flowers', 'leaf'=>'leaves', 'lollipop'=>'lollipops', 'toffee'=>'toffees', 'candy'=>'candies', 'tree'=>'trees', 'frog'=>'frogs', 'dog'=>'dogs', 'cat'=>'cats', 'elephant'=>'elephants', 'fish'=>'fish', 'umbrella'=>'umbrellas', 'cone'=>'cones', 'dot'=>'dots', 'step'=>'steps', 'jump'=>'jumps', 'number'=>'numbers', 'doll'=>'dolls', 'pencil'=>'pencils', 'pen'=>'pens', 'matchstick'=>'matchsticks', 'spoon'=>'spoons', 'table'=>'tables', 'chair'=>'chairs', 'kangaroo'=>'kangaroos', 'monkey'=>'monkeys', 'arrow'=>'arrows', 'house'=>'houses', 'bird'=>'birds', 'aeroplane'=>'aeroplanes', 'girl'=>'girls', 'boy'=>'boys', 'child'=>'children', 'kid'=>'kids', 'goat'=>'goats', 'hen'=>'hens', 'egg'=>'eggs', 'class'=>'classes', 'classroom'=>'classrooms', 'fruit'=>'fruits', 'toy'=>'toys', 'vehicle'=>'vehicles', 'cow'=>'cows', 'school'=>'schools', 'teacher'=>'teachers', 'student'=>'students', 'book'=>'books', 'necklace'=>'necklaces', 'bundle'=>'bundles', 'bead'=>'beads', 'sweet'=>'sweets','sketch-pen'=>'sketch-pens');

$Singular = array('car', 'ball', 'apple', 'banana', 'mango', 'grape', 'tomato', 'potato', 'boat', 'kite', 'cap', 'butterfly', 'flower', 'leaf', 'lollipop', 'toffee', 'candy', 'tree', 'frog', 'dog', 'cat', 'elephant', 'fish', 'umbrella', 'cone', 'dot', 'step', 'jump', 'number', 'doll', 'pencil', 'pen', 'matchstick', 'spoon', 'table', 'chair', 'kangaroo', 'monkey', 'arrow', 'house', 'bird', 'aeroplane', 'girl', 'boy', 'child', 'kid', 'goat', 'hen', 'egg', 'class', 'classroom', 'fruit', 'toy', 'vehicle', 'cow', 'school', 'teacher', 'student', 'book','necklace','bundle','bead','sweet','sketch-pen');
$Plural = array('cars', 'balls', 'apples', 'bananas', 'mangoes', 'grapes', 'tomatoes', 'potatoes', 'boats', 'kites', 'caps', 'butterflies', 'flowers', 'leaves', 'lollipops', 'toffees', 'candies', 'trees', 'frogs', 'dogs', 'cats', 'elephants', 'fish', 'umbrellas', 'cones', 'dots', 'steps', 'jumps', 'numbers', 'dolls', 'pencils', 'pens', 'matchsticks', 'spoons', 'tables', 'chairs', 'kangaroos', 'monkeys', 'arrows', 'houses', 'birds', 'aeroplanes', 'girls', 'boys', 'children', 'kids', 'goats', 'hens', 'eggs', 'classes', 'classrooms', 'fruits', 'toys', 'vehicles', 'cows', 'schools', 'teachers', 'students', 'books','necklaces','bundles','beads','sweets','sketch-pens');

function generateDynamicQues($question, $optiona, $optionb, $optionc, $optiond, $correct_answer, $display_answer, $question_type, $generatedParams="", $mode="",$display_answerA="",$display_answerB="",$display_answerC="",$display_answerD="")
{
	global $Singular,$Plural;
	$question = str_replace("&nbsp;", " ", $question); //Replace nbsp with space since html_entity_decode() replaces nbsp with a code other than %20(ASCII code for Space)
	$optiona = str_replace("&nbsp;", " ", $optiona);
	$optionb = str_replace("&nbsp;", " ", $optionb);
	$optionc = str_replace("&nbsp;", " ", $optionc);
	$optiond = str_replace("&nbsp;", " ", $optiond);
	$correct_answer = str_replace("&nbsp;", " ", $correct_answer);
	$display_answerA = str_replace("&nbsp;", " ", $display_answerA);
	$display_answerB = str_replace("&nbsp;", " ", $display_answerB);
	$display_answerC = str_replace("&nbsp;", " ", $display_answerC);
	$display_answerD = str_replace("&nbsp;", " ", $display_answerD);
	$display_answer = str_replace("&nbsp;", " ", $display_answer);

	$question = str_replace("&times;", "``", $question); //Replace nbsp with space since html_entity_decode() replaces nbsp with a code other than %20(ASCII code for Space)
	$optiona = str_replace("&times;", "``", $optiona);
	$optionb = str_replace("&times;", "``", $optionb);
	$optionc = str_replace("&times;", "``", $optionc);
	$optiond = str_replace("&times;", "``", $optiond);
	$correct_answer = str_replace("&times;", "``", $correct_answer);
	$display_answerA = str_replace("&times;", "``", $display_answerA);
	$display_answerB = str_replace("&times;", "``", $display_answerB);
	$display_answerC = str_replace("&times;", "``", $display_answerC);
	$display_answerD = str_replace("&times;", "``", $display_answerD);
	$display_answer = str_replace("&times;", "``", $display_answer);
	
	$question = str_replace("&divide;", "~~", $question); 
	$optiona = str_replace("&divide;", "~~", $optiona);
	$optionb = str_replace("&divide;", "~~", $optionb);
	$optionc = str_replace("&divide;", "~~", $optionc);
	$optiond = str_replace("&divide;", "~~", $optiond);	

	
	$question1 =  html_entity_decode($question)."@".html_entity_decode($optiona)."@".html_entity_decode($optionb)."@".html_entity_decode($optionc)."@".html_entity_decode($optiond)."@".html_entity_decode($correct_answer)."@".html_entity_decode($display_answer)."@"." "."@".html_entity_decode($display_answerA)."@".html_entity_decode($display_answerB)."@".html_entity_decode($display_answerC)."@".html_entity_decode($display_answerD);
	
	/*$question1 =  htmlspecialchars_decode($question)."@".htmlspecialchars_decode($optiona)."@".htmlspecialchars_decode($optionb)."@".htmlspecialchars_decode($optionc)."@".htmlspecialchars_decode($optiond)."@".htmlspecialchars_decode($correct_answer)."@".htmlspecialchars_decode($display_answer)."@"." "."@".htmlspecialchars_decode($display_answerA)."@".htmlspecialchars_decode($display_answerB)."@".htmlspecialchars_decode($display_answerC)."@".htmlspecialchars_decode($display_answerD);*/

	if($mode=='answer')
	{
		$str1 = explode(":",$generatedParams);	//The dynamic variables generated and the position of the options are separated by ":"
		$parameters = explode(",",$str1[0]);
	}
	//echo $question1;

	// Pre-defined variables
	$boyname = array("Ravi", "Sandeep", "Gopal", "Shastri", "Salim", "Nadim");
	$girlname = array("Sushma", "Radhika", "Salma", "Mary", "Usha", "Geeta");
	$object = array("apple", "pencil", "leaf", "bat", "ball");
	$g_objgrp = array("apple~orange", "bat~ball", "dog~cat", "banana~apple");
	$animals = array("dog", "cat");

	$try = 0; //Try only ten times
	$rep = array();
	do
	{
		$question = $question1;

		preg_match_all("/\{([^}]*)\}/", $question, $matches);

		$duplicate=0;
		for ($i=0; $i<count($matches[0]); $i++)
		{
			if($mode=='answer')
			{
				//echo $parameters[$i]."<br>";
				$rep[$i] = $parameters[$i];
			}
			else
			{
				$match[$i] = $matches[1][$i];

				if (!(strpos($match[$i], '#') === false))			// Meaning it is a #variable (could be #boyname, #2, #num())
				{	if (preg_match("/#(g_[a-z0-9_]*) *, *([0-9]+) *, *([0-9]+)/",$match[$i],$vno) == 1)	// #g_objgrp type
					{	$gvar = ${$vno[1]};		// will set $varno to the corresponding array (pre-defined variable)
						$gset = $vno[2];			// will set the number; the 1 in (#g_objgrp,1,2)
						$ginstance = $vno[3];	// will set the number; the 2 in (#g_objgrp,1,2)
						if (!isset($randomval[$gset]))
						{
							$randomval[$gset] = mt_rand(0,count($gvar)-1);
						}
						$temp = explode("~",$gvar[$randomval[$gset]]);
						$rep[$i] = $temp[$ginstance - 1];
					}
					else
					{	if (preg_match("/#[0-9]+[-+*^\/ ]*/",$match[$i]) == 1) // #2 type
						{	eval("\$rep[\$i] = ".preg_replace("/#([0-9]+)([-+*^\/ ]*)/","\$rep[$1-1]$2",$match[$i]).";");
						}
						else
						{	if (preg_match("/#([a-z][a-z0-9_]*)[<>= ]*/",$match[$i],$vno) == 1)	// #boyname type
							{
								$prevar = ${$vno[1]};		// will set $varno to the corresponding array (pre-defined variable)
								$rep[$i] = $prevar[mt_rand(0,count($prevar)-1)];
							}
						}
					}
				}
				else
				{
					if (!(strpos($match[$i], 'frac(') === false))			// Meaning if it is a frac
					{						
						$rep[$i] = "{".$match[$i]."}";						
					}
					else if (!(strpos($match[$i], ',') === false))			// Meaning if it does have a comma
					{
						$options = explode(",", $match[$i]);
						$val = $options[mt_rand(0,count($options)-1)];
						//$val = random_string($val);
						//$rep[$i] = $val;
						/*if(array_key_exists($val,$Sing2Plural))
						{
				            $val = str_replace($val,$Sing2Plural[$val],$val);
						}*/
						if(in_array($val,$Singular))
						{
							$val = str_replace($val,$Plural[array_search($val,$Singular)],$val);
						}
						/*for($j=0; $j<count($Plural); $j++)
						{
							$val = str_replace($Singular[$j], $Plural[$j], $val);
						}*/

						$rep[$i] = $val;
					}
				}
			}
		}
		$question = str_replace($matches[0], $rep, $question);

		$dynamicParams = implode(",", $rep);

		//Predefined
		$boyArray = array("Ram","Rahul","Arpit","Rohit","Ravi");
		$fruitsArray = array("apple","mango","banana");

		$boy = $boyArray[rand(0,count($boyArray)-1)];
		$fruits = $fruitsArray[rand(0,count($fruitsArray)-1)];

		preg_match_all("/<\?([^?>]*)\?\>/", $question, $matches);

		$j=$i;

		for ($i=0; $i<count($matches[0]); $i++,$j++)
		{
			if($mode=='answer')
			{
				//echo $parameters[$j]."<br>";
				$rep[$i] = $parameters[$j];
			}
			else
			{
				$match[$i] = $matches[1][$i];
				if (!(strpos($match[$i], '=') === false))
				{
					$rep[$i] = '';
					eval($match[$i]);
				}
				else
				{
					eval("\$rep[$i] = ".$match[$i].";");
				}
			}
		}

		//echo $question;

		//echo "<br><br>";

		$question = str_replace($matches[0],$rep,$question);

		//print_r($matches[0]);

		//echo "<br><br>";

		//print_r($rep);

		$dynamicParams1 = implode(",", $rep);

		if($dynamicParams!='')
		{
			if($dynamicParams1!='')
				$dynamicParams .= ",".$dynamicParams1;
		}
		else
		{
			if($dynamicParams1!='')
				$dynamicParams = $dynamicParams1;
		}

		$questionArray = explode("@",$question);
		$questionArray[7]	=	$dynamicParams;
		//array_push($questionArray,$dynamicParams);

		switch($question_type)
		{
			case "MCQ-2":
							if($questionArray[1]==$questionArray[2])
								$duplicate=1;
							break;
			case "MCQ-3":
							if($questionArray[1]==$questionArray[2] || $questionArray[2]==$questionArray[3] || $questionArray[1]==$questionArray[3])
								$duplicate=1;
							break;
			case "MCQ-4":
							if($questionArray[1]==$questionArray[2] || $questionArray[1]==$questionArray[3] || $questionArray[1]==$questionArray[4] || $questionArray[2]==$questionArray[3] || $questionArray[2]==$questionArray[4] || $questionArray[3]==$questionArray[4])
								$duplicate=1;
							break;
		}
		$try++;
		//echo "Try:".$try." Duplicate:".$duplicate;
	}while($duplicate==1 && $try<=10);

	/*if($try>=10)
		echo "<font color=red>Please change the options</font>";*/


	if($question_type=='MCQ-2' || $question_type=='MCQ-3' || $question_type=='MCQ-4')
	{
		if($mode=='answer')//Code for recreating the question from the parameters given.
		{
			$optionPos = explode(",",$str1[1]);
			if($correct_answer=='A')
				$currectPos = 1;
			elseif($correct_answer=='B')
				$currectPos = 2;
			elseif($correct_answer=='C')
				$currectPos = 3;
			elseif($correct_answer=='D')
				$currectPos = 4;

			switch ($currectPos)
			{
				case $optionPos[0]: $correct_answer='A';
					 				break;
				case $optionPos[1]: $correct_answer='B';
									break;
				case $optionPos[2]: $correct_answer='C';
									break;
				case $optionPos[3]: $correct_answer='D';
									break;
			}

			$optiona = stripslashes($questionArray[$optionPos[0]]);
			$optionb = stripslashes($questionArray[$optionPos[1]]);
			$optionc = stripslashes($questionArray[$optionPos[2]]);
			$optiond = stripslashes($questionArray[$optionPos[3]]);

			$questionArray[1] = $optiona;
			$questionArray[2] = $optionb;
			$questionArray[3] = $optionc;
			$questionArray[4] = $optiond;
			$questionArray[5] = $correct_answer;
		}
		else	//Shuffle the options in case of MCQ
		{			
			if($question_type=="MCQ-2")
				$optionPos = array(1,2);
			if($question_type=="MCQ-3")
				$optionPos = array(1,2,3);
			if($question_type=="MCQ-4")
				$optionPos = array(1,2,3,4);

			//shuffle($optionPos); //Remove shuffling of options based on Ankit's and SRR's mail

			if($correct_answer=='A')
				$currectPos = 1;
			elseif($correct_answer=='B')
				$currectPos = 2;
			elseif($correct_answer=='C')
				$currectPos = 3;
			elseif($correct_answer=='D')
				$currectPos = 4;

			switch ($currectPos)
			{
				case $optionPos[0]: $correct_answer='A';
						break;
				case $optionPos[1]: $correct_answer='B';
						break;
				case $optionPos[2]: $correct_answer='C';
						break;
				case $optionPos[3]: $correct_answer='D';
						break;
			}

			$optiona = stripslashes($questionArray[$optionPos[0]]);
			$optionb = stripslashes($questionArray[$optionPos[1]]);
			$optionc = stripslashes($questionArray[$optionPos[2]]);
			$optiond = stripslashes($questionArray[$optionPos[3]]);

			$questionArray[1] = $optiona;
			$questionArray[2] = $optionb;
			$questionArray[3] = $optionc;
			$questionArray[4] = $optiond;

			$questionArray[5] = $correct_answer;

			//Include the option position in the parameters for recreating at a later stage
			$questionArray[7] .= ":";
			$questionArray[7] .= implode(",",$optionPos);
		}
	}

	//Temp work around
	$questionArray[0] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[0]));	
	$questionArray[1] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[1]));
	$questionArray[2] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[2]));
	$questionArray[3] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[3]));
	$questionArray[4] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[4]));
	$questionArray[5] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[5]));
	$questionArray[6] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[6]));
	$questionArray[7] = str_replace("``","&times;",str_replace("~~","&divide;",$questionArray[7]));
	
	//End
	return $questionArray;
}

//Function converts words following 1 from Plural to Singular
function Plural_to_Singular($str)
{
	$str = str_replace("&nbsp;", " ", $str);
	$word_arr = explode(" ",$str);
	global $Singular,$Plural;

	for($i=0;$i<count($word_arr)-1;$i++)
	{
		if($word_arr[$i]==1)
		{
			$k=1;
			$temp = $word_arr[$i+$k];
			while($temp == "")
			{
				$k++;
				$temp = $word_arr[$i+$k];
			}
			for($j=0; $j<count($Plural); $j++)
			{
				$temp = str_replace($Plural[$j], $Singular[$j], $temp);
			}
			$word_arr[$i+$k] = $temp;
			$k=1;
		}
	}
	$str = implode(" ",$word_arr);
	return $str;
}

function sub_wo_borrow($firstFrom, $firstTo, $secondFrom, $secondTo)
{
	if($secondFrom > $firstFrom)
	{
		$tempFrom  = $secondFrom;
		$secondFrom = $firstFrom;
		$firstFrom = $tempFrom;
	}
	if($secondTo > $firstTo)
	{
		 $tempFrom = $secondTo;
		 $secondFrom = $firstTo;
		 $firstFrom = $tempFrom;
	}
	$flag=1;
	do
	{
		$n1 = rand($firstFrom,$firstTo);
		$n2 = rand($secondFrom,$secondTo);

		$n = array();
		array_push($n,$n1);
		array_push($n,$n2);

		if($n1 > $n2)
		{
			$flag=0;
			$digit = strlen($n1);
			for($i=0;$i<$digit;$i++)
			{
				if(($n1%pow(10,$i)) < ($n2%pow(10,$i)))
				{
					$flag=1;
					break;
				}
			}
		}
	}while ($flag==1);

	return $n;
}

function random_n1_n2_positive($n1_from,$n1_to,$n2_from,$n2_to)
{

	$n = array();
	do
	{
		$n1 = rand($n1_from,$n1_to);
		$n2 = rand($n2_from,$n2_to);
	}while($n1 <= $n2);

	array_push($n,$n1);
	array_push($n,$n2);

	return $n;

}

function random_n1_n2_not_equal($n1_from,$n1_to,$n2_from,$n2_to)
{

	$n = array();
	do
	{
		$n1 = rand($n1_from,$n1_to);
		$n2 = rand($n2_from,$n2_to);
	}while($n1 == $n2);

	array_push($n,$n1);
	array_push($n,$n2);

	return $n;

}

function random_n_except($n_from,$n_to,$except)
{
	do
	{
		$n = rand($n_from,$n_to);
	}while($n==$except);

	return $n;
}

function random_string($s1)
{
	$s = explode(",",$s1);
	return $s[rand(0,count($s)-1)];

	/*global $Singular,$Plural;
	$s = explode(",",$s1);
	$s1 = $s[rand(0,count($s)-1)];
	if(array_key_exists($s1,$Sing2Plural))
	{
		$s1 = str_replace($s1,$Sing2Plural[$s1],$s1);
	}

	for($j=0; $j<count($Plural); $j++)
	{
		$val = str_replace($Singular[$j], $Plural[$j], $val);
	}
	return s1;*/

}

function sub_with_borrow($firstFrom, $firstTo, $secondFrom, $secondTo)
{
	if($secondFrom > $firstFrom)
	{
		$tempFrom  = $secondFrom;
		$secondFrom = $firstFrom;
		$firstFrom = $tempFrom;
	}
	if($secondTo > $firstTo)
	{
		 $tempFrom = $secondTo;
		 $secondFrom = $firstTo;
		 $firstFrom = $tempFrom;
	}
	$flag=0;
	do
	{
		$n1 = rand($firstFrom,$firstTo);
		$n2 = rand($secondFrom,$secondTo);

		if($n1 > $n2)
		{
			$digit = strlen($n1);
			for($i=0;$i<$digit;$i++)
			{
				if(($n1%pow(10,$i)) < ($n2%pow(10,$i)))
				{
					$flag=1;
					break;
				}
			}
		}
	}while ($flag==0);

	$n = array();
	array_push($n,$n1);
	array_push($n,$n2);

	return $n;
}

function mult($multiple,$n_from,$n_to,$except="")
{
	do
	{
		$n = rand($n_from,$n_to);
	}while($n==$except || $n%$multiple!=0);

	return $n;
}

?>