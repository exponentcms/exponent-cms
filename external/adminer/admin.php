<?php
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
        new AdminerEditCalendar,
        new AdminerCKeditor,
//        new AdminerEditTextarea,
        new AdminerEnumOption,
        new AdminerTablesFilter,
        new AdminerEditTextSerializedarea,
        //new AdminerEmailTable,
        //new AdminerEditForeign,
        //new AdminerForeignSystem,
        new AdminerVersionNoverify,
    );
    
    /* It is possible to combine customization and plugins: */
    class AdminerCustomization extends AdminerPlugin { 
		function name() { // custom name in title and heading 
			return gt('Exponent Database');
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

			return ($user->isLoggedIn() && $user->isSuperAdmin());
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
include "./adminer-3.6.3-mysql.php";
?>