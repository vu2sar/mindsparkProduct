<?php 
require('config.php'); 
	
function get_dirs(){
	$foldersDir = dir(IMG_PATH);
	$ignore = array( '.', '..' ); 
	while(false !== ($entry = $foldersDir->read())){
		if(!in_array($entry, $ignore)){
			$length = 18;
			$dirName = (strlen($entry) > $length) ? substr($entry, 0, $length).'..': $entry;
			if(is_dir(IMG_PATH.$entry)){
				$folders .= "<li><a rel=\"open_folder\" href=\"#".$dirName."\">".$dirName."</a></li>";
			}
		}
	}
	$foldersDir->close();
	return $folders;
}

function get_images(){
	if(isset($_REQUEST['folder'])){
		
		$request = explode("#",$_REQUEST['folder']);
		$request_folder = $request[1];
		
		if ($handle = opendir(IMG_PATH_TEMP.$request_folder.THUMB_DIR)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$images .= '<a href="#'.IMG_PATH_LIVE.$request_folder.'/'.$file.'" rel="image_src">';
					$images .= '<img class="thumb_view" src="'.IMG_PATH_TEMP.$request_folder.THUMB_DIR.$file.'" />';
					$images .= '</a>';
				}
			}
			closedir($handle);
		}
	}
	return $images;
}
?>

<table style="width:100%">
	<tr>
		<td style="width:140px;vertical-align:top">
			<ul id="folder_list">
				<?php echo get_dirs(); ?>
			</ul>
		</td>
		<td style="vertical-align:top">
			<div id="library_cont">
				<div class="info">Mappe: "<?php echo $_REQUEST['folder']; ?>"</div>
					<?php echo get_images(); ?>
			<div style="clear:both"></div>
			</div>
		</td>
	</tr>
</table>
