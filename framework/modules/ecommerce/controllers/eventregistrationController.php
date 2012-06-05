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
    public $useractions = array(
        'showall'=>'Show all events',
		'showByTitle' => "Show events by title",
    );

    function displayname() { return gt("Online Event Registration"); }
    function description() { return gt("Use this module to manage event registrations on your website"); }

    function showall() {
        expHistory::set('viewable', $this->params);
        $events = $this->eventregistration->find('all', 'product_type="eventregistration"');
		// eDebug($events, true);
        assign_to_template(array('page'=>$events));
    }
   
    function view_registrants() {
		expHistory::set('viewable', $this->params);
			$event = new eventregistration($this->params['id']);

		if (isset($event->registrants)) $registrants = expUnserialize($event->registrants);
		else $registrants = null;

        assign_to_template(array('event'=>$event,'registrants'=>$registrants));
    } 

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
    
    function showByTitle() {
		global $order, $template, $user;
        expHistory::set('viewable', $this->params);
		
        $product = new eventregistration(addslashes($this->params['title']));
  
        if(empty($product->id)) {
            redirect_to(array('controller'=>'notfound','action'=>'page_not_found','title'=>$this->params['title']));            
        }

		assign_to_template(array('product'=>$product));
    }
	
	function eventregistration_form() {
		// eDebug($this->params, true);
		expHistory::set('viewable', $this->params);
		$record = expSession::get("record");
		if(!empty($record['eventregistration']['price'])) {
			$product_id = $record['eventregistration']['product_id'];
			$base_price = $record['eventregistration']['price'];
		} else {
			$product_id = $this->params['eventregistration']['product_id'];
			$base_price = $this->params['eventregistration']['price'];
		}
		
		expHistory::set('viewable', $this->params);
		
		assign_to_template(array('record'=>$record, 'product_id'=>$product_id, 'base_price'=>$base_price));
	}
	
	function eventregistration_process() {
		global $db, $user, $order;
		
		expSession::set('record', $this->params);

		$address = new address();
		//Address
		if ($user->isLoggedIn()) {
			$this->params['address']['is_default'] = 1;
			$this->params['address']['user_id'] = $user->id;
			$this->params['address']['is_billing'] = 1;
			$this->params['address']['is_shipping'] = 1;
			$addy = $this->params['address'];
			$address->update($addy);
		} else {
		
			$arr = array();
			$arr['firstname'] = $this->params['address']['firstname'];
			$arr['lastname']  = $this->params['address']['lastname'];
			$arr['email']     = $this->params['address']['email'];
			$arr['username']  = $this->params['address']['email'] . time();
			$arr['pass1']     = $this->params['address']['email'];
			$arr['pass2']     = $this->params['address']['email'];
	
			$user = new user($arr);
			$ret = $user->setPassword($arr['pass1'], $arr['pass2']);
			$user->save();
			
			user::login($arr['username'], $arr['pass1']);

			$this->params['address']['is_default'] = 1;
			$this->params['address']['user_id'] = $user->id;
			$this->params['address']['is_billing'] = 1;
			$this->params['address']['is_shipping'] = 1;
			$addy2 = $this->params['address'];			
			$address->update($addy2);
		}
		
		//Billing
		$billing = new billing();
		$opts = $billing->calculator->userFormUpdate($this->params['billing']);
		$opts->recurring = 0;
		
		expSession::set('billing_options', $opts);
		
		//Add to Cart
		$product_id = $this->params['eventregistration']['product_id'];
		$product_type = "eventregistration";
		$product = new $product_type($product_id, true, true);
	
		$product->addToCart($this->params['eventregistration']);
		
		$order->setOrderType($this->params);
        $order->setOrderStatus($this->params);
          
        $order->calculateGrandTotal(); 
   
        $result = $billing->calculator->preprocess($billing->billingmethod, $opts, $this->params, $order);
		
		redirect_to(array('controller'=>'cart', 'action'=>'confirm'));
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