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

class portfolioController extends expController {
    public $useractions = array(
        'showall'=>'Show all', 
        'tags'=>"Tags",
        'slideshow'=>"Slideshow"
    );
    protected $manage_permissions = array(
        'import'=>'Import Portfolio Items',
        'export'=>'Export Portfolio Items'
    );
    public $remove_configs = array(
        'comments',
        'ealerts',
        'facebook',
        'rss',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Portfolio"); }
    static function description() { return gt("Display a portfolio or listing."); }
    static function isSearchable() { return true; }

    static function canImportData() {
        return true;
    }

    static function canExportData() {
        return true;
    }

    public function showall() {
        expHistory::set('viewable', $this->params);
        $limit = (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10;
        if (!empty($this->params['view']) && ($this->params['view'] == 'showall_accordion' || $this->params['view'] == 'showall_tabbed')) {
            $limit = '0';
        }
        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        $page = new expPaginator(array(
            'model'=>$this->basemodel_name,
            'where'=>$this->aggregateWhereClause(),
            'limit'=>$limit,
            'order'=>$order,
            'categorize'=>empty($this->config['usecategories']) ? false : $this->config['usecategories'],
            'uncat'=>!empty($this->config['uncat']) ? $this->config['uncat'] : gt('Not Categorized'),
            'groups'=>!isset($this->params['group']) ? array() : array($this->params['group']),
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'columns'=>array(
                gt('Title')=>'title'
            ),
        ));

        assign_to_template(array(
            'page'=>$page,
            'rank'=>($order==='rank')?1:0,
            'params'=>$this->params,
        ));
    }
    
    public function slideshow() {
        expHistory::set('viewable', $this->params);

        $order = isset($this->config['order']) ? $this->config['order'] : 'rank';
        //FIXME we need to change this to expPaginator to get category grouping
        $s = new portfolio();
        $slides = $s->find('all',$this->aggregateWhereClause(),$order);

        assign_to_template(array(
            'slides'=>$slides,
            'rank'=>($order==='rank')?1:0
        ));
    }

    /**
     * Returns rich snippet PageMap meta data
     *
     * @param $request
     * @param $object
     *
     * @return string
     */
    function meta_rich($request, $object) {
        if (!empty($object->expFile[0]) && file_exists(BASE.$object->expFile[0]->directory.$object->expFile[0]->filename)) {
            $rich_meta = '<!--
        <PageMap>
            <DataObject type="thumbnail">
                <Attribute name="src" value="' . URL_FULL . $object->expFile[0]->directory . $object->expFile[0]->filename . '"/>
                <Attribute name="width" value="' . $object->expFile[0]->image_width . '"/>
                <Attribute name="height" value="' . $object->expFile[0]->image_height . '"/>
            </DataObject>
        </PageMap>
    -->';
            return $rich_meta;
        }
    }

    /**
     * The aggregateWhereClause function creates a sql where clause which also includes aggregated module content
     *
     * @param string $type
     *
     * @return string
     */
   	function aggregateWhereClause($type='') {
        $sql = parent::aggregateWhereClause();
        $sql .= (!empty($this->config['only_featured']))?"AND featured=1":"";

        return $sql;
    }

}

?>