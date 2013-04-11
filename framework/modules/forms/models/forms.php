<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
     * Transfers form entries to database
     *
     * @static
     *
     * @param $object
     *
     * @return mixed
     */
    public function updateTable() {
        global $db;

        if ($this->is_saved == 1) {
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
                $control_type = '';
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

    public function afterDelete() {
        global $db;

        // get and delete the controls for this form
        $fc = new forms_control();
        $controls = $fc->find('all', 'forms_id=' . $this->id);
        foreach ($controls as $control) {
            $control->delete();
        }

        // delete the table for this form
        if ($this->is_saved == 1) {
            $db->dropTable("forms_" . $this->table_name);
        }
    }

}

?>