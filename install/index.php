<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
# Written and Designed by James Hunt
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

ob_start();

// If we do not have a not_configured file, the user has already gone through the installer once.
// Pop them back to the main page.
if (!file_exists('not_configured')) {
	header('Location: ../index.php');
	exit('This Exponent Site has already been configured.');
}

// Initialize the Database engine so that the correct backend gets initialized.
if (isset($_POST['c'])) {
	define('DB_BACKEND',$_POST['c']['db_engine']);
}

// Initialize the language
if (isset($_POST['lang'])) {
	define('LANG', $_POST['lang']);
}

define('SCRIPT_EXP_RELATIVE','install/');
define('SCRIPT_FILENAME','index.php');
include_once('../exponent.php');

// Load i18n values
$i18n = exponent_lang_loadFile('install/index.php');

// Initialize the language
if (isset($_POST['lang'])) {
	//prepare value array for exponent_config_saveConfiguration
	$values = array("c");
	$values["c"]["LANG"] = LANG;
	include_once(BASE . "/subsystems/config.php");
	exponent_config_saveConfiguration($values);
}
		
if (!isset($_REQUEST['page'])) {
	$_REQUEST['page'] = 'setlang';
}
$page = $_REQUEST['page'];

$page_image = '';
$page_text = '';
switch ($page) {
	case 'setlang':
		$page_image = 'setlang';
		$page_text = $i18n['setlang'];
		break;
	case 'sanity':
		$page_image = 'sanity';
		$page_text = $i18n['sanity'];
		break;
	case 'dbconfig':
		$page_image = 'database';
		$page_text = $i18n['dbconfig'];
		break;
	case 'dbcheck':
		$page_image = 'database';
		$page_text = $i18n['dbcheck'];
		break;
	case 'admin_user':
		$page_image = 'account';
		$page_text = $i18n['admin_user'];
		break;
	case 'upgrade_version':
		$page_image = 'system';
		$page_text = $i18n['upgrade_version'];
		break;
	case 'upgrade':
		$page_image = 'system';
		$page_text = $i18n['upgrade'];
		break;
	default:
		$page_image = '';
		break;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?php echo $i18n['page_title']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo LANG_CHARSET; ?>" />
	<link rel="stylesheet" href="style.css" />
	<script type="text/javascript">
	
	function pop(page) {
		url = "popup.php?page="+page;
		window.open(url,"pop","height=400,width=600,title=no,titlebar=no,scrollbars=yes");
		return false;
	}
	
	</script>
	<style type="text/css">
		div#main2 {
			background-image: url(images/mainbar_03.png);
			background-repeat: repeat-y;
			<?php if ($page_image != '') { ?>
			padding-left: 95px;
			<?php } else { ?>
			padding-left: 15px;
			<?php } ?>
			padding-right: 15px;
		}
		
		div#sidebar {
			padding-top: 70px;
			background-image: url(images/<?php echo $page_image; ?>.png);
			background-repeat: no-repeat;
		}
	</style>
</head>
<body>
	<div id="installer">
		<?php if ($page_image != '') { ?>
		<div id="side">
			<div id="side1"><!-- Empty div for background-images on CSS-capable browsers --></div>
			<div id="side2">
				<div id="sidebar" class="bodytext">
					<?php echo $page_text; ?>
				</div>
			</div>
			<div id="side3"><!-- Empty div for background-images on CSS-capable browsers --></div>
		</div>
		<?php } ?>
		<div id="main">
			<div id="main1"><!-- Empty div for background-images on CSS-capable browsers --></div>
			<div id="main2" class="bodytext">
				<h1 id="maintitle"><span class="noncss"><?php echo $i18n['installer_title']; ?></span></h1>
				<?php
				if (file_exists('pages/'.$page.'.php')) {
					include('pages/'.$page.'.php');
				} else {
					echo sprintf($i18n['unknown_page'],strip_tags($page));
				}
				?>
				<br />
			</div>
			<div id="main3"><!-- Empty div for background-images on CSS-capable browsers --></div>
		</div>
	</div>
</body>
</html>

<?php
ob_end_flush();
?>
