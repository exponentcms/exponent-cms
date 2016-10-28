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
 * @package    Modules
 */

class importexportController extends expController {
    protected $add_permissions = array(
        'import' => 'Import Data',
        'export' => 'Export Data'
    );
    protected $manage_permissions = array(
        'importProduct' => 'Import Product',
    );
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

        if (empty($this->params['import_aggregate'])) {
            expValidator::setErrorField('import_aggregate[]');
            expValidator::failAndReturnToForm(gt('You must select a module.'), $this->params);
        }

        //Get the temp directory to put the uploaded file
        $directory = "tmp";

        //Get the file save it to the temp directory
        if (!empty($_FILES["import_file"]) && $_FILES["import_file"]["error"] == UPLOAD_ERR_OK) {
            $file = expFile::fileUpload("import_file", false, false, time() . "_" . $_FILES['import_file']['name'], $directory.'/');
            if ($file === null) {
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
                $data = expFile::parseDatabase(BASE . $directory . "/" . $file->filename, $errors, $type->model_table);  //FIXME this may crash on large .eql files
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
        } else {
            expValidator::setErrorField('import_file');
            expValidator::failAndReturnToForm(gt('File failed to upload.'), $this->params);  // file upload error
        }
    }

    function import_process() {
        $type = expModules::getController($this->params['import_type']);
        if (method_exists($type, 'import_process')) {  // allow for controller specific method
            redirect_to(array('controller'=>$type->baseclassname, 'action'=>'import_process'));
        }

        if (!count($this->params['items'])) {
            expValidator::setErrorField('items');
            expValidator::failAndReturnToForm(gt('You must select at least one item.'), $this->params);
        }

        if (!empty($this->params['filename']) && (strpos($this->params['filename'], 'tmp/') === false || strpos($this->params['folder'], '..') !== false)) {
            header('Location: ' . URL_FULL);
            exit();  // attempt to hack the site
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
        $data = expFile::parseDatabase(BASE . $filename, $errors, $tables);  //FIXME this may crash on large .eql files

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
            unset(  // clear out the stuff that gets auto-set when integrated into existing records
                $data[$type->model_table]->records[$select]['id'],
                $data[$type->model_table]->records[$select]['sef_url'],
                $data[$type->model_table]->records[$select]['rank']
            );
            $data[$type->model_table]->records[$select]['location_data'] = serialize(expCore::makeLocation($type->baseclassname, $src));
            $item = new $type->basemodel_name($data[$type->model_table]->records[$select]);  // create new populated record to auto-set things
            $item->update();

            if ($this->params['import_attached']) {
                $params = array();;
                foreach ($attached as $link=>$model) {
                    foreach ($attachments[$link] as $aitem) {
                        if ($aitem['content_id'] == $current_id) {
                            //$item is content_ record
                            //$item['content'] is the attachment
                            switch ($model) {
                                case 'expCat':
                                    foreach ($data['expCats']->records as $key=>$ct) {
                                        if ($ct['id'] == $aitem['expcats_id']) {
                                            $cat = new expCat($ct['title']);
                                            if (empty($cat->id)) {
                                                $cat->title = $ct['title'];
                                                $cat->module = $type->baseclassname;
                                                $cat->save();
                                            }
                                            $params['expCat'][] = $cat->id;
                                        }
                                    }
                                    break;
                                case 'expComment':
                                    foreach ($data['expComments']->records as $key=>$cm) {
                                        unset($cm['id']);
                                        $cm['parent_id'] = 0; //fixme this flattens reply comments
                                        $comment = new expComment($cm);
                                        $comment->update();  // create and attach the comment
                                        $comment->attachComment($type->baseclassname, $item->id, $aitem['subtype']);
                                    }
                                    break;
                                case 'expFile':
                                    //FIXME we can't handle file attachments since this is only a db import
                                    break;
                                case 'expTag':
                                    foreach ($data['expTags']->records as $key=>$tg) {
                                        if ($tg['id'] == $aitem['exptags_id']) {
                                            $tag = new expTag($tg['title']);
                                            if (empty($tag->id))
                                                $tag->update(array('title'=>$tg['title']));
                                            $params['expTag'][] = $tag->id;
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
                $item->update($params);  // add expCat & expTag attachments to item
            }
        }
        unlink($this->params['filename']);

        // update search index
        $type->addContentToSearch();

        flashAndFlow('message', count($selected) . ' ' . $type->baseclassname . ' ' . gt('items were imported.'));
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
            echo expFile::dumpDatabase($tables, 'export', $awhere);  //FIXME we need to echo inside call
            exit; // Exit, since we are exporting
        }
        expHistory::back();
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

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
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
        ini_set('auto_detect_line_endings',$line_end);

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
            $file = new stdClass();
            $file->path = $_FILES['import_file']['tmp_name'];
        }
        if (empty($file->path)) {
            echo gt('Not a Product Import CSV File');
            return;
        }
        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $handle = fopen($file->path, "r");

        // read in the header line
        $header = fgetcsv($handle, 10000, ",");
        if (!($header[0] == 'id' || $header[0] == 'model')) {
            echo gt('Not a Product Import CSV File');
            return;
        }

        $count = 1;
        $errorSet = array();
        $product = null;
        /*  original order of columns
            0=id
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
            35=weight
            36=height
            37=width
            38=length
            39=companies_id
            40=image1 url to mainimage to download
            41=image2 url to additional image to download
            ..
            44=image5 url to additional image to download
*/

        // read in the data lines
//        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
        while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $count++;
            $createCats = array();
            $createCatsRank = array();
            $data = array_combine($header, $row);

            //eDebug($data, true);
            if ($header[0] == 'id') {
                if (isset($data['id']) && $data['id'] != 0) {
                    $product = new product($data['id'], false, false);
                    if (empty($product->id)) {
                        $errorSet[$count] = gt("Is not an existing product ID.");
                        continue;
                    }
                } else {
                    //$errorSet[$count] = "Product ID not supplied.";
                    //continue;
                    $product = new product();
                    //$product->save(false);
                }
            } elseif ($header[0] == 'model') {
                if (!empty($data['model'])) {
                    $p = new product();
                    $product = $p->find('first','model="' . $data['model'] . '"');
                    if (empty($product->id)) {
                        $errorSet[$count] = gt("Is not an existing product SKU/Model.");
                        continue;
                    }
                } else {
                    $product = new product();
                }
            }
            if ($product->product_type != 'product') {
                $errorSet[$count] = gt("Existing product is wrong product type.");
                continue;
            }

            // new products must have a title
            if (empty($product->id)) {  // new product require mandatory values
                $checkTitle = trim($data['title']);
                if (empty($checkTitle)) {
                    $errorSet[$count] = gt("No product name (title) supplied.");
                    continue;
                }
                $product->minimum_order_quantity = 1;
            }

            // parse $data columns
            foreach ($data as $key=>$value) {
                $value = trim($value);
                switch ($key) {
                    case 'parent_id': // integer
                    case 'child_rank':
                    case 'tax_class_id':
                    case 'quantity':
                    case 'availability_type':
                    case 'use_special_price':
                    case 'active_type':
                    case 'product_status_id':
                        $product->$key = intval($value);
                        break;
                    case 'companies_id':
                        if (is_numeric($value)) {
                            $product->$key = intval($value);
                        } elseif (!empty($value)) {  // it's a company name, not a company id#
                            $co = new company();
                            $company = $co->find('first', 'title=' . $value);
                            if (empty($company->id)) {
                                $params['title'] = $value;
                                $company->update();
                            }
                            $product->$key = $company->id;
                        }
                        break;
                    case 'sef_url':
                        $product->$key = stripslashes(stripslashes($value));
                        if (!is_bool(expValidator::uniqueness_of('sef_url', $product, array()))) {
                            $product->makeSefUrl();
                        }
                        break;
                    case 'title':  // string
                    case 'model':
                    case 'warehouse_location':
                    case 'meta_title':
                    case 'meta_keywords':
                    case 'meta_description':
                    case 'feed_title':
                    case 'feed_body':
                        $product->$key = stripslashes(stripslashes($value));
                        break;
                    case 'body':
                        $product->$key = utf8_encode(stripslashes(expString::parseAndTrimImport(($value), true)));
                        break;
                    case 'base_price':  // float
                    case 'special_price':
                    case 'surcharge':
                    case 'weight':
                    case 'height':
                    case 'width':
                    case 'length':
                        $product->$key = floatval($value);
                        break;
                    case 'image1':
                    case 'image2':
                    case 'image3':
                    case 'image4':
                    case 'image5':
                        if (!empty($value)) {
                            $product->save(false);
                            if (is_integer($value)) {
                                $_objFile = new expFile ($value);
                            } else {
                                // import image from url
                                $_destFile = basename($value);  // get filename from end of url
                                $_destDir = UPLOAD_DIRECTORY_RELATIVE;
                                $_destFullPath = BASE . $_destDir . $_destFile;
                                if (file_exists($_destFullPath)) {
                                    $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
                                    $_destFullPath = BASE . $_destDir . $_destFile;
                                }

                                expCore::saveData($value, $_destFullPath);  // download the image

                                if (file_exists($_destFullPath)) {
                                    $__oldumask = umask(0);
                                    chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
                                    umask($__oldumask);

                                    // Create a new expFile Object
                                    $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
                                    $_objFile = new expFile ($_fileParams);
                                    $_objFile->save();
                                }
                            }
                            // attach product images expFile object
                            if (!empty($_objFile->id)) {
                                if ($key == 'image1') {
                                    $product->attachItem($_objFile, 'mainimage');
                                } else {
                                    $product->attachItem($_objFile, 'images', false);
                                }
                            }
                        }
                        break;
                    case 'category1':
                    case 'category2':
                    case 'category3':
                    case 'category4':
                    case 'category5':
                    case 'category6':
                    case 'category7':
                    case 'category8':
                    case 'category9':
                    case 'category10':
                    case 'category11':
                    case 'category12':
                        if ($product->parent_id == 0) {
//                            $rank = !empty($data['rank']) ? $data['rank'] : 1;
                            $rank = intval(str_replace('category', '', $key));
//                            if (!empty($value)) $result = storeCategory::parseCategory($value);
                            if (!empty($value)) $result = storeCategory::importCategoryString($value);
                            else continue;

//                            if (is_numeric($result)) {
                            if ($result) {
                                $createCats[] = $result;
                                $createCatsRank[$result] = $rank;
                            } else {
                                $errorSet[$count][] = $result;
                                continue 2;
                            }
                        }
                        break;
                    default:
                        if (property_exists('product', $key)) {
                            $product->key = $value;
                        }
                }
            }

//            $checkTitle = trim($data['title']);
//            if (empty($checkTitle)) {
//                $errorSet[$count] = gt("No product name (title) supplied, skipping this record...");
//                continue;
//            }
//            $product->parent_id = $data[1];
//            $product->child_rank = $data[2];
//            $product->title = stripslashes(stripslashes($data[3]));
//            $product->body = utf8_encode(stripslashes(expString::parseAndTrimImport(($data[4]), true)));
//            //$product->body = utf8_encode(stripslashes(stripslashes(($data[4]))));
//            $product->model = stripslashes(stripslashes($data[5]));
//            $product->warehouse_location = stripslashes(stripslashes($data[6]));
//            $product->sef_url = stripslashes(stripslashes($data[7]));
////FIXME        this is where canonical should be
//            $product->meta_title = stripslashes(stripslashes($data[8]));
//            $product->meta_keywords = stripslashes(stripslashes($data[9]));
//            $product->meta_description = stripslashes(stripslashes($data[10]));
//
//            $product->tax_class_id = $data[11];
//
//            $product->quantity = $data[12];
//
//            $product->availability_type = $data[13];
//
//            $product->base_price = $data[14];
//            $product->special_price = $data[15];
//            $product->use_special_price = $data[16];
//            $product->active_type = $data[17];
//            $product->product_status_id = $data[18];
//
//            $product->surcharge = $data[31];
//            $product->feed_title = stripslashes(stripslashes($data[33]));
//            $product->feed_body = stripslashes(stripslashes($data[34]));
//            if (!empty($data[35])) $product->weight = $data[35];
//            if (!empty($data[36])) $product->height = $data[36];
//            if (!empty($data[37])) $product->width = $data[37];
//            if (!empty($data[38])) $product->length = $data[38];
//            if (!empty($data[39])) $product->companies_id = $data[39];
//            if (!empty($data[40])) {
//                // import image from url
//                $_destFile = basename($data[40]);  // get filename from end of url
//                $_destDir = UPLOAD_DIRECTORY_RELATIVE;
//                $_destFullPath = BASE . $_destDir . $_destFile;
//                if (file_exists($_destFullPath)) {
//                    $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
//                    $_destFullPath = BASE . $_destDir . $_destFile;
//                }
//
//                expCore::saveData($data[40], $_destFullPath);  // download the image
//
//                if (file_exists($_destFullPath)) {
//                    $__oldumask = umask(0);
//                    chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
//                    umask($__oldumask);
//
//                    // Create a new expFile Object
//                    $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
//                    $_objFile = new expFile ($_fileParams);
//                    $_objFile->save();
//                    // attach/replace product main image with new expFile object
//                    $product->attachItem($_objFile, 'mainimage');
//                }
//            }
//            for ($i=41; $i<=44; $i++) {
//                if (!empty($data[$i])) {
//                    // import image from url
//                    $_destFile = basename($data[$i]);  // get filename from end of url
//                    $_destDir = UPLOAD_DIRECTORY_RELATIVE;
//                    $_destFullPath = BASE . $_destDir . $_destFile;
//                    if (file_exists($_destFullPath)) {
//                        $_destFile = expFile::resolveDuplicateFilename($_destFullPath);
//                        $_destFullPath = BASE . $_destDir . $_destFile;
//                    }
//
//                    expCore::saveData($data[$i], $_destFullPath);  // download the image
//
//                    if (file_exists($_destFullPath)) {
//                        $__oldumask = umask(0);
//                        chmod($_destFullPath, octdec(FILE_DEFAULT_MODE_STR + 0));
//                        umask($__oldumask);
//
//                        // Create a new expFile Object
//                        $_fileParams = array('filename' => $_destFile, 'directory' => $_destDir);
//                        $_objFile = new expFile ($_fileParams);
//                        $_objFile->save();
//                        // attach product additional images with new expFile object
//                        $product->attachItem($_objFile, 'images', false);
//                    }
//                }
//            }
//
//            if (empty($product->id)) $product->minimum_order_quantity = 1;
//
//            if ($product->parent_id == 0) {
//                $createCats = array();
//                $createCatsRank = array();
//                for ($x = 19; $x <= 30; $x++) {
//                    if (!empty($data[$x])) $result = storeCategory::parseCategory($data[$x]);
//                    else continue;
//
//                    if (is_numeric($result)) {
//                        $createCats[] = $result;
//                        $createCatsRank[$result] = $data[32];
//                    } else {
//                        $errorSet[$count][] = $result;
//                        continue 2;
//                    }
//                }
//            }

            //NOTE: we manipulate existing user input fields to store them properly?
            //eDebug($createCats,true);
            if (!empty($product->user_input_fields) && is_array($product->user_input_fields))
                $product->user_input_fields = serialize($product->user_input_fields);
            //eDebug($product->user_input_fields);

            if (!empty($product->user_input_fields) && !is_array($product->user_input_fields))
                $product->user_input_fields = str_replace("'", "\'", $product->user_input_fields);

            //eDebug($product->user_input_fields,true);
            $product->save(true);
            //eDebug($product->body);

            //sort order and categories
            if ($product->parent_id == 0) {
                $product->saveCategories($createCats, $createCatsRank);
                //eDebug($createCatsRank);
            }
            echo "Successfully imported/updated row " . $count . ", product: " . $product->title . "<br/>";
            //eDebug($product);

        }

        if (count($errorSet)) {
            echo "<br/><hr><br/><div style='color:red'><strong>".gt('The following records were NOT imported').":</strong><br/>";
            foreach ($errorSet as $rownum => $err) {
                echo "Row: " . $rownum;
                if (is_array($err)) {
                    foreach ($err as $e) {
                        echo " -- " . $e . "<br/>";
                    }
                } else echo " -- " . $err . "<br/>";
            }
            echo "</div>";
        }

        fclose($handle);
        ini_set('auto_detect_line_endings',$line_end);

        // update search index
        $this->addContentToSearch();
    }

}

?>