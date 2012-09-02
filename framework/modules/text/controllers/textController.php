<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @subpackage Controllers
 * @package Modules
 */

class textController extends expController {
	public $useractions = array(
        'showall'=>'Show all',
        'showRandom'=>'Show Random Text',
	);
	public $remove_configs = array(
        'categories',
		'comments',
        'ealerts',
		'rss',
		'tags'
	); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    static function displayname() { return gt("Text"); }
    static function description() { return gt("Puts text on your web pages"); }

	public function showall() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
		$items = $this->text->find('all', $where, $order);
		assign_to_template(array(
            'items'=>$items
        ));
	}
	
	public function showRandom() {
	    expHistory::set('viewable', $this->params);
		//This is a better way to do showRandom, you can pull in random text from all over the site if you need to.
		$where = $this->aggregateWhereClause();
		$limit = isset($this->params['limit']) ? $this->params['limit'] : 1;
		$order = 'RAND()';
		assign_to_template(array(
            'items'=>$this->text->find('all', $where, $order, $limit)
        ));
	}
    
    public function update() {
        // update the record.
        $record = $this->text->update($this->params);
        
        // update the search index since text is relegated to page content.
        //FIXME need to come up with a better method
        navigationController::addContentToSearch();
        
        // go back to where we came from.
        expHistory::back();
    }
    
}

?>