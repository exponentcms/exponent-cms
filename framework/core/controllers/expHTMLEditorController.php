<?php
/**
 * This file is part of Exponent Content Management System
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * @category   Exponent CMS
 * @package    Framework
 * @subpackage Controllers
 * @author     Adam Kessler <adam@oicgroup.net>
 * @copyright  2004-2009 OIC Group, Inc.
 * @license    GPL: http://www.gnu.org/licenses/gpl.txt
 * @version    Release: @package_version@
 * @link       http://www.exponent-docs.org/api/package/PackageName
 */

class expHTMLEditorController extends expController {

	function name() { return $this->displayname(); }
    function displayname() { return "Editors"; }
    function description() { return "Mostly for CKEditor"; }
    function author() { return "Phillip Ball"; }
    function hasSources() { return false; }
    function hasViews() { return true; }
	function hasContent() { return false; }
	protected $add_permissions = array('activate'=>"activate",'preview'=>"preview CKEditor toolbars");
    
    
    function manage () {
        global $db;
        // yet more gluecode
        if (SITE_WYSIWYG_EDITOR=="FCKeditor") redirect_to(array("module"=>"","administrationmodule"=>"htmlarea_configs"));
        
        expHistory::set('manageable',$this->params);
        
        // otherwise, on to cke
        $configs = $db->selectObjects('htmleditor_ckeditor',1);
        
        assign_to_template(array('configs'=>$configs));
    }

    function update () {
        global $db;
        $obj = $db->selectObject('htmleditor_ckeditor',"id=".$this->params['id']);
        $obj->name = $this->params['name'];
        $obj->data = stripSlashes($this->params['data']);
        if (empty($this->params['id'])) {
            $db->insertObject($obj,'htmleditor_ckeditor');
        } else {
            $db->updateObject($obj,'htmleditor_ckeditor',null,'id');
        }

        expHistory::back();
    }

    function edit() {
        global $db;
        expHistory::set('editable', $this->params);
        $tool = @$db->selectObject('htmleditor_ckeditor',"id=".$this->params['id']);
        $tool->data = @stripSlashes($tool->data);
        assign_to_template(array('record'=>$tool));
    }
    
    function activate () {
        global $db;
        
        $db->toggle('htmleditor_ckeditor',"active",'active=1');
        if ($this->params['id']!="default") {
            $active = $db->selectObject('htmleditor_ckeditor',"id=".$this->params['id']);
            $active->active = 1;
        
            $db->updateObject($active,'htmleditor_ckeditor',null,'id');
        }

        expHistory::back();
    }

    function preview () {
        global $db;
        if ($this->params['id']=="default") {
            $demo->name="Default";
            $demo->data="default";
        } else {
            $demo = $db->selectObject('htmleditor_ckeditor',"id=".$this->params['id']);
        }
        assign_to_template(array('demo'=>$demo));
    }

}

?>