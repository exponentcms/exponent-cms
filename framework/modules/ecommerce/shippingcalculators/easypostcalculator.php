<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
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
 * @package Modules
 */

/** @define "BASE" "../../../.." */
class easypostcalculator extends shippingcalculator
{
    /*
     * Returns the name of the shipping calculator, for use in the Shipping Administration Module
     */
    //overridden methods:
    public function name()
    {
        return gt('easypost');
    }

    public function description()
    {
        return gt(
            'Shipping calculator for dynamically calculating shipping rates and handles order fulfillment from multiple carriers'
        );
    }

    public $multiple_carriers = true;
    public $shippingcarriers = array(
        'USPS',
        'UPS',
        'FedEx'
    );
    public $shippingmethods = array(
        'USPS' => array(
            'First' => 'First Class',
            'Priority' => 'Priority Mail',
            'Express' => 'Express Mail',
            'ParcelSelect' => 'Parcel Select',
            'LibraryMail' => 'Library Mail',
            'MediaMail' => 'Media Mail',
            'CriticalMail' => 'Critical Mail',
            'FirstClassMailInternational' => 'First Class Mail International',
            'FirstClassPackageInternationalService' => 'First Class Package International Service',
            'PriorityMailInternational' => 'Priority Mail International',
            'ExpressMailInternational' => 'Express Mail International',
        ),
        'UPS' => array(
            'Ground' => "Ground",
            'UPSStandard' => "Standard",
            'UPSSaver' => "Saver",
            'Express' => "Worldwide Express",
            'ExpressPlus' => "Worldwide Express Plus",
            'Expedited' => "Worldwide Expedited",
            'NextDayAir' => "Next Day Air",
            'NextDayAirSaver' => "Next Day Air Saver",
            'NextDayAirEarlyAM' => "Next Day Air Early AM",
            '2ndDayAir' => "Second Day Air",
            '2ndDayAirAM' => "Second Day Air AM",
            '3DaySelect' => "Three-Day Select",
        ),
        'FedEx' => array(
            "FIRST_OVERNIGHT" => "Next Day Air - Delivery by 8:30AM",
            "PRIORITY_OVERNIGHT" => "Next Day Air - Delivery by 10:30AM",
            "STANDARD_OVERNIGHT" => "Standard Overnight - Delivery by 3PM",
            "FEDEX_2_DAY_AM" => "2-Day - Delivery by 10:30AM",
            "FEDEX_2_DAY" => "2-Day - Delivery by 4:30PM",
            "FEDEX_EXPRESS_SAVER" => "3-Day Express Saver - Delivery by 4:30PM",
            "FEDEX_GROUND" => "Ground - 1-5 Business Days",
            'INTERNATIONAL_ECONOMY' => 'International Economy',
            'INTERNATIONAL_FIRST' => 'International - Delivery by 8:30AM',
            'INTERNATIONAL_PRIORITY' => 'International Priority',
            'GROUND_HOME_DELIVERY' => 'Ground Home Deliver',
            'SMART_POST' => 'Smart Post',
        ),
    );

    public function __construct($params = null, $get_assoc = true, $get_attached = true)
    {
        parent::__construct($params, $get_assoc, $get_attached);

        $this->icon = array();
        foreach ($this->shippingcarriers as $carrier) {
            if (file_exists(
                BASE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $carrier . '-logo-ca.png'
            )) {
                $this->icon[$carrier] = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/' . $carrier . '-logo-ca.png';
            } else {
                $this->icon[$carrier] = PATH_RELATIVE . 'framework/modules/ecommerce/shippingcalculators/icons/default.png';
            }
        }
    }

