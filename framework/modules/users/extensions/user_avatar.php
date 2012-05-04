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
 * @subpackage Profile-Extensions
 * @package Modules
 */
class user_avatar extends expRecord {
#    public $validates = array(
#        'presence_of'=>array(
#            'title'=>array('message'=>'Title is a required field.'),
#            'body'=>array('message'=>'Body is a required field.'),
#        ));

	/**
	 * @return string
	 */
	public function name() { return 'Avatars'; }

	/**
	 * @return string
	 */
	public function description() { return 'The extension allows users to select their avatar image.'; }

	/**
	 * @param array $params
	 * @return bool
	 */
	public function update($params=array()) {

        // if not user id then we should not be doing anything here
        if (empty($params['user_id'])) return false;
        $this->user_id = $params['user_id'];
        
        // check for a previous avatar otherwise set the default
        $this->image = $params['current_avatar'];
        if (empty($this->image)) $this->image = PATH_RELATIVE.'framework/modules/users/assets/images/avatar_not_found.jpg';

        if (!empty($_FILES['avatar']['tmp_name'])) {  // if the user uploaded a new avatar lets save it!
            $info = expFile::getImageInfo($_FILES['avatar']['tmp_name']);
            if ($info['is_image']) {
                // figure out the mime type and set the file extension and name
                $extinfo = explode('/',$info['mime']);
                $extension = $extinfo[1];
                $avatar_name = $this->user_id.'.'.$extension;
                
                // save the file to the filesystem
                $file = expFile::fileUpload('avatar', true, false, $avatar_name, 'files/avatars/');

                //save the file to the database                
                $this->image = $file->url;
                $this->use_gravatar = false;  // if we uploaded a file, we don't want to use gravatar
            }
        } elseif (!empty($params['use_gravatar'])) {  // if the user chose gravatar, create the link and save it!
	            $this->use_gravatar = $params['use_gravatar'];
	            $emailMD5 = md5(strtolower(trim(user::getEmailById($params['user_id']))));
	            $this->image = "http://www.gravatar.com/avatar/" . $emailMD5 .  ".jpg";
        }
        
        parent::update();
    }	
}

?>