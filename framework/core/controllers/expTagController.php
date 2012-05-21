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
 * This is the class expTagController
 *
 * @package Core
 * @subpackage Controllers
 */

class expTagController extends expController {
	//public $useractions = array('browse'=>'Browse content by tags');
	public $useractions = array();

	/**
	 * name of module
	 * @return string
	 */
	function displayname() { return gt("Tag Manager"); }

	/**
	 * description of module
	 * @return string
	 */
	function description() { return gt("This module is for managing your tags"); }

	/**
	 * does module have sources available?
	 * @return bool
	 */
	function hasSources() { return false; }

    /**
   	 * default view for individual item
   	 */
   	function show() {
       global $db;
       expHistory::set('viewable', $this->params);
       $modelname = $this->basemodel_name;

       // figure out if we're looking this up by id or title
       $id = null;
       if (isset($this->params['id'])) {
           $id = $this->params['id'];
       } elseif (isset($this->params['title'])) {
           $id = $this->params['title'];
       }

       $record = new $modelname($id);
       foreach ($db->selectColumn('content_expTags','content_type',null,null,true) as $contenttype) {
              $attatchedat = $record->findWhereAttachedTo($contenttype);
              if (!empty($attatchedat)) {
                  $record->attachedcount = @$record->attachedcount + count($attatchedat);
                  $record->attached[$contenttype] = $attatchedat;
              }
       }

       assign_to_template(array('record'=>$record));
    }

	/**
	 * manage tags
	 */
	function manage() {
        global $db;
        expHistory::set('manageable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $order = "title";
        $page = new expPaginator(array(
                    'model'=>$modelname,
                    'where'=>$where, 
                    'limit'=>50,
                    'order'=>$order,
                    'controller'=>$this->baseclassname,
                    'action'=>$this->params['action'],
                    'src'=>$this->hasSources() == true ? $this->loc->src : null,
                    'columns'=>array(gt('ID#')=>'id',gt('Title')=>'title',gt('Body')=>'body'),
                    ));

        foreach ($db->selectColumn('content_expTags','content_type',null,null,true) as $contenttype) {
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
        
        assign_to_template(array('page'=>$page));
    }

}

?>