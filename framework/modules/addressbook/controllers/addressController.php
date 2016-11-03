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
 * @package Modules
 * @subpackage Controllers
 */

class addressController extends expController {
//	public $useractions = array(
//        'myaddressbook'=>'Show my addressbook'
//    );
    protected $remove_permissions = array(
        'create',
        'edit',
        'delete'
    );
    protected $manage_permissions = array(
//        'import' => 'Import External Addresses',
        'process' => 'Import External Addresses',
        'edit_country' => 'Edit Country',
        'delete_country' => 'Delete Country',
        'update_country' => 'Update Country',
        'edit_region' => 'Edit Region',
        'delete_region' => 'Delete Region',
        'update_region' => 'Update Region',
    );
    public $requires_login = array(
        'edit'=>'You must be logged in to perform this action',
        'myaddressbook'=>'You must be logged in to perform this action',
    );
	public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Addresses"); }
    static function description() { return gt("Display and manage addresses of users on your site."); }
    static function canImportData() { return true;}

    static function hasSources() {
        return false;
    }

    function showall() {
//        redirect_to(array("controller"=>'address',"action"=>'myaddressbook'));
        $this->myaddressbook();
	}

    public function edit()
    {
        global $user;

        $id = !empty($this->params['id']) ? $this->params['id'] : null;

        // check to see if we should be editing.  You either need to be an admin, or editing own account.
        if ($user->isAdmin() || ($user->id == $id)) {
            $record = new address($id);
        } else {
            flash('error', gt('You do not have the proper permissions to edit this address'));
            expHistory::back();
        }

        $config = ecomconfig::getConfig('address_allow_admins_all');
        assign_to_template(array(
            'record'=>$record,
            'admin_config'=>$config
        ));
        if (expSession::get('customer-signup')) {
            assign_to_template(array(
                'checkout'=>true
            ));
        }
    }

	public function myaddressbook() {
		global $user;

		// check if the user is logged in.
		expQueue::flashIfNotLoggedIn('message',gt('You must be logged in to manage your address book.'));  //fixme is this redundant to common routine?
        if (!$user->isAdmin() && $this->params['user_id'] != $user->id) {
            unset($this->params['user_id']);
        }
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
        global $user;

        if (expSession::get('customer-signup')) expSession::set('customer-signup', false);
        if (isset($this->params['address_country_id'])) {
            $this->params['country'] = $this->params['address_country_id'];
            unset($this->params['address_country_id']);
        }
        if (isset($this->params['address_region_id'])) {
            $this->params['state'] = $this->params['address_region_id'];
            unset($this->params['address_region_id']);
        }
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

        $count = $this->address->find('count', 'user_id=' . $user->id);
        if($count > 1)
        {
            $address = new address($this->params['id']);
	        if ($user->isAdmin() || ($user->id == $address->user_id)) {
                if ($address->is_billing)
                {
                    $billAddress = $this->address->find('first', 'user_id=' . $user->id . " AND id != " . $address->id);
                    $billAddress->is_billing = true;
                    $billAddress->save();
                }
                if ($address->is_shipping)
                {
                    $shipAddress = $this->address->find('first', 'user_id=' . $user->id . " AND id != " . $address->id);
                    $shipAddress->is_shipping = true;
                    $shipAddress->save();
                }
	            parent::delete();
	        }
        }
        else
        {
            flash("error", gt("You must have at least one address."));
        }
	    expHistory::back();
	}

    public function activate_address()
    {
        global $db, $user;

        $object = new stdClass();
        $object->id = $this->params['id'];
        $db->setUniqueFlag($object, 'addresses', expString::escape($this->params['is_what']), "user_id=" . $user->id);
        flash("message", gt("Successfully updated address."));
        expHistory::back();
    }

