<?php

//TODO: make into php5 class with access modifiers proprities and all that jazz.
class paypalExpressCheckout extends billingcalculator {
    /**
    * The name that will be displayes in the payment methods selector admin screen.
    * @return string Then name of the billing calculator
    */
	function name() { return "PayPal Express Checkout"; }
	public function captureEnabled() {return true; }
    public function voidEnabled() {return true; }
    public function creditEnabled() {return true; }
    
    /**
    * The description that will be displayed in the payment methods selector admin screen
    * @return string A short description
    */
    function description() {
	    return "Enabling this payment option will allow your customers to use their PayPal account to make purchases. I requires a Merchant Account with Paypal in order to get an API signature.";
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
    
    public $title = 'PayPal Express Checkout';
    public $payment_type = 'PayPal';
    
    /**
    * Called for billing method seletion screen, return true if it's a valid billing method.
    * 
    * TODO: not a clue what thi function is supposed to do. May not be used any more
    * 
    * @param mixed $config_object 
    * @param mixed $order
    * @param mixed $billaddress
    * @param mixed $shippingaddress
    * @return boolean
    */
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
    function preprocess($method, $opts, $params, $order)
    {
	
        global $db, $user;
        
        //eDebug($params);
        if(!isset($params['token'])) 
        {
        
            //eDebug($method);
            //eDebug($opts);
            // make sure we have some billing options saved.
            if (empty($method) /*|| empty($opts)*/) 
            {
                return false;
            }
            
            // get a shipping address to display in the invoice email.
            $shippingaddress = $order->getCurrentShippingMethod();
            $shipping_state = new geoRegion($shippingaddress->state);
            $shipping_country = new geoCountry($shipping_state->country_id);
            
            $state = new geoRegion($method->state);
            $country = new geoCountry($state->country_id);
            
            $config = unserialize($this->config);
            //eDebug($config, true);  
            if ($config['testmode']) 
            {
                /**
                * This is the URL that the buyer is first sent to to authorize payment with their paypal account change the URL depending if you are testing on the sandbox or going to the live PayPal site For the sandbox, the URL is https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token= For the live site, the URL is https://www.paypal.com/webscr&cmd=_express-checkout&token=
                * 
                * @var string
                */
                $paypal_url = 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=';
                //flash('message','This Transaction is in TEST MODE');   
            }
            else
            {
                $paypal_url = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=';
            }
            
            
            /**
            * After the user has completed things at Paypal they will be sent back to our site. This tells PayPal where to send them
            * 
            * @var string
            */
            $returnURL = makeLink(array('controller'=>'cart','action'=>'preprocess'));
            
            /**
            * If the user cancels the transaction at PayPal, they are sent back to our site. This tells PayPal where to send them
            * 
            * @var string
            */
            $cancelURL = makeLink(array('controller'=>'cart','action'=>'checkout'));;
            
            $shipname = $shippingaddress->firstname . ' ';
            $shipname .= empty($shippingaddress->middlename) ? $shippingaddress->lastname : $shippingaddress->middlename . ' ' . $shippingaddress->lastname;
            
            $shipstreet = $shippingaddress->address1;
            $shipstreet .= empty($shippingaddress->address2) ? '' : ', ' . $shippingaddress->address2;
            
            /**
            * An array of the data sent to PayPal. It will be transformend into Name=Value pairs later.
            * 
            * @var array
            */                                                                                       
            $data = array(
                // required parameters
                'METHOD'    => 'SetExpressCheckout',
                'USER'      => $config['username'],
                'PWD'       => $config['password'],
                'SIGNATURE' => $config['signature'],
                'VERSION'   => '59.0',
                'ReturnUrl' => $returnURL,
                'CANCELURL' => $cancelURL,
                // TODO: build data from odrer
                'AMT'       => number_format($order->grand_total, 2, '.', ''),
                'ADDROVERRIDE' => '1',
                'SHIPTONAME' => $shipname,
                'SHIPTOSTREET' => $shipstreet,
                'SHIPTOCITY' => $shippingaddress->city,
                'SHIPTOSTATE' => $shipping_state->code,
                'SHIPTOCOUNTRYCODE' => $shipping_country->iso_code_2letter,
                'SHIPTOZIP' => $shippingaddress->zip        
            );

           //eDebug($data, true);
           /* eDebug($shippingaddress);
            eDebug($shipping_state);
            eDebug($shipping_country, true); */
            
            $nvpResArray = $this->paypalApiCall($data);    

            if (!empty($nvpResArray['curl_error'])) 
            { 
                //curl error
                
                $object->errorCode = curl_errno($ch); //Response reason code
                $object->message = curl_error($ch);
                
                $opts->result = $object;
                $method->update(array('billing_options'=>serialize($opts)));
            }
            elseif ($nvpResArray['ACK'] == 'Error' || $nvpResArray['ACK'] == 'Failure' || $nvpResArray['ACK'] == 'FailureWithWarning' || $nvpResArray['ACK'] == 'Warning') 
            { 
                // paypal error
                $object->errorCode = "";
                $object->message = "The following errors occured: ";
                
                // its possible there are more than one error. 
                foreach ($nvpResArray as $k=>$v) {
                    if (is_array($v)) 
                    {
                        $object->errorCode .= $v['ERRORCODE'].", ";
                        $object->message .= $v['LONGMESSAGE'].", ";
                    }
                }
                // remove the trailing ", " (comma space)
                $object->errorCode = preg_replace("/,\s$/", "", $object->errorCode);
                $object->message = preg_replace("/,\s$/", ".", $object->message);
                
                $opts->result = $object;
                $method->update(array('billing_options'=>serialize($opts)));
            } 
            else 
            { 
                // Approved
                $object->errorCode = 0;
                $object->message = "SetExpressCheckout successfully returned token.";
                $object->token = $nvpResArray['TOKEN'];
                $object->correlationID = $nvpResArray['CORRELATIONID'];
                
                $opts->result = $object;
                $method->update(array('billing_options'=>serialize($opts)));
                
                // redirect
                redirect_to($paypal_url.$nvpResArray['TOKEN']);
            }   
        } else {
            //eDebug($params);
            //eDebug($method);
            $object = expUnserialize($method->billing_options);
            //eDebug($object,true);
            if ($object->result->token == $params['token']) {
                $object->result->errorCode = 0;
                $object->result->message = "User has approved the payment at PayPal";
                $object->result->PayerID = $params['PayerID'];                
                $method->update(array('billing_options'=>serialize($object)));                
                return $object; 
            }else{
                $object->result->errorCode = 1;
                $object->result->message = "PayPal Token Mismatch";
                $object->result->PayerID = $params['PayerID'];                
                $method->update(array('billing_options'=>serialize($object)));                 
                return $object;   
            }
        }        
        return $object;
    }
    
    
	function process($method, $opts, $params, $invoice_number) {
	    global $order, $db, $user; 
        $billing_options = expUnserialize($method->billing_options);
        $config = expUnserialize($this->config);
        
        //eDebug($order);
        $data = array(
            // required parameters
            'USER'      => $config['username'],
            'PWD'       => $config['password'],
            'SIGNATURE' => $config['signature'],
            'VERSION'   => '59.0',
            'METHOD'    => 'DoExpressCheckoutPayment',
            'SOLUTIONTYPE' => 'Sole',      //added per post
            'LANDINGPAGE' => 'Billing',    //added per post
            'TOKEN' => $billing_options->result->token, 
            'AMT'       => number_format($order->grand_total, 2, '.', ''),
            'PAYERID' => $billing_options->result->PayerID,
            'CURRENCYCODE' => 'USD',
            'INVNUM' => $invoice_number,
            'CUSTOM' => 'Invoice #' . $invoice_number,
            'PAYMENTACTION' => $config['process_mode'],
            'ITEMAMT' => number_format($order->total, 2, '.', ''),
            'SHIPPINGAMT' => number_format($order->shipping_total + $order->surcharge_total, 2, '.', ''),
            'TAXAMT' => number_format($order->tax, 2, '.', '')
        );
        
        $it = 0;
        $tt = 0;
        for ($n=0; $n<count($order->orderitem); $n++){
            $data['L_NAME' . $n] = strlen($order->orderitem[$n]->products_name) > 127 ? substr($order->orderitem[$n]->products_name,0,124) . "..." : $order->orderitem[$n]->products_name ;
            $data['L_NUMBER' . $n] = strlen($order->orderitem[$n]->product->model) > 127 ? substr($order->orderitem[$n]->product->model,0,124) . "..." : $order->orderitem[$n]->product->model;            
            $data['L_QTY' . $n] = $order->orderitem[$n]->quantity;
            $data['L_TAXAMT' . $n] = number_format(($order->orderitem[$n]->products_tax), 2, '.', '');
            $data['L_AMT' . $n] = number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '');
            //$it += number_format(($order->orderitem[$n]->products_tax), 2, '.', '') * $order->orderitem[$n]->quantity;
            //$tt += number_format(($order->orderitem[$n]->products_price_adjusted), 2, '.', '') * $order->orderitem[$n]->quantity;
        }
        
        //eDebug($it);
        //eDebug($tt);
        //eDebug($data);  
        //eDebug($billing_options, true);  
        
        $nvpResArray = $this->paypalApiCall($data);    
        //eDebug($nvpResArray);  
        
        if ($nvpResArray['ACK'] == 'Failure' || $nvpResArray['ACK'] == 'FailureWithWarning') 
        { 
            $billing_options->result->status = $nvpResArray['ACK'];
            $billing_options->result->errorCode = $nvpResArray[0]['ERRORCODE'];
            $billing_options->result->message = $nvpResArray[0]['SHORTMESSAGE'] . ":" . $nvpResArray[0]['LONGMESSAGE']; ; 
            $billing_options->result->correlationID = $nvpResArray['CORRELATIONID'];  
            $transaction_state = "Failure";      
            $trax_state = "error";                                                               
        }else{
            /*
            [TOKEN] => EC-7YW97132PA0236148 [TIMESTAMP] => 2010-01-16T21:49:15Z [CORRELATIONID] => 7f49bba2eac7e 
            [ACK] => Success [VERSION] => 59.0 [BUILD] => 1152253 [TRANSACTIONID] => 1AA09727DG247464P [TRANSACTIONTYPE] => cart 
            [PAYMENTTYPE] => instant [ORDERTIME] => 2010-01-16T21:49:14Z [AMT] => 118.09 [FEEAMT] => 3.72 [TAXAMT] => 6.75 
            [CURRENCYCODE] => USD [PAYMENTSTATUS] => Pending [PENDINGREASON] => paymentreview [REASONCODE] => None 
            [PROTECTIONELIGIBILITY] => Ineligible 
            */
            $billing_options->result->status = $nvpResArray['ACK'];
            $billing_options->result->errorCode = 0;
            if ($nvpResArray['ACK'] == 'SuccessWithWarning'){
                $billing_options->result->message = $nvpResArray['ACK'] . ":" . $nvpResArray[0]['SHORTMESSAGE'] . ":" . $nvpResArray[0]['LONGMESSAGE']; ;     
            }else{
                $billing_options->result->message = $nvpResArray['ACK'];     
            }
            $billing_options->result->correlationID = $nvpResArray['CORRELATIONID'];                                     
            $billing_options->result->paymenttype = $nvpResArray['PAYMENTTYPE'];                                     
            $billing_options->result->timestamp = $nvpResArray['TIMESTAMP'];                                     
            $billing_options->result->fee_amt = $nvpResArray['FEEAMT'];                                     
            $billing_options->result->payment_status = $nvpResArray['PAYMENTSTATUS'];                                     
            $billing_options->result->pending_reason = $nvpResArray['PENDINGREASON'];                                     
            $billing_options->result->reason_code = $nvpResArray['REASONCODE'];
			$billing_options->result->transactionID = $nvpResArray['TRANSACTIONID'];
			
            $transaction_state = $nvpResArray['PAYMENTSTATUS'];
            $trax_state = "complete";                                          
        }
        //eDebug($billing_options,true);                                                               
        $method->update(array('billing_options'=>serialize($billing_options), 'transaction_state'=>$transaction_state));
        $this->createBillingTransaction($method, number_format($order->grand_total, 2, '.', ''),$billing_options->result,$trax_state);
        return $billing_options->result;    
        
	}
    
