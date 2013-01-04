<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

class importexportController extends expController {
    public $useractions = array(
        /*'showall'=>'Show all products & categories',
        'showall_featured_products'=>'Show all featured products',
        'upcoming_events'=>'Show all upcoming events',
        'showallSubcategories'=>'Show subcategories to the current category.',
        'showallManufacturers'=>'Show products by manufacturer',
        'quicklinks'=>'Quick Links for Users',
        'showTopLevel'=>'Show Top Level Store Categories',
        'search_by_model_form'=>'Product Search - By Model',
        'events_calendar'=>'Show events in a calendar'     */
    );
    
    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
        'rss',
        'tags'
    ); // all options: ('aggregation','categories','comments','ealerts','files','module_title','pagination','rss','tags')
    
    //protected $permissions = array_merge(array("test"=>'Test'), array('copyProduct'=>"Copy Product"));
    protected $add_permissions = array('import'=>'Import Data', 'export'=>'Export Data');
     
    static function displayname() { return gt("Data Import / Export Module"); }
    static function description() { return gt("Use this module to import and export data from your Exponent website."); }
    static function hasSources() { return false; }
    static function hasContent() { return false; }

    function __construct($src=null,$params=array()) {
         parent::__construct($src=null,$params);
        
    }
    
    function import() {
        assign_to_template(array(
            'type'=>$this->params['import_type']
        ));
    }
    
    function parseCategory($data)
    {
        global $db;
        if (!empty($data))
        {
            $cats1 = explode("::",trim($data));
            //eDebug($cats1);
            $cats1count = count($cats1);
            $counter = 1;
            $categories1 = array();
            foreach ($cats1 as $key=>$cat)
            {
                //eDebug($cat);
                if ($counter == 1) $categories1[$counter] = $db->selectObject('storeCategories', 'title="'. $cat .'" AND parent_id=0');
                else $categories1[$counter] = $db->selectObject('storeCategories', 'title="'. $cat .'" AND parent_id=' . $categories1[$counter-1]->id);
                //eDebug($categories1);
                if (empty($categories1[$counter]->id)) 
                {
                    return "'" . $cat . "' of the set: '" . $data . "' is not a valid category.";                    
                }  
                                   
                if ($counter == $cats1count)
                {
                    return $categories1[$counter]->id;
                }   
                $counter++;                           
            }
            //eDebug($createCats);
            //eDebug($categories1,true);
        }else{
            return "Category was empty.";
        }    
    }
    
    function validate()
    {
        global $db;
        //eDebug($this->params,true); 
        set_time_limit(0);
        //$file = new expFile($this->params['expFile']['import_file'][0]);
        if(!empty($_FILES['import_file']['error']))
        {
            flash('error',gt('There was an error uploading your file.  Please try again.'));
            redirect_to(array('controller'=>'store','action'=>'import_external_addresses'));
        }

        $file = new stdClass();
        $file->path = $_FILES['import_file']['tmp_name'];
        echo "Attempting import...<br/>";
        
        $checkhandle = fopen($file->path, "r");
        $checkdata = fgetcsv($checkhandle, 10000, ",");
        $fieldCount = count($checkdata);   
        
        $count = 1;
        while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
            $count++;
            if (count($checkdata) != $fieldCount) 
            {                   
                echo "Line ". $count ." of your CSV import file does not contain the correct number of columns.<br/>";
                echo "Found " . $fieldCount . " header fields, but only " . count($checkdata) ." field in row " . $count . " Please check your file and try again.";
                exit();
            }
        }        
        fclose($checkhandle);
        
        echo "<br/>".gt("CSV File passed validation")."...<br/>";
        
        if($this->params['import_type'] == 'storeController') $this->importProduct($file);
        //else if($this->params['import_type'] == 'addressController') $this->importAddresses($file);
       
    }
    
    /*function importAddresses($file)
    {
        $handle = fopen($file->path, "r");
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);        
        $source = '';   
        foreach ($data as $key=>$value)
        {
            $dataset[$value] = '';            
            if($key == 2 && $value=='Unique_Bill_Name') $source = '1';    //SMC
        }
        
        //eDebug($source);
        //eDebug($dataset,true);
        $count = 1;
        $errorSet = array();
        $successSet = array();
        eDebug($dataset);
        
        $extAddy = null;
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;
            $extAddy = new external_address();             
            $bName = explode(' ',$data[3]);
            eDebug($bName);
            $extAddy->firstname = $bName[0];
            if(count($bName) == 3)
            {
                $extAddy->middlename = $bName[1];
                $extAddy->lastname = $bName[2];    
            }
            else if (count($bName) ==1)
            {
                $extAddy->middlename = '';
                $extAddy->lastname = '';    
            }
            else
            {
                $extAddy->middlename = '';
                $extAddy->lastname = $bName[1];        
            }
            
            $extAddy->organization = $data[4];
            $extAddy->address1 = $data[5];
            $extAddy->address2 = $data[6];            
            $extAddy->address2 = $data[6];            
            $extAddy->city = $data[7];            
            
            $s = new geoRegion();
            $state = $s->find('first','code="'.trim($data[8]).'"');
            eDebug($state);
            $extAddy->state = $state->id;                        
            $extAddy->zip = str_ireplace("'",'',$data[9]);            
            $extAddy->phone = $data[20];            
            $extAddy->email = $data[21];            
            $extAddy->source = $source;
            
            
            //shipping
            if($data[3] == $data[12] && $data[5] == $data[14] && $data[6] == $data[15])  //shipping and billing same
            {
                $extAddy->is_billing = 1;
                $extAddy->is_shipping = 1;            
                $extAddy->save(false);            
            }
            else
            {                
                $extAddy->is_billing = 1;
                $extAddy->is_shipping = 0;            
                $extAddy->save(false);            
                
                $extAddy = new external_address();             
                $sName = explode(' ',$data[12]);
                eDebug($sName);
                $extAddy->firstname = $sName[0];
                if(count($sName) == 3)
                {
                    $extAddy->middlename = $sName[1];
                    $extAddy->lastname = $sName[2];    
                }
                else if (count($sName) ==1)
                {
                    $extAddy->middlename = '';
                    $extAddy->lastname = '';    
                }
                else
                {
                    $extAddy->middlename = '';
                    $extAddy->lastname = $sName[1];        
                }
                
                $extAddy->organization = $data[13];
                $extAddy->address1 = $data[14];
                $extAddy->address2 = $data[15];                            
                $extAddy->city = $data[16];            
                
                $s = new geoRegion();
                $state = $s->find('first','code="'.trim($data[17]).'"');
                eDebug($state);
                $extAddy->state = $state->id;                        
                $extAddy->zip = str_ireplace("'",'',$data[18]);            
                $extAddy->phone = $data[20];            
                $extAddy->email = $data[21];            
                $extAddy->is_billing = 0;
                $extAddy->is_shipping = 1;
                $extAddy->source = $source;   
                
                $extAddy->save(false);
            }
            
            echo "Sucessfully imported row " . $count . ", name: " . $extAddy->firstname . " " . $extAddy->lastname . "<br/>";
            //eDebug($product);
        
        }   
        
        if(count($errorSet))
        {
            echo "<br/><hr><br/><font color='red'>The following records were NOT imported:<br/>";
            foreach ($errorSet as $row=>$err)
            {
                echo "Row: " . $row . ". Reason:<br/>";
                if (is_array($err))
                {
                    foreach ($err as $e)
                    {
                        echo "--" . $e . "<br/>";
                    }
                }
                else echo "--" . $err . "<br/>";
            }
            echo "</font>";
        }    
    }*/
    
    function importProduct($file)
    {
        $handle = fopen($file->path, "r");
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);        
        foreach ($data as $key=>$value)
        {
            $dataset[$value] = '';    
        }
        
        //eDebug($dataset,true);
        $count = 1;
        $errorSet = array();
        $successSet = array();
        //$createCats = array();
        $product = null;
        /*
        0= id    
        1=parent_id    
        2=child_rank    
        3=title    
        4=model    
        5=warehouse_location    
        6=sef_url    
        7=meta_title    
        8=meta_keywords    
        9=meta_description    
        10=base_price    
        11=special_price    
        12=use_special_price    
        13=active_type    
        14=product_status_id    
        15=category1    
        16=category2    
        17=category3    
        18=category4    
        19=surcharge                                        
        20=rank   
        21=feed_title
        22=feed_body   
        */
        
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;
               
			//eDebug($data, true);
            if (isset($data[0]) && $data[0] != 0)
            {
                $product = new product($data[0],false,false);
                if (empty($product->id)) 
                {
                    $errorSet[$count] = $product->id . " is not a valid product ID.";
                    continue;
                }
            }else{
                //$errorSet[$count] = "Product ID not supplied.";
                //continue;
                $product = new product();
                //$product->save(false);
            }
           
            $checkTitle = trim($data[3]);
            if (empty($checkTitle)) 
            {
                $errorSet[$count] = "No product name (title) supplied, skipping this record...";
                continue;    
            } 
            $product->parent_id = $data[1];
            $product->child_rank = $data[2];
            $product->title = stripslashes(stripslashes($data[3]));
            $product->body = utf8_encode(stripslashes(reportController::parseAndTrimImport(($data[4]),true)));            
            //$product->body = utf8_encode(stripslashes(stripslashes(($data[4]))));            
            $product->model = stripslashes(stripslashes($data[5]));
            $product->warehouse_location = stripslashes(stripslashes($data[6]));
            $product->sef_url = stripslashes(stripslashes($data[7]));
            $product->meta_title = stripslashes(stripslashes($data[8]));
            $product->meta_keywords = stripslashes(stripslashes($data[9]));
            $product->meta_description = stripslashes(stripslashes($data[10]));
            
            $product->tax_class_id = $data[11];
            
            $product->quantity = $data[12];
            
            $product->availability_type = $data[13];   
            
            $product->base_price = $data[14];
            $product->special_price = $data[15];
            $product->use_special_price = $data[16];
            $product->active_type = $data[17];
            $product->product_status_id = $data[18];
            
            $product->surcharge = $data[31];
            $product->feed_title = stripslashes(stripslashes($data[33]));
            $product->feed_body = stripslashes(stripslashes($data[34]));
            
            if(empty($product->id)) $product->minimum_order_quantity = 1;
             
            if ($product->parent_id==0)
            {
                $createCats = array();
                $createCatsRank = array();            
                for ($x=19; $x<=30; $x++)
                {
                    if (!empty($data[$x])) $result = $this->parseCategory($data[$x]);
                    else continue;
                   
                    if (is_numeric($result)) 
                    {
                        $createCats[] = $result;
                        $createCatsRank[$result] = $data[32];    
                    }else{
                        $errorSet[$count][] = $result;
                        continue 2;   
                    }
                }
            }
            /*[0] => id
            [1] => parent_id
            [2] => child_rank
            [3] => title
            [4] => model
            [5] => warehouse_location
            [6] => sef_url
            [7] => meta_title
            [8] => meta_keywords
            [9] => meta_description
            [10] => base_price
            [11] => special_price
            [12] => use_special_price
            [13] => active_type
            [14] => product_status_id
            [15] => category1
            [16] => category2
            [17] => category3
            [18] => category4
            [19] => surcharge*/
            
            //eDebug($createCats,true);
            if(!empty($product->user_input_fields) && is_array($product->user_input_fields))
                $product->user_input_fields = serialize($product->user_input_fields);
            //eDebug($product->user_input_fields);                
            
            if(!empty($product->user_input_fields) && !is_array($product->user_input_fields))
                $product->user_input_fields = str_replace("'","\'",$product->user_input_fields);

            //eDebug($product->user_input_fields,true);                
            $product->save(false);
            //eDebug($product->body);
            
            //sort order and categories             
            if ($product->parent_id==0) 
            {
                $product->saveCategories($createCats,$createCatsRank);                
                //eDebug($createCatsRank);
            }
            echo "Sucessfully imported row " . $count . ", product: " . $product->title . "<br/>";
            //eDebug($product);
        
        }   
        
        if(count($errorSet))
        {
            echo "<br/><hr><br/><style color:'red'>The following records were NOT imported:<br/>";
            foreach ($errorSet as $row=>$err)
            {
                echo "Row: " . $row . ". Reason:<br/>";
                if (is_array($err))
                {
                    foreach ($err as $e)
                    {
                        echo "--" . $e . "<br/>";
                    }
                }
                else echo "--" . $err . "<br/>";
            }
            echo "</style>";
        }    
    }
    
    function export() {
        eDebug($this->params);        
    }
    
    function manage() {
	
        global $available_controllers;
        $importDD = array();
        $exportDD = array();
        foreach ($available_controllers as $key=>$path) {
			if(strpos($key, "Controller") !== false) {
				$c = new $key();
				if ($c->canImportData()) $importDD[$key] = $c->name(); 
				if ($c->canExportData()) $exportDD[$key] = $c->name();
			}
        }
        
        assign_to_template(array(
            'importDD'=>$importDD, 
            'exportDD'=>$exportDD, 
        ));
        
    }
}

?>