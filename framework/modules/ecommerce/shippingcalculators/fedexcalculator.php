<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * @package Framework
 */
/** @define "BASE" "../../../.." */

class fedexcalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return gt('FedEx Shipping'); }
	public function description() { return gt('Shipping calculator for dynamically calculating shipping rates using the FedEx API.'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }


    public $shippingmethods = array(
        "FIRST_OVERNIGHT"=>"FedEx Next Day Air - Delivery by 8:30AM",
        "PRIORITY_OVERNIGHT"=>"FedEx Next Day Air - Delivery by 10:30AM",
        "STANDARD_OVERNIGHT"=>"FedEx Standard Overnight - Delivery by 3PM",
        "FEDEX_2_DAY"=>"FedEx 2Day - Delivery by 4:30PM",
        "FEDEX_EXPRESS_SAVER"=>"FedEx 3Day Express Saver - Delivery by 4:30PM",
        "FEDEX_GROUND"=>"FedEx Ground - 1-5 Business Days"        
    );

    public function getRates($items)
    {
        global $order;
        require_once(BASE.'external/fedex-php/fedex-common.php');
        //require_once('fedex-common.php');

        //The WSDL is not included with the sample code.
        //Please include and reference in $path_to_wsdl variable.
        $path_to_wsdl = BASE.'external/fedex-php/RateService_v9.wsdl';

        ini_set("soap.wsdl_cache_enabled", "0");
         
        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

        $request['WebAuthenticationDetail'] = array('UserCredential' =>
                                                    array(  'Key' => $this->configdata['fedex_key'], 
                                                            'Password' => $this->configdata['fedex_password']
                                                            )
                                                    ); 
        $request['ClientDetail'] = array('AccountNumber' => $this->configdata['fedex_account_number'], 'MeterNumber' => $this->configdata['fedex_meter_number']);
        //$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***');
        $request['TransactionDetail'] = array('CustomerTransactionId' => md5("Probody " . date('c')));
        $request['Version'] = array('ServiceId' => 'crs', 'Major' => '9', 'Intermediate' => '0', 'Minor' => '0');
        $request['ReturnTransitAndCommit'] = true;
        $request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
        $request['RequestedShipment']['ShipTimestamp'] = date('c');
        // Service Type and Packaging Type are not passed in the request
        $this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address1'];
        if(!empty($this->configdata['shipfrom']['address2']))$this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address2'];
        if(!empty($this->configdata['shipfrom']['address3']))$this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address3'];
        
        unset($this->configdata['shipfrom']['address1']);
        unset($this->configdata['shipfrom']['address2']);
        unset($this->configdata['shipfrom']['address3']);
        unset($this->configdata['shipfrom']['state']);
        unset($this->configdata['shipfrom']['country']);
        
        //eDebug($this->configdata['shipfrom'],true);
        
        $request['RequestedShipment']['Shipper'] = array('Address'=>$this->configdata['shipfrom']);
                // get the current shippingmethod and format the address for ups
        $currentmethod = $order->getCurrentShippingMethod();        
        $request['RequestedShipment']['Recipient'] = array('Address'=>$this->formatAddress($currentmethod));
        $request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                                'Payor' => array('AccountNumber' =>$this->configdata['fedex_account_number'], // Replace 'XXX' with payor's account number
                                                                             'CountryCode' => $this->configdata['shipfrom']['CountryCode']));
        $request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
        $request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
        $request['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
        
        
        // set the standard box sizes.
        $box_width  = empty($this->configdata['default_width']) ? 0 : $this->configdata['default_width'];
        $box_height = empty($this->configdata['default_height']) ? 0 : $this->configdata['default_height'];
        $box_length = empty($this->configdata['default_length']) ? 0 : $this->configdata['default_length'];               
        $box_volume = $box_height * $box_width * $box_length;
        
        // set some starting/default values
        $weight = 0;
        $volume = 0;  
        $count = 0;
        $package_items = array();
        
        // loop each product in this shipment and create the packages        
        foreach ($items->orderitem as $item) {
            for($i=0; $i<$item->quantity; $i++) {
                if (empty($item->product->no_shipping) && $item->product->requiresShipping == true) {
                    $lbs = empty($item->product->weight) ? $this->configdata['default_max_weight'] : $item->product->weight;
                    $width = empty($item->product->width) ? $this->configdata['default_width'] : $item->product->width;
                    $height = empty($item->product->height) ? $this->configdata['default_height'] : $item->product->height;
                    $length = empty($item->product->length) ? $item->product->width : $item->product->length;
                    $length = empty($length) ? $this->configdata['default_length'] : $length;
                    
                    $package_items[$count]->volume = ($width * $length * $height);
                    $package_items[$count]->weight = $lbs;
                    $package_items[$count]->w = $width;
                    $package_items[$count]->h = $height;
                    $package_items[$count]->l = $length;
                    $package_items[$count]->name = $item->product->title;
                    $count += 1;    
                }
            }
        }       
                
        //eDebug($package_items);
        // sort the items by volume
        $package_items = expSorter::sort(array('array'=>$package_items,'sortby'=>'volume', 'order'=>'DESC'));
        
        // loop over all the items and try to put them into packages in a semi-intelligent manner
        // we have sorted the list of items from biggest to smallest.  Items with a volume larger than
        // our standard box will generate a package with the dimensions set to the size of the item.
        // otherwise we just keep stuffing items in the current package until we can't find anymore that will
        // fit.  Once that happens we close that package and start a new one...repeating until we are out of items
        $space_left = $box_volume;
        //eDebug($space_left);
        $total_weight = 0;
        $box_count = 0;
        $fedexItemArray = array();
        while(!empty($package_items)) {
            $no_more_room = false;
            $used = array();         
            foreach($package_items as $idx=>$pi) {
                /*if ($pi->volume > $box_volume) {
#                    echo $pi->name."is too big for standard box <br>";
#                    eDebug('created OVERSIZED package with weight of '.$pi->weight);
#                    eDebug('dimensions: height: '.$pi->h." width: ".$pi->w." length: ".$pi->l);
#                    echo "<hr>";
                    $weight = $pi->weight > 1 ? $pi->weight : 1;
                    $upsRate->package(array('description'=>'shipment','weight'=>$weight,'code'=>'02','length'=>$pi->l,'width'=>$pi->w,'height'=>$pi->h));
                    $used[] = $idx;
                    $no_more_room = false;
                } elseif($pi->volume <= $space_left) {*/
                if ($pi->volume >= $space_left)
                {
                    $no_more_room = true;
                    break;
                }                
                $space_left = $space_left - $pi->volume;
                $total_weight += $pi->weight;
#                    echo "Adding ".$pi->name."<br>";
#                    echo "Space left in current box: ".$space_left."<br>";                
                $used[] = $idx;
                //}                
            }

            // remove the used items from the array so they wont be there on the next go around.
            foreach ($used as $idx) {
                unset($package_items[$idx]);
            }            
            
            // if there is no more room left on the current package or we are out of items then
            // add the package to the shippment.
            if ($no_more_room || (empty($package_items) && $total_weight > 0)) {
                $box_count++;
                $total_weight = $total_weight > 1 ? $total_weight : 1;
#                eDebug('created standard sized package with weight of '.$total_weight);
#                echo "<hr>";
                //$upsRate->package(array('description'=>'shipment','weight'=>$total_weight,'code'=>'02','length'=>$box_length,'width'=>$box_width,'height'=>$box_height));
                $fedexItemArray[] = array(
                'Weight' => 
                        array(
                            'Value' => $total_weight,
                            'Units' => 'LB'),
                            'Dimensions' => 
                                    array(
                                        'Length' => $box_length,
                                        'Width' => $box_width,
                                        'Height' => $box_height,
                                        'Units' => 'IN'));
                $space_left = $box_volume;
                $total_weight = 0;
            }
        }
        
        //eDebug($fedexItemArray,true);
        //eDebug($box_count . " boxes in this shipment.");
        
        $request['RequestedShipment']['PackageCount'] = "$box_count";        
        $request['RequestedShipment']['RequestedPackageLineItems'] = $fedexItemArray;
        
        //eDebug($request['RequestedShipment']['PackageCount']);
        //eDebug($request['RequestedShipment']['RequestedPackageLineItems'],true);
        
        /*array(
        '0' => array(
                'Weight' => 
                        array(
                            'Value' => 2.0,
                            'Units' => 'LB'),
                            'Dimensions' => 
                                    array(
                                        'Length' => 10,
                                        'Width' => 10,
                                        'Height' => 3,
                                        'Units' => 'IN')),
        '1' => array(
                'Weight' => 
                        array(
                            'Value' => 5.0,
                            'Units' => 'LB'),
                            'Dimensions' => 
                                    array(
                                        'Length' => 20,
                                        'Width' => 20,
                                        'Height' => 10,
                                        'Units' => 'IN')
                                        )
                                    );
                                                                                                
                                                                                                                              */                                                                  
        
        try 
        {
            if(setEndpoint('changeEndpoint'))
            {
                $newLocation = $client->__setLocation(setEndpoint('endpoint'));
            }
            
            $response = $client ->getRates($request);
            //eDebug($response,true);    
            if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR')
            {
                //echo 'Rates for following service type(s) were returned.'. Newline. Newline;
                //echo '<table border="1">';
                //echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td>';
                
                $rates = array();
                //array(
                //"03"=>array("id"=>"03", "title"=>"UPS Ground", "cost"=>5.00),
                //"02"=>array("id"=>"02", "title"=>"UPS Second Day Air", "cost"=>10.00),
                //"01"=>array("id"=>"01", "title"=>"UPS Next Day Air", "cost"=>20.00) 
             //);
             /*eDebug($this->configdata['shipping_methods'],true);
             eDebug($response -> RateReplyDetails,true);*/
                foreach ($response -> RateReplyDetails as $rateReply)
                {   
                    if(in_array($rateReply -> ServiceType,$this->configdata['shipping_methods']))                                   {
                        $rates[$rateReply -> ServiceType] = array("id"=>$rateReply -> ServiceType,
                                                                  "title"=>$this->shippingmethods[$rateReply -> ServiceType],
                                                                  "cost"=>number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",")
                                                                  );
                    }
                    
                   /* $amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
                    if(array_key_exists('DeliveryTimestamp',$rateReply)){
                        $deliveryDate= '<td>' . $rateReply->DeliveryTimestamp . '</td>';
                    }else{
                        $deliveryDate= '<td>' . $rateReply->TransitTime . '</td>';
                    }
                    echo $serviceType . $amount. $deliveryDate;
                    echo '</tr>';*/
                }
                //eDebug($rates,true);
                return array_reverse($rates); 
            }
            else
            {
                printError($client, $response); 
            } 
            
            writeToLog($client);    // Write to log file   

        } catch (SoapFault $exception) {
           printFault($exception, $client);        
        }   
    }
    
    public function oldgetRates($items) {
        global $order;
        
        // Require the main ups class and upsRate
        include_once(BASE.'external/ups-php/classes/class.ups.php');
        include_once(BASE.'external/ups-php/classes/class.upsRate.php');
        
        $upsConnect = new ups($this->configdata['accessnumber'],$this->configdata['username'],$this->configdata['password']);
	    $upsConnect->setTemplatePath(BASE.'external/ups-php/xml/');
	    
	    $upsConnect->setTestingMode($this->configdata['testmode']); // Change this to 0 for production

	    $upsRate = new upsRate($upsConnect);
	    $upsRate->request(array('Shop' => true));

        // set the address we will be shipping from.  this should be in the config data
	    $upsRate->shipper($this->configdata['shipfrom']);

        // get the current shippingmethod and format the address for ups
        $currentmethod = $order->getCurrentShippingMethod();
	    $upsRate->shipTo($this->formatAddress($currentmethod));  
        
        // set the standard box sizes.
        $box_width  = empty($this->configdata['default_width']) ? 0 : $this->configdata['default_width'];
        $box_height = empty($this->configdata['default_height']) ? 0 : $this->configdata['default_height'];
        $box_length = empty($this->configdata['default_length']) ? 0 : $this->configdata['default_length'];               
        $box_volume = $box_height * $box_width * $box_length;
        
        // set some starting/default values
        $weight = 0;
        $volume = 0;  
        $count = 0;
        $package_items = array();
        
        // loop each product in this shipment and create the packages
        $has_giftcard = false;
        foreach ($items->orderitem as $item) {
            for($i=0; $i<$item->quantity; $i++) {
                if (empty($item->product->no_shipping) && $item->product->requiresShipping == true) {
                    if ($item->product_type != 'giftcard') {
                        $lbs = empty($item->product->weight) ? $this->configdata['default_max_weight'] : $item->product->weight;
                        $width = empty($item->product->width) ? $this->configdata['default_width'] : $item->product->width;
                        $height = empty($item->product->height) ? $this->configdata['default_height'] : $item->product->height;
                        $length = empty($item->product->length) ? $this->configdata['default_length'] : $item->product->length;
                        
                        $package_items[$count]->volume = ($width * $length * $height);
                        $package_items[$count]->weight = $lbs;
                        $package_items[$count]->w = $width;
                        $package_items[$count]->h = $height;
                        $package_items[$count]->l = $length;
                        $package_items[$count]->name = $item->product->title;
                        $count += 1;	    
                    } else {
                        $has_giftcard = true;
                    }
                }
            }
        }
        
        // kludge for the giftcard shipping
        if (count($package_items) == 0 && $has_giftcard) {
            $rates = array(
                "03"=>array("id"=>"03", "title"=>"UPS Ground", "cost"=>5.00),
                "02"=>array("id"=>"02", "title"=>"UPS Second Day Air", "cost"=>10.00),
                "01"=>array("id"=>"01", "title"=>"UPS Next Day Air", "cost"=>20.00) 
             );
             
             return $rates; 
        }
        
        // sort the items by volume
        $package_items = expSorter::sort(array('array'=>$package_items,'sortby'=>'volume', 'order'=>'DESC'));
        
        // loop over all the items and try to put them into packages in a semi-intelligent manner
        // we have sorted the list of items from biggest to smallest.  Items with a volume larger than
        // our standard box will generate a package with the dimensions set to the size of the item.
        // otherwise we just keep stuffing items in the current package until we can't find anymore that will
        // fit.  Once that happens we close that package and start a new one...repeating until we are out of items
        $space_left = $box_volume;
        $total_weight = 0;
        while(!empty($package_items)) {
            $no_more_room = true;
            $used = array();         
            foreach($package_items as $idx=>$pi) {
                if ($pi->volume > $box_volume) {
#                    echo $pi->name."is too big for standard box <br>";
#                    eDebug('created OVERSIZED package with weight of '.$pi->weight);
#                    eDebug('dimensions: height: '.$pi->h." width: ".$pi->w." length: ".$pi->l);
#                    echo "<hr>";
                    $weight = $pi->weight > 1 ? $pi->weight : 1;
                    $upsRate->package(array('description'=>'shipment','weight'=>$weight,'code'=>'02','length'=>$pi->l,'width'=>$pi->w,'height'=>$pi->h));
                    $used[] = $idx;
                    $no_more_room = false;
                } elseif($pi->volume <= $space_left) {
                    $space_left = $space_left - $pi->volume;
                    $total_weight += $pi->weight;
#                    echo "Adding ".$pi->name."<br>";
#                    echo "Space left in current box: ".$space_left."<br>";
                    $no_more_room = false;
                    $used[] = $idx;
                }                
            }

            // remove the used items from the array so they wont be there on the next go around.
            foreach ($used as $idx) {
                unset($package_items[$idx]);
            }            
            
            // if there is no more room left on the current package or we are out of items then
            // add the package to the shippment.
            if ($no_more_room || (empty($package_items) && $total_weight > 0)) {
                $total_weight = $total_weight > 1 ? $total_weight : 1;
#                eDebug('created standard sized package with weight of '.$total_weight);
#                echo "<hr>";
                $upsRate->package(array('description'=>'shipment','weight'=>$total_weight,'code'=>'02','length'=>$box_length,'width'=>$box_width,'height'=>$box_height));
                $space_left = $box_volume;
                $total_weight = 0;
            }
        }
            
	    $upsRate->shipment(array('description' => 'my description','serviceType' => '03'));

	    $rateFromUPS = $upsRate->sendRateRequest();
	    
	    $handling = empty($has_giftcard) ? 0 : 5;
	    if ($rateFromUPS['RatingServiceSelectionResponse']['Response']['ResponseStatusCode']['VALUE'] == 1) {
	        $rates = array();
	        $available_methods = $this->availableMethods();
	        foreach ($rateFromUPS['RatingServiceSelectionResponse']['RatedShipment'] as $rate) {
	            if (array_key_exists($rate['Service']['Code']['VALUE'], $available_methods)) {
	                $rates[$rate['Service']['Code']['VALUE']] = $rate['TotalCharges']['MonetaryValue']['VALUE'];
	                $rates[$rate['Service']['Code']['VALUE']] = array('id' => $rate['Service']['Code']['VALUE'],
				                          'title' => $this->shippingmethods[$rate['Service']['Code']['VALUE']],
				                          'cost' => $rate['TotalCharges']['MonetaryValue']['VALUE'] + $handling);
	            }
	        }
	        return $rates;
	    } else {
	        return $rateFromUPS['RatingServiceSelectionResponse']['Response']['Error']['ErrorDescription']['VALUE'];
	    }
    }	
    
   	public function configForm() { 
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/fedexcalculator/configure.tpl';
   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('fedex_account_number', 'fedex_meter_number', 'fedex_key', 'fedex_password', 'shipping_methods', 'shipfrom', 'default_width', 'default_length', 'default_height', 'default_max_weight', 'testmode');
	    foreach ($config_vars as $varname) {
	        $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
	        if ($varname == 'shipfrom') {
	            $config[$varname]['state'] = geoRegion::getAbbrev($values[$varname]['region']);
	            $config[$varname]['country'] = geoRegion::getCountryCode($values[$varname]['region']);
	        }
	    }
	    
		return $config;
	}
	
	function availableMethods() {
	    if (empty($this->configdata['shipping_methods'])) return array();
	    $available_methods = array();
	    foreach ($this->configdata['shipping_methods'] as $method) {
	        $available_methods[$method] = $this->shippingmethods[$method];
	    }
	    
	    return $available_methods;
	}
	
	function formatAddress($params) {
	    $addy['Streetlines'][] = $params->address1;
        if(isset($params->address2)) $addy['Streetlines'][] = $params->address2;
	    $addy['City'] = isset($params->city) ? $params->city : '';
	    $addy['StateOrProvinceCode'] = isset($params->state) ? geoRegion::getAbbrev($params->state) : '';
	    $addy['CountryCode'] = isset($params->state) ? geoRegion::getCountryCode($params->state) : '';
	    $addy['PostalCode'] = isset($params->zip) ? $params->zip : '';	    
	    return $addy;
	}
	
	public static function sortByVolume($a, $b) {
	    eDebug($a);
	    return ($a->volume > $b->volume ? -1 : 1);
	}
}

?>
