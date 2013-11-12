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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Text Editor Control
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class tinymcecontrol extends formcontrol {

    var $rows;
    var $cols;
    var $maxchars;
    var $toolbar;

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
//        global $db;

        $contentCSS = '';
        $cssabs     = BASE . 'themes/' . DISPLAY_THEME . '/editors/tinymce/tinymce.css';
        $css        = PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/editors/tinymce/tinymce.css';
        if (THEME_STYLE != "" && is_file(BASE . 'themes/' . DISPLAY_THEME . '/editors/tinymce/tinymce_' . THEME_STYLE . '.css')) {
            $cssabs = BASE . 'themes/' . DISPLAY_THEME . '/editors/tinymce/tinymce_' . THEME_STYLE . '.css';
            $css    = PATH_RELATIVE . 'themes/' . DISPLAY_THEME . '/editors/tinymce/tinymce_' . THEME_STYLE . '.css';
        }
        if (is_file($cssabs)) {
            $contentCSS = "content_css : '" . $css . "',
            ";
        }
        if (empty($this->toolbar)) {
//            $settings = $db->selectObject('htmleditor_ckeditor', 'active=1');
            $settings = expHTMLEditorController::getActiveEditorSettings();
        } elseif (intval($this->toolbar) != 0) {
//            $settings = $db->selectObject('htmleditor_ckeditor', 'id=' . $this->toolbar);
            $settings = expHTMLEditorController::getEditorSettings($this->toolbar);
        }
        $plugins = 'plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons paste textcolor visualblocks importcss"
            ],
        ';
        if (!empty($settings)) {
            $tb         = stripSlashes($settings->data);
            $skin       = $settings->skin;
            $scayt_on   = $settings->scayt_on ? 'true' : 'false';
            $paste_word = $settings->paste_word ? 'pasteFromWordPromptCleanup : true,' : 'forcePasteAsPlainText : true,';
//            $plugins    = stripSlashes($settings->plugins);
            $stylesset  = stripSlashes($settings->stylesset);
            $formattags = stripSlashes($settings->formattags);
            $fontnames  = stripSlashes($settings->fontnames);
        }
        if (!empty($this->additionalConfig)) {
            $additionalConfig = $this->additionalConfig;
//            $plugins .= ',fieldinsert';
        }
        if (!empty($this->plugin)) {
//            $plugins .= ',' . $this->plugin;
        }

        // set defaults
        if (empty($tb)) {
            if ($this->toolbar == 'basic') {
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
                $tb = '';
            }
        } else {
            $tb = "toolbar : [".$tb."],";
        }
        if (empty($skin) || !is_dir(BASE . 'external/editors/ckeditor/skins/' . $skin)) $skin = 'kama';
        if (empty($scayt_on)) $scayt_on = 'true';
        if (empty($paste_word)) $paste_word = 'forcePasteAsPlainText : true,';
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
                if(!Y.Lang.isUndefined(EXPONENT.editor" . createValidId($name) . ")){
                    return true;
                };
                EXPONENT.editor" . createValidId($name) . " = tinymce.init({
                    selector : '#" . createValidId($name) . "',
                    " . $plugins . "
                    " . $contentCSS . "
                    document_base_url : '" . PATH_RELATIVE . "',
                    toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
                    toolbar2: 'print preview media | forecolor backcolor emoticons',
                    image_advtab: true,
                    style_formats: [
                        {title: 'Image Left', selector: 'img', styles: {
                            'float' : 'left',
                            'margin': '0 10px 0 10px'
                        }},
                        {title: 'Image Right', selector: 'img', styles: {
                            'float' : 'right',
                            'margin': '0 10px 0 10px'
                        }},
                        {title: 'Headers', items: [
                            {title: 'h1', block: 'h1'},
                            {title: 'h2', block: 'h2'},
                            {title: 'h3', block: 'h3'},
                            {title: 'h4', block: 'h4'},
                            {title: 'h5', block: 'h5'},
                            {title: 'h6', block: 'h6'}
                        ]},
                        {title: 'Blocks', items: [
                            {title: 'p', block: 'p'},
                            {title: 'div', block: 'div'},
                            {title: 'pre', block: 'pre'}
                        ]},
                        {title: 'Containers', items: [
                            {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
                            {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
                            {title: 'blockquote', block: 'blockquote', wrapper: true},
                            {title: 'hgroup', block: 'hgroup', wrapper: true},
                            {title: 'aside', block: 'aside', wrapper: true},
                            {title: 'figure', block: 'figure', wrapper: true}
                        ]}
                    ],
                    end_container_on_empty_block: true,
                    file_browser_callback: function expBrowser (field_name, url, type, win) {
                        tinymce.activeEditor.windowManager.open({
                            file: '" . makelink(array("controller"=> "file", "action"=> "picker", "ajax_action"=> 1, "update"=> "tiny")) . "',
                            title: 'File Manager',
                            width: " . FM_WIDTH . ",
                            height: " . FM_HEIGHT . ",
                            resizable: 'yes'
                        }, {
                            setUrl: function (url) {
                                win.document.getElementById(field_name).value = url;
                            }
                        });
                        return false;
                    },
                });

            });
            if (!Y.one('#" . createValidId($name) . "').ancestor('.exp-skin-tabview')) {
                Y.Global.fire('lazyload:cke');
            }
        });
        ";

        expJavascript::pushToFoot(array(
            "unique"  => "000-tinymce" . $name,
            "yui3mods"=> "1",
            "content" => $content,
            //"src"=>PATH_RELATIVE."external/tinymce/tinymce.min.js"
        ));
        $html = "<script src=\"" . PATH_RELATIVE . "external/editors/tinymce/tinymce.min.js\"></script>";
        // $html .= ($this->lazyload==1)?"<!-- cke lazy -->":"";
        $html .= "<!-- cke lazy -->";
        $html .= "<textarea class=\"textarea\" id=\"" . createValidId($name) . "\" name=\"$name\"";
        $html .= " rows=\"" . $this->rows . "\" cols=\"" . $this->cols . "\"";
        if ($this->accesskey != "") $html .= " accesskey=\"" . $this->accesskey . "\"";
        if (!empty($this->class)) $html .= " class=\"" . $this->class . "\"";
        if ($this->tabindex >= 0) $html .= " tabindex=\"" . $this->tabindex . "\"";

        $html .= ">";
        $html .= htmlentities($this->default, ENT_COMPAT, LANG_CHARSET);
        $html .= "</textarea>";
        if (!empty($this->description)) $html .= "<div class=\"control-desc\">".$this->description."</div>";
        return $html;
    }

}

?>
