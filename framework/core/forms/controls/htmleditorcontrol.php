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

if (!defined('EXPONENT')) exit('');
/** @define "BASE" "../../.." */

if (SITE_WYSIWYG_EDITOR === "ckeditor") {

    /**
     * HTML Editor Control - displays wysiwyg editor widget
     *
     * @package Subsystems-Forms
     * @subpackage Control
     */
    class htmleditor extends ckeditorcontrol
    {

    }

} elseif (SITE_WYSIWYG_EDITOR === "ckeditor5") {

    /**
     * HTML Editor Control - displays wysiwyg editor widget
     *
     * @package Subsystems-Forms
     * @subpackage Control
     */
    class htmleditor extends ckeditor5control
    {

    }

} elseif (SITE_WYSIWYG_EDITOR === "tinymce") {

    /**
     * HTML Editor Control - displays wysiwyg editor widget
     *
     * @package Subsystems-Forms
     * @subpackage Control
     */
    class htmleditor extends tinymcecontrol
    {

    }

} elseif (SITE_WYSIWYG_EDITOR === "tinymce5") {

    /**
     * HTML Editor Control - displays wysiwyg editor widget
     *
     * @package Subsystems-Forms
     * @subpackage Control
     */
    class htmleditor extends tinymce5control
    {

    }

} else {

    /**
     * HTML Editor Control - displays wysiwyg editor widget
     *
     * @package Subsystems-Forms
     * @subpackage Control
     */
    class htmleditor extends texteditorcontrol
    {
        var $module = "";
        var $toolbar = "";

        static function name()
        {
            return "WYSIWYG Editor";
        }

        function __construct($default = "", $module = "", $rows = 20, $cols = 60, $toolbar = "", $height = 300)
        {
            $this->default = $default;
            $this->module = $module; // For looking up templates.
            $this->toolbar = $toolbar;
            $this->height = $height;
        }

        function controlToHTML($name, $label)
        {
            global $db;

            if ($this->toolbar == "") {
                $config = $db->selectObject("toolbar_" . SITE_WYSIWYG_EDITOR, "active=1");
            } else {
                $config = $db->selectObject("toolbar_" . SITE_WYSIWYG_EDITOR, "name='" . $this->toolbar . "'");
            }

            //as long as we don't have proper datamodels, emulate them
            //there are at least two sets of data: view data and content data
            $view = new StdClass();
            $content = new StdClass();

            if (isset($config->data)) {
                $view->toolbar = $config->data;
            } else {
                $view->toolbar = null;
            }
            $view->path_to_editor = PATH_RELATIVE . "external/editors/" . SITE_WYSIWYG_EDITOR . "/";

            $content->name = $name;

            $content->value = $this->default;

            //create new view object
            //WARNING: automatic fallback to Default.tpl will not work
            //until expCore::resolveFilePaths() gets an update
            //waiting for switch to PHP5: strrpos() will take strings as needle
//			$viewObj = new controltemplate("EditorControl", SITE_WYSIWYG_EDITOR);
//
//			//assign the data models to the view object
//			$viewObj->assign("view", $view);
//			$viewObj->assign("content", $content);
//			$viewObj->assign('height', $this->height);
//
//			//return the processed template to the caller for display
//			$html = $viewObj->render();

            //spares us to send the js editor init code more than once
            //TODO: Convert to OO API and use eXp->EditorControl->doneInit instead
//            if (!defined('SITE_WYSIWYG_INIT')) {
//                define('SITE_WYSIWYG_INIT', 1);
//            }

//			return $html;

        }

        static function parseData($name, $values, $for_db = false)
        {
            $html = $values[$name];
            if (trim($html) === "<br />") $html = "";
            return $html;
        }
    }

}

/**
 * HTML Editor Control - displays wysiwyg editor widget
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class htmleditorcontrol extends htmleditor
{
    static function name() { return "Text Area - WYSIWYG"; }

    static function isSimpleControl()
    {
        return true;
    }

    static function getFieldDefinition()
    {
        return array(
            DB_FIELD_TYPE => DB_DEF_STRING,
            DB_FIELD_LEN => 10000);
    }

    static function form($object)
    {
        $form = new form();
        if (empty($object)) $object = new stdClass();
        if (!isset($object->identifier)) {
            $object->identifier = "";
            $object->caption = "";
            $object->description = "";
            $object->default = "";
            $object->placeholder = "";
            $object->rows = 5;
            $object->cols = 38;
            $object->maxchars = 0;
            $object->maxlength = 0;
            $object->is_hidden = false;
        }
        if (empty($object->description)) $object->description = "";
        $form->register("identifier", gt('Identifier/Field'), new textcontrol($object->identifier),true, array('required'=>true));
        $form->register("caption", gt('Caption'), new textcontrol($object->caption));
        $form->register("description", gt('Control Description'), new textcontrol($object->description));
//        $form->register("default", gt('Default value'), new texteditorcontrol($object->default));
        $form->register("default", gt('Default value'), new htmleditorcontrol($object->default));
        $form->register("placeholder", gt('Placeholder'), new textcontrol($object->placeholder));
        $form->register("rows", gt('Rows'), new textcontrol($object->rows, 4, false, 3, "integer"));
        $form->register("cols", gt('Columns'), new textcontrol($object->cols, 4, false, 3, "integer"));
        $form->register("maxlength", gt('Maximum Length'), new textcontrol((($object->maxlength == 0) ? "" : $object->maxlength), 4, false, 3, "integer"));
        $form->register("is_hidden", gt('Make this a hidden field on initial entry'), new checkboxcontrol(!empty($object->is_hidden), false));
        if (!expJavascript::inAjaxAction())
            $form->register("submit", "", new buttongroupcontrol(gt('Save'), '', gt('Cancel'), "", 'editable'));
        return $form;
    }

    static function update($values, $object)
    {
        if ($object == null) $object = new htmleditorcontrol();
        if ($values['identifier'] == "") {
            $post = expString::sanitize($_POST);
            $post['_formError'] = gt('Identifier is required.');
            expSession::set("last_POST", $post);
            return null;
        }
        $object->identifier = $values['identifier'];
        $object->caption = $values['caption'];
        $object->description = $values['description'];
        if (isset($values['default'])) $object->default = $values['default'];
        if (isset($values['placeholder'])) $object->placeholder = $values['placeholder'];
        if (isset($values['rows'])) $object->rows = (int)($values['rows']);
        if (isset($values['cols'])) $object->cols = (int)($values['cols']);
        if (isset($values['maxchars'])) $object->maxchars = (int)($values['maxchars']);
        if (isset($values['maxlength'])) $object->maxlength = (int)($values['maxlength']);
        $object->required = !empty($values['required']);
        $object->is_hidden = !empty($values['is_hidden']);
        return $object;
    }

    //    static function parseData($original_name,$values,$for_db = false) {
    //   		return str_replace(array("\r\n","\n","\r"),'<br />', htmlspecialchars($values[$original_name]));
    //   	}

    static function templateFormat($db_data, $ctl)
    {
        return str_replace(array("\r\n", "\n", "\r", '\r\n', '\n', '\r'), '<br />', $db_data);
    }
}

?>
