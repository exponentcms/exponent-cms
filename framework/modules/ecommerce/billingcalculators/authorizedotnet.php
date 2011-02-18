<?php

define("ECOM_AUTHORIZENET_AUTH_CAPTURE",0);
define("ECOM_AUTHORIZENET_AUTH_ONLY",1);

class authorizedotnet extends creditcard {
	function name() { return "Authorize.net Payment Gateway"; }
	function description() {
	    return "Enabling this payment option will allow your customers to use their credit card to make purchases on your site.  It does require
	    an account with Authorize.net before you can use it to process credit cards.";
	}
	function hasConfig() { return true;}
	function hasUserForm() { return true;}
	function isOffsite() { return false; }
	function isSelectable() { return true; }

	function process($method, $opts) {
	    global $order, $db, $user;
	    // make sure we have some billing options saved.
		if (empty($method) || empty($opts)) return false;
		
		// get a shipping address to display in the invoice email.
		$shippingaddress = $order->getCurrentShippingMethod();
		$shipping_state = new geoRegion($shippingaddress->state);
		$shipping_country = new geoCountry($shipping_state->country_id);
		
		$config = unserialize($this->config);
		
		$state = new geoRegion($method->state);
		$country = new geoCountry($state->country_id);
		
		$data = array(
			"x_login"=>$config['username'],
			"x_version"=>'3.1',
			"x_tran_key"=>$config['transaction_key'],
			"x_test_request"=> $config['testmode'], 
			//"x_password"=>$config['password'],
			"x_delim_data"=>'TRUE',
			"x_delim_char"=>'|',
			"x_relay_response"=>'FALSE',
			"x_first_name"=>$method->firstname,
			"x_last_name"=>$method->lastname,
			"x_address"=>$method->address1,
			"x_city"=>$method->city,
			"x_state"=>$state->code,
			"x_zip"=>$method->zip,
			"x_country"=>$country->iso_code_2letter,
			//"x_phone"=>empty($method->phone) ? '' : $method->phone,
			"x_phone"=>'309-680-5600',
			"x_email"=>$user->email,
			"x_invoice_num"=>$order->getInvoiceNumber(),
			"x_ship_to_first_name"=>$shippingaddress->firstname,
			"x_ship_to_last_name"=>$shippingaddress->lastname,
			"x_ship_to_address"=>$shippingaddress->address1,
			"x_ship_to_city"=>$shippingaddress->city,
			"x_ship_to_state"=>$shipping_state->code,
			"x_ship_to_zip"=>$shippingaddress->zip,
			"x_ship_to_country"=>$shipping_country->iso_code_2letter,
			"x_amount"=>$order->grand_total,
			"x_description"=>"Secure Order from " . HOSTNAME,
			"x_method"=>'CC',
			"x_recurring_billing"=>'NO',
			"x_card_num"=>$opts->cc_number,
			"x_exp_date"=>$opts->exp_month.'/'.$opts->exp_year,
			"x_card_code"=>$opts->cvv,
		);
		
		if (!empty($user->email) && $config['email_customer']) {
			$data['x_email_customer'] = 'TRUE';
		} else {
			$data['x_email_customer'] = 'FALSE';
		}
		
		if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_CAPTURE) {
			$data['x_type'] = "AUTH_CAPTURE";
		} else if ($config['process_mode'] == ECOM_AUTHORIZENET_AUTH_ONLY) {
			$data['x_type'] = "AUTH_ONLY";
		}

		$data2 = "";
		while(list($key, $value) = each($data)) {
			$data2 .= $key . '=' . urlencode(ereg_replace(',', '', $value)) . '&';
		}
			
		// take the last & out for the string
		$data2 = substr($data2, 0, -1);

		$url = "https://secure.authorize.net/gateway/transact.dll";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  //Windows 2003 Compatibility 
		$authorize = curl_exec($ch);
		curl_close($ch);
		
		$response = split("\|", $authorize);	

