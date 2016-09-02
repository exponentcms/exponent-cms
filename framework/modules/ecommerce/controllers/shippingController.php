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
 * @subpackage Controllers
 * @package Modules
 */

class shippingController extends expController {
    protected $add_permissions = array(
        'toggle'=>'Enable/Disable Options'
    );

    static function displayname() { return gt("e-Commerce Shipping Controller"); }
    static function description() { return ""; }
	static function hasSources() { return false; }
    static function hasContent() { return false; }

    /**
     * Ajax method to return a shipping calculator object within a shipping object
     */
	function selectShippingCalculator() {
	    global $db;

		$shipping = new shipping();
		
		// update the shippingmethod
		$shipping->shippingmethod->update(array('shippingcalculator_id'=>$this->params['shippingcalculator_id'],'option'=>null,'option_title'=>null));
		
		// fetch the calculator
		$calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->params['shippingcalculator_id']);
		//eDebug($this->params['shippingcalculator_id']);
		//eDebug($calcname);
		$shipping->calculator = new $calcname($this->params['shippingcalculator_id']);
		
		$ar = new expAjaxReply(200, 'ok', $shipping, array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}

    /**
     * Ajax method to select/update a shipping method
     */
	function selectShippingOption() {
	    global $order; //FIXME we do NOT want the global $order

		$shipping = new shipping();
		$id = $this->params['option'];
		$rates = $shipping->calculator->getRates($order);  //FIXME a lot of work just to get one set of data since we'll be doing it again for cart/checkout redisplay...maybe cache???
		$rate = $rates[$id];
		$shipping->shippingmethod->update(array('option'=>$id,'option_title'=>$rate['title'],'shipping_cost'=>$rate['cost']));
		$ar = new expAjaxReply(200, 'ok', array('title'=>$rate['title'], 'cost'=>number_format($rate['cost'], 2)), array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}

    /**
     * Ajax method to set a shipping address
     */
	function setAddress() {
		$shipping = new shipping();
		$shipping->shippingmethod->setAddress($this->params['shipping_address']);
		$shipping->refresh();
		$ar = new expAjaxReply(200, 'ok', new address($shipping->shippingmethod->addresses_id), array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}
	
    /**
     * Ajax method to set a shipping 'gift' message
     */
	function leaveMessage() {
		if (!empty($this->params['shippingmessageid'])) {
		    $sm = new shippingmethod($this->params['shippingmessageid']);
		    
		    if ($this->params['nosave'] == false) {
		        $sm->to = empty($this->params['shpmessageto']) ? null : $this->params['shpmessageto'];   
		        $sm->from = empty($this->params['shpmessagefrom']) ? null : $this->params['shpmessagefrom']; 
		        $sm->message = empty($this->params['shpmessage']) ? null : $this->params['shpmessage']; 
		        $sm->save();
		    }
		}
		
		$ar = new expAjaxReply(200, 'ok', $sm, array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();		
	}
	
	function renderOptions() {  //FIXME do we ever call this?
//	    global $db, $order;
        global $order; //FIXME we do NOT want the global $order

	    $shipping = new shipping();
        //FIXME perhaps check for cached rates if calculator didn't change???
        $shipping->pricelist = $shipping->calculator->getRates($order);
        $shipping->multiple_carriers = $shipping->calculator->multiple_carriers;

        if (empty($shipping->shippingmethod->option)) {
            if ($shipping->multiple_carriers) {
                $opt = current($shipping->pricelist[0]);
            } else {
                $opt = current($shipping->pricelist);
            }
        } else {
            if ($shipping->multiple_carriers) {
                $opt = $shipping->pricelist[0][$shipping->shippingmethod->option];
            } else {
                $opt = $shipping->pricelist[$shipping->shippingmethod->option];
            }
        }
        
        $shipping->shippingmethod->update(array('option'=>$opt['id'],'option_title'=>$opt['title'],'shipping_cost'=>$opt['cost']));
        
        assign_to_template(array(
            'shipping'=>$shipping,
            'order'=>$order
        ));
	}
	
    /**
     * Ajax method to return a shipping calculator object within a shipping object
     */
	function listPrices() {
	    $shipping = new shipping();  //FIXME this model has no listPrices() method???
	    $ar = new expAjaxReply(200, 'ok', $shipping->listPrices(), array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}
	
	function manage() {
	    global $db;
	    
	    expHistory::set('manageable', $this->params);
	    $calculators = array();
        $dir = BASE."framework/modules/ecommerce/shippingcalculators";
        $default = false;
        $on = false;
        if (is_readable($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (is_file("$dir/$file") && substr("$dir/$file", -4) == ".php") {
                    include_once("$dir/$file");
                    $classname = substr($file, 0, -4);
                    $id = $db->selectValue('shippingcalculator', 'id', 'calculator_name="'.$classname.'"');                    
                    if (empty($id)) {
                        $calcobj = new $classname($this->params);
                        if ($calcobj->isSelectable() == true) {                            
                            $calcobj->update(array('title'=>$calcobj->name(),'body'=>$calcobj->description(),'calculator_name'=>$classname,'enabled'=>false));
                        }
                    } else {
                        $calcobj = new $classname($id);
                    }
                    $calculators[] = $calcobj;
                    if (!$default) $default = $calcobj->is_default;
                    if (!$on && $calcobj->enabled) $on = $calcobj->id;
                }
            }
            if (!$default && $on) {
                $db->toggle('shippingcalculator', 'is_default', 'id='.$on);
                foreach ($calculators as $idx=>$calc) {
                    if ($calc->id == $on) $calculators[$idx]->is_default = 1;
                }
            }
        }
        assign_to_template(array(
            'calculators'=>$calculators
        ));
	}
	
		
	public function toggle() {
	    global $db;

	    if (isset($this->params['id'])) $db->toggle('shippingcalculator', 'enabled', 'id='.$this->params['id']);
        //FIXME we need to ensure our default calculator is still active...not sure this does it
        if ($db->selectValue('shippingcalculator', 'is_default', 'id='.$this->params['id']) && !$db->selectValue('shippingcalculator', 'enabled', 'id='.$this->params['id'])) {
            $db->toggle('shippingcalculator', 'is_default', 'id='.$this->params['id']);
        }

        $calc = new shippingcalculator($this->params['id']);
        $calc_obj = new $calc->calculator_name();
        if ($calc_obj->hasConfig() && empty($calc->config)) {
            flash('message', $calc_obj->name().' '.gt('requires configuration. Please do so now.'));
            redirect_to(array('controller'=>'shipping', 'action'=>'configure', 'id'=>$calc->id));
        }
	    expHistory::back();
	}

    public function toggle_default() {
  	    global $db;

        $db->toggle('shippingcalculator',"is_default",'is_default=1');
  	    if (isset($this->params['id'])) {
            $active = $db->selectObject('shippingcalculator',"id=".$this->params['id']);
            $active->is_default = 1;
            $db->updateObject($active,'shippingcalculator',null,'id');
        }
        if ($db->selectValue('shippingcalculator', 'is_default', 'id='.$this->params['id']) && !$db->selectValue('shippingcalculator', 'enabled', 'id='.$this->params['id'])) {
            $db->toggle('shippingcalculator', 'enabled', 'id='.$this->params['id']);
        }
  	    expHistory::back();
  	}

    public function configure() {
        global $db;

        if (empty($this->params['id'])) return false;
        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->params['id']);
        $calc = new $calcname($this->params['id']);
        assign_to_template(array(
            'calculator'=>$calc,
            'title'=>static::displayname()
        ));
    }
    
    public function saveconfig() {
        global $db;

        if (empty($this->params['id'])) return false;
        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->params['id']);
        $calc = new $calcname($this->params['id']);
        $conf = serialize($calc->parseConfig($this->params));        
        $calc->update(array('config'=>$conf));
        expHistory::back();
    }
	
	public function editspeed() {
        global $db;

        if (empty($this->params['id'])) return false;
        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->params['id']);
        $calc = new $calcname($this->params['id']);
        assign_to_template(array(
            'calculator'=>$calc
        ));
		
    }
	
	public function saveEditSpeed() {
		global $db;

        $obj = new stdClass();
		$obj->speed = $this->params['speed'];
		$obj->shippingcalculator_id = $this->params['shippingcalculator_id'];
		$db->insertObject($obj, $this->params['table']);
		redirect_to(array('controller'=>'shipping', 'action'=>'configure', 'id'=>$this->params['shippingcalculator_id']));
	}
	
	public function deleteSpeed() {
		global $db;

        if (empty($this->params['id'])) return false;
		$db->delete('shippingspeeds',' id =' . $this->params['id']);
		expHistory::back();
	}

    public function tracker() {
        global $db;

        // we ALWAYS assume this is coming from easypost webhook
        $calc_id = $db->selectValue('shippingcalculator','id','calculator_name="easypostcalculator" AND enabled=1');
        if ($calc_id) {
            $ep = new easypostcalculator($calc_id);
            if ($ep->trackerEnabled()) {
                $ep->handleTracking();
            }
        }
        exit();  // graceful exit
    }

}

?>