<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

/**
 * @subpackage Calculators
 * @package    Modules
 */

class creditcard extends billingcalculator {

    function name() {
        return gt('Credit Card');
    }

//    public $use_title = 'Credit Card';
    public $payment_type = 'Credit Card';

//    function hasConfig() {
//        return false;
//    }

    function hasUserForm() {
        return false;
    }

    function isSelectable() {
        return false;
    }

    public $cards = array(
        "AmExCard" => "American Express",
        "DiscoverCard" => "Discover",
        "MasterCard" => "MasterCard",
        "VisaCard" => "Visa"
    );
    public $card_images = array(
        "AmExCard"     => 'Amex.png',
        "DiscoverCard" => 'Discover.png',
        "MasterCard"   => 'Mastercard.png',
        "VisaCard"     => 'Visa.png'
    );

    function userForm($config_object = null, $user_data = null) {
        // make sure we have some billing options saved.
        //if (empty($this->opts)) return false;

        //exponent_javascript_toFoot('creditcard',"",null,'', URL_FULL.'framework/core/forms/js/AuthorizeNet.validate.js');
        //$opts->first_name = isset($this->opts->first_name) ? $this->opts->first_name : null;
        //$opts->last_name = isset($this->opts->last_name) ? $this->opts->last_name : null;
        $this->opts = expSession::get('billing_options');
        $opts = new stdClass();
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

        //$cvvhelp = new htmlcontrol("<a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>What's this?</a>");

        $form .= '<div class="' . $this->calculator_name . ' credit-cards control form-group"><label class="' . (bs3()?'control-label col-sm-2':'label') . '"></label>';
        if (bs3()) {
            $form .= '<div class="col-sm-10">';
        }
        foreach ($this->getAvailableCards() as $key=>$card) {
            $form .= '<img id="' . $key . '" src="'.PATH_RELATIVE . 'framework/modules/ecommerce/billingcalculators/icons/' . $this->card_images[$key] . '" title="' . gt('Click to select this card type') . '" />';
        }
        if (bs3()) {
            $form .= '</div>';
        }
        $form .= '</div>';

        $cardtypes = new dropdowncontrol("", $this->getAvailableCards());
        $cardtypes->id = "cc_type_" . $this->calculator_name;
        $cardtypes->horizontal = true;
        //$cvvhelp->id = "cvvhelp";
        //FIXME we need to display/obtain user information if we are doing a quickPay checkout???
        //$form .= $fname->toHTML("First Name", "first_name");
        //$form .= $lname->toHTML("Last Name", "last_name");
        $form .= $cardtypes->toHTML(gt("Card Type"), "cc_type_" . $this->calculator_name);

        $cardnumber = new textcontrol("", 20, false, 20, "integer", true);
        $cardnumber->id = "cc_number";
        $cardnumber->horizontal = true;
        $form .= $cardnumber->toHTML(gt("Card #"), "cc_number");

        //$form .= "<strong class=\"example\">Example: 1234567890987654</strong>";

        $expiration = new monthyearcontrol("", "");
        $expiration->id = "expiration";
        $expiration->horizontal = true;
        $form .= $expiration->toHTML(gt("Expiration"), "expiration");

        $cvv = new textcontrol("", 4, false, 4, "integer", true);
        $cvv->id = "cvv";
        $cvv->size = 5;
        $cvv->horizontal = true;
        $form .= $cvv->toHTML("CVV # <br /><a href='http://en.wikipedia.org/wiki/Card_Verification_Value' target='_blank'>" . gt('What\'s this?') . "</a>", 'cvv');
        //$form .= $cvvhelp->toHTML('', 'cvvhelp');
        //$form .= "<a class=\"exp-ecom-link-dis continue\" href=\"#\" id=\"checkoutnow\"><strong><em>Continue Checkout</em></strong></a>";
        //$form .= '<input id="cont-checkout" type="submit" value="Continue Checkout">';
        // click card image to select card type
        $src = "
            $('." .$this->calculator_name  . ".credit-cards img').click(function() {
                $('#cc_type_" . $this->calculator_name ."').val($(this).attr('id'));
            });
        ";
        expJavascript::pushToFoot(array(
            "unique"  => 'creditcard-' . $this->calculator_name,
            "jquery"=> 1,
            "content"=> $src,
        ));

        return $form;
    }

    public function getAvailableCards() {
        if (empty($this->config)) {
            return;
        }
        $config = unserialize($this->config);
        $availablecards = array();
        if (!empty($config['accepted_cards'])) foreach ($config['accepted_cards'] as $card) {
            $availablecards[$card] = $this->cards[$card];
        }

        return $availablecards;
    }

