<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class upscalculator extends shippingcalculator {
	/*
	 * Returns the name of the shipping calculator, for use in the Shipping Administration Module
	 */
	//overridden methods:
	public function name() { return exponent_lang_getText('UPS Shipping'); }
	public function description() { return exponent_lang_getText('Shipping calculator for dynamically calculating shipping rates using the UPS XML Rate API.'); }
	public function hasUserForm() { return true; }
	public function hasConfig() { return true; }
	public function addressRequired() { return true; }
	public function isSelectable() { return true; }

    public $shippingmethods = array(
        "01"=>"UPS Next Day Air",
        "02"=>"UPS Second Day Air",
        "03"=>"UPS Ground",
        "07"=>"UPS Worldwide Express",
        "08"=>"UPS Worldwide Expedited",
        "11"=>"UPS Standard",
        "12"=>"UPS Three-Day Select",
        "13"=>"Next Day Air Saver",
        "14"=>"UPS Next Day Air Early AM",
        "54"=>"UPS Worldwide Express Plus",
        "59"=>"UPS Second Day Air AM",
        "65"=>"UPS Saver",
    );

    public function getRates($items) {
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
   	    return BASE.'framework/modules/ecommerce/shippingcalculators/views/upscalculator/configure.tpl';
   	}
	
	//process config form
	function parseConfig($values) {
	    $config_vars = array('username', 'accessnumber', 'password', 'shipping_methods', 'shipfrom', 'default_width', 'default_length', 'default_height', 'default_max_weight', 'testmode');
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
	    $addy['companyName'] = isset($params->companyName) ? $params->companyName : '';
	    $addy['attentionName'] = isset($params->firstname) ? $params->firstname : '';
	    $addy['attentionName'] .= isset($params->lastname) ? $params->lastname : '';
	    $addy['address1'] = isset($params->address1) ? $params->address1 : '';
	    $addy['address2'] = isset($params->address2) ? $params->address2 : '';
	    $addy['address3'] = isset($params->address3) ? $params->address3 : '';
	    $addy['city'] = isset($params->city) ? $params->city : '';
	    $addy['state'] = isset($params->state) ? geoRegion::getAbbrev($params->state) : '';
	    $addy['countryCode'] = isset($params->state) ? geoRegion::getCountryCode($params->state) : '';
	    $addy['postalCode'] = isset($params->zip) ? $params->zip : '';
	    $addy['phone'] = isset($params->phone) ? $params->phone : '';
	    return $addy;
	}
	
	public static function sortByVolume($a, $b) {
	    eDebug($a);
	    return ($a->volume > $b->volume ? -1 : 1);
	}
}

?>
