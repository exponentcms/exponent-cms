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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * File Manager Control - displays file picker widget
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class filemanagercontrol extends formcontrol {

    var $html;
    var $span;
    var $description = "";
    var $folder = "";
    var $accept = "";

    static function name() { return "Manage Files"; }
    static function isStatic() { return true; }
    
    function __construct($subtype=null, $html = "",$span = true) {
        $this->span = $span;
        $this->html = $html;
        $this->subtype = isset($subtype) ? $subtype : '';
    }

    function toHTML($label,$name) {
        global $user;

    	$assets_path = SCRIPT_RELATIVE.'framework/core/forms/controls/assets/';
        $subTypeName = empty($this->subtype) ? "expFile[]" : "expFile[".$this->subtype."][]";
        $files = $this->buildImages();
        $html = '<div id="filemanager'.$name.'" class="filemanager control form-group'.(empty($this->class)?"":" ".$this->class).'">';
        //$html .= '<div id="displayfiles" class="displayfiles" style="padding:5px; border:1px solid #444"> </div>';
        $html .= '<div class="hd"><label class="control-label">'.$label.'';
        if ($this->limit!=null){
            $html .= ' | <small>'.gt('Limit').': <em class="limit">'.$this->limit.'</em></small>';
        }
        if ($this->count < $this->limit) {
            $hide = '';
        } else {
            $hide = ' class="hide"';
        }
        $btn_size = expTheme::buttonSize();
        $icon_size = expTheme::iconSize();
        $html .= ' <span id="adders-'.$name.'"'.$hide.'> <a class="btn btn-success '. $btn_size.'" href="#" id="addfiles-'.$name.'" title="'.gt('Add Files using the File Manager').'"><i class="fa fa-plus-circle '.$icon_size.'"></i> '.gt('Add Files').'</a>';
        if (!$user->globalPerm('prevent_uploads')) {
        $html .= ' <a class="btn btn-success '. $btn_size.'" href="#" id="quickaddfiles-'.$name.'" title="'.gt('One-step Upload and Add Files').'"><i class="fa fa-plus-circle '.$icon_size.'"></i> '.gt('Quick Add').'</a></span>';
        } else {
        $html .= '</span>';
        }
        $html .= '</label></div>';

        if (empty($files)) {
            $this->count = 0;
            $files = '<li class="blank">'.gt('You need to add some files').'</li>';
        }
        $html .= '<div class="filembox"><div class="filebox"><ul id="filelist'.$name.'" class="scrollable filelist">';
        $html .= $files;
        $html .= '</ul></div>';
        $html .= '<div id="progressBox-'.$name.'" class="progressbox"></div><div style="clear:both"></div></div>';
        $html .= '<input type="hidden" name="'.$subTypeName.'" value="'.$subTypeName.'">';
        if ($this->limit>1) $this->description .= " " . gt('Drag the files to change their sequence.');
        if (!empty($this->description)) $html .= "<div class=\"help-block\">".$this->description."</div>";
        $html .= '</div>';
        if (strpos($this->accept,'image/*') !== false) {
            $filter = 'image';
        } else {
            $filter = 0;
        }
        $js = "
            $(document).ready(function(){
                var limit = ".$this->limit.";
                var filesAdded = ".$this->count.";
                var fl = $('#filelist".$name."');

                // file picker window opener
                function openFilePickerWindow(e){
                    e.preventDefault();
                    win = window.open('".makeLink($params=array('controller'=>'file','action'=>'picker','ajax_action'=>"1",'update'=>$name, 'filter'=>$filter))."', 'IMAGE_BROWSER','left=20,top=20,scrollbars=yes,width=".FM_WIDTH.",height=".FM_HEIGHT.",toolbar=no,resizable=yes,status=0');
                    if (!win) {
                        //Catch the popup blocker
                        alert('".gt('Please disable your popup blocker')."!!');
                    }
                };

                // quick file upload
                if ($('#quickaddfiles-".$name."') != null) {
                var quickUpload = new ss.SimpleUpload({
                    button: '#quickaddfiles-".$name."',
                    url: '" . makelink(array("controller"=> "file", "action"=> "quickUpload")) . "',
                    data: {controller: 'file', action: 'quickUpload', ajax_action: 1, json: 1, folder: '" . $this->folder . "'},
                    dropzone: 'filelist".$name."',
                    dragClass: 'dragit',
                    responseType: 'json',
                    name: 'uploadfile',
                    disabledClass: 'quick-upload-disabled ajax',
                    hoverClass: 'active',
                    multiple: (limit-filesAdded > 1),
                    maxUploads: limit,
                    multipart: false,
                    noParams: false,
                    maxSize: " . intval(ini_get('upload_max_filesize')*1024) . ",";
        if (!empty($this->accept)) {
            $js .= '
                    accept: "'.$this->accept.'",';
        }
        $js .= "
                    onSubmit: function(file, ext){
//                             if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
//                                // extension is not allowed
//                                return false;
//                            }
//                            quickUpload.disable();
//                        if (quickUpload._activeUploads) {
                           $('#quickaddfiles-".$name."').addClass('ajax');
                           $('#quickaddfiles-".$name."').addClass('quick-upload-disabled');
//                        }
                        // Create the elements of our progress bar
                        var progress = document.createElement('div'), // container for progress bar
                            bar = document.createElement('div'), // actual progress bar
                            fileSize = document.createElement('div'), // container for upload file size
                            wrapper = document.createElement('div'), // container for this progress bar
                            progressBox = document.getElementById('progressBox-".$name."'); // on page container for progress bars

                        // Assign each element its corresponding class
                        progress.className = 'progress';
                        bar.className = 'bar';
                        fileSize.className = 'size';
                        wrapper.className = 'wrapper';

                        // Assemble the progress bar and add it to the page
                        progress.appendChild(bar);
                        wrapper.innerHTML = '<div class=\"name\">'+file+'</div>'; // filename is passed to onSubmit()
                        wrapper.appendChild(fileSize);
                        wrapper.appendChild(progress);
                        progressBox.appendChild(wrapper); // just an element on the page to hold the progress bars

                        // Assign roles to the elements of the progress bar
                        this.setProgressBar(bar); // will serve as the actual progress bar
                        this.setFileSizeBox(fileSize); // display file size beside progress bar
                        this.setProgressContainer(wrapper); // designate the containing div to be removed after upload
                    },
                    onComplete: function(file, response){
                        //Add uploaded file to list
                        if(response.replyCode==200){
                            EXPONENT.passBackFile".$name."(response.data);
                        }
//                            quickUpload.enable();
                        if (!quickUpload._activeUploads) {
                            $('#quickaddfiles-".$name."').removeClass('ajax');
                            $('#quickaddfiles-".$name."').removeClass('quick-upload-disabled');
                        }
                    },
                    onSizeError: function(filename, fileSize){
                        alert(filename+' ".gt('is is too large to upload')."');
                    },
                    onError: function(filename, errorType, response){
                        alert(filename+' '+errorType+' '+response);
                    },
                });
                }

                var listenForAdder = function(){
                    var af = $('#addfiles-".$name."');
                    af.click(openFilePickerWindow);

                    var afq = $('#quickaddfiles-".$name."');
                    if (afq != null) {
                        afq.click(quickUpload);
                    }
                };
                
                var showEmptyLI = function(){
                    var blank = $('<li class=\"blank\">".gt('You need to add some files')."</li>');
                    $('#filelist".$name."').append(blank);
                };
                
                if (limit > filesAdded) {
                    listenForAdder();
                }
                                
                // remove the file from the list
                $('#filelist".$name."').delegate('.btn-danger', 'click', function(e){
                    $(e.target).closest('li').remove();
                    showFileAdder();
                });

                var showFileAdder = function(){
                    listenForAdder();
                    filesAdded--;
                    if (filesAdded < limit) {
                        $('#adders-".$name."').removeClass('hide');
                        quickUpload.clearQueue();
                        quickUpload.enable();
                    }
                    if (filesAdded == 0) showEmptyLI();
                };

                if (EXPONENT.batchAddFiles==undefined) {
                    EXPONENT.batchAddFiles = {};
                }

                EXPONENT.batchAddFiles.".$name." = function(ids) {
                    var j=0;
                    $.each(ids, function(k,obj){
                        if (j<limit) {

                            var df = $('#filelist".$name."');

                            if (obj.mimetype=='image/png' || obj.mimetype=='image/gif' || obj.mimetype=='image/jpeg' || obj.mimetype=='image/pjpeg' || obj.mimetype=='image/x-png') {
                                var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+obj.id+'&amp;w=24&amp;h=24&amp;zc=1\">';
                            } else if (obj.mimetype=='audio/mpeg') {
                                var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'audio_22x22.png\">';
                            } else if (obj.mimetype=='video/x-flv' || obj.mimetype=='video/mp4' || obj.mimetype=='video/x-m4v' || obj.mimetype=='video/webm' || obj.mimetype=='video/ogg') {
                                var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'video_22x22.png\">';
                            } else {
                                var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'generic_22x22.png\">';
                            }
                            
                            var html = '<li>';
                            html += '<input type=\"hidden\" name=\"".$subTypeName."\" value=\"'+obj.id+'\">';";
                            $icon = expTheme::buttonIcon('delete');
                            $js .= "
                            html += '<a class=\" btn-danger btn-sm\" rel=\"imgdiv'+obj.id+'\" href=\"javascript:{}\" title=\"".gt('Remove this file')."\"><i class=\"fa fa-" . $icon->class . " " . $icon->size . "\"></i> </a>';";
                            $js .= "
                            html += filepic;
                            if (obj.title) {
                                filetitle = obj.title;
                            } else {
                                filetitle = obj.filename;
                            }
                            html += '<span class=\"filename\" title=\"'+obj.filename+'\">'+filetitle+'<\/span>';
                            html += '<\/li>';
                            
                            htmln = $(html);

                            df.append(htmln);

                            if (filesAdded==0) {
                                $('#filelist".$name." .blank').remove();
                            }
                            filesAdded++;

                            if (filesAdded>=limit) {
                                $('#adders-".$name."').addClass('hide');
                                quickUpload.disable();
                            }

                            j++;
                        }
                    })
                };

                EXPONENT.passBackBatch".$name." = function(ids) {
                    $.each(ids, function(k,id){
                        EXPONENT.passBackFile".$name."(id);
                    });
                };

                // callback function from open window
                EXPONENT.passBackFile".$name." = function(id) {
                    if ($.isArray(id)) {
                        EXPONENT.batchAddFiles.".$name."(id);
                        return;
                    }

                    var complete = function (o, ioId) {
                      if (filesAdded < limit) {
                        var df = $('#filelist".$name."');
                        var obj = o.data;
                        if (obj.mimetype=='image/png' || obj.mimetype=='image/gif' || obj.mimetype=='image/jpeg' || obj.mimetype=='image/pjpeg' || obj.mimetype=='image/x-png') {
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+obj.id+'&amp;w=24&amp;h=24&amp;zc=1\">';
                        } else if (obj.mimetype=='audio/mpeg') {
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'audio_22x22.png\">';
                        } else if (obj.mimetype=='video/x-flv' || obj.mimetype=='video/mp4' || obj.mimetype=='video/x-m4v' || obj.mimetype=='video/webm' || obj.mimetype=='video/ogg') {
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'video_22x22.png\">';
                        } else {
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.MIMEICON_RELATIVE+'generic_22x22.png\">';
                        }
                    
                        var html = '<li>';
                        html += '<input type=\"hidden\" name=\"".$subTypeName."\" value=\"'+obj.id+'\">';";
                        $icon = expTheme::buttonIcon('delete');
                        $js .= "
                        html += '<a class=\" btn-danger btn-sm\" rel=\"imgdiv'+obj.id+'\" href=\"javascript:{}\" title=\"".gt('Remove this file')."\"><i class=\"fa fa-" . $icon->class . " " . $icon->size . "\"></i> </a>';";
                        $js .= "
                        html += filepic;
                        if (obj.title) {
                            filetitle = obj.title;
                        } else {
                            filetitle = obj.filename;
                        }
                        html += '<span class=\"filename\" title=\"'+obj.filename+'\">'+filetitle+'<\/span>';
                        html += '<\/li>';
                        htmln = $(html);

                        df.append(htmln);

                        if (filesAdded==0) {
                            $('#filelist".$name." .blank').remove();
                        }
                        filesAdded++;

                        if (filesAdded>=limit) {
                            $('#adders-".$name."').addClass('hide');
                            quickUpload.disable();
                        }
                      }
                    };
                    
                    $.ajax({
                        url: EXPONENT.PATH_RELATIVE+'index.php.php?controller=file&action=getFile&ajax_action=1&json=1&id='+id,
                        success: complete
                    });
                }

                new Sortable(document.getElementById('filelist" . $name . "'));
            });
            ";

            expCSS::pushToHead(array(
        	    "unique"    => "attachable-files",
        	    "link"      => $assets_path."files/attachable-files.css"
            ));
            expCSS::pushToHead(array(
                "unique"    => "attachable-files",
                "link"      => $assets_path."files/attachable-files-bs3.css"
            ));

            expJavascript::pushToFoot(array(
                "unique"    => "filepicker".$name,
                "jquery"    => 'Sortable,SimpleAjaxUploader',
                "content"   => $js,
             ));
        return $html;
    }
    
    function buildImages() {
    	$assets_path = SCRIPT_RELATIVE.'framework/core/forms/controls/assets/';
        if (empty($this->value)) return null;

        //get the array of files
        if (empty($this->subtype)) {
            $filearray = $this->value;
            foreach ($filearray as $key=>$val){
                if (!is_int($key)) {
                    unset($filearray[$key]);
                }
            }
        } else {
            $filearray = $this->value[$this->subtype];
        }

        if (empty($filearray)) return null;
        $this->count = count($filearray);
        
        $subTypeName = empty($this->subtype) ? "expFile[]" : "expFile[".$this->subtype."][]";
        // loop over each file and build out the HTML
        //$cycle = "odd";
        $html='';
        foreach($filearray as $val) {
            if ($val->mimetype=="image/png" || $val->mimetype=="image/gif" || $val->mimetype=="image/jpeg" || $val->mimetype=="image/pjpeg" || $val->mimetype=="image/x-png") {
                $filepic = "<img class=\"filepic\" src=\"".PATH_RELATIVE."thumb.php?id=".$val->id."&amp;w=24&amp;h=24&amp;zc=1\">";
            } elseif ($val->mimetype=="audio/mpeg") {
                $filepic = "<img class=\"filepic\" src='".MIMEICON_RELATIVE."audio_22x22.png'>";
            } elseif ($val->mimetype=="video/x-flv" || $val->mimetype=="video/mp4" || $val->mimetype=="video/x-m4v" || $val->mimetype=="video/webm" || $val->mimetype=="video/ogg") {
                $filepic = "<img class=\"filepic\" src='".MIMEICON_RELATIVE."video_22x22.png'>";
            } else {
                $filepic = "<img class=\"filepic\" src='".MIMEICON_RELATIVE."generic_22x22.png'>";
            }
            $html .= "<li>";
            $html .= "<input type=\"hidden\" name=\"".$subTypeName."\" value=\"".$val->id."\">";
            //$html .= "<div class=\"fpdrag\"></div>";
            $icon = expTheme::buttonIcon('delete');
            $html .= "<a class=\"btn btn-danger btn-sm\" rel=\"imgdiv".$val->id."\" href='javascript:{}' title=\"".gt('Remove this file')."\"><i class=\"fa fa-" . $icon->class . " " . $icon->size . "\"></i> </a>";
            $html .= $filepic;
            $filetitle = !empty($val->title) ? $val->title : $val->filename;
            $html .= "<span class=\"filename\" title=\"".$val->filename."\">".$filetitle."</span>";
            $html .= "</li>";
            //$cycle = $cycle=="odd" ? "even" : "odd";
        }
        
        return $html;
    }
    
    function controlToHTML($name,$label) {
        return $this->html;
    }
    
    static function form($object) {
        $form = new form();
        if (!isset($object->html)) {
            $object->html = "";
        } 
        $form->register("html",'',new htmleditorcontrol($object->html));
        if (!expJavascript::inAjaxAction())
            $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }
    
    static function update($values, $object) {
        if ($object == null) $object = new htmlcontrol();
        $object->html = preg_replace("/<br ?\/>$/","",trim($values['html']));
        $object->caption = '';
        $object->identifier = uniqid("");
        $object->is_static = 1;
        return $object;
    }
    
}

?>
