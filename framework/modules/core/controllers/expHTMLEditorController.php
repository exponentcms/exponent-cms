<?php
##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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
 * This is the class expHTMLEditorController
 *
 * @package    Core
 * @subpackage Controllers
 */

class expHTMLEditorController extends expController
{
    protected $manage_permissions = array(
        'activate' => "Activate",
        'preview'  => "Preview Editor Toolbars"
    );
    public $requires_login = array(
        'preview'=>'Preview Editor',
    );

    static function displayname()
    {
        return gt("Editors");
    }

    static function description()
    {
        return gt("Customize CKEditor and TinyMCE Usage");
    }

    static function author()
    {
        return "Phillip Ball";
    }

    static function hasSources()
    {
        return false;
    }

    static function hasContent()
    {
        return false;
    }

    function __construct($src = null, $params = array())
    {
        parent:: __construct($src, $params);
        if (empty($this->params['editor'])) {
            $this->params['editor'] = SITE_WYSIWYG_EDITOR;
        } else {
            $this->params['editor'] = preg_replace("/[^[:alnum:][:space:]]/u", '', $this->params['editor']);
        }
    }

    function manage()
    {
        global $db;

        expHistory::set('manageable', $this->params);
        if (SITE_WYSIWYG_EDITOR === "FCKeditor") {
            flash('error', gt('FCKeditor is deprecated!'));
            redirect_to(array("module" => "administration", "action" => "configure_site"));
        }

        // otherwise, on to the show
        $configs = $db->selectObjects('htmleditor_' . $this->params['editor'], 1);

        assign_to_template(
            array(
                'configs' => $configs,
                'editor' => $this->params['editor']
            )
        );
    }

    function update()
    {
        global $db;

        $obj = self::getEditorSettings($this->params['id'], $this->params['editor']);
        $obj->name = $this->params['name'];
        $obj->data = expStripSlashes($this->params['data']);
        $obj->skin = $this->params['skin'];
        $obj->scayt_on = $this->params['scayt_on'];
        $obj->paste_word = $this->params['paste_word'];
        $obj->plugins = expStripSlashes($this->params['plugins']);
        $obj->stylesset = expStripSlashes($this->params['stylesset']);
        $obj->formattags = expStripSlashes($this->params['formattags']);
        $obj->fontnames = expStripSlashes($this->params['fontnames']);
        $obj->additionalconfig = expStripSlashes($this->params['additionalconfig']);
        if (empty($this->params['id'])) {
            $this->params['id'] = $db->insertObject($obj, 'htmleditor_' . $this->params['editor']);
        } else {
            $db->updateObject($obj, 'htmleditor_' . $this->params['editor'], null, 'id');
        }
        if ($this->params['active']) {
            $this->activate();
        }
        expHistory::returnTo('manageable');
    }

    function edit()
    {
        expHistory::set('editable', $this->params);
        $tool = self::getEditorSettings(!empty($this->params['id'])?$this->params['id']:null, $this->params['editor']);
//        if ($tool == null) $tool = new stdClass();
        $tool->data = !empty($tool->data) ? @expStripSlashes($tool->data) : '';
        $tool->plugins = !empty($tool->plugins) ? @expStripSlashes($tool->plugins) : '';
        $tool->stylesset = !empty($tool->stylesset) ? @expStripSlashes($tool->stylesset) : '';
        $tool->formattags = !empty($tool->formattags) ? @expStripSlashes($tool->formattags) : '';
        $tool->fontnames = !empty($tool->fontnames) ? @expStripSlashes($tool->fontnames) : '';
        $tool->additionalconfig = !empty($tool->additionalconfig) ? @expStripSlashes($tool->additionalconfig) : '';
        if ($this->params['editor'] === 'tinymce5') {
            $dir = BASE . 'external/editors/' . $this->params['editor'] . '/skins/ui';
        } else {
            $dir = BASE . 'external/editors/' . $this->params['editor'] . '/skins';
        }
        $skins_dir = opendir($dir);
        $skins = array();
        while (($skin = readdir($skins_dir)) !== false) {
            if ($skin !== '.' && $skin !== '..') {
                $skins[] = $skin;
            }
        }
        assign_to_template(
            array(
                'record' => $tool,
                'skins'  => $skins,
                'editor' => $this->params['editor']
            )
        );
    }

    function delete()
    {
        global $db;

        expHistory::set('editable', $this->params);
        @$db->delete('htmleditor_' . $this->params['editor'], "id=" . $this->params['id']);
        expHistory::returnTo('manageable');
    }

