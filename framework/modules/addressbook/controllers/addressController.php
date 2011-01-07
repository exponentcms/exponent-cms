<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Created by Adam Kessler @ 05/28/2008
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

class addressController extends expController {
	function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Addresses"; }
    function description() { return "Use this module to display and manage addresses of users on your site."; }
    function author() { return "Adam Kessler @ OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    
    public $remove_permissions = array('create', 'edit', 'delete');
    
	public function myaddressbook() {
		global $user;
		// check if the user is logged in.
		expQueue::flashIfNotLoggedIn('message', 'You must be logged in to manage your address book.');
		expHistory::set('viewable', $this->params);
		$userid = (empty($this->params['user_id'])) ? $user->id : $this->params['user_id'];
		assign_to_template(array('addresses'=>$this->address->find('all', 'user_id='.$userid)));
	}
	
	function show() {
	    expHistory::set('viewable', $this->params);
		assign_to_template(array('address'=>new address($this->params['id'])));
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
	        if ($user->isAdmin() || ($user->id == $address->user_id)) {
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
            flash("error","You must have at least one address.");
        }
	    expHistory::back();
	}
    
    public function activate_address()
    {
        global $db, $user;
        $object->id = $this->params['id'];
        $db->setUniqueFlag($object, 'addresses', $this->params['is_what'], "user_id=" . $user->id);
        flash("message", "Successfully updated address.");
        expHistory::back(); 
    }
}

?>
