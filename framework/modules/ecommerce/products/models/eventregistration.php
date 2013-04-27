<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @subpackage Models
 * @package    Modules
 */

class eventregistration extends expRecord {
    public $table = 'product';
    public $has_one = array();
    public $has_and_belongs_to_many = array('storeCategory');
    public $has_many = array('optiongroup');
    public $get_assoc_for = array('optiongroup');

    public $product_name = 'Event Registration';
    public $product_type = 'eventregistration';
    public $requiresShipping = false;
    public $requiresBilling = true; //FIXME only if a cost is involved
    public $isQuantityAdjustable = false;

    public $default_sort_field = 'rank';
    public $rank_by_field = 'rank';
    public $default_sort_direction = "asc";

    public $early_discount_amount_modifiers = array('$' => '$', '%' => '%');

    protected $attachable_item_types = array(
//        'content_expCats'=>'expCat',
//        'content_expComments'=>'expComment',
//        'content_expDefinableFields' => 'expDefinableField',
        'content_expFiles'           => 'expFile',
//        'content_expRatings'=>'expRating',
//        'content_expSimpleNote'=>'expSimpleNote',
//        'content_expTags'=>'expTag',
    );

    public function __construct($params = array(), $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // trick this record into looking to the eventregistration table
        // and adding it to our data as if it were in the product table with the rest
        // of the product data.
        $origid = $this->id; // save the id from the product table
        $this->table = $this->product_type;
        parent::__construct($this->product_type_id, false, false);
        $this->id = $origid; // put the product table id back.
        $this->table = 'product';
        $this->tablename = 'product';
        if (!empty($this->use_early_price) && !empty($this->earlydiscountdate) && ($this->earlydiscountdate > time())) {
            $this->use_special_price = true;
        }
    }

    public function update($params = array()) {
        global $db;

        if (!empty($params)) {
            // handle definable fields
//            if (isset($params['id'])) {
//                $db->delete('content_expDefinableFields', 'content_type="' . $this->classname . '" AND content_id=' . $params['id']);
//            }

            // creating or editing the event reservation
            if (isset($params['id'])) {
                $product = new product($params['id']);
            }
            // eDebug($params, true);
            // Save the event info to the eventregistration table
            #	    $event = new expRecord();
            #	    $event->tablename = 'eventregistration';
            $event = new stdClass();
//            $event->num_guest_allowed = !empty($params['quantity']) ? $params['quantity'] : 0;
            $event->eventdate = strtotime($params['eventdate']);
            $event->eventenddate = strtotime($params['eventenddate']);
            $event->event_starttime = datetimecontrol::parseData('event_starttime', $params);
            $event->event_endtime = datetimecontrol::parseData('event_endtime', $params);
            $event->signup_cutoff = strtotime($params['signup_cutoff']);

            $event->forms_id = $params['forms_id'];
            $event->multi_registrant = $params['multi_registrant'];

            $event->location = $params['location'];
            $event->terms_and_condition = $params['terms_and_condition'];
            $event->require_terms_and_condition = !empty($params['require_terms_and_condition']) ? $params['require_terms_and_condition'] : false;
            $event->terms_and_condition_toggle = $params['terms_and_condition_toggle'];

            $event->earlydiscountdate = strtotime($params['earlydiscountdate']);
            $event->early_discount_amount = !empty($params['early_discount_amount']) ? $params['early_discount_amount'] : 0;
            $event->early_discount_amount_mod = $params['early_discount_amount_mod'];
            $event->use_early_price = !empty($params['use_early_price']) ? $params['use_early_price'] : false;

            $event->id = empty($product->product_type_id) ? null : $product->product_type_id;

            //Option Group Tab
            if (!empty($params['optiongroups'])) {
                foreach ($params['optiongroups'] as $title => $group) {
                    if (isset($this->params['original_id']) && $params['original_id'] != 0) $group['id'] = ''; //for copying products

                    $optiongroup = new  optiongroup($group);
                    $optiongroup->product_id = $product->id;
                    $optiongroup->save();

                    foreach ($params['optiongroups'][$title]['options'] as $opt_title => $opt) {
                        if (isset($params['original_id']) && $params['original_id'] != 0) $opt['id'] = ''; //for copying products

                        $opt['product_id'] = $product->id;
                        $opt['is_default'] = false;
                        $opt['title'] = $opt_title;
                        $opt['optiongroup_id'] = $optiongroup->id;
                        if (isset($params['defaults'][$title]) && $params['defaults'][$title] == $opt['title']) {
                            $opt['is_default'] = true;
                        }

                        $option = new option($opt);
                        $option->save();
                    }
                }
            }

            if (!empty($event->id)) {
                $db->updateObject($event, 'eventregistration');
            } else {
                $event->id = $db->insertObject($event, 'eventregistration');
            }

            $params['product_type_id'] = $event->id;
        } else {
            // customer is completing the actual reservation
            $event = $db->selectObject('eventregistration', 'id=' . $this->product_type_id);
//            $event->number_of_registrants = $this->number_of_registrants;
//            $event->registrants = $this->registrants;
            $db->updateObject($event, 'eventregistration');
        }
        // eDebug($params, true);
        // $product->expFile= $params['expFile'];
        parent::update($params);
    }

