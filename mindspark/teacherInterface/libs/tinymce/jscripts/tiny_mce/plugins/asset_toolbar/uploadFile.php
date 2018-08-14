<?php

//echo $_SERVER['DOCUMENT_ROOT']."<br>";
session_start();
//echo $_SESSION['img_path']."<br>";
$uploadPath = $_SESSION['img_path'];

//$uploadPath = $_SERVER['DOCUMENT_ROOT']."/insideei_issues/uploadedfiles/";

$error = "";
$content = "";
$filePath = "";
$permitArray = array('jpg','jpeg','gif','swf','png','pdf','xls','xlsx','doc','docx','ppt','pptx');
$imgExtArray = array('jpg','jpeg','gif','png');

if(isset($_FILES["file"]))
{
	if(is_uploaded_file($_FILES["file"]["tmp_name"]))
	{
		if ($_FILES["file"]["error"] > 0)
		{
			$error = "Return Code: " . $_FILES["file"]["error"] . "<br />";
		}
		else
		{
			$fileinfo = pathinfo($_FILES["file"]["name"]);
			$newFile = $fileinfo["filename"]."".date('U').".".$fileinfo["extension"];
			if(in_array(strtolower($fileinfo["extension"]),$permitArray))
			{
				move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath.$newFile) or exit;
				//$filePath = $uploadPath.$newFile;
				$filePath_arr = explode("/", $uploadPath);
				$ind = count($filePath_arr) - 2;
				$filePath = "uploadedfiles/".$filePath_arr[$ind]."/".$newFile;
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#example_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
    <script type="text/javascript">
<?php
	if($filePath != "")
	{
		if(in_array(strtolower($fileinfo["extension"]),$imgExtArray))
		{
			$showFilePath = $filePath;
			echo "tinyMCEPopup.editor.execCommand('mceInsertContent', false, '<img src=\"$showFilePath\" />');";
		}
		else
		{
			$showFilePath = $filePath;
			echo "tinyMCEPopup.editor.execCommand('mceInsertContent', false, '<a href=\"$showFilePath\">$newFile</a>');";
		}
		echo "tinyMCEPopup.close();";
	}
?>
	</script>
</head>
<body>
<form action="uploadFile.php" name="uploadForm" method="post" enctype="multipart/form-data">
	<span style="color:#F00"><?=$error?></span>
	<p><input name="file" id="file" type="file" /></p>
    <br /><br /><br />
    <div class="mceActionPanel">
		<input type="button" id="insert" name="insert" value="Upload" onclick="ExampleDialog.insert();" />
		<input type="button" id="cancel" name="cancel" value="Cancel" onclick="tinyMCEPopup.close();" />
	</div> 
</form>
</body>
</html>
