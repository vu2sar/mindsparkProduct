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



	$image = imagecreatetruecolor(180, 180) ;
	// background color
	$bg = imagecolorallocate($image, 255, 255, 255) ;
	$blue = imagecolorallocate($image, 0, 0, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	// line color
	$color = imagecolorallocate($image, 15,15 ,255) ;
	$red = imagecolorallocate($image, 224,87 ,87) ;
	$red1 = imagecolorallocate($image, 255,0 ,0) ;
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
	$depth=30;

	$i=rand(1,2);

	if($i==1)
	{
	$x1=40;
	$y1=40;
	$x2=40-$second;
	$y2=40+$third;
	$x3=40+$first-$second;
	$y3=40+$third;
	$x4=40+$first;
	$y4=40;
	}

	if($i==2)
	{
	$x1=40;
	$y1=40;
	$x2=40+$second;
	$y2=40+$third;
	$x3=40+$first+$second;
	$y3=40+$third;
	$x4=40+$first;
	$y4=40;
	}

	imagefilledrectangle($image,0,0,180,180,$light_yellow);

	$array_len=4;

	if($i == 1)
	{
		$values=array($x1,$y1,
				    $x2,$y2,
				    $x3,$y3,
				    $x4,$y4
				     );


		imagepolygon($image,$values,$array_len,$grey);
		imagedashedline($image,40,40,40,40+$third, $grey);
		imageline($image,40,40+$third -8,48,40+$third -8,$grey);
		imageline($image,48,40+$third -8,48,40+$third,$grey);
	}
	if($i == 2)
	{
		$values=array($x1,$y1,
				    $x2,$y2,
				    $x3,$y3,
				    $x4,$y4
				     );


		imagepolygon($image,$values,$array_len,$grey);
		imagedashedline($image,$x4,$y4,$x4,$y4+$third, $grey);
		imageline($image,$x4,$y4+$third-8,$x4+8,$y4+$third-8,$grey);
		imageline($image,$x4+8,$y4+$third-8,$x4+8,$y4+$third,$grey);
	}
	//imagecolortransparent($image, $black);
	if($i==1)
	{
		if($mes=='cm')
		{
		imagestring($image,2.5,($x1+$x4)/2-10, $y1-14, (($first)/2).$mes, $red);
		imagestring($image,2.5,$x1+3, ($y1+$y2)/2-10, ($third/2).$mes, $red);
		}
		else
		{
		imagestring($image,2.5,($x1+$x4)/2-10, $y1-14, (($first)/2).$mes, $red);
		imagestring($image,2.5,$x1+3, ($y1+$y2)/2-10, ($third/2).$mes, $red);
		}
	}
	if($i==2)
	{
		if($mes=='cm')
		{
		imagestring($image,2.5,($x1+$x4)/2-10, $y1-14, (($first)/2).$mes, $red);
		imagestring($image,2.5,$x4+3, ($y4+$y3)/2-10, ($third/2).$mes, $red);
		}
		else
		{
		imagestring($image,2.5,($x1+$x4)/2-10, $y1-14, (($first)/2).$mes, $red);
		imagestring($image,2.5,$x4+3, ($y4+$y3)/2-10, ($third/2).$mes, $red);
		}
	}


	imagecolortransparent($image, $light_yellow);
	imageantialias($image, true);

	header("Content-type: image/png") ;
	imagepng($image) ;

?>


