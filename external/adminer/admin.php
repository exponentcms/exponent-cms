<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
require_once('../../exponent.php');

function adminer_object() {
    // required to run any plugin
    include_once "./plugins/plugin.php";
    
    // autoloader
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }
    
    $plugins = array(
        // specify enabled plugins here
//        new AdminerDumpAlter,
//        new AdminerDumpBz2,  // adds bz2 option to export
//        new AdminerDumpDate,
        new AdminerDumpZip,  // adds zip option to export
        new AdminerEditCalendar,  // add calendar popup for date/time fileds
        new AdminerEnumOption,  // turns enum fields into select input
        new AdminerTablesFilter,  // adds filter input to tables list
        new AdminerEditTextSerializedarea,  // displays unserialized data as a tooltip
        //new AdminerEmailTable,
        //new AdminerEditForeign,
        //new AdminerForeignSystem,
        new AdminerVersionNoverify,  // disable adminer version check/notifiy
    );
    if (SITE_WYSIWYG_EDITOR == 'tinymce') {
        $plugins[] = new AdminerTinymce;  // inserts wysiwyg editor for 'body' fields
    } else {
        $plugins[] = new AdminerCKeditor;  // inserts wysiwyg editor for 'body' fields
    }
    $plugins[] = new AdminerEditTextarea;  // adjusts box size smaller, MUST be last in chain for textarea widgets

    /* It is possible to combine customization and plugins: */
    class AdminerCustomization extends AdminerPlugin { 
		function name() { // custom name in title and heading 
			return gt('Exponent CMS Database');
		} 
		function permanentLogin() { // key used for permanent login 
			return ""; 
		} 
		function credentials() { // server, username and password for connecting to database 
			return array(DB_HOST, DB_USER , DB_PASS);
		}
		function database() { // database name, will be escaped by Adminer 
			return DB_NAME;
		}
		function login($login, $password) { // validate user submitted credentials
            global $user;

            if (empty($user->id)) {
                return false;
            } else {
                return ($user->isLoggedIn() && $user->isSuperAdmin());
            }
		}
		function databases($flush = true) {
			return array(DB_NAME);
		}
        function loginForm() {
       		?>
        <h3><?php echo gt('You must already be logged into Exponent!'); ?></h3>
       <table cellspacing="0">
       <tr><th><?php echo lang('Server'); ?><td><input type="hidden" name="auth[driver]" value="<?php echo "server"; ?>"><input type="hidden" name="auth[server]" value="<?php echo DB_HOST; ?>"><?php echo DB_HOST; ?>
       <tr><th><?php echo lang('Username'); ?><td><input id="username" name="auth[username]" value="<?php echo DB_USER;  ?>">
       <tr><th><?php echo lang('Password'); ?><td><input type="password" name="auth[password]" value="<?php echo DB_PASS;  ?>">
       </table>
       <p><input type="submit" value="<?php echo lang('Login'); ?>">
       <?php
       		return true;
       	}

	} 
    
    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.0.0-mysql.php";
?>