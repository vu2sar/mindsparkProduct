<?php
ini_set('memory_limit','-1');
set_time_limit(0);

if (isset($_REQUEST['word'])) $word = $_REQUEST['word'];
else $word="care";

//require_once '../../connect.php';
include($_SERVER['DOCUMENT_ROOT']."/connectPDO.php");
// Get all the about 57K words from the dictionary into array $words
$i=0;
$words = array();
$hash = array();
$final = array();
$db=getDBConnection();
//echo '<h2> New program</h2><br>';
$stmt=$db->prepare("select word, type, subtype from dictionary where type='t:dict_word'"); 
$stmt->execute(); 

while($line1=$stmt->fetch(PDO::FETCH_ASSOC)) 
{ 
//$result1 = mysqli_query($local, "select word, type, subtype from dictionary where type='t:dict_word'");
//while ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))

//{	
	//echo $line1['word'];;
	$this_word = $line1['word'];
	$this_len = strlen($this_word);
	$words[$i] = $this_word;
	$hash[$this_len][] = $i;
	for ($j=0; $j < $this_len; $j++)
	{	$hash[strtolower($this_word[$j])][] = $i;
	}
	$i++;
}
//

$start_time = microtime(true);
$rev_freq = "etaoinsrhdlucmfywgpbvkxqjz";
$l=strlen($word);

//echo $word." has ".$l." letters.<br><br>";

$main_repeats = remove_one_arrange($word);


$all_letters = array_keys(array_flip(str_split($rev_freq))); 	// ['e', 't', 'a', 'o'... etc] See Ome_Henk in http://php.net/manual/en/function.array-unique.php

$letters = array_keys(array_flip(str_split($word)));
$missing_letters = array_diff($all_letters, $letters); 		// missing letters from high to low frequency

// Assume $word = "hello" - we will take all words and remove words that do not have 't', then 'a', etc.. starting from higher to lower freq letters

$result = array();
for ($k=1; $k<=$l; $k++)
{	if (isset($hash[$k]))
	{	$result = array_merge($result, $hash[$k]);
	}
}
//echo "Number of words upto ".$l." letters long: ".(count($result))."<br>";
//echo "<pre>";print_r($result);echo "</pre>";
$result1 = array();
$result1 = array_flip($result);
foreach ($missing_letters as $missing)
{	
	
	//$result = array_diff($result, $hash[$missing]);
	$hash1[$missing] = array_flip($hash[$missing]);
	$result1 = array_diff_key($result1, $hash1[$missing]);

//	echo "After removing words with '".$missing."': ".(count($result))."<br>";
}

$result = array_flip($result1);
foreach ($result as $no)
{	$this_repeats = remove_one_arrange($words[$no]);
	if (($this_repeats == "") || (stripos($main_repeats, $this_repeats) !== false)) $final[] = $words[$no];
}

//echo "There are ".(count($final))." valid words:<br>";
//foreach ($final as $final_word) echo $final_word."<br>";

echo json_encode($final);

$elapsed_time = microtime(true)-$start_time;


//echo "<br>Total elapsed time: ".$elapsed_time."`seconds<br>";

function remove_one_arrange($word)
{	$drop = array();
	$keep = array();
	for ($i=0; $i<strlen($word); $i++)
	{	if (in_array($word[$i],$drop)) $keep[] = $word[$i];
		else $drop[] = $word[$i];
	}
	asort($keep);
	return(join($keep));
}

?>