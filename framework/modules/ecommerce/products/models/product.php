<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
/** @define "BASE" "../../../../.." */

class product extends expRecord {
    public $table = 'product';

    public $has_one = array('company', 'product_status');
    public $has_many = array('optiongroup', 'model_alias', 'product_notes');
    public $has_many_self = array('childProduct');
    public $has_and_belongs_to_many = array('storeCategory');
    public $has_and_belongs_to_self = array('crosssellItem');
    public $get_assoc_for = array('optiongroup');

    public $product_name = 'Product';
    public $product_type = 'product';
    public $requiresShipping = true;
    public $requiresBilling = true;
    public $isQuantityAdjustable = true;

    public $quantity_display = array(
        0 => 'Always available even if out of stock.',
        1 => '* Available to order, but will display the message below if out of stock.',
        2 => '* Unavailable if out of stock and will display the message below.',
        3 => 'Show as &quot;Call for Price&quot;.'
    );

    public $active_display = array(
        0 => 'Active',
        1 => 'Inactive but findable.',
        2 => 'Inactive and disabled.',
    );
    public $active_display_desc = array(
        0 => '',
        1 => 'It will not be shown in product listings and the "Add to Cart" button is disabled but is still viewable directly. This can be advantageous with the page caching in the search engines.',
        2 => 'Trying to view this product will produce an error indicating this product is currently not available.',
    );

    public $quantity_discount_items_modifiers = array('gte' => 'Equal to or more than', 'gt' => 'More than');
    public $quantity_discount_amount_modifiers = array('$' => '$', '%' => '%');

    protected $attachable_item_types = array(
//        'content_expCats'=>'expCat',
        'content_expComments'=>'expComment',
//        'content_expDefinableFields'=> 'expDefinableField',
        'content_expFiles'      => 'expFile',
        'content_expRatings'    => 'expRating',
        'content_expSimpleNote' => 'expSimpleNote',
//        'content_expTags'=>'expTag',
    );

    public function __construct($params = array(), $get_assoc = true, $get_attached = true) {
//        global $db;

        parent::__construct($params, $get_assoc, $get_attached);
        $this->extra_fields = expUnserialize($this->extra_fields);
        $this->price = $this->getBasePrice();
        $this->user_input_fields = expUnserialize($this->user_input_fields);
        /*if (!empty($this->childProduct))
        {
            foreach($this->childProduct as &$child)
            {
                $child->expFile = $this->expFile;
            }
        } */
        if (!empty($this->parent_id)) {
            $parent = new product($this->parent_id, false, true);
            //eDebug($parent->expFile);
            $this->expFile = $parent->expFile;
            //eDebug($this);
        }

        if (!empty($this->meta_fb))
            $this->meta_fb = expUnserialize($this->meta_fb);
        if (!empty($this->meta_fb['fbimage']) && !empty($this->expFile['fbimage'][0])) {
            $this->meta_fb['fbimage'][0] = $this->expFile['fbimage'][0];
//            unset($this->expFile['fbimage']);
        }
        if (!empty($this->meta_tw))
            $this->meta_tw = expUnserialize($this->meta_tw);
        if (!empty($this->meta_tw['twimage']) && !empty($this->meta_tw['twimage'][0]))
            $this->meta_tw['twimage'][0] = new expFile($this->meta_tw['twimage'][0]);

        //sort the children by child_rank
        if ($this->hasChildren()) {
            if (isset($this->childProduct)) usort($this->childProduct, array("product", "sortChildren"));
        }
    }

    function incrementQuantity($oldval) {
        return ++$oldval;
    }

    function updateQuantity($newval) {
        return $newval;
    }

