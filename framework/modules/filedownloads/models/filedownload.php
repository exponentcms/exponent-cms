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
 * @subpackage Models
 * @package Modules
 */

class filedownload extends expRecord {
//	public $table = 'filedownloads';

    protected $attachable_item_types = array(
        'content_expCats'=>'expCat',
        'content_expComments'=>'expComment',
        'content_expFiles'=>'expFile',
        'content_expTags'=>'expTag',
    );

	public $validates = array(
		'presence_of'=>array(
			'title'=>array('message'=>'Title is a required field.'),
            //'body'=>array('message'=>'Body is a required field.'),
		)
    );

    public function __construct($params=null, $get_assoc=true, $get_attached=true) {
        parent::__construct($params, $get_assoc, $get_attached);

        if (!empty($this->meta_fb))
            $this->meta_fb = expUnserialize($this->meta_fb);
        if (!empty($this->meta_fb['fbimage']) && !empty($this->meta_fb['fbimage'][0]))
            $this->meta_fb['fbimage'][0] = new expFile($this->meta_fb['fbimage'][0]);
        if (!empty($this->meta_tw))
            $this->meta_tw = expUnserialize($this->meta_tw);
        if (!empty($this->meta_tw['twimage']) && !empty($this->meta_tw['twimage'][0]))
            $this->meta_tw['twimage'][0] = new expFile($this->meta_tw['twimage'][0]);

        if (!empty($this->id)) {
            include_once(BASE.'external/mp3file.php');
            if (!empty($this->expFile['downloadable'][0]) && ($this->expFile['downloadable'][0]->mimetype == "audio/mpeg") && (file_exists(BASE.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename))) {
                $mp3 = new mp3file(BASE.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename);
                $id3 = $mp3->get_metadata();
                if (($id3['Encoding']=='VBR') || ($id3['Encoding']=='CBR')) {
                    $this->expFile['downloadable'][0]->duration = $id3['Length mm:ss'];
                }
                if (!empty($this->meta_fb['fbimage']) && !empty($this->meta_fb['fbimage'][0]->id))
                    $this->meta_fb['type'] = 'audio';
            } elseif (!empty($this->expFile['downloadable'][0]) && (($this->expFile['downloadable'][0]->mimetype == "video/mp4") || ($this->expFile['downloadable'][0]->mimetype == "application/x-shockwave-flash")) && (file_exists(BASE.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename))) {
                if (!empty($this->meta_fb['fbimage']) && !empty($this->meta_fb['fbimage'][0]->id))
                    $this->meta_fb['type'] = 'video';
            }
        }
    }

    public function beforeCreate() {
   	    if (empty($this->publish) || $this->publish == 'on') {
   	        $this->publish = time();
   	    }
   	}

    public function update($params = array()) {
        if (isset($params['expFile']['fbimage'][0]) && is_numeric($params['expFile']['fbimage'][0]))
            $params['fb']['fbimage'][0] = $params['expFile']['fbimage'][0];
        unset ($params['expFile']['fbimage']);
        if (isset($params['fb'])) {
            $params['meta_fb'] = serialize($params['fb']);
            unset ($params['fb']);
        }
        if (isset($params['expFile']['twimage'][0]) && is_numeric($params['expFile']['twimage'][0]))
            $params['tw']['twimage'][0] = $params['expFile']['twimage'][0];
        unset ($params['expFile']['twimage']);
        if (isset($params['tw'])) {
            $params['meta_tw'] = serialize($params['tw']);
            unset ($params['tw']);
        }
        parent::update($params);
    }

    public function download_link() {
        if (!empty($this->ext_file)) {
            return $this->ext_file;
        } else {
            return URL_FULL.$this->expFile['downloadable'][0]->directory.$this->expFile['downloadable'][0]->filename;
        }
    }
}

?>