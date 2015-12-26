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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_sef_urls
 */
class fix_sef_urls extends upgradescript {
	protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.2.1';
    public $optional = true;
    public $priority = 51;

	/**
	 * name/title of upgrade script
	 * @return string
	 */
	static function name() { return "Validate and fix module item sef urls"; }

	/**
	 * generic description of upgrade script
	 * @return string
	 */
	function description() { return "In some instances, the sef urls may be duplicated within a module or invalid.  This script searches for duplicate or invalid sef urls and renames them.
	This script can take a long time for a site with many items (eCommerce)!"; }

	/**
	 * additional test(s) to see if upgrade script should be run
	 * @return bool
	 */
	function needed() {
        return true; // we will always run
	}

	/**
	 * Searches for and corrects duplicate sef urls within a model
     *
	 * @return bool
	 */
	function upgrade() {
        global $db, $router;

        // first get all the system models
        $models = expModules::initializeModels();
        $fixed = 0;
        $fixed_i = 0;

        foreach ($models as $modelname=>$modelpath) {
            $model = new $modelname();
            if (property_exists($model, 'sef_url')) {
                // we need to segregate groupings for help & expCat models
                if ($model->classname == 'help' || $model->classname == 'expCat') {
                    if ($model->classname == 'help') {
                        $column = 'help_version_id';
                    } elseif ($model->classname == 'expCat') {
                        $column = 'module';
                    }
                    $groups = $model->findValue('all', $column, 1, null, true);
                    foreach ($groups as $group) {
                        $items = $model->find('all', $column . ' = ' . $group);
                        foreach ($items as $item) {
                            if ($model->classname == 'help') {
                                $opts = array('grouping_sql' => " AND help_version_id='" . $item->help_version_id . "'");
                            } elseif ($model->classname == 'expCat') {
                                $opts = array('grouping_sql' => " AND module='" . $item->module . "'");
                            }
                            if (empty($item->title)) {
                                $item->title = 'Untitled';  // fix badly mangled database from long ago
                            }
                            // check for duplicate sef url
                            if (!is_bool(expValidator::uniqueness_of('sef_url', $item, $opts))) {
                                $item->makeSefUrl();  // make a new unique sef_url from scratch
                                $item->update();
                                $fixed++;
                            }
                            // also check for valid sef url
                            if (!is_bool(expValidator::is_valid_sef_name('sef_url', $item, $opts))) {
                                $item->sef_url = $router->encode($item->sef_url);
                                // we need to test for uniqueness or update will fail
                                $dupe = $db->selectValue($model->tablename, 'sef_url', 'sef_url="'.$item->sef_url.'"' . $opts['grouping_sql']);
                        		if (!empty($dupe)) {
                        			list($u, $s) = explode(' ',microtime());
                                    $item->sef_url .= '-'.$s.'-'.$u;
                        		}
                                $item->update();
                                $fixed_i++;
                            }
                        }
                    }
                } else {
                    $items = $model->find();
                    foreach ($items as $item) {
                        if (empty($item->title)) {
                            $item->title = 'Untitled';  // fix badly mangled database from long ago
                        }
                        // check for duplicate sef url
                        if (!is_bool(expValidator::uniqueness_of('sef_url', $item, array()))) {
                            $item->makeSefUrl();  // make a new unique sef_url from scratch
                            $item->update();
                            $fixed++;
                        }
                        // also check for valid sef url
                        if (!is_bool(expValidator::is_valid_sef_name('sef_url', $item, array()))) {
                            $item->sef_url = $router->encode($item->sef_url);
                            // we need to test for uniqueness or update will fail
                            $dupe = $db->selectValue($model->tablename, 'sef_url', 'sef_url="'.$item->sef_url.'"');
                    		if (!empty($dupe)) {
                    			list($u, $s) = explode(' ',microtime());
                                $item->sef_url .= '-'.$s.'-'.$u;
                    		}
                            $item->update();
                            $fixed_i++;
                        }
                    }
                }
            }
        }

        return ($fixed_i?$fixed_i:gt('No')).' '.gt('invalid sef urls and').' '.($fixed?$fixed:gt('No')).' '.gt('duplicate sef urls were found and corrected.');
	}

}

?>
