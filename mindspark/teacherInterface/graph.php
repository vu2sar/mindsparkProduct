<?php
	header("Content-type: image/png");
	if(isset($_GET['actual']) && isset($_GET['max']) && isset($_GET['avg']))
	{
		$avg = $origavg = $_GET['avg'];
		$actual = $origactual = $_GET['actual'];
		$max = ceil($_GET['max']);
		$scale = ceil($_GET['scale']);

		if(isset($_GET['width']))
			$width =  $_GET['width'];
		else
			$width =  500;
		if(isset($_GET['height']))
			$height =  $_GET['height'];
		else
			$height =  60;

	    $showMax = isset($_GET['showMax'])?$_GET['showMax']:1;
	    $showAvg = isset($_GET['showAvg'])?$_GET['showAvg']:1;
		$paddingleftright = 20;
		$paddingtopbottom = 5;
		$boxWidth 	= $width-$paddingleftright;
		$boxheight  = $height-$paddingtopbottom - 4;
		$actual = (round($actual)/$scale)*$boxWidth;
		$max = (round($max)/$scale)*$boxWidth + ($paddingleftright/2) ;
		$avg = (round($avg)/$scale)*$boxWidth + $paddingleftright/2 ;
		$im = imagecreate($width,$height);
		$white = imagecolorallocate($im,255,255,255);
		$black = imagecolorallocate($im,0,0,0);
		$green = imagecolorallocate($im,187,255,187);
		$blue = imagecolorallocate($im,73,1,222);
		$red = imagecolorallocate($im, 255,0,0);
		$bgcolor = imagecolorallocatealpha($im,230,187,116,127);//make the color transparent
		$x1 = ($paddingleftright/2);
		$y1 = 10;
		$x2 = $boxWidth + $paddingleftright/2;
		$y2 = $boxheight;
		$x2actual = 0;
		$x2avg = 0;
		$x2max = 0;
		imagefilledrectangle($im, 0, 0, $width, $height, $bgcolor);//background color outside box
		imagerectangle ( $im , $x1 , $y1 , $x2-1 ,$y2 , $black );//outer box
		imagefilledrectangle($im , $x1+1 , $y1+1 , $x2-2 ,$y2-1, $white);//background color inside box
		if($boxWidth == $actual)
			$x2actual = $x1 + $actual - 1.5 ;
		else
			$x2actual = $x1 + 1 +  $actual  ;
		if($avg == ($boxWidth + $paddingleftright/2) )
			$x2avg = $avg-1.5;
		else
			$x2avg = $avg;

		if($max == ($boxWidth + $paddingleftright/2))
			$x2max = $max-1.5;
		else
			$x2max = $max;
		imagefilledrectangle($im, ($x1+1), $y1+1,$x2actual , $y2-1, $green);//actual score
		//imagedashedline($im, $x2actual, $y1, $x2actual, $y2, $black);//actual line

		if($showAvg)
		{
		    imagedashedline($im, $x2avg-0.5, $y1, $x2avg-0.5, $y2, $blue);//avg line
		    imagedashedline($im, $x2avg+0.5, $y1, $x2avg+0.5, $y2, $blue);//avg line
		    imagestring($im,1,  $x2avg-2 ,$y2,$origavg,$black);//show number (average value)
		}

		if($showMax)
		{
		    imagedashedline($im, $x2max-0.5, $y1, $x2max-0.5, $y2, $red);//max line
		    imagedashedline($im, $x2max+0.5, $y1, $x2max+0.5, $y2, $red);//max line
		}

		if($x2avg-2 == $x2max-2 && $showAvg && $showMax)
		{
			imagestring($im,2,$x2avg-2-5,$y1-12,'A,M',$black);//avg string
		}else
		{
		    if($showAvg)
			    imagestring($im,2,$x2avg-2,$y1-12,'A',$black);//avg string
			if($showMax)
			    imagestring($im,2,$x2max-2,$y1-12,'M',$black);//max string
		}
		//show numbers
		/*for($i=0; $i<5 ; $i++)
		{
			if($i == 0){
				$shiftNo = 3;
				imagestring($im,1,  ($paddingleftright/2) + ($boxWidth/4)*$i - $shiftNo ,$y2,(25*($i)),$black);//show number 0
			}
			else if($i == 4)
			{
				$shiftNo = 10;
				imagestring($im,1,  ($paddingleftright/2) + ($boxWidth/4)*$i - $shiftNo ,$y2,(25*($i)),$black);//show number 100
			}
			if($i != 0 && $i != 4)
				imageline($im,($paddingleftright/2) + ($boxWidth/4)*$i,$y2-3,($paddingleftright/2) + ($boxWidth/4)*$i,$y2+3,$black);//show ruler
		}*/
        //imagestring($im,1,  ($paddingleftright/2)-2 ,$y2,"0",$black);//show number 0
        //imagestring($im,1,  ($paddingleftright/2)+ $boxWidth -4 ,$y2,$scale,$black);//show number (Scale)




		imagepng($im);//output a png image to file or browser
		imagedestroy($im);//frees the memory from the image
	}


?>