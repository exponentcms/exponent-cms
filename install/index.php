<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

/**
 * Minimum PHP version check
 */
if (version_compare(PHP_VERSION, '5.3.1', 'lt')) {
    echo "<h1 style='padding:10px;border:5px solid #992222;color:red;background:white;position:absolute;top:100px;left:300px;width:400px;z-index:999'>
        PHP 5.3.1+ is required!  Please refer to the Exponent documentation for details:<br />
        <a href=\"http://docs.exponentcms.org/docs/current/requirements-running-exponent-cms\" target=\"_blank\">http://docs.exponentcms.org/</a>
        </h1>";
    die();
}

ob_start();


// Jumpstart to Initialize the installer language before it's set to default
if (isset($_REQUEST['lang'])) {
    $_REQUEST['sc']['LANGUAGE'] = trim($_REQUEST['lang'], "'");
}
if (isset($_REQUEST['sc']['LANGUAGE'])) {
    if (!defined('LANGUAGE')) {
        define('LANGUAGE', $_REQUEST['sc']['LANGUAGE']);
    }
}

include_once('../exponent.php');
expString::sanitize($_REQUEST);

// Switch to a saved profile as requested
if (isset($_REQUEST['profile'])) {
    expSettings::activateProfile($_REQUEST['profile']);
    expTheme::removeSmartyCache(); //FIXME is this still necessary?
    expSession::clearAllUsersSessionCache();
    flash('message', gt("New Configuration Profile Loaded"));
    header('Location: ../index.php');
}

// Create or update the config settings
if (isset($_REQUEST['sc'])) {
    if (file_exists("../framework/conf/config.php")) {
        // Update the config
        foreach ($_REQUEST['sc'] as $key => $value) {
//            $value = expString::sanitize($value);
            expSettings::change($key, $value);
        }
    } else {
        // Initialize /framework/conf/config
        $values = array(
//            'c'          => expString::sanitize($_REQUEST['sc']),
            'c'          => $_REQUEST['sc'],
            'opts'       => array(),
            'configname' => 'Default',
            'activate'   => 1
        );
        expSettings::saveConfiguration($values);
    }
}

// Install a sample database as requested
if (isset($_REQUEST['install_sample'])) {
    $eql = BASE . "themes/" . DISPLAY_THEME_REAL . "/" . $_REQUEST['install_sample'] . ".eql";
    if (!file_exists($eql)) {
        $eql = BASE . "install/samples/" . $_REQUEST['install_sample'] . ".eql";
    }
    if (file_exists($eql)) {
        $errors = array();
        expFile::restoreDatabase($eql, $errors);
        $files = BASE . "themes/" . DISPLAY_THEME_REAL . "/" . $_REQUEST['install_sample'] . ".tar.gz";
        if (!file_exists($files)) {
            $files = BASE . "install/samples/" . $_REQUEST['install_sample'] . ".tar.gz";
        }
        if (file_exists($files)) { // only install if there was an archive
            include_once(BASE . 'external/Tar.php');
            $tar = new Archive_Tar($files);
            $return = $tar->extract(BASE);
        }
    }
    //FIXME we need to output this into an element and not simply out on the page
    if (DEVELOPMENT && !empty($errors)) {
        echo '<h2>' . gt('Errors were encountered populating the site database.') . '</h2><ul>';
        foreach ($errors as $e) {
            echo '<li>' . $e . '</li>';
        }
        echo '</ul>';
    } else {
//        echo gt('Sample content was added to your database.  This content should help you learn how Exponent works, and how to use it for your website.');
    }
}

// Make sure our 'page' is set correctly
if (file_exists("../framework/conf/config.php") && !isset($_REQUEST['page'])) {
    $_REQUEST['page'] = 'upgrade-1';
}
if (!file_exists("../framework/conf/config.php") && !isset($_REQUEST['page'])) {
    $_REQUEST['page'] = 'welcome';
}
$page = $_REQUEST['page'];

// Superadmin must be logged in to do an upgrade
if (strpos($page, 'upgrade-') !== false && empty($user->is_admin)) {
    header('Location: ../index.php');
    exit();
}

// Only run installation if not already installed
if (strpos($page, 'upgrade-') === false && !file_exists(BASE . 'install/not_configured')) {
    header('Location: ../index.php');
    exit();
}

