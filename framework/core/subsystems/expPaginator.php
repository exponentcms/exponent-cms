<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expPaginator class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Phillip Ball <phillip@oicgroup.net>
 * @version 2.0.0
 */
/**
 * Exponent Pagination Subsystem
 *
 * The expPaginator class is used to retrieve objects from the database
 * and paginate them.  It automagically handles the calls to other pages
 * and had built-in sorting using the defined column headers.
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
 * @subpackage Core-Subsytems
 * @package Framework
 */ 
class expPaginator {
    /**#@+
     * @access public
     * @var string
     */
	public $model = null;
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
	/**#@+
     * @access public
     * @var integer
     */
	public $page  = 1;
	public $limit = 10;
	public $start = 0;
	public $last = 0;
	public $pages_to_show = 10;
	public $total_records = 0;
	public $total_pages = 0;
	public $page_offset = 0;
	/**#@+
     * @access public
     * @var array
     */	
	public $pages = array();
	public $records = array();
	
	/**
	 * expPaginator Constructor
	 *
	 * This is the main entry point for using the expPaginator.  See example above.
	 *
	 * @param array $params Use this to set any of the class variables. Ones not passed will be set to a default.
	 * @return void
	 *
	 */
	public function __construct($params=array()) {
		global $router,$db;		
		
		$this->where = empty($params['where']) ? null : $params['where'];
		$this->records = empty($params['records']) ? array() : $params['records'];
		$this->limit = empty($params['limit']) ? 10 : $params['limit'];
		$this->page = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
		$this->action = empty($params['action']) ? '' : $params['action'];
		$this->controller = empty($params['controller']) ? '' : $params['controller'];
		$this->sql = empty($params['sql']) ? '' : $params['sql'];
        $this->count_sql = empty($params['count_sql']) ? '' : $params['count_sql'];
		$this->order = empty($params['order']) ? 'id' : $params['order'];
		$this->dir = empty($params['dir']) ? 'ASC' : $params['dir'];
		$this->src = empty($params['src']) ? null : $params['src'];
		
		// if a view was passed we'll use it.
		if (isset($params['view'])) $this->view = $params['view'];

        // setup the model if one was passed.
        if (isset($params['model'])) {
            $this->model = $params['model'];
            $class = new $this->model(null, false, false);
        }
	
	    // auto-include the CSS for pagination links
	    expCSS::pushToHead(array(
		    "unique"=>"pagination",
		    "link"=>PATH_RELATIVE."framework/core/assets/css/pagination.css",
		    )
		);
		
	
		$this->start = (($this->page * $this->limit) - $this->limit);
		
		//setup the columns and default ordering of records
		if (isset($params['columns'])) {
		    $this->columns = array();
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
		
		// if we are in an action, see if the action if for this controller/action..if so pull the order
		// and order direction from the request params...this is how the params are passed via the column
		// headers.
		$this->order_direction = $this->dir;	
		if (expTheme::inAction()) {
		    //FIXME: module/controller glue code
		    $mod = !empty($_REQUEST['controller']) ? $_REQUEST['controller'] : $_REQUEST['module'];
//		    if ($this->controller == $mod && $this->action == $_REQUEST['action']) {
			    $this->order = isset($_REQUEST['order']) ? $_REQUEST['order'] : $this->order;
			    $this->order_direction = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : $this->dir;
//			}
		} 		
		
		// figure out how many records we're dealing with & grab the records
		if (isset($params['records'])) { //PRB: we could have empty records, and need to hit this
		    // sort, count and slice the records that were passed in to us
		    usort($this->records,array('expPaginator', strtolower($this->order_direction)));
		    $this->total_records = count($this->records);
		    $this->records = array_slice($this->records, $this->start, $this->limit);
		} elseif (!empty($class)) { //where clause     //FJD: was $this->class, but wasn't working...
			$this->total_records = $class->find('count', $this->where);
			$this->records = $class->find('all', $this->where, $this->order.' '.$this->order_direction, $this->limit, $this->start);
		} else { //sql clause
			//$records = $db->selectObjectsBySql($this->sql);
			//$this->total_records = count($records);
            //this is MUCH faster if you supply a proper count_sql param using a COUNT() function; if not,
            //we'll run the standard sql and do a queryRows with it
            
//			$this->total_records = $this->count_sql == '' ? $db->queryRows($this->sql) : $db->selectValueBySql($this->count_sql);
			$this->total_records = $db->queryRows($this->sql);
			//eDebug($this->count_sql);
            if (!empty($this->order)) $this->sql .= ' ORDER BY '.$this->order.' '.$this->order_direction;
			if (!empty($this->limit)) $this->sql .= ' LIMIT '.$this->start.','.$this->limit;
			
			$this->records = array();
			if (isset($this->model) || isset($params['model_field'])) {
			    foreach($db->selectObjectsBySql($this->sql) as $record) {
			        $classname = isset($params['model_field']) ? $record->$params['model_field'] : $this->model;
			        $this->records[] = new $classname($record->id, true, true); //added false, true, as we shouldn't need associated items here, but do need attached. FJD.
			    }
			} else {
			    $this->records = $db->selectObjectsBySql($this->sql);
			}
		}	
		
        //eDebug($this->records);
		// get the number of the last record we are showing...this is used in the page links.
		// i.e.  "showing 10-19 of 57"...$this->last would be the 19 in that string
		if ($this->total_records > 0) {
			$this->firstrecord = $this->start + 1;
			$this->lastrecord = ($this->total_records < $this->limit) ? ($this->start + $this->total_records) : ($this->start + $this->limit);
			if ($this->lastrecord > $this->total_records) $this->lastrecord = $this->total_records;
		} else {
			$this->firstrecord = 0;
			$this->lastrecord = 0;
		}
			
		// get the page parameters from the router to build the links
		$page_params = $router->params;
		//if (empty($page_params['module'])) $page_params['module'] = $this->controller;
		//if (empty($page_params['action'])) $page_params['action'] = $this->action;
		//if (empty($page_params['src']) && isset($params['src'])) $page_params['src'] = $params['src'];
		if (!empty($this->controller)) {
		    unset($page_params['module']);
		    $page_params['controller'] = str_replace("Controller","",$this->controller);
		}
		
		if (!empty($this->action)) $page_params['action'] =  $this->action;
		if (!empty($this->src)) $page_params['src'] =  $this->src;
		
		if (isset($page_params['section'])) unset($page_params['section']);
		if (!empty($this->view)) $page_params['view'] = $this->view;
		
		//build out a couple links we can use in the views.		
		$this->pagelink = $router->makeLink($page_params, null, null, true);
		
		// if don't have enough records for more than one page then we're done.
		//if ($this->total_records <= $this->limit) return true;
		
		$this->total_pages = ($this->limit > 0) ? ceil($this->total_records/$this->limit) : 0;
	
		//setup the pages for the links
		if ($this->total_pages > $this->pages_to_show) {
			$this->first_pagelink = max(1, floor(($this->page) - ($this->pages_to_show) / 2));
		    $this->last_pagelink = $this->first_pagelink + $this->pages_to_show - 1;
		    if ($this->last_pagelink > $this->total_pages) {
		        $this->first_pagelink = max(1, $this->total_pages - $this->pages_to_show);
		        $this->last_pagelink = $this->total_pages; 
		    }
		} else {
			$this->first_pagelink = 1;
			$this->last_pagelink = $this->total_pages;
		}
		
		// setup the previous link
		if ($this->page > 1) {
			$page_params['page'] = $this->page - 1;
			$this->previous_page = $router->makeLink($page_params, null, null, true);
		}

		// setup the next link
		if ($this->page < $this->total_pages) {
			$page_params['page'] = $this->page + 1;
			$this->next_page = $router->makeLink($page_params, null, null, true);
		}

		// setup the previous 10 link
		if ($this->page > $this->pages_to_show) {
			$page_params['page'] = $this->first_pagelink - 1;
        	$this->previous_shift = $router->makeLink($page_params, null, null, true);
			$page_params['page'] = 1;
			$this->firstpage = $router->makeLink($page_params, null, null, true);
		}

		// setup the next 10 link
		if ($this->page < ($this->total_pages - $this->pages_to_show)) {
            $page_params['page'] = $this->last_pagelink + 1;
            $this->next_shift = $router->makeLink($page_params, null, null, true);
			$page_params['page'] = $this->total_pages;
			$this->lastpage = $router->makeLink($page_params, null, null, true);
        }

		// setup the links to the pages being displayed.
		for($i=$this->first_pagelink; $i<=$this->last_pagelink; $i++) { 
			$page_params['page'] = $i;
			$this->pages[$i] = $router->makeLink($page_params, null, null, true); 
		} 	

		$links_template = get_common_template('pagination_links', null, 'common');
		$links_template->assign('page', $this);
		$this->links = $links_template->render();
		
		$this->makeHeaderCols($page_params);
         
        $sortparams = array_merge($page_params, $router->params);
        
		$this->makeSortDropdown($sortparams);
       
        
        $table_template = get_common_template('pagination_table', null, 'common');
        $table_template->assign('page', $this);
        $this->table = $table_template->render();
       // eDebug($this);
       //eDebug($this);
                                                           
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
            
            $current = '';
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
                    $class  = 'current';
                    $class .= ' '.strtolower($this->order_direction);
                    if (isset($_REQUEST['dir'])) {
                        $params['dir'] = $_REQUEST['dir'] == 'ASC' ? 'DESC' : 'ASC';
                    } else {
                        $params['dir'] = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
                    }
                } 

                $params['order'] = $col;
                
                $this->header_columns .= '<th class="'.$class.'">';
                // if this column is empty then it's not supposed to be a sortable column

                if (empty($col)) {
                    $this->header_columns .= '<span>'.$colname.'</span>';
                    $this->columns[$colname] = ' ';
                } else if($colname=="actupon") {
                    $this->header_columns .= '<input type=checkbox name=selall value=1 class="select-all"/>';
                    
                    $js = "
                    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
                        Y.all('input[type=checkbox]').on('click',function(e){
                            if (e.target.test('.select-all')) {
                                if (!e.target.get('checked')) {
                                    this.each(function(n){
                                        n.set('checked',false);
                                    });
                                } else {
                                    this.each(function(n){
                                        n.set('checked',true);
                                    });
                                };
                            };
                        });
                    });
                    ";
                    
