<?php

##################################################
#
# Copyright (c) 2004-2018 OIC Group, Inc.
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

if (!defined('EXPONENT')) {
    exit('');
}

global $db;

$passed = true;
$warning = array();

?>
    <h1><?php echo gt('Checking Database Configuration'); ?></h1>
    <table class="exp-skin-table">
    <thead>
    <tr>
        <th colspan=2><?php echo gt('Results'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php

    function echoStart($msg)
    {
        echo '<tr><td valign="top" class="bodytext">' . $msg . '</td><td valign="top" class="bodytext">';
    }

    function echoSuccess($msg = "")
    {
        echo '<span class="success">' . gt('Succeeded') . '</span>';
        if ($msg != "") {
            echo ' : ' . $msg;
        }
        echo '</td></tr>';
    }

    function echoWarning($msg = "")
    {
        echo '<span class="warning">' . gt('Warning') . '</span>';
        if ($msg != "") {
            echo ' : ' . $msg;
        }
        echo '</td></tr>';
    }

    function echoFailure($msg = "")
    {
        global $passed;

        $passed = false;
        echo '<span class="failed">' . gt('Failed') . '</span>';
        if ($msg != "") {
            echo ' : ' . $msg;
        }
        echo '</td></tr>';
    }

    function isAllGood($str)
    {
        return !preg_match("/[^A-Za-z0-9]/", $str);
    }

    //expSession::set("installer_config",$_REQUEST['sc']);
    $config = $_REQUEST['sc'];
    //$config['sef_urls'] = empty($_REQUEST['c']['sef_urls']) ? 0 : 1;

    if (preg_match('/[^A-Za-z0-9]/', $config['db_table_prefix'])) {
        echoFailure(gt('Invalid table prefix.  The table prefix can only contain alphanumeric characters.'));
    }

    if ($passed) {
        //set connection encoding, works only on mySQL > 4.1
        if ($config["db_engine"] === "mysqli") {
            if (!defined('DB_ENCODING')) {
                define('DB_ENCODING', $config["DB_ENCODING"]);
            }
            if (!defined('DB_STORAGE_ENGINE')) {
                define('DB_STORAGE_ENGINE', $config["DB_STORAGE_ENGINE"]);
            }
        }
        $db = expDatabase::connect(
            $config['db_user'],
            $config['db_pass'],
            $config['db_host'] . ':' . $config['db_port'],
            $config['db_name'],
            $config['db_engine'],
            1
        );

        $db->prefix = $config['db_table_prefix'] . '_';

        $status = array();

        echoStart(gt('Connecting') . ':');

        if ($db->connection == null) {
            echoFailure(gt("Trying to Connect to Database") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Getting Tables') . ':');
        $tables = @$db->getTables();
        if ($db->inError()) {
            echoFailure(gt("Trying to Get Tables") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed && $config["db_engine"] === "mysqli") {
        echoStart(gt('MySQL lower_case_table_names setting') . ':');
        $server = @mysqli_fetch_assoc($db->sql("SHOW VARIABLES LIKE '%lower_case_file%'", false));
        $setting = @mysqli_fetch_assoc($db->sql("SHOW VARIABLES LIKE '%lower_case_table%'", false));
        if (($server['Variable_name'] == 'lower_case_file_system' && $server['Value'] == 'ON') && ($setting['Variable_name'] == 'lower_case_table_names' && $setting['Value'] != 2)) {
            echoWarning(gt('NOT set to \'2\''));
            $warning[] = gt(
                'Since your server uses lowercase filenames, you must ensure the MySQL ini file has \'lower_case_table_names = 2\' in the [mysqld] section to prevent issues!'
            );
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Not a 0.9x database') . ':');
        if (!@$db->tableExists('textitem')) {
            echoSuccess();
        } else {
            echoFailure(
                gt("This is a 0.9x database.") . ' ' . gt(
                    "Create a new database, then MIGRATE from the 0.9x database after installation."
                )
            );
        }
    }

    if ($passed) {
        echoStart(gt('Not an existing database') . ':');
        if (!@$db->tableExists('text')) {
            echoSuccess();
        } else {
            echoFailure(
                gt("This is an existing Exponent CMS database.") . ' ' . gt(
                    "Create a new database, then re-run the installation."
                )
            );
        }
    }

    $tablename = "installer_test" . time(); // Used for other things

    $dd = array(
        "id" => array(
            DB_FIELD_TYPE => DB_DEF_ID,
            DB_PRIMARY    => true,
            DB_INCREMENT  => true
        ),
        "installer_test" => array(
            DB_FIELD_TYPE => DB_DEF_STRING,
            DB_FIELD_LEN  => 100
        )
    );

    if ($passed) {
        @$db->createTable($tablename, $dd, array());

        echoStart(gt('Checking CREATE TABLE privilege') . ':');
        if (@$db->tableExists($tablename)) {
            echoSuccess();
        } else {
            echoFailure(gt("Trying to Create Tables"));
        }
    }

    $insert_id = null;
    $obj = new stdClass();

    if ($passed) {
        echoStart(gt('Checking INSERT privilege') . ':');
        $obj->installer_test = "Exponent Installer Wizard";
        $insert_id = @$db->insertObject($obj, $tablename);
        if ($insert_id == 0) {
            echoFailure(gt("Trying to Insert Items") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Checking SELECT privilege') . ':');
        $obj = @$db->selectObject($tablename, "id=" . $insert_id);
        if ($obj == null || $obj->installer_test !== "Exponent Installer Wizard") {
            echoFailure(gt("Trying to Select Items") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Checking UPDATE privilege') . ':');
        if (empty($obj)) {
            $obj = new stdClass();
        }
        $obj->installer_test = "Exponent 2";
        if (!@$db->updateObject($obj, $tablename)) {
            echoFailure(gt("Trying to Update Items") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Checking DELETE privilege') . ':');
        @$db->delete($tablename, "id=" . $insert_id);
        $error = $db->error();
        $obj = @$db->selectObject($tablename, "id=" . $insert_id);
        if ($obj != null) {
            echoFailure(gt("Trying to Delete Items") . " (" . $error . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        $dd["exponent"] = array(
            DB_FIELD_TYPE => DB_DEF_STRING,
            DB_FIELD_LEN  => 8
        );

        echoStart(gt('Checking ALTER TABLE privilege') . ':');
        @$db->alterTable($tablename, $dd, array());
        $error = $db->error();

        $obj = new stdClass();
        $obj->installer_test = "Exponent Installer ALTER test";
        $obj->exponent = "Exponent";

        if (!@$db->insertObject($obj, $tablename)) {
            echoFailure(gt("Trying to Alter Tables") . " (" . $error . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Checking DROP TABLE privilege') . ':');
        @$db->dropTable($tablename);
        $error = $db->error();
        if (@$db->tableExists($tablename)) {
            echoFailure(gt("Trying to Drop Tables") . " (" . $error . ")");
        } else {
            echoSuccess();
        }
    }

    if ($passed) {
        echoStart(gt('Installing Tables') . ':');

        $tables = expDatabase::install_dbtables();

        if (@$db->tableIsEmpty('user')) {
            $user = new stdClass();
            $user->username = 'admin';
            $user->password = user::encryptPassword('admin');
            $user->is_admin = 1;
            $user->is_acting_admin = 1;
            $user->is_system_user = 1;
            @$db->insertObject($user, 'user');
        }

        if (@$db->tableIsEmpty('modstate')) {
            $modstate[0] = new stdClass();
            $modstate[0]->module = 'text';
            $modstate[0]->active = 1;
            foreach ($modstate as $key => $val) {
                @$db->insertObject($modstate[$key], 'modstate');
            }
        }

        if (@$db->tableIsEmpty('section')) {
            $section = new stdClass();
            $section->name = gt('Home'); //FIXME not sure if we should do this?
            $section->public = 1;
            $section->active = 1;
            $section->rank = 0;
            $section->parent = 0;
            $sid = @$db->insertObject($section, 'section');
        }

        echoSuccess();
    }

    if ($passed) {
        echoStart(gt('Saving Configuration'));
        foreach ($config as $key => $value) {
            expSettings::change($key, addslashes($value));
        }

        // version tracking
        @$db->delete('version', 1); // clear table of old accumulated entries
        $vo = expVersion::swVersion();
        $vo->created_at = time();
        if (!@$db->insertObject($vo, 'version')) {
            echoFailure(gt("Trying to Add Version to DB") . " (" . $db->error() . ")");
        } else {
            echoSuccess();
        }

    }

    ?>
    </tbody>
    </table>
<?php

if ($passed) {
    // Do some final cleanup
    foreach (@$db->getTables() as $t) {
        // FIX table prefix problem
        if (substr($t, 0, 14 + strlen($db->prefix)) == $db->prefix . 'installer_test') {
            @$db->dropTable(str_replace($db->prefix, '', $t));
        }
    }
    echo '<p>';
    echo gt('Database tests passed.');
    echo '</p>';
    if (!empty($warning)) {
        foreach ($warning as $message) {
            echo '<p><span class="warning">' . gt('Warning') . ':</span> ';
            echo $message;
            echo '</p>';
        }
    }

    ?>
    <a class="awesome green large" href="?page=install-4"><?php echo gt('Continue Installation'); ?></a>
<?php
} else {
    ?>
    <a class="awesome red large" href="?page=install-2" onclick="history.go(-1); return false;"><?php echo gt(
            'Edit Your Database Settings'
        ); ?></a>
<?php
}
?>