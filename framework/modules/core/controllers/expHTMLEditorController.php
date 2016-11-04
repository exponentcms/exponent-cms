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
        return gt("Mostly for CKEditor");
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
        if (SITE_WYSIWYG_EDITOR == "FCKeditor") {
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
        $obj->data = stripSlashes($this->params['data']);
        $obj->skin = $this->params['skin'];
        $obj->scayt_on = $this->params['scayt_on'];
        $obj->paste_word = $this->params['paste_word'];
        $obj->plugins = stripSlashes($this->params['plugins']);
        $obj->stylesset = stripSlashes($this->params['stylesset']);
        $obj->formattags = stripSlashes($this->params['formattags']);
        $obj->fontnames = stripSlashes($this->params['fontnames']);
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
        if ($tool == null) $tool = new stdClass();
        $tool->data = !empty($tool->data) ? @stripSlashes($tool->data) : '';
        $tool->plugins = !empty($tool->plugins) ? @stripSlashes($tool->plugins) : '';
        $tool->stylesset = !empty($tool->stylesset) ? @stripSlashes($tool->stylesset) : '';
        $tool->formattags = !empty($tool->formattags) ? @stripSlashes($tool->formattags) : '';
        $tool->fontnames = !empty($tool->fontnames) ? @stripSlashes($tool->fontnames) : '';
        $skins_dir = opendir(BASE . 'external/editors/' . $this->params['editor'] . '/skins');
        $skins = array();
        while (($skin = readdir($skins_dir)) !== false) {
            if ($skin != '.' && $skin != '..') {
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
        if ($this->params['id'] != "default") {
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
            if ($this->params['editor'] == 'ckeditor') {
                $demo->skin = 'kama';
            } elseif ($this->params['editor'] == 'tinymce') {
                $demo->skin = 'lightgray';
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

        return @$db->selectObject('htmleditor_' . $editor, "id=" . $settings_id);
    }

    public static function getActiveEditorSettings($editor)
    {
        global $db;

        return $db->selectObject('htmleditor_' . $editor, 'active=1');
    }

}

?>