    public function getRates($order)
    {   // Require the main class
        include_once(BASE . 'external/easypost-php-2.1.0/lib/easypost.php');

        if ($this->configdata['testmode']) {
            $apikey = $this->configdata['testkey'];
        } else {
            $apikey = $this->configdata['apikey'];
        }
        try {
            \EasyPost\EasyPost::setApiKey($apikey);
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }

        // set the address we will be shipping from.  this should be in the config data
        if (is_numeric($this->configdata['shipfrom']['state'])) {
            $this->configdata['shipfrom']['state'] = geoRegion::getAbbrev($this->configdata['shipfrom']['state']);
        }
        if (is_numeric($this->configdata['shipfrom']['country'])) {
            $this->configdata['shipfrom']['country'] = geoRegion::getCountryCode(
                $this->configdata['shipfrom']['country']
            );
        }
        try {
            $from_address = \EasyPost\Address::create($this->configdata['shipfrom']);
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }

        // get the current shippingmethod and format the address for easypost
        $currentmethod = $order->getCurrentShippingMethod();  // pulls in the 'to' address
        try {
            $to_address = \EasyPost\Address::create($this->formatAddress($currentmethod));
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }

        // set the standard box sizes.
        $box_width = empty($this->configdata['default_width']) ? 0 : $this->configdata['default_width'];
        $box_height = empty($this->configdata['default_height']) ? 0 : $this->configdata['default_height'];
        $box_length = empty($this->configdata['default_length']) ? 0 : $this->configdata['default_length'];
        $box_volume = $box_height * $box_width * $box_length;

        // set some starting/default values
//        $weight = 0;
        $count = 0;
        $package_items = array();

        // loop each product in this shipment and create the packages
        foreach ($order->orderitem as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                if (empty($item->product->no_shipping) && $item->product->requiresShipping == true) {
                    // calculate option weight
                    $item_weight = $item->getWeight();
                    $lbs = empty($item_weight) ? $this->configdata['default_max_weight'] : $item_weight;
                    $width = empty($item->product->width) ? $this->configdata['default_width'] : $item->product->width;
                    $height = empty($item->product->height) ? $this->configdata['default_height'] : $item->product->height;
                    $length = empty($item->product->length) ? $this->configdata['default_length'] : $item->product->length;

                    $package_items[$count] = new stdClass();
                    $package_items[$count]->volume = ($width * $length * $height);
                    $package_items[$count]->weight = $lbs;
                    $package_items[$count]->w = $width;
                    $package_items[$count]->h = $height;
                    $package_items[$count]->l = $length;
                    $package_items[$count]->name = $item->product->title;
                    $count++;
                }
            }
        }

        if (empty($package_items)) {
            return array();
        }  // why proceed with zero packages?

        // sort the items by volume
        $package_items = expSorter::sort(array('array' => $package_items, 'sortby' => 'volume', 'order' => 'DESC'));

        //FIXME for now just doing a single package
//        $total_weight = 0;
//        foreach ($package_items as $idx => $pi) {
//            $total_weight += $pi->weight;
//        }
//        try {
//        $parcel = \EasyPost\Parcel::create(
//            array(
//                'weight' => $total_weight,
//                'length' => $length,
//                'width' => $width,
//                'height' => $height,
//                'predefined_package' => null,
//            )
//        );
//        } catch (Exception $e) {
//            $msg =  $e->prettyPrint(false);
//            flash('error', 'easypost: <br>' . $msg);
//            return $msg;
//        }
        //FIXME end single package

