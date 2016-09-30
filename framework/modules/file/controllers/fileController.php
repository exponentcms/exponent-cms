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

class fileController extends expController {
    public $basemodel_name = "expFile";
    protected $remove_permissions = array(
        'delete'
    );
//    protected $manage_permissions = array(
//        'picker'=>'Manage Files',
//        'import'=>'Import',
//        'export'=>'Export',
//    );
    public $requires_login = array(
        'picker'=>'You must be logged in to perform this action',
        'adder'=>'You must be logged in to perform this action',
        'addit'=>'You must be logged in to perform this action',
        'batchDelete'=>'You must be logged in to perform this action',
        'createFolder'=>'You must be logged in to perform this action',
        'deleter'=>'You must be logged in to perform this action',
        'deleteit'=>'You must be logged in to perform this action',
        'edit'=>'You must be logged in to perform this action',
        'quickUpload'=>'You must be logged in to perform this action',
        'upload'=>'You must be logged in to perform this action',
        'uploader'=>'You must be logged in to perform this action',
    );

    static function displayname() { return gt("File Manager"); }
    static function description() { return gt("Add and manage Exponent Files"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }

    public function manage_fixPaths() {
        // fixes file directory issues when the old file class was used to save record
        // where the trailing forward slash was not added. This simply checks to see
        // if the trailing / is there, if not, it adds it.

        $file = new expFile();
        $files = $file->find('all');

        foreach ($files as $key=>$file) {
            if (substr($files[$key]->directory,-1,1)!="/") {
                $files[$key]->directory = $files[$key]->directory.'/';
            }
            $files[$key]->save();
        }

//        eDebug($files,true);
    }

    public function picker() {
//        global $user;

        $expcat = new expCat();
        $cats = $expcat->find('all','module="file"');
        $jscatarray = array();
        $catarray = array();
        $catarray[] = 'Root Folder';
        foreach ($cats as $key=>$cat) {
            $jscatarray[$key]['label'] = $cat->title;
            $jscatarray[$key]['value'] = $cat->id;
            $catarray[$cat->id] = $cat->title;
        }
        $jsuncat['label'] = 'Root';
        $jsuncat['value'] = null;
        array_unshift($jscatarray,$jsuncat);
        $catarray['-1'] = 'All Folders';
        if (strstr($this->params['update'],'?')) {
            $update = explode('?',$this->params['update']);
            if (!empty($update[0])) $this->params['update'] = $update[0];
        }
        assign_to_template(array(
            'update'=>$this->params['update'],
            'filter'=>!empty($this->params['filter'])?$this->params['filter']:null,
            'cats'=>$catarray,
            'jscats'=>json_encode($jscatarray)
        ));
    }

    public function uploader() {
        global $user;
        //expHistory::set('manageable', $this->params);
        flash('message',gt('Upload size limit').': '.ini_get('upload_max_filesize'));
        if(intval(ini_get('upload_max_filesize'))!=intval(ini_get('post_max_size')) && $user->isAdmin()){
            flash('error',gt('In order for the uploader to work correctly, \'"post_max_size\' and \'upload_max_filesize\' within your php.ini file must match one another'));
        }

        $expcat = new expCat();
        $cats = $expcat->find('all','module="file"');
        $catarray = array();
        $catarray[] = 'Root Folder';
        foreach ($cats as $cat) {
            $catarray[$cat->id] = $cat->title;
        }
        assign_to_template(array(
            'update'=>$this->params['update'],
            "upload_size"=>ini_get('upload_max_filesize'),
            "post_size"=>ini_get('post_max_size'),
            "bmax"=>intval(ini_get('upload_max_filesize')/1024*1000000000),
            'cats'=>$catarray,
        ));
    }

    /**
     * Returns attached file view template configuration settings template
     *
     */
    public function get_view_config() {
        global $template;

        // set paths we will search in for the view
        $paths = array(
            BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/file/configure',
            BASE.'framework/modules/common/views/file/configure',
        );

        foreach ($paths as $path) {
            $view = $path.'/'.$this->params['view'].'.tpl';
            if (is_readable($view)) {
                if (bs(true)) {
                    $bstrapview = $path.'/'.$this->params['view'].'.bootstrap.tpl';
                    if (file_exists($bstrapview)) {
                        $view = $bstrapview;
                    }
                }
                if (bs3(true)) {
                    $bstrapview = $path.'/'.$this->params['view'].'.bootstrap3.tpl';
                    if (file_exists($bstrapview)) {
                        $view = $bstrapview;
                    }
                }
                $template = new controllertemplate($this, $view);
                $ar = new expAjaxReply(200, 'ok');
		        $ar->send();
            }
        }
    }

    /**
     * Returns view template configuration settings view template
     *
     */
    public function get_module_view_config() {
        global $template;

//        $controller = new $this->params['mod'];
        $controller = expModules::getController($this->params['mod']);
        // set paths we will search in for the view
        $paths = array(
//            BASE.'themes/'.DISPLAY_THEME.'/modules/'.$this->params['mod'].'/views/'.$this->params['mod'].'/configure',
//            BASE.'framework/modules/'.$this->params['mod'].'/views/'.$this->params['mod'].'/configure',
            $controller->viewpath.'/configure',
  	        BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/configure'
        );

        $config_found = false;
        foreach ($paths as $path) {
            $view = $path.'/'.$this->params['view'].'.config';
            if (is_readable($view)) {
                if (bs(true)) {
                    $bstrapview = $path.'/'.$this->params['view'].'.bootstrap.config';
                    if (file_exists($bstrapview)) {
                        $view = $bstrapview;
                    }
                }
                if (bs3(true)) {
                    $bstrapview = $path.'/'.$this->params['view'].'.bootstrap3.config';
                    if (file_exists($bstrapview)) {
                        $view = $bstrapview;
                    }
                }
                $template = new controllertemplate($this, $view);
                $config_found = true;
            }
        }
        $parts = explode('_', $this->params['view']);
        if (!$config_found && ($this->params['view'] != $parts[0])) {
            foreach ($paths as $path) {
                $actview = $path.'/'.$parts[0].'.config';
                if (is_readable($actview)) {
                    if (bs(true)) {
                        $bstrapview = $path . '/' . $actview . '.bootstrap.config';
                        if (file_exists($bstrapview)) {
                            $actview = $bstrapview;
                        }
                    }
                    if (bs3(true)) {
                        $bstrapview = $path . '/' . $actview . '.bootstrap3.config';
                        if (file_exists($bstrapview)) {
                            $actview = $bstrapview;
                        }
                    }
                    $template = new controllertemplate($this, $actview);
                    $config_found = true;
                }
            }
        }
        if (!$config_found) {
            echo "<p>".gt('There Are No View Specific Settings')."</p>";
            $template = expTemplate::get_common_template('blank', null);
        }

//        expTemplate::get_config_template($this);
        $ar = new expAjaxReply(200, 'ok');
        $ar->send();
    }

    /**
     * Get a file record by id or pathname and return it as JSON via Ajax
     */
    public function getFile() {
        if (is_numeric($this->params['id'])) {
            $file = new expFile($this->params['id']);
        } else {
            $efile = new expFile();
            $path = str_replace(BASE, '', $this->params['id']);
            $path = str_replace('\\', '/', $path);
            $file = $efile->find('first','directory="'.dirname($path).'/'.'" AND filename="'.basename($path).'"');
        }
        $ar = new expAjaxReply(200, 'ok', $file);
        $ar->send();
    }

    public function getFilesByJSON() {
        global $user;

        $modelname = $this->basemodel_name;
        $results = 25; // default get all
        $startIndex = 0; // default start at 0
//        $sort = null; // default don't sort
//        $dir = 'asc'; // default sort dir is asc
//        $sort_dir = SORT_ASC;

        // How many records to get?
        if(strlen($this->params['results']) > 0) {
            $results = intval($this->params['results']);
        }

        // Start at which record?
        if(strlen($this->params['startIndex']) > 0) {
            $startIndex = intval($this->params['startIndex']);
        }

        // Sorted?
        if(strlen($this->params['sort']) > 0) {
            if ($this->params['sort'] == 'cat') {
                $sort = 'id';
            } else {
                $sort = expString::escape($this->params['sort']);
            }
//            if ($sort = 'id') $sort = 'filename';
        }

        // Sort dir?
        if (($this->params['dir'] == 'false') || ($this->params['dir'] == 'desc') || ($this->params['dir'] == 'yui-dt-desc')) {
            $dir = 'desc';
            $sort_dir = SORT_DESC;
        } else {
            $dir = 'asc';
            $sort_dir = SORT_ASC;
        }
        $totalrecords = 0;

        if (!empty($this->params['query'])) {
            $this->params['query'] = expString::escape($this->params['query']);
            $filter = '';
            if (!$user->isAdmin()) {
                $filter = "(poster=".$user->id." OR shared=1) AND ";
            };
//            if ($this->params['update']=='ck' || $this->params['update']=='tiny') {
            if (!empty($this->params['filter']) && $this->params['filter'] == 'image') {
                $filter .= "is_image=1 AND ";
            }

//            $this->params['query'] = expString::sanitize($this->params['query']);
//            $totalrecords = $this->$modelname->find('count',"filename LIKE '%".$this->params['query']."%' OR title LIKE '%".$this->params['query']."%' OR alt LIKE '%".$this->params['query']."%'");
//            $files = $this->$modelname->find('all',$filter."filename LIKE '%".$this->params['query']."%' OR title LIKE '%".$this->params['query']."%' OR alt LIKE '%".$this->params['query']."%'".$imagesOnly,$sort.' '.$dir, $results, $startIndex);
            $files = $this->$modelname->find('all',$filter."(filename LIKE '%".$this->params['query']."%' OR title LIKE '%".$this->params['query']."%' OR alt LIKE '%".$this->params['query']."%')",$sort.' '.$dir);

            //FiXME we need to get all records then group by cat, then trim/paginate
            $querycat = !empty($this->params['cat']) ? $this->params['cat'] : '0';
            $groupedfiles = array();
            foreach ($files as $key=>$file) {
                $filecat = !empty($file->expCat[0]->id) ? $file->expCat[0]->id : 0;
                if (($querycat == $filecat || $querycat == -1)) {
                    $totalrecords++;
                    if (count($groupedfiles) < ($startIndex + $results)) {
                        $groupedfiles[$key] = $files[$key];
                        if (!empty($file->expCat[0]->title)) {
                            $groupedfiles[$key]->cat = $file->expCat[0]->title;
                            $groupedfiles[$key]->catid = $file->expCat[0]->id;
                        }
                        $tmpusr = new user($file->poster);
                        $groupedfiles[$key]->user = new stdClass();
                        $groupedfiles[$key]->user->firstname = $tmpusr->firstname;
                        $groupedfiles[$key]->user->lastname = $tmpusr->lastname;
                        $groupedfiles[$key]->user->username = $tmpusr->username;
                    }
                }
            }
            $groupedfiles = array_values(array_filter($groupedfiles));
            $files = array_slice($groupedfiles,$startIndex,$results);

            $returnValue = array(
                'recordsReturned'=>count($files),
                'totalRecords'=>$totalrecords,
                'startIndex'=>$startIndex,
                'sort'=>$sort,
                'dir'=>$dir,
                'pageSize'=>$results,
                'records'=>$files
            );
        } else {
            if (!$user->isAdmin()) {
                $filter = "(poster=".$user->id." OR shared=1)";
            };
//            if ($this->params['update']=='ck' || $this->params['update']=='tiny') {
            if (!empty($this->params['filter']) && $this->params['filter'] == 'image') {
                $filter .= !empty($filter) ? " AND " : "";
                $filter .= "is_image=1";
            }

//            $totalrecords = $this->$modelname->find('count',$filter);
//            $files = $this->$modelname->find('all',$filter,$sort.' '.$dir, $results, $startIndex);
            $files = $this->$modelname->find('all', $filter, $sort.' '.$dir);

            $groupedfiles = array();
            foreach ($files as $key=>$file) {
                if (empty($file->expCat[0]->title)) {
                    $totalrecords++;
                    if (count($groupedfiles) < ($startIndex + $results)) {
                        $groupedfiles[$key] = $files[$key];
    //                    $files[$key]->cat = $file->expCat[0]->title;
    //                    $files[$key]->catid = $file->expCat[0]->id;
                        $tmpusr = new user($file->poster);
                        $groupedfiles[$key]->user = new stdClass();
                        $groupedfiles[$key]->user->firstname = $tmpusr->firstname;
                        $groupedfiles[$key]->user->lastname = $tmpusr->lastname;
                        $groupedfiles[$key]->user->username = $tmpusr->username;
                    }
                }
            }
            $groupedfiles = array_values(array_filter($groupedfiles));
            $files = array_slice($groupedfiles,$startIndex,$results);

            $returnValue = array(
                'recordsReturned'=>count($files),
                'totalRecords'=>$totalrecords,
                'startIndex'=>$startIndex,
                'sort'=>$sort,
                'dir'=>$dir,
                'pageSize'=>$results,
                'records'=>$files
            );

        }

        echo json_encode($returnValue);
    }

    /**
     * create a new virtual folder in response to an ajax request
     * return updated list of virtual folders in response to an ajax request
     */
    public function createFolder() {
        if (!empty($this->params['folder'])) {
            $expcat = new expCat($this->params['folder']);
            if (empty($expcat->id)) {
                $expcat->module = 'file';
                $expcat->title = $this->params['folder'];
                $expcat->update();
            }
//            $this->params['module'] = 'file';
//            $this->params['title'] = $this->params['folder'];
//            parent::update();
            $cats = $expcat->find('all','module="file"','rank');
            $catarray = array();
            $catarray[] = 'Root Folder';
            foreach ($cats as $cat) {
                $catarray[$cat->id] = $cat->title;
            }
            echo json_encode($catarray);
        }
    }

    public function delete() {
//        global $db,$user;
        global $user;

        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->isAdmin()) {
            $file->delete();
            if (unlink($file->directory.$file->filename)) {
                flash('message',$file->filename.' '.gt('was successfully deleted'));
            } else {
                flash('error',$file->filename.' '.gt('was deleted from the database, but could not be removed from the file system.'));
            }
        } else {
            flash('error',$file->filename.' '.gt('wasn\'t deleted because you don\'t own the file.'));
        }
        redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"filter"=>$this->params['filter']));
    }

    public function deleter() {
//        global $db;

        $notafile = array();
//        $files = $db->selectObjects('expFiles',1);
        foreach (expFile::selectAllFiles() as $file) {
            if (!is_file(BASE.$file->directory.$file->filename)) {
                $notafile[$file->id] = $file;
            }
        }
        assign_to_template(array(
            'files'=>$notafile
        ));
    }

    public function deleteit() {
        global $user;
        if (!empty($this->params['deleteit'])) {
            foreach ($this->params['deleteit'] as $file) {
                $delfile = new expFile($file);
                if ($user->id==$delfile->poster || $user->isAdmin()) {
                    $delfile->delete();
                    flash('error',$delfile->filename.' '.gt('was deleted from the database.'));
                }
            }
        }
        redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"filter"=>$this->params['filter']));
    }

    public function batchDelete() {
        global $user;

        $error = false;
//        if (get_magic_quotes_gpc()) $this->params['files'] = stripslashes($this->params['files']);  // magic quotes fix
        $this->params['files'] = stripslashes($this->params['files']);
        $files = json_decode($this->params['files']);
        switch (json_last_error()) {  //FIXME json error checking/reporting, may no longer be needed
            case JSON_ERROR_NONE:
            break;
            case JSON_ERROR_DEPTH:
                $error = 'JSON - Maximum stack depth exceeded';
            break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'JSON - Underflow or the modes mismatch';
            break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'JSON - Unexpected control character found';
            break;
            case JSON_ERROR_SYNTAX:
                $error = 'JSON - Syntax error, malformed JSON';
            break;
            case JSON_ERROR_UTF8:
                $error = 'JSON - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
            default:
                $error = 'JSON - Unknown error';
            break;
        }

        if (empty($error)) foreach ($files as $file) {
            $delfile = new expFile($file->id);
            if ($user->id==$delfile->poster || $user->isAdmin()) {
                $delfile->delete();
                unlink($delfile->directory.$delfile->filename);
            } else {
                $error = gt("you didn't have permission");
            }
        }
        if (!empty($error)) {
            $ar = new expAjaxReply(300, gt("Some files were NOT deleted because") . ' ' . $error);
        } else {
            $ar = new expAjaxReply(200, gt('Your files were deleted successfully'), $file);
        }
        $ar->send();
    }

    public function adder() {
        global $db;

        $notindb = array();
        $allfiles = expFile::listFlat(BASE.'files',true,null,array(),BASE);
        foreach ($allfiles as $path=>$file) {
            if ($file[0] != '.') {
//                $found = false;
                $npath = preg_replace('/'.$file.'/','',$path, 1);
//                $dbfiles = $db->selectObjects('expFiles',"filename='".$file."' AND directory='".$npath."'");
                $dbfile = $db->selectObject('expFiles',"filename='".$file."' AND directory='".$npath."'");
//                foreach ($dbfiles as $dbfile) {
//                    if (!empty($dbfile)) $found = ($dbfile->directory == str_replace($file,'',$path));
//                }
//                if (!$found) {
//                    $notindb[$path] = $file;
//                }
                if (empty($dbfile)) {
                    $notindb[$path] = $file;
                }
            }
        }
        assign_to_template(array(
            'files'=>$notindb
        ));
    }

    public function addit() {
        foreach ($this->params['addit'] as $file) {
            $newfile = new expFile(array('directory'=>dirname($file).'/','filename'=>basename($file)));
            $newfile->posted = $newfile->last_accessed = filemtime($file);
            $newfile->save();
            flash('message',$newfile->filename.' '.gt('was added to the File Manager.'));
        }
        redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"filter"=>$this->params['filter']));
    }

    public function upload() {

        // upload the file, but don't save the record yet...
        if ($this->params['resize'] != 'false') {
            $maxwidth = $this->params['max_width'];
        } else {
            $maxwidth = null;
        }
        $file = expFile::fileUpload('Filedata',false,false,null,null,$maxwidth);
        // since most likely this function will only get hit via flash in YUI Uploader
        // and since Flash can't pass cookies, we lose the knowledge of our $user
        // so we're passing the user's ID in as $_POST data. We then instantiate a new $user,
        // and then assign $user->id to $file->poster so we have an audit trail for the upload

        if (is_object($file)) {
            $resized = !empty($file->resized) ? true : false;
            $user = new user($this->params['usrid']);
            $file->poster = $user->id;
            $file->posted = $file->last_accessed = time();
            $file->save();
            if (!empty($this->params['cat'])) {
                $expcat = new expCat($this->params['cat']);
                $params['expCat'][0] = $expcat->id;
                $file->update($params);
            }

            // a echo so YUI Uploader is notified of the function's completion
            if ($resized) {
                echo gt('File resized and then saved');
            } else {
                echo gt('File saved');
            }
        } else {
            echo gt('File was NOT uploaded!');
//            flash('error',gt('File was not uploaded!'));
        }
    }

    public function quickUpload(){
        global $user;

        if (!empty($this->params['folder']) || (defined('QUICK_UPLOAD_FOLDER') && QUICK_UPLOAD_FOLDER != '' && QUICK_UPLOAD_FOLDER != 0)) {
            // prevent attempt to place file somewhere other than /files folder
            if (!empty($this->params['folder']) && strpos($this->params['folder'], '..') !== false) {
                $ar = new expAjaxReply(300, gt("File was not uploaded!"));
                $ar->send();
            }
            if (SITE_FILE_MANAGER == 'picker') {
                $quikFolder = !empty($this->params['folder']) ? $this->params['folder'] :QUICK_UPLOAD_FOLDER;
                $destDir = null;
            } elseif (SITE_FILE_MANAGER == 'elfinder') {
                $quikFolder = null;
                $destDir = UPLOAD_DIRECTORY_RELATIVE . (!empty($this->params['folder']) ? $this->params['folder'] :QUICK_UPLOAD_FOLDER) . '/';
                // create folder if non-existant
                expFile::makeDirectory($destDir);
            }
        } else {
            $quikFolder = null;
            $destDir = null;
        }

        //extensive suitability check before doing anything with the file...
        if (isset($_SERVER['HTTP_X_FILE_NAME'])) {  //HTML5 XHR upload
            $file = expFile::fileXHRUpload($_SERVER['HTTP_X_FILE_NAME'],false,false,null,$destDir,intval(QUICK_UPLOAD_WIDTH));
            $file->poster = $user->id;
            $file->posted = $file->last_accessed = time();
            $file->save();
            if (!empty($quikFolder)) {
                $expcat = new expCat($quikFolder);
                $params['expCat'][0] = $expcat->id;
                $file->update($params);
            }
            $ar = new expAjaxReply(200, gt('Your File was uploaded successfully'), $file->id);
            $ar->send();
        } else {  //$_POST upload
            if (($_FILES['uploadfile'] == "none") OR (empty($_FILES['uploadfile']['name'])) ) {
                $message = gt("No file uploaded.");
            } else if ($_FILES['uploadfile']["size"] == 0) {
                $message = gt("The file is zero length.");
    //            } else if (($_FILES['upload']["type"] != "image/pjpeg") AND ($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/png")) {
    //                $message = gt("The image must be in either JPG or PNG format. Please upload a JPG or PNG instead.");
            } else if (!is_uploaded_file($_FILES['uploadfile']["tmp_name"])) {
                $message = gt("You may be attempting to hack our server.");
            } else {
                // upload the file, but don't save the record yet...
                $file = expFile::fileUpload('uploadfile',false,false,null,$destDir,intval(QUICK_UPLOAD_WIDTH));
                // since most likely this function will only get hit via flash in YUI Uploader
                // and since Flash can't pass cookies, we lose the knowledge of our $user
                // so we're passing the user's ID in as $_POST data. We then instantiate a new $user,
                // and then assign $user->id to $file->poster so we have an audit trail for the upload
                if (is_object($file)) {
                    $file->poster = $user->id;
                    $file->posted = $file->last_accessed = time();
                    $file->save();
                    if (!empty($quikFolder)) {
                        $expcat = new expCat($quikFolder);
                        $params['expCat'][0] = $expcat->id;
                        $file->update($params);
                    }
                    $ar = new expAjaxReply(200, gt('Your File was uploaded successfully'), $file->id);
                } else {
                    $ar = new expAjaxReply(300, gt("File was not uploaded!").' - '.$file);
                }
                $ar->send();
            }
        }
    }

    public function editCat() {
        global $user;
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->isAdmin()) {
            $expcat = new expCat($this->params['newValue']);
            $params['expCat'][0] = $expcat->id;
            $file->update($params);
            $file->cat = $expcat->title;
            $file->catid = $expcat->id;
            $ar = new expAjaxReply(200, gt('Your Folder was updated successfully'), $file);
        } else {
            $ar = new expAjaxReply(300, gt("You didn't create this file, so you can't edit it."));
        }
        $ar->send();
    }

    public function editTitle() {
        global $user;
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->isAdmin()) {
            $file->title = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, gt('Your title was updated successfully'), $file);
        } else {
            $ar = new expAjaxReply(300, gt("You didn't create this file, so you can't edit it."));
        }
        $ar->send();
    }

    public function editAlt() {
        global $user;
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->isAdmin()) {
            $file->alt = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, gt('Your alt was updated successfully'), $file);
        } else {
            $ar = new expAjaxReply(300, gt("You didn't create this file, so you can't edit it."));
        }
        $ar->send();
        echo json_encode($file);  //FIXME we exit before hitting this
    }

    public function editShare() {
        global $user;
        $file = new expFile($this->params['id']);
		if(!isset($this->params['newValue'])) {
			$this->params['newValue'] = 0;
		}
        if ($user->id==$file->poster || $user->isAdmin()) {
            $file->shared = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, gt('This file is now shared.'), $file);
        } else {
            $ar = new expAjaxReply(300, gt("You didn't create this file, so it's not yours to share."));
        }
        $ar->send();
        echo json_encode($file);  //FIXME we exit before hitting this
    }

    public function import_eql() {
    }

    public  function import_eql_process() {
        global $db;

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
        	switch($_FILES['file']['error']) {
        		case UPLOAD_ERR_INI_SIZE:
        		case UPLOAD_ERR_FORM_SIZE:
        			echo gt('The file you uploaded exceeded the size limits for the server.').'<br />';
        			break;
        		case UPLOAD_ERR_PARTIAL:
        			echo gt('The file you uploaded was only partially uploaded.').'<br />';
        			break;
        		case UPLOAD_ERR_NO_FILE:
        			echo gt('No file was uploaded.').'<br />';
        			break;
        	}
        } else {
            $errors = array();
            expSession::clearAllUsersSessionCache();

            // copy in deprecated definitions files to aid in import
            $src = BASE . "install/old_definitions";
            $dst = BASE . "framework/core/definitions";
            if (is_dir($src) && expUtil::isReallyWritable($dst)) {
                $dir = opendir($src);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (!file_exists($dst . '/' . $file)) {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
                closedir($dir);
            }

            expFile::restoreDatabase($_FILES['file']['tmp_name'], $errors);

            // now remove deprecated definitions files
            $src = BASE . "install/old_definitions";
            $dst = BASE . "framework/core/definitions";
            if (is_dir($src) && expUtil::isReallyWritable($dst)) {
                $dir = opendir($src);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (file_exists($dst . '/' . $file)) {
                            unlink($dst . '/' . $file);
                        }
                        // remove empty deprecated tables
                        $table = substr($file, 0, -4);
                        if ($db->tableIsEmpty($table)) {
                            $db->dropTable($table);
                        }
                    }
                }
                closedir($dir);
            }

            // update search index
            searchController::spider();

            // check to see if we need to install or upgrade the restored database
            expVersion::checkVersion();

            assign_to_template(
                array(
                    'success' => !count($errors),
                    'errors' => $errors,
                )
            );
        }
    }

    public static function getTables() {
        global $db;

        expDatabase::fix_table_names();
        $tables = $db->getTables();
        if (!function_exists('tmp_removePrefix')) {
        	function tmp_removePrefix($tbl) {
        		// we add 1, because DB_TABLE_PREFIX  no longer has the trailing
        		// '_' character - that is automatically added by the database class.
        		return substr($tbl,strlen(DB_TABLE_PREFIX)+1);
        	}
        }
        $tables = array_map('tmp_removePrefix',$tables);
        usort($tables,'strnatcmp');
        return $tables;
    }

    public function export_eql() {
//        global $db, $user;
        global $user;

//        expDatabase::fix_table_names();
//        $tables = $db->getTables();
//        if (!function_exists('tmp_removePrefix')) {
//        	function tmp_removePrefix($tbl) {
//        		// we add 1, because DB_TABLE_PREFIX  no longer has the trailing
//        		// '_' character - that is automatically added by the database class.
//        		return substr($tbl,strlen(DB_TABLE_PREFIX)+1);
//        	}
//        }
//        $tables = array_map('tmp_removePrefix',$tables);
//        usort($tables,'strnatcmp');

        assign_to_template(array(
            'user' => $user,
            'tables' => self::getTables(),
        ));
    }

    public function export_eql_process() {
//        global $db;

        if (!isset($this->params['tables'])) { // No checkboxes clicked so we'll dump all tables
            $this->params['tables'] = self::getTables();
            $this->params['tables'] = array_flip($this->params['tables']);
        }
//        	echo gt('You must choose at least one table to export.');
//        } else { // All good
        	$filename = str_replace(
        		array('__DOMAIN__','__DB__'),
        		array(str_replace('.','_',HOSTNAME),DB_NAME),
                $this->params['filename']);
        	$filename = preg_replace('/[^A-Za-z0-9_.-]/','-',strftime($filename,time()).'.eql');

        	ob_end_clean();
        	ob_start("ob_gzhandler");

        	if (isset($this->params['save_sample'])) { // Save as a theme sample is checked off
        		$path = BASE . "themes/".DISPLAY_THEME."/sample.eql";
        		if (!$eql = fopen ($path, "w")) {
        			flash('error',gt("Error opening eql file for writing")." ".$path);
        		} else {
                    //TODO we need to write inside call passing $eql file pointer
                    $eqlfile = expFile::dumpDatabase(array_keys($this->params['tables']));
        			if (fwrite ($eql, $eqlfile)  === FALSE) {
        				flash('error',gt("Error writing to eql file")." ".$path);
        			}
        			fclose ($eql);
        			flash('message',gt("Sample database (eql file) saved to")." '".DISPLAY_THEME."' ".gt("theme"));
        			expHistory::back();
        		}
        	} else {
        		// This code was lifted from phpMyAdmin, but this is Open Source, right?

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
                echo expFile::dumpDatabase(array_keys($this->params['tables']));  //FIXME we need to echo inside call
        		exit; // Exit, since we are exporting
        	}
//        }
    }

    public function import_files() {
    }

    public function import_files_process() {
        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
        	switch($_FILES['file']['error']) {
        		case UPLOAD_ERR_INI_SIZE:
        		case UPLOAD_ERR_FORM_SIZE:
        			echo gt('The file you uploaded exceeded the size limits for the server.').'<br />';
        			break;
        		case UPLOAD_ERR_PARTIAL:
        			echo gt('The file you uploaded was only partially uploaded.').'<br />';
        			break;
        		case UPLOAD_ERR_NO_FILE:
        			echo gt('No file was uploaded.').'<br />';
        			break;
        	}
        } else {
        	$basename = basename($_FILES['file']['name']);

        	include_once(BASE.'external/Tar.php');
        	$tar = new Archive_Tar($_FILES['file']['tmp_name'],'gz');

        	$dest_dir = BASE.'tmp/extensionuploads/'.uniqid('');
        	@mkdir($dest_dir,DIR_DEFAULT_MODE_STR);
        	if (!file_exists($dest_dir)) {
        		echo gt('Unable to create temporary directory to extract files archive.');
        	} else {
        		$return = $tar->extract($dest_dir);
        		if (!$return) {
        			echo '<br />'.gt('Error extracting TAR archive').'<br />';
        		} else if (!file_exists($dest_dir.'/files') || !is_dir($dest_dir.'/files')) {
        			echo '<br />'.gt('Invalid archive format, no \'/files\' folder').'<br />';
        		} else {
        			// Show the form for specifying which mod types to 'extract'

        			$mods = array(); // Stores the mod classname, the files list, and the module's real name

        			$dh = opendir($dest_dir.'/files');
        			while (($file = readdir($dh)) !== false) {
        				if ($file{0} != '.' && is_dir($dest_dir.'/files/'.$file)) {
        					$mods[$file] = array(
        						'',
        						array_keys(expFile::listFlat($dest_dir.'/files/'.$file,1,null,array(),$dest_dir.'/files/'))
        					);
        //					if (class_exists($file)) {
        //						$mods[$file][0] = call_user_func(array($file,'name')); // $file is the class name of the module
        //					}
        				} elseif ($file != '.' && $file != '..') {
        					$mods[$file] = array(
        						'',
        						$file
        					);
        				}
        			}

                    assign_to_template(array(
                        'dest_dir' => $dest_dir,
                        'file_data' => $mods,
                    ));
        		}
        	}
        }
    }

    public function import_files_extract() {
        $dest_dir = $this->params['dest_dir'];
        $files = array();
        foreach (array_keys($this->params['mods']) as $file) {
        	$files[$file] = expFile::canCreate(BASE.'files/'.$file);
        //	if (class_exists($mod)) {
        //		$files[$mod][0] = call_user_func(array($mod,'name'));
        //	}
        //	foreach (array_keys(expFile::listFlat($dest_dir.'/files',1,null,array(),$dest_dir.'/files/')) as $file) {
        //		$files[$mod][1][$file] = expFile::canCreate(BASE.'files/'.$file);
        //	}
        }

        expSession::set('dest_dir',$dest_dir);
        expSession::set('files_data',$files);

        assign_to_template(array(
            'files_data' => $files,
        ));
    }

    public function import_files_finish() {
        $dest_dir = expSession::get('dest_dir');
        $files = expSession::get('files_data');
        if (!file_exists(BASE.'files')) {
        	mkdir(BASE.'files',DIR_DEFAULT_MODE_STR);
        }

        $filecount = 0;
        foreach (array_keys($files) as $file) {
            expFile::copyDirectoryStructure($dest_dir.'/files/'.$file,BASE.'files/'.$file);
        	copy($dest_dir.'/files/'.$file,BASE.'files/'.$file);
        	$filecount++;
        }

        expSession::un_set('dest_dir');
        expSession::un_set('files_data');

        expFile::removeDirectory($dest_dir);

        assign_to_template(array(
            'file_count' => $filecount,
        ));
    }

    public function export_files() {
        global $user;

        $loc = expCore::makeLocation($this->params['controller'],isset($this->params['src'])?$this->params['src']:null,isset($this->params['int'])?$this->params['int']:null);
        //$mods = array();
        //$dh = opendir(BASE.'files');
        //while (($file = readdir($dh)) !== false) {
        //	if (is_dir(BASE.'files/'.$file) && $file{0} != '.' && class_exists($file)) {
        //		$mods[$file] = call_user_func(array($file,'name'));
        //	}
        //}
        //uasort($mods,'strnatcmp');

        assign_to_template(array(
            'user' => $user,
        ));
    }

    public function export_files_process() {
//        global $db;

        //if (!isset($this->params['mods'])) {
        //	echo gt('You must select at least one module to export files for.');
        //	return;
        //}

        include_once(BASE.'external/Tar.php');

        $files = array();
        //foreach (array_keys($this->params['mods']) as $mod) {
        //	foreach ($db->selectObjects('file',"directory LIKE 'files/".$mod."%'") as $file) {
//            foreach ($db->selectObjects('expFiles',1) as $file) {
        foreach (expFile::selectAllFiles() as $file) {
            $files[] = BASE.$file->directory.$file->filename;
        }
        //}

        $fname = tempnam(BASE.'/tmp','exporter_files_');
        $tar = new Archive_Tar($fname,'gz');
        $tar->createModify($files,'',BASE);

        $filename = str_replace(
            array('__DOMAIN__','__DB__'),
            array(str_replace('.','_',HOSTNAME),DB_NAME),
            $this->params['filename']);
        $filename = preg_replace('/[^A-Za-z0-9_.-]/','-',strftime($filename,time()).'.tar.gz');

        if (isset($this->params['save_sample'])) { // Save as a theme sample is checked off
            copy($fname,BASE . "themes/".DISPLAY_THEME_REAL."/sample.tar.gz");
            unlink($fname);
            flash('message',gt("Sample uploaded files archive saved to")." '".DISPLAY_THEME_REAL."' ".gt("theme"));
            expHistory::back();
        } else {
            ob_end_clean();
            // This code was lifted from phpMyAdmin, but this is Open Source, right?

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

            $fh = fopen($fname,'rb');
            while (!feof($fh)) {
                echo fread($fh,8192);
            }
            fclose($fh);
            unlink($fname);
        }

        exit(''); // Exit, since we are exporting.
    }

}

?>