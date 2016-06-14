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
//        new AdminerSimpleMenu(),
        new AdminerJsonPreview(),
//        new AdminerDumpAlter,
        new AdminerDumpBz2,  // adds bz2 option to export
//        new AdminerDumpDate,
        new AdminerDumpZip,  // adds zip option to export
        new AdminerEditCalendar(
            "<script type='text/javascript' src='".JQUERY_SCRIPT."'></script>\n<script type='text/javascript' src='".JQUERYUI_SCRIPT."'></script>\n<script type='text/javascript' src='".JQUERY_RELATIVE."addons/js/jquery-ui-timepicker-addon.js'></script>\n<link rel='stylesheet' type='text/css' href='".JQUERYUI_CSS."'>\n<link rel='stylesheet' type='text/css' href='".JQUERY_RELATIVE."addons/css/jquery-ui-timepicker-addon.css'>\n",
            JQUERY_RELATIVE."js/ui/i18n/datepicker-%s.js"
        ),  // add calendar popup for date/time fileds
        new AdminerEnumOption,  // turns enum fields into select input
        new AdminerTablesFilter,  // adds filter input to tables list
        new AdminerEditTextSerializedarea,  // displays unserialized data as a tooltip
        //new AdminerEmailTable,
        new AdminerEditForeign,
//        new AdminerForeignSystem,
        new ConventionForeignKeys,
        new AdminerVersionNoverify,  // disable adminer version check/notifiy

    );
    if (SITE_WYSIWYG_EDITOR == 'tinymce') {
        $plugins[] = new AdminerTinymce(
            PATH_RELATIVE."external/editors/tinymce/tinymce.min.js"
        );  // inserts wysiwyg editor for 'body' fields
    } else {
        $plugins[] = new AdminerCKeditor(
            array(
                PATH_RELATIVE."external/editors/ckeditor/ckeditor.js"
            ),
            "options"
        );  // inserts wysiwyg editor for 'body' fields
    }
    $plugins[] = new AdminerEditTextarea;  // adjusts box size smaller, MUST be last in chain for textarea widgets
    $plugins[] = new AdminerTheme('default-blue');  // sets responsive theme color and other details

    /* It is possible to combine customization and plugins: */
    class AdminerCustomization extends AdminerPlugin {
        /** Name in title and navigation
         * @return string HTML code
         */
		function name() { // custom name in title and heading 
			return gt('Exponent CMS Database');
		}

        /** Get key used for permanent login
         * @param bool
         * @return string cryptic string which gets combined with password or false in case of an error
         */
//		function permanentLogin() { // key used for permanent login
//			return "";
//		}

        /** Connection parameters
         * @return array ($server, $username, $password)
         */
		function credentials() { // server, username and password for connecting to database 
			return array(DB_HOST, DB_USER , DB_PASS);
		}

        /** Identifier of selected database
         * @return string
         */
		function database() { // database name, will be escaped by Adminer 
			return DB_NAME;
		}

        /** Authorize the user
         * @param string
         * @param string
         * @return bool
         */
		function login($login, $password) { // validate user submitted credentials
            global $user;

            if (empty($user->id)) {
                return false;
            } else {
                return ($user->isLoggedIn() && $user->isSuperAdmin());
            }
		}

        /** Get cached list of databases
         * @param bool
         * @return array
         */
		function databases($flush = true) {
			return array(DB_NAME);
		}

        /** Print login form
         * @return null
         */
        function loginForm() {
            ?>
           <h3><?php echo gt('You must already be logged into Exponent!'); ?></h3>
            <?php
            global $user;
            if (!$user->isSuperAdmin()) {
                return false;
            }
            ?>
       <table cellspacing="0">
       <tr><th><?php echo lang('Server'); ?><td><input type="hidden" name="auth[driver]" value="<?php echo "server"; ?>"><input type="hidden" name="auth[server]" value="<?php echo DB_HOST; ?>"><?php echo DB_HOST; ?>
       <tr><th><?php echo lang('Database'); ?><td><?php echo DB_NAME; ?>
       <tr><th><?php echo lang('Username'); ?><td><input id="username" name="auth[username]">
       <tr><th><?php echo lang('Password'); ?><td><input type="password" name="auth[password]">
       </table>
       <p><input type="submit" value="<?php echo lang('Login'); ?>">
       <?php
       		return true;
       	}

	} 
    
    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.2.5-mysql.php";

if (SITE_WYSIWYG_EDITOR != 'tinymce') {
?>
    <script type='text/javascript'>
        CKEDITOR.disableAutoInline = true;
    </script>
<?php
}
?>