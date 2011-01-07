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

if (!defined('EXPONENT')) exit('');

$i18n = exponent_lang_loadFile('install/pages/dbcheck.php');

?>
<h2 id="subtitle"><?php echo $i18n['subtitle']; ?></h2>
<table>
<?php

function echoStart($msg) {
	echo '<tr><td valign="top" class="bodytext">'.$msg.'</td><td valign="top" class="bodytext">';
}

function echoSuccess($msg = "") {
	global $i18n;
	echo '<span class="success">'.$i18n['succeeded'].'</span>';
	if ($msg != "") echo ' : ' . $msg;
	echo '</td></tr>';
}

function echoFailure($msg = "") {
	global $i18n;
	echo '<span class="failed">'.$i18n['failed'].'</span>';
	if ($msg != "") echo ' : ' . $msg;
	echo '</td></tr>';
}

function isAllGood($str) {
	return !preg_match("/[^A-Za-z0-9]/",$str);
}

exponent_sessions_set("installer_config",$_POST['c']);
$config = $_POST['c'];
$config['sef_urls'] = empty($_POST['c']['sef_urls']) ? 0 : 1;

$passed = true;

if (preg_match('/[^A-Za-z0-9]/',$config['db_table_prefix'])) {
	echoFailure($i18n['bad_prefix']);
	$passed = false;
}

if ($passed) {
	//set connection encoding, works only on mySQL > 4.1
	if($config["db_engine"] == "mysql") {
		if (!defined("DB_ENCODING")) define("DB_ENCODING", $config["DB_ENCODING"]);
	}
	$db = exponent_database_connect($config['db_user'],$config['db_pass'],$config['db_host'],$config['db_name'],$config['db_engine'],1);

	$db->prefix = $config['db_table_prefix'] . '_';

	$status = array();

	echoStart($i18n['connecting'].':');

	if ($db->connection == null) {
		echoFailure($db->error());
		// FIXME:BETTER ERROR CHECKING
		$passed = false;
	}
}

if ($passed) {
	$tables = $db->getTables();
	if ($db->inError()) {
		echoFailure($db->error());
		$passed = false;
	} else {
		echoSuccess();
	}
}

$tablename = "installer_test".time(); // Used for other things

$dd = array(
	"id"=>array(
		DB_FIELD_TYPE=>DB_DEF_ID,
		DB_PRIMARY=>true,
		DB_INCREMENT=>true),
	"installer_test"=>array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>100)
);

if ($passed) {
	$db->createTable($tablename,$dd,array());

	echoStart($i18n['check_create'].':');
	if ($db->tableExists($tablename)) {
		echoSuccess();
	} else {
		echoFailure();
		$passed = false;
	}
}

$insert_id = null;
$obj = null;

if ($passed) {
	echoStart($i18n['check_insert'].':');
	$obj->installer_test = "Exponent Installer Wizard";
	$insert_id = $db->insertObject($obj,$tablename);
	if ($insert_id == 0) {
		$passed = false;
		echoFailure($db->error());
	} else {
		echoSuccess();
	}
}

if ($passed) {
	echoStart($i18n['check_select'].':');
	$obj = $db->selectObject($tablename,"id=".$insert_id);
	if ($obj == null || $obj->installer_test != "Exponent Installer Wizard") {
		$passed = false;
		echoFailure($db->error());
	} else {
		echoSuccess();
	}
}

if ($passed) {
	echoStart($i18n['check_update'].':');
	$obj->installer_test = "Exponent 2";
	if (!$db->updateObject($obj,$tablename)) {
		$passed = false;
		echoFailure($db->error());
	} else {
		echoSuccess();
	}
}

if ($passed) {
	echoStart($i18n['check_delete'].':');
	$db->delete($tablename,"id=".$insert_id);
	$error = $db->error();
	$obj = $db->selectObject($tablename,"id=".$insert_id);
	if ($obj != null) {
		$passed = false;
		echoFailure($error);
	} else {
		echoSuccess();
	}
}