    public function manage()
    {
        expHistory::set('manageable',$this->params);
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
            $gc = new geoCountry(intval($this->params['country_default']));
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

    function edit_country() {
        $country_id = !empty($this->params['id']) ? $this->params['id'] : null;
        $country = new geoCountry($country_id);
        assign_to_template(array(
            'record'=>$country,
        ));
    }

    function update_country() {
        $country_id = !empty($this->params['id']) ? $this->params['id'] : null;
        $country = new geoCountry($country_id);
        $country->update($this->params);
        expHistory::returnTo('manageable');
    }

    function delete_country() {
        if (empty($this->params['id'])) {
            flash('error', gt('Missing id for the country you would like to delete'));
            expHistory::back();
        }
        $country = new geoCountry($this->params['id']);
        $country->delete();
        expHistory::returnTo('manageable');
    }

    function edit_region() {
        $region_id = !empty($this->params['id']) ? $this->params['id'] : null;
        $region = new geoRegion($region_id);
        assign_to_template(array(
            'record'=>$region,
        ));
    }

    function update_region() {
        $region_id = !empty($this->params['id']) ? $this->params['id'] : null;
        $region = new geoRegion($region_id);
        $region->update($this->params);
        expHistory::returnTo('manageable');
    }

    function delete_region() {
        if (empty($this->params['id'])) {
            flash('error', gt('Missing id for the region you would like to delete'));
            expHistory::back();
        }
        $region = new geoRegion($this->params['id']);
        $region->delete();
        expHistory::returnTo('manageable');
    }

    /**
     * Import external addresses
     */
    function import() {
        $sources = array('mc' => 'MilitaryClothing.com', 'nt' => 'NameTapes.com', 'am' => 'Amazon');
        assign_to_template(array(
            'sources' => $sources
        ));
    }

    function process_external_addresses() {
        global $db;

        set_time_limit(0);
        //$file = new expFile($this->params['expFile']['batch_process_upload'][0]);
        eDebug($this->params);
//        eDebug($_FILES,true);
        if (!empty($_FILES['address_csv']['error'])) {
            flash('error', gt('There was an error uploading your file.  Please try again.'));
            redirect_to(array('controller' => 'store', 'action' => 'import_external_addresses'));
//            $this->import_external_addresses();
        }

        $file = new stdClass();
        $file->path = $_FILES['address_csv']['tmp_name'];
        echo "Validating file...<br/>";

        //replace tabs with commas
        /*if($this->params['type_of_address'][0] == 'am')
        {
            $checkhandle = fopen($file->path, "w");
            $oldFile = file_get_contents($file->path);
            $newFile = str_ireplace(chr(9),',',$oldFile);
            fwrite($checkhandle,$newFile);
            fclose($checkhandle);
        }*/

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $checkhandle = fopen($file->path, "r");
        if ($this->params['type_of_address'][0] == 'am') {
            $checkdata = fgetcsv($checkhandle, 10000, "\t");
            $fieldCount = count($checkdata);
        } else {
            $checkdata = fgetcsv($checkhandle, 10000, ",");
            $fieldCount = count($checkdata);
        }

        $count = 1;
        if ($this->params['type_of_address'][0] == 'am') {
            while (($checkdata = fgetcsv($checkhandle, 10000, "\t")) !== FALSE) {
                $count++;
                //eDebug($checkdata);
                if (count($checkdata) != $fieldCount) {
                    echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                    echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                    exit();
                }
            }
        } else {
            while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
                $count++;
                if (count($checkdata) != $fieldCount) {
                    echo "Line " . $count . " of your CSV import file does not contain the correct number of columns.<br/>";
                    echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) . " field in row " . $count . " Please check your file and try again.";
                    exit();
                }
            }
        }

        fclose($checkhandle);
        ini_set('auto_detect_line_endings',$line_end);

