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
	function preprocess($method, $opts, $params, $order)
    {

         //just save the opts        
        $method->update(array('billing_options'=>serialize($opts)));
        //eDebug($method,true);
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
	
	//Config Form
	function form($config_object) {
		include_once(BASE."framework/core/subsystems-1/forms.php");
		$form = new form();
		if (!$config_object) {
			$config_object->give_change = true;
		}
		$form->register("give_change","Give Change?",new checkboxcontrol($config_object->give_change));
		$form->register("submit","",new buttongroupcontrol("Save","","Cancel"));
		return $form->toHTML();
	}
	
	//process config form
	function update($values, $config_object) {
		$config_object->give_change = $values['give_change'];
		return $config_object;
	}
	
	//Form for user input
	function userForm($config_object=null, $user_data=null) {
		$form = '';
		
		$cash_amount = new textcontrol("",20,false,20,"", true);
		$cash_amount->id = "cash_amount";
		
		$form .= $cash_amount->toHTML("Cash Amount", "cash_amount");
		
		return $form;	
	}
	
	//process user input. This function should return an object of the user input.
	//the returnd object will be saved in the session and passed to post_process.
	//If need be this could use another method of data storage, as long post_process can get the data.
	function userProcess($values, $config_object, $user_data) {
		$user_data->cash_amount = $values["cash_amount"];
		return $user_data;
	}
	
	//This is called when a billing method is deleted. It can be used to clean up if you
	//have any custom user_data storage.
	function delete($config_object) {
		return;
	}
	
	//This should return html to display config settings on the view billing method page
	function view($config_object) {
		return "Give Change: ".(($config_object->give_change)?"Yes":"No")."<br>";
	
	}
	
	//Should return html to display user data.
	function userView($opts) {
		if (empty($opts)) return false;
		  
		return "Cash: $". number_format($opts->cash_amount,2,".",",");
	}
	
	function userFormUpdate($params) {
		global $order; 
		if ($order->grand_total > $params["cash_amount"]) {
			expValidator::failAndReturnToForm("The total amount of your order is greater than what the amount you have input. <br /> Please enter exact or greater amount of your total.");
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
