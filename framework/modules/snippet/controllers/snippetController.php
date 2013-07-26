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
 * @subpackage Controllers
 * @package Modules
 */

class snippetController extends expController {
    public $basemodel_name = 'snippet';
	public $useractions = array(
		'showall'=>'Display Code Snippet',
//		'showall_highlight'=>'Highlight and Display Snippet'
	);
    public $remove_configs = array(
        'categories',
   		'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
   		'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Code Snippets"); }
    static function description() { return gt("Use this to easily place snippets of code, i.e. Javascript, embedded video, etc, on your site."); }
	
	public function showall() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
//		$items = $this->text->find('all', $where, $order);
		$items = $this->snippet->find('all', $where, $order);
        foreach ($items as $item) {
            $item->highlight = highlight_string($item->body, true);
        }
		assign_to_template(array(
            'items'=>$items
        ));
	}	

	public function showall_highlight() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
//		$items = $this->text->find('all', $where, $order);
		$items = $this->snippet->find('all', $where, $order);
		foreach ($items as $item) {
			$item->body = highlight_string($item->body, true); 
		}
		assign_to_template(array(
            'items'=>$items
        ));
	}

    public function update() {
        // update the record.
        $this->snippet->update($this->params);

        // go back to where we came from.
        expHistory::back();
    }

}

?>