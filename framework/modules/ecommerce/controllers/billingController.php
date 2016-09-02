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

class billingController extends expController {
    protected $add_permissions = array(
        'activate'=>'Activate Payment Options'
    );
    
    static function displayname() { return gt("e-Commerce Billing Controller"); }
    static function description() { return ""; }
	static function hasSources() { return false; }
	
	function selectBillingCalculator() {
		$billing = new billing();
		$billing->billingmethod->update($this->params);
		$ar = new expAjaxReply(200, 'ok', $billing, array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}

	function setAddress() {
		$billing = new billing();
		$billing->billingmethod->setAddress($this->params['billing_address']);
		$ar = new expAjaxReply(200, 'ok', new address($billing->billingmethod->addresses_id), array('controller'=>'cart', 'action'=>'checkout'),true);
		$ar->send();
	}

	function selectBillingOptions() {
		
	}
	
	function manage() {
	    global $db;
	    
	    expHistory::set('manageable', $this->params);
//	    $classes = array();
        $dir = BASE."framework/modules/ecommerce/billingcalculators";
        if (is_readable($dir)) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (is_file("$dir/$file") && substr("$dir/$file", -4) == ".php") {
                    include_once("$dir/$file");
                    $classname = substr($file, 0, -4);
                    $id = $db->selectValue('billingcalculator', 'id', 'calculator_name="'.$classname.'"');
                    if (empty($id)) {
//                        $calobj = null;
                        $calcobj = new $classname();
                        if ($calcobj->isSelectable() == true) {
                            $obj = new billingcalculator(array(
                                'title'=>$calcobj->name(),
//                                'user_title'=>$calcobj->title,
                                'body'=>$calcobj->description(), 
                                'calculator_name'=>$classname,
                                'enabled'=>false));
                            $obj->save();
                        }
                    }
                }
            }
        }
        
        $bcalc = new billingcalculator();
        $calculators = $bcalc->find('all');
        assign_to_template(array(
            'calculators'=>$calculators
        ));
	}
	
	public function activate(){	
	    if (isset($this->params['id'])) {
	        $calc = new billingcalculator($this->params['id']);
	        $calc->update($this->params);
            //FIXME we need to ensure our default calculator is still active
	        if ($calc->calculator->hasConfig() && empty($calc->config)) {
	            flash('message', $calc->calculator->name().' '.gt('requires configuration. Please do so now.'));
	            redirect_to(array('controller'=>'billing', 'action'=>'configure', 'id'=>$calc->id));
	        }
	    }
	    expHistory::back();
	}

    public function toggle_default() {
  	    global $db;

        $db->toggle('billingcalculator',"is_default",'is_default=1');
  	    if (isset($this->params['id'])) {
            $active = $db->selectObject('billingcalculator',"id=".$this->params['id']);
            $active->is_default = 1;
            $db->updateObject($active,'billingcalculator',null,'id');
        }
        if ($db->selectValue('billingcalculator', 'is_default', 'id='.$this->params['id']) && !$db->selectValue('billingcalculator', 'enabled', 'id='.$this->params['id'])) {
            $db->toggle('billingcalculator', 'enabled', 'id='.$this->params['id']);
        }
  	    expHistory::back();
  	}

    public function configure() {
        if (empty($this->params['id'])) return false;
        $calc = new billingcalculator($this->params['id']);
        assign_to_template(array(
            'calculator'=>$calc,
            'title'=>static::displayname()
        ));
    }
    
    public function saveconfig() {
        $calc = new billingcalculator($this->params['id']);
        $conf = serialize($calc->calculator->parseConfig($this->params));
        $calc->update(array('config'=>$conf));
        expHistory::back();
    }

}

?>