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
	public $useractions = array(
        'showall'=>'Show all',
        'select_version'=>'Select Help Version'
    );

	function displayname() { return "Help"; }
	function description() { return "Module for managing Exponent CMS help files."; }
	function isSearchable() { return true; }
	
    function __construct($src=null, $params=array()) {
        global $db;
        parent::__construct($src,$params);
        // only set the system help version if it's not already set as a session variable
        if (!expSession::is_set('help-version')) {
            $version = $db->selectValue('help_version', 'version','is_current=1');
            if(!empty($params['version'])) {
                $version = isset($params['version']) ? (($params['version'] == 'current') ? $version : $params['version']) : $version;
            }
            expSession::set('help-version',$version);
        }
        $this->help_version = expSession::get('help-version');
	}

    /**
     * Display list of help documents
     */
	public function showall() {
	    expHistory::set('viewable', $this->params);
	    $hv = new help_version();
	    //$current_version = $hv->find('first', 'is_current=1');
	    $ref_version = $hv->find('first', 'version=\''.$this->help_version.'\'');

        // pagination parameter..hard coded for now.	    
		$where = $this->aggregateWhereClause();
	    $where .= 'AND help_version_id='.(empty($ref_version->id)?'0':$ref_version->id);
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

    /**
     * Create or Edit a help document
     */
	public function edit() {
	    global $db, $sectionObj;
	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $help = new help($id);
	    
	    // get the id of the current version and use it if we need to.
        if (expSession::is_set('help-version')) {
            $version = expSession::get('help-version');  // version the site is currently using
        } else {
            $version = $db->selectValue('help_version', 'id', 'is_current=1');
        }
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

    /**
     * Display a help document
     */
	public function show() {
	    global $db;
	
	    expHistory::set('viewable', $this->params);

	    $help = new help();
        if (empty($this->params['version']) || $this->params['version'] == 'current') {
	        $version_id = $db->selectValue('help_version', 'id', 'is_current=1');
	    } else {
	        $version_id = $db->selectValue('help_version', 'id', 'version=\''.$this->params['version'].'\'');
            if (empty($version_id)) {
                $version_id = $db->selectValue('help_version', 'id', 'is_current=1');
            }
	    }
	    $doc = $help->find('first', 'help_version_id='.$version_id.' AND sef_url="'.$this->params['title'].'"');
	    assign_to_template(array('doc'=>$doc,"hv"=>$this->help_version));
	}

    /**
     * Manage help documents
     */
	public function manage() {
	    expHistory::set('manageable', $this->params);
	    global $db;
	    
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');
	    
	    if (empty($current_version)) {
	        flash('error', gt("You don't have any software versions created yet.  Please do so now."));
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

    /**
     * Routine to copy all existing help docs from a version to the new version
     * @static
     * @param $from
     * @param $to
     * @return bool
     */
	private static function copydocs($from, $to) {
	    global $db;
	    	    
	    $help = new help();
	    $current_docs = $help->find('all', 'help_version_id='.$from);
	    foreach ($current_docs as $key=>$doc) {
	        unset($doc->id);
	        $doc->help_version_id = $to;
		    
//	        $tmpsef = $doc->sef_url;
//	        $doc->sef_url = "";
//	        $doc->save();
//	        $doc->sef_url = $tmpsef;
//	        $doc->do_not_validate = array('sef_url');
	        $doc->save();
		    
//	        $doc->sef_url = $doc->makeSefUrl();
//	        $doc->save();

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
	    flash('message', gt('Copied all docs from version').' '.$oldvers.' '.gt('to new version').' '.$newvers);
	    return true;
	}

    /**
     * Manage help versions
     */
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

    /**
     * Create or Edit details about a help version
     */
	public function edit_version() {
	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $version = new help_version($id);
	    assign_to_template(array('record'=>$version));
	}

    /**
     * Delete a help version and all assoc docs
     */
	public function delete_version() {
	    if (empty($this->params['id'])) {
	        flash('error', gt('The version you are trying to delete could not be found'));
	    }
	    
	    // get the version
	    $version = new help_version($this->params['id']);
	    if (empty($version->id)) {
	        flash('error', gt('The version you are trying to delete could not be found'));
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
	    expSession::un_set('help-version');

	    flash('message', gt('Deleted version').' '.$version->version.' '.gt('and').' '.$num_docs.' '.gt('documents that were in that version.'));
	    expHistory::back();	    
	}

    /**
     * Creates a new help version, possibly based on existing help version
     */
	public function update_version() {
	    global $db;
	    
	    // get the current version
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');
	    
	    // check to see if the we have a new current version and unset the old current version.
	    if (!empty($this->params['is_current'])) {
//	        $db->sql('UPDATE '.DB_TABLE_PREFIX.'_help_version set is_current=0');
		    $db->toggle('help_version',"is_current",'is_current=1');
	    }
	    expSession::un_set('help-version');

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

	    flash('message', gt('Saved help version').' '.$version->version);
	    expHistory::back();
	}

    /**
     * Switches current help version globally
     */
	public function activate_version() {
	    global $db;

	    // unset the old current version.
	    $db->toggle('help_version',"is_current",'is_current=1');
	    expSession::un_set('help-version');

		$id = $this->params['id'];
	    $version = new help_version($id);
	    $this->params['is_current'] = 1;
	    $version->update($this->params);

	    flash('message', gt('Changed active help version to').' '.$version->version);
	    expHistory::back();
	}

    /**
     * Displays available help versions
     */
	public function select_version() {
        global $db;

  	    $hv = expSession::get('help-version');
        $selected = $db->selectValue('help_version', 'id', 'version="'.$hv.'"');
   	    $versions = $db->selectDropdown('help_version','version',1,'version');
   	    assign_to_template(array('current_version'=>$hv, 'selected'=>$selected, 'versions'=>$versions));
	}

    /**
     * Switches current help version temporarily
     */
	public function switch_version() {
        global $db;

	    // unset the current version.
	    expSession::un_set('help-version');
        // set the requested version.
        $version = $db->selectValue('help_version','version','id="'.$this->params['version'].'"');
        expSession::set('help-version',$version);
	    flash('message', gt('Now displaying Help version').' '.$version);
        expHistory::back();
	}

    /**
   	 * add only current version of docs to search index
   	 * @return int
   	 */
   	function addContentToSearch() {
       global $db, $router;

       $count = 0;
       $model = new $this->basemodel_name(null, false, false);
       $content = $db->selectArrays($model->tablename,'help_version_id="'.$db->selectValue('help_version','id','is_current=1').'"');
       foreach ($content as $cnt) {
           $origid = $cnt['id'];
           unset($cnt['id']);

           // get the location data for this content
           if (isset($cnt['location_data'])) $loc = expUnserialize($cnt['location_data']);
           $src = isset($loc->src) ? $loc->src : null;

           //build the search record and save it.
           $search_record = new search($cnt, false, false);
           $search_record->original_id = $origid;
           $search_record->posted = empty($cnt['created_at']) ? null : $cnt['created_at'];
           $link = str_replace(URL_FULL,'', makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$cnt['sef_url'])));
//	        if (empty($search_record->title)) $search_record->title = 'Untitled';
           $search_record->view_link = $link;
           $search_record->ref_module = $this->classname;
           $search_record->category = $this->searchName();
           $search_record->ref_type = $this->searchCategory();
           $search_record->save();
           $count += 1;
        }

        return $count;
   }

    /**
     * Hack to try and determine page which help doc is assoc with
     * @static
     * @param $params
     * @return null|void
     */
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
