<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * This is the class expPaginator
 * Exponent Pagination Subsystem
 *
 * The expPaginator class is used to retrieve objects from the database
 * and paginate them and optionally group the by category.
 * It automagically handles the calls to other pages
 * and has built-in sorting using the defined column headers.
 *
 * Usage Example:
 *
 * <code>
 *
 * $page = new expPaginator(array(
 *      'model'=>'faq',
 *      'where'=>1,
 *      'limit'=>25,
 *      'order'=>'rank',
 *      'controller'=>$this->baseclassname,
 *      'action'=>$this->params['action'],
 *      'columns'=>array('In FAQ'=>'include_in_faq', 'Submitted'=>'created_at', 'Submitted By'=>'submitter_name'),
 *  ));
 * </code>
 *
 * @package Subsystems
 * @subpackage Subsystems
 */

class expPaginator {
    /**#@+
     * @access public
     * @var string
     */
	public $model = null;
    public $search_string = '';
	public $sql = '';
    public $count_sql = '';
	public $where = '';
	public $controller = '';
	public $action = '';
	public $order = '';
	public $order_direction = '';
	public $firstpage = '';
	public $lastpage = '';
	public $previous_page = '';
	public $next_page = '';
	public $previous_shift = '';
	public $next_shift = '';
	public $pagelink = '';
	public $header_columns = '';
	public $default = '';
	public $view = null;
    public $uncat ='';
//    public $content_type = '';
//    public $author = '';
//    public $tag = '';
//    public $tags = '';
	/**#@+
     * @access public
     * @var integer
     */
	public $page  = 1;
//	public $limit = 10;
    public $limit = 0;
	public $start = 0;
	public $last = 0;
	public $pages_to_show = 10;
	public $total_records = 0;
	public $total_pages = 0;
	public $page_offset = 0;
    public $categorize = false;
//    public $version = 0;
//    public $content_id = 0;
	/**#@+
     * @access public
     * @var array
     */
	public $pages = array();
	public $records = array();
    public $cats = array();
    public $sort_dropdown = array();

	/**
	 * expPaginator Constructor
	 *
	 * This is the main entry point for using the expPaginator.  See example above.
	 *
	 * @param array $params Use this to set any of the class variables. Ones not passed will be set to a default.
	 * @return \expPaginator
	 */
	public function __construct($params=array()) {
		global $router, $db;

        $this->pages_to_show = expTheme::is_mobile() ? 6 : 10; // fewer paging links for small devices
		$this->where = empty($params['where']) ? null : $params['where'];
		$this->records = empty($params['records']) ? array() : $params['records'];
//		$this->limit = empty($params['limit']) ? 10 : $params['limit'];
        $this->limit = empty($params['limit']) ? 0 : intval($params['limit']);
        $this->page = empty($params['page']) ? 1 : intval($params['page']);
		$this->action = empty($params['action']) ? '' : $params['action'];
		$this->controller = empty($params['controller']) ? '' : $params['controller'];
		$this->sql = empty($params['sql']) ? '' : $params['sql'];
        $this->count_sql = empty($params['count_sql']) ? '' : $params['count_sql'];
		$this->order = empty($params['order']) ? 'id' : expString::escape($params['order']);
		$this->dir = empty($params['dir']) || !in_array($params['dir'], array('ASC', 'DESC')) ? 'ASC' : $params['dir'];
		$this->src = empty($params['src']) ? null : expString::escape($params['src']);
        $this->categorize = empty($params['categorize']) ? false : $params['categorize'];
        $this->uncat = !empty($params['uncat']) ? $params['uncat'] : gt('Not Categorized');
        $this->groups = !empty($params['groups']) ? $params['groups'] : array();
        $this->grouplimit = !empty($params['grouplimit']) ? $params['grouplimit'] : null;
        $this->dontsortwithincat = !empty($params['dontsortwithincat']) ? $params['dontsortwithincat'] : null;
        $this->dontsort = !empty($params['dontsort']) ? $params['dontsort'] : null;

		// if a view was passed we'll use it.
		if (isset($params['view']))
            $this->view = $params['view'];

        // setup the model if one was passed.
        if (isset($params['model'])) {
            $this->model = $params['model'];
            $class = new $this->model(null, false, false);
        }

	    // auto-include the CSS for pagination links
	    expCSS::pushToHead(array(
//		    "unique"=>"pagination",
//		    "link"=>PATH_RELATIVE."framework/core/assets/css/pagination.css",
            'corecss'=>'pagination'
		    )
		);

		if ($this->limit)
            $this->start = (($this->page * $this->limit) - $this->limit);
        if ($this->start < 0)
            $this->start = 0;

		//setup the columns
        $this->columns = array();
		if (isset($params['columns'])) {
		    foreach($params['columns'] as $key=>$col){
		        $colparse[$key] = explode('|',$col);
		        $column = array($key=>$colparse[$key][0]);
		        $this->columns = array_merge($this->columns,$column);
		        if (!empty($colparse[$key][1])) {
		            $params = explode(',',$colparse[$key][1]);
		            foreach ($params as $paramval) {
		                $prm = explode('=',$paramval);
		                $this->linkables[$key][$prm[0]] = $prm[1];
		            }
		        }
		    }
		}

		//setup the default ordering of records
		// if we are in an action, see if the action is for this controller/action..if so pull the order
		// and order direction from the request params...this is how the params are passed via the column
		// headers.
		$this->order_direction = $this->dir;

        // allow passing of a single order/dir as stored in config
        if (strstr($this->order," ")) {
            $orderby = explode(" ",$this->order);
            $this->order = $orderby[0];
            $this->order_direction = $orderby[1];
        }
        if(!preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $this->order))
            $this->order = 'id';
        if (!in_array($this->order_direction, array('ASC', 'DESC')))
            $this->order_direction = 'ASC';
        if ($this->dontsort)
            $sort = null;
        else
            $sort = $this->order.' '.$this->order_direction;

