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

class orderController extends expController {
	//protected $basemodel_name = '';
	//public $useractions = array('showall'=>'Show all');
	public $useractions = array();
	protected $add_permissions = array('showall'=>'Manage', 'show'=>'View Orders', 'setStatus'=>'Change Status', 
    'edit_payment_info'=>'Edit Payment Info','save_payment_info'=>'Save Payment Info',
    'edit_address'=>'Edit Address','save_address'=>'Save Address',
    'edit_order_item'=>'Edit Order Item','save_order_item'=>'Save Order Item',
    'add_order_item'=>'Add Order Item','save_new_order_item'=>'Save New Order Item',
    'edit_totals'=>'Edit Totals','save_totals'=>'Save Totals',
    'edit_invoice_id'=>'Edit Invoice Id','save_invoice_id'=>'Save Invoice Id', 
    'update_sales_reps'=>'Manage Sales Reps', 'quickfinder'=>'Do a quick order lookup', 
    'edit_shipping_method'=>'Edit Shipping Method', 'save_shipping_method'=>'Save Shipping Method',    
    'create_new_order'=>'Create A New Order', 'save_new_order'=>'Save a new order', 
    'createReferenceOrder'=>'Create Reference Order', 'save_reference_order'=>'Save Reference Order'      
    );
	
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
        $sql  = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.middlename as middlename, b.lastname as lastname, os.title as status, ot.title as order_type ';
        $sql .= 'FROM '.DB_TABLE_PREFIX.'_orders o, '.DB_TABLE_PREFIX.'_billingmethods b, ';
        $sql .= DB_TABLE_PREFIX.'_order_status os, ';                                          
        $sql .= DB_TABLE_PREFIX.'_order_type ot ';                                          
        $sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.order_type_id = ot.id AND o.purchased > 0';     
		
		
		if (!empty($status_where)) {
		    $status_where .= ')';
		    $sql .= $status_where;
		}
		$limit = empty($this->config['limit']) ? 50 : $this->config['limit'];
		//eDebug($sql, true);
		$page = new expPaginator(array(
			//'model'=>'order',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'sql'=>$sql,            
			'order'=>'purchased',
			'dir'=>'DESC',
            'limit'=>$limit,
			'columns'=>array(
				'Customer'=>'lastname',
				'Order #'=>'invoice_id', 
				'Total'=>'total',
				'Date Purchased'=>'purchased',
                'Type'=>'order_type_id',
				'Status'=>'order_status_id',
                'Ref'=>'orig_referrer',   
				)
			));
        //eDebug($page,true);
		assign_to_template(array('page'=>$page, 'closed_count'=>$closed_count));
	}
	
	function show() {
	    global $db,$user;
//eDebug($_REQUEST);
//eDebug($this->params,true);
//if (!empty($this->params['printerfriendly'])) $_REQUEST['printerfriendly'] = 1;
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
        if (isset($this->params['printerfriendly'])) $pf = $this->params['printerfriendly'];
        else $pf = 0;
        
        $to_addresses[] = $order->billingmethod[0]->email;
        $s = array_pop($order->shippingmethods);
        if($s->email != $order->billingmethod[0]->email) $to_addresses[] = $s->email;
               
        $from_addresses = array();
        $from_addresses[SMTP_FROMADDRESS] = SMTP_FROMADDRESS; 
        $from_addresses[ecomconfig::getConfig('from_address')] = ecomconfig::getConfig('from_address');
        $from_addresses[$user->email] = $user->email;                 
        $from_addresses['other'] = 'Other (enter below)';
        
        $email_subject = 'Message from ' . ecomconfig::getConfig('storename') . ' about your order (#'.$order->invoice_id . ')';      
        
        $order->setReferencingIds();
        
        $css = file_get_contents(BASE.'framework/modules/ecommerce/assets/css/print-invoice.css');
	    
        
        assign_to_template(array('css'=>$css,'printerfriendly'=>$pf,'order'=>$order,'shipping'=>$order->orderitem[0],'billing'=>$billing, 'messages'=>$status_messages, 'order_type'=>$order_type, 'storeConfig'=>$storeConfig->config,'sales_reps'=>$order->getSalesReps(), 'from_addresses'=>$from_addresses, 'from_default'=>ecomconfig::getConfig('from_address'),'email_subject'=>$email_subject,'to_addresses'=>implode(',',$to_addresses)));
	}
	
	function myOrder() {
	    global $user, $db;
	    $order = new order($this->params['id']);
        if($order->purchased == 0) flashAndFlow('error', 'You do not have permission to view this order.');
        
        $this->loc->src = "@globalstoresettings";

        // We're forcing the location. Global store setting will always have this loc
        $cfg->mod = "ecomconfig";
        $cfg->src = "@globalstoresettings";
        $cfg->int = "";
        $storeConfig = new expConfig($cfg);
        
        //check here for the hash in the params, or session set w/ perms to view...shs = xaf7y0s87d7elshd70 etc
        //if present, promt user for the order number and email address on the order
        //and if they pass, show the order to them. Need to maybe set something in the session then for subsequent
        //viewing of the order?        
		if ($user->id != $order->user_id) {
		    if ($user->isAdmin()) {
		        redirect_to(array('controller'=>'order', 'action'=>'show', 'id'=>$this->params['id']));
		    } else {
		        flashAndFlow('error', 'You do not have permission to view this order.');
		    }                                                                       
		} 
		
		expHistory::set('viewable', $this->params);
		
		$billing = new billing($this->params['id']);
        $status_messages = $db->selectObjects('order_status_messages');
        $order_type = $order->getOrderType();
        $order->total_items = 0;
        foreach ($order->orderitem as $item)
        {
            $order->total_items += $item->quantity;
            $order->shipping_city =  $item->shippingmethod->city;  
            $order->shipping_state = $item->shippingmethod->state;
        }                                 
        $state = new geoRegion($order->shipping_state);
        $country = new geoCountry($state->country_id);  
        $order->shipping_country = $country->iso_code_3letter;        
        $order->shipping_state = $state->name;
        
        //eDebug($order,true);
        
        $order->billingmethod[0]->billingtransaction = array_reverse($order->billingmethod[0]->billingtransaction);
		if (isset($this->params['printerfriendly'])) $pf = $this->params['printerfriendly'];
        else $pf = 0;
        
        $trackMe = false;
        if (isset($this->params['tc']) && $this->params['tc'] == 1)
        {
            if(exponent_sessions_isset('orders_tracked'))
            {
                $trackingArray = exponent_sessions_get('orders_tracked');    
                if(in_array($order->invoice_id,$trackingArray)) 
                {
                    $trackMe = false;
                }
                else {
                    $trackMe = true;
                    $trackingArray[] = $order->invoice_id;
                    exponent_sessions_set('orders_tracked',$trackingArray);    
                }
            }
            else
            {
                $trackMe = true;
                $trackingArray[] = $order->invoice_id;
                exponent_sessions_set('orders_tracked',$trackingArray);        
            }
        }
        if(DEVELOPMENT != 0)$trackMe = false;
        assign_to_template(array('printerfriendly'=>$pf,'order'=>$order,'shipping'=>$order->orderitem[0],'billing'=>$billing, 'order_type'=>$order_type, 'storeConfig'=>$storeConfig->config, 'tc'=>$trackMe));
	    
	}
	
	function email() {
	     global $template, $user;
	    
	    // setup a template suitable for emailing
	    $template = get_template_for_action($this, 'email_invoice', $this->loc);
	    $order = new order($this->params['id']);		
	    $billing = new billing($this->params['id']);
	    //$css = file_get_contents(BASE.'framework/modules/ecommerce/assets/css/print-invoice.css');
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
					//'to'=>$order->billingmethod[0]->email,
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
	
   
    function getPDF($orders = null)
    {
        global $user;
	    //$invoice = '<HTML><HEAD><link rel="stylesheet" type="text/css" href="'.NONSSL_URL.PATH_RELATIVE.'external/yui2/build/reset-fonts-grids/reset-fonts-grids.css" ><link rel="stylesheet" type="text/css" href="'.NONSSL_URL.THEME_RELATIVE.'css/base-styles.css"><link rel="stylesheet" type="text/css" href="'.NONSSL_URL.PATH_RELATIVE.'framework/modules/ecommerce/assets/css/print-invoice.css"><style>html{background:none;} #store-header{text-align:left;}</style></HEAD><BODY>';
        $invoice = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><HTML><HEAD></HEAD><BODY>';
        if(is_array($orders))
        {
            foreach ($orders as $order)
            {
                if ($user->isAdmin()) 
                {
                    $invoice .= renderAction(array('controller'=>'order', 'action'=>'show', 'view'=>'show_printable', 'id'=>$order['id'], 'printerfriendly'=>'1','no_output'=>'true'));                        
                }
                else
                {
                    $invoice .= renderAction(array('controller'=>'order', 'action'=>'myOrder', 'view'=>'show_printable', 'id'=>$order['id'], 'printerfriendly'=>'1','no_output'=>'true'));                        
                }
                $invoice .= '<p style="page-break-before: always;"></p>';       
            }
            $invoice = substr($invoice,0,(strlen($invoice)-42));           
        }
        else
        {
            if ($user->isAdmin()) 
            {
                $invoice .= renderAction(array('controller'=>'order', 'action'=>'show', 'view'=>'show_printable', 'id'=>$this->params['id'], 'printerfriendly'=>'1','no_output'=>'true'));    
            }
            else
            {
                $invoice .= renderAction(array('controller'=>'order', 'action'=>'myOrder', 'view'=>'show_printable', 'id'=>$this->params['id'], 'printerfriendly'=>'1','no_output'=>'true'));        
            }            
        }
        
	    $invoice .= "</BODY></HTML>";
        // eDebug($invoice);
        $org_name = str_ireplace(" ","_",ORGANIZATION_NAME); 
		if (stristr(PHP_OS, 'Win')) {
			if(file_exists(HTMLTOPDF_PATH)) {
				do{
					$htmltopdftmp = HTMLTOPDF_PATH_TMP .mt_rand().'.html';
				} while(file_exists($htmltopdftmp));
			}
			file_put_contents($htmltopdftmp, $invoice);
			// eDebug(HTMLTOPDF_PATH . " " . $htmltopdftmp . " " . HTMLTOPDF_PATH_TMP . "MUS_Invoice.pdf", true);
            
			exec(HTMLTOPDF_PATH . " " . $htmltopdftmp . " " . HTMLTOPDF_PATH_TMP . $org_name . "_Invoice.pdf");
			$this->returnFile(HTMLTOPDF_PATH_TMP . $org_name . "_Invoice.pdf", $org_name . "_Invoice.pdf", "pdf"); 
			exit();
		} else {
			$pdfer = new expHtmlToPDF();
			$pdfer->set_html($invoice);
			$pdfer->set_orientation('Portrait');
			$pdfer->set_page_size('Letter');
			$pdfer->set_grayscale(true);
			//$pdfer->set_title("MUS_Invoice#" . $this->params['id'] . ".pdf");  
			$pdfer->render();
			ob_clean();
			//$pdfer->output('D',"MUS_Invoice" . $this->params['id'] . ".pdf");    
			$pdfer->output('D', $org_name . "_Invoice" . ".pdf");	
			exit();
		}
    }
    
	private function returnFile($file, $name, $mime_type='') {
        /*
            This function takes a path to a file to output ($file),
            the filename that the browser will see ($name) and
            the MIME type of the file ($mime_type, optional).

            If you want to do something on download abort/finish,
            register_shutdown_function('function_name');
        */
        if (!is_readable($file)) die('File not found or inaccessible!');

        $size = filesize($file);
        $name = rawurldecode($name);

        /* Figure out the MIME type (if not specified) */
        $known_mime_types = array(
                "pdf" => "application/pdf",
                "txt" => "text/plain",
                "html" => "text/html",
                "htm" => "text/html",
                "exe" => "application/octet-stream",
                "zip" => "application/zip",
                "doc" => "application/msword",
                "xls" => "application/vnd.ms-excel",
                "ppt" => "application/vnd.ms-powerpoint",
                "gif" => "image/gif",
                "png" => "image/png",
                "jpeg"=> "image/jpg",
                "jpg" =>  "image/jpg",
                "php" => "text/plain"
        );

        if ($mime_type == '')
        {
            $file_extension = strtolower(substr(strrchr($file, "."), 1));

            if (array_key_exists($file_extension, $known_mime_types))
            {
                $mime_type = $known_mime_types[$file_extension];
            }
            else
            {
                $mime_type = "application/force-download";
            }
        }

        //@ob_end_clean(); //turn off output buffering to decrease cpu usage
        // required for IE, otherwise Content-Disposition may be ignored
        if (ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        /* The three lines below basically make the download non-cacheable */
        header('Cache-control: private');
        header('Pragma: private');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        // multipart-download and download resuming support
        if (isset($_SERVER['HTTP_RANGE']))
        {
            list($a, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(',', $range, 2);
            list($range, $range_end) = explode('-', $range);

            $range = intval($range);

            $range_end = (!$range_end) ? $size-1 : intval($range_end);
            $new_length = $range_end-$range + 1;

            header('HTTP/1.1 206 Partial Content');
            header('Content-Length: ' . $new_length);
            header('Content-Range: bytes ' . ($range - $range_end / $size));
        }
        else
        {
            $new_length = $size;

            header('Content-Length: ' . $size);
        }

        /* output the file itself */
        $chunksize = 1 * (1024 * 1024); //you may want to change this
        $bytes_send = 0;

        if ($file = fopen($file, 'r'))
        {
            if (isset($_SERVER['HTTP_RANGE'])) fseek($file, $range);

            while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length))
            {
                $buffer = fread($file, $chunksize);

                print($buffer);
                flush();

                $bytes_send += strlen($buffer);
            }

            fclose($file);
        }
        else
        {
            die('Error - can not open file.');
        }
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
        
        //set order type
        if(isset($this->params['order_type_id'])) $order->order_type_id = $this->params['order_type_id'];
        
        //only save the status change if it actually changed to something different
        if($order->order_status_id != $this->params['order_status_id'])
        {
	        $change = new order_status_changes();	        
	        // save the changes
	        $change->from_status_id = $order->order_status_id;
	        $change->comment = $this->params['comment'];
	        $change->to_status_id = $this->params['order_status_id'];
	        $change->orders_id = $order->id;
	        $change->save();
	        
	        // update the status of the order
	        $order->order_status_id = $this->params['order_status_id'];
            
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
        }
	    
        $order->save();	           
	    
	    flash('message', 'Order Type and/or Status Updated.');
	    expHistory::back();
	}
	
    function emailCustomer() {
        //eDebug($this->params,true);
        global $db, $template, $user;
        if (empty($this->params['id'])) expHistory::back();
        
        // get the order
        $order = new order($this->params['id']);
        
        // Save the message for future use if that is what the user wanted.
        if (!empty($this->params['save_message'])) {
            $message->body = $this->params['email_message'];
            $db->insertObject($message, 'order_status_messages');
        }      
        
        $email_addys[] = explode(',',$this->params['to_addresses']); //$order->billingmethod[0]->email;
        //eDebug($email_addy,true);
        if (!empty($email_addys[0])) 
        {   
            assign_to_template(
                    array(
                        'message'=>$this->params['email_message']
                        ));
            $html = $template->render();
            if($this->params['include_invoice'])
            {
                $html .= '<br><hr><br>';
                $html .= renderAction(array('controller'=>'order', 'action'=>'show', 'view'=>'email_invoice', 'id'=>$this->params['id'], 'printerfriendly'=>'1','no_output'=>'true'));                        
            }else
            {
                $html .= ecomconfig::getConfig('footer');
            }
            
            //eDebug($html,true);
            if(isset($this->params['from_address']))
            {
                if($this->params['from_address'] == 'other')
                {
                    $from = $this->params['other_from_address'];   
                }
                else
                {
                    $from = $this->params['from_address'];    
                }                                                         
            }
            else
            {
                $from = ecomconfig::getConfig('from_address');
            }
            
            if(isset($this->params['email_subject']))
            {
                $email_subject = $this->params['email_subject'];
            }else
            {
                $email_subject = 'Message from ' . ecomconfig::getConfig('storename') . ' about your order (#'.$order->invoice_id . ')';                            
            }
            
            $mail = new expMail();
            foreach($email_addys as $email_addy)
            {
                $mail->quickSend(array(
                        'html_message'=>$html,
                        'text_message'=>str_replace("<br>", "\r\n", $template->render()),
                        'to'=>$email_addy,
                        'from'=>$from,
                        'subject'=>$email_subject                    
                ));
            }
            $emailed_to = implode(',',$email_addys);
            $note = new expSimpleNote();
            $note->body = "<strong>[action]: Emailed message to " .$emailed_to.":</strong><br><br>" . $this->params['email_message'];
            $note->approved = 1;
            $note->name = $user->firstname . " " . $user->lastname;
            $note->email = $user->email;
            
            $note->save();
            $note->refresh();  
            $noteObj->expsimplenote_id = $note->id;
            $noteObj->content_id = $order->id;
            $noteObj->content_type = 'order';
                        
            $db->insertObject($noteObj, 'content_expSimpleNote');
                                    
            //eDebug($note,true);            
        } else {
            flash('error', 'The email address was NOT sent. An email address count not be found for this customer');
            expHistory::back();
        }       
        
        flash('message', 'Email sent.');
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
        
        if (empty($result->errorCode)) {
            flash('message', 'The transaction has been successfully voided');
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
    
    function edit_payment_info ()
    {
        //$order = new order($this->params['id']);
        $billing = new billing($this->params['id']);
        $opts = expUnserialize($billing->billingmethod->billing_options) ;
        //eDebug($billing);
        eDebug($opts);
        assign_to_template(array('orderid'=>$this->params['id'],'opts'=>$opts->result));         
    }
    
    function edit_shipping_method ()
    {
        //$order = new order($this->params['id']);
        if(!isset($this->params['id'])) flashAndFlow('error','Unable to process request.  Order invalid.');
        $order = new order($this->params['id']);
        $s = array_pop($order->shippingmethods);
        $sm = new shippingmethod($s->id);
        //eDebug($sm);
        assign_to_template(array('orderid'=>$this->params['id'],'shipping'=>$sm));         
    }
    
    function save_shipping_method()
    {
        if(!isset($this->params['id'])) flashAndFlow('error','Unable to process request.  Order invalid.');
        if(!isset($this->params['sid'])) flashAndFlow('error','Unable to process request.  Order invalid.');        
        $sm = new shippingmethod($this->params['sid']);            
        $sm->option_title = $this->params['shipping_method_title'];
        $sm->carrier = $this->params['shipping_method_carrier'];
        $sm->save();
        flashAndFlow('message','Shipping method updated.');
    }
    
    function save_reference_order()
    {
        global $user;
        //eDebug($this->params,true);
        $order = new order($this->params['original_orderid']);
        //eDebug($order,true); 
        //x
        $newOrder = new order();
        $newOrder->order_status_id = $this->params['order_status_id'];
        $newOrder->order_type_id = $this->params['order_type_id'];
        //$newOrder->order_references = $order->id;
        $newOrder->reference_id = $order->id;
        $newOrder->user_id = $order->user_id;                
        $newOrder->purchased = time();                
        $newOrder->updated = time();
        $newOrder->invoice_id = $newOrder->getInvoiceNumber();               
        $newOrder->orderitem = array();
        $newOrder->subtotal = $this->params['subtotal'];
        $newOrder->total_discounts = $this->params['total_discounts'];
        $newOrder->tax = $this->params['tax'];
        $newOrder->shipping_total = $this->params['shipping_total'];
        $newOrder->surcharge_total = $this->params['surcharge_total'];
        
        if($this->params['autocalc'] == true)
        {
            $newOrder->grand_total = ($newOrder->subtotal - $newOrder->total_discounts) + $newOrder->tax + $newOrder->shipping_total + $newOrder->surcharge_total;   
        }else
        {
            $newOrder->grand_total = round($this->params['grand_total'],2);   
        }        
        $newOrder->save();
        $newOrder->refresh();
        
        $tObj->result->errorCode = 0;
        $tObj->result->message = "Reference Order Pending";
        $tObj->result->PNREF = "Pending";
        $tObj->result->authorization_code = "Pending";
        $tObj->result->AVSADDR = "Pending";
        $tObj->result->AVSZIP = "Pending";
        $tObj->result->CVV2MATCH = "Pending";
        $tObj->result->traction_type = "Pending"; 
        
        $newBillingMethod = $order->billingmethod[0];
        $newBillingMethod->id = null;
        $newBillingMethod->orders_id = $newOrder->id;
        $newBillingMethod->billing_cost = 0;
        $newBillingMethod->billingcalculator_id = 6;
        $newBillingMethod->transaction_state = 'authorization pending';
        $newBillingMethod->billing_options = serialize($tObj);
        $newBillingMethod->save();
        
        //eDebug(expUnserialize($order->billingmethod[0]->billing_options));        
        //eDebug(expUnserialize($order->billingmethod[0]->billingtransaction[0]->billing_options),true); 
               
        $newBillingTransaction = new billingtransaction();
        $newBillingTransaction->billingcalculator_id = 6; ///setting to manual/passthru
        $newBillingTransaction->billing_cost = 0;
        $newBillingTransaction->billingmethods_id = $newBillingMethod->id;
        $newBillingTransaction->transaction_state = 'authorization pending';
               
        $newBillingTransaction->billing_options = serialize($tObj);
        $newBillingTransaction->save();
        
        $sid = $order->orderitem[0]->shippingmethods_id;
        $newShippingMethod = $order->shippingmethods[$sid];        
        $newShippingMethod->id = null;        
        $newShippingMethod->shipping_cost = 0;
        $newShippingMethod->save();
        $newShippingMethod->refresh();
        
        foreach($this->params['oi'] as $oikey=>$oi)
        {
            //eDebug($oikey);
            $newOi = new orderitem($oikey);
            $newOi->id = null;
            $newOi->quantity = $this->params['quantity'][$oikey];
            $newOi->orders_id = $newOrder->id;            
            $newOi->products_name = $this->params['products_name'][$oikey];        
            $newOi->products_price = $this->params['products_price'][$oikey];        
            $newOi->products_price_adjusted = $this->params['products_price'][$oikey];
            //$newOi->products_tax = 0;        
            $newOi->shippingmethods_id = $newShippingMethod->id;            
            $newOi->save();
        }
        
        flash('message','Reference Order #'. $newOrder->invoice_id . " created successfully.");
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$newOrder->id));        
    }
    
    function createReferenceOrder()
    {
        if (!isset($this->params['id']))
        {
            flashAndFlow('error','Unable to process request. Invalid order number.');
            expHistory::back();
        }
        $order = new order($this->params['id']);
        assign_to_template(array('order'=>$order));
        
    }
    
    function create_new_order()
    {             
        $order = new order();
        assign_to_template(array('order'=>$order));
        
    }
    
    function save_new_order()
    {
        //eDebug($this->params);
        /*addresses_id
        customer_type = 1 //new
        customer_type = 2 //existing MUS
        customer_type = 3 //existing external*/
        global $user,$db;
        //eDebug($this->params,true);
        //$order = new order($this->params['original_orderid']);
        //eDebug($order,true); 
        
        $newAddy = new address();
        if ($this->params['customer_type'] == 1)
        {
            //blank order
            $newAddy->save(false);
        }
        else if ($this->params['customer_type'] == 2)
        {
            //mus customer
            $newAddy = new address($this->params['addresses_id']);
        }
        else if ($this->params['customer_type'] == 3)
        {
            //other customer
            $otherAddy = new external_address($this->params['addresses_id']);           

            $newAddy->user_id = $otherAddy->user_id;
            $newAddy->firstname = $otherAddy->firstname;
            $newAddy->lastname = $otherAddy->lastname;    
            $newAddy->organization = $otherAddy->organization;
            $newAddy->address1 = $otherAddy->address1;
            $newAddy->address2 = $otherAddy->address2;
            $newAddy->city = $otherAddy->city;            
            $newAddy->state = $otherAddy->state;
            $newAddy->zip = $otherAddy->zip;
            $newAddy->phone = $otherAddy->phone;
            $newAddy->email = $otherAddy->email;  
            $newAddy->save();         
        }
        
        $newOrder = new order();
        $newOrder->order_status_id = $this->params['order_status_id'];
        $newOrder->order_type_id = $this->params['order_type_id'];
        //$newOrder->order_references = $order->id;
        $newOrder->reference_id = 0;
        $newOrder->user_id = 0;
        $newOrder->purchased = time();                
        $newOrder->updated = time();
        $newOrder->invoice_id = $newOrder->getInvoiceNumber();               
        $newOrder->orderitem = array();
        $newOrder->subtotal = 0;
        $newOrder->total_discounts = 0;
        $newOrder->tax = 0;
        $newOrder->shipping_total = 0;
        $newOrder->surcharge_total = 0;        
        $newOrder->grand_total = 0;
        
        $newOrder->save();
        $newOrder->refresh();
        
        $tObj->result->errorCode = 0;
        $tObj->result->message = "Reference Order Pending";
        $tObj->result->PNREF = "Pending";
        $tObj->result->authorization_code = "Pending";
        $tObj->result->AVSADDR = "Pending";
        $tObj->result->AVSZIP = "Pending";
        $tObj->result->CVV2MATCH = "Pending";
        $tObj->result->traction_type = "Pending"; 
        
        $newBillingMethod = new billingmethod();                
        $newBillingMethod->addresses_id = $newAddy->id;
        $newBillingMethod->orders_id = $newOrder->id;
        $newBillingMethod->billing_cost = 0;
        $newBillingMethod->billingcalculator_id = 6;
        $newBillingMethod->transaction_state = 'authorization pending';
        $newBillingMethod->billing_options = serialize($tObj);            
        $newBillingMethod->firstname = $newAddy->firstname;
        $newBillingMethod->lastname = $newAddy->lastname;    
        $newBillingMethod->organization = $newAddy->organization;
        $newBillingMethod->address1 = $newAddy->address1;
        $newBillingMethod->address2 = $newAddy->address2;
        $newBillingMethod->city = $newAddy->city;            
        $newBillingMethod->state = $newAddy->state;
        $newBillingMethod->zip = $newAddy->zip;
        $newBillingMethod->phone = $newAddy->phone;
        $newBillingMethod->email = $newAddy->email;
            
        $newBillingMethod->save();
        
        //eDebug(expUnserialize($order->billingmethod[0]->billing_options));        
        //eDebug(expUnserialize($order->billingmethod[0]->billingtransaction[0]->billing_options),true); 
               
        $newBillingTransaction = new billingtransaction();
        $newBillingTransaction->billingcalculator_id = 6; ///setting to manual/passthru
        $newBillingTransaction->billing_cost = 0;
        $newBillingTransaction->billingmethods_id = $newBillingMethod->id;
        $newBillingTransaction->transaction_state = 'authorization pending';
               
        $newBillingTransaction->billing_options = serialize($tObj);
        $newBillingTransaction->save();
        
        $newShippingMethod = new shippingmethod();                         
        $newShippingMethod->shipping_cost = 0;
        $newShippingMethod->shippingcalculator_id = $db->selectValue('shippingcalculator','id','is_default=1');;
        $newShippingMethod->addresses_id = $newAddy->id;
        $newShippingMethod->firstname = $newAddy->firstname;
        $newShippingMethod->lastname = $newAddy->lastname;    
        $newShippingMethod->organization = $newAddy->organization;
        $newShippingMethod->address1 = $newAddy->address1;
        $newShippingMethod->address2 = $newAddy->address2;
        $newShippingMethod->city = $newAddy->city;            
        $newShippingMethod->state = $newAddy->state;
        $newShippingMethod->zip = $newAddy->zip;
        $newShippingMethod->phone = $newAddy->phone;
        $newShippingMethod->email = $newAddy->email;
        $newShippingMethod->save();
        $newShippingMethod->refresh();
        
        $oi = new orderitem();
        $oi->orders_id = $newOrder->id;
        $oi->product_id = 0;
        $oi->product_type = 'product';
        $oi->products_name = "N/A";
        $oi->products_model = "N/A";
        $oi->products_price = 0;
        $oi->shippingmethods_id = $newShippingMethod->id;
        $oi->save(false);
        
        flash('message','New Order #'. $newOrder->invoice_id . " created successfully.");
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$newOrder->id));  
    }
    
    function save_payment_info ()
    {
        //need to save billing methods and billing options
        //$order = new order($this->params['id']);
       //eDebug($this->params, true);
       $obj = '';       
       foreach($this->params['result'] as $resultKey=>$resultItem)
       {
           $obj->$resultKey = $resultItem;
       }
       $res = serialize($obj);
       $billing = new billing($this->params['id']);
       // eDebug($billing);
       $billingmethod = $billing->billingmethod;
       $billingtransaction = $billingmethod->billingtransaction[0];
       
       $bmopts = expUnserialize($billingmethod->billing_options);
       $bmopts->result = $obj;
       $billingmethod->billing_options = serialize($bmopts);
       $billingmethod->save();
       
       $btopts = expUnserialize($billingtransaction->billing_options);
       $btopts->result = $obj;
       $billingtransaction->billing_options = serialize($btopts);
       $billingtransaction->save();
       
       flashAndFlow('message','Payment info updated.');
       redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['id']));
    }
    
    function edit_address ()
    {           
        //if $type = 'b' - business
        //if $type = 's' - shipping
        //addresses_id
        $order = new order($this->params['id']);
        $same = false;
        
        $sm = array_pop($order->shippingmethods);
        //$bm = array_pop($order->billingmethods);
        
        //eDebug($sm->addresses_id);
        //eDebug($order->billingmethod[0]->addresses_id);
        if($sm->addresses_id == $order->billingmethod[0]->addresses_id)
        {
            $same = true;
           // echo "Yes";
            //$addy = new address($sm->addresses_id);
        }
        
        if($this->params['type']=='b')
        {
            $type = 'billing';
            $addy = new address($order->billingmethod[0]->addresses_id);
        }else if ($this->params['type']=='s')
        {
            $type = 'shipping';
            $addy = new address($sm->addresses_id);
        }
       /* eDebug($this->params);
        eDebug($addy);
        eDebug($order,true);*/
        $billingmethod = new billingmethod($this->params['id']);
        //eDebug($billingmethod,true);
        //$opts = expUnserialize($billing->billingmethod->billing_options);
        //eDebug($billing);
        //eDebug($opts);
        assign_to_template(array('orderid'=>$this->params['id'],'record'=>$addy,'same'=>$same, 'type'=>$type));         
    }
    
    function save_address ()
    {
        global $db;
        $order = new order($this->params['orderid']);
        $billing = new billing($this->params['orderid']);
        $s = array_pop($order->shippingmethods);
        $shippingmethod = new shippingmethod($s->id);

        //eDebug($order);
        //eDebug($this->params,true);
        //eDebug($shippingmethod);
        $billingmethod = $billing->billingmethod;
       /*
       eDebug($order);
       eDebug($shippingmethod);
       eDebug($billingmethod);*/
              
        if ($this->params['save_option'] == 0)
        {
            //update existing
            //echo "Update";
            $addy = new address($this->params['addyid']);            
        }else if ($this->params['save_option'] == 1)
        {
            //create new
            //echo "New";
            $oldaddy = new address($this->params['addyid']);            
            $addy = new address();
            $addy->user_id = $oldaddy->user_id;
        }
        
        //eDebug($addy,true);
       
        foreach($this->params['address'] as $key=>$val)
        {
            $addy->$key = $val;
            if(isset($billingmethod->$key)) $billingmethod->$key = $val;
            if(isset($shippingmethod->$key)) $shippingmethod->$key = $val;
        }
        $addy->is_billing = 0;
        $addy->is_shipping = 0;
        $addy->save();
        $addy->refresh();
       
        
         if ($this->params['type'] == 'billing' || ($this->params['same'] == true && $this->params['save_option'] == 0))
        {
            //echo "Billing";
            $billingmethod->addresses_id = $addy->id;
            $billingmethod->save();
            $addy->is_billing = 1;        
        }
        
        if ($this->params['type'] == 'shipping' || ($this->params['same'] == true && $this->params['save_option'] == 0))
        {
            //eDebug("Shipping",true);
            $shippingmethod->addresses_id = $addy->id;
            $shippingmethod->save();            
            $addy->is_shipping = 1; 
        }
        
        $addy->save();   
        if($addy->is_default) $db->setUniqueFlag($addy,'addresses','is_default','user_id='.$addy->user_id);
        
        //eDebug($shippingmethod,true);
        flashAndFlow('message','Address updated.');
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['id']));
    }
    
    function delete_order_item()
    {
        $order = new order($this->params['orderid']);
        if(count($order->orderitem) <= 1) 
        {
            flash('error','You may not delete the only item on an order.  Please edit this item, or add another item before removing this one.');
            expHistory::back();
        }
        
        $oi = new orderitem($this->params['id']);
        $oi->delete();
        
        $s = array_pop($order->shippingmethods);
        $sm = new shippingmethod($s->id);
      
        $shippingCalc = new shippingcalculator($sm->shippingcalculator_id);
        $calcName = $shippingCalc->calculator_name; 
        $calculator = new $calcName($shippingCalc->id);
         
        $pricelist = $calculator->getRates($order);
        
        foreach($pricelist as $rate)
        {
            if($rate['id'] == $sm->option)
            {
                $sm->shipping_cost = $rate['cost'];                  
            }
        }       
        $sm->save();
      
        $order->refresh();
        $order->calculateGrandTotal();
        $order->save();
        
        flashAndFlow('message','Order item removed and order totals updated.');
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['orderid']));
    }
    
    function edit_order_item()
    {           
        $oi = new orderitem($this->params['id'],true,true);
        //oi->options = expUnserialize($oi->options);
        $oi->user_input_fields = expUnserialize($oi->user_input_fields);
        $oi->product = new product($oi->product->id,true,true);
        if ($oi->product->parent_id != 0) 
        {
            $parProd = new product($oi->product->parent_id);
            //$oi->product->optiongroup = $parProd->optiongroup;   
            $oi->product = $parProd;   
        }
        $oi->selectedOpts = array();
        if(!empty($oi->opts))
        {
            foreach ($oi->opts as $opt)
            {   
                $option = new option($opt[0]);
                $og = new optiongroup($option->optiongroup_id);
                if(!is_array($oi->selectedOpts[$og->id])) $oi->selectedOpts[$og->id] = array($option->id);
                else array_push($oi->selectedOpts[$og->id], $option->id);  
            }
        }
        //eDebug($oi->selectedOpts);
        assign_to_template(array('oi'=>$oi));            
    }
    
    function edit_invoice_id()
    {
        if(!isset($this->params['id'])) flashAndFlow('error','Unable to process request.  Order invalid.');
        $order = new order($this->params['id']);
        assign_to_template(array('orderid'=>$this->params['id'], 'invoice_id'=>$order->invoice_id));         
    }
    
    function save_invoice_id()
    {
        if(!isset($this->params['id'])) flashAndFlow('error','Unable to process request.  Order invalid.');
        if(empty($this->params['invoice_id']) || !is_numeric($this->params['invoice_id'])) flashAndFlow('error','Unable to process request.  Invoice ID #.');        
        $order = new order($this->params['id']);
        $order->invoice_id = $this->params['invoice_id'];
        $order->save(false);
        flashAndFlow('message','Invoice # saved.');
    }
    
    function save_order_item()
    {
        $oi = new orderitem($this->params['id']);
        //eDebug($this->params);
        
        /*eDebug($oi);
        eDebug(expUnserialize($oi->options));
        eDebug(expUnserialize($oi->user_input_fields),true);*/
        $oi->products_price = $this->params['products_price'];
        $oi->quantity = $this->params['quantity'];
        $oi->products_name = $this->params['products_name'];        
        
        if($oi->product->parent_id != 0)
        {
            $oi->product = new product($oi->product->parent_id,true,false);
        }else
        {
            //reattach the product so we get the optoin fields and such
            $oi->product = new product($oi->product->id,true,false);
        }
        
        //eDebug($oi->product,true);
        
        foreach ($oi->product->optiongroup as $og) {
            $isOptionEmpty = true;
            if (!empty($this->params['options'][$og->id]))
            {
                foreach ($this->params['options'][$og->id] as $opt)
                {  
                     if (!empty($opt)) $isOptionEmpty = false;
                }
            }
            if (!$isOptionEmpty) {
                foreach ($this->params['options'][$og->id] as $opt_id) {
                    $selected_option = new option($opt_id);
                    $cost = $selected_option->modtype == '$' ? $selected_option->amount :  $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost = $selected_option->updown == '+' ? $cost : $cost * -1;                                          
                    $options[] = array($selected_option->id,$selected_option->title,$selected_option->modtype,$selected_option->updown,$selected_option->amount);
                }
            }
        }
        
        eDebug($this->params);
        //eDebug($oi,true);
        
        $user_input_info = array();
        //check user input fields
        //$this->user_input_fields = expUnserialize($this->user_input_fields);
        //eDebug($this,true);
        foreach ($oi->product->user_input_fields as $uifkey=>$uif)
        {   
            /*if ($uif['is_required'] || (!$uif['is_required'] && strlen($params['user_input_fields'][$uifkey]) > 0)) 
            {
                if (strlen($params['user_input_fields'][$uifkey]) < $uif['min_length'])
                {
                    //flash('error', 'test');    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));  
                    $params['error'] .= $uif['name'].' field has a minimum requirement of ' . $uif['min_length'] . ' characters.<br/>';
                    
                }else if (strlen($params['user_input_fields'][$uifkey]) > $uif['max_length'] && $uif['max_length'] > 0)
                {
                    //flash('error', );    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));      
                    $params['error'] .= $uif['name'].' field has a maximum requirement of ' . $uif['max_length'] . ' characters.<br/>';
                } 
            }*/
            $user_input_info[] = array($uif['name']=>$this->params['user_input_fields'][$uifkey]);
        }
        //eDebug($options);
        //eDebug($user_input_info,true);
        
        $oi->options = serialize($options);
        $oi->user_input_fields = serialize($user_input_info);
        //eDebug($oi);        
        $oi->save();
        $oi->refresh();
        //eDebug($oi,true);
        
        $order = new order($oi->orders_id);
        $order->calculateGrandTotal();
              
        $s = array_pop($order->shippingmethods);
        eDebug($s);
        $sm = new shippingmethod($s->id);
       
        $shippingCalc = new shippingcalculator($sm->shippingcalculator_id);
        $calcName = $shippingCalc->calculator_name; 
        $calculator = new $calcName($shippingCalc->id);
         
        $pricelist = $calculator->getRates($order);
        
        foreach($pricelist as $rate)
        {
            if($rate['id'] == $sm->option)
            {
                $sm->shipping_cost = $rate['cost'];                  
                break;
            }
        }       
        $sm->save();
        $order->refresh();
        $order->calculateGrandTotal();
        $order->save();
        
        flashAndFlow('message','Order item updated and order totals recalculated.');
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['orderid']));
    }
    
    function add_order_item()
    {
        eDebug($this->params);
        $product = new product($this->params['product_id']);
        $paramsArray = array('orderid'=>$this->params['orderid']);        
        assign_to_template(array('product'=>$product, 'params'=>$paramsArray));            
    }
    
    function save_new_order_item()
    {
        //eDebug($this->params,true);
         //check for multiple product adding
        $order = new order($this->params['orderid']);
        if (isset($this->params['prod-quantity'])) 
        {
            //we are adding multiple children, so we approach a bit different
            //we'll send over the product_id of the parent, along with id's and quantities of children we're adding 
            foreach ($this->params['prod-quantity'] as $qkey=>&$quantity)
            {
                if (in_array($qkey,$this->params['prod-check']))
                {
                    $this->params['children'][$qkey] = $quantity;    
                }
                if (isset($child)) $this->params['product_id'] = $child->parent_id;                                                       
            }
        }         
        
        $product = new $this->params['product_type']($this->params['product_id'], true, true);  //need true here?
               
        if ($product->addToCart($this->params,$this->params['orderid']))
        {               
            $order->refresh();
            $order->calculateGrandTotal();
            $order->save();
            flashAndFlow('message','Product added to order.');
            redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['orderid']));
        } 
        /*else
        {
            expHistory::back();
        }*/
    }
    
    function edit_totals()
    {
        //eDebug($this->params);
        $order = new order($this->params['orderid']);        
        assign_to_template(array('orderid'=>$this->params['id'],'order'=>$order));            
    }
    
    function save_totals()
    {
        //eDebug($this->params);
        //if(!is_numeric($this->params['subtotal']))
        $order = new order($this->params['orderid']);        
        $order->subtotal = $this->params['subtotal'];
        $order->total_discounts = $this->params['total_discounts'];
        $order->total = round($order->subtotal - $order->total_discounts,2);
        $order->tax = $this->params['tax'];
        $order->shipping_total = $this->params['shipping_total'];
        //note: the shippingmethod record will still reflect the ORIGINAL shipping amount for this order.
        $order->surcharge_total = $this->params['surcharge_total'];
        
        if($this->params['autocalc'] == true)
        {
            $order->grand_total = round(($order->subtotal - $order->total_discounts) + $order->tax + $order->shipping_total + $order->surcharge_total,2);   
        }else
        {
            $order->grand_total = round($this->params['grand_total'],2);   
        }
        $order->save();
        flashAndFlow('message','Order totals updated.');
        redirect_to(array('controller'=>'order','action'=>'show','id'=>$this->params['orderid']));
    }
    
    function update_sales_reps()
    {
        if (!isset($this->params['id']))
        {
            flashAndFlow('error','Unable to process request. Invalid order number.');
            //expHistory::back();
        }
        $order = new order($this->params['id']);        
        $order->sales_rep_1_id = $this->params['sales_rep_1_id'];
        $order->sales_rep_2_id = $this->params['sales_rep_2_id'];
        $order->sales_rep_3_id = $this->params['sales_rep_3_id'];
        $order->save();
        flashAndFlow('message','Sales reps updated.');
    }
    
    function quickfinder()
    {
        
        global $db;  
        $search = $this->params['ordernum'];
        $searchInv = intval($search);
        
        $sql = "SELECT DISTINCT(o.id), o.invoice_id, FROM_UNIXTIME(o.purchased,'%c/%e/%y %h:%i:%s %p') as purchased_date, b.firstname as bfirst, b.lastname as blast, o.grand_total, os.title as status_title, ot.title as order_type";             
        $sql .= " from " . $db->prefix . "orders as o ";
        $sql .= "INNER JOIN " . $db->prefix . "orderitems as oi ON oi.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_type as ot ON ot.id = o.order_type_id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_status as os ON os.id = o.order_status_id ";
        $sql .= "INNER JOIN " . $db->prefix . "billingmethods as b ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "shippingmethods as s ON s.id = oi.shippingmethods_id ";
        
        $sqlwhere = "WHERE o.purchased != 0";
        if($searchInv != 0) $sqlwhere .= " AND (o.invoice_id LIKE '%" . $searchInv ."%' OR";
        else $sqlwhere .= " AND (";
        $sqlwhere .= " b.firstname LIKE '%" . $search . "%'";       
        $sqlwhere .= " OR s.firstname LIKE '%" . $search . "%'";       
        $sqlwhere .= " OR b.lastname LIKE '%" . $search . "%'";       
        $sqlwhere .= " OR s.lastname LIKE '%" . $search . "%'";       
        $sqlwhere .= " OR b.email LIKE '%" . $search . "%')";       
        
        $limit = empty($this->config['limit']) ? 350 : $this->config['limit'];
        //eDebug($sql . $sqlwhere)  ;
        $page = new expPaginator(array(            
            'sql'=>$sql . $sqlwhere, 
            'limit'=>$limit,
            'order'=>'o.invoice_id',
            'dir'=>'DESC',            
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array('actupon'=>true,'Order #'=>'invoice_id|controller=order,action=show,showby=id','Purchased Date'=>'purchased_date','First'=>'bfirst','Last'=>'blast','Total'=>'grand_total','Order Type'=>'order_type','Status'=>'status_title'),
        ));
        assign_to_template(array('page'=>$page));   
        
        //eDebug($this->params);
        /*$o = new order();
        $b = new billingmethod();
        $s = new shippingmethod();
        
        $search = intval($this->params['ordernum']);
        if (is_int($oid) && $oid > 0)
        {
            $orders = $o->find('all',"invoice_id LIKE '%".$oid."%'");
            if(count($orders == 1))
            {
                redirect_to(array('controller'=>'order','action'=>'show','id'=>$order[0]->id));            
            }
            else
            {
                flashAndFlow('message',"Orders containing " . $search . " in the order number not found.");
            }
        }
        else
        {
            //lookup just a customer
            $bms = $b->find('all', )
        }*/
        /*$o = new order();
        $oid = intval($this->params['ordernum']);
        if (is_int($oid) && $oid > 0)
        {
            $order = $o->find('first','invoice_id='.$oid);
            if(!empty($order->id))
            {
                redirect_to(array('controller'=>'order','action'=>'show','id'=>$order->id));            
            }
            else
            {
                flashAndFlow('message',"Order #" . intval($this->params['ordernum']) . " not found.");
            }
        }
        else
        {
            flashAndFlow('message','Invalid order number.');        
        }*/
    }
    
    public function verifyReturnShopper()
    {
        global $user,$order;        
        $sessAr = exponent_sessions_get('verify_shopper');
        if(isset($sessAr))
        {
            assign_to_template(array('firstname'=>$sessAr['firstname']));
            /*eDebug(exponent_sessions_get('verify_shopper'));
            eDebug($this->params);
            eDebug("here");
            eDebug($user);
            eDebug($order);*/   
        }        
    }
    
    public function verifyAndRestoreCart()
    {
        global $user,$order;
        $sessAr = exponent_sessions_get('verify_shopper');        
        if(isset($sessAr) && isset($this->params['cid']) && $this->params['cid'] == $sessAr['cid'])        
        {
            $tmpCart = new order($sessAr['cid']);
            if(isset($tmpCart->id))
            {   
                //eDebug($tmpCart,true); 
                $shippingMethod = $tmpCart->shippingmethod;
                $billingMethod = $tmpCart->billingmethod[0];                
                             
                if( ($this->params['lastname'] == $shippingMethod->lastname || $this->params['lastname'] == $billingMethod->lastname) &&
                    ($this->params['email'] == $shippingMethod->email || $this->params['email'] == $billingMethod->email) &&
                    ($this->params['zip_code'] == $shippingMethod->zip || $this->params['zip_code'] == $billingMethod->zip))
                {
                    //validatio succeed, so restore order, login user and continue on to orig_path
                    //eDebug("Validated",true);
                    $sessAr['validated'] = true;
                    exponent_sessions_set('verify_shopper',$sessAr); 
                    redirect_to($sessAr['orig_path']);  
                }
                else
                {
                    //eDebug("Validated NOT",true);
                    //validation failed, so go back
                    flash('error',"We're sorry, but we could not verify your information.  Please try again, or start a new shopping cart.");
                    redirect_to(array('controller'=>'order','action'=>'verifyReturnShopper','id'=>$sessAr['cid']));  
                }
            }            
            else
            {
                flash('error','We were unable to restore the previous order, we apologize for the inconvenience.  Please start a new shopping cart.');
                $this->clearCart();
            }               
        }        
    }
    
    public function clearCartCookie()
    {
         exponent_sessions_unset('verify_shopper');
         order::setCartCookie(null);
    }

    public function clearCart()
    {   
        global $order;
        $sessAr = exponent_sessions_get('verify_shopper');
        if(isset($sessAr))       
        {               
            order::setCartCookie($order);            
            $orig_path = $sessAr['orig_path'];
            exponent_sessions_unset('verify_shopper');
            redirect_to($orig_path);
        }else
        {
            expHistory::back();
        }
    }
    
    public function search() {
        global $db, $user;
        $sql = "select DISTINCT(a.id) as id, a.firstname as firstname, a.middlename as middlename, a.lastname as lastname, a.organization as organization, a.email as email ";
        $sql .= "from " . $db->prefix . "addresses as a "; //R JOIN " . 
        //$db->prefix . "billingmethods as bm ON bm.addresses_id=a.id ";
        $sql .= " WHERE match (a.firstname,a.lastname,a.email,a.organization) against ('" . $this->params['query'] . 
        "*' IN BOOLEAN MODE) "; 
        $sql .= "order by match (a.firstname,a.lastname,a.email,a.organization)  against ('" . $this->params['query'] . "*') ASC LIMIT 12";
        $res = $db->selectObjectsBySql($sql);
        //eDebug($sql);
        $ar = new expAjaxReply(200, gettext('Here\'s the items you wanted'), $res);
        $ar->send();
    }    
    
    public function search_external() {
        global $db, $user;
        $sql = "select DISTINCT(a.id) as id, a.source as source, a.firstname as firstname, a.middlename as middlename, a.lastname as lastname, a.organization as organization, a.email as email ";
        $sql .= "from " . $db->prefix . "external_addresses as a "; //R JOIN " . 
        //$db->prefix . "billingmethods as bm ON bm.addresses_id=a.id ";
        $sql .= " WHERE match (a.firstname,a.lastname,a.email,a.organization) against ('" . $this->params['query'] . 
        "*' IN BOOLEAN MODE) "; 
        $sql .= "order by match (a.firstname,a.lastname,a.email,a.organization)  against ('" . $this->params['query'] . "*') ASC LIMIT 12";
        $res = $db->selectObjectsBySql($sql);
        //eDebug($sql);
        $ar = new expAjaxReply(200, gettext('Here\'s the items you wanted'), $res);
        $ar->send();
    }
    
}

?>
