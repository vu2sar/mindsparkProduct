<?php

	//get the value of length
	if($_GET[height] != "")
	{
		$height=$_GET[height];
	}
	if($_GET[width] != "")
	{
		$width=$_GET[width];
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
	$green = imagecolorallocate($image, 0,255,0) ;
	$grey= imagecolorallocate($image, 0,0,255) ;
	$light_yellow=imagecolorallocate($image, 255,253,190) ;
	imagesetthickness($image, 1);
	//apply scale
	/*
	if($height>= 22 && $width<= 22 )
	{	
		$height=$height*4;
		$width=$width*4;
		$scale=4;
	}
	else if($width > 16 && $width< 20 )
	{	
		$height=$height*5;
		$width=$width*5;
		$scale=5;
	}
	else if($width <= 16)
	{
		$height=$height*7;
		$width=$width*7;
		$scale=7;
	}*/
	$scale=11;
	$height=$height*$scale;
	$width=$width*$scale;
	
	$values=array(25,25,25,25+$height,25+$width,25+$height,25+$width,25);
	
	//$values=apply_rotation(0.5,25,0.5,25+$height,0.5+$width,25+$height,0.5+$width,25);
	
	//echo "<pre>";
	//print_r($values);
	//echo "</pre>";
	//exit;
	
	//$values=array(25,25,25,25+$height,25+$width,25+$height,25+$width,25);
	$array_len=4;	
		
	imagefilledrectangle($image,0,0,180,180,$light_yellow);
	//imagecolortransparent($image, $black);
	
	if($mes=='cm')
	{
		imagestring($image,2.5,($values[0]+$values[6])/2-20, ($values[1]+$values[7])/2-15 , $width/$scale.$mes, $red);
		imagestring($image,2.5 , ($values[0]+$values[2])/2-25,($values[1]+$values[3])/2-5, $height/$scale.$mes, $red);
	}
	else
	{
		imagestring($image,2.5,($values[0]+$values[6])/2-15, ($values[1]+$values[7])/2-15 , $width/$scale.$mes, $red);
		imagestring($image,2.5 , ($values[0]+$values[2])/2-20,($values[1]+$values[3])/2-5, $height/$scale.$mes, $red);
	}
	//$font = 'font/arial.ttf';
	//$text="10cm";
	//imagettftext($image, 18, 0, $values[0], $values[1]-40, $green, $font, $text);
	
	imagepolygon($image,$values,$array_len,$grey);
	
	//imagecolortransparent($image, $bg);
	//$image = imagerotate($image, 45,0);
	imagecolortransparent($image, $light_yellow);
	imageantialias($image, true);
	
	
	//imagecolortransparent($image, $black);
	//imagefilledpolygon($image, $values, $array_len, $bg);
	
	header("Content-type: image/png") ;
	imagepng($image) ;
?>
<?php
function apply_rotation($px0,$py0,$px1,$py1,$px2,$py2,$px3,$py3)
{
	
	//$height=150;
	//$width=150;
	global $x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3;
	global $values;
	$x0=$px0;
	$y0=$py0;
	$x1=$px1;
	$y1=$py1;
	$x2=$px2;
	$y2=$py2;
	$x3=$px3;
	$y3=$py3;
	//$x0=50;
	//$y0=50;
	//$x1=50;
	//$y1=50+$height;
	//$x2=50+$width;
	//$y2=50+$height;
	//$x3=50+$width;
	//$y3=50;
	
	//$values=array($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3);
	//$values=array(250,250,250,250+$height,250+$width,250+$height,250+$width,250);
	
	$r0=sqrt($x0*$x0+$y0*$y0);
	$f0=(atan(($y0/$x0)));
	$re=tan(deg2rad(45));
	$d=tan(atan(1));
	//echo "fo is ".tan(($f0));
	//echo "re is ".$re;
	//exit;
	
	$r1=sqrt($x1*$x1+$y1*$y1);
	$f1=atan(($y1/$x1));
	
	$r2=sqrt($x2*$x2+$y2*$y2);
	$f2=atan(($y2/$x2));
	
	$r3=sqrt($x3*$x3+$y3*$y3);
	$f3=atan(($y3/$x3));
	
	//new angle
	$f0=$f0+deg2rad(-45);
	$f1=$f1+deg2rad(-45);
	$f2=$f2+deg2rad(-45);
	$f3=$f3+deg2rad(-45);
	
	//$r0=$r1=$r3=$r2=115;
	//new values
	$x0=$r0*cos(($f0));
	$y0=$r0*sin(($f0));
	
	$x1=$r1*cos(($f1));
	$y1=$r1*sin(($f1));
	
	$x2=$r2*cos(($f2));
	$y2=$r2*sin(($f2));
	
	$x3=$r3*cos(($f3));
	$y3=$r3*sin(($f3));
	
	//rotate
	$values=array($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3);
	$values=array($x0,$y0+70,$x1,$y1+70,$x2,$y2+70,$x3,$y3+70);
	//echo "<pre>";
	//print_r($values);
	//echo "</pre>";
	//exit;
	return $values;
}
?>
