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
    
    /**
    * For paypal this will call out to the PP api and get a token then redirect to PP.
    * PP then redirects back the site with token in the url. We can pick up that token 
    * from the url such that if we already have it we'll ccall another PP api to get the
    * details and make it match up to the order.
    * 
    * @param mixed $method The billing method information for this user
    * @param mixed $opts 
    * @param array $params The url prameters, as if sef was off. 
    * @return mixed An object indicating pass of failure. 
    */
    function preprocess($method, $opts, $params, $order) {
	
        global $db, $user;
        
		// make sure we have some billing options saved.
		if (empty($method)) {
			return false;
		}
            
		// get a shipping address to display in the invoice email.
		$shippingaddress = $order->getCurrentShippingMethod();
		$shipping_state = new geoRegion($shippingaddress->state);
		$shipping_country = new geoCountry($shipping_state->country_id);
            
		$state = new geoRegion($method->state);
		$country = new geoCountry($state->country_id);
            
		$config = unserialize($this->config);
		$worldpay_url = 'https://secure-test.wp3.rbsworldpay.com/wcc/purchase';
            
                                                                                                  
		$data = array(
			// required parameters
			'instId'    => $config['installationid'],
			'amount'    => number_format($order->grand_total, 2, '.', ''),
			'testMode'  => '100',
			'currency'  => 'USD',
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
		
		//setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $worldpay_url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1); 
        //setting the datapost as POST FIELD to curl
		
        curl_setopt($ch,CURLOPT_POSTFIELDS, $datapost);
        
        //getting response from server
        $response = curl_exec($ch);
		curl_close($ch);
		echo $response;
        exit();
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
	    $config_vars = array('username', 'password', 'installationid', 'testmode', 'email_customer', 'email_admin', 'notification_addy');
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
            return $ret->result->correlationID;
        }
        else
        {
            return $ret->correlationID;
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
        // make sure we have some billing options saved.
        //if (empty($this->opts)) return false;
        
        //exponent_javascript_toFoot('creditcard',"",null,'', URL_FULL.'subsystems/forms/js/AuthorizeNet.validate.js');
        //$opts->first_name = isset($this->opts->first_name) ? $this->opts->first_name : null;
        //$opts->last_name = isset($this->opts->last_name) ? $this->opts->last_name : null;
        $this->opts = expSession::get('billing_options');
        $opts->cc_type = isset($this->opts->cc_type) ? $this->opts->cc_type : null;
        $opts->cc_number = isset($this->opts->cc_number) ? $this->opts->cc_number : null;
        $opts->exp_month = isset($this->opts->exp_month) ? $this->opts->exp_month : null;
        $opts->exp_year = isset($this->opts->exp_year) ? $this->opts->exp_year : null;
        $opts->cvv = isset($this->opts->cvv) ? $this->opts->cvv : null;

        $form = '';
        /* FIXME: hard coded options!!
          if ($config_object->accept_amex) $cards["AmExCard"] = "American Express";
        if ($config_object->accept_discover) $cards["DiscoverCard"] = "Discover";
        if ($config_object->accept_mastercard) $cards["MasterCard"] = "MasterCard";
        if ($config_object->accept_visa) $cards["VisaCard"] = "Visa";
        */
        //$fname = new textcontrol($opts->first_name);
        //$lname = new textcontrol($opts->last_name);
        
        $cardtypes = new dropdowncontrol($opts->cc_type,$this->cards);
        $cardnumber = new textcontrol($opts->cc_number,20,false,20,"integer", true);
        $expiration = new monthyearcontrol($opts->exp_month, $opts->exp_year);
        $cvv = new textcontrol($opts->cvv,4,false,4,"integer", true);
        $cvvhelp = new htmlcontrol("<a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>What's this?</a>");

        $cardtypes->id = "cc_type";
        $cardnumber->id = "cc_number";
        $expiration->id = "expiration";
        $cvv->id = "cvv";
        $cvv->size = 5;
        $cvvhelp->id = "cvvhelp";

        //$form .= $fname->toHTML("First Name", "first_name");
        //$form .= $lname->toHTML("Last Name", "last_name");
        $form .= $cardtypes->toHTML("Card Type", "cc_type");
        $form .= $cardnumber->toHTML("Card #", "cc_number");
        //$form .= "<strong class=\"example\">Example: 1234567890987654</strong>";
        $form .= $expiration->toHTML("Expiration", "expiration");
        $form .= $cvv->toHTML("CVV #", 'cvv');
        $form .= $cvvhelp->toHTML('', 'cvvhelp');
        
        return $form;    
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