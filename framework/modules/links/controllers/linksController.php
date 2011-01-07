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

class linksController extends expController {
    //public $basemodel_name = '';
    public $useractions = array('showall'=>'Show all');
    //public $add_permissions = array('show'=>'View Links');
    //public $remove_permissions = array('edit');

    function requiresConfiguration() { return true; }

	public $remove_configs = array(
        'aggregretion',
        'comments',
        'files',
        'rss',
        'tags'
    );

    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Link Manager"; }
    function description() { return "Add and manage a list of URLs"; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return true; }

    function showall() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->params['limit']) ? $this->params['limit'] : null;
        $order = isset($this->params['order']) ? $this->params['order'] : "rank ASC";
        $links = $this->$modelname->find('all', $where, $order, $limit);
        assign_to_template(array('items'=>$links, 'modelname'=>$modelname));
    }
    
    public function show() {
        redirect_to(array("controller"=>'links',"action"=>'showall',"src"=>$this->loc->src));
    }
}

?>