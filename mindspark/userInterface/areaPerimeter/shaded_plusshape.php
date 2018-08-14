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
		$reserve_third=$_GET[third];
	}
	if($_GET[pos] != "")
	{
		$pos=$_GET[pos];
	}
	if($_GET[mes] != "")
	{
		$mes=$_GET[mes];
	}
	if($_GET['depth'] != "")
	{
		$depth=$_GET['depth'];
	}
	//$first=25;
	//$second=25;
	//$third=25;



	$image = imagecreatetruecolor(185, 190) ;
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
	$specified_grey=imagecolorallocate($image, 128,128,128) ;
	$specified_bisc=imagecolorallocate($image, 234,230,209) ;
	imagesetthickness($image, 1);

	$height=$height*4.5;
	$width=$width*4.5;
	$scale=2;

	$first=$first*$scale;
	$second=$second*$scale;
	$third=$third*$scale;
	//$depth=rand(15,25);

	//$depth=22;
	$depth1=$depth*2;
	//$depth=25;

	$x1=20+$depth1;
	$y1=20;
	$x2=20+$depth1;
	$y2=20+$first;
	$x3=20;
	$y3=20+$first;
	$x4=20;
	$y4=20+$first+$second;
	$x5=20+$depth1;
	$y5=20+$first+$second;
	$x6=20+$depth1;
	$y6=20+$first+$second+$first;
	$x7=20+$depth1+$third;
	$y7=20+$first+$second+$first;
	$x8=20+$depth1+$third;
	$y8=20+$first+$second;
	$x9=20+$depth1+$third+$depth1;
	$y9=20+$first+$second;
	$x10=20+$depth1+$third+$depth1;
	$y10=20+$first;
	$x11=20+$depth1+$third;
	$y11=20+$first;
	$x12=20+$depth1+$third;
	$y12=20;

	$values1=array($x1,$y1,
			    $x2,$y2,
			    $x3,$y3,
			    $x4,$y4,
			    $x5,$y5,
			    $x6,$y6,
			    $x7,$y7,
			    $x8,$y8,
			    $x9,$y9,
			    $x10,$y10,
			    $x11,$y11,
			    $x12,$y12    );

	//$values=array(25,25,25,25+$height,25+$width,25+$height,25+$width,25);
	$array_len1=12;

	imagefilledrectangle($image,0,0,185,190,$light_yellow);
	//imagecolortransparent($image, $black);





	//imagestring($image,2.5 , $values[0],$values[1]+20, $height/$scale.$mes, $red);

	//$font = 'font/arial.ttf';
	//$text="10cm";
	//imagettftext($image, 18, 0, $values[0], $values[1]-40, $green, $font, $text);



	$nx1=20;
	$ny1=$y1;
	$nx2=$x9;
	$ny2=$y1;

	imageline($image, $nx1,$ny1,$nx2, $ny2, $color);

	$nx3=20;
	$ny3=$y1;
	$nx4=20;
	$ny4=$y6;

	imageline($image, $nx3,$ny3,$nx4, $ny4, $color);

	$nx5=20;
	$ny5=$y6;
	$nx6=$x9;
	$ny6=$y6;

	imageline($image, $nx5,$ny5,$nx6, $ny6, $color);

	$nx7=$x9;
	$ny7=$y1;
	$nx8=$x9;
	$ny8=$y6;

	imageline($image, $nx7,$ny7,$nx8, $ny8, $color);

	$values=array( $nx1,$ny1,
			$nx2,$ny2,
			$nx6,$ny6,
			$nx4,$ny4,
			);
	$array_len=4;

	imagefilledpolygon($image,$values,$array_len,$specified_bisc);

	imagefilledpolygon($image,$values1,$array_len1,$specified_grey);


	if($mes=='cm')
	{
		//imagestring($image,2.5,$x1-24, $y1+($first)/2-0, '|', $red);
		imagestring($image,2.5,0, $y1+($first)/2-10, ($first/2).$mes, $red);
		//imagestring($image,2.5,$x1-24, $y1+($first)/2-20, '|', $red);
		imagestring($image,2.5,(($x1+$x12)/2)-12, ($y1+$y12)/2-20, ($third/2).$mes, $red);
		//imagestring($image,2.5,$x10-24, $y10+($second)/2-0, '|', $red);
		imagestring($image,2.5,$x10+3, $y10+($second)/2-10, ($second/2).$mes, $red);
		//imagestring($image,2.5,$x10-24, $y10+($second)/2-20, '|', $red);
		imagestring($image,2.5,$x4+($depth1)/2-12 , $y4, ''.($depth).$mes, $red);
	}
	else
	{
		imagestring($image,2.5,2, $y1+($first)/2-10, ($first/2).$mes, $red);
		imagestring($image,2.5,(($x1+$x12)/2)-10, ($y1+$y12)/2-20, ($third/2).$mes, $red);
		imagestring($image,2.5,$x10+3, $y10+($second)/2-10, ($second/2).$mes, $red);
		imagestring($image,2.5,$x4+($depth1)/2-10 , $y4, ''.($depth).$mes, $red);
	}

	imageline($image,$x1,($y1+$y12)/2-5,$x12,($y1+$y12)/2-5, $color);
	imageline($image,$x1,($y1+$y12)/2-2,$x1,($y1+$y12)/2-8, $color);
	imageline($image,$x12,($y1+$y12)/2-2,$x12,($y1+$y12)/2-8, $color);

	//imageline($image,$x10,$y1,$x10,$y12, $color);
	//imageline($image,$x1,($y1+$y12)/2-2,$x1,($y1+$y12)/2-8, $color);
	//imageline($image,$x12,($y1+$y12)/2-2,$x12,($y1+$y12)/2-8, $color);


	$nx5=20;
	$ny5=$y6+10;
	$nx6=$x9;
	$ny6=$y6+10;

	imageline($image, $nx5,$ny5,$nx6/2-4, $ny6, $color);
	imagestring($image,2.5,$nx6/2 , $ny6-7, (2*$depth+$reserve_third).$mes, $red);
	imageline($image,$nx5,$ny5-3,$nx5, $ny6+3, $color);

	if($mes=='m')
	{
		imageline($image, $nx6/2+20,$ny5,$nx6, $ny6, $color);

	}
	else
	{
		imageline($image, $nx6/2+25,$ny5,$nx6, $ny6, $color);
	}
	imageline($image, $nx6,$ny6-3,$nx6, $ny6+3, $color);

	imageline($image, 10,20,10, $y2/2, $color);
	imageline($image, 7,20,13, 20, $color);
	imageline($image, 10,($y2)/2+14,10, $y2, $color);
	imageline($image, 7,$y2,13, $y2, $color);

	imageline($image, 10,$y4,10, ($y5+$y6)/2-7, $color);
	imageline($image, 7,$y4,13, $y4, $color);
	imagestring($image,2.5,0, ($y5+$y6)/2-7, ($first/2).$mes, $red);
	imageline($image, 10,($y5+$y6)/2+7,10, $y6, $color);
	imageline($image, 7,$y6,13, $y6, $color);
	//imagecolortransparent($image, $bg);
	//$image = imagerotate($image, 45,0);
	imagecolortransparent($image, $light_yellow);
	imageantialias($image, true);


	//imagecolortransparent($image, $black);
	//imagefilledpolygon($image, $values, $array_len, $bg);

	header("Content-type: image/png") ;
	imagepng($image) ;

?>