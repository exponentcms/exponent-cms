<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expController class.
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/**
 * This is the class expController
 *
 * @subpackage Core-Controllers
 * @package Framework
 */

class expController {   
    protected $basemodel = null;
    protected $classname = '';
    protected $permissions = array('create'=>'Create', 'edit'=>'Edit', 'delete'=>'Delete', 'configure'=>'Configure', 'perms'=>'Manage Permissions', 'manage'=>'Manage Module');
    protected $remove_permissions = array();    
    protected $add_permissions = array();

	/**
	 * can this module import data?
	 * @return bool
	 */
	function canImportData() { return false; }

	/**
	 * can this module export data?
	 * @return bool
	 */
	function canExportData() { return false; }

	/**
	 * does this module require configuration?
	 * @return bool
	 */
	function requiresConfiguration() { return false; }
    
    public $requires_login = array();
    public $remove_configs = array();
    public $config = array();
    public $basemodel_name = '';
    public $model_table = '';
    public $classinfo = null;
    public $loc = null;
    public $module_name = '';
    public $filepath = '';
    public $viewpath = '';
    public $relative_viewpath = '';
	public $codequality = 'stable';

	/**
	 * @param null $src
	 * @param array $params
	 * @return expController
	 *
	 */
	function __construct($src=null, $params=array()) {
        // setup some basic information about this class
        $this->classinfo = new ReflectionClass($this);
        $this->classname = $this->classinfo->getName();
        $this->baseclassname = substr($this->classinfo->getName(), 0, -10);
        $this->filepath = __realpath($this->classinfo->getFileName());

        // figure out which "module" we belong to and setup view path information
        $controllerpath = explode('/', $this->filepath);
        $this->module_name = $controllerpath[(count($controllerpath)-2)];
       
        // set up the path to this module view files
        array_pop($controllerpath);
        $controllerpath[count($controllerpath)-1] = 'views';
        array_push($controllerpath, $this->baseclassname);
        $this->relative_viewpath = implode('/', array_slice($controllerpath, -3, 3));
        $this->viewpath = BASE.'framework/modules/'.$this->relative_viewpath;
        
        //grab the path to the module's assets
        array_pop($controllerpath);
        $controllerpath[count($controllerpath)-1] = 'assets';
        $this->asset_path = PATH_RELATIVE.'framework/'.implode('/', array_slice($controllerpath, -3, 3))."/";

        // figure out which model we're using and setup some info about it
        if (empty($this->basemodel_name)) $this->basemodel_name = get_model_for_controller($this->classname);
        $modelname = $this->basemodel_name;
        if (class_exists($modelname)) {
            $this->$modelname = new $modelname(null, false, false);
            $this->model_table = $this->$modelname->tablename;
        } else {
            $this->basemodel_name = 'expRecord';
            $this->$modelname = new expRecord(null, false, false);
            $this->model_table = null;
        }
        
        // set the location data
        $this->loc = exponent_core_makeLocation($this->baseclassname, $src, null);

        // get this controllers config data if there is any
        $config = new expConfig($this->loc);
        $this->config = $config->config;
        
        $this->params = $params;

    }

	/**
	 * name of module for backwards compat with old modules
	 * @return string
	 */
	function name() { return $this->displayname(); }
	
	/**
	 * name of module
	 * @return string
	 */
	function displayname() { return "Exponent Base Controller"; }

	/**
	 * author of module
	 * @return string
	 */
	function author() { return "Adam Kessler @ OIC Group, Inc"; }

	/**
	 * description of module
	 * @return string
	 */
	function description() { return "This is the base controller that most Exponent modules will inherit from."; }

	/**
	 * does module have sources available?
	 * @return bool
	 */
	function hasSources() { return true; }

	/**
	 * does module have views available?
	 * @return bool
	 */
	function hasViews() { return true; }

	/**
	 * does module have content available?
	 * @return bool
	 */
	function hasContent() { return true; }

	/**
	 * does module support workflow?
	 * @return bool
	 */
	function supportsWorkflow() { return false; }

	/**
	 * is module content searchable?
	 * @return bool
	 */
	function isSearchable() { return false; }

	/**
	 * glue to make module aware of itself
	 */
	function moduleSelfAwareness() {
        assign_to_template(array(
            'asset_path'=>$this->asset_path,
            'model_name'=>$this->basemodel_name,
            'table'=>$this->model_table,
            'controller'=>$this->baseclassname
        ));
    }

	/**
	 * default module view method for all items
	 */
	function showall() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = isset($this->params['order']) ? $this->params['order'] : null;
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

