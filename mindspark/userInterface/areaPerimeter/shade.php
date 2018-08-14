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


	//1 lshape with 5 points
	//2 reverse l shape with 5 points
	//3 lshape with 4 points
	//4 reverse l shape with 4 points
	//5  irregular l shpae with 3 points
	//6  reverse irregular l shpae with 3 points
	//7
	//$first=6;

	if($first == 1)
	{
		$range=array('0-6','9-15','18-24','27-33','36-42','45-51','54-60');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$point[2]+1;
		$point[4]=$point[2]+2;
	}



	if($first == 2)
	{
		$range=array('2-8','11-17','20-26','29-35','38-44','47-53','56-62');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$point[2]-1;
		$point[4]=$point[2]-2;
	}

	if($first == 3)
	{
		$range=array('0-6','9-15','18-24','27-33','36-42','45-51');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$oneRandNo+27;
		$point[4]=$point[3]+1;
		$point[5]=$point[3]+2;
	}
	if($first == 4)
	{
		$range=array('2-8','11-17','20-26','29-35','38-44','47-53');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$oneRandNo+27;
		$point[4]=$point[3]-1;
		$point[5]=$point[3]-2;
	}

	if($first == 5)
	{
		$range=array('0-6','9-15','18-24','27-33','36-42','45-51');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$oneRandNo+27;
		$point[4]=$point[2]+1;
		$point[5]=$point[2]+2;
	}

	if($first == 6)
	{
		$range=array('2-8','11-17','20-26','29-35','38-44','47-53');
		$ar_key = array_rand($range);
		$ar_value=$range[$ar_key];
		$arr_range=explode("-",$ar_value);
		$oneRandNo=rand($arr_range[0],$arr_range[1]);
		$point[0]=$oneRandNo;
		$point[1]=$oneRandNo+9;
		$point[2]=$oneRandNo+18;
		$point[3]=$oneRandNo+27;
		$point[4]=$point[2]-1;
		$point[5]=$point[2]-2;
	}


	Header("Content-type: image/png");
	$Width=180;
	$Height=180;

	$img = ImageCreateTrueColor($Width, $Height);

	$bg = imagecolorallocate($img, 255, 255, 255);
	$black = imagecolorallocate($img, 0, 0, 0);

	imagefill($img, 0, 0, $bg);

	//$grid = imagecolorallocate($img, 225, 245, 249);
	$grid = imagecolorallocate($img, 175, 175, 175);

	imagesetstyle($img, array($bg, $grid));
	imagegrid($img, $Width, $Height, 20, IMG_COLOR_STYLED);
	//makegrid($img, $Width, $Height, 10, $grid);


	for($i=0;$i < count($point); $i++)
	{
		fillOneBox($img,$point[$i]);
	}

	$n=0;

	for($i=0; $i<9; $i++)
	{

		for($j=0; $j<9; $j++)
		{
			$x=20*$j;
			$y=20*$i;
			//$no=$j;
			//$n=$n+$j;

			//imagesetpixel($img, round($x),round($y), $black);
			//imagestring($img, 2, $x,$y,$n++ , $black);

		}

	}

	ImagePNG($img);
	ImageDestroy($img);

	function imagegrid($image, $w, $h, $s, $color)
	{
	    for($iw=1; $iw<$w/$s; $iw++){imageline($image, $iw*$s, 0, $iw*$s, $w, $color); }
	    for($ih=1; $ih<$h/$s; $ih++){imageline($image, 0, $ih*$s, $w, $ih*$s, $color);  }
	}



	function fillOneBox($img,$boxNo)
	{

		$lineNo=(int)($boxNo/9);

		$x1=20*(($boxNo%9));
		$y1=20*$lineNo;
		$x2=$x1+20;
		$y2=$y1+20;
		imagefilledrectangle($img,$x1,$y1,$x2,$y2,$black);
	}
?>


