<?php
##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Created by Adam Kessler @ 05/28/2008
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

class splitcreditcard extends creditcard {
	function name() { return 'Split Credit Card'; }
	function description() {
	    return "Enabling this payment option will allow your customers to use their credit card to make purchases on your site.  The credit card number
	    will be split with part of it being stored in the database and the other part getting emailed to site administrator.";
	}
	function hasConfig() { return true;}
	function hasUserForm() { return true;}
	function isOffsite() { return false; }
	function isSelectable() { return true; }
	
	public $title = 'Credit Card';
	
    //TODO: I don't think this is used any more but i don't have a clue
	//Called for billing method selection screen, return true if it's a valid billing method.
	function pre_process() {
		return true;
	}
	
	//called when the order is submitted. Should return an object...
	function process($method, $opts) {
		// make sure we have some billing options saved.
		if (empty($opts)) return false;
		
		// get the configuration data
		$config = unserialize($this->config);
		
		$txtmessage = "The following order requires your attention.\r\n\r\n";
        $txtmessage .= $this->textmessage($this->opts);
                
		$htmlmessage = "The following order requires your attention.<br><br>";
		$htmlmessage .= $this->htmlmessage($this->opts);
		
		$addresses = explode(',', $config['notification_addy']);
        foreach ($addresses as $address) {
		    $mail = new expMail();
		    $mail->quickSend(array(
		                'html_message'=>$htmlmessage,
					    'text_message'=>$txtmessage,
					    'to'=>trim($address),
//					    'from'=>ecomconfig::getConfig('from_address'),
//					    'from_name'=>ecomconfig::getConfig('from_name'),
					    'from'=>array(ecomconfig::getConfig('from_address')=>ecomconfig::getConfig('from_name')),
					    'subject'=>'Billing Information for an order placed on '.ecomconfig::getConfig('storename'),
		    ));
		}
		
		$this->opts->cc_number = 'XXXX-XXXX-XXXX-'.substr($this->opts->cc_number,-4);
		$method->update(array('billing_options'=>serialize($this->opts)));
		return true;
	}	
	
	function postProcess() {
		return true;
	}
	
	//Form for user input
	function userForm() {
		return parent::userForm();
	}
	
	//process user input. This function should return an object of the user input.
	//the returnd object will be saved in the session and passed to post_process.
	//If need be this could use another method of data storage, as long post_process can get the data.
	function userFormUpdate($params) {
		return parent::userFormUpdate($params);
	}
	
	//Should return html to display user data.
	function userView($opts) {
		return parent::userView($opts);
	}
	
	//Config Form
	function configForm() {
		$form = BASE.'framework/modules/ecommerce/billingcalculators/views/splitcreditcard/configure.tpl';	
		return $form;
	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('accepted_cards', 'email_customer', 'email_admin', 'notification_addy');
	    foreach ($config_vars as $varname) {
	        $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	    }
	    
		return $config;
	}
	
	//process config form
	function update($values, $config_object) {
		/*$config_object->email_contact = $values['email_contact'];
		$config_object->email_subject = $values['email_subject'];
		$config_object->email_intro = $values['email_intro'];
		
		$config_object->accept_amex = (isset($values['accept_amex']) ? 1 : 0);
		$config_object->accept_discover = (isset($values['accept_discover']) ? 1 : 0);
		$config_object->accept_mastercard = (isset($values['accept_mastercard']) ? 1 : 0);
		$config_object->accept_visa = (isset($values['accept_visa']) ? 1 : 0);
		$config_object->email_customer = isset($values['email_customer']);		
		$config_object->email_customer_invoice = isset($values['email_customer_invoice']);		
		$config_object->email_other_invoice = $values['email_other_invoice'];		
		$config_object->email_customer_status_change = isset($values['email_customer_status_change']);		
		$config_object->email_other_status_change = $values['email_other_status_change'];		
		return $config_object;*/
	}
	
	
	
	
	//This is called when a billing method is deleted. It can be used to clean up if you
	//have any custom user_data storage.
	function delete($config_object) {
		return;
	}
	
	//This should return html to display config settings on the view billing method page
	function view($config_object) {
		/*$html .= '<br />Settings:<hr>';
		$html .= 'Email Contact: ' . $config_object->email_contact.'<br />';
		$html .= 'Subject of Message: '. $config_object->email_subject.'<br />';
		$html .= 'Message: '.$config_object->email_message.'<br />';
		$html .= '<br />Accepted Cards:<hr>';
		$html .= 'American Express: '.(($config_object->accept_amex)?'Yes':'No').'<br />';
		$html .= 'Discover: '.(($config_object->accept_discover)?'Yes':'No').'<br />';
		$html .= 'Mastercard: '.(($config_object->accept_mastercard)?'Yes':'No').'<br />';
		$html .= 'Visa: '.(($config_object->accept_visa)?'Yes':'No').'<br />';
		return $html;*/
	}
	
	function textmessage($opts) {
		global $order;
		//FIXME: hard coded text!!
		$message = "Order Number: $order->invoice_id\r\n";
        $message .= 'Credit Card Number: '.substr($opts->cc_number,0,-4).'XXXX'."\r\n";
        $message .= 'Credit Card CVV Number: '.$opts->cvv."\r\n";
        $message .= 'Expires on: '.$opts->exp_month.'/'.$opts->exp_year."\r\n";
		return $message;
	}
	
	function htmlmessage($opts) {
		global $order;
		//FIXME: hard coded text!!
		$message = "Order Number: $order->invoice_id<br>";
        $message .= 'Credit Card Number: '.substr($opts->cc_number,0,-4).'XXXX'."<br>";
        $message .= 'Credit Card CVV Number: '.$opts->cvv."<br>";
        $message .= 'Expires on: '.$opts->exp_month.'/'.$opts->exp_year."<br>";
		return $message;
	}
	
	function getPaymentAuthorizationNumber($billingmethod){
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->token;       
    }
    
    function getPaymentReferenceNumber($opts) {
        $ret = expUnserialize($opts);
        if (isset($ret->result))
        {
            return $ret->result->transId;
        }
        else
        {
            return $ret->transId;
        }
    }
    
    function getPaymentStatus($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->payment_status;
    }
    
    function getPaymentMethod($billingmethod) {
        return $this->title;
    }
    
    function showOptions() {
        return;
    }
	
	function getAVSAddressVerified($billingmethod) {
        return 'X';
    }
    
    function getAVSZipVerified($billingmethod) {
        return 'X';
    }
    
    function getCVVMatched($billingmethod) {
        return 'X';
    }
	
}

?>