		// figure out how many records we're dealing with & grab the records
		//if (!empty($this->records)) { //from Merge <~~ this doesn't work. Could be empty, but still need to hit.
        if (!empty($this->categorize))
            $limit = null;
        else
            $limit = $this->limit;

		if (isset($params['records'])) { // if we pass $params['records'], we WANT to hit this
		    // sort the records that were passed in to us
            if (!empty($sort))
                usort($this->records,array('expPaginator', strtolower($this->order_direction)));
//		    $this->total_records = count($this->records);
		} elseif (!empty($class)) { //where clause     //FJD: was $this->class, but wasn't working...
			$this->total_records = $class->find('count', $this->where);
            $this->records = $class->find('all', $this->where, $sort, $limit, $this->start);
		} elseif (!empty($this->where)) { //from Merge....where clause
			$this->total_records = $class->find('count', $this->where);
            $this->records = $class->find('all', $this->where, $sort, $limit, $this->start);
		} else { //sql clause  //FIXME we don't get attachments in this approach
			//$records = $db->selectObjectsBySql($this->sql);
			//$this->total_records = count($records);
            //this is MUCH faster if you supply a proper count_sql param using a COUNT() function; if not,
            //we'll run the standard sql and do a queryRows with it
			//$this->total_records = $this->count_sql == '' ? $db->queryRows($this->sql) : $db->selectValueBySql($this->count_sql); //From Merge

//			$this->total_records =  $db->countObjectsBySql($this->count_sql); //$db->queryRows($this->sql); //From most current Trunk

            if (!empty($sort)) $this->sql .= ' ORDER BY '.$sort;
            if (!empty($this->count_sql)) $this->total_records = $db->countObjectsBySql($this->count_sql);
			if (!empty($this->limit)) $this->sql .= ' LIMIT '.$this->start.','.$this->limit;

			$this->records = array();
			if (isset($this->model) || isset($params['model_field'])) {
			    foreach($db->selectObjectsBySql($this->sql) as $record) {
                    $type = $params['model_field'];
			        $classname = isset($params['model_field']) ? $record->$type : $this->model;
			        //$this->records[] = new $classname($record->id, true, true); //From current trunk // added false, true, as we shouldn't need associated items here, but do need attached. FJD.
					$this->records[] = new $classname($record->id, false, true); //From Merge //added false, true, as we shouldn't need associated items here, but do need attached. FJD.
			    }
			} else {
			    $this->records = $db->selectObjectsBySql($this->sql);
			}
		}

