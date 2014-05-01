<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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

class donationController extends expController {
    public $basemodel_name = 'donation';

    public $useractions = array(
        'showall'=>'Show all Donation Causes',
    );

    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Online Donations"); }
    static function description() { return gt("Allows you to accept donations on your website"); }

    function showall() {
        expHistory::set('viewable', $this->params);
        $causes = $this->donation->find('all');
        //eDebug($causes);
        assign_to_template(array(
            'causes'=>$causes
        ));
    }
    
    function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action we are in.
//        $action   = $_REQUEST['action'];
        $action   = $router->params['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'', 'canonical'=> '', 'noindex' => '', 'nofollow' => '');
        switch($action) {
            case 'showall':
            case 'show':
                $metainfo['title'] = gt('Online Donations') . ' - ' . SITE_TITLE;
                $metainfo['keywords'] = gt('donate online');
                $metainfo['description'] = gt("Make a donation");
            break;
            default:
                $metainfo['title'] = $this->displayname()." - ".SITE_TITLE;
                $metainfo['keywords'] = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
        }
        
        return $metainfo;
    }
    
//    function index() {
//        redirect_to(array('controller'=>'donation', 'action'=>'showall'));
////        $this->showall();
//    }
    
    function show() {
        redirect_to(array('controller'=>'donation', 'action'=>'showall'));
//        $this->showall();
    }
    
    function delete() {
        redirect_to(array('controller'=>'donation', 'action'=>'showall'));
//        $this->showall();
    } 
}

?>