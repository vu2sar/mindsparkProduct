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
	

	
	$image = imagecreatetruecolor(180, 150) ;
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
	$depth=30;
	
	
	$x1=30;
	$y1=30;
	$x2=30;
	$y2=30+$second;
	$x3=30+$first+$third;
	$y3=30+$second;
	$x4=30+$first+$third;
	$y4=30+$second-$fourth;
	$x5=30+$first;
	$y5=30+$second-$fourth;
	$x6=30+$first;
	$y6=30;
	
	$values=array($x1,$y1,
			    $x2,$y2,
			    $x3,$y3,
			    $x4,$y4,
			    $x5,$y5,
			    $x6,$y6     );
	
	imagefilledrectangle($image,0,0,180,180,$light_yellow);
	
	$angle=270;
	$about_x=0;
	$about_y=0;
	$shift_x=0;
	$shift_y=0;
	$translated=0;
	
	//if you want to rotate make if condition properly
	if( 1==0)
	{
	$translated=1;
	translate_point(&$x1,&$y1,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x2,&$y2,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x3,&$y3,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x4,&$y4,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x5,&$y5,$angle,$about_x,$about_y,$shift_x,$shift_y);
	translate_point(&$x6,&$y6,$angle,$about_x,$about_y,$shift_x,$shift_y);
	
	$y_array=array($y1,$y2,$y3,$y4,$y5,$y6);
	$shift_y=abs(min($y_array));
	
	
	$values=array($x1,$y1,
			    $x2,$y2,
			    $x3,$y3,
			    $x4,$y4,
			    $x5,$y5,
			    $x6,$y6     );
	
	
	$x1=shift($x1,$shift_x);
	$x2=shift($x2,$shift_x);
	$x3=shift($x3,$shift_x);
	$x4=shift($x4,$shift_x);
	$x5=shift($x5,$shift_x);
	$x6=shift($x6,$shift_x);
	
	$y1=shift($y1,$shift_y);
	$y2=shift($y2,$shift_y);
	$y3=shift($y3,$shift_y);
	$y4=shift($y4,$shift_y);
	$y5=shift($y5,$shift_y);
	$y6=shift($y6,$shift_y);
	
	}
	
	$array_len=6;	
	
	
	$values=array($x1,$y1,
			    $x2,$y2,
			    $x3,$y3,
			    $x4,$y4,
			    $x5,$y5,
			    $x6,$y6     );
			    
	
	imagepolygon($image,$values,$array_len,$grey);
	
	//imagecolortransparent($image, $black);
	if($mes=='cm')
	{
		//imagestring($image,2.5,$x1-24, $y1+($first)/2-0, '|', $red);
		imagestring($image,2.5,$x1+($first)/2-12, $y1-15, ($first/2).$mes, $red);
		imagestring($image,2.5,$x1-25, $y1+($second)/2-10, ($second/2).$mes, $red);
		imagestring($image,2.5,$x2+($third)/2,$y2, ( $first/2+$third/2 ).$mes, $red);
		imagestring($image,2.5,$x4+3,$y4+($fourth/2)-10, ( $fourth/2).$mes, $red);
	}
	else
	{
		imagestring($image,2.5,$x1+($first)/2-8, $y1-15, ($first/2).$mes, $red);
		imagestring($image,2.5,$x1-20, $y1+($second)/2-10, ($second/2).$mes, $red);
		imagestring($image,2.5,$x2+($third)/2,$y2, ( $first/2+$third/2 ).$mes, $red);
		imagestring($image,2.5,$x4+3,$y4+($fourth/2)-10, ( $fourth/2).$mes, $red);
	}
	imagecolortransparent($image, $light_yellow);
	imageantialias($image, true);
	header("Content-type: image/png") ;
	imagepng($image) ;

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
	function rotate_right90($im)
	{
		 $wid = imagesx($im);
		 $hei = imagesy($im);
		 $im2 = imagecreatetruecolor($hei,$wid);

		for($i = 0;$i < $wid; $i++)
		{
			for($j = 0;$j < $hei; $j++)
			{
			   $ref = imagecolorat($im,$i,$j);
			   imagesetpixel($im2,($hei - 1) - $j,$i,$ref);
			}
		}
	 return $im2;
	}
	
	function flip($im)
	{
		 $wid = imagesx($im);
		 $hei = imagesy($im);
		 $im2 = imagecreatetruecolor($wid,$hei);

		for($i = 0;$i < $wid; $i++)
		{
			for($j = 0;$j < $hei; $j++)
			{
				$ref = imagecolorat($im,$i,$j);
				imagesetpixel($im2,$i,$hei - $j,$ref);
			}
		}
	return $im2;
	}
	
	function mirror($im)
	{
		 $wid = imagesx($im);
		 $hei = imagesy($im);
		 $im2 = imagecreatetruecolor($wid,$hei);

		for($i = 0;$i < $wid; $i++)
		{
			for($j = 0;$j < $hei; $j++)
			{
			$ref = imagecolorat($im,$i,$j);
			imagesetpixel($im2,$wid - $i,$j,$ref);
			}
		}
	return $im2;
	}
	
	function imageRotate1($src_img, $angle, $bicubic=false) 
	{
		
	   // convert degrees to radians
	   $angle = $angle + 180;
	   $angle = deg2rad($angle);
	 
	   $src_x = imagesx($src_img);
	   $src_y = imagesy($src_img);
	 
	   $center_x = floor($src_x/2);
	   $center_y = floor($src_y/2);

	   $cosangle = cos($angle);
	   $sinangle = sin($angle);

	   $corners=array(array(0,0), array($src_x,0), array($src_x,$src_y), array(0,$src_y));

	   foreach($corners as $key=>$value) {
	     $value[0]-=$center_x;        //Translate coords to center for rotation
	     $value[1]-=$center_y;
	     $temp=array();
	     $temp[0]=$value[0]*$cosangle+$value[1]*$sinangle;
	     $temp[1]=$value[1]*$cosangle-$value[0]*$sinangle;
	     $corners[$key]=$temp;   
	   }
	  
	   $min_x=1000000000000000;
	   $max_x=-1000000000000000;
	   $min_y=1000000000000000;
	   $max_y=-1000000000000000;
	  
	   foreach($corners as $key => $value) {
	     if($value[0]<$min_x)
	       $min_x=$value[0];
	     if($value[0]>$max_x)
	       $max_x=$value[0];
	  
	     if($value[1]<$min_y)
	       $min_y=$value[1];
	     if($value[1]>$max_y)
	       $max_y=$value[1];
	   }

	   $rotate_width=round($max_x-$min_x);
	   $rotate_height=round($max_y-$min_y);

	   $rotate=imagecreatetruecolor($rotate_width,$rotate_height);
	   imagealphablending($rotate, false);
	   imagesavealpha($rotate, true);

	   //Reset center to center of our image
	   $newcenter_x = ($rotate_width)/2;
	   $newcenter_y = ($rotate_height)/2;

	   for ($y = 0; $y < ($rotate_height); $y++) {
	     for ($x = 0; $x < ($rotate_width); $x++) {
	       // rotate...
	       $old_x = round((($newcenter_x-$x) * $cosangle + ($newcenter_y-$y) * $sinangle))
		 + $center_x;
	       $old_y = round((($newcenter_y-$y) * $cosangle - ($newcenter_x-$x) * $sinangle))
		 + $center_y;
	     
	       if ( $old_x >= 0 && $old_x < $src_x
		     && $old_y >= 0 && $old_y < $src_y ) {

		   $color = imagecolorat($src_img, $old_x, $old_y);
	       } else {
		 // this line sets the background colour
		 $color = imagecolorallocatealpha($src_img, 255, 255, 255, 127);
		 //$color=imagecolorallocate($src_img, 0, 0, 0);
	       }
	       imagesetpixel($rotate, $x, $y, $color);
	     }
	   }
	  
	  return($rotate);
	}

?>

	
