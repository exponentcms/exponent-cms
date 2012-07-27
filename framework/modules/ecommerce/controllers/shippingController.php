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
 * @subpackage Controllers
 * @package Modules
 */

class shippingController extends expController {
	public $useractions = array();
	public $add_permissions = array('toggle'=>'Enable/Disable Options');

	function displayname() { return gt("Ecommerce Shipping Controller"); }
	function description() { return ""; }
	function hasSources() { return false; }
	function hasContent() { return false; }
	
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
		
		$ar = new expAjaxReply(200, 'ok', $shipping, array('controller'=>'cart', 'action'=>'checkout'));
		$ar->send();
	}

	function selectShippingOption() {
	    global $order;
		$shipping = new shipping();
		$id = $this->params['option'];
		$rates = $shipping->calculator->getRates($order);
		$rate = $rates[$id];
		$shipping->shippingmethod->update(array('option'=>$id,'option_title'=>$rate['title'],'shipping_cost'=>$rate['cost']));
		$ar = new expAjaxReply(200, 'ok', array('title'=>$rate['title'], 'cost'=>number_format($rate['cost'], 2)), array('controller'=>'cart', 'action'=>'checkout'));
		$ar->send();
	}

	function setAddress() {
		$shipping = new shipping();
		$shipping->shippingmethod->setAddress($this->params['shipping_address']);
		$shipping->refresh();
		$ar = new expAjaxReply(200, 'ok', new address($shipping->shippingmethod->addresses_id), array('controller'=>'cart', 'action'=>'checkout'));
		$ar->send();
	}
	
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
		
		$ar = new expAjaxReply(200, 'ok', $sm, array('controller'=>'cart', 'action'=>'checkout'));
		$ar->send();		
	}
	
	function renderOptions() {
	    global $db, $order;	    
	    $shipping = new shipping(); 
        $shipping->pricelist = $shipping->calculator->getRates($order);
        
        if (empty($shipping->shippingmethod->option)) {
            $opt = current($shipping->pricelist);
        } else {
            $opt = $shipping->pricelist[$shipping->shippingmethod->option];
        }
        
        $shipping->shippingmethod->update(array('option'=>$opt['id'],'option_title'=>$opt['title'],'shipping_cost'=>$opt['cost']));
        
        assign_to_template(array(
            'shipping'=>$shipping,
            'order'=>$order
        ));
	}
	
	function listPrices() {
	    $shipping = new shipping();
	    $ar = new expAjaxReply(200, 'ok', $shipping->listPrices(), array('controller'=>'cart', 'action'=>'checkout'));
		$ar->send();
	}
	
	function manage() {
	    global $db;
	    
	    expHistory::set('manageable', $this->params);
	    $calculators = array();
        $dir = BASE."framework/modules/ecommerce/shippingcalculators";
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
	    expHistory::back();
	}

    public function configure() {
        global $db;
        if (empty($this->params['id'])) return false;
        $calcname = $db->selectValue('shippingcalculator', 'calculator_name', 'id='.$this->params['id']);
        $calc = new $calcname($this->params['id']);
        assign_to_template(array(
            'calculator'=>$calc,
            'title'=>$this->displayname()
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
}

?>