    function displayForm($form, $params) {
        // eDebug($params, true);
        //$product_type = isset($this->params['product_type']) ? $this->params['product_type'] : 'product';
        //$product = new $product_type($this->params['product_id'],true,true);     
        //eDebug($product);   
        //if (!empty($product->user_input_fields)) $product->user_input_fields = expUnserialize($product->user_input_fields);
        //eDebug($product);
        $form = new controllertemplate(new storeController(), $this->getForm($form));
        $form->assign('params', $params);
        $form->assign('product', $this);
        if (!empty($params['children'])) {
            $form->assign('children', $params['children']);
        }
        if (!empty($params['extra_data'])) {
            $form->assign('extra_data', expUnserialize($params['extra_data']));
        }

        /*if (!empty($this->params['children'])) 
        {
            $form->assign('children', expUnserialize($this->params['children']));   
        }*/
        if (!empty($params['no_output'])) {
            return $form->render();
        } else {
            echo $form->render();
        }
    }

    public function getSEFURL() {
        if (!empty($this->sef_url)) return $this->sef_url;
        $parent = new product($this->parent_id, false, false);
        return $parent->sef_url;
    }

    public function hasOptions() {
        // eDebug($this, true);
        foreach ($this->optiongroup as $og) {
            if (count($og->option) > 0) {
                foreach ($og->option as $option) {
                    if ($option->enable == true) return true;
                }
            }
        }
        return false;
    }

    public function hasUserInputFields() {
        return false;  //FIXME not sure this accurate based on expDefinableFields & Forms
    }

    public function hasRequiredOptions() {
        foreach ($this->optiongroup as $og) {
            if ($og->required) return true;
        }
        return false;
    }

    public function optionDropdown($key, $display_price_as) {
        $items = array();

        foreach ($this->optiongroup as $index => $group) {
            if ($group->title == $key) {
                foreach ($group->option as $option) {
                    if ($option->enable == true) {
                        $text = $option->title;

                        $price = '';
                        if (isset($option->amount)) {
                            if ($option->modtype == '%') {
                                $diff = ($this->getBasePrice() * ($option->amount * .01)) + $this->getBasePrice();
                            } else {
                                $diff = $option->amount;
                            }

                            if ($display_price_as == 'total') {
                                $newprice = ($option->updown == '+') ? ($this->getBasePrice() + $diff) : ($this->getBasePrice() - $diff);
                                $price = ' (' . expCore::getCurrencySymbol() . number_format($newprice, 2) . ')';
                            } else {
                                if ($diff > 0) {
                                    $diff = expCore::getCurrencySymbol() . number_format($diff, 2);
                                    $price = ' (' . $option->updown . $diff . ')';
                                } else {
                                    $price = '';
                                }
                            }
                        }

                        $items[$option->id] = $text . $price;
                    }
                }
            }
        }
        return $items;
    }

