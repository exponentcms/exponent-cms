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
/** @define "BASE" "../../../.." */

class fedexcalculator extends shippingcalculator {
    /*
      * Returns the name of the shipping calculator, for use in the Shipping Administration Module
      */
    //overridden methods:
    public function name() { return gt('FedEx'); }
    public function description() { return gt('Shipping calculator for dynamically calculating shipping rates using the FedEx API'); }

    public $shippingmethods = array(
        "FIRST_OVERNIGHT"    => "FedEx Next Day Air - Delivery by 8:30AM",
        "PRIORITY_OVERNIGHT" => "FedEx Next Day Air - Delivery by 10:30AM",
        "STANDARD_OVERNIGHT" => "FedEx Standard Overnight - Delivery by 3PM",
        "FEDEX_2_DAY_AM"     => "FedEx 2Day - Delivery by 10:30AM",
        "FEDEX_2_DAY"        => "FedEx 2Day - Delivery by 4:30PM",
        "FEDEX_EXPRESS_SAVER"=> "FedEx 3Day Express Saver - Delivery by 4:30PM",
        "FEDEX_GROUND"       => "FedEx Ground - 1-5 Business Days"
    );

    public function getRates($order) {
//        require_once(BASE . 'external/fedex-php/fedex-common.php');
        //require_once('fedex-common.php');

        //The WSDL is not included with the sample code.
        //Please include and reference in $path_to_wsdl variable.
        $path_to_wsdl = BASE . 'external/fedex-phpv16/wsdl/RateService_v16.wsdl';

        ini_set("soap.wsdl_cache_enabled", "0");

        $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

        $request['WebAuthenticationDetail'] = array('UserCredential' =>
                                                    array('Key'      => $this->configdata['fedex_key'],
                                                          'Password' => $this->configdata['fedex_password']
                                                    )
        );
        $request['ClientDetail']            = array('AccountNumber' => $this->configdata['fedex_account_number'], 'MeterNumber' => $this->configdata['fedex_meter_number']);
        //$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v9 using PHP ***');
        $request['TransactionDetail']                  = array('CustomerTransactionId' => md5("Probody " . date('c')));
        $request['Version']                            = array('ServiceId' => 'crs', 'Major' => '16', 'Intermediate' => '0', 'Minor' => '0');
        $request['ReturnTransitAndCommit']             = true;
        $request['RequestedShipment']['DropoffType']   = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
        $request['RequestedShipment']['ShipTimestamp'] = date('c');

        // Service Type and Packaging Type are not passed in the request
        $this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address1'];
        if (!empty($this->configdata['shipfrom']['address2'])) $this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address2'];
        if (!empty($this->configdata['shipfrom']['address3'])) $this->configdata['shipfrom']['StreetLines'][] = $this->configdata['shipfrom']['address3'];

        unset(
            $this->configdata['shipfrom']['address1'],
            $this->configdata['shipfrom']['address2'],
            $this->configdata['shipfrom']['address3'],
            $this->configdata['shipfrom']['state'],
            $this->configdata['shipfrom']['country']
        );

        if (is_numeric($this->configdata['shipfrom']['StateOrProvinceCode'])) {
            $this->configdata['shipfrom']['StateOrProvinceCode'] = geoRegion::getAbbrev($this->configdata['shipfrom']['StateOrProvinceCode']);
        }
        if (is_numeric($this->configdata['shipfrom']['CountryCode'])) {
            $this->configdata['shipfrom']['CountryCode'] = geoRegion::getCountryCode($this->configdata['shipfrom']['CountryCode']);
        }

        //eDebug($this->configdata['shipfrom'],true);

        $request['RequestedShipment']['Shipper'] = array('Address'=> $this->configdata['shipfrom']);
        // get the current shippingmethod and format the address for ups
        $currentmethod                                          = $order->getCurrentShippingMethod();
        $request['RequestedShipment']['Recipient']              = array('Address'=> $this->formatAddress($currentmethod));
        $request['RequestedShipment']['ShippingChargesPayment'] = array('PaymentType' => 'SENDER',
                                                                        'Payor'       => array( 'AccountNumber' => $this->configdata['fedex_account_number'], // Replace 'XXX' with payor's account number
                                                                                                'Contact' => null,
                                                                                                'Address' => array(
                                                                         				            'CountryCode' => $this->configdata['shipfrom']['CountryCode']
                                                                                                )));
//        $request['RequestedShipment']['RateRequestTypes']       = 'ACCOUNT';
//        $request['RequestedShipment']['RateRequestTypes']       = 'LIST';
//        $request['RequestedShipment']['PackageDetail']          = 'INDIVIDUAL_PACKAGES';

        // set the standard box sizes.
        $box_width  = empty($this->configdata['default_width']) ? 0 : $this->configdata['default_width'];
        $box_height = empty($this->configdata['default_height']) ? 0 : $this->configdata['default_height'];
        $box_length = empty($this->configdata['default_length']) ? 0 : $this->configdata['default_length'];
        $box_volume = $box_height * $box_width * $box_length;

        // set some starting/default values
        $weight        = 0;
        $volume        = 0;
        $count         = 0;
        $package_items = array();

        // loop each product in this shipment and create the packages        
        foreach ($order->orderitem as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                if (empty($item->product->no_shipping) && $item->product->requiresShipping == true) {
//                    $lbs    = empty($item->product->weight) ? $this->configdata['default_max_weight'] : $item->product->weight;
                    // calculate option weight
                    $item_weight = $item->getWeight();
                    $lbs    = empty($item_weight) ? $this->configdata['default_max_weight'] : $item_weight;
                    $width  = empty($item->product->width) ? $this->configdata['default_width'] : $item->product->width;
                    $height = empty($item->product->height) ? $this->configdata['default_height'] : $item->product->height;
                    $length = empty($item->product->length) ? $item->product->width : $item->product->length;
                    $length = empty($length) ? $this->configdata['default_length'] : $length;

                    $package_items[$count]         = new stdClass();
                    $package_items[$count]->volume = ($width * $length * $height);
                    $package_items[$count]->weight = $lbs;
                    $package_items[$count]->w      = $width;
                    $package_items[$count]->h      = $height;
                    $package_items[$count]->l      = $length;
                    $package_items[$count]->name   = $item->product->title;
                    $count++;
                }
            }
        }