	/**
	 * default view for individual item
	 */
	function show() {
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
        assign_to_template(array('record'=>$record));
    }

	/**
	 * view the item by referring to its title
	 */
	function showByTitle() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        // first we'll check to see if this matches the sef_url field...if not then we'll look for the 
        // title field
        $record = $this->$modelname->find('first', "sef_url='".$this->params['title']."'");
        if (!is_object($record)) {
            $record = $this->$modelname->find('first', "title='".$this->params['title']."'");
        }
        $this->loc = unserialize($record->location_data);
        
        // adding src to template's __loc var so that our links get build correct when linking to controller actions.
        global $template;
        assign_to_template(array('record'=>$record,"__loc"=>$this->loc));
    }

	/**
	 * view a random item
	 */
	public function showRandom() {
		$where = $this->hasSources() ? $this->aggregateWhereClause() : null;
		$limit = isset($this->params['limit']) ? $this->params['limit'] : 1;
		$order = 'RAND()';
		assign_to_template(array('items'=>$this->text->find('all', $where, $order, $limit)));
	}

	/**
	 * view items referenced by tags
	 */
	function showByTags() {
        global $db;

        // set the history point for this action
        expHistory::set('viewable', $this->params);
        
        // setup some objects
        $tagobj = new expTag();
        $modelname = empty($this->params['model']) ? $this->basemodel_name : $this->params['model'];
        $model = new $modelname();

        // start building the sql query
        $sql  = 'SELECT DISTINCT m.id FROM '.DB_TABLE_PREFIX.'_'.$model->table.' m ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_'.$tagobj->attachable_table.' ct '; 
        $sql .= 'ON m.id = ct.content_id WHERE (';
        $first = true;

        if (isset($this->params['tags'])) {
            $tags = is_array($this->params['tags']) ? $this->params['tags'] : array($this->params['tags']); 
        } elseif (isset($this->config['expTags'])) {
            $tags = $this->config['expTags'];
        } else {
            $tags = array();
        }

        foreach ($tags as $tagid) {
            $sql .= ($first) ? 'exptag_id='.intval($tagid) : ' OR exptag_id='.intval($tagid);
            $first = false;
        }
        $sql .= ") AND content_type='".$model->classname."'";

        // get the objects and render the template
        $tag_assocs = $db->selectObjectsBySql($sql);
        $records = array();
        foreach ($tag_assocs as $assoc) {
            $records[] = new $modelname($assoc->id);
        }

        assign_to_template(array('items'=>$records, 'modelname'=>$modelname));
    }

	/**
	 * create an item in this module
	 */
	function create() {
        $args = array('controller'=>$this->params['controller'], 'action'=>'edit');
        //if (!empty($this->params['instance'])) $args['instance'] = $this->params['instance'];
        if (!empty($this->params['src'])) $args['src'] = $this->params['src'];
        redirect_to($args);
    }

	/**
	 * edit item in module
	 */
	function edit() {
        expHistory::set('editable', $this->params);
        $modelname = $this->basemodel_name;
        assign_to_template(array('controller'=>$this->params['controller']));
        assign_to_template(array('modelname'=>$modelname));
        $record = isset($this->params['id']) ? $this->$modelname->find($this->params['id']) : new $modelname($this->params);
        assign_to_template(array('record'=>$record, 'table'=>$this->$modelname->tablename));
    }

	/**
	 * update item in module
	 */
	function update() {
        global $db;
                
        //check for and handle tags
        if (array_key_exists('expTag',$this->params)) {
	        if (isset($this->params['id'])) {
    	        $db->delete('content_expTags', 'content_type="'.(!empty($this->params['content_type'])?$this->params['content_type']:$this->basemodel_name).'" AND content_id='.$this->params['id']);
    	    }
	        $tags = explode(",", $this->params['expTag']);
	        unset($this->params['expTag']);
	        
	        foreach($tags as $tag) {
	            $tag = strtolower(trim($tag));
	            $expTag = new expTag($tag);
	            if (empty($expTag->id)) $expTag->update(array('title'=>$tag));
	            $this->params['expTag'][] = $expTag->id;
	        }
	    }
	    
        $modelname = $this->basemodel_name;
        $this->$modelname->update($this->params);
        $this->addContentToSearch($this->params);

	    // check for eAlerts
	    if (!empty($this->params['send_ealerts'])) {
            redirect_to(array('controller'=>'ealert','action'=>'send_confirm','model'=>$modelname,'id'=>$this->$modelname->id, 'src'=>$this->loc->src,'orig_controller'=>getControllerName($this->classname)));
        } else {
            expHistory::back();
        }
    }

	/**
	 * delete item in module
	 */
	function delete() {
        $modelname = $this->basemodel_name;
        if (empty($this->params['id'])) {
	        flash('error', 'Missing id for the '.$modelname.' you would like to delete');
	        expHistory::back();
	    }
        
        $obj = new $modelname($this->params['id']);
        $rows = $obj->delete();
        
        // if this module is searchable lets delete spidered content
        if ($this->isSearchable()) {
            $search = new search();
            $content = $search->find('first', 'original_id='.$this->params['id']." AND ref_module='".$this->classname."'");
            if (!empty($content->id)) $content->delete();
        }
        
        expHistory::back();
    }

	/**
	 * rerank items in module
	 */
	function rerank() {
        $modelname = $this->basemodel_name;
        $obj = new $modelname($this->params['id']);
        $obj->rerank($this->params['push']);
        expHistory::back();
    }

	/**
	 * display module management view
	 */
	function manage() {
        expHistory::set('manageable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = isset($this->params['order']) ? $this->params['order'] : null;
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

	/**
	 * display view to rerank items
	 */
	function manage_ranks() {
        $rank = 1;
        foreach($this->params['rerank'] as $key=>$id) {
            $modelname = $this->params['model'];
            $obj = new $modelname($id);
            $obj->rank = $rank;
            $obj->save();
            $rank += 1;
        }
        
        redirect_to($this->params['lastpage']);
        
    }
    
	/**
	 * generic config action
	 */
	function configure() {
        expHistory::set('editable', $this->params);
        $pullable_modules = listInstalledControllers($this->classname, $this->loc);
        $views = get_config_templates($this, $this->loc);
        assign_to_template(array('config'=>$this->config, 'pullable_modules'=>$pullable_modules, 'views'=>$views));
    }


    // had to back out of the architecture a bit here. 
    // Attachable items are borking RSS feeds.

    // function getRSSContent() {
    //     global $db;     
    // 
    //     // setup the where clause for looking up records.
    //     $where = $this->aggregateWhereClause();
    //     
    //     // get the news items from the database
    //     $model = new $this->basemodel_name(null,false,false);
    //     //eDebug($model);
    //     
    //     $items = $model->find('all', $where);
    //     
    //     //Convert the items to rss items
    //     $rssitems = array();
    //     foreach ($items as $key => $item) { 
    //         $rss_item = new FeedItem();
    //         $rss_item->title = $item->title;
    //         $rss_item->description = $item->body;
    //         $rss_item->date = isset($item->publish_date) ? date('r',$item->publish_date) : date('r', time());
    //         $rss_item->link = makeLink(array('controller'=>$this->classname, 'action'=>'showByTitle', 'title'=>$item->sef_url));
    //         $rssitems[$key] = $rss_item;
    //     }
    //     return $rssitems;
    // }

	/**
	 * get the items in an rss feed format
	 * @return array
	 */
	function getRSSContent() {
        // this function is very general and will most of the time need to be overwritten and customized
        
        global $db;     
    
        // setup the where clause for looking up records.
        $where = $this->aggregateWhereClause();
        $items = $db->selectObjects($this->basemodel_name, $where.' ORDER BY created_at');

        //Convert the items to rss items
        $rssitems = array();
        foreach ($items as $key => $item) { 
            $rss_item = new FeedItem();
            $rss_item->title = $item->title;
            $rss_item->description = $item->body;
            $rss_item->date = isset($item->publish_date) ? date('r',$item->publish_date) : date('r', $item->created_at);
            $rss_item->link = makeLink(array('controller'=>$this->classname, 'action'=>'show', 'title'=>$item->sef_url));
            $rssitems[$key] = $rss_item;
        }
        return $rssitems;
    }

	/**
	 * save module configuration
	 */
	function saveconfig() {
        // create a new RSS object if enable is checked.
        if (!empty($this->params['enable_rss'])) {
            $rssfeed = new expRss($this->params);
            $rssfeed->update($this->params);
        }
        
        // create a new eAlerts object if enable is checked.
        if (!empty($this->params['enable_ealerts'])) {
            $ealert = new expeAlerts($this->params);
            $ealert->update($this->params);
        }
        
        // unset some unneeded params
        unset($this->params['module']);
        unset($this->params['controller']);
        unset($this->params['src']);
        unset($this->params['int']);
        unset($this->params['id']);
        unset($this->params['action']);
        unset($this->params['PHPSESSID']);
        
        // setup and save the config
        $config = new expConfig($this->loc);
        $config->update(array('config'=>$this->params));
        flash('message', 'Configuration updated');
        expHistory::back();
    }

	/**
	 * download a file attached to item
	 */
	function downloadfile() {
        global $db;    
        
        if (!isset($this->config['allowdownloads']) || $this->config['allowdownloads'] == true) { 
            //if ($db->selectObject('content_expFiles', 'content_type="'.$this->baseclassname.'" AND expfiles_id='.$this->params['id']) != null) {
                expFile::download($this->params['id']);
            //}
        } else {
            flash('error', 'Downloads have not been enabled for this file'); 
            expHistory::back();         
        }
        
    }

	/**
	 * permission functions
	 * @return array
	 */
	function permissions() {
        //set the permissions array
        $perms = array();
        foreach($this->permissions as $perm=>$name) {
            if (!in_array($perm, $this->remove_permissions)) $perms[$perm] = $name;
        }
        $perms = array_merge($perms, $this->add_permissions);
        return $perms;
    }

	/**
	 * get the models (datatypes) associated with this module
	 * @return array
	 */
	function getModels() {
        return isset($this->models) ? $this->models : array($this->basemodel_name);
    }

	/**
	 * type of items searched in the module
	 * @return string
	 */
	function searchName() {
        return $this->name();
    }

	/**
	 * category of items searched in the module
	 * @return string
	 */
	function searchCategory() {
        return $this->basemodel_name;
    }

	/**
	 * add all module items to search index
	 * @return int
	 */
	function addContentToSearch() {
        global $db, $router;
        
        $count = 0;
        $model = new $this->basemodel_name(null, false, false);
        $content = $db->selectArrays($model->tablename);
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
            $link = str_replace(URL_FULL,'', makeLink(array('controller'=>$this->baseclassname, 'action'=>'show', 'id'=>$origid, 'src'=>$src)));
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
	 * remove module items from search index
	 */
	function delete_search() {
        global $db;        
        // remove this modules entries from the search table.
        if ($this->isSearchable()) {
            $where = "ref_module='".$this->classname."' AND location_data='".serialize($this->loc)."'";            
            $test = $db->selectObjects('search', $where);
            $db->delete('search', $where);
        }
    }

	/**
	 * delete an item by instance for backwards compat with old modules
	 * @param $loc
	 */
	function delete_In($loc) { $this->delete_instance(); }

	/**
	 * delete an item by instance
	 */
	function delete_instance() {
        global $db;
        $model = new $this->basemodel_name();
        $where = null;
        if ($this->hasSources()) $where = "location_data='".serialize($this->loc)."'";
        $db->delete($model->tablename, $where);
    }

	/**
	 * get the metainfo for this module
	 * @return array
	 */
	function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action 
        // we are in.
        $action = $_REQUEST['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'');
        $modelname = $this->basemodel_name;
        switch($action) {
            case 'showall':
                $metainfo = array('title'=>"Showing all - ".$this->displayname(), 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
            break;
            case 'show':
            case 'showByTitle':
                // look up the record.
                if (isset($_REQUEST['id']) || isset($_REQUEST['title'])) {
                    $lookup = isset($_REQUEST['id']) ? $_REQUEST['id'] :$_REQUEST['title']; 
                    $object = new $modelname($lookup);
                    // set the meta info
                    if (!empty($object)) {
                        $metainfo['title'] = empty($object->meta_title) ? $object->title : $object->meta_title;
                        $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;
                        $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;
                    }              
                }
            break;
            default:
                //check for a function in the controller called 'action'_meta and use it if so
                $functionName = $action."_meta";
                $mod = new $this->classname;                
                if(method_exists($mod,$functionName))
                {
                    $metainfo = $mod->$functionName($_REQUEST);
                }                    
                else
                {
                    $metainfo = array('title'=>$this->displayname()." - ".SITE_TITLE, 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
                }
        }
        
        return $metainfo;
    }

	/**
	 * The aggregateWhereClause function creates a sql where clause which also includes aggregated module content
	 *
	 * @return string
	 */
	function aggregateWhereClause() {
        $sql = '';
        
        if (!$this->hasSources() && empty($this->config['add_source'])) { return $sql; }
        
        if (!empty($this->config['aggregate'])) $sql .= '(';
        
        $sql .= "location_data ='".serialize($this->loc)."'";
        
        if (!empty($this->config['aggregate'])) {
            foreach ($this->config['aggregate'] as $src) {
                $loc = makeLocation($this->baseclassname, $src);
                $sql .= " OR location_data ='".serialize($loc)."'";
            }
            
            $sql .= ')';
        }       
        
        return $sql;
    }
}
?>