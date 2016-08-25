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
/** @define "BASE" "../../../.." */

class ecomconfigController extends expController {
    protected $add_permissions = array(
        'show'=>'View Admin Options'
    );
	
    static function displayname() { return gt("e-Commerce Configuration Manager"); }
    static function description() { return gt("Use this module to configure your e-Commerce store"); }
    static function hasSources() { return false; }

    function show() {
        expHistory::set('manageable', $this->params);
    }
    
    /*****************************************************************/
    /***************  PRODUCT OPTIONS *******************************/
    /*****************************************************************/
    function edit_optiongroup_master() {
        expHistory::set('editable', $this->params);
        
        $id = isset($this->params['id']) ? $this->params['id'] : null;
        $record = new optiongroup_master($id);       
        assign_to_template(array(
            'record'=>$record
        ));
    }
    
    function update_optiongroup_master() {
        global $db;

        $id = empty($this->params['id']) ? null : $this->params['id'];
        $og = new optiongroup_master($id);
        $oldtitle = $og->title;
        $og->update($this->params);
        
        // if the title of the master changed we should update the option groups that are already using it.
        if ($oldtitle != $og->title) {
            $db->sql('UPDATE '.$db->prefix.'optiongroup SET title="'.$og->title.'" WHERE title="'.$oldtitle.'"');
        }
        
        expHistory::back();
    }
    
    function delete_optiongroup_master() {
        global $db;
        
        $mastergroup = new optiongroup_master($this->params);
        
        // delete all the options for this optiongroup master
        foreach ($mastergroup->option_master as $masteroption) {
            $db->delete('option', 'option_master_id='.$masteroption->id);
            $masteroption->delete();
        }
        
        // delete the mastergroup
        $db->delete('optiongroup', 'optiongroup_master_id='.$mastergroup->id);
        $mastergroup->delete();
        
        expHistory::back();
    }
    
    function delete_option_master() {
        global $db;

        $masteroption = new option_master($this->params['id']);
        
        // delete any implementations of this option master
        $db->delete('option', 'option_master_id='.$masteroption->id);
        $masteroption->delete('optiongroup_master_id=' . $masteroption->optiongroup_master_id);
        //eDebug($masteroption);
        expHistory::back();
    }
    
    function edit_option_master() {
        expHistory::set('editable', $this->params);
        
        $params = isset($this->params['id']) ? $this->params['id'] : $this->params;
        $record = new option_master($params);      
        assign_to_template(array(
            'record'=>$record
        ));
    }
    
    function update_option_master() {        
        global $db;

        $id = empty($this->params['id']) ? null : $this->params['id'];
        $opt = new option_master($id);
        $oldtitle = $opt->title;
        
        $opt->update($this->params);
        
        // if the title of the master changed we should update the option groups that are already using it.
        if ($oldtitle != $opt->title) {
            
        }$db->sql('UPDATE '.$db->prefix.'option SET title="'.$opt->title.'" WHERE option_master_id='.$opt->id);
        
        expHistory::back();
    }
    
    public function options() {
        expHistory::set('viewable', $this->params);
        $optiongroup = new optiongroup_master();
        $optiongroups = $optiongroup->find('all');
        assign_to_template(array(
            'optiongroups'=>$optiongroups
        ));
    }
    
    function rerank_optionmaster() {
        $om = new option_master($this->params['id']);
        $om->rerank($this->params['push'], 'optiongroup_master_id=' . $this->params['master_id']);
        expHistory::back();
    }
    
    /*****************************************************************/
    /***************  DISCOUNTS        *******************************/
    /*****************************************************************/
    public function manage_discounts() {
        expHistory::set('manageable', $this->params);
		
        $page = new expPaginator(array(
            'model'=>'discounts',
			'sql'=>'SELECT * FROM '.DB_TABLE_PREFIX.'_discounts',
			'limit'=> 10,
			'order'=>isset($this->params['order']) ? $this->params['order'] : null,
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action' => $this->params['action'],
			'columns'=>array(gt('Enabled')=>'enabled',gt('Name')=>'title',gt('Type')=>'action_type',gt('Coupon Code')=>'coupon_code',gt('Valid Until')=>'enddate'),
        ));

        assign_to_template(array(
        /*'apply_rules'=>$discountObj->apply_rules, 'discount_types'=>$discountObj->discount_types,*/
            'page'=>$page
        ));
    }
    
