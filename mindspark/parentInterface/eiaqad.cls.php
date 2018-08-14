<?php
function generateAQADtemplate($day, $cls, $userAnswer,$childname) {
    $message = "";
    $qno = 0;
    $subject_arr = array("", "English", "Maths", "Science", "Social Studies");
    //$today = date("2010-06-25");
    $today = date("Y-m-d");

    $current_time = mktime(0, 0, 0, substr($day, 5, 2), substr($day, 8, 2), substr($day, 0, 4));

    # figure out what 1 days is in seconds
    $one_day = 1 * 24 * 60 * 60;
    $two_day = 2 * 24 * 60 * 60;
    $three_day = 3 * 24 * 60 * 60;
    $title = "Question for date: ".$day;
    # make last day date based on a past timestamp
    if (date("D", $current_time) == "Sun") {
        echo "<div style='font-size: medium; text-align: center; padding-top: 20px;'>Selected date is a sunday and AQAD is not sent on sundays</div>";
        return;
        //continue;
    }
    if ($cls == "3" || $cls == "4" || $day <= '2010-06-28') {
        if (date("D", $current_time) == "Sat") {
            echo "<div style='font-size: medium; text-align: center; padding-top: 20px;'>Selected date is a saturday and AQAD is not sent on saturdays for class 3 and 4</div>";
            return;
            //continue;
        }
    }
	
	$yesterday = $day;


//    if ($this->action != 'PrintData')
          //$message.="<div class='theading' align='right'>Please <a href=\"javascript:openNewWindow('" . formatDate($day) . "','" . $cls . "')\">Click Here</a> to save as PDF and to take the print<br><input type=\"hidden\" name=\"clsaqad_hdnsrno\" value='" . $srno . "'></div>";
    $message.="<br>";
    $message.="<table width=\"60%\"  style=\"border: 1px solid rgb(0, 0, 0); font-family: Arial; font-size: 12pt;\" align=\"center\" bgcolor=\"#ffffff\" border=\"0\" cellspacing=\"0\">";
    $message.="<tr><td colspan=\"3\"><BLOCKQUOTE> <P><font face=\"Arial\">";


    $header = "<div align=center><img src='../userInterface/assets/aqad.jpg' BORDER=1 width='70%' align='absmiddle'></div>";

    $message.=$header;

    $message.="</FONT></P></BLOCKQUOTE>";
    $message.="</td>";
    $message.="</tr>";



    $yesterday_question_query = "SELECT papercode,qno FROM aqad_master where date='" . $yesterday . "' and class='" . $cls . "'";
    $yesterday_question_dbquery = mysql_query($yesterday_question_query);
    $yesterday_question = mysql_fetch_array($yesterday_question_dbquery);
//    $yesterday_question_dbquery = new dbquery($yesterday_question_query, $connid) or die("Yesterday-" . mysql_error());
//    $yesterday_question = $yesterday_question_dbquery->getrowarray();
    $papercode = $yesterday_question['papercode'];
    $qno = $yesterday_question['qno'];

    $str = "SELECT q.qcode,papercode,class,subjectno,qno,question,optiona,optionb,optionc,optiond,
			correct_answer FROM questions q, paper_master pm WHERE q.qcode=pm.qcode and papercode='" . $papercode . "' AND qno=" . $qno;

    //echo "$str<br>";
    $result = mysql_query($str);
    $line = mysql_fetch_array($result);

//    $dbquery = new dbquery($str, $connid);
//
//    $line = $dbquery->getrowarray();

    $class = $line['class'];

    $round = substr($line['papercode'], 2, 1);

    $subjectno = $line['subjectno'];

    $question = $line['question'];
    $question = str_replace("^", "'", $question);
    $optiona = $line['optiona'];
    $optiona = str_replace("^", "'", $optiona);
    $optionb = $line['optionb'];
    $optionb = str_replace("^", "'", $optionb);
    $optionc = $line['optionc'];
    $optionc = str_replace("^", "'", $optionc);
    $optiond = $line['optiond'];
    $optiond = str_replace("^", "'", $optiond);

    $correct_answer = $line['correct_answer'];

    $message.="<tr> ";
    $message.="<td colspan=\"3\">";
    $message.="<BLOCKQUOTE><P><font face=\"Arial\"><BR>";
    if ($subjectno == 1)
        $imgfolder = "Round" . $round . "/english_images/class" . $class;
    elseif ($subjectno == 2)
        $imgfolder = "Round" . $round . "/MATHS_IMAGES/class" . $class;
    elseif ($subjectno == 3)
        $imgfolder = "Round" . $round . "/sci_images/class" . $class;
    elseif ($subjectno == 4)
        $imgfolder = "Round" . $round . "/social_images/class" . $class;

    $question = orig_to_html($line['question'], $imgfolder);
    $question = str_replace("^", "'", $question);


    $optiona = orig_to_html($line['optiona'], $imgfolder);
    $optiona = str_replace("^", "'", $optiona);
    $optionb = orig_to_html($line['optionb'], $imgfolder);
    $optionb = str_replace("^", "'", $optionb);
    $optionc = orig_to_html($line['optionc'], $imgfolder);
    $optionc = str_replace("^", "'", $optionc);
    $optiond = orig_to_html($line['optiond'], $imgfolder);
    $optiond = str_replace("^", "'", $optiond);


    $correct_answer = $line['correct_answer'];

    $header = "<center> $title | Class $class | $subject_arr[$subjectno]</center><br>"; //  | $papercode - $qno

    $header .= $question . "<br><br>";

    $header .= "<table style='font-family: Arial; font-size: 12pt;' width=596><tr><td>A. " . $optiona . "</td><td>B. " . $optionb . "</td></tr><tr><td>C. " . $optionc . "</td><td>D. " . $optiond . "</td></tr></table>";

    $header .= "<br><div>Correct Answer : <b>" . $correct_answer . "</b></div>";
	$header .= "<br><div>Answer given by ".$childname.": <b>" . $userAnswer . "</b></div>";

    $message.=$header;

    $message.="</FONT></P></BLOCKQUOTE><br></td></tr>";
    $footer = "</table><br>";

    $message.=$footer;
    return $message;
}

function orig_to_html($orig, $img_folder) {
    $pattern[0] = "/\[([a-z0-9_\.]*)\]/i";
    $replacement[0] = "<img src='http://www.assetonline.in/asset_online/$img_folder/\$1'>";
    //$pattern[1] = "/\[([a-z0-9_\.]*)[ ]*,[ ]*(.*)\]/i";
    $pattern[1] = "/\[([a-z0-9_\.]*)[ ]*,[ ]*(.[^\]]*)\]/i";
    $replacement[1] = "<img src='http://www.assetonline.in/asset_online/$img_folder/\$1' height=\$2>";
    $pattern[2] = "/\r\n/";
    $replacement[2] = "<br>\n";
    $html_ver = preg_replace($pattern, $replacement, $orig);
    return ($html_ver);
}
function formatDate($oldformat) // function which converts yyyy-mm-dd to dd/mm/yyyy format
	{
	
		$dateParameters=explode("-",$oldformat);
	
		$newformat=$dateParameters[2]."-".$dateParameters[1]."-".$dateParameters[0];
	
		return $newformat;
	
	}
?>