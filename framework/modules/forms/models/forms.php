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
 * @subpackage Models
 * @package    Modules
 */

class forms extends expRecord {
//	public $table = 'text';
    public $has_many = array(
        'forms_control',
    );

//    protected $attachable_item_types = array(
//        'content_expFiles'=>'expFile'
//    );

#	public $validates = array(
#		'presence_of'=>array(
#			'body'=>array('message'=>'Body is a required field.'),
#		));

    /**
     * Transfers form fields to database table columns
     *
     * @static
     * @return mixed
     */
    public function updateTable() {
        global $db;

        if (!empty($this->is_saved)) {
            $datadef = array(
                'id'            => array(
                    DB_FIELD_TYPE => DB_DEF_ID,
                    DB_PRIMARY    => true,
                    DB_INCREMENT  => true),
                'ip'            => array(
                    DB_FIELD_TYPE => DB_DEF_STRING,
                    DB_FIELD_LEN  => 25),
                'referrer'      => array(
                    DB_FIELD_TYPE => DB_DEF_STRING,
                    DB_FIELD_LEN  => 1000),
                'timestamp'     => array(
                    DB_FIELD_TYPE => DB_DEF_TIMESTAMP),
                'user_id'       => array(
                    DB_FIELD_TYPE => DB_DEF_ID),
                'location_data' => array(
                    DB_FIELD_TYPE => DB_DEF_STRING,
                    DB_FIELD_LEN  => 250,
                    DB_INDEX      => 10)
            );

            if (!isset($this->id)) {
                $this->table_name = preg_replace('/[^A-Za-z0-9]/', '_', $this->title);
                $tablename = 'forms_' . $this->table_name;
                $index = '';
                while ($db->tableExists($tablename . $index)) {
                    $index++;
                }
                $tablename = $tablename . $index;
                $db->createTable($tablename, $datadef, array());
                $this->table_name .= $index;
            } else {
                if ($this->table_name == '') {
                    $tablename = preg_replace('/[^A-Za-z0-9]/', '_', $this->title);
                    $index = '';
                    while ($db->tableExists('forms_' . $tablename . $index)) {
                        $index++;
                    }
                    $this->table_name = $tablename . $index;
                    $this->update(); // save our table name to form
                }

                $tablename = 'forms_' . $this->table_name;

                //If table is missing, create a new one.
                if (!$db->tableExists($tablename)) {
                    $db->createTable($tablename, $datadef, array());
                }

                $ctl = null;
//                $control_type = '';
                $tempdef = array();
                foreach ($db->selectObjects('forms_control', 'forms_id=' . $this->id) as $control) {
                    if ($control->is_readonly == 0) {
                        $ctl = unserialize($control->data);
                        $ctl->identifier = $control->name;
                        $ctl->caption = $control->caption;
                        $ctl->id = $control->id;
                        $control_type = get_class($ctl);
                        $def = call_user_func(array($control_type, 'getFieldDefinition'));
                        if ($def != null) {
                            $tempdef[$ctl->identifier] = $def;
                        }
                    }
                }
                $datadef = array_merge($datadef, $tempdef);
                $db->alterTable($tablename, $datadef, array(), true);
            }
        }
        return $this->table_name;
    }

    /**
     * Check to see if forms table exists
     *
     * @return bool
     */
    public function tableExists() {
        global $db;

        return $db->tableExists("forms_" . $this->table_name);
    }

    /**
     * Returns form records as objects
     *
     * @param string $where
     *
     * @return array
     */
    public function getRecords($where="1") {
        global $db;

        return $db->selectObjects('forms_' . $this->table_name, $where);
    }

    /**
     * Returns form records as an array
     *
     * @param string $where
     *
     * @return array
     */
    public function selectRecordsArray($where="1") {
        global $db;

        return $db->selectArrays('forms_' . $this->table_name, $where);
    }

    /**
     * Returns single forms record as object
     *
     * @param null $id record to retrieve or first record if null
     *
     * @return null|object|void
     */
    public function getRecord($id=null) {
        global $db;

        if ($id == null) {
            $record = $db->selectObject('forms_' . $this->table_name, "1 LIMIT 0,1");  // get first record
        } elseif (is_numeric($id)) {
            $record =  $db->selectObject('forms_' . $this->table_name, "id ='{$id}'");
        } else {
            $record =  $db->selectObject('forms_' . $this->table_name, $id);
        }
        return empty($record) ? null : $record;
    }

    /**
     * Count of form records
     *
     * @param string $where
     *
     * @return int
     */
    public function countRecords($where="1") {
        global $db;

        return $db->countObjects("forms_" . $this->table_name, $where);
    }

    /**
     * Insert a form record
     *
     * @param null $record
     *
     * @return null
     */
    public function insertRecord($record=null) {
        global $db;

        if ($record == null) return null;
        $db->insertObject($record, 'forms_' . $this->table_name);
    }

    /**
     * Update a form record
     *
     * @param null $record
     *
     * @return null
     */
    public function updateRecord($record=null) {
        global $db;

        if ($record == null) return null;
        $db->updateObject($record, 'forms_' . $this->table_name);
    }

    /**
     * Delete a form record
     *
     * @param null $id
     */
    public function deleteRecord($id=null) {
        global $db;

        if ($id == null) return;
        $db->delete('forms_' . $this->table_name, "id='{$id}'");
    }

    /**
     * is run after deleting form
     */
    public function afterDelete() {
        global $db;

        // get and delete the controls for this form
        $fc = new forms_control();
        $controls = $fc->find('all', 'forms_id=' . $this->id);
        foreach ($controls as $control) {
            $control->delete();
        }

        // delete the table for this form
        if (!empty($this->is_saved)) {
            $db->dropTable("forms_" . $this->table_name);
        }
    }

}

?>