    //process user input. This function should return an object of the user input.
    //the returned object will be saved in the session and passed to post_process.
    //If need be this could use another method of data storage, as long post_process can get the data.
    function userFormUpdate($params) {
        //eDebug($params);        
        if (!$this->validate_card_number($params['cc_number']) || !$this->validate_card_type($params['cc_number'], $params['cc_type']))
            expValidator::failAndReturnToForm(gt("Either the card number you entered is not a") . " " . $this->cards[$params['cc_type']] . ", " . gt("or the credit card you entered is not a valid credit card number. Please select the proper credit card type and verify the number entered and try again.") . "<br/>" . gt("For your security, your previously entered credit card information has been cleared."));
        if (!$this->validate_card_expire($params['expiration_month'] . substr($params['expiration_year'], 2, 2))) expValidator::failAndReturnToForm(gt("Please enter a valid expiration data.") . "<br/>" . gt("For your security, your previously entered credit card information has been cleared."));
        if (!$this->validate_cvv($params['cvv'])) expValidator::failAndReturnToForm(gt("Please enter a valid CVV number.") . "<br/>" . gt("For your security, your previously entered credit card information has been cleared."));

        //eDebug(debug_backtrace(), true);
        //eDebug($params);
        //this is broke to fuck, as you can't validate more than one type of anything without overwriting it. duh. 
        //so calling twice instead....needs to be  fixed though TODO:
        //expValidator::validate(array('presence_of'=>'cc_number'), $params);
        //expValidator::validate(array('presence_of'=>'cvv'), $params); 
        $this->opts = new stdClass();
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
    function userView($billingmethod) {
        $opts = expUnserialize($billingmethod->billing_options);
        if (empty($opts)) return false;

        $billinginfo = '<table id="ccinfo"' . (bs3()?' class=" table"':'') . ' border=0 cellspacing=0 cellpadding=0>';
        $billinginfo .= '<thead><tr><th colspan="2">' . gt('Paying by') . ' ' . $this->name() . '</th></tr></thead>';
        $billinginfo .= '<tbody>';
        $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt('Type of Credit Card') . ': </td><td class="pmt-value">' . $this->cards[$opts->cc_type] . '</td></tr>';
        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt('Credit Card Number') . ': </td><td class="pmt-value">' . 'xxxx-xxxx-xxxx-' . substr($opts->cc_number, -4) . '</td></tr>';
        $billinginfo .= '<tr class="odd"><td class="pmt-label">' . gt('Expires on') . ': </td><td class="pmt-value">' . $opts->exp_month . '/' . $opts->exp_year . '</td></tr>';
//        $billinginfo .= '<tr class="even"><td class="pmt-label">' . gt('CVV/Security Number') . ': </td><td class="pmt-value">' . $opts->cvv . '</td></tr>';
        $billinginfo .= '</tbody>';
        $billinginfo .= '</table>';

        return $billinginfo;
    }

    /**
     * For paypal this will call out to the PP api and get a token then redirect to PP.
     * PP then redirects back the site with token in the url. We can pick up that token
     * from the url such that if we already have it we'll ccall another PP api to get the
     * details and make it match up to the order.
     *
     * @param mixed $billingmethod The billing method information for this user
     * @param mixed $opts
     * @param array $params The url prameters, as if sef was off.
     * @param       $order
     *
     * @return mixed An object indicating pass of failure.
     */
    function preprocess($billingmethod, $opts, $params, $order) {
        //just save the opts
        $billingmethod->update(array('billing_options' => serialize($opts)));
        //eDebug($billingmethod,true);
    }

    function validate_card_expire($mmyy) {
        if (!is_numeric($mmyy) || strlen($mmyy) != 4) {
            return false;
        }
        $mm = substr($mmyy, 0, 2);
        $yy = substr($mmyy, 2, 2);
        if ($mm < 1 || $mm > 12) {
            return false;
        }
        $year = date('Y');
        $yy = substr($year, 0, 2) . $yy; // eg 2007
        if (is_numeric($yy) && $yy >= $year && $yy <= ($year + 10)) {
        } else {
            return false;
        }
        if ($yy == $year && $mm < date('n')) {
            return false;
        }
        return true;
    }

    function validate_cvv($cvv) {
        //eDebug(strlen($cvv));
        //eDebug(strspn($cvv, '0123456789'), true);
        if (strlen($cvv) > 2 && strlen($cvv) < 5 && strspn($cvv, '0123456789') > 2 && strspn($cvv, '0123456789') < 5) {
            return true;
        }

        return false;
    }

    function validate_card_type($cc_num, $type) {
        if ($type == "AmExCard") {
            $pattern = "/^3[47][0-9]{13}$/"; //American Express
            if (preg_match($pattern, $cc_num)) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == "DiscoverCard") {
            $pattern = "/^6(?:011|5[0-9]{2})[0-9]{12}$/"; //Discover Card
            if (preg_match($pattern, $cc_num)) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == "MasterCard") {
            $pattern = "/^5[1-5][0-9]{14}$/"; //Mastercard
            if (preg_match($pattern, $cc_num)) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == "VisaCard") {
            $pattern = "/^4[0-9]{12}(?:[0-9]{3})?$/"; //Visa

            if (preg_match($pattern, $cc_num)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // luhn algorithm
    function validate_card_number($card_number) {
        $card_number = preg_replace('[^0-9]', '', $card_number);
        if ($card_number < 9) {
            return false;
        }
        $card_number = strrev($card_number);
        $total = 0;
        for ($i = 0, $iMax = strlen($card_number); $i < $iMax; $i++) {
            $current_number = substr($card_number, $i, 1);
            if ($i % 2 == 1) {
                $current_number *= 2;
            }
            if ($current_number > 9) {
                $first_number = $current_number % 10;
                $second_number = ($current_number - $first_number) / 10;
                $current_number = $first_number + $second_number;
            }
            $total += $current_number;
        }
        return ($total % 10 == 0);
    }

    function formatCreditCard($cc, $cc_type) {
        $cc = str_replace(array('-', ' '), '', $cc);
        $cc_length = strlen($cc);
        $newCreditCard = substr($cc, -4);

        for ($i = $cc_length - 5; $i >= 0; $i--) {

            if ((($i + 1) - $cc_length) % 4 == 0)
                $newCreditCard = '-' . $newCreditCard;

            $newCreditCard = $cc[$i] . $newCreditCard;
        }

        return $newCreditCard;
    }

}

?>