<?php

//TODO: make into php5 class with access modifiers proprities and all that jazz.
class worldpayCheckout extends billingcalculator {
    /**
    * The name that will be displayes in the payment methods selector admin screen.
    * @return string Then name of the billing calculator
    */
	function name() { return "Worldpay Checkout"; }
	public function captureEnabled() {return true; }
    public function voidEnabled() {return true; }
    public function creditEnabled() {return true; }
    
    /**
    * The description that will be displayed in the payment methods selector admin screen
    * @return string A short description
    */
    function description() {
	    return "Enabling this payment option will allow your customers to use their worldpay account to make purchases.";
	}
    
    /**
    * Does this billing calculator need some configuration to work?
    * @return boolean
    */
	function hasConfig() { return true;}
    
    /**
    * Does this billing calculator have a User Form?
    * @return boolean
    */
	function hasUserForm() { return false;}
    
    /**
    * Does this billing calculator take the user offsite?
    * @return boolean
    */
	function isOffsite() { return true; }
    
    /**
    * Is this billing calculator selectable in the payment methods. It may not be if it is meant more as base class for other calculators to extend from
    * @return boolean
    */
	function isSelectable() { return true; }
    
    public $title = 'Worldpay Checkout';
    public $payment_type = 'Worldpay';
    

    function pre_process($config_object,$order,$billaddress,$shippingaddress) {
        return true;
    }
    
    function preprocess($method, $opts, $params, $order) {
	
        global $db, $user;
        
		if(!isset($params['transStatus'])) {
			// make sure we have some billing options saved.
			if (empty($method)) {
				return false;
			}
				
			$config = unserialize($this->config);
			$worldpay_url = 'https://secure-test.worldpay.com/wcc/dispatcher';

			if (isset($config['testmode'])) {
				$testmode = 100;
			} else {
				$testmode = 0;
			}
			
			if (isset($config['authCurrency'])) {
				$authCurrency = $config['authCurrency'];
			} else {
				$authCurrency = "USD";
			}

			$data = array(
				// required parameters
				'testMode'  => $testmode,
				'instId'    => $config['installationid'],
				'amount'    => number_format($order->grand_total, 2, '.', ''),
				'currency'  => $authCurrency,
				'cartId'    => $order->id,
				'MC_callback' => URL_FULL . 'external/worldpay/callback.php'
			);
			 // convert the api params to a name value pair string
			$datapost = "";
			while(list($key, $value) = each($data)) 
			{
				$datapost .= $key . '=' . urlencode(str_replace(',', '', $value)) . '&';
			}
				
			// take the last & out for the string
			$datapost = substr($datapost, 0, -1);
			$url = $worldpay_url . '?' . $datapost;
			header('location: ' . $url);
			exit();
			
		} else {
		
			$object = expUnserialize($method->billing_options);
            if ($params['transStatus'] == 'Y') {
				$object->result->errorCode = 0;
                $object->result->message = "User has approved the payment at Worldpay";
                $object->result->transId = $params['transId'];    
				$object->result->payment_status = "Pending"; 				
                $method->update(array('billing_options'=>serialize($object), 'transaction_state' => "Pending"));    
				$this->createBillingTransaction($method, number_format($order->grand_total, 2, '.', ''),$object, 'success');				
                redirect_to(array('controller'=>'cart', 'action'=>'process'));
            } else {
                redirect_to(array('controller'=>'cart', 'action'=>'checkout'));
            }
        }        
    }
    
	function process($method, $opts, $params, $invoice_number) {
		
	}
    
	function configForm() {
		$form = BASE.'framework/modules/ecommerce/billingcalculators/views/worldpayCheckout/configure.tpl';	
		return $form;
	}
	
	/**
    * process config form
    * 
    * @param mixed $values
    */
	function parseConfig($values) {
	    $config_vars = array('username', 'password', 'installationid', 'authCurrency', 'testmode', 'email_customer', 'email_admin', 'notification_addy');
	    foreach ($config_vars as $varname) {
	        $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	    }
	    
		return $config;
	}
	
	/**
    * This is called when a billing method is deleted. It can be used to clean up if you have any custom user_data storage.
    * 
    * @param mixed $config_object
    */
	function delete($config_object) {
		return;
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
	
	// credit transaction
    function credit_transaction($method, $amount) {
		
    }
	
	public $cards = array("AmExCard"=>"American Express","DiscoverCard"=>"Discover","MasterCard"=>"MasterCard", "VisaCard"=>"Visa");
    public $card_images = array(
        "AmExCard"=>"path/to/image.png",
        "DiscoverCard"=>"path/to/image.png",
        "MasterCard"=>"path/to/image.png", 
        "VisaCard"=>"path/to/image.png"
    );
	
	function userForm() {

		return '';    
    }
	
	function userView($opts) {
		return '';
	}
	
	function userFormUpdate($params) {
	
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