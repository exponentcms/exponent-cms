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
 * @package Modules
 */

/** @define "BASE" "../../../.." */
class easypostcalculator extends shippingcalculator
{
    private static $version = '3.1.3';  // library version
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
            'Ground' => 'Ground',
            'UPSStandard' => 'Standard',
            'UPSSaver' => 'Saver',
            'Express' => 'Worldwide Express',
            'ExpressPlus' => 'Worldwide Express Plus',
            'Expedited' => 'Worldwide Expedited',
            'NextDayAir' => 'Next Day Air',
            'NextDayAirSaver' => 'Next Day Air Saver',
            'NextDayAirEarlyAM' => 'Next Day Air Early AM',
            '2ndDayAir' => 'Second Day Air',
            '2ndDayAirAM' => 'Second Day Air AM',
            '3DaySelect' => 'Three-Day Select',
        ),
        'FedEx' => array(
            'FIRST_OVERNIGHT' => 'Next Day Air - Delivery by 8:30AM',
            'PRIORITY_OVERNIGHT' => 'Next Day Air - Delivery by 10:30AM',
            'STANDARD_OVERNIGHT' => 'Standard Overnight - Delivery by 3PM',
            'FEDEX_2_DAY_AM' => '2-Day - Delivery by 10:30AM',
            'FEDEX_2_DAY' => '2-Day - Delivery by 4:30PM',
            'FEDEX_EXPRESS_SAVER' => '3-Day Express Saver - Delivery by 4:30PM',
            'FEDEX_GROUND' => 'Ground - 1-5 Business Days',
            'INTERNATIONAL_ECONOMY' => 'International Economy',
            'INTERNATIONAL_FIRST' => 'International - Delivery by 8:30AM',
            'INTERNATIONAL_PRIORITY' => 'International Priority',
            'GROUND_HOME_DELIVERY' => 'Ground Home Deliver',
            'SMART_POST' => 'Smart Post',
        ),
    );
    public $predefinedpackages = array(
        'USPS' => array(
            'Card' => 'Card',
            'Letter' => 'Letter',
            'Flat' => 'Flat',
            'Parcel' => 'Parcel',
            'LargeParcel' => 'Large Parcel',
            'IrregularParcel' => 'Irregular Parcel',
            'FlatRateEnvelope' => 'Flat Rate Envelope',
            'FlatRateLegalEnvelope' => 'Flat Rate Legal Envelope',
            'FlatRatePaddedEnvelope' => 'Flat Rate Padded Envelope',
            'FlatRateGiftCardEnvelope' => 'Flat Rate Gift Card Envelope',
            'FlatRateWindowEnvelope' => 'Flat Rate Window Envelope',
            'FlatRateCardboardEnvelope' => 'Fla tRate Cardboard Envelope',
            'SmallFlatRateEnvelope' => 'Small Flat Rat eEnvelope',
            'SmallFlatRateBox' => 'Small Flat Rate Box',
            'MediumFlatRateBox' => 'Medium Flat Rate Box',
            'LargeFlatRateBox' => 'Large Flat Rate Box',
            'RegionalRateBoxA' => 'Regional Rate Box A',
            'RegionalRateBoxB' => 'Regional Rate Box B',
            'RegionalRateBoxC' => 'Regional Rate Box C',
            'LargeFlatRateBoardGameBox' => 'Large Flat Rate Board Game Box',
        ),
        'UPS' => array(
            'UPSLetter' => 'UPS Letter',
            'UPSExpressBox' => 'UPS Express Box',
            'UPS25kgBox' => 'UPS 25kg Box',
            'UPS10kgBox' => 'UPS 10kg Box',
            'Tube' => 'Tube',
            'Pak' => 'Pak',
            'Pallet' => 'Pallet',
            'SmallExpressBox' => 'Small Express Box',
            'MediumExpressBox' => 'Medium Express Box',
            'LargeExpressBox' => 'Large Express Box',
        ),
        'FedEx' => array(
            'FedExEnvelope' => 'FedEx Envelope',
            'FedExBox' => 'FedEx Box',
            'FedExPak' => 'FedEx Pak',
            'FedExTube' => 'FedEx Tube',
            'FedEx10kgBox' => 'FedEx 10kg Box',
            'FedEx25kgBox' => 'FedEx 25kg Box',
            'FedExSmallBox' => 'FedEx Small Box',
            'FedExMediumBox' => 'FedEx Medium Box',
            'FedExLargeBox' => 'FedEx Large Box',
            'FedExExtraLargeBox' => 'FedEx Extra Large Box',
        ),
    );

    public function labelsEnabled()
    {
        return true;
    }

    public function pickupEnabled()
    {
        return true;
    }

    public function trackerEnabled()
    {
        return true;
    }

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
    {
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

        $from_address = self::ep_set_from_address();
        if (is_string($from_address)) {
            return $from_address;
        }  // error

        // get the current shippingmethod and format the address for easypost
        $currentmethod = $order->getCurrentShippingMethod();  // pulls in the 'to' address
        $to_address = self::ep_set_to_address($currentmethod);
        if (is_string($to_address)) {
            return $to_address;
        }  // error

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
//                'weight' => $total_weight * 16,  // convert to oz
//                'length' => $length,
//                'width' => $width,
//                'height' => $height,
//                'predefined_package' => null,
//            )
//        );
//        } catch (Exception $e) {
//            return $this->easypost_error($e, gt('create parcel'));
//        }
        //FIXME end single package

        // loop over all the items and try to put them into packages in a semi-intelligent manner
        // we have sorted the list of items from biggest to smallest.  Items with a volume larger than
        // our standard box will generate a package with the dimensions set to the size of the item.
        // otherwise we just keep stuffing items in the current package until we can't find anymore that will
        // fit.  Once that happens we close that package and start a new one...repeating until we are out of items
        $space_left = $box_volume;
        $total_weight = 0;
        $pkg_weight_oz = 0;
        $parcels = array();
        while (!empty($package_items)) {
            $no_more_room = true;
            $used = array();
            foreach ($package_items as $idx => $pi) {
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
                            "weight" => $weight * 16,  // convert to oz
                            'predefined_package' => null,
                        ),
//                        "options" => array("cod_amount" => 14.99)
                    );
                    $total_weight += $weight * 16;
                    //FIXME we need to begin adding the rates per package here
                    $used[] = $idx;
                    $no_more_room = false;
                } elseif ($pi->volume <= $space_left) {
                    $space_left = $space_left - $pi->volume;
                    $pkg_weight_oz += $pi->weight;
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
            if ($no_more_room || (empty($package_items) && $pkg_weight_oz > 0)) {
                $pkg_weight_oz = $pkg_weight_oz > 1 ? $pkg_weight_oz : 1;
#                eDebug('created standard sized package with weight of '.$pkg_weight_oz);
#                echo "<hr>";
//                $upsRate->package(array(
//                    'description'=>'shipment',
//                    'weight'=>$pkg_weight_oz,
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
                        "weight" => $pkg_weight_oz * 16,  // convert to oz
                        'predefined_package' => null,
                    ),
                );
                $total_weight += $weight * 16;
                //FIXME we need to begin adding the rates per package here
                $space_left = $box_volume;
                $pkg_weight_oz = 0;
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
            return $this->easypost_error($e, gt('create order'));
        }

        $handling = empty($this->configdata['handling']) ? 0 : $this->configdata['handling'];

        try {
            $messages = \EasyPost\Util::convertEasyPostObjectToArray($shipment->messages);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('convert messages'));
        }
        foreach ($messages as $message) {
            //note we definitely get errors if USPS used for over 70 lbs
            if (!($message['carrier'] == 'USPS' && $total_weight > 1120))
                flash('error', 'easypost: ' . $message['carrier'] . ': ' . $message['message']);
        }

        try {
            $rates = \EasyPost\Util::convertEasyPostObjectToArray($shipment->rates);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('convert rates'));
        }
        $eprates = array();
        if (!empty($rates)) {
            $available_methods = $this->availableMethods(true);
            // initialize the array to get assumed lowest rate initially selected
            foreach ($this->shippingcarriers as $carrier) {
                if (isset($available_methods[$carrier])) {
                    $eprates[$carrier] = array();
                }
            }
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
            if (empty($eprates[$carrier])) {
                unset ($eprates[$carrier]);
            } else {
                uasort($eprates[$carrier], 'self::sortByRate');
            }
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

    function availableMethods($multilevel = false)
    {
        $available_methods = array();
        if (empty($this->configdata['shipping_methods']))
            return array();
        if ($multilevel == true) {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                $available_methods[$carrier] = $this->shippingmethods[$carrier];
            }
        } else {
            foreach ($this->configdata['shipping_carriers'] as $carrier) {
                foreach ($this->shippingmethods[$carrier] as $key => $method) {
                    $available_methods[$carrier . ':' . $key] = $carrier . ' - ' . $method;
                }
            }
        }
        return $available_methods;

//FIXME old code
//        if (empty($this->configdata['shipping_carriers']) || empty($this->configdata['shipping_methods'])) {
//            return array();
//        }
//        $available_methods = array();
//        if ($multilevel == true) {
//            foreach ($this->configdata['shipping_carriers'] as $carrier) {
//                $available_methods[$carrier] = $this->shippingmethods[$carrier];
//            }
//        } else {
//            foreach ($this->configdata['shipping_carriers'] as $carrier) {
//                foreach ($this->shippingmethods[$carrier] as $key=>$method) {
//                    $available_methods[$carrier . ':' . $key] = $carrier . ' - ' . $method;
//                }
//            }
//        }
//        return $available_methods;
    }

    function getPackages($carrier)
    {
        return $this->predefinedpackages[$carrier];
    }

    function createLabel($shippingmethod)
    {
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

        $from_address = self::ep_set_from_address();
        if (is_string($from_address)) {
            return $from_address;
        }  // error

        $to_address = self::ep_set_to_address($shippingmethod);
        if (is_string($to_address)) {
            return $to_address;
        }  // error

        if (!empty($shippingmethod->predefinedpackage)) {
            $package = array(
                'weight' => $shippingmethod->weight * 16,  // convert to oz
                'predefined_package' => $shippingmethod->predefinedpackage
            );
        } else {
            $package = array(
                'weight' => $shippingmethod->weight * 16,  // convert to oz
                'length' => $shippingmethod->length,
                'width' => $shippingmethod->width,
                'height' => $shippingmethod->height
            );
        }
        try {
            $parcel = \EasyPost\Parcel::create($package);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('create parcel'));
        }

        // create shipment
        $shipment_params = array(
            "from_address" => $from_address,
            "to_address" => $to_address,
            "parcel" => $parcel  // single package
        );
        try {
            $shipment = \EasyPost\Shipment::create($shipment_params);  // single package
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('create shipment'));
        }
        try {
            $rates = \EasyPost\Util::convertEasyPostObjectToArray($shipment->rates);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('convert shipping rates'));
        }
        $eprates = array();
        if (!empty($rates)) {
            $available_methods = $this->availableMethods(true);
            // initialize the array to get assumed lowest rate initially selected
            foreach ($this->shippingcarriers as $carrier) {
                if (isset($available_methods[$carrier])) {
                    $eprates[$carrier] = array();
                }
            }
            foreach ($rates as $rate) {
                if (isset($available_methods[$rate['carrier']])) {
                    $eprates[$rate['carrier']][$rate['service']] = array(
                        'id' => $rate['carrier'] . ':' . $rate['service'],
                        'title' => $available_methods[$rate['carrier']][$rate['service']],
                        'cost' => $rate['rate'],
                        'delivery' => strtotime($rate['delivery_date']),
                    );
                }
            }
        }
        // sort each carrier by cost
        foreach ($eprates as $carrier => $crates) {
            uasort($eprates[$carrier], 'self::sortByRate');
        }

        $shipping_options = array(
            'shipment_id' => $shipment->id,
            'shipment_rates' => $eprates,  //FIXME not sure we need to get/save these???
            'shipment_status' => 'created'
        );
        $shippingmethod->update(array('shipping_options' => serialize($shipping_options)));

        return $shipment;
    }

    function buyLabel($shippingmethod)
    {
        // here's where we buy a 'shipment'
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

        try {
            $shipment = \EasyPost\Shipment::retrieve(array('id' => $shippingmethod->shipping_options['shipment_id']));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('retrieve shipment'));
        }
        $method = explode(':', $shippingmethod->option);
        try {
            $shipment->buy(
                $shipment->lowest_rate(array($method[0]), array($method[1]))
            );//FIXME we need to select the correct carrier/method based on package type/size
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('buy shipment'));
        }
        $sm_options = $shippingmethod->shipping_options;
        $sm_options['shipment_cost'] = $shipment->selected_rate->rate;
        $sm_options['shipment_tracking_number'] = $shipment->tracking_code;
        $sm_options['shipment_label'] = $shipment->postage_label->label_url;
        $sm_options['shipment_status'] = 'purchased';
        $shippingmethod->update(array('shipping_tracking_number' => $shipment->tracking_code, 'shipping_options' => serialize($sm_options)));
    }

    function getLabel($shippingmethod)
    {
        // here's where we output the label url
        header("Location: " . $shippingmethod->shipping_options['shipment_label']);
        exit('Redirecting...');
    }

    function cancelLabel($shippingmethod)
    {
        // here's where we refund a 'shipment'
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

        try {
            $shipment = \EasyPost\Shipment::retrieve(array('id' => $shippingmethod->shipping_options['shipment_id']));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('retrieve shipment'));
        }
        try {
            $shipment->refund();
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('refund shipment'));
        }
        $sm_options = $shippingmethod->shipping_options;
        $sm_options['shipment_cost'] = 0;
        $sm_options['shipment_status'] = 'cancelled';
        $shippingmethod->update(array('shipping_options' => serialize($sm_options)));
    }

    function createPickup($shippingmethod, $pickupdate, $pickupenddate, $instructions = '')
    {
        // here's where we create a 'pickup'
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

//        $from_address = self::ep_set_from_address();
//        if (is_string($from_address))
//            return $from_address;  // error

        try {
            $shipment = \EasyPost\Shipment::retrieve(array('id' => $shippingmethod->shipping_options['shipment_id']));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('retrieve shipment'));
        }

        try {
            $pickup = \EasyPost\Pickup::create(
                array(
                    "address" => $shipment->from_address,
                    "shipment" => $shipment,
                    "reference" => $shipment->id,
                    "max_datetime" => date("Y-m-d H:i:s", $pickupdate),
                    "min_datetime" => date("Y-m-d H:i:s", $pickupenddate),
                    "is_account_address" => false,
                    "instructions" => $instructions
                )
            );
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('create pickup'));
        }

        try {
            $rates = \EasyPost\Util::convertEasyPostObjectToArray($pickup->pickup_rates);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('convert pickup rates'));
        }
        $eprates = array();
        if (!empty($rates)) {
            foreach ($rates as $rate) {
                $eprates[$rate['service']] = array(
                    'id' => $rate['service'],
                    'cost' => $rate['rate'],
                );
            }
        }
        // sort by cost
        uasort($eprates, 'self::sortByRate');

        $sm_options = $shippingmethod->shipping_options;
        $sm_options['pickup_id'] = $pickup->id;
        $sm_options['pickup_date'] = $pickupdate;
        $sm_options['pickup_date_end'] = $pickupenddate;
        $sm_options['pickup_instructions'] = $instructions;
        $sm_options['pickup_rates'] = $eprates;  //FIXME not sure we need to get/save these???
        $sm_options['pickup_status'] = 'created';
        $shippingmethod->update(array('shipping_options' => serialize($sm_options)));
    }

    function buyPickup($shippingmethod, $type)
    {
        // here's where we optionally buy a 'pickup'
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

//        $from_address = self::ep_set_from_address();
//        if (is_string($from_address))
//            return $from_address;  // error

//        try {
//            $shipment = \EasyPost\Shipment::retrieve(array('id' => $shippingmethod->shipping_options['shipment_id']));
//        } catch (Exception $e) {
//            $msg = $e->prettyPrint(false);
//            flash('error', 'easypost: (retrieve shipment)<br>' . $msg);
//            return $msg;
//        }

//        try {
//            $pickup = \EasyPost\Pickup::create(
//                array(
//                    "address" => $shipment->from_address,
//                    "shipment" => $shipment,
//                    "reference" => $shipment->id,
//                    "max_datetime" => date("Y-m-d H:i:s", $start),
//                    "min_datetime" => date("Y-m-d H:i:s", $end),
//                    "is_account_address" => false,
//                    "instructions" => "Will be next to garage"
//                )
//            );
//        } catch (Exception $e) {
//            return $this->easypost_error($e, gt('retrieve pickup'));
//        }

        try {
            $pickup = \EasyPost\Pickup::retrieve(array('id' => $shippingmethod->shipping_options['pickup_id']));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('retrieve pickup'));
        }

        $method = explode(':', $shippingmethod->option);
        try {
            $pickup->buy(
                array(array('carrier' => $method[0], 'service' => $type))
            );
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('buy pickup'));
        }

        $sm_options = $shippingmethod->shipping_options;
        $sm_options['pickup_cost'] = $pickup->pickup_rates[0]->rate;
        $sm_options['pickup_status'] = 'purchased';
        $shippingmethod->update(array('shipping_options' => serialize($sm_options)));
    }

    function cancelPickup($shippingmethod)
    {
        // here's where we cancel a 'pickup'
        $init = self::ep_initialize();
        if ($init !== true) {
            return $init;
        }  // error

        try {
            $pickup = \EasyPost\Pickup::retrieve(array('id' => $shippingmethod->shipping_options['pickup_id']));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('retrieve pickup'));
        }
        try {
            $pickup->cancel();
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('cancel pickup'));
        }
        $sm_options = $shippingmethod->shipping_options;
        $sm_options['pickup_cost'] = 0;
        $sm_options['pickup_status'] = 'cancelled';
        $shippingmethod->update(array('shipping_options' => serialize($sm_options)));
    }

    function handleTracking() {
        $inputJSON = file_get_contents('php://input');
        $init = self::ep_initialize();
        if (!empty($inputJSON)) {
            $event = \EasyPost\Event::receive($inputJSON);
            if($event->description == 'tracker.updated') {
                //process event here
                $sm = new shippingmethod();
                $my_sm = $sm->find('first', 'carrier="' . $event->result->carrier . '" AND shipping_tracking_number="' . $event->result->tracking_code . '"');
                if (!empty($my_sm->id)) {
                    $my_sm->shipping_options['tracking'] = $event->result;
                    $my_sm->delivery = $event->result->est_delivery_date;
                    $my_sm->update();
                }
            }
        }
        $ar = new expAjaxReply(200, gt('Tracking message handled'));
        $ar->send();
    }

    function getPackageDetails($shippingmethod, $tracking_only=false)
    {
        $msg = '';
        if (($shippingmethod->shipping_options['shipment_status'] == 'created' || $shippingmethod->shipping_options['shipment_status'] == 'purchased') && !$tracking_only) {
            $msg .= '<h4>' . $shippingmethod->carrier . ' - ' . $shippingmethod->option_title . '</h4>';
            $msg .= gt('Package').':<ul>';
            if ($shippingmethod->predefinedpackage) {
                $msg .= '<li>'.$shippingmethod->predefinedpackage.'</li>';
            } else {
                $msg .= '<li>'.$shippingmethod->width . 'in x ' . $shippingmethod->height . 'in x ' . $shippingmethod->length . 'in</li>';
            }
            $msg .= '<li>' . $shippingmethod->weight . 'lbs</li>';
            $msg .= '</ul>'.gt('Items').':<ul>';
            foreach ($shippingmethod->orderitem as $oi) {
                $msg .= '<li>' . $oi->quantity . ' x ' . $oi->products_model . ' - ' . $oi->products_name . '</li>';
            }
            $msg .= '</ul>';
            if ($shippingmethod->shipping_options['shipment_status'] == 'purchased') {
                $msg .= gt('Shipping Cost').': ';
                $sc = $shippingmethod->shipping_options['shipment_cost'];
                $msg .= expCore::getCurrency($sc);
            }
            if ($shippingmethod->shipping_options['pickup_status'] == 'purchased') {
                $msg .= '<br>'.gt('Pickup Cost').': ';
                $pc = $shippingmethod->shipping_options['pickup_cost'];
                $msg .= expCore::getCurrency($pc);
                $msg .= '<br><strong>'.gt('Total Shipping Cost').': ';
                $msg .= expCore::getCurrency($sc+$pc);
                $msg .= '</strong>';
            }
        }
        if (!empty($shippingmethod->shipping_options['tracking'])) {
            $msg .= '<br>'.gt('Delivery Status').': ';
            $msg .= ucwords($shippingmethod->shipping_options['tracking']->status);
            $msg .= '<br>'.gt('Estimated Delivery Date').': ';
            $msg .= $shippingmethod->delivery;
            $msg .= '<br>'.gt('Package Tracking Details').':<ul>';
            foreach ($shippingmethod->shipping_options['tracking']->tracking_details as $details) {
                $msg .= '<li>';
                $msg .= $details->datetime.' - '.ucwords($details->message);
                if (!empty($details->tracking_location->city)) {
                    $msg .= ' - '.ucwords($details->city).', '.ucwords($details->state).' '.$details->country;
                }
                $msg .= '</li>';
            }
            $msg .= '</ul>';
        }

        return $msg;
    }

    function formatAddress($params)
    {
        $addy['company'] = isset($params->companyName) ? $params->companyName : '';
        if (empty($addy['company'])) {
            $addy['company'] = $params->firstname . ' ' . $params->lastname;
        }
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

    function ep_initialize()
    {
        // Require the main class
        include_once(BASE . 'external/easypost-php-' . $this::$version . '/lib/easypost.php');

        if ($this->configdata['testmode']) {
            $apikey = $this->configdata['testkey'];
        } else {
            $apikey = $this->configdata['apikey'];
        }
        try {
            \EasyPost\EasyPost::setApiKey($apikey);
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('api key'));
        }
        return true;
    }

    function ep_set_from_address()
    {
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
            return $this->easypost_error($e, gt('create from address'));
        }
        return $from_address;
    }

    function ep_set_to_address($shippingmethod)
    {
        try {
            $to_address = \EasyPost\Address::create($this->formatAddress($shippingmethod));
        } catch (Exception $e) {
            return $this->easypost_error($e, gt('create to address'));
        }
        return $to_address;
    }

    function easypost_error($error, $function) {
        $msg = $error->prettyPrint(false);
        flash('error', "easypost: ($function)<br>" . $msg);
        return $msg;
    }

}

?>