      public function edit_discount() {
        $id = empty($this->params['id']) ? null : $this->params['id'];
        $discount = new discounts($id);
        
        //grab all user groups
        $group = new group();
        
        //create two 'default' groups:
        $groups = array( 
                -1 => 'ALL LOGGED IN USERS',
                -2 => 'ALL NON-LOGGED IN USERS'
                );
        //loop our groups and append them to the array
       // foreach ($group->find() as $g){
       //this is a workaround for older code. Use the previous line if possible:
       $allGroups = group::getAllGroups();
       if (count($allGroups))
       {
           foreach ($allGroups as $g)
           {
                $groups[$g->id] = $g->name;
           };
       }
       //find our selected groups for this discount already
       // eDebug($discount);                        
       $selected_groups = array();
       if (!empty($discount->group_ids))
       {
            $selected_groups = expUnserialize($discount->group_ids);
       }
       
       if ($discount->minimum_order_amount == "") $discount->minimum_order_amount = 0;
       if ($discount->discount_amount == "") $discount->discount_amount = 0;
       if ($discount->discount_percent == "") $discount->discount_percent = 0;
       
        // get the shipping options and their methods
        $shipping_services = array();
        $shipping_methods = array();
//        $shipping = new shipping();
        foreach (shipping::listAvailableCalculators() as $calcid=>$name) {
            if (class_exists($name)) {
                $calc = new $name($calcid);
                $shipping_services[$calcid] = $calc->title;
                $shipping_methods[$calcid] = $calc->availableMethods();
            }
        }
        
       assign_to_template(array(
           'discount'=>$discount,
           'groups'=>$groups,
           'selected_groups'=>$selected_groups,
           'shipping_services'=>$shipping_services,
           'shipping_methods'=>$shipping_methods
       ));
    }
    
    public function update_discount() {
        $id = empty($this->params['id']) ? null : $this->params['id'];
        $discount = new discounts($id);
        // find required shipping method if needed
        if ($this->params['required_shipping_calculator_id'] > 0) {
            $this->params['required_shipping_method'] = $this->params['required_shipping_methods'][$this->params['required_shipping_calculator_id']];
        } else {
            $this->params['required_shipping_calculator_id'] = 0;
        }
        
        $discount->update($this->params);
        expHistory::back();
    }
    
    public function activate_discount(){    
        if (isset($this->params['id'])) {
            $discount = new discounts($this->params['id']);
            $discount->update($this->params);
            //if ($discount->discountulator->hasConfig() && empty($discount->config)) {
                //flash('messages', $discount->discountulator->name().' requires configuration. Please do so now.');
                //redirect_to(array('controller'=>'billing', 'action'=>'configure', 'id'=>$discount->id));
            //}
        }
        
        expHistory::back();
    }
    
    /*****************************************************************/
    /***************  PROMO CODE       *******************************/
    /*****************************************************************/
	public function manage_promocodes() {
		expHistory::set('manageable', $this->params);
        $pc = new promocodes();
        $do = new discounts();
        $promo_codes = $pc->find('all');
        $discounts = $do->find('all');
		assign_to_template(array(
            'promo_codes'=>$promo_codes,
            'discounts'=>$discounts
        ));
	}

	public function update_promocode() {
//	    global $db;
	    //$id = empty($this->params['id']) ? null : $this->params['id'];
	    $code = new promocodes();
	    $code->update($this->params);
	    expHistory::back();
	}
	
