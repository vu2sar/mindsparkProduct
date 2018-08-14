<?php
	
	//genrate random number between 3 to 8;
	
	$x = $_GET['x'];
	$y = $_GET['y'];
	$c = $_GET['c'];
	$a = $_GET['a'];
	$b = $_GET['b'];
	$b1 = $_GET['b1'];
	$b2 = $_GET['b2'];
	$ht= $_GET['ht'];
	if($_GET[mes] != "")
	{
		$mes=$_GET[mes];
	}
	/*
	//$maxwidth = $c*20+20;
	$image= imagecreate(180, 150);
	//$bg= imagecolorallocate ($image, 255, 255, 255);
	$text_color= imagecolorallocate ($image, 0,0,255);
	$graph_color= imagecolorallocate ($image,0,0,0);
	*/
	$image = imagecreatetruecolor(180, 180);
	//imageantialias($image, true);
	$black = imagecolorallocate($image, 0, 0, 0);
	$line_color=imagecolorallocate($image, 255, 0, 0);
	$white = imagecolorallocate($image, 0,0, 255);
	$grey= imagecolorallocate($image, 0,0,255) ;
	$red = imagecolorallocate($image, 224,87 ,87) ;
	$light_yellow=imagecolorallocate($image, 255,253,190) ;
	imagesetthickness($image, 1);
		
	imagefilledrectangle($image,0,0,180,180,$light_yellow);	
	/*imageline($img,0,0,$c*16,0, $graph_color);
	imageline($img,0,0,$x*16,$y*16, $graph_color);
	imageline($img,$c*16,0,$x*16,$y*16, $graph_color);
	*/
	$s1=4;
	
	
	//imageline($image,0,$y*$s1,$c*$s1,$y*$s1, $grey);
	//imageline($image,0,$y*$s1,$x*$s1,0, $grey);
	//imageline($image,$c*$s1,$y*$s1,$x*$s1,0, $grey);
	
	$scale=4;
	$b1=$b1*$scale;
	$b2=$b2*$scale;
	$y=$y*$scale;
	
	$x1=80;
	$y1=0;
	$x2=80-$b1;
	$y2=$y;
	$x3=80;
	$y3=$y;
	$x4=80+$b2;
	$y4=$y;
	$array_len=4;	
	
	$angle=270;
	$about_x=0;
	$about_y=0;
	$shift_x=0;
	$shift_y=0;
	$translated=0;
	
	if( rand(0,10)%2 ==0)
	{
	$translated=1;
	translate_point(&$x1,&$y1,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x2,&$y2,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x3,&$y3,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x4,&$y4,$angle,$about_x,$about_y,$shift_x,$shift_y);
	
	$y_array=array($y1,$y2,$y3,$y4);
	$shift_y=abs(min($y_array));
	
	
	$x1=shift($x1,$shift_x);
	$x2=shift($x2,$shift_x);
	$x3=shift($x3,$shift_x);
	$x4=shift($x4,$shift_x);
	
	$y1=shift($y1,$shift_y);
	$y2=shift($y2,$shift_y);
	$y3=shift($y3,$shift_y);
	$y4=shift($y4,$shift_y);
	}
	$values=array($x1,$y1,
			    $x2,$y2,
			    $x3,$y3,
			    $x4,$y4 );
			    
	imageline($image,$x1,$y1,$x3,$y3,$grey);
	
	if(!$translated)
	{
		imageline($image,$x3,$y3-7,$x3+7,$y3-7,$grey);
		imageline($image,$x3+7,$y3-7,$x3+7,$y3,$grey);
		
		imagestring($image,2,($x1+$x3)/2+3, ($y1+$y3)/2,$ht.$mes,$red);
		imagestring($image,2,($x2+$x4)/2, ($y2+$y4)/2,$c.$mes,$red);
		
	}
	else
	{
		imageline($image,$x3,$y3-7,$x3-7,$y3-7,$grey);
		imageline($image,$x3-7,$y3-7,$x3-7,$y3,$grey);
		
		imagestring($image,2,($x1+$x3)/2, ($y1+$y3)/2,$ht.$mes,$red);
		imagestring($image,2,($x2+$x4)/2+3, ($y2+$y4)/2,$c.$mes,$red);
	}
	
	/*
	if($b2 >= $b1)
	{
		
		if($c==1)
		{
			imageline($image,$x3,$y3-7,$x3+7,$y3-7,$grey);
			imageline($image,$x3+7,$y3-7,$x3+7,$y3,$grey);
		}
		else
		{
			imageline($image,$x3,$y3-10,$x3+10,$y3-10,$grey);
			imageline($image,$x3+10,$y3-10,$x+10,$y3,$grey);
		}
	}
	else
	{
		//imageline($image,$x,$y-10,$x-10,$y-10,$grey);
		//imageline($image,$x-10,$y-10,$x-10,$y,$grey);
		if($c==1)
		{
			imageline($image,$x3,$y3-7,$x3-7,$y3-7,$grey);
			imageline($image,$x3-7,$y3-7,$x3-7,$y3,$grey);
		}
		else
		{
			imageline($image,$x3,$y3-10,$x3-10,$y3-10,$grey);
			imageline($image,$x3-10,$y3-10,$x3-10,$y3,$grey);
		}
	}
	*/
	
	
	
	/*
	echo "<pre>";
	print_r($values);
	print_r($y_array);
	echo "shift_y is ".$shift_y;
	echo "</pre>";
	*/
	imagepolygon($image,$values,$array_len,$grey);
	
	imagecolortransparent($image, $light_yellow);
	
	header('Content-type: image/png');
	imagepng($image);
	imagedestroy($image);

	
	function translate_point(&$x,&$y,$angle,$about_x,$about_y,$shift_x,$shift_y)
	{
	    $x -= $about_x;
	    $y -= $about_y;
	    $angle = ($angle / 180) * M_PI;
		/* math:
		[x2,y2] = [x,  *  [[cos(a),-sin(a)],
			   y]      [sin(a),cos(a)]]
		==>
		x = x * cos(a) + y*sin(a)
		y = x*-sin(a) + y*cos(a)
		*/

	    $new_x = $x * cos($angle) - $y * sin($angle);
	    $new_y = $x * sin($angle) + $y * cos($angle);
	    //$x = $new_x+ $about_x + $shift_x ;
	   // $y = $new_y + $about_y + $shift_y;
	   $x = $new_x;
	   $y = $new_y;
	   //$x=$x+$shift_x;
	   //$y=$y+$shift_y;
	}
	function shift($x,$shift)
	{
		$shifted_x=($x)+($shift);
		return $shifted_x;
	}

?>