    function getBasePrice($orderitem = null) {
        if ($this->use_special_price) {
            return $this->special_price;
        } else {
            return $this->base_price;
        }
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

    function getDefaultQuantity() {
        //FIXME Make this actually do something.
        return 1;
    }

    function updateCart() {
        // nothing to do for this callback.
    }

    function checkout() {
        // nothing to do for this callback.
    }

    /*function hasOptions() {
        global $db;
        $og = $db->selectObject('option', 'enable=1 AND product_id='.$this->id);
        return empty($og) ? false : true;
    }*/

    /* function preAddToCart($params) {
         //if has options, or if has user input fields, we retrn false so cart can show the addToCart form
         //otherwise we send to addToCart directly, which double checks things
         if (count($this->optiongroup) > 0) return false;
         if (count($this->user_input_fields) >0) return false;

         $this->addToCart($this->params);
     } */

    function addToCart($params, $orderid = null) {
        // eDebug($params,true);
        if ($orderid == null) global $order;
        else $order = new order($orderid);
        //eDebug($this);
        //eDebug($params,true);
        $params['qty'] = isset($params['qty']) ? $params['qty'] : 1;
        if (!isset($params['product_type'])) $params['product_type'] = 'product';

        $params['error'] = '';

        if (empty($params['children'])) {
            //$oiObj = new orderitem();
            //$oi = $oiObj->find('all','product_id='.$this->id);
            $qCheck = 0; //$this->quantity;
            //if (!empty($oi))
            //{
            foreach ($order->orderitem as $orderItem) {
                //eDebug($orderItem->quantity);
                if ($orderItem->product_id == $this->id) $qCheck += $orderItem->quantity;
            }
            //}
            $qty = $params['qty'];
            if (($this->quantity - $qCheck) < $qty) {
                if ($this->availability_type == 2) {
                    flash('error', $this->title . ' ' . gt('only has') . ' ' . $this->quantity . ' ' . gt('on hand. You can not add more than that to your cart.'));
                    //return false;
                    expHistory::back();
                }
            }
            //check minimum quantity
            if (($qty + $qCheck) < $this->minimum_order_quantity) {
                flash('message', $this->title . ' ' . gt('has a minimum order quantity of') . ' ' . $this->minimum_order_quantity . '. ' . gt('The quantity has been adjusted accordingly.'));
                $params['qty'] += $this->minimum_order_quantity - ($qty + $qCheck);
                $qty = $params['qty'];
            }
            //FIXME adjust multiple quantity here
        } else {
            foreach ($params['children'] as $idKey => $childQty) {
                $cprod = new childProduct($idKey);
                //$oiObj = new orderitem();
                //$oi = $oiObj->find('all','product_id='.$idKey);
                $qCheck = 0; //$cprod->quantity;
                //if (!empty($oi))
                //{
                foreach ($order->orderitem as $orderItem) {
                    //eDebug($orderItem->quantity);
                    if ($orderItem->product_id == $idKey) $qCheck += $orderItem->quantity;
                }
                //}
                /*eDebug("Qty:".$childQty);
                eDebug("Product Quantity:".$cprod->quantity);
                eDebug("Qcheck:".$qCheck,true);*/
                if (($cprod->quantity - $qCheck) < $childQty) {
                    if ($cprod->availability_type == 2) {
                        flash('error', $this->title . ' - ' . $cprod->model . ' ' . gt('only has') . ' ' . $cprod->quantity . ' ' . gt('on hand. You can not add more than that to your cart.'));
                        //return false;
                        expHistory::back();
                    }
                }
                //check minimum quantity
                if (($childQty + $qCheck) < $cprod->minimum_order_quantity) {
                    flash('message', $cprod->title . ' ' . gt('has a minimum order quantity of') . ' ' . $cprod->minimum_order_quantity . '. ' . gt('The quantity has been adjusted accordingly.'));
                    $params['children'][$idKey] += $cprod->minimum_order_quantity - ($childQty + $qCheck);
                    //$qty = $params['qty'];
                }
                //FIXME adjust multiple quantity here for child products???
            }
        }

        $optional_input = false;
        if ($this->hasOptions()) {
            if (empty($params['options_shown'])) {
                $params['option_error'] = true;
            } else {
                $needs_input = false;
                foreach ($this->optiongroup as $og) {
                    if ($og->required) {
                        $err = true;
                        if (!empty($params['options'][$og->id])) {
                            foreach ($params['options'][$og->id] as $opt) {
                                //eDebug($opt,true);
                                //make sure at least one is not empty to cover both single and mult selects
                                if (!empty($opt)) {
                                    $err = false;
                                }
                            }
                        }
                        if ($err) {
                            $params['error'] .= gt('You must select an option from the') . ' ' . $og->title . ' ' . gt('options below before you can add this to your cart.') . ' <br/>';
                            $params['option_error'] = true;
                        }
                    }
                    //eDebug($og->title . ":" .$og->required);
                    if ($og->input_needed) {
                        $optional_input = true;
                        foreach ($params['options'][$og->id] as $opt) {
                            //see if the selected option requires user input
                            $opt_input = new option($opt);
                            if (!empty($opt_input->show_input)) {
                                $needs_input = true;
                            }
                        }
                    }
                }
            }
        }
        //check user input fields
        //$this->user_input_fields = expUnserialize($this->user_input_fields);
        //eDebug($this,true);
//        if (!empty($this->user_input_fields)) foreach ($this->user_input_fields as $uifkey => $uif) {
        $user_input_info = array();
        if ($this->hasUserInputFields()) {
            if (($optional_input && $needs_input) | (!$optional_input && empty($params['input_shown']))) {
                $params['input_error'] = true;
            } else {
                foreach ($this->user_input_fields as $uifkey => $uif) {
                    if ($uif['is_required'] || (!$uif['is_required'] && strlen($params['user_input_fields'][$uifkey]) > 0)) {
                        if (strlen($params['user_input_fields'][$uifkey]) < $uif['min_length']) {
                            //flash('error', 'test');
                            //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));
                            $params['error'] .= $uif['name'] . ' ' . gt('field has a minimum requirement of') . ' ' . $uif['min_length'] . ' ' . gt('characters.') . '<br/>';
                        } else {
                            if (strlen(
                                    $params['user_input_fields'][$uifkey]
                                ) > $uif['max_length'] && $uif['max_length'] > 0
                            ) {
                                //flash('error', );
                                //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));
                                $params['error'] .= $uif['name'] . ' ' . gt('field has a maximum requirement of') . ' ' . $uif['max_length'] . ' ' . gt('characters.') . '<br/>';
                            }
                        }
                    }
                    $user_input_info[] = array($uif['name'] => $params['user_input_fields'][$uifkey]);
                }
            }
        }

        if ($orderid == null) {
            if ($params['error'] != '' || !empty($params['option_error']) || !empty($params['input_error'])) {
                $this->displayForm('addToCart', $params);
                return false;
            }
        } else {
            if ($params['error'] != '' || !empty($params['option_error']) || !empty($params['input_error'])) {
                $this->displayForm('addToOrder', $params);
                return false;
            }
        }

        if (empty($params['children'])) {
            $this->createOrderItem($this, $params, $user_input_info, $orderid);
        } else {
            foreach ($params['children'] as $ckey => $cqty) {
                $params['qty'] = 1;
                for ($qty = 1; $qty <= $cqty; $qty++) {
                    $child = new $params['product_type']($ckey); //$params['prod-quantity'][$ckey];
                    $this->createOrderItem($child, $params, $user_input_info, $orderid);

                    /*foreach($this->childProduct as $child)
                    {
                        if ($child->id == $ckey) $this->createOrderItem($child, $params, $user_input_info);
                        break;
                    }*/
                }

            }
            //die();
        }
        return true;
    }

    function displayForm($form, $params) {
        // eDebug($form, true);
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

    private function createOrderItem($product, $params, $user_input_info, $orderid) {
        //eDebug($params,true);
        global $db;
        if ($orderid == null) global $order;
        else $order = new order($orderid);

        $price = $product->getBasePrice();
        $options = array();

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
                    $price = $price + $cost;
                    $options[] = array($selected_option->id, $selected_option->title, $selected_option->modtype, $selected_option->updown, $selected_option->amount, $selected_option->optionweight);
                }
            }
        }