    /*****************************************************************/
    /***************  GROUP DISCOUNTS  *******************************/
    /*****************************************************************/
	public function manage_groupdiscounts() {
		global $db;

		expHistory::set('manageable', $this->params);
		$groups = group::getAllGroups();
		$discounts = $db->selectObjects('discounts');
//		$group_discounts = $db->selectObjects('groupdiscounts', null, 'rank');
        $gd = new groupdiscounts();
        $group_discounts = $gd->find('all', null, 'rank');
        if (!empty($group_discounts)) foreach ($group_discounts as $key=>$group_discount) {
            $group_discounts[$key]->title = $group_discount->group->name . ' (' . $group_discount->discounts->title . ')';
        }
		assign_to_template(array(
            'groups'=>$groups,
            'discounts'=>$discounts,
            'group_discounts'=>$group_discounts
        ));
	}

	public function update_groupdiscounts() {
	    global $db;
	    
	    if (empty($this->params['id'])) {
	        // look for existing discounts for the same group
	        $existing_id = $db->selectValue('groupdiscounts', 'id', 'group_id='.$this->params['group_id']);
	        if (!empty($existing_id)) flashAndFlow('error',gt('There is already a discount for that group.'));
	    }

        $gd = new groupdiscounts();
	    $gd->update($this->params);
	    expHistory::back();
	}
	
	function rerank_groupdiscount() {
        $gd = new groupdiscounts($this->params['id']);
        $gd->rerank($this->params['push']);
        expHistory::back();
    }
    
    /*****************************************************************/
    /***************  GENERAL STORE CONFIG  *******************************/
    /*****************************************************************/
    function configure() {
        expHistory::set('editable', $this->params);
        // little bit of trickery so that that categories can have their own configs
        
        $this->loc->src = "@globalstoresettings";
        $config = new expConfig($this->loc);
        $this->config = $config->config;
        $pullable_modules = expModules::listInstalledControllers($this->baseclassname, $this->loc);
        $views = expTemplate::get_config_templates($this, $this->loc);
        
        $gc = new geoCountry();             
        $countries = $gc->find('all');
        
        $gr = new geoRegion();             
        $regions = $gr->find('all');
        
        assign_to_template(array(
            'config'=>$this->config,
            'pullable_modules'=>$pullable_modules,
            'views'=>$views,
            'countries'=>$countries,
            'regions'=>$regions,
            'title'=>static::displayname()
        ));
    }   

    function saveconfig() {
        $this->params['min_order'] = substr($this->params['min_order'], 1) ;
   		$this->params['minimum_gift_card_purchase'] = substr($this->params['minimum_gift_card_purchase'], 1) ;
   		$this->params['custom_message_product']     = substr($this->params['custom_message_product'], 1) ;
        if (isset($this->params['store']['address_country_id'])) {
            $this->params['store']['country'] = $this->params['store']['address_country_id'];
            unset($this->params['store']['address_country_id']);
        }
        if (isset($this->params['store']['address_region_id'])) {
            $this->params['store']['state'] = $this->params['store']['address_region_id'];
            unset($this->params['store']['address_region_id']);
        }
   		parent::saveconfig();
   	}

	/*****************************************************************/
    /***************  Upcharge Rate   *******************************/
    /*****************************************************************/
	
	 function manage_upcharge() {
		$this->loc->src = "@globalstoresettings";
        $config = new expConfig($this->loc);
		$this->config = $config->config;

		$gc = new geoCountry();             
        $countries = $gc->find('all');
        
        $gr = new geoRegion();             
        $regions = $gr->find('all',null,'rank asc,name asc');
        assign_to_template(array(
            'countries'=>$countries,
            'regions'=>$regions,
            'upcharge'=>!empty($this->config['upcharge'])?$this->config['upcharge']:''
        ));
	 }
	 
	 function update_upcharge() {
        $this->loc->src = "@globalstoresettings";
        $config = new expConfig($this->loc);
		$this->config = $config->config;
		
		//This will make sure that only the country or region that given a rate value will be saved in the db
		$upcharge = array();
		foreach($this->params['upcharge'] as $key => $item) {
			if(!empty($item)) {
				$upcharge[$key] = $item;
			}
		}
		$this->config['upcharge'] = $upcharge;
		
        $config->update(array('config'=>$this->config));
        flash('message', gt('Configuration updated'));
        expHistory::back();
    }
	
}

?>