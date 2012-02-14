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

class eventregistrationController extends expController {
    public $basemodel_name = 'eventregistration';
    // public $useractions = array(
    //     'showall'=>'Show all events',
    // );
    public $useractions = array();
    
    public $add_permissions = array('showall'=>'View Event Registrations','view_registrants'=>'View Event Registrations','export'=>'Export Event Registrations');

    function displayname() { return "Online Event Registration"; }
    function description() { return "Use this module to manage event registrations on your website"; }

    function showall() {
        expHistory::set('viewable', $this->params);
        $events = $this->eventregistration->find('all', 'product_type="eventregistration"');
	$page = new expPaginator(array(
            'model'=>'eventregistration',
            'where'=>'product_type="eventregistration"',
            'default'=>'Event Title',
            'columns'=>array('Event Title'=>'title','Event Date'=>'eventdate', 'Registrants'=>'quantity')
            ));
        assign_to_template(array('page'=>$page));
    }
   
    function view_registrants() {
	expHistory::set('viewable', $this->params);
        $event = new eventregistration($this->params['id']);
//eDebug($event);
//eDebug("a:" . isset($event->registrants));
//eDebug("b:" . is_array($event->registrants));
	if (isset($event->registrants)) $registrants = expUnserialize($event->registrants);
	else $registrants = null;
//eDebug($registrants);
        assign_to_template(array('event'=>$event,'registrants'=>$registrants));
    } 
//    function view_registrations
    function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action we are in.
        $action = $_REQUEST['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'');
        switch($action) {
            case 'donate':
                $metainfo['title'] = 'Make a eventregistration';
                $metainfo['keywords'] = 'donate online';
                $metainfo['description'] = "Make a eventregistration";    
            break;
            default:
                $metainfo = array('title'=>$this->displayname()." - ".SITE_TITLE, 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
        }
        
        return $metainfo;
    }
    
    function index() {
        redirect_to(array('controller'=>'eventregistrations', 'action'=>'showall'));
    }
    
    function show() {
        redirect_to(array('controller'=>'eventregistrations', 'action'=>'showall'));
    }
    
    function delete() {
        redirect_to(array('controller'=>'eventregistrations', 'action'=>'showall'));
    } 

     public function export() {
                //ob_end_clean();
                $event = new eventregistration($this->params['id']);
                $out = '"Registrant Name","Registrant Email","Registrant Phone"' . "\n";
                foreach (expUnserialize($event->registrants) as $r) {
                        $out .='"'.$r['name'].'","'.$r['email'].'","'.$r['phone'].'"' . "\n";
                }
                // Open file export.csv.
                $fp =  BASE . 'tmp/';
                $fn = str_replace(' ', '_', $event->title) . '.csv';
                $f = fopen ($fp . $fn, 'w');
                // Put all values from $out to export.csv.
                fputs($f, $out);
                fclose($f);
                $mimetype = 'application/octet-stream;';
                header('Content-Type: ' . $mimetype);
                header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Content-Transfer-Encoding: binary');
                header('Content-Encoding:');
                header('Content-Disposition: attachment; filename="' . $fn . '";');
                // IE need specific headers
                if (EXPONENT_USER_BROWSER == 'IE') {
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Vary: User-Agent');
                } else {
                        header('Pragma: no-cache');
                }
                readfile($fp . $fn);
                exit();
    }

}

?>
