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

class expNestedNodeController extends expController {
	
	function name() { return $this->displayname(); } //for backwards compat with old modules
	function displayname() { return "Nested Node Manager"; }
	function description() { return "This module is for manageing your Nested Nodes"; }
	function author() { return "Adam Kessler @ OIC Group, Inc"; }
	function hasSources() { return true; }
	function hasViews() { return true; }

	function edit() {
		global $db;
		if (empty($this->params['id'])) { 
			//$parent = new $this->basemodel_name($this->params['parent_id']);
			$node = new $this->basemodel_name(array('parent_id'=>$this->params['parent'])); 
		} else { 
			$node = new $this->basemodel_name($this->params['id']);
		}	

		assign_to_template(array('node'=>$node));
	}

	function delete() {
		$node = new $this->basemodel_name($this->params['id']);
		$node->delete();
		redirect_to(array('controller'=>$this->classname, 'action'=>'manage'));
	}
	
	function reorder() {
		global $db;
		if (empty($this->params['type'])) return false;
	
		$movenode = new $this->basemodel_name($this->params['move']);
		switch($this->params['type']) {
			case 'addbefore':
				$movenode->moveBefore($this->params['target']);
				break;
			case 'addafter':
				$movenode->moveAfter($this->params['target']);
				break;
			case 'append':
				$movenode->moveInto($this->params['target']);
				break;
		}
	}

	function adsubnode() {
		redirect_to(array('controller'=>$this->classname, 'action'=>'edit', 'parent'=>$this->params['id']));
	}

	function manage() {
		global $db;
		$nodes = $db->selectNestedTree($this->model_table);
		foreach($nodes as $i=>$val){
			$nodes[$i]->draggable = true; 
			$nodes[$i]->pickable = true; 
		}
		assign_to_template(array('nodes'=>$nodes));	
	}

	function create() {
		$modelname = $this->basemodel_name;
		$this->$modelname->create($this->params);
		redirect_to(array('controller'=>$this->classname, 'action'=>'manage'));
	}

	function update() {
		$modelname = $this->basemodel_name;
		$this->$modelname->update($this->params);
		redirect_to(array('controller'=>$this->classname, 'action'=>'manage'));
	}
}

?>