    function activate()
    {
        global $db;

        $db->toggle('htmleditor_' . $this->params['editor'], "active", 'active=1');
        if ($this->params['id'] !== "default") {
            $active = self::getEditorSettings($this->params['id'], $this->params['editor']);
            $active->active = 1;
            $db->updateObject($active, 'htmleditor_' . $this->params['editor'], null, 'id');
        }
        expHistory::returnTo('manageable');
    }

    function preview()
    {
        if ($this->params['id'] == 0) { // we want the default editor
            $demo = new stdClass();
            $demo->id = 0;
            $demo->name = "Default";
            if ($this->params['editor'] === 'ckeditor') {
                $demo->skin = 'kama';
            } elseif ($this->params['editor'] === 'tinymce') {
                $demo->skin = 'lightgray';
            } elseif ($this->params['editor'] === 'tinymce5') {
                $demo->skin = 'oxide';
            }
        } else {
            $demo = self::getEditorSettings($this->params['id'], $this->params['editor']);
        }
        assign_to_template(
            array(
                'demo' => $demo,
                'editor' => $this->params['editor']
            )
        );
    }

    public static function getEditorSettings($settings_id, $editor)
    {
        global $db;

        $settings = @$db->selectObject('htmleditor_' . $editor, "id=" . $settings_id);
        if (empty($settings)) {
            $settings = new stdClass();
        }
        return $settings;
    }

    public static function getActiveEditorSettings($editor)
    {
        global $db;

        return $db->selectObject('htmleditor_' . $editor, 'active=1');
    }

    /**
     * Load elFinder files needed to run the File Browser under TinyMCE
     *
     * @param string $editor
     * @return void
     */
    public static function load_tiny_elFinder($editor = 'tinymce') {
        expCSS::pushToHead(array(
                "unique" => "jqueryui",
                "link" => PATH_RELATIVE . "external/jquery/css/smoothness/jquery-ui.min.css",
            )
        );
        expJavascript::pushToFoot(
            array(
                "unique" => "elfinder",
                "jquery" => "jqueryui",
                "src" => array(
                    PATH_RELATIVE . "external/elFinder/js/elFinder.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.version.js",
                    PATH_RELATIVE . "external/elFinder/js/jquery.dialogelfinder.js",
                    PATH_RELATIVE . "external/elFinder/js/jquery.elfinder.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.mimetypes.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.options.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.options.netmount.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.history.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.command.js",
                    PATH_RELATIVE . "external/elFinder/js/elFinder.resources.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/button.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/contextmenu.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/cwd.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/dialog.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/fullscreenbutton.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/navbar.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/navdock.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/overlay.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/panel.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/path.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/places.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/searchbutton.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/sortbutton.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/stat.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/toast.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/toolbar.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/tree.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/uploadButton.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/viewbutton.js",
                    PATH_RELATIVE . "external/elFinder/js/ui/workzone.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/archive.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/back.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/chmod.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/colwidth.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/copy.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/cut.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/download.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/duplicate.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/edit.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/empty.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/extract.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/forward.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/fullscreen.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/getfile.js",
                    PATH_RELATIVE . "framework/modules/file/connector/help.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/hidden.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/hide.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/home.js",
                    PATH_RELATIVE . "framework/modules/file/connector/info.js",
                    PATH_RELATIVE . "framework/modules/file/connector/links.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/mkdir.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/mkfile.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/netmount.js",
                    PATH_RELATIVE . "framework/modules/file/connector/open.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/opendir.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/opennew.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/paste.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/places.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/preference.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/quicklook.js",
                    PATH_RELATIVE . "framework/modules/file/connector/quicklook.plugins.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/reload.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/rename.js",
                    PATH_RELATIVE . "framework/modules/file/connector/resize.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/restore.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/rm.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/search.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/selectall.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/selectinvert.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/selectnone.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/sort.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/undo.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/up.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/upload.js",
                    PATH_RELATIVE . "external/elFinder/js/commands/view.js",
                    PATH_RELATIVE . "framework/modules/file/connector/i18n/elfinder." . substr(LOCALE, 0, 2) . ".js",
                    PATH_RELATIVE . "external/elFinder/js/extras/editors.default.js",
                )
            )
        );
        expJavascript::pushToFoot(
            array(
                "unique" => "tinymcepu",
                "src" => PATH_RELATIVE . "external/editors/" . $editor . "/plugins/quickupload/plupload.full.min.js"
            )
        );
        expJavascript::pushToFoot(
            array(
                "unique" => "tinymce",
                "src" => array(
                    PATH_RELATIVE . "external/editors/" . $editor . "/tinymce.min.js",
                    PATH_RELATIVE . "framework/modules/file/connector/tinymceElfinder.js",
                )
            )
        );
    }

}

?>