        //eDebug($params,true);
        // add the product to the cart.
        if ($orderid != null) {
            if (empty($params['children'])) $price = $params['products_price'];
            else $price = $params['prod-price'][$product->id];
        }

        $params['product_id'] = $product->id;
        $params['options'] = serialize($options);
        $params['products_price'] = $price;
        $params['user_input_fields'] = serialize($user_input_info);
        $params['orderid'] = $orderid;
        /*$params['products_status'] =
        $params['products_warehouse_location'] =
        $params['products_model'] = $product->model;*/
        $item = new orderitem($params);
        //eDebug($item);
        $item->products_price = $price;

        /*eDebug($item->quantity);
        eDebug($params);
        eDebug($product->minimum_order_quantity);*/

        $item->quantity += is_numeric($params['qty']) && $params['qty'] >= $product->minimum_order_quantity ? $params['qty'] : $product->minimum_order_quantity;
        //FIXME adjust multiple quantity here
        if ($item->quantity < 1) $item->quantity = 1;
        // eDebug($item->quantity,true);
        //eDebug($params);
        //eDebug($item, true);
        //eDebug($item, true);
        $item->options = serialize($options);
        $item->user_input_fields = $params['user_input_fields'];
        $item->products_status = $product->product_status->title;
        if ($product->parent_id == 0 || $product->warehouse_location != '') {
            //eDebug("here1",true);
            $item->products_warehouse_location = $product->warehouse_location;
        } else {
            $item->products_warehouse_location = $db->selectValue('product', 'warehouse_location', 'id=' . $product->parent_id);
        }

