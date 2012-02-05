<?php
/**
 *  This file is part of Exponent
 * 
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expCategoryController class.
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expCategoryController
 *
 * @subpackage Core-Controllers
 * @package Framework
 */

class expCatController extends expController {
	//public $useractions = array('browse'=>'Browse content by categories');
	public $useractions = array();

	/**
	 * name of module
	 * @return string
	 */
	function displayname() { return "Category Manager"; }

	/**
	 * description of module
	 * @return string
	 */
	function description() { return "This module is for managing your categories"; }

	/**
	 * does module have sources available?
	 * @return bool
	 */
	function hasSources() { return false; }

	/**
	 * manage categories
	 */
	function manage() {
        global $db;
        expHistory::set('manageable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
//        $order = "title";
        $order = "rank";
        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>50,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->hasSources() == true ? $this->loc->src : null,
                    'columns'=>array('ID#'=>'id','Title'=>'title', 'Body'=>'body'),
                    ));

        foreach ($db->selectColumn('content_expCats','content_type',null,null,true) as $contenttype) {
            foreach ($page->records as $key => $value) {
                $attatchedat = $page->records[$key]->findWhereAttachedTo($contenttype);
                if (!empty($attatchedat)) {
                    $page->records[$key]->attachedcount = @$page->records[$key]->attachedcount + count($attatchedat);
                    $page->records[$key]->attached[$contenttype] = $attatchedat;
                }
            }
        }
        assign_to_template(array(
            'page'=>$page
        ));
    }
    function edit() {
        $modules = expModules::listControllers();
        $mod = array();
        foreach ($modules as $modname=>$mods) {
            if (!strstr($mods,'Controller')) {
                $mod[$modname] = ucfirst($modname);
            }
        }
        ksort($mod);
        assign_to_template(array(
            'mods'=>$mod
        ));
        parent::edit();
    }
}
?>