        // next we'll sort them based on categories if needed
        if (!empty($this->categorize) && $this->categorize && empty($this->dontsort))
            expCatController::addCats($this->records,$sort,$this->uncat,$this->groups,$this->dontsortwithincat);

        // let's see how many total records there are
        if (empty($this->total_records))
            $this->total_records = count($this->records);
        if ($this->limit && $this->start >= $this->total_records)
            $this->start = $this->total_records - $this->limit;

        // at this point we generally have all our records, now we'll trim the records to the number requested
        //FIXME we may want some more intelligent selection here based on cats/groups, e.g., don't break groups across pages, number of picture rows, etc...
        if (empty($this->grouplimit) && ($this->limit) && count($this->records) > $this->limit)
            $this->records = array_slice($this->records, $this->start, $this->limit);
        // finally, we'll create another multi-dimensional array of categories populated with assoc items
        if (empty($this->dontsort)) {
            if (!empty($this->categorize) && $this->categorize) {
                expCatController::sortedByCats($this->records,$this->cats,$this->groups,$this->grouplimit);
            } elseif (empty($this->dontsortwithincat)) {  // categorized is off, so let's categorize by alpha instead for 'rolodex' type use
                $order = $this->order;
                if (in_array($order,array('created_at','edited_at','publish'))) {
                    if ($this->total_records && (abs($this->records[0]->$order - $this->records[count($this->records)-1]->$order)  >= (60 * 60 * 24 *365 *2))) {
                        $datetype = 'Y';  // more than 2 years of records, so break down by year
                    } else {
                        $datetype = 'M Y';  // less than 2 years of records, so break down by month/year
                    }
                    foreach ($this->records as $record) {
                        if (is_numeric($record->$order)) {
                            $title = date($datetype,$record->$order);
                            $title = empty($title)?gt('Undated'):$title;
                        } else {
                            $title = gt('Undated');
                        }
                        if (empty($this->cats[$title])) {
                            $this->cats[$title] = new stdClass();
                            $this->cats[$title]->count = 1;
                            $this->cats[$title]->name = $title;
                        } else {
                            $this->cats[$title]->count++;
                        }
                        $this->cats[$title]->records[] = $record;
                    }
                } else {
                    foreach ($this->records as $record) {
                        if (!empty($record->$order) && is_string($record->$order) && !is_numeric($record->$order)) {
                            $title = ucfirst($record->$order);
                            $title = empty($title[0])?'':$title[0];
                        } else {
                            $title = '';
                        }
                        if (empty($this->cats[$title])) {
                            $this->cats[$title] = new stdClass();
                            $this->cats[$title]->count = 1;
                            $this->cats[$title]->name = $title;
                        } else {
                            $this->cats[$title]->count++;
                        }
                        $this->cats[$title]->records[] = $record;
                    }
                }
            }
            if (!empty($this->grouplimit)) {
                if ($this->limit)
                    $this->records = array_slice($this->records, $this->start, $this->limit);
            } else {
                if ($this->limit)
                    $this->cats = array_slice($this->cats, $this->start, $this->limit);
            }
        }

        if (isset($params['records']))
            $this->runCallback(); // isset($params['records']) added to correct search for products.

        //eDebug($this->records);
		// get the number of the last record we are showing...this is used in the page links.
		// i.e.  "showing 10-19 of 57"...$this->last would be the 19 in that string
		if ($this->total_records > 0) {
			$this->firstrecord = $this->start + 1;
			$this->lastrecord = ($this->total_records < $this->limit) ? ($this->start + $this->total_records) : ($this->start + $this->limit);
			if ($this->lastrecord > $this->total_records || $this->lastrecord == 0)
                $this->lastrecord = $this->total_records;
		} else {
			$this->firstrecord = 0;
			$this->lastrecord = 0;
		}

		// get the page parameters from the router to build the links
        $page_params = $router->params;
//		$page_params = $this->cleanParams($router->params);
        foreach (array("__utma", "__utmz", "route_sanitized") as $key) {
            if (isset($page_params[$key]))
                unset($page_params[$key]);
        }
        if (!empty($page_params['search_string']))
            $page_params['search_string'] = urlencode($page_params['search_string']);

