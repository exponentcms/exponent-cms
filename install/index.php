<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

//define('SCRIPT_EXP_RELATIVE','install/');
//define('SCRIPT_FILENAME','index.php');

// Jumpstart to Initialize the installer language before it's set to default
if (isset($_REQUEST['lang'])) {
    $_POST['sc']['LANGUAGE'] = trim($_REQUEST['lang'],"'");
}
if (isset($_POST['sc']['LANGUAGE'])) {
	if (!defined('LANGUAGE')) {
        define('LANGUAGE', $_POST['sc']['LANGUAGE']);
    }
}

include_once('../exponent.php');

//if (!file_exists('not_configured') && file_exists(BASE.'framework/conf/config.php')) {
//	flash('notice',gt('This Exponent Site has already been configured.'));
//	header('Location: ../index.php');
//	exit(gt('This Exponent Site has already been configured.'));
//}

if (isset($_POST['sc'])) {
    if (file_exists("../framework/conf/config.php")) {
        // Update the config
        foreach ($_POST['sc'] as $key => $value) {
            expSettings::change($key, $value);
        }
    } else {
        // Initialize /framework/conf/config
    	$values = array(
    		'c'=>$_POST['sc'],
    		'opts'=>array(),
    		'configname'=>'Default',
    		'activate'=>1
    	);
    	expSettings::saveConfiguration($values);
    }
}

if (isset($_POST['install_sample'])) {
    // we still use $_POST['install_sample'] from install-5.php, even though the system samples are in a static location
    $eql = BASE . "themes/".DISPLAY_THEME_REAL . "/" . $_POST['install_sample'] . ".eql";
    if (!file_exists($eql)) $eql = BASE . "install/samples/" . $_POST['install_sample'] . ".eql";
	if (file_exists($eql)) {
		$errors = array();
		expFile::restoreDatabase($db,$eql,$errors);
        $files = BASE . "themes/" . DISPLAY_THEME_REAL . "/" .  $_POST['install_sample'] . ".tar.gz";
        if (!file_exists($files)) $files = BASE . "install/samples/" . $_POST['install_sample'] . ".tar.gz";
		if (file_exists($files)) {  // only install if there was an eql file
			include_once(BASE.'external/Tar.php');
			$tar = new Archive_Tar($files);
			$return = $tar->extract(BASE);
		}
	}
    if (DEVELOPMENT && !empty($errors)) {
        echo '<h2>'.gt('Errors were encountered populating the site database.').'</h2><ul>';
        foreach ($errors as $e) echo '<li>'.$e.'</li>';
        echo '</ul>';
    } else {
//        echo gt('Sample content was added to your database.  This content should help you learn how Exponent works, and how to use it for your website.');
    }
}

if (file_exists("../framework/conf/config.php") && !isset($_REQUEST['page'])) {
	$_REQUEST['page'] = 'upgrade-1';
}
		
if (!isset($_REQUEST['page']) && !file_exists("../framework/conf/config.php")) {
    $_REQUEST['page'] = '';
}
$page = $_REQUEST['page'];

$page_image = '';
$page_text = '';

switch ($page) {
	case 'upgrade-1':
	    $masthead = gt("Upgrade");
		$page_text = gt("It appears you've upgraded your Exponent code.").'<br /><br />'.gt("Before you begin the upgrade you should").' <a href="javascript:void(0)" onclick="return pop(\'changes\');">'.gt('read about the changes!').'</a> ';
		break;
	case 'upgrade-2':
	    $masthead = gt("Upgrade");
		$page_text = gt("Installing Tables adds any new fields to existing tables, and adds any additional tables Exponent needs to be awesome.");
		break;
	case 'upgrade-3':
	    $masthead = gt("Upgrade");
		$page_text = gt("We'll now run any upgrade scripts needed for this version of Exponent.");
		break;
	case 'install-1':
        $masthead = gt("New Installation");
		$page_text = gt('Exponent requires that several file permissions be set correctly in order to operate.').' '.
            gt('Sanity checks are being run right now to ensure that the web server directory you wish to install Exponent in, is suitable.').'<br><br>'.
	        gt('If something fails, please').' <a href="javascript:void(0)" onclick="return pop(\'sanity\');">'.gt('read about each sanity check').'</a> '.
            gt('for an explanation of what exactly the installer is checking for, and how to fix it.');
		break;
	case 'install-2':
        $masthead = gt("New Installation");
		$page_text = gt('Exponent requires a database to store and manage content.').' '.
			gt('Simply create a database using your database tool of of choice, and fill in the information on this page.');
		break;
	case 'install-3':
        $masthead = gt("New Installation");
		$page_text = gt('Exponent is now checking to make sure that the database configuration information you provided is valid.');
		break;
	case 'install-4':
        $masthead = gt("New Installation");
		$page_text = gt('Please enter some basic information for your site.');
		break;
	case 'install-5':
        $masthead = gt("New Installation");
		$page_text = gt('Your theme is your site\'s look and feel. Select what you\'d like you site to look like from the list of themes.');
		break;
	case 'install-6':
        $masthead = gt("New Installation");
		$page_text = gt('The user you\'re about to create will be the').' <strong>'.gt('Super Administrator').'</strong> '.gt('for the entire system.').' '.
			gt('This level of administration has un-restricted access and abilities throughout the entire website.');
		break;
	case 'install-7':
        $masthead = gt("New Installation");
		$page_text = gt('The user you\'re about to create will be the').' <strong>'.gt('Super Administrator').'</strong> '.gt('for the entire system.').' '.
			gt('This level of administration has un-restricted access and abilities throughout the entire website.');
		break;
	case 'final':
        $masthead = (isset($_REQUEST['upgrade']))?gt("Upgrade"):gt("New Installation");
        $page_text = (isset($_REQUEST['upgrade']))?gt("Your upgrade is complete!"):gt("Your installation is complete!");
		break;
	default:
        $masthead = gt("New Installation");
		$page = 'welcome';
		$page_text = gt('This installation wizard will guide you step by step through the configuration and setup of your new Exponent-powered website.');
		break;
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo gt('Exponent Install Wizard'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo LANG_CHARSET; ?>" />
    <meta name="Generator" content="Exponent Content Management System - <?php echo expVersion::getVersion(true); ?>" />
	<link rel="stylesheet" href="<?php echo YUI3_RELATIVE; ?>cssreset/reset.css" />
	<link rel="stylesheet" href="<?php echo YUI3_RELATIVE; ?>cssfonts/fonts.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/forms.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/button.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/tables.css" />
	<link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/common.css" />
	<link rel="stylesheet" href="style.css" />

	<script type="text/javascript" src="<?php echo YUI3_RELATIVE; ?>/yui/yui-min.js"></script>
    
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
    		} else {
                echo gt('OOPS! Couldn\'t find the').' <strong>'.$page.'</strong> '.gt('page').'!';
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
