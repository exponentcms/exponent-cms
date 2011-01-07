<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Created by Adam Kessler @ 09/06/2007
# Updated by Adam Kessler @ 09/06/2007
#
# This file is part of Acorn Web API
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

class storeCategory extends expNestedNode {
	public $table = 'storeCategories';
	public $attachable_table = 'content_storeCategories';     
                                                            
    protected $attachable_item_types = array(
        'content_expFiles'=>'expFile', 
    ); 
	
	public function __construct($params=array(), $get_assoc=true, $get_attached=true) {
		global $db;
		parent::__construct($params, $get_assoc, $get_attached);

		// if this is an empty category object we'll set the lft & rgt to the max
		// so they encompass the all of the categories, in essence creating an
		// unnamed  parent category to everything.
		if (empty($this->id)) {
			$this->lft = $db->min($this->table,'lft');
			$this->rgt = $db->max($this->table,'rgt');
		}
	}
	
	public function getEcomSubcategories() {
		global $db;
		$subcats = array();
		if (empty($this->id)) {
			$children = $this->getTopLevel();
		} else {
			$children = $db->selectNestedNodeChildren($this->table, $this->id);
		}
		
		for($i=0; $i<count($children); $i++) {
			$sql  = 'SELECT count(DISTINCT p.id) as count FROM '.DB_TABLE_PREFIX.'_product p JOIN '.DB_TABLE_PREFIX.'_product_storeCategories sc ';
          	$sql .= 'ON p.id = sc.product_id WHERE sc.storecategories_id IN (';
          	$sql .= 'SELECT id FROM '.DB_TABLE_PREFIX.'_storeCategories WHERE rgt BETWEEN '.$children[$i]->lft.' AND '.$children[$i]->rgt.")";

            //TODO: Category count update
          	//$count = $db->selectObjectBySql($sql);
          	$children[$i]->product_count = 0;//$count->count;
		}

		return $children;
	}

}

?>