    public function spacesLeft() {
        global $db;

//        return $this->quantity - $this->number_of_registrants;
        $f = new forms($this->forms_id);
        return $this->quantity - $db->countObjects('forms_' . $f->table_name, "referrer='" . $this->id . "'");
    }

    public function cartSummary($item) {
        $view = new controllertemplate($this, $this->getForm('cartSummary'));
        $view->assign('product', $this);
        $view->assign('item', $item);

        $view->assign('asset_path', PATH_RELATIVE ."framework/modules/ecommerce/assets/");

        // grab the options
        $options = expUnserialize($item->options);
        $view->assign('options', $options);

        // grab all the registrants
        $registrants = expUnserialize($item->extra_data);
        $view->assign('registrants', $registrants);

        //assign the number registered to the view
        $number = count($registrants);
        $view->assign('number', $number);

        return $view->render();
    }

    function getBasePrice($orderitem = null) {
        if ($this->use_special_price) {
            return $this->special_price;
        } else {
            return $this->base_price;
        }
    }

    function getDefaultQuantity() {
        //TMP: Make this actually do something.
        return 1;
    }

    function getSurcharge() {
        $sc = 0;
        //take parent level surcharge, but override surcharge child product is set            
        if ($this->surcharge == 0 && $this->parent_id != 0) {
            $parentProd = new product($this->parent_id);
            $sc = $parentProd->surcharge;
        } else {
            $sc = $this->surcharge;
        }
        //eDebug($sc);
        return $sc;
    }

