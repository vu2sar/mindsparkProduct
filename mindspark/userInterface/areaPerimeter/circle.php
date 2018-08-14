<?php
//get the value of length
	if($_GET['first'] != "")
	{
		$first=$_GET['first'];
	}
	if($_GET['second'] != "")
	{
		$second=$_GET['second'];
	}
	if($_GET['third'] != "")
	{
		$third=$_GET['third'];
	}
	if($_GET['fourth'] != "")
	{
		$fourth=$_GET['fourth'];
	}
	if($_GET['pos'] != "")
	{
		$pos=$_GET['pos'];
	}
	if($_GET['mes'] != "")
	{
		$mes=$_GET['mes'];
	}



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
	imagesetthickness($image, 1);

	$height=$height*4.5;
	$width=$width*4.5;
	$scale=2;

	$first=$first*$scale;
	$second=$second*$scale;
	$third=$third*$scale;
	$fourth=$fourth*$scale;


	imagefilledrectangle($image,0,0,180,180,$light_yellow);

	imageellipse($image, 80, 80, $first, $first, $grey);
	imagefilledellipse($image,80,80,5,5,$black);

	$i=rand(1,4);
	$j=rand(1,4);
	//$j=4;

	if($i==1)
	{
		if($j==1)
		{
			imageline($image,80- ($first)/2,80,80+ ($first)/2,80,$grey);
			imagestring($image,2.5, 80-10, 68, "D=".($first).$mes, $red);
		}
		else
		{
			imageline($image,80,80,80+ ($first)/2,80,$grey);
			imagestring($image,2.5, 80+ ($first)/4 -10 , 68, ($first/2).$mes, $red);
		}

	}
	else if ($i == 2)
	{
		if($j==2)
		{
			imageline($image,80,80-($first)/2,80,80+($first)/2,$grey);
			imagestring($image,2.5, 80+3, 80-10,  "D=".($first).$mes, $red);
		}
		else
		{
			imageline($image,80,80,80,80+($first)/2,$grey);
			imagestring($image,2.5, 80+3, 80+($first)/4-10,  ($first/2).$mes, $red);
		}

	}
	else if ($i == 3)
	{
		if($j==3)
		{
			imageline($image,80+ ($first)/2,80,80-($first)/2,80,$grey);
			imagestring($image,2.5, 70 , 68, "D=".($first).$mes, $red);
		}
		else
		{
			imageline($image,80,80,80-($first)/2,80,$grey);
			imagestring($image,2.5, 80 - ($first)/4 -10 , 68, ($first/2).$mes, $red);
		}

	}
	else if ($i == 4)
	{
		if($j==4)
		{
			imageline($image,80,80+($first)/2,80,80-($first)/2,$grey);
			imagestring($image,2.5, 80+3, 80,  "D=".($first).$mes, $red);
		}
		else
		{
			imageline($image,80,80,80,80-($first)/2,$grey);
			imagestring($image,2.5, 80+3, 80-($first)/4,  ($first/2).$mes, $red);
		}

	}

	//imagecolortransparent($image, $black);
	if($mes=='cm')
	{
		imagestring($image,2.5,($x1+$x6)/2-10, $y1-14, (($second+$fourth)/2).$mes, $red); //top
	}
	else
	{
		imagestring($image,2.5,($x1+$x6)/2-10, $y1-14, (($second+$fourth)/2).$mes, $red);
	}

	imagecolortransparent($image, $light_yellow);
	imageantialias($image, true);

	header("Content-type: image/png") ;
	imagepng($image) ;

?>


