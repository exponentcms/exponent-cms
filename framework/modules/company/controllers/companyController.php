<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class companyController extends expController {
	public $useractions = array('showall'=>'Show all');
	public $codequality = 'beta';

	public $remove_configs = array(
        'aggregretion',
        'comments',
        //'files',
        'rss',
        'tags'
    );

	function name() { return $this->displayname(); } //for backwards compat with old modules
	function displayname() { return "Company Listings"; }
	function description() { return "This module shows company listings"; }
	function author() { return "Adam Kessler - OIC Group, Inc"; }
	function hasSources() { return false; }
	function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }	
	
	function showall() {
        expHistory::set('viewable', $this->params);
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = isset($this->params['order']) ? $this->params['order'] : 'rank';
        $page = new expPaginator(array(
                    'model'=>$this->basemodel_name,
                    'where'=>1, 
                    'limit'=>$limit,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'columns'=>array('Title'=>'title', 'Link'=>'website'),
                    ));
        
        assign_to_template(array('page'=>$page, 'items'=>$page->records));
    }
}

?>
