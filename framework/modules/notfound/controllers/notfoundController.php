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
 * @subpackage Controllers
 * @package Modules
 */

class notfoundController extends expController {
    //public $basemodel_name = '';
    //public $useractions = array('showall'=>'Show all');
    public $add_permissions = array('showall'=>'Showall', 'show'=>'Show');

    function displayname() { return gt("Not Found Controller"); }
    function description() { return gt("This controller handles routing not found pages to the appropriate place."); }
    function hasSources() { return false; }
    function hasViews() { return false; }
    function hasContent() { return false; }
    
    public function handle() {
        global $router, $db;
        $args = array_merge(array('controller'=>'notfound', 'action'=>'page_not_found'), $router->url_parts);   
        header("Refresh: 0; url=".$router->makeLink($args), false, 404);
    }
    
    public function page_not_found() {
        global $router;
        $params = $router->params;
        unset($params['controller']);
        unset($params['action']);
        $terms = empty($params[0]) ? '' : $params[0];
        if (empty($terms) && !empty($params['title'])) $terms = $params['title'];
        expCSS::pushToHead(array(
	        "unique"=>"search-results",
	        "link"=>$this->asset_path."css/results.css",
	        )
	    );
        // If magic quotes is on and the user uses modifiers like " (quotes) they get escaped. We don't want that in this case.
        if (get_magic_quotes_gpc()) {
            $terms = stripslashes($terms);
        }
        $terms = htmlspecialchars($terms);

        $search = new search();
		$page = new expPaginator(array(
			'model'=>'search',
//			'controller'=>$this->params['controller'],
//			'action'=>$this->params['action'],
			'records'=>$search->getSearchResults($terms),
			//'sql'=>$sql,
			'order'=>'score',
			'dir'=>'DESC',
			));

        assign_to_template(array(
            'page'=>$page,
            'terms'=>$terms
        ));
    }

}

?>