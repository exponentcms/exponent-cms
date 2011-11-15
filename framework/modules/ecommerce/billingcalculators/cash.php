<?php
/** @define "BASE" "../../../.." */

class cash extends billingcalculator{
	function name() { return "Cash/Check"; }
	function description() { return "Enabling this payment option will allow your customers to pay by sending cash or check to you."; }
	function hasConfig() { return false;}
	function hasUserForm() { return false;}
	function isOffsite() { return false; }
	function isSelectable() { return true; }
	
	public $title = 'Cash/Check';
	public $payment_type = 'Cash';
	
	//Called for billing medthod seletion screen, return true if it's a valid billing method.
	function preprocess($method, $opts, $params, $order) {
         //just save the opts        
        $method->update(array('billing_options'=>serialize($opts)));
     
    }
	
	function process($method, $opts, $params, $invoice_number) {
		global $order, $db, $user;
		$object->errorCode = 0;
		
		$opts->result = $object;  
		$opts->result->payment_status = "Pending";
        $method->update(array('billing_options'=>serialize($opts)));
		$this->createBillingTransaction($method, number_format($order->grand_total, 2, '.', ''),$opts->result,'pending');
		return $object;
	}
	
	function userForm($config_object=null, $user_data=null) {
		$form = '';
		
		$cash_amount = new textcontrol("",20,false,20,"", true);
		$cash_amount->id = "cash_amount";
		
		$form .= $cash_amount->toHTML("Cash Amount", "cash_amount");
		
		return $form;	
	}
	
	//Should return html to display user data.
	function userView($opts) {
	
		if (empty($opts)) return false;
		  
		return "Cash: $". number_format($opts->cash_amount,2,".",",");
	}
	
	function userFormUpdate($params) {
	
		global $order; 
		
		if ($order->grand_total > $params["cash_amount"]) {
			expValidator::failAndReturnToForm(gt("The total amount of your order is greater than what the amount you have input.")."<br />".gt("Please enter exact or greater amount of your total."));
		}
      
		$this->opts = null;
 
        $this->opts->cash_amount = $params["cash_amount"];
		return $this->opts;
	}
	
	function getPaymentAuthorizationNumber($billingmethod){
	
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->token;       
    }
    
    function getPaymentReferenceNumber($opts) {
        $ret = expUnserialize($opts);
		
        if (isset($ret->result)) {
            return $ret->result->transId;
        }
        else {
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