		//if (empty($page_params['module'])) $page_params['module'] = $this->controller;
		//if (empty($page_params['action'])) $page_params['action'] = $this->action;
		//if (empty($page_params['src']) && isset($params['src'])) $page_params['src'] = $params['src'];
		if (!empty($this->controller)) {
		    unset($page_params['module']);
		    $page_params['controller'] = expModules::getModuleName($this->controller);
		} else {
            if (expTheme::inAction() && empty($params)) {
                //FIXME: module/controller glue code
    //		    $mod = !empty($_REQUEST['controller']) ? expString::sanitize($_REQUEST['controller']) : expString::sanitize($_REQUEST['module']);
    //		    if ($this->controller == $mod && $this->action == $_REQUEST['action']) {
    //			    $this->order = isset($_REQUEST['order']) ? $_REQUEST['order'] : $this->order;
    //			    $this->order_direction = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : $this->dir;
    //			}
                $mod = !empty($router->params['controller']) ? $router->params['controller'] : $router->params['module'];
                if ($this->controller == $mod && $this->action == $router->params['action']) {
                    $this->order = isset($router->params['order']) ? $router->params['order'] : $this->order;
                    $this->order_direction = isset($router->params['dir']) ? $router->params['dir'] : $this->dir;
                    if(!preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', $this->order))
                        $this->order = 'id';
                    if (!in_array($this->order_direction, array('ASC', 'DESC')))
                        $this->order_direction = 'ASC';
                }
            } else {
                if (isset($params->controller)) {
                    $mod = $params->controller;
                } else {
                    $mod = '';
                }
            }
            $page_params['controller'] = $mod;  // we can't be passing an empty controller or module to the router
        }

		if (!empty($this->action))
            $page_params['action'] =  $this->action;
		if (!empty($this->src))
            $page_params['src'] =  $this->src;

		if (isset($page_params['section']))
            unset($page_params['section']);

		//build a 'more' link we can use in the headlines views.
		$this->morelink = $router->makeLink($page_params, false, false, true);

		if (!empty($this->view))
            $page_params['view'] = $this->view;

		//build a couple more links we can use in the views.
		$this->pagelink = $router->makeLink($page_params, false, false, true);

		// if don't have enough records for more than one page then we're done.
		//if ($this->total_records <= $this->limit) return true;

		$this->total_pages = ($this->limit > 0) ? ceil($this->total_records/$this->limit) : 0;

        // correct current page # to be within limits of number of pages
		if ($this->page > $this->total_pages) {
			$this->page = $this->total_pages;
            //FIXME return 404 error for infinite page scroll plugin
            if (!empty($this->total_pages)) header(':', true, 404);
		}

        // setup the previous link
        if ($this->page > 1) {
            $page_params['page'] = $this->page - 1;
            $this->previous_pagenum = $this->page - 1;
            $this->previous_page = $router->makeLink($page_params, false, false, true);
            if (expTheme::is_mobile())
                $this->pages_to_show--;
        }

        // setup the next link
        if ($this->page < $this->total_pages) {
            $page_params['page'] = $this->page + 1;
            $this->next_pagenum = $this->page + 1;
            $this->next_page = $router->makeLink($page_params, false, false, true);
            if (expTheme::is_mobile())
                $this->pages_to_show--;
        }

		//setup the pages for the links
		if ($this->total_pages > $this->pages_to_show) {
			$this->first_pagelink = max(1, floor(($this->page) - ($this->pages_to_show) / 2));
		    $this->last_pagelink = $this->first_pagelink + $this->pages_to_show - 1;
		    if ($this->last_pagelink > $this->total_pages) {
		        $this->first_pagelink = max(1, $this->total_pages - $this->pages_to_show) + 1;
		        $this->last_pagelink = $this->total_pages;
		    }
		} else {
			$this->first_pagelink = 1;
			$this->last_pagelink = $this->total_pages;
		}

		// setup the previous 10 'group jump' link
		if ($this->page > $this->pages_to_show) {
			$page_params['page'] = $this->first_pagelink - 1;
            $this->previous_shiftnum = $this->first_pagelink - 1;
        	$this->previous_shift = $router->makeLink($page_params, false, false, true);
			$page_params['page'] = 1;
			$this->firstpage = $router->makeLink($page_params, false, false, true);
		}

		// setup the next 10 'group jump' link
		if ($this->page < ($this->total_pages - $this->pages_to_show)) {
            $page_params['page'] = $this->last_pagelink + 1;
            $this->next_shiftnum = $this->last_pagelink + 1;
            $this->next_shift = $router->makeLink($page_params, false, false, true);
			$page_params['page'] = $this->total_pages;
			$this->lastpage = $router->makeLink($page_params, false, false, true);
        }

		// setup the links to the remaining pages being displayed.
		for($i=$this->first_pagelink; $i<=$this->last_pagelink; $i++) {
			$page_params['page'] = $i;
			$this->pages[$i] = $router->makeLink($page_params, false, false, true);
		}

		$links_template = expTemplate::get_common_template('pagination_links', null, 'common');
		$links_template->assign('page', $this);
		$this->links = $links_template->render();

		$this->makeHeaderCols($page_params);  // headers for table view

        $sortparams = array_merge($page_params, $router->params);

		//From Merge ****
        if (isset($router->params['page']))
            $sortparams['page'] = $router->params['page'];
        else
            unset($sortparams['page']);
        //End From Merge ****

		$this->makeSortDropdown($sortparams);  // used on non-table views

        $table_template = expTemplate::get_common_template('pagination_table', null, 'common');
        $table_template->assign('page', $this);
        $this->table = $table_template->render();  // table view

	}