    /**
     * addToCart - we are simply adding an event registration to our shopping cart
     * this is where we create/update the eventregistration_registrants records
     *
     * @param array $params
     * @param null  $orderid
     *
     * @return bool
     */
    function addToCart($params, $orderid = null) {
        // eDebug($params, true);
        global $db, $order, $user;

//        if (!empty($params['event'])) {
//            $sess_id = session_id();
        $sess_id = expSession::getTicketString();
        expSession::set('session_id', $sess_id);

 //        $item = new orderitem($params);
        // if the item is in the cart already use it, if not we'll create a new one
        if (!empty($params['orderitem_id'])) $item = $order->isItemInCart($params['product_id'], $params['product_type'], $params['orderitem_id']);
        if (empty($item->id)) $item = new orderitem($params);
        $item->save();  // we need to get an orderitem->id even if it's a new order

        //FIXME for now we'll just add a new registration 'purchase' to the cart since that's the way the code flows.
//        $data = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND event_id =" . $params['product_id']);
//        if (!empty($data)) {
//            //FIXME we are adding updating an existing item in the cart??
//            // we're only updating the items that were already in the cart?  what if more or less were added.
//            foreach ($data as $item) {
//                if (!empty($params['event'][$item->control_name])) {
//                    $item->value = serialize($params['event'][$item->control_name]);
//                    $db->updateObject($item, "eventregistration_registrants");
//                }
//            }
//        } else {
        $f = new forms($this->forms_id);
        $registrants = array();
        if (!empty($params['registrant']) && !empty($f->is_saved)) {  // is there user input data
            // first invert the key sequence by registrant index instead of by field name
            foreach ($params['registrant'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $registrants[$key1][$key] = $value1;
                }
            }
            $loc_data = new stdClass();
            $loc_data->order_id = $order->id;
            $loc_data->orderitem_id = strval($item->id);
            $loc_data->event_id = $params['product_id'];
            $locdata = serialize($loc_data);
            if (!empty($params['orderitem_id'])) $db->delete('forms_' . $f->table_name, "location_data ='{$locdata}'");  // remove existing entries for this registration
            $fc = new forms_control();
            $controls = $fc->find('all', "forms_id=" . $f->id . " and is_readonly=0",'rank');
            foreach ($registrants as $registrant) {
                $db_data = new stdClass();
                foreach ($controls as $c) {
                    $ctl = expUnserialize($c->data);
                    $control_type = get_class($ctl);
                    $def = call_user_func(array($control_type, "getFieldDefinition"));
                    if ($def != null) {
                        $emailValue = htmlspecialchars_decode(call_user_func(array($control_type, 'parseData'), $c->name, $registrant, true));
                        $value = stripslashes($db->escapeString($emailValue));
                        $varname = $c->name;
                        $db_data->$varname = $value;
                    }
                }
                $db_data->ip = $_SERVER['REMOTE_ADDR'];
                $db_data->referrer = $params['product_id'];
                $db_data->timestamp = time();
                if (expSession::loggedIn()) {
                    $db_data->user_id = $user->id;
                } else {
                    $db_data->user_id = 0;
                }
                $db_data->location_data = $locdata;
                $db->insertObject($db_data, 'forms_' . $f->table_name);
            }

        // we're replacing an existing registration based on updated input
//        if (!empty($params['orderitem_id'])) $db->delete("eventregistration_registrants", "connector_id ='{$order->id}' AND orderitem_id ='" . $params['orderitem_id'] . "' AND event_id ='" . $params['product_id'] ."'");
//            foreach ($registrants as $key => $value) {
//                $obj = new stdClass();
//                $obj->event_id = $params['product_id'];
//                $obj->control_name = $key;
//                $obj->value = serialize($value);
//                $obj->connector_id = $order->id;
//                $obj->orderitem_id = $item->id;
//                $obj->registered_date = time();
//                $db->insertObject($obj, "eventregistration_registrants");
//            }
//
//
//            foreach ($registrants as $key => $value) {
//                $obj = new stdClass();
//                $obj->event_id = $params['product_id'];
//                $obj->control_name = $key;
//                $obj->value = serialize($value);
//                $obj->connector_id = $order->id;
//                $obj->orderitem_id = $item->id;
//                $obj->registered_date = time();
//                $db->insertObject($obj, "eventregistration_registrants");
//            }
        } else {  // handle registration with NO user input?
            $obj = new stdClass();
            $obj->event_id = $params['product_id'];
            $obj->connector_id = $order->id;
            $obj->orderitem_id = $item->id;
            $obj->registered_date = time();
            $obj->control_name = $user->firstname . ' ' . $user->lastname;
            $obj->value = $params['qtyr'];
            $db->insertObject($obj, "eventregistration_registrants");
            $registrants[0]['name'] = $user->firstname . ' ' . $user->lastname;
            $registrants[0]['qty'] = $params['qtyr'];
        }
//        }
//        }

//        $item->extra_data = serialize($params['registrant']);
        $item->extra_data = serialize($registrants);  // we'll save this as extra_data, though it's really user input

        $product = new eventregistration($params['product_id']);
        $item->products_name = $product->title . " - " . date("F d, Y", $product->eventdate);

        $options = array();
//        $price = 0;
//        $price = $product->base_price;
        $price = $this->getBasePrice();
        foreach ($this->optiongroup as $og) {
            $isOptionEmpty = true;
            if (!empty($params['options'][$og->id])) {
                foreach ($params['options'][$og->id] as $opt) {
                    if (!empty($opt)) $isOptionEmpty = false;
                }
            }
            if (!$isOptionEmpty) {
                foreach ($params['options'][$og->id] as $opt_id) {
                    $selected_option = new option($opt_id);
                    $cost = $selected_option->modtype == '$' ? $selected_option->amount : $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost = $selected_option->updown == '+' ? $cost : $cost * -1;
                    if (@$params['options_quantity'][$opt_id] > 0) {
                        $price += $cost * $params['options_quantity'][$opt_id];

                    } else {
                        $params['options_quantity'][$opt_id] = 1;
                        $price += $cost * $params['options_quantity'][$opt_id];
                    }
                    // eDebug($price);
                    $options[$og->id] = array($selected_option->id, $selected_option->title, $selected_option->modtype, $selected_option->updown, $selected_option->amount, $params['options_quantity'][$opt_id]);
                }
            }
        }
        // eDebug($price);
        // eDebug($options, true);
        // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
        // other giftcards in the cart already
//        $item->id = null;
        if (!empty($params['options_quantity'])) {
//            $quantity = 1;
            $quantity = $params['qtyr'];
            $item->quantity = $quantity;
            $item->products_price = $price;
        } else { // no options selected
            if (empty($params['qtyr'])) {
                $params['qtyr'] = 1;
            }
            if (!empty($params['base_price'])) $item->products_price = preg_replace("/[^0-9.]/", "", $params['base_price']);
            else $item->products_price = $product->base_price;
            $item->quantity = $params['qtyr'];
        }

//        $this->displayForm('addToCart',$params);
//        return false;
        $item->options = serialize($options);
        $item->save();
        return true;
    }

