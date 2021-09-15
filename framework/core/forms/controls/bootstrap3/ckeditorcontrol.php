<?php

##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class ckeditorcontrol extends formcontrol {

    var $rows;
    var $cols;
    var $maxchars;
    var $toolbar;
    var $tb_collapsed = false;
    var $lazyload;
    var $plugin;
    var $additionalConfig;

    static function name() {
        return "CKEditor";
    }

    function __construct($default = "", $rows = 5, $cols = 45) {
        $this->default  = $default;
        $this->rows     = $rows;
        $this->cols     = $cols;
        $this->required = false;
        $this->maxchars = 0;
    }

    function controlToHTML($name, $label) {
        global $user;

        $contentCSS = '';
        $css        = 'themes/' . DISPLAY_THEME . '/editors/ckeditor/ckeditor.css';
        if (THEME_STYLE != "" && is_file(BASE . 'themes/' . DISPLAY_THEME . '/editors/ckeditor/ckeditor_' . THEME_STYLE . '.css'))
            $css    = 'themes/' . DISPLAY_THEME . '/editors/ckeditor/ckeditor_' . THEME_STYLE . '.css';
        if (is_file(BASE . $css))
            $contentCSS = "contentsCss : '" . PATH_RELATIVE . $css . "',";

        if (is_file(BASE . 'themes/' . DISPLAY_THEME . '/editors/ckeditor/config.js'))
            $configjs = "customConfig : '" . PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/editors/ckeditor/config.js' . "',";
        else
            $configjs = "";

        if ($this->toolbar === '')
            $settings = expHTMLEditorController::getActiveEditorSettings('ckeditor');
        elseif ((int)($this->toolbar) != 0)
            $settings = expHTMLEditorController::getEditorSettings($this->toolbar, 'ckeditor');
        $plugins = '';
        if (!empty($settings)) {
            $tb         = stripSlashes($settings->data);
            $skin       = $settings->skin;
            $scayt_on   = $settings->scayt_on ? 'true' : 'false';
            $paste_word = $settings->paste_word ? 'pasteFromWordPromptCleanup : true,' : 'forcePasteAsPlainText : true,';
            $plugins    = stripSlashes($settings->plugins);
            $stylesset  = stripSlashes($settings->stylesset);
            $formattags = stripSlashes($settings->formattags);
            $fontnames  = stripSlashes($settings->fontnames);
        }
        if (!empty($this->additionalConfig))
            $additionalConfig = $this->additionalConfig;
        elseif (!empty($settings->additionalconfig))
            $additionalConfig = stripSlashes($settings->additionalconfig);
        else
            $additionalConfig = '';
        if (!empty($additionalConfig) && strpos($additionalConfig,",",-1) === false)
            $additionalConfig .= ",";  // MUST end with comma
        if (!empty($this->plugin))
            $plugins .= ',' . $this->plugin;
        // clean up (custom) plugins list from missing plugins
        if (!empty($plugins)) {
            $plugs = explode(',',trim($plugins));
            foreach ($plugs as $key=>$plug) {
                if (empty($plug) || !is_dir(BASE . 'external/editors/ckeditor/plugins/' . $plug)) unset($plugs[$key]);
            }
            $plugins = implode(',',$plugs);
        }
        // set defaults
        // make sure the (custom) skin exists
        if (empty($skin) || !is_dir(BASE . 'external/editors/ckeditor/skins/' . $skin))
            $skin = 'kama';
        if (empty($tb)) {
            if ($this->toolbar === 'basic') {
                $tb = "
                toolbar : [
                    ['Bold','Italic','Underline','RemoveFormat','-','NumberedList','BulletedList','-','Link','Unlink','-','About']
                ],";
            } else {
//                $tb = "
//                toolbarGroups : [
//                    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
//                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
//                    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
//                    { name: 'links' },
//                    { name: 'insert' },
//                    { name: 'forms' },
//                    { name: 'tools' },
//                    { name: 'others' },
//                    '/',
//                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//                    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
//                    { name: 'styles' },
//                    { name: 'colors' },
//                    { name: 'about' }
//                ],";
                $tb = '';  // auto-generate toolbar
            }
        } else {
            if (!empty($this->plugin)) {
                $tbc = ',[';
                $plugs = explode(',',trim($this->plugin));
                $first = true;
                foreach ($plugs as $key=>$plug) {
                    if (!$first)
                        $tbc .= ',';
                    $first = false;
                    $tbc .= "'" . $plug . "'";
                }
//                $tbc .= ",'Fields','fieldinsert'";
                $tbc .= ']';
            } else {
                $tbc = '';
            }
            // $tb must be javascript array [..]
            $tb = expString::check_javascript(trim($tb));  // $styleset must be enclosed in quotes '..' or brackets [..]
            $tb = "toolbar : [".$tb.$tbc."],";
        }
        if (MOBILE) {
            $tb .= "
            toolbarStartupExpanded : false,
            removePlugins : 'elementspath',
            resize_enabled : false,";
        }
        if (!MOBILE && $this->tb_collapsed)
            $tb .= 'toolbarStartupExpanded : false,';
        if (empty($paste_word))
            $paste_word = 'forcePasteAsPlainText : true,';
        if (!$user->globalPerm('prevent_uploads')) {
            $upload = "filebrowserUploadUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/uploader.php',";
            $upload .= "filebrowserUploadMethod : 'form',";
            $upload .= "uploadUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/uploader_paste.php',";
        } else
            $upload = '';
        if (empty($scayt_on))
            $scayt_on = 'true';
        if (empty($stylesset))
            $stylesset = "'default'";
        else
            $stylesset = expString::check_javascript($stylesset);  // $styleset must be enclosed in quotes '..' or brackets [..]
        if (empty($formattags))
            $formattags = "'p;h1;h2;h3;h4;h5;h6;pre;address;div'";
        else
            $formattags = expString::check_javascript($formattags, true);  // $formattags must be enclosed in quotes '..'
        if (empty($fontnames)) {
            $fontnames = "'Arial/Arial, Helvetica, sans-serif;' +
                'Comic Sans MS/Comic Sans MS, cursive;' +
                'Courier New/Courier New, Courier, monospace;' +
                'Georgia/Georgia, serif;' +
                'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
                'Tahoma/Tahoma, Geneva, sans-serif;' +
                'Times New Roman/Times New Roman, Times, serif;' +
                'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
                'Verdana/Verdana, Geneva, sans-serif'";
        } else {
            $fontnames = expString::check_javascript($fontnames, true);  // $fontnames must be enclosed in quotes '..'
        }
        $content = "
            $(document).ready(function(){
                if(typeof(EXPONENT.editor" . createValidId($name) . ") !== 'undefined'){
                    delete CKEDITOR.instances['editor" . createValidId($name) . "'];
//                    return true;
                };
                EXPONENT.editor" . createValidId($name) . " = CKEDITOR.replace('" . createValidId($name) . "', {
                    skin : '" . $skin . "',
                    " . $tb . "
                    " . $paste_word . "
                    scayt_autoStartup : " . $scayt_on . ",
                    filebrowserBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck")) . "',
                    filebrowserImageBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck", "filter"=> 'image')) . "',
                    filebrowserFlashBrowseUrl : '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "ck")) . "',
                    " . $upload . "
                    filebrowserWindowWidth : " . FM_WIDTH . ",
                    filebrowserWindowHeight : " . FM_HEIGHT . ",
                    filebrowserImageBrowseLinkUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/ckeditor_link.php?update=ck',
                    filebrowserLinkBrowseUrl : '" . PATH_RELATIVE . "framework/modules/file/connector/ckeditor_link.php?update=ck',
                    filebrowserLinkWindowWidth : 320,
                    filebrowserLinkWindowHeight : 600,
                    extraPlugins : 'autosave,tableresize,image2,uploadimage,uploadfile,quicktable,showborders," . $plugins . "',
                    removePlugins: 'image,forms,flash',
                    image2_alignClasses: [ 'image-left', 'image-center', 'image-right' ],
                    image2_captionedClass: 'image-captioned',
                    " . $additionalConfig . "
                    autoGrow_minHeight : 200,
                    autoGrow_maxHeight : 400,
                    autoGrow_onStartup : false,
                    height : 200,
                    toolbarCanCollapse : true,
                    entities_additional : '',
                    " . $contentCSS . "
                    " . $configjs . "
                    stylesSet : " . $stylesset . ",
                    format_tags : " . $formattags . ",
                    font_names :
                        " . $fontnames . ",
                    uiColor : '#aaaaaa',
                    baseHref : '" . PATH_RELATIVE . "',
                    disallowedContent : 'table[cellspacing,cellpadding,border,align,summary,bgcolor,frame,rules,width]; td[axis,abbr,scope,align,bgcolor,char,charoff,height,nowrap,valign,width]; th[axis,abbr,align,bgcolor,char,charoff,height,nowrap,valign,width]; tbody[align,char,charoff,valign]; tfoot[align,char,charoff,valign]; thead[align,char,charoff,valign]; tr[align,bgcolor,char,charoff,valign]; col[align,char,charoff,valign,width]; colgroup[align,char,charoff,valign,width];',
                });

                // set formatting rules - CKEditor rules defaults are all true for these block tags
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
        ";

        expJavascript::pushToFoot(array(
            "unique"  => "ckeditor",
            "src"=>PATH_RELATIVE."external/editors/ckeditor/ckeditor.js"
        ));
        expJavascript::pushToFoot(array(
            "unique"  => "000-cke" . $name,
            "jquery"=> "1",
            "content" => $content,
        ));

        $html = "<textarea class=\"textarea\" id=\"" . createValidId($name) . "\" name=\"$name\"";
        if ($this->focus) $html .= " autofocus";
        $html .= " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"";
        if ($this->accesskey != "") $html .= " accesskey=\"" . $this->accesskey . "\"";
        if (!empty($this->class)) $html .= " class=\"" . $this->class . "\"";
        if ($this->tabindex >= 0) $html .= " tabindex=\"" . $this->tabindex . "\"";
        $html .= ">";
        $html .= htmlentities($this->default, ENT_COMPAT, LANG_CHARSET);
        $html .= "</textarea>";

        if ($this->horizontal) {
            $html = '<div class="col-sm-10">' . $html . '</div>';
        }

        if (!empty($this->description)) $html .= "<div class=\"".(bs3()?"help-block":"control-desc")."\">".$this->description."</div>";
        return $html;
    }

}

?>
