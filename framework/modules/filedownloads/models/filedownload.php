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
 * @subpackage Models
 * @package Modules
 */

class filedownload extends expRecord {
//	public $table = 'filedownloads';

    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile',
        'content_expTags'=>'expTag',
        'content_expComments'=>'expComment',
        'content_expCats'=>'expCat'
    );

	public $validates = array(
		'presence_of'=>array(
			'title'=>array('message'=>'Title is a required field.'),
		)
    );

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);

        if ($this->id) {
            include_once(BASE.'external/mp3file.php');
            if (!empty($this->expFile['downloadable'][0]) && ($this->expFile['downloadable'][0]->mimetype == "audio/mpeg") && (file_exists(BASE.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $this->expFile['downloadable'][0]->duration = $id3['Length mm:ss'];
                }
            }
        }
    }

    public function beforeCreate() {
   	    if (empty($this->publish) || $this->publish == 'on') {
   	        $this->publish = time();
   	    }
   	}

    public function download_link() {
        if (!empty($this->ext_file)) {
            return $this->ext_file;
        } else {
            $url = makeLink(array('controller'=>'filedownload','action'=>'downloadfile','file_id'=>$this->id));
            return $url;
        }
    }
}

?>