        /*
     function addToCart($params, $orderid = null) {
         global $db, $order;
         if (isset($params['registrants'])) {
             // save the order item
             for($x=0; $x<count($params['registrants']); $x++){
                  $ed[$x]['name']= $params['registrants'][$x];
                  $ed[$x]['email']= $params['registrant_emails'][$x];
                  $ed[$x]['phone']= $params['registrant_phones'][$x];
             }

             // if the item is in the cart already use it, if not we'll create a new one
             $item = $order->isItemInCart($params['product_id'], $params['product_type']);
             if (empty($item->id)) $item = new orderitem($params);

             // if we already have this event in our cart then we need to merge the registrants
             $registrants = array();
             if (!empty($item->extra_data)) $registrants = expUnserialize($item->extra_data);
             $registrants = array_merge($registrants, $ed);
             $item->quantity = count($registrants);
             $item->extra_data = serialize($registrants);
             $item->save();
             return true;
         } else {
             return false;
         }
     }
     */

    /**
     * process - here we are actually making a 'reservation' by submitting the purchase
     *
     * @param product $item the event registration product
     *
     * @return bool
     */
    public function process($item) {
        global $db, $order;

        // save the names of the registrants to the eventregistration table too
        //FIXME we need to be dealing w/ eventregistration_registrants here also/primarily
        // eventregistration table is the product extension fields - 1 per event; but eventregistration_registrants are generated 1 per order
        // if related order_id != 0, the order is completed based on eventregistration_registrants->connector_id == order->sessionticket_ticket
        $product = new eventregistration($item->product_id);
//        $registrants = expUnserialize($product->registrants);
//        $registrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND event_id =" . $item->product_id);
//        $f = new forms($this->forms_id);
//        if ($f->is_saved == 1) {  // is there user input data
//            $loc_data = new stdClass();
//            $loc_data->order_id =$item->orders_id;
//            $loc_data->orderitem_id = strval($item->id);
//            $loc_data->event_id = $item->product_id;
//            $locdata = serialize($loc_data);
//            $order_registrations = $db->selectObjects('forms_' . $f->table_name, "location_data='" . $locdata . "'");
//        }
//        $order_registrations  = expUnserialize($item->extra_data);
//        $order_registrations = array($item->user_id);
        // update paid status
//        $order_registrations = array();
//        if (!empty($registrants)) foreach ($registrants as $registrant) {
//            $order_registrations[] = expUnserialize($registrant->value);
//
//            $value = expUnserialize($registrant->value);
//            $billingstatus = expUnserialize($order->billingmethod[0]->billing_options);
////            $value['payment'] = !empty($billingstatus->payment_due) ? expCore::getCurrencySymbol() . number_format($billingstatus->payment_due, 2) . ' ' . gt('Due') : 'paid';
//            $value['payment'] = !empty($billingstatus->payment_due) ? gt('payment due') : gt('paid');
//            $registrant->value = serialize($value);
//            $db->updateObject($registrant,"eventregistration_registrants");
//        }
//        $product->registrants = is_array($registrants) ? array_merge($registrants, $order_registrations) : $order_registrations; //: array_merge($registrants, $order_registrations);

        // create an object to update the event table.
//        $event                        = new stdClass();
//        $event->id                    = $product->product_type_id;
//        $event->number_of_registrants += count($product->registrants);
//        $event->registrants           = serialize($product->registrants);
//        $db->updateObject($event, 'eventregistration');

//        $product->number_of_registrants += count($order_registrations);
//        $product->registrants = serialize($order_registrations);
        $product->update();
        // eDebug(expSession::get('expDefinableField'), true);
//        foreach (expSession::get('expDefinableField') as $key => $value) {
//            $obj = new stdClass();
//            $obj->expdefinablefields_id = $key;
//            $obj->content_id = $item->product_id;
//            $obj->connector_id = $order->id;
//            $obj->content_type = "eventregistration";
//            $obj->value = $value;
//            $db->insertObject($obj, 'content_expDefinableFields_value');
//        }
        //add unset here

        return true;
    }

