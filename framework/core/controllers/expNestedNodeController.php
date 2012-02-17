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

/**
 * This is the class expNestedNodeController
 *
 * @package Core
 * @subpackage Controllers
 */

class expNestedNodeController extends expController {
	
	function displayname() { return "Nested Node Manager"; }
	function description() { return "This module is for manageing your Nested Nodes"; }
	function author() { return "OIC Group, Inc"; }

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