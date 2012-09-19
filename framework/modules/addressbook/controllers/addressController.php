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
 * @package Modules
 * @subpackage Controllers
 */

class addressController extends expController {
	public $useractions = array(
        'myaddressbook'=>'Show my addressbook'
    );
    public $remove_permissions = array(
        'create',
        'edit',
        'delete'
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'pagination',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')

    static function displayname() { return gt("Addresses"); }
    static function description() { return gt("Use this module to display and manage addresses of users on your site."); }
    static function canImportData() { return true;}

    function showall() {
//        redirect_to(array("controller"=>'address',"action"=>'myaddressbook'));
        $this->myaddressbook();
	}
    
    public function edit()
    {
        if((isset($this->params['id']))) $record = new address(intval($this->params['id']));
        else $record = null;
        $config = ecomconfig::getConfig('address_allow_admins_all');
        assign_to_template(array(
            'record'=>$record,
            'admin_config'=>$config
        ));
    }
    
	public function myaddressbook() {
		global $user;
		// check if the user is logged in.
		expQueue::flashIfNotLoggedIn('message',gt('You must be logged in to manage your address book.'));
		expHistory::set('viewable', $this->params);
		$userid = (empty($this->params['user_id'])) ? $user->id : $this->params['user_id'];
		assign_to_template(array(
            'addresses'=>$this->address->find('all', 'user_id='.$userid)
        ));
	}
	
	function show() {
	    expHistory::set('viewable', $this->params);
		assign_to_template(array(
            'address'=>new address($this->params['id'])
        ));
	}

	public function update() {
		global $db, $user;
		if ($user->isLoggedIn()) {
			// check to see how many other addresses this user has already.
			$count = $this->address->find('count', 'user_id='.$user->id);
			// if this is first address save for this user we'll make this the default
			if ($count == 0) 
            {
                $this->params['is_default'] = 1;
                $this->params['is_billing'] = 1;
                $this->params['is_shipping'] = 1;
            }
			// associate this address with the current user.
			$this->params['user_id'] = $user->id;
			// save the object
			$this->address->update($this->params);
		}
        else { //if (ecomconfig::getConfig('allow_anonymous_checkout')){
            //user is not logged in, but allow anonymous checkout is enabled so we'll check 
            //a few things that we don't check in the parent 'stuff and create a user account.
            $this->params['is_default'] = 1;
            $this->params['is_billing'] = 1;
            $this->params['is_shipping'] = 1; 
            $this->address->update($this->params);
        }
		expHistory::back(); 
	}
	
	public function delete() {
	    global $user;
        $count = $this->address->find('count', 'user_id='.$user->id);
        if($count > 1)
        {    
	        if ($user->isAdmin() || ($user->id == $address->user_id)) {  //FIXME $address not set
                $address = new address($this->params['id']);
                if ($address->is_billing) 
                {
                    $billAddress = $this->address->find('first', 'user_id='.$user->id . " AND id != " .$address->id);
                    $billAddress->is_billing = true;
                    $billAddress->save();
                }
                if ($address->is_shipping) 
                {
                    $shipAddress = $this->address->find('first', 'user_id='.$user->id . " AND id != " .$address->id);
                    $shipAddress->is_shipping = true;
                    $shipAddress->save();
                }
	            parent::delete();
	        }
        }
        else
        {
            flash("error",gt("You must have at least one address."));
        }
	    expHistory::back();
	}
    
    public function activate_address()
    {
        global $db, $user;
        $object->id = $this->params['id'];  //FIXME $object not set
        $db->setUniqueFlag($object, 'addresses', $this->params['is_what'], "user_id=" . $user->id);
        flash("message", gt("Successfully updated address."));
        expHistory::back(); 
    }
    
    public function manage()
    {
        $gc = new geoCountry();             
        $countries = $gc->find('all');
        
        $gr = new geoRegion();             
        $regions = $gr->find('all',null,'rank asc,name asc');
        
        assign_to_template(array(
            'countries'=>$countries,
            'regions'=>$regions
        ));
    }
    
    public function manage_update()
    {
        global $db;
        //eDebug($this->params,true);
        //countries
        $db->columnUpdate('geo_country','active',0,'active=1');
        foreach($this->params['country'] as $country_id=>$is_active)
        {
            $gc = new geoCountry($country_id);
            $gc->active = true;            
            $gc->save();            
        }
        //country default
        $db->columnUpdate('geo_country','is_default',0,'is_default=1');
        if(isset($this->params['country_default']))
        {
            $gc = new geoCountry($this->params['country_default']);            
            $db->setUniqueFlag($gc,'geo_country','is_default','id=' . $gc->id);    
            $gc->refresh();            
        }
        //regions
        $db->columnUpdate('geo_region','active',0,'active=1');
        foreach($this->params['region'] as $region_id=>$is_active)
        {
            $gr = new geoRegion($region_id);
            $gr->active = true;
            if(isset($this->params['region_rank'][$region_id])) $gr->rank = $this->params['region_rank'][$region_id];
            $gr->save();            
        }
        flash('message',gt('Address configurations successfully updated.'));
        redirect_to(array('controller'=>'address','action'=>'manage'));
//        $this->manage();
    }

}

?>