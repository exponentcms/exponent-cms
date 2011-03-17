<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Phillip Ball

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

class fileController extends expController {
    public $basemodel_name = "expFile";
    //public $useractions = array('showall'=>'Show all');
    //public $add_permissions = array('picker'=>'Manage Files');
    //public $remove_permissions = array('edit');
    public $requires_login = array('picker'=>'must be logged in','edit_alt'=>'must be logged in');


    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "File Manager"; }
    function description() { return "Add and manage Exponent Files"; }
    function author() { return "Phillip Ball - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }

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
    
        eDebug($files,true);
    }
    
    public function picker() {
        global $user;
        assign_to_template(array('update'=>$_GET['update']));
    }
    
    public function uploader() {
        global $user;
        //expHistory::set('manageable', $this->params);
        flash('message','Upload size limit: '.ini_get('upload_max_filesize'));
        if(intval(ini_get('upload_max_filesize'))!=intval(ini_get('post_max_size')) && $user->is_admin){
            flash('error','In order for the uploader to work correctly, "post_max_size" and "upload_max_filesize" within your php.ini file must match one another');
        }

        assign_to_template(
            array('update'=>$_GET['update'],
            "upload_size"=>ini_get('upload_max_filesize'),
            "post_size"=>ini_get('post_max_size'),
            "bmax"=>intval(ini_get('upload_max_filesize')/1024*1000000000)
            )
        );
    }
    
    public function get_view_config() {
        global $template;
        
        // set paths we will search in for the view
        $paths = array(
            BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/common/views/file/configure',
            BASE.'framework/modules/common/views/file/configure',            
        );        
        
        foreach ($paths as $path) {
            $view = $path.'/'.$this->params['view'].'.tpl';
            if (is_readable($view)) {
                $template = new controllerTemplate($this, $view);
                $ar = new expAjaxReply(200, 'ok');
		        $ar->send();
            }
        }
    }
    
    public function getFile() {
        $file = new expFile($this->params['id']);
        $ar = new expAjaxReply(200, 'ok', $file);
        $ar->send();
    } 

    public function getFilesByJSON() {
        global $db,$user;
        $modelname = $this->basemodel_name;
        $results = 25; // default get all
        $startIndex = 0; // default start at 0
        $sort = null; // default don't sort
        $dir = 'asc'; // default sort dir is asc
        $sort_dir = SORT_ASC;

        // How many records to get?
        if(strlen($_GET['results']) > 0) {
            $results = $_GET['results'];
        }

        // Start at which record?
        if(strlen($_GET['startIndex']) > 0) {
            $startIndex = $_GET['startIndex'];
        }

        // Sorted?
        if(strlen($_GET['sort']) > 0) {
            $sort = $_GET['sort'];
        }

        // Sort dir?
        if((strlen($_GET['dir']) > 0) && ($_GET['dir'] == 'desc')) {
            $dir = 'desc';
            $sort_dir = SORT_DESC;
        }
        else {
            $dir = 'asc';
            $sort_dir = SORT_ASC;
        }
        
        if (isset($_GET['query'])) {

            if ($user->is_acting_admin!=1) {
                $filter = "(poster=".$user->id." OR shared=1) AND ";
            };
            if ($_GET['fck']==1) {
                $filter .= "is_image=1 AND ";
            }

            $totalrecords = $this->$modelname->find('count','id!=0 '.$filter);
            
            $files = $this->$modelname->find('all',$filter."filename LIKE '%".$_GET['query']."%' OR title LIKE '%".$_GET['query']."%' OR alt LIKE '%".$_GET['query']."%'".$imagesOnly,$sort.' '.$dir, $results, $startIndex);

            foreach ($files as $key=>$file) {
                $tmpusr = new user($file->poster);
                $files[$key]->user->firstname = $tmpusr->firstname;
                $files[$key]->user->lastname = $tmpusr->lastname;
                $files[$key]->user->username = $tmpusr->username;
            }

            $returnValue = array(
                'recordsReturned'=>count($files),
                'totalRecords'=>count($files),
                'startIndex'=>$startIndex,
                'sort'=>$sort,
                'dir'=>$dir,
                'pageSize'=>$results,
                'records'=>$files
            );
        } else {
            if ($user->is_acting_admin!=1) {
                $filter = "(poster=".$user->id." OR shared=1)";
            };
            if ($_GET['fck']==1) {
                $filter .= !empty($filter) ? " AND " : "";
                $filter .= "is_image=1";
            }
            
            
            $totalrecords = $this->$modelname->find('count',$filter);

            $files = $this->$modelname->find('all',$filter,$sort.' '.$dir, $results, $startIndex);
            
            foreach ($files as $key=>$file) {
                $tmpusr = new user($file->poster);
                $files[$key]->user->firstname = $tmpusr->firstname;
                $files[$key]->user->lastname = $tmpusr->lastname;
                $files[$key]->user->username = $tmpusr->username;
            }
            
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
    
    public function delete() {
        global $db;
        $file = new expFile($this->params['id']);
        $file->delete();
        if (unlink($file->directory.$file->filename)) {
            flash('message',$file->filename.' was successfully deleted');
        } else {
            flash('error',$file->filename.' was deleted from the database, but could not be removed from the file system.');
        }
        redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"fck"=>$this->params['fck']));
    } 
    
    public function upload() {
        
        // upload the file, but don't save the record yet...
        $file = expFile::fileUpload('Filedata',false,false);
        
        // since most likely this function will only get hit via flash in YUI Uploader
        // and since Flash can't pass cookies, we lose the knowlege of our $user
        // so we're passing the user's ID in as $_POST data. We then instantiate a new $user,
        // and then assign $user->id to $file->poster so we have an audit trail for the upload

        $user = new user($_REQUEST['usrid']);
        $file->poster = $user->id;
        $file->save();
     
        // a blank echo so YUI Uploader is notified of the function's completion
        echo ' ';
    } 

    public function editTitle() {
        global $user;
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->is_acting_admin==1) {
            $file->title = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, 'Your title was updated successfully', $file);
        } else {
            $ar = new expAjaxReply(300, "You didn't create this file, so you can't edit it.");
        }
        $ar->send();
    } 

    public function editAlt() {
        global $user;        
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->is_acting_admin==1) {
            $file->alt = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, 'Your alt was updated successfully', $file);
        } else {
            $ar = new expAjaxReply(300, "You didn't create this file, so you can't edit it.");
        }
        $ar->send();
        echo json_encode($file);
    } 

    public function editShare() {
        global $user;
        $file = new expFile($this->params['id']);
        if ($user->id==$file->poster || $user->is_acting_admin==1) {
            $file->shared = $this->params['newValue'];
            $file->save();
            $ar = new expAjaxReply(200, 'This file is now shared.', $file);
        } else {
            $ar = new expAjaxReply(300, "You didn't create this file, so it's not yours to share.");
        }
        $ar->send();
        echo json_encode($file);
        
    } 
}

?>