    public function isAvailable() {
        return (($this->spacesLeft() != 0 || $this->quantity == 0) && $this->signup_cutoff > time()) ? true : false;
    }

    public function getAllControls() {
        $f = new forms($this->forms_id);
        if (empty($f->is_saved)) return null;  // useless unless the form data is being saved
        $fc = new forms_control();
        $controls = $fc->find('all','forms_id='.$this->forms_id);
        foreach ($controls as $key=>$control) {
            $controls[$key]->ctl = expUnserialize($control->data);
        }
        return $controls;
    }

    public function showControl($field, $name, $escape = '', $value = '', $adminedit = false, $hidecaption = false) {
        $id = $field->id;
        $control = $field->data;
        $type = $field->type;
        $ctl = expUnserialize($control);
        if (empty($name)) {
            $name = $ctl->name;
        }

        if (!empty($this->params['token'])) {
            $record = expSession::get("last_POST_Paypal");
        } else {
            $record = expSession::get("last_POST");
        }

        if (!empty($value)) {
            $ctl->default = $value;
        } else {
            if (!empty($record['event'][$name])) $ctl->default = $record['event'][$name];
        }
        if ($hidecaption) {
            $caption = '';
        } else {
            $caption = $ctl->caption;
//            $caption = '';
        }
        if ($escape) {
            return addslashes($ctl->toHTML($caption, "$name"));
        } else {
            if ($name == "email" && $adminedit == true) {
                return $ctl->toHTML($caption, "$name", true);  //FIXME there is no 3rd param for this
            } else {
                return $ctl->toHTML($caption, "$name");
            }
        }
    }

    function checkout() {
        // nothing to do for this callback.
    }

    public function removeItem($item) {
        global $db;

//        $db->delete("eventregistration_registrants", "connector_id ='{$item->orders_id}' AND event_id =" . $item->product_id);
        $f = new forms($this->forms_id);
        if (!empty($f->is_saved)) {  // is there user input data
            $loc_data = new stdClass();
            $loc_data->order_id =$item->orders_id;
            $loc_data->orderitem_id = strval($item->id);
            $loc_data->event_id = $item->product_id;
            $locdata = serialize($loc_data);
            $db->delete('forms_' . $f->table_name, "location_data ='{$locdata}'");  // remove existing entries for this registration
        } else {
            $db->delete('eventregistration_registrants', "connector_id ='{$item->orders_id}' AND event_id =" . $item->product_id);  // remove existing entries for this registration
        }
        return true;
    }