    /**
    * Clean up after ourselves
    * @return boolean
    */
   
	/**
    * Point to the location of the config template.
    * @return string The location of the config.tpl
    * 
    * TODO: this is hard coded. why? needs to pick up this like a controller does
    */
	function configForm() {
		$form = BASE.'framework/modules/ecommerce/billingcalculators/views/paypalExpressCheckout/configure.tpl';	
		return $form;
	}
	
	/**
    * process config form
    * 
    * @param mixed $values
	 * @return array
	 */
	function parseConfig($values) {
	    $config_vars = array('username', 'password', 'signature', 'testmode', 'process_mode', 'email_customer', 'email_admin', 'notification_addy');
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
	
	/**
    * This should return html to display config settings on the view billing method page
    * @param mixed $config_object
    * 
    * TODO: Hasen't this been depricated in favor of the config.tpl?
	 * @return string
	 */
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
    
####
#### These are commented out in favor of the ones that have the credit cards.
#### This is meant only FOR TESTING
####
    
    /**
    * The html of the form that shows up in the "pop up" that the user enteres their info 
    * @return string|boolean
    */
//    function userForm() {
//        return false;
//    }
    
    /**
    * process user input. This function should return an object of the user input. the returnd object will be saved in the session and passed to post_process. If need be this could use another method of data storage, as long post_process can get the data.
    * @param mixed $params The params array that gets passed around all over Exponent
    */
//    function userFormUpdate($params) {
//        return false;
//    }
    
    /**
    * Should return html to display user data.
    * @param mixed $opts
    */
//    function userView($opts) {
//        $html = 'You will be redirected to Paypal to process the transaction. ';
//        return $html;
//    }
    
    
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
        $this->opts = exponent_sessions_get('billing_options');
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
    
    //process user input. This function should return an object of the user input.
    //the returnd object will be saved in the session and passed to post_process.
    //If need be this could use another method of data storage, as long post_process can get the data.
    function userFormUpdate($params) {
        /*//expValidator::validate(array('presence_of'=>'cc_number','presence_of'=>'cvv'), $params);
        $this->opts = null;
        //$this->opts->first_name = $params["first_name"];
        //$this->opts->last_name = $params["last_name"];
        $this->opts->cc_type = $params["cc_type"];
        $this->opts->cc_number = $params["cc_number"];
        $this->opts->exp_month = $params["expiration_month"];
        $this->opts->exp_year = $params["expiration_year"];
        $this->opts->cvv = $params["cvv"];    
        return $this->opts;  */
    }
    
    //Should return html to display user data.
    function userView($opts) {
        /*if (empty($opts)) return false;
        $html = '';
        $html .= '<table id="ccinfo" border=0 cellspacing=0 cellpadding=0><tbody>';
        $html .= '<tr class="odd"><td>Type of Credit Card: </td><td>'.$opts->cc_type.'</td></tr>';
        $html .= '<tr class="even"><td>Credit Card Number: </td><td>'.'xxxx-xxxx-xxxx-'.substr($opts->cc_number, -4). '</td></tr>';
        $html .= '<tr class="odd"><td>Expires on: </td><td>'.$opts->exp_month.'/'.$opts->exp_year.'</td></tr>';
        $html .= '<tr class="even"><td>CVV/Security Number: </td><td>'.$opts->cvv.'</td></tr>';
        $html .= '<tbody></table>';
        return $html;*/
        return ''; 
    }
    
    
    /**
    * A utility a call to Paypal's api CURL
    * 
    * @param array $apiParams an Associative array of the name-value pairs that will be sent as url params to the paypal api
    * @return array An associative array containing the PayPal response or a curl error.
    */
    function paypalApiCall($apiParams)
    {
        $config = unserialize($this->config);
        
        if ($config['testmode']) 
        {
            // Testing
            
            /**
            * this is the server URL which you have to connect for submitting your API request
            * 
            * @var string
            */
            $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            flash('message','This Transaction is in TEST MODE');   
        }
        else
        {
            // LIVE
            
            $api_endpoint = 'https://api-3t.paypal.com/nvp';
        }
        
        // convert the api params to a name value pair string
        $nvpstr = "";
        while(list($key, $value) = each($apiParams)) 
        {
            $nvpstr .= $key . '=' . urlencode(str_replace(',', '', $value)) . '&';
        }
            
        // take the last & out for the string
        $nvpstr = substr($nvpstr, 0, -1);

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$api_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        //setting the nvpstr as POST FIELD to curl
        curl_setopt($ch,CURLOPT_POSTFIELDS, $nvpstr);
        
        //getting response from server
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) 
        { 
            $ret =  array(
                'curl_errno' => curl_errno($ch),
                'curl_error' => curl_error($ch),
            );
        }
        else
        {
            $ret = $this->deformatNVP($response);
        }
        
        curl_close($ch);
        
        return $ret;
    }
    
