<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

class helpController extends expController {
	public $useractions = array(
        'showall'=>'Show all',
        'select_version'=>'Select Help Version'
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

    static function displayname() { return gt("Help"); }
    static function description() { return gt("Manage Exponent CMS help files."); }
    static function isSearchable() { return true; }

    function __construct($src=null, $params=array()) {
        parent::__construct($src,$params);
        // only set the system help version if it's not already set as a session variable
        if (!expSession::is_set('help-version')) {
            $version = help_version::getCurrentHelpVersion();
            if (empty($version)) {
                // there is no help version set to 'is_current'
                $hv = new help_version();
           	    $newversion = $hv->find('first','1');
                if (!empty($newversion)) {
                    $this->params['is_current'] = 1;
             	    $newversion->update($this->params);
                    $version = $newversion->version;
                }
            }
            if(!empty($params['version'])) {
                $params['version'] = expString::escape($params['version']);
                $version = isset($params['version']) ? (($params['version'] === 'current') ? $version : $params['version']) : $version;
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
        if (empty($this->params['parent'])) {
            $where .= ' AND (parent=0 OR parent IS NULL)';
        } else {
            $where .= ' AND parent=' . (int)($this->params['parent']);
        }
//	    $limit = 999;
	    $order = isset($this->config['order']) ? $this->config['order'] : 'rank';

	    // grab the pagination object
		$page = new expPaginator(array(
            'model'=>'help',
            'where'=> $where,
//	                'limit'=>$limit,
            'order'=>$order,
            'dir'=>'ASC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title',
                gt('Details')=>'body',
                gt('Version')=>'help_version_id'
            ),
        ));

        foreach ($page->records as $key=>$doc) {
            $doc->addChildren();
        }

	    assign_to_template(array(
            'current_version'=>$ref_version,
            'page'=>$page,
            'rank'=>($order==='rank')?1:0
        ));
	}

    /**
     * Display a help document
     */
	public function show() {
	    expHistory::set('viewable', $this->params);
	    $help = new help();
        if (empty($this->params['version']) || $this->params['version'] === 'current') {
            $version_id = help_version::getCurrentHelpVersionId();
	    } else {
            $version_id = help_version::getHelpVersionId($this->params['version']);
            if (empty($version_id)) {
                $version_id = help_version::getCurrentHelpVersionId();
            }
	    }
	    $this->params['title'] = expString::escape($this->params['title']);  // escape title to prevent sql injection
	    $doc = $help->find('first', 'help_version_id='.$version_id.' AND sef_url=\''.$this->params['title'].'\'');
        if (empty($doc)) {
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));
        }
        $children = $help->find('count','parent='.$doc->id);
        $config = expConfig::getConfig($doc->location_data);

	    assign_to_template(array(
            'doc'=>$doc,
            'children'=>$children,
            "hv"=>$this->help_version,
            "hvid"=>$version_id,
            'config'=>$config
        ));
	}