    public function getForm($form) {
        $dirs = array(
            BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/views/' . $this->product_type . '/', // make sure we check the controller view first
            BASE . 'framework/modules/ecommerce/views/' . $this->product_type . '/',
            BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/products/views/' . $this->product_type . '/',
            BASE . 'framework/modules/ecommerce/products/views/' . $this->product_type . '/',
            BASE . 'themes/' . DISPLAY_THEME . '/modules/ecommerce/products/views/product/',
            BASE . 'framework/modules/ecommerce/products/views/product/',
        );

        foreach ($dirs as $dir) {
            if (file_exists($dir . $form . '.tpl')) return $dir . $form . '.tpl';
        }

        return false;
    }

    /**
     * Return list of all confirmed registrants for the event
     *   confirmation is invoice # set on order
     *   searches form table and/or non-form table
     *   assigns 'payment' property based on billingtransaction
     */
    public function getRegistrants() {
        global $db;

        $f = new forms($this->forms_id);
        // get list of all orders for this event
        $order_ids_complete = array();
        if (!empty($f->is_saved)) {  // is there user input data
            $registrants = $db->selectObjects('forms_' . $f->table_name, "referrer = {$this->id}", "timestamp");
            foreach ($registrants as $key=>$registrant) {
                $order_data = expUnserialize($registrant->location_data);
                $order_ids_complete[] = $order_data->order_id;
            }
        } else {
            $order_ids_complete = $db->selectColumn("eventregistration_registrants", "connector_id", "connector_id <> '0' AND event_id = {$this->id}", "registered_date", true);
        }
        // build list of completed orders
        $orders = new order();
        $order_ids = array();
        foreach ($order_ids_complete as $item) {
            $odr = $orders->find("first", "id ='{$item}' and invoice_id <> 0");
            if (!empty($odr) || strpos($item, "admin-created") !== false) {
                $order_ids[] = $item;
            }
        }
        // build list of registrants with completed orders
        $registered = array();
        if (!empty($order_ids)) {
            if (!empty($f->is_saved)) {  // is there user input data
                $registrants = $db->selectObjects('forms_' . $f->table_name, "referrer = {$this->id}", "timestamp");
                foreach ($registrants as $key=>$registrant) {
                    $order_data = expUnserialize($registrant->location_data);
                    if (in_array($order_data->order_id, $order_ids)) {
                        $registered[$key] = $registrant;
                        if (is_numeric($order_data->order_id)) {
                            $registered[$key]->order_id = $order_data->order_id;
                            $order = new order($order_data->order_id);
                            $billingstatus = expUnserialize($order->billingmethod[0]->billing_options);
                            $registered[$key]->payment = !empty($billingstatus->payment_due) ? gt('payment due') : gt('paid');
                        } else {
                            $registered[$key]->payment = '???';
                        }
                    }
                }
            } else {
                foreach ($order_ids as $order_id) {
                    $registrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order_id}'");
                    foreach ($registrants as $person) {
                        $new_registered = new stdClass();
                        $new_registered->id = $person->id;
                        $new_registered->user = $person->control_name;
                        $new_registered->qty = $person->value;
                        $new_registered->registered_date = $person->registered_date;
                        if (is_numeric($person->connector_id)) {
                            $new_registered->order_id = $order_id;
                            $order = new order($person->connector_id);
                            $billingstatus = expUnserialize($order->billingmethod[0]->billing_options);
                            $new_registered->payment = !empty($billingstatus->payment_due) ? gt('payment due') : gt('paid');
                        } else {
                            $new_registered->payment = '???';
                        }
                        $registered[] = $new_registered;
                    }
                }
            }
        }
        return $registered;
    }

    /**
     * Return count of registrants confirmed for the event
     *   confirmation is invoice # set on order
     *   searches form table and/or non-form table
     */
    public function countRegistrants() {
        global $db;

        $f = new forms($this->forms_id);
        if (!empty($f->is_saved)) {
            $count = count($this->getRegistrants());
        } else {
            $blind_regs = $this->getRegistrants();
            $count = 0;
            foreach ($blind_regs as $blind_reg) {
                $count += $blind_reg->qty;
            }
        }
        return $count;
    }

}

?>