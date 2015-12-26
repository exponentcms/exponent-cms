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

class pixidouController extends expController {
//    public $cacheDir = "framework/modules/pixidou/images/";
	public $cacheDir = "tmp/pixidou/";
    public $requires_login = array(
        'editor',
        'exitEditor'
    );

    static function displayname() { return gt("Pixidou Image Editor"); }
    static function description() { return gt("Add and manage Exponent Files"); }
    static function author() { return "Phillip Ball - OIC Group, Inc"; }

    static function hasSources()
    {
        return false;
    }

    function editor() {
        global $user;
        
        $file = new expFile($this->params['id']);
        
        $canSaveOg = $user->id==$file->poster || $user->is_admin ? 1 : 0 ;
	    if (file_exists(BASE.$file->directory.$file->filename)) {
			$file->copyToDirectory(BASE.$this->cacheDir);
			assign_to_template(array(
                'image'=>$file,
                'update'=>$this->params['update'],
                'saveog'=>$canSaveOg
            ));
	    } else {
		    flash('error',gt('The file').' "'.BASE.$file->directory.$file->filename.'" '.gt('does not exist on the server.'));
		    redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"filter"=>$this->params['filter']));
	    }
    }
    
    public function exitEditor() {

        //eDebug($this->params,true);
        switch ($this->params['exitType']) {
            case 'saveAsCopy':
                $oldimage = new expFile($this->params['fid']);                
                $copyname = expFile::resolveDuplicateFilename($oldimage->path); 
                copy(BASE.$this->cacheDir."/".$this->params['cpi'],$oldimage->directory.$copyname); //copy the edited file over to the files dir
                $newFile = new expFile(array("filename"=>$copyname)); //construct a new expFile
                $newFile->directory = $oldimage->directory;
                $newFile->title = $oldimage->title;
                $newFile->shared = $oldimage->shared;
                $newFile->mimetype = $oldimage->mimetype;
                $newFile->posted = time();
                $newFile->filesize = filesize(BASE.$this->cacheDir."/".$this->params['cpi']);
                $resized = getimagesize(BASE.$this->cacheDir."/".$this->params['cpi']);
                $newFile->image_width = $resized[0];
                $newFile->image_height = $resized[1];
                $newFile->alt = $oldimage->alt;
                $newFile->is_image = $oldimage->is_image;
                $newFile->save(); //Save it to the database

                break;
            case 'saveAsIs':
                //eDebug($this->params,true);
                $oldimage = new expFile($this->params['fid']);
                $resized = getimagesize(BASE.$this->cacheDir."/".$this->params['cpi']);
                $oldimage->image_width = $resized[0];
                $oldimage->image_height = $resized[1];
                $oldimage->save();
                copy(BASE.$this->cacheDir."/".$this->params['cpi'],$oldimage->directory.$oldimage->filename); //copy the edited file over to the files dir
                break;
            
            default:
                # code...
                break;
        }
        // proper file types to look for
        $types = array(".jpg",".gif",".png");
        
        //Pixidou images directory, the editor's cache
        $cachedir = BASE.$this->cacheDir;
        
        if (is_dir($cachedir) && is_readable($cachedir) ) {
            $dh = opendir($cachedir);
            while (($tmpfile = readdir($dh)) !== false) {
                if (in_array(substr($tmpfile,-4,4),$types)) {
                    $filename = $cachedir.$tmpfile;
                    unlink($filename);
                }
            }
        }
        
        redirect_to(array("controller"=>'file',"action"=>'picker',"ajax_action"=>1,"update"=>$this->params['update'],"filter"=>$this->params['filter']));
    }
    
}

?>