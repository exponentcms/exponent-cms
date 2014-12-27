<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
 * @package    Modules
 */

class importexportController extends expController {
    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    //protected $permissions = array_merge(array("test"=>'Test'), array('copyProduct'=>"Copy Product"));
    protected $add_permissions = array(
        'import' => 'Import Data',
        'export' => 'Export Data'
    );

    static function displayname() {
        return gt("Data Import / Export Module");
    }

    static function description() {
        return gt("Use this module to import and export data from your Exponent website.");
    }

    static function hasSources() {
        return false;
    }

    static function hasContent() {
        return false;
    }

//    function __construct($src = null, $params = array()) {
//        parent::__construct($src, $params);
//    }

    function manage() {
        global $available_controllers;

        expHistory::set('manageable', $this->params);
        $importDD = array();
        $exportDD = array();
        foreach ($available_controllers as $key => $path) {
            if (strpos($key, "Controller") !== false) {
                $c = new $key();
                if ($c->canImportData()) $importDD[$key] = $c->name();
                if ($c->canExportData()) $exportDD[$key] = $c->name();
            }
        }
        assign_to_template(array(
            'importDD' => $importDD,
            'exportDD' => $exportDD,
        ));
    }

    function import() {
        $type = expModules::getController($this->params['import_type']);
        if (method_exists($type, 'import')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'import'));
        }

        $pullable_modules = expModules::listInstalledControllers($type->baseclassname);
        $modules = new expPaginator(array(
            'records' => $pullable_modules,
            'controller' => $this->loc->mod,
            'action' => $this->params['action'],
            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns' => array(
                gt('Title') => 'title',
                gt('Page')  => 'section'
            ),
        ));

        assign_to_template(array(
            'modules' => $modules,
            'import_type' => $type->baseclassname
        ));
    }

    function import_select() {
        $type = expModules::getController($this->params['import_type']);
        if (method_exists($type, 'import_select')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'import_select'));
        }

        //Get the temp directory to put the uploaded file
        $directory = "tmp";

        //Get the file save it to the temp directory
        if ($_FILES["import_file"]["error"] == UPLOAD_ERR_OK) {
            $file = expFile::fileUpload("import_file", false, false, time() . "_" . $_FILES['import_file']['name'], $directory.'/');
            if ($file == null) {
                switch ($_FILES["import_file"]["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->params['_formError'] = gt('The file you attempted to upload is too large.  Contact your system administrator if this is a problem.');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->params['_formError'] = gt('The file was only partially uploaded.');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->params['_formError'] = gt('No file was uploaded.');
                        break;
                    default:
                        $this->params['_formError'] = gt('A strange internal error has occurred.  Please contact the Exponent Developers.');
                        break;
                }
                expSession::set("last_POST", $this->params);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit("");
            } else {
                $errors = array();
                //TODO this will crash on large .eql files
                $data = expFile::parseDatabase(BASE . $directory . "/" . $file->filename, $errors, $type->model_table);
                if (!empty($errors)) {
                    $message = gt('Importing encountered the following errors') . ':<br>';
                    foreach ($errors as $error) {
                        $message .= '* ' . $error . '<br>';
                    }
                    flash('error', $message);
                }

                assign_to_template(array(
                    'import_type' => $this->params['import_type'],
                    'items' => $data[$type->model_table]->records,
                    'filename' => $directory . "/" . $file->filename,
                    'source' => $this->params['import_aggregate'][0]
               ));
            }
        }
    }

    function import_process() {
        $type = expModules::getController($this->params['import_type']);
        if (method_exists($type, 'import_process')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'import_process'));
        }

        $filename = $this->params['filename'];
        $src = $this->params['source'];
        $selected = $this->params['items'];
        $errors = array();
        $model = new $type->basemodel_name;
        $tables = array();
        $attached = $model->getAttachableItemTables();
        foreach ($attached as $link=>$model) {
            $tables[] = $link;
            $attach = new $model;
            $tables[] = $attach->tablename;
        }
        array_unshift($tables, $type->model_table);
        //TODO this will crash on large .eql files
        $data = expFile::parseDatabase(BASE . $filename, $errors, $tables);

        // parse out attachments data using the content_id for easier access
        $attachments = array();
        foreach ($attached as $link=>$model) {
            if (!empty($data[$link]->records)) {
                $attachments[$link] = array();
                foreach ($data[$link]->records as $item) {
                    $attachments[$link][$item['content_id']] = $item;
                }
                $attach = new $model;
                foreach ($data[$attach->tablename]->records as $item) {
                    $attachments[$link][$item['id']]['content'] = $item;
                }
            }
        }

        foreach ($selected as $select) {
            $current_id = $data[$type->model_table]->records[$select]['id'];
            unset($data[$type->model_table]->records[$select]['id']);
            unset($data[$type->model_table]->records[$select]['sef_url']);
            unset($data[$type->model_table]->records[$select]['rank']);
            $data[$type->model_table]->records[$select]['location_data'] = serialize(expCore::makeLocation($type->baseclassname, $src));
            $item = new $type->basemodel_name($data[$type->model_table]->records[$select]);
            $item->update();

            if ($this->params['import_attached']) {
                $params = null;;
                foreach ($attached as $link=>$model) {
                    foreach ($attachments[$link] as $aitem) {
                        if ($aitem['content_id'] == $current_id) {
                            //$item is content_ record
                            //$item['content'] is the attachment
                            switch ($model) {
                                case 'expCat':
                                    $cat = new expCat($aitem['content']['title']);
                                    if (empty($cat->id)) {
                                        $cat->title = $aitem['content']['title'];
                                        $cat->module = $type->baseclassname;
                                        $cat->save();
                                    }
                                    $params['expCat'][] = $cat->id;
                                    break;
                                case 'expComment':
                                    unset($aitem['content']['id']);
                                    $comment = new expComment($aitem['content']);
                                    $comment->update();  // create and attach the comment
                                    $comment->attachComment($type->baseclassname, $item->id, $aitem['subtype']);
                                    break;
                                case 'expFile':
                                    //FIXME we can't handle file attachments since this is only a db import
                                    break;
                                case 'expTag':
                                    $tag = new expTag($aitem['content']['title']);
                                    if (empty($tag->id))
                                        $tag->update(array('title'=>$aitem['content']['title']));
                                    $params['expTag'][] = $tag->id;
                                    break;
                            }
                        }
                    }
                }
                $item->update($params);  // add expCat & expTag attachments to item
            }
        }
        unlink($this->params['filename']);
        flash('message', count($selected) . ' ' . $type->baseclassname . ' ' . gt('items were imported.'));
        expHistory::back();
    }

    function export() {
        $type = expModules::getController($this->params['export_type']);
        if (method_exists($type, 'export')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'export'));
        }

        $pullable_modules = expModules::listInstalledControllers($type->baseclassname);
        $modules = new expPaginator(array(
            'records' => $pullable_modules,
            'controller' => $this->loc->mod,
            'action' => $this->params['action'],
            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns' => array(
                gt('Title') => 'title',
                gt('Page')  => 'section'
            ),
        ));
        assign_to_template(array(
            'modules' => $modules,
            'export_type' => $type->baseclassname
        ));
    }

    function export_process() {
        $type = expModules::getController($this->params['export_type']);
        if (method_exists($type, 'export_process')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'export_process'));
        }

        if (!empty($this->params['export_aggregate'])) {
            $tables = array($type->model_table);
            $selected = $this->params['export_aggregate'];
            $where = '(';
            foreach ($selected as $key=>$src) {
                if ($key) $where .= ' OR ';
                $where .= "location_data='" . serialize(expCore::makeLocation($type->baseclassname, $src)) . "'";
            }
            $where .= ')';
            $awhere[] = $where;

            if ($this->params['export_attached']) {
                $model = new $type->basemodel_name;
                foreach ($model->getAttachableItemTables() as $link=>$model) {
                    $tables[] = $link;
                    $awhere[] = "content_type='" . $type->baseclassname . "'";
                    $attach = new $model;
                    $tables[] = $attach->tablename;
                    $awhere[] = '';
                }
            }

            $filename = $type->baseclassname . '.eql';

            ob_end_clean();
            ob_start("ob_gzhandler");

            // 'application/octet-stream' is the registered IANA type but
            //        MSIE and Opera seems to prefer 'application/octetstream'
            $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';

            header('Content-Type: ' . $mime_type);
            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            // IE need specific headers
            if (EXPONENT_USER_BROWSER == 'IE') {
                header('Content-Disposition: inline; filename="' . $filename . '"');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
            }
            echo expFile::dumpDatabase($tables, 'export', $awhere);  //TODO we need to echo inside call
            exit; // Exit, since we are exporting
        }
        expHistory::back();
    }

    function parseCategory($data) {
        global $db;
        if (!empty($data)) {
            $cats1 = explode("::", trim($data));
            //eDebug($cats1);
            $cats1count = count($cats1);
            $counter = 1;
            $categories1 = array();
            foreach ($cats1 as $cat) {
                //eDebug($cat);
                if ($counter == 1) $categories1[$counter] = $db->selectObject('storeCategories', 'title="' . $cat . '" AND parent_id=0');
                else $categories1[$counter] = $db->selectObject('storeCategories', 'title="' . $cat . '" AND parent_id=' . $categories1[$counter - 1]->id);
                //eDebug($categories1);
                if (empty($categories1[$counter]->id)) {
                    return "'" . $cat . "' ".gt('of the set').": '" . $data . "' ".gt("is not a valid category").".";
                }

                if ($counter == $cats1count) {
                    return $categories1[$counter]->id;
                }
                $counter++;
            }
            //eDebug($createCats);
            //eDebug($categories1,true);
        } else {
            return gt("Category was empty.");
        }
    }

    function validate() {
//        global $db;
        //eDebug($this->params,true); 
        set_time_limit(0);
        //$file = new expFile($this->params['expFile']['import_file'][0]);
        if (!empty($_FILES['import_file']['error'])) {
            flash('error', gt('There was an error uploading your file.  Please try again.'));
            redirect_to(array('controller' => 'store', 'action' => 'import_external_addresses'));
        }

        $file = new stdClass();
        $file->path = $_FILES['import_file']['tmp_name'];
        echo gt("Attempting import")."...<br/>";

        $checkhandle = fopen($file->path, "r");
        $checkdata = fgetcsv($checkhandle, 10000, ",");
        $fieldCount = count($checkdata);

        $count = 1;
        while (($checkdata = fgetcsv($checkhandle, 10000, ",")) !== FALSE) {
            $count++;
            if (count($checkdata) != $fieldCount) {
                echo gt("Line ") . $count . " ".gt("of your CSV import file does not contain the correct number of columns.")."<br/>";
                echo gt("Found")." " . $fieldCount . " ".gt("header fields, but only")." " . count($checkdata) . " ".gt("field in row")." " . $count . " ".gt("Please check your file and try again.");
                exit();
            }
        }
        fclose($checkhandle);

        echo "<br/>" . gt("CSV File passed validation") . "...<br/>";

        if ($this->params['import_type'] == 'store') $this->importProduct($file);
        //else if($this->params['import_type'] == 'address') $this->importAddresses($file);
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
            
            echo "Successfully imported row " . $count . ", name: " . $extAddy->firstname . " " . $extAddy->lastname . "<br/>";
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

    function importProduct($file=null) {
        if (empty($file->path)) {
            $file->path = $_FILES['import_file']['tmp_name'];
        }
        $handle = fopen($file->path, "r");
        $data = fgetcsv($handle, 10000, ",");
        //eDebug($data);        
        foreach ($data as $value) {
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
            4=body
            5=model
            6=warehouse_location
            7=sef_url
//FIXME        this is where canonical should be
            8=meta_title
            9=meta_keywords
            10=meta_description
            11=tax_class_id
            12=quantity
            13=availability_type
            14=base_price
            15=special_price
            16=use_special_price
            17=active_type
            18=product_status_id
            19=category1
            20=category2
            21=category3
            22=category4
            ..
            30=category12
            31=surcharge
            32=rank category_rank
            33=feed_title
            34=feed_body
        */

        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;

            //eDebug($data, true);
            if (isset($data[0]) && $data[0] != 0) {
                $product = new product($data[0], false, false);
                if (empty($product->id)) {
                    $errorSet[$count] = $product->id . " ".gt("is not a valid product ID.");
                    continue;
                }
            } else {
                //$errorSet[$count] = "Product ID not supplied.";
                //continue;
                $product = new product();
                //$product->save(false);
            }

            $checkTitle = trim($data[3]);
            if (empty($checkTitle)) {
                $errorSet[$count] = gt("No product name (title) supplied, skipping this record...");
                continue;
            }
            $product->parent_id = $data[1];
            $product->child_rank = $data[2];
            $product->title = stripslashes(stripslashes($data[3]));
            $product->body = utf8_encode(stripslashes(expString::parseAndTrimImport(($data[4]), true)));
            //$product->body = utf8_encode(stripslashes(stripslashes(($data[4]))));            
            $product->model = stripslashes(stripslashes($data[5]));
            $product->warehouse_location = stripslashes(stripslashes($data[6]));
            $product->sef_url = stripslashes(stripslashes($data[7]));
//FIXME        this is where canonical should be
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

            if (empty($product->id)) $product->minimum_order_quantity = 1;

            if ($product->parent_id == 0) {
                $createCats = array();
                $createCatsRank = array();
                for ($x = 19; $x <= 30; $x++) {
                    if (!empty($data[$x])) $result = $this->parseCategory($data[$x]);
                    else continue;

                    if (is_numeric($result)) {
                        $createCats[] = $result;
                        $createCatsRank[$result] = $data[32];
                    } else {
                        $errorSet[$count][] = $result;
                        continue 2;
                    }
                }
            }

            //eDebug($createCats,true);
            if (!empty($product->user_input_fields) && is_array($product->user_input_fields))
                $product->user_input_fields = serialize($product->user_input_fields);
            //eDebug($product->user_input_fields);                

            if (!empty($product->user_input_fields) && !is_array($product->user_input_fields))
                $product->user_input_fields = str_replace("'", "\'", $product->user_input_fields);

            //eDebug($product->user_input_fields,true);                
            $product->save(false);
            //eDebug($product->body);

            //sort order and categories             
            if ($product->parent_id == 0) {
                $product->saveCategories($createCats, $createCatsRank);
                //eDebug($createCatsRank);
            }
            echo "Successfully imported row " . $count . ", product: " . $product->title . "<br/>";
            //eDebug($product);

        }

        if (count($errorSet)) {
            echo "<br/><hr><br/><style color:'red'>".gt('The following records were NOT imported').":<br/>";
            foreach ($errorSet as $row => $err) {
                echo "Row: " . $row . ". Reason:<br/>";
                if (is_array($err)) {
                    foreach ($err as $e) {
                        echo "--" . $e . "<br/>";
                    }
                } else echo "--" . $err . "<br/>";
            }
            echo "</style>";
        }
    }

}

?>