		if ($response[0] == 1) { //Approved !!!
			$object->errorCode = 0;
			$object->message = $response[3] . " Approval Code: ".$response[4];
			$object->status = 'Approved';
            $object->AUTHCODE = $response[4];
            $object->AVSResponse = $response[5];
            $object->HASH = $response[37];            
	     $object->CVVResponse = $response[38];
            $object->PNREF = $response[6];
		} else {
			$object->errorCode = $response[2]; //Response reason code
			$object->message = $response[3];
		}

        $opts->result = $object;        
        $opts->cc_number = 'xxxx-xxxx-xxxx-'.substr($opts->cc_number, -4);
        $method->update(array('billing_options'=>serialize($opts)));
		return $object;
	}
	
	//Config Form
	function configForm() {
		$form = BASE.'framework/modules/ecommerce/billingcalculators/views/authorizedotnet/configure.tpl';	
		return $form;
	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('username', 'transaction_key', 'password', 'testmode', 'accepted_cards', 'email_customer', 'email_admin', 'notification_addy', 'process_mode');
	    foreach ($config_vars as $varname) {
	        $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	    }
	    
		return $config;
	}
	
	//This is called when a billing method is deleted. It can be used to clean up if you
	//have any custom user_data storage.
	function delete($config_object) {
		return;
	}
	
	//This should return html to display config settings on the view billing method page
	function view($config_object) {
		$html = "<br>Settings:<br/><hr>";
		$html .= "API Login ID: " . $config_object->username."<br>";
		$html .= "Transaction Key: ". $config_object->transaction_key."<br>";
		$html .= "Password: " . $config_object->password."<br>";
		$html .= "Test Mode: ".(($config_object->test_mode)?"Yes":"No")."<br>";
		$html .= "Process Mode: ";
		if ($config_object->process_mode == ECOM_AUTHORIZENET_AUTH_CAPTURE) {
			$html .="Authorize and Capture<br>";
		}else if ($config_object->process_mode == ECOM_AUTHORIZENET_AUTH_ONLY) {
			$html .="Authorize and Capture<br>";
		}
		$html .= "<br>Accepted Cards:<hr>";
		$html .= "American Express: ".(($config_object->accept_amex)?"Yes":"No")."<br>";
		$html .= "Discover: ".(($config_object->accept_discover)?"Yes":"No")."<br>";
		$html .= "Mastercard: ".(($config_object->accept_mastercard)?"Yes":"No")."<br>";
		$html .= "Visa: ".(($config_object->accept_visa)?"Yes":"No")."<br><br>";
		//$html .= "Offer Tax Exempt Field: ".(($config_object->offer_tax_exempt_field)?"Yes":"No")."<br>";

		return $html;
	}

    public function postProcess($order=null,$params=null) {
        $this->opts = null;
        return true;
    }
    
    function getPaymentAuthorizationNumber($billingmethod){
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->result->AUTHCODE;
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
        return $ret->result->status;
    }
    
    function getAVSAddressVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->AVSResponse;
        if (stristr($response, 'P') || stristr($response, 'S') || stristr($response, 'U')) return "N/A";
        elseif (stristr($response, 'A') || stristr($response, 'X') || stristr($response, 'Y')) return 'Y';
        else return 'X';
    }
                                
    function getAVSZipVerified($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->AVSResponse;
        if (stristr($response, 'P') || stristr($response, 'S') || stristr($response, 'U')) return "N/A";
        elseif (stristr($response, 'W') || stristr($response, 'X') || stristr($response, 'Y') || stristr($response, 'Z')) return 'Y';
        else return 'X';
    }
    
    function getCVVMatched($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        $response = $ret->result->CVVResponse;
        if (stristr($response, 'M')) return 'Y';
        else return 'X';
    }
 
    function getPaymentMethod($billingmethod) {
        $ret = expUnserialize($billingmethod->billing_options);
        return $ret->cc_type;
    }
}

?>


