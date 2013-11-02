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
        $html = '<div id="filemanager'.$name.'" class="filemanager control'.(empty($this->class)?"":" ".$this->class).'">';
        //$html .= '<div id="displayfiles" class="displayfiles" style="padding:5px; border:1px solid #444"> </div>';
        $html .= '<div class="hd"><label class="">'.$label.'';
        if ($this->limit!=null){
            $html .= ' | <small>'.gt('Limit').': <em class="limit">'.$this->limit.'</em></small>';
        }
        if ($this->count < $this->limit) {
            $hide = '';
        } else {
            $hide = ' class="hide"';
        }
        if (BTN_SIZE == 'large') {
            $btn_size = 'btn-small';
            $icon_size = 'icon-large';
        } else {
            $btn_size = 'btn-mini';
            $icon_size = '';
        }
        $html .= ' <span id="adders-'.$name.'"'.$hide.'>| <a class="btn btn-success '. $btn_size.'" href="#" id="addfiles-'.$name.'" title="'.gt('Add Files using the File Manager').'"><i class="icon-plus-sign '.$icon_size.'"></i> '.gt('Add Files').'</a>';
        if (!$user->globalPerm('prevent_uploads')) {
        $html .= ' | <a class="btn btn-success '. $btn_size.'" href="#" id="quickaddfiles-'.$name.'" title="'.gt('One-step Upload and Add Files').'"><i class="icon-plus-sign '.$icon_size.'"></i> '.gt('Quick Add').'</a></span>';
        } else {
        $html .= '</span>';
        }
        $html .= '</label></div>';

        if (empty($files)) {
            $this->count = 0;
            $files = '<li class="blank">'.gt('You need to add some files').'</li>';
        }
        $html .= '<div class="filembox"><div class="filebox"><ul id="filelist'.$name.'" class="filelist">';
        $html .= $files;
        $html .= '</ul></div>';
        $html .= '<div id="progressBox-'.$name.'" class="progressbox"></div><div style="clear:both"></div></div>';
        $html .= '<input type="hidden" name="'.$subTypeName.'" value="'.$subTypeName.'">';
        if ($this->limit>1) $this->description .= " " . gt('Drag the files to change their sequence.');
        if (!empty($this->description)) $html .= "<div class=\"help-block\">".$this->description."</div>";
        $html .= '</div>';
        $js = "
            EXPONENT.YUI3_CONFIG.modules.SimpleAjaxUploader = {
                fullpath: EXPONENT.URL_FULL+'external/SimpleAjaxUploader.js'
            };

            YUI(EXPONENT.YUI3_CONFIG).use('dd-constrain','dd-proxy','dd-drop','json','io','SimpleAjaxUploader', function(Y) {
                var limit = ".$this->limit.";
                var filesAdded = ".$this->count.";
                var fl = Y.one('#filelist".$name."');
                
                // file picker window opener
                function openFilePickerWindow(e){
                    e.halt();
                    win = window.open('".makeLink($params=array('controller'=>'file','action'=>'picker','ajax_action'=>"1",'update'=>$name))."', 'IMAGE_BROWSER','left=20,top=20,scrollbars=yes,width=800,height=600,toolbar=no,resizable=yes,status=0');
                    if (!win) {
                        //Catch the popup blocker
                        alert('".gt('Please disable your popup blocker')."!!');
                    }
                };

                if (Y.one('#quickaddfiles-".$name."') != null) {
                var quickUpload = new Y.ss.SimpleUpload({
                    button: '#quickaddfiles-".$name."',
                    action: '" . makelink(array("controller"=> "file", "action"=> "quickUpload", "ajax_action"=> 1, "json"=> 1)) . "',
                    data: {controller: 'file', action: 'quickUpload', ajax_action: 1, json: 1},
                    responseType: 'json',
                    name: 'uploadfile',
                    disabledClass: 'quick-upload-disabled ajax',
//                    hoverClass: 'a:hover',
                    multiple: (limit-filesAdded > 1),
                    maxUploads: limit,
                    maxSize: " . intval(ini_get('upload_max_filesize')*1024) . ",
//                    debug: true,";
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
                           Y.one('#quickaddfiles-".$name."').addClass('ajax');
                           Y.one('#quickaddfiles-".$name."').addClass('quick-upload-disabled');
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
                            Y.one('#quickaddfiles-".$name."').removeClass('ajax');
                            Y.one('#quickaddfiles-".$name."').removeClass('quick-upload-disabled');
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
                    var af = Y.one('#addfiles-".$name."');
                    af.on('click',openFilePickerWindow);
                    var afq = Y.one('#quickaddfiles-".$name."');
                    if (afq != null) {
                        afq.on('click',quickUpload);
                    }
                };
                
                var showEmptyLI = function(){
                    var blank = Y.Node.create('<li class=\"blank\">".gt('You need to add some files')."</li>');
                    fl.appendChild(blank);
                };
                
                if (limit > filesAdded) {
                    listenForAdder();
                }
                                
                // remove the file from the list
                fl.delegate('click',function(e){
                    e.target.ancestor('li').remove();
                    showFileAdder();
                },'.delete');
                
                var showFileAdder = function() {
                    listenForAdder();
                    filesAdded--;
                    if (filesAdded < limit) Y.one('#adders-".$name."').removeClass('hide');
                    if (filesAdded == 0) showEmptyLI();
                }

                //Drag Drop stuff
                
                //Listen for all drop:over events
                Y.DD.DDM.on('drop:over', function(e) {
                    //Get a reference to out drag and drop nodes
                    var drag = e.drag.get('node'),
                        drop = e.drop.get('node');

                    //Are we dropping on a li node?
                    if (drop.get('tagName').toLowerCase() === 'li') {
                        //Are we not going up?
                        if (!goingUp) {
                            drop = drop.get('nextSibling');
                        }
                        //Add the node to this list
                        e.drop.get('node').get('parentNode').insertBefore(drag, drop);
                        //Resize this nodes shim, so we can drop on it later.
                        e.drop.sizeShim();
                    }
                });
                //Listen for all drag:drag events
                Y.DD.DDM.on('drag:drag', function(e) {
                    //Get the last y point
                    var y = e.target.lastXY[1];
                    //is it greater than the lastY var?
                    if (y < lastY) {
                        //We are going up
                        goingUp = true;
                    } else {
                        //We are going down..
                        goingUp = false;
                    }
                    //Cache for next check
                    lastY = y;
                });
                //Listen for all drag:start events
                Y.DD.DDM.on('drag:start', function(e) {
                    //Get our drag object
                    var drag = e.target;
                    //Set some styles here
                    drag.get('node').setStyle('opacity', '.25');
                    drag.get('dragNode').set('innerHTML', drag.get('node').get('innerHTML'));
                    drag.get('dragNode').setStyles({
                        opacity: '.85',
                        borderColor: drag.get('node').getStyle('borderColor'),
                        backgroundImage: drag.get('node').getStyle('backgroundImage')
                    });
                });
                //Listen for a drag:end events
                Y.DD.DDM.on('drag:end', function(e) {
                    var drag = e.target;
                    //Put out styles back
                    drag.get('node').setStyles({
                        visibility: '',
                        opacity: '1'
                    });
                });
                //Listen for all drag:drophit events
                Y.DD.DDM.on('drag:drophit', function(e) {
                    var drop = e.drop.get('node'),
                        drag = e.drag.get('node');

                    //if we are not on an li, we must have been dropped on a ul
                    if (drop.get('tagName').toLowerCase() !== 'li') {
                        if (!drop.contains(drag)) {
                            drop.appendChild(drag);
                        }
                    }
                });

                //Static Vars
                var goingUp = false, lastY = 0;

                var initDragables =  function(){
                    //Get the list of li's in the lists and make them draggable
                    var lis = Y.Node.all('#filelist".$name." li');
                    if (lis){
                        lis.each(function(v, k) {
                            var dd = new Y.DD.Drag({
                                node: v,
                                proxy: true,
                                moveOnEnd: false,
                                target: {
                                    padding: '0 0 0 20'
                                }
                            }).plug(Y.Plugin.DDConstrained, {
                                //Keep it inside the #list1 node
                                constrain2node: '#filelist".$name."',
                                stickY:true
                            }).plug(Y.Plugin.DDProxy, {
                                //Don't move the node at the end of the drag
                                moveOnEnd: false,
                                borderStyle:'0'
                            });//.addHandle('.fpdrag');
                        });
                    }

                    //var tar = new Y.DD.Drop({ node:Y.one('#filelist".$name."')});
                }
                
                initDragables();

                if (EXPONENT.batchAddFiles==undefined) {
                    EXPONENT.batchAddFiles = {};
                }

                EXPONENT.batchAddFiles.".$name." = function(ids) {
                    var j=0;
                    Y.each(ids, function(obj,k){
                        if (j<limit) {

                            var df = Y.one('#filelist".$name."');

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
                            html += '<input type=\"hidden\" name=\"".$subTypeName."\" value=\"'+obj.id+'\">';
                            html += '<a class=\"delete\" rel=\"imgdiv'+obj.id+'\" href=\"javascript:{}\">".gt('delete')."<\/a>';
                            html += filepic;
                            if (obj.title) {
                                filetitle = obj.title;
                            } else {
                                filetitle = obj.filename;
                            }
                            html += '<span class=\"filename\" title=\"'+obj.filename+'\">'+filetitle+'<\/span>';
//                            html += '<span class=\"filename\">'+obj.filename+'<\/span>';
                            html += '<\/li>';
                            
                            htmln = Y.Node.create(html);                        
                            
                            df.append(htmln);

                            var dd = new Y.DD.Drag({
                                node: htmln,
                                proxy: true,
                                moveOnEnd: false,
                                target: {
                                    padding: '0 0 0 20'
                                }
                            }).plug(Y.Plugin.DDConstrained, {
                                constrain2node: '#filelist".$name."',
                                stickY:true
                            }).plug(Y.Plugin.DDProxy, {
                                moveOnEnd: false,
                                borderStyle:'0'
                            });

                            if (filesAdded==0) {
                                fl.one('.blank').remove();
                            }

                            filesAdded++

//                            if (limit>=filesAdded) {
                            if (filesAdded>=limit) {
                                Y.one('#adders-".$name."').addClass('hide');
                            }

                            j++;
                        }
                    })
                    // Y.log(ids);
                }

                // callback function from open window
                EXPONENT.passBackFile".$name." = function(id) {
                    if (Y.Lang.isArray(id)) {
                        EXPONENT.batchAddFiles.".$name."();
                        return;
                    }

                    var complete = function (ioId, o) {
                      if (filesAdded < limit) {
                        var df = Y.one('#filelist".$name."');
                        var objson = Y.JSON.parse(o.responseText);
                        var obj = objson.data;
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
                        html += '<input type=\"hidden\" name=\"".$subTypeName."\" value=\"'+obj.id+'\">';
                        html += '<a class=\"delete\" rel=\"imgdiv'+obj.id+'\" href=\"javascript:{}\">".gt('delete')."<\/a>';
                        html += filepic;
                        if (obj.title) {
                            filetitle = obj.title;
                        } else {
                            filetitle = obj.filename;
                        }
                        html += '<span class=\"filename\" title=\"'+obj.filename+'\">'+filetitle+'<\/span>';
                        html += '<\/li>';
                        htmln = Y.Node.create(html);

                        df.append(htmln);

                        var dd = new Y.DD.Drag({
                            node: htmln,
                            proxy: true,
                            moveOnEnd: false,
                            target: {
                                padding: '0 0 0 20'
                            }
                        }).plug(Y.Plugin.DDConstrained, {
                            constrain2node: '#filelist".$name."',
                            stickY:true
                        }).plug(Y.Plugin.DDProxy, {
                            moveOnEnd: false,
                            borderStyle:'0'
                        });

                        if (filesAdded==0) {
                            fl.one('.blank').remove();
                        }

                        filesAdded++

//                        if (limit>=filesAdded) {
                        if (filesAdded>=limit) {
                            Y.one('#adders-".$name."').addClass('hide');
                        }

                        //initDragables();
                      }
                    };
                    
                    var cfg = {
                        on:{
                            success:complete
                        }
                    };
                    Y.io(EXPONENT.PATH_RELATIVE+'index.php.php?controller=file&action=getFile&ajax_action=1&json=1&id='+id, cfg);
                    //ej.fetch({action:'getFile',controller:'fileController',json:1,params:'&id='+id});
                }

            });
            "; // END PHP STRING LITERAL

            expCSS::pushToHead(array(
        	    "unique"=>"attachable-files",
        	    "link"=>$assets_path."files/attachable-files.css"
        	    )
        	);

//        exponent_javascript_toFoot("filepicker".$name,"json,connection","dd-constrain,dd-proxy,dd-drop",$js,"");
            expJavascript::pushToFoot(array(
                "unique"=>"filepicker".$name,
                "yui3mods"=>"1",
                "content"=>$js,
             ));
    //         expJavascript::pushToFoot(array(
    //             "unique"=>"quickupload",
    //             "jquery"=>"1",
    //             "src"=>PATH_RELATIVE."external/SimpleAjaxUploader.js"
    //          ));
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
            $html .= "<a class=\"delete\" rel=\"imgdiv".$val->id."\" href='javascript:{}'>".gt('Delete')."</a>";
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