if ($passed) {
	$dd["exponent"] = array(
		DB_FIELD_TYPE=>DB_DEF_STRING,
		DB_FIELD_LEN=>8);

	echoStart($i18n['check_alter'].':');
	$db->alterTable($tablename,$dd,array());
	$error = $db->error();

	$obj = null;
	$obj->installer_test = "Exponent Installer ALTER test";
	$obj->exponent = "Exponent";

	if (!$db->insertObject($obj,$tablename)) {
		$passed = false;
		echoFailure($error);
	} else {
		echoSuccess();
	}
}


if ($passed) {
	echoStart($i18n['check_drop'].':');
	$db->dropTable($tablename);
	$error = $db->error();
	if ($db->tableExists($tablename)) {
		$passed = false;
		echoFailure($error);
	} else {
		echoSuccess();
	}
}

if ($passed) {
	echoStart($i18n['installing_tables'].':');

	$dirs = array(
			BASE."datatypes/definitions",
			BASE."framework/core/database/definitions",
		);
	
	foreach ($dirs as $dir) {
	    if (is_readable($dir)) {
		    $dh = opendir($dir);
		    while (($file = readdir($dh)) !== false) {
			    if (is_readable("$dir/$file") && is_file("$dir/$file") && substr($file,-4,4) == ".php" && substr($file,-9,9) != ".info.php") {
				    $tablename = substr($file,0,-4);
				    $dd = include("$dir/$file");
				    $info = array();
				    if (is_readable("$dir/$tablename.info.php")) $info = include("$dir/$tablename.info.php");
				    if (!$db->tableExists($tablename)) {
					    $db->createTable($tablename,$dd,$info);
				    } else {
					    $db->alterTable($tablename,$dd,$info);
				    }
			    }
		    }
	    }
	}

	if ($db->tableIsEmpty('user')) {
		$user = null;
		$user->username = 'admin';
		$user->password = md5('admin');
		$user->is_admin = 1;
		$user->is_acting_admin = 1;
		$db->insertObject($user,'user');
	}

	if ($db->tableIsEmpty('modstate')) {
		$modstate = array();
		$modstate[0]->module = 'textController';
		$modstate[0]->active = 1;
		foreach($modstate as $key=>$val){
    		$db->insertObject($modstate[$key],'modstate');
		}
	}

	if ($db->tableIsEmpty('section')) {
		$section = null;
		$section->name = 'Home';
		$section->public = 1;
		$section->active = 1;
		$section->rank = 0;
		$section->parent = 0;
		$sid = $db->insertObject($section,'section');
	}

	echoSuccess();
}

if ($passed) {
	echoStart($i18n['saving_config']);

	$values = array(
		'c'=>$config,
		'opts'=>array(),
		'configname'=>'Default',
		'activate'=>1
	);

	if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');
	exponent_config_saveConfiguration($values);
	// ERROR CHECKING
	echoSuccess();
}

?>
</table>
<?php

if ($passed) {
	// Do some final cleanup
	foreach ($db->getTables() as $t) {
		// FIX table prefix problem
		if (substr($t,0,14+strlen($db->prefix)) == $db->prefix.'installer_test') {
			$db->dropTable(str_replace($db->prefix,'',$t));
		}
	}
	echo '<br /><br />';
	echo $i18n['passed'];
	echo '<br /><br />';

	if (isset($_POST['install_default'])) {
		if (!defined('SYS_BACKUP')) include_once(BASE.'subsystems/backup.php');

		$eql = BASE.'install/sitetypes/db/_default.eql';
		$errors = array();
		exponent_backup_restoreDatabase($db,$eql,$errors,0);
		if (count($errors)) {
			echo $i18n['errors_encountered_eql'].'<br /><br />';
			foreach ($errors as $e) echo $e . '<br />';
		} else {
			echo $i18n['eql_success'];
		}
	}
	?>
	<br /><br /><a href='?page=admin_user'><?php echo $i18n['continue']; ?></a>.
	<?php
} else {
	?>
	<br /><br /><a href="?page=dbconfig" onclick="history.go(-1); return false;"><?php echo $i18n['back']; ?></a>
	<?php
}
?>
