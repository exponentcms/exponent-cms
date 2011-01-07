<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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

class notfoundController extends expController {
    //public $basemodel_name = '';
    //public $useractions = array('showall'=>'Show all');
    public $add_permissions = array('showall'=>'Showall', 'show'=>'Show');

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Not Found Controller"; }
    function description() { return "This controller handles routing not found pages to the appropriate place."; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return false; }
    function hasViews() { return false; }
    function hasContent() { return false; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }
    
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
        $search = new search();
        $results = $search->getSearchResults(implode(' ', $params));
        assign_to_template(array('results'=>$results));
    }

}

?>
