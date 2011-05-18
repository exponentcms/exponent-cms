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

if (file_exists("../conf/config.php") && !isset($_REQUEST['page'])) {
	$_REQUEST['page'] = 'upgrade-1';
}
		
if (!isset($_REQUEST['page']) && file_exists("../conf/config.php")) {
	$_REQUEST['page'] = 'setlang';
}
$page = $_REQUEST['page'];

$page_image = '';
$page_text = '';

switch ($page) {
	case 'upgrade-1':
	    $masthead = "Upgrade";
		$page_text = gt("You've upgraded your Exponent code.");
		break;
	case 'upgrade-2':
	    $masthead = "Upgrade";
		$page_text = gt("Installing Tables add any new fields to existing tables, and adds any additional tables Exponent needs to be awesome.");
		break;
	case 'upgrade-3':
	    $masthead = "Upgrade";
		$page_text = gt("We'll now run any upgrade scripts for this versio of Exponent.");
		break;
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
	case 'final':
        $masthead = (isset($_REQUEST['upgrade']))?"Upgrade":"Install";
    	$page_text = gt("Your upgrade is complete!");
		break;
	default:
		$page_image = 'welcome';
		$page = 'welcome';
		$page_text = $i18n['guide'];
		break;
}

?>
<!DOCTYPE>
<html>
<head>
	<title><?php echo $i18n['page_title']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo LANG_CHARSET; ?>" />
	<link rel="stylesheet" href="<?php echo YUI3_PATH; ?>cssreset/reset.css" />
	<link rel="stylesheet" href="<?php echo YUI3_PATH; ?>cssbase/base.css" />
	<link rel="stylesheet" href="<?php echo YUI3_PATH; ?>cssfonts/fonts.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/forms.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/button.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/tables.css" />
	<link rel="stylesheet" href="style.css" />

	<script type="text/javascript" src="<?php echo YUI3_PATH; ?>/yui/yui-min.js"></script>
    
	<script type="text/javascript">	
	function pop(page) {
    		var url = "popup.php?page="+page;
    		window.open(url,"pop","height=400,width=600,title=no,titlebar=no,scrollbars=yes");
	}
	
	</script>
</head>
<body>
	<div id="installer">
	    <div id="hd">
	       <h1 id="logo">
	           <a href="http://www.exponentcms.org/" target="_blank">
                   Exponent CMS
               </a>
	       </h1>
	       <strong><?php echo $masthead ?></strong>
	    </div>
		<div id="bd">
		    <div id="leftcol">
    		<?php
    		if (file_exists('pages/'.$page.'.php')) {
    			include('pages/'.$page.'.php');
    		}
    		?>
    		</div>
            <div id="rightcol">
                <p>
        		<?php echo $page_text; ?>
        		</p>
            </div>
		</div>
	</div>
</body>
</html>

<?php
ob_end_flush();
?>