        echo "<br/>CSV File passed validation...<br/><br/>Importing....<br/><br/>";
        //exit();
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $handle = fopen($file->path, "r");
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);
        $dataset = array();

        //mc=1, nt=2, amm=3

        if ($this->params['type_of_address'][0] == 'mc') {
            //militaryclothing
            $db->delete('external_addresses', 'source=1');

        } else if ($this->params['type_of_address'][0] == 'nt') {
            //nametapes
            $db->delete('external_addresses', 'source=2');
        } else if ($this->params['type_of_address'][0] == 'am') {
            //amazon
            $db->delete('external_addresses', 'source=3');
        }

        if ($this->params['type_of_address'][0] == 'am') {
            while (($data = fgetcsv($handle, 10000, "\t")) !== FALSE) {
                //eDebug($data,true);
                $extAddy = new external_address();

                //eDebug($data);
                $extAddy->source = 3;
                $extAddy->user_id = 0;
                $name = explode(' ', $data[15]);
                $extAddy->firstname = $name[0];
                if (isset($name[3])) {
                    $extAddy->firstname .= ' ' . $name[1];
                    $extAddy->middlename = $name[2];
                    $extAddy->lastname = $name[3];
                } else if (isset($name[2])) {
                    $extAddy->middlename = $name[1];
                    $extAddy->lastname = $name[2];
                } else {
                    $extAddy->lastname = $name[1];
                }
                $extAddy->organization = $data[15];
                $extAddy->address1 = $data[16];
                $extAddy->address2 = $data[17];
                $extAddy->city = $data[19];
                $state = new geoRegion();
                $state = $state->findBy('code', trim($data[20]));
                if (empty($state->id)) {
                    $state = new geoRegion();
                    $state = $state->findBy('name', trim($data[20]));
                }
                $extAddy->state = $state->id;
                $extAddy->zip = str_ireplace("'", '', $data[21]);
                $extAddy->phone = $data[6];
                $extAddy->email = $data[4];
                //eDebug($extAddy);
                $extAddy->save();
            }
        } else {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                eDebug($data);
                $extAddy = new external_address();
                if ($this->params['type_of_address'][0] == 'mc') {
                    $extAddy->source = 1;
                    $extAddy->user_id = 0;
                    $name = explode(' ', $data[3]);
                    $extAddy->firstname = $name[0];
                    if (isset($name[2])) {
                        $extAddy->middlename = $name[1];
                        $extAddy->lastname = $name[2];
                    } else {
                        $extAddy->lastname = $name[1];
                    }
                    $extAddy->organization = $data[4];
                    $extAddy->address1 = $data[5];
                    $extAddy->address2 = $data[6];
                    $extAddy->city = $data[7];
                    $state = new geoRegion();
                    $state = $state->findBy('code', $data[8]);
                    $extAddy->state = $state->id;
                    $extAddy->zip = str_ireplace("'", '', $data[9]);
                    $extAddy->phone = $data[20];
                    $extAddy->email = $data[21];
                    //eDebug($extAddy);
                    $extAddy->save();

                    //Check if the shipping add is same as the billing add
                    if ($data[5] != $data[14]) {
                        $extAddy = new external_address();
                        $extAddy->source = 1;
                        $extAddy->user_id = 0;
                        $name = explode(' ', $data[12]);
                        $extAddy->firstname = $name[0];
                        if (isset($name[2])) {
                            $extAddy->middlename = $name[1];
                            $extAddy->lastname = $name[2];
                        } else {
                            $extAddy->lastname = $name[1];
                        }
                        $extAddy->organization = $data[13];
                        $extAddy->address1 = $data[14];
                        $extAddy->address2 = $data[15];
                        $extAddy->city = $data[16];
                        $state = new geoRegion();
                        $state = $state->findBy('code', $data[17]);
                        $extAddy->state = $state->id;
                        $extAddy->zip = str_ireplace("'", '', $data[18]);
                        $extAddy->phone = $data[20];
                        $extAddy->email = $data[21];
                        // eDebug($extAddy, true);
                        $extAddy->save();
                    }
                }
                if ($this->params['type_of_address'][0] == 'nt') {
                    //eDebug($data,true);
                    $extAddy->source = 2;
                    $extAddy->user_id = 0;
                    $extAddy->firstname = $data[16];
                    $extAddy->lastname = $data[17];
                    $extAddy->organization = $data[15];
                    $extAddy->address1 = $data[18];
                    $extAddy->address2 = $data[19];
                    $extAddy->city = $data[20];
                    $state = new geoRegion();
                    $state = $state->findBy('code', $data[21]);
                    $extAddy->state = $state->id;
                    $extAddy->zip = str_ireplace("'", '', $data[22]);
                    $extAddy->phone = $data[23];
                    $extAddy->email = $data[13];
                    //eDebug($extAddy);
                    $extAddy->save();
                }
            }
        }
        fclose($handle);
        ini_set('auto_detect_line_endings',$line_end);
        echo "Done!";
    }

}

?>