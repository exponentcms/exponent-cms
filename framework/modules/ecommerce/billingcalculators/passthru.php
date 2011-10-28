<?php
/** @define "BASE" "../../../.." */

class passthru extends billingcalculator {
	function name() { return "Passthru Payment"; }
	function description() { return "Enabling this payment option will allow you or your customers to bypass payment processing at the cart and allow payment methods after the order is processed, such as cash, check, pay in store, or manually process via credit card.<br>** This is a restricted payment option and only accessible by site admins."; }
	function hasConfig() { return false;}
	function hasUserForm() { return false;}
	function isOffsite() { return false; }
	function isSelectable() { return true; }
    function isRestricted() { return true; }
	
	public $title = 'Passthru Payment';
	public $payment_type = 'Passthru';
	
	//Called for billing medthod seletion screen, return true if it's a valid billing method.
	function pre_process($config_object,$order,$billaddress,$shippingaddress) {
		return true;
	}
	
	function post_process() {
		return true;
	}
	
	//Config Form
	function form($config_object) {
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
		//add restrictions config stuff here
        return '';
	
	}
	
	//Should return html to display user data.
	function userView($opts) {          
        //eDebug($opts,true);
        if(isset($opts->result)) return '';
        $ot = new order_type($opts->order_type);
        $os = new order_status($opts->order_status);
        $sr1 = new sales_rep($opts->sales_rep_1_id);
        $sr2 = new sales_rep($opts->sales_rep_2_id);
        $sr3 = new sales_rep($opts->sales_rep_3_id);
        $msg = 'Order Type: ' . $ot->title;                
        $msg .= '<br>Order Status: ' . $os->title;                
        $msg .= '<br>Sales Rep 1: ' . $sr1->initials;                
        $msg .= '<br>Sales Rep 2: ' . $sr2->initials;                
        $msg .= '<br>Sales Rep 3: ' . $sr3->initials;                
        //$order
        return $msg;
	}
    
    function userFormUpdate($params)
    {  
        //eDebug($params,true);
        //eturn array('order_type'=>$params['order_type'],'order_status'=>$params['order_status'],'sales_rep_1_id'=>$params['sales_rep_1_id'],'sales_rep_2_id'=>$params['sales_rep_2_id'],'sales_rep_3_id'=>$params['sales_rep_3_id']);        
        $obj->order_type = $params['order_type'];
        $obj->order_status = $params['order_status'];
        $obj->sales_rep_1_id = $params['sales_rep_1_id'];
        $obj->sales_rep_2_id = $params['sales_rep_2_id'];
        $obj->sales_rep_3_id = $params['sales_rep_3_id'];
        return $obj;
    }
    
    function preprocess($method, $opts, $params, $order)
    {
        $method->update(array('billing_options'=>serialize($opts)));
        if(isset($params['sales_rep_1_id'])) $order->sales_rep_1_id = $params['sales_rep_1_id'];
        if(isset($params['sales_rep_2_id'])) $order->sales_rep_2_id = $params['sales_rep_2_id'];
        if(isset($params['sales_rep_3_id'])) $order->sales_rep_3_id = $params['sales_rep_3_id'];
        $order->save();
       /* eDebug($method);
        eDebug($opts);
        eDebug($params,true); */
        return true;
    }
    
    function process($method, $opts, $params, $invoice_number)
    {
        global $order;
        $object->errorCode = 0;
        $object->message = 'Authorization pending.';
        $object->PNREF = 'Pending';
        $object->authorization_code = 'Pending';
        $object->AVSADDR = 'Pending';
        $object->AVSZIP = 'Pending';
        $object->CVV2MATCH = 'Pending';
        $object->traction_type = 'Pending';
        $trax_state = "authorization pending"; 
        
        //$opts2->billing_info = $opts;
        $opts2->result = $object;
        //eDebug($opts,true);
        /*$opts->result = $object;        
        $opts->cc_number = 'xxxx-xxxx-xxxx-'.substr($opts->cc_number, -4);*/
        $method->update(array('billing_options'=>serialize($opts2), 'transaction_state'=>$trax_state));
        $this->createBillingTransaction($method,number_format($order->grand_total, 2, '.', ''),$object,$trax_state);
        return $object;
    }
    
    function postProcess($order, $params)
    {
        //check order types and create new user if necessary
        global $db,$user;
        $ot = new order_type($order->order_type_id);
        if ($ot->creates_new_user == true)
        {
            $addy = new address($order->billingmethod[0]->addresses_id);
            $newUser = new user();
            $newUser->username = $addy->email . time();  //make a unique username
            $password = md5(time().rand(50,000));  //generate random password
            $newUser->setPassword($password, $password);
            $newUser->email = $addy->email;
            $newUser->firstname = $addy->firstname;
            $newUser->lastname = $addy->lastname;            
            $newUser->is_system_user = false;
            $newUser->save(true);
            $newUser->refresh();            
            $addy->user_id = $newUser->id;
            $addy->is_default = true;
            $addy->save();
            $order->user_id = $newUser->id;
            $order->save();
            
            if($order->orderitem[0]->shippingmethod->addresses_id != $addy->id)
            {
                $addy = new address($order->orderitem[0]->shippingmethod->addresses_id);                            
                $addy->user_id = $newUser->id;
                $addy->is_default = false;
                $addy->save();
            }
            
            //make sure current user is good to go
            $defAddy = $addy->find('first','user_id='.$user->id);
            $obj->id = $defAddy->id;
            $db->setUniqueFlag($obj, 'addresses', 'is_default', 'user_id='.$user->id);
            $db->setUniqueFlag($obj, 'addresses', 'is_shipping', 'user_id='.$user->id);
            $db->setUniqueFlag($obj, 'addresses', 'is_billing', 'user_id='.$user->id);
        }
        return true;
    }
    
     function getPaymentAuthorizationNumber($billingmethod){
        $ret = expUnserialize($billingmethod->billing_options);
        //eDebug($ret);
        return $ret->result->authorization_code;
    }
    
    function getPaymentReferenceNumber($opts) {
        $ret = expUnserialize($opts);
        if (isset($ret->result))
        {
            return $ret->result->PNREF;    
        }
        else
        {
            return $ret->PNREF;
        }         
    }
    
    function getPaymentStatus($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->message;
    }
    
    function getAVSAddressVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AVSADDR;
    }
                                
    function getAVSZipVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AVSZIP;
    }
    
    function getCVVMatched($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->CVV2MATCH;
    }
    
    function getPaymentMethod($billingmethod) {
        //$ret = expUnserialize($billingmethod->billing_options);
        return 'Manual';
    }
}

?>