        if (empty($package_items)) return array();  // fedex needs at least one item to keep from complaining.

        //eDebug($package_items);
        // sort the items by volume
        $package_items = expSorter::sort(array('array'=> $package_items, 'sortby'=> 'volume', 'order'=> 'DESC'));

        // loop over all the items and try to put them into packages in a semi-intelligent manner
        // we have sorted the list of items from biggest to smallest.  Items with a volume larger than
        // our standard box will generate a package with the dimensions set to the size of the item.
        // otherwise we just keep stuffing items in the current package until we can't find anymore that will
        // fit.  Once that happens we close that package and start a new one...repeating until we are out of items
        $space_left = $box_volume;
        //eDebug($space_left);
        $total_weight   = 0;
        $box_count      = 0;
        $fedexItemArray = array();
        while (!empty($package_items)) {
            $no_more_room = false;
            $used         = array();
            foreach ($package_items as $idx=>$pi) {
                if ($pi->volume > $box_volume) {
#                    echo $pi->name."is too big for standard box <br>";
#                    eDebug('created OVERSIZED package with weight of '.$pi->weight);
#                    eDebug('dimensions: height: '.$pi->h." width: ".$pi->w." length: ".$pi->l);
#                    echo "<hr>";
                    $box_count++;
                    $weight = $pi->weight > 1 ? $pi->weight : 1;
                    $fedexItemArray[] = array(
                       'SequenceNumber'    => $box_count,
                       'GroupPackageCount' => 1,
                       'Weight'            => array(
                           'Value' => $weight,
                           'Units' => 'LB'  //FIXME we need to be able to set this
                       ),
                       'Dimensions'        => array(
                           'Length' => $pi->l,
                           'Width'  => $pi->w,
                           'Height' => $pi->h,
                           'Units'  => 'IN'  //FIXME we need to be able to set this
                       )
                    );
                    $used[] = $idx;
                    $no_more_room = false;
                } elseif($pi->volume <= $space_left) {
                    $space_left = $space_left - $pi->volume;
                    $total_weight += $pi->weight;
#                    echo "Adding ".$pi->name."<br>";
#                    echo "Space left in current box: ".$space_left."<br>";                
                    $used[] = $idx;
                    $no_more_room = true;
                }
            }

            // remove the used items from the array so they wont be there on the next go around.
            foreach ($used as $idx) {
                unset($package_items[$idx]);
            }

            // if there is no more room left on the current package or we are out of items then
            // add the package to the shipment.
            if ($no_more_room || (empty($package_items) && $total_weight > 0)) {
                $box_count++;
                $total_weight = $total_weight > 1 ? $total_weight : 1;
#                eDebug('created standard sized package with weight of '.$total_weight);
#                echo "<hr>";
                $fedexItemArray[] = array(
                    'SequenceNumber'    => $box_count,
                    'GroupPackageCount' => 1,
                    'Weight'            => array(
                        'Value' => $total_weight,
                        'Units' => 'LB'  //FIXME we need to be able to set this
                    ),
                    'Dimensions'        => array(
                        'Length' => $box_length,
                        'Width'  => $box_width,
                        'Height' => $box_height,
                        'Units'  => 'IN'  //FIXME we need to be able to set this
                    )
                );
                $space_left       = $box_volume;
                $total_weight = 0;
            }
        }

