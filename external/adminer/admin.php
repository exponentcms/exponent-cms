<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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
//        new AdminerJsonPreview(),
//        new AdminerDumpAlter,
        new AdminerDumpBz2,  // adds bz2 option to export
//        new AdminerDumpDate,
        new AdminerDumpZip,  // adds zip option to export
        new AdminerEditCalendar(
            "<link rel='stylesheet' type='text/css' href='".JQUERYUI_CSS."'>\n"
            . "<link rel='stylesheet' type='text/css' href='".JQUERY_RELATIVE."addons/css/jquery-ui-timepicker-addon.css'>\n"
            . script_src(JQUERY_SCRIPT)
            . script_src(JQUERYUI_SCRIPT)
            . script_src(JQUERY_RELATIVE."addons/js/jquery-ui-timepicker-addon.js"),
            JQUERY_RELATIVE."js/ui/i18n/datepicker-%s.js"
        ),  // add calendar popup for date/time fileds
        new AdminerEnumOption,  // turns enum fields into select input
        new AdminerTablesFilter,  // adds filter input to tables list
        new AdminerSerializedPreview,  // displays unserialized data as a table
        new AdminerJsonPreview,  // displays json data as a table
        new AdminerEditTextSerializedarea,  // displays unserialized data as a tooltip
        //new AdminerEmailTable,
        new AdminerEditForeign,
//        new AdminerForeignSystem,
        new ConventionForeignKeys,
        new AdminerVersionNoverify,  // disable adminer version check/notify
//        new AdminerStructComments,
        new AdminerTableIndexesStructure,
        new AdminerTableStructure,
//        new AdminerTreeViewer(PATH_RELATIVE . 'external/adminer/plugins/script.js')
    );
//    if (SITE_WYSIWYG_EDITOR === 'tinymce') {
        $plugins[] = new AdminerTinymce(
            PATH_RELATIVE."external/editors/tinymce/tinymce.min.js"
        );  // inserts wysiwyg editor for 'body' fields
//    } else {
//        $plugins[] = new AdminerCKeditor(
//            array(
//                PATH_RELATIVE."external/editors/ckeditor/ckeditor.js"
//            ),
//            "options"
//        );  // inserts wysiwyg editor for 'body' fields
//    }
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
       	* @return mixed true for success, string for error message, false for unknown error
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
//        function loginForm() {
//            global $drivers, $user;
//
//            if (empty($user->id) || !($user->isLoggedIn() && $user->isSuperAdmin())) {
//                echo '<h3>' . gt('You must already be logged into Exponent!') . '</h3>';
//            } else {
//                echo "<table cellspacing='0' class='layout'>\n";
//                echo $this->loginFormField('driver', '<tr><th>' . lang('System') . '<td>', html_select("auth[driver]", $drivers, DRIVER, "loginDriver(this);") . "\n");
//                echo $this->loginFormField('server', '<tr><th>' . lang('Server') . '<td>', '<input name="auth[server]" value="' . DB_HOST . '" title="hostname[:port]" placeholder="localhost" autocapitalize="off">' . "\n");
//                echo $this->loginFormField('username', '<tr><th>' . lang('Username') . '<td>', '<input name="auth[username]" id="username" value="' . DB_USER . '" autocomplete="username" autocapitalize="off">' . script("focus(qs('#username')); qs('#username').form['auth[driver]'].onchange();"));
//                echo $this->loginFormField('password', '<tr><th>' . lang('Password') . '<td>', '<input type="password" name="auth[password]" autocomplete="current-password">' . "\n");
//                echo $this->loginFormField('db', '<tr><th>' . lang('Database') . '<td>', '<input name="auth[db]" value="' . DB_NAME . '" autocapitalize="off">' . "\n");
//                echo "</table>\n";
//                echo "<p><input type='submit' value='" . lang('Login') . "'>\n";
//                echo checkbox("auth[permanent]", 1, $_COOKIE["adminer_permanent"], lang('Permanent login')) . "\n";
//            }
//       	}

        /** Get Content Security Policy headers
        * @return array of arrays with directive name in key, allowed sources in value
        */
        function csp() {
        	return array(
        		array(
        			"script-src" => "'self' 'unsafe-inline' 'nonce-" . get_nonce() . "' 'strict-dynamic'", // 'self' is a fallback for browsers not supporting 'strict-dynamic', 'unsafe-inline' is a fallback for browsers not supporting 'nonce-'
        			"style-src" => "'self' 'unsafe-inline'",
        			"connect-src" => "'self'",
        			"frame-src" => "https://www.adminer.org",
                    "object-src" => "'none'",
//                    "base-uri" => "'none'",
                    "form-action" => "'self'",
                ),
        	);
        }

	}

    return new AdminerCustomization($plugins);
}

// include original Adminer or Adminer Editor
include "./adminer-4.8.1.php";

//if (SITE_WYSIWYG_EDITOR != 'tinymce') {
//?>
<!--    <script type='text/javascript'  --><?php //echo nonce(); ?><!-->-->
<!--        CKEDITOR.disableAutoInline = true;-->
<!--    </script>-->
<?php
//}
//?>