	//From Merge
    private function cleanParams($params) {
        $defaultParams = array('title'=>'','module'=>'','controller'=>'','src'=>'','id'=>'','dir'=>'','_common'=>'');
        $newParams = array();
        $func = new ReflectionClass($this);
        foreach ($params as $pKey=>$pVal) {
            $propname = $pKey;
            if (array_key_exists($propname,$defaultParams)) {
                $newParams[$propname] = $params[$propname];
            }
        }
        foreach ($func->getProperties() as $p) {
            $propname = $p->name;
            if (array_key_exists($propname,$params)) {
                $newParams[$propname] = $params[$propname];
            }
        }

        return $newParams;
    }

    public function makeHeaderCols($params) {
        global $router;

        if (!empty($this->columns) && is_array($this->columns)) {
            $this->header_columns = '';

            // get the parameters used to make this page.
            if (!expTheme::inAction()) {
                unset($params['section']);
                if (empty($params['controller'])) $params['controller'] = $this->controller;
                if (empty($params['action'])) $params['action'] = $this->action;
            }

//            $current = '';
            if (isset($params['order'])) {
                $current = $params['order'];
                unset($params['order']);
            } else {
                $current = $this->order;
            }

            //loop over the columns and build out a list of <th>'s to be used in the page table
            foreach ($this->columns as $colname=>$col) {
                // if this is the column we are sorting on right now we need to setup some class info
                $class = isset($this->class) ? $this->class : 'page';
                $params['dir'] = 'ASC';

                if ($col == $current) {
                    $class  = 'current '.strtolower($this->order_direction);
                    $params['dir'] = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
                }

                $params['order'] = $col;

                $this->header_columns .= '<th class="'.$class.'">';
                // if this column is empty then it's not supposed to be a sortable column

                if (empty($col)) {
                    $this->header_columns .= '<span>'.$colname.'</span>';
                    $this->columns[$colname] = ' ';
                } else if($colname=="actupon") {
                    $this->header_columns .= '<input type=checkbox name=selall id=selall value=1 class="select-all"/>';

//                    $js = "
//                    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
//                        Y.all('input[type=checkbox]').on('click',function(e){
//                            if (e.target.test('.select-all')) {
//                                if (!e.target.get('checked')) {
//                                    this.each(function(n){
//                                        n.set('checked',false);
//                                    });
//                                } else {
//                                    this.each(function(n){
//                                        n.set('checked',true);
//                                    });
//                                };
//                            };
//                        });
//                    });
//                    ";

                    $js = "
                    $('#selall').change(function () {
                        $('input[name=\"act-upon[]\"]').prop('checked', this.checked);
                    });
                    ";

                    expJavascript::pushToFoot(array(
                        "unique"=>'select-all',
//                        "yui3mods"=>1,
                        "jquery"=>1,
                        "content"=>$js,
//                        "src"=>""
                     ));

                } else {
					unset($params['page']);  // we want to go back to the first page on a re-sort
                    if ($col == 'no-sort') {
                        $this->header_columns .= $colname;
                    } else {
                        $this->header_columns .= '<a href="'.$router->makeLink($params, false, false, true).'" alt="sort by '.$colname.'" rel="nofollow">'.$colname.'</a>';
                    }
                }

                $this->header_columns .= '</th>';
            }
        }
    }