        //eDebug($fedexItemArray,true);
        //eDebug($box_count . " boxes in this shipment.");

        $request['RequestedShipment']['PackageCount']              = $box_count;
        $request['RequestedShipment']['RequestedPackageLineItems'] = $fedexItemArray;

        //eDebug($request['RequestedShipment']['PackageCount']);
        //eDebug($request['RequestedShipment']['RequestedPackageLineItems'],true);

        try {
//            if (setEndpoint('changeEndpoint')) {
//                $newLocation = $client->__setLocation('');
//            }

            $response = $client->getRates($request);
            //eDebug($response,true);    
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                //echo 'Rates for following service type(s) were returned.'. Newline. Newline;
                //echo '<table border="1">';
                //echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td>';

                $rates = array();
                //array(
                //"03"=>array("id"=>"03", "title"=>"UPS Ground", "cost"=>5.00),
                //"02"=>array("id"=>"02", "title"=>"UPS Second Day Air", "cost"=>10.00),
                //"01"=>array("id"=>"01", "title"=>"UPS Next Day Air", "cost"=>20.00) 
                //);
//             eDebug($this->configdata['shipping_methods']);
//             eDebug($response->RateReplyDetails);
                if (!empty($response->RateReplyDetails)) foreach ($response->RateReplyDetails as $rateReply) {
                    if (in_array($rateReply->ServiceType, $this->configdata['shipping_methods'])) {
                        $rates[$rateReply->ServiceType] = array(
                            "id"   => $rateReply->ServiceType,
                            "title"=> $this->shippingmethods[$rateReply->ServiceType],
                            "cost" => number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount, 2, ".", ",")
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
            } else {
                if (!is_array($response->Notifications)) {
                    $messages = array($response->Notifications);
                } else {
                    $messages = $response->Notifications;
                }
                $message = '';
                foreach ($messages as $msg) {
                    $message .= empty($message) ? '' : '<br />       ';
                    $message .= $msg->Message;
                }
                flash('error','FedEx: '.$message);
                return $message;
            }
        } catch (SoapFault $exception) {
            flash('error','FedEx: '.$exception->getMessage());
        }
    }

//    public function configForm() {
//        return BASE . 'framework/modules/ecommerce/shippingcalculators/views/fedexcalculator/configure.tpl';
//    }

    //process config form
    function parseConfig($values) {
        $config_vars = array(
            'fedex_account_number',
            'fedex_meter_number',
            'fedex_key',
            'fedex_password',
            'shipping_methods',
            'shipfrom',
            'default_width',
            'default_length',
            'default_height',
            'default_max_weight',
            'testmode'
        );
        $config = array();
        foreach ($config_vars as $varname) {
            $config[$varname] = isset($values[$varname]) ? $values[$varname] : null;
            if ($varname == 'shipfrom') {
                $config[$varname]['StateOrProvinceCode'] = geoRegion::getAbbrev($values[$varname]['address_region_id']);
   	            $config[$varname]['CountryCode'] = geoRegion::getCountryCode($values[$varname]['address_country_id']);
                unset(
                    $config[$varname]['address_region_id'],
                    $config[$varname]['address_country_id']
                );
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
        if (isset($params->address2)) $addy['Streetlines'][] = $params->address2;
        $addy['City']                = isset($params->city) ? $params->city : '';
        $addy['StateOrProvinceCode'] = isset($params->state) ? geoRegion::getAbbrev($params->state) : '';
        $addy['CountryCode']         = isset($params->country) ? geoRegion::getCountryCode($params->country) : '';
        $addy['PostalCode']          = isset($params->zip) ? $params->zip : '';
        return $addy;
    }

//    public static function sortByVolume($a, $b) {
////        eDebug($a);
//        return ($a->volume > $b->volume ? -1 : 1);
//    }

}

?>