        // loop over all the items and try to put them into packages in a semi-intelligent manner
        // we have sorted the list of items from biggest to smallest.  Items with a volume larger than
        // our standard box will generate a package with the dimensions set to the size of the item.
        // otherwise we just keep stuffing items in the current package until we can't find anymore that will
        // fit.  Once that happens we close that package and start a new one...repeating until we are out of items
        $space_left = $box_volume;
        $total_weight = 0;
        $parcels = array();
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
//                    $upsRate->package(array(
//                        'description'=>'shipment',
//                        'weight'=>$weight,
//                        'weight_type'=>'LBS',  //FIXME we need to be able to set this
//                        'code'=>'02',
//                        'length'=>$pi->l,
//                        'width'=>$pi->w,
//                        'height'=>$pi->h,
//                        'measure_type'=>'IN',  //FIXME we need to be able to set this
//                    ));
                    $parcels[] = array(
                        "parcel" => array(
                            "length" => $pi->l,
                            "width" => $pi->w,
                            "height" => $pi->h,
                            "weight" => $weight,
                            'predefined_package' => null,
                        ),
//                        "options" => array("cod_amount" => 14.99)
                    );
                    //FIXME we need to begin adding the rates per package here
                    $used[] = $idx;
                    $no_more_room = false;
                } elseif($pi->volume <= $space_left) {
                    $space_left = $space_left - $pi->volume;
                    $total_weight += $pi->weight;
#                    echo "Adding ".$pi->name."<br>";
#                    echo "Space left in current box: ".$space_left."<br>";
                    $used[] = $idx;
                    $no_more_room = false;
                }
            }

            // remove the used items from the array so they wont be there on the next go around.
            foreach ($used as $idx) {
                unset($package_items[$idx]);
            }

            // if there is no more room left in the current box or we are out of items then
            // add the remaining package to the shipment.
            if ($no_more_room || (empty($package_items) && $total_weight > 0)) {
                $total_weight = $total_weight > 1 ? $total_weight : 1;
#                eDebug('created standard sized package with weight of '.$total_weight);
#                echo "<hr>";
//                $upsRate->package(array(
//                    'description'=>'shipment',
//                    'weight'=>$total_weight,
//                    'weight_type'=>'LBS',  //FIXME we need to be able to set this
//                    'code'=>'02',
//                    'length'=>$box_length,
//                    'width'=>$box_width,
//                    'height'=>$box_height,
//                    'measure_type'=>'IN',  //FIXME we need to be able to set this
//                ));
                $parcels[] = array(
                    "parcel" => array(
                        "length" => $box_length,
                        "width" => $box_width,
                        "height" => $box_height,
                        "weight" => $total_weight,
                        'predefined_package' => null,
                    ),
                );
                //FIXME we need to begin adding the rates per package here
                $space_left = $box_volume;
                $total_weight = 0;
            }
        }

        // create shipment
        $shipment_params = array(
            "from_address" => $from_address,
            "to_address" => $to_address,
//            "parcel" => $parcel  //FIXME single package
            "shipments" => $parcels  // multiple packages
        );
        try {
//            $shipment = \EasyPost\Shipment::create($shipment_params);  //FIXME single package
            $shipment = \EasyPost\Order::create($shipment_params);  // multiple packages
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }

        $handling = empty($this->configdata['handling']) ? 0 : $this->configdata['handling'];

        try {
            $messages = \EasyPost\Util::convertEasyPostObjectToArray($shipment->messages);
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }
        foreach ($messages as $message) {
            flash('error', 'easypost: ' . $message['carrier'] . ': ' . $message['message']);
        }

        try {
            $rates = \EasyPost\Util::convertEasyPostObjectToArray($shipment->rates);
        } catch (Exception $e) {
            $msg =  $e->prettyPrint(false);
            flash('error', 'easypost: <br>' . $msg);
            return $msg;
        }
        $eprates = array();
        if (!empty($rates)) {
            $available_methods = $this->availableMethods(true);
            foreach ($rates as $rate) {
                if (isset($available_methods[$rate['carrier']])) {
                    $eprates[$rate['carrier']][$rate['service']] = array(
                        'id' => $rate['carrier'] . ':' . $rate['service'],
                        'title' => $available_methods[$rate['carrier']][$rate['service']],
                        'cost' => $rate['rate'] + $handling,
                        'delivery' => strtotime($rate['delivery_date']),
                    );
                }
            }
        }
        // sort each carrier by cost
        foreach ($eprates as $carrier => $crates) {
            uasort($eprates[$carrier], 'self::sortByRate');
        }
        //NOTE if an error return a string message, else array of rates
        return $eprates;
    }

    //process config form
    function parseConfig($values)
    {
        $config_vars = array(
            'apikey',
            'testkey',
            'testmode',
            'handling',
            'shipping_carriers',
            'shipfrom',
            'default_width',
            'default_length',
            'default_height',
            'default_max_weight',
        );
        $config = array();
        foreach ($config_vars as $varname) {
            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
            if ($varname == 'shipfrom') {
                $config[$varname]['state'] = geoRegion::getAbbrev($values[$varname]['address_region_id']);
                $config[$varname]['country'] = geoRegion::getCountryCode($values[$varname]['address_country_id']);
                unset(
                    $config[$varname]['address_region_id'],
                    $config[$varname]['address_country_id']
                );
            } elseif ($varname == 'handling') {
                $config[$varname] = isset($values[$varname]) ? expUtil::currency_to_float($values[$varname]) : null;
            }

        }

        return $config;
    }

    function availableMethods($multilevel=false)
    {
        $available_methods = array();
        if ($multilevel == true) {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                $available_methods[$carrier] = $this->shippingmethods[$carrier];
            }
        } else {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                foreach ($this->shippingmethods[$carrier] as $key=>$method) {
                    $available_methods[$carrier . ':' . $key] = $carrier . ' - ' . $method;
                }
            }
        }
        return $available_methods;

//FIXME old code
        if (empty($this->configdata['shipping_carriers']) || empty($this->configdata['shipping_methods'])) {
            return array();
        }
        $available_methods = array();
        if ($multilevel == true) {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                $available_methods[$carrier] = $this->shippingmethods[$carrier];
            }
        } else {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                foreach ($this->shippingmethods[$carrier] as $key=>$method) {
                    $available_methods[$carrier . ':' . $key] = $carrier . ' - ' . $method;
                }
            }
        }
        return $available_methods;
    }

    function formatAddress($params)
    {
        $addy['company'] = isset($params->companyName) ? $params->companyName : '';
        $addy['street1'] = isset($params->address1) ? $params->address1 : '';
        $addy['street2'] = isset($params->address2) ? $params->address2 : '';
        $addy['city'] = isset($params->city) ? $params->city : '';
        $addy['state'] = isset($params->state) ? geoRegion::getAbbrev($params->state) : '';
        $addy['country'] = isset($params->state) ? geoRegion::getCountryCode($params->country) : '';
        $addy['zip'] = isset($params->zip) ? $params->zip : '';
        $addy['phone'] = isset($params->phone) ? $params->phone : '';
        return $addy;
    }

//    public static function sortByVolume($a, $b)
//    {
//        return ($a->volume > $b->volume ? -1 : 1);
//    }

    public static function sortByRate($a, $b)
    {
        return ($a['cost'] < $b['cost'] ? -1 : 1);
    }

}

?>