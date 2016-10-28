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
 * This is the class expCatController
 *
 * @package Core
 * @subpackage Controllers
 */

class expCatController extends expController {
    protected $manage_permissions = array(
        'change' => 'Change Cats'
    );

	/**
	 * name of module
	 * @return string
	 */
    static function displayname() { return gt("Category Manager"); }

	/**
	 * description of module
	 * @return string
	 */
    static function description() { return gt("This module is used to manage categories"); }

    /**
   	 * author of module
   	 * @return string
   	 */
    static function author() { return "Dave Leffler"; }

	/**
	 * does module have sources available?
	 * @return bool
	 */
	static function hasSources() { return false; }

	/**
	 * manage categories
	 */
	function manage() {
//        global $db;

        expHistory::set('manageable', $this->params);
        if (!empty($this->params['model'])) {
//            $modulename = expModules::getControllerClassName($this->params['model']);
//            $module = new $modulename(empty($this->params['src'])?null:$this->params['src']);
            $module = expModules::getController($this->params['model'], empty($this->params['src']) ? null : $this->params['src']);
            $where = $module->aggregateWhereClause();
            if ($this->params['model'] == 'file') $where = 1;
            $page = new expPaginator(array(
                'model'=>($this->params['model'] == 'file') ? 'expFile' : $this->params['model'],
//                        'where'=>"location_data='".serialize(expCore::makeLocation($this->params['model'],$this->loc->src,''))."'",
                'where'=>$where,
//                        'order'=>'module,rank',
                'categorize'=>true,
                'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
                'controller'=>$this->params['model'],
//                        'action'=>$this->params['action'],
//                        'src'=>static::hasSources() == true ? $this->loc->src : null,
//                        'columns'=>array(gt('ID#')=>'id',gt('Title')=>'title',gt('Body')=>'body'),
            ));
            if ($this->params['model'] == 'faq') {
                foreach ($page->records as $record) {
                    $record->title = $record->question;
                }
            } elseif ($this->params['model'] == 'file') {
                foreach ($page->records as $record) {
                    $record->title = $record->filename;
                }
            }
        } else $page = '';
        $cats = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>empty($this->params['model']) ? null : "module='".$this->params['model']."'",
//            'limit'=>50,
            'order'=>'module,rank',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>static::hasSources() == true ? $this->loc->src : null,
            'columns'=>array(
                gt('ID#')=>'id',
                gt('Title')=>'title',
//                gt('Body')=>'body'
            ),
        ));