    /**
     * Create or Edit a help document
     */
	public function edit() {
        global $db;

	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $help = new help($id);
        if (!empty($this->params['copy'])) {
            $help->id = null;
            if (isset($help->sef_url))
                $help->sef_url = null;
        }
        if (empty($help->id) && empty($help->loc)) {
            $help->loc = $this->loc;
        }

	    // get the id of the current version and use it if we need to.
        if (expSession::is_set('help-version')) {
            $version_id = help_version::getHelpVersionId(expSession::get('help-version'));  // version the site is currently using
        } else {
            $version_id = help_version::getCurrentHelpVersionId();
        }
	    if (empty($help->help_version_id)) $help->help_version_id = $version_id;

        $parentlist = array('0'=>'-- '.gt('Top Level Help Doc').' --');
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        $helpdocs = $help->find('all',"help_version_id=".$help->help_version_id." AND location_data='".serialize($help->loc)."'",$order);
        foreach ($helpdocs as $helpdoc) {
            $parentlist[$helpdoc->id] = $helpdoc->title;
        }

		$sectionlist = array();
//        $helpsections = $help->find('all',"help_version_id=".$help->help_version_id);
//		foreach ($helpsections as $helpsection) {
//			if (!empty($helpsection->location_data)) {
//				$helpsrc = expUnserialize($helpsection->location_data);
//				if (!array_key_exists($helpsrc->src, $sectionlist)) {
//                    $sectionlist[$helpsrc->src] = $db->selectValue('section', 'name', 'id="' . $db->selectValue('sectionref', 'section', 'module = "help" AND source="' . $helpsrc->src .'"').'"');
//				}
//			}
//		}
        $helplocs = $help->findValue('all', 'location_data', "help_version_id=" . $version_id, null, true);
        foreach ($helplocs as $helploc) {
            if (!empty($helploc)) {
                $helpsrc = expUnserialize($helploc);
                $id = $db->selectValue('sectionref', 'section', 'module = \'help\' AND source=\'' . $helpsrc->src . '\'');
                $sectionlist[$helpsrc->src] = '(' . $id . ') ' . $db->selectValue('section', 'name', 'id=' . (int)$id);
            }
        }
        $sectionlist[$this->loc->src] .= ' '.gt("(current section)");

	    assign_to_template(array(
            'record'=>$help,
            'parents'=>$parentlist,
            'parent'=>isset($this->params['parent'])?$this->params['parent']:0,
            "current_section"=>$this->loc->src,
            "sections"=>$sectionlist
        ));
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
//            $this->edit_version();
	    }

        $sections = array();
        foreach ($db->selectObjects('sectionref','module=\'help\'') as $sectionref) {
            if (!empty($sectionref->source) && empty($sections[$sectionref->source])) {
                $sections[$sectionref->source] = $db->selectValue('section', 'name', 'id=\'' . $sectionref->section .'\'');
            }
        }

