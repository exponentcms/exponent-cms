<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Created by Adam Kessler @ 09/06/2007
#
# This file is part of Acorn Web API
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

class address extends expRecord {
	public $table = 'addresses';
    public $validates = array(
		    'presence_of'=>array(
			    'firstname'=>array('message'=>'First name is a required field.'),
			    'lastname'=>array('message'=>'Last name is a required field.'),
			    'address1'=>array('message'=>'Street Address is a required field.'),
			    'city'=>array('message'=>'City name is a required field.'),
			    'state'=>array('message'=>'You must choose a state.'),			
		    ),
		    'is_valid_zipcode'=>array(
			    'zip'=>array('message'=>'The zip code you entered does not appear to be a valid US zip code.')
	        ),
	        'is_valid_phonenumber'=>array(
			    'phone'=>array('message'=>'The phone number you entered does not appear to be valid.')
	        ),
		    'is_valid_email'=>array(
			    'email'=>array('message'=>'The email address you entered does not appear to be a valid email address')
	        )
		);
    
	public function __construct($params=null, $get_assoc = false, $get_attached = false) {
		global $db;
		parent::__construct($params, $get_assoc, $get_attached);
		if (!empty($this->state)) {
			$stateObj = $db->selectObject('geo_region', 'id='.$this->state);
			$this->statename = $stateObj->name;
			$this->state_code = $stateObj->code;
		}
	}

	public function dropdownByUser($user) {
		$id = is_numeric($user) ? $user : $user->id;
		$addys = $this->find('all', 'user_id='.$id, 'is_default DESC');
		
		$ddmenu = array();
		foreach($addys as $addy) {
			$ddmenu[$addy->id] = "$addy->firstname $addy->middlename $addy->lastname, $addy->address1, $addy->address2, $addy->city...";
		}

		return $ddmenu;
	}	
    
    function afterValidationOnUpdate()
    {
        //echo "This worked - update";
        //die();    
    }
    
    //this is here as a somewhat clunky workaround to allow easier checkout.
    //we're still creating a user account for everyone, but making it smoother for those that provide a 
    //password, and making it seem like it's anonymous for those that aren't
    function afterValidationOnCreate()
    {                             
        global $user, $db;
        //check if user is logged in.  If so, then we won't have the password and capture fields
        //eDebug($_POST,true);
      
        if (!$user->isLoggedIn())
        {
            //user is not logged in, so we assume they are creating their first address 
            //we'll check to see if they have elected to 'remember me' and if so, check the username and passwords.
            //if not, then we just check the captha and create an account manually

            
            $password = $_POST['password'];
            if (isset($_POST['remember_me']) && $_POST['remember_me'] == true)  
            {
                $user->username = $_POST['email'];     
                $validateUser = $user->setPassword($password,$_POST['password2']);
                if (!is_bool($validateUser))                
                 {                    
                    expValidator::failAndReturnToForm($validateUser, $_POST);    
                 }   
            } else {
                $user->username = $_POST['email'] . time();  //make a unique username
                $password = md5(time().rand(50,000));  //generate random password
                $user->setPassword($password, $password);
            }
            
            //expValidator::check_antispam($_POST, "Your anti-spam verification failed.  Please try again.");
            
            //if we've come this far, we're good to create the new user account
            $user->email = $_POST['email'];
            $user->firstname = $_POST['firstname'];
            $user->lastname = $_POST['lastname'];
            //eDebug($_POST);
            //eDebug($user);
            $checkUser = $db->selectObject('user','username="' . $user->username . '"');
            if (isset($checkUser->id))
            {
                expValidator::failAndReturnToForm("The email address you entered already exists as a user. If you have lost your password, you may reset it here: <a href='/users/reset_password'>Reset Password</a>.", $_POST);    
            }
            $user->is_system_user = false;
            $user->save(true);
            $user->login($user->username,$password);
            $this->user_id = $user->id;
            $this->is_default = true;
            //eDebug($user,true); 
            //$user-> = $_POST['first_name'];          
            //eDebug($this,true);  
            //set this back since we now have a logged in user and we don't want things going goofy if they logout and log back in and such
            exponent_sessions_unset("ALLOW_ANONYMOUS_CHECKOUT");
        }
    }
}
?>
