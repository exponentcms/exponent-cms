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

class textController extends expController {
	//protected $basemodel_name = '';
	public $useractions = array(
			'showall'=>'Show all',
			'showRandom'=>'Show Random Text',
	);
	
	public $remove_configs = array(
		'comments',
		'rss',
		'ealerts',
		'tags'
	); // all options: ('aggregation','files','comments','rss','ealerts','tags')
    public $codequality = 'stable';



	function displayname() { return "Text"; }
	function description() { return "Puts text on your webpages"; }
	function isSearchable() { return true; }
	
	public function showall() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
		$items = $this->text->find('all', $where, $order);
		assign_to_template(array('items'=>$items));
	}
	
	public function showRandom() {
	    expHistory::set('viewable', $this->params);
		//This is a better way to do showRandom, you can pull in random text from all over the site if you need to.
		$where = $this->aggregateWhereClause();
		$limit = isset($this->params['limit']) ? $this->params['limit'] : 1;
		$order = 'RAND()';
		assign_to_template(array('items'=>$this->text->find('all', $where, $order, $limit)));
	}
    
    public function update() {
        // update the record.
        $record = $this->text->update($this->params);
        
        // update the search table.
        navigationmodule::spiderContent();
        
        // go back to where we came from.
        expHistory::back();
    }
    
//	public function addContentToSearch() {
//	    // do nothing...this is handled by the section for now.
//	    return false;
//	}
}

?>
