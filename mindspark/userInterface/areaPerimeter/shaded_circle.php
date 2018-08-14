<?php
//get the value of length
	if($_GET[first] != "")
	{
		$first=$_GET[first];
	}
	if($_GET[second] != "")
	{
		$second=$_GET[second];
	}
	if($_GET[third] != "")
	{
		$third=$_GET[third];
	}
	if($_GET[fourth] != "")
	{
		$fourth=$_GET[fourth];
	}
	if($_GET[pos] != "")
	{
		$pos=$_GET[pos];
	}
	if($_GET[mes] != "")
	{
		$mes=$_GET[mes];
	}
	if($_GET[deg1] != "")
	{
		$deg1=$_GET[deg1];
	}
	if($_GET[deg2] != "")
	{
		$deg2=$_GET[deg2];
	}
	
	$first=70;
	
	$image = imagecreatetruecolor(180, 180) ;
	// background color
	$bg = imagecolorallocate($image, 255, 255, 255) ;
	$blue = imagecolorallocate($image, 0, 0, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	// line color
	$color = imagecolorallocate($image, 15,15 ,255) ;
	$red = imagecolorallocate($image, 224,87 ,87) ;
	$green = imagecolorallocate($image, 0,255,0) ;
	$grey= imagecolorallocate($image, 0,0,255) ;
	$light_yellow=imagecolorallocate($image, 255,253,190) ;
	$navy     = imagecolorallocate($image, 0x00, 0x00, 0x80);
	$grid = imagecolorallocate($image, 175, 175, 175);
	$realgray= imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
	$darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);
	$white    = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$gray     = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
	$darkgray = imagecolorallocate($image, 0x90, 0x90, 0x90);
	$navy     = imagecolorallocate($image, 0x00, 0x00, 0x80);
	$darknavy = imagecolorallocate($image, 0x00, 0x00, 0x50);
	$red      = imagecolorallocate($image, 0xFF, 0x00, 0x00);
	$darkred  = imagecolorallocate($image, 0x90, 0x00, 0x00);
	$steelblue=imagecolorallocate($image, 70,130,180) ;
	$light_grey=imagecolorallocate($image, 205,205,205) ;
	
	
	imagesetthickness($image, 1);
	
	$Width=180;
	$Height=180;
	
	
	$height=$height*4.5;
	$width=$width*4.5;
	$scale=2;

	//imagefill($image, 0, 0, $bg);
	imagefilledrectangle($image,0,0,180,180,$light_yellow);
	$start=0;
	
	

	$x1Point = 90+$first*cos(deg2rad($deg1));      // cos 90 =0
	$y1Point = 90+$first*sin(deg2rad($deg1));	//sin 90 =1
	
	$x2Point = 90+$first*cos(deg2rad($deg2));      // cos 90 =0
	$y2Point = 90+$first*sin(deg2rad($deg2));	
	
	imageellipse($image, 90, 90, $first*2, $first*2, $light_grey);
	
	imagefilledarc($image, 90, 90, $first*2, $first*2, $deg1, $deg2, $light_grey, IMG_ARC_PIE);
	
	imagearc($image, 90, 90, $first/2, $first/2, $deg1, $deg2, $black);
	
	//imagefilledarc($image, 80, 80, $first, $first, $deg1, $deg2, $light_grey, IMG_ARC_PIE);
	//imagefilledarc($image, 80, 80, $r, $r, 45, 75 , $white, IMG_ARC_PIE);
	//imagefilledarc($image, 80, 80, $r, $r, 75, 360 , $white, IMG_ARC_PIE);
	//imagegrid($image, $Width, $Height, 20, IMG_COLOR_STYLED);
	
	imagestring($image,7,80, 80, "P", $black);
	imagesetpixel($image, 90,90, $red);
	//imagestring($image,3,$xPoint+80, $yPoint+80-12, "q", $red); //top
	
	$x1=(90+$x1Point)/2;
	$x2=(90+$x2Point)/2;
	
	$y1=(90+$y1Point)/2;
	$y2=(90+$y2Point)/2;
	
	$midX=($x1+$x2)/2;
	$midY=($y1+$y2)/2;	
	
	$angle='&#60;';
	$angle=$deg2-$deg1;
	imagestring($image,7, $x1Point, $y1Point, "Q", $black); //top
	imagestring($image,7, $x2Point, $y2Point, "R", $black); //top
	
	//imagestring($image,4, 120, 10, "pqr=".$angle, $black); //top
	
	//imageline($image,90,90,$x1Point,$y1Point,$red);
	//imageline($image,90,90,$x2Point,$y2Point,$red);
	
	//imageline($image,$x1, $y1,$x2,$y2, $blue);
	
	//imageline($image,$x1,$y1,$midX, $midY, $red);
	
	imagecolortransparent($image, $light_yellow);
	//imageantialias($image, true);
	
	header("Content-type: image/png") ;
	imagepng($image) ;
	
	
?>

	
