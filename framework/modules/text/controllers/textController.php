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

class textController extends expController {
	public $useractions = array(
        'showall'=>'Show all',
        'showRandom'=>'Show Random Text',
	);
	public $remove_configs = array(
        'categories',
		'comments',
        'ealerts',
        'pagination',
        'rss',
		'tags'
    );  // all options: ('aggregation','categories','comments','ealerts','files','pagination','rss','tags')

    static function displayname() { return gt("Text"); }
    static function description() { return gt("Puts text on your web pages"); }

	public function showall() {
	    expHistory::set('viewable', $this->params);
		$where = $this->aggregateWhereClause();
		$order = 'rank ASC';
		$items = $this->text->find('all', $where, $order);
        $level = 99;
        if (expSession::is_set('uilevel')) {
        	$level = expSession::get('uilevel');
        }
		assign_to_template(array(
            'items'=>$items,
            'preview'=>($level == UILEVEL_PREVIEW)  // needed for inline edit to work
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
        $this->text->update($this->params);
        
        // update the search index since text is relegated to page content.
        //FIXME need to come up with a better method
        navigationController::addContentToSearch();
        
        // go back to where we came from.
        expHistory::back();
    }

    /**
     * function to update the text item object sent via ajax
     * we only have to deal with a title and body which can be edited by ckeditor4 inline
     */
    public function saveItem() {
        $text = new text($this->params['id']);
        if ($this->params['type'] != 'revert') {
            if ($this->params['id'] != 0) {
                $prop = $this->params['type'];
                $data = !empty($this->params['value']) ? $this->params['value'] : '';
                if ($prop == 'title') $data = trim(strip_tags($data));
                $text->$prop = $data;
            } else {
                $text->title = 'title placeholder';
                $text->body = '<p>content placeholder</p>';
                $text->location_data = serialize(expCore::makeLocation('text',$this->params['src'],''));
            }
            $text->update();
            $ar = new expAjaxReply(200, gt('The text item was saved'), $text->id);
        } else {
            $ar = new expAjaxReply(200, gt('The text item was saved'), json_encode($text));
        }
        $ar->send();
    }

    /**
     * function to delete the text item object sent via ajax
     */
    public function deleteItem() {
        if (!empty($this->params['id'])) {
            $text = new text($this->params['id']);
            $text->delete();
            $ar = new expAjaxReply(200, gt('The text item was deleted'), $text->id);
            $ar->send();
        }
    }

}

?>
