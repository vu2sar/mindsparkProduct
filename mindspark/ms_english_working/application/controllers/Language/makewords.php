<?php

Class MakeWords extends MY_Controller
{
	function __construct(){
        parent::__construct();
        $this->dbEnglish = $this->load->database('mindspark_english',TRUE);
	}
    
	function index()
	{
		$this->dbEnglish = $this->load->database('mindspark_english',TRUE);
		set_time_limit(0);

		//if (isset($_REQUEST['word'])) $word = $_REQUEST['word'];
		if (isset($_POST['wordToBeUsed'])) $word = $_POST['wordToBeUsed'];
		else $word="care";

		// Get all the about 57K words from the dictionary into array $words
		$i=0;
		$words = array();
		$hash = array();
		$final = array();

		$this->dbEnglish->Select('word, type, subtype');
		$this->dbEnglish->from('dictionary');
		$this->dbEnglish->where('type', 't:dict_word');
		$query = $this->dbEnglish->get();
		$line1 = $query->result_array();

		foreach ($line1 as $key => $value) 
		{
			$this_word = $value['word'];
			$this_len = strlen($this_word);
			$words[$i] = $this_word;
			$hash[$this_len][] = $i;
			for ($j=0; $j < $this_len; $j++)
			{	
				$hash[strtolower($this_word[$j])][] = $i;
			}
			$i++;
		}

		$start_time = microtime(true);
		$rev_freq = "etaoinsrhdlucmfywgpbvkxqjz";
		$l=strlen($word);

		//echo $word." has ".$l." letters.<br><br>";
		$main_repeats = $this->remove_one_arrange($word);

		$all_letters = array_keys(array_flip(str_split($rev_freq))); 	// ['e', 't', 'a', 'o'... etc] See Ome_Henk in http://php.net/manual/en/function.array-unique.php

		$letters = array_keys(array_flip(str_split($word)));
		$missing_letters = array_diff($all_letters, $letters); // missing letters from high to low frequency

		// Assume $word = "hello" - we will take all words and remove words that do not have 't', then 'a', etc.. starting from higher to lower freq letters

		$result = [];
		for ($k=1; $k<=$l; $k++)
		{	if (isset($hash[$k]))
			{	$result = array_merge($result, $hash[$k]);
			}
		}
		//echo "Number of words upto ".$l." letters long: ".(count($result))."<br>";

		foreach ($missing_letters as $missing)
		{	$result = array_diff($result, $hash[$missing]);
		//	echo "After removing words with '".$missing."': ".(count($result))."<br>";
		}

		foreach ($result as $no)
		{	$this_repeats = $this->remove_one_arrange($words[$no]);
			if (($this_repeats == "") || (stripos($main_repeats, $this_repeats) !== false)) $final[] = $words[$no];
		}

		//echo "There are ".(count($final))." valid words:<br>";
		$return_format['active']      = 'true';
		echo $this->return_response(json_encode($final));
		//echo json_encode($final);

		//foreach ($final as $final_word) echo $final_word."<br>";

		$elapsed_time = microtime(true)-$start_time;
		//echo "<br>Total elapsed time: ".$elapsed_time."`seconds<br>";
	}

	function remove_one_arrange($word)
	{	$drop = [];
		$keep = [];
		for ($i=0; $i<strlen($word); $i++)
		{	if (in_array($word[$i],$drop)) $keep[] = $word[$i];
			else $drop[] = $word[$i];
		}
		asort($keep);
		return(join($keep));
	}
}



?>