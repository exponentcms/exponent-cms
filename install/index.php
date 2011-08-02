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

//define('DEVELOPMENT',1);

define('SCRIPT_EXP_RELATIVE','install/');
define('SCRIPT_FILENAME','index.php');
include_once('../exponent.php');

if (!file_exists('not_configured') && file_exists(BASE.'conf/config.php')) {
	header('Location: ../index.php');
	exit('This Exponent Site has already been configured.');
}

// Load i18n values
$i18n = exponent_lang_loadFile('install/index.php');

//if (!defined('SYS_CONFIG')) include_once(BASE . 'framework/core/subsystems-1/config.php');
include_once(BASE . 'framework/core/subsystems-1/config.php');

// Initialize the language
if (isset($_POST['sc'])) {

    if (file_exists("../conf/config.php")) {
        // Update the config
        $config = $_POST['sc'];
        foreach ($config as $key => $value) {
            exponent_config_change($key, stripslashes($value));
        }
    } else {
        // Update init the config
        $config = $_POST['sc'];
    	$values = array(
    		'c'=>$config,
    		'opts'=>array(),
    		'configname'=>'Default',
    		'activate'=>1
    	);
    	exponent_config_saveConfiguration($values);
    }
}

if (file_exists("../conf/config.php") && !isset($_REQUEST['page'])) {
	$_REQUEST['page'] = 'upgrade-1';
}
		
if (!isset($_REQUEST['page']) && !file_exists("../conf/config.php")) {
    $_REQUEST['page'] = '';
}
$page = $_REQUEST['page'];

$page_image = '';
$page_text = '';

switch ($page) {
	case 'upgrade-1':
	    $masthead = "Upgrade";
		$page_text = gt("It appears you've upgraded your Exponent code.");
		break;
	case 'upgrade-2':
	    $masthead = "Upgrade";
		$page_text = gt("Installing Tables adds any new fields to existing tables, and adds any additional tables Exponent needs to be awesome.");
		break;
	case 'upgrade-3':
	    $masthead = "Upgrade";
		$page_text = gt("We'll now run any upgrade scripts needed for this version of Exponent.");
		break;
	case 'setlang':
		$page_image = 'setlang';
		$page_text = $i18n['setlang'];
		break;
	case 'install-1':
        $masthead = "New Installation";
		$page_text = gt('
        Exponent requires that several file permissions be set correctly in order to operate.
        Sanity checks are being run right now to ensure that the web server directory you wish to install Exponent in, is suitable.
        <br><br>
        If something fails, please 
        <a href="javascript:void(0)" onclick="return pop(\'sanity\');">read about each sanity check</a>
        for an explanation of what exactly the installer is checking for, and how to fix it.        		
		');
		break;
	case 'install-2':
        $masthead = "New Installation";
		$page_text = gt('
        Exponent requires a database to store and manage content. Simply create a database using your database tool of of choice, and fill in the information on this page.
		');
		break;
	case 'install-3':
        $masthead = "New Installation";
		$page_text = gt('
            Exponent is now checking to make sure that the database configuration information you provided is valid.
 		');
		break;
	case 'install-4':
        $masthead = "New Installation";
		$page_text = gt('
            Please enter some basic information for your site.
 		');
		break;
	case 'install-5':
        $masthead = "New Installation";
		$page_text = gt('
            Your theme is your site\'s look and feel. Select what you\'d like you site to look like from the list of themes.
 		');
		break;
	case 'install-6':
        $masthead = "New Installation";
		$page_text = gt('
            The user you\'re about to create will be the <b>Super Administrator</b> for the entire system. This level of administration has un-restricted access and abilities throughout the entire website.
 		');
		break;
	case 'install-7':
        $masthead = "New Installation";
		$page_text = gt('
            The user you\'re about to create will be the <b>Super Administrator</b> for the entire system. This level of administration has un-restricted access and abilities throughout the entire website.
 		');
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
        $masthead = (isset($_REQUEST['upgrade']))?"Upgrade":"New Installation";
        $page_text = (isset($_REQUEST['upgrade']))?gt("Your upgrade is complete!"):gt("Your installation is complete!");
		break;
	default:
        $masthead = "New Installation";
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
	<link rel="stylesheet" href="<?php echo YUI3_PATH; ?>cssfonts/fonts.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/forms.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/button.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/tables.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/common.css" />
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