        if ($this->params['version'] === 'current') {
            $this->params['version'] = $current_version->id;
        }
	    $where = empty($this->params['version']) ? 1 : 'help_version_id='.(int)($this->params['version']);
	    $page = new expPaginator(array(
            'model'=>'help',
            'where'=>$where,
//            'limit'=>30,
            'order'      => (isset($this->params['order']) ? $this->params['order'] : 'help_version_id'),
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'DESC'),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title',
                gt('Version')=>'help_version_id',
                gt('Section')=>'section',
//                gt('Location')=>'location_data'
            ),
        ));

	    assign_to_template(array(
            'current_version'=>$current_version,
            'page'=>$page,
            'sections'=>$sections
        ));
	}

    /**
     * Routine to copy all existing help docs from a version to the new version
     * @static
     * @param $from
     * @param $to
     * @return bool
     */
	private static function copydocs($from, $to) {
	    $help = new help();
        $order = 'rank DESC';
        $old_parents = help::getHelpParents($from);
        $new_parents = array();

        // copy parent help docs
	    $current_docs = $help->find('all', 'help_version_id='.$from.' AND parent=0',$order);
	    foreach ($current_docs as $doc) {
            $origid = $doc->id;
	        unset($doc->id);
	        $doc->help_version_id = $to;

//	        $tmpsef = $doc->sef_url;
//	        $doc->sef_url = "";
//	        $doc->save();
//	        $doc->sef_url = $tmpsef;
//	        $doc->do_not_validate = array('sef_url');
	        $doc->save();
            if (in_array($origid, $old_parents)) {
                $new_parents[$origid] = $doc->id;
            }

//	        $doc->sef_url = $doc->makeSefUrl();
//	        $doc->save();

	        foreach($doc->expFile as $subtype=>$files) {
	            foreach($files as $file) {
	                $doc->attachItem($file, $subtype, false);
	            }
	        }
	    }

        // copy child help docs
        $current_docs = $help->find('all', 'help_version_id='.$from.' AND parent!=0',$order);
   	    foreach ($current_docs as $key=>$doc) {
   	        unset($doc->id);
            $doc->parent = $new_parents[$doc->parent];
   	        $doc->help_version_id = $to;
   	        $doc->save();
   	        foreach($doc->expFile as $subtype=>$files) {
   	            foreach($files as $file) {
   	                $doc->attachItem($file, $subtype, false);
   	            }

   	        }
   	    }

	    // get version #'s for the two versions
        $oldvers = help_version::getHelpVersion($from);
        $newvers = help_version::getHelpVersion($to);

	    // send a message saying what we've done
	    flash('message', gt('Copied all docs from version').' '.$oldvers.' '.gt('to new version').' '.$newvers);
	    return true;
	}

    /**
     * Manage help versions
     */
	public function manage_versions() {
	    global $db;

	    expHistory::set('manageable', $this->params);

	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');

	    $sql  = 'SELECT hv.*, COUNT(h.title) AS num_docs FROM ' . $db->tableStmt('help') . ' h ';
	    $sql .= 'RIGHT JOIN ' . $db->tableStmt('help_version') . ' hv ON h.help_version_id=hv.id GROUP BY hv.version, hv.id, hv.title, hv.is_current';

	    $page = new expPaginator(array(
            'sql'=>$sql,
            'limit'=>30,
            'order'      => (isset($this->params['order']) ? $this->params['order'] : 'version'),
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'DESC'),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Version')=>'version',
                gt('Title')=>'title',
                gt('Current')=>'is_current',
                gt('# of Docs')=>'num_docs'
            ),
        ));

	    assign_to_template(array(
            'current_version'=>$current_version,
            'page'=>$page
        ));
	}

    /**
     * Create or Edit details about a help version
     */
	public function edit_version() {
	    expHistory::set('editable', $this->params);
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $version = new help_version($id);
	    assign_to_template(array(
            'record'=>$version
        ));
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

	    expSession::un_set('help-version');

	    flash('message', gt('Deleted version').' '.$version->version.' '.gt('and all documents in that version.'));
	    expHistory::back();
	}

    /**
     * Creates a new help version, possibly based on existing help version
     */
	public function update_version() {
	    // get the current version
	    $hv = new help_version();
	    $current_version = $hv->find('first', 'is_current=1');

	    // check to see if the we have a new current version and unset the old current version.
	    if (!empty($this->params['is_current'])) {
//	        $db->sql('UPDATE ' . $db->tableStmt('help_version') . ' set is_current=0');
            help_version::clearHelpVersion();
	    }
	    expSession::un_set('help-version');

	    // save the version
	    $id = empty($this->params['id']) ? null : $this->params['id'];
	    $version = new help_version();
	    // if we don't have a current version yet so we will force this one to be it
	    if (empty($current_version->id)) $this->params['is_current'] = 1;
	    $version->update($this->params);

	    // if this is a new version we need to copy over docs
	    if (empty($id)) {
	        self::copydocs($current_version->id, $version->id);
	    }
        // let's update the help search index to reflect the current help version
	    $this->addContentToSearch();

	    flash('message', gt('Saved help version').' '.$version->version);
	    expHistory::back();
	}

    /**
     * Switches current help version globally
     */
	public function activate_version() {
	    // unset the old current version.
        help_version::clearHelpVersion();
	    expSession::un_set('help-version');

		$id = $this->params['id'];
	    $version = new help_version($id);
	    $this->params['is_current'] = 1;
	    $version->update($this->params);
        // let's update the help search index to reflect the current help version
        $this->addContentToSearch();
	    flash('message', gt('Changed active help version to').' '.$version->version);
	    expHistory::back();
	}

    /**
     * Displays available help versions
     */
	public function select_version() {
  	    $hv = expSession::get('help-version');
        $selected = help_version::getHelpVersionId($hv);
        $versions = help_version::getHelpVersionsDropdown();
   	    assign_to_template(array(
               'current_version'=>$hv,
               'selected'=>$selected,
               'versions'=>$versions
           ));
	}

    /**
     * Switches current help version temporarily
     */
	public function switch_version() {
	    // unset the current version.
	    expSession::un_set('help-version');
        // set the requested version.
        $version = help_version::getHelpVersion($this->params['version']);
        expSession::set('help-version',$version);
	    flash('message', gt('Now displaying Help version').' '.$version);
        expHistory::back();
	}

    /**
   	 * add only current version of docs to search index
     *
   	 * @return int
   	 */
   	function addContentToSearch() {
        global $db;

        // let's rebuild this help search index from scratch instead of simply updating current contents
        $db->delete('search', "ref_module='" . $this->baseclassname . "'");

        // we only want the current version of help docs
        $where = 'help_version_id="'.help_version::getCurrentHelpVersionId().'"';
        $modelname = $this->basemodel_name;
//        $where .= (!empty($this->$modelname->id)) ? ' AND id=' . $this->$modelname->id : null;
        $total = $db->countObjects($this->$modelname->tablename, $where);
        $count = 0;
        for ($i = 0; $i < $total; $i += 100) {
            $orderby = 'id '. $db->limitStmt(100, $i);
            $content = $db->selectArrays($this->$modelname->tablename, $where, $orderby);
            foreach ($content as $cnt) {
                $origid = $cnt['id'];
                unset($cnt['id']);

                // get the location data for this content
//                if (isset($cnt['location_data'])) $loc = expUnserialize($cnt['location_data']);
//                $src = isset($loc->src) ? $loc->src : null;
//                $search_record = new search($cnt, false, false);
                //build the search record and save it.
                $sql = "original_id=" . $origid . " AND ref_module='" . $this->baseclassname . "'";
                $oldindex = $db->selectObject('search', $sql);
                if (!empty($oldindex)) {
                    $search_record = new search($oldindex->id, false, false);
                    $search_record->update($cnt);
                } else {
                    $search_record = new search($cnt, false, false);
                }

                $search_record->original_id = $origid;
                $search_record->posted = empty($cnt['created_at']) ? null : $cnt['created_at'];
//                $link = str_replace(URL_FULL,'', makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'title'=>$cnt['sef_url'])));
                $link = str_replace(URL_FULL, '', makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'title' => $cnt['sef_url'])));
//	              if (empty($search_record->title)) $search_record->title = 'Untitled';
                $search_record->view_link = $link;
//                $search_record->ref_module = $this->classname;
                $search_record->ref_module = $this->baseclassname;
                $search_record->category = $this->searchName();
                $search_record->ref_type = $this->searchCategory();
                $search_record->save();
                $count++;
            }
        }

        return $count;
   }

    /**
     * Hack to try and determine page which help doc is assoc with
     * @static
     * @param $params
     *
     * @return section
     */
	public static function getSection($params) {
	    global $db;

        $help = new help();
        if (empty($params['version']) || $params['version'] === 'current') {
            $version_id = help_version::getCurrentHelpVersionId();
        } else {
            $version_id = help_version::getHelpVersionId($params['version']);
            if (empty($version_id)) {
                $version_id = help_version::getCurrentHelpVersionId();
            }
        }
        $doc = $help->find('first','help_version_id='.$version_id.' and sef_url="'.$params['title'].'"');
	    $session_section = expSession::get('last_section') ? expSession::get('last_section') : 1 ;
        $help_sectionref = $db->selectObject('sectionref','module="help" AND source=\''. expUnserialize($doc->location_data)->src.'\'');
        $sid = !empty($help_sectionref) ? $help_sectionref->section : (($doc->section!=0) ? $doc->section : $session_section);
        if (!expSession::get('last_section')) {
            expSession::set('last_section',$sid);
        }
//	    $section = $db->selectObject('section','id='. (int)($sid));
        $section = new section((int)($sid));
	    return $section;
	}

}

?>