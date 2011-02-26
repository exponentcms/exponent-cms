<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
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

class creditcard extends billingcalculator {
	function name() { return 'Credit Card'; }
	function hasConfig() { return false;}
	function hasUserForm() { return false;}
	function isOffsite() { return false; }
	function isSelectable() { return false; }
	
	public $title = 'Credit Card';
	public $payment_type = 'Credit Card';
	
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
		
		/*
		$cardtypes = new dropdowncontrol($opts->cc_type,$this->getAvailableCards());
		$cardnumber = new textcontrol($opts->cc_number,20,false,20,"integer", true);
		$expiration = new monthyearcontrol($opts->exp_month, $opts->exp_year);
		$cvv = new textcontrol($opts->cvv,4,false,4,"integer", true);
		$cvvhelp = new htmlcontrol("<a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>What's this?</a>");
		*/

		$cardtypes = new dropdowncontrol("",$this->getAvailableCards());
		$cardnumber = new textcontrol("",20,false,20,"integer", true);
		$expiration = new monthyearcontrol("", "");
		$cvv = new textcontrol("",4,false,4,"integer", true);
		//$cvvhelp = new htmlcontrol("<a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>What's this?</a>");

		$cardtypes->id = "cc_type";
		$cardnumber->id = "cc_number";
		$expiration->id = "expiration";
		$cvv->id = "cvv";
		$cvv->size = 5;
		//$cvvhelp->id = "cvvhelp";

		//$form .= $fname->toHTML("First Name", "first_name");
		//$form .= $lname->toHTML("Last Name", "last_name");
		$form .= $cardtypes->toHTML("Card Type", "cc_type");
		$form .= $cardnumber->toHTML("Card #", "cc_number");
		//$form .= "<strong class=\"example\">Example: 1234567890987654</strong>";
		$form .= $expiration->toHTML("Expiration", "expiration");
		$form .= $cvv->toHTML("CVV # <br /><a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>What's this?</a>", 'cvv');
		//$form .= $cvvhelp->toHTML('', 'cvvhelp');
		//$form .= "<a class=\"exp-ecom-link-dis continue\" href=\"#\" id=\"checkoutnow\"><strong><em>Continue Checkout</em></strong></a>";
        //$form .= '<input id="cont-checkout" type="submit" value="Continue Checkout">';
		return $form;	
	}
	
	public function getAvailableCards() {
        if (empty($this->config)) {
            return;
        }
	    $configdata = unserialize($this->config);
	    $avaiablecards = array();
	    foreach($configdata['accepted_cards'] as $card) {
	        $availablecards[$card] = $this->cards[$card];
	    }
	    
	    return $availablecards;
	}
	
	//process user input. This function should return an object of the user input.
	//the returnd object will be saved in the session and passed to post_process.
	//If need be this could use another method of data storage, as long post_process can get the data.
	function userFormUpdate($params) {
        //eDebug($params);
        if (!$this->validate_card_number($params['cc_number'])) expValidator::failAndReturnToForm("Please enter a valid credit card number.");
        if (!$this->validate_card_expire($params['expiration_month'] . substr($params['expiration_year'],2,2))) expValidator::failAndReturnToForm("Please enter a valid expiration data.");
        if (!$this->validate_cvv($params['cvv'])) expValidator::failAndReturnToForm("Please enter a valid CVV number.");
            
        //eDebug(debug_backtrace(), true);
        //eDebug($params);
        //this is broke to fuck, as you can't validate more than one type of anything without overwriting it. duh. 
        //so calling twice instead....needs to be  fixed though TODO:
		//expValidator::validate(array('presence_of'=>'cc_number'), $params);
        //expValidator::validate(array('presence_of'=>'cvv'), $params); 
		$this->opts = null;
        //$this->opts->first_name = $params["first_name"];
        //$this->opts->last_name = $params["last_name"];
        $this->opts->cc_type = $params["cc_type"];
        $this->opts->cc_number = $params["cc_number"];
        $this->opts->exp_month = $params["expiration_month"];
        $this->opts->exp_year = $params["expiration_year"];
        $this->opts->cvv = $params["cvv"];	
		return $this->opts;
	}
	
	//Should return html to display user data.
	function userView($opts) {
	    if (empty($opts)) return false;
		$html = '';
		$html .= '<table id="ccinfo" border=0 cellspacing=0 cellpadding=0 class="collapse nowrap"><tbody>';
		$html .= '<tr class="odd"><td class="right">Type of Credit Card: </td><td>'.$opts->cc_type.'</td></tr>';
		$html .= '<tr class="even"><td class="right">Credit Card Number: </td><td>'.'xxxx-xxxx-xxxx-'.substr($opts->cc_number, -4). '</td></tr>';
		$html .= '<tr class="odd"><td class="right">Expires on: </td><td>'.$opts->exp_month.'/'.$opts->exp_year.'</td></tr>';
		$html .= '<tr class="even"><td class="right">CVV/Security Number: </td><td>'.$opts->cvv.'</td></tr>';
		$html .= '<tbody></table>';
		return $html;
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
    function preprocess($method, $opts, $params)
    {
         //just save the opts        
        $method->update(array('billing_options'=>serialize($opts)));
        //eDebug($method,true);
    }
	
    function validate_card_expire($mmyy) 
    {
        if (!is_numeric($mmyy) || strlen($mmyy) != 4) 
        {
            return false;
        }
        $mm = substr($mmyy, 0, 2);
        $yy = substr($mmyy, 2, 2);        
        if ($mm < 1 || $mm > 12) 
        {
            return false;
        }
        $year = date('Y');
        $yy = substr($year, 0, 2) . $yy; // eg 2007
        if (is_numeric($yy) && $yy >= $year && $yy <= ($year + 10)) 
        {
        } 
        else
        {
            return false;
        }
        if ($yy == $year && $mm < date('n')) 
        {
            return false;
        }      
        return true;
    }
    
    function validate_cvv($cvv)
    {
        //eDebug(strlen($cvv));
        //eDebug(strspn($cvv, '0123456789'), true);
        if (strlen($cvv) > 2 && strlen($cvv) < 5  && strspn($cvv, '0123456789') > 2  && strspn($cvv, '0123456789') < 5 ) {
            return true;
        }

        return false;

    }
    // luhn algorithm
    function validate_card_number($card_number) 
    {
        $card_number = ereg_replace('[^0-9]', '', $card_number);      
        if ($card_number < 9)
        {
            return false;
        }
        $card_number = strrev($card_number);
        $total = 0;
        for ($i = 0; $i < strlen($card_number); $i++) 
        {
            $current_number = substr($card_number, $i, 1);
            if ($i % 2 == 1) 
            {
                $current_number *= 2;
            }
            if ($current_number > 9) 
            {
                $first_number = $current_number % 10;
                $second_number = ($current_number - $first_number) / 10;
                $current_number = $first_number + $second_number;
            }
            $total += $current_number;
        }
        return ($total % 10 == 0);
    }
}

?>

