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
	function pre_process($config_object,$order,$billaddress,$shippingaddress) {
		return true;
	}
	
	function post_process() {
		return true;
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
		include_once(BASE."framework/core/subsystems-1/forms.php");
		$form = new form();
		$htmlinfo = "You may place your order and pay with a check or money order.  If paying by check, your order will be held util we receive the check and it clears our bank account.  Money order orders will be processed upon our receipt of the money order.<br/><br/>";
		$form->register(uniqid(""),"", new htmlcontrol($htmlinfo));
	  	$form->register("cash_amount","Cash Amount:",new textcontrol());
		return $form->toHTML();
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
	function userView($config_object,$user_data=null) {
		return "Cash: $". number_format($user_data->cash_amount,2,".",",");
	}
}

?>