        $item->products_model = $product->model;

        $sm = $order->getCurrentShippingMethod();
        $item->shippingmethods_id = $sm->id;
        //eDebug($item,true);
        $item->save();
        return;
    }

    public function removeItem($item) {
        return true;
    }

    /**
     * Process submitted order and update product inventory/quantity
     *
     * @param $item
     * @param bool $affects_inventory
     */
    public function process($item, $affects_inventory=false) {
        global $db;

        if ($affects_inventory) $this->quantity = $this->quantity - $item->quantity;
        if ($this->quantity < 0) $this->quantity = 0;
        //$this->save();
        $pobj = new stdClass();
        $pobj->id = $this->id;
        $pobj->quantity = $this->quantity;
        $db->updateObject($pobj, 'product', 'id=' . $this->id);
    }

    public function optionDropdown($key, $display_price_as) {
        $items = array();

        foreach ($this->optiongroup as $group) {
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

    //FIXME was this replaced by orderitem->getFormattedExtraData() ?  It's not used
    public function formatExtraData($item) {
        $viewname = $this->getForm('formatExtraData');
        if (!$viewname) return null;

        $view = new controllertemplate($this, $viewname);
        $view->assign('extra_data', expUnserialize($item->extra_data));
        return $view->render();
    }

    public function storeListing() {
        $viewname = $this->getForm('storeListing');
        if (!$viewname) return null;

        $view = new controllertemplate($this, $viewname);
        $view->assign('listing', $this);
        return $view->render();
    }

    public function cartSummary($item) {
        $viewname = $this->getForm('cartSummary');
        if (!$viewname) return null;

        $view = new controllertemplate($this, $viewname);
        $view->assign('product', $this);
        $view->assign('item', $item);

        // grab the options
        $options = expUnserialize($item->options);
        $view->assign('options', $options);

        return $view->render();
    }

    public function getSEFURL() {
        if (!empty($this->sef_url)) return $this->sef_url;
        $parent = new product($this->parent_id, false, false);
        return $parent->sef_url;
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
        if (bs2()) {
            $vars = array(
                '.bootstrap',
                '',
            );
        } elseif (bs3(true)) {
            $vars = array(
                '.bootstrap3',
                '.bootstrap',
                '',
            );
        } elseif (bs4(true)) {
            $vars = array(
                '.bootstrap4',
                '.bootstrap3',
                '.bootstrap',
                '',
            );
        } elseif (bs5(true)) {
            $vars = array(
                '.bootstrap5',
                '.bootstrap4',
                '.bootstrap3',
                '.bootstrap',
                '',
            );
        } else {
            $vars = array(
                '',
            );
        }

        foreach ($vars as $var) {
            foreach ($dirs as $dir) {
                if (file_exists($dir . $form . $var . '.tpl')) return $dir . $form . $var . '.tpl';
            }
        }

        return false;
    }

    public function getViewDir() {
        return 'modules/ecommerce/products/views/';
    }

    public function beforeSave() {
        if (is_array($this->extra_fields)) $this->extra_fields = serialize($this->extra_fields);
        parent::beforeSave();
        return true;
    }

    public function afterDelete() {
        global $db;

        // delete all child products
        if ($this->parent_id == 0) {
            $children = $this->find('all', 'parent_id=' . $this->id);
            foreach ($children as $child) {
                $child->delete();
            }

            // delete product storeCategory connections
            $db->delete('product_storeCategories', 'product_id=' . $this->id . ' AND product_type="' . $this->product_type . '"');

            // delete product notes
            $db->delete('product_notes', 'product_id=' . $this->id);

            // delete product options
            $db->delete('option', 'product_id=' . $this->id . " AND optiongroup_id IN (SELECT id from " . $db->tableStmt('optiongroup') . " WHERE product_id=" . $this->id . ")");

            // delete product option groups
            $db->delete('optiongroup', 'product_id=' . $this->id);

            // delete model aliases
            $db->delete('model_aliases', 'product_id=' . $this->id);

            // delete related product connections
            $db->delete('crosssellItem_product', 'product_type="' . $this->product_type . '" AND (product_id=' . $this->id . ' OR crosssellItem_id=' . $this->id . ')');

            // delete search index entry
            $db->delete('search', "ref_type='" . $this->product_type . "' AND ref_module='" . $this->classname . "' AND original_id = " . $this->id);
        } else {
            // if the last child product is deleted, delete the parent
            if (!$db->countObjects($this->table, 'parent_id=' . $this->parent_id)) {
                $parent = new product($this->parent_id);
                $parent->delete();  // do it this way to get other tables artifacts above
            }
        }
    }

    protected function getAttachableItems() {
        $this->classname = $this->product_type;
        parent::getAttachableItems();
    }

    public function hasOptions() {
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

    public function hasUserInputFields() {
        //eDebug($this->user_input_fields);
        if (!empty($this->user_input_fields) && count($this->user_input_fields) > 0) return true;
        else return false;
    }

    public function isChild() {
        if ($this->parent_id == 0) return false;
        else return true;
    }

    //this is not guaranteed to be correct if the object was instantiated withOUT associated items,
    //so be careful where you call it
    public function hasChildren() {
        global $db;
        if (isset($this->childProduct)) {
            if (!empty($this->childProduct) && count($this->childProduct) == 0) return false;
            else return true;
        } else {
            //$sql = "SELECT id from " . $db->prefix . "product WHERE parent_id=" . $this->id;
            $count = $db->countObjects("product", "parent_id=" . $this->id);
            //eDebug($count);
            //$count = $db->queryRows($sql);
            if ($count > 0) return true;
            else return false;
        }
    }

    static function sortChildren($a, $b) {
        if ($a->child_rank < $b->child_rank) return -1;
        else if ($a->child_rank > $b->child_rank) return 1;
        else if ($a->child_rank == $b->child_rank) return 0;
    }

    public function saveCategories($catArray, $catRankArray = null, $id = '', $product_type = '') {
        global $db;

        if (empty($id)) {
            $id = $this->id;
        }

        if (empty($product_type)) {
            $product_type = $this->product_type;
        }

        // if there are no categories specified we'll set this to the 0 category..meaning uncategorized'
        //eDebug($this->params['storeCategory']);
        if (empty($catArray)) {
            $db->delete('product_storeCategories', 'product_id=' . $id);
//            $catArray = array(0);
            $assoc = new stdClass();
            $assoc->storecategories_id = 0;
            $assoc->product_id = $id;
            $assoc->product_type = $product_type;
            $assoc->rank = 0;
            $db->insertObject($assoc, 'product_storeCategories');
        } else {
            //we need to preserve the rank, so we need to check if we are in cateogories:
            $cats = $db->selectArrays('product_storeCategories', 'product_id=' . $id);
            $curCats = array();
            foreach ($cats as $c) {
                $curCats[] = $c['storecategories_id'];
            }
            //eDebug($curCats); //445 //1303D //1315
            //if it's in a category already we leave it
            //flip check the arrays - if not in one, we add. if vice versa, we delete:
            foreach ($catArray as $cat) {
                $assoc = new stdClass();
                if (!in_array($cat, $curCats)) {
                    //create new
                    $assoc->storecategories_id = $cat;
                    $assoc->product_id = $id;
                    $assoc->product_type = $product_type;
                    if ($catRankArray != null && isset($catRankArray[$cat]) && $catRankArray[$cat] != '' && $catRankArray[$cat] != '0') {
                        $assoc->rank = $catRankArray[$cat];
                    } else {
                        $assoc->rank = $db->max('product_storeCategories', 'rank', null, 'storecategories_id=' . $cat) + 1;
                    }

                    $db->insertObject($assoc, 'product_storeCategories');
                    //eDebug("Adding " . $cat);
                } else {
                    //update old
                    $assoc->storecategories_id = $cat;
                    $assoc->product_id = $id;
                    $assoc->product_type = $product_type;
                    if ($catRankArray != null && isset($catRankArray[$cat]) && $catRankArray[$cat] != '' && $catRankArray[$cat] != '0') {
                        $assoc->rank = $catRankArray[$cat];
                    } else {
                        $assoc->rank = $db->selectValue('product_storeCategories', 'rank', 'storecategories_id=' . $cat . ' AND product_id=' . $id);
                    }
                    $db->updateObject($assoc, 'product_storeCategories', 'product_id=' . $id . ' AND storecategories_id=' . $cat);
                    //eDebug("Adding " . $cat);
                }
            }
            foreach ($curCats as $delcat) {
                if (!in_array($delcat, $catArray)) {
                    $db->delete('product_storeCategories', 'product_id=' . $id . ' AND storecategories_id=' . $delcat);
                    //$db->decrement('product_storeCategories', 'rank', 1, ' AND storecategories_id=' . $delcat);
                }
            }
            //die();
        }
    }

    /**
     * Called when updating product
     * @return bool
     */
    public function addContentToSearch() {
        global $db;

        // unlike controller->addContentToSearch() we only handle a single item;
        // only add top level products, not children
        if ($this->parent_id != 0)
            $product = new product($this->parent_id);
        else
            $product = $this;

        $exists = $db->selectObject('search', "original_id = " . $product->id . " AND ref_module='" . $product->classname . "' AND ref_type='" . $product->product_type . "'");

        $search = new stdClass();
        $search->category = $product->product_name;
        $search->ref_module  = $product->classname;
        $search->ref_type = $product->product_type;
        $search->posted = empty($product->created_at) ? null : $product->created_at;

        $search->original_id = $product->id;
        $search->title = $product->title;
        if (ecomconfig::getConfig('ecom_search_results') != '') {
            // we only want a picture if we are an ecom only type search since we don't include search images in other modules
            $search->title = (isset($product->expFile['mainimage'][0]) ? '<img src="' . PATH_RELATIVE . 'thumb.php?id=' . $product->expFile['mainimage'][0]->id . '&w=40&h=40&zc=1" style="float:left;margin-right:5px;" />' : '') . $search->title;
        }
        $search->view_link = str_replace(URL_FULL, '', makeLink(array('controller' => 'store', 'action' => 'show', 'title' => $product->sef_url)));

        $search->body = $product->body;
        $children_titles = $db->selectColumn('product','title','parent_id=' . $product->id);
        $cnt = count($children_titles);
        if ($cnt) {
            $search->body .= " - ";
            $loopcnt = 1;
            foreach ($children_titles as $child) {
                $search->body .= $child;
                if ($loopcnt < $cnt)
                    $search->body .= " - ";
                $loopcnt++;
            }
        }

        $search->keywords = $product->model;
        $children_models = $db->selectColumn('product','model','parent_id=' . $product->id);
        $cnt = count($children_models);
        if ($cnt) {
            $search->keywords .= ", ";
            $loopcnt = 1;
            foreach ($children_models as $model) {
                $search->keywords .= $model;
                if ($loopcnt < $cnt)
                    $search->keywords .= ", ";
                $loopcnt++;
            }
        }

        if (empty($exists))
            $db->insertObject($search, 'search');
        else {
            $search->id = $exists->id;
            $db->updateObject($search, 'search');
        }

        return true;
    }


    static public function canView($id) {
        global $db;

        if ($db->selectValue('product', 'active_type', 'id=' . $id) == 2) return false;

        return true;
        //check if category is
    }

    public function paginationCallback(&$item) {
        // add passed properties to the object and pass back an instantiated object
        $item = (object) array_merge((array) $this, (array) $item);
        $item = expCore::cast($item, 'product');
    }

    public function update($params = array()) {
        global $db;

        if (empty($params['general']['companies_id'])) {
            $params['general']['companies_id'] = 0;
        }
        if (empty($params['general']['parent_id'])) {
            $params['general']['parent_id'] = 0;
        }
        if ($this->product_type !== 'product') {
            parent::update($params);
            return;
        }

        //Get the product
        if (isset($params['id'])) {
            $product = $db->selectObject('product', 'id =' . $params['id']);
            //Get product files
            $product->expFile = $this->getProductFiles($params['id']);
            // eDebug($product, true);
        }

        if (empty($product))
            $product = new stdClass();

        $tab_loaded = !empty($params['tab_loaded']) ? $params['tab_loaded'] : array();
        //check if we're saving a newly copied product and if we create children also
        $originalId = isset($params['original_id']) && isset($params['copy_children']) ? $params['original_id'] : 0;
        $originalModel = isset($params['original_model']) && isset($params['copy_children']) ? $params['original_model'] : 0;

        if (!empty($product->parent_id)) $product->sef_url = ''; //if child, set sef_url to nada

        //Tabs not directly being saved in the product table and need some special operations
        $tab_exceptions = array(
            'categories',
            'options',
            'related',
            'userinput',
            'extrafields',
            'model',
            'notes',
            'facebook'
        );

        foreach ($tab_loaded as $tab_key => $tab_item) {
            if (!in_array($tab_key, $tab_exceptions)) {
                foreach ($params[$tab_key] as $key => $item) {
                    $product->$key = $item;
                }
            }
        }

        if (isset($tab_loaded['images'])) {
            $product->expFile = $params['expFile'];
        }

        if (!empty($params['shipping']['required_shipping_calculator_id'])) {
            if ($params['shipping']['required_shipping_calculator_id'] > 0) {
                $product->required_shipping_method = $params['required_shipping_methods'][$params['shipping']['required_shipping_calculator_id']];
            }
        } else {
            $params['shipping']['required_shipping_calculator_id'] = 0;
        }

        if (isset($tab_loaded['userinput'])) {
            //User Input fields Tab
            if (isset($params['user_input_use']) && is_array($params['user_input_use'])) {
                foreach ($params['user_input_use'] as $ukey => $ufield) {
                    $user_input_fields[] = array('use' => $params['user_input_use'][$ukey], 'name' => $params['user_input_name'][$ukey], 'is_required' => $params['user_input_is_required'][$ukey], 'min_length' => $params['user_input_min_length'][$ukey], 'max_length' => $params['user_input_max_length'][$ukey], 'description' => $params['user_input_description'][$ukey]);
                }
                $product->user_input_fields = serialize($user_input_fields);
            } else {
                $product->user_input_fields = serialize(array());
            }
        }

        if (isset($tab_loaded['extrafields'])) {
            //Extra Field Tab
            foreach ($params['extra_fields_name'] as $xkey => $xfield) {
                if (!empty($xfield)) {
                    $extra_fields[] = array('name' => $xfield, 'value' => $params['extra_fields_value'][$xkey]);
                }
            }
            if (is_array($extra_fields)) {
                $product->extra_fields = serialize($extra_fields);
            } else {
                unset($product->extra_fields);
            }
        }

        if (isset($tab_loaded['facebook'])) {
            //Facebook Tab
            foreach ($params['facebook'] as $fbkey => $fbfield) {
                if (!empty($fbfield)) {
                    $fb_meta[$fbkey] = $params['facebook'][$fbkey];
                }
            }
            if (is_array($fb_meta)) {
                $product->meta_fb = serialize($fb_meta);
            } else {
                unset($product->meta_fb);
            }
        }

        //Check if we are copying and not just editing product
        if (isset($params['original_id'])) {
            // eDebug($product->id, true);
            unset($product->id);
//            unset($product->sef_url);
            $product->sef_url = $params['sef_url'];
            $product->original_id = $params['original_id'];
            // eDebug($product, true);
        }
        // create/update our product
        parent::update($product);
        //note now $this is our new product

        if (isset($tab_loaded['options'])) {
            //Option Group Tab
            $product->show_options = $params['options']['show_options'];
            $product->segregate_options = $params['options']['segregate_options'];
            parent::update($product);
            if (!empty($params['optiongroups'])) {
                foreach ($params['optiongroups'] as $title => $group) {
                    if (isset($params['original_id']) && $params['original_id'] != 0) $group['id'] = ''; //for copying products

                    $optiongroup = new  optiongroup($group);
                    $optiongroup->product_id = $this->id;
                    $optiongroup->save();

                    foreach ($params['optiongroups'][$title]['options'] as $opt_title => $opt) {
                        if (isset($params['original_id']) && $params['original_id'] != 0) $opt['id'] = ''; //for copying products

                        $opt['product_id'] = $this->id;
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
        }

        if (isset($tab_loaded['categories'])) {
            $this->saveCategories($params['storeCategory'], null, $this->id, $this->classname);
        }

        // Copy Children Products if needed
        if (!empty($originalId) && !empty($params['copy_children'])) {
//            $origProd = new $product->product_type($originalId); //FIXME $product_type is not set, changed to $product->product_type
            $origProd = new $this->product_type($originalId);
            $children = $origProd->find('all', 'parent_id=' . $originalId);
            foreach ($children as $child) {
                unset($child->id);
//                $child->parent_id = $product->id;
//                $child->title = $product->title;
                $child->parent_id = $this->id;
                $child->title = $this->title;
                $child->sef_url = '';
                if (isset($params['adjust_child_price']) && isset($params['new_child_price']) && is_numeric($params['new_child_price'])) {
                    $child->base_price = $params['new_child_price'];
                }

                if (!empty($originalModel)) {
//                    $child->model = str_ireplace($originalModel, $product->model, $child->model);
                    $child->model = str_ireplace($originalModel, $this->model, $child->model);
                }
                $child->save();
            }
        }

        if (isset($tab_loaded['related'])) {
            //Related Products Tab
            $db->delete('crosssellItem_product', 'product_id=' . $this->id);
            foreach ($params['relatedProducts'] as $prodid) {
                $ptype = new product($prodid);
                $tmp = new stdClass();
                $tmp->product_id = $this->id;
                $tmp->crosssellItem_id = $prodid;
                $tmp->product_type = $ptype->product_type;
                $db->insertObject($tmp, 'crosssellItem_product');

                if (isset($params['relateBothWays'][$prodid])) {
                    $tmp->crosssellItem_id = $this->id;
                    $tmp->product_id = $prodid;
                    $tmp->product_type = $ptype->product_type;
                    $db->insertObject($tmp, 'crosssellItem_product');
                }
            }
        }

        // Copy related products if needed
        if (!empty($originalId) && !empty($params['copy_related'])) {
            $relprods = $db->selectObjects('crosssellItem_product', "product_id=" . $params['original_id']);
            foreach ($relprods as $prodid) {
                $prodid->product_id = $this->id;
                $db->insertObject($prodid, 'crosssellItem_product');

                // now relate both ways
                $tmp = new stdClass();
                $tmp->product_id = $prodid->crosssellItem_id;
                $tmp->crosssellItem_id = $prodid->product_id;
                $tmp->product_type = $prodid->product_type;
                $db->insertObject($tmp, 'crosssellItem_product');
            }
        }
    }

    private function getProductFiles($id = '') {
        global $db;

        if (empty($id)) return false;

        $expFilesObj = $db->selectObjects("content_expFiles", "content_id = {$id} AND content_type = 'product'");

        $files = array();
        foreach ($expFilesObj as $item) {
            $files[$item->subtype][] = $item->expfiles_id;  //FIXME not sure why we are creating 2 array entries??
            $files[$item->subtype][] = "expFile[{$item->subtype}][]";
        }

        return $files;
    }
}

?>