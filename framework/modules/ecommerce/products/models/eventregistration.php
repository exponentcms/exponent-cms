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
    public $requiresBilling = true;
    public $isQuantityAdjustable = false;

    public $default_sort_field = 'rank';
    public $rank_by_field = 'rank';
    public $default_sort_direction = "asc";

    protected $attachable_item_types = array(
//        'content_expCats'=>'expCat',
//        'content_expComments'=>'expComment',
        'content_expDefinableFields'=> 'expDefinableField',
        'content_expFiles'=>'expFile',
//        'content_expRatings'=>'expRating',
//        'content_expSimpleNote'=>'expSimpleNote',
//        'content_expTags'=>'expTag',
    );


    public function __construct($params = array(), $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);

        // trick this record into looking to the eventregistration table
        // and adding it to our data as if it were in the product table with the rest
        // of the product data.
        $origid      = $this->id; // save the id from the product table
        $this->table = $this->product_type;
        parent::__construct($this->product_type_id, false, false);
        $this->id        = $origid; // put the product table id back.
        $this->table     = 'product';
        $this->tablename = 'product';
    }

    public function update($params = array()) {
        global $db;

        if (!empty($params)) {
            if (isset($params['id'])) {
                $product = new product($params['id']);
            }
            // eDebug($params, true);
            // Save the event info to the eventregistration table
    #	    $event = new expRecord();
    #	    $event->tablename = 'eventregistration';
            $event                              = new stdClass();
            $event->eventdate                   = strtotime($params['eventdate']);
//            $event->eventenddate                = strtotime($params['eventenddate']);
            $event->event_starttime             = datetimecontrol::parseData('event_starttime', $params) + $event->eventdate;
            $event->event_endtime               = datetimecontrol::parseData('event_endtime', $params) + $event->eventdate;
            $event->signup_cutoff               = strtotime($params['signup_cutoff']);
//            $event->location                    = $params['location'];
//            $event->terms_and_condition         = $params['terms_and_condition'];
//            $event->require_terms_and_condition = !empty($params['require_terms_and_condition']) ? $params['require_terms_and_condition'] : false;
//            $event->terms_and_condition_toggle  = $params['terms_and_condition_toggle'];
//            $event->num_guest_allowed           = !empty($params['quantity']) ? $params['quantity'] : 0;
//            $event->number_of_registrants           = !empty($params['quantity']) ? $params['quantity'] : 0;
            $event->id                          = empty($product->product_type_id) ? null : $product->product_type_id;

            //Option Group Tab
            if (!empty($params['optiongroups'])) {

                foreach ($params['optiongroups'] as $title=> $group) {
                    if (isset($this->params['original_id']) && $params['original_id'] != 0) $group['id'] = ''; //for copying products

                    $optiongroup             = new  optiongroup($group);
                    $optiongroup->product_id = $product->id;
                    $optiongroup->save();

                    foreach ($params['optiongroups'][$title]['options'] as $opt_title=> $opt) {
                        if (isset($params['original_id']) && $params['original_id'] != 0) $opt['id'] = ''; //for copying products

                        $opt['product_id']     = $product->id;
                        $opt['is_default']     = false;
                        $opt['title']          = $opt_title;
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
            $event = $db->selectObject('eventregistration','id='.$this->product_type_id);
            $event->number_of_registrants       = $this->number_of_registrants;
            $event->registrants                 = $this->registrants;
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

        /*if (!empty($this->params['children'])) 
        {
            $form->assign('children', expUnserialize($this->params['children']));   
        }*/
        echo $form->render();
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

    public function hasRequiredOptions() {
        foreach ($this->optiongroup as $og) {
            if ($og->required) return true;
        }
        return false;
    }

    public function optionDropdown($key, $display_price_as) {
        $items = array();

        foreach ($this->optiongroup as $index=> $group) {
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
                                $price    = ' ($' . number_format($newprice, 2) . ')';
                            } else {
                                if ($diff > 0) {
                                    $diff  = '$' . number_format($diff, 2);
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
        return $this->quantity - $this->number_of_registrants;
    }

    public function cartSummary($item) {
        $view = new controllertemplate($this, $this->getForm('cartSummary'));
        $view->assign('product', $this);
        $view->assign('item', $item);

        // grab all the registrants
        $registrants = expUnserialize($item->extra_data);

        //assign the number registered to the view
        $number = count($registrants);
        $view->assign('number', $number);

        // assign the list of names to the view.
        $people = '';
        foreach ($registrants as $reg) {
            $people .= $reg['name'] . ',';
        }
        $people = substr($people, 0, -1);
        $view->assign('people', $people);
        return $view->render('cartSummary');
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
            $sc         = $parentProd->surcharge;
        } else {
            $sc = $this->surcharge;
        }
        //eDebug($sc);
        return $sc;
    }

    public function process($item) {
        global $db, $order;
        // save the names of the registrants to the eventregistration table too
        $product              = new eventregistration($item->product_id);
        $registrants          = expUnserialize($product->registrants);
//        $order_registrations  = expUnserialize($item->extra_data);
        $order_registrations  = array($item->user_id);
        $product->registrants = is_array($registrants) ? array_merge($registrants, $order_registrations) : $order_registrations; //: array_merge($registrants, $order_registrations);

        // create an object to update the event table.
//        $event                        = new stdClass();
//        $event->id                    = $product->product_type_id;
//        $event->number_of_registrants += count($product->registrants);
//        $event->registrants           = serialize($product->registrants);
//        $db->updateObject($event, 'eventregistration');

        $product->number_of_registrants += count($product->registrants);
        $product->registrants           = serialize($product->registrants);
        $product->update();
        // eDebug(expSession::get('expDefinableField'), true);
        foreach (expSession::get('expDefinableField') as $key => $value) {
            $obj                        = new stdClass();
            $obj->expDefinableFields_id = $key;
            $obj->content_id            = $item->product_id;
            $obj->connector_id          = $order->id;
            $obj->content_type          = "eventregistration";
            $obj->value                 = $value;
            $db->insertObject($obj, 'content_expDefinableFields_value');
        }
        //add unset here

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
    function addToCart($params, $orderid = null) {
        // eDebug($params, true);
        global $db;

//        if (!empty($params['event'])) {
//            $sess_id = session_id();
            $sess_id = expSession::getTicketString();
            $data    = $db->selectObjects("eventregistration_registrants", "connector_id ='{$sess_id}' AND event_id =" . $params['product_id']);
            if (!empty($data)) {
                foreach ($data as $item) {
                    if (!empty($params['event'][$item->control_name])) {
                        $item->value = $params['event'][$item->control_name];
                        $db->updateObject($item, "eventregistration_registrants");
                    }
                }
            } else {
                if (!empty($params['event'])) foreach ($params['event'] as $key => $value) {
                    $obj                  = new stdClass();
                    $obj->event_id        = $params['product_id'];
                    $obj->control_name    = $key;
                    $obj->value           = $value;
                    $obj->connector_id    = $sess_id;
                    $obj->registered_date = time();
                    $db->insertObject($obj, "eventregistration_registrants");
                } else {
                    $obj                  = new stdClass();
                    $obj->event_id        = $params['product_id'];
                    $obj->connector_id    = $sess_id;
                    $obj->registered_date = time();
                    $db->insertObject($obj, "eventregistration_registrants");
                }
            }
            expSession::set('session_id', $sess_id);
//        }

        $item = new orderitem($params);

        $product             = new eventregistration($params['product_id']);
        $item->products_name = $product->title . " - " . date("F d, Y", $product->eventdate);

        $options = array();
        $price = 0;
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
                    $cost            = $selected_option->modtype == '$' ? $selected_option->amount : $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost            = $selected_option->updown == '+' ? $cost : $cost * -1;
                    if (@$params['options_quantity'][$opt_id] > 0) {
                        $price = $price + $cost * $params['options_quantity'][$opt_id];

                    } else {
                        $params['options_quantity'][$opt_id] = 1;
                        $price                               = $price + $cost * $params['options_quantity'][$opt_id];
                    }
                    // eDebug($price);
                    $options[] = array($selected_option->id, $selected_option->title, $selected_option->modtype, $selected_option->updown, $selected_option->amount, $params['options_quantity'][$opt_id]);
                }
            }
        }
        // eDebug($price);
        // eDebug($options, true);
        // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
        // other giftcards in the cart already
        $item->id = null;
        if (!empty($params['options_quantity'])) {
            $quantity             = 1;
            $item->quantity       = $quantity;
            $item->products_price = $price;
        } else {
            if (empty($params['qtyr'])) {
                $params['qtyr'] = 1;
            }
            if (!empty($params['base_price'])) $item->products_price = preg_replace("/[^0-9.]/", "", $params['base_price']);
            else $item->products_price = $product->base_price;
            $item->quantity       = $params['qtyr'];
        }

//        $this->displayForm('addToCart',$params);
//        return false;
        $item->options = serialize($options);
        $item->save();
        return true;
    }

    public function isAvailable() {
        return (($this->spacesLeft() != 0 || $this->quantity == 0) && $this->signup_cutoff > time()) ? true : false;
    }

    public function getControl($field, $name, $escape = '', $value = '', $adminedit = false) {

        $id      = $field->id;
        $control = $field->data;
        $type    = $field->type;
        $ctl     = unserialize($control);
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
            $ctl->default = $record['event'][$name];
        }
        if ($escape) {
            return addslashes($ctl->toHTML($ctl->caption, "event[$name]"));
        } else {
            if ($name == "email" && $adminedit == true) {
                return $ctl->toHTML($ctl->caption, "event[$name]", true);
            } else {
                return $ctl->toHTML($ctl->caption, "event[$name]");
            }
        }

    }

    function checkout() {
        // nothing to do for this callback.
    }

    public function removeItem($item) {
        return true;
    }

    public function getForm($form) {
        $dirs = array(
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

}

?>