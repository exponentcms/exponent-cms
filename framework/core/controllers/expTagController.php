<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Controllers
 * @author     Adam Kessler <adam@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

class expTagController extends expController {
	//public $useractions = array('browse'=>'Browse content by tags');
	public $useractions = array();
	
	function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Tag Manager"; }
    function description() { return "This module is for manageing your tags"; }
    function author() { return "Adam Kessler @ OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }
	function isSearchable() { return false; }
	
    function manage() {
        expHistory::set('manageable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = "title";
        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->hasSources() == true ? $this->loc->src : null,
                    'columns'=>array('ID#'=>'id','Title'=>'title', 'Body'=>'body'),
                    ));
        
        assign_to_template(array('page'=>$page, 'items'=>$page->records, 'modelname'=>$modelname));
    }
}
?>