    //here if we want to modify the record for some reason. e.g. Using in search results w/ products
    private function runCallback() {
        foreach ($this->records as &$record) {
            if (isset($record->ref_type)) {
                $refType = $record->ref_type;
                if (class_exists($record->ref_type)) {
                    $type = new $refType();
                    $classinfo = new ReflectionClass($type);
                    if ($classinfo->hasMethod('paginationCallback')) {
                        $item = new $type($record->original_id);
                        $item->paginationCallback($record);
                    }
                }
            }
        }
    }

	public function makeSortDropdown($params) {
		global $router;

		if (!empty($this->columns) && is_array($this->columns)) {
			$this->sort_dropdown = array();

			// get the parameters used to make this page.
			if (!expTheme::inAction()) {
				unset($params['section']);
				if (empty($params['controller'])) $params['controller'] = $this->controller;
				if (empty($params['action'])) $params['action'] = $this->action;
			}

			/*$current = '';
			if (isset($params['order'])) {
				$current = $params['order'];
				unset($params['order']);
			} else {
				$current = $this->order;
			}  */

			//loop over the columns and build out a list of <th>'s to be used in the page table
           // eDebug($router);
            $defaultParams['controller'] = $params['controller'];
            $defaultParams['action'] = $params['action'];
            if (isset($params['title']))
                $defaultParams['title'] = $params['title'];
            if (isset($params['page']))
                $defaultParams['page'] = $params['page'];

            $this->sort_dropdown[$router->makeLink($defaultParams, false, false, true)] = "Default";
			foreach ($this->columns as $colname=>$col) {
				// if this is the column we are sorting on right now we need to setup some class info
				/*$class = isset($this->class) ? $this->class : 'page';
				$params['dir'] = 'ASC';*/

				/*if ($col == $current) {
					$class  = 'current';
					$class .= ' '.$this->order_direction;
					if (isset($params['dir'])) {
						$params['dir'] = $params['dir'] == 'ASC' ? 'DESC' : 'ASC';
					} else {
						$params['dir'] = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
					}
				}
                */
				$params['order'] = $col;

				if (!empty($col)) {
                    if ($colname == 'Price') {
                        $params['dir'] = 'ASC';
                        $this->sort_dropdown[$router->makeLink($params, false, false, true)] = $colname . " - Lowest to Highest";
                        $params['dir'] = 'DESC';
                        $this->sort_dropdown[$router->makeLink($params, false, false, true)] = $colname . " - Highest to Lowest";
                    } else {
                        $params['dir'] = 'ASC';
                        $this->sort_dropdown[$router->makeLink($params, false, false, true)] = $colname . " - A-Z";
                        $params['dir'] = 'DESC';
                        $this->sort_dropdown[$router->makeLink($params, false, false, true)] = $colname . " - Z-A";
                    }
				}
			}
		}
	}

    /** exdoc
     * Object/Array sorting comparison function -- sorts by a specified column in ascending order.
     * @node Subsystems:expPaginator
     */
    public function asc($a,$b) {
        $col = $this->order;
        if (is_object($a)) {
            return ($a->$col < $b->$col ? -1 : 1);
        } elseif (is_array($a)) {
            return ($a[$col] < $b[$col] ? -1 : 1);
        } else {
            return ($a < $b ? -1 : 1);
        }
    }

    /** exdoc
     * Object/Array sorting comparison function -- sorts by a specified column in descending order.
     * @node Subsystems:expPaginator
     */
    public function desc($a,$b) {
        $col = $this->order;
        if (is_object($a)) {
            return ($a->$col > $b->$col ? -1 : 1);
        } elseif (is_array($a)) {
            return ($a[$col] > $b[$col] ? -1 : 1);
        } else {
            return ($a > $b ? -1 : 1);
        }
    }
}

?>