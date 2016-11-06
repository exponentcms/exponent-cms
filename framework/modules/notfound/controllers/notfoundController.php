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
 * @package Modules
 */

class notfoundController extends expController {
    protected $add_permissions = array(
        'showall'=>'Showall',
        'show'=>'Show'
    );

    static function displayname() { return gt("Page Not Found"); }
    static function description() { return gt("This controller handles routing to the appropriate place when pages are not found."); }
    static function hasSources() { return false; }
    static function hasViews() { return false; }
    static function hasContent() { return false; }

    public function handle() {
        global $router;

        $args = array_merge(array('controller'=>'notfound', 'action'=>'page_not_found'), $router->url_parts);
        header("Refresh: 0; url=".$router->makeLink($args), false, 404);
    }

    public function page_not_found() {
        global $router;

        header(':', true, 404);
        $params = $router->params;
        unset(
            $params['controller'],
            $params['action']
        );
        $terms = empty($params[0]) ? '' : $params[0];
        if (empty($terms) && !empty($params['title'])) $terms = $params['title'];
        expCSS::pushToHead(array(
//	        "unique"=>"search-results",
	        "link"=>$this->asset_path."css/results.css",
	        )
	    );
        // If magic quotes is on and the user uses modifiers like " (quotes) they get escaped. We don't want that in this case.
        if (get_magic_quotes_gpc()) {
            $terms = stripslashes($terms);
        }
        $terms = expString::escape(htmlspecialchars($terms));

        // check for server requested error documents here instead of treating them as a search request
        if ($terms == SITE_404_FILE) {
            self::handle_not_found();
        } elseif ($terms == SITE_403_FILE) {
            self::handle_not_authorized();
        } elseif ($terms == SITE_500_FILE) {
            self::handle_internal_error();
        }

        $search = new search();
		$page = new expPaginator(array(
			'model'=>'search',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'records'=>$search->getSearchResults($terms, false, 0, 30),
			//'sql'=>$sql,
            'limit'=>10,
			'order'=>'score',
			'dir'=>'DESC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
        ));

        assign_to_template(array(
            'page'=>$page,
            'terms'=>$terms
        ));
    }

    public static function handle_not_found() {
        header(':', true, 404);
        echo '<h1>', SITE_404_TITLE, '</h1><br />';
        echo SITE_404_HTML;
    }

    public static function handle_not_authorized() {
        header(':', true, 403);
        echo SITE_403_HTML;
    }

    public static function handle_internal_error() {
        header(':', true, 500);
        echo '<h1>', gt('An Internal Server Error was Encountered!'), '</h1>';
    }

}

?>