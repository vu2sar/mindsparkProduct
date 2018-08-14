<?php include("header.php") ?>

<title>Add Comment</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/addComment.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Add a Comment</span>
			</div>
			<div id="containerBody">
				<table  cellpadding="5">
		
		<tr>
		
		
		<td> 
		<table>
		<tr>
		<td><label for="teacherID">Comment ID</label>  </td>
		<td ><label style="margin-left: 280px;" for="type">Type</label></td> 
		</tr>
		</table>
		
		</td>
		
		
		
		</tr>
		<tr>
			<td>
			<table>
			<tr>
			<td><input type="textbox" name="teacherID" id="teacherID" disabled " value=""/></td>
			<td >
				<select name="type" id="type" style="margin-left: 200px;">
					<option value="Suggestion" selected="Suggestion">Suggestion</option>
					<option value="Doubt" >Doubt</option>
					<option value="Complaint" >Complaint</option>
					<option value="Other">Other</option>
				</select>
			</td>
			</tr>	
			</table>
			
			</td>
			
			
		</tr>
		
	
		<tr> <td><label for="mailTo">Mail To</label></td> </tr>
	    <tr>
		    
		    <td>
		        <input type="text" id="mailTo" name="mailTo" value="<?=$mailTo?>" size="75" disabled>
		    </td>
		</tr>
		<tr> <td><label for="mailTo">Cc</label></td> </tr>
		<tr>
		   
		    <td>
		        <input type="text" id="ccList" name="ccList" value="<?=$ccList?>" size="75">
		    </td>
		</tr>


		<tr> <td><label for="comment">Comment</label></td> </tr>
		<tr>
			
			<td><textarea id="comment" name="comment" rows="10" cols="58"></textarea></td>
		</tr>
		
		<tr>  <td><label>Attach File</label></td> </tr>
		<tr>
		   
		    <td align="left">
		        <label>File 1:</label> <input type="file" name="uploaded_file1" id="upload_file1"><br>
		        <label>File 2:</label> <input type="file" name="uploaded_file2" id="upload_file2"><br>
		        <label>File 3:</label> <input type="file" name="uploaded_file3" id="upload_file3">
		    </td>
		</tr>
		
		</table>
			
				<input type='submit' name="submit" id= "button1" value='Submit'  class="buttons">
				<input type="button" id="button2"  value="Cancel" class="buttons">
			
	
			<div style="margin-top: 3%">
			Note: The size of the attachment should be less than 1 MB. Allowed file types: jpg, jpeg, bmp, png, gif
			</div>	
			</div>
		</div>
		
		
	</div>

<?php include("footer.php") ?>