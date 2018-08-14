<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Default master view. Displays a HTML template with a header and footer.
 *
 * @package esoTalk
 */
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='<?php echo T("charset", "utf-8"); ?>'>
<title><?php echo sanitizeHTML($data["pageTitle"]); ?></title>
<?php echo $data["head"]; ?>
</head>

<body class='<?php echo $data["bodyClass"]; ?>'>
<?php $this->trigger("pageStart"); ?>

<div id='messages'>
<?php foreach ($data["messages"] as $message): ?>
<div class='messageWrapper'>
<div class='message <?php echo $message["className"]; ?>' data-id='<?php echo @$message["id"]; ?>'><?php echo $message["message"]; ?></div>
</div>
<?php endforeach; ?>
</div>

<div id='wrapper'>

<!-- HEADER -->
<div id='hdr'>
<div id="eiColors">
               <div id="orange" style = "width:25%; margin-left : 0px ; background-color:#2f99cb; margin-top:0px ; height :5px "></div>
               <div id="orang" style = "width:25%; margin-left : 25% ; background-color:#fbd212; margin-top:-5px ;height :5px"></div>
               <div id="orang1e" style = "width:25%; margin-left : 50% ; background-color:#e75903; margin-top:-5px; height :5px "></div>
               <div id="orange2" style = "width:25%; margin-left : 75% ; background-color:#9ec956; margin-top:-5px; height :5px "></div>
 </div>
<div id='hdr-content'>

 
<div id='hdr-inner'>

<?php if ($data["backButton"]): ?>
<a href='<?php echo $data["backButton"]["url"]; ?>' id='backButton' title='<?php echo T("Back to {$data["backButton"]["type"]}"); ?>'><i class="icon-chevron-left"></i></a>
<?php endif; ?>

<h1 id='forumTitle' data-intro=' <?php echo T("ForumHomeHelp"); ?>'><a href='<?php echo URL(""); ?>'><?php echo $data["forumTitle"]; ?></a></h1>

<ul id='mainMenu' class='menu'>
<?php if (!empty($data["mainMenuItems"])) echo $data["mainMenuItems"]; ?>
</ul>

<ul id='userMenu' class='menu'>
<?php echo $data["userMenuItems"]; ?>
<li><select id="chooseHelp" class="chooseHelp" onChange="actionHelp(this.value)" data-intro='<?php echo T("HelpHelp") ?>'>
				<option value="Help" style="display:none;">Help</option>
				<option value="FAQ">FAQ</option>
				<option value="Walkthrough">Walkthrough</option>
			</select>			
</li>
<li><a href='<?php echo URL("conversation/start"); ?>' class='link-newConversation button' data-intro='<?php echo T("NewConversationHelp"); ?>'><?php echo T("New Conversation"); ?></a></li>
</ul>

</div>
</div>
</div>

<!-- BODY -->
<div id='body'>
<div id='body-content'>
<?php echo $data["content"]; ?>
</div>
</div>

<!-- FOOTER -->
<div id='ftr'>
<div id='ftr-content'>
<ul class='menu'>
<li id='goToTop'><a href='#'><?php echo T("Go to top"); ?></a></li>
<?php echo $data["metaMenuItems"]; ?>
<?php if (!empty($data["statisticsMenuItems"])) echo $data["statisticsMenuItems"]; ?>
</ul>
</div>
</div>
<?php $this->trigger("pageEnd"); ?>

</div>

</body>
</html>
