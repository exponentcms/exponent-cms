<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

class linksController extends expController {
    public $useractions = array(
        'showall'=>'Show all'
    );
    //public $add_permissions = array('show'=>'View Links');
    //public $remove_permissions = array('edit');
    public $remove_configs = array(
       'comments',
       'ealerts',
        'facebook',
       'files',
       'pagination',
       'rss',
       'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function requiresConfiguration() { return true; }
    static function displayname() { return gt("Link Manager"); }
    static function description() { return gt("Add and manage a list of URLs"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }
    static function isSearchable() { return true; }

    function showall() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->config['limit']) ? $this->config['limit'] : null;
        $order = isset($this->config['order']) ? $this->config['order'] : "rank";
        $links = $this->$modelname->find('all', $where, $order, $limit);

        if (empty($this->config['usecategories']) ? false : $this->config['usecategories']) {
            expCatController::addCats($links,$order,!empty($this->config['uncat'])?$this->config['uncat']:gt('Not Categorized'));
            $cats[] = new stdClass();
            $cats[0]->name = '';
            expCatController::sortedByCats($links,$cats);
            assign_to_template(array(
                'cats'=>$cats
            ));
        }
        assign_to_template(array(
            'items'=>$links,
//            'rank'=>($order==='rank')?1:0
        ));
    }
    
    public function show() {
        redirect_to(array("controller"=>'links',"action"=>'showall',"src"=>$this->loc->src));
    }
}

?>