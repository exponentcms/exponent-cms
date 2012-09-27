<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Text Editor Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class ckeditorcontrol extends formcontrol {

    var $rows;
    var $cols;
    var $maxchars;
    var $toolbar;

    static function name() { return "CKEditor"; }
    
    function __construct ($default="",$rows = 5,$cols = 45) {
        $this->default = $default;
        $this->rows = $rows;
        $this->cols = $cols;
        $this->required = false;
        $this->maxchars = 0;
    }

    function controlToHTML($name,$label) {
        global $db;

        $contentCSS = '';
        $cssabs = BASE.'themes/'.DISPLAY_THEME.'/editors/ckeditor/ckeditor.css';
        $css = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/editors/ckeditor/ckeditor.css';
        if (THEME_STYLE!="" && is_file(BASE.'themes/'.DISPLAY_THEME.'/editors/ckeditor/ckeditor_'.THEME_STYLE.'.css')) {
            $cssabs = BASE.'themes/'.DISPLAY_THEME.'/editors/ckeditor/ckeditor_'.THEME_STYLE.'.css';
            $css = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/editors/ckeditor/ckeditor_'.THEME_STYLE.'.css';
        }
        if (is_file($cssabs)) {
            $contentCSS = "contentsCss : '".$css."',";
        }
        if (empty($this->toolbar)) {
            $settings = $db->selectObject('htmleditor_ckeditor','active=1');
        } elseif (intval($this->toolbar)!=0) {
            $settings = $db->selectObject('htmleditor_ckeditor','id='.$this->toolbar);
        }
        if (!empty($settings)) {
            $tb = stripSlashes($settings->data);
            $skin = $settings->skin;
            $scayt_on = $settings->scayt_on ? 'true' : 'false';
            $paste_word = $settings->paste_word ? 'pasteFromWordPromptCleanup : true,' : 'forcePasteAsPlainText : true,';
            $plugins = stripSlashes($settings->plugins);
            $stylesset = stripSlashes($settings->stylesset);
            $formattags = stripSlashes($settings->formattags);
            $fontnames = stripSlashes($settings->fontnames);
        }

        // set defaults
        if (empty($tb)) {
            if ($this->toolbar == 'basic') {
                $tb = "
                    ['Bold','Italic','Underline','-','NumberedList','BulletedList','-','Link','Unlink','-','About']";
            } else {
                $tb = "
                    ['Source','-','Preview','-','Templates'],
                    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print','SpellChecker','Scayt'],
                    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
                    '/',
                    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
                    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['Link','Unlink','Anchor'],
                    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
                    '/',
                    ['Styles','Format','Font','FontSize'],
                    ['TextColor','BGColor'],
                    ['Maximize', 'ShowBlocks','-','About']";
            }
        }
        if (empty($skin)) $skin = 'kama';
        if (empty($scayt_on)) $scayt_on = 'true';
        if (empty($paste_word)) $paste_word = 'forcePasteAsPlainText : true,';
        if (empty($plugins)) $plugins = '';
        if (empty($stylesset)) $stylesset = "'default'";
        if (empty($formattags)) $formattags = "'p;h1;h2;h3;h4;h5;h6;pre;address;div'";
        if (empty($fontnames)) $fontnames = "'Arial/Arial, Helvetica, sans-serif;' +
                                    'Comic Sans MS/Comic Sans MS, cursive;' +
                                    'Courier New/Courier New, Courier, monospace;' +
                                    'Georgia/Georgia, serif;' +
                                    'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
                                    'Tahoma/Tahoma, Geneva, sans-serif;' +
                                    'Times New Roman/Times New Roman, Times, serif;' +
                                    'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
                                    'Verdana/Verdana, Geneva, sans-serif'";
        $content = "
        YUI(EXPONENT.YUI3_CONFIG).use('yui','node','event-custom', function(Y) {
           Y.Global.on(\"lazyload:cke\", function () {
                if(!Y.Lang.isUndefined(EXPONENT.editor".createValidId($name).")){
                    return true;
                };
                EXPONENT.editor".createValidId($name)." = CKEDITOR.replace('".createValidId($name)."',
                    {
                        skin : '".$skin."',
                        toolbar : [".$tb."],
                        ".$paste_word."
                        scayt_autoStartup : ".$scayt_on.",
                        filebrowserBrowseUrl : '".makelink(array("controller"=>"file", "action"=>"picker", "ajax_action"=>1, "ck"=>1, "update"=>"fck"))."',
                        filebrowserUploadUrl : '".PATH_RELATIVE."external/editors/connector/uploader.php',
                        filebrowserWindowWidth : '".FM_WIDTH."',
                        filebrowserWindowHeight : '".FM_HEIGHT."',
                        filebrowserLinkBrowseUrl : '".PATH_RELATIVE."external/editors/connector/ckeditor_link.php',
                        filebrowserLinkWindowWidth : '320',
                        filebrowserLinkWindowHeight : '600',
                        filebrowserImageBrowseLinkUrl : '".PATH_RELATIVE."external/editors/connector/ckeditor_link.php',
                        extraPlugins : 'stylesheetparser,tableresize,".$plugins."',
                        autoGrow_maxHeight : 400,
                        entities_additional : '',
                        ".$contentCSS."
                        stylesSet : ".$stylesset.",
                        format_tags : ".$formattags.",
                        font_names :
                            ".$fontnames.",
                        uiColor : '#aaaaaa',
                        baseHref : '".PATH_RELATIVE."'
                    });

                    CKEDITOR.on( 'instanceReady', function( ev ) {
                        var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre','ol','ul','li'];
                        var rules =  {
                            indent : false,
                            breakBeforeOpen : false,
                            breakAfterOpen : false,
                            breakBeforeClose : false,
                            breakAfterClose : true
                        };
                        for (var i=0; i<blockTags.length; i++) {
                            ev.editor.dataProcessor.writer.setRules( blockTags[i], rules );
                        }
                    });

           });
            
            if (!Y.one('#".createValidId($name)."').ancestor('.exp-skin-tabview')) {
                Y.Global.fire('lazyload:cke');
            }

        });
        ";
        
        expJavascript::pushToFoot(array(
            "unique"=>"zzz-cke".$name,
            "yui3mods"=>"1",
            "content"=>$content,
            //"src"=>PATH_RELATIVE."external/ckeditor/ckeditor.js"
         ));
        $html = "<script src=\"".PATH_RELATIVE."external/editors/ckeditor/ckeditor.js\"></script>";
        // $html .= ($this->lazyload==1)?"<!-- cke lazy -->":"";
        $html .= "<!-- cke lazy -->";
        $html .= "<textarea class=\"textarea\" id=\"".createValidId($name)."\" name=\"$name\"";
        $html .= " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"";
        if ($this->accesskey != "") $html .= " accesskey=\"" . $this->accesskey . "\"";
        if (!empty($this->class)) $html .= " class=\"" . $this->class . "\"";
        if ($this->tabindex >= 0) $html .= " tabindex=\"" . $this->tabindex . "\"";

        $html .= ">";
        $html .= htmlentities($this->default,ENT_COMPAT,LANG_CHARSET);
        $html .= "</textarea>";
        return $html;
    }
        
}

?>
