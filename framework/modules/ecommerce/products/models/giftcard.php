<?php

##################################################
#
# Copyright (c) 2004-2020 OIC Group, Inc.
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

class giftcard extends expRecord {
    public $table = 'product';
    public $has_one = array('company');
    public $has_and_belongs_to_many = array('storeCategory');

    public $product_name = 'Gift Card';
    public $product_type = 'giftcard';
    public $requiresShipping = true;
    public $requiresBilling = true;
    public $isQuantityAdjustable = true;

    protected $attachable_item_types = array(
//        'content_expCats'=>'expCat',
//        'content_expComments'=>'expComment',
//        'content_expDefinableFields'=> 'expDefinableField',
        'content_expFiles' => 'expFile',
//        'content_expRatings'=>'expRating',
//        'content_expSimpleNote'=>'expSimpleNote',
//        'content_expTags'=>'expTag',
    );

    public function __construct($params = array(), $get_assoc = true, $get_attached = true) {
        parent::__construct($params, $get_assoc, $get_attached);
        $this->price = '';
    }

    /**
     * Called when updating product
     * @return bool
     */
    public function addContentToSearch() {
        global $db;

        // unlike controller->addContentToSearch() we only handle a single item;
        $exists = $db->selectObject('search', "original_id = " . $this->id . " AND ref_module='" . $this->classname . "' AND ref_type='" . $this->product_type . "'");

        $search = new stdClass();
        $search->ref_module  = $this->classname;
        $search->ref_type = $this->product_type;
        $search->category = $this->product_name;
        $search->posted = empty($this->created_at) ? null : $this->created_at;

        $search->original_id = $this->id;
        $search->title = $this->title;
        if (ecomconfig::getConfig('ecom_search_results') != '') {
            // we only want a picture if we are an ecom only type search since we don't include search images in other modules
            $search->title = (isset($this->expFile['mainimage'][0]) ? '<img src="' . PATH_RELATIVE . 'thumb.php?id=' . $this->expFile['mainimage'][0]->id . '&w=40&h=40&zc=1" style="float:left;margin-right:5px;" />' : '') . $search->title;
        }
        $search->view_link = str_replace(URL_FULL, '', makeLink(array('controller' => 'store', 'action' => 'showGiftCards')));
        $search->body = $this->body;
//        $search->keywords = $this->keywords;  //fixme there is no keywords field!!!

        if (empty($exists))
            $db->insertObject($search, 'search');
        else {
            $search->id = $exists->id;
            $db->updateObject($search, 'search');
        }

        return true;
    }

    public function cartSummary($item) {
        $view = new controllertemplate($this, $this->getForm('cartSummary'));
        $view->assign('product', $this);
        $view->assign('item', $item);

        // grab the message
        $message = expUnserialize($item->extra_data);
        $view->assign('message', $message);

        return $view->render();
    }

    function getPrice($orderitem = null) {
        return 1;
    }

    function addToCart($params, $orderid = null) {
        if (empty($params['options_shown'])) {  //get options and user input if needed
            $this->displayForm('addToCart', $params);
            return false;
        }

        if ($orderid == null) global $order;
        else $order = new order($orderid);

        expSession::set('params', $params);
        //get the configuration
        $config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        //FIXME we're missing the category, but we don't give gifts categories?
        $this->config = (empty($catConfig->config) || @$catConfig->config['use_global'] == 1) ? $config->config : $catConfig->config; //FIXME $catConfig doesn't exist
        $min_amount = $this->config['minimum_gift_card_purchase'];
        $custom_message_product = $this->config['custom_message_product'];

        if (empty($params['product_id'])) {
            flash('error', gt("Please specify the style of the gift card you want to purchase."));
            expHistory::back();
        }

        if (empty($params['card_amount']) && empty($params['card_amount_txt'])) {
            flash('error', gt("You need to specify the card amount for the gift card."));
            expHistory::back();
        } else {
            // eDebug($params, true);
            $item = new orderitem($params);
            $sm = $order->getCurrentShippingMethod();

            $item->shippingmethods_id = $sm->id;

            if (isset($params['card_amount_txt'])) {
                $params['card_amount_txt'] = expUtil::currency_to_float($params['card_amount_txt']);
            }

            if (!empty($params['card_amount_txt']) && $params['card_amount_txt'] > 0) {
                $item->products_price = expUtil::currency_to_float($params['card_amount_txt']);
            } else {
                $item->products_price = expUtil::currency_to_float($params['card_amount']);
            }

            if ($item->products_price < $min_amount) {
                flash('error', gt("The minimum amount of gift card is") . " " . $min_amount);
                expHistory::back();
            }

            $item->products_name = expCore::getCurrencySymbol() . $item->products_price . ' ' . $this->title . " Style Gift Card";

            if (!empty($params['toname'])) {
                $ed['To'] = isset($params['toname']) ? $params['toname'] : '';
            }

            if (!empty($params['fromname'])) {
                $ed['From'] = isset($params['fromname']) ? $params['fromname'] : '';
            }

            if (!empty($params['msg'])) {
                $ed['Message'] = isset($params['msg']) ? $params['msg'] : '';
                $item->products_price += $custom_message_product;
                $item->products_name = $item->products_name . " (with message)";
            }

            $item->extra_data = serialize($ed);

            // we need to unset the orderitem's ID to force a new entry..other wise we will overwrite any
            // other giftcards in the cart already
            $item->id = null;
            $item->quantity = $this->getDefaultQuantity();
            $item->save();
            return true;
        }
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

    public function hasUserInputFields() {
        return true;
    }

    public function hasOptions() {
        return false;
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

    function getBasePrice($orderitem = null) {
        return $this->products_price;
    }

    function getDefaultQuantity() {
        //TMP: Make this actually do something.
        return 1;
    }

    function getSurcharge() {
        return '';
    }

    public function removeItem($item) {
        return true;
    }

    function updateCart() {
        // nothing to do for this callback.
    }

    function checkout() {
        // nothing to do for this callback.
    }

    public function process($item, $affects_inventory=false) {

    }

}

?>