//        foreach ($db->selectColumn('content_expCats','content_type',null,null,true) as $contenttype) {
        foreach (expCat::selectAllCatContentType() as $contenttype) {
            foreach ($cats->records as $key => $value) {
                $attatchedat = $cats->records[$key]->findWhereAttachedTo($contenttype);
                if (!empty($attatchedat)) {
                    $cats->records[$key]->attachedcount = @$cats->records[$key]->attachedcount + count($attatchedat);
                    $cats->records[$key]->attached[$contenttype] = $attatchedat;
                    //FIXME here is a hack to get the faq to be listed
                    if ($contenttype == 'faq' && !empty($cats->records[$key]->attached[$contenttype][0]->question)) {
                        $cats->records[$key]->attached[$contenttype][0]->title = $cats->records[$key]->attached[$contenttype][0]->question;
                    }
                }
            }
        }
        foreach ($cats->records as $record) {
            $cats->modules[$record->module][] = $record;
        }
        if (SITE_FILE_MANAGER == 'elfinder') {
            unset($cats->modules['file']);  // we're not using the traditional file manager
        }
        if (!empty($this->params['model']) && $this->params['model'] == 'file') {
            $catlist[0] = gt('Root Folder');
        } else {
            $catlist[0] = gt('Uncategorized');
        }
        if (!empty($cats->modules)) foreach ($cats->modules as $key=>$module) {
            foreach ($module as $listing) {
                $catlist[$listing->id] = $listing->title;
            }
        }
        assign_to_template(array(
            'catlist'=>$catlist,
            'cats'=>$cats,
            'page'=>$page,
            'model'=>empty($this->params['model']) ? null : $this->params['model']
        ));
    }

    function edit() {
        $mod = array();
//        $modules = expModules::listControllers();
//        foreach ($modules as $modname=>$mods) {
//            if (!strstr($mods,'Controller')) {
//                $mod[$modname] = ucfirst($modname);
//            }
//        }
        $modules = expModules::listUserRunnableControllers();
        foreach ($modules as $modname) {
//            $modname = expModules::getControllerName($modname);
            $mod[expModules::getControllerName($modname)] = ucfirst(expModules::getControllerDisplayName($modname));
        }
        $mod['expFile'] = 'File';
        asort($mod);
        assign_to_template(array(
            'mods'=>$mod
        ));
        if (!empty($this->params['model'])) {
            assign_to_template(array(
                'model'=>$this->params['model'],
            ));
        }
        parent::edit();
    }

    function update() {
        if ($this->params['module'] == 'expFile') $this->params['module'] = 'file';
        parent::update();
    }

    /**
     * this method changes the category of the selected items to the chosen category
     */
    function change_cats() {
        if (!empty($this->params['change_cat'])) {
            foreach ($this->params['change_cat'] as $item) {
                $classname = $this->params['mod'];
                $object = new $classname($item);
                $params['expCat'][0] = $this->params['newcat'];
                $object->update($params);
            }
        }
        expHistory::returnTo('viewable');
    }

    /**
     * this method adds cats properties to object and then sorts by category
     *  it is assumed the records have expCats attachments, even if they are empty
     *
     * @static
     * @param array  $records
     * @param string $order      sort order/dir for items
     * @param string $uncattitle name to use for uncategorized group
     * @param array  $groups     limit set to these groups only if set
     * @param bool   $dontsort
     *
     * @return void
     */
    public static function addCats(&$records,$order,$uncattitle,$groups=array(),$dontsort=false) {
        if (empty($uncattitle)) $uncattitle = gt('Not Categorized');
        foreach ($records as $key=>$record) {
            foreach ($record->expCat as $cat) {
                $records[$key]->catid = $cat->id;
                $records[$key]->catrank = $cat->rank;
                $records[$key]->cat = $cat->title;
                $catcolor = empty($cat->color) ? null : trim($cat->color);
                if (substr($catcolor,0,1)=='#') $catcolor = '" style="color:'.$catcolor.';';
                $records[$key]->color = $catcolor;
                $records[$key]->module = empty($cat->module) ? null : $cat->module;
                break;
            }
            if (empty($records[$key]->catid)) {
                $records[$key]->catid = 0;
                $records[$key]->expCat[0] = new stdClass();
                $records[$key]->expCat[0]->id = 0;
                $records[$key]->catrank = 9999;
                $records[$key]->cat = $uncattitle;
                $records[$key]->color = null;
            }
            if (!empty($groups) && !in_array($records[$key]->catid,$groups)) {
                unset ($records[$key]);
            }
        }
        // we don't always want to sort  by cat first
        if (!$dontsort) {
            $orderby = explode(" ",$order);
            $order = $orderby[0];
            $order_direction = !empty($orderby[1]) && $orderby[1] == 'DESC' ? SORT_DESC : SORT_ASC;
            expSorter::osort($records, array('catrank',$order => $order_direction));
        }
    }

    /**
     * this method fills a multidimensional array from a sorted records object
     *  it is assumed the records object is already processed by expCatController::addCats
     *
     * @static
     * @param array $records
     * @param array $cats array of site category objects
     * @param array $groups limit set to these groups only if set
     * @param null $grouplimit
     * @return void
     */
    public static function sortedByCats($records,&$cats,$groups=array(),$grouplimit=null) {
        foreach ($records as $record) {
            if (empty($groups) || in_array($record->catid,$groups)) {
                if (empty($record->catid)) $record->catid = 0;
                if (empty($cats[$record->catid])) {
                    $cats[$record->catid] = new stdClass();
                    $cats[$record->catid]->count = 1;
                    $cats[$record->catid]->name = $record->cat;
                    $cats[$record->catid]->color = $record->color;
                } else {
                    $cats[$record->catid]->count++;
                }
                if (empty($grouplimit)) {
                    $cats[$record->catid]->records[] = $record;
                } else {
                    if (empty($cats[$record->catid]->records[0])) $cats[$record->catid]->records[0] = $record;
                }
            }
        }
    }

}

?>