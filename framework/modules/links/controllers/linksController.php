<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
    public $useractions = array(
        'showall'=>'Show all'
    );
    //public $add_permissions = array('show'=>'View Links');
    //public $remove_permissions = array('edit');
    public $remove_configs = array(
           'comments',
           'files',
           'pagination',
           'rss',
           'tags'
       ); // all options: ('aggregation', 'categories','comments','ealerts','files','module_title','pagination', 'rss','tags')

    function requiresConfiguration() { return true; }
    function displayname() { return "Link Manager"; }
    function description() { return "Add and manage a list of URLs"; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function isSearchable() { return true; }

    function showall() {
        expHistory::set('viewable', $this->params);
        $modelname = $this->basemodel_name;
        $where = $this->hasSources() ? $this->aggregateWhereClause() : null;
        $limit = isset($this->config['limit']) ? $this->config['limit'] : null;
        $order = isset($this->config['order']) ? $this->config['order'] : "rank";
        $links = $this->$modelname->find('all', $where, $order, $limit);

        if (empty($this->config['usecategories']) ? false : $this->config['usecategories']) {
//            foreach ($links as $key=>$record) {
//                foreach ($record->expCat as $cat) {
//                    $links[$key]->catid = $cat->id;
//                    $links[$key]->catrank = $cat->rank;
//                    $links[$key]->cat = $cat->title;
//                    $links[$key]->color = empty($cat->color) ? null : $cat->color;
//                    $links[$key]->module = empty($cat->module) ? null : $cat->module;
//                    break;
//                }
//                if (empty($links[$key]->catid)) {
//                    $links[$key]->catid = null;
//                    $links[$key]->catrank = 999999;
//                    $links[$key]->cat = 'Not Categorized';
//                }
//            }
//            expSorter::osort($links, array(array('catrank'),array($order)));
            expCatController::addCats($links,$order);

            $cats = array();
            $cats[0]->name = '';
//            foreach ($links as $record) {
//                if (empty($record->catid)) $record->catid = 0;
//                if (empty($cats[$record->catid])) {
//                    $cats[$record->catid]->count = 1;
//                    $cats[$record->catid]->name = $record->cat;
//                } else {
//                    $cats[$record->catid]->count += 1;
//                }
//                $cats[$record->catid]->records[] = $record;
//            }
            expCatController::createCats($links,$cats);
            assign_to_template(array('cats'=>$cats));
        }

        assign_to_template(array('items'=>$links, 'rank'=>($order==='rank')?1:0));
    }
    
    public function show() {
        redirect_to(array("controller"=>'links',"action"=>'showall',"src"=>$this->loc->src));
    }
}

?>