                    exponent_javascript_toFoot('select-all', '', null, $js, null);
                    
                } else {
                    $this->header_columns .= '<a href="'.$router->makeLink($params, null, null, true).'" alt="sort by '.$colname.'" rel="nofollow">'.$colname.'</a>';
                }
                
                $this->header_columns .= '</th>';
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
			foreach ($this->columns as $colname=>$col) {
				// if this is the column we are sorting on right now we need to setup some class info
				/*$class = isset($this->class) ? $this->class : 'page';
				$params['dir'] = 'ASC';*/
				
				/*if ($col == $current) {
					$class  = 'current';
					$class .= ' '.$this->order_direction;
					if (isset($_REQUEST['dir'])) {
						$params['dir'] = $_REQUEST['dir'] == 'ASC' ? 'DESC' : 'ASC';
					} else {
						$params['dir'] = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
					}
				} 
                */
				$params['order'] = $col;      				                        
				
				if (!empty($col)) {	
                    if ($colname == 'Price')
                    {
                        $params['dir'] = 'ASC'; 
                        $this->sort_dropdown[$router->makeLink($params, null, null, true)] = $colname . " - Lowest to Highest";
                        $params['dir'] = 'DESC'; 
                        $this->sort_dropdown[$router->makeLink($params, null, null, true)] = $colname . " - Highest to Lowest";                          
                    }else{
                        $params['dir'] = 'ASC'; 
                        $this->sort_dropdown[$router->makeLink($params, null, null, true)] = $colname . " - A-Z";
                        $params['dir'] = 'DESC';
                        $this->sort_dropdown[$router->makeLink($params, null, null, true)] = $colname . " - Z-A";
                    }	
				}                  								
			}
			
		}
	}
	
    /* exdoc
     * Object sorting comparison function -- sorts by a specified column in ascending order.
     * @node Subsystems:expPaginator
     */
    public function asc($a,$b) {
        $col = $this->order;
	    return ($a->$col < $b->$col ? -1 : 1);
    }

    /* exdoc
     * Object sorting comparison function -- sorts by a specified column in descending order.
     * @node Subsystems:expPaginator
     */
    public function desc($a,$b) {
        $col = $this->order;
	    return ($a->$col > $b->$col ? -1 : 1);
    }
}

?>
