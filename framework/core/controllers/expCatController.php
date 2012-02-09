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
                    //FIXME here is a hack to get the faq to be listed
                    if ($contenttype == 'faq' && !empty($page->records[$key]->attached[$contenttype][0]->question)) {
                        $page->records[$key]->attached[$contenttype][0]->title = $page->records[$key]->attached[$contenttype][0]->question;
                    }
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
        asort($mod);
        assign_to_template(array(
            'mods'=>$mod
        ));
        parent::edit();
    }

    /**
     * this method adds cats properties to object and then sorts by category
     *  it is assumed the records have expCats attachments, even if they are empty
     *
     * @static
     * @param $records
     * @param $order
     */
    public static function addCats(&$records,$order) {
        foreach ($records as $key=>$record) {
            foreach ($record->expCat as $cat) {
                $records[$key]->catid = $cat->id;
                $records[$key]->catrank = $cat->rank;
                $records[$key]->cat = $cat->title;
                $records[$key]->color = empty($cat->color) ? null : $cat->color;
                $records[$key]->module = empty($cat->module) ? null : $cat->module;
                break;
            }
            if (empty($records[$key]->catid)) {
                $records[$key]->catid = null;
                $records[$key]->catrank = 9999;
                $records[$key]->cat = 'Not Categorized';
            }
        }
        $orderby = explode(" ",$order);
        $order = $orderby[0];
        $order_direction = $orderby[1] == 'DESC' ? SORT_DESC : SORT_ASC;
        expSorter::osort($records, array('catrank',$order => $order_direction));
    }

    /**
     * this method fills an multidimensional array from a sorted records object
     *  it is assumed the records object came from expCatController::addCats
     *
     * @static
     * @param $records
     * @param $cats
     */
    public static function sortedByCats($records,&$cats) {
        foreach ($records as $record) {
            if (empty($record->catid)) $record->catid = 0;
            if (empty($cats[$record->catid])) {
                $cats[$record->catid]->count = 1;
                $cats[$record->catid]->name = $record->cat;
            } else {
                $cats[$record->catid]->count += 1;
            }
            $cats[$record->catid]->records[] = $record;
        }
    }

}
?>