switch ($page) {
    case 'upgrade-1':
        $masthead = gt("Upgrade");
        $page_text = gt("It appears you've upgraded your Exponent code.") . '<br /><br />' . gt(
                "Before you begin the upgrade you should"
            ) . ' <a href="javascript:void(0)" onclick="return pop(\'changes\');">' . gt(
                'read about the changes!'
            ) . '</a> ';
        break;
    case 'upgrade-2':
        $masthead = gt("Upgrade");
        $page_text = gt('Exponent requires that several file permissions be set correctly in order to operate.') . ' ' .
            gt(
                'Sanity checks are being run right now to ensure that the web server directory you wish to upgrade Exponent in, is suitable.'
            ) . '<br><br>' .
            gt('If something fails, please') . ' <a href="javascript:void(0)" onclick="return pop(\'sanity\');">' . gt(
                'read about each sanity check'
            ) . '</a> ' .
            gt('for an explanation of what exactly the upgrade is checking for, and how to fix it.');
        break;
    case 'upgrade-3':
        $masthead = gt("Upgrade");
        $page_text = gt(
            "Installing Tables adds any new fields to existing tables, and adds any additional tables Exponent needs to be awesome."
        );
        break;
    case 'upgrade-4':
        $masthead = gt("Upgrade");
        $page_text = gt("We'll now run any upgrade scripts needed for this version of Exponent.");
        break;
    case 'install-1':
        $masthead = gt("New Installation");
        $page_text = gt('Exponent requires that several file permissions be set correctly in order to operate.') . ' ' .
            gt(
                'Sanity checks are being run right now to ensure that the web server directory you wish to install Exponent in, is suitable.'
            ) . '<br><br>' .
            gt('If something fails, please') . ' <a href="javascript:void(0)" onclick="return pop(\'sanity\');">' . gt(
                'read about each sanity check'
            ) . '</a> ' .
            gt('for an explanation of what exactly the installer is checking for, and how to fix it.');
        break;
    case 'install-2':
        $masthead = gt("New Installation");
        $page_text = gt('Exponent requires a database to store and manage content.') . ' ' .
            gt(
                'Simply create a database using your database tool of of choice, and fill in the information on this page.'
            );
        break;
    case 'install-3':
        $masthead = gt("New Installation");
        $page_text = gt(
            'Exponent is now checking to make sure that the database configuration information you provided is valid.'
        );
        break;
    case 'install-4':
        $masthead = gt("New Installation");
        $page_text = gt('Please enter some basic information for your site.');
        break;
    case 'install-5':
        $masthead = gt("New Installation");
        $page_text = gt(
            'Your theme is your site\'s look and feel. Select what you\'d like your site to look like from the list of themes. You may also give your site some sample content.'
        );
        break;
    case 'install-6':
        $masthead = gt("New Installation");
        $page_text = gt('The user you\'re about to create will be the') . ' <strong>' . gt(
                'Super Administrator'
            ) . '</strong> ' . gt('for the entire system.') . ' ' .
            gt('This level of administration has un-restricted access and abilities throughout the entire website.');
        break;
    case 'install-7':
        $masthead = gt("New Installation");
        $page_text = gt('The user you\'re about to create will be the') . ' <strong>' . gt(
                'Super Administrator'
            ) . '</strong> ' . gt('for the entire system.') . ' ' .
            gt('This level of administration has un-restricted access and abilities throughout the entire website.');
        break;
    case 'final':
        $masthead = (isset($_REQUEST['upgrade'])) ? gt("Upgrade") : gt("New Installation");
        $page_text = (isset($_REQUEST['upgrade'])) ? gt("Your upgrade is complete!") : gt(
            "Your installation is complete!"
        );
        break;
    default:
        $page = 'welcome';
        $masthead = gt("New Installation");
        $page_text = gt(
            'This installation wizard will guide you step by step through the configuration and setup of your new Exponent-powered website.'
        );
        break;
}

?>
    <!DOCTYPE HTML>
    <html>
    <head>
        <title><?php echo gt('Exponent Install Wizard'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo LANG_CHARSET; ?>"/>
        <meta name="Generator"
              content="Exponent Content Management System - <?php echo expVersion::getVersion(true); ?>"/>
        <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>external/normalize/normalize.css"/>
        <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/forms.css"/>
        <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/button.css"/>
        <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/tables.css"/>
        <link rel="stylesheet" href="<?php echo PATH_RELATIVE; ?>framework/core/assets/css/common.css"/>
        <link rel="stylesheet" href="style.css"/>
        <!--	<script type="text/javascript" src="--><?php //echo YUI3_RELATIVE; ?><!--/yui/yui-min.js"></script>-->
        <script type="text/javascript">
            function pop(page) {
                var url = "popup.php?page=" + page;
                window.open(url , "pop" , "height=400,width=600,title=no,titlebar=no,scrollbars=yes");
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
                if (file_exists('pages/' . $page . '.php')) {
                    include('pages/' . $page . '.php');
                } else {
                    echo gt('OOPS! Couldn\'t find the') . ' <strong>' . $page . '</strong> ' . gt('page') . '!';
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