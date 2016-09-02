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
 * @subpackage Controllers
 * @package    Modules
 */
/** @define "BASE" "../../../.." */

class storeController extends expController {
    public $basemodel_name = 'product';

    public $useractions = array(
        'showall'                         => 'Products - All Products and Categories',
        'showallFeaturedProducts'         => 'Products - Only Featured',
        'showallCategoryFeaturedProducts' => 'Products - Featured Products under current category',
        'showallManufacturers'            => 'Products - By Manufacturer',
        'showTopLevel'                    => 'Product Categories Menu - Show Top Level',
        'showFullTree'                    => 'Product Categories Menu - Show Full Tree',  //FIXME image variant needs a separate method
        'showallSubcategories'            => 'Product Categories Menu - Subcategories of current category',
//        'upcomingEvents'                  => 'Event Registration - Upcoming Events',
//        'eventsCalendar'                  => 'Event Registration - Calendar View',
        'ecomSearch'                      => 'Product Search - Autocomplete',
        'searchByModel'                   => 'Product Search - By Model',
        'quicklinks'                      => 'Links - User Links',
        'showGiftCards'                   => 'Gift Cards UI',
    );

    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
//        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    //protected $permissions = array_merge(array("test"=>'Test'), array('copyProduct'=>"Copy Product"));
    protected $add_permissions = array(
        'copyProduct'                 => "Copy Product",
        'delete_children'             => "Delete Children",
        'reimport'                    => 'ReImport Products',
        'findDupes'                   => 'Fix Duplicate SEF Names',
        'manage_sales_reps'           => 'Manage Sales Reps',
        'batch_process'               => 'Batch capture order transactions',
        'process_orders'              => 'Batch capture order transactions',
        'import_external_addresses'   => 'Import addresses from other sources',
        'showallImpropercategorized'  => 'View products in top level categories that should not be',
        'showallUncategorized'        => 'View all uncategorized products',
        'nonUnicodeProducts'          => 'View all non-unicode charset products',
        'cleanNonUnicodeProducts'     => 'Clean all non-unicode charset products',
        'uploadModelAliases'          => 'Upload model aliases',
        'processModelAliases'         => 'Process uploaded model aliases',
        'saveModelAliases'            => 'Save uploaded model aliases',
        'deleteProcessedModelAliases' => 'Delete processed uploaded model aliases',
        'delete_model_alias'          => 'Process model aliases',
        'update_model_alias'          => 'Save model aliases',
        'edit_model_alias'            => 'Delete model aliases',
        'import'                      => 'Import Products',
        'export'                      => 'Export Products',
    );

    static function displayname() {
        return gt("e-Commerce Store Front");
    }

    static function description() {
        return gt("Displays the products and categories in your store");
    }

    static function author() {
        return "OIC Group, Inc";
    }

    static function isSearchable() {
        return true;
    }

    public function searchName() {
        return gt('e-Commerce Item');
    }

    static function canImportData() {
        return true;
    }

    static function canExportData() {
        return true;
    }

    function __construct($src = null, $params = array()) {
        global $db, $router, $section, $user;
//        parent::__construct($src = null, $params);
        if (empty($params)) {
            $params = $router->params;
        }
        parent::__construct($src, $params);

        // we're setting the config here from the module and globally
        $this->grabConfig();

//        if (expTheme::inAction() && !empty($router->url_parts[1]) && ($router->url_parts[0] == "store" && $router->url_parts[1] == "showall")) {
        if (!empty($params['action']) && ($params['controller'] == "store" && $params['action'] == "showall") ) {
//            if (isset($router->url_parts[array_search('title', $router->url_parts) + 1]) && is_string($router->url_parts[array_search('title', $router->url_parts) + 1])) {
            if (isset($params['title']) && is_string($params['title'])) {
//                $default_id = $db->selectValue('storeCategories', 'id', "sef_url='" . $router->url_parts[array_search('title', $router->url_parts) + 1] . "'");
//                $active     = $db->selectValue('storeCategories', 'is_active', "sef_url='" . $router->url_parts[array_search('title', $router->url_parts) + 1] . "'");
                $default_id = $db->selectValue('storeCategories', 'id', "sef_url='" . $params['title'] . "'");
                $active = $db->selectValue('storeCategories', 'is_active', "sef_url='" . $params['title'] . "'");
                if (empty($active) && !$user->isAdmin()) {
                    redirect_to(array("section" => SITE_DEFAULT_SECTION)); // selected category is NOT active
                }
            } elseif (isset($this->config['category'])) { // the module category to display
                $default_id = $this->config['category'];
            } else {
                $default_id = 0;
            }
//        } elseif (expTheme::inAction() && !empty($router->url_parts[1]) && ($router->url_parts[0] == "store" && ($router->url_parts[1] == "show" || $router->url_parts[1] == "showByTitle"))) {
        } elseif (!empty($params['action']) && ($params['controller'] == "store" && ($params['action'] == "show" || $params['action'] == "showByTitle" || $params['action'] == "categoryBreadcrumb"))) {
//            if (isset($router->url_parts[array_search('id', $router->url_parts) + 1]) && ($router->url_parts[array_search('id', $router->url_parts) + 1] != 0)) {
            if (!empty($params['id'])) {
//                $default_id = $db->selectValue('product_storeCategories', 'storecategories_id', "product_id='" . $router->url_parts[array_search('id', $router->url_parts) + 1] . "'");
                $default_id = $db->selectValue('product_storeCategories', 'storecategories_id', "product_id='" . $params['id'] . "'");
            } elseif (!empty($params['title'])) {
//                $prod_id    = $db->selectValue('product', 'id', "sef_url='" . $router->url_parts[array_search('title', $router->url_parts) + 1] . "'");
                $prod_id = $db->selectValue('product', 'id', "sef_url='" . $params['title'] . "'");
                $default_id = $db->selectValue('product_storeCategories', 'storecategories_id', "product_id='" . $prod_id . "'");
            }
        } elseif (isset($this->config['show_first_category']) || (!expTheme::inAction() && $section == SITE_DEFAULT_SECTION)) {
            if (!empty($this->config['show_first_category'])) {
                $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
            } else {
                $default_id = null;
//                flash('error','store-show first category empty, but default seciton');
            }
        } elseif (!isset($this->config['show_first_category']) && !expTheme::inAction()) {
            $default_id = null;
//            flash('error','store-don\'t show first category empty');
        } else {
            $default_id = null;
        }
//        if (empty($default_id)) $default_id = 0;
        if (!is_null($default_id)) expSession::set('catid', $default_id);

        // figure out if we need to show all categories and products or default to showing the first category.
        // elseif (!empty($this->config['category'])) {
        //     $default_id = $this->config['category'];
        // } elseif (ecomconfig::getConfig('show_first_category')) {
        //     $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
        // } else {
        //     $default_id = 0;
        // }

        $this->parent = expSession::get('catid');
        $this->category = new storeCategory($this->parent);
        if ($this->parent) { // we're setting the config here for the category
            $this->grabConfig($this->category);
        }
    }

    function showall() {
        global $db, $user, $router;

        expHistory::set('viewable', $this->params);

        if (empty($this->category->is_events)) {
            $count_sql_start = 'SELECT COUNT(DISTINCT p.id) as c FROM ' . $db->prefix . 'product p ';

            $sql_start = 'SELECT DISTINCT p.*, IF(base_price > special_price AND use_special_price=1,special_price, base_price) as price FROM ' . $db->prefix . 'product p ';
            $sql = 'JOIN ' . $db->prefix . 'product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'WHERE ';
            if (!$user->isAdmin()) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';
            $sql .= 'sc.storecategories_id IN (';
            $sql .= 'SELECT id FROM ' . $db->prefix . 'storeCategories WHERE rgt BETWEEN ' . $this->category->lft . ' AND ' . $this->category->rgt . ')';

            $count_sql = $count_sql_start . $sql;
            $sql = $sql_start . $sql;

//            $order = 'title'; // $order = 'sc.rank'; //$this->config['orderby'];
//            $dir = 'ASC'; //$this->config['orderby_dir'];
            $order = !empty($this->params['order']) ? $this->params['order'] : $this->config['orderby'];
            $dir = !empty($this->params['dir']) ? $this->params['dir'] : $this->config['orderby_dir'];
            if (empty($order)) $order = 'title';
            if (empty($dir)) $dir = 'ASC';
        } else { // this is an event category
            $sql_start = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM ' . $db->prefix . 'product p ';
            $count_sql_start = 'SELECT COUNT(DISTINCT p.id) as c, er.event_starttime, er.signup_cutoff FROM ' . $db->prefix . 'product p ';
            $sql = 'JOIN ' . $db->prefix . 'product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'JOIN ' . $db->prefix . 'eventregistration er ON p.product_type_id = er.id ';
            $sql .= 'WHERE sc.storecategories_id IN (';
            $sql .= 'SELECT id FROM ' . $db->prefix . 'storeCategories WHERE rgt BETWEEN ' . $this->category->lft . ' AND ' . $this->category->rgt . ')';
            if ($this->category->hide_closed_events) {
                $sql .= ' AND er.signup_cutoff > ' . time();
            }

            $count_sql = $count_sql_start . $sql;
            $sql = $sql_start . $sql;

            $order = !empty($this->params['order']) ? $this->params['order'] : 'event_starttime';
            $dir = !empty($this->params['dir']) ? $this->params['dir'] : 'ASC';
        }

        if (empty($router->params['title']))  // we need to pass on the category for proper paging
            $router->params['title'] = $this->category->sef_url;
        $limit = !empty($this->config['limit']) ? $this->config['limit'] : (!empty($this->config['pagination_default']) ? $this->config['pagination_default'] : 10);
        if ($this->category->find('count') > 0) { // there are categories
            $page = new expPaginator(array(
                'model_field' => 'product_type',
                'sql'         => $sql,
                'count_sql'   => $count_sql,
                'limit'       => $limit,
                'order'       => $order,
                'dir'         => $dir,
                'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
                'controller'  => $this->params['controller'],
                'action'      => $this->params['action'],
                'columns'     => array(
                    gt('Model #')      => 'model',
                    gt('Product Name') => 'title',
                    gt('Price')        => 'price'
                ),
            ));
        } else { // there are no categories
            $page = new expPaginator(array(
                'model_field' => 'product_type',
                'sql'         => 'SELECT * FROM ' . $db->prefix . 'product WHERE 1',
                'limit'       => $limit,
                'order'       => $order,
                'dir'         => $dir,
                'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
                'controller'  => $this->params['controller'],
                'action'      => $this->params['action'],
                'columns'     => array(
                    gt('Model #')      => 'model',
                    gt('Product Name') => 'title',
                    gt('Price')        => 'price'
                ),
            ));
        }

        $ancestors = $this->category->pathToNode();
        $categories = ($this->parent == 0) ? $this->category->getTopLevel(null, false, true) : $this->category->getChildren(null, false, true);

        $rerankSQL = "SELECT DISTINCT p.* FROM " . $db->prefix . "product p JOIN " . $db->prefix . "product_storeCategories sc ON p.id = sc.product_id WHERE sc.storecategories_id=" . $this->category->id . " ORDER BY rank ASC";
        //eDebug($router);
        $defaultSort = $router->current_url;

        assign_to_template(array(
            'page'             => $page,
            'defaultSort'      => $defaultSort,
            'ancestors'        => $ancestors,
            'categories'       => $categories,
            'current_category' => $this->category,
            'rerankSQL'        => $rerankSQL
        ));
        $this->categoryBreadcrumb();
    }

    function grabConfig($category = null) {

        // grab the configs for the passed category
        if (is_object($category)) {
            $catConfig = new expConfig(expCore::makeLocation("storeCategory","@store-" . $category->id,""));
        } elseif (empty($this->config)) {  // config not set yet
            $global_config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
            $this->config = $global_config->config;
            return;
        }

        // grab the store general settings
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));

        // $this->config currently holds the module settings - merge together with any cat config settings having priority
        $this->config = empty($catConfig->config) || @$catConfig->config['use_global'] == 1 ?  @array_merge($config->config, $this->config) :  @array_merge($config->config, $this->config, $catConfig->config);

        //This is needed since in the first installation of ecom the value for this will be empty and we are doing % operation for this value
        //So we need to ensure if the value is = 0, we make it the default
        if (empty($this->config['images_per_row'])) {
            $this->config['images_per_row'] = 3;
        }
    }

    /**
     * @deprecated 2.0.0 moved to eventregistration
     */
    function upcomingEvents() {
        $this->params['controller'] = 'eventregistration';
        redirect_to($this->params);

        //fixme old code
        $sql = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM ' . DB_TABLE_PREFIX . '_product p ';
        $sql .= 'JOIN ' . DB_TABLE_PREFIX . '_eventregistration er ON p.product_type_id = er.id ';
        $sql .= 'WHERE 1 AND er.signup_cutoff > ' . time();

        $limit = empty($this->config['event_limit']) ? 10 : $this->config['event_limit'];
        $order = 'eventdate';
        $dir = 'ASC';

        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'limit'       => $limit,
            'order'       => $order,
            'dir'         => $dir,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page' => $page
        ));
    }

    /**
     * @deprecated 2.0.0 moved to eventregistration
     */
    function eventsCalendar() {
        $this->params['controller'] = 'eventregistration';
        redirect_to($this->params);

        //fixme old code
        global $db, $user;

        expHistory::set('viewable', $this->params);

        $time = isset($this->params['time']) ? $this->params['time'] : time();
        assign_to_template(array(
            'time' => $time
        ));

//        $monthly = array();
//        $counts  = array();

        $info = getdate($time);
        $nowinfo = getdate(time());
        if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
        // Grab non-day numbers only (before end of month)
//        $week        = 0;
        $currentweek = -1;

        $timefirst = mktime(0, 0, 0, $info['mon'], 1, $info['year']);
        $week = intval(date('W', $timefirst));
        if ($week >= 52 && $info['mon'] == 1) $week = 1;
        $infofirst = getdate($timefirst);

//        if ($infofirst['wday'] == 0) {
//            $monthly[$week] = array(); // initialize for non days
//            $counts[$week]  = array();
//        }
//        for ($i = 1 - $infofirst['wday']; $i < 1; $i++) {
//            $monthly[$week][$i] = array();
//            $counts[$week][$i]  = -1;
//        }
//        $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
        $monthly[$week] = array(); // initialize for non days
        $counts[$week] = array();
        if (($infofirst['wday'] == 0) && (DISPLAY_START_OF_WEEK == 1)) {
            for ($i = -6; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                $monthly[$week][$i] = array();
                $counts[$week][$i] = -1;
            }
            $weekday = $infofirst['wday'] + 7; // day number in grid.  if 7+, switch weeks
        } else {
            for ($i = 1 - $infofirst['wday']; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                $monthly[$week][$i] = array();
                $counts[$week][$i] = -1;
            }
            $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
        }
        // Grab day counts
        $endofmonth = date('t', $time);

        for ($i = 1; $i <= $endofmonth; $i++) {
            $start = mktime(0, 0, 0, $info['mon'], $i, $info['year']);
            if ($i == $nowinfo['mday']) $currentweek = $week;

//            $dates              = $db->selectObjects("eventregistration", "`eventdate` = $start");
//            $dates = $db->selectObjects("eventregistration", "(eventdate >= " . expDateTime::startOfDayTimestamp($start) . " AND eventdate <= " . expDateTime::endOfDayTimestamp($start) . ")");
            $er = new eventregistration();
//            $dates = $er->find('all', "(eventdate >= " . expDateTime::startOfDayTimestamp($start) . " AND eventdate <= " . expDateTime::endOfDayTimestamp($start) . ")");

            if ($user->isAdmin()) {
                $events = $er->find('all', 'product_type="eventregistration"', "title ASC");
            } else {
                $events = $er->find('all', 'product_type="eventregistration" && active_type=0', "title ASC");
            }
            $dates = array();

            foreach ($events as $event) {
                // $this->signup_cutoff > time()
                if ($event->eventdate >= expDateTime::startOfDayTimestamp($start) && $event->eventdate <= expDateTime::endOfDayTimestamp($start)) {
                    $dates[] = $event;
                }
                // eDebug($event->signup_cutoff, true);
            }

            $monthly[$week][$i] = $this->getEventsForDates($dates);
            $counts[$week][$i] = count($monthly[$week][$i]);
            if ($weekday >= (6 + DISPLAY_START_OF_WEEK)) {
                $week++;
                $monthly[$week] = array(); // allocate an array for the next week
                $counts[$week] = array();
                $weekday = DISPLAY_START_OF_WEEK;
            } else $weekday++;
        }
        // Grab non-day numbers only (after end of month)
        for ($i = 1; $weekday && $i < (8 + DISPLAY_START_OF_WEEK - $weekday); $i++) {
            $monthly[$week][$i + $endofmonth] = array();
            $counts[$week][$i + $endofmonth] = -1;
        }

        $this->params['time'] = $time;
        assign_to_template(array(
            'currentweek' => $currentweek,
            'monthly'     => $monthly,
            'counts'      => $counts,
            "prevmonth3"  => strtotime('-3 months', $timefirst),
            "prevmonth2"  => strtotime('-2 months', $timefirst),
            "prevmonth"   => strtotime('-1 months', $timefirst),
            "nextmonth"   => strtotime('+1 months', $timefirst),
            "nextmonth2"  => strtotime('+2 months', $timefirst),
            "nextmonth3"  => strtotime('+3 months', $timefirst),
            'now'         => $timefirst,
            "today"       => expDateTime::startOfDayTimestamp(time()),
            'params'      => $this->params,
            'daynames'    => event::dayNames(),
        ));
    }

    /*
    * Helper function for the Calendar view
     * @deprecated 2.0.0 moved to eventregistration
    */
    function getEventsForDates($edates, $sort_asc = true) {
        global $db;
        $events = array();
        foreach ($edates as $edate) {
//            if (!isset($this->params['cat'])) {
//                if (isset($this->params['title']) && is_string($this->params['title'])) {
//                    $default_id = $db->selectValue('storeCategories', 'id', "sef_url='" . $this->params['title'] . "'");
//                } elseif (!empty($this->config['category'])) {
//                    $default_id = $this->config['category'];
//                } elseif (ecomconfig::getConfig('show_first_category')) {
//                    $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
//                } else {
//                    $default_id = 0;
//                }
//            }
//
//            $parent = isset($this->params['cat']) ? intval($this->params['cat']) : $default_id;
//
//            $category = new storeCategory($parent);

            $sql = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM ' . $db->prefix . 'product p ';
//            $sql .= 'JOIN ' . $db->prefix . 'product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'JOIN ' . $db->prefix . 'eventregistration er ON p.product_type_id = er.id ';
            $sql .= 'WHERE 1 ';
//            $sql .= ' AND sc.storecategories_id IN (SELECT id FROM exponent_storeCategories WHERE rgt BETWEEN ' . $category->lft . ' AND ' . $category->rgt . ')';
//            if ($category->hide_closed_events) {
//                $sql .= ' AND er.signup_cutoff > ' . time();
//            }
//            $sql .= ' AND er.id = ' . $edate->id;
            $sql .= ' AND er.id = ' . $edate->product_type_id;

            $order = 'event_starttime';
            $dir = 'ASC';

            $o = $db->selectObjectBySql($sql);
            $o->eventdate = $edate->eventdate;
            $o->eventstart = $edate->event_starttime + $edate->eventdate;
            $o->eventend = $edate->event_endtime + $edate->eventdate;
            $o->expFile = $edate->expFile;
            $events[] = $o;
        }
        $events = expSorter::sort(array('array' => $events, 'sortby' => 'eventstart', 'order' => $sort_asc ? 'ASC' : 'DESC'));
        return $events;
    }

    function categoryBreadcrumb() {
//        global $db, $router;

        //eDebug($this->category);

        /*if(isset($router->params['action']))
        {
            $ancestors = $this->category->pathToNode();       
        }else if(isset($router->params['section']))
        {
            $current = $db->selectObject('section',' id= '.$router->params['section']);
            $ancestors[] = $current;
            if( $current->parent != -1 || $current->parent != 0 )
            {                   
                while ($db->selectObject('section',' id= '.$router->params['section']);)
                    if ($section->id == $id) {
                        $current = $section;
                        break;
                    }
                }
            }
            eDebug($sections);
            $ancestors = $this->category->pathToNode();       
        }*/

        $ancestors = $this->category->pathToNode();
        // eDebug($ancestors);
        assign_to_template(array(
            'ancestors' => $ancestors
        ));
    }

    function showallUncategorized() {
        expHistory::set('viewable', $this->params);

//        $sql = 'SELECT p.* FROM ' . DB_TABLE_PREFIX . '_product p JOIN ' . DB_TABLE_PREFIX . '_product_storeCategories ';
//        $sql .= 'sc ON p.id = sc.product_id WHERE sc.storecategories_id = 0 AND parent_id=0';
        $sql = 'SELECT p.* FROM ' . DB_TABLE_PREFIX . '_product p LEFT OUTER JOIN ' . DB_TABLE_PREFIX . '_product_storeCategories ';
        $sql .= 'sc ON p.id = sc.product_id WHERE sc.product_id is null AND p.parent_id=0';

        expSession::set('product_export_query', $sql);

        $limit = !empty($this->config['limit']) ? $this->config['limit'] : 10;
        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'limit'       => !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : $limit,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page'        => $page,
            'moduletitle' => 'Uncategorized Products'
        ));
    }

    function manage() {
        expHistory::set('manageable', $this->params);

        if (ECOM_LARGE_DB) {
            $limit = !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : 10;
        } else {
            $limit = 0;  // we'll paginate on the page
        }
        $page = new expPaginator(array(
            'model'      => 'product',
            'where'      => 'parent_id=0',
            'limit'      => $limit,
            'order'      => (isset($this->params['order']) ? $this->params['order'] : 'title'),
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
            'columns'    => array(
                gt('Type')         => 'product_type',
                gt('Product Name') => 'title',
                gt('Model #')      => 'model',
                gt('Price')        => 'base_price'
            )
        ));
        assign_to_template(array(
            'page' => $page
        ));
    }

    function showallImpropercategorized() {
        expHistory::set('viewable', $this->params);

        //FIXME not sure this is the correct sql, not sure what we are trying to pull out
        $sql = 'SELECT DISTINCT(p.id),p.product_type FROM ' . DB_TABLE_PREFIX . '_product p ';
        $sql .= 'JOIN ' . DB_TABLE_PREFIX . '_product_storeCategories psc ON p.id = psc.product_id ';
        $sql .= 'JOIN '.DB_TABLE_PREFIX.'_storeCategories sc ON psc.storecategories_id = sc.parent_id ';
        $sql .= 'WHERE p.parent_id=0 AND sc.parent_id != 0';

        expSession::set('product_export_query', $sql);

        $limit = !empty($this->config['limit']) ? $this->config['limit'] : 10;
        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'limit'       => !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : $limit,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page'        => $page,
            'moduletitle' => 'Improperly Categorized Products'
        ));
    }

    function exportMe() {
        redirect_to(array('controller' => 'report', 'action' => 'batch_export', 'applytoall' => true));
    }

    function export() {
        global $db;

        $this->params['applytoall'] = 1;  //FIXME we simply do all now

        //eDebug($this->params);
        //$sql = "SELECT * INTO OUTFILE '" . BASE . "tmp/export.csv' FIELDS TERMINATED BY ','  FROM exponent_product WHERE 1 LIMIT 10";
//        $out = '"id","parent_id","child_rank","title","body","model","warehouse_location","sef_url","canonical","meta_title","meta_keywords","meta_description","tax_class_id","quantity","availability_type","base_price","special_price","use_special_price","active_type","product_status_id","category1","category2","category3","category4","category5","category6","category7","category8","category9","category10","category11","category12","surcharge","category_rank","feed_title","feed_body"' . chr(13) . chr(10);
        $out = '"id","parent_id","child_rank","title","body","model","warehouse_location","sef_url","meta_title","meta_keywords","meta_description","tax_class_id","quantity","availability_type","base_price","special_price","use_special_price","active_type","product_status_id","category1","category2","category3","category4","category5","category6","category7","category8","category9","category10","category11","category12","surcharge","category_rank","feed_title","feed_body","weight","width","height","length","image1","image2","image3","image4","image5","companies_id"' . chr(13) . chr(10);
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $sql = expSession::get('product_export_query');
            if (empty($sql)) $sql = 'SELECT DISTINCT(p.id) from ' . $db->prefix . 'product as p WHERE (product_type="product")';
            //eDebug($sql);
            //expSession::set('product_export_query','');
            $prods = $db->selectArraysBySql($sql);
            //eDebug($prods);
        } else {
            foreach ($this->params['act-upon'] as $prod) {
                $prods[] = array('id' => $prod);
            }
        }
        set_time_limit(0);
        $baseProd = new product();

        //$p = new product($pid['id'], false, false);
        foreach ($prods as $pid) {
            $except = array('company', 'crosssellItem', 'optiongroup');
            $p = $baseProd->find('first', 'id=' . $pid['id'], null, null, 0, true, true, $except, true);

            //eDebug($p,true);
            $out .= expString::outputField($p->id);
            $out .= expString::outputField($p->parent_id);
            $out .= expString::outputField($p->child_rank);
            $out .= expString::outputField($p->title);
            $out .= expString::outputField(expString::stripLineEndings($p->body), ",", true);
            $out .= expString::outputField($p->model);
            $out .= expString::outputField($p->warehouse_location);
            $out .= expString::outputField($p->sef_url);
//            $out .= expString::outputField($p->canonical);  FIXME this is NOT in the import sequence
            $out .= expString::outputField($p->meta_title);
            $out .= expString::outputField($p->meta_keywords);
            $out .= expString::outputField($p->meta_description);
            $out .= expString::outputField($p->tax_class_id);
            $out .= expString::outputField($p->quantity);
            $out .= expString::outputField($p->availability_type);
            $out .= expString::outputField($p->base_price);
            $out .= expString::outputField($p->special_price);
            $out .= expString::outputField($p->use_special_price);
            $out .= expString::outputField($p->active_type);
            $out .= expString::outputField($p->product_status_id);

            $rank = 0;
            //eDebug($p);
            for ($x = 0; $x < 12; $x++) {
                $this->catstring = '';
                if (isset($p->storeCategory[$x])) {
                    $out .= expString::outputField(storeCategory::buildCategoryString($p->storeCategory[$x]->id, true));
                    $rank = $db->selectValue('product_storeCategories', 'rank', 'product_id=' . $p->id . ' AND storecategories_id=' . $p->storeCategory[$x]->id);
                } else $out .= ',';
            }
            $out .= expString::outputField($p->surcharge);
            $out .= expString::outputField($rank);  // no longer used
            $out .= expString::outputField($p->feed_title);
            $out .= expString::outputField($p->feed_body);
            $out .= expString::outputField($p->weight);
            $out .= expString::outputField($p->height);
            $out .= expString::outputField($p->width);
            $out .= expString::outputField($p->length);
            //output images
            if (isset($p->expFile['mainimage'][0])) {
                $out .= expString::outputField($p->expFile['mainimage'][0]->id);
            } else $out .= ',';
            for ($x = 0; $x < 3; $x++) {
                if (isset($p->expFile['images'][$x])) {
                    $out .= expString::outputField($p->expFile['images'][$x]->id);
                } else $out .= ',';
            }
            $out .= expString::outputField($p->companies_id, chr(13) . chr(10)); //Removed the extra "," in the last element

            foreach ($p->childProduct as $cp) {
                //$p = new product($pid['id'], true, false);
                //eDebug($p,true);
                $out .= expString::outputField($cp->id);
                $out .= expString::outputField($cp->parent_id);
                $out .= expString::outputField($cp->child_rank);
                $out .= expString::outputField($cp->title);
                $out .= expString::outputField(expString::stripLineEndings($cp->body));
                $out .= expString::outputField($cp->model);
                $out .= expString::outputField($cp->warehouse_location);
                $out .= expString::outputField($cp->sef_url);
//                $out .= expString::outputField($cp->canonical);  FIXME this is NOT in the import sequence
                $out .= expString::outputField($cp->meta_title);
                $out .= expString::outputField($cp->meta_keywords);
                $out .= expString::outputField($cp->meta_description);
                $out .= expString::outputField($cp->tax_class_id);
                $out .= expString::outputField($cp->quantity);
                $out .= expString::outputField($cp->availability_type);
                $out .= expString::outputField($cp->base_price);
                $out .= expString::outputField($cp->special_price);
                $out .= expString::outputField($cp->use_special_price);
                $out .= expString::outputField($cp->active_type);
                $out .= expString::outputField($cp->product_status_id);
                $out .= ',,,,,,,,,,,,';  // for categories
                $out .= expString::outputField($cp->surcharge);
                $out .= ',,,'; //for rank, feed title, feed body
                $out .= expString::outputField($cp->weight);
                $out .= expString::outputField($cp->height);
                $out .= expString::outputField($cp->width);
                $out .= expString::outputField($cp->length);
                $out .= ',,,,,';  // for images
                $out .= expString::outputField($cp->companies_id, chr(13) . chr(10));

                //echo($out);
            }

        }

//        $outFile = 'tmp/product_export_' . time() . '.csv';
//        $outHandle = fopen(BASE . $outFile, 'w');
//        fwrite($outHandle, $out);
//        fclose($outHandle);
//
//        echo "<br/><br/>Download the file here: <a href='" . PATH_RELATIVE . $outFile . "'>Product Export</a>";

        $filename = 'product_export_' . time() . '.csv';

        ob_end_clean();
        ob_start("ob_gzhandler");

        // 'application/octet-stream' is the registered IANA type but
        //        MSIE and Opera seems to prefer 'application/octetstream'
        $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';

        header('Content-Type: ' . $mime_type);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        // IE need specific headers
        if (EXPONENT_USER_BROWSER == 'IE') {
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
        }
        echo $out;
        exit; // Exit, since we are exporting

        /*eDebug(BASE . "tmp/export.csv");
        $db->sql($sql);
        eDebug($db->error());*/
        /*OPTIONALLY ENCLOSED BY '" . '"' .
        "' ESCAPED BY '\\'
        LINES TERMINATED BY '" . '\\n' .
        "' */
    }

    /**
     * @deprecated 2.3.3 moved to company
     */
    function showallByManufacturer() {
        expHistory::set('viewable', $this->params);

        $limit = !empty($this->config['limit']) ? $this->config['limit'] : 10;
        $page = new expPaginator(array(
            'model'      => 'product',
            'where'      => 'companies_id=' . $this->params['id'] . ' AND parent_id=0',
            'limit'      => !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : $limit,
            'default'    => 'Product Name',
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
            'columns'    => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            )
        ));

        $company = new company($this->params['id']);

        assign_to_template(array(
            'page'    => $page,
            'company' => $company
        ));
    }

    /**
     * @deprecated 2.3.3 moved to company
     */
    function showallManufacturers() {
        global $db;
        expHistory::set('viewable', $this->params);
        $sql = 'SELECT comp.* FROM ' . $db->prefix . 'companies as comp JOIN ' . $db->prefix . 'product AS prod ON prod.companies_id = comp.id WHERE parent_id=0 GROUP BY comp.title ORDER BY comp.title;';
        $manufacturers = $db->selectObjectsBySql($sql);
        assign_to_template(array(
            'manufacturers' => $manufacturers
        ));
    }

    function showGiftCards() {
        expHistory::set('viewable', $this->params);
        //Get all giftcards
        $product_type = 'giftcard';
        $giftcard = new $product_type();
        $giftcards = $giftcard->find("all", "product_type = 'giftcard'");

        //Grab the global config
        $this->grabConfig();

        //Set the needed config for the view
        $config['custom_message_product'] = $this->config['custom_message_product'];
        $config['minimum_gift_card_purchase'] = $this->config['minimum_gift_card_purchase'];
        $records = expSession::get('params');
        expSession::un_set('params');
        assign_to_template(array(
            'giftcards' => $giftcards,
            'config'    => $config,
            'records'   => $records
        ));
    }

    function show() {
        global $db, $order, $template, $user;

        expHistory::set('viewable', $this->params);
//        $classname = $db->selectValue('product', 'product_type', 'id=' . $this->params['id']);
//        $product   = new $classname($this->params['id'], true, true);

        $id = isset($this->params['title']) ? $this->params['title'] : $this->params['id'];
        $product = new product($id);
        $product_type = new $product->product_type($product->id);
        $product_type->title = expString::parseAndTrim($product_type->title, true);
        $product_type->image_alt_tag = expString::parseAndTrim($product_type->image_alt_tag, true);

        //if we're trying to view a child product directly, then we redirect to it's parent show view
        //bunk URL, no product found
        if (empty($product->id)) {
            redirect_to(array('controller' => 'notfound', 'action' => 'page_not_found', 'title' => $this->params['title']));
        }
        // we do not display child products by themselves
        if (!empty($product->parent_id)) {
            $product = new product($product->parent_id);
            redirect_to(array('controller' => 'store', 'action' => 'show', 'title' => $product->sef_url));
        }
        if ($product->active_type == 1) {
            $product_type->user_message = "This product is temporarily unavailable for purchase.";
        } elseif ($product->active_type == 2 && !$user->isAdmin()) {
            flash("error", $product->title . " " . gt("is currently unavailable."));
            expHistory::back();
        } elseif ($product->active_type == 2 && $user->isAdmin()) {
            $product_type->user_message = $product->title . " is currently marked as unavailable for purchase or display.  Normal users will not see this product.";
        }

        // pull in company attachable files
        if (!empty($product_type->companies_id)) {
            $product_type->company = new company($product_type->companies_id);
        }

        if (!empty($product_type->crosssellItem)) foreach ($product_type->crosssellItem as &$csi) {
            $csi->getAttachableItems();
        }

        $tpl = $product_type->getForm('show');

        if (!empty($tpl)) $template = new controllertemplate($this, $tpl);
        $this->grabConfig(); // grab the global config

        assign_to_template(array(
            'config'        => $this->config,
            'asset_path'    => $this->asset_path,
//            'product'      => $product,
            'product'       => $product_type,
            'last_category' => !empty($order->lastcat) ? $order->lastcat : null,
        ));
        $this->categoryBreadcrumb();
    }

    function showByTitle() {
        global $order, $template, $user;
        //need to add a check here for child product and redirect to parent if hit directly by ID
        expHistory::set('viewable', $this->params);

        $product = new product(addslashes($this->params['title']));
        $product_type = new $product->product_type($product->id);
        $product_type->title = expString::parseAndTrim($product_type->title, true);
        $product_type->image_alt_tag = expString::parseAndTrim($product_type->image_alt_tag, true);

        //if we're trying to view a child product directly, then we redirect to it's parent show view
        //bunk URL, no product found
        if (empty($product->id)) {
            redirect_to(array('controller' => 'notfound', 'action' => 'page_not_found', 'title' => $this->params['title']));
        }
        if (!empty($product->parent_id)) {
            $product = new product($product->parent_id);
            redirect_to(array('controller' => 'store', 'action' => 'show', 'title' => $product->sef_url));
        }
        if ($product->active_type == 1) {
            $product_type->user_message = "This product is temporarily unavailable for purchase.";
        } elseif ($product->active_type == 2 && !$user->isAdmin()) {
            flash("error", $product->title . " " . gt("is currently unavailable."));
            expHistory::back();
        } elseif ($product->active_type == 2 && $user->isAdmin()) {
            $product_type->user_message = $product->title . " is currently marked as unavailable for purchase or display.  Normal users will not see this product.";
        }
        if (!empty($product_type->crosssellItem)) foreach ($product_type->crosssellItem as &$csi) {
            $csi->getAttachableItems();
        }
        //eDebug($product->crosssellItem);

        $tpl = $product_type->getForm('show');
        //eDebug($product);
        if (!empty($tpl)) $template = new controllertemplate($this, $tpl);
        $this->grabConfig(); // grab the global config

        assign_to_template(array(
            'config'        => $this->config,
            'product'       => $product_type,
            'last_category' => !empty($order->lastcat) ? $order->lastcat : null,
        ));
    }

    function showByModel() {
        global $order, $template, $db;

        expHistory::set('viewable', $this->params);
        $product = new product();
        $model = $product->find("first", 'model="' . $this->params['model'] . '"');
        //eDebug($model);
        $product_type = new $model->product_type($model->id);
        //eDebug($product_type);
        $tpl = $product_type->getForm('show');
        if (!empty($tpl)) $template = new controllertemplate($this, $tpl);
        //eDebug($template);
        $this->grabConfig(); // grab the global config
        assign_to_template(array(
            'config'        => $this->config,
            'product'       => $product_type,
            'last_category' => $order->lastcat
        ));
    }

    function showallSubcategories() {
//        global $db;

        expHistory::set('viewable', $this->params);
//        $parent = isset($this->params['cat']) ? $this->params['cat'] : expSession::get('catid');
        $catid = expSession::get('catid');
        $parent = !empty($catid) ? $catid : (!empty($this->params['cat']) ? $this->params['cat'] : 0);
        $category = new storeCategory($parent);
        $categories = $category->getEcomSubcategories();
        $ancestors = $category->pathToNode();
        assign_to_template(array(
            'categories' => $categories,
            'ancestors'  => $ancestors,
            'category'   => $category
        ));
    }

    function showallFeaturedProducts() {
        expHistory::set('viewable', $this->params);
        $order = !empty($this->params['order']) ? $this->params['order'] : $this->config['orderby'];
        $dir = !empty($this->params['dir']) ? $this->params['dir'] : $this->config['orderby_dir'];
        if (empty($order)) $order = 'title';
        if (empty($dir)) $dir = 'ASC';

        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => 'SELECT * FROM ' . DB_TABLE_PREFIX . '_product WHERE is_featured=1',
            'limit'       => ecomconfig::getConfig('pagination_default'),
            'order'       => $order,
            'dir'         => $dir,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page' => $page
        ));
    }

    function showallCategoryFeaturedProducts() {
        expHistory::set('viewable', $this->params);
        $curcat = $this->category;

        $order = !empty($this->params['order']) ? $this->params['order'] : $this->config['orderby'];
        $dir = !empty($this->params['dir']) ? $this->params['dir'] : $this->config['orderby_dir'];
        if (empty($order)) $order = 'title';
        if (empty($dir)) $dir = 'ASC';
        //FIXME bad sql statement needs to be a JOIN
        $sql = 'SELECT * FROM ' . DB_TABLE_PREFIX . '_product as p,' . DB_TABLE_PREFIX . '_product_storeCategories as sc WHERE sc.product_id = p.id and p.is_featured=1 and sc.storecategories_id =' . $curcat->id;
        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'limit'       => ecomconfig::getConfig('pagination_default'),
            'order'       => $order,
            'dir'         => $dir,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page' => $page
        ));
    }

    function showTopLevel() {
        expHistory::set('viewable', $this->params);
        $category = new storeCategory(null, false, false);
        //$categories = $category->getEcomSubcategories();
        $categories = $category->getTopLevel(null, false, true);
        $ancestors = $this->category->pathToNode();
        $curcat = $this->category;

        assign_to_template(array(
            'categories' => $categories,
            'curcat'     => $curcat,
            'topcat'     => @$ancestors[0]
        ));
    }

    function showTopLevel_images() {
        global $user;

        expHistory::set('viewable', $this->params);
        $count_sql_start = 'SELECT COUNT(DISTINCT p.id) as c FROM ' . DB_TABLE_PREFIX . '_product p ';
        $sql_start = 'SELECT DISTINCT p.* FROM ' . DB_TABLE_PREFIX . '_product p ';
        $sql = 'JOIN ' . DB_TABLE_PREFIX . '_product_storeCategories sc ON p.id = sc.product_id ';
        $sql .= 'WHERE ';
        if (!$user->isAdmin()) $sql .= '(p.active_type=0 OR p.active_type=1)'; //' AND ' ;
        //$sql .= 'sc.storecategories_id IN (';
        //$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$this->category->lft.' AND '.$this->category->rgt.')';

        $count_sql = $count_sql_start . $sql;
        $sql = $sql_start . $sql;

        $order = 'sc.rank'; //$this->config['orderby'];
        $dir = 'ASC'; //$this->config['orderby_dir'];

        $limit = !empty($this->config['limit']) ? $this->config['limit'] : 10;
        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'count_sql'   => $count_sql,
            'limit'       => !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : $limit,
            'order'       => $order,
            'dir'         => $dir,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        $category = new storeCategory(null, false, false);
        //$categories = $category->getEcomSubcategories();
        $categories = $category->getTopLevel(null, false, true);
        $ancestors = $this->category->pathToNode();
        $curcat = $this->category;

        assign_to_template(array(
            'page'       => $page,
            'categories' => $categories
        ));
    }

    function showFullTree() {  //FIXME we also need a showFullTree_images method like above
        expHistory::set('viewable', $this->params);
        $category = new storeCategory(null, false, false);
        //$categories = $category->getEcomSubcategories();
        $categories = $category->getFullTree();
        $ancestors = $this->category->pathToNode();
        $curcat = $this->category;

        assign_to_template(array(
            'categories' => $categories,
            'curcat'     => $curcat,
            'topcat'     => @$ancestors[0]
        ));
    }

    function ecomSearch() {

    }

    function billing_config() {

    }

    /**
     * Add all products (products, event registrations, donations, & gift cards) to search index
     *
     * @return int
     */
    function addContentToSearch() {
        global $db, $router;

        $model = new $this->basemodel_name();

        $total = $db->countObjects($model->table);

        $count = 0;
        for ($i = 0; $i < $total; $i += 100) {
            $orderby = 'id LIMIT ' . ($i) . ', 100';
            $content = $db->selectArrays($model->table, 'parent_id=0', $orderby);

            foreach ($content as $cnt) {
                $origid = $cnt['id'];
                $prod = new product($cnt['id']);
                unset($cnt['id']);
                if (ecomconfig::getConfig('ecom_search_results') == '') {
                    $cnt['title'] = (isset($prod->expFile['mainimage'][0]) ? '<img src="' . PATH_RELATIVE . 'thumb.php?id=' . $prod->expFile['mainimage'][0]->id . '&w=40&h=40&zc=1" style="float:left;margin-right:5px;" />' : '') . $cnt['title'] . (!empty($cnt['model']) ? ' - SKU#: ' . $cnt['model'] : '');
                }

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

                $search_record->posted = empty($cnt['created_at']) ? null : $cnt['created_at'];
                if ($cnt['product_type'] == 'giftcard') {
                    $search_record->view_link = str_replace(URL_FULL, '', $router->makeLink(array('controller' => 'store', 'action' => 'showGiftCards')));
                } else {
//                    $search_record->view_link = str_replace(URL_FULL, '', $router->makeLink(array('controller' => $this->baseclassname, 'action' => 'show', 'title' => $cnt['sef_url'])));
                    $search_record->view_link = str_replace(URL_FULL, '', $router->makeLink(array('controller' => $cnt['product_type'], 'action' => 'show', 'title' => $cnt['sef_url'])));
                }
//                $search_record->ref_module = 'store';
                $search_record->ref_module  = $this->baseclassname;
//                $search_record->ref_type = $this->basemodel_name;
                $search_record->ref_type = $cnt['product_type'];
//                $search_record->category = 'Products';
                $prod = new $search_record->ref_type($origid);
                $search_record->category = $prod->product_name;
                if ($search_record->ref_type == 'eventregistration') {
                    $search_record->title .= ' - ' . expDateTime::format_date($prod->eventdate);
                }

                $search_record->original_id = $origid;
                //$search_record->location_data = serialize($this->loc);
                $search_record->save();
                $count++;
            }
        }
        return $count;
    }

    function searchByModel() {
        //do nothing...just show the view.
    }

    function edit() {
        global $db;

//        $expDefinableField = new expDefinableField();
//        $definablefields = $expDefinableField->find('all','1','rank');

        //Make sure that the view is the edit.tpl and not any ajax views
        if (isset($this->params['view']) && $this->params['view'] == 'edit') {
            expHistory::set('editable', $this->params);
        }

        // first we need to figure out what type of ecomm product we are dealing with
        if (!empty($this->params['id'])) {
            // if we have an id lets pull the product type from the products table.
            $product_type = $db->selectValue('product', 'product_type', 'id=' . $this->params['id']);
            if (empty($product_type)) redirect_to(array('controller' => 'store', 'action' => 'picktype'));
        } else {
            if (empty($this->params['product_type'])) redirect_to(array('controller' => 'store', 'action' => 'picktype'));
            $product_type = $this->params['product_type'];
        }

        if (!empty($this->params['id'])) {
            $record = new $product_type($this->params['id']);
            if (!empty($this->user_input_fields) && !is_array($record->user_input_fields)) $record->user_input_fields = expUnserialize($record->user_input_fields);
        } else {
            $record = new $product_type();
            $record->user_input_fields = array();
        }

//        if (!empty($this->params['parent_id']))

        // get the product options and send them to the form
        $editable_options = array();
        //$og = new optiongroup();
        $mastergroups = $db->selectExpObjects('optiongroup_master', null, 'optiongroup_master');
        //eDebug($mastergroups,true);
        foreach ($mastergroups as $mastergroup) {
            // if this optiongroup_master has already been made into an option group for this product
            // then we will grab that record now..if not, we will make a new one.
            $grouprec = $db->selectArray('optiongroup', 'optiongroup_master_id=' . $mastergroup->id . ' AND product_id=' . $record->id);
            //if ($mastergroup->id == 9) eDebug($grouprec,true);
            //eDebug($grouprec);
            if (empty($grouprec)) {
                $grouprec['optiongroup_master_id'] = $mastergroup->id;
                $grouprec['title'] = $mastergroup->title;
                $group = new optiongroup($grouprec);
            } else {
                $group = new optiongroup($grouprec['id']);
            }

            $editable_options[$group->title] = $group;

            if (empty($group->option)) {
                foreach ($mastergroup->option_master as $optionmaster) {
                    $opt = new option(array('title' => $optionmaster->title, 'option_master_id' => $optionmaster->id), false, false);
                    $editable_options[$group->title]->options[] = $opt;
                }

            } else {
                if (count($group->option) == count($mastergroup->option_master)) {
                    $editable_options[$group->title]->options = $group->option;
                } else {
                    // check for any new options or deleted since the last time we edited this product
                    foreach ($mastergroup->option_master as $optionmaster) {
                        $opt_id = $db->selectValue('option', 'id', 'option_master_id=' . $optionmaster->id . " AND product_id=" . $record->id);
                        if (empty($opt_id)) {
                            $opt = new option(array('title' => $optionmaster->title, 'option_master_id' => $optionmaster->id), false, false);
                        } else {
                            $opt = new option($opt_id);
                        }

                        $editable_options[$group->title]->options[] = $opt;
                    }
                }
            }
            //eDebug($editable_options[$group->title]);        
        }
        //die();

        uasort($editable_options, array("optiongroup", "sortOptiongroups"));

        // get the shipping options and their methods
//        $shipping = new shipping();
//        foreach (shipping::listAvailableCalculators() as $calcid => $name) {
        foreach (shipping::listCalculators() as $calcid => $name) {
            // must make sure (custom) calculator exists
            if (class_exists($name)) {
                $calc = new $name($calcid);
                $shipping_services[$calcid] = $calc->title;
                $shipping_methods[$calcid] = $calc->availableMethods();
            }
        }

#        eDebug($shipping_services);
#        eDebug($shipping_methods);

        if (!empty($this->params['product_type']) && ($this->params['product_type'] == "product" || $this->params['product_type'] == "childProduct")) {
            //if new record and it's a child, then well set the child rank to be at the end
            if (empty($record->id) && $record->isChild()) {
                $record->child_rank = $db->max('product', 'child_rank', null, 'parent_id=' . $record->parent_id) + 1;
            }
            //eDebug($record,true);
        }
        $view = '';
        $parent = null;
        if ((isset($this->params['parent_id']) && empty($record->id))) {
            //NEW child product
            $view = 'edit';
            $parent = new $product_type($this->params['parent_id'], false, true);
            $record->parent_id = $this->params['parent_id'];
        } elseif ((!empty($record->id) && $record->parent_id != 0)) {
            //EDIT child product
            $view = 'edit';
            $parent = new $product_type($record->parent_id, false, true);
        } else {
            $view = 'edit';
        }

        $f = new forms();
        $forms_list = array();
        $forms_list[0] = '- '.gt('No User Input Required').' -';
        $forms = $f->find('all', 'is_saved=1');
        if (!empty($forms)) foreach ($forms as $frm) {
            if (!$db->countObjects('eventregistration', 'forms_id='.$frm->id) || (!empty($record->forms_id) && $record->forms_id == $frm->id))
                $forms_list[$frm->id] = $frm->title;
        }

        assign_to_template(array(
            'record'            => $record,
            'parent'            => $parent,
            'form'              => $record->getForm($view),
            'optiongroups'      => $editable_options,
//            'definablefields'   => isset($definablefields) ? $definablefields : '',
            'forms'=> $forms_list,
            'shipping_services' => isset($shipping_services) ? $shipping_services : '', // Added implication since the shipping_services default value is a null
            'shipping_methods'  => isset($shipping_methods) ? $shipping_methods : '', // Added implication since the shipping_methods default value is a null
            'product_types'     => isset($this->config['product_types']) ? $this->config['product_types'] : ''
            //'status_display'=>$status_display->getStatusArray()
        ));
    }

    function copyProduct() {
        global $db;

        //expHistory::set('editable', $this->params);
        $f = new forms();
        $forms_list = array();
        $forms_list[0] = '- '.gt('No User Input Required').' -';
        $forms = $f->find('all', 'is_saved=1');
        if (!empty($forms)) foreach ($forms as $frm) {
            $forms_list[$frm->id] = $frm->title;
        }

        // first we need to figure out what type of ecomm product we are dealing with
        if (!empty($this->params['id'])) {
            // if we have an id lets pull the product type from the products table.
            $product_type = $db->selectValue('product', 'product_type', 'id=' . $this->params['id']);
        } else {
            if (empty($this->params['product_type'])) redirect_to(array('controller' => 'store', 'action' => 'picktype'));
            $product_type = $this->params['product_type'];
        }

        $record = new $product_type($this->params['id']);
        // get the product options and send them to the form
        $editable_options = array();

        $mastergroups = $db->selectExpObjects('optiongroup_master', null, 'optiongroup_master');
        foreach ($mastergroups as $mastergroup) {
            // if this optiongroup_master has already been made into an option group for this product
            // then we will grab that record now..if not, we will make a new one.
            $grouprec = $db->selectArray('optiongroup', 'optiongroup_master_id=' . $mastergroup->id . ' AND product_id=' . $record->id);
            //eDebug($grouprec);
            if (empty($grouprec)) {
                $grouprec['optiongroup_master_id'] = $mastergroup->id;
                $grouprec['title'] = $mastergroup->title;
                $group = new optiongroup($grouprec);
            } else {
                $group = new optiongroup($grouprec['id']);
            }

            $editable_options[$group->title] = $group;

            if (empty($group->option)) {
                foreach ($mastergroup->option_master as $optionmaster) {
                    $opt = new option(array('title' => $optionmaster->title, 'option_master_id' => $optionmaster->id), false, false);
                    $editable_options[$group->title]->options[] = $opt;
                }
            } else {
                if (count($group->option) == count($mastergroup->option_master)) {
                    $editable_options[$group->title]->options = $group->option;
                } else {
                    // check for any new options or deleted since the last time we edited this product
                    foreach ($mastergroup->option_master as $optionmaster) {
                        $opt_id = $db->selectValue('option', 'id', 'option_master_id=' . $optionmaster->id . " AND product_id=" . $record->id);
                        if (empty($opt_id)) {
                            $opt = new option(array('title' => $optionmaster->title, 'option_master_id' => $optionmaster->id), false, false);
                        } else {
                            $opt = new option($opt_id);
                        }

                        $editable_options[$group->title]->options[] = $opt;
                    }
                }
            }
        }

        // get the shipping options and their methods
//        $shipping = new shipping();
//        foreach (shipping::listAvailableCalculators() as $calcid => $name) {
        foreach (shipping::listCalculators() as $calcid => $name) {
            if (class_exists($name)) {
                $calc = new $name($calcid);
                $shipping_services[$calcid] = $calc->title;
                $shipping_methods[$calcid] = $calc->availableMethods();
            }
        }

        $record->original_id = $record->id;
        $record->original_model = $record->model;
        $record->sef_url = NULL;
        $record->previous_id = NULL;
        $record->editor = NULL;

        if ($record->isChild()) {
            $record->child_rank = $db->max('product', 'child_rank', null, 'parent_id=' . $record->parent_id) + 1;
        }

        assign_to_template(array(
            'copy'              => true,
            'record'            => $record,
            'parent'            => new $product_type($record->parent_id, false, true),
            'form'              => $record->getForm($record->parent_id == 0 ? 'edit' : 'child_edit'),
            'optiongroups'      => $editable_options,
            'forms'=> $forms_list,
            'shipping_services' => $shipping_services,
            'shipping_methods'  => $shipping_methods
        ));
    }

    function picktype() {
        $prodfiles = storeController::getProductTypes();
        $products = array();
        foreach ($prodfiles as $filepath => $classname) {
            $prodObj = new $classname();
            $products[$classname] = $prodObj->product_name;
        }
        assign_to_template(array(
            'product_types' => $products
        ));
    }

    function update() {
//        global $db;
        //Get the product type
        $product_type = isset($this->params['product_type']) ? $this->params['product_type'] : 'product';

        $record = new $product_type();

        $record->update($this->params);

        if ($product_type == "childProduct" || $product_type == "product") {
            $record->addContentToSearch();
            //Create a flash message and redirect to the page accordingly
            if ($record->parent_id != 0) {
                $parent = new $product_type($record->parent_id, false, false);
                if (isset($this->params['original_id'])) {
                    flash("message", gt("Child product saved."));
                } else {
                    flash("message", gt("Child product copied and saved."));
                }
                redirect_to(array('controller' => 'store', 'action' => 'show', 'title' => $parent->sef_url));
            } elseif (isset($this->params['original_id'])) {
                flash("message", gt("Product copied and saved. You are now viewing your new product."));
            } else {
                flash("message", gt("Product saved."));
            }
            redirect_to(array('controller' => 'store', 'action' => 'show', 'title' => $record->sef_url));
        } elseif ($product_type == "giftcard") {
            flash("message", gt("Giftcard saved."));
            redirect_to(array('controller' => 'store', 'action' => 'manage'));
        } elseif ($product_type == "eventregistration") {
            //FIXME shouldn't event registrations be added to search index?
//            $record->addContentToSearch();  //FIXME there is NO eventregistration::addContentToSearch() method
            flash("message", gt("Event saved."));
            redirect_to(array('controller' => 'store', 'action' => 'manage'));
        } elseif ($product_type == "donation") {
            flash("message", gt("Donation saved."));
            redirect_to(array('controller' => 'store', 'action' => 'manage'));
        }
    }

    function delete() {
        global $db;

        if (empty($this->params['id'])) return false;
        $product_type = $db->selectValue('product', 'product_type', 'id=' . $this->params['id']);
        $product = new $product_type($this->params['id'], true, false);
        //eDebug($product_type);  
        //eDebug($product, true);
        //if (!empty($product->product_type_id)) {
        //$db->delete($product_type, 'id='.$product->product_id);
        //}

        $db->delete('option', 'product_id=' . $product->id . " AND optiongroup_id IN (SELECT id from " . $db->prefix . "optiongroup WHERE product_id=" . $product->id . ")");
        $db->delete('optiongroup', 'product_id=' . $product->id);
        //die();
        $db->delete('product_storeCategories', 'product_id=' . $product->id . ' AND product_type="' . $product_type . '"');

        if ($product->product_type == "product") {
            if ($product->hasChildren()) {
                $this->deleteChildren();
            }
        }

        $product->delete();

        flash('message', gt('Product deleted successfully.'));
        expHistory::back();
    }

    function quicklinks() {
        global $order;

        $oicount = !empty($order->item_count) ? $order->item_count : 0;
        //eDebug($itemcount);
        assign_to_template(array(
            "oicount" => $oicount,
        ));
    }

    public static function getProductTypes() {
        $paths = array(
            BASE . 'framework/modules/ecommerce/products/models',
        );

        $products = array();
        foreach ($paths as $path) {
            if (is_readable($path)) {
                $dh = opendir($path);
                while (($file = readdir($dh)) !== false) {
                    if (is_readable($path . '/' . $file) && substr($file, -4) == '.php' && $file != 'childProduct.php') {
                        $classname = substr($file, 0, -4);
                        $products[$path . '/' . $file] = $classname;
                    }
                }
            }
        }

        return $products;
    }

    function metainfo() {
        global $router;

        if (empty($router->params['action'])) return array();

        // figure out what metadata to pass back based on the action we are in.
        $action = $router->params['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'', 'canonical'=> '', 'noindex' => false, 'nofollow' => false);
        $storename = ecomconfig::getConfig('storename');
        switch ($action) {
            case 'showall': //category page
                $cat = $this->category;
                if (!empty($cat)) {
                    $metainfo['title'] = empty($cat->meta_title) ? $cat->title . ' ' . gt('Products') . ' - ' . $storename : $cat->meta_title;
                    $metainfo['keywords'] = empty($cat->meta_keywords) ? $cat->title : strip_tags($cat->meta_keywords);
                    $metainfo['description'] = empty($cat->meta_description) ? strip_tags($cat->body) : strip_tags($cat->meta_description);
                    $metainfo['canonical'] = empty($cat->canonical) ? $router->plainPath() : strip_tags($cat->canonical);
                    $metainfo['noindex'] = empty($cat->meta_noindex) ? false : $cat->meta_noindex;
                    $metainfo['nofollow'] = empty($cat->meta_nofollow) ? false : $cat->meta_nofollow;
                }
                break;
            case 'show':
            case 'showByTitle':
                $prod = new product(isset($router->params['title']) ? expString::sanitize($router->params['title']) : intval($router->params['id']));
                if (!empty($prod)) {
                    $metainfo['title'] = empty($prod->meta_title) ? $prod->title . " - " . $storename : $prod->meta_title;
                    $metainfo['keywords'] = empty($prod->meta_keywords) ? $prod->title : strip_tags($prod->meta_keywords);
                    $metainfo['description'] = empty($prod->meta_description) ? strip_tags($prod->body) : strip_tags($prod->meta_description);
                    $metainfo['canonical'] = empty($prod->canonical) ? $router->plainPath() : strip_tags($prod->canonical);
                    $metainfo['noindex'] = empty($prod->meta_noindex) ? false : $prod->meta_noindex;
                    $metainfo['nofollow'] = empty($prod->meta_nofollow) ? false : $prod->meta_nofollow;
                    if (!empty($prod->expFile['mainimage'][0]) && file_exists(BASE.$prod->expFile['mainimage'][0]->directory.$prod->expFile['mainimage'][0]->filename)) {
                        $metainfo['rich'] = '<!--
        <PageMap>
            <DataObject type="thumbnail">
                <Attribute name="src" value="'.$prod->expFile['mainimage'][0]->url.'"/>
                <Attribute name="width" value="'.$prod->expFile['mainimage'][0]->image_width.'"/>
                <Attribute name="height" value="'.$prod->expFile['mainimage'][0]->image_width.'"/>
            </DataObject>
        </PageMap>
    -->';
                    }
                    $metainfo['fb']['type'] = 'product';
                    $metainfo['fb']['title'] =  substr(empty($prod->meta_fb['title']) ? $prod->title : $prod->meta_fb['title'], 0, 87);
                    $metainfo['fb']['description'] = substr(empty($prod->meta_fb['description']) ? $metainfo['description'] : $prod->meta_fb['description'], 0, 199);
                    $metainfo['fb']['url'] = empty($prod->meta_fb['url']) ? $metainfo['canonical'] : $prod->meta_fb['url'];
                    $metainfo['fb']['image'] = empty($prod->meta_fb['fbimage'][0]) ? '' : $prod->meta_fb['fbimage'][0]->url;
                    if (empty($metainfo['fb']['image'])) {
                        if (!empty($prod->expFile['mainimage'][0]))
                            $metainfo['fb']['image'] = $prod->expFile['mainimage'][0]->url;
                        if (empty($metainfo['fb']['image']))
                            $metainfo['fb']['image'] = URL_BASE . '/framework/modules/ecommerce/assets/images/no-image.jpg';
                    }
                    break;
                }
            default:
                $metainfo['title'] = gt("Shopping") . " - " . $storename;
                $metainfo['keywords'] = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
        }

        // Remove any quotes if there are any.
//        $metainfo['title'] = expString::parseAndTrim($metainfo['title'], true);
//        $metainfo['description'] = expString::parseAndTrim($metainfo['description'], true);
//        $metainfo['keywords'] = expString::parseAndTrim($metainfo['keywords'], true);
//        $metainfo['canonical'] = expString::parseAndTrim($metainfo['canonical'], true);
//        $metainfo['noindex'] = expString::parseAndTrim($metainfo['noindex'], true);
//        $metainfo['nofollow'] = expString::parseAndTrim($metainfo['nofollow'], true);

        return $metainfo;
    }

    /**
     * Configure the module
     */
    public function configure() {
        if (empty($this->config['enable_ratings_and_reviews'])) {
            $this->remove_configs[] = 'comments';
        }
        parent::configure();
    }

    public function deleteChildren() {
        //eDebug($data[0],true);
        //if($id!=null) $this->params['id'] = $id;
        //eDebug($this->params,true);        
        $product = new product($this->params['id']);
        //$product = $product->find("first", "previous_id =" . $previous_id);
        //eDebug($product, true);
        if (empty($product->id)) // || empty($product->previous_id)) 
        {
            flash('error', gt('There was an error deleting the child products.'));
            expHistory::back();
        }
        $childrenToDelete = $product->find('all', 'parent_id=' . $product->id);
        foreach ($childrenToDelete as $ctd) {
            //fwrite($lfh, "Deleting:" . $ctd->id . "\n");                             
            $ctd->delete();
        }
    }

    function searchByModelForm() {
        // get the search terms
        $terms = $this->params['search_string'];

        $sql = "model like '%" . $terms . "%'";

        $limit = !empty($this->config['limit']) ? $this->config['limit'] : 10;
        $page = new expPaginator(array(
            'model'      => 'product',
            'where'      => $sql,
            'limit'      => !empty($this->config['pagination_default']) ? $this->config['pagination_default'] : $limit,
            'order'      => 'title',
            'dir'        => 'DESC',
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
            'columns'    => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));

        assign_to_template(array(
            'page'  => $page,
            'terms' => $terms
        ));
    }

    /**
     * AJAX search for products by model/sku
     */

    function search_by_model() {
        global $db, $user;

        $sql = "select DISTINCT(p.id) as id, p.title, model from " . $db->prefix . "product as p WHERE ";
        if (!($user->isAdmin())) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';

        //if first character of search is a -, then we do a wild card, else from beginning
        if ($this->params['query'][0] == '-') {
            $sql .= " p.model LIKE '%" . $this->params['query'];
        } else {
            $sql .= " p.model LIKE '" . $this->params['query'];
        }

        $sql .= "%' AND p.parent_id=0 GROUP BY p.id ";
        $sql .= "order by p.model ASC LIMIT 30";
        $res = $db->selectObjectsBySql($sql);
        //eDebug($sql);
        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }

    /**
     * AJAX search for products by title, description, or model/sku
     *
     */
    public function search() {
        global $db, $user;

        if (SAVE_SEARCH_QUERIES && INCLUDE_AJAX_SEARCH == 1) {  // only to add search query record
            $qry = trim($this->params['query']);
            if (!empty($qry)) {
                if (INCLUDE_ANONYMOUS_SEARCH == 1 || $user->id <> 0) {
                    $queryObj = new stdClass();
                    $queryObj->user_id = $user->id;
                    $queryObj->query = $qry;
                    $queryObj->timestamp = time();

                    $db->insertObject($queryObj, 'search_queries');
                }
            }
        }
        //$this->params['query'] = str_ireplace('-','\-',$this->params['query']);
        $terms = explode(" ", $this->params['query']);
        $search_type = ecomconfig::getConfig('ecom_search_results');

        // look for term in full text search
        $sql = "select DISTINCT(p.id) as id, p.title, model, sef_url, f.id as fileid, match (p.title,p.body) against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) as score ";
        $sql .= "  from " . $db->prefix . "product as p LEFT JOIN " .
            $db->prefix . "content_expFiles as cef ON p.id=cef.content_id AND cef.content_type IN ('product','eventregistration','donation','giftcard') AND cef.subtype='mainimage' LEFT JOIN " . $db->prefix .
            "expFiles as f ON cef.expFiles_id = f.id WHERE ";
        if (!($user->isAdmin())) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';
        if ($search_type == 'products') $sql .= 'product_type = "product" AND ';
        $sql .= " match (p.title,p.body) against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) AND p.parent_id=0  GROUP BY p.id ";
        $sql .= "order by score desc LIMIT 10";

        $firstObs = $db->selectObjectsBySql($sql);
        foreach ($firstObs as $set) {
            $set->weight = 1;
            unset($set->score);
            $index = !empty($set->model) ? $set->model : $set->sef_url;
            $res[$index] = $set;
        }

        // look for specific term in fields
        $sql = "select DISTINCT(p.id) as id, p.title, model, sef_url, f.id as fileid  from " . $db->prefix . "product as p LEFT JOIN " .
            $db->prefix . "content_expFiles as cef ON p.id=cef.content_id AND cef.content_type IN ('product','eventregistration','donation','giftcard') AND cef.subtype='mainimage' LEFT JOIN " . $db->prefix .
            "expFiles as f ON cef.expFiles_id = f.id WHERE ";
        if (!($user->isAdmin())) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';
        if ($search_type == 'products') $sql .= 'product_type = "product" AND ';
        $sql .= " (p.model like '%" . $this->params['query'] . "%' ";
        $sql .= " OR p.title like '%" . $this->params['query'] . "%') ";
        $sql .= " AND p.parent_id=0 GROUP BY p.id LIMIT 10";

        $secondObs = $db->selectObjectsBySql($sql);
        foreach ($secondObs as $set) {
            $set->weight = 2;
            $index = !empty($set->model) ? $set->model : $set->sef_url;
            $res[$index] = $set;
        }

        // look for begins with term in fields
        $sql = "select DISTINCT(p.id) as id, p.title, model, sef_url, f.id as fileid  from " . $db->prefix . "product as p LEFT JOIN " .
            $db->prefix . "content_expFiles as cef ON p.id=cef.content_id AND cef.content_type IN ('product','eventregistration','donation','giftcard') AND cef.subtype='mainimage' LEFT JOIN " . $db->prefix .
            "expFiles as f ON cef.expFiles_id = f.id WHERE ";
        if (!($user->isAdmin())) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';
        if ($search_type == 'products') $sql .= 'product_type = "product" AND ';
        $sql .= " (p.model like '" . $this->params['query'] . "%' ";
        $sql .= " OR p.title like '" . $this->params['query'] . "%') ";
        $sql .= " AND p.parent_id=0 GROUP BY p.id LIMIT 10";

        $thirdObs = $db->selectObjectsBySql($sql);
        foreach ($thirdObs as $set) {
            if (strcmp(strtolower(trim($this->params['query'])), strtolower(trim($set->model))) == 0)
                $set->weight = 10;
            else if (strcmp(strtolower(trim($this->params['query'])), strtolower(trim($set->title))) == 0)
                $set->weight = 9;
            else
                $set->weight = 3;

            $index = !empty($set->model) ? $set->model : $set->sef_url;
            $res[$index] = $set;
        }

        function sortSearch($a, $b) {
            return ($a->weight == $b->weight ? 0 : ($a->weight < $b->weight) ? 1 : -1);
        }

        if (count($terms)) {
            foreach ($res as $r) {
                $index = !empty($r->model) ? $r->model : $r->sef_url;
                foreach ($terms as $term) {
                    if (stristr($r->title, $term)) $res[$index]->weight = $res[$index]->weight + 1;
                }
            }
        }
        usort($res, 'sortSearch');

        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }

    /**
     * AJAX search for products by title, description, or model/sku
     *
     */
    public function searchNew() {
        global $db, $user;
        //$this->params['query'] = str_ireplace('-','\-',$this->params['query']);
        $sql = "select DISTINCT(p.id) as id, p.title, model, sef_url, f.id as fileid, ";
        $sql .= "match (p.title,p.model,p.body) against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) as relevance, ";
        $sql .= "CASE when p.model like '" . $this->params['query'] . "%' then 1 else 0 END as modelmatch, ";
        $sql .= "CASE when p.title like '%" . $this->params['query'] . "%' then 1 else 0 END as titlematch ";
        $sql .= "from " . $db->prefix . "product as p INNER JOIN " .
            $db->prefix . "content_expFiles as cef ON p.id=cef.content_id AND cef.content_type IN ('product','eventregistration','donation','giftcard') AND cef.subtype='mainimage'  INNER JOIN " . $db->prefix .
            "expFiles as f ON cef.expFiles_id = f.id WHERE ";
        if (!$user->isAdmin()) $sql .= '(p.active_type=0 OR p.active_type=1) AND ';
        $sql .= " match (p.title,p.model,p.body) against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) AND p.parent_id=0 ";
        $sql .= " HAVING relevance > 0 ";
        //$sql .= "GROUP BY p.id "; 
        $sql .= "order by modelmatch,titlematch,relevance desc LIMIT 10";

        eDebug($sql);
        $res = $db->selectObjectsBySql($sql);
        eDebug($res, true);
        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }

    function batch_process() {
        $os = new order_status();
        $oss = $os->find('all',1,'rank');
        $order_status = array();
        $order_status[-1] = '';
        foreach ($oss as $status) {
            $order_status[$status->id] = $status->title;
        }
        assign_to_template(array(
            'order_status' => $order_status
        ));
    }

    function process_orders() {
        /*
          Testing
        */
        /*echo "Here?";
        $inv = 30234;
        $req = 'a29f9shsgh32hsf80s7';        
        $amt = 101.00;
        for($count=1;$count<=25;$count+=2)
        {   
            $data[2] = $inv + $count;
            $amt += $count*$count;
            $successSet[$count]['message'] = "Sucessfully imported row " . $count . ", order: " . $data[2] . "<br/>";                
            $successSet[$count]['order_id'] = $data[2];
            $successSet[$count]['amount'] = $amt;
            $successSet[$count]['request_id'] = $req;
            $successSet[$count]['reference_id'] = $req;
            $successSet[$count]['authorization_code'] = $req;
            $successSet[$count]['shipping_tracking_number'] = '1ZNF453937547';    
            $successSet[$count]['carrier'] = 'UPS';
        }
        for($count=2;$count<=25;$count+=2)
        {   
            $data[2] = $inv + $count;                
            $amt += $count*$count;        
            $errorSet[$count]['error_code'] = '42';
            $errorSet[$count]['message'] = "No go for some odd reason. Try again.";
            $errorSet[$count]['order_id'] = $data[2];
            $errorSet[$count]['amount'] = $amt;
        }
        
        assign_to_template(array('errorSet'=>$errorSet, 'successSet'=>$successSet));     
        return;*/

        ###########

        global $db;
        $template = expTemplate::get_template_for_action(new orderController(), 'setStatus', $this->loc);

        //eDebug($_FILES);
        //eDebug($this->params,true); 
        set_time_limit(0);
        //$file = new expFile($this->params['expFile']['batch_process_upload'][0]);
        if (!empty($_FILES['batch_upload_file']['error'])) {
            flash('error', gt('There was an error uploading your file.  Please try again.'));
            redirect_to(array('controller' => 'store', 'action' => 'batch_process'));
//            $this->batch_process();
        }

        $file = new stdClass();
        $file->path = $_FILES['batch_upload_file']['tmp_name'];
        echo "Validating file...<br/>";

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $checkhandle = fopen($file->path, "r");
        // read in the header line
        $checkdata = fgetcsv($checkhandle, 10000, ",");
        $fieldCount = count($checkdata);
        $count = 1;
        // read in the data lines
        while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
            $count++;
            if (count($checkdata) != $fieldCount) {
                echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                exit();
            }
        }
        fclose($checkhandle);
        ini_set('auto_detect_line_endings',$line_end);

        echo "<br/>CSV File passed validation...<br/><br/>Detecting carrier type....<br/>";
        //exit();
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $handle = fopen($file->path, "r");

        // read in the header line
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);      
//        $dataset = array();
        $carrier = '';
        if (trim($data[0]) == 'ShipmentInformationShipmentID') {
            echo "Detected UPS file...<br/>";
            $carrier = "UPS";
            $carrierTrackingLink = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=";
        } elseif (trim($data[0]) == 'PIC') {
            echo "Detected United States Post Service file...<br/>";
            $carrier = "USPS";
            $carrierTrackingLink = "https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=";
        }

        //eDebug($carrier);
        $count = 1;
        $errorSet = array();
        $successSet = array();

        $oo = new order();

        // read in the data lines
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;
            $originalOrderId = $data[2];
            $data[2] = intval($data[2]);
            $order = new stdClass();
            $bm = new stdClass();
            $transactionState = null;

            //check for valid order number - if not present or not order, fail and continue with next record
            if (isset($data[2]) && !empty($data[2])) {
                $order = $oo->findBy('invoice_id', $data[2]);
                if (empty($order->id)) {
                    $errorSet[$count]['message'] = $originalOrderId . " is not a valid order in this system.";
                    $errorSet[$count]['order_id'] = $originalOrderId;
                    continue;
                }
            } else {
                $errorSet[$count]['message'] = "Row " . $count . " has no order number.";
                $errorSet[$count]['order_id'] = "N/A";
                continue;
            }

            /*we have a valid order, so let's see what we can do: */

            //set status of order to var
            $currentStat = $order->order_status;
            //eDebug($currentStat,true);

            //-- check the order for a closed status - if so, do NOT process or set shipping
            if ($currentStat->treat_as_closed == true) {
                $errorSet[$count]['message'] = "This is currently a closed order. Not processing.";
                $errorSet[$count]['order_id'] = $data[2];
                continue;
            }

            //ok, if we made it here we have a valid order that is "open"
            //we'll try to capture the transaction if it's in an authorized state, but set shipping regardless
            if (isset($order->billingmethod[0])) {
                $bm = $order->billingmethod[0];
                $transactionState = $bm->transaction_state;
            } else {
                $bm = null;
                $transactionState = '';
            }

            if ($transactionState == 'authorized') {
                //eDebug($order,true);
                $calc = $bm->billingcalculator->calculator;
                $calc->config = $bm->billingcalculator->config;
                if (method_exists($calc, 'delayed_capture')) {
                    //$result = $calc->delayed_capture($bm,$bm->billing_cost);
                    $result = $calc->delayed_capture($bm, $order->grand_total, $order);
                    if ($result->errorCode == 0) {
                        //we've succeeded.  transaction already created and billing info updated.
                        //just need to set the order shipping info, check and see if we send user an email, and set statuses.  
                        //shipping info:                                      
                        $successSet[$count]['order_id'] = $data[2];
                        $successSet[$count]['message'] = "Sucessfully captured order " . $data[2] . " and set shipping information.";
                        $successSet[$count]['amount'] = $order->grand_total;
                        $successSet[$count]['request_id'] = $result->request_id;
                        $successSet[$count]['reference_id'] = $result->PNREF;
                        $successSet[$count]['authorization_code'] = $result->AUTHCODE;
                        $successSet[$count]['shipping_tracking_number'] = $data[0];
                        $successSet[$count]['carrier'] = $carrier;
                    } else {
                        //failed capture, so we report the error but still set the shipping information
                        //because it's already out the door
                        //$failMessage = "Attempted to delay capture order " . $data[2] . " and it failed with the following error: " . $result->errorCode . " - " .$result->message;   
                        //if the user seelected to set a different status for failed orders, set it here.
                        /*if(isset($this->params['order_status_fail'][0]) && $this->params['order_status_fail'][0] > -1)
                        {
                            $change = new order_status_changes();
                            // save the changes
                            $change->from_status_id = $order->order_status_id;
                            //$change->comment = $this->params['comment'];
                            $change->to_status_id = $this->params['order_status_fail'][0];
                            $change->orders_id = $order->id;
                            $change->save();
                            
                            // update the status of the order
                            $order->order_status_id = $this->params['order_status_fail'][0];
                            $order->save();                             
                        }*/
                        $errorSet[$count]['error_code'] = $result->errorCode;
                        $errorSet[$count]['message'] = "Capture failed: " . $result->message . "<br/>Setting shipping information.";
                        $errorSet[$count]['order_id'] = $data[2];
                        $errorSet[$count]['amount'] = $order->grand_total;
                        $errorSet[$count]['shipping_tracking_number'] = $data[0];
                        $errorSet[$count]['carrier'] = $carrier;
                        //continue;   
                    }
                } else {
                    //dont suppose we do anything here, as it may be set to approved manually 
                    //$errorSet[$count] = "Order " . $data[2] . " does not use a billing method with delayed capture ability.";  
                    $successSet[$count]['message'] = 'No capture processing available for order:' . $data[2] . '. Setting shipping information.';
                    $successSet[$count]['order_id'] = $data[2];
                    $successSet[$count]['amount'] = $order->grand_total;
                    $successSet[$count]['shipping_tracking_number'] = $data[0];
                    $successSet[$count]['carrier'] = $carrier;
                }
            } //if we hit this else, it means we have an order that is not in an authorized state
            //so we do not try to process it = still set shipping though.  //FIXME what about 'complete'?
            else {
                $successSet[$count]['message'] = 'No processing necessary for order:' . $data[2] . '. Setting shipping information.';
                $successSet[$count]['order_id'] = $data[2];
                $successSet[$count]['amount'] = $order->grand_total;
                $successSet[$count]['shipping_tracking_number'] = $data[0];
                $successSet[$count]['carrier'] = $carrier;
            }

            $order->shipped = time();
            $order->shipping_tracking_number = $data[0];
            $order->save();

            $s = array_pop($order->shippingmethods);
            $sm = new shippingmethod($s->id);
            $sm->carrier = $carrier;
            $sm->save();

            //statuses and email
            if (isset($this->params['order_status_success'][0]) && $this->params['order_status_success'][0] > -1) {
                $change = new order_status_changes();
                // save the changes
                $change->from_status_id = $order->order_status_id;
                //$change->comment = $this->params['comment'];
                $change->to_status_id = $this->params['order_status_success'][0];
                $change->orders_id = $order->id;
                $change->save();

                // update the status of the order
                $order->order_status_id = $this->params['order_status_success'][0];
                $order->save();

                // email the user if we need to
                if (!empty($this->params['email_customer'])) {
                    $email_addy = $order->billingmethod[0]->email;
                    if (!empty($email_addy)) {
                        $from_status = $db->selectValue('order_status', 'title', 'id=' . $change->from_status_id);
                        $to_status = $db->selectValue('order_status', 'title', 'id=' . $change->to_status_id);
//                        $template->assign(
                        assign_to_template(
                            array(
                                'comment'          => $change->comment,
                                'to_status'        => $to_status,
                                'from_status'      => $from_status,
                                'order'            => $order,
                                'date'             => date("F j, Y, g:i a"),
                                'storename'        => ecomconfig::getConfig('storename'),
                                'include_shipping' => true,
                                'tracking_link'    => $carrierTrackingLink . $order->shipping_tracking_number,
                                'carrier'          => $carrier
                            )
                        );

                        $html = $template->render();
                        $html .= ecomconfig::getConfig('ecomfooter');

                        $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
                        if (empty($from[0])) $from = SMTP_FROMADDRESS;
                        try {
                            $mail = new expMail();
                            $mail->quickSend(array(
                                'html_message' => $html,
                                'text_message' => str_replace("<br>", "\r\n", $template->render()),
                                'to'           => array($email_addy => $order->billingmethod[0]->firstname . ' ' . $order->billingmethod[0]->lastname),
                                'from'         => $from,
                                'subject'      => 'Your Order Has Been Shipped (#' . $order->invoice_id . ') - ' . ecomconfig::getConfig('storename')
                            ));
                        } catch (Exception $e) {
                            //do nothing for now
                            eDebug("Email error:");
                            eDebug($e);
                        }
                    }
                    //else {
                    //    $errorSet[$count]['message'] .= "<br/>Order " . $data[2] . " was captured successfully, however the email notification was not successful.";
                    //}
                }
            }

            //eDebug($product);        
        }
        fclose($handle);
        ini_set('auto_detect_line_endings',$line_end);

        assign_to_template(array(
            'errorSet'   => $errorSet,
            'successSet' => $successSet
        ));
    }

    function manage_sales_reps() {

    }

    function showHistory() {
        $h = new expHistory();
//        echo "<xmp>";
        echo "<pre>";
        print_r($h);
//        echo "</xmp>";
        echo "</pre>";
    }

    function import_external_addresses() {
        $sources = array('mc' => 'MilitaryClothing.com', 'nt' => 'NameTapes.com', 'am' => 'Amazon');
        assign_to_template(array(
            'sources' => $sources
        ));
    }

    function process_external_addresses() {
        global $db;
        set_time_limit(0);
        //$file = new expFile($this->params['expFile']['batch_process_upload'][0]);
        eDebug($this->params);
//        eDebug($_FILES,true);
        if (!empty($_FILES['address_csv']['error'])) {
            flash('error', gt('There was an error uploading your file.  Please try again.'));
            redirect_to(array('controller' => 'store', 'action' => 'import_external_addresses'));
//            $this->import_external_addresses();
        }

        $file = new stdClass();
        $file->path = $_FILES['address_csv']['tmp_name'];
        echo "Validating file...<br/>";

        //replace tabs with commas
        /*if($this->params['type_of_address'][0] == 'am')
        {
            $checkhandle = fopen($file->path, "w");
            $oldFile = file_get_contents($file->path);
            $newFile = str_ireplace(chr(9),',',$oldFile);
            fwrite($checkhandle,$newFile);
            fclose($checkhandle);
        }*/

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $checkhandle = fopen($file->path, "r");
        if ($this->params['type_of_address'][0] == 'am') {
            // read in the header line
            $checkdata = fgetcsv($checkhandle, 10000, "\t");
            $fieldCount = count($checkdata);
        } else {
            // read in the header line
            $checkdata = fgetcsv($checkhandle, 10000, ",");
            $fieldCount = count($checkdata);
        }

        $count = 1;
        if ($this->params['type_of_address'][0] == 'am') {
            // read in the data lines
            while (($checkdata = fgetcsv($checkhandle, 10000, "\t")) !== FALSE) {
                $count++;
                //eDebug($checkdata);
                if (count($checkdata) != $fieldCount) {
                    echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                    echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                    exit();
                }
            }
        } else {
            // read in the data lines
            while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
                $count++;
                if (count($checkdata) != $fieldCount) {
                    echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                    echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                    exit();
                }
            }
        }

        fclose($checkhandle);
        ini_set('auto_detect_line_endings',$line_end);

        echo "<br/>CSV File passed validation...<br/><br/>Importing....<br/><br/>";
        //exit();
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $handle = fopen($file->path, "r");

        // read in the header line and discard it
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);      
//        $dataset = array();

        //mc=1, nt=2, amm=3

        if ($this->params['type_of_address'][0] == 'mc') {
            //militaryclothing
            $db->delete('external_addresses', 'source=1');

        } else if ($this->params['type_of_address'][0] == 'nt') {
            //nametapes
            $db->delete('external_addresses', 'source=2');
        } else if ($this->params['type_of_address'][0] == 'am') {
            //amazon
            $db->delete('external_addresses', 'source=3');
        }

        if ($this->params['type_of_address'][0] == 'am') {
            // read in the data lines
            while (($data = fgetcsv($handle, 10000, "\t")) !== FALSE) {
                //eDebug($data,true);
                $extAddy = new external_address();

                //eDebug($data);
                $extAddy->source = 3;
                $extAddy->user_id = 0;
                $name = explode(' ', $data[15]);
                $extAddy->firstname = $name[0];
                if (isset($name[3])) {
                    $extAddy->firstname .= ' ' . $name[1];
                    $extAddy->middlename = $name[2];
                    $extAddy->lastname = $name[3];
                } else if (isset($name[2])) {
                    $extAddy->middlename = $name[1];
                    $extAddy->lastname = $name[2];
                } else {
                    $extAddy->lastname = $name[1];
                }
                $extAddy->organization = $data[15];
                $extAddy->address1 = $data[16];
                $extAddy->address2 = $data[17];
                $extAddy->city = $data[19];
                $state = new geoRegion();
                $state = $state->findBy('code', trim($data[20]));
                if (empty($state->id)) {
                    $state = new geoRegion();
                    $state = $state->findBy('name', trim($data[20]));
                }
                $extAddy->state = $state->id;
                $extAddy->zip = str_ireplace("'", '', $data[21]);
                $extAddy->phone = $data[6];
                $extAddy->email = $data[4];
                //eDebug($extAddy);
                $extAddy->save();
            }
        } else {
            // read in the data lines
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                eDebug($data);
                $extAddy = new external_address();
                if ($this->params['type_of_address'][0] == 'mc') {
                    $extAddy->source = 1;
                    $extAddy->user_id = 0;
                    $name = explode(' ', $data[3]);
                    $extAddy->firstname = $name[0];
                    if (isset($name[2])) {
                        $extAddy->middlename = $name[1];
                        $extAddy->lastname = $name[2];
                    } else {
                        $extAddy->lastname = $name[1];
                    }
                    $extAddy->organization = $data[4];
                    $extAddy->address1 = $data[5];
                    $extAddy->address2 = $data[6];
                    $extAddy->city = $data[7];
                    $state = new geoRegion();
                    $state = $state->findBy('code', $data[8]);
                    $extAddy->state = $state->id;
                    $extAddy->zip = str_ireplace("'", '', $data[9]);
                    $extAddy->phone = $data[20];
                    $extAddy->email = $data[21];
                    //eDebug($extAddy);
                    $extAddy->save();

                    //Check if the shipping add is same as the billing add
                    if ($data[5] != $data[14]) {
                        $extAddy = new external_address();
                        $extAddy->source = 1;
                        $extAddy->user_id = 0;
                        $name = explode(' ', $data[12]);
                        $extAddy->firstname = $name[0];
                        if (isset($name[2])) {
                            $extAddy->middlename = $name[1];
                            $extAddy->lastname = $name[2];
                        } else {
                            $extAddy->lastname = $name[1];
                        }
                        $extAddy->organization = $data[13];
                        $extAddy->address1 = $data[14];
                        $extAddy->address2 = $data[15];
                        $extAddy->city = $data[16];
                        $state = new geoRegion();
                        $state = $state->findBy('code', $data[17]);
                        $extAddy->state = $state->id;
                        $extAddy->zip = str_ireplace("'", '', $data[18]);
                        $extAddy->phone = $data[20];
                        $extAddy->email = $data[21];
                        // eDebug($extAddy, true);
                        $extAddy->save();
                    }
                }
                if ($this->params['type_of_address'][0] == 'nt') {
                    //eDebug($data,true);
                    $extAddy->source = 2;
                    $extAddy->user_id = 0;
                    $extAddy->firstname = $data[16];
                    $extAddy->lastname = $data[17];
                    $extAddy->organization = $data[15];
                    $extAddy->address1 = $data[18];
                    $extAddy->address2 = $data[19];
                    $extAddy->city = $data[20];
                    $state = new geoRegion();
                    $state = $state->findBy('code', $data[21]);
                    $extAddy->state = $state->id;
                    $extAddy->zip = str_ireplace("'", '', $data[22]);
                    $extAddy->phone = $data[23];
                    $extAddy->email = $data[13];
                    //eDebug($extAddy);
                    $extAddy->save();
                }
            }
        }
        fclose($handle);
        ini_set('auto_detect_line_endings',$line_end);
        echo "Done!";
    }

    function nonUnicodeProducts() {
        global $db, $user;

        $products = $db->selectObjectsIndexedArray('product');
        $affected_fields = array();
        $listings = array();
        $listedProducts = array();
        $count = 0;
        //Get all the columns of the product table
        $columns = $db->getTextColumns('product');
        foreach ($products as $item) {

            foreach ($columns as $column) {
                if ($column != 'body' && $column != 'summary' && $column != 'featured_body') {
                    if (!expString::validUTF($item->$column) || strrpos($item->$column, '?')) {
                        $affected_fields[] = $column;
                    }
                } else {
                    if (!expString::validUTF($item->$column)) {
                        $affected_fields[] = $column;
                    }
                }
            }

            if (isset($affected_fields)) {
                if (count($affected_fields) > 0) {
                    //Hard coded fields since this is only for displaying
                    $listedProducts[$count]['id'] = $item->id;
                    $listedProducts[$count]['title'] = $item->title;
                    $listedProducts[$count]['model'] = $item->model;
                    $listedProducts[$count]['sef_url'] = $item->sef_url;
                    $listedProducts[$count]['nonunicode'] = implode(', ', $affected_fields);
                    $count++;
                }
            }
            unset($affected_fields);
        }

        assign_to_template(array(
            'products' => $listedProducts,
            'count'    => $count
        ));
    }

    function cleanNonUnicodeProducts() {
        global $db, $user;

        $products = $db->selectObjectsIndexedArray('product');
        //Get all the columns of the product table
        $columns = $db->getTextColumns('product');
        foreach ($products as $item) {
            //Since body, summary, featured_body can have a ? intentionally such as a link with get parameter.
            //TO Improved
            foreach ($columns as $column) {
                if ($column != 'body' && $column != 'summary' && $column != 'featured_body') {
                    if (!expString::validUTF($item->$column) || strrpos($item->$column, '?')) {
                        $item->$column = expString::convertUTF($item->$column);
                    }
                } else {
                    if (!expString::validUTF($item->$column)) {
                        $item->$column = expString::convertUTF($item->$column);
                    }
                }
            }

            $db->updateObject($item, 'product');
        }

        redirect_to(array('controller' => 'store', 'action' => 'nonUnicodeProducts'));
//        $this->nonUnicodeProducts();
    }

    //This function is being used in the uploadModelaliases page for showing the form upload
    function uploadModelAliases() {
        global $db;
        set_time_limit(0);

        if (isset($_FILES['modelaliases']['tmp_name'])) {
            if (!empty($_FILES['modelaliases']['error'])) {
                flash('error', gt('There was an error uploading your file.  Please try again.'));
//				redirect_to(array('controller'=>'store','action'=>'uploadModelAliases'));
                $this->uploadModelAliases();
            }

            $file = new stdClass();
            $file->path = $_FILES['modelaliases']['tmp_name'];
            echo "Validating file...<br/>";

            $line_end = ini_get('auto_detect_line_endings');
            ini_set('auto_detect_line_endings',TRUE);
            $checkhandle = fopen($file->path, "r");
            // read in the header line
            $checkdata = fgetcsv($checkhandle, 10000, ",");
            $fieldCount = count($checkdata);
            $count = 1;

            // read in the data lines
            while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
                $count++;
                if (count($checkdata) != $fieldCount) {
                    echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                    echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                    exit();
                }
            }

            fclose($checkhandle);
            ini_set('auto_detect_line_endings',$line_end);

            echo "<br/>CSV File passed validation...<br/><br/>Importing....<br/><br/>";
            $line_end = ini_get('auto_detect_line_endings');
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen($file->path, "r");
            // read in the header line
            $data = fgetcsv($handle, 10000, ",");

            //clear the db
            $db->delete('model_aliases_tmp');
            // read in the data lines
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

                $tmp = new stdClass();
                $tmp->field1 = expString::onlyReadables($data[0]);
                $tmp->field2 = expString::onlyReadables($data[1]);
                $db->insertObject($tmp, 'model_aliases_tmp');
            }
            fclose($handle);
            ini_set('auto_detect_line_endings',$line_end);
            redirect_to(array('controller' => 'store', 'action' => 'processModelAliases'));
            echo "Done!";
        }

        //check if there are interrupted model alias in the db
        $res = $db->selectObjectsBySql("SELECT * FROM ".$db->prefix."model_aliases_tmp WHERE is_processed = 0");
        if (!empty($res)) {
            assign_to_template(array(
                'continue' => '1'
            ));
        }
    }

    // This function process the uploading of the model aliases in the uploadModelAliases page
    function processModelAliases($index = 0, $error = '') {
        global $db;

        //Going next and delete the previous one
        if (isset($this->params['index'])) {
            $index = $this->params['index'];

            //if go to the next processs
            if (isset($this->params['next'])) {
                $res = $db->selectObjectBySql("SELECT * FROM ".$db->prefix."model_aliases_tmp LIMIT " . ($index - 1) . ", 1");
                //Update the record in the tmp table to mark it as process
                $res->is_processed = 1;
                $db->updateObject($res, 'model_aliases_tmp');
            }
        }

        $product_id = '';
        $autocomplete = '';

        do {
            $count = $db->countObjects('model_aliases_tmp', 'is_processed=0');
            $res = $db->selectObjectBySql("SELECT * FROM ".$db->prefix."model_aliases_tmp LIMIT {$index}, 1");
            //Validation
            //Check the field one
            if (!empty($res)) {
                $product_field1 = $db->selectObject("product", "model='{$res->field1}'");
                $product_field2 = $db->selectObject("product", "model='{$res->field2}'");
            }
            if (!empty($product_field1)) {
                $product_id = $product_field1->id;
                //check the other field if it also being used by another product
                if (!empty($product_field2) && $product_field1->id != $product_field2->id) {
                    $error = "Both {$res->field1} and {$res->field2} are models of a product. <br />";
                } else {
                    //Check the field2 if it is already in the model alias
                    $model_alias = $db->selectObject("model_aliases", "model='{$res->field2}'");
                    if (empty($model_alias) && @$model_alias->product_id != $product_field1->id) {
                        //Add the first field
                        $tmp = new  stdClass();
                        $tmp->model = $res->field1;
                        $tmp->product_id = $product_field1->id;
                        $db->insertObject($tmp, 'model_aliases');
                        //Add the second field
                        $tmp->model = $res->field2;
                        $tmp->product_id = $product_field1->id;
                        $db->insertObject($tmp, 'model_aliases');
                        //Update the record in the tmp table to mark it as process
                        $res->is_processed = 1;
                        $db->updateObject($res, 'model_aliases_tmp');

                    } else {
                        $error = "{$res->field2} has already a product alias. <br />";
                    }
                }
            } elseif (!empty($product_field2)) {
                $product_id = $product_field2->id;
                $model_alias = $db->selectObject("model_aliases", "model='{$res->field1}'");
                if (empty($model_alias) && @$model_alias->product_id != $product_field2->id) {
                    //Add the first field
                    $tmp = new stdClass();
                    $tmp->model = $res->field1;
                    $tmp->product_id = $product_field2->id;
                    $db->insertObject($tmp, 'model_aliases');
                    //Add the second field
                    $tmp->model = $res->field2;
                    $tmp->product_id = $product_field2->id;
                    $db->insertObject($tmp, 'model_aliases');
                    //Update the record in the tmp table to mark it as process
                    $res->is_processed = 1;
                    $db->updateObject($res, 'model_aliases_tmp');
                } else {
                    $error = "{$res->field1} has already a product alias. <br />";
                }
            } else {
                $model_alias1 = $db->selectObject("model_aliases", "model='{$res->field1}'");
                $model_alias2 = $db->selectObject("model_aliases", "model='{$res->field2}'");

                if (!empty($model_alias1) || !empty($model_alias2)) {
                    $error = "The {$res->field1} and {$res->field2} are already being used by another product.<br />";
                } else {
                    $error = gt("No product match found, please choose a product to be alias in the following models below") . ":<br />";
                    $error .= $res->field1 . "<br />";
                    $error .= $res->field2 . "<br />";
                    $autocomplete = 1;
                }
            }
            $index++;
        } while (empty($error));
        assign_to_template(array(
            'count'        => $count,
            'alias'        => $res,
            'index'        => $index,
            'product_id'   => $product_id,
            'autocomplete' => $autocomplete,
            'error'        => $error
        ));
    }

    // This function save the uploaded processed model aliases in the uploadModelAliases page
    function saveModelAliases() {
        global $db;

        $index = $this->params['index'];
        $title = expString::escape($this->params['product_title']);
        $product = $db->selectObject("product", "title='{$title}'");

        if (!empty($product->id)) {
            $res = $db->selectObjectBySql("SELECT * FROM ".$db->prefix."model_aliases_tmp LIMIT " . ($index - 1) . ", 1");
            //Add the first field
            $tmp = new stdClass();
            $tmp->model = $res->field1;
            $tmp->product_id = $product->id;
            $db->insertObject($tmp, 'model_aliases');
            //Add the second field
            $tmp->model = $res->field2;
            $tmp->product_id = $product->id;
            $db->insertObject($tmp, 'model_aliases');

            //if the model is empty, update the product table so that it will used the field 1 as its primary model
            if (empty($product->model)) {
                $product->model = $res->field1;
                $db->updateObject($product, 'product');
            }

            //Update the record in the tmp table to mark it as process
            $res->is_processed = 1;
            $db->updateObject($res, 'model_aliases_tmp');
            flash("message", gt("Product successfully Saved."));
            redirect_to(array('controller' => 'store', 'action' => 'processModelAliases', 'index' => $index));
        } else {
            flash("error", gt("Product title is invalid."));
            redirect_to(array('controller' => 'store', 'action' => 'processModelAliases', 'index' => $index - 1, 'error' => 'Product title is invalid.'));
        }
    }

    // This function delete all the already processed model aliases in the uploadModelAliases page
    function deleteProcessedModelAliases() {
        global $db;

        $db->delete('model_aliases_tmp', 'is_processed=1');
        redirect_to(array('controller' => 'store', 'action' => 'processModelAliases'));
    }

    // This function show the form of model alias to be edit or add in the product edit page
    function edit_model_alias() {
        global $db;

        if (isset($this->params['id'])) {
            $model_alias = $db->selectObject('model_aliases', 'id =' . $this->params['id']);
            assign_to_template(array(
                'model_alias' => $model_alias
            ));
        } else {
            assign_to_template(array(
                'product_id' => $this->params['product_id']
            ));
        }
    }

    // This function update or add the model alias in the product edit page
    function update_model_alias() {
        global $db;

        if (empty($this->params['id'])) {
            $obj = new stdClass();
            $obj->model = $this->params['model'];
            $obj->product_id = $this->params['product_id'];
            $db->insertObject($obj, 'model_aliases');

        } else {
            $model_alias = $db->selectObject('model_aliases', 'id =' . $this->params['id']);
            $model_alias->model = $this->params['model'];
            $db->updateObject($model_alias, 'model_aliases');
        }

        expHistory::back();
    }

    // This function delete the model alias in the product edit page
    function delete_model_alias() {
        global $db;

        if (empty($this->params['id'])) return false;
        $db->delete('model_aliases', 'id =' . $this->params['id']);

        expHistory::back();
    }

    function setup_wizard() {

    }

    function import() {
        assign_to_template(array(
            'type' => $this
        ));
    }

    function importProduct($file=null) {
        if (empty($file->path)) {
            $file = new stdClass();
            $file->path = $_FILES['import_file']['tmp_name'];
        }
        if (empty($file->path)) {
            echo gt('Not a Product Import CSV File');
            return;
        }
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $handle = fopen($file->path, "r");

        // read in the header line
        $header = fgetcsv($handle, 10000, ",");
        if (!($header[0] == 'id' || $header[0] == 'model')) {
            echo gt('Not a Product Import CSV File');
            return;
        }

        $count = 1;
        $errorSet = array();
        $product = null;
        /*  original order of columns
            0=id
            1=parent_id
            2=child_rank
            3=title
            4=body
            5=model
            6=warehouse_location
            7=sef_url
//FIXME        this is where canonical should be
            8=meta_title
            9=meta_keywords
            10=meta_description
            11=tax_class_id
            12=quantity
            13=availability_type
            14=base_price
            15=special_price
            16=use_special_price
            17=active_type
            18=product_status_id
            19=category1
            20=category2
            21=category3
            22=category4
            ..
            30=category12
            31=surcharge
            32=rank category_rank
            33=feed_title
            34=feed_body
            35=weight
            36=height
            37=width
            38=length
            39=companies_id
            40=image1 url to mainimage to download
            41=image2 url to additional image to download
            ..
            44=image5 url to additional image to download
*/

        // read in the data lines
//        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;
            $createCats = array();
            $createCatsRank = array();
            $data = array_combine($header, $row);

            //eDebug($data, true);
            if ($header[0] == 'id') {
                if (isset($data['id']) && $data['id'] != 0) {
                    $product = new product($data['id'], false, false);
                    if (empty($product->id)) {
                        $errorSet[$count] = gt("Is not an existing product ID.");
                        continue;
                    }
                } else {
                    //$errorSet[$count] = "Product ID not supplied.";
                    //continue;
                    $product = new product();
                    //$product->save(false);
                }
            } elseif ($header[0] == 'model') {
                if (!empty($data['model'])) {
                    $p = new product();
                    $product = $p->find('first','model="' . $data['model'] . '"');
                    if (empty($product->id)) {
                        $errorSet[$count] = gt("Is not an existing product SKU/Model.");
                        continue;
                    }
                } else {
                    $product = new product();
                }
            }
            if ($product->product_type != 'product') {
                $errorSet[$count] = gt("Existing product is wrong product type.");
                continue;
            }

            // new products must have a title
            if (empty($product->id)) {  // new product require mandatory values
                $checkTitle = trim($data['title']);
                if (empty($checkTitle)) {
                    $errorSet[$count] = gt("No product name (title) supplied.");
                    continue;
                }
                $product->minimum_order_quantity = 1;
            }

            // parse $data columns
            foreach ($data as $key=>$value) {
                $value = trim($value);
                switch ($key) {
                    case 'parent_id': // integer
                    case 'child_rank':
                    case 'tax_class_id':
                    case 'quantity':
                    case 'availability_type':
                    case 'use_special_price':
                    case 'active_type':
                    case 'product_status_id':
                        $product->$key = intval($value);
                        break;
                    case 'companies_id':
                        if (is_numeric($value)) {
                            $product->$key = intval($value);
                        } elseif (!empty($value)) {  // it's a company name, not a company id#
                            $co = new company();
                            $company = $co->find('first', 'title=' . $value);
                            if (empty($company->id)) {
                                $params['title'] = $value;
                                $company->update();
                            }
                            $product->$key = $company->id;
                        }
                        break;
                    case 'sef_url':
                        $product->$key = stripslashes(stripslashes($value));
                        if (!is_bool(expValidator::uniqueness_of('sef_url', $product, array()))) {
                            $product->makeSefUrl();
                        }
                        break;
                    case 'title':  // string
                    case 'model':
                    case 'warehouse_location':
                    case 'meta_title':
                    case 'meta_keywords':
                    case 'meta_description':
                    case 'feed_title':
                    case 'feed_body':
                        $product->$key = stripslashes(stripslashes($value));
                        break;
                    case 'body':
                        $product->$key = utf8_encode(stripslashes(expString::parseAndTrimImport(($value), true)));
                        break;
                    case 'base_price':  // float
                    case 'special_price':
                    case 'surcharge':
                    case 'weight':
                    case 'height':
                    case 'width':
                    case 'length':
                        $product->$key = floatval($value);
                        break;
                    case 'image1':
                    case 'image2':
                    case 'image3':
                    case 'image4':
                    case 'image5':
                        if (!empty($value)) {
                            $product->save(false);
                            if (is_integer($value)) {
                                $_objFile = new expFile ($value);
                            } else {
                                // import image from url
                                $_destFile = basename($value);  // get filename from end of url
                                $_destDir = UPLOAD_DIRECTORY_RELATIVE;
                                $_destFullPath = BASE . $_destDir . $_destFile;
                                if (file_exists($_destFullPath)) {
                                    $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
                                    $_destFullPath = BASE . $_destDir . $_destFile;
                                }

                                expCore::saveData($value, $_destFullPath);  // download the image

                                if (file_exists($_destFullPath)) {
                                    $__oldumask = umask(0);
                                    chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
                                    umask($__oldumask);

                                    // Create a new expFile Object
                                    $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
                                    $_objFile = new expFile ($_fileParams);
                                    $_objFile->save();
                                }
                            }
                            // attach product images expFile object
                            if (!empty($_objFile->id)) {
                                if ($key == 'image1') {
                                    $product->attachItem($_objFile, 'mainimage');
                                } else {
                                    $product->attachItem($_objFile, 'images', false);
                                }
                            }
                        }
                        break;
                    case 'category1':
                    case 'category2':
                    case 'category3':
                    case 'category4':
                    case 'category5':
                    case 'category6':
                    case 'category7':
                    case 'category8':
                    case 'category9':
                    case 'category10':
                    case 'category11':
                    case 'category12':
                        if ($product->parent_id == 0) {
//                            $rank = !empty($data['rank']) ? $data['rank'] : 1;
                            $rank = intval(str_replace('category', '', $key));
//                            if (!empty($value)) $result = storeCategory::parseCategory($value);
                            if (!empty($value)) $result = storeCategory::importCategoryString($value);
                            else continue;

//                            if (is_numeric($result)) {
                            if ($result) {
                                $createCats[] = $result;
                                $createCatsRank[$result] = $rank;
                            } else {
                                $errorSet[$count][] = $result;
                                continue 2;
                            }
                        }
                        break;
                    default:
                        if (property_exists('product', $key)) {
                            $product->key = $value;
                        }
                }
            }

//            $checkTitle = trim($data['title']);
//            if (empty($checkTitle)) {
//                $errorSet[$count] = gt("No product name (title) supplied, skipping this record...");
//                continue;
//            }
//            $product->parent_id = $data[1];
//            $product->child_rank = $data[2];
//            $product->title = stripslashes(stripslashes($data[3]));
//            $product->body = utf8_encode(stripslashes(expString::parseAndTrimImport(($data[4]), true)));
//            //$product->body = utf8_encode(stripslashes(stripslashes(($data[4]))));
//            $product->model = stripslashes(stripslashes($data[5]));
//            $product->warehouse_location = stripslashes(stripslashes($data[6]));
//            $product->sef_url = stripslashes(stripslashes($data[7]));
////FIXME        this is where canonical should be
//            $product->meta_title = stripslashes(stripslashes($data[8]));
//            $product->meta_keywords = stripslashes(stripslashes($data[9]));
//            $product->meta_description = stripslashes(stripslashes($data[10]));
//
//            $product->tax_class_id = $data[11];
//
//            $product->quantity = $data[12];
//
//            $product->availability_type = $data[13];
//
//            $product->base_price = $data[14];
//            $product->special_price = $data[15];
//            $product->use_special_price = $data[16];
//            $product->active_type = $data[17];
//            $product->product_status_id = $data[18];
//
//            $product->surcharge = $data[31];
//            $product->feed_title = stripslashes(stripslashes($data[33]));
//            $product->feed_body = stripslashes(stripslashes($data[34]));
//            if (!empty($data[35])) $product->weight = $data[35];
//            if (!empty($data[36])) $product->height = $data[36];
//            if (!empty($data[37])) $product->width = $data[37];
//            if (!empty($data[38])) $product->length = $data[38];
//            if (!empty($data[39])) $product->companies_id = $data[39];
//            if (!empty($data[40])) {
//                // import image from url
//                $_destFile = basename($data[40]);  // get filename from end of url
//                $_destDir = UPLOAD_DIRECTORY_RELATIVE;
//                $_destFullPath = BASE . $_destDir . $_destFile;
//                if (file_exists($_destFullPath)) {
//                    $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
//                    $_destFullPath = BASE . $_destDir . $_destFile;
//                }
//
//                expCore::saveData($data[40], $_destFullPath);  // download the image
//
//                if (file_exists($_destFullPath)) {
//                    $__oldumask = umask(0);
//                    chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
//                    umask($__oldumask);
//
//                    // Create a new expFile Object
//                    $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
//                    $_objFile = new expFile ($_fileParams);
//                    $_objFile->save();
//                    // attach/replace product main image with new expFile object
//                    $product->attachItem($_objFile, 'mainimage');
//                }
//            }
//            for ($i=41; $i<=44; $i++) {
//                if (!empty($data[$i])) {
//                    // import image from url
//                    $_destFile = basename($data[$i]);  // get filename from end of url
//                    $_destDir = UPLOAD_DIRECTORY_RELATIVE;
//                    $_destFullPath = BASE . $_destDir . $_destFile;
//                    if (file_exists($_destFullPath)) {
//                        $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
//                        $_destFullPath = BASE . $_destDir . $_destFile;
//                    }
//
//                    expCore::saveData($data[$i], $_destFullPath);  // download the image
//
//                    if (file_exists($_destFullPath)) {
//                        $__oldumask = umask(0);
//                        chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
//                        umask($__oldumask);
//
//                        // Create a new expFile Object
//                        $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
//                        $_objFile = new expFile ($_fileParams);
//                        $_objFile->save();
//                        // attach product additional images with new expFile object
//                        $product->attachItem($_objFile, 'images', false);
//                    }
//                }
//            }
//
//            if (empty($product->id)) $product->minimum_order_quantity = 1;
//
//            if ($product->parent_id == 0) {
//                $createCats = array();
//                $createCatsRank = array();
//                for ($x = 19; $x <= 30; $x++) {
//                    if (!empty($data[$x])) $result = storeCategory::parseCategory($data[$x]);
//                    else continue;
//
//                    if (is_numeric($result)) {
//                        $createCats[] = $result;
//                        $createCatsRank[$result] = $data[32];
//                    } else {
//                        $errorSet[$count][] = $result;
//                        continue 2;
//                    }
//                }
//            }

            //NOTE: we manipulate existing user input fields to store them properly?
            //eDebug($createCats,true);
            if (!empty($product->user_input_fields) && is_array($product->user_input_fields))
                $product->user_input_fields = serialize($product->user_input_fields);
            //eDebug($product->user_input_fields);

            if (!empty($product->user_input_fields) && !is_array($product->user_input_fields))
                $product->user_input_fields = str_replace("'", "\'", $product->user_input_fields);

            //eDebug($product->user_input_fields,true);
            $product->save(true);
            //eDebug($product->body);

            //sort order and categories
            if ($product->parent_id == 0) {
                $product->saveCategories($createCats, $createCatsRank);
                //eDebug($createCatsRank);
            }
            echo "Successfully imported/updated row " . $count . ", product: " . $product->title . "<br/>";
            //eDebug($product);

        }

        if (count($errorSet)) {
            echo "<br/><hr><br/><div style='color:red'><strong>".gt('The following records were NOT imported').":</strong><br/>";
            foreach ($errorSet as $rownum => $err) {
                echo "Row: " . $rownum;
                if (is_array($err)) {
                    foreach ($err as $e) {
                        echo " -- " . $e . "<br/>";
                    }
                } else echo " -- " . $err . "<br/>";
            }
            echo "</div>";
        }

        fclose($handle);
        ini_set('auto_detect_line_endings',$line_end);

        // update search index
        $this->addContentToSearch();
    }

}

?>