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

class orderController extends expController {
	//protected $basemodel_name = '';
	//public $useractions = array('showall'=>'Show all');
	public $useractions = array();
	protected $add_permissions = array('showall'=>'Manage', 'show'=>'View Orders', 'setStatus'=>'Change Status');
	
	function name() { return $this->displayname(); } //for backwards compat with old modules
	function displayname() { return "Ecommerce Order Manager"; }
	function description() { return "Use this module to manage the orders from your ecommerce store."; }
	function author() { return "Adam Kessler - OIC Group, Inc"; }
	function hasSources() { return true; }
	function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }
	
	function showall() {
	    global $db;
	    
	    expHistory::set('viewable', $this->params);
	    
	    // remove abaondoned carts
        /*$count = $db->countObjects('orders', 'purchased=0');
        for($i=0; $i<$count; $i++) {
            // get the cart
            $cart = $db->selectObject('orders','purchased=0');
            
            // check to make sure this isn't an active session
            $ticket = $db->selectObject('sessionticket', "ticket='".$cart->sessionticket_ticket."'");
            if (empty($ticket)) {
                // delete all the order items for this cart and their shippingmethods
                foreach($db->selectObjects('orderitems', 'orders_id='.$cart->id) as $oi) {
                    $db->delete('shippingmethods', 'id='.$oi->shippingmethods_id);
                    $db->delete('orderitems', 'orders_id='.$cart->id);    
                }
                
                // delete the billing methods for this cart.
                $db->delete('billingmethods', 'orders_id='.$cart->id);
                $db->delete('orders', 'id='.$cart->id);
            }           
            
        } */
	    
	    // find orders with a "closed" status type
	    $closed_count = 0;
	    if (empty($this->params['showclosed'])) {
	        $closed_status = $db->selectColumn('order_status', 'id', 'treat_as_closed=1');
	        $status_where = '';
	        
	        foreach ($closed_status as $status) {
	            if (empty($status_where)) {
	                $status_where .= ' AND (order_status_id!='.$status;
	            } else {
	                $status_where .= ' AND order_status_id!='.$status;
	            }
	            $closed_count += $db->countObjects('orders', 'order_status_id='.$status);
	        }
        } else {
            $closed_count = -1;
        }
        
		// build out a SQL query that gets all the data we need and is sortable.
        $sql  = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.middlename as middlename, b.lastname as lastname, os.title as status ';
        $sql .= 'FROM '.DB_TABLE_PREFIX.'_orders o, '.DB_TABLE_PREFIX.'_billingmethods b, ';
        $sql .= DB_TABLE_PREFIX.'_order_status os ';                                          
        $sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.purchased > 0';     
		
		
		if (!empty($status_where)) {
		    $status_where .= ')';
		    $sql .= $status_where;
		}
		
		//eDebug($sql, true);
		$page = new expPaginator(array(
			//'model'=>'order',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'sql'=>$sql,
			'order'=>'purchased',
			'dir'=>'DESC',
			'columns'=>array(
				'Customer'=>'lastname',
				'Invoice #'=>'invoice_id', 
				'Total'=>'total',
				'Date Purchased'=>'purchased',
				'Status'=>'order_status_id',
				)
			));
        //eDebug($page,true);
		assign_to_template(array('page'=>$page, 'closed_count'=>$closed_count));
	}
	
	function show() {
	    global $db;
	    expHistory::set('viewable', $this->params);
	    
		$order = new order($this->params['id']);
        
        // We're forcing the location. Global store setting will always have this loc
        $cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $storeConfig = new expConfig($cfg);
        
        $billing = new billing($this->params['id']);
        $status_messages = $db->selectObjects('order_status_messages');
        $order_type = $order->getOrderType();
        //eDebug($order->billingmethod[0]->billingtransaction);
        $order->billingmethod[0]->billingtransaction = array_reverse($order->billingmethod[0]->billingtransaction);
        //eDebug($order->billingmethod[0]->billingtransaction);
		assign_to_template(array('order'=>$order,'shipping'=>$order->orderitem[0],'billing'=>$billing, 'messages'=>$status_messages, 'order_type'=>$order_type, 'storeConfig'=>$storeConfig->config));
	}
	
	function myOrder() {
	    global $user, $db;
	    $order = new order($this->params['id']);
        $this->loc->src = "@globalstoresettings";

        // We're forcing the location. Global store setting will always have this loc
        $cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $storeConfig = new expConfig($cfg);
        
                
		if ($user->id != $order->user_id) {
		    if ($user->isAdmin()) {
		        redirect_to(array('controller'=>'order', 'action'=>'show', 'id'=>$this->params['id']));
		    } else {
		        flashAndFlow('error', 'The order you are trying to view was not one purchased by you.');
		    }
		    
		} 
		
		expHistory::set('viewable', $this->params);
		
		$billing = new billing($this->params['id']);
        $status_messages = $db->selectObjects('order_status_messages');
        $order_type = $order->getOrderType();
        $order->billingmethod[0]->billingtransaction = array_reverse($order->billingmethod[0]->billingtransaction);
		assign_to_template(array('order'=>$order,'shipping'=>$order->orderitem[0],'billing'=>$billing, 'order_type'=>$order_type, 'storeConfig'=>$storeConfig->config));
	    
	}
	
	function email() {
	    global $template, $user;
	    
	    // setup a template suitable for emailing
	    $template = get_template_for_action($this, 'show_printable', $this->loc);
	    $order = new order($this->params['id']);		
	    $billing = new billing($this->params['id']);
	    assign_to_template(array('order'=>$order,'shipping'=>$order->orderitem[0],'billing'=>$billing));
	
	    // build the html and text versions of the message
	    $html = $template->render();
	    $txt = strip_tags($html);
	    		    
	    // send email invoices to the admins if needed
	    if (ecomconfig::getConfig('email_invoice') == true) {
            $addresses = explode(',', ecomconfig::getConfig('email_invoice_addresses'));
            foreach ($addresses as $address) {
                $mail = new expMail();
		        $mail->quickSend(array(
		                'html_message'=>$html,
					    'text_message'=>$txt,
					    'to'=>trim($address),
					    'from'=>ecomconfig::getConfig('from_address'),
					    'from_name'=>ecomconfig::getConfig('from_name'),
					    'subject'=>'An order was placed on the '.ecomconfig::getConfig('storename'),
		        ));
		    }
        }
        
        // email the invoice to the user if we need to
        if (ecomconfig::getConfig('email_invoice_to_user') == true && !empty($user->email)) {
            $usermsg  = "<p>".ecomconfig::getConfig('invoice_msg')."<p>";
            $usermsg .= $html;
            $usermsg .= ecomconfig::getConfig('footer');
            
            $mail = new expMail();
            $mail->quickSend(array(
                    'html_message'=>$usermsg,
			        'text_message'=>$txt,
			        'to'=>$user->email,
			        'from'=>ecomconfig::getConfig('from_address'),
			        'from_name'=>ecomconfig::getConfig('from_name'),
			        'subject'=>ecomconfig::getConfig('invoice_subject'),
            ));      
        }
	}
	
	function update_shipping() {
	    $order = new order($this->params['id']);
	    $this->params['shipped'] = datetimecontrol::parseData('shipped',$this->params);
	    $order->update($this->params);
	    flash('message', 'Shipping information updated.');
	    expHistory::back();
	}
	
    function set_order_type() 
    {
        global $db;
        if (empty($this->params['id'])) expHistory::back();
        
        // get the order and update the type
        $order = new order($this->params['id']);
        $order->order_type_id = $this->params['order_type_id'];
        $order->save();
        
        
        
        flash('message', 'Invoice #'.$order->invoice_id.' has been set to '.$order->getOrderType());
        expHistory::back();
    }
    
	function setStatus() {
	    global $db, $template;
	    if (empty($this->params['id'])) expHistory::back();
	    
	    // get the order and create a new order_Status_change
	    $order = new order($this->params['id']);
	    $change = new order_status_changes();
	    
	    // save the changes
	    $change->from_status_id = $order->order_status_id;
	    $change->comment = $this->params['comment'];
	    $change->to_status_id = $this->params['order_status_id'];
	    $change->orders_id = $order->id;
	    $change->save();
	    
	    // update the status of the order
	    $order->order_status_id = $this->params['order_status_id'];
	    $order->save();
	    
	    // Save the message for future use if that is what the user wanted.
	    if (!empty($this->params['save_message'])) {
	        $message->body = $this->params['comment'];
	        $db->insertObject($message, 'order_status_messages');
	    }
	    
	    // email the user if we need to
	    if (!empty($this->params['email_user'])) {
	        $email_addy = $order->billingmethod[0]->email;
	        if (!empty($email_addy)) {
	            $from_status = $db->selectValue('order_status', 'title', 'id='.$change->from_status_id);
	            $to_status = $db->selectValue('order_status', 'title', 'id='.$change->to_status_id);
	            assign_to_template(
	                array(
	                    'comment'=>$change->comment, 
	                    'to_status'=>$to_status, 
	                    'from_status'=>$from_status, 
	                    'order'=>$order, 
	                    'date'=>date("F j, Y, g:i a"),
	                    'storename'=>ecomconfig::getConfig('storename'),
	                    'include_shipping'=>isset($this->params['include_shipping_info']) ? true : false
	                    )
	             );
	            
	            $html = $template->render();
	            $html .= ecomconfig::getConfig('footer');
	            
	            $mail = new expMail();
                $mail->quickSend(array(
                        'html_message'=>$html,
			            'text_message'=>str_replace("<br>", "\r\n", $template->render()),
			            'to'=>$email_addy,
			            'from'=>ecomconfig::getConfig('from_address'),
			            'subject'=>'The status of your order (#'.$order->invoice_id.') has been updated on '.ecomconfig::getConfig('storename').'.'
                ));
            } else {
                flash('error', 'The email address was NOT send. An email address count not be found for this customer');
            }
        }
	    
	    flash('message', 'The status of invoice #'.$order->invoice_id.' has been set to '.$order->getStatus());
	    expHistory::back();
	}
	
	function ordersbyuser() {
		global $user;
		// if the user isn't logged in flash an error msg
		if (!$user->isLoggedIn()) expQueue::flashAndFlow('error', 'You must be logged in to view past orders.');
		
		expHistory::set('viewable', $this->params);
		$page = new expPaginator(array(
			'model'=>'order',
			'where'=>'purchased > 0 AND user_id='.$user->id,
			'order'=>'purchased',
			'dir'=>'DESC',
			'columns'=>array(
				'Date Purchased'=>'purchased',
				'Invoice #'=>'invoice_id', 
				)
			));
		assign_to_template(array('page'=>$page));
		
	}
	
	function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;
        
        // figure out what metadata to pass back based on the action 
        // we are in.
        $action = $_REQUEST['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'');
        switch($action) {
            case 'showall':
                $metainfo = array('title'=>"Managing Invoices", 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
            break;
            case 'show':
            case 'showByTitle':                
                $metainfo['title'] = 'Viewing Invoice';
                $metainfo['keywords'] = empty($object->meta_keywords) ? SITE_KEYWORDS : $object->meta_keywords;
                $metainfo['description'] = empty($object->meta_description) ? SITE_DESCRIPTION : $object->meta_description;            
            break;
            default:
                $metainfo = array('title'=>"Order Management - ".SITE_TITLE, 'keywords'=>SITE_KEYWORDS, 'description'=>SITE_DESCRIPTION);
        }
        
        return $metainfo;
    }
    
    function captureAuthorization ()
    {
        //eDebug($this->params,true);
        $order = new order($this->params['id']);
        /*eDebug($this->params); 
        //eDebug($order,true);*/ 
        //eDebug($order,true);
        //$billing = new billing();
      
        //eDebug($billing, true);
        //$billing->calculator = new $calcname($order->billingmethod[0]->billingcalculator_id);
        $calc = $order->billingmethod[0]->billingcalculator->calculator;
        $calc->config = $order->billingmethod[0]->billingcalculator->config;
        
        //$calc = new $calc-
        //eDebug($calc,true);
        if (!method_exists($calc, 'delayed_capture'))
        {
            flash('error', 'The Billing Calculator does not support delayed capture');
            expHistory::back();
        }
        
        $result = $calc->delayed_capture($order->billingmethod[0], $this->params['capture_amt']);
        
        if (empty($result->errorCode)) {
            flash('message', 'The authorized payment was successfully captured');
            expHistory::back();
            
        } else {
            flash('error', 'An error was encountered while capturing the authorized payment.<br /><br />'.$result->message);
            expHistory::back();
        }
    }
    
    function voidAuthorization ()
    {
        $order = new order($this->params['id']);
        $billing = $order->billingmethod[0];
        
        $calc = $order->billingmethod[0]->billingcalculator->calculator;
        $calc->config = $order->billingmethod[0]->billingcalculator->config;
        
        if (!method_exists($calc, 'void_transaction'))
        {
            flash('error', 'The Billing Calculator does not support void');
            expHistory::back();
        }
        
        $result = $calc->void_transaction($order->billingmethod[0]);
        
        if (empty($result->errorCode) == '0') {
            flash('message', 'The transaction has been voided');
            expHistory::back();
            
        } else {
            flash('error', 'An error was encountered while voiding the authorized payment.<br /><br />'.$result->message);
            expHistory::back();
        }
    }
    
    function creditTransaction ()
    {
        $order = new order($this->params['id']);
        $billing = new billing($this->params['id']);
        //eDebug($this->params,true);
        $result = $billing->calculator->credit_transaction($billing->billingmethod, $this->params['capture_amt']);
        
        if ($result->errorCode == '0') {
            flash('message', 'The transaction has been credited');
            expHistory::back();
            
        } else {
            flash('error', 'An error was encountered while capturing the authorized payment.<br /><br />'.$result->message);
            expHistory::back();
        }
    }
}

?>
