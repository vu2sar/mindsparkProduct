<?php
error_reporting(E_ERROR);
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');


function fetchFullName($userID)
{
	$fullname=" ";

	$query = "SELECT firstName,lastName FROM emp_master WHERE userID = '".$userID."' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$user_row=mysql_fetch_array($result);
	$fullname = $user_row["firstName"]." ".$user_row["lastName"];
	
	if($fullname == " ")
	{
		$query = "SELECT firstName,lastName FROM old_emp_master WHERE userID = '".$userID."' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$user_row = mysql_fetch_array($result);
		$fullname = $user_row["firstName"]." ".$user_row["lastName"];
	}
	return $fullname;
}

function generateCertificate($kudo_id)
{
$query = 'SELECT sender, receiver, sent_date, message, kudo_type FROM kudos_master WHERE kudo_id = '.$kudo_id.' 	LIMIT 1';
$result = mysql_query($query) or die('Select Query Failed: '.mysql_error());
while($row = mysql_fetch_array($result))
{
	$from = fetchFullName($row['sender']);
	$type = $row['kudo_type'];
	$to = fetchFullName($row['receiver']);
	$message = $row['message'];
	$date = date('d F, Y', strtotime($row['sent_date']));
	//$message = nl2br($message);
}

$backImg = '';

if($type == 'Thank You')
{
	$backColor = '#FCD210';
	/*$backColor_r = 252;
	$backColor_g = 210;
	$backColor_b = 16;*/
	$backImg = 'thankyou_background.png';
}
elseif($type == 'Good Work')
{
	$backColor = '#FF9146';
	/*$backColor_r = 255;
	$backColor_g = 145;
	$backColor_b = 70;*/
	$backImg = 'goodwork_background.png';
}
elseif($type == 'Impressive')
{
	$backColor = '#B1DE5D';
	/*$backColor_r = 177;
	$backColor_g = 222;
	$backColor_b = 93;*/
	$backImg = 'impressive_background.png';
}
elseif($type == 'Exceptional')
{
	$backColor = '#36A9E1';
	/*$backColor_r = 54;
	$backColor_g = 169;
	$backColor_b = 225;*/
	$backImg = 'exceptional_background.png';
}

$docName = $kudo_id;

$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Educational Initiatives');
$pdf->SetTitle('Certificate');
$pdf->SetSubject('Certificate');
$pdf->SetKeywords('Certificate');



// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(10, 10, 5);
//$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(0);
	

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}*/

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// convert TTF font to TCPDF format and store it on the fonts folder
/*$politicaItalic = $pdf->addTTFfont('../fonts/PoliticaItalic.ttf', 'TrueTypeUnicode', '', 96);
$politicaBold = $pdf->addTTFfont('../fonts/Politica Bold.ttf', 'TrueTypeUnicode', '', 96);
*/


// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 24, '', true);

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// Add a page
$pdf->AddPage();


// use the font
//$pdf->SetFont($politicaBold, '', 24, '', false);


$upperCaseType = strtoupper($type);

$img_file = 'images/'.$backImg;
$pdf->Image($img_file, 8, 73, 280, 125.5, 'PNG', '', '', false, 300, '', false, false, 0);

// Set some content to print
$html = <<<EOD

		<img src="images/ei_logo.png" height='20px' width='20px'>
	
		<h1 style = 'text-decoration:none;font-color:$backColor'>$upperCaseType</h1>
	
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'L', true);

$pdf->SetFont('dejavusans', '', 18, '', true);

// use the font
//$pdf->SetFont($politicaBold, '', 18, '', false);

//<div style="text-decoration:none;background-color:$backColor;color:white;border-radius:0px 10px 10px 0px;border: 3px dashed $backColor;padding-bottom:5px;">

$html = <<<EOD
<div bgcolor='$backColor' color="#FFF" style="text-decoration:none;">
<h3>This certificate of <label style="font-size:20;font-weight:bold">$type</label> is awarded to</h3>
<label style="font-size:24;">$to</label>
<br/>
<br/>
<label>For</label>
<br/>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);

$html = <<<EOD
<label style="font-size:22;">$message</label>
EOD;

// store current object
$pdf->startTransaction();
// get the number of lines for multicell

$lines = $pdf->MultiCell(0, 0, $html, 0, 'C', 0, 0, '', '', true, 0, false, true, 0);
// restore previous object
$pdf = $pdf->rollbackTransaction();

if($lines < 5)
{
	$brAppend = '';
	for($i=0; $i<5-$lines; $i++)
	{
		$brAppend .= '<br/>';
	}	
}
	
	
$html = <<<EOD
<label style="font-size:22;color: #fff">$message</label>
$brAppend
</div>
EOD;

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'C', true);

$pdf->Image('images/ei_icon.png', 220, 15, 70, 70, 'PNG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);

$pdf->SetXY(220, 185);
$pdf->write(0, $from, '', 0, 'C', true, 0, false, false, 0);

$pdf->SetXY(13, 75);
$pdf->write(0, $date, '', 0, 'L', true, 0, false, false, 0);



// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output("certificates/".$docName.".pdf", 'F');
$pdf->Output("certificates/".$type."_".$docName.".pdf", 'F');

//============================================================+
// END OF FILE
//============================================================+

}

?>