<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

$config = array(
	'db_engine'=>'mysqli',
	'db_host'=>'localhost',
	'db_port'=>'3306',
	'db_name'=>'',
	'db_user'=>'',
	'db_pass'=>'',
	'db_table_prefix'=>'exponent',
	'DB_ENCODING'=>'utf8'
);

?>
<script language="javascript">
	function hideAllOptions() {
		allEngines = new Array("mysql");
		for(option in allEngines) {
			optionObj = document.getElementById(allEngines[option] + "_options");
			optionObj.style.display = "none";
		}
	}
	function showOptions(engine) {
		hideAllOptions();

		myOptions = document.getElementById(engine + "_options");
		if(myOptions) {
			myOptions.style.display = "block";
		}
	}
</script>

<h1><?php echo gt('Configure Database'); ?></h1>
<form method="post" action="index.php">
    <input type="hidden" name="page" value="install-3" />
	<div class="control">
        <span class="label"><?php echo gt('Backend'); ?>: </span>
        <select name="sc[db_engine]" onchange="showOptions(this.value);">
		<?php
		require_once(BASE.'framework/core/subsystems-1/database.php');
		foreach (exponent_database_backends(1) as $name=>$display) {
            echo '<option value="'.$name.'"';
            if ($config['db_engine'] == $name) {
            	echo ' selected="selected"';
            }
        	echo '>'.$display.'</option>';
        }
		?>
		</select>
		<div class="control_help">
			<?php echo gt('Select which database server software package your web server is running.  If the software is not listed, it is not supported by Exponent.'); ?>
			<br /><br />
			<?php echo gt('If in doubt, contact your system administrator or hosting provider.'); ?>
		</div>
	</div>
	<div class="control" id="mysql_options">
        <span class="label"><?php echo gt('Database Encoding'); ?>: </span>

		<select name="sc[DB_ENCODING]" value="<?php echo $config['DB_ENCODING']; ?>" >
			<?PHP
				if (!defined('SYS_CONFIG')) {
					include_once(BASE.'framework/core/subsystems-1/config.php');
				}

				foreach(exponent_config_dropdownData("DB_ENCODING") as $key=>$value){
					echo '<option value="' . $key . '">' . $value . '</option>';
				}
			?>
		</select>

		<div class="control_help">
			<?php echo gt('Don\'t change that unless you know what you are doing.'); ?>
			<br /><br />
			<?php echo gt('If in doubt, contact your system administrator or hosting provider.'); ?>
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Address'); ?>: </span>
			<input class="text" type="text" name="sc[db_host]" value="<?php echo $config['db_host']; ?>" />
			<div class="control_help">
				<?php echo gt('If your database server software runs on a different physical machine than the web server, enter the address of the database server machine.  Either an IP address (like 1.2.3.4) or an internet domain name (such as example.com) will work.<br /><br />If your database server software runs on the same machine as the web server, use the default setting, "localhost".'); ?>
				<br /><br />
				<?php echo gt('If in doubt, contact your system administrator or hosting provider.'); ?>
			</div>
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Port');?>: </span>
			<input class="text" type="text" name="sc[db_port]" value="<?php echo $config['db_port']; ?>" size="5" />
			<div class="control_help">
				<?php echo gt('If you are using a database server that supports TCP or other network connection protocols, and that database software runs on a different physical machine than the web server, enter the connection port.<br /><br />If you entered "localhost" in the Address field, you should leave this as the default setting.'); ?>
				<br /><br />
				<?php echo gt('If in doubt, contact your system administrator or hosting provider.'); ?>
			</div>
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Database Name'); ?>: </span>
			<input class="text" type="text" name="sc[db_name]" value="<?php echo $config['db_name']; ?>" />
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Username'); ?>: </span>
			<input class="text" type="text" name="sc[db_user]" value="<?php echo $config['db_user']; ?>" />
			<div class="control_help">
				<?php echo gt('All database server software supported by Exponent require some sort of authentication.  Enter the name of the user account to use for logging into the database server.'); ?>
				<br /><br />
				<?php echo gt('Make sure that this user has the proper database user privileges.'); ?>  (<a href="" onclick="return pop('db_priv');"><?php echo gt('More Information'); ?></a>)
			</div>
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Password'); ?>: </span>
			<input class="text" type="password" name="sc[db_pass]" value="<?php echo $config['db_pass']; ?>" />
			<div class="control_help">
				<?php echo gt('Enter the password for the username you entered above.  The password will <b>not</b> be obscured, because it cannot be obscured in the configuration file.  The Exponent developers urge you to use a completely new password, unlike any of your others, for security reasons.'); ?>
			</div>
		</div>
		<div class="control">
			<span class="label"><?php echo gt('Table Prefix'); ?>: </span>
			<input class="text" type="text" name="sc[db_table_prefix]" value="<?php echo $config['db_table_prefix']; ?>" />
			<div class="control_help">
				<?php echo gt('A table prefix helps Exponent differentiate tables for this site from other tables that may already exist (or eventually be created by other scripts).  If you are using an existing database, you may want to change this.'); ?>
				<br /><br />
				<?php echo gt('<b>Note:</b> A table prefix may only contain numbers and letters.  Spaces and symbols (including "_") are not allowed.  An underscore will be added for you, by Exponent.'); ?>
			</div>
		</div>
	</div>
	<button class="awesome large green"><?php echo gt('Install Database'); ?></button>
</form>
