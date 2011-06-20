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

class helpController extends expController {
	public $useractions = array('showall'=>'Show all');
	public $codequality = 'beta';

	function name() { return $this->displayname(); } //for backwards compat with old modules
	function displayname() { return "Help"; }
	function description() { return "Module for managing Exponent CMS help files."; }
	function author() { return "Adam Kessler - OIC Group, Inc"; }
	function hasSources() { return true; }
	function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }
	function isSearchable() { return true; }
	
    function __construct($src=null, $params=array()) {
        global $db;
        parent::__construct($src,$params);
        if(!empty($params['version']) && !expSession::get('help-version')) {
            expSession::set('help-version',isset($params['version']));
        } elseif (empty($params['version']) && !expSession::get('help-version')) {
    	    $version = $db->selectValue('help_version', 'version','is_current=1');
            expSession::set('help-version',$version);            
        }
        
        $this->help_version = expSession::get('help-version');
	}
	
	public function showall() {
	    expHistory::set('viewable', $this->params);
	    $hv = new help_version();
	    //$current_version = $hv->find('first', 'is_current=1');
	    $ref_version = $hv->find('first', 'version=\''.$this->help_version.'\'');

        // pagination parameter..hard coded for now.	    
		$where = $this->aggregateWhereClause();
	    $where .= 'AND help_version_id='.$ref_version->id;
	    $limit = 500;
//	    $order = 'rank';
	    $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
	    $dir   = 'ASC';
	    
	    // grab the pagination object
		$page = new expPaginator(array(
	                'model'=>'help',
	                'where'=> $where, 
	                'limit'=>$limit,
	                'order'=>$order,
	                'dir'=>$dir,
	                'controller'=>$this->baseclassname,
	                'action'=>$this->params['action'],
	                'columns'=>array('Title'=>'title', 'Body'=>'body', 'Version'=>'help_version_id'),
	                ));
	    
	    assign_to_template(array('current_version'=>$ref_version, 'page'=>$page, 'rank'=>($order==='rank')?1:0));
	}
	
	public function edit() {
	    global $db, $sectionObj;
	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $help = new help($id);
	    
	    // get the id of the current version and use it if we need to.
	    $version = $db->selectValue('help_version', 'id', 'is_current=1');
	    if (empty($help->help_version_id)) $help->help_version_id = $version;

		$sectionlist = array();
		$sections = $db->selectObjectsIndexedArray('section',1);
		$helpsections = $db->selectObjects('help',1);
		foreach ($helpsections as $helpsection) {
			if ($helpsection->location_data != null) {
				$helpsrc = expUnserialize($helpsection->location_data);
				if (!array_key_exists($helpsrc->src, $sectionlist) && $helpsection->section != 0) {
					$sectionlist[$helpsrc->src] = $sections[$helpsection->section]->name;
					if ($helpsection->section == $sectionObj->id) {
						$sectionlist[$helpsrc->src] .= " (current section)";
					}
				}
			}
		}
		$sectionlist[$this->loc->src] = $sectionObj->name." (current section)";
//	    assign_to_template(array('record'=>$help,"cursec"=>$sectionObj->id,"sections"=>$sectionlist));
	    assign_to_template(array('record'=>$help,"cursec"=>$this->loc->src,"sections"=>$sectionlist));
	}
	
	public function show() {
	    global $db;
	
	    expHistory::set('viewable', $this->params);

	    $help = new help();
	    if (empty($this->params['version'])) {
	        $version_id = $db->selectValue('help_version', 'id', 'is_current=1');
	    } else {
	        $version_id = $db->selectValue('help_version', 'id', 'version=\''.$this->params['version'].'\'');
	    }	    

	    $doc = $help->find('first', 'help_version_id='.$version_id.' AND sef_url="'.$this->params['title'].'"');
	    assign_to_template(array('doc'=>$doc,"hv"=>$this->help_version));
	}
	
	public function manage() {
	    expHistory::set('manageable', $this->params);
	    global $db;
	    
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');
	    
	    if (empty($current_version)) {
	        flash('error', "You don't have any software versions created yet.  Please do so now.");
	        redirect_to(array('controller'=>'help', 'action'=>'edit_version'));
	    }

		// for now we'll display help by section, though location_data would be more accurate
		//  this helps find problems in older help docs
	    $sections = $db->selectObjectsIndexedArray('section',1);

	    $where = empty($this->params['version']) ? 1 : 'help_version_id='.$this->params['version'];
	    $page = new expPaginator(array(
	                'model'=>'help',
	                'where'=>$where, 
	                'limit'=>30,
	                'order'=>'help_version_id',
	                'dir'=>'DESC',
	                'controller'=>$this->baseclassname,
	                'action'=>$this->params['action'],
	                'columns'=>array('Title'=>'title', 'Version'=>'help_version_id', 'Section'=>'section'),
	                ));
	    
	    assign_to_template(array('current_version'=>$current_version, 'page'=>$page, 'sections'=>$sections));
	}
	
	private static function copydocs($from, $to) {
	    global $db;
	    	    
	    $help = new help();
	    $current_docs = $help->find('all', 'help_version_id='.$from);
	    foreach ($current_docs as $key=>$doc) {
	        unset($doc->id);
	        unset($doc->id);
	        $doc->help_version_id = $to;
	        $doc->sef_url = $doc->makeSefUrl();
	        $doc->save();

	        foreach($doc->expFile as $subtype=>$files) {
	            foreach($files as $file) {
	                $doc->attachItem($file, $subtype);
	            }
	            
	        }
	    }

	    // get version #'s for the two versions
	    $oldvers = $db->selectValue('help_version', 'version', 'id='.$from);
	    $newvers = $db->selectValue('help_version', 'version', 'id='.$to);
	    
	    // send a message saying what we've done
	    flash('message', 'Copied all docs from version '.$oldvers.' to new version '.$newvers);
	    return true;
	}
	
	public function manage_versions() {
	    expHistory::set('manageable', $this->params);
	    
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');
	    
	    $sql  = 'SELECT hv.*, COUNT(h.title) AS num_docs FROM '.DB_TABLE_PREFIX.'_help h ';
	    $sql .= 'RIGHT JOIN '.DB_TABLE_PREFIX.'_help_version hv ON h.help_version_id=hv.id GROUP BY hv.version';
	    
	    $page = new expPaginator(array(
	                'sql'=>$sql, 
	                'limit'=>30,
	                'order'=>'help_version_id',
	                'dir'=>'DESC',
	                'controller'=>$this->baseclassname,
	                'action'=>$this->params['action'],
	                'columns'=>array('Version'=>'version', 'Title'=>'title', 'Current'=>'is_current', '# of Docs'=>'num_docs'),
	                ));
	    
	    assign_to_template(array('current_version'=>$current_version, 'page'=>$page));
	}
	
	public function edit_version() {
	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $version = new help_version($id);
	    assign_to_template(array('record'=>$version));
	}
	
	public function delete_version() {
	    if (empty($this->params['id'])) {
	        flash('error', 'The version you are trying to delete could not be found');
	    }
	    
	    // get the version
	    $version = new help_version($this->params['id']);
	    if (empty($version->id)) {
	        flash('error', 'The version you are trying to delete could not be found');
	    }
	    
	    // if we have errors than lets get outta here!
	    if (!expQueue::isQueueEmpty('error')) expHistory::back();
	    
	    // delete the version
	    $version->delete();
	    
	    // get and delete the docs for this version
	    $help = new help();
	    $docs = $help->find('all', 'help_version_id='.$version->id);
	    $num_docs = count($docs);
	    foreach ($docs as $doc) {
	        $doc->delete();
	    }
	    
	    flash('message', 'Deleted version '.$version->version.' and '.$num_docs.' documents that were in that version.');
	    expHistory::back();	    
	}
	
	public function update_version() {
	    global $db;
	    
	    // get the current version
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');
	    
	    // check to see if the we have a new current version and unset the old current version.
	    if (!empty($this->params['is_current'])) {
	        $db->sql('UPDATE '.DB_TABLE_PREFIX.'_help_version set is_current=0');
	    }
	    
	    // save the version
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $version = new help_version();
	    // if we dont have a current version yet we will force this one to be it
	    if (empty($current_version->id)) $this->params['is_current'] = 1;
	    $version->update($this->params);
	    
	    // if this is a new version we need to copy over docs
	    if (empty($id)) {
	        self::copydocs($current_version->id, $version->id);	        
	    }
	    
	    flash('message', 'Saved version '.$version->version);
	    expHistory::back();
	}
	
	public static function getSection($params) {
	    global $db;
	    $h = new help();
	    $hv = $db->selectValue('help_version', 'id', 'version='.$params['version']);
	    $help = $h->find('first','help_version_id='.$hv.' and sef_url=\''.$params['title'].'\'');
	    $sessec = expSession::get('last_section') ? expSession::get('last_section') : 1 ;
	    $sid = ($help->section!=0)?$help->section:$sessec;
        if (!expSession::get('last_section')) {
            expSession::set('last_section',$sid);
        }
	    $section = $db->selectObject('section','id='. intval($sid));
	    return $section;
	}
	
}

?>
