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

if (exponent_sessions_isset('installer_config')) {
	$config = exponent_sessions_get('installer_config');
} else {
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
}

$i18n = exponent_lang_loadFile('install/pages/dbconfig.php');

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
<h2><?php echo $i18n['subtitle']; ?></h2>

<form method="post" action="index.php">
<input type="hidden" name="page" value="dbcheck" />

<h2><?php echo $i18n['server_info']; ?></h2>
<div class="form_section">
	<div class="control">
		 <span class="label"><?php echo $i18n['backend']; ?>: </span>
		<select name="c[db_engine]" onchange="showOptions(this.value);">
		<?php
		require_once(BASE.'subsystems/database.php');
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
			<?php echo $i18n['backend_desc']; ?>
			<br /><br />
			<?php echo $i18n['in_doubt']; ?>
		</div>
	</div>


	<div class="control" id="mysql_options">
		 <span class="label"><?php echo $i18n['DB_ENCODING']; ?>: </span>
		<select name="c[DB_ENCODING]" value="<?php echo $config['DB_ENCODING']; ?>" >
<?PHP
	if (!defined('SYS_CONFIG')) {
		include_once(BASE.'subsystems/config.php');
	}

	foreach(exponent_config_dropdownData("DB_ENCODING") as $key=>$value){
		echo '			<option value="' . $key . '">' . $value . '</option>';
	}
?>
		</select>
		<div class="control_help">
			<?php echo $i18n['DB_ENCODING_desc']; ?>
			<br /><br />
			<?php echo $i18n['in_doubt']; ?>
		</div>
	</div>

	<div class="control">
		 <span class="label"><?php echo $i18n['address']; ?>: </span>
		<input class="text" type="text" name="c[db_host]" value="<?php echo $config['db_host']; ?>" />
		<div class="control_help">
			<?php echo $i18n['address_desc']; ?>
			<br /><br />
			<?php echo $i18n['in_doubt']; ?>
		</div>
	</div>

	<div class="control">
		 <span class="label"><?php echo $i18n['port'];?>: </span>
		<input class="text" type="text" name="c[db_port]" value="<?php echo $config['db_port']; ?>" size="5" />
		<div class="control_help">
			<?php echo $i18n['port_desc']; ?>
			<br /><br />
			<?php echo $i18n['in_doubt']; ?>
		</div>
	</div>
</div>

<h2><?php echo $i18n['database_info']; ?></h2>
<div class="form_section">
	<div class="control">
		 <span class="label"><?php echo $i18n['dbname']; ?>: </span>
		<input class="text" type="text" name="c[db_name]" value="<?php echo $config['db_name']; ?>" />

	</div>

	<div class="control">
		 <span class="label"><?php echo $i18n['username']; ?>: </span>
		<input class="text" type="text" name="c[db_user]" value="<?php echo $config['db_user']; ?>" />
		<div class="control_help">
			<?php echo $i18n['username_desc']; ?>
			<br /><br />
			<?php echo $i18n['username_desc2']; ?>  (<a href="" onclick="return pop('db_priv');"><?php echo $i18n['more_info']; ?></a>)
		</div>
	</div>
	<div class="control">
		 <span class="label"><?php echo $i18n['password']; ?>: </span>
		<input class="text" type="password" name="c[db_pass]" value="<?php echo $config['db_pass']; ?>" />
		<div class="control_help">
			<?php echo $i18n['password_desc']; ?>
		</div>
	</div>

	<div class="control">
		 <span class="label"><?php echo $i18n['prefix']; ?>: </span>
		<input class="text" type="text" name="c[db_table_prefix]" value="<?php echo $config['db_table_prefix']; ?>" />
		<div class="control_help">
			<?php echo $i18n['prefix_desc']; ?>
			<br /><br />
			<?php echo $i18n['prefix_note']; ?>
		</div>
	</div>
</div>

<!--h2><?php echo $i18n['default_content']; ?></h2>
<div class="form_section">
	<div class="control">
		 <span class="label"><?php echo $i18n['install']; ?></span>
		<input type="checkbox" name="install_default" checked="checked" />
		<div class="control_help">
		<?php echo $i18n['install_desc']; ?>
		<br /><br />
		</div>
	</div>
</div-->

<h2><?php echo $i18n['sef']; ?></h2>
<div class="form_section">
	<div class="control">
		 <span class="label"><?php echo $i18n['enable_sef']; ?></span>
		<input type="checkbox" name="c[sef_urls]" checked="checked" />
		<div class="control_help">
                <?php echo $i18n['sef_desc']; ?>
                <br /><br />
                </div>
	</div>
</div>

<h2><?php echo $i18n['verify']; ?></h2>
<div class="form_section">
	<div class="control">
		<div class="control_help">
		<?php echo $i18n['verify_desc']; ?>
		<br /><br />
		</div>
		<button class="awesome large green"><?php echo $i18n['test_settings']; ?></button>
	</div>
</div>
</form>
