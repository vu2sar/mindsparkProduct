<?php include("header.php");
	$curDate = new DateTime("now");
 ?>

<title>Welcome</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/welcome.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
	}
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
	<?php include("eiColors.php") ?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php") ?>
	</div>
	<div id="topBar">
		<?php include("topBar.php") ?>
	</div>
	<div id="sideBar">
			<?php include("sideBar.php") ?>
	</div>

	<div id="container">
            <?php include('referAFriendIcon.php') ?>
		<table id="childDetails">
				<td width="33%" id="sectionRemediation" class="pointer"><div class="smallCircle red"></div><label class="textRed pointer" value="secRemediation">WELCOME PARENT!</label></td>
		</table>
		<table class="studentDetails" align="left">
			<?php if(count($childID)==1){ ?>
				<tr>
					<td align="left">Congratulations! get your child started on Mindspark Now!</td>
				</tr>
				
			<?php }else{ 
			for($i=0; $i<count($childID); $i++)	{ 
				$child[$i] = new user($childID[$i]);
				$value= convert_number_to_words($i+1);
			?>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th style="text-transform:capitalize"><?=$value?> child details</th>
					</tr>
					<tr>
						<th>Mindspark Username</th>
						<td>
							: <?=$child[$i]->username?>
						</td>
					</tr>
					
			<?php } } ?>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
						<td class="createButton" style="width:180px"><a href="welcomeParent.php">Add another child</a></td>
				</tr>
				<tr>
						<td>&nbsp;</td>
					</tr>
				
			
			</table>
		
	</div>

<?php 
function convert_number_to_words($number) {
    
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'first',
        2                   => 'second',
        3                   => 'third',
        4                   => 'fourth',
        5                   => 'fifth',
        6                   => 'sixth',
        7                   => 'seventh',
        8                   => 'eighth',
        9                   => 'nineth',
        10                  => 'tenth',
        11                  => 'eleventh',
        12                  => 'twelveth',
        13                  => 'thirteenth',
        14                  => 'fourteenth',
        15                  => 'fifteenth',
        16                  => 'sixteenth',
        17                  => 'seventeenth',
        18                  => 'eighteenth',
        19                  => 'nineteenth',
        20                  => 'twentieth'
    );
    
    if (!is_numeric($number)) {
        return false;
    }
    
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
    
    $string = $fraction = null;
    
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
    
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
            break;
        default:
            $string = $number."th";
            break;
    }
    
    return $string;
}
include("footer.php"); ?>