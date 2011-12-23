<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Phillip Ball
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
 * File Manager Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class filemanagercontrol extends formcontrol {

    var $html;
    var $span;
    
    function name() { return "Manage Files"; }
    function isSimpleControl() { return false; }
    
    function __construct($subtype=null, $html = "",$span = true) {
        $this->span = $span;
        $this->html = $html;
        $this->subtype = isset($subtype) ? $subtype : '';
    }

    function toHTML($label,$name) {
    	$assets_path = SCRIPT_RELATIVE.'framework/core/subsystems/forms/controls/assets/';
        $subTypeName = empty($this->subtype) ? "expFile[]" : "expFile[".$this->subtype."][]";
        $files = $this->buildImages();
        $html = '<div id="filemanager'.$name.'" class="filemanager control'.(empty($this->class)?"":" ".$this->class).'">';
        //$html .= '<div id="displayfiles" class="displayfiles" style="padding:5px; border:1px solid #444"> </div>';
        $html .= '<div class="hd"><label class="label">'.$label.'';
        if ($this->limit!=null){
            $html .= ' | <small>'.gt('Limit').': <em class="limit">'.$this->limit.'</em></small>';
        }
        if ($this->count < $this->limit){
            $html .= ' | <a class="add" href="#" id="addfiles-'.$name.'">'.gt('Add Files').'</a>';
        }
        $html .= '</label></div>';

        if (empty($files)) {
            $this->count = 0;
            $files = '<li class="blank">'.gt('You need to add some files').'</li>';
        }
        $html .= '<ul id="filelist'.$name.'" class="filelist">';
        $html .= $files;
        $html .= '</ul>';
        $html .= '<input type="hidden" name="'.$subTypeName.'" value="'.$subTypeName.'">';
        $html .= '</div>';
        $js = "
            YUI(EXPONENT.YUI3_CONFIG).use('dd-constrain','dd-proxy','dd-drop','json','io', function(Y) {
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
                
                var listenForAdder = function(){
                    var af = Y.one('#addfiles-".$name."');
                    af.on('click',openFilePickerWindow);
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
                    var sf = Y.one('#addfiles-".$name."');
                    if (Y.Lang.isNull(sf)) {
                        var afl = Y.Node.create('<a class=\"add\" href=\"#\" id=\"addfiles-".$name."\">".gt('Add Files')."</a>');
                        Y.one('#filemanager".$name." .hd').append(afl);
                        listenForAdder();
                    }
                    filesAdded--;
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

                // calback function from open window
                EXPONENT.passBackFile".$name." = function(id) {

                    var complete = function (ioId, o) {
                        var df = Y.one('#filelist".$name."');
                        var objson = Y.JSON.parse(o.responseText);
                        var obj = objson.data;
                        if (obj.mimetype!='image/png' && obj.mimetype!='image/gif' && obj.mimetype!='image/jpeg'){
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.ICON_RELATIVE+'\"attachableitems/generic_22x22.png\">';
                        } else {
                            var filepic = '<img class=\"filepic\" src=\"'+EXPONENT.URL_FULL+'thumb.php?id='+obj.id+'&amp;w=24&amp;h=24&amp;zc=1\">';
                        }
                    
                        var html = '<li>';
                        html += '<input type=\"hidden\" name=\"".$subTypeName."\" value=\"'+obj.id+'\">';
                        html += '<a class=\"delete\" rel=\"imgdiv'+obj.id+'\" href=\"javascript:{}\">".gt('delete')."<\/a>';
                        html += filepic;
                        html += '<span class=\"filename\">'+obj.filename+'<\/span>';
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

                        
                        var af = Y.one('#addfiles-".$name."');

                        if (filesAdded==0) {
                            fl.one('.blank').remove();
                        }

                        filesAdded++

                        if (!Y.Lang.isNull(af) && limit==filesAdded) {
                            af.remove();
                        }

                        //initDragables();
                    };
                    
                    var cfg = {
                        on:{
                            success:complete
                        }
                    };
                    Y.io(EXPONENT.URL_FULL+'index.php.php?controller=file&action=getFile&ajax_action=1&json=1&id='+id, cfg);
                    //ej.fetch({action:'getFile',controller:'fileController',json:1,params:'&id='+id});
                }

            });
            "; // END PHP STRING LITERAL

            expCSS::pushToHead(array(
        	    "unique"=>"cal2",
        	    "link"=>$assets_path."files/attachable-files.css"
        	    )
        	);

//        exponent_javascript_toFoot("filepicker".$name,"json,connection","dd-constrain,dd-proxy,dd-drop",$js,"");
            expJavascript::pushToFoot(array(
                "unique"=>"filepicker".$name,
                "yui3mods"=>"1",
                "content"=>$js,
                "src"=>""
             ));
        return $html;
    }
    
    function buildImages() {
    	$assets_path = SCRIPT_RELATIVE.'framework/core/subsystems/forms/controls/assets/';
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
        foreach($filearray as $val) {
            if ($val->mimetype!="image/png" && $val->mimetype!="image/gif" && $val->mimetype!="image/jpeg"){
                $filepic = "<img class=\"filepic\" src='".ICON_RELATIVE."attachableitems/generic_22x22.png'>";
            } else {
                $filepic = "<img class=\"filepic\" src=\"".URL_FULL."thumb.php?id=".$val->id."&amp;w=24&amp;h=24&amp;zc=1\">";
            }
            $html = "<li>";
            $html .= "<input type=\"hidden\" name=\"".$subTypeName."\" value=\"".$val->id."\">";
            //$html .= "<div class=\"fpdrag\"></div>";
            $html .= "<a class=\"delete\" rel=\"imgdiv".$val->id."\" href='javascript:{}'>Delete</a>";
            $html .= $filepic;
            $html .= "<span class=\"filename\">".$val->filename."</span>";
            $html .= "</li>";
            //$cycle = $cycle=="odd" ? "even" : "odd";
        }
        
        return $html;
    }
    
    function controlToHTML($name) {
        return $this->html;
    }
    
    function form($object) {
        $form = new form();
        if (!isset($object->html)) {
            $object->html = "";
        } 
        $form->register("html",'',new htmleditorcontrol($object->html));
        $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }
    
    function update($values, $object) {
        if ($object == null) $object = new htmlcontrol();
        $object->html = preg_replace("/<br ?\/>$/","",trim($values['html']));
        $object->caption = '';
        $object->identifier = uniqid("");
        $object->is_static = 1;
        return $object;
    }
    
}

?>