    /**
    * A utility function that will take the Name-Value-Pair string returned by PayPal API and create a multi-deminsional array matching up related data where appropriate
    * Note that this is Paypal speciific as we need to do some funky things to match related data.
    * 
    * @param string $nvpstr The nvp string returned by paypal.
    * @return array
    */
    function deformatNVP($nvpstr) 
    {
        $intial=0;
        $nvpArray = array();
        
        
        while(strlen($nvpstr)) 
        {
            //postion of Key
            $keypos= strpos($nvpstr,'=');
            //position of value
            $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval=substr($nvpstr,$intial,$keypos);
            $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] =urldecode( $valval);
            $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
        }
        
        // now we'll group the related NVPs into their own arrays and make a multidimensional array out of the whole thing
        // Take note that the Key of the new array will be L_0, L_1... L_n The L_ forces the array to have a string index. 
        // If we let it have numeric indicies array_merge_recursive() wouldn't work as expected. Since "non-related data" is 
        // also in the multidimensional you'll be treating this thing as a associative array anyway so really this is easier 
        foreach ($nvpArray as $k=>$v) 
        {
            // check if it has a number at the end of the key
            if (preg_match('/[0-9]+$/', $k, $matches)) 
            {
                // rip off the "L_" from the beginning of the key and the matched number from the end
                // make a new array using "l_(matched number)" as the index.
                // merge the new array to the multidimensional array
                $multiArr = array_merge_recursive($multiArr, array("L_$matches[0]"=>array( preg_replace("/(L_)|$matches[0]/", "", $k)=>$v)));
            } 
            else 
            {
                // if the key doesn't have a number at the end we don't need to do anythin special to try to match up any related data as above. 
                // Simply stick it on the multidimensional array
                $multiArr[$k] = $v;
            }
            //print_r($multiArr);
        }
        
        // array_merge_recursive() needed a string index to work. now we don't actually want an array index of "L_0, L_1... L_n" we just wan the number
        foreach($multiArr as $k=>$v) 
        {
            if (preg_match('/L_/', $k, $matches)) 
            {
                $multiArr[preg_replace('/L_/', "", $k)] = $v;
                unset($multiArr[$k]);
            }
        }
        
        return $multiArr;
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
    
    function getAVSAddressVerified($billingmethod) {
        return 'X';
    }
    
    function getAVSZipVerified($billingmethod) {
        return 'X';
    }
    
    function getCVVMatched($billingmethod) {
        return 'X';
    }
    
    function getPaymentMethod($billingmethod) {
        return $this->title;
    }
    
    function showOptions()
    {
        return;
    }
	
	// credit transaction
    function credit_transaction($method, $amount) 
    {
		global $order, $db;
		// eDebug($method, true);
		$billing_options = unserialize($method->billing_options);
		$billing_transaction_options = unserialize($method->billingtransaction[0]->billing_options);
		$config = unserialize($this->config);
	
		// Set request-specific fields.
		$transactionID = urlencode($billing_options->result->transactionID);
		$refundType = urlencode('Full');						// or 'Partial'
		$memo = "Transaction Refunded";													// required if Partial.
		$currencyID = urlencode('USD');							// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

		// Add request-specific fields to the request string.
		$nvpStr = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID";

		if(isset($memo)) {
			$nvpStr .= "&NOTE=$memo";
		}

		if(strcasecmp($refundType, 'Partial') == 0) {
			if(!isset($amount)) {
				exit('Partial Refund Amount is not specified.');
			} else {
				$nvpStr = $nvpStr."&AMT=$amount";
			}

			if(!isset($memo)) {
				exit('Partial Refund Memo is not specified.');
			}
		}

		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = $this->PPHttpPost('RefundTransaction', $nvpStr);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			//update the billing method option
			$billing_options->result->payment_status = 'Refunded';
			unset($billing_options->result->pending_reason);
						
			//Create another billing transaction option
			$billing_transaction_options->result->payment_status = 'Refunded';
			unset($billing_transaction_options->result->pending_reason);
			$method->update(array('billing_options'=>serialize($billing_options), 'transaction_state'=>'refunded'));
			
			$billing_options->result->correlationID = urldecode($httpParsedResponseAr['CORRELATIONID']);
			$this->createBillingTransaction($method,urldecode($httpParsedResponseAr['NETREFUNDAMT']),$billing_options->result,'refunded');
			flash('message', 'Refund Completed Successfully.');
			redirect_to(array('controller'=>'order', 'action'=>'show', 'id'=>$method->orders_id));
		} else  {
			exit('RefundTransaction failed: ' . $httpParsedResponseAr["L_LONGMESSAGE0"]);
		}
    }
	
	/**
	 * Send HTTP POST Request
	 *
	 * @param	string	The API method name
	 * @param	string	The POST Message fields in &name=value pair format
	 * @return	array	Parsed HTTP Response body
	 */
	 function PPHttpPost($methodName_, $nvpStr_) {
		$environment = 'sandbox';
		$config = unserialize($this->config);
		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName = urlencode($config['username']);
		$API_Password = urlencode($config['password']);
		$API_Signature = urlencode($config['signature']);
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
		if("sandbox" === $environment || "beta-sandbox" === $environment) {
			$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
		}
		$version = urlencode('51.0');

		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		// Get response from the server.
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}

		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}

		return $httpParsedResponseAr